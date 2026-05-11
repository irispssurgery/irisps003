<?
/*************************************************************
	FSBOARD Authentication
*************************************************************/


	if(!$login_included) exit; //login.php 직접접근 막기

	$nav = trim($_GET["nav"]);
	$cidx = intval($_GET["cidx"]);
	$tidx = intval($_GET["tidx"]);
	$usrAuth = false;

	if($nav == "edit") {
		$title = ereg("english",$skin) ? "Authentication for Edit" : "게시물 수정";

		$query = "SELECT isMember FROM ".$_table_id_board." WHERE idx={$idx};";
		$result = mysql_query($query) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		if(!empty($MemId)&&$rs[0]==$MemId) $usrAuth = true;
		mysql_free_result($result);
	}

	else if($nav == "remove") {
		$title = ereg("english",$skin) ? "Authentication for Delete" : "게시물 삭제";

		$query = "SELECT isMember FROM ".$_table_id_board." WHERE idx={$idx};";
		$result = mysql_query($query) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		if(!empty($MemId)&&$rs[0]==$MemId) $usrAuth = true;
		mysql_free_result($result);
	}

	else if($nav == "removeMemo") {
		$title = ereg("english",$skin) ? "Authentication for Delete to Comment" : "메모글 삭제";

		$query = "SELECT isMember FROM ".$_table_id_comment." WHERE cidx={$cidx};";
		$result = mysql_query($query) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		if(!empty($MemId)&&$rs[0]==$MemId) $usrAuth = true;
		mysql_free_result($result);
	}

	else if($nav == "removeTrackback") {
		$title = ereg("english",$skin) ? "Authentication for Delete to Trackback" : "트랙백 기록 삭제";
	}

	else if($nav == "multidelete") {
		$title = ereg("english",$skin) ? "Authentication for Multi-Delete" : "게시물 다중 삭제";
	}

	else if($nav == "multimoveobjs") {
		$title = ereg("english",$skin) ? "Authentication for Multi-Move" : "게시물 이동";
	}

	else if($nav == "view") {
		$title = ereg("english",$skin) ? "Authentication for View Secret Document" : "비밀글 보기";
	}

	else if($nav == "admin") {
		$title = ereg("english",$skin) ? "Login for Administrator" : "관리자 로그인";
	}

	else if($nav == "login") {
		$title = ereg("english",$skin) ? "LOGIN" : "로그인";
	}

	else {
		$title = ereg("english",$skin) ? "AUTHENTICATION" : "암호를 입력하세요";
	}


	/////버튼
	$btnPath = "$FSBOARD_PATH/img/btn"; //버튼 경로

	$btnSubmit = "<a href=\"javascript:auth();\" class=\"lnk_login\"><img src=\"{$btnPath}/submit.gif\" alt=\"확인\" /></a>";
	$btnCancel = "<a href=\"javascript:window.history.back();\" class=\"lnk_login\"><img src=\"{$btnPath}/cancel.gif\" alt=\"취소\" /></a>";

	$btnMoveSubmit = "<a href=\\\"javascript:if(confirm('선택한 게시물(들)을 정말 이동하시겠습니까?')){auth();}else{history.back();}\\\" class=\\\"lnk_login\\\"><img src=\\\"{$btnPath}/submit.gif\\\" alt=\\\"확인\\\" /></a>";
	$btnMoveCancel = "<a href=\\\"javascript:window.history.back();\\\" class=\\\"lnk_login\\\"><img src=\\\"{$btnPath}/cancel.gif\\\" alt=\\\"취소\\\" /></a>";

	if(!$combinedDesign) {
		$btnJoin = "<a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=join','','width=570,height=550,resizable=1,scrollbars=1')\" class=\"lnk_login\"><img src=\"{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />회원가입</a>";
		$btnForgotPasswd = "<a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=forgot_passwd','forgot_login','width=490,height=425,resizable=1,scrollbars=1');\" class=\"lnk_login\"><img src=\"{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />암호분실</a>";
	}







////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
ContentTop();





//로그인폼 스타일
echo "
	<style type=\"text/css\">
	/* 로그인폼 스타일 */
	a.lnk_login, a:visited.lnk_login { color:#555555; text-decoration:none; }
	a:hover.lnk_login, a:active.lnk_login { color:#0000FF; text-decoration:none; }
	.login_txtbox { border:1px solid #E7E7E7; font-size:12px; font-family:Tahoma; width:200px; ime-mode:inactive; }
	</style>
";

//인증후 자동전송 스크립트
echo "
	<script type=\"text/javascript\">
	//<![CDATA[
	function auth() {
		var frm = document.forms.__ctl;
		var aid = frm.auth_id;
		var apw = frm.auth_passwd;

		frm.action = '{$PHP_SELF}?".QryStr($nav)."&nav={$nav}&idx={$idx}&cidx={$cidx}&tidx={$tidx}&page={$page}';
		frm.submit();
	}
	//]]>
	</script>
";


//다중삭제 또는 다중 이동일경우 게시물의 고유번호들을 폼전송에 포함
if($nav=="multidelete" || $nav=="multimoveobjs") {
	for($i=0; $i<$pageSize; $i++) {
		${"idx".$i} = $_GET["idx".$i];
		if(${"idx".$i}) echo "<input type=\"hidden\" name=\"idx{$i}\" value=\"".${"idx".$i}."\" />\n";
	}
}









if($isAdmin||$usrAuth||$delete_level) { //////////////////////////////////////////////////관리자이거나 권한인증되었을 경우

	if(!$usrAuth) echo "<input type=\"hidden\" name=\"auth_passwd\" value=\"{$adminPasswd}\" />";
	else echo "<input type=\"hidden\" name=\"auth_passwd\" value=\"".$_SESSION["MemPasswd"]."\" />";

	echo "
	<style type=\"text/css\">
	#AuthFrame { margin:0px auto; font-size:13px; }
		#AuthLayout { margin:0px auto; width:400px; }
			#AuthMainLayout { text-align:center; border-left:1px solid #F0F0F0; border-right:1px solid #F0F0F0; border-bottom:1px solid #F0F0F0; }
				#AuthMainTitle { border-top:1px solid #F0F0F0; border-bottom:1px solid #F0F0F0; background-color:#FAFBF7; }
					#AuthMainTitleMsg { margin:5px; font-weight:bold; }
				#AuthMainBody { margin:18px; border:0px solid red; }
					#AuthMainElementMove { margin:5px; }
			#AuthBtn { margin:7px; text-align:center; }
	</style>
	<script type=\"text/javascript\">
	//<![CDATA[
	var strNav = '{$nav}';

	function pre_processing() {
		if(strNav=='view' || strNav=='edit' || strNav=='editMemo') {
			auth();
		}
		else if(strNav=='remove' || strNav=='removeMemo' || strNav=='removeTrackback') {
			if(confirm('정말 삭제하시겠습니까?')) { auth(); } else { window.history.back(); }
		}
		else if(strNav=='multidelete') {
			if(confirm('선택한 게시물(들)을 정말 삭제하시겠습니까?')) { auth(); } else { window.history.back(); }
		}
		else if(strNav=='multimoveobjs') {
	";
	if($nav=="multimoveobjs") { //관리자 인증상태에서 게시물이동일 경우
		$query = "SELECT aidx,boardId,boardName FROM {$_table_id_admin} ORDER BY aidx ASC;";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			echo "
			document.write(\"<br /><br />\");
			document.write(\"<div id=\\\"AuthFrame\\\">\");
				document.write(\"<div id=\\\"AuthLayout\\\">\");


						document.write(\"<div id=\\\"AuthMainLayout\\\">\");
							document.write(\"<div id=\\\"AuthMainTitle\\\">\");
								document.write(\"<div id=\\\"AuthMainTitleMsg\\\">\");
									document.write(\"$title\");
								document.write(\"</div>\");
							document.write(\"</div>\");
							document.write(\"<div id=\\\"AuthMainBody\\\">\");
								document.write(\"<div id=\\\"AuthMainElementMove\\\">\");
									document.write(\"이동시키고자 하는 대상 게시판을 선택하세요.<br />\");
									document.write(\"<select name=\\\"targetBoardId\\\">\");
			";
			while($rs = mysql_fetch_array($result)) {
				$board_id = $rs["boardId"];
				$board_name = $rs["boardName"];
				if($board_name) $board_name = "({$board_name})";
				echo "document.write(\"<option value=\\\"{$board_id}\\\">{$board_id}{$board_name}</option>\");";
			}
			echo "
									document.write(\"</select>\");
								document.write(\"</div>\");
							document.write(\"</div>\");
						document.write(\"</div>\");
						document.write(\"<div id=\\\"AuthBtn\\\">\");
							document.write(\"{$btnMoveSubmit} {$btnMoveCancel}<br /><br />\");
						document.write(\"</div>\");


				document.write(\"</div>\");
			document.write(\"</div>\");
			document.write(\"<br /><br /><br />\");
			";
		}
	}
	echo "
		}
		else {
			window.alert('Invalid mode');
		}
	}

	window.onLaod = pre_processing();
	//]]>
	</script>
	";









}
else { ////////////////////////////////////////////////////////////////////// 암호묻기 또는 로그인
?>
<style type="text/css">
<!--
#AuthFrame { margin:0px auto; font-size:13px; text-align:center; }
	#AuthLayout { margin:0px auto; width:380px; }
		#AuthMainLayout { text-align:center; border-left:1px solid #F0F0F0; border-right:1px solid #F0F0F0; border-bottom:1px solid #F0F0F0; }
			#AuthMainTitle { border-top:1px solid #F0F0F0; border-bottom:1px solid #F0F0F0; background-color:#FAFBF7; }
				#AuthMainTitleMsg { margin:5px; font-weight:bold; }
			#AuthMainBody { margin:18px; border:0px solid red; }
				#AuthMainElementMove { margin:5px; }
				#AuthMainElementId { margin:2px; }
					#AuthMainElementId1 { float:left; width:110px; font-family:Verdana; text-align:right; }
					#AuthMainElementId2 { text-align:left; }
				#AuthMainElementPw { margin:2px; }
					#AuthMainElementPw1 { float:left; width:110px; font-family:Verdana; text-align:right; }
					#AuthMainElementPw2 { text-align:left; }
					#AuthMainElementPw3 { font-size:11px; color:red; }
		#AuthBtn1 { margin:10px; text-align:center; }
		#AuthBtn2 { margin:25px; text-align:center; }
-->
</style>
<br />
<div id="AuthFrame">
	<div id="AuthLayout">
		<div id="AuthMainLayout">
			<div id="AuthMainTitle">
				<div id="AuthMainTitleMsg"><?=$title?></div>
			</div>
			<div id="AuthMainBody">
<?
	//게시물 이동일 경우 설치된 게시판 목록 보이기
	if($nav=="multimoveobjs") {
		$query = "SELECT boardId FROM {$_table_id_admin} ORDER BY aidx ASC;";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			echo "
				<div id=\"AuthMainElementMove\">
					이동할 게시판 선택
					<select name=\"targetBoardId\">
			";
			while($rs = mysql_fetch_array($result)) {
				$board_id = $rs["boardId"];
				echo "\n<option value=\"{$board_id}\">{$board_id}</option>";
			}
			echo "
					</select>
				</div>
			";
		}
	}

	//로그인일 경우 아이디 입력 부분 보이기
	if($nav=="login" || $nav=="admin") {
		echo "
				<div id=\"AuthMainElementId\">
					<div id=\"AuthMainElementId1\">ID &nbsp;</div>
					<div id=\"AuthMainElementId2\"><input type=\"text\" name=\"auth_id\" size=\"25\" class=\"login_txtbox\" /></div>
				</div>
		";
	}
?>
				<div id="AuthMainElementPw">
					<div id="AuthMainElementPw1">Password &nbsp;</div>
					<div id="AuthMainElementPw2"><input type="password" name="auth_passwd" size="25" class="login_txtbox" onkeypress="chkCapsLock(event,'AuthMainElementPw3'); if(event.keyCode==13){auth();}" /></div>
					<span id="AuthMainElementPw3"></span>
				</div>
<?
	if($nav=="login") {
		echo "
				<div>
					<input type=\"checkbox\" id=\"auto_auth\" name=\"auto_auth\" value=\"1\" />자동로그인
				</div>
		";
	}
?>
			</div>
		</div>
		<div id="AuthBtn1">
			<input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>" />
			<?echo "{$btnSubmit} {$btnCancel}";?>
		</div>
		<div id="AuthBtn2">
			<?echo "{$btnJoin} &nbsp; {$btnForgotPasswd}";?>
		</div>
	</div>
</div>
<br />
<br />


<script type="text/javascript">
//<![CDATA[
<?
	if($nav=="login" || $nav=="admin") echo "document.getElementById('auth_id').focus();\n";
	else echo "document.getElementById('auth_passwd').focus();\n";

	echo "\n//]]>\n</script>\n";

} ////////////////////////////////////////////////////////////////////// 암호묻기 또는 로그인 끝
?>
