# 그누보드5 이벤트 게시판 스킨

`skin/board/event/`을 그누보드5 사이트의 같은 경로로 복사해 사용하는 이벤트 카드형 게시판 스킨입니다. 목록에서 첨부 이미지 썸네일과 이벤트 기간에 따른 상태(예정·진행 중·종료)를 표시하고, 상세 화면에서 첨부 이미지와 본문을 보여줍니다. 글 등록·수정·삭제 권한은 그누보드5 게시판 권한을 그대로 따릅니다.

## 설치

1. 사이트와 DB를 백업합니다.
2. `skin/board/event/` 디렉터리를 그누보드5 루트의 `skin/board/event/`에 업로드합니다. 테마별 게시판 스킨을 쓰는 경우, 관리자 설정에서 선택하는 스킨 디렉터리의 `event` 폴더에 복사합니다.
3. 관리자 화면의 **게시판 관리 → 게시판 수정**에서 게시판을 생성하거나 선택하고, 게시판 스킨을 `event`로 지정합니다.
4. 글쓰기 권한, 파일 업로드 개수·파일당 용량, 허용할 파일 확장자와 이미지 처리 정책을 관리자에서 설정합니다. 이벤트 게시판 ID는 설치 사이트에서 정하고, 예시 경로는 `/bbs/board.php?bo_table=event`입니다.
5. 로그인 계정에 권한을 부여한 뒤 목록, 이미지 첨부, 수정·삭제 기능을 확인합니다.

별도의 DB 스키마 변경은 필요 없습니다. 이벤트 시작일과 종료일은 그누보드 글의 여분필드 `wr_1`, `wr_2`에 `YYYY-MM-DD` 형식으로 저장합니다. `write_update.head.skin.php`가 서버에서도 날짜 형식과 기간 순서를 검증합니다.

## 이미지 등록·수정

글쓰기에서 대표 이미지와 추가 이미지를 그누보드 기본 파일 첨부 입력으로 업로드합니다. 파일을 선택하면 브라우저에서 미리보기를 표시합니다. 기존 글 수정에서는 새 파일을 선택해 첨부 슬롯을 교체하거나, 현재 첨부 삭제를 선택할 수 있습니다. 실제 파일 검증·저장·삭제는 그누보드5의 기본 `write_update.php` 처리와 게시판 설정을 이용하며, 이 스킨은 별도의 임의 파일 저장 코드를 추가하지 않습니다.

GIF·JPG·PNG 등 실제 허용 확장자 및 용량 제한은 운영 중인 그누보드5의 파일 업로드 설정과 서버 설정을 확인해 정하십시오. 브라우저의 파일 선택 필터는 편의 기능일 뿐 서버 측 파일 검증을 대체하지 않습니다. PHP2Shell에 취약한 PHP 런타임을 쓰지 말고, 배포판이 CVE 패치를 백포트했는지 확인하십시오.

## 참고 자료

- [그누보드5 기본 갤러리 글쓰기 스킨](https://raw.githubusercontent.com/gnuboard/gnuboard5/master/theme/basic/skin/board/gallery/write.skin.php) — 기본 첨부 필드와 수정 시 첨부 삭제 패턴
- [그누보드5 기본 갤러리 목록 스킨](https://raw.githubusercontent.com/gnuboard/gnuboard5/master/theme/basic/skin/board/gallery/list.skin.php) — `get_list_thumbnail()` 사용 패턴
- [그누보드5 기본 갤러리 보기 스킨](https://raw.githubusercontent.com/gnuboard/gnuboard5/master/theme/basic/skin/board/gallery/view.skin.php) — 파일 썸네일과 본문 출력 패턴
- [그누보드5 글쓰기 컨트롤러](https://raw.githubusercontent.com/gnuboard/gnuboard5/master/bbs/write.php) — 게시판 권한, 첨부 변수 및 skin 경로
- [그누보드5 글 저장 컨트롤러](https://raw.githubusercontent.com/gnuboard/gnuboard5/master/bbs/write_update.php) — 기본 업로드 검증과 수정 처리

## 시안 이미지

- [이벤트 목록 시안](images/event-board-list-mockup.png)
- [이벤트 등록·수정 시안](images/event-board-edit-mockup.png)

시안은 디자인 참고용 이미지이며, 실제 동작은 게시판 스킨과 설치한 그누보드5의 버전·테마·권한 설정에 따라 달라질 수 있습니다.
