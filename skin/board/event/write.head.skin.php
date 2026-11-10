<?php
if (!defined('_GNUBOARD_')) exit;

// 수정 화면 자체를 보호합니다. 화면의 수정 링크 숨김은 보안 경계가 아닙니다.
if (isset($w) && $w === 'u' && !in_array($is_admin ?? '', ['super', 'group', 'board'], true)) {
    alert('이벤트 게시글 수정은 관리자만 할 수 있습니다.');
}
