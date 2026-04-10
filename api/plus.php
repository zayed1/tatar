<?php
require_once(__DIR__ . '/config.php');
require_once(dirname(__DIR__) . '/core-f/config-f/s1.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$action = $_GET['action'] ?? 'status';

if ($action === 'status') {
    $result = mysqli_query($db, "
        SELECT gold_num, silver_num, active_plus_account, goldclub
        FROM p_players WHERE id = $playerId
    ");
    $player = mysqli_fetch_assoc($result);

    // Get active plus features from queue
    $qResult = mysqli_query($db, "
        SELECT proc_type, GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), end_date)) as remaining_sec
        FROM p_queue
        WHERE player_id = $playerId AND proc_type BETWEEN 18 AND 56 AND end_date > NOW()
    ");
    $activeFeatures = [];
    while ($q = mysqli_fetch_assoc($qResult)) {
        $activeFeatures[] = [
            'type' => intval($q['proc_type']),
            'remaining_sec' => intval($q['remaining_sec']),
        ];
    }

    $cfg = $GLOBALS['AppConfig']['Game'];

    $features = [
        ['id' => 0, 'name' => 'حساب بلاس', 'icon' => '⭐', 'cost' => intval($cfg['plus3']), 'duration' => $cfg['plus1'] . ' يوم', 'type' => 'duration'],
        ['id' => 1, 'name' => 'خشب +25%', 'icon' => '🪵', 'cost' => intval($cfg['plus4']), 'duration' => $cfg['plus2'] . ' يوم', 'type' => 'duration'],
        ['id' => 2, 'name' => 'طين +25%', 'icon' => '🧱', 'cost' => intval($cfg['plus4']), 'duration' => $cfg['plus2'] . ' يوم', 'type' => 'duration'],
        ['id' => 3, 'name' => 'حديد +25%', 'icon' => '⛏️', 'cost' => intval($cfg['plus4']), 'duration' => $cfg['plus2'] . ' يوم', 'type' => 'duration'],
        ['id' => 4, 'name' => 'قمح +25%', 'icon' => '🌾', 'cost' => intval($cfg['plus4']), 'duration' => $cfg['plus2'] . ' يوم', 'type' => 'duration'],
        ['id' => 5, 'name' => 'إنهاء البناء فوراً', 'icon' => '⚡', 'cost' => intval($cfg['plus5']), 'duration' => 'فوري', 'type' => 'instant'],
        ['id' => 6, 'name' => 'تاجر مبادلة', 'icon' => '🔄', 'cost' => intval($cfg['plus6']), 'duration' => 'فوري', 'type' => 'instant'],
        ['id' => 7, 'name' => 'إنهاء التدريب فوراً', 'icon' => '⚔️', 'cost' => intval($cfg['plus7']), 'duration' => 'فوري', 'type' => 'instant'],
        ['id' => 8, 'name' => 'هجوم +20%', 'icon' => '🗡️', 'cost' => intval($cfg['plus_by_abdullah_1_2']), 'duration' => $cfg['plus_by_abdullah_1_1'] . ' يوم', 'type' => 'duration'],
        ['id' => 9, 'name' => 'دفاع +20%', 'icon' => '🛡️', 'cost' => intval($cfg['plus_by_abdullah_2_2']), 'duration' => $cfg['plus_by_abdullah_2_1'] . ' يوم', 'type' => 'duration'],
        ['id' => 10, 'name' => 'حماية المنجنيق +20%', 'icon' => '🏰', 'cost' => intval($cfg['plus_by_abdullah_3_2']), 'duration' => $cfg['plus_by_abdullah_3_1'] . ' يوم', 'type' => 'duration'],
        ['id' => 11, 'name' => 'سرعة القوات +30%', 'icon' => '🏃', 'cost' => intval($cfg['plus_by_abdullah_4_2']), 'duration' => $cfg['plus_by_abdullah_4_1'] . ' يوم', 'type' => 'duration'],
        ['id' => 13, 'name' => 'حمولة القوات +20%', 'icon' => '📦', 'cost' => intval($cfg['plus_by_abdullah_6_2']), 'duration' => $cfg['plus_by_abdullah_6_1'] . ' يوم', 'type' => 'duration'],
    ];

    
    jsonSuccess([
        'gold' => intval($player['gold_num']),
        'silver' => intval($player['silver_num']),
        'plus_active' => intval($player['active_plus_account']),
        'features' => $features,
        'active_features' => $activeFeatures,
    ]);
}

if ($action === 'packages') {
    $packages = $GLOBALS['AppConfig']['plus']['packages'] ?? [];
    $result = [];
    foreach ($packages as $i => $pkg) {
        $result[] = [
            'index' => $i,
            'name' => $pkg['name'] ?? '',
            'gold' => intval($pkg['gold'] ?? 0),
            'gold_plus' => intval($pkg['goldplus'] ?? 0),
            'plus_days' => intval($pkg['plus'] ?? 0),
            'cost' => floatval($pkg['cost'] ?? 0),
            'currency' => $pkg['currency'] ?? 'usd',
            'image' => $pkg['image'] ?? '',
        ];
    }

    $payments = [];
    $paymentsCfg = $GLOBALS['AppConfig']['plus']['payments'] ?? [];
    foreach ($paymentsCfg as $key => $pay) {
        $payments[] = [
            'key' => $key,
            'name' => $pay['name'] ?? $key,
            'image' => $pay['image'] ?? '',
        ];
    }

    
    jsonSuccess([
        'packages' => $result,
        'payment_methods' => $payments,
    ]);
}


jsonError('Invalid action. Use: status, packages', 400);
