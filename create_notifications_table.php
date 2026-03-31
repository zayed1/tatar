<?php
// One-time script to create notifications table
// Run: https://your-domain/create_notifications_table.php
// Then delete this file
require_once("core-f/config-f/s1.php");
$port = isset($AppConfig['db']['port']) ? intval($AppConfig['db']['port']) : 3306;
$link = mysqli_connect($AppConfig['db']['host'], $AppConfig['db']['user'], $AppConfig['db']['password'], $AppConfig['db']['database'], $port);
if (!$link) { die("DB connection failed: " . mysqli_connect_error()); }

$sql = "CREATE TABLE IF NOT EXISTS p_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT '',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_player (player_id, is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($link, $sql)) {
    echo "Table p_notifications created successfully!";
} else {
    echo "Error: " . mysqli_error($link);
}
mysqli_close($link);
?>
