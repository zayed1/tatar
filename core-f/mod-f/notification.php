<?php

class NotificationModel extends ModelBase {

    public function add($playerId, $type, $message, $link = '') {
        $this->provider->executeQuery(
            "INSERT INTO p_notifications (player_id, type, message, link) VALUES (%s, '%s', '%s', '%s')",
            array($playerId, $type, $message, $link)
        );
    }

    public function getUnreadCount($playerId) {
        return (int)$this->provider->fetchScalar(
            "SELECT COUNT(*) FROM p_notifications WHERE player_id=%s AND is_read=0",
            array($playerId)
        );
    }

    public function getRecent($playerId, $limit = 20) {
        return $this->provider->fetchResultSet(
            "SELECT * FROM p_notifications WHERE player_id=%s ORDER BY created_at DESC LIMIT %s",
            array($playerId, $limit)
        );
    }

    public function markAllRead($playerId) {
        $this->provider->executeQuery(
            "UPDATE p_notifications SET is_read=1 WHERE player_id=%s AND is_read=0",
            array($playerId)
        );
    }

    public function markRead($id, $playerId) {
        $this->provider->executeQuery(
            "UPDATE p_notifications SET is_read=1 WHERE id=%s AND player_id=%s",
            array($id, $playerId)
        );
    }

    public function cleanup($playerId) {
        $this->provider->executeQuery(
            "DELETE FROM p_notifications WHERE player_id=%s AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)",
            array($playerId)
        );
    }
}

?>
