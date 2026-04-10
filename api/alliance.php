<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$action = $_GET['action'] ?? 'info';

if ($action === 'info') {
    $aid = intval($_GET['id'] ?? 0);

    // If no ID, get player's alliance
    if ($aid <= 0) {
        $pResult = mysqli_query($db, "SELECT alliance_id FROM p_players WHERE id = $playerId");
        $pRow = mysqli_fetch_assoc($pResult);
        $aid = intval($pRow['alliance_id'] ?? 0);
        if ($aid <= 0) { jsonError('أنت لست في تحالف'); }
    }

    $result = mysqli_query($db, "
        SELECT a.id, a.name, a.name2, a.player_count, a.max_player_count,
               a.rating, a.creation_date, a.description1, a.description2,
               a.players_ids, a.contracts_alliance_id, a.war_alliance_id,
               a.attack_points, a.defense_points,
               a.week_attack_points, a.week_defense_points, a.week_dev_points
        FROM p_alliances a WHERE a.id = $aid
    ");
    $alliance = mysqli_fetch_assoc($result);
    if (!$alliance) { jsonError('التحالف غير موجود', 404); }

    // Get members
    $memberIds = $alliance['players_ids'] ?? '';
    $members = [];
    if ($memberIds !== '') {
        $safeMemberIds = mysqli_real_escape_string($db, $memberIds);
        $mResult = mysqli_query($db, "
            SELECT p.id, p.name, p.total_people_count, p.villages_count,
                   p.alliance_roles, p.color_name,
                   FLOOR(TIME_TO_SEC(TIMEDIFF(NOW(), p.last_login_date))/3600) as hours_ago
            FROM p_players p WHERE p.id IN ($safeMemberIds)
            ORDER BY p.total_people_count DESC
        ");
        while ($m = mysqli_fetch_assoc($mResult)) {
            $roleParts = explode(' ', $m['alliance_roles'] ?? '0 عضو', 2);
            $members[] = [
                'id' => intval($m['id']),
                'name' => $m['name'],
                'population' => intval($m['total_people_count']),
                'villages' => intval($m['villages_count']),
                'role' => $roleParts[1] ?? 'عضو',
                'role_num' => intval($roleParts[0] ?? 0),
                'hours_ago' => intval($m['hours_ago']),
            ];
        }
    }

    // Parse diplomacy
    $contracts = [];
    $contractStr = $alliance['contracts_alliance_id'] ?? '';
    if ($contractStr !== '') {
        foreach (explode(',', $contractStr) as $c) {
            $parts = explode(' ', trim($c));
            if (count($parts) >= 2) {
                $caid = intval($parts[0]);
                $cResult = mysqli_query($db, "SELECT name FROM p_alliances WHERE id = $caid");
                $cRow = mysqli_fetch_assoc($cResult);
                $contracts[] = [
                    'alliance_id' => $caid,
                    'name' => $cRow['name'] ?? '?',
                    'status' => intval($parts[1]), // 0=accepted, 1=sent, 2=received
                ];
            }
        }
    }

    $wars = [];
    $warStr = $alliance['war_alliance_id'] ?? '';
    if ($warStr !== '') {
        foreach (explode(',', $warStr) as $wid) {
            $wid = intval(trim($wid));
            if ($wid <= 0) continue;
            $wResult = mysqli_query($db, "SELECT name FROM p_alliances WHERE id = $wid");
            $wRow = mysqli_fetch_assoc($wResult);
            $wars[] = ['alliance_id' => $wid, 'name' => $wRow['name'] ?? '?'];
        }
    }

    // Get rank
    $score = intval($alliance['rating']) * 100 + intval($alliance['player_count']);
    $rankResult = mysqli_query($db, "SELECT COUNT(*)+1 as alliance_rank FROM p_alliances WHERE (rating*100+player_count) > $score");
    $rank = intval(mysqli_fetch_assoc($rankResult)['alliance_rank']);

    // Check if player is member
    $isMember = in_array($playerId, array_map('intval', explode(',', $memberIds)));

    
    jsonSuccess([
        'id' => intval($alliance['id']),
        'name' => $alliance['name'],
        'full_name' => $alliance['name2'],
        'rank' => $rank,
        'members_count' => intval($alliance['player_count']),
        'max_members' => intval($alliance['max_player_count']),
        'created' => $alliance['creation_date'],
        'description' => trim(($alliance['description1'] ?? '') . "\n" . ($alliance['description2'] ?? '')),
        'points' => [
            'attack' => intval($alliance['attack_points']),
            'defense' => intval($alliance['defense_points']),
            'week_attack' => intval($alliance['week_attack_points']),
            'week_defense' => intval($alliance['week_defense_points']),
            'week_dev' => intval($alliance['week_dev_points']),
        ],
        'members' => $members,
        'contracts' => $contracts,
        'wars' => $wars,
        'is_member' => $isMember,
    ]);
}


jsonError('Invalid action', 400);
