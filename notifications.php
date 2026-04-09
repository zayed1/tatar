<?php
require(".".DIRECTORY_SEPARATOR."core-f".DIRECTORY_SEPARATOR."boot.php");
require_once(MODEL_PATH."notification.php");

$p = new Player();
$player = $p->getInstance();
if (!$player || !$player->playerId) { header('Content-Type: application/json'); echo json_encode(['error' => 'not_logged_in']); exit; }

$nm = new NotificationModel();
$action = $_GET['action'] ?? '';
$pid = $player->playerId;

header('Content-Type: application/json; charset=utf-8');

switch ($action) {
    case 'count':
        $count = $nm->getUnreadCount($pid);
        echo json_encode(['count' => intval($count)]);
        break;
    case 'list':
        $result = $nm->getRecent($pid);
        $notifications = [];
        if ($result) {
            while ($result->next()) {
                $notifications[] = $result->row;
            }
        }
        echo json_encode(['notifications' => $notifications]);
        break;
    case 'read':
        $nm->markAllRead($pid);
        $nm->cleanup($pid);
        echo json_encode(['success' => true, 'count' => 0]);
        break;
    case 'read_one':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) $nm->markRead($id, $pid);
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['error' => 'invalid_action']);
}
$nm->dispose();
?>