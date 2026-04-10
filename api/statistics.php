<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$db = getDb();

$tab = $_GET['tab'] ?? 'players';
$page = max(0, intval($_GET['page'] ?? 0));
$limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
$offset = $page * $limit;

$TATAR_TYPE = 4; // PLAYERTYPE_TATAR

if ($tab === 'players') {
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_players WHERE player_type != $TATAR_TYPE");
    $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

    $result = mysqli_query($db, "
        SELECT p.id, p.name, p.alliance_name, p.tribe_id, p.total_people_count, p.villages_count
        FROM p_players p
        WHERE p.player_type != $TATAR_TYPE
        ORDER BY (p.total_people_count*10+p.villages_count) DESC, p.id ASC
        LIMIT $offset, $limit
    ");
    $items = [];
    $rank = $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $rank++;
        $items[] = [
            'rank' => $rank,
            'id' => intval($row['id']),
            'name' => $row['name'],
            'alliance' => $row['alliance_name'] ?: '',
            'tribe_id' => intval($row['tribe_id']),
            'population' => intval($row['total_people_count']),
            'villages' => intval($row['villages_count']),
        ];
    }
    
    jsonSuccess(['items' => $items, 'total' => $total, 'page' => $page]);
}

if ($tab === 'alliances') {
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_alliances");
    $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

    $result = mysqli_query($db, "
        SELECT a.id, a.name, a.player_count,
               SUM(p.total_people_count) as points,
               ROUND(AVG(p.total_people_count)) as average
        FROM p_alliances a
        LEFT JOIN p_players p ON p.alliance_id = a.id AND p.player_type != $TATAR_TYPE
        GROUP BY a.id
        ORDER BY (a.rating*100+a.player_count) DESC, a.id ASC
        LIMIT $offset, $limit
    ");
    $items = [];
    $rank = $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $rank++;
        $items[] = [
            'rank' => $rank,
            'id' => intval($row['id']),
            'name' => $row['name'],
            'members' => intval($row['player_count']),
            'points' => intval($row['points']),
            'average' => intval($row['average']),
        ];
    }
    
    jsonSuccess(['items' => $items, 'total' => $total, 'page' => $page]);
}

if ($tab === 'attack' || $tab === 'defense') {
    $col = $tab === 'attack' ? 'attack_points' : 'defense_points';
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_players WHERE player_type != $TATAR_TYPE AND $col > 0");
    $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

    $result = mysqli_query($db, "
        SELECT p.id, p.name, p.alliance_name, p.tribe_id, p.$col as points
        FROM p_players p
        WHERE p.player_type != $TATAR_TYPE AND p.$col > 0
        ORDER BY p.$col DESC, p.id ASC
        LIMIT $offset, $limit
    ");
    $items = [];
    $rank = $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $rank++;
        $items[] = [
            'rank' => $rank,
            'id' => intval($row['id']),
            'name' => $row['name'],
            'alliance' => $row['alliance_name'] ?: '',
            'points' => intval($row['points']),
        ];
    }
    
    jsonSuccess(['items' => $items, 'total' => $total, 'page' => $page]);
}

if ($tab === 'heroes') {
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_players WHERE player_type != $TATAR_TYPE AND hero_troop_id > 0");
    $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

    $result = mysqli_query($db, "
        SELECT p.id, p.name, p.hero_troop_id, p.hero_level, p.hero_points,
               IFNULL(p.hero_name, p.name) as hero_name
        FROM p_players p
        WHERE p.player_type != $TATAR_TYPE AND p.hero_troop_id > 0
        ORDER BY (p.hero_points*10+p.hero_level) DESC, p.id ASC
        LIMIT $offset, $limit
    ");
    $items = [];
    $rank = $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $rank++;
        $items[] = [
            'rank' => $rank,
            'id' => intval($row['id']),
            'name' => $row['name'],
            'hero_name' => $row['hero_name'],
            'level' => intval($row['hero_level']),
            'points' => intval($row['hero_points']),
        ];
    }
    
    jsonSuccess(['items' => $items, 'total' => $total, 'page' => $page]);
}

if ($tab === 'villages') {
    $countResult = mysqli_query($db, "SELECT COUNT(*) as cnt FROM p_villages WHERE player_id IS NOT NULL AND is_oasis = 0");
    $total = intval(mysqli_fetch_assoc($countResult)['cnt']);

    $result = mysqli_query($db, "
        SELECT v.id, v.village_name, v.player_name, v.people_count, v.rel_x, v.rel_y
        FROM p_villages v
        WHERE v.player_id IS NOT NULL AND v.is_oasis = 0
        ORDER BY v.people_count DESC, v.id ASC
        LIMIT $offset, $limit
    ");
    $items = [];
    $rank = $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $rank++;
        $items[] = [
            'rank' => $rank,
            'id' => intval($row['id']),
            'name' => $row['village_name'],
            'player' => $row['player_name'],
            'population' => intval($row['people_count']),
            'x' => intval($row['rel_x']),
            'y' => intval($row['rel_y']),
        ];
    }
    
    jsonSuccess(['items' => $items, 'total' => $total, 'page' => $page]);
}


jsonError('Invalid tab. Use: players, alliances, attack, defense, heroes, villages', 400);
