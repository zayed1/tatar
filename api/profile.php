<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$action = $_GET['action'] ?? 'view';
$uid = intval($_GET['uid'] ?? $playerId);

if ($action === 'view') {
    $result = mysqli_query($db, "
        SELECT p.id, p.name, p.tribe_id, p.alliance_id, p.alliance_name,
               p.total_people_count, p.villages_count, p.gold_num,
               p.attack_points, p.defense_points, p.thief_points,
               p.week_attack_points, p.week_defense_points,
               p.hero_level, p.hero_points, p.hero_troop_id,
               IFNULL(p.hero_name, p.name) as hero_name,
               p.description1, p.gender, p.birth_date, p.avatar,
               p.active_plus_account, p.registration_date,
               FLOOR(TIME_TO_SEC(TIMEDIFF(NOW(), p.last_login_date))/3600) as hours_ago
        FROM p_players p WHERE p.id = $uid
    ");
    $player = mysqli_fetch_assoc($result);
    if (!$player) { jsonError('Player not found', 404); }

    // Get villages
    $vResult = mysqli_query($db, "
        SELECT id, village_name, rel_x, rel_y, people_count, is_capital
        FROM p_villages WHERE player_id = $uid AND is_oasis = 0
        ORDER BY is_capital DESC, id ASC
    ");
    $villages = [];
    while ($v = mysqli_fetch_assoc($vResult)) {
        $villages[] = [
            'id' => intval($v['id']),
            'name' => $v['village_name'],
            'x' => intval($v['rel_x']),
            'y' => intval($v['rel_y']),
            'population' => intval($v['people_count']),
            'is_capital' => intval($v['is_capital']),
        ];
    }

    // Get rank
    $pop = intval($player['total_people_count']);
    $vc = intval($player['villages_count']);
    $score = $pop * 10 + $vc;
    $rankResult = mysqli_query($db, "SELECT COUNT(*)+1 as player_rank FROM p_players WHERE (total_people_count*10+villages_count) > $score AND player_type != 4");
    $rank = intval(mysqli_fetch_assoc($rankResult)['player_rank']);

    $tribes = [1 => 'العرب', 2 => 'الرومان', 3 => 'اليونان', 4 => 'الجرمان', 5 => 'المغول', 6 => 'الفرس'];

    
    jsonSuccess([
        'id' => intval($player['id']),
        'name' => $player['name'],
        'tribe' => $tribes[intval($player['tribe_id'])] ?? 'غير معروف',
        'tribe_id' => intval($player['tribe_id']),
        'alliance' => $player['alliance_name'] ?: null,
        'alliance_id' => intval($player['alliance_id']),
        'population' => $pop,
        'villages_count' => $vc,
        'rank' => $rank,
        'gold' => intval($player['gold_num']),
        'points' => [
            'attack' => intval($player['attack_points']),
            'defense' => intval($player['defense_points']),
            'thief' => intval($player['thief_points']),
            'week_attack' => intval($player['week_attack_points']),
            'week_defense' => intval($player['week_defense_points']),
            'week_dev' => 0,
        ],
        'hero' => intval($player['hero_troop_id']) > 0 ? [
            'name' => $player['hero_name'],
            'level' => intval($player['hero_level']),
            'points' => intval($player['hero_points']),
        ] : null,
        'description' => $player['description1'] ?: '',
        'registered' => $player['registration_date'],
        'hours_ago' => intval($player['hours_ago']),
        'is_self' => $uid === $playerId,
        'villages' => $villages,
    ]);
}


jsonError('Invalid action', 400);
