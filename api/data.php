<?php
require_once(__DIR__ . '/config.php');

$auth = requireAuth();
$playerId = intval($auth['id']);

$db = getDb();

// Get player data with resources
$pResult = mysqli_query($db, "
    SELECT p.gold_num, p.new_mail_count, p.new_report_count,
           p.selected_village_id, p.active_plus_account,
           v.resources, v.village_name, v.id as vid,
           v.crop_consumption,
           TIME_TO_SEC(TIMEDIFF(NOW(), v.last_update_date)) as elapsed
    FROM p_players p
    LEFT JOIN p_villages v ON v.id = p.selected_village_id
    WHERE p.id = $playerId
");
$row = mysqli_fetch_assoc($pResult);

if (!$row) {
    jsonError('Player not found', 404);
}

// Parse resources string: "1 val max init rate pct,2 val max init rate pct,..."
$resources = [];
$resStr = $row['resources'] ?? '';
$elapsed = floatval($row['elapsed'] ?? 0);
$cropConsumption = intval($row['crop_consumption'] ?? 0);

if ($resStr !== '') {
    $rArr = explode(',', $resStr);
    $types = [1 => 'wood', 2 => 'clay', 3 => 'iron', 4 => 'crop'];
    foreach ($rArr as $rStr) {
        $r = explode(' ', trim($rStr));
        if (count($r) < 6) continue;
        $type = intval($r[0]);
        $value = floatval($r[1]);
        $maxLimit = intval($r[2]);
        $rate = intval($r[4]);
        $pct = intval($r[5]);
        $prodRate = floor($rate * (1 + $pct / 100)) - ($type === 4 ? $cropConsumption : 0);
        $current = floor($value + $elapsed * ($prodRate / 3600));
        if ($current > $maxLimit) $current = $maxLimit;
        if ($current < 0) $current = 0;
        $name = $types[$type] ?? "r$type";
        $resources[$name] = [
            'current' => $current,
            'max' => $maxLimit,
            'rate' => $prodRate
        ];
    }
}

// Get all villages
$vResult = mysqli_query($db, "SELECT id, village_name, rel_x, rel_y, is_capital FROM p_villages WHERE player_id=$playerId ORDER BY is_capital DESC, id ASC");
$villages = [];
while ($v = mysqli_fetch_assoc($vResult)) {
    $villages[] = [
        'id' => intval($v['id']),
        'name' => $v['village_name'],
        'x' => intval($v['rel_x']),
        'y' => intval($v['rel_y']),
        'is_capital' => intval($v['is_capital'])
    ];
}

jsonSuccess([
    'gold' => intval($row['gold_num']),
    'new_messages' => intval($row['new_mail_count']),
    'new_reports' => intval($row['new_report_count']),
    'plus_active' => intval($row['active_plus_account']),
    'selected_village_id' => intval($row['selected_village_id']),
    'village_name' => $row['village_name'] ?? '',
    'resources' => $resources,
    'villages' => $villages
]);
