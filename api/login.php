<?php
require_once(__DIR__ . '/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$name = trim($input['name'] ?? '');
$password = trim($input['password'] ?? '');

if ($name === '' || $password === '') {
    jsonError('اسم المستخدم وكلمة المرور مطلوبان');
}

$db = getDb();
$safeName = mysqli_real_escape_string($db, $name);

// Try by name first, then by email
$result = mysqli_query($db, "SELECT id, name, pwd, is_active, is_blocked, player_type, gold_num, tribe_id FROM p_players WHERE name='$safeName'");
$player = mysqli_fetch_assoc($result);

if (!$player) {
    $result = mysqli_query($db, "SELECT id, name, pwd, is_active, is_blocked, player_type, gold_num, tribe_id FROM p_players WHERE email='$safeName'");
    $player = mysqli_fetch_assoc($result);
}

if (!$player) {
    mysqli_close($db);
    jsonError('اسم المستخدم غير موجود');
}

if (strtolower(md5($password)) !== strtolower($player['pwd'])) {
    mysqli_close($db);
    jsonError('كلمة المرور غير صحيحة');
}

if ($player['is_blocked']) {
    mysqli_close($db);
    jsonError('هذا الحساب محظور');
}

// Update last login
$ip = mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
$playerId = intval($player['id']);
mysqli_query($db, "UPDATE p_players SET last_ip='$ip', last_login_date=NOW() WHERE id=$playerId");

// Get villages
$vResult = mysqli_query($db, "SELECT id, village_name, x, y, is_capital FROM p_villages WHERE player_id=$playerId ORDER BY is_capital DESC, id ASC");
$villages = [];
while ($v = mysqli_fetch_assoc($vResult)) {
    $villages[] = [
        'id' => intval($v['id']),
        'name' => $v['village_name'],
        'x' => intval($v['x']),
        'y' => intval($v['y']),
        'is_capital' => intval($v['is_capital'])
    ];
}

mysqli_close($db);

$token = generateToken($player['id'], $player['pwd']);

jsonSuccess([
    'token' => $token,
    'player' => [
        'id' => intval($player['id']),
        'name' => $player['name'],
        'tribe_id' => intval($player['tribe_id']),
        'gold' => intval($player['gold_num']),
        'is_admin' => intval($player['player_type']) === 2
    ],
    'villages' => $villages
]);
