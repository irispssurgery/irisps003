<?
/*************************************************************
	FSBOARD Out Login Assemble Parts
*************************************************************/


@session_start();

	//라이브러리 포함
	include_once $INC_PATH."lib.php";


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

		if($loginmode==="true") {
			$mem_id = trim($_POST["mem_id"]);
			$mem_passwd = trim($_POST["mem_passwd"]);
			$mem_id = StrAddSlashes($mem_id);
			$mem_passwd = md5($mem_passwd);

			if(!$dbConnect) DBConn();

			//접근가능 확인
			$query = "SELECT mem_ip_failed,mem_loginfailed,mem_faildate FROM ".$_table_id_members." WHERE mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				if(mysql_num_rows($result)) {
					$rs = mysql_fetch_array($result);
					$mem_ip_failed = $rs["mem_ip_failed"];
					$mem_loginfailed = $rs["mem_loginfailed"];
					$mem_faildate = $rs["mem_faildate"];
					if($mem_loginfailed>5 && (mktime()-$mem_faildate)<60*30) {
						Error("로그인을 5번이상 실패하여 30분동안 접근금지 되었습니다.<br><br>무작위 해킹공격을 차단하기 위함이므로 양해 바랍니다.");
					}
				}
				mysql_free_result($result);
			}

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
	MovePage(!$HTTP_REFERER?"/":$HTTP_REFERER);
	/*echo ("
						<script> 
						parent.window.close(); 
						parent.opener.location.reload(); 
						</script>
	");*/

}

////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
?>



 
   



<table style="border:1px solid #E0E0E0;">
	<tr>
		<td>
<? if(!$_SESSION["MemId"]) { //////////////////////////////////////////////////로그인 이전 ?>
			<!--로그인 폼 시작 -->
                        <table width="575" border="0" cellspacing="0" cellpadding="0" align="center">
						<form name="frmLogin" method="post" action="<?=$_SERVER["$PHP_SELF"]?>">
						<input type="hidden" name="loginmode" value="true">
                          <tr> 
                            <td valign="top" height="337" background="images/member_bg.gif"> 
                              <table width="500" border="0" cellspacing="0" cellpadding="0" align="center">
                                <tr> 
                                  <td height="102">&nbsp;</td>
                                </tr>
                                <tr> 
                                  <td> 
								  <form name="member_login" method="post" action="proc_login.php" onsubmit="return checkForm();">
                                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                                      <tr> 
                                        <td width="83"><img src="images/member_id.gif" width="83" height="18"></td>
                                        <td> 
                                          <input type="text" name="mem_id"  class="input01" style="BORDER-RIGHT: #A8A292 1px solid; BORDER-TOP: #A8A292 1px solid; BORDER-LEFT: #A8A292 1px solid; BORDER-BOTTOM: #A8A292 1px solid; FONT-SIZE: 10pt; width: 148px; HEIGHT: 18px">
                                        </td>
                                        <td rowspan="2" width="72" align="right"> 
                                          <a href="javascript:authLogin();"><img height=58 width=55 src="images/member_enter.gif" name="image" border="0"></a>
                                        </td>
                                      </tr>
                                      <tr> 
                                        <td><img src="images/member_pw.gif" width="83" height="18"></td>
                                        <td> 
                                          <input type="password" name="mem_passwd"  class="input01" style="BORDER-RIGHT: #A8A292 1px solid; BORDER-TOP: #A8A292 1px solid; BORDER-LEFT: #A8A292 1px solid; BORDER-BOTTOM: #A8A292 1px solid; FONT-SIZE: 10pt; width: 148px; HEIGHT: 18px" onKeyPress="if(event.keyCode==13){authLogin();}">
                                        </td>
                                      </tr>
                                    </table>
									</form>
                                  </td>
                                </tr>
                                <tr> 
                                  <td>&nbsp;</td>
                                </tr>
                                <tr> 
                                  <td align="center"><a href="member_passwd.html"><img src="images/member_idpw.gif" width="133" height="23" border="0"></a> 
                                    <a href="member_join.html"><img src="images/member_join.gif" width="133" height="23" border="0"></a></td>
                                </tr>
                              </table>
                            </td>
                          </tr>
						</form>
                        </table>



			<script type="text/javascript">function authLogin(){var frm=document.forms["frmLogin"];if(!frm.mem_id.value){alert("아이디를 입력해 주세요.");frm.mem_id.focus();}else if(!frm.mem_passwd.value){alert("암호를 입력해 주세요.");frm.mem_passwd.focus();}else{frm.submit();} }</script>
			<!--로그인 폼 끝 -->
<? } else { //////////////////////////////////////////////////로그인 이후 ?>
			<!--로그인 이후 시작 -->

			<table>
				<tr>
					<td height="35" align="center">
						<b><?=$_SESSION["MemName"]?></b>님 로그인
					</td>
				</tr>
				<tr>
					<td height="20" align="center">
						<a href="<?="/{$FSBOARD_PATH}/lib"?>/logout.php">로그아웃</a>
						&nbsp;
						<a href="<?="/{$FSBOARD_PATH}/lib"?>members.php">정보수정</a>
					</td>
				</tr>
			</table>
			<!-- 로그인 이후 끝 -->
<? } ?>
		</td>
	</tr>
</table>

