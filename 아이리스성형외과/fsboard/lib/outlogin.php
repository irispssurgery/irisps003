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
			$auth_login = trim($_POST["mode"]);//관리자모드
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
					$_SESSION["Auth_login"] = $auth_login;	

					$query = "UPDATE ".$_table_id_members." SET mem_latestdate=".mktime().", mem_ip_login='".$_SERVER["REMOTE_ADDR"]."', mem_loginnum=mem_loginnum+1, mem_loginfailed=0 WHERE mem_id='".$mem_id."';";
					mysql_query($query) or Error(mysql_error());

					if($mem_loginfailed) {
						echo "<script type=\"text/javascript\">window.alert('정상적으로 로그인 되었습니다.\\n\\n".date("Y-m-d H:i:s",$mem_faildate)." 에 로그인을 {$mem_loginfailed} 번 실패하였습니다.\\n해킹시도일 가능성이 있으므로 암호를 변경하시기 바랍니다.');window.location.href='".(!$HTTP_REFERER?"/":$HTTP_REFERER)."';</script>";
						exit;
					}
					else {
						MovePage(!$ref_url?"/":$ref_url);
						exit;
					}
				}
				else Error("회원 정보가 일치하지 않습니다.");
			}
		}
	}
	echo ("
						<script> 
						window.location.href='".(!$HTTP_REFERER?"/":$HTTP_REFERER)."';
						</script>
	");

}

////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
?>






<? if(!$_SESSION["MemId"]) { //////////////////////////////////////////////////로그인 이전 ?>
			<!--로그인 폼 시작 -->
			<div id="outer" style="height:334px">
				<div id="outer-line">
					<div id="outer-pad" style="height:324px">
						<table>
						<form name="frmLogin" method="post" action="<?=$_SERVER["$PHP_SELF"]?>">
						<input type="hidden" name="ref_url" value="<?=$_SERVER["HTTP_REFERER"]?>">  
						<tr valign="top">
							<td width="239"><img src="../../member/image/img_login.jpg" title="로그인" /></td>
							<td style="padding:34px 0 0 10px">
								<div><img src="../../member/image/login_h1.gif" title="로그인" /></div>
									
								<table width="263" style="margin:29px 0 0 11px">
								<input type="hidden" name="urlInfo" value="01">
								<input type="hidden" name="url" value="">
								<tr align="right" valign="top" height="28">
									<td><img src="../../member/image/login_id.gif" title="회원ID" style="margin-top:5px"/></td><td><input type="text" name="mem_id" style="width:146px;" class="input-login"/></td><td rowspan="2"><a href="javascript:authLogin();"><img src="../../member/image/btn_login.gif" style="margin-top:1px" border="0" title="로그인"/></a></td>
								</tr>
								<tr align="right" valign="top">
									<td><img src="../../member/image/login_pw.gif" title="비밀번호" style="margin-top:5px" /></td><td><input type="password" style="width:146px;" class="input-login" name="mem_passwd" onKeyPress="if(event.keyCode==13){authLogin();}" /></td>
								</tr>
								</table>
								<table style="margin:37px 0 0 2px">
								<tr>
									<td width="216"><img src="../../member/image/login_txt1.gif" title="거제백병원에 가입하시겠습니까?" /></td><td><a href="member_2.html"><img src="../../member/image/btn_join.gif" title="회원가입" /></a><input type="hidden" name="loginmode" value="true"></td>
								</tr>
								<tr height="7"><td></td></tr>
								<tr>
									<td><img src="../../member/image/login_txt2.gif" title="아이디/비밀번호가 기억나지 않으세요?" /></td><td><a href="member_3.html" onfocus="this.blur();"><img src="../../member/image/btn_find.gif" title="아이디/비밀번호 찾기" /></a></td>
								</tr>
								</table>
							</td>
						</tr>
						</form>
						</table>
					</div>
				</div>	
			</div>

			<script type="text/javascript">function authLogin(){var frm=document.forms["frmLogin"];if(!frm.mem_id.value){alert("아이디를 입력해 주세요.");frm.mem_id.focus();}else if(!frm.mem_passwd.value){alert("암호를 입력해 주세요.");frm.mem_passwd.focus();}else{frm.submit();} }</script>
			<!--로그인 폼 끝 -->
<? } else { //////////////////////////////////////////////////로그인 이후 ?>
			<!--로그인 이후 시작 -->
<?
	echo ("
						<script> 
						window.location.href='".(!$HTTP_REFERER?"/":$HTTP_REFERER)."';
						</script>
	");
?>
		<!--<table style="border:1px solid #E0E0E0;">
			<tr>
				<td>
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
				</td>
			</tr>
		</table>-->
		<!-- 로그인 이후 끝 -->
<? } ?>