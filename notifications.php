<?php
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
require_once(MODEL_PATH."notification.php");

$p = new Player();
$player = $p->getInstance();
if (!$player) { header('Content-Type: application/json'); echo json_encode(['error' => 'not_logged_in']); exit; }

$nm = new NotificationModel();
$action = $_GET['action'] ?? '';

header('Content-Type: application/json; charset=utf-8');

switch ($action) {
    case 'count':
        echo json_encode(['count' => $nm->getUnreadCount($player->playerId)]);
        break;
    case 'list':
        $result = $nm->getRecent($player->playerId);
        $notifications = [];
        while ($result && $result->next()) {
            $notifications[] = $result->row;
        }
        echo json_encode(['notifications' => $notifications]);
        break;
    case 'read':
        $nm->markAllRead($player->playerId);
        echo json_encode(['success' => true]);
        break;
    case 'read_one':
        $id = intval($_GET['id'] ?? 0);
        $nm->markRead($id, $player->playerId);
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['error' => 'invalid_action']);
}
?>
