<?php
if (!defined('_GNUBOARD_')) exit;
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css">', 0);

$event_start = isset($write['wr_1']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $write['wr_1']) ? $write['wr_1'] : '';
$event_end = isset($write['wr_2']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $write['wr_2']) ? $write['wr_2'] : '';
?>
<section class="evt-write">
    <a class="evt-back" href="<?=get_pretty_url($bo_table)?>">← 이벤트 목록</a>
    <header class="evt-write__head">
        <p class="evt-eyebrow">EVENT EDITOR</p>
        <h2><?=$w === 'u' ? '이벤트 수정' : '이벤트 등록'?></h2>
        <p>이미지와 기간을 입력해 이벤트를 게시하세요.</p>
    </header>

    <form name="fwrite" id="fwrite" class="evt-form" action="<?=$action_url?>" onsubmit="return event_write_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="uid" value="<?=get_uniqid()?>">
        <input type="hidden" name="w" value="<?=htmlspecialchars($w, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="bo_table" value="<?=htmlspecialchars($bo_table, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="wr_id" value="<?=htmlspecialchars((string)$wr_id, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="sca" value="<?=htmlspecialchars($sca, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="sfl" value="<?=htmlspecialchars($sfl, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="stx" value="<?=htmlspecialchars($stx, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="spt" value="<?=htmlspecialchars($spt, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="sst" value="<?=htmlspecialchars($sst, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="sod" value="<?=htmlspecialchars($sod, ENT_QUOTES, G5_CHARSET)?>">
        <input type="hidden" name="page" value="<?=htmlspecialchars((string)$page, ENT_QUOTES, G5_CHARSET)?>">
        <?=$option_hidden ?? ''?>

        <?php if ($is_category) { ?>
            <div class="evt-field"><label for="ca_name">분류</label><select name="ca_name" id="ca_name" required><option value="">분류를 선택하세요</option><?=$category_option?></select></div>
        <?php } ?>

        <?php if ($is_name || $is_password || $is_email || $is_homepage) { ?>
            <div class="evt-field-grid">
                <?php if ($is_name) { ?><div class="evt-field"><label for="wr_name">이름</label><input type="text" name="wr_name" id="wr_name" value="<?=htmlspecialchars($name, ENT_QUOTES, G5_CHARSET)?>" required></div><?php } ?>
                <?php if ($is_password) { ?><div class="evt-field"><label for="wr_password">비밀번호</label><input type="password" name="wr_password" id="wr_password" <?=$password_required?>></div><?php } ?>
                <?php if ($is_email) { ?><div class="evt-field"><label for="wr_email">이메일</label><input type="email" name="wr_email" id="wr_email" value="<?=htmlspecialchars($email, ENT_QUOTES, G5_CHARSET)?>"></div><?php } ?>
                <?php if ($is_homepage) { ?><div class="evt-field"><label for="wr_homepage">홈페이지</label><input type="url" name="wr_homepage" id="wr_homepage" value="<?=htmlspecialchars($homepage, ENT_QUOTES, G5_CHARSET)?>"></div><?php } ?>
            </div>
        <?php } ?>

        <?php if ($is_notice || $is_html || $is_secret || $is_mail) { ?>
            <div class="evt-options">
                <?php if ($is_notice) { ?><label><input type="checkbox" name="notice" value="1" <?=$notice_checked?>> 공지</label><?php } ?>
                <?php if ($is_html && !$is_dhtml_editor) { ?><label><input type="checkbox" name="html" value="<?=$html_value?>" <?=$html_checked?>> HTML</label><?php } ?>
                <?php if ($is_secret && ($is_admin || $is_secret == 1)) { ?><label><input type="checkbox" name="secret" value="secret" <?=$secret_checked?>> 비밀글</label><?php } ?>
                <?php if ($is_mail) { ?><label><input type="checkbox" name="mail" value="mail" <?=$recv_email_checked?>> 답변 메일 받기</label><?php } ?>
            </div>
            <?php if ($is_html && $is_dhtml_editor) { ?><input type="hidden" name="html" value="html1"><?php } ?>
            <?php if ($is_secret && !$is_admin && $is_secret != 1) { ?><input type="hidden" name="secret" value="secret"><?php } ?>
        <?php } ?>

        <div class="evt-field">
            <label for="wr_subject">이벤트 제목 <span aria-hidden="true">*</span></label>
            <input type="text" name="wr_subject" id="wr_subject" value="<?=$subject?>" maxlength="255" required placeholder="예: 가을 축제 인증 이벤트">
        </div>

        <div class="evt-field">
            <span class="evt-label">이벤트 기간</span>
            <div class="evt-date-row">
                <label class="sound_only" for="event-start">시작일</label>
                <input type="date" id="event-start" name="wr_1" value="<?=htmlspecialchars($event_start, ENT_QUOTES, G5_CHARSET)?>">
                <span aria-hidden="true">—</span>
                <label class="sound_only" for="event-end">종료일</label>
                <input type="date" id="event-end" name="wr_2" value="<?=htmlspecialchars($event_end, ENT_QUOTES, G5_CHARSET)?>">
            </div>
            <small>시작일과 종료일을 선택하면 목록에 진행 상태가 표시됩니다.</small>
        </div>

        <?php for ($i = 0; $is_file && $i < $file_count; $i++) { ?>
            <div class="evt-field evt-upload-field">
                <label for="bf_file_<?=$i?>"><?=$i === 0 ? '대표 이미지' : '추가 이미지 ' . $i?></label>
                <div class="evt-upload-box">
                    <input class="evt-file-input" type="file" name="bf_file[]" id="bf_file_<?=$i?>" accept=".jpg,.jpeg,.png,.gif" aria-describedby="evt-upload-help-<?=$i?>">
                    <label for="bf_file_<?=$i?>" class="evt-button evt-button--light">이미지 선택</label>
                    <span class="evt-file-name" id="evt-file-name-<?=$i?>"><?=$w === 'u' && !empty($file[$i]['source']) ? htmlspecialchars($file[$i]['source'], ENT_QUOTES, G5_CHARSET) : 'JPG, PNG, GIF 등의 이미지를 선택하세요.'?></span>
                    <?php if ($w === 'u' && !empty($file[$i]['view'])) { ?><div class="evt-existing-preview"><?=$file[$i]['view']?></div><?php } ?>
                    <div class="evt-image-preview" id="evt-preview-<?=$i?>" aria-live="polite"></div>
                </div>
                <small id="evt-upload-help-<?=$i?>">업로드 가능한 파일 수와 크기는 게시판 관리자 설정을 따릅니다. 서버에서 허용된 이미지 형식만 선택하세요.</small>
                <?php if ($w === 'u' && !empty($file[$i]['file'])) { ?>
                    <label class="evt-delete-file"><input type="checkbox" name="bf_file_del[<?=$i?>]" value="1"> 현재 첨부 삭제: <?=htmlspecialchars($file[$i]['source'], ENT_QUOTES, G5_CHARSET)?></label>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($is_link) { for ($i = 1; $i <= G5_LINK_COUNT; $i++) { ?>
            <div class="evt-field"><label for="wr_link<?=$i?>">관련 링크 <?=$i?></label><input type="url" name="wr_link<?=$i?>" id="wr_link<?=$i?>" value="<?=$w === 'u' ? htmlspecialchars($write['wr_link' . $i], ENT_QUOTES, G5_CHARSET) : ''?>" placeholder="https://"></div>
        <?php } } ?>

        <div class="evt-field">
            <label for="wr_content">이벤트 내용 <span aria-hidden="true">*</span></label>
            <div class="evt-editor <?=$is_dhtml_editor ? $config['cf_editor'] : ''?>"><?=$editor_html?></div>
        </div>

        <?php if ($is_use_captcha) { ?><div class="evt-captcha"><?=$captcha_html?></div><?php } ?>
        <div class="evt-form-actions">
            <a class="evt-button evt-button--light" href="<?=get_pretty_url($bo_table)?>">취소</a>
            <button class="evt-button evt-button--primary" type="submit" id="btn_submit"><?=$w === 'u' ? '수정 완료' : '등록하기'?></button>
        </div>
    </form>
</section>
<script>
(function () {
    document.querySelectorAll('.evt-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var preview = document.getElementById(input.id.replace('bf_file_', 'evt-preview-'));
            var filename = document.getElementById(input.id.replace('bf_file_', 'evt-file-name-'));
            if (!preview || !filename) return;
            preview.textContent = '';
            var file = input.files && input.files[0];
            if (!file) return;
            filename.textContent = file.name;
            if (!file.type || file.type.indexOf('image/') !== 0) {
                preview.textContent = '이미지 파일인지 확인해 주세요.';
                return;
            }
            var img = document.createElement('img');
            img.alt = '선택한 이미지 미리보기';
            img.src = URL.createObjectURL(file);
            img.onload = function () { URL.revokeObjectURL(img.src); };
            preview.appendChild(img);
        });
    });
})();

function event_write_submit(form) {
    <?=$editor_js?>
    if (form.wr_subject.value.trim() === '') {
        alert('이벤트 제목을 입력해 주세요.');
        form.wr_subject.focus();
        return false;
    }
    var start = form.wr_1.value;
    var end = form.wr_2.value;
    if (start && end && start > end) {
        alert('종료일은 시작일보다 빠를 수 없습니다.');
        form.wr_2.focus();
        return false;
    }
    $.ajax({
        url: g5_bbs_url + '/ajax.filter.php',
        type: 'POST',
        data: {subject: form.wr_subject.value, content: form.wr_content.value},
        dataType: 'json',
        async: false,
        cache: false,
        success: function (data) {
            if (data.subject || data.content) {
                alert('제목 또는 내용에 사용할 수 없는 단어가 있습니다.');
                form.dataset.filtered = '1';
            }
        }
    });
    if (form.dataset.filtered === '1') {
        delete form.dataset.filtered;
        return false;
    }
    <?=$captcha_js?>
    document.getElementById('btn_submit').disabled = true;
    return true;
}
</script>
