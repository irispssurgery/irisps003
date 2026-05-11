<?
/*************************************************************

	FSBOARD Library 1.2

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: November 2, 2007

	Copyright(c)2000-2007 FSBOARD. All Rights Reserved.

*************************************************************/


/**************************************************
 lib 초기화
**************************************************/

	//W3C P3P 규약설정
	@header ("P3P : CP=\"ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC\"");

	//include 되었는지 확인
	if(defined("lib_included")) return;
	define("lib_included", true);

	//에러리포팅 설정 및 register_globals_on일때 변수 재정의
	@error_reporting(E_ALL ^ E_NOTICE);
	foreach($_GET as $key=>$val) $$key = StripHtmlChars($val);
	@extract($_POST);
	@extract($_SERVER);
	@extract($_ENV);
	
	$time_server = time();
	$time_ymdhis = date("Y-m-d H:i:s");
	$time_ymd = date("Y-m-d");
	$time_his = date("H:i:s");

	//실행시간 계산 변수
	$_startTime = GetMicrotime();

	//lib.php파일이 위치한 경로
	$tmpFile = realpath(__FILE__);
	$FSLIB_PATH = $tmpFile ? eregi_replace("lib.php","",$tmpFile) : "";

	//게시판이 위치한 경로
	$FSBOARD_PATH = str_replace("\\", "/", $FSLIB_PATH);
	$FSBOARD_PATH = str_replace("/lib/", "", $FSBOARD_PATH);
	$FSBOARD_PATH = str_replace("\\", "/", $FSBOARD_PATH);
	$FSBOARD_PATH = substr($FSBOARD_PATH, strlen($FSBOARD_PATH)-strpos(strrev($FSBOARD_PATH),"/"), strlen($FSBOARD_PATH));

	//로컬 절대경로 설정
	$FSBOARD_ROOT = $_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH; //게시판 서버 절대경로
	$FSDATA_ROOT = $FSBOARD_ROOT."/data"; //첨부파일 서버 절대경로

	//세션 디렉토리명
	$session_path = "/___SESSION_TMP";

	//세션 디렉토리 설정
	if(!is_dir($FSDATA_ROOT.$session_path)) {
		@mkdir($FSDATA_ROOT.$session_path, 0777);
		@chmod($FSDATA_ROOT.$session_path, 0777);
	}

	//session 디렉토리의 쓰기 권한 확인
	if(is_dir($FSDATA_ROOT.$session_path) && !is_writable($FSDATA_ROOT.$session_path)) Error("세션 디렉토리(".$FSDATA_ROOT.$session_path.")의 쓰기 권한이 없습니다.");

	@session_save_path($FSDATA_ROOT.$session_path);
	@session_cache_limiter('nocache, must_revalidate');
	//@session_set_cookie_params(0,"/");

	// 세션변수 등록
	@session_start();

	//주도메인이 같은 다른 사이트 자동로그인 방지
	//if($_SESSION["MemLevel"]) { if($_SESSION["Host"]!=$_SERVER["HTTP_HOST"]) @session_destroy(); }










/**************************************************
 공용 변수 초기화
**************************************************/

	//현재 실행파일의 디자인적용 확인
	$_fsMainExecFile = "index.php"; //메인 실행 파일
	$combinedDesign = !ereg($_fsMainExecFile, $_SERVER["PHP_SELF"]) ? true : false;
	/*
	if($combinedDesign) {
		$menukey = trim($_GET["menukey"]);
		$menuaddr = $menukey!="" ? "menukey=".$menukey : "";
	}
	*/


	//DB테이블명
	$_table_id_admin		= "_board__admin__"; //관리 테이블
	$_table_id_board		= "_board_".$id; //게시판 테이블
	$_table_id_comment		= "_board__commentdata__"; //댓글 테이블
	$_table_id_trackback	= "_board__trackbackdata__"; //트랙백 테이블
	$_table_id_tagcloud		= "_board__tagcloud__"; //태그리스트 테이블
	$_table_id_messages		= "_messages_"; //쪽지 테이블
	$_table_id_members		= "_members_"; //회원 테이블
	$_table_id_sms			= "_smsresult_"; //sms 테이블


	//회원구분 분류
	$mem_part_element = Array(
			"관리자",
			"직원",
			"일반회원",
			"비회원"
		);

	//회원소속 분류
	$mem_part_element2 = Array(
		);

	//회원입사연차 분류
	$mem_part_element3 = Array(
		);








/**************************************************
 DB 관련 함수
**************************************************/

	//MySQL DB연결
	function DbConn() {
		global $dbConnect, $FSLIB_PATH, $_dbconn_included;

		if($_dbconn_included) return;
		$_dbconn_included = true;

		$f = @file($FSLIB_PATH."dbcon.php") or Error("dbcon.php 파일이 없습니다.<br />DB 설정을 먼저 하세요.");

		for($i=1;$i<=4;$i++) $f[$i] = trim(str_replace("\n","",$f[$i]));

		if(!$dbConnect) $dbConnect = @mysql_connect($f[1],$f[2],$f[3]) or Error("DB 연결중 에러가 발생했습니다");

		@mysql_select_db($f[4], $dbConnect) or Error("DB Select 에러가 발생했습니다");

		return $dbConnect;
	}


	//DB테이블이 있는지 검사
	function SeekTable($tableName) {
		global $dbConnect;
		$chktbl = false;

		if($tableName) {
			$query = "SHOW TABLES LIKE '{$tableName}';";
			$result = mysql_query($query);
			if($result) {
				while($rs = mysql_fetch_row($result)) {
					if($rs[0] == $tableName) {
						$chktbl = $rs[0];
						break;
					}
				}
			}
		}

		return $chktbl;
	}







/**************************************************
 환경설정 관련 함수
**************************************************/

	//기본설정 파일의 정보를 가져옴
	function GetDefaultSetup() {
		global $INC_PATH;
		$data = GetReadFile($INC_PATH."lib/_setup.php");
		$data = str_replace("<?/*","",$data);
		$data = str_replace("*/?>","",$data);
		$data = explode("\n", $data);
		$_c = count($data);
		unset($defaultsetup);
		for($i=0; $i<$_c; $i++) {
			if(!ereg(";",$data[$i]) && strlen(trim($data[$i]))) {
				$tmpStr = explode("=",$data[$i]);
				$name = trim($tmpStr[0]);
				$value = trim($tmpStr[1]);
				$defaultsetup[$name] = $value;
			}
		}
		if(!$defaultsetup["url"])						$defaultsetup["url"]						= $_SERVER["HTTP_HOST"];
		if(!$defaultsetup["sitename"])					$defaultsetup["sitename"]					= $_SERVER["HTTP_HOST"];
		if(!$defaultsetup["session_path"])				$defaultsetup["session_path"]				= "data/__fsTMP~SESSION";
		if(!$defaultsetup["session_view_size"])			$defaultsetup["session_view_size"]			= 512;
		if(!$defaultsetup["session_vote_size"])			$defaultsetup["session_vote_size"]			= 256;
		if(!$defaultsetup["login_time"])				$defaultsetup["login_time"]					= 60*30;
		if(!$defaultsetup["nowconnect_enable"])			$defaultsetup["nowconnect_enable"]			= "true";
		if(!$defaultsetup["nowconnect_refresh_time"])	$defaultsetup["nowconnect_refresh_time"]	= 60*3;
		if(!$defaultsetup["nowconnect_time"])			$defaultsetup["nowconnect_tim"]				= 60*5;
		if(!$defaultsetup["enable_hangul_id"])			$defaultsetup["enable_hangul_id"]			= "false";
		if(!$defaultsetup["check_email"])				$defaultsetup["check_email"]				= "true";
		if(!$defaultsetup["memo_limit_time"])			$defaultsetup["memo_limit_time"]			= 7;

		$defaultsetup["memo_limit_time"] = 60 * 60 * 24 * $defaultsetup["memo_limit_time"];
		 
		return $defaultsetup;
	}


	//USER_AGENT에서 시스템 정보 반환
	function usr_agent($svr_agent, $type=0) {
		$match = array();

		switch($type) {
			case 0 :
				$match[0] = $svr_agent;
				break;

			case 1 :
				if(eregi('msie', $svr_agent)) $pattern = '/(MSIE[0-9\.\/ ]+)/i';
				else if(eregi('firefox', $svr_agent)) $pattern = '/(Firefox[0-9\.\/ ]+)/i';
				else if(eregi('safari', $svr_agent)) $pattern = '/(Safari[0-9\.\/ ]+)/i';
				else if(eregi('opera', $svr_agent)) $pattern = '/(Opera[0-9\.\/ ]+)/i';
				else if(eregi('netscape', $svr_agent)) $pattern = '/(Netscape[0-9\.\/ ]+)/i';
				else $pattern = '';
				break;

			case 2 :
				if(eregi('windows', $svr_agent)) $pattern = '/(Windows[a-z0-9\.\/ ]+)/i';
				else if(eregi('linux', $svr_agent)) $pattern = '/(Linux[a-z0-9\.\/ ]+)/i';
				else if(eregi('mac', $svr_agent)) $pattern = '/(Mac[a-z0-9\.\/ ]+)/i';
				else $pattern = '';
				break;

			default:
				$match[0] = 'Etc';
				break;
		}

		if($svr_agent && $pattern) preg_match($pattern, $svr_agent, $match);
		if(!$match[0]) $match[0] = 'Etc';

		return $match[0];
	}


	//윈도우 운영체제 확인
	function IsWindows() {
		$svrsw = $_SERVER["SERVER_SOFTWARE"];
		if(eregi("Microsoft", $svrsw)) {
			return true;
		}
		else {
			return false;
		}
	}


	//아파치 적재 모듈 확인 - Rewrite 모듈 확인: apache_is_module_loaded("mod_rewrite")
	function apache_is_module_loaded($mod_name) {
		$modules = apache_get_modules();
		if (in_array($mod_name, $modules)) {
			return true;
		}
		else {
			return false;
		}
	}


	//rewrite 모듈사용 상태 체크
	function IsRewrite() {
		global $FSBOARD_ROOT;

		if(file_exists($FSBOARD_ROOT.'/.htaccess') && !IsWindows() && apache_is_module_loaded('mod_rewrite')) {
			return true;
		}
		else {
			return false;
		}
	}


	//웹표준 DOCTYPE선언부
	function DclrDocType() {
//		$dt .= "<"."?xml version=\"1.0\" encoding=\"euc-kr\"?".">\n"; //XML선언
		$dt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"; //XHTML 1.0 표준 Strict
		//$dt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"; //XHTML 1.0 표준 Transitional
		//$dt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"; //XHTML 1.1 표준
		//$dt .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n"; //HTML4.01 표준 Strict
		//$dt .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/transitional.dtd\">\n"; //HTML4.01 표준 Transitional
		//$dt .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n"; //HTML4.0 일반
		$dt .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ko\" xml:lang=\"euc-kr\">\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\" />".fslib();
		return $dt;
	}


	//게시판 상단 기본 내용
	function ContentTop() {
		global $id, $FSBOARD_PATH, $skin, $headFile, $headMsg, $topcontent_included, $combinedDesign, $isAdmin;
		global $width, $align, $useCategory, $origin_width, $bgColor, $bgImage, $srhctgr, $srhstr, $ctgrstr, $rowctgr, $rowmode;

		if($topcontent_included) return;
		$topcontent_included = true;

		if(!$combinedDesign) {
			echo DclrDocType();

			//RSS Feed 연결 태그
			echo "<title>".$id."</title>\n";
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"".(IsRewrite()?"http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/rss/".$id:"http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=rss.xml")."\" title=\"Syndicate this site using RSS 2.0\" />\n";
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$FSBOARD_PATH."/skin/".$skin."/style.php?w=".urlencode($width)."&amp;o=".urlencode($origin_width)."&amp;a=".urlencode($align)."&amp;u=".urlencode($useCategory)."&amp;b=".urlencode($bgColor)."&amp;i=".urlencode($bgImage)."&amp;c=".urlencode($srhctgr)."&amp;s=".urlencode($srhstr)."&amp;g=".urlencode($ctgrstr)."&amp;=r=".urlencode($rowctgr)."&amp;m=".urlencode($rowmode)."\" />\n";
			echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/javascript.php?defer=".$_SERVER["PHP_SELF"]."\"></script>\n";
			echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/Calendar1.js\"></script>\n";   
			echo "\n</head>\n<body>\n";
		}
		else {
			echo fslib();
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$FSBOARD_PATH."/skin/".$skin."/style.php?w=".urlencode($width)."&amp;o=".urlencode($origin_width)."&amp;a=".urlencode($align)."&amp;u=".urlencode($useCategory)."&amp;b=".urlencode($bgColor)."&amp;i=".urlencode($bgImage)."&amp;c=".urlencode($srhctgr)."&amp;s=".urlencode($srhstr)."&amp;g=".urlencode($ctgrstr)."&amp;=r=".urlencode($rowctgr)."&amp;m=".urlencode($rowmode)."\" />\n";
			echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/javascript.php\"></script>\n";
			echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/Calendar1.js\"></script>\n";   
		}
		echo "\n<form id=\"__ctl\" name=\"__ctl\" method=\"post\" enctype=\"multipart/form-data\" action=\"\">\n";

		//게시판 상단 포함 파일이 있을경우 포함 시킴
		if($headFile&&file_exists($_SERVER["DOCUMENT_ROOT"]."/".$headFile)) {
			if(eregi("\.htm",$headFile)||eregi("\.php",$headFile)) include $_SERVER["DOCUMENT_ROOT"]."/".$headFile;
			else echo "<span style=\"color:red;\">상단 포함 파일은 htm,html,php만 가능합니다.</span>\n";
		}

		//게시판 상단 포함 메시지
		if($headMsg) echo $headMsg;

		//관리자 모드 표시
		//if($isAdmin) echo "<div style=\"clear:both; width:".(!$width?"100%":$width)."; margin:0 auto; text-align:center;\"><div style=\"color:red; font-size:13px; font-family:굴림;\"><blink>[관리자 모드]</blink></div></div>\n";

		} function fslib() {
		//$arrpn = array(0x66,0x73,0x62,0x6F,0x61,0x72,0x64);//$arrdn = array(0x4A,0x75,0x6E,0x67,0x68,0x79,0x75,0x6E,0x20,0x43,0x68,0x6F);//$arrtc = array(0x73,0x61,0x69,0x75,0x72,0x40,0x6D,0x73,0x6E,0x60,0x63,0x6F,0x6D);
		$vs="1.1.0";$lu="Nov. 9, 2007";$pn="FSBOARD";$ev="(Apache/PHP/MySQL)";$cn="BALHAE Corporation";$dn="Junghyun Cho";$tc="saiur@msn.com";$cr="Copyright(c)2000-".date("Y")." {$pn}, {$cn}. All Rights Reserved.";$ds="\n<!-- **************************************************************************\n\n    {$pn} {$vs} {$ev}\n\n      Powered by {$dn}\n      Technical contact to {$tc}\n      Last update at {$lu}\n\n    {$cr}\n\n**************************************************************************** -->\n";return $ds;
	}


	//게시판 하단 기본 내용
	function ContentBottom() {
		global $INC_PATH, $tailFile, $tailMsg, $_startTime, $bottomcontent_included, $combinedDesign;

		if($bottomcontent_included) return;
		$bottomcontent_included = true;

		//게시판 하단 포함 메시지
		if($tailMsg) echo $tailMsg;

		//게시판 하단 포함 파일이 있을 경우 포함시킴
		if($tailFile&&file_exists($_SERVER["DOCUMENT_ROOT"]."/".$tailFile)) {
			if(eregi("\.htm",$tailFile)||eregi("\.php",$tailFile)) include $_SERVER["DOCUMENT_ROOT"]."/".$tailFile;
			else echo "<span style=\"color:red;\">하단 포함 파일은 htm,html,php만 가능합니다.</span>\n";
		}

		//echo "\n<div style=\"clear:both; margin:10px auto; font-family:Arial;font-size:10px; text-align:center;\">Copyright(c)".date("Y")." BALHAE Corporation. All rights reserved.</div>";
		echo "\n</form>\n";
		if(!$combinedDesign) echo "</body>\n</html>";

		if($_startTime) {
			echo "\n<!-- ".$_SERVER["REMOTE_ADDR"]." -->";
			echo "\n<!-- Running time : ".sprintf("%0.4f",GetMicrotime()-$_startTime)." second(s) -->";
		}
	}


	//작성자 표시
	function MemberName($mem_id,$author,$authorLimit=10,$e_mail,$homeUrl) {
		global $id, $FSDATA_ROOT, $FSDATA_PATH, $FSBOARD_PATH, $FSMEMIMG_PATH;

		//원래이름 보관
		$author_origin = $author;

		//이름 이미지가 있을 경우 이미지로 작성자 표시
		if($mem_id && file_exists($FSDATA_ROOT.$FSMEMIMG_PATH."/mem_name_".md5($mem_id).".gif")) {
			$author = "<img src=\"".$FSDATA_PATH.$FSMEMIMG_PATH."/mem_name_".md5($mem_id).".gif\" id=\"".GetRandomString(16,1)."\" alt=\"회원이름\" />";
		}
		else {
			if($authorLimit) $author = CutStr($author,$authorLimit); //긴 이름 잘라냄
			$author = StripHtmlChars($author);
			if($mem_id) $author = "<strong>".$author."</strong>"; //회원이면 작성자를 진하게 표시
		}

		//회원마크 이미지가 있을 경우 작성자 앞에 표시
		if($mem_id && file_exists($FSDATA_ROOT.$FSMEMIMG_PATH."/mem_mark_".md5($mem_id).".gif")) {
			$author = "<img src=\"".$FSDATA_PATH.$FSMEMIMG_PATH."/mem_mark_".md5($mem_id).".gif\" id=\"".GetRandomString(16,1)."\" alt=\"회원마크\" onload=\"if(this.height<16){this.style.verticalAlign='0';return;}\" style=\"vertical-align:middle;\" />".$author;
		}

		//작성자 정보 보기 링크 추가
		$author = "<a href=\"javascript:void(0);\" title=\"작성자 정보 보기\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=mem_info&amp;tx={$id}&amp;ref=".($mem_id?urlencode(base64_encode($mem_id)):urlencode(base64_encode("{$author_origin}|	|{$e_mail}|	|{$homeUrl}")))."','','width=500,height=300,left='+(window.screen.availWidth-500)/2+',top='+((window.screen.availHeight-400)/2-100)+',resizable=1,scrollbars=1');\">{$author}</a>";
		//$author = "<div>".$author."</div>";

		return $author;
	}


	//쿠키 강제 만료
	function ExpireCookie($ckname) {
		@setcookie($ckname,'',mktime(0,0,0,date("m"),date("d"),date("Y")),"/");
	}







/**************************************************
 페이지 처리 관련 함수
**************************************************/

	//에러 메시지 출력
	function Error($msg, $url="", $vmode="") {
		global $dbConnect, $id, $INC_PATH, $FSLIB_PATH, $width, $align;

		switch($vmode) {
			case "MsgBox" :
				$msg = str_replace("<br />","\\n",$msg);
				$msg = str_replace("\"","\\\"",$msg);
				$pageLink = $url ? "window.location.href='".$url."';" : "window.history.back();";
				echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('{$msg}');\n{$pageLink}\n//]]>\n</script>\n";
				break;
			case "Popup" :
				$msg = str_replace("<br />","\\n",$msg);
				$msg = str_replace("\"","\\\"",$msg);
				echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('{$msg}');\nwindow.close();\n//]]>\n</script>\n";
				break;
			default :
				$FSBOARD_PATH = str_replace("\\", "/", $FSLIB_PATH);
				$FSBOARD_PATH = str_replace("/lib/","",$FSBOARD_PATH);
				$FSBOARD_PATH = substr($FSBOARD_PATH, strlen($FSBOARD_PATH)-strpos(strrev($FSBOARD_PATH),"/"), strlen($FSBOARD_PATH));
				$FSBOARD_PATH = "/".$FSBOARD_PATH;

				include $INC_PATH."_error.php";
				break;
		}
		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
		exit;
	}


	//페이지 이동
	function MovePage($url="?") {
		global $dbConnect;

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
		if(!headers_sent()) { $redir = 1; }
		if(IsWindows()) { $redir = 0; }

		if($redir) {
			@ob_end_clean();
			header("location:".$url);
		}
		else {
			echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location.href='".$url."';\n//]]>\n</script>\n";
		}
		exit;
	}


	//페이지 바로가기
	function NavPage($page,$divpage,$totalpage,$querystr,$property="",$arwstyle="[◀],[▶]",$brkstyle="[,]") {
		$pagestring = "";
		$qstr = $querystr;
		$blockpage = 0;
		$arrow = explode(",",$arwstyle);
		$bracket = explode(",",$brkstyle);

		if(!$qstr) $qstr = "tmpstr=nil";

		if($page > $divpage) $pagestring .= " <a href=\"".$_SERVER["PHP_SELF"]."?{$qstr}&amp;page=1\" title=\"처음 1 페이지\"{$property}><span style=\"white-space:nowrap;\">{$bracket[0]}1{$bracket[1]}</span></a>&nbsp; ";
		$blockpage = intval(($page - 1) / $divpage) * $divpage + 1;

		if($blockpage != 1) $pagestring .= "<a href=\"".$_SERVER["PHP_SELF"]."?{$qstr}&amp;page=".($blockpage-1)."\" title=\"이전 {$divpage}개 페이지\"{$property}><span style=\"white-space:nowrap;\">{$arrow[0]}</span></a>";

		$i = 1;
		while($i<=$divpage && $blockpage<=$totalpage) {
			if($blockpage == $page) $pagestring .= "<span{$property}><span style=\"white-space:nowrap;color:red;\">{$bracket[0]}{$blockpage}{$bracket[1]}</span></span>";
			else $pagestring .= "<a href=\"".$_SERVER["PHP_SELF"]."?{$qstr}&amp;page={$blockpage}\"{$property} title=\"{$blockpage} 페이지\"><span style=\"white-space:nowrap;\">{$bracket[0]}{$blockpage}{$bracket[1]}</span></a>";
			$blockpage++;
			$i++;
		}

		if($blockpage <= $totalpage) $pagestring .= "<a href=\"".$_SERVER["PHP_SELF"]."?{$qstr}&amp;page={$blockpage}\" titlel=\"다음 {$divpage}개 페이지\"{$property}><span style=\"white-space:nowrap;\">{$arrow[1]}</span></a>";

		if($blockpage <= $totalpage) $pagestring .= " &nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?{$qstr}&amp;page={$totalpage}\" title=\"마지막 {$totalpage} 페이지\"{$property}><span style=\"white-space:nowrap;\">{$bracket[0]}{$totalpage}{$bracket[1]}</span></a> ";

		return $pagestring;
	}

	// 마이크로 타임 구함
	function GetMicrotime() {
		$microtimestmp = split(" ",microtime());
		return $microtimestmp[0]+$microtimestmp[1];
	}

/**************************************************
 문자열 처리 관련 함수
**************************************************/

	//쿼리문자열 반환
	function QryStr($mode, $amp=0) {
		global $id, $idx, $srhctgr, $srhstr, $optr, $rowctgr, $current_rowmode, $ctgrstr;

		//기본 쿼리문자열
		$qstr = "id={$id}&mode={$mode}";

		//검색모드일경우 검색쿼리문자열 연결
		if($srhctgr&&$srhstr) $qstr .= "&srhctgr=".urlencode($srhctgr)."&srhstr=".urlencode($srhstr)."&optr=".trim($optr);

		//사용자정렬모드일경우 정렬쿼리문자열 연결
		if($rowctgr&&$current_rowmode) $qstr .= "&rowctgr={$rowctgr}&rowmode={$current_rowmode}";

		//카테고리모드일경우 쿼리문자열 연결
		if($ctgrstr) $qstr .= "&ctgrstr=".urlencode($ctgrstr);

		//amposand 처리
		if($amp) $qstr = str_replace("&","&amp;",$qstr);

		return $qstr;
	}


	//문자열 자르기
	function CutStr($msg,$cut_size,$ellipsis="...") {
		if($cut_size<=0) return $msg;
		if(ereg("\[re\]",$msg)) $cut_size=$cut_size+4;
		for($i=0;$i<$cut_size;$i++) if(ord($msg[$i])>127) $han++; else $eng++;
		$cut_size=$cut_size+(int)$han*0.6;
		$point=1;
		for ($i=0;$i<strlen($msg);$i++) {
			if ($point>$cut_size) return $pointtmp.$ellipsis;
			if (ord($msg[$i])<=127) {
				$pointtmp.= $msg[$i];
				if ($point%$cut_size==0) return $pointtmp.$ellipsis;
			} else {
				if ($point%$cut_size==0) return $pointtmp.$ellipsis;
				$pointtmp.=$msg[$i].$msg[++$i];
				$point++;
			}
			$point++;
		}
		return $pointtmp;
	}


	//지정한 문자열 구간내의 문자열을 추출
	function ExtractStr($haystack, $needle1, $needle2) {
		$str = $haystack;
		$str = strchr($str, $needle1);
		$str = substr($str,0,strpos($str,$needle2));
		$str = str_replace($needle1, "", $str);

		return $str;
	}


	//아이피주소를 일부분 가려서 리턴
	function PrvtIp($ip,$id1,$id2) {
		global $isAdmin;
		$ipStr = $ip;

		if(!$ipStr) $ipStr = "???.???.???.???";
		$ipDiv = explode(".",$ipStr); //아이피주소 분리
		$ipStr = "$ipDiv[0].$ipDiv[1].";

		if($id1&&$id2&&$id1==$id2 || $isAdmin)
			$ipStr .= "$ipDiv[2].$ipDiv[3]";
		else {
			if(strlen($ipDiv[2])>2)
				$ipStr .= substr($ipDiv[2],0,2) . "X";
			else if(strlen($ipDiv[2])>1)
				$ipStr .= substr($ipDiv[2],0,1) . "X";
			else
				$ipStr .= "X";

			$ipStr .= ".";

			for($i=1,$till=strlen($ipDiv[3]); $i<=$till; $i++)
				$ipStr .= "X";
		}
		return $ipStr;
	}


	//랜덤 문자열 출력 generate a random string of letters/numbers
	function GetRandomString($length, $cset=true) {
		$template = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; //you could repeat the alphabet to get more randomness
		$rndstr = '';
		$len = strlen($template) - 1;
		$len = $cset ? $len : $len - 10;

		for ($i=0; $i<=$length; $i++) {
			$rndstr .= $template[mt_rand(0,$len)];
		}
		return $rndstr;
	}


	//숫자 크기에 따른 색상 지정
	function NumColor($num) {
		$strNum = $num;
		if($num>=1000) $strNum = "<span style=\"color:red;\">{$strNum}</span>";
		else if($num<1000 && $num>=500) $strNum = "<span style=\"color:blue;\">{$strNum}</span>";
		else if($num<500 && $num>=100) $strNum = "<span style=\"color:green;\">{$strNum}</span>";
		else $strNum = "<span style=\"color:gray;\">{$strNum}</span>";
		return $strNum;
	}


	//싱클쿼테이션과 역슬래쉬를 애드슬래쉬 시킴
	function StrAddSlashes($str) {
		//if(!ini_get("magic_quotes_gpc")) {
		if(!get_magic_quotes_gpc()) {
			$str = str_replace("\\","\\\\",$str);
			$str = str_replace("'","\\'",$str);
		}
		return $str;
	}


	//E-mail 주소 패턴 확인
	function IsEmail($str) {
		if( eregi("([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)", $str) ) return $str;
		else return ''; 
	}


	//E-mail 의 MX를 검색하여 실제 존재하는 메일인지 검사
	function mail_mx_check($email) {
		if(!IsEmail($email)) return false;
		list($user, $host) = explode("@", $email);
		if (checkdnsrr($host, "MX") or checkdnsrr($host, "A")) return true;
		else return false;
	}


	//홈페이지 주소가 올바른지 검사
	function IsHomepage($str) {
		if(eregi("^http://([a-z0-9\_\-\./~@?=&amp;-\#{5,}]+)", $str)) return $str;
		else return '';
	}


	//URL, Mail을 자동으로 체크하여 링크만듬
	function AutoLink($str) {
		//URL 치환
		$homepage_pattern = "/([^\"\'\=\>])(http|HTTP|ftp|FTP|mms|MMS|telnet|TELNET|news|irc)\:\/\/(.[^ \n\r\<\"\']+)/";
		$str = preg_replace($homepage_pattern,"\\1<a href=\"\\2://\\3\" onclick=\"window.open(this.href,'_blank'); return false;\">\\2://\\3</a>", " ".$str);

		//메일 치환
		$email_pattern = "/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/";
		$str = preg_replace($email_pattern,"\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", "".$str);

		return $str;
	}


	//HTML태그 블럭을 웹문자로 리턴
	function StripHtmlChars($str) {
		$str = str_replace(">", "&gt;", $str);
		$str = str_replace("<", "&lt;", $str);
		$str = str_replace("\"", "&#34;", $str);
		$str = str_replace("'", "&#39;", $str);
		return $str;
	}


	//빈문자열 경우 1을 리턴
	function IsBlank($str) {
		$temp = str_replace("　","",$str);
		$temp = str_replace("\n","",$temp);
		//$temp = strip_tags($temp);
		$temp = str_replace("&nbsp;","",$temp);
		$temp = str_replace(" ","",$temp);
		if(eregi("[^[:space:]]",$temp)) return 0;
		return 1;
	}


	//숫자일 경우 1을 리턴
	function IsNum($str) {
		if(eregi("[^0-9]",$str)) return 0;
		return 1;
	}


	//숫자, 영문자 일경우 1을 리턴
	function IsAlNum($str) {
		if(eregi("[^0-9a-zA-Z\_]",$str)) return 0;
		return 1;
	}







/**************************************************
 파일 처리 관련 함수
**************************************************/

	//파일명 중복 방지
	function GetUniqueName(&$strFileName, $DirectoryPath) {
		$strName = "";
		$strExt = "";
		$tmp = "";
		$temp = "";
		$bExist = true;
		$strFileWholePath = "";
		$countFileName = "";

		$tmp = strpos(strrev($strFileName), ".");
		$temp = strlen($strFileName) - $tmp;

		if($tmp) {
			$strName = substr($strFileName, 0, $temp-1); //확장자를 제외한 파일명
			$strExt = substr($strFileName, strlen($strName)+1, strlen($strFileName));

			//확장명 검사
			if(eregi("htm|php|inc|phtm|shtm|cgi|dot|asp|ztx|pl",$strExt)) $strExt .= ".txt";
		}
		else { //확장명이 없을경우
			$strName = $strFileName;
			$strExt = "unknown";
		}

		$strFileName = $strName . "." . $strExt; //파일이름 재결합

		$bExist = true;
		$strFileWholePath = $DirectoryPath . $strName . "." . $strExt; //저장할 파일의 완전한 이름 구성
		$countFileName = 0; //파일이 존재할 경우, 이름 뒤에 붙일 숫자를 세팅함.

		do { //우선 있다고 가정
			if(file_exists($strFileWholePath)) { //같은 이름의 파일이 있을 때
				$countFileName = $countFileName + 1; //파일명에 숫자를 붙인 새로운 파일 이름 생성
				$strFileName = $strName . "(" . $countFileName . ")." . $strExt; //파일명 변경
				$strFileWholePath = $DirectoryPath . $strFileName;
			} else {
				$bExist = false;
			}
		}
		while($bExist==true);

		return $strFileWholePath;
	}


	//파일 사이즈를 KB, MB에 맞게 변환해서 리턴
	function GetFileSize($size) {
		$strSize = "";

		if(!$size) $strSize = "0 Byte";

		if($size<1024) {
			$strSize = ($size." Byte");
		}
		else if($size >1024 && $size< 1024 *1024) {
			$strSize = sprintf("%0.1f KB",$size / 1024);
		}
		else {
			$strSize = sprintf("%0.2f MB",$size / (1024*1024));
		}
		return $strSize;
	}


	//파일 확장명에 따른 파일 아이콘 선택
	function FileTypeIcon($fileName) {
		global $FSBOARD_PATH;

		//파일의 확장자 추출
		$fileType = strpos($fileName, ".")>0 ? strtolower(substr($fileName, strrpos($fileName, ".")+1, strlen($fileName))) : "";
		$path = $FSBOARD_PATH . "/img/filetype/";
		$img_ico = "<img src=\"" . $path;

		//파일 확장자 확인
		if(eregi("(asf|avi|bmp|doc|exe|gif|hnc|htm|html|hwp|jpg|js|mid|mp3|mpeg|mpg|pdf|ppt|ra|ram|rar|swf|txt|wav|wma|wmv|xls|zip)", $fileType)) {
			$img_ico .= $fileType;
		}
		else {
			$img_ico .= "unknown";
		}

		$img_ico .= ".gif\" alt=\"첨부파일\" style=\"vertical-align:middle;\" onerror=\"this.style.display='none';\" />";

		if($fileName=="") { $img_ico = "<img src=\"" . $path . "default.gif\" alt=\"문서\" style=\"vertical-align:middle;\" />"; }

		return $img_ico;
	}


	//첨부파일 자동으로 보이기
	function ExecFile($strFileName, $strFilePath, $seqNum, $width, $strExts) {
		global $useRszImg, $origin_width, $id, $FSDATA_ROOT, $id;
		$strObj = "";
		$onLoad = "";
		if($strFileName) {
			$temp = explode(".",$strFileName);
			$s_point = count($temp)-1;
			$chkExt = $temp[$s_point];

			if($strExts&&!eregi($chkExt,$strExts)||!$chkExt) return;

			$maintainCode = md5(session_id());

			//그림 파일일 경우
			if(eregi("\.jpg",$strFileName)||eregi("\.gif",$strFileName)||eregi("\.bmp",$strFileName)||eregi("\.png",$strFileName)) {
				if(!$width) { //이미지폭 자동맞춤으로 지정했을 경우
					if($origin_width>100) $width = $origin_width - 20; //게시판 폭에 맞춤
					else $width = "document.body.clientWidth-200"; //게시판 폭이 자동일경우 브라우저 크기에 맞춤
				}
				if($useRszImg) $onLoad="controlImage(this.id,{$width});";
				//$strObj = "<img id=\"uploaded_image{$seqNum}\" style=\"cursor:hand;\" src=\"{$strFilePath}/{$strFileName}\" onload=\"{$onLoad}\" onclick=\"vwimgrzmv(this,\"{$strFilePath}/{$strFileName}\")\" /><br /><br />";
				$strObj = "<div style=\"margin:0.5em auto;\"><img src=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=filelink&amp;maintainCode={$maintainCode}&amp;filename=".urlencode($strFileName)."\" id=\"UPLOADED_IMAGE{$seqNum}\" onload=\"{$onLoad}\" onclick=\"vwimgrzmv(this,'".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=filelink&amp;maintainCode={$maintainCode}&amp;filename=".urlencode($strFileName)."')\" alt=\"{$strFileName}\" style=\"cursor:hand;\" /></div>";
			}

			//동영상 파일일 경우
			else if(eregi("\.wmv",$strFileName)||eregi("\.asf",$strFileName)||eregi("\.asx",$strFileName)||eregi("\.mpg",$strFileName)||eregi("\.mpeg",$strFileName)||eregi("\.wax",$strFileName)||eregi("\.wvx",$strFileName)) {
				//$strObj = "<embed src=\"{$strFilePath}/{$strFileName}\" hidden=\"false\" showcontrols=\"true\" showstatusbar=\"true\" autostart=\"".($seqNum==1?"true":"false")."\" /><br /><br />";
				$strObj = "<div style=\"margin:1.5em auto;\"><embed src=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=filelink&amp;maintainCode={$maintainCode}&amp;filename=".urlencode($strFileName)."\" id=\"UPLOADED_MOVIE{$seqNum}\" hidden=\"false\" showcontrols=\"true\" showstatusbar=\"true\" autostart=\"".($seqNum==1?"true":"false")."\" title=\"{$strFileName}\" /></div>";
			}

			//음악 파일일 경우
			else if(eregi("\.wma",$strFileName)||eregi("\.mp3",$strFileName)||eregi("\.mp2",$strFileName)||eregi("\.mid",$strFileName)) {
				//$strObj = "<embed src=\"{$strFilePath}/{$strFileName}\" hidden=\"false\" autostart=\"".($seqNum==1?"true":"false")."\" /><br /><br />";
				$strObj = "<div style=\"margin:1.5em auto;\"><embed src=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=filelink&amp;maintainCode={$maintainCode}&amp;filename=".urlencode($strFileName)."\" id=\"UPLOADED_SOUND{$seqNum}\" hidden=\"false\" autostart=\"".($seqNum==1?"true":"false")."\" title=\"{$strFileName}\" /></div>";
			}

			//플래시 파일일 경우
			else if(eregi("\.swf",$strFileName)) {
				if(!$width) { //폭 자동맞춤일경우
					if($origin_width>100) { 
						$width = $origin_width -20; //게시판 폭에 맞춤
						$height = $origin_width*(3/4);
					}
					else { //게시판폭이 100보다 작아 %일경우
						$width = 600; //폭 크기 지정
						$height = $width * (3/4);
					}
				}
				else {
					$height = $width * (3/4);
				}
				//$strObj = "<embed src=\"{$strFilePath}/{$strFileName}\" width=\"$width\" height=\"$height\" /><br /><br />";
				$strObj = "<div id=\"attachedFile{$seqNum}\" style=\"margin:1.5em auto;\">";
				if($seqNum==1) {
					$strObj .="<embed src=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=filelink&amp;maintainCode={$maintainCode}&amp;filename=".urlencode($strFileName)."\" id=\"UPLOADED_FLASH{$seqNum}\" style=\"width:{$width}px; height:{$height}px;\" />";
				}
				else {
					$strObj .= "<div id=\"attachedFile{$seqNum}\" style=\"width:{$width}px; height:{$height}px; text-align:center; background-color:#eee;\">";
					$strObj .= "<p style=\"position:relative; top:".($height/2-11)."px;\">";
					$strObj .= "<a href=\"javascript:playFlash('attachedFile{$seqNum}','{$id}','{$maintainCode}','".urlencode($strFileName)."',{$seqNum},{$width},{$height});\">[Play Flash]</a></p></div>";
				}
				$strObj .= "</div>";
			}
			else return;
		}
		return $strObj;
	}


	//지정한 디렉토리의 파일 정보를 구함
	function GetDirInfo($path) {
		$handle=@opendir($path);
		while($info = readdir($handle)) {
			if($info != "." && $info != "..") {
				$dir[] = $info;
			}
		}
		closedir($handle);
		return $dir;
	}


	//파일 삭제 함수
	function RemoveFile($filename) {
		@chmod($filename,0777);
		$handle = @unlink($filename);
		if(@file_exists($filename)) {
			@chmod($filename,0775);
			$handle=@unlink($filename);
		}
		return $handle;
	}


	//지정된 파일의 내용을 읽어옴
	function GetReadFile($filename) {
		if(!file_exists($filename)) return "";

		$f = fopen($filename,"r");
		$str = fread($f, filesize($filename));
		fclose($f);

		return $str;
	}


	//지정된 파일에 주어진 데이타를 씀
	function WriteFile($filename, $str) {
		$f = fopen($filename,"w");
		$lock = flock($f,2);
		if($lock) {
			fwrite($f, $str);
		}
		flock($f,3);
		fclose($f);
	}


	//지정된 파일이 Locking중인지 검사
	function ChkFileIsLocked($filename) {
		$f=@fopen($filename,w);
		$count = 0;
		$break = true;
		while(!@flock($f,2)) {
			$count++;
			if($count>10) {
				$break = false;
				break;
			}
		}
		if($break!=false) @flock($f,3);
		@fclose($f);
	}


	//순환적으로 디렉토리를 삭제
	function RemoveDir($path) { 
		$directory = dir($path); 
		while($entry = $directory->read()) { 
			if ($entry != "." && $entry != "..") { 
				if (is_Dir($path."/".$entry)) { 
					RemoveDir($path."/".$entry); 
				} else { 
					@unLink ($path."/".$entry); 
				} 
			} 
		} 
		$directory->close(); 
		@rmdir($path); 
	}







/**************************************************
 E-Mail 관련 함수
**************************************************/

	//E-Mail 보내기
	function SendEmail($type, $to, $to_name, $from, $from_name, $subject, $comment, $cc="", $bcc="") {
		$recipient = "{$to_name} <{$to}>";

		if($type==1) $comment = nl2br($comment);

		$headers = "From: {$from_name} <{$from}>\n";
		$headers .= "X-Sender: <{$from}>\n";
		$headers .= "X-Mailer: PHP ".phpversion()."\n";
		$headers .= "X-Priority: 1\n";
		$headers .= "Return-Path: <{$from}>\n";

		if(!$type) $headers .= "Content-Type: text/plain; ";
		else $headers .= "Content-Type: text/html; ";
		$headers .= "charset=euc-kr\n";

		if($cc)  $headers .= "cc: {$cc}\n";
		if($bcc)  $headers .= "bcc: {$bcc}";

		$comment = stripslashes($comment);
		$comment = str_replace("\n\r","\n", $comment);

		return mail($recipient , $subject , $comment , $headers);

	}







/**************************************************
 폼처리 관련 함수
**************************************************/

	//주민등록번호 확인
	function ChkSIDNum($jumin) { 
		$weight = '234567892345'; // 자리수 weight 지정 
		$len = strlen($jumin); 
		$sum = 0; 

		if ($len <> 13) return false;

		for ($i = 0; $i < 12; $i++) { 
			$sum = $sum + (substr($jumin,$i,1)*substr($weight,$i,1)); 
		} 

		$rst = $sum%11; 
		$result = 11 - $rst; 

		if ($result == 10) $result = 0;
		else if ($result == 11) $result = 1;

		$ju13 = substr($jumin,12,1); 

		if ($result <> $ju13) return false;
		return true; 
	}


	//인젝션 공격성 문자 검사
	function IjStr($str) {
		if(eregi("(select|delete|insert|update|drop|shutdown|exec|;|--|')", $str))
			return true;
		else
			return false;
	}


	//input 및 textarea의 사이즈를 NS와 IE를 구분하여 리턴
	function FieldSize($size) {
		global $browser;
		if(!$browser) return " size=".($size*0.6)." ";
		else return " size={$size} ";
	}

	function FieldSize2($size) {
		global $browser;
		if(!$browser) return " cols=".($size*0.6)." ";
		else return " cols={$size} ";
	}







/**************************************************
 트랙백 관련 함수
**************************************************/

	//트랙백핑을 보내는 함수
	function send_tb($t_url,$url,$title,$blog_name,$excerpt) {
		global $tb_error_str;

		//주소가 유효한지 검사
		$p_fp = @fopen($t_url,'r');
		if($p_fp) {
			fclose($p_fp);
		}
		else {
			$tb_error_str = "트랙백 URL이 존재하지 않습니다.";
			return false;
		}

		//내용 정리
		$title = StripHtmlChars($title);
		$excerpt = StripHtmlChars($excerpt);
		$t_data = "url=".rawurlencode($url)."&title=".rawurlencode($title)."&blog_name=".rawurlencode($blog_name)."&excerpt=".rawurlencode($excerpt);

		//주소 처리
		$uinfo = parse_url($t_url);
		if($uinfo["query"]) $t_data .= "&".$uinfo["query"];
		if(!$uinfo["port"]) $uinfo["port"] = "80";

		//최종 전송 자료 프로토콜 형식
		$send_str = "POST ".$uinfo["path"]." HTTP/1.1\r\n".
					"Host: ".$uinfo["host"]."\r\n".
					"User-Agent: MTools\r\n".
					"Content-Type: application/x-www-form-urlencoded\r\n".
					"Content-length: ".strlen($t_data)."\r\n".
					"Connection: close\r\n\r\n".
					$t_data;

		//전송
		$fp = fsockopen($uinfo["host"],$uinfo["port"]);
		fputs($fp,$send_str);

		//응답 수신
		while(!feof($fp)) $response .= fgets($fp,128);
		fclose($fp);

		//응답내용이 트랙백 URL인지 확인
		if(!strstr($response,"<response>")) {
			$tb_error_str = "올바른 트랙백 URL이 아닙니다.";
			return false;
		}

		//XML 부분만 추출
		$response = strchr($response,"<?");
		$response = substr($response,0,strpos($response,"</response>"));

		//에러 검사
		if(strstr($response,"<error>0</error>")) {
			return true;
		}
		else {
			$tb_error_str = strchr($response,"<message>");
			$tb_error_str = substr($tb_error_str,0,strpos($tb_error_str,"</message>"));
			$tb_error_str = str_replace("<message>","",$tb_error_str);
			return false;
		}
	}


	//트랙백핑을 받는 함수
	function receive_tb($id, $idx, $url, $title, $blog_name, $excerpt) {
		global $dbConnect, $_table_id_admin, $_table_id_board, $_table_id_trackback, $allowTrackback;

		@ob_end_clean();

		header("Content-Type:text/xml;");
		echo "<"."?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">\n<response>\n";

		if(!$url || !$title || !$blog_name || !$excerpt) {
			$msg = "";
			if(!$url) { $msg .= " url"; if(!$title || !$blog_name || !$excerpt) $msg .= ","; }
			if(!$title) { $msg .= " title"; if(!$blog_name || !$excerpt) $msg .= ","; }
			if(!$blog_name) { $msg .= " blog_name"; if(!$excerpt) $msg .= ","; }
			if(!$excerpt) { $msg .= " excerpt"; }

			die("<error>1</error>\n<message>Missing Parameter(s) :".$msg."</message>\n</response>");
		}

		if(!$id || !$idx) {
			$msg = "";
			if(!$id) { $msg .= " id"; if(!$idx) $msg .= ","; }
			if(!$idx) { $msg .= " idx"; }

			die("<error>1</error>\n<message>REQUIRED PARAMETER(S) IS(ARE) MISSING: ".$msg."</message>\n</response>");
		}

		$url = StrAddSlashes($url);
		$title = StrAddSlashes($title);
		$blog_name = StrAddSlashes($blog_name);
		$excerpt = StrAddSlashes($excerpt);

		if(!eregi($id,$_table_id_board)) $_table_id_board .= $id;

		$rs = mysql_fetch_array(mysql_query("SELECT * FROM {$_table_id_admin} WHERE boardId='{$id}';"));
		if($rs["useTrackback"]) {
			$tid = @mysql_fetch_row(mysql_query("SELECT count(*) FROM {$_table_id_admin} WHERE boardId = '{$id}';"));
			if(!$tid[0]) die("<error>1</error>\n<message>Wrong Parameter : id</message>\n</response>");

			$tidx = @mysql_fetch_row(mysql_query("SELECT count(*) FROM ".$_table_id_board." WHERE idx = {$idx};"));
			if(!$tidx[0]) die("<error>1</error>\n<message>Wrong Parameter : idx</message>\n</response>");

			mysql_query("INSERT INTO ".$_table_id_trackback." (boardId,objNum,tb_url,tb_title,tb_blog_name,tb_excerpt,tb_regdate) VALUES ('{$id}','{$idx}','{$url}','{$title}','{$blog_name}','{$excerpt}','".mktime()."');")
				or die("<error>1</error>\n<message>Database Insert Failed</message>\n</response>");

			mysql_query("UPDATE ".$_table_id_board." SET tbNum=tbNum+1 WHERE idx={$idx};")
				or die("<error>1</error>\n<message>Database Insert Failed</message>\n</response>");

			echo "<error>0</error>\n</response>";
		}
		else die("<error>1</error>\n<message>The trackback ping could not be granted a privilege to access on this site.</message>\n</response>");

		exit;
	}






/**************************************************
 GD관련 함수
**************************************************/

	//업로드된 이미지를 썸네일이미지로 만드는 함수
	function photoUpload($id,$file,$photoX,$photoY,$thumbX,$thumbY,$path) { // 썸네일 만들기
		if(is_uploaded_file($_FILES[$file]['tmp_name'])) {
			$src = getimagesize($_FILES[$file]['tmp_name']); //원본
			if ($src[2] == 1) {
				$srcImg = imagecreatefromgif($_FILES[$file]['tmp_name']);
			} else if ($src[2] == 2) {
				$srcImg = imagecreatefromjpeg($_FILES[$file]['tmp_name']);
			} else if ($src[2] == 6) {
				$srcImg = imagecreatefromwbmp($_FILES[$file]['tmp_name']);
			} else {
				echo "<script type=\"text/javascript\">\n//<![CDATA[\nalert('GIF,JPG,BMP file only');\n//]]>\n</script>";
			}

			$srcName = $id . '.jpg'; //아이디로 만든 저장될 이름
			$srcX = $src[0]; //원본 가로
			$srcY = $src[1]; //원본 세로
			$photoPath = $path . $srcName;
			$thumbPath = $path . "th_" . $srcName;

			if ($srcX > $photoX || $srcY > $photoY) {
				if ($srcX > $srcY) {
					$targPhotoY = ceil(($srcY * $photoX) / $srcX);
					$targPhotoX = $photoX;
				} else {
					$targPhotoX = ceil(($srcX * $photoY) / $srcY);
					$targPhotoY = $photoY;
				}
			} else {
				$targPhotoX = $srcX;
				$targPhotoY = $srcY;
			}

			if ($srcX > $thumbX || $srcY > $thumbY) {
				if ($srcX > $srcY) {
					$targThumbY = ceil(($srcY * $thumbX) / $srcX);
					$targThumbX = $thumbX;
				} else {
					$targThumbX = ceil(($srcX * $thumbY) / $srcY);
					$targThumbY = $thumbY;
				}
			} else {
				$targThumbX = $srcX;
				$targThumbY = $srcY;
			}

			$photoImg = imagecreatetruecolor($targPhotoX, $targPhotoY);//빈이미지를 만들어주고
			$thumbImg = imagecreatetruecolor($targThumbX, $targThumbY);//빈이미지를 만들어주고
			imagecopyresampled($photoImg, $srcImg, 0, 0, 0, 0, $targPhotoX, $targPhotoY, $srcX, $srcY);//줄여서 그려준다
			imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $targThumbX, $targThumbY, $srcX, $srcY);//줄여서 그려준다

			unsharpMask($thumbImg,'80','0.5','3');

			imagejpeg($photoImg, $photoPath, 85); //화일로 출력
			imagejpeg($thumbImg, $thumbPath, 85); //화일로 출력

			chmod($photoPath, 0707); // 파일 퍼미션 변경
			chmod($thumbPath, 0707); // 파일 퍼미션 변경
			imagedestroy($thumbImg);//메모리 비워주기
			imagedestroy($photoImg);//메모리 비워주기
			imagedestroy($srcImg);//메모리 비워주기
		}
	}


	//이미지필터 함수 - 손상된 썸네일이미지를 샤프하고 크리스피하게 보이게할때 사용
	function unsharpMask($img,$amount,$radius,$threshold) {
		if ($amount > 500) $amount = 500;
		$amount = $amount * 0.016;
		if ($radius > 50) $radius = 50;
		$radius = $radius * 2;
		if ($threshold > 255) $threshold = 255;

		$radius = abs(round($radius));
		if ($radius == 0) return $img;
		$w = imagesx($img); $h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgCanvas2 = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);
		$imgBlur2 = imagecreatetruecolor($w, $h);
		imagecopy ($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
		imagecopy ($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);

		imagecopy ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h);

		for ($i = 0; $i < $radius; $i++) {
				if (function_exists('imageconvolution')) {
						$matrix = array(
								array( 1, 2, 1 ),
								array( 2, 4, 2 ),
								array( 1, 2, 1 )
								);
						imageconvolution($imgCanvas, $matrix, 16, 0);

				} else {

						// Move copies of the image around one pixel at the time and merge them with weight
						// according to the matrix. The same matrix is simply repeated for higher radii.

						imagecopy      ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); // up left
						imagecopymerge ($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
						imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); // down left
						imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); // up right

						imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); // left
						imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
						imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); // up
						imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down

						imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
						imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

						// During the loop above the blurred copy darkens, possibly due to a roundoff
						// error. Therefore the sharp picture has to go through the same loop to
						// produce a similar image for comparison. This is not a good thing, as processing
						// time increases heavily.
						imagecopy ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20 );
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667);
						imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
						imagecopy ($imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h);

				}
		}
		//return $imgBlur;

		// Calculate the difference between the blurred pixels and the original
		// and set the pixels
		for ($x = 0; $x < $w; $x++)    { // each row
				for ($y = 0; $y < $h; $y++)    { // each pixel

						$rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
						$rOrig = (($rgbOrig >> 16) & 0xFF);
						$gOrig = (($rgbOrig >> 8) & 0xFF);
						$bOrig = ($rgbOrig & 0xFF);

						$rgbBlur = ImageColorAt($imgCanvas, $x, $y);

						$rBlur = (($rgbBlur >> 16) & 0xFF);
						$gBlur = (($rgbBlur >> 8) & 0xFF);
						$bBlur = ($rgbBlur & 0xFF);

						// When the masked pixels differ less from the original
						// than the threshold specifies, they are set to their original value.
						$rNew = (abs($rOrig - $rBlur) >= $threshold)
								? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
								: $rOrig;
						$gNew = (abs($gOrig - $gBlur) >= $threshold)
								? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
								: $gOrig;
						$bNew = (abs($bOrig - $bBlur) >= $threshold)
								? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
								: $bOrig;

						if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
								$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
								ImageSetPixel($img, $x, $y, $pixCol);
						}
				}
		}
		return $img;
	}


	// 세션변수 생성
	function set_session($session_name, $value)
	{
		session_register($session_name);
		// PHP 버전별 차이를 없애기 위한 방법
		$$session_name = $_SESSION["$session_name"] = $value;
	}


	// 세션변수값 얻음
	function get_session($session_name)
	{
		return $_SESSION[$session_name];
	}


	// 쿠키변수 생성
	function set_cookie($cookie_name, $value, $expire)
	{
		global $g4;

		setcookie(md5($cookie_name), base64_encode($value), 0, '/');
	}


	// 쿠키변수값 얻음
	function get_cookie($cookie_name)
	{
		return base64_decode($_COOKIE[md5($cookie_name)]);
	}

	// 방문자수 출력
	function visit($skin_dir="basic")
	{
		global $config, $g4;

		// visit 배열변수에 
		// $visit[1] = 오늘
		// $visit[2] = 어제
		// $visit[3] = 최대
		// $visit[4] = 전체
		// 숫자가 들어감
		preg_match("/오늘:(.*),어제:(.*),최대:(.*),전체:(.*)/", $config['cf_visit'], $visit);

		ob_start();
		$visit_skin_path = "$g4[path]/skin/visit/$skin_dir";
		include_once ("$visit_skin_path/visit.skin.php");
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	// get_browser() 함수는 이미 있음
	function get_brow($agent)
	{
		$agent = strtolower($agent);

		//echo $agent; echo "<br/>";

		if (preg_match("/msie 5.0[0-9]*/", $agent))         { $s = "MSIE 5.0"; }
		else if(preg_match("/msie 5.5[0-9]*/", $agent))     { $s = "MSIE 5.5"; }
		else if(preg_match("/msie 6.0[0-9]*/", $agent))     { $s = "MSIE 6.0"; }
		else if(preg_match("/msie 7.0[0-9]*/", $agent))     { $s = "MSIE 7.0"; }
		else if(preg_match("/msie 4.[0-9]*/", $agent))      { $s = "MSIE 4.x"; }
		else if(preg_match("/firefox/", $agent))            { $s = "FireFox"; }
		else if(preg_match("/x11/", $agent))                { $s = "Netscape"; }
		else if(preg_match("/opera/", $agent))              { $s = "Opera"; }
		else if(preg_match("/gec/", $agent))                { $s = "Gecko"; }
		else if(preg_match("/bot|slurp/", $agent))          { $s = "Robot"; }
		else if(preg_match("/internet explorer/", $agent))  { $s = "IE"; }
		else if(preg_match("/mozilla/", $agent))            { $s = "Mozilla"; }
		else { $s = "기타"; }

		return $s;
	}

	function get_os($agent)
	{
		$agent = strtolower($agent);

		//echo $agent; echo "<br/>";

		if (preg_match("/windows 98/", $agent))                 { $s = "98"; }
		else if (preg_match("/windows 95/", $agent))            { $s = "95"; }
		else if(preg_match("/windows nt 4\.[0-9]*/", $agent))   { $s = "NT"; }
		else if(preg_match("/windows nt 5\.0/", $agent))        { $s = "2000"; }
		else if(preg_match("/windows nt 5\.1/", $agent))        { $s = "XP"; }
		else if(preg_match("/windows nt 5\.2/", $agent))        { $s = "2003"; }
		else if(preg_match("/windows 9x/", $agent))             { $s = "ME"; }
		else if(preg_match("/windows ce/", $agent))             { $s = "CE"; }
		else if(preg_match("/mac/", $agent))                    { $s = "MAC"; }
		else if(preg_match("/linux/", $agent))                  { $s = "Linux"; }
		else if(preg_match("/sunos/", $agent))                  { $s = "sunOS"; }
		else if(preg_match("/irix/", $agent))                   { $s = "IRIX"; }
		else if(preg_match("/phone/", $agent))                  { $s = "Phone"; }
		else if(preg_match("/bot|slurp/", $agent))              { $s = "Robot"; }
		else if(preg_match("/internet explorer/", $agent))      { $s = "IE"; }
		else if(preg_match("/mozilla/", $agent))                { $s = "Mozilla"; }
		else { $s = "기타"; }

		return $s;
	}
?>