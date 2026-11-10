-- 그누보드5 인디 슬롯 게임 데이터 테이블
-- 기본 테이블 접두사(g5_)를 사용하지 않는 사이트는 g5_slot_member의 g5_를 실제 접두사로 변경하세요.
-- 기존 그누보드 회원 포인트(mb_point)와 분리하여 저장합니다.
CREATE TABLE IF NOT EXISTS `g5_slot_member` (
  `mb_id` varchar(20) NOT NULL,
  `balance` bigint NOT NULL DEFAULT 10000,
  `rounds` bigint unsigned NOT NULL DEFAULT 0,
  `total_wagered` bigint unsigned NOT NULL DEFAULT 0,
  `total_won` bigint unsigned NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
