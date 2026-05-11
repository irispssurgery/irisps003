<?
/*************************************************************

	FSBOARD Setup Manager 1.0

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 11, 2007

	Copyright(c)2000-2007 FSBOARD. All rights reserved.

*************************************************************/


/**************************************************
 Setup 초기화
**************************************************/

	//라이브러리 포함
	include_once $INC_PATH."lib.php";

	//변수 초기화
	$MODE = trim($_GET["mode"]);
	$EXEC = trim($_GET["exec"]);

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("setup.php",$_SERVER["PHP_SELF"])) ? true : false;

	//DB연결
	if($MODE) DbConn();



$MemId=$_SESSION["MemId"];
echo "$MemId";
?>

