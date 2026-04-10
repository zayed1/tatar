<?php
require_once(__DIR__ . '/config.php');
require_once(dirname(__DIR__) . '/core-f/config-f/s1.php');
require_once(dirname(__DIR__) . '/core-f/metadata.php');

$auth = requireAuth();
$playerId = intval($auth['id']);
$db = getDb();

$action = $_GET['action'] ?? 'buildings';

// Building names
$BNAMES = [
    1=>'حطّاب',2=>'منجم طين',3=>'منجم حديد',4=>'مزرعة قمح',5=>'منشرة خشب',
    6=>'مصنع طوب',7=>'مسبك حديد',8=>'طاحونة',9=>'مخبز',10=>'مخزن',
    11=>'مخزن حبوب',12=>'حداد',13=>'مستودع أسلحة',14=>'ميدان تدريب',
    15=>'ثكنة',16=>'إسطبل',17=>'سوق',18=>'سفارة',19=>'ثكنة كبيرة',
    20=>'إسطبل كبير',21=>'سور المدينة',22=>'سور الأرض',23=>'سور الحصن',
    24=>'حصن',25=>'قصر',26=>'قاعة المدينة',27=>'أكاديمية',28=>'مكتب التجار',
    29=>'ثكنة التجار',30=>'مخبأ',31=>'فخاخ',32=>'بطل المنزل',33=>'مستشفى',
    34=>'معجزة العالم',35=>'حصن الأسوار',36=>'خيمة المحاربين',37=>'قلعة الماء',
    38=>'حظيرة',39=>'قبو',40=>'مكتب الخرائط'
];

if ($action === 'buildings') {
    $vid = intval($_GET['vid'] ?? 0);

    // Get village
    if ($vid <= 0) {
        $pResult = mysqli_query($db, "SELECT selected_village_id FROM p_players WHERE id = $playerId");
        $vid = intval(mysqli_fetch_assoc($pResult)['selected_village_id']);
    }

    $vResult = mysqli_query($db, "SELECT buildings, village_name FROM p_villages WHERE id = $vid AND player_id = $playerId");
    $village = mysqli_fetch_assoc($vResult);
    if (!$village) { jsonError('Village not found', 404); }

    // Parse buildings
    $buildings = [];
    $bStr = $village['buildings'] ?? '';
    if ($bStr !== '') {
        $slots = explode(',', $bStr);
        $slot = 0;
        foreach ($slots as $entry) {
            $parts = explode(' ', trim($entry));
            $bid = intval($parts[0] ?? 0);
            $level = intval($parts[1] ?? 0);
            $slot++;
            if ($bid <= 0) continue;
            $buildings[] = [
                'slot' => $slot,
                'id' => $bid,
                'name' => $BNAMES[$bid] ?? "مبنى #$bid",
                'level' => $level,
            ];
        }
    }

    // Get active queue for this village (building)
    $qResult = mysqli_query($db, "
        SELECT q.id, q.proc_type, q.building_id, q.end_date,
               GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), q.end_date)) as remaining_sec
        FROM p_queue q
        WHERE q.village_id = $vid AND q.player_id = $playerId
              AND q.proc_type IN (2,3)
        ORDER BY q.end_date ASC
    ");
    $queue = [];
    while ($q = mysqli_fetch_assoc($qResult)) {
        $queue[] = [
            'id' => intval($q['id']),
            'type' => intval($q['proc_type']) === 2 ? 'upgrade' : 'demolish',
            'remaining_sec' => intval($q['remaining_sec']),
            'end_date' => $q['end_date'],
        ];
    }

    
    jsonSuccess([
        'village_name' => $village['village_name'],
        'buildings' => $buildings,
        'queue' => $queue,
    ]);
}

if ($action === 'troops') {
    $vid = intval($_GET['vid'] ?? 0);
    if ($vid <= 0) {
        $pResult = mysqli_query($db, "SELECT selected_village_id, tribe_id FROM p_players WHERE id = $playerId");
        $pRow = mysqli_fetch_assoc($pResult);
        $vid = intval($pRow['selected_village_id']);
        $tribeId = intval($pRow['tribe_id']);
    } else {
        $tResult = mysqli_query($db, "SELECT tribe_id FROM p_players WHERE id = $playerId");
        $tribeId = intval(mysqli_fetch_assoc($tResult)['tribe_id']);
    }

    $vResult = mysqli_query($db, "SELECT troops_num, troops_training, village_name FROM p_villages WHERE id = $vid AND player_id = $playerId");
    $village = mysqli_fetch_assoc($vResult);
    if (!$village) { jsonError('Village not found', 404); }

    // Parse troops_num
    $troopCounts = [];
    $tStr = $village['troops_num'] ?? '';
    if ($tStr !== '') {
        // Format: "ownerid:tid count,tid count,..."
        $parts = explode(':', $tStr, 2);
        $troopsPart = $parts[1] ?? $parts[0];
        foreach (explode(',', $troopsPart) as $tc) {
            $tp = explode(' ', trim($tc));
            if (count($tp) >= 2) {
                $troopCounts[intval($tp[0])] = intval($tp[1]);
            }
        }
    }

    // Parse troops_training (research status)
    $troopResearch = [];
    $trStr = $village['troops_training'] ?? '';
    if ($trStr !== '') {
        foreach (explode(',', $trStr) as $tr) {
            $tp = explode(' ', trim($tr));
            if (count($tp) >= 2) {
                $troopResearch[intval($tp[0])] = intval($tp[1]); // 1=researched
            }
        }
    }

    // Get troop names from metadata
    $TNAMES = [];
    $tribeStart = ($tribeId - 1) * 10 + 1;
    $tribeEnd = $tribeStart + 9;
    // Arabic troop names by tribe
    $allTNames = [
        1=>'مشاة خفيفة',2=>'مشاة ثقيلة',3=>'رماة',4=>'فارس استطلاع',5=>'فارس ثقيل',
        6=>'كبش',7=>'منجنيق',8=>'سناتور',9=>'مستوطن',10=>'بطل',
        11=>'هراوة',12=>'رجل الرمح',13=>'فأس',14=>'كشاف',15=>'فارس تيوتوني',
        16=>'كبش تيوتوني',17=>'منجنيق تيوتوني',18=>'زعيم',19=>'مستوطن',20=>'بطل',
        21=>'كتيبة',22=>'حامل السيف',23=>'مقاتل',24=>'كشاف مغولي',25=>'فارس مغولي',
        26=>'كبش مغولي',27=>'منجنيق مغولي',28=>'خان',29=>'مستوطن',30=>'بطل',
        31=>'فأس حرب',32=>'مبارز',33=>'حارس',34=>'فارس يوناني',35=>'فارس ثقيل يوناني',
        36=>'كبش يوناني',37=>'منجنيق يوناني',38=>'قائد',39=>'مستوطن',40=>'بطل',
    ];

    $troops = [];
    for ($tid = $tribeStart; $tid <= $tribeEnd; $tid++) {
        $troops[] = [
            'id' => $tid,
            'name' => $allTNames[$tid] ?? "جندي #$tid",
            'count' => $troopCounts[$tid] ?? 0,
            'researched' => ($troopResearch[$tid] ?? 0) === 1,
        ];
    }

    // Get training queue
    $qResult = mysqli_query($db, "
        SELECT q.id, q.proc_params, q.threads,
               GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), q.end_date)) as remaining_sec
        FROM p_queue q
        WHERE q.village_id = $vid AND q.player_id = $playerId AND q.proc_type = 7
        ORDER BY q.end_date ASC
    ");
    $trainingQueue = [];
    while ($q = mysqli_fetch_assoc($qResult)) {
        $troopId = intval($q['proc_params']);
        $trainingQueue[] = [
            'troop_id' => $troopId,
            'troop_name' => $allTNames[$troopId] ?? "جندي #$troopId",
            'count' => intval($q['threads']),
            'remaining_sec' => intval($q['remaining_sec']),
        ];
    }

    
    jsonSuccess([
        'village_name' => $village['village_name'],
        'troops' => $troops,
        'training_queue' => $trainingQueue,
    ]);
}


jsonError('Invalid action. Use: buildings, troops', 400);
