<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'get';

if ($method === 'GET' && $action === 'get') {
    // Get last 50 messages
    $result = mysqli_query($db, "SELECT ID, username, userid, date, text FROM g_chat ORDER BY ID DESC LIMIT 50");
    $messages = [];
    $colors = ['#84674e', '#0b91b2', '#6ebe22', '#a93eab', '#3559b0'];
    while ($row = mysqli_fetch_assoc($result)) {
        $uid = intval($row['userid']);
        $messages[] = [
            'id' => intval($row['ID']),
            'name' => $row['username'],
            'user_id' => $uid,
            'text' => $row['text'],
            'time' => date('H:i', intval($row['date'])),
            'color' => $colors[$uid % 5],
        ];
    }
    // Reverse to show oldest first
    $messages = array_reverse($messages);

    // Reset chat count for this player
    mysqli_query($db, "UPDATE p_players SET chat_count = 0 WHERE id = $playerId");

    
    jsonSuccess(['messages' => $messages]);
}

if ($method === 'POST' && $action === 'send') {
    $input = json_decode(file_get_contents('php://input'), true);
    $text = trim($input['text'] ?? '');

    if ($text === '' || mb_strlen($text) > 250) {
        jsonError('الرسالة فارغة أو طويلة جداً');
    }

    // Get player name
    $pResult = mysqli_query($db, "SELECT name FROM p_players WHERE id = $playerId");
    $player = mysqli_fetch_assoc($pResult);
    if (!$player) { jsonError('Player not found'); }

    $safeName = mysqli_real_escape_string($db, $player['name']);
    $safeText = mysqli_real_escape_string($db, htmlspecialchars($text));
    $now = time();

    mysqli_query($db, "INSERT INTO g_chat (username, userid, date, text) VALUES ('$safeName', $playerId, '$now', '$safeText')");

    // Increment chat_count for all other players
    mysqli_query($db, "UPDATE p_players SET chat_count = chat_count + 1 WHERE id != $playerId");

    // Delete old messages (keep 50)
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM g_chat");
    $cnt = intval(mysqli_fetch_assoc($countResult)['cnt']);
    if ($cnt > 50) {
        $del = $cnt - 50;
        mysqli_query($db, "DELETE FROM g_chat ORDER BY ID ASC LIMIT $del");
    }

    
    jsonSuccess(['sent' => true]);
}

// Alliance chat
if ($method === 'GET' && $action === 'alliance') {
    $aResult = mysqli_query($db, "SELECT alliance_id FROM p_players WHERE id = $playerId");
    $aRow = mysqli_fetch_assoc($aResult);
    $allianceId = intval($aRow['alliance_id'] ?? 0);
    if ($allianceId <= 0) { jsonError('أنت لست في تحالف'); }

    $result = mysqli_query($db, "SELECT ID, username, userid, date, text FROM g_chat_alliance WHERE alliance_id = '$allianceId' ORDER BY ID DESC LIMIT 50");
    $messages = [];
    $colors = ['#84674e', '#0b91b2', '#6ebe22', '#a93eab', '#3559b0'];
    while ($row = mysqli_fetch_assoc($result)) {
        $uid = intval($row['userid']);
        $messages[] = [
            'id' => intval($row['ID']),
            'name' => $row['username'],
            'user_id' => $uid,
            'text' => $row['text'],
            'time' => date('H:i', intval($row['date'])),
            'color' => $colors[$uid % 5],
        ];
    }
    $messages = array_reverse($messages);
    
    jsonSuccess(['messages' => $messages]);
}

if ($method === 'POST' && $action === 'alliance_send') {
    $input = json_decode(file_get_contents('php://input'), true);
    $text = trim($input['text'] ?? '');
    if ($text === '' || mb_strlen($text) > 250) jsonError('الرسالة فارغة أو طويلة جداً');

    $pResult = mysqli_query($db, "SELECT name, alliance_id FROM p_players WHERE id = $playerId");
    $player = mysqli_fetch_assoc($pResult);
    $allianceId = intval($player['alliance_id'] ?? 0);
    if ($allianceId <= 0) { jsonError('أنت لست في تحالف'); }

    $safeName = mysqli_real_escape_string($db, $player['name']);
    $safeText = mysqli_real_escape_string($db, htmlspecialchars($text));
    $now = time();

    mysqli_query($db, "INSERT INTO g_chat_alliance (username, userid, date, text, alliance_id) VALUES ('$safeName', $playerId, '$now', '$safeText', '$allianceId')");

    // Cleanup old
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM g_chat_alliance WHERE alliance_id = '$allianceId'");
    $cnt = intval(mysqli_fetch_assoc($countResult)['cnt']);
    if ($cnt > 50) {
        $del = $cnt - 50;
        mysqli_query($db, "DELETE FROM g_chat_alliance WHERE alliance_id = '$allianceId' ORDER BY ID ASC LIMIT $del");
    }

    
    jsonSuccess(['sent' => true]);
}


jsonError('Invalid action', 400);
