<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

$CAT_NAMES = [0 => 'all', 1 => 'spy', 2 => 'trade', 3 => 'attack', 4 => 'defense', 5 => 'reinforce', 6 => 'archive'];

if ($method === 'GET') {
    if ($action === 'list') {
        $cat = max(0, min(6, intval($_GET['cat'] ?? 0)));
        $page = max(0, intval($_GET['page'] ?? 0));
        $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
        $offset = $page * $limit;

        $baseWhere = "r.delete_status != 3 AND (
            (r.to_player_id = $playerId AND r.delete_status != 1) OR
            (r.from_player_id = $playerId AND r.delete_status != 2)
        )";

        if ($cat === 0) {
            $where = "$baseWhere AND r.to_archiv = 0 AND r.form_archiv = 0";
        } elseif ($cat === 6) {
            $where = "$baseWhere AND (r.to_archiv = 1 OR r.form_archiv = 1)";
        } else {
            $where = "$baseWhere AND r.rpt_cat = $cat AND r.to_archiv = 0 AND r.form_archiv = 0";
        }

        $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_rpts r WHERE $where");
        $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

        $result = mysqli_query($db, "
            SELECT r.id, r.from_player_id, r.from_player_name, r.from_village_name,
                   r.to_player_id, r.to_player_name, r.to_village_name,
                   r.rpt_cat, r.rpt_result, r.read_status,
                   DATE_FORMAT(r.creation_date, '%Y-%m-%d %H:%i') as date
            FROM p_rpts r
            WHERE $where
            ORDER BY r.creation_date DESC
            LIMIT $offset, $limit
        ");

        $reports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $isAttacker = intval($row['from_player_id']) === $playerId;
            $readBit = $isAttacker ? 2 : 1;
            $isRead = (intval($row['read_status']) & $readBit) > 0;

            $reports[] = [
                'id' => intval($row['id']),
                'from_name' => $row['from_player_name'],
                'from_village' => $row['from_village_name'],
                'to_name' => $row['to_player_name'],
                'to_village' => $row['to_village_name'],
                'category' => intval($row['rpt_cat']),
                'result' => intval($row['rpt_result']),
                'is_read' => $isRead,
                'is_attacker' => $isAttacker,
                'date' => $row['date'],
            ];
        }

        mysqli_close($db);
        jsonSuccess(['reports' => $reports, 'total' => $total, 'page' => $page]);
    }

    if ($action === 'read') {
        $rptId = intval($_GET['id'] ?? 0);
        if ($rptId <= 0) jsonError('Invalid report ID');

        $result = mysqli_query($db, "
            SELECT r.id, r.from_player_id, r.from_player_name, r.from_village_id, r.from_village_name,
                   r.to_player_id, r.to_player_name, r.to_village_id, r.to_village_name,
                   r.rpt_body, r.rpt_cat, r.rpt_result, r.read_status,
                   DATE_FORMAT(r.creation_date, '%Y-%m-%d %H:%i') as date
            FROM p_rpts r
            WHERE r.id = $rptId AND (r.from_player_id = $playerId OR r.to_player_id = $playerId)
        ");
        $rpt = mysqli_fetch_assoc($result);
        if (!$rpt) { mysqli_close($db); jsonError('Report not found', 404); }

        // Mark as read
        $isAttacker = intval($rpt['from_player_id']) === $playerId;
        $readBit = $isAttacker ? 2 : 1;
        $currentRead = intval($rpt['read_status']);
        if (($currentRead & $readBit) === 0) {
            $newRead = $currentRead + $readBit;
            mysqli_query($db, "UPDATE p_rpts SET read_status = $newRead WHERE id = $rptId");
            mysqli_query($db, "UPDATE p_players SET new_report_count = GREATEST(0, new_report_count - 1) WHERE id = $playerId");
        }

        mysqli_close($db);
        jsonSuccess([
            'id' => intval($rpt['id']),
            'from_name' => $rpt['from_player_name'],
            'from_village' => $rpt['from_village_name'],
            'to_name' => $rpt['to_player_name'],
            'to_village' => $rpt['to_village_name'],
            'body' => $rpt['rpt_body'],
            'category' => intval($rpt['rpt_cat']),
            'result' => intval($rpt['rpt_result']),
            'date' => $rpt['date'],
        ]);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'delete') {
        $rptId = intval($input['id'] ?? 0);
        if ($rptId <= 0) jsonError('Invalid report ID');

        $result = mysqli_query($db, "SELECT from_player_id, to_player_id, read_status, delete_status FROM p_rpts WHERE id = $rptId");
        $rpt = mysqli_fetch_assoc($result);
        if (!$rpt) { mysqli_close($db); jsonError('Report not found', 404); }

        $isAttacker = intval($rpt['from_player_id']) === $playerId;
        $isDefender = intval($rpt['to_player_id']) === $playerId;
        if (!$isAttacker && !$isDefender) { mysqli_close($db); jsonError('Unauthorized', 403); }

        $currentStatus = intval($rpt['delete_status']);
        if ($currentStatus !== 0 || intval($rpt['from_player_id']) === intval($rpt['to_player_id'])) {
            mysqli_query($db, "UPDATE p_rpts SET delete_status = 3 WHERE id = $rptId");
        } else {
            $newStatus = $isDefender ? 1 : 2;
            mysqli_query($db, "UPDATE p_rpts SET delete_status = $newStatus WHERE id = $rptId");
        }

        // Update unread count
        $readBit = $isAttacker ? 2 : 1;
        if ((intval($rpt['read_status']) & $readBit) === 0) {
            mysqli_query($db, "UPDATE p_players SET new_report_count = GREATEST(0, new_report_count - 1) WHERE id = $playerId");
        }

        mysqli_close($db);
        jsonSuccess(['deleted' => true]);
    }
}

mysqli_close($db);
jsonError('Invalid action', 400);
