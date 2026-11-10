<?php
if (!defined('_GNUBOARD_')) exit;
include_once(G5_LIB_PATH . '/thumbnail.lib.php');
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);
$event_admin = in_array($is_admin ?? '', ['super', 'group', 'board'], true);

$valid_event_date = function ($value) {
    return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
};
$event_start = $valid_event_date($view['wr_1'] ?? '');
$event_end = $valid_event_date($view['wr_2'] ?? '');
$today = G5_TIME_YMD;
if (!$event_start && !$event_end) {
    $event_status = ['기간 미정', 'unknown'];
} elseif ($event_start && $event_start > $today) {
    $event_status = ['예정', 'upcoming'];
} elseif ($event_end && $event_end < $today) {
    $event_status = ['종료', 'ended'];
} else {
    $event_status = ['진행 중', 'active'];
}
?>
<article class="evt-view">
    <header class="evt-view__head">
        <a class="evt-back" href="<?=htmlspecialchars($list_href, ENT_QUOTES, G5_CHARSET)?>">← 이벤트 목록</a>
        <div class="evt-view__title-row">
            <div>
                <span class="evt-badge evt-badge--<?=htmlspecialchars($event_status[1], ENT_QUOTES, G5_CHARSET)?>"><?=htmlspecialchars($event_status[0], ENT_QUOTES, G5_CHARSET)?></span>
                <?php if (!empty($view['ca_name'])) { ?><span class="evt-card__category"><?=htmlspecialchars($view['ca_name'], ENT_QUOTES, G5_CHARSET)?></span><?php } ?>
                <h2><?=htmlspecialchars($view['wr_subject'], ENT_QUOTES, G5_CHARSET)?></h2>
            </div>
            <div class="evt-view__actions">
                <?php if ($event_admin && $update_href) { ?><a class="evt-button evt-button--light" href="<?=htmlspecialchars($update_href, ENT_QUOTES, G5_CHARSET)?>">수정</a><?php } ?>
                <?php if ($delete_href) { ?><a class="evt-button evt-button--light" href="<?=htmlspecialchars($delete_href, ENT_QUOTES, G5_CHARSET)?>" onclick="return confirm('이 이벤트를 삭제하시겠습니까?');">삭제</a><?php } ?>
            </div>
        </div>
        <div class="evt-view__meta">
            <span><?=$view['name']?></span>
            <span><?=htmlspecialchars($view['datetime'], ENT_QUOTES, G5_CHARSET)?></span>
            <span>조회 <?=number_format((int)$view['wr_hit'])?></span>
        </div>
        <?php if ($event_start || $event_end) { ?>
            <div class="evt-period"><strong>이벤트 기간</strong><span><?=htmlspecialchars($event_start ?: '시작일 미정', ENT_QUOTES, G5_CHARSET)?> — <?=htmlspecialchars($event_end ?: '종료일 미정', ENT_QUOTES, G5_CHARSET)?></span></div>
        <?php } ?>
    </header>

    <?php if (!empty($view['file']['count']) && is_array($view['file'])) { ?>
        <section class="evt-view__images" aria-label="이벤트 첨부 이미지">
            <?php foreach ($view['file'] as $event_file) {
                if (is_array($event_file) && !empty($event_file['source'])) echo get_file_thumbnail($event_file);
            } ?>
        </section>
    <?php } ?>

    <section class="evt-view__content">
        <h3 class="sound_only">이벤트 내용</h3>
        <?=get_view_thumbnail($view['content'])?>
    </section>

    <footer class="evt-view__bottom">
        <a class="evt-button evt-button--light" href="<?=htmlspecialchars($list_href, ENT_QUOTES, G5_CHARSET)?>">목록으로</a>
        <?php if ($write_href) { ?><a class="evt-button evt-button--primary" href="<?=htmlspecialchars($write_href, ENT_QUOTES, G5_CHARSET)?>">이벤트 등록</a><?php } ?>
    </footer>

    <?php include_once(G5_BBS_PATH . '/view_comment.php'); ?>
</article>
