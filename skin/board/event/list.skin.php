<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH . '/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);

$today = G5_TIME_YMD;
$event_state = function ($start, $end) use ($today) {
    $is_date = function ($value) {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    };
    if (!$is_date($start) && !$is_date($end)) return ['기간 미정', 'unknown'];
    if ($is_date($start) && $start > $today) return ['예정', 'upcoming'];
    if ($is_date($end) && $end < $today) return ['종료', 'ended'];
    return ['진행 중', 'active'];
};
?>
<section class="evt-board" aria-labelledby="evt-board-title">
    <header class="evt-board__head">
        <div>
            <p class="evt-eyebrow">EVENTS &amp; BENEFITS</p>
            <h2 id="evt-board-title"><?=htmlspecialchars($board['bo_subject'], ENT_QUOTES, G5_CHARSET)?></h2>
            <p class="evt-subtitle">놓치지 말아야 할 소식과 혜택을 확인해 보세요.</p>
        </div>
        <?php if ($write_href) { ?>
            <a class="evt-button evt-button--primary" href="<?=htmlspecialchars($write_href, ENT_QUOTES, G5_CHARSET)?>">이벤트 등록</a>
        <?php } ?>
    </header>

    <?php if ($is_category) { ?>
        <nav class="evt-categories" aria-label="이벤트 분류">
            <?=$category_option?>
        </nav>
    <?php } ?>

    <form class="evt-search" method="get" action="<?=G5_BBS_URL?>/board.php">
        <input type="hidden" name="bo_table" value="<?=htmlspecialchars($bo_table, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="sfl" value="wr_subject||wr_content">
        <label class="sound_only" for="evt-search-input">이벤트 검색</label>
        <input id="evt-search-input" type="search" name="stx" value="<?=htmlspecialchars($stx, ENT_QUOTES, G5_CHARSET)?>" placeholder="이벤트를 검색하세요">
        <button class="evt-button evt-button--light" type="submit">검색</button>
    </form>

    <div class="evt-board__meta">
        <span>전체 <strong><?=number_format($total_count)?></strong>개</span>
        <span><?=number_format($page)?> 페이지</span>
    </div>

    <?php if (count($list)) { ?>
        <div class="evt-grid">
            <?php foreach ($list as $item) {
                $thumb = get_list_thumbnail($board['bo_table'], $item['wr_id'], 720, 420, false, true, 'center');
                $status = $event_state($item['wr_1'] ?? '', $item['wr_2'] ?? '');
                $start = isset($item['wr_1']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $item['wr_1']) ? $item['wr_1'] : '';
                $end = isset($item['wr_2']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $item['wr_2']) ? $item['wr_2'] : '';
                $date_label = $start && $end ? $start . ' — ' . $end : ($start ? $start . ' 시작' : ($end ? $end . ' 종료' : '기간 미정'));
                $summary = cut_str(strip_tags($item['wr_content'] ?? ''), 110, '…');
            ?>
                <article class="evt-card">
                    <a class="evt-card__link" href="<?=htmlspecialchars($item['href'], ENT_QUOTES, G5_CHARSET)?>">
                        <div class="evt-card__image">
                            <?php if (!empty($thumb['src'])) { ?>
                                <img src="<?=htmlspecialchars($thumb['src'], ENT_QUOTES, G5_CHARSET)?>" alt="<?=htmlspecialchars($thumb['alt'] ?: $item['subject'], ENT_QUOTES, G5_CHARSET)?>" loading="lazy">
                            <?php } else { ?>
                                <div class="evt-card__placeholder" aria-label="첨부 이미지 없음"><span>EVENT</span><b>이미지를<br>첨부해 주세요</b></div>
                            <?php } ?>
                            <span class="evt-badge evt-badge--<?=htmlspecialchars($status[1], ENT_QUOTES, G5_CHARSET)?>"><?=htmlspecialchars($status[0], ENT_QUOTES, G5_CHARSET)?></span>
                        </div>
                        <div class="evt-card__body">
                            <?php if (!empty($item['ca_name'])) { ?><span class="evt-card__category"><?=htmlspecialchars($item['ca_name'], ENT_QUOTES, G5_CHARSET)?></span><?php } ?>
                            <h3><?=$item['subject']?></h3>
                            <?php if ($summary !== '') { ?><p class="evt-card__summary"><?=htmlspecialchars($summary, ENT_QUOTES, G5_CHARSET)?></p><?php } ?>
                            <p class="evt-card__date"><span aria-hidden="true">▦</span> <?=htmlspecialchars($date_label, ENT_QUOTES, G5_CHARSET)?></p>
                            <div class="evt-card__footer"><span><?=$item['name']?></span><span>조회 <?=number_format((int)$item['wr_hit'])?></span></div>
                        </div>
                    </a>
                </article>
            <?php } ?>
        </div>
        <nav class="evt-pagination" aria-label="게시판 페이지">
            <?=$write_pages?>
        </nav>
    <?php } else { ?>
        <div class="evt-empty"><strong>아직 등록된 이벤트가 없습니다.</strong><p>첫 번째 이벤트를 등록해 보세요.</p></div>
    <?php } ?>
</section>
