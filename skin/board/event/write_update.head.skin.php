<?php
if (!defined('_GNUBOARD_')) exit;

$event_admin = in_array($is_admin ?? '', ['super', 'group', 'board'], true);
if (isset($w) && $w === 'u' && !$event_admin) {
    alert('이벤트 게시글 수정은 관리자만 할 수 있습니다.');
}

// 브라우저의 type/accept 검사는 우회 가능하므로 서버에서도 이벤트 첨부를 이미지로 제한합니다.
if (isset($_FILES['bf_file'])) {
    $event_upload = $_FILES['bf_file'];
    if (!is_array($event_upload) || !isset($event_upload['name'], $event_upload['tmp_name'], $event_upload['error'])
        || !is_array($event_upload['name']) || !is_array($event_upload['tmp_name']) || !is_array($event_upload['error'])) {
        alert('첨부 파일 요청 형식이 올바르지 않습니다.');
    }

    $event_image_types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    foreach ($event_upload['name'] as $index => $original_name) {
        if (!is_string($original_name) || !array_key_exists($index, $event_upload['error'])
            || !array_key_exists($index, $event_upload['tmp_name'])) {
            alert('첨부 파일 정보가 올바르지 않습니다.');
        }

        $upload_error = (int) $event_upload['error'][$index];
        if ($upload_error === UPLOAD_ERR_NO_FILE || $upload_error !== UPLOAD_ERR_OK) {
            continue; // 업로드 오류는 그누보드 코어가 기존 절차에 따라 처리합니다.
        }

        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $temporary_path = $event_upload['tmp_name'][$index];
        if (!isset($event_image_types[$extension]) || !is_string($temporary_path)
            || !is_uploaded_file($temporary_path)) {
            alert('JPG, PNG, GIF 형식의 이미지만 첨부할 수 있습니다.');
        }

        $image_info = @getimagesize($temporary_path);
        if (!$image_info || empty($image_info['mime']) || $image_info['mime'] !== $event_image_types[$extension]) {
            alert('파일 확장자와 실제 이미지 형식이 일치하지 않습니다.');
        }
    }
}

$event_dates = [];
foreach (['wr_1' => '시작일', 'wr_2' => '종료일'] as $field => $label) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    if ($value === '') {
        $event_dates[$field] = '';
        continue;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    $errors = DateTimeImmutable::getLastErrors();
    if (!$date || ($errors && ($errors['warning_count'] || $errors['error_count'])) || $date->format('Y-m-d') !== $value) {
        alert($label . ' 형식이 올바르지 않습니다.');
    }
    $event_dates[$field] = $value;
}

if ($event_dates['wr_1'] !== '' && $event_dates['wr_2'] !== '' && $event_dates['wr_1'] > $event_dates['wr_2']) {
    alert('종료일은 시작일보다 빠를 수 없습니다.');
}
