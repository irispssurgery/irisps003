<?
ob_start();
session_start();


/*************************************************************
	FSBOARD Logout
*************************************************************/


	include $_SERVER["DOCUMENT_ROOT"]."/fsboard/lib/lib.php";

	if(eregi(":\/\/",$INC_PATH)||eregi("\.\.",$INC_PATH)) $INC_PATH ="./";

	include_once $_SERVER["DOCUMENT_ROOT"]."lib/lib.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."lib/conf.php";


	//변수 소멸
	unset($MemId);
	unset($MemPasswd);
	unset($MemLevel);
	unset($MemName);

	//세션변수 비움
	$_SESSION["MemId"] = "";
	$_SESSION["MemPasswd"] = "";
	$_SESSION["MemLevel"] = "";
	$_SESSION["MemName"] = "";

	//자동로그인 쿠키 만료
	ExpireCookie("amid");
	ExpireCookie("pswd");

	//세션 파괴
	session_destroy();
	//session_unset();

	//외부지정 로그인 파일 포함
	//include $INC_PATH."logout.php";

	//페이지 복귀
	//$url = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : $_SERVER["PHP_SELF"]."?".QryStr("list");


	MovePage("/");
?>
