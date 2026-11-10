<?php
if (!defined('_GNUBOARD_')) exit;

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
