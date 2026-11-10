<?php
include_once(__DIR__ . '/../../common.php');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

function slot_json_response($status, $payload)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (empty($is_member)) {
    slot_json_response(401, ['ok' => false, 'message' => '로그인 후 이용해 주세요.']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    slot_json_response(405, ['ok' => false, 'message' => '허용되지 않은 요청입니다.']);
}
$provided_token = isset($_POST['csrf']) ? (string)$_POST['csrf'] : '';
$session_token = isset($_SESSION['slot_csrf']) ? (string)$_SESSION['slot_csrf'] : '';
if ($session_token === '' || $provided_token === '' || !hash_equals($session_token, $provided_token)) {
    slot_json_response(403, ['ok' => false, 'message' => '보안 토큰이 만료되었습니다. 페이지를 새로고침해 주세요.']);
}
$bet = filter_var($_POST['bet'] ?? null, FILTER_VALIDATE_INT);
if ($bet === false || $bet === null || $bet < 20 || $bet > 300 || $bet % 20 !== 0) {
    slot_json_response(422, ['ok' => false, 'message' => '베팅 금액은 20~300 사이의 20 단위여야 합니다.']);
}

$table = G5_TABLE_PREFIX . 'slot_member';
$mb_id = sql_escape_string($member['mb_id']);
$multipliers = [5, 8, 10, 12, 15, 20, 25, 30, 40, 50, 100];

try {
    sql_query('START TRANSACTION');
    sql_query("INSERT IGNORE INTO `{$table}` (`mb_id`, `balance`) VALUES ('{$mb_id}', 10000)");
    $slot_user = sql_fetch("SELECT `balance`, `rounds` FROM `{$table}` WHERE `mb_id` = '{$mb_id}' FOR UPDATE");
    if (!$slot_user) {
        sql_query('ROLLBACK');
        slot_json_response(500, ['ok' => false, 'message' => '게임 포인트 정보를 읽지 못했습니다. 설치 안내를 확인해 주세요.']);
    }
    $balance = (int)$slot_user['balance'];
    if ($balance < $bet) {
        sql_query('ROLLBACK');
        slot_json_response(422, ['ok' => false, 'message' => '게임 포인트가 부족합니다.']);
    }

    $symbols = [];
    for ($i = 0; $i < 9; $i++) {
        $symbols[] = random_int(0, 10);
    }
    $lines = [];
    $win = 0;
    for ($row = 0; $row < 3; $row++) {
        $offset = $row * 3;
        if ($symbols[$offset] === $symbols[$offset + 1] && $symbols[$offset + 1] === $symbols[$offset + 2]) {
            $lines[] = $row + 1;
            $win += $bet * $multipliers[$symbols[$offset]];
        }
    }
    $new_balance = $balance - $bet + $win;
    $rounds = (int)$slot_user['rounds'] + 1;
    sql_query("UPDATE `{$table}` SET `balance` = {$new_balance}, `rounds` = {$rounds}, `total_wagered` = `total_wagered` + {$bet}, `total_won` = `total_won` + {$win}, `updated_at` = NOW() WHERE `mb_id` = '{$mb_id}'");
    sql_query('COMMIT');
    $message = $win > 0 ? '축하합니다! 게임 포인트를 획득했습니다.' : '다음 기회를 노려보세요.';
    slot_json_response(200, [
        'ok' => true,
        'symbols' => $symbols,
        'lines' => $lines,
        'bet' => (int)$bet,
        'win' => $win,
        'balance' => $new_balance,
        'rounds' => $rounds,
        'message' => $message
    ]);
} catch (Throwable $e) {
    @sql_query('ROLLBACK');
    slot_json_response(500, ['ok' => false, 'message' => '게임 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.']);
}
