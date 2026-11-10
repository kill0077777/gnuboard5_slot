<?php
include_once(__DIR__ . '/../../common.php');

if (empty($is_member)) {
    $login_url = G5_BBS_URL . '/login.php?url=' . urlencode(G5_URL . $_SERVER['REQUEST_URI']);
    goto_url($login_url);
}

$table = G5_TABLE_PREFIX . 'slot_member';
$mb_id = sql_escape_string($member['mb_id']);
sql_query("INSERT IGNORE INTO `{$table}` (`mb_id`, `balance`) VALUES ('{$mb_id}', 10000)");
$slot_user = sql_fetch("SELECT `balance`, `rounds`, `total_wagered`, `total_won` FROM `{$table}` WHERE `mb_id` = '{$mb_id}'");
if (!$slot_user) {
    alert('슬롯 게임 테이블을 찾을 수 없습니다. plugin/slot/README.md의 설치 안내를 확인해 주세요.');
}

if (empty($_SESSION['slot_csrf'])) {
    $_SESSION['slot_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['slot_csrf'];
$balance = (int)$slot_user['balance'];
$slot_assets = G5_PLUGIN_URL . '/slot/images';
$g5['title'] = '인디 슬롯';
include_once(G5_PATH . '/head.php');
?>
<style>
.slot-page{max-width:860px;margin:32px auto;padding:20px;color:#222;font-family:inherit}
.slot-page *{box-sizing:border-box}
.slot-shell{display:grid;grid-template-columns:minmax(260px,360px) minmax(250px,1fr);gap:28px;align-items:center;justify-content:center}
.slot-machine{position:relative;width:100%;max-width:360px;aspect-ratio:260/510;margin:auto;background:url('<?=htmlspecialchars($slot_assets, ENT_QUOTES, 'UTF-8')?>/slotmain.jpg') center/100% 100% no-repeat}
.slot-reels{position:absolute;left:15.5%;top:21.1%;width:69%;height:38.5%;display:grid;grid-template-columns:repeat(3,1fr);grid-template-rows:repeat(3,1fr);overflow:hidden}
.slot-cell{display:flex;align-items:center;justify-content:center;min-width:0;min-height:0}
.slot-cell img{width:min(80%,50px);height:auto;image-rendering:auto;filter:drop-shadow(0 1px 1px #777)}
.slot-machine-stats{position:absolute;left:17%;top:69%;width:66%;height:17%;display:flex;flex-direction:column;justify-content:center;gap:3px;text-align:center;font-size:clamp(10px,2.5vw,14px);font-weight:700;color:#4b270f}
.slot-panel{padding:24px;border:1px solid #ddd;border-radius:14px;background:#fff;box-shadow:0 5px 20px #0000000d}
.slot-panel h2{margin:0 0 12px;font-size:24px}
.slot-balance{font-size:18px;margin-bottom:20px}.slot-balance strong{font-size:25px;color:#9b2c22}
.slot-bet{display:flex;align-items:center;gap:10px;margin:14px 0}
.slot-bet button,.slot-spin{border:0;border-radius:8px;padding:11px 15px;cursor:pointer;font-size:16px}
.slot-bet button{background:#eee}.slot-spin{display:flex;align-items:center;gap:10px;background:#8a211b;color:white;font-weight:700}
.slot-spin img{width:34px;height:34px}.slot-message{min-height:48px;margin:16px 0;padding:12px;border-radius:8px;background:#f7f4ef;line-height:1.5}
.slot-help{font-size:13px;line-height:1.65;color:#555}.slot-paytable{width:100%;border-collapse:collapse;font-size:13px;margin-top:12px}.slot-paytable th,.slot-paytable td{padding:6px;border-bottom:1px solid #eee;text-align:left}
@media(max-width:680px){.slot-page{padding:12px}.slot-shell{grid-template-columns:1fr;gap:20px}.slot-machine{max-width:min(360px,90vw)}.slot-panel{padding:18px}}
</style>
<main class="slot-page">
  <div class="slot-shell">
    <section class="slot-machine" aria-label="슬롯 머신">
      <div class="slot-reels" id="slot-reels" aria-live="polite">
        <?php for ($i = 0; $i < 9; $i++): ?>
          <div class="slot-cell"><img src="<?=htmlspecialchars($slot_assets, ENT_QUOTES, 'UTF-8')?>/spin<?=($i % 11)?>.gif" alt="슬롯 기호"></div>
        <?php endfor; ?>
      </div>
      <div class="slot-machine-stats">
        <div>보유 포인트: <span id="machine-balance"><?=number_format($balance)?></span></div>
        <div>플레이: <span id="machine-rounds"><?=number_format((int)$slot_user['rounds'])?></span>회</div>
      </div>
    </section>
    <section class="slot-panel">
      <h2>인디 슬롯</h2>
      <div class="slot-balance">게임 포인트 <strong id="balance"><?=number_format($balance)?></strong></div>
      <p>베팅 금액</p>
      <div class="slot-bet">
        <button type="button" id="bet-down" aria-label="베팅 금액 줄이기">−</button>
        <strong><span id="bet-value">20</span> 포인트</strong>
        <button type="button" id="bet-up" aria-label="베팅 금액 늘리기">＋</button>
      </div>
      <button class="slot-spin" type="button" id="spin-button"><img src="<?=htmlspecialchars($slot_assets, ENT_QUOTES, 'UTF-8')?>/spin.gif" alt=""> 스핀</button>
      <div class="slot-message" id="message" role="status">같은 줄에 같은 기호 3개가 나오면 포인트를 획득합니다.</div>
      <div class="slot-help"><strong>지급표</strong>
        <table class="slot-paytable"><thead><tr><th>기호 번호</th><th>3개 일치 지급</th></tr></thead><tbody>
        <?php foreach ([0 => 5, 1 => 8, 2 => 10, 3 => 12, 4 => 15, 5 => 20, 6 => 25, 7 => 30, 8 => 40, 9 => 50, 10 => 100] as $symbol => $multiplier): ?>
          <tr><td><img src="<?=htmlspecialchars($slot_assets, ENT_QUOTES, 'UTF-8')?>/spin<?=$symbol?>.gif" width="24" height="24" alt="기호 <?=$symbol?>"> 기호 <?=$symbol?></td><td>베팅액 × <?=$multiplier?></td></tr>
        <?php endforeach; ?>
        </tbody></table>
        <p>게임 포인트는 사이트 내 게임용 수치이며 현금 가치나 환전 기능이 없습니다.</p>
      </div>
    </section>
  </div>
</main>
<script>
(() => {
  const token = <?=json_encode($csrf, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  const endpoint = <?=json_encode(G5_PLUGIN_URL . '/slot/play.php', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  const asset = <?=json_encode($slot_assets, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
  const cells = [...document.querySelectorAll('.slot-cell img')];
  const betText = document.getElementById('bet-value');
  const message = document.getElementById('message');
  const spinButton = document.getElementById('spin-button');
  let bet = 20;
  document.getElementById('bet-down').addEventListener('click', () => { bet = Math.max(20, bet - 20); betText.textContent = bet; });
  document.getElementById('bet-up').addEventListener('click', () => { bet = Math.min(300, bet + 20); betText.textContent = bet; });
  spinButton.addEventListener('click', async () => {
    spinButton.disabled = true;
    message.textContent = '슬롯을 돌리는 중입니다…';
    try {
      const body = new URLSearchParams({csrf: token, bet: String(bet)});
      const response = await fetch(endpoint, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body});
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || '게임 요청에 실패했습니다.');
      result.symbols.forEach((symbol, index) => { cells[index].src = `${asset}/spin${symbol}.gif`; });
      document.getElementById('balance').textContent = Number(result.balance).toLocaleString('ko-KR');
      document.getElementById('machine-balance').textContent = Number(result.balance).toLocaleString('ko-KR');
      document.getElementById('machine-rounds').textContent = Number(result.rounds).toLocaleString('ko-KR');
      const lineText = result.lines.length ? `당첨 줄 ${result.lines.join(', ')}번` : '당첨 줄 없음';
      message.textContent = `${lineText} · 베팅 ${Number(result.bet).toLocaleString('ko-KR')}P · 지급 ${Number(result.win).toLocaleString('ko-KR')}P · ${result.message}`;
    } catch (error) {
      message.textContent = error.message;
    } finally {
      spinButton.disabled = false;
    }
  });
})();
</script>
<?php include_once(G5_PATH . '/tail.php'); ?>
