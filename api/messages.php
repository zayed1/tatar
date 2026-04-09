<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'inbox';

// GET: list or read
if ($method === 'GET') {
    if ($action === 'inbox' || $action === 'sent') {
        $page = max(0, intval($_GET['page'] ?? 0));
        $limit = min(50, max(1, intval($_GET['limit'] ?? 30)));
        $offset = $page * $limit;
        $isInbox = ($action === 'inbox');

        if ($isInbox) {
            $where = "m.to_player_id = $playerId AND m.delete_status != 1 AND m.to_archiv = 0";
        } else {
            $where = "m.from_player_id = $playerId AND m.delete_status != 2 AND m.form_archiv = 0";
        }

        $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_msgs m WHERE $where");
        $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

        $result = mysqli_query($db, "
            SELECT m.id, m.from_player_id, m.from_player_name, m.to_player_id, m.to_player_name,
                   m.msg_title, m.is_readed, DATE_FORMAT(m.creation_date, '%Y-%m-%d %H:%i') as date
            FROM p_msgs m
            WHERE $where
            ORDER BY m.creation_date DESC
            LIMIT $offset, $limit
        ");

        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'id' => intval($row['id']),
                'from_id' => intval($row['from_player_id']),
                'from_name' => $row['from_player_name'],
                'to_id' => intval($row['to_player_id']),
                'to_name' => $row['to_player_name'],
                'title' => $row['msg_title'],
                'is_read' => intval($row['is_readed']),
                'date' => $row['date'],
            ];
        }

        mysqli_close($db);
        jsonSuccess(['messages' => $messages, 'total' => $total, 'page' => $page]);
    }

    if ($action === 'read') {
        $msgId = intval($_GET['id'] ?? 0);
        if ($msgId <= 0) jsonError('Invalid message ID');

        $result = mysqli_query($db, "
            SELECT m.id, m.from_player_id, m.from_player_name, m.to_player_id, m.to_player_name,
                   m.msg_title, m.msg_body, m.is_readed,
                   DATE_FORMAT(m.creation_date, '%Y-%m-%d %H:%i') as date
            FROM p_msgs m
            WHERE m.id = $msgId AND (m.from_player_id = $playerId OR m.to_player_id = $playerId)
        ");
        $msg = mysqli_fetch_assoc($result);
        if (!$msg) { mysqli_close($db); jsonError('Message not found', 404); }

        // Mark as read
        if (intval($msg['to_player_id']) === $playerId && intval($msg['is_readed']) === 0) {
            mysqli_query($db, "UPDATE p_msgs SET is_readed = 1 WHERE id = $msgId");
            mysqli_query($db, "UPDATE p_players SET new_mail_count = GREATEST(0, new_mail_count - 1) WHERE id = $playerId");
        }

        mysqli_close($db);
        jsonSuccess([
            'id' => intval($msg['id']),
            'from_id' => intval($msg['from_player_id']),
            'from_name' => $msg['from_player_name'],
            'to_id' => intval($msg['to_player_id']),
            'to_name' => $msg['to_player_name'],
            'title' => $msg['msg_title'],
            'body' => $msg['msg_body'],
            'is_read' => 1,
            'date' => $msg['date'],
        ]);
    }
}

// POST: send or delete
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'send') {
        $toName = trim($input['to'] ?? '');
        $title = trim($input['title'] ?? '');
        $body = trim($input['body'] ?? '');

        if ($toName === '' || $title === '' || $body === '') {
            jsonError('جميع الحقول مطلوبة');
        }

        $safeToName = mysqli_real_escape_string($db, $toName);
        $toResult = mysqli_query($db, "SELECT id, name FROM p_players WHERE name = '$safeToName'");
        $toPlayer = mysqli_fetch_assoc($toResult);
        if (!$toPlayer) { mysqli_close($db); jsonError('اللاعب غير موجود'); }
        if (intval($toPlayer['id']) === $playerId) { mysqli_close($db); jsonError('لا يمكنك إرسال رسالة لنفسك'); }

        $fromResult = mysqli_query($db, "SELECT name FROM p_players WHERE id = $playerId");
        $fromPlayer = mysqli_fetch_assoc($fromResult);

        $safeTitle = mysqli_real_escape_string($db, htmlspecialchars($title));
        $safeBody = mysqli_real_escape_string($db, htmlspecialchars($body));
        $fromName = mysqli_real_escape_string($db, $fromPlayer['name']);
        $toId = intval($toPlayer['id']);

        mysqli_query($db, "
            INSERT INTO p_msgs (from_player_id, from_player_name, to_player_id, to_player_name,
                               msg_title, msg_body, creation_date, is_readed, delete_status)
            VALUES ($playerId, '$fromName', $toId, '$safeToName', '$safeTitle', '$safeBody', NOW(), 0, 0)
        ");
        $msgId = mysqli_insert_id($db);

        mysqli_query($db, "UPDATE p_players SET new_mail_count = new_mail_count + 1 WHERE id = $toId");

        mysqli_close($db);
        jsonSuccess(['id' => $msgId]);
    }

    if ($action === 'delete') {
        $msgId = intval($input['id'] ?? 0);
        if ($msgId <= 0) jsonError('Invalid message ID');

        $result = mysqli_query($db, "SELECT from_player_id, to_player_id, is_readed, delete_status FROM p_msgs WHERE id = $msgId");
        $msg = mysqli_fetch_assoc($result);
        if (!$msg) { mysqli_close($db); jsonError('Message not found', 404); }

        $isReceiver = intval($msg['to_player_id']) === $playerId;
        $isSender = intval($msg['from_player_id']) === $playerId;
        if (!$isReceiver && !$isSender) { mysqli_close($db); jsonError('Unauthorized', 403); }

        $currentStatus = intval($msg['delete_status']);
        if ($currentStatus !== 0) {
            mysqli_query($db, "DELETE FROM p_msgs WHERE id = $msgId");
        } else {
            $newStatus = $isReceiver ? 1 : 2;
            mysqli_query($db, "UPDATE p_msgs SET delete_status = $newStatus WHERE id = $msgId");
        }

        if ($isReceiver && intval($msg['is_readed']) === 0) {
            mysqli_query($db, "UPDATE p_players SET new_mail_count = GREATEST(0, new_mail_count - 1) WHERE id = $playerId");
        }

        mysqli_close($db);
        jsonSuccess(['deleted' => true]);
    }
}

mysqli_close($db);
jsonError('Invalid action', 400);
