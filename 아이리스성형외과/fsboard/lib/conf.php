<?
/*************************************************************

	FSBOARD Configuration 0.6

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 9, 2007

	Copyright(c)2000-2007 FSBOARD, All Rights Reserved.

*************************************************************/


/**************************************************
 게시판 기본변수 초기화
**************************************************/

	//include 두번이상 실행 방지
	if($conf_php_executed) return;
	$conf_php_executed = true;

	//기본 변수 에러 방지
	unset($query);
	unset($result);
	unset($rs);
	unset($numrows);
	unset($id);
	unset($mode);
	unset($idx);
	unset($page);
	unset($srhctgr);
	unset($srhstr);
	unset($ctgrstr);

	//게시판 아이디 //$id = $_REQUEST["id"];
	$id = StrAddSlashes($_GET["id"]);

	//실행 모드
	$mode = trim($_GET["mode"]);

	//게시물 고유번호
	$idx = intval($_GET["idx"]);

	//게시판 목록 페이지
	$page = intval($_GET["page"]?$_GET["page"]:1);

	//검색 변수
	$srhctgr = trim($_GET["srhctgr"]?$_GET["srhctgr"]:$_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"]?$_GET["srhstr"]:$_POST["srhstr"]);

	//카테고리 변수
	$ctgrstr = trim($_GET["ctgrstr"]);








/**************************************************
 트랙백 받기
**************************************************/

	if($_POST["mode"]=="trackback" || $mode=="trackback") {
		//DB연결
		if(!$dbConnect) $dbConnect = DbConn();

		$id = $_POST["id"];
		$idx = $_POST["idx"];

		//기본 트랙백핑 변수
		$url = trim($_POST["url"]);
		$title = trim($_POST["title"]);
		$blog_name = trim($_POST["blog_name"]);
		$excerpt = trim($_POST["excerpt"]);

		//트랙백 응답 처리
		receive_tb($id, $idx, $url, $title, $blog_name, $excerpt);

		exit;
	}










/**************************************************
 기본 오류 사항 확인
**************************************************/

	if(IjStr($id)) {
		@ob_end_clean();
		die("게시판 아이디가 잘못되었습니다.");
	}

	if(!IsNum($page)) {
		@ob_end_clean();
		die("페이지 번호가 잘못되었습니다.");
	}

	if($idx!="" && !IsNum($idx)) {
		@ob_end_clean();
		die("인덱스 번호가 잘못되었습니다.");
	}

	if(!$id) {
		//Error("게시판 아이디가 지정되지 않았습니다.");
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">".
			"<html lang=\"ko\">".
			"<head>".
			"<meta http-equiv=\"Refresh\" content=\"1;url=lib/setup.php\" />".
			"</head>".
			"<body>".
			"<div style=\"width:400px; margin:3em auto; padding:1em; border:1px solid #e1e1e1; font-size:13px; text-align:center; line-height:150%;\"><p style=\"margin:0;\">게시판 아이디가 지정되지 않았습니다.</p><p style=\"margin:0;\"><a href=\"lib/setup.php\">도움말 페이지로 이동합니다</a></p></div>".
			"</body>".
			"</html>";
		exit;
	}

	if(IjStr($id)) {
		@ob_end_clean();
		die("게시판 아이디에 잘못된 문자열이 포함되어 있습니다.");
	}

	if(ereg("'", $mode) || ereg("'", $nav)) {
		@ob_end_clean();
		die("처리모드에 잘못된 문자열이 포함되어 있습니다.");
	}

	if(IjStr($ctgrstr)) {
		@ob_end_clean();
		die("카테고리에 잘못된 문자열이 포함되어 있습니다.");
	}










/**************************************************
 게시판 설정 내용 로드
**************************************************/
if($id != "auth") {

	/////DB연결
	if(!$dbConnect) $dbConnect = DbConn();

	//게시판의 관리정보 가져오기
	$query = "SELECT * FROM {$_table_id_admin} WHERE boardId='{$id}';";
	$result = mysql_query($query) or Error(mysql_error());
	$numrows = mysql_num_rows($result);
	if(!$numrows) Error("존재하지 않는 게시판입니다.","/");
	if($result) {
		$rs = mysql_fetch_array($result);

		//MODE 공통
		$aidx				= $rs["aidx"];					//관리 고유번호
		$boardId			= $rs["boardId"];				//게시판 ID
		$boardName			= $rs["boardName"];				//게시판관리용 이름
		$combinedFileName	= $rs["combinedFileName"];		//게시판에 포함되는 디자인 파일
		$adminIDs			= $rs["adminIDs"];				//관리자 아이디 목록
		$adminPasswd		= $rs["adminPasswd"];			//수정,삭제 관리용 암호
		$skin				= $rs["skin"];					//스킨이름
		$todayCount			= $rs["todayCount"];			//오늘 방문수
		$totalCount			= $rs["totalCount"];			//총 방문수
		$totalObj			= $rs["totalObj"];				//게시물수
		$allowNoticeNum		= $rs["allowNoticeNum"];		//공지글 허용 갯수
		$noticeNum			= $rs["noticeNum"];				//실제 올려진 공지글 갯수
		$bgColor			= $rs["bgColor"];				//게시판 전체 배경색
		$bgImage			= $rs["bgImage"];				//게시판 전체 배경이미지
		$origin_width		= $rs["width"];					//게시판 전체 폭
		$pageSize			= $rs["pageSize"];				//한페이지에 표시되는 게시물수
		$divPage			= $rs["divPage"];				//페이지 바로가기 갯수
		$subjectLimit		= $rs["subjectLimit"];			//리스트에서 제목글자수 제한
		$authorLimit		= $rs["authorLimit"];			//리스트에서 이름글자수 제한
		$contentLimit		= $rs["contentLimit"];			//리스트에서 미리보기 내용길이 제한
		$align				= $rs["align"];					//게시판 전체 정렬 방식
		$headFile			= $rs["headFile"];				//상단 포함 파일
		$tailFile			= $rs["tailFile"];				//하단 포함 파일
		$headMsg			= $rs["headMsg"];				//상단 포함 메시지
		$tailMsg			= $rs["tailMsg"];				//하단 포함 메시지
		$useListAtView		= $rs["useListAtView"];			//내용에서 리스트 보기여부
		$useReply			= $rs["useReply"];				//답변기능사용여부
		$useTrackback		= $rs["useTrackback"];			//답변기능사용여부
		$useRssFeed			= $rs["useRssFeed"];			//답변기능사용여부
		$useMemo			= $rs["useMemo"];				//댓글 사용여부
		$useAutoLink		= $rs["useAutoLink"];			//자동링크 사용여부
		$usePreview			= $rs["usePreview"];			//리스트에서 내용 미리보기 사용여부
		$useSiteLink1		= $rs["useSiteLink1"];			//사이트링크1 사용여부
		$useSiteLink2		= $rs["useSiteLink2"];			//사이트링크2 사용여부
		$useSecret			= $rs["useSecret"];				//비밀글 사용여부
		$useBlockSpam		= $rs["useBlockSpam"];			//스팸방지 기능 사용여부
		$useBlockAnyLink	= $rs["useBlockAnyLink"];		//무단링크방지기능 사용여부
		$useHideButtons		= $rs["useHideButtons"];		//버튼 모두 보이기 사용여부
		$useViewClientIp	= $rs["useViewClientIp"];		//작성자 IP주소 보이기 사용여부
		$useViewClientInfo	= $rs["useViewClientInfo"];		//작성자 시스템정보 보이기 사용여부
		$useNewIcon			= $rs["useNewIcon"];			//NEW아이콘 사용여부
		$termNewIcon		= $rs["termNewIcon"];			//NEW아이콘 지속기간
		$useExecFile		= $rs["useExecFile"];			//내용에서 첨부파일 자동 보이기 사용여부
		$useRszImg			= $rs["useRszImg"];				//내용에서 큰이미지 자동 줄이기 사용여부
		$imgRszWidth		= $rs["imgRszWidth"];			//내용에서 큰이미지 자동줄임 크기
		$allowEmbedFileExts	= $rs["allowEmbedFileExts"];	//내용에서 자동실행파일의 허용 확장자
		$fileMaxLimit		= $rs["fileMaxLimit"];			//첨부파일 용량제한
		$fileMaxNum			= $rs["fileMaxNum"];			//첨부파일 동시 업로드 갯수
		$dataPath			= $rs["dataPath"];				//첨부파일 저장 경로
		$useCategory		= $rs["useCategory"];			//카테고리 사용여부
		$categories			= $rs["categories"];			//카테고리 목록
		$useWordFilter		= $rs["useWordFilter"];			//내용에서 불량단어 감추기 사용여부
		$badWords			= $rs["badWords"];				//불량단어 목록
		$currDate			= $rs["currDate"];				//오늘 첫접속일자
		$useother01			= $rs["useother01"];				//기타필드
		$useother02			= $rs["useother02"];				//기타필드
		$useother03			= $rs["useother03"];				//기타필드
		$useother04			= $rs["useother04"];				//기타필드
		$useother05			= $rs["useother05"];				//기타필드
		$useother06			= $rs["useother06"];				//기타필드
		$useother07			= $rs["useother07"];				//기타필드
		$useother08			= $rs["useother08"];				//기타필드
		$useother09			= $rs["useother09"];				//기타필드
		$useother10			= $rs["useother10"];				//기타필드

		//권한레벨 관련
		$levelList			= $rs["levelList"];				//리스트 보기 레벨
		$levelView			= $rs["levelView"];				//내용 보기 레벨
		$levelSecret		= $rs["levelSecret"];			//비밀글 보기 레벨
		$levelWrite			= $rs["levelWrite"];			//글쓰기 레벨
		$levelReply			= $rs["levelReply"];			//답변쓰기 레벨
		$levelMemoWrite		= $rs["levelMemoWrite"];		//댓글쓰기 레벨
		$levelNoticeWrite	= $rs["levelNoticeWrite"];		//공지사항 쓰기 레벨
		$levelUseHtml		= $rs["levelUseHtml"];			//HTML사용 레벨
		$levelDelete		= $rs["levelDelete"];			//삭제 레벨

		//글등록 관련
		$writeFrmDefMsg		= $rs["writeFrmDefMsg"];		//새글 작성시 글쓰기폼 기본 메시지
		$editMode			= $rs["editMode"];				//에디터 상태 - text / html / html+<br /> / editor
		$useAttachFile		= $rs["useAttachFile"];			//파일첨부기능 사용여부
		$useHtml			= $rs["useHtml"];				//HTML사용여부
		$allowTags			= $rs["allowTags"];				//허용할 태그
		$allowExts			= $rs["allowExts"];				//첨부파일 허용확장자


		//게시판 폭이 100이하일경우 %단위로 바꿈
		$width = $origin_width<=100 ? $origin_width."%" : $origin_width."px";

		//새글 New표시 기간설정
		if($useNewIcon) { $nterm = $useNewIcon==1 ? mktime(0,0,0,date("m"),date("d")+$termNewIcon,date("Y")) - mktime() : 3600*24*$termNewIcon; }

		mysql_free_result($result);

	} else Error("게시판 정보를 가져올 수 없습니다.");
}





/**************************************************
 게시판 웹경로 및 로컬경로 설정
**************************************************/
if($id != "auth") {

	//게시판이 설치된 경로 구하기
	//$FSBOARD_PATH = str_replace("/lib/","",$FSLIB_PATH);
	//$FSBOARD_PATH = substr($FSBOARD_PATH, strlen($FSBOARD_PATH)-strpos(strrev($FSBOARD_PATH),"/"), strlen($FSBOARD_PATH));

	//웹 절대경로 설정
	$FSBOARD_PATH = "/$FSBOARD_PATH"; //게시판 웹 절대경로239301577012023930158406102221213
	$FSDATA_PATH = $FSBOARD_PATH.$dataPath; //첨부파일 웹 절대경로
	$FSMEMIMG_PATH = "/__IMG_MEMBERS"; //회원이미지 디렉토리명

	//로컬 절대경로 설정
	$FSBOARD_ROOT = $DOCUMENT_ROOT.$FSBOARD_PATH; //게시판 서버 절대경로
	$FSDATA_ROOT = $FSBOARD_ROOT.$dataPath; //첨부파일 서버 절대경로

	//게시판이 실행되고 있는 파일의 웹경로
	//$NOW_PATH = substr($PHP_SELF, 0, strlen($PHP_SELF)-strpos(strrev($PHP_SELF),"/")); //현재 실행되고 있는 파일의 웹 절대경로

	//디렉토리 에러 처리
	if(!is_dir($FSBOARD_ROOT)) Error("게시판이 설치된 디렉토리 이름이 잘못되었습니다.<br />게시판이 설치된 디렉토리를 확인해 주세요.");
	if(!is_dir($FSDATA_ROOT)) Error("첨부파일업로드 디렉토리 이름이 잘못되었습니다.<br />첨부파일업로드 디렉토리를 확인해 주세요.");
	$perms = fileperms($FSDATA_ROOT);
	if($perms!=16839 && $perms!=16879 && $perms!=16895) Error("첨부파일업로드 디렉토리에 쓰기권한이 없습니다.<br />Telnet이나 FTP에서 권한을 조정해 주세요."); // 707:16839, 757:16879, 777:16895
	if(!is_dir($FSBOARD_ROOT."/skin/".$skin)) Error("<strong>".$skin."</strong> 스킨이 존재하지 않습니다.");
}



/**************************************************
 회원 및 레벨 관련 처리
**************************************************/

	unset($MemId);
	unset($MemPasswd);
	unset($MemLevel);
	unset($isAdmin);


	//게시판 제어에 필요한 회원로그인 세션정보 //외부 회원테이블 사용시 4가지 세션변수만 설정해 주면 게시판 제어가 가능함
	$MemId = $_SESSION["MemId"];			//인증,식별,권한 등에 사용
	$MemPasswd = $_SESSION["MemPasswd"];	//새글 작성시 암호로 사용(MD5암호화된 문자열 필요)
	$MemLevel = $_SESSION["MemLevel"];		//권한 부여에 필요
	$MemName = $_SESSION["MemName"];		//새글 작성시 이름으로 사용


	//레벨세션이 없으면 기본레벨 부여
	$defaultLevel = sizeof($mem_part_element);
	if(empty($MemLevel)) $MemLevel = $defaultLevel + 1;


	//이름이 없으면 아이디로 글쓰기시 사용
	if(!empty($MemId)&&empty($MemName)) $MemName = $MemId;


	//관리자인지 체크
	$isAdmin = false;
	if($MemId && $adminIDs) { if(ereg($MemId, $adminIDs)) $isAdmin = true; }
	if($MemLevel<=1 || strval($MemLevel)=="0") { $isAdmin = true; }


	//레벨변수가 비어있으면 기본레벨 설정
	if(empty($levelList)) $levelList = $defaultLevel;
	if(empty($levelView)) $levelView = $defaultLevel;
	if(empty($levelWrite)) $levelWrite = $defaultLevel-1;
	if(empty($levelReply)) $levelReply = $defaultLevel-1;
	if(empty($levelMemoWrite)) $levelMemoWrite = $defaultLevel-1;
	if(empty($levelSecret)) $levelSecret = 1;
	if(empty($levelNoticeWrite)) $levelNoticeWrite = 1;
	if(empty($levelUseHtml)) $levelUseHtml = $defaultLevel-1;
	if(empty($levelDelete)) $levelDelete = 1;


	/////권한 설정
	$list_level			= $MemLevel <= $levelList			|| $levelList			>=$defaultLevel || $isAdmin ? true : false; //목록보기권한
	$view_level			= $MemLevel <= $levelView			|| $levelView			>=$defaultLevel || $isAdmin ? true : false; //내용보기권한
	$write_level		= $MemLevel <= $levelWrite			|| $levelWrite			>=$defaultLevel || $isAdmin ? true : false; //내용쓰기권한
	$reply_level		= $MemLevel <= $levelReply			|| $levelReply			>=$defaultLevel || $isAdmin ? true : false; //답변쓰기권한
	$memowrite_level	= $MemLevel <= $levelMemoWrite		|| $levelMemoWrite		>=$defaultLevel || $isAdmin ? true : false; //댓글쓰기권한
	$secret_level		= $MemLevel <= $levelSecret			|| $levelSecret			>=$defaultLevel || $isAdmin ? true : false; //비밀보기권한
	$noticewrite_level	= $MemLevel <= $levelNoticeWrite	|| $levelNoticeWrite	>=$defaultLevel || $isAdmin ? true : false; //공지쓰기권한
	$usehtml_level		= $MemLevel <= $levelUseHtml		|| $levelUseHtml		>=$defaultLevel || $isAdmin ? true : false; //HTML사용권한
	$delete_level		= $MemLevel <= $levelDelete			|| $levelDelete			>=$defaultLevel || $isAdmin ? true : false; //내용삭제권한

?>