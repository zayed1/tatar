<?php
// Admin authentication - checks player_type from game session
// All admin pages use: if ($name==$a && $pwd==$p) { ... }
// We set $a/$p to match $_SESSION values when player is admin (player_type=2)

$a = '___no_match___';
$p = '___no_match___';

// Check if current game session has admin player
if (isset($_SESSION['is_rig'])) {
    $db_port_adm = isset($GLOBALS['AppConfig']['db']['port']) ? intval($GLOBALS['AppConfig']['db']['port']) : 3306;
    $link_adm = @mysqli_connect(
        $GLOBALS['AppConfig']['db']['host'],
        $GLOBALS['AppConfig']['db']['user'],
        $GLOBALS['AppConfig']['db']['password'],
        $GLOBALS['AppConfig']['db']['database'],
        $db_port_adm
    );
    if ($link_adm) {
        $safeName = mysqli_real_escape_string($link_adm, $_SESSION['is_rig']);
        $result_adm = mysqli_query($link_adm, "SELECT player_type FROM p_players WHERE name='$safeName' LIMIT 1");
        $row_adm = mysqli_fetch_assoc($result_adm);
        if ($row_adm && intval($row_adm['player_type']) === 2) {
            // Player is admin - make the check pass automatically
            $a = $_SESSION['nm_admin'] ?? '';
            $p = $_SESSION['pwd_admin'] ?? '';
        }
        mysqli_close($link_adm);
    }
}
?>
