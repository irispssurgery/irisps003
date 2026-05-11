<?php 
ob_start(); 
session_start(); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
extract($HTTP_COOKIE_VARS); 
 


if(eregi(":\/\/",$INC_PATH)||eregi("\.\.",$INC_PATH)) $INC_PATH ="./";

include_once "../../fsboard/lib/lib.php";

	//DB연결
	$dbConnect = DbConn();

	
	$mem_id = trim($_POST["auth_id"]);
	$mem_passwd = trim($_POST["auth_passwd"]);
	$referer = StrAddSlashes($_POST["referer"]);
	$nav = StrAddSlashes($_POST["nav"]);
	$nav = "/wb_admin";

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	if($mem_id && $mem_passwd) {
		$mem_passwd = md5($mem_passwd);
		$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND mem_passwd='{$mem_passwd}';";
		echo "$query";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$numrows = mysql_num_rows($result);
			if(!$numrows) { Error("관리자 정보가 일치하지 않습니다"); exit; }
			else {
				$rs = mysql_fetch_array($result);
				$mem_level = $rs["mem_level"];
				if($mem_level=="" || $mem_level>1) { Error("관리자 권한이 아닙니다."); exit; }
				else {
					$_SESSION["MemId"] = $rs["mem_id"];
					$_SESSION["MemPasswd"] = $rs["mem_passwd"];
					$_SESSION["MemLevel"] = $rs["mem_level"];
					$_SESSION["MemName"] = $rs["mem_name"];
					$_SESSION["Host"] = $_SERVER["HTTP_HOST"];

					MovePage($nav);
				}
			}
			mysql_free_result($result);
		}
	}else{
		MovePage($_SERVER["HTTP_REFERER"]);
	}

?>