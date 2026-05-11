<?
/*************************************************************

	FSBOARD Members Manager 1.0

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: November 5, 2007

	Copyright(c)2000-2007 FSBOARD. All Rights Reserved.

*************************************************************/



/**************************************************
 Members 초기화
**************************************************/

	//라이브러리 포함
	include_once $INC_PATH."lib.php";

	//게시판 웹 절대경로
	$FSBOARD_PATH = "/".$FSBOARD_PATH;

	//변수초기화
	$MODE = trim($_GET["mode"]);

	//기본사용변수
	$width = "98%";
	$align = "center";
	$MemDefaultLevel = sizeof($mem_part_element) - 1; //회원가입시 기본 레벨
	$MemId = $_SESSION["MemId"];

	//데이터 경로
	$FSDATA_ROOT = $_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH."/data";
	$dataPath = "__IMG_MEMBERS";

	//사진파일 업로드 용량 제한
	$fileMaxLimit = 2097152; //2MB

	//이미지 허용확장자
	$allowExts = "jpg,gif,bmp,png";

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("members.php",$_SERVER["PHP_SELF"])) ? true : false;

	//DB연결
	$dbConnect = DbConn();






/**************************************************
 전용 함수
**************************************************/

	//관리자 상단 기본내용
	function members_head() {
		global $combinedDesign, $FSBOARD_PATH;
		echo !$combinedDesign ? DclrDocType() : fslib();
		echo "
		<style type=\"text/css\">
		img { border:0; }
		.defstyle { font-size:12px; font-family:돋움, Verdana; }
		a.lnk_def:link, a.lnk_def:visited { color:#000; text-decoration:none; }
		a.lnk_def:hover, a.lnk_def:active { color:#00f; text-decoration:underline; }
		.txtbox { border:1px solid #E0E0E0; height:20px; background-color:#FBFAF7; font-size:12px; }
		.txtbox1 { border:1px solid #E0E0E0; height:20px; background-color:#FBFAF7; width:100%; font-size:12px; }
		.txtbox2 { border:1px solid #DBDBDB; background-color:#FBFAF7; font-size:12px; width:99%; overflow:auto; }
		</style>
		";
		echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/javascript.php\"></script>\n";
		if(!$combinedDesign) {
			echo "\n</head>\n<body>\n";
		}
		
	}

	//관리자 하단 기본내용
	function members_foot() {
		echo "\n</body>\n</html>";
	}





/**************************************************
 모드별 처리
**************************************************/

//////////////////////////////////////////////////////////////////////////////////////////회원 로그인
if(!$MODE || $MODE == "login") {

	$referer = str_replace("&", "&amp;", $_SERVER["HTTP_REFERER"]);
	$nav = StrAddSlashes(trim($_GET["nav"]));

	members_head();
?>
<style type="text/css">
#member_login_layout { width:400px; margin:0 auto; font-size:9pt; }
	#member_login_title { margin:0; font-family:Arial; }
	#member_login_layout p { clear:both; margin:0.5em; text-align:center; }
	#member_login_body { width:260px; margin:0 auto; text-align:center; }
	#member_login_layout .member_login_label { float:left; width:50px; margin:5px 0 5px 0; }
	#member_login_layout .member_login_input { float:left; }
	#member_login_layout span { width:100px; border:1px solid red; }
</style>
<div id="member_login_layout">
	<form id="member_login_form" method="post" action="<?=$_SERVER["PHP_SELF"]?>?mode=auth">
		<fieldset>
			<legend><h4 id="member_login_title">Member Login</h4></legend>
			<div id="member_login_body">
				<p>
					<div class="member_login_label">아이디</div>
					<div class="member_login_input"><input type="text" id="mem_id" name="mem_id" size="27" /></div>
				</p>
				<p>
					<div class="member_login_label">암호</div>
					<div class="member_login_input"><input type="password" id="mem_passwd" name="mem_passwd" size="27" onkeypress="if(event.keyCode==0x0D){this.form.submit();}" /></div>
				</p>
			</div>
		</fieldset>
		<p>
			<input type="button" value="  확 인  " onclick="document.getElementById('member_login_form').submit();" />
			<input type="button" value="  취 소  " onclick="window.history.back();" />
			<input type="hidden" id="referer" name="referer" value="<?=$referer?>" />
			<input type="hidden" id="nav" name="nav" value="<?=$nav?>" />
		</p>
	</form>
</div>
<?
	members_foot();










//////////////////////////////////////////////////////////////////////////////////////////로그인 인증
} else if($MODE == "auth") {

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	$mem_id = StrAddSlashes($_POST["mem_id"]);
	$mem_passwd = md5($_POST["mem_passwd"]);
	$referer = StrAddSlashes($_POST["referer"]);
	$nav = StrAddSlashes($_POST["nav"]);

	if(!$mem_id || !$mem_passwd) MovePage($_SERVER["HTTP_REFERER"]);

	if(eregi("[^a-zA-Z0-9_]", $mem_id)) {
		Error("입력값에 유효하지 않은 문자가 포함되어 있습니다.");
	}

	//접근가능 확인
	$query = "SELECT mem_ip_failed,mem_loginfailed,mem_faildate FROM ".$_table_id_members." WHERE mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."' AND mem_id='".$auth_id."';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		if(mysql_num_rows($result)) {
			$rs = mysql_fetch_array($result);
			$mem_ip_failed = $rs["mem_ip_failed"];
			$mem_loginfailed = $rs["mem_loginfailed"];
			$mem_faildate = $rs["mem_faildate"];

			if($mem_loginfailed>10 && (mktime()-$mem_faildate)<60*60) {
				Error("로그인을 10번이상 실패하여 한시간 동안 로그인이 금지 되었습니다.<br /><br />이후 부터는 한 실패할때마다 한시간씩 로그인이 제한 됩니다<br /><br />무작위 해킹공격을 방지하기 위함이므로 양해 바랍니다.<br /><br />다음 로그인 가능 시간까지 남은 시간 : ".intval((60*60-(mktime()-$mem_faildate))/60)."분");
			}
		}
		mysql_free_result($result);
	}

	//아이디 존재 여부 확인
	$query = "SELECT mem_id FROM ".$_table_id_members." WHERE mem_id='{$mem_id}';";
	$result = mysql_query($query) or die(mysql_error());

	if($result) $numrows = mysql_num_rows($result);
	else $numrows = 0;

	if(!$numrows) {
		Error("가입 되어 있지 않은 아이디입니다.");
	}

	//회원정보 확인
	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND mem_passwd='" . $mem_passwd . "';";
	$result = mysql_query($query) or Error(mysql_error());

	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) {
			//로그인 실패 내용 기록
			$query = "UPDATE ".$_table_id_members." SET mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."', mem_loginfailed=mem_loginfailed+1, mem_faildate=".mktime()." WHERE mem_id='{$mem_id}';";
			mysql_query($query) or Error(mysql_error());

			Error("회원 정보가 일치하지 않습니다.","","MsgBox");
		}
		else {
			$rs = mysql_fetch_array($result);
			mysql_free_result($result);

			$mem_id = $rs["mem_id"];
			$mem_passwd = $rs["mem_passwd"];
			$mem_level = $rs["mem_level"];
			$mem_name = $rs["mem_name"];
			$mem_nickname = $rs["mem_nickname"];
			$mem_latestdate = $rs["mem_latestdate"];
			$mem_faildate = $rs["mem_faildate"];
			$mem_loginfailed = $rs["mem_loginfailed"];

			if($rs["mem_passwd"]!=$mem_passwd) {
				Error("회원정보가 일치하지 않습니다.","","MsgBox");
			}
			else {
				$_SESSION["MemId"] = $mem_id;
				$_SESSION["MemPasswd"] = $mem_passwd;
				$_SESSION["MemLevel"] = $mem_level;
				$_SESSION["MemName"] = $mem_nickname ? $mem_nickname : $mem_name;

				//관리자일경우 관리권한 지정
				if($_SESSION["MemLevel"]==1) { $_SESSION["IsAdmin"] = true; }

				$query = "UPDATE ".$_table_id_members." SET mem_latestdate=".mktime().", mem_ip_login='".$_SERVER["REMOTE_ADDR"]."', mem_loginnum=mem_loginnum+1, mem_loginfailed=0 WHERE mem_id='".$mem_id."';";
				mysql_query($query) or Error(mysql_error());

				if($nav) { $url = $nav; }
				else if($referer) { $url = $referer; }
				else { $url = "/"; }

				if($mem_loginfailed) {
					$msg = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('{$mem_id}님 정상적으로 로그인 되었습니다.\\n\\n".date("Y-m-d H:i:s",$mem_faildate)." 에 로그인을 {$mem_loginfailed} 번 실패하였습니다.');window.location.href='{$url}';\n//]]>\n</script>\n";
					echo !$combinedDesign ? "<html><head>{$msg}</head><body></body></html>" : $msg;
				}
				else {
					MovePage($url);
					exit;
				}
				exit;
			}
		}
	}
	else {
		Error("데이터를 가져올 수 없습니다.");
	}










//////////////////////////////////////////////////////////////////////////////////////////회원가입폼(기본)
} else if(!$MODE || $MODE == "join") {

	members_head();

	if($combinedDesign) {
		$btnCancel = "javascript:window.history.back();";
		$width = "95%";
	}
	else {
		$btnCancel = "javascript:window.close();";
		$width = "45em";

		$bodyStyle = "
			document.body.leftMargin = '0';
			document.body.topMargin = '0';
			document.body.style.border = 'none';
		";
	}

	$btnSubmit = "<img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" onclick=\"sendfrm();\" alt=\"확인\" style=\"cursor:hand;\" />";
	$btnCancel = "<a href=\"{$btnCancel}\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>";
?>
<style type="text/css">
#member_join_layout { width:<?=$width?>; margin:0 auto; padding:0.28em; font-size:9pt; }
	#stipulation { height:8em; padding:0.5em; overflow-y:auto; border:1px solid #e7e7e7; }
		#stipulation h4 { margin:0 auto; font-size:1.11em; text-align:center; }
		#stipulation h5 { margin-bottom:0.3em; font-size:1em; }
		#stipulation ul { margin:0; padding-left:1em; padding-bottom:0.3em; }
	#stipulation_chk { float:left; }
	#member_join_inform { float:right; margin-top:0.5em; }
	#member_join_main { clear:both; padding-bottom:2.5em; border-top:1px solid #e7e7e7; border-bottom:1px solid #e7e7e7; }
		.member_join_cr { clear:both; margin:0.1em; background-color:#fff; border-bottom:1px solid #e1e1e1; }
		.member_join_el { clear:both; margin:0.1em; }
			.member_join_mleft { float:left; width:7.5em; padding:0.4em; }
			.member_join_mright { float:left; padding:0.4em; }
	#member_join_btn { clear:both; margin:0.5em; text-align:center; }
</style>

<form id="__ctl1" name="__ctl1" method="post" enctype="multipart/form-data" action="<?=$_SERVER["PHP_SELF"]?>?mode=register">
<div id="member_join_layout">
	<div id="member_join_inform"><b>*표시</b>는 필수 입력&nbsp;</div>
	<div id="member_join_main">
		<div class="member_join_el">
			<div class="member_join_mleft"><b>아이디*</b></div>
			<div class="member_join_mright">
				<input type="text" id="mem_id" name="mem_id" size="25" class="txtbox" maxlength="127" tag="M||[a-zA-Z]\w+||아이디는 영문 또는 영문숫자조합만 가능합니다." />
				<a href="javascript:void(0);" onclick="window.open('<?=$FSBOARD_PATH?>/lib/members.php?mode=idchk&amp;mem_id='+document.getElementById('mem_id').value,'','width=334,height=145,left='+(window.screen.availWidth-334)/2+',top='+((window.screen.availHeight-145)/2-100)+',location=0,menubar=0,resizable=1,scrollbars=0,status=1,toolbar=0');"><img src="<?=("{$FSBOARD_PATH}/img/mem/btn_idchk.gif")?>" alt="아이디중복검사" /></a>
				<input type="checkbox" id="public_id" name="public_id" value="1" checked="checked" />공개<br />
				영문(대소문자 구분),숫자,조합
			</div>
		</div>
		<div class="member_join_cr">
			<div class="member_join_mleft"><b>암호*</b></div>
			<div class="member_join_mright">
				<input type="password" id="mem_passwd1" name="mem_passwd1" size="30" class="txtbox" maxlength="127" tag="M||.+||암호를 입력하세요." /> 영문(대소문자 구분), 숫자, 조합
			</div>
		</div>
		<div class="member_join_el">
			<div class="member_join_mleft"><b>암호확인*</b></div>
			<div class="member_join_mright">
				<input type="password" id="mem_passwd2" name="mem_passwd2" size="30" class="txtbox" maxlength="127" tag="M||.+||암호확인을 입력하세요." /> 입력한 암호 재확인
			</div>
		</div>

		<div class="member_join_cr">
			<div class="member_join_mleft">암호 힌트 질문</div>
			<div class="member_join_mright">
				<select id="mem_question" name="mem_question">
					<option value="">질문을 선택해 주세요</option>
					<option>가장 재밌게 본 영화는?</option>
					<option>기억에 남는 사람은?</option>
					<option>나만의 기념일은?</option>
					<option>나의 보물 1호는?</option>
					<option>나의 성격은?</option>
					<option>나의 이상형은?</option>
					<option>나의 좌우명은?</option>
					<option>아끼는 물건은?</option>
					<option>자주 가는 곳은?</option>
					<option>자주가는 사이트는?</option>
					<option>좋아하는 게임은?</option>
					<option>좋아하는 사람은?</option>
					<option>좋아하는 색깔은?</option>
					<option>좋아하는 연예인은?</option>
					<option>좋아하는 음식은?</option>
					<option>좋아하는 음악은?</option>
					<option>좋아하는 패션은?</option>
					<option>첫데이트 장소는?</option>
					<option>첫키스 장소는?</option>
					<option>최근 읽었던 책은?</option>
					<option>평소 주량은?</option>
				</select>
				암호분실시 본인확인 질문 내용
			</div>
		</div>
		<div class="member_join_el">
			<div class="member_join_mleft">암호 힌트 답</div>
			<div class="member_join_mright">
				<input type="text" id="mem_answer" name="mem_answer" size="30" class="txtbox" maxlength="127" />
				본인확인 질문에 대한 답변 내용
			</div>
		</div>

		<div class="member_join_cr">
			<div class="member_join_mleft">회원구분</div>
			<div class="member_join_mright">
				<select id="mem_part" name="mem_part">
					<option value="">--선택--</option>
<?
	for($i=1; $i<sizeof($mem_part_element); $i++) {
		echo "					<option>".$mem_part_element[$i]."</option>\n";
	}
?>
				</select>
				해당 되는 회원분류를 선택
			</div>
		</div>


		<div class="member_join_cr">
			<div class="member_join_mleft"><b>이름*</b></div>
			<div class="member_join_mright">
				<input type="text" id="mem_name" name="mem_name" size="25" class="txtbox" maxlength="20" tag="M||.+||이름을 입력하세요." />
				<input type="checkbox" id="public_name" name="public_name" value="1" checked="checked" />공개
			</div>
		</div>
<!--
		<div class="member_join_el">
			<div class="member_join_mleft">닉네임</div>
			<div class="member_join_mright">
				<input type="text" id="mem_nickname" name="mem_nickname" size="25" class="txtbox" maxlength="30" />
				이름대신 사용할 별명, 없으면 이름이 공개됨
			</div>
		</div>
-->
		<div class="member_join_cr">
			<div class="member_join_mleft">주민등록번호</div>
			<div class="member_join_mright">
				<input type="text" id="mem_idsn1" name="mem_idsn1" class="txtbox" size="9" maxlength="6" tag="O||\d+||주민등록 번호를 정확히 입력하세요." /> -
				<input type="password" id="mem_idsn2" name="mem_idsn2" class="txtbox" size="10" maxlength="7" tag="O||\d+||주민등록 번호를 정확히 입력하세요." />
				회원탈퇴 및 암호분실시 확인용, 암호화 저장됨
			</div>
		</div>
		<div class="member_join_cr">
			<div class="member_join_mleft"><b>이메일*</b></div>
			<div class="member_join_mright">
				<input type="text" id="mem_email" name="mem_email" size="46" class="txtbox" maxlength="255" tag="M||([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)||이메일주소를 정확히 입력하세요." />
				<input type="checkbox" id="public_email" name="public_email" value="1" checked="checked" />공개
				<input type="checkbox" id="mem_mailing" name="mem_mailing" value="1" checked="checked" />메일받기
			</div>
		</div>
<!--
		<div class="member_join_el">
			<div class="member_join_mleft">홈페이지</div>
			<div class="member_join_mright">
				<input type="text" id="mem_homepage" name="mem_homepage" size="58" class="txtbox" maxlength="255" value="http://" />
				<input type="checkbox" id="public_homepage" name="public_homepage" value="1" checked="checked" />공개
			</div>
		</div>
-->

		<div class="member_join_cr">
			<div class="member_join_mleft">우편번호</div>
			<div class="member_join_mright">
				<input type="text" id="mem_zipcode1" name="mem_zipcode1" size="7" class="txtbox" maxlength="3" tag="O||\d+||우편번호는 숫자만 입력하세요." /> -
				<input type="text" id="mem_zipcode2" name="mem_zipcode2" size="7" class="txtbox" maxlength="3" tag="O||\d+||우편번호는 숫자만 입력하세요." />
				<a href="javascript:void(0);" onclick="window.open('<?=$FSBOARD_PATH?>/lib/members.php?mode=srhzipcode&amp;srhmode=join','','width=430,height=300,left='+(window.screen.availWidth-430)/2+',top='+((window.screen.availHeight-300)/2-50)+',location=0,menubar=0,resizable=1,scrollbars=1,status=1,toolbar=0');"><img src="<?=("{$FSBOARD_PATH}/img/mem/btn_zsrh.gif")?>" alt="주소/우편번호 검색" /></a>
			</div>
		</div>
		<div class="member_join_el">
			<div class="member_join_mleft">주소</div>
			<div class="member_join_mright">
				<input type="text" id="mem_addr1" name="mem_addr1" size="58" class="txtbox" maxlength="200" />
				<input type="checkbox" id="public_addr" name="public_addr" value="1" />공개
			</div>
		</div>
		<div class="member_join_el">
			<div class="member_join_mleft">주소나머지</div>
			<div class="member_join_mright">
				<input type="text" id="mem_addr2" name="mem_addr2" size="50" class="txtbox" maxlength="200" />
			</div>
		</div>

		<div class="member_join_cr">
			<div class="member_join_mleft">전화번호</div>
			<div class="member_join_mright">
				<input type="text" id="mem_telnum1" name="mem_telnum1" size="7" class="txtbox" maxlength="3" tag="O||\d+||전화번호는 숫자만 입력하세요." /> -
				<input type="text" id="mem_telnum2" name="mem_telnum2" size="7" class="txtbox" maxlength="4" tag="O||\d+||전화번호는 숫자만 입력하세요." /> -
				<input type="text" id="mem_telnum3" name="mem_telnum3" size="7" class="txtbox" maxlength="4" tag="O||\d+||전화번호는 숫자만 입력하세요." />
				<input type="checkbox" id="public_telnum" name="public_telnum" value="1" />공개
			</div>
		</div>


		<div class="member_join_el">
			<div class="member_join_mleft">휴대폰번호</div>
			<div class="member_join_mright">
				<input type="text" id="mem_hpnum1" name="mem_hpnum1" size="7" class="txtbox" maxlength="3" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." /> -
				<input type="text" id="mem_hpnum2" name="mem_hpnum2" size="7" class="txtbox" maxlength="4" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." /> -
				<input type="text" id="mem_hpnum3" name="mem_hpnum3" size="7" class="txtbox" maxlength="4" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." />
				<input type="checkbox" id="public_hpnum" name="public_hpnum" value="1" />공개
			</div>
		</div>
<!--
		<div class="member_join_cr">
			<div class="member_join_mleft">직업</div>
			<div class="member_join_mright">
				<input type="text" id="mem_job" name="mem_job" size="25" class="txtbox" maxlength="20" />
				<input type="checkbox" id="public_job" name="public_job" value="1" checked="checked" />공개
			</div>
		</div>

		<div class="member_join_el">
			<div class="member_join_mleft">취미</div>
			<div class="member_join_mright">
				<input type="text" id="mem_hobby" name="mem_hobby" size="25" class="txtbox" maxlength="10" />
				<input type="checkbox" id="public_hobby" name="public_hobby" value="1" checked="checked" />공개
			</div>
		</div>
		<div class="member_join_cr">
			<div class="member_join_mleft">생년월일</div>
			<div class="member_join_mright">
				<select id="mem_birthday1" name="mem_birthday1">
				<option value="">----</option>
				<? for($i=date("Y"); $i>=date("Y")-100; $i--) { echo "<option>{$i}</option>\n"; } ?>
				</select>년
				<select id="mem_birthday2" name="mem_birthday2">
				<option value="">--</option>
				<? for($i=1; $i<=12; $i++) { echo "<option>{$i}</option>\n"; } ?>
				</select>월
				<select id="mem_birthday3" name="mem_birthday3">
				<option value="">--</option>
				<? for($i=1; $i<=31; $i++) { echo "<option>{$i}</option>\n"; } ?>
				</select>일
				<input type="checkbox" id="public_birthday" name="public_birthday" value="1" />공개
			</div>
		</div>
		<div class="member_join_cr">
			<div class="member_join_mleft">사진</div>
			<div class="member_join_mright">
				<input type="file" id="attachFile" name="attachFile" size="43" class="txtbox" />
				<input type="checkbox" id="public_picture" name="public_picture" value="1" checked="checked" />공개
				<br />가로/세로 200픽셀 내외, JPG,GIF,BMP,PNG파일
			</div>
		</div>
-->


<!--
		<div class="member_join_cr">
			<div class="member_join_mleft">이름마크</div>
			<div class="member_join_mright">
				<input type="file" id="attachFile2" name="attachFile2" size="45" class="txtbox" />
			</div>
			<div class="member_join_mleft">이름이미지</div>
			<div class="member_join_mright">
				<input type="file" id="attachFile3" name="attachFile3" size="45" class="txtbox" />
			</div>
		</div>
-->


<!--
		<div class="member_join_cr">
			<div class="member_join_mleft">정보공개</div>
			<div class="member_join_mright">
				<input type="checkbox" id="public_all" name="public_all" value="1" checked="checked" />회원정보 공개(공개에 체크된 항목만 해당됨)
			</div>
		</div>
-->
	</div>
	<div id="member_join_btn"><?echo $btnSubmit." ".$btnCancel;?></div>
</div>
</form>
<script type="text/javascript">
//<![CDATA[
function sendfrm() {
	var frm = document.forms['__ctl1'];
	//if(!frm.agreement.checked) {
	//	window.alert("회원약관을 반드시 모두 읽어 보시고 동의해 주세요.");
	//	frm.agreement.focus();
	//	document.body.scrollTop = 0;
	//	return;
	//}
	//else {
		if(checkValue(document.forms['__ctl1'])) frm.submit();
	//}
}

/*/Netscape일 경우 레이어가 깨지는 현상때문에 숨김
if(window.navigator.appName.indexOf('Netscape')==0) {
	document.getElementById('agreement').checked = true;
	document.getElementById('stipulation').style.display = 'none';
	document.getElementById('stipulation_chk').style.display = 'none';
}*/

<?=$bodyStyle?>
//]]>
</script>
<?
	members_foot();












//////////////////////////////////////////////////////////////////////////////////////////회원가입처리
} else if($MODE == "register") {

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	$mem_id				= StrAddSlashes(trim($_POST["mem_id"]));
	$mem_passwd1		= trim($_POST["mem_passwd1"]);
	$mem_passwd2		= trim($_POST["mem_passwd2"]);
	$mem_question		= StrAddSlashes(trim($_POST["mem_question"]));
	$mem_answer			= StrAddSlashes(trim($_POST["mem_answer"]));
	$mem_part			= StrAddSlashes(trim($_POST["mem_part"]));
	$mem_jumin1			= StrAddSlashes(trim($_POST["mem_jumin1"]));
	$mem_jumin2			= StrAddSlashes(trim($_POST["mem_jumin2"]));

	$mem_part			    = 10;

	$mem_name			= StrAddSlashes(trim($_POST["mem_name"]));
	$mem_nickname		= StrAddSlashes(trim($_POST["mem_nickname"]));

	$mem_idsn1			= StrAddSlashes(trim($_POST["mem_idsn1"]));
	$mem_idsn2			= StrAddSlashes(trim($_POST["mem_idsn2"]));
	$mem_email			= StrAddSlashes(trim($_POST["mem_email"]));
	$mem_homepage		= StrAddSlashes(trim($_POST["mem_homepage"]));
	$mem_zipcode1		= StrAddSlashes(trim($_POST["mem_zipcode1"]));
	$mem_zipcode2		= StrAddSlashes(trim($_POST["mem_zipcode2"]));
	$mem_addr1			= StrAddSlashes(trim($_POST["mem_addr1"]));
	$mem_addr2			= StrAddSlashes(trim($_POST["mem_addr2"]));
	$mem_telnum1		= StrAddSlashes(trim($_POST["mem_telnum1"]));
	$mem_telnum2		= StrAddSlashes(trim($_POST["mem_telnum2"]));
	$mem_telnum3		= StrAddSlashes(trim($_POST["mem_telnum3"]));
	$mem_hpnum1			= StrAddSlashes(trim($_POST["mem_hpnum1"]));
	$mem_hpnum2			= StrAddSlashes(trim($_POST["mem_hpnum2"]));
	$mem_hpnum3			= StrAddSlashes(trim($_POST["mem_hpnum3"]));
	$mem_job			= StrAddSlashes(trim($_POST["mem_job"]));
	$mem_hobby			= StrAddSlashes(trim($_POST["mem_hobby"]));
	$mem_birthday1		= StrAddSlashes(trim($_POST["mem_birthday1"]));
	$mem_birthday2		= StrAddSlashes(trim($_POST["mem_birthday2"]));
	$mem_birthday3		= StrAddSlashes(trim($_POST["mem_birthday3"]));
	$mem_intro			= StrAddSlashes(trim($_POST["mem_intro"]));
	$mem_mailing		= StrAddSlashes(trim($_POST["mem_mailing"]));

	$public_id			= StrAddSlashes(trim($_POST["public_id"]));
	$public_name		= StrAddSlashes(trim($_POST["public_name"]));
	$public_email		= StrAddSlashes(trim($_POST["public_email"]));
	$public_homepage	= StrAddSlashes(trim($_POST["public_homepage"]));
	$public_addr		= StrAddSlashes(trim($_POST["public_addr"]));
	$public_telnum		= StrAddSlashes(trim($_POST["public_addr"]));
	$public_hpnum		= StrAddSlashes(trim($_POST["public_hpnum"]));
	$public_job			= StrAddSlashes(trim($_POST["public_job"]));
	$public_hobby		= StrAddSlashes(trim($_POST["public_hobby"]));
	$public_birthday	= StrAddSlashes(trim($_POST["public_birthday"]));
	$public_picture		= StrAddSlashes(trim($_POST["public_picture"]));
	$public_intro		= StrAddSlashes(trim($_POST["public_intro"]));
	$public_regdate		= StrAddSlashes(trim($_POST["public_regdate"]));
	$public_latestdate	= StrAddSlashes(trim($_POST["public_latestdate"]));
	$public_all			= StrAddSlashes(trim($_POST["public_all"]));

	$mem_mailing		= $mem_mailing		? 1 : 0;
	$public_id			= $public_id		? 1 : 0;
	$public_name		= $public_name		? 1 : 0;
	$public_email		= $public_email		? 1 : 0;
	$public_homepage	= $public_homepage	? 1 : 0;
	$public_addr		= $public_addr		? 1 : 0;
	$public_telnum		= $public_telnum	? 1 : 0;
	$public_hpnum		= $public_hpnum		? 1 : 0;
	$public_job			= $public_job		? 1 : 0;
	$public_hobby		= $public_hobby		? 1 : 0;
	$public_birthday	= $public_birthday	? 1 : 0;
	$public_picture		= $public_picture	? 1 : 0;
	$public_intro		= $public_intro		? 1 : 0;
	$public_regdate		= $public_regdate	? 1 : 0;
	$public_latestdate	= $public_latestdate? 1 : 0;
	$public_all			= $public_all		? 1 : 0;

	if(IsBlank($mem_id)) Error("아이디를 입력해 주세요.");
	if(strlen($mem_id)<3) Error("아이디는 3글자 이상 가능합니다.");
	if(IsBlank($mem_passwd1)) Error("암호를 입력해 주세요.");
	if(IsBlank($mem_passwd2)) Error("암호확인을 입력해 주세요.");
	if($mem_passwd1!=$mem_passwd2) Error("암호와 암호확인이 일치하지 않습니다.");
	if(strlen($mem_passwd1)<4) Error("암호를 4글자 이상 입력해 주세요.");
	if(IsBlank($mem_name)) Error("이름을 입력해 주세요.");
	if(IsBlank($mem_email)) Error("이메일을 입력해 주세요.");
	if(!mail_mx_check($mem_email)) Error("이메일주소가 올바르지 않습니다.");
	if(!IsEmail($mem_email)) Error("이메일 주소가 올바르지 않습니다.");
	if($mem_homepage=="http://") $mem_homepage = "";
	if($mem_homepage) { if(!IsHomepage($mem_homepage)) Error("홈페이지주소가 형식에 맞지 않습니다."); }

	if(eregi("[^-_a-zA-Z0-9]", $mem_id)) Error("<strong>{$mem_id}</strong> 에 사용할 수 없는 문자가 포함되어 있습니다.");
	if(eregi("admin|root|fsboard|select|insert|delete|update|drop|shutdown|exec", $mem_id)) Error("<strong>{$mem_id}</strong> 은(는) 사용할 수 없는 아이디입니다.");
	if(preg_match("/[^a-zA-Z\xA1-\xFE]/", $mem_name)) Error("이름에 유효하지 않은 문자가 포함되어 있습니다.");
	if($mem_nickname) {
		if(preg_match("/[^a-zA-Z0-9\xA1-\xFE\-_]/", $mem_nickname)) Error("닉네임에 유효하지 않은 문자가 포함되어 있습니다.");
		if(eregi("admin|root|fsboard|select|insert|delete|update|drop|shutdown|exec|관리자", $mem_nickname)) Error("{$mem_nickname} 은(는) 사용할 수 없는 닉네임입니다.");
	}
	if($mem_zipcode1 || $mem_zipcode2) {
		if(!IsNum($mem_zipcode1) || !IsNum($mem_zipcode2)) Error("우편번호가 올바르지 않습니다.");
	}
	if($mem_telnum1 || $mem_telnum2 || $mem_telnum3) {
		if(!IsNum($mem_telnum1) || !IsNum($mem_telnum2) || !IsNum($mem_telnum3)) Error("전화번호는 숫자만 입력해 주세요.");
	}
	if($mem_hpnum1 || $mem_hpnum2 || $mem_hpnum3) {
		if(!IsNum($mem_hpnum1) || !IsNum($mem_hpnum2) || !IsNum($mem_hpnum3)) Error("휴대폰번호는 숫자만 입력해 주세요.");
	}
	if($mem_birthday1 || $mem_birthday2 || $mem_birthday3) {
		if(!IsNum($mem_birthday1) || !IsNum($mem_birthday2) || !IsNum($mem_birthday3)) Error("생년월일은 숫자만 입력해 주세요.");
	}

	//아이디 중복체크
	$result = mysql_query("SELECT mem_id FROM ".$_table_id_members." WHERE mem_id='{$mem_id}';") or Error(mysql_error());
	if($result) {
		if(mysql_num_rows($result)) Error("<b>{$mem_id}</b> (은)는 이미 가입된 아이디입니다.");
	}

	//이메일 중복체크
	$result = mysql_query("SELECT mem_email FROM ".$_table_id_members." WHERE mem_email='{$mem_email}';") or Error(mysql_error());
	if($result) {
		if(mysql_num_rows($result)) Error("<b>{$mem_email}</b> (은)는 이미 가입된 이메일주소입니다.");
	}

	//주민등록번호 체크
	if($mem_idsn1 && $mem_idsn2) {
		if(!ChkSidNum($mem_idsn1.$mem_idsn2)) Error("주민등록번호가 올바르지 않습니다.");
		$mem_idsn = base64_encode("{$mem_idsn1}-{$mem_idsn2}");
	}

	//분리된 입력값 결합
	if($mem_zipcode1 && $mem_zipcode2) $mem_zipcode = "{$mem_zipcode1}-{$mem_zipcode2}";
	if($mem_telnum1 && $mem_telnum2 && $mem_telnum3) $mem_telnum = "{$mem_telnum1}-{$mem_telnum2}-{$mem_telnum3}";
	if($mem_hpnum1 && $mem_hpnum2 && $mem_hpnum3) $mem_hpnum = "{$mem_hpnum1}-{$mem_hpnum2}-{$mem_hpnum3}";
	if($mem_birthday1 && $mem_birthday2 && $mem_birthday3) $mem_birthday = "{$mem_birthday1}-{$mem_birthday2}-{$mem_birthday3}";

	//자체 입력값 설정
	$mem_passwd = md5($mem_passwd1);
	$mem_level = $MemDefaultLevel;
	$mem_ip_reg = $_SERVER["REMOTE_ADDR"];
	$mem_ip_login = $mem_ip_reg;
	$mem_regdate = mktime();
	$mem_latestdate = mktime();
	$mem_homepage = $mem_homepage=="http://" ? "" : $mem_homepage;

	//업로드파일 처리
	if($_FILES["attachFile"]) {
		$file_tmpn = $_FILES["attachFile"]["tmp_name"];
		$file_name = $_FILES["attachFile"]["name"];
		$file_size = $_FILES["attachFile"]["size"];

		if($file_size>0 && $file_tmpn) {
			if(!is_uploaded_file($file_tmpn)) Error("정상적인 방법으로 업로드 해주세요");
			$file_size = filesize($file_tmpn);

			//업로드 용량 제한
			if($fileMaxLimit<$file_size&&!$isAdmin) {
				Error("파일 업로드는 ".GetFileSize($fileMaxLimit)." 까지 가능합니다");
			}

			$temp = explode(".",$file_name);
			$s_point = count($temp)-1;
			$upload_check = $temp[$s_point];
			if(!eregi($upload_check,$allowExts)||!$upload_check) Error("파일 업로드는 {$allowExts} 확장자만 가능합니다");

			$file_tmpn = eregi_replace("\\\\","\\",$file_tmpn);

			if(!is_dir($FSDATA_ROOT."/{$dataPath}")) { //디렉토리 검사
				@mkdir($FSDATA_ROOT."/{$dataPath}",0777);
				@chmod($FSDATA_ROOT."/{$dataPath}",0757);
			}

			$file_name = StrAddSlashes($file_name);

			$file_fwpn = GetUniqueName($file_name, "{$FSDATA_ROOT}/{$dataPath}/"); //중복파일 검사
			if(!move_uploaded_file($file_tmpn, $file_fwpn)) Error("파일업로드가 제대로 되지 않았습니다");
			@chmod($file_fwpn,0646);
		}
	}

	$query = "
		INSERT INTO ".$_table_id_members." (
				mem_id,
				mem_passwd,
				mem_level,
				mem_part,
				mem_name,
				mem_nickname,
				mem_idsn,
				mem_email,
				mem_homepage,
				mem_zipcode,
				mem_addr1,
				mem_addr2,
				mem_telnum,
				mem_hpnum,
				mem_job,
				mem_hobby,
				mem_birthday,
				mem_question,
				mem_answer,
				mem_mailing,
				mem_picture,
				mem_regdate,
				mem_latestdate,
				mem_ip_reg,
				mem_ip_login,
				public_id,
				public_name,
				public_email,
				public_homepage,
				public_addr,
				public_telnum,
				public_hpnum,
				public_job,
				public_hobby,
				public_birthday,
				public_picture,
				public_intro,
				public_regdate,
				public_latestdate,
				public_all,
				mem_intro
			) VALUES (
				'$mem_id',
				'$mem_passwd',
				$mem_level,
				'$mem_part',
				'$mem_name',
				'$mem_nickname',
				'$mem_idsn',
				'$mem_email',
				'$mem_homepage',
				'$mem_zipcode',
				'$mem_addr1',
				'$mem_addr2',
				'$mem_telnum',
				'$mem_hpnum',
				'$mem_job',
				'$mem_hobby',
				'$mem_birthday',
				'$mem_question',
				'$mem_answer',
				$mem_mailing,
				'$file_name',
				$mem_regdate,
				$mem_latestdate,
				'$mem_ip_reg',
				'$mem_ip_login',
				$public_id,
				$public_name,
				$public_email,
				$public_homepage,
				$public_addr,
				$public_telnum,
				$public_hpnum,
				$public_job,
				$public_hobby,
				$public_birthday,
				$public_picture,
				$public_intro,
				$public_regdate,
				$public_latestdate,
				$public_all,
				'$mem_intro'
			);
	";

//echo $query;exit;
	mysql_query($query) or Error(mysql_error());

	//로그인 처리
	if(!$MemId) {
		$_SESSION["MemId"] = $mem_id;
		$_SESSION["MemPasswd"] = $mem_passwd;
		$_SESSION["MemLevel"] = $mem_level;
		$_SESSION["MemName"] = $mem_nickname ? $mem_nickname : $mem_name;
	}


	if($combinedDesign) MovePage("/main.html");
	else echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry{opener.location.reload();window.close();}catch(e){}\n//]]>\n</script>";












//////////////////////////////////////////////////////////////////////////////////////////회원정보수정
} else if($MODE == "modify") {

	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];

	$mem_id = $_GET["mem_id"] ? $_GET["mem_id"] : $MemId;

	if(!$MemId||!$MemLevel) Error("로그인 상태가 아닙니다.");

	//회원정보 가져오기
	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("회원정보가 없거나 세션시간이 만료되었습니다.");
		else {
			$rs = mysql_fetch_array($result);

			$idx				= $rs["idx"];
			$mem_id				= $rs["mem_id"];
			$mem_level			= $rs["mem_level"];
			$mem_grade			= $rs["mem_grade"];
			$mem_part			= $rs["mem_part"];
			$mem_auth			= $rs["mem_auth"];
			$mem_name			= $rs["mem_name"];
			$mem_nickname		= $rs["mem_nickname"];
			$mem_idsn			= $rs["mem_idsn"];
			$mem_email			= $rs["mem_email"];
			$mem_homepage		= $rs["mem_homepage"];
			$mem_zipcode		= $rs["mem_zipcode"];
			$mem_addr1			= $rs["mem_addr1"];
			$mem_addr2			= $rs["mem_addr2"];
			$mem_telnum			= $rs["mem_telnum"];
			$mem_hpnum			= $rs["mem_hpnum"];
			$mem_job			= $rs["mem_job"];
			$mem_hobby			= $rs["mem_hobby"];
			$mem_birthday		= $rs["mem_birthday"];
			$mem_question		= $rs["mem_question"];
			$mem_answer			= $rs["mem_answer"];
			$mem_mailing		= $rs["mem_mailing"];
			$mem_picture		= $rs["mem_picture"];
			$mem_imgmark		= $rs["mem_imgmark"];
			$mem_imgname		= $rs["mem_imgname"];
			$mem_intro			= $rs["mem_intro"];
			$mem_regdate		= $rs["mem_regdate"];
			$mem_editdate		= $rs["mem_editdate"];
			$mem_latestdate		= $rs["mem_latestdate"];
			$mem_ip_reg			= $rs["mem_ip_reg"];
			$mem_ip_edit		= $rs["mem_ip_edit"];
			$mem_ip_login		= $rs["mem_ip_login"];
			$mem_loginnum		= $rs["mem_loginnum"];

			$public_id			= $rs["public_id"];
			$public_name		= $rs["public_name"];
			$public_email		= $rs["public_email"];
			$public_homepage	= $rs["public_homepage"];
			$public_addr		= $rs["public_addr"];
			$public_telnum		= $rs["public_telnum"];
			$public_hpnum		= $rs["public_hpnum"];
			$public_job			= $rs["public_job"];
			$public_hobby		= $rs["public_hobby"];
			$public_birthday	= $rs["public_birthday"];
			$public_picture		= $rs["public_picture"];
			$public_intro		= $rs["public_intro"];
			$public_regdate		= $rs["public_regdate"];
			$public_latestdate	= $rs["public_latestdate"];
			$public_all			= $rs["public_all"];

			$zipcode = explode("-",$mem_zipcode);
			$telnum = explode("-",$mem_telnum);
			$hpnum = explode("-",$mem_hpnum);
			$birthday = explode("-",$mem_birthday);

			//텍스트박스에서 따옴표 에러 방지
			$mem_answer = StripHtmlChars($mem_answer);
			$mem_name = StripHtmlChars($mem_name);
			$mem_email = StripHtmlChars($mem_email);
			$mem_homepage = StripHtmlChars($mem_homepage);
			$mem_zipcode = StripHtmlChars($mem_zipcode);
			$mem_addr1 = StripHtmlChars($mem_addr1);
			$mem_addr2 = StripHtmlChars($mem_addr2);
			$mem_telnum = StripHtmlChars($mem_telnum);
			$mem_hpnum = StripHtmlChars($mem_hpnum);
			$mem_job = StripHtmlChars($mem_job);
			$mem_hobby = StripHtmlChars($mem_hobby);
			$mem_intro = StripHtmlChars($mem_intro);
		}
		mysql_free_result($result);
	} else Error("서버관리자에게 문의하거나 잠시후 다시 시도해 주세요.");

	members_head();

	if($combinedDesign) {
		$btnCancel = "javascript:window.history.back();";
		$btnSecede = "window.location.href='?mode=secede_member&amp;mem_id={$MemId}';";
		$width = "95%";
	}
	else {
		$btnCancel = "javascript:window.close();";
		$btnSecede = "window.open('?mode=secede_member&amp;mem_id={$MemId}','secede_member','width=490,height=300,resizable=1,scrollbars=1');window.close();";
		$width = "45em";

		$bodyStyle = "
			document.body.leftMargin = 0;
			document.body.topMargin = 0;
			document.body.style.border = 'none';
		";
	}

	$btnSubmit = "<img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" onclick=\"sendfrm();\" alt=\"확인\" style=\"cursor:hand;\" />";
	$btnCancel = "<a href=\"{$btnCancel}\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>";

	if(!$combinedDesign) {
		$btnSecede = "<nobr><span onclick=\"{$btnSecede}\" style=\"cursor:hand;\"><img src=\"{$FSBOARD_PATH}/img/clip/xbutton.gif\" alt=\"X\" /> 회원탈퇴</span></nobr>";
	}
	else {
		$btnSecede = "";
	}
?>
<style type="text/css">
#member_modify_layout { width:<?=$width?>; margin:0 auto; padding:0.28em; font-size:9pt; }
	#member_modify_title { float:left; }
	#member_modify_inform { float:right; }
	#member_modify_main { clear:both; padding-bottom:2.5em; border-top:1px solid #e7e7e7; border-bottom:1px solid #e7e7e7; }
		.member_modify_cr { clear:both; margin:0.1em; background-color:#fff; border-bottom:1px solid #e1e1e1; }
		.member_modify_el { clear:both; margin:0.1em; }
			.member_modify_mleft { float:left; width:7.5em; padding:0.4em; }
			.member_modify_mright { float:left; padding:0.4em; }
	#member_modify_btn { position:relative; margin:0.5em; text-align:center; }
		#member_modify_btn_secede { position:absolute; right:1em; top:0.3em; }
</style>

<form id="__ctl1" name="__ctl1" method="post" enctype="multipart/form-data" action="<?=$_SERVER["PHP_SELF"]?>?mode=modifysave&amp;mem_id=<?=$mem_id?>&amp;idx=<?=$idx?>">
<div id="member_modify_layout">
	<div id="member_modify_title"><img src="<?=$FSBOARD_PATH."/img/clip/doc2.gif"?>" alt="icon" /> <b> 회원정보 수정 - <?=$MemId?></b></div>
	<div id="member_modify_inform"><b>* 표시</b>는 필수 입력</div>
	<div id="member_modify_main">
		<div class="member_modify_el">
			<div class="member_modify_mleft">아이디</div>
			<div class="member_modify_mright">
				<b style="font-family:Verdana;font-size:13px;"><?=$mem_id?></b>
				<input type="checkbox" id="public_id" name="public_id" value="1"<?=($public_id?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">암호</div>
			<div class="member_modify_mright">
				<input type="password" id="mem_passwd1" name="mem_passwd1" size="30" class="txtbox" maxlength="127" /> <span style="color:black;">변경할 경우에만 입력</span>
			</div>
		</div>
		<div class="member_modify_el">
			<div class="member_modify_mleft">암호확인</div>
			<div class="member_modify_mright">
				<input type="password" id="mem_passwd2" name="mem_passwd2" size="30" class="txtbox" maxlength="127" /> 입력한 암호 재확인
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">암호 힌트 질문</div>
			<div class="member_modify_mright">
				<select id="mem_question" name="mem_question">
					<option value="">질문을 선택해 주세요</option>
<?
	echo "
					<option".($mem_question=="가장 재밌게 본 영화는?"?" selected=\"selected\"":"").">가장 재밌게 본 영화는?</option>
					<option".($mem_question=="기억에 남는 사람은?"?" selected=\"selected\"":"").">기억에 남는 사람은?</option>
					<option".($mem_question=="나만의 기념일은?"?" selected=\"selected\"":"").">나만의 기념일은?</option>
					<option".($mem_question=="나의 보물 1호는?"?" selected=\"selected\"":"").">나의 보물 1호는?</option>
					<option".($mem_question=="나의 성격은?"?" selected=\"selected\"":"").">나의 성격은?</option>
					<option".($mem_question=="나의 이상형은?"?" selected=\"selected\"":"").">나의 이상형은?</option>
					<option".($mem_question=="나의 좌우명은?"?" selected=\"selected\"":"").">나의 좌우명은?</option>
					<option".($mem_question=="아끼는 물건은?"?" selected=\"selected\"":"").">아끼는 물건은?</option>
					<option".($mem_question=="자주 가는 곳은?"?" selected=\"selected\"":"").">자주 가는 곳은?</option>
					<option".($mem_question=="자주가는 사이트는?"?" selected=\"selected\"":"").">자주가는 사이트는?</option>
					<option".($mem_question=="좋아하는 게임은?"?" selected=\"selected\"":"").">좋아하는 게임은?</option>
					<option".($mem_question=="좋아하는 사람은?"?" selected=\"selected\"":"").">좋아하는 사람은?</option>
					<option".($mem_question=="좋아하는 색깔은?"?" selected=\"selected\"":"").">좋아하는 색깔은?</option>
					<option".($mem_question=="좋아하는 연예인은?"?" selected=\"selected\"":"").">좋아하는 연예인은?</option>
					<option".($mem_question=="좋아하는 음식은?"?" selected=\"selected\"":"").">좋아하는 음식은?</option>
					<option".($mem_question=="좋아하는 음악은?"?" selected=\"selected\"":"").">좋아하는 음악은?</option>
					<option".($mem_question=="좋아하는 패션은?"?" selected=\"selected\"":"").">좋아하는 패션은?</option>
					<option".($mem_question=="첫데이트 장소는?"?" selected=\"selected\"":"").">첫데이트 장소는?</option>
					<option".($mem_question=="첫키스 장소는?"?" selected=\"selected\"":"").">첫키스 장소는?</option>
					<option".($mem_question=="최근 읽었던 책은?"?" selected=\"selected\"":"").">최근 읽었던 책은?</option>
					<option".($mem_question=="평소 주량은?"?" selected=\"selected\"":"").">평소 주량은?</option>
	";
?>
				</select>
				암호분실시 본인확인 질문 내용
			</div>
		</div>


		<div class="member_modify_el">
			<div class="member_modify_mleft">암호 힌트 답</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_answer" name="mem_answer" size="30" class="txtbox" maxlength="127" value="<?=$mem_answer?>" />
				본인확인 질문에 대한 답변 내용
			</div>
		</div>

		<div class="member_modify_cr">
			<div class="member_modify_mleft">회원구분</div>
			<div class="member_modify_mright">
<?
	if($mem_level==1) {
		echo "
				<input type=\"hidden\" id=\"mem_part\" name=\"mem_part\" value=\"{$mem_part}\" />
		";
	}
	echo "
				<select id=\"mem_part\" name=\"mem_part\"".($mem_level==1?" disabled":"").">
					<option value=\"\">--선택--</option>
	";
	for($i=sizeof($mem_part_element)-1; $i>0; $i--) {
		echo "			<option".($mem_part==$mem_part_element[$i]?" selected=\"selected\"":"").">".$mem_part_element[$i]."</option>\n";
	}
	echo "
				</select>
	";
	if($mem_level==1) {
		echo "
				<span style=\"color:red;\">관리자는 자기자신의 레벨을 변경할수 없습니다</span>
		";
	}
	else {
		echo "
				<span style=\"color:red;\">변경하면 기본레벨로 리셋됨</span>
		";
	}
?>
			</div>

		<div class="member_modify_cr">
			<div class="member_modify_mleft"><b>이름*</b></div>
			<div class="member_modify_mright">
				<input type="text" id="mem_name" name="mem_name" size="25" class="txtbox" maxlength="20" tag="M||.+||이름을 입력하세요." value="<?=$mem_name?>" />
				<input type="checkbox" id="public_name" name="public_name" value="1"<?=($public_name?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
<!--
		<div class="member_modify_el">
			<div class="member_modify_mleft">닉네임</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_nickname" name="mem_nickname" size="25" class="txtbox" maxlength="30" value="<?=$mem_nickname?>" />
				이름대신 사용할 별명, 없으면 이름이 공개됨
			</div>
		</div>
-->
		<div class="member_modify_cr">
			<div class="member_modify_mleft"><b>이메일*</b></div>
			<div class="member_modify_mright">
				<input type="text" id="mem_email" name="mem_email" size="46" class="txtbox" maxlength="255" tag="M||([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)||이메일주소를 정확히 입력하세요." value="<?=$mem_email?>" />
				<input type="checkbox" id="public_email" name="public_email" value="1"<?=($public_email?" checked=\"checked\"":"")?> />공개
				<input type="checkbox" id="mem_mailing" name="mem_mailing" value="1"<?=($mem_mailing?" checked=\"checked\"":"")?> />메일받기
			</div>
		</div>
<!--
		<div class="member_modify_el">
			<div class="member_modify_mleft">홈페이지</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_homepage" name="mem_homepage" size="58" class="txtbox" maxlength="255" value="<?=$mem_homepage?>" />
				<input type="checkbox" id="public_homepage" name="public_homepage" value="1"<?=($public_homepage?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
-->

		<div class="member_modify_cr">
			<div class="member_modify_mleft">우편번호</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_zipcode1" name="mem_zipcode1" size="7" class="txtbox" maxlength="3" tag="O||\d+||우편번호는 숫자만 입력하세요." value="<?=$zipcode[0]?>" /> -
				<input type="text" id="mem_zipcode2" name="mem_zipcode2" size="7" class="txtbox" maxlength="3" tag="O||\d+||우편번호는 숫자만 입력하세요." value="<?=$zipcode[1]?>" />
				<a href="javascript:void(0);" onclick="window.open('<?=$FSBOARD_PATH?>/lib/members.php?mode=srhzipcode&amp;srhmode=join','','width=430,height=300,left='+(window.screen.availWidth-430)/2+',top='+((window.screen.availHeight-300)/2-50)+',location=0,menubar=0,resizable=1,scrollbars=1,status=1,toolbar=0');"><img src="<?=("{$FSBOARD_PATH}/img/mem/btn_zsrh.gif")?>" alt="주소/우편번호 검색" /></a>
			</div>
		</div>
		<div class="member_modify_el">
			<div class="member_modify_mleft">주소</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_addr1" name="mem_addr1" size="58" class="txtbox" maxlength="200" value="<?=$mem_addr1?>" />
				<input type="checkbox" id="public_addr" name="public_addr" value="1"<?=($public_addr?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_el">
			<div class="member_modify_mleft">주소나머지</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_addr2" name="mem_addr2" size="45" class="txtbox" maxlength="200" value="<?=$mem_addr2?>" />
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">전화번호</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_telnum1" name="mem_telnum1" size="7" class="txtbox" maxlength="3" tag="O||\d+||전화번호는 숫자만 입력하세요." value="<?=$telnum[0]?>" /> -
				<input type="text" id="mem_telnum2" name="mem_telnum2" size="7" class="txtbox" maxlength="4" tag="O||\d+||전화번호는 숫자만 입력하세요." value="<?=$telnum[1]?>" /> -
				<input type="text" id="mem_telnum3" name="mem_telnum3" size="7" class="txtbox" maxlength="4" tag="O||\d+||전화번호는 숫자만 입력하세요." value="<?=$telnum[2]?>" />
				<input type="checkbox" id="public_telnum" name="public_telnum" value="1"<?=($public_telnum?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_el">
			<div class="member_modify_mleft">휴대폰번호</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_hpnum1" name="mem_hpnum1" size="7" class="txtbox" maxlength="3" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." value="<?=$hpnum[0]?>" /> -
				<input type="text" id="mem_hpnum2" name="mem_hpnum2" size="7" class="txtbox" maxlength="4" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." value="<?=$hpnum[1]?>" /> -
				<input type="text" id="mem_hpnum3" name="mem_hpnum3" size="7" class="txtbox" maxlength="4" tag="O||\d+||휴대폰번호는 숫자만 입력하세요." value="<?=$hpnum[2]?>" />
				<input type="checkbox" id="public_hpnum" name="public_hpnum" value="1"<?=($public_hpnum?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
<!--
		<div class="member_modify_cr">
			<div class="member_modify_mleft">직업</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_job" name="mem_job" size="25" class="txtbox" maxlength="20" value="<?=$mem_job?>" />
				<input type="checkbox" id="public_job" name="public_job" value="1"<?=($public_job?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_el">
			<div class="member_modify_mleft">취미</div>
			<div class="member_modify_mright">
				<input type="text" id="mem_hobby" name="mem_hobby" size="25" class="txtbox" maxlength="10" value="<?=$mem_hobby?>" />
				<input type="checkbox" id="public_hobby" name="public_hobby" value="1"<?=($public_hobby?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">생년월일</div>
			<div class="member_modify_mright">
				<select id="mem_birthday1" name="mem_birthday1">
				<option value="">----</option>
				<? for($i=date("Y")-100; $i<=date("Y"); $i++) { echo "<option".($i==$birthday[0]?" selected=\"selected\"":"").">$i</option>?>"; } ?>
				</select>년
				<select id="mem_birthday2" name="mem_birthday2">
				<option value="">--</option>
				<? for($i=1; $i<=12; $i++) { echo "<option".($i==$birthday[1]?" selected=\"selected\"":"").">$i</option>"; } ?>
				</select>월
				<select id="mem_birthday3" name="mem_birthday3">
				<option value="">--</option>
				<? for($i=1; $i<=31; $i++) { echo "<option".($i==$birthday[2]?" selected=\"selected\"":"").">$i</option>"; } ?>
				</select>일
				<input type="checkbox" id="public_birthday" name="public_birthday" value="1"<?=($public_birthday?" checked=\"checked\"":"")?> />공개
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">사진</div>
			<div class="member_modify_mright">
				<input type="file" id="attachFile" name="attachFile" size="43" class="txtbox" />
				<input type="checkbox" id="public_picture" name="public_picture" value="1"<?=($public_picture?" checked=\"checked\"":"")?> />공개
				<br />가로/세로 200픽셀 내외, JPG,GIF,BMP,PNG파일
<?
	if($mem_picture) {
		echo "<br /><img src=\"{$FSBOARD_PATH}/data/{$dataPath}/{$mem_picture}\" id=\"mem_img1\" onload=\"controlImage(this.id,200);\" onclick=\"vwimgrzmv(this,this.src);\" alt=\"회원사진\" style=\"border:1px solid #e0e0e0;\" /><br />
						{$mem_picture} <input type=\"checkbox\" id=\"delAttachFile\" name=\"delAttachFile\" value=\"1\" />삭제
		";
	}
?>
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">자기소개</div>
			<div class="member_modify_mright">
				<textarea id="mem_intro" name="mem_intro" cols="65" rows="4" class="txtbox2"><?=$mem_intro?></textarea><br />
				<input type="checkbox" id="public_intro" name="public_intro" value="1"<?=($public_intro?" checked=\"checked\"":"")?> />자기소개 공개
				<input type="checkbox" id="public_regdate" name="public_regdate" value="1"<?=($public_regdate?" checked=\"checked\"":"")?> />가입일자 공개
				<input type="checkbox" id="public_latestdate" name="public_latestdate" value="1"<?=($public_latestdate?" checked=\"checked\"":"")?> />마지막로그인일자 공개
			</div>
		</div>
		<div class="member_modify_cr">
			<div class="member_modify_mleft">정보공개</div>
			<div class="member_modify_mright">
				<input type="checkbox" id="public_all" name="public_all" value="1"<?=($public_all?" checked=\"checked\"":"")?> />회원정보 공개(공개에 체크된 항목만 해당됨)
			</div>
		</div>
-->

	</div>
	<div id="member_modify_btn">
		<?echo $btnSubmit." ".$btnCancel;?>
		<div id="member_modify_btn_secede">
			<?=$btnSecede?>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
//<![CDATA[
function sendfrm() {
	if(checkValue(document.forms['__ctl1'])) document.forms['__ctl1'].submit();
}
<?=$bodyStyle?>
//]]>
</script>
<?
	members_foot();












//////////////////////////////////////////////////////////////////////////////////////////회원정보수정처리
} else if($MODE == "modifysave") {

	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");
	if(!$MemId) Error("로그인 상태가 아니거나 세션시간이 만료되었습니다.");

	$idx				= intval($_GET["idx"]);
	$mem_id				= StrAddSlashes(trim($mem_id));
	$mem_passwd1		= StrAddSlashes(trim($_POST["mem_passwd1"]));
	$mem_passwd2		= StrAddSlashes(trim($_POST["mem_passwd2"]));
	$mem_question		= StrAddSlashes(trim($_POST["mem_question"]));
	$mem_answer			= StrAddSlashes(trim($_POST["mem_answer"]));
	$mem_part			= StrAddSlashes(trim($_POST["mem_part"]));
	$mem_part			= 10;
	$mem_name			= StrAddSlashes(trim($_POST["mem_name"]));
	$mem_nickname		= StrAddSlashes(trim($_POST["mem_nickname"]));
	$mem_email			= StrAddSlashes(trim($_POST["mem_email"]));
	$mem_homepage		= StrAddSlashes(trim($_POST["mem_homepage"]));
	$mem_zipcode1		= StrAddSlashes(trim($_POST["mem_zipcode1"]));
	$mem_zipcode2		= StrAddSlashes(trim($_POST["mem_zipcode2"]));
	$mem_addr1			= StrAddSlashes(trim($_POST["mem_addr1"]));
	$mem_addr2			= StrAddSlashes(trim($_POST["mem_addr2"]));
	$mem_telnum1		= StrAddSlashes(trim($_POST["mem_telnum1"]));
	$mem_telnum2		= StrAddSlashes(trim($_POST["mem_telnum2"]));
	$mem_telnum3		= StrAddSlashes(trim($_POST["mem_telnum3"]));
	$mem_hpnum1			= StrAddSlashes(trim($_POST["mem_hpnum1"]));
	$mem_hpnum2			= StrAddSlashes(trim($_POST["mem_hpnum2"]));
	$mem_hpnum3			= StrAddSlashes(trim($_POST["mem_hpnum3"]));
	$mem_job			= StrAddSlashes(trim($_POST["mem_job"]));
	$mem_hobby			= StrAddSlashes(trim($_POST["mem_hobby"]));
	$mem_birthday1		= StrAddSlashes(trim($_POST["mem_birthday1"]));
	$mem_birthday2		= StrAddSlashes(trim($_POST["mem_birthday2"]));
	$mem_birthday3		= StrAddSlashes(trim($_POST["mem_birthday3"]));
	$mem_intro			= StrAddSlashes(trim($_POST["mem_intro"]));
	$mem_mailing		= StrAddSlashes(trim($_POST["mem_mailing"]));
	$delAttachFile		= StrAddSlashes(trim($_POST["delAttachFile"]));

	$public_id			= StrAddSlashes(trim($_POST["public_id"]));
	$public_name		= StrAddSlashes(trim($_POST["public_name"]));
	$public_email		= StrAddSlashes(trim($_POST["public_email"]));
	$public_homepage	= StrAddSlashes(trim($_POST["public_homepage"]));
	$public_addr		= StrAddSlashes(trim($_POST["public_addr"]));
	$public_telnum		= StrAddSlashes(trim($_POST["public_addr"]));
	$public_hpnum		= StrAddSlashes(trim($_POST["public_hpnum"]));
	$public_job			= StrAddSlashes(trim($_POST["public_job"]));
	$public_hobby		= StrAddSlashes(trim($_POST["public_hobby"]));
	$public_birthday	= StrAddSlashes(trim($_POST["public_birthday"]));
	$public_picture		= StrAddSlashes(trim($_POST["public_picture"]));
	$public_intro		= StrAddSlashes(trim($_POST["public_intro"]));
	$public_regdate		= StrAddSlashes(trim($_POST["public_regdate"]));
	$public_latestdate	= StrAddSlashes(trim($_POST["public_latestdate"]));
	$public_all			= StrAddSlashes(trim($_POST["public_all"]));

	$mem_mailing		= $mem_mailing		? 1 : 0;
	$public_id			= $public_id		? 1 : 0;
	$public_name		= $public_name		? 1 : 0;
	$public_email		= $public_email		? 1 : 0;
	$public_homepage	= $public_homepage	? 1 : 0;
	$public_addr		= $public_addr		? 1 : 0;
	$public_telnum		= $public_telnum	? 1 : 0;
	$public_hpnum		= $public_hpnum		? 1 : 0;
	$public_job			= $public_job		? 1 : 0;
	$public_hobby		= $public_hobby		? 1 : 0;
	$public_birthday	= $public_birthday	? 1 : 0;
	$public_picture		= $public_picture	? 1 : 0;
	$public_intro		= $public_intro		? 1 : 0;
	$public_regdate		= $public_regdate	? 1 : 0;
	$public_latestdate	= $public_latestdate? 1 : 0;
	$public_all			= $public_all		? 1 : 0;

	if($MemLevel>1) {
		if($mem_id != $MemId) Error("잘못된 접근입니다.");
	}

	if($mem_passwd1!=$mem_passwd2) Error("암호와 암호확인이 일치하지 않습니다.");
	if($mem_passwd1 && strlen($mem_passwd1)<4) Error("암호는 최소 4글자 이상 입력해 주세요.");
	if(IsBlank($mem_name)) Error("이름을 입력해 주세요.");
	if(IsBlank($mem_email)) Error("이메일을 입력해 주세요.");
	if(!mail_mx_check($mem_email)) Error("이메일주소가 올바르지 않습니다.");
	if(!IsEmail($mem_email)) Error("이메일 주소가 올바르지 않습니다.");
	if($mem_homepage) { if(!IsHomepage($mem_homepage)) Error("홈페이지주소가 형식에 맞지 않습니다."); }

	if(preg_match("/[^a-zA-Z\xA1-\xFE]/", $mem_name)) Error("이름에 유효하지 않은 문자가 포함되어 있습니다.");
	if($mem_nickname) {
		if(preg_match("/[^a-zA-Z0-9\xA1-\xFE\-_]/", $mem_nickname)) Error("닉네임에 유효하지 않은 문자가 포함되어 있습니다.");
		if(eregi("admin|root|fsboard|select|insert|delete|update|drop|shutdown|exec|관리자", $mem_nickname)) Error("{$mem_nickname} 은(는) 사용할 수 없는 닉네임입니다.");
	}
	if($mem_zipcode1 || $mem_zipcode2) {
		if(!IsNum($mem_zipcode1) || !IsNum($mem_zipcode2)) Error("우편번호가 올바르지 않습니다.");
	}
	if($mem_telnum1 || $mem_telnum2 || $mem_telnum3) {
		if(!IsNum($mem_telnum1) || !IsNum($mem_telnum2) || !IsNum($mem_telnum3)) Error("전화번호는 숫자만 입력해 주세요.");
	}
	if($mem_hpnum1 || $mem_hpnum2 || $mem_hpnum3) {
		if(!IsNum($mem_hpnum1) || !IsNum($mem_hpnum2) || !IsNum($mem_hpnum3)) Error("휴대폰번호는 숫자만 입력해 주세요.");
	}
	if($mem_birthday1 || $mem_birthday2 || $mem_birthday3) {
		if(!IsNum($mem_birthday1) || !IsNum($mem_birthday2) || !IsNum($mem_birthday3)) Error("생년월일은 숫자만 입력해 주세요.");
	}

	if($mem_zipcode1 && $mem_zipcode2) $mem_zipcode = "{$mem_zipcode1}-{$mem_zipcode2}";
	if($mem_telnum1 && $mem_telnum2 && $mem_telnum3) $mem_telnum = "{$mem_telnum1}-{$mem_telnum2}-{$mem_telnum3}";
	if($mem_hpnum1 && $mem_hpnum2 && $mem_hpnum3) $mem_hpnum = "{$mem_hpnum1}-{$mem_hpnum2}-{$mem_hpnum3}";
	if($mem_birthday1 && $mem_birthday2 && $mem_birthday3) $mem_birthday = "{$mem_birthday1}-{$mem_birthday2}-{$mem_birthday3}";

	$mem_ip_edit = $_SERVER["REMOTE_ADDR"];
	$mem_editdate = mktime();

	//기존 정보 가져오기
	$query = "SELECT mem_passwd,mem_level,mem_part,mem_auth,mem_picture FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("회원 정보가 없거나 세션시간이 만료되었습니다.<br /><br />재로그인 후 시도해 보시고 계속해서 문제가 발생시 관리자에게 문의 바랍니다.");
		else {
			$rs = mysql_fetch_array($result);
			$mem_oldpasswd = $rs["mem_passwd"];
			$mem_oldlevel = $rs["mem_level"];
			$mem_oldpart = $rs["mem_part"];
			$mem_oldauth = $rs["mem_auth"];
			$mem_oldpicture = $rs["mem_picture"];
		}
		mysql_free_result($result);
	}

	//바뀔암호가 있을 경우
	if($mem_passwd1 && $mem_passwd2) {
		$mem_passwd = md5($mem_passwd1);
	}
	//바뀔암호가 없을 경우
	else {
		$mem_passwd = $mem_oldpasswd;
	}

	//회원 구분이 바뀔경우 레벨을 리셋하고 비인증 상태로 설정
	if($mem_part!=$mem_oldpart) {
		$mem_auth = "";
		$mem_level = $MemDefaultLevel;
	}
	else {
		$mem_auth = $mem_oldauth;
		$mem_level = $mem_oldlevel;
	}

	//업로드파일 처리
	if($_FILES["attachFile"]) {
		$file_tmpn = $_FILES["attachFile"]["tmp_name"];
		$file_name = $_FILES["attachFile"]["name"];
		$file_size = $_FILES["attachFile"]["size"];

		if($file_size>0 && $file_tmpn) {
			if(!is_uploaded_file($file_tmpn)) Error("정상적인 방법으로 업로드 해주세요");
			$file_size = filesize($file_tmpn);

			//업로드 용량 제한
			if($fileMaxLimit<$file_size&&!$isAdmin) {
				Error("파일 업로드는 ".GetFileSize($fileMaxLimit)." 까지 가능합니다");
			}

			$temp = explode(".",$file_name);
			$s_point = count($temp)-1;
			$upload_check = $temp[$s_point];
			if(!eregi($upload_check,$allowExts)||!$upload_check) Error("파일 업로드는 {$allowExts} 확장자만 가능합니다");

			$file_tmpn = eregi_replace("\\\\","\\",$file_tmpn);

			if(!is_dir($FSDATA_ROOT."/{$dataPath}")) { //디렉토리 검사
				@mkdir($FSDATA_ROOT."/{$dataPath}",0777);
				@chmod($FSDATA_ROOT."/{$dataPath}",0757);
			}

			$file_name = StrAddSlashes($file_name);

			$file_fwpn = GetUniqueName($file_name, "{$FSDATA_ROOT}/{$dataPath}/"); //중복파일 검사
			if(!move_uploaded_file($file_tmpn, $file_fwpn)) Error("파일업로드가 제대로 되지 않았습니다");
			@chmod($file_fwpn,0646);
		}
	}

	$query = "
		UPDATE ".$_table_id_members." SET
				mem_passwd			= '$mem_passwd',
				mem_level			= $mem_level,
				mem_part			= '$mem_part',

				mem_auth			= '$mem_auth',
				mem_name			= '$mem_name',
				mem_nickname		= '$mem_nickname',
				mem_email			= '$mem_email',
				mem_homepage		= '$mem_homepage',
				mem_zipcode			= '$mem_zipcode',
				mem_addr1			= '$mem_addr1',
				mem_addr2			= '$mem_addr2',
				mem_telnum			= '$mem_telnum',
				mem_hpnum			= '$mem_hpnum',
				mem_job				= '$mem_job',
				mem_hobby			= '$mem_hobby',
				mem_birthday		= '$mem_birthday',
				mem_question		= '$mem_question',
				mem_answer			= '$mem_answer',
				mem_mailing			= $mem_mailing,
	";
	//사진 삭제만 할 경우
	if($delAttachFile) {
		if(file_exists("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}")) unlink("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}");
		$query .= "
				mem_picture			= '',
		";
	}
	//수정된 사진이 있을 경우
	if($file_name) {
		if($mem_oldpicture) {
			if(file_exists("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}")) unlink("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}");
		}
		$query .= "
				mem_picture			= '$file_name',
		";
	}
	$query .= "
				mem_editdate		= $mem_editdate,
				mem_ip_edit			= '$mem_ip_edit',

				public_id			= $public_id,
				public_name			= $public_name,
				public_email		= $public_email,
				public_homepage		= $public_homepage,
				public_addr			= $public_addr,
				public_telnum		= $public_telnum,
				public_hpnum		= $public_hpnum,
				public_job			= $public_job,
				public_hobby		= $public_hobby,
				public_birthday		= $public_birthday,
				public_picture		= $public_picture,
				public_intro		= $public_intro,
				public_regdate		= $public_regdate,
				public_latestdate	= $public_latestdate,
				public_all			= $public_all,

				mem_intro			= '$mem_intro'
			WHERE mem_id='{$mem_id}' AND idx={$idx};
	";

	//echo $query;exit;
	mysql_query($query) or Error(mysql_error());

	//바뀐 내용으로 세션 적용
	if($MemLevel>1) {
		$_SESSION["MemLevel"] = $mem_level;
		$_SESSION["MemPasswd"] = $mem_passwd;
		$_SESSION["MemName"] = $mem_nickname ? $mem_nickname : $mem_name;
	}

	//디자인 파일에 포함되어 실행될 경우 홈으로 이동
	if($combinedDesign) {
		//MovePage("/");
		echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry{window.alert('수정되었습니다.');window.location.href='{$PHP_SELF}?mode=modify&mem_id={$mem_id}';}catch(e){}\n//]]>\n</script>";
	}
	//창으로 실행될 경우 alert으로 알리기
	else {
		echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry{window.alert('수정되었습니다.');window.location.href='{$PHP_SELF}?mode=modify&mem_id={$mem_id}';}catch(e){}\n//]]>\n</script>";
	}












//////////////////////////////////////////////////////////////////////////////////////////회원정보 보기
} else if($MODE == "mem_info") {

	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];

	//참조값
	$ref = trim($_GET["ref"]);
	if(!$ref) Error("잘못된 접근입니다.");

	//게시판 아이디
	$id = trim($_GET["tx"]);

	$ref = base64_decode($ref); //참조값 복호화
	$mem_info = explode("|	|", $ref); //참조값 분리

	//회원정보 가져옴
	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='".$mem_info[0]."';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if($numrows) {
			$rs = mysql_fetch_array($result);

			$idx				= $rs["idx"];
			$mem_id				= $rs["mem_id"];
			$mem_level			= $rs["mem_level"];
			$mem_grade			= $rs["mem_grade"];
			$mem_part			= $rs["mem_part"];
			$mem_auth			= $rs["mem_auth"];
			$mem_name			= $rs["mem_name"];
			$mem_nickname		= $rs["mem_nickname"];
			$mem_idsn			= $rs["mem_idsn"];
			$mem_email			= $rs["mem_email"];
			$mem_homepage		= $rs["mem_homepage"];
			$mem_zipcode		= $rs["mem_zipcode"];
			$mem_addr1			= $rs["mem_addr1"];
			$mem_addr2			= $rs["mem_addr2"];
			$mem_telnum			= $rs["mem_telnum"];
			$mem_hpnum			= $rs["mem_hpnum"];
			$mem_job			= $rs["mem_job"];
			$mem_hobby			= $rs["mem_hobby"];
			$mem_birthday		= $rs["mem_birthday"];
			$mem_question		= $rs["mem_question"];
			$mem_answer			= $rs["mem_answer"];
			$mem_mailing		= $rs["mem_mailing"];
			$mem_picture		= $rs["mem_picture"];
			$mem_imgmark		= $rs["mem_imgmark"];
			$mem_imgname		= $rs["mem_imgname"];
			$mem_intro			= $rs["mem_intro"];
			$mem_regdate		= $rs["mem_regdate"];
			$mem_editdate		= $rs["mem_editdate"];
			$mem_latestdate		= $rs["mem_latestdate"];
			$mem_ip_reg			= $rs["mem_ip_reg"];
			$mem_ip_edit		= $rs["mem_ip_edit"];
			$mem_ip_login		= $rs["mem_ip_login"];
			$mem_loginnum		= $rs["mem_loginnum"];

			$public_id			= $rs["public_id"];
			$public_name		= $rs["public_name"];
			$public_email		= $rs["public_email"];
			$public_homepage	= $rs["public_homepage"];
			$public_addr		= $rs["public_addr"];
			$public_telnum		= $rs["public_telnum"];
			$public_hpnum		= $rs["public_hpnum"];
			$public_job			= $rs["public_job"];
			$public_hobby		= $rs["public_hobby"];
			$public_birthday	= $rs["public_birthday"];
			$public_picture		= $rs["public_picture"];
			$public_intro		= $rs["public_intro"];
			$public_regdate		= $rs["public_regdate"];
			$public_latestdate	= $rs["public_latestdate"];
			$public_all			= $rs["public_all"];

			//닉네임이 없을 경우 이름으로 대체
			if(!$mem_nickname) $mem_nickname = $mem_name;

			if($public_all) $open_info = 1;
		}
		//회원정보가 없을 경우
		else {
			$mem_part = "비회원";
			$mem_nickname = $mem_info[0];
			if(sizeof($mem_info)>1) {
				$mem_email = $mem_info[1];
				$public_email = 1;
				$open_info = 1;
			}
			if(sizeof($mem_info)>2) {
				$mem_homepage = $mem_info[2];
				$public_homepage = 1;
				$open_info = 1;
			}
		}
		mysql_free_result($result);
	}

	//관리자인지 확인
	if($mem_id && $mem_level==1) $isAdmin = true;

	$mem_part = $mem_auth ? "<img src=\"{$FSBOARD_PATH}/img/clip/btn_modify.gif\" alt=\"icon\" /> {$mem_part}" : $mem_part;
	$author = $mem_nickname;
	$mem_email = $mem_email ? "<a class=\"deflnk\" href=\"javascript:mail_to('".base64_encode($mem_email)."');\">이메일 보내기</a>" : "";
	$mem_homepage = $mem_homepage ? "<a class=\"englnk\" href=\"{$mem_homepage}\" onclick=\"window.open(this.href,'_blank'); return false;\">{$mem_homepage}</a>" : "";
	$mem_intro = StripHtmlChars($mem_intro);
	$mem_intro = nl2br($mem_intro);

	if($open_info && $public_name && $mem_name) $mem_ids = " - {$mem_name}";
	if($open_info && $public_id && $mem_id) $mem_ids .= "({$mem_id})";
	if($open_info && $public_picture && $mem_picture && file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_picture}")) $mem_pic = "{$FSBOARD_PATH}/data/{$dataPath}/{$mem_picture}"; else $mem_pic = "{$FSBOARD_PATH}/img/clip/no_image.gif";

	//이름 이미지가 있을 경우 이미지로 작성자 표시
	if($mem_id && file_exists("{$FSDATA_ROOT}/{$dataPath}/mem_name_".md5($mem_id).".gif")) {
		$author = "<img src=\"{$FSBOARD_PATH}/data/{$dataPath}/mem_name_".md5($mem_id).".gif\" alt=\"회원 이름\" />";
	}
	else {
		$author = StripHtmlChars($author);
		//if($authorLimit) $author = CutStr($author,$authorLimit);
		if($mem_id) $author = "<b>{$author}</b>"; //회원이면 작성자를 진하게 표시
	}

	//회원마크 이미지가 있을 경우 작성자 앞에 표시
	if($mem_id && file_exists("{$FSDATA_ROOT}/{$dataPath}/mem_mark_".md5($mem_id).".gif")) {
		$author = "<img src=\"{$FSBOARD_PATH}/data/{$dataPath}/mem_mark_".md5($mem_id).".gif\" alt=\"회원 마크\" />".$author;
	}

	echo DclrDocType();
?>
<title>회원정보</title>
<style type="text/css">
<!--
.defstyle { font-size:12px; font-family:돋움; color:#555555; }
.deflnk:link, .deflnk:visited { text-decoration:none; font-size:11px; font-family:돋움,Verdana; color:gray; }
.deflnk:hover { text-decoration:none; color:#0000FF; }
.englnk, .englnk:visited { text-decoration:none; font-size:11px; font-family:Arial; color:gray; }
.englnk:hover { text-decoration:none; color:#0000FF; }
.msgstyle { font-family:돋움; color:gray; }
.divline { border-bottom:1px solid #E1E1E1; }
img { border:0px; }
div { font-size:12px; font-family:돋움; color:#555555; border:0px solid #777777; }
#mem_info_layout { clear:both; width:465px; margin:0 auto; }
	#mem_info_title { height:1em; padding:0.75em; background-color:#fafbf7; border:1px solid #e1e1e1; }
		#mem_info_name { float:left; }
		#mem_info_part { float:right; }
	#mem_info_body { padding:0.1em; border-left:1px solid #e1e1e1; border-right:1px solid #e1e1e1; border-bottom:1px solid #e1e1e1; }
		#mem_info_pic { float:left; width:130px; margin:0.5em; text-align:center; }
		#mem_info_contents { text-align:center; }
			#mem_info_contents table { width:310px; text-align:left; white-space:normal; word-break:break-all; }
			#mem_info_contents td { height:2.2em; border-bottom:1px solid #e1e1e1; }
				.mem_info_l { width:80px; padding-left:0.7em; }
				.mem_info_m { width:5px; color:#e1e1e1; }
				.mem_info_r { padding-left:0.5em; }
			#mem_info_comment { clear:both; margin:0.25em; padding:1em; text-align:left; border-top:1px solid #e1e1e1; }
			#mem_info_btn { clear:both; margin:0.5em; text-align:right; }
	#mem_info_bottom { clear:both; margin:0.5em; text-align:center; }
-->
</style>
<script type="text/javascript" src="<?=$FSBOARD_PATH?>/lib/javascript.php"></script>
<script type="text/javascript">
//<![CDATA[
function mail_to(str) {
	window.location.href = "mailto:" + decode64(str);
}
//]]>
</script>
</head>

<body style="border-style:none;">

<!-- Begin DIV Layout -->
<div id="mem_info_layout">
	<div id="mem_info_title">
		<div id="mem_info_name">
					<img src="<?=$FSBOARD_PATH?>/img/clip/shutter.gif" alt="icon" style="vertical-align:middle;" />
					<?echo ($mem_id?"회원정보":"작성자 정보").$mem_ids;?>
		</div>
		<div id="mem_info_part">
			&nbsp;<?=$mem_part?>
		</div>
	</div>
	<div id="mem_info_body">
		<div id="mem_info_pic">
			<img src="<?=$mem_pic?>" id="mem_img1" onload="controlImage(this.id,120);" onclick="vwimgrzmv(this,this.src);" onError="this.src='<?=$FSBOARD_PATH?>/img/clip/no_image.gif';" alt="사진" style="border:1px solid #E1E1E1;" />
		</div>
		<div id="mem_info_contents">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="mem_info_l">이름</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$author?></td>
			</tr>
<?if($open_info && $public_email && $mem_email) {?>
			<tr>
				<td class="mem_info_l">이메일</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><img src="<?=$FSBOARD_PATH?>/img/clip/email.gif" alt="E-mail" style="vertical-align:middle;" /> <?=$mem_email?></td>
			</tr>
<?}?>
<?if($open_info && $public_homepage && $mem_homepage) {?>
			<tr>
				<td class="mem_info_l">홈페이지</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><!-- img src="<?=$FSBOARD_PATH?>/img/clip/home.gif" style="vertical-align:middle;" / --> <?=$mem_homepage?></td>
			</tr>
<?}?>
<?if($open_info && $public_addr && $mem_addr1) {?>
			<tr>
				<td class="mem_info_l">주소</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$mem_addr1?></td>
			</tr>
<?}?>
<?if($open_info && $public_telnum && $mem_telnum) {?>
			<tr>
				<td class="mem_info_l">전화</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$mem_telnum?></td>
			</tr>
<?}?>
<?if($open_info && $public_hpnum && $mem_hpnum) {?>
			<tr>
				<td class="mem_info_l">휴대폰</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$mem_hpnum?></td>
			</tr>
<?}?>
<?if($open_info && $public_job && $mem_job) {?>
			<tr>
				<td class="mem_info_l">직업</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$mem_job?></td>
			</tr>
<?}?>
<?if($open_info && $public_hobby && $mem_hobby) {?>
			<tr>
				<td class="mem_info_l">취미</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=$mem_hobby?></td>
			</tr>
<?}?>
<?if($open_info && $public_birthday && $mem_birthday) {?>
			<tr>
				<td class="mem_info_l">생일</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=date("Y년 m월 d일",strtotime($mem_birthday))?></td>
			</tr>
<?}?>
<?if($open_info && $public_regdate && $mem_regdate) {?>
			<tr>
				<td class="mem_info_l">가입일</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=date("Y년 m월 d일",$mem_regdate)?></td>
			</tr>
<?}?>
<?if($open_info && $public_latestdate && $mem_latestdate) {?>
			<tr>
				<td class="mem_info_l">최근 로그인</td>
				<td class="mem_info_m">|</td>
				<td class="mem_info_r"><?=date("Y년 m월 d일",$mem_latestdate)?></td>
			</tr>
<?}?>
			</table>
			<div id="mem_info_btn">
				<?if($id) {?>
					<a href="javascript:void(0);" class="deflnk" onclick="window.open('<?="{$FSBOARD_PATH}/{$_fsMainExecFile}?id={$id}&amp;mode=list&amp;srhctgr=0100&amp;srhstr=".urlencode($mem_nickname)?>');window.close();"><img src="<?="{$FSBOARD_PATH}/img/clip/doc3.gif"?>" alt="icon" />작성한글 보기</a>
				<?}?>
				<?if($MemId) {?>
					<a href="javascript:void(0);" class="deflnk" onclick="window.open('<?=$FSBOARD_PATH?>/lib/members.php?mode=modify','','width=570,height=550,resizable=1,scrollbars=1');window.close();"><img src="<?="{$FSBOARD_PATH}/img/clip/doc3.gif"?>" alt="icon" />내정보수정</a>
					<?if($MemLevel==1) {?><a href="javascript:void(0);" class="deflnk" onclick="window.open('<?="{$FSBOARD_PATH}/lib/members.php?mode=Admin.MemEdit&amp;idx={$idx}"?>');window.close();"><img src="<?="{$FSBOARD_PATH}/img/clip/doc3.gif"?>" alt="icon" />회원정보관리</a><?}?>
				<?} else {?>
					<a href="javascript:void(0);" class="deflnk" onclick="window.open('<?=$FSBOARD_PATH?>/lib/members.php?mode=join','','width=570,height=550,resizable=1,scrollbars=1');window.close();"><img src="<?="{$FSBOARD_PATH}/img/clip/doc3.gif"?>" alt="icon" />회원가입</a>
				<?}?>
			</div>
<?if($open_info && $public_intro && $mem_intro) {?>
			<div id="mem_info_comment">
				<?=$mem_intro?>
			</div>
<?}?>
		</div>
	</div>
	<div id="mem_info_bottom">
		<a href="javascript:window.close();"><img src="<?=$FSBOARD_PATH?>/img/btn/close.gif" alt="Close this window" /></a>
	</div>
</div>
<!-- End of DIV Layout -->

</body>
</html>
<?













//////////////////////////////////////////////////////////////////////////////////////////아이디중복체크
} else if($MODE == "idchk") {

	$width = "340px";
	$mem_id = $_GET["mem_id"] ? $_GET["mem_id"] : $_POST["mem_id"];
	$mem_id = StrAddSlashes(trim($mem_id));
	$msg = "";

	if($mem_id) {
		$query = "SELECT mem_id FROM ".$_table_id_members." WHERE mem_id='".$mem_id."';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rownum = mysql_num_rows($result);
			if($rownum) {
				$msg = "<span style=\"color:red;\"><b>'{$mem_id}'</b> 은(는) 이미 사용중인 아이디 입니다.</span>";
			}
			else {
				$msg = "<span style=\"color:green\"><b>{$mem_id}</b> 은(는) 사용가능한 아이디 입니다.</span><br /><br /><img src=\"{$FSBOARD_PATH}/img/clip/btn_confirm.gif\" onclick=\"opener.document.getElementById('mem_id').value='{$mem_id}';window.close();\" alt=\"확인\" style=\"vertical-align:middle;cursor:hand;\" />";
			}
			mysql_free_result($result);
		}

		if(eregi("[^a-zA-Z0-9_]", $mem_id)) {
			$msg = "<span style=\"color:red;\">영문 또는 영문,숫자 조합만 사용가능합니다!</span>";
		}
		if(strlen($mem_id)<3) {
			$msg = "<span style=\"color:red;\">아이디는 최소한 3글자 이상이어야 합니다.</span>";
		}
		if(strlen($mem_id)>200) {
			$msg = "<span style=\"color:red;\">아이디의 길이는 200글자를 넘을수 없습니다.</span>";
		}
		if(eregi("(fuck|suck|shit|bitch|damn|cunt)",$mem_id)) {
			$msg = "<span style=\"color:red;\">불량단어가 포함되어 있습니다.</span>";
		}
		if(eregi("(admin|root|master|manager|sysop)",$mem_id)) {
			$msg = "<span style=\"color:red;\"><b>'{$mem_id}'</b> 은(는) 사용할수 없는 아이디 입니다.</span>";
		}
	}
	echo DclrDocType();
?>
	<title>아이디 중복 확인</title>
	<style type="text/css">
	body { margin:0 auto; border-style:none; }
	div { font-size:12px; font-family:돋움,Verdana; }
	img { border:0px; }
	.txtbox { border:1px solid #DBDBDB; height:20px; }
	#idchk_layout { width:<?=$width?>; margin:0 auto; text-align:center; border-bottom:5px solid #e7e7e7; }
	#idchk_title {}
	#idchk_input { margin:1em; }
	#idchk_input input { vertical-align:middle; }
	#idchk_msg { margin-bottom:1em; }
	</style>
	</head>

	<body>
	<form id="__ctl1" name="__ctl1" method="post" action="<?=$_SERVER["PHP_SELF"]?>?mode=idchk">
	<div id="idchk_layout">
		<div id="idchk_title"><img src="<?=$FSBOARD_PATH?>/img/mem/title_id.gif" alt="아이디 중복 확인" /></div>
		<div id="idchk_input">
			<input type="text" size="25" id="mem_id" name="mem_id" value="<?=$mem_id?>" maxlength="127" class="txtbox"
			/><input type="image" src="<?=$FSBOARD_PATH?>/img/mem/btn_search.gif" />
		</div>
<?
	if($msg) {
		echo "
		<div id=\"idchk_msg\">{$msg}</div>
		";
	}
?>
	</div>
	<script type="text/javascript">
	//<![CDATA[
	document.getElementById("mem_id").focus();
	//]]>
	</script>
	</form>
	</body>
	</html>
<?











//////////////////////////////////////////////////////////////////////////////////////////우편번호 및 주소 찾기
} else if($MODE == "srhzipcode") {

	$width = "415px";
	$srhmode = trim($_GET["srhmode"]);
	$areaname = StrAddSlashes(trim($_POST["areaname"]));

	echo DclrDocType();
	echo "
	<title>우편번호 및 주소 검색</title>
	<style type=\"text/css\">
	body { margin:0 auto; border-style:none; }
	img { border:0px; }
	div { font-size:12px; font-family:돋움,Verdana; word-break:break-all; }
	.titlebar { font-weight:normal; height:25px; background-color:#EEF5E9; color:#5CA727; }
	.content { padding:5px; border-bottom:1px solid #DBDBDB; }
	.txtbox { border:1px solid #DBDBDB;height:20px; }
	.href { text-decoration:none; color:#555555; }
	.href:hover { text-decoration:underline; color:blue; }
	.href:visited { color:black; }

	#srhzipcode_layout { width:{$width}; margin:0 auto; text-align:center; border-bottom:5px solid #f0f0f0; }
	#srhzipcode_title {}
	#srhzipcode_input { margin:1.5em; }
	#srhzipcode_input input { vertical-align:middle; }
	#srhzipcode_result_title { padding:0.5em; font-weight:normal; background-color:#eef5e9; color:#5ca727; border:1px solid #e1e1e1; }
	.srhzipcode_result { padding:0.5em; text-align:left; border-bottom:1px solid #e1e1e1; }
	</style>
	<script type=\"text/javascript\">
	//<![CDATA[
	function dataApply(pzip1,pzip2,paddr,srhMode) {
		opener.document.getElementById('mem_zipcode1').value = pzip1;
		opener.document.getElementById('mem_zipcode2').value = pzip2;
		opener.document.getElementById('mem_addr1').value = paddr;

		window.close();
	}
	function init() {
		document.getElementById('areaname').focus();
	}
	window.onload = init;
	//]]>
	</script>
	</head>

	<body>
	<form id=\"__ctl1\" name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=srhzipcode&amp;srhmode=\">
	<div id=\"srhzipcode_layout\">
		<div id=\"srhzipcode_title\"><img src=\"{$FSBOARD_PATH}/img/mem/title_zip.gif\" alt=\"주소/우편번호 검색\" /></div>
		<div id=\"srhzipcode_input\">
			검색하고자 하는 지역의 읍/면/동을 입력하세요.<br />
			<input type=\"text\" size=\"30\" id=\"areaname\" name=\"areaname\" value=\"{$areaname}\" class=\"txtbox\" /><input type=\"image\" src=\"{$FSBOARD_PATH}/img/mem/btn_search.gif\" />
		</div>
	";

	if($areaname) {
		if(preg_match("/[^a-zA-Z0-9\xA1-\xFE\-_]/", $areaname)) {
			echo "<div>검색어에 유효하지 않은 문자가 포함되어 있습니다.</div><br /><br />";
			exit;
		}

		$query = "SHOW TABLES LIKE 'zipcode'";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) $numrows = mysql_num_rows($result);
		if(!$numrows) {
			echo "<div>우편번호 DB테이블이 없습니다.</div><br /><br />";
			exit;
		}

		$query = "SELECT * FROM zipcode WHERE dong LIKE '%{$areaname}%' ORDER BY sido,gugun,dong,bunji,zipcode ASC;";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rownum = mysql_num_rows($result);
			if($rownum>0) {

				echo "
		<div id=\"srhzipcode_result_title\">찾고자 하는 주소를 클릭하세요.</div>
				";

				while($rs = mysql_fetch_array($result)) {
					$zipcode = $rs["ZIPCODE"];
					$zipcode1 = substr($zipcode,0,3);
					$zipcode2 = substr($zipcode,4,7);
					$sido = $rs["SIDO"];
					$gugun =$rs["GUGUN"];
					$dong = $rs["DONG"];
					$bunji = $rs["BUNJI"];
					echo "
		<div class=\"srhzipcode_result\">
			<a class=\"href\" href=\"javascript:dataApply('{$zipcode1}','{$zipcode2}','{$sido} {$gugun} {$dong}','{$srhmode}');\">[{$zipcode}] {$sido} {$gugun} {$dong} {$bunji}</a>
		</div>
					";
				}
			}
			else {
				echo  "<div>검색된 결과가 없습니다.</div><br /><br />";
			}
			mysql_free_result($result);
		}
	}

	echo "
	</div>
	</form>
	</body>
	</html>
	";












//////////////////////////////////////////////////////////////////////////////////////////암호분실
} else if($MODE == "forgot_passwd") {

	//기본정보 확인과정을 거쳤는지 확인
	$verified1 = intval($_POST["verified1"]);

	$btnConfirm1 = "<a href=\"javascript:void(0)\" onclick=\"sendfrm(document.forms.__ctl1);\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"icon\" /></a>";
	$btnConfirm2 = "<a href=\"javascript:void(0)\" onclick=\"sendfrm(document.forms.__ctl2);\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"icon\" /></a>";
	if($combinedDesign) {
		$btnCancel = "<a href=\"javascript:window.history.back();\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>";
	}
	else {
		$btnCancel = "<a href=\"javascript:window.close();\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>";
	}

	members_head();

	if(!$verified1) {
		$mem_name = $_GET["mem_name"];
		$mem_email = $_GET["mem_email"];
		$mem_id = $_GET["mem_id"];

		echo "
		<table width=\"450\" style=\"border:7px solid #E7E7E7; color:#777; font-size:9pt;\" cellpadding=\"2\" cellspacing=\"3\">
		<form id=\"__ctl1\" name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=forgot_passwd\">
		<input type=\"hidden\" id=\"verified1\" name=\"verified1\" value=\"1\" />
		<input type=\"hidden\" id=\"fmode\" name=\"fmode\" value=\"fid\" />
			<tr>
				<td colspan=\"2\"><table><tr><td valign=\"top\"><img src=\"{$FSBOARD_PATH}/img/clip/arrow2.gif\" alt=\"icon\" /></td><td>아이디 찾기</td></tr></table></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td width=\"20%\">이름</td>
				<td><input type=\"text\" size=\"25\" id=\"mem_name\" name=\"mem_name\" maxlength=\"20\" tag=\"M||.+||이름을 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>주민등록번호</td>
				<td><input type=\"text\" size=\"10\" id=\"mem_idsn1\" name=\"mem_idsn1\" maxlength=\"6\" class=\"txtbox\" /> - <input type=\"password\" size=\"10\" id=\"mem_idsn2\" name=\"mem_idsn2\" maxlength=\"7\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>이메일</td>
				<td><input type=\"text\" size=\"45\" id=\"mem_email\" name=\"mem_email\" maxlength=\"255\" tag=\"M||.+||이메일주소를 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
		</table>
		<table width=\"450\">
			<tr>
				<td align=\"center\">
					{$btnConfirm1} {$btnCancel}
				</td>
			</tr>
		</form>
		</table>

		<br />

		<table width=\"450\" style=\"border:7px solid #E7E7E7; color:#777; font-size:9pt;\" cellpadding=\"2\" cellspacing=\"3\">
		<form id=\"__ctl2\" name=\"__ctl2\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=forgot_passwd\">
		<input type=\"hidden\" id=\"verfied1\" name=\"verified1\" value=\"1\" />
		<input type=\"hidden\" id=\"fmode\" name=\"fmode\" value=\"fpw\" />
			<tr>
				<td colspan=\"2\"><table><tr><td valign=\"top\"><img src=\"{$FSBOARD_PATH}/img/clip/arrow2.gif\" alt=\"icon\" /></td><td>암호 찾기</td></tr></table></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td width=\"20%\">아이디</td>
				<td><input type=\"text\" size=\"30\" id=\"mem_id\" name=\"mem_id\" maxlength=\"127\" tag=\"M||.+||아이디를 입력하세요.\" class=\"txtbox\" value=\"{$mem_id}\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>이름</td>
				<td><input type=\"text\" size=\"25\" id=\"mem_name\" name=\"mem_name\" maxlength=\"20\" tag=\"M||.+||이름을 입력하세요.\" class=\"txtbox\" value=\"{$mem_name}\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>주민등록번호</td>
				<td><input type=\"text\" size=\"10\" id=\"mem_idsn1\" name=\"mem_idsn1\" maxlength=\"6\" class=\"txtbox\" /> - <input type=\"password\" size=\"10\" id=\"mem_idsn2\" name=\"mem_idsn2\" maxlength=\"7\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>이메일</td>
				<td><input type=\"text\" size=\"45\" id=\"mem_email\" name=\"mem_email\" maxlength=\"255\" tag=\"M||.+||이메일주소를 입력하세요.\" class=\"txtbox\" value=\"{$mem_email}\" /></td>
			</tr>
		</table>
		<table width=\"450\">
			<tr>
				<td align=\"center\">
					{$btnConfirm2} {$btnCancel}
				</td>
			</tr>
		</form>
		</table>
		<script type=\"text/javascript\">
		//<![CDATA[
		function sendfrm(objFrm) {
			var frm = objFrm;
			if(checkValue(frm)) {
				frm.submit();
			}
			return;
		}
		//]]>
		</script>
		";
	}
	else { //암호힌트 질문 부분

		//외부접근 방지
		if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");
		if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

		//폼전송된 데이터 처리
		$fmode = trim($_POST["fmode"]);
		$mem_name = StrAddSlashes(trim($_POST["mem_name"]));
		$mem_idsn1 = StrAddSlashes(trim($_POST["mem_idsn1"]));
		$mem_idsn2 = StrAddSlashes(trim($_POST["mem_idsn2"]));
		$mem_email = StrAddSlashes(trim($_POST["mem_email"]));
		$mem_id = StrAddSlashes(trim($_POST["mem_id"]));

		$mem_idsn = $mem_idsn1&&$mem_idsn2 ? base64_encode("{$mem_idsn1}-{$mem_idsn2}") : "";

		if($fmode!="fid"&&$fmode!="fpw") Error("잘못된 접근입니다.");

		//원래 정보 가져옴
		$query = "SELECT * FROM ".$_table_id_members." WHERE mem_name='{$mem_name}' AND mem_email='{$mem_email}' ";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_array($result);
			$mem_idsn_origin = $rs["mem_idsn"]; //등록된 주민등록번호 가져옴
			mysql_free_result($result);
		} else Error("잠시후 다시 시도해 주세요.");

		//등록된 주민등록번호가 있을 경우
		if($mem_idsn_origin && $mem_idsn) $query .= " AND mem_idsn='{$mem_idsn}' ";
		if($mem_idsn_origin && !$mem_idsn) Error("주민등록번호를 입력해 주세요.");

		//아이디 찾기일 경우
		if($fmode == "fid") {
			if(!$mem_name) Error("이름을 입력해 주세요.");
			if(!$mem_email) Error("이메일주소를 입력해 주세요.");

			$query .= ";";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				if($numrows) {
					$rs = mysql_fetch_array($result);
					$idx = $rs["idx"];
					$mem_id_origin = $rs["mem_id"];
					$mem_name_origin = $rs["mem_name"];
					$mem_question = $rs["mem_question"];
					$mem_answer = $rs["mem_answer"];
				}
				else Error("입력하신 정보가 등록된 회원정보와 일치하지 않습니다.");
				mysql_free_result($result);
			}
		}

		//암호찾기일 경우
		if($fmode == "fpw") {
			if(!$mem_id) Error("아이디를 입력해 주세요.");
			if(!$mem_name) Error("이름을 입력해 주세요.");
			if(!$mem_email) Error("이메일주소를 입력해 주세요.");

			$query .= " AND mem_idsn='{$mem_idsn}';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				if($numrows) {
					$rs = mysql_fetch_array($result);
					$idx = $rs["idx"];
					$mem_id_origin = $rs["mem_id"];
					$mem_name_origin = $rs["mem_name"];
					$mem_question = $rs["mem_question"];
					$mem_answer = $rs["mem_answer"];
				}
				else Error("입력하신 정보가 등록된 회원정보와 일치하지 않습니다.");
				mysql_free_result($result);
			}
		}

		//암호힌트 질문 및 답변이 없을 경우
		if(!$mem_question) Error("회원님은 회원가입 당시 암호힌트 질문 및 암호힌트 답을 입력하지 않아 더이상 진행할수 없습니다.<br /><br />관리자에게 문의해 주시기 바랍니다.");

		echo "
			<table width=\"450\" style=\"border:7px solid #E7E7E7; color:#777777;\" cellpadding=\"2\" cellspacing=\"3\">
			<form id=\"__ctl1\" name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=recover_passwd\">
			<input type=\"hidden\" id=\"fmode\" name=\"fmode\" value=\"{$fmode}\" />
			<input type=\"hidden\" id=\"mem_name\" name=\"mem_name\" value=\"{$mem_name}\" />
			<input type=\"hidden\" id=\"mem_email\" name=\"mem_email\" value=\"{$mem_email}\" />
			<input type=\"hidden\" id=\"mem_id\" name=\"mem_id\" value=\"{$mem_id}\" />
			<input type=\"hidden\" id=\"mem_idsn\" name=\"mem_idsn\" value=\"{$mem_idsn}\" />
			<input type=\"hidden\" id=\"mem_question\" name=\"mem_question\" value=\"{$mem_question}\" />
			<input type=\"hidden\" id=\"idx\" name=\"idx\" value=\"{$idx}\" />
				<tr>
					<td colspan=\"2\"><table><tr><td><img src=\"{$FSBOARD_PATH}/img/clip/notice2.gif\" alt=\"icon\" /></td><td valign=\"bottom\">아래의 질문에 대한 답변을 입력해 주세요.</td></tr></table></td>
				</tr>
				<tr>
					<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
				</tr>
				<tr>
					<td width=\"20\"></td>
					<td>{$mem_question}</td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"text\" size=\"40\" id=\"mem_answer\" name=\"mem_answer\" maxlength=\"127\" tag=\"M||.+||답변을 입력하세요.\" class=\"txtbox\" /></td>
				</tr>
			</table>
			<table width=\"450\">
				<tr>
					<td align=\"center\">
						<a href=\"javascript:void(0)\" onclick=\"sendfrm();\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a>
						<a href=\"javascript:window.history.back();\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>
					</td>
				</tr>
			</form>
			</table>
			<script type=\"text/javascript\">
			//<![CDATA[
			function sendfrm() {
				var frm = document.forms.__ctl1;
				if(checkValue(frm)) frm.submit();
				return;
			}
			//]]>
			</script>
		";
	}

	members_foot();












//////////////////////////////////////////////////////////////////////////////////////////암호찾기 처리
} else if($MODE == "recover_passwd") {

	//외부접근 방지
	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	$fmode = trim($_POST["fmode"]);
	$mem_name = StrAddSlashes(trim($_POST["mem_name"]));
	$mem_email = StrAddSlashes(trim($_POST["mem_email"]));
	$mem_id = StrAddSlashes(trim($_POST["mem_id"]));
	$mem_idsn = StrAddSlashes(trim($_POST["mem_idsn"]));
	$idx = intval($_POST["idx"]);
	$mem_question = StrAddSlashes(trim($_POST["mem_question"]));
	$mem_answer = StrAddSlashes(trim($_POST["mem_answer"]));

	if(!$mem_answer) Error("질문에 대한 답변을 입력해 주세요.");
	if($fmode!="fid"&&$fmode!="fpw") Error("잘못된 접근입니다.");

	$btnFPW = "<a href=\"javascript:void(0)\" onclick=\"window.location.href='?mode=forgot_passwd&amp;mem_id=".urlencode($mem_id_origin)."&amp;mem_name=".urlencode($mem_name)."&amp;mem_email=".urlencode($mem_email)."'\">암호찾기</a>";
	if($combinedDesign) {
		$btnClose = "<a href=\"/\">홈으로</a>";
	}
	else {
		$btnClose = "<a href=\"javascript:window.close();\">창닫기</a>";
	}

	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_name='{$mem_name}' AND mem_email='{$mem_email}' AND mem_question='{$mem_question}' AND mem_answer='{$mem_answer}' AND idx={$idx} ";

	if($fmode=="fid") {
		$query .= ";";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$numrows = mysql_num_rows($result);
			if($numrows) {
				$rs = mysql_fetch_array($result);
				$mem_id_origin = $rs["mem_id"];

				members_head();
				echo "
					<table width=\"450\" style=\"border:7px solid #E7E7E7; color:#777777;\" cellpadding=\"2\" cellspacing=\"3\">
						<tr>
							<td colspan=\"2\"><table><tr><td><img src=\"{$FSBOARD_PATH}/img/clip/notice2.gif\" alt=\"icon\" /></td><td valign=\"bottom\">일치하는 회원정보의 검색 결과입니다.</td></tr></table></td>
						</tr>
						<tr>
							<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
						</tr>
						<tr>
							<td width=\"10\"></td>
							<td height=\"70\"><b>{$mem_name}</b> 님의 아이디는 <b style=\"font-size:14px;color:blue;\">{$mem_id_origin}</b> 입니다.<br /><br /></td>
						</tr>
					</table>
					<table width=\"450\">
						<tr>
							<td align=\"center\" height=\"30\">
								{$btnFPW} &nbsp; {$btnClose}
							</td>
						</tr>
					</table>
				";
				members_foot();
			}
			else Error("질문에 대한 답변이 올바르지 않습니다.");
			mysql_free_result($result);
		}
	}

	if($fmode=="fpw") {
		$query .= " AND mem_id='{$mem_id}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$numrows = mysql_num_rows($result);
			if($numrows) {
				//정보가 일치하면 임시암호로 변경
				$mem_passwd_new = GetRandomString(12);
				$mem_passwd = md5($mem_passwd_new);

				//변경된 암호를 이메일로 전송
				$from_mail = "webmaster@".$_SERVER["HTTP_HOST"];
				$from_name = "관리자";
				$mail_subject = "{$mem_name}님의 변경된 임시 암호입니다.";
				$mem_content = "{$mem_name}님의 암호가 임시암호로 변경되었습니다.\r\n\r\n로그인하신후 반드시 사용하시는 암호로 변경하시기 바랍니다.\r\n\r\n변경된 임시암호는 아래와 같습니다.\r\n\r\n{$mem_passwd_new}\r\n\r\n암호는 대소문자를 구분하므로 사용에 주의하시기 바랍니다.\r\n";
				@SendEmail(0, $mem_email, $mem_name, $from_mail, $from_name, $mail_subject, $mail_content, $cc="", $bcc="");

				//변경된 암호 업데이트
				$query = "UPDATE ".$_table_id_members." SET mem_passwd='{$mem_passwd}' WHERE mem_name='{$mem_name}' AND mem_email='{$mem_email}' AND mem_question='{$mem_question}' AND mem_answer='{$mem_answer}' AND idx={$idx} AND mem_id='{$mem_id}';";
				mysql_query($query) or Error(mysql_error());
				
				members_head();
				echo "
					<table width=\"450\" style=\"border:7px solid #E7E7E7; color:#777777;\" cellpadding=\"2\" cellspacing=\"3\">
						<tr>
							<td colspan=\"2\"><table><tr><td><img src=\"{$FSBOARD_PATH}/img/clip/notice2.gif\" alt=\"icon\" /></td><td valign=\"bottom\">일치하는 회원정보의 검색 결과입니다.</td></tr></table></td>
						</tr>
						<tr>
							<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
						</tr>
						<tr>
							<td width=\"10\"></td>
							<td>
								<br />
								<b>{$mem_name}</b> 님의 암호는 임시로 변경되었으며<br />
								변경된 암호를 이메일로 전송하였습니다.<br />
								<br />
								변경된 암호는 이메일을 통해 확인이 가능하며,<br />
								로그인 하신후 반드시 사용하는 암호로 변경하시기 바랍니다.<br />
								<br />
							</td>
						</tr>
					</table>
					<table width=\"450\">
						<tr>
							<td align=\"center\" height=\"30\">
								<a href=\"javascript:window.close();\"><img src=\"{$FSBOARD_PATH}/img/btn/close.gif\" alt=\"취소\" /></a>
							</td>
						</tr>
					</table>
				";
				members_foot();
			}
			else Error("질문에 대한 답변이 올바르지 않습니다.");
			mysql_free_result($result);
		}
	}












//////////////////////////////////////////////////////////////////////////////////////////회원탈퇴
} else if($MODE == "secede_member") {

	members_head();
	echo "
		<table width=\"450\" align=\"center\" style=\"border:7px solid #E7E7E7; color:#777; font-size:9pt;\" cellpadding=\"2\" cellspacing=\"3\">
		<form id=\"__ctl1\" name=\"__ctl1\" method=\"post\" action=\"{$PHP_SELF}?mode=secede_handling\">
			<tr>
				<td colspan=\"2\" bgcolor=\"#FCFBFA\">
					<img src=\"{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /> 회원탈퇴를 하게되면 회원정보가 모두 삭제되며 다시 복구할 수 없습니다.<br />
					&nbsp; &nbsp; 지금 즉시 탈퇴를 원하시면 아래의 사항들을 정확히 입력해 주세요.<br />
				</td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td width=\"20%\">아이디</td>
				<td><input type=\"text\" size=\"30\" id=\"mem_id\" name=\"mem_id\" maxlength=\"127\" tag=\"M||.+||아이디를 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>암호</td>
				<td><input type=\"password\" size=\"30\" id=\"mem_passwd\" name=\"mem_passwd\" maxlength=\"255\" tag=\"M||.+||암호를 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>이름</td>
				<td><input type=\"text\" size=\"25\" id=\"mem_name\" name=\"mem_name\" maxlength=\"20\" tag=\"M||.+||이름을 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>주민등록번호</td>
				<td><input type=\"text\" size=\"10\" id=\"mem_idsn1\" name=\"mem_idsn1\" maxlength=\"6\" class=\"txtbox\" /> - <input type=\"password\" size=\"10\" id=\"mem_idsn2\" name=\"mem_idsn2\" maxlength=\"7\" class=\"txtbox\" /></td>
			</tr>
			<tr>
				<td colspan=\"2\" bgcolor=\"#E0E0E0\"></td>
			</tr>
			<tr>
				<td>이메일주소</td>
				<td><input type=\"text\" size=\"40\" id=\"mem_email\" name=\"mem_email\" maxlength=\"255\" tag=\"M||.+||이메일주소를 입력하세요.\" class=\"txtbox\" /></td>
			</tr>
		</table>
		<table width=\"450\" align=\"center\">
			<tr>
				<td align=\"center\">
					<a href=\"javascript:void(0)\" onclick=\"sendfrm();\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a>
					<a href=\"javascript:window.close();\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"닫기\" /></a>
				</td>
			</tr>
		</form>
		</table>
		<script type=\"text/javascript\">
		//<![CDATA[
		function sendfrm() {
			var frm = document.forms.__ctl1;

			if(checkValue(frm)) {
				if(confirm('정말 탈퇴하시겠습니까?')) {
					frm.submit();
				}
			}
			return;
		}
		//]]>
		</script>
	";
	members_foot();












//////////////////////////////////////////////////////////////////////////////////////////회원탈퇴 처리
} else if($MODE == "secede_handling") {

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];

	if(!$MemId) Error("로그인 상태가 아닙니다.<br /><br />로그인 상태에서만 탈퇴가 가능합니다");

	$mem_id = StrAddSlashes(trim($_POST["mem_id"]));
	$mem_passwd = trim($_POST["mem_passwd"]);
	$mem_name = StrAddSlashes(trim($_POST["mem_name"]));
	$mem_idsn1 = StrAddSlashes(trim($_POST["mem_idsn1"]));
	$mem_idsn2 = StrAddSlashes(trim($_POST["mem_idsn2"]));
	$mem_email = StrAddSlashes(trim($_POST["mem_email"]));
	$idx = 0;

	if(!$mem_id) Error("아이디를 입력해 주세요.");
	if(!$mem_passwd) Error("암호를 입력해 주세요.");
	if(!$mem_name) Error("이름을 입력해 주세요.");
	if(!$mem_email) Error("이메일 주소를 입력해 주세요.");

	$mem_passwd = md5($mem_passwd);

	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND mem_passwd='{$mem_passwd}' AND mem_name='{$mem_name}' AND mem_email='{$mem_email}' ";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("회원정보가 일치하지 않습니다.");
		else {
			$rs = mysql_fetch_array($result);
			$idx = $rs["idx"];
			$mem_id_origin = $rs["mem_id"];
			$mem_passwd_origin = $rs["mem_passwd"];
			$mem_name_origin = $rs["mem_name"];
			$mem_idsn_origin = $rs["mem_idsn"];
			$mem_email_origin = $rs["mem_email"];

			$mem_picture = $rs["mem_picture"];
			$mem_imgmark = $rs["mem_imgmark"];
			$mem_imgname = $rs["mem_imgname"];
		}
		mysql_free_result($result);
	} else Error("잠시후 다시 시도해 주세요.");

	if($mem_idsn_origin && (!$mem_idsn1 || !$mem_idsn2)) Error("주민등록번호를 입력해 주세요.");

	if($mem_idsn_origin && ($mem_idsn1 && $mem_idsn2)) {
		$mem_idsn = base64_encode("{$mem_idsn1}-{$mem_idsn2}");
		$query .= " AND mem_idsn='{$mem_idsn}';";
	}

	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if($numrows) {
			//사진파일 삭제
			if($mem_picture) {
				if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_picture}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_picture}");
			}

			//마크이미지 삭제
			if($mem_imgmark) {
				if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgmark}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgmark}");
			}

			//이름이미지 삭제
			if($mem_imgname) {
				if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgname}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgname}");
			}

			//태그구름 삭제
			$query = "DELETE FROM ".$_table_id_tagcloud." WHERE memberId='".$MemId."';";
			mysql_query($query) or Error(mysql_error());

			//회원데이터 삭제
			$query = "DELETE FROM ".$_table_id_members." WHERE idx={$idx};";
			mysql_query($query) or Error(mysql_error());

			session_destroy();

			if($combinedDesign) {
				echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('탈퇴처리 되었습니다.');window.location.href='/';\n//]]>\n</script>";
			}
			else {
				echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('탈퇴처리 되었습니다.');window.close();\n//]]>\n</script>";
			}
		}
		else Error("회원정보가 일치하지 않습니다.");
	}












//#################################################################################################### ↑사용자 영역
//####################################################################################################
//#################################################################################################### ↓관리자영역


//////////////////////////////////////////////////////////////////////////////////////////ADMIN.리스트
} else if($MODE == "Admin.MemList") {

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("{$FSBOARD_PATH}/lib/setup.php?mode=Login&nav=List"); exit; }

	$pagesize = 25;
	$divpage = 10;

	$srhctgr = trim($_GET["srhctgr"] ? $_GET["srhctgr"] : $_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"] ? $_GET["srhstr"] : $_POST["srhstr"]);

	$page = intval($_GET["page"]);
	if(!$page) $page = 1;

	$query = "SELECT * FROM ".$_table_id_members." ";
	if($srhctgr&&$srhstr) $query .= " WHERE {$srhctgr} LIKE '%".str_replace("'","\\'",$srhstr)."%' ";

	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$totalObj = mysql_num_rows($result);
		mysql_free_result($result);
	}

	$totalpage = intval(($totalObj - 1) / $pagesize) + 1;
	if($page>=1) $sequence = $totalObj - ($pagesize * ($page - 1));

	$query .= " ORDER BY idx DESC LIMIT ".(($page-1)*$pagesize).",{$pagesize};";
	$result = mysql_query($query) or Error(mysql_error());

	if($result) {
		$numrows = mysql_num_rows($result);
		members_head();
		echo "
			<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<tr>
					<td><img src=\"{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /> ".($srhctgr&&$srhstr?"검색된 회원":"전체 회원")." : <b>{$numrows}</b></td>
					<td align=\"right\"><a href=\"javascript:void(0)\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=join','','width=570,height=550,resizable=1,scrollbars=1')\" title=\"회원추가\" class=\"lnk_def\"><img src=\"{$FSBOARD_PATH}/img/clip/docn.gif\" alt=\"icon\" style=\"vertical-align:middle;\" /> 회원 추가</a></td>
				</tr>
			</table>
			<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
				<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
					<td width=\"50\">번호</td>
					<td>아이디</td>
					<td>이름</td>
					<td>주민번호</td>
					<td width=\"30\">레벨</td>
					<td width=\"30\">인증</td>
					<td width=\"100\">가입일자</td>
					<td width=\"100\">로그인일자</td>
					<td width=\"50\">로그인수</td>
					<td width=\"40\">관리</td>
					<td width=\"30\">삭제</td>
				</tr>
		";
		if($numrows) {
			$i = 0;
			while($rs = mysql_fetch_array($result)) {
				$idx = $rs["idx"];
				$mem_id = $rs["mem_id"];
				$mem_name = $rs["mem_name"];
				$mem_nickname = $rs["mem_nickname"];
				$mem_level = $rs["mem_level"];
				$mem_part = $rs["mem_part"];
				$mem_jumin1 = $rs["mem_jumin1"];
				$mem_jumin2 = $rs["mem_jumin2"];
				$mem_jumin = $mem_jumin1."-".$mem_jumin2;
				$mem_auth = $rs["mem_auth"];
				$mem_regdate = $rs["mem_regdate"];
				$mem_latestdate = $rs["mem_latestdate"];
				$mem_loginnum = $rs["mem_loginnum"];
				$mem_imgmark = $rs["mem_imgmark"];
				$mem_imgname = $rs["mem_imgname"];

				$regdate = date("Y-m-d",$mem_regdate);
				$logindate = $mem_latestdate ? date("Y-m-d",$mem_latestdate) : "";

				$icoNew = ((mktime() - $mem_regdate) <= 60*60*24) ? "<img src=\"{$FSBOARD_PATH}/img/clip/new_red.gif\" alt=\"new\" style=\"vertical-align:middle;\" />" : "";
				$colLv = $mem_level<2 ? "<span style=\"color:red;font-weight:bold;\">{$mem_level}</span>" : "<span style=\"color:gray\">{$mem_level}</span>";
				$colLv = $mem_auth ? "<b>{$colLv}</b>" : $colLv;
				$colLg = ($logindate==date("Y-m-d")) ? "<span style=\"color:black;\">" : "<span style=\"color:silver;\">";
				$memauth = $mem_auth=="auth" ? "<span style=\"color:green\">○</span>" : "<span style=\"color:silver\">×</span>";
				$regdate = $regdate==date("Y-m-d") ? "<b>".date("H:i:s",$mem_regdate)."</b>" : $regdate;

				$mem_nickname = $mem_imgname ? "<img src=\"{$FSBOARD_PATH}/data/{$dataPath}/{$mem_imgname}\" alt=\"회원 이름\" /> &nbsp;<span style=\"color:silver;\">{$mem_nickname}</span>" : $mem_nickname;
				$mem_nickname = $mem_imgmark ? "<img src=\"{$FSBOARD_PATH}/data/{$dataPath}/{$mem_imgmark}\" alt=\"회원 마크\" />{$mem_nickname}" : $mem_nickname;

				if(!$mem_nickname) $mem_nickname = "<span style=\"color:silver;\">없음</span>";
				$mem_nickname = "<a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=mem_info&amp;ref=".urlencode(base64_encode($mem_id))."','','width=500,height=300,resizable=1,scrollbars=1');\" class=\"lnk_def\">{$mem_nickname}</a>";

				echo "
				<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
					<td>{$sequence}</td>
					<td align=\"left\">&nbsp; <a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=modify&amp;mem_id={$mem_id}','','width=570,height=550,resizable=1,scrollbars=1')\" class=\"lnk_def\"><b>$mem_id</b></a> $icoNew</td>
					<td>{$mem_name}</td>
					<td>{$mem_jumin}</td>
					<td>{$colLv}</td>
					<td>{$memauth}</td>
					<td>{$regdate}</td>
					<td>{$colLg} {$logindate}</span></td>
					<td>{$mem_loginnum}</td>
					<td><a href=\"".$_SERVER["PHP_SELF"]."?mode=Admin.MemEdit&amp;idx={$idx}&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}\"><img src=\"{$FSBOARD_PATH}/img/clip/admin.gif\" alt=\"관리\" /></a></td>
					<td><a href=\"".$_SERVER["PHP_SELF"]."?mode=Admin.MemErase&amp;idx={$idx}&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}\"><img src=\"{$FSBOARD_PATH}/img/clip/xbutton.gif\" alt=\"삭제\" /></a></td>
				</tr>
				";
				$sequence--;
				$i++;
			}
		}
		else {
			if($srhctgr&&$srhstr) {
				echo"
				<tr>
					<td colspan=\"12\" align=\"center\" height=\"30\">검색된 회원이 없습니다.</td>
				</tr>
				";
			} else {
				echo "
				<tr>
					<td colspan=\"12\" align=\"center\" height=\"30\">등록된 회원이 없습니다.</td>
				</tr>
				";
			}
		}
		echo "
			</table>
			<table width=\"{$width}\" align=\"{$align}\" class=\"defstyle\">
			<form id=\"__ctl_sr1\" name=\"__ctl_sr1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode={$MODE}\">
				<tr>
					<td align=\"center\">".NavPage($page,$divpage,$totalpage,"mode=Admin.MemList&srhctgr={$srhctgr}&srhstr={$srhstr}"," class=\"defstyle\""," <img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_left.gif\" alt=\"<<\" style=\"vertical-align:middle;\" /> , <img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_right.gif\" alt=\">>\" style=\"vertical-align:middle;\" /> ","[,]")."</td>
				</tr>
				<tr>
					<td align=\"center\">
						<table cellpadding=\"0\" cellspacing=\"0\">
							<tr>
								<td>
									<select id=\"srhctgr\" name=\"srhctgr\">
										<option value=\"mem_id\">아이디</option>
										<option value=\"mem_name\">이름</option>
										<option value=\"mem_nickname\">닉네임</option>
										<option value=\"mem_part\">회원구분</option>
										<option value=\"mem_level\">레벨</option>
										<option value=\"mem_grade\">등급</option>
										<option value=\"mem_email\">이메일</option>
										<option value=\"mem_homepage\">홈페이지</option>
										<option value=\"mem_zipcode\">우편번호</option>
										<option value=\"mem_addr1\">주소-앞</option>
										<option value=\"mem_addr2\">주소-뒤</option>
										<option value=\"mem_telnum\">전화번호</option>
										<option value=\"mem_hpnum\">휴대폰번호</option>
										<option value=\"mem_job\">직업</option>
										<option value=\"mem_hobby\">취미</option>
										<option value=\"mem_birthday\">생년월일</option>
										<option value=\"mem_question\">암호찾기질문</option>
										<option value=\"mem_answer\">암호찾기답변</option>
										<option value=\"mem_picture\">사진파일명</option>
										<option value=\"mem_intro\">자기소개</option>
									</select>
								</td>
								<td>
									<input type=\"text\" id=\"srhstr\" name=\"srhstr\" size=\"20\" style=\"border:1px solid #E0E0E0;height:20px;\" />
								</td>
								<td>
									<input type=\"image\" src=\"$FSBOARD_PATH/img/btn/search.gif\" style=\"vertical-align:middle;\" />
								</td>
		";
		if($srhctgr&&$srhstr) {
			echo "
								<td>
									<a href=\"{$PHP_SELF}?mode=Admin.MemList\"><img src=\"{$FSBOARD_PATH}/img/btn/list.gif\" alt=\"목록\" style=\"vertical-align:middle;\" /></a>
								</td>
			";
		}
		echo "
							</tr>
						</table>
					</td>
				</tr>
		";
		/*if(!$combinedDesign) {
			echo "
				<tr>
					<td align=\"center\" height=\"50\">
						<a href=\"javascript:void(0)\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=join','','width=570,height=550,resizable=1,scrollbars=1')\" class=\"lnk_def\"><img src=\"{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />회원 추가</a>
						&nbsp;
						<a href=\"{$FSBOARD_PATH}/lib/setup.php?mode=List\" class=\"lnk_def\"><img src=\"{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />게시판관리</a>
						&nbsp;
						<a href=\"{$FSBOARD_PATH}/lib/logout.php\" class=\"lnk_def\"><img src=\"{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />로그아웃</a>
					</td>
				</tr>
			";
		}*/
		echo "
			</form>
			</table>
		";

		members_foot();
		mysql_free_result($result);

	}

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";











//////////////////////////////////////////////////////////////////////////////////////////ADMIN.회원정보 수정
} else if($MODE == "Admin.MemEdit") {

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { Error("관리자만 접근가능합니다."); exit; }

	$idx = $_GET["idx"];

	$srhctgr = trim($_GET["srhctgr"] ? $_GET["srhctgr"] : $_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"] ? $_GET["srhstr"] : $_POST["srhstr"]);
	$page = intval($_GET["page"]);
	if(!$page) $page = 1;

	$query = "SELECT * FROM ".$_table_id_members." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());

	$width = 600;

	if($result) {
		$rs = mysql_fetch_array($result);
		mysql_free_result($result);

		$idx = $rs["idx"];
		$mem_id				= $rs["mem_id"];
		$mem_level			= $rs["mem_level"];
		$mem_grade			= $rs["mem_grade"];
		$mem_part			= $rs["mem_part"];
		$mem_jumin1			= $rs["mem_jumin1"];
		$mem_jumin2			= $rs["mem_jumin2"];
		$mem_auth			= $rs["mem_auth"];
		$mem_name			= $rs["mem_name"];
		$mem_nickname		= $rs["mem_nickname"];
		$mem_idsn			= $rs["mem_idsn"];
		$mem_email			= $rs["mem_email"];
		$mem_homepage		= $rs["mem_homepage"];
		$mem_zipcode		= $rs["mem_zipcode"];
		$mem_addr1			= $rs["mem_addr1"];
		$mem_addr2			= $rs["mem_addr2"];
		$mem_telnum			= $rs["mem_telnum"];
		$mem_hpnum			= $rs["mem_hpnum"];
		$mem_job			= $rs["mem_job"];
		$mem_hobby			= $rs["mem_hobby"];
		$mem_birthday		= $rs["mem_birthday"];
		$mem_question		= $rs["mem_question"];
		$mem_answer			= $rs["mem_answer"];
		$mem_mailing		= $rs["mem_mailing"];
		$mem_picture		= $rs["mem_picture"];
		$mem_imgmark		= $rs["mem_imgmark"];
		$mem_imgname		= $rs["mem_imgname"];
		$mem_intro			= $rs["mem_intro"];
		$mem_regdate		= $rs["mem_regdate"];
		$mem_editdate		= $rs["mem_editdate"];
		$mem_latestdate		= $rs["mem_latestdate"];
		$mem_ip_reg			= $rs["mem_ip_reg"];
		$mem_ip_edit		= $rs["mem_ip_edit"];
		$mem_ip_login		= $rs["mem_ip_login"];
		$mem_loginnum		= $rs["mem_loginnum"];

		$public_id			= $rs["public_id"];
		$public_name		= $rs["public_name"];
		$public_email		= $rs["public_email"];
		$public_homepage	= $rs["public_homepage"];
		$public_addr		= $rs["public_addr"];
		$public_telnum		= $rs["public_telnum"];
		$public_hpnum		= $rs["public_hpnum"];
		$public_job			= $rs["public_job"];
		$public_hobby		= $rs["public_hobby"];
		$public_birthday	= $rs["public_birthday"];
		$public_picture		= $rs["public_picture"];
		$public_intro		= $rs["public_intro"];
		$public_regdate		= $rs["public_regdate"];
		$public_latestdate	= $rs["public_latestdate"];
		$public_all			= $rs["public_all"];

		//텍스트박스에서 따옴표 에러 방지
		$mem_answer = StripHtmlChars($mem_answer);
		$mem_name = StripHtmlChars($mem_name);
		$mem_email = StripHtmlChars($mem_email);
		$mem_homepage = StripHtmlChars($mem_homepage);
		$mem_zipcode = StripHtmlChars($mem_zipcode);
		$mem_addr1 = StripHtmlChars($mem_addr1);
		$mem_addr2 = StripHtmlChars($mem_addr2);
		$mem_telnum = StripHtmlChars($mem_telnum);
		$mem_hpnum = StripHtmlChars($mem_hpnum);
		$mem_job = StripHtmlChars($mem_job);
		$mem_hobby = StripHtmlChars($mem_hobby);
		$mem_intro = StripHtmlChars($mem_intro);
	}

	members_head();
	echo "
	<table width=\"{$width}\" align=\"{$align}\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" cellpadding=\"2\" cellspacing=\"0\" class=\"defstyle\">
	<form id=\"__ctl1\" name=\"__ctl1\" method=\"post\" enctype=\"multipart/form-data\" action=\"{$PHP_SELF}?mode=Admin.MemEditSave&amp;idx={$idx}&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}\">
		<tr>
			<td colspan=\"2\">
				<table width=\"100%\" cellpadding=\"3\" bgcolor=\"#FBFAF7\">
					<tr>
						<td>".
							(!ereg("Admin.MemEdit",$_SERVER["HTTP_REFERER"])?
							"<img src=\"{$FSBOARD_PATH}/img/clip/doc2.gif\" alt=\"icon\" /> <b style=\"color:gray;\">회원정보 수정</b>":
							"<img src=\"{$FSBOARD_PATH}/img/clip/alert.gif\" alt=\"icon\" /> <b style=\"color:red;\">수정되었습니다.</b>")
						."</td>
						<td align=\"right\"><b>* 표시</b>는 반드시 확인 필요</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width=\"20%\">&nbsp; <b>아이디*</b></td>
			<td>
				<input type=\"text\" id=\"mem_id\" name=\"mem_id\" class=\"txtbox\" value=\"{$mem_id}\" size=\"30\" />
				<!-- img src=\"{$FSBOARD_PATH}/img/mem/btn_idchk.gif\" style=\"vertical-align:middle;cursor:hand;\" onclick=\"window.open('?mode=idchk&amp;mem_id='+document.getElementById('mem_id').value,'','width=334,height=145,resizable=1,scrollbars=0');\" / -->
				<input type=\"checkbox\" id=\"public_id\" name=\"public_id\" value=\"1\"".($public_id?" checked=\"checked\"":"")." />공개
			</td>
		</tr>
		<tr>
			<td>&nbsp; 암호</td>
			<td>
				<input type=\"password\" id=\"mem_passwd\" name=\"mem_passwd\" size=\"30\" class=\"txtbox\" />(변경할 경우만 입력)</td>
		</tr>


		<tr>
			<td>&nbsp; <b>회원구분*</b></td>
			<td>
				<select id=\"mem_part\" name=\"mem_part\"".($mem_id==$MemId?" disabled":"")." onchange=\"var frm=document.forms['__ctl1'];frm.mem_level.options[this.selectedIndex].selected=true;frm.mem_auth[0].checked=true;\">
	";
	for($i=sizeof($mem_part_element)-1; $i>=0; $i--) {
		echo "		<option".($mem_part==$mem_part_element[$i]?" selected=\"selected\"":"").">".$mem_part_element[$i]."</option>";
	}
	echo "
				</select>
				<input type=\"radio\" id=\"mem_auth1\" name=\"mem_auth\"".($mem_id==$MemId?" disabled":"")." value=\"auth\"".($mem_auth=="auth"?" checked=\"checked\"":"")." onclick=\"var frm=document.forms['__ctl1'];frm.mem_level.options[frm.mem_part.selectedIndex].selected=true\" />인증
				<input type=\"radio\" id=\"mem_auth2\" name=\"mem_auth\"".($mem_id==$MemId?" disabled":"")." value=\"\"".(!$mem_auth?" checked=\"checked\"":"")." onclick=\"document.forms['__ctl1'].mem_level.options[1].selected=true;\" />비인증
				(회원구분 선택에 따라 회원레벨이 자동변경됨)
			</td>
		</tr>

		<tr>
			<td>&nbsp; <b>회원레벨*</b></td>
			<td>
	";
	if($mem_id==$MemId) {
		echo "
				<input type=\"hidden\" id=\"mem_level\" name=\"mem_level\" value=\"{$mem_level}\" />
				<input type=\"hidden\" id=\"mem_part\" name=\"mem_part\" value=\"".StripHtmlChars($mem_part)."\" />
				<input type=\"hidden\" id=\"mem_auth\" name=\"mem_auth\" value=\"{$mem_auth}\" />
		";
	}
	echo "
				<select id=\"mem_level\" name=\"mem_level\"".($mem_id==$MemId?" disabled":"").">
	";
	for($i=sizeof($mem_part_element); $i>=1; $i--) {
		echo "<option value=\"$i\"".($mem_level==$i?" selected=\"selected\"":"").">$i</option>\n";
	}
	echo "
				</select>
				게시판에 적용될 레벨
			</td>
		</tr>
		";
/*
	echo "
		<tr>
			<td>&nbsp; <b>소 속*</b></td>
			<td>
				<select id=\"mem_jumin1\" name=\"mem_jumin1\"".($mem_id==$MemId?" disabled":"").">
	";
	for($i=0;$i < sizeof($mem_part_element2); $i++) {
		echo "		<option".($mem_jumin1==$mem_part_element2[$i]?" selected=\"selected\"":"").">".$mem_part_element2[$i]."</option>";
	}
	echo "
				</select>

			</td>
		</tr>
*/
	echo "
		<tr>
			<td>&nbsp; <b>이름*</b></td>
			<td>
				<input type=\"text\" id=\"mem_name\" name=\"mem_name\" class=\"txtbox\" value=\"{$mem_name}\" />
				<input type=\"checkbox\" id=\"public_name\" name=\"public_name\" value=\"1\"".($public_name?" checked=\"checked\"":"")." />공개
			</td>
		</tr>
		<tr>
			<td>&nbsp; 이메일</td>
			<td>
				<input type=\"text\" id=\"mem_email\" name=\"mem_email\" class=\"txtbox\" value=\"{$mem_email}\" size=\"50\" />
				<input type=\"checkbox\" id=\"public_email\" name=\"public_email\" value=\"1\"".($public_email?" checked=\"checked\"":"")." />공개
				<input type=\"checkbox\" id=\"mem_mailing\" name=\"mem_mailing\" value=\"1\"".($mem_mailing?" checked=\"checked\"":"")." />수신허용
			</td>
		</tr>
		<tr>
			<td>&nbsp; 주소</td>
			<td>
				<table>
					<tr>
						<td align=\"right\">우편번호</td>
						<td><input type=\"text\" id=\"mem_zipcode\" name=\"mem_zipcode\" class=\"txtbox\" value=\"{$mem_zipcode}\" size=\"8\" />(000-000)</td>
					</tr>
					<tr>
						<td align=\"right\">주소</td>
						<td><input type=\"text\" id=\"mem_addr1\" name=\"mem_addr1\" class=\"txtbox\" value=\"{$mem_addr1}\" size=\"50\" /> <input type=\"checkbox\" id=\"public_addr\" name=\"public_addr\" value=\"1\"".($public_addr?" checked=\"checked\"":"")." />공개</td>
					</tr>
					<tr>
						<td align=\"right\">주소나머지</td>
						<td><input type=\"text\" id=\"mem_addr2\" name=\"mem_addr2\" class=\"txtbox\" value=\"{$mem_addr2}\" size=\"40\" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp; 전화번호</td>
			<td>
				<input type=\"text\" id=\"mem_telnum\" name=\"mem_telnum\" class=\"txtbox\" value=\"{$mem_telnum}\" />(02-0000-0000)
				<input type=\"checkbox\" id=\"public_telnum\" name=\"public_telnum\" value=\"1\"".($public_telnum?" checked=\"checked\"":"")." />공개
			</td>
		</tr>
		<tr>
			<td>&nbsp; 휴대폰번호</td>
			<td>
				<input type=\"text\" id=\"mem_hpnum\" name=\"mem_hpnum\" class=\"txtbox\" value=\"{$mem_hpnum}\" />(010-0000-0000)
				<input type=\"checkbox\" id=\"public_hpnum\" name=\"public_hpnum\" value=\"1\"".($public_hpnum?" checked=\"checked\"":"")." />공개
			</td>
		</tr>
		<tr>
			<td>&nbsp; 암호찾기</td>
			<td>
				<table>
					<tr>
						<td align=\"right\">질문</td>
						<td><input type=\"text\" id=\"mem_question\" name=\"mem_question\" class=\"txtbox\" value=\"{$mem_question}\" size=\"50\" /></td>
					</tr>
					<tr>
						<td align=\"right\">답</td>
						<td><input type=\"text\" id=\"mem_answer\" name=\"mem_answer\" class=\"txtbox\" value=\"{$mem_answer}\" size=\"30\" /></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table width=\"{$width}\" align=\"{$align}\">
		<tr>
			<td align=\"center\">
				<input type=\"image\" src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" />
				<a href=\"javascript:window.history.back();\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>
				<a href=\"{$PHP_SELF}?mode=Admin.MemList&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}\"><img src=\"{$FSBOARD_PATH}/img/btn/list.gif\" alt=\"목록\" /></a>
			</td>
		</tr>
	</form>
	</table>
	<br />
	<br />
	<br />
	";

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";











//////////////////////////////////////////////////////////////////////////////////////////ADMIN.수정저장


} else if($MODE == "Admin.MemEditSave") {

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { Error("관리자만 접근가능합니다."); exit; }

	$idx = intval($_GET["idx"]);

	$srhctgr = trim($_GET["srhctgr"] ? $_GET["srhctgr"] : $_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"] ? $_GET["srhstr"] : $_POST["srhstr"]);
	$page = intval($_GET["page"]);
	if(!$page) $page = 1;

	$mem_id				= StrAddSlashes(trim($_POST["mem_id"]));
	$mem_passwd			= trim($_POST["mem_passwd"]);
	$mem_level			= intval(trim($_POST["mem_level"]));
	$mem_part			= StrAddSlashes(trim($_POST["mem_part"]));
//	$mem_jumin1			= StrAddSlashes(trim($_POST["mem_jumin1"]));
//	$mem_jumin2			= StrAddSlashes(trim($_POST["mem_jumin2"]));
	$mem_auth			= StrAddSlashes(trim($_POST["mem_auth"]));
	$mem_grade			= StrAddSlashes(trim($_POST["mem_grade"]));
	$mem_name			= StrAddSlashes(trim($_POST["mem_name"]));
	$mem_nickname		= StrAddSlashes(trim($_POST["mem_nickname"]));
	$mem_email			= StrAddSlashes(trim($_POST["mem_email"]));
	$mem_mailing		= trim($_POST["mem_mailing"]);
	$mem_homepage		= StrAddSlashes(trim($_POST["mem_homepage"]));
	$mem_zipcode		= StrAddSlashes(trim($_POST["mem_zipcode"]));
	$mem_addr1			= StrAddSlashes(trim($_POST["mem_addr1"]));
	$mem_addr2			= StrAddSlashes(trim($_POST["mem_addr2"]));
	$mem_telnum			= StrAddSlashes(trim($_POST["mem_telnum"]));
	$mem_hpnum			= StrAddSlashes(trim($_POST["mem_hpnum"]));
	$mem_job			= StrAddSlashes(trim($_POST["mem_job"]));
	$mem_hobby			= StrAddSlashes(trim($_POST["mem_hobby"]));
	$mem_birthday		= StrAddSlashes(trim($_POST["mem_birthday"]));
	$mem_question		= StrAddSlashes(trim($_POST["mem_question"]));
	$mem_answer			= StrAddSlashes(trim($_POST["mem_answer"]));
	$mem_intro			= StrAddSlashes(trim($_POST["mem_intro"]));
	$delAttachFile1		= intval($_POST["delAttachFile1"]);
	$delAttachFile2		= intval($_POST["delAttachFile2"]);
	$delAttachFile3		= intval($_POST["delAttachFile3"]);

	$public_id			= trim($_POST["public_id"]);
	$public_name		= trim($_POST["public_name"]);
	$public_email		= trim($_POST["public_email"]);
	$public_homepage	= trim($_POST["public_homepage"]);
	$public_addr		= trim($_POST["public_addr"]);
	$public_telnum		= trim($_POST["public_addr"]);
	$public_hpnum		= trim($_POST["public_hpnum"]);
	$public_job			= trim($_POST["public_job"]);
	$public_hobby		= trim($_POST["public_hobby"]);
	$public_birthday	= trim($_POST["public_birthday"]);
	$public_picture		= trim($_POST["public_picture"]);
	$public_intro		= trim($_POST["public_intro"]);
	$public_regdate		= trim($_POST["public_regdate"]);
	$public_latestdate	= trim($_POST["public_latestdate"]);
	$public_all			= trim($_POST["public_all"]);

	$mem_mailing		= $mem_mailing		? 1 : 0;
	$public_id			= $public_id		? 1 : 0;
	$public_name		= $public_name		? 1 : 0;
	$public_email		= $public_email		? 1 : 0;
	$public_homepage	= $public_homepage	? 1 : 0;
	$public_addr		= $public_addr		? 1 : 0;
	$public_telnum		= $public_telnum	? 1 : 0;
	$public_hpnum		= $public_hpnum		? 1 : 0;
	$public_job			= $public_job		? 1 : 0;
	$public_hobby		= $public_hobby		? 1 : 0;
	$public_birthday	= $public_birthday	? 1 : 0;
	$public_picture		= $public_picture	? 1 : 0;
	$public_intro		= $public_intro		? 1 : 0;
	$public_regdate		= $public_regdate	? 1 : 0;
	$public_latestdate	= $public_latestdate? 1 : 0;
	$public_all			= $public_all		? 1 : 0;

	$mem_editdate		= mktime();
	$mem_ip_edit		= $_SERVER["REMOTE_ADDR"];

	if(IsBlank($mem_id)) Error("회원의 아이디를 입력해 주세요.");
	if(IsBlank($mem_name)) Error("회원의 이름을 입력해 주세요.");

	$query = "SELECT mem_id,mem_passwd,mem_picture,mem_imgmark,mem_imgname FROM ".$_table_id_members." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if($numrows) {
			$rs = mysql_fetch_array($result);
			$mem_oldid = $rs["mem_id"];
			$mem_oldpasswd = $rs["mem_passwd"];
			$mem_oldpicture = $rs["mem_picture"];
			$mem_oldimgmark = $rs["imgmark"];
			$mem_oldimgname = $rs["imgname"];
		}
		else Error("회원정보가 이미 삭제되었거나 존재하지 않습니다.");

		mysql_free_result($result);

		$mem_passwd = $mem_passwd ? md5($mem_passwd) : $mem_oldpasswd;
	}
	else Error("처리중 문제가 발생하였습니다.<br /><br />서버 상태를 점검하거나 잠시후 다시 시도해 주세요.");

	if($mem_id != $mem_oldid) {
		$result = mysql_query("SELECT mem_id FROM ".$_table_id_members." WHERE mem_id='{$mem_id}';") or Error(mysql_error());
		if($result) {
			if(mysql_num_rows($result)) Error("{$mem_id} (은)는 이미 사용중인 아이디입니다.<br /><br />다른 아이디로 지정해 주세요.");
			mysql_free_result($result);
		}
	}

	/////파일 업로드
	$file_tmpn = array();
	$file_name = array();
	$file_size = array();
	$file_fwpn = array();

	for($i=0; $i<3; $i++) {
		if($_FILES["attachFile".($i+1)]) {
			$file_tmpn[$i] = $_FILES["attachFile".($i+1)]["tmp_name"];
			$file_name[$i] = $_FILES["attachFile".($i+1)]["name"];
			$file_size[$i] = $_FILES["attachFile".($i+1)]["size"];

			if($file_size[$i]>0 && $file_tmpn[$i]) {

				if(!is_uploaded_file($file_tmpn[$i])) Error("정상적인 방법으로 업로드 해주세요");
				$file_size[$i] = filesize($file_tmpn[$i]);

				//업로드 용량 제한
				if($fileMaxLimit<$file_size[$i]&&!$isAdmin) {
					Error("파일 업로드는 ".GetFileSize($fileMaxLimit)." 까지 가능합니다");
				}

				if($file_size[$i]>0) {

					//허용확장자 검사
					if($allowExts) {
						$temp = explode(".",$file_name[$i]);
						$s_point = count($temp)-1;
						$upload_check = $temp[$s_point];

						if($i>0) $allowExts = "gif";
						if(!eregi($upload_check,$allowExts)||!$upload_check) { Error("파일 업로드는 {$allowExts} 확장자만 가능합니다"); }
					}

					$file_tmpn[$i] = eregi_replace("\\\\","\\",$file_tmpn[$i]);
					//$file_name[$i] = str_replace(" ","_",$file_name[$i]);

					if(!is_dir($FSDATA_ROOT."/".$dataPath)) { //디렉토리 검사
						@mkdir($FSDATA_ROOT."/".$dataPath,0777);
						@chmod($FSDATA_ROOT."/".$dataPath,0757);
					}

					switch($i) {
						case 0 :
							$file_fwpn[$i] = GetUniqueName($file_name[$i], "{$FSDATA_ROOT}/{$dataPath}/"); //중복파일 검사
							if($mem_oldpicture) $delAttachFile1 = 1;
							break;
						case 1 :
							$file_fwpn[$i] = "{$FSDATA_ROOT}/{$dataPath}/mem_mark_".md5($mem_id).".gif";
							if($mem_oldimgmark) $delAttachFile2 = 1;
							break;
						case 2 :
							$file_fwpn[$i] = "{$FSDATA_ROOT}/{$dataPath}/mem_name_".md5($mem_id).".gif";
							if($mem_oldimgname) $delAttachFile3 = 1;
							break;
					}
					if(!move_uploaded_file($file_tmpn[$i], $file_fwpn[$i])) Error("파일업로드가 제대로 되지 않았습니다");
					@chmod($file_fwpn[$i],0646);
				}
			}
		}
	}

	if($delAttachFile1) {
		if(file_exists("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}")) unlink("$FSDATA_ROOT/{$dataPath}/{$mem_oldpicture}");
	}
	if($delAttachFile2) {
		if(file_exists("$FSDATA_ROOT/{$dataPath}/mem_mark_".md5($mem_id).".gif")) unlink("$FSDATA_ROOT/{$dataPath}/mem_mark_".md5($mem_id).".gif");
	}
	if($delAttachFile3) {
		if(file_exists("$FSDATA_ROOT/{$dataPath}/mem_name_".md5($mem_id).".gif")) unlink("$FSDATA_ROOT/{$dataPath}/mem_name_".md5($mem_id).".gif");
	}

	$query = "
		UPDATE ".$_table_id_members." SET
			mem_id				= '{$mem_id}',
			mem_passwd			= '{$mem_passwd}',
			mem_level			= {$mem_level},
			mem_part			= '{$mem_part}',
			mem_auth			= '{$mem_auth}',
			mem_grade			= '{$mem_grade}',
			mem_name			= '{$mem_name}',
			mem_nickname		= '{$mem_nickname}',
			mem_email			= '{$mem_email}',
			mem_mailing			= {$mem_mailing},
			mem_homepage		= '{$mem_homepage}',
			mem_zipcode			= '{$mem_zipcode}',
			mem_addr1			= '{$mem_addr1}',
			mem_addr2			= '{$mem_addr2}',
			mem_telnum			= '{$mem_telnum}',
			mem_hpnum			= '{$mem_hpnum}',
			mem_job				= '{$mem_job}',
			mem_hobby			= '{$mem_hobby}',
			mem_birthday		= '{$mem_birthday}',
			mem_question		= '{$mem_question}',
			mem_answer			= '{$mem_answer}',
			mem_editdate		= {$mem_editdate},
			mem_ip_edit			= '{$mem_ip_edit}',
	";

	if($delAttachFile1)	$query .= "mem_picture			= '',	";
	if($delAttachFile2)	$query .= "mem_imgmark			= '',	";
	if($delAttachFile3)	$query .= "mem_imgname			= '',	";
	if($file_name[0])	$query .= "mem_picture			= '{$file_name[0]}',	";
	if($file_name[1])	$query .= "mem_imgmark			= 'mem_mark_".md5($mem_id).".gif',	";
	if($file_name[2])	$query .= "mem_imgname			= 'mem_name_".md5($mem_id).".gif',	";

	$query .= "
			public_id			= {$public_id},
			public_name			= {$public_name},
			public_email		= {$public_email},
			public_homepage		= {$public_homepage},
			public_addr			= {$public_addr},
			public_telnum		= {$public_telnum},
			public_hpnum		= {$public_hpnum},
			public_job			= {$public_job},
			public_hobby		= {$public_hobby},
			public_birthday		= {$public_birthday},
			public_picture		= {$public_picture},
			public_intro		= {$public_intro},
			public_regdate		= {$public_regdate},
			public_latestdate	= {$public_latestdate},
			public_all			= {$public_all},

			mem_intro			= '{$mem_intro}'
		WHERE idx={$idx};
	";

	//echo $query;exit;
	mysql_query($query) or Error(mysql_error());
	MovePage("{$PHP_SELF}?mode=Admin.MemEdit&idx={$idx}&srhctgr={$srhctgr}&srhstr={$srhstr}&page={$page}");












//////////////////////////////////////////////////////////////////////////////////////////ADMIN.회원삭제
} else if($MODE == "Admin.MemErase") {

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { Error("관리자만 접근가능합니다."); exit; }

	$idx = intval($_GET["idx"]);
	if(!$idx) Error("키값이 잘못되었습니다.");

	$srhctgr = trim($_GET["srhctgr"] ? $_GET["srhctgr"] : $_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"] ? $_GET["srhstr"] : $_POST["srhstr"]);
	$page = intval($_GET["page"]);
	if(!$page) $page = 1;

	$confirmed = trim($_GET["confirmed"]);
	if($confirmed !== "true") {
		echo "
			<script type=\"text/javascript\">
			//<![CDATA[
			if(confirm('해당 회원의 정보가 모두 삭제되며 \\n삭제된 데이터는 다시는 복구할 수 없습니다.\\n\\n정말 삭제하시겠습니까?')) {
				window.location.href = '{$PHP_SELF}?mode=Admin.MemErase&idx={$idx}&confirmed=true&srhctgr={$srhctgr}&srhstr={$srhstr}&page={$page}';
			}
			else {
				window.history.back();
			}
			//]]>
			</script>
		";
		exit;
	}

	//회원정보 가져오기
	$query = "SELECT mem_id,mem_picture,mem_imgmark,mem_imgname FROM ".$_table_id_members." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("데이터가 존재하지 않습니다.");
		$rs = mysql_fetch_array($result);
		mysql_free_result($result);

		$mem_id = $rs["mem_id"];
		$mem_picture = $rs["mem_picture"];
		$mem_imgmark = $rs["mem_imgmark"];
		$mem_imgname = $rs["mem_imgname"];
	}

	//관리자가 자신의 데이터를 삭제하려할 경우
	if($mem_id==$MemId && $MemLevel==1) { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('관리자는 자기자신의 데이터를 삭제할 수 없습니다.');window.history.go(-2);\n//]]>\n</script>";exit; }

	//사진파일 삭제
	if($mem_picture) {
		if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_picture}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_picture}");
	}

	//마크이미지 삭제
	if($mem_imgmark) {
		if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgmark}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgmark}");
	}

	//이름이미지 삭제
	if($mem_imgname) {
		if(file_exists("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgname}")) unlink("{$FSDATA_ROOT}/{$dataPath}/{$mem_imgname}");
	}

	//데이터 삭제
	$query = "DELETE FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND idx={$idx};";
	mysql_query($query) or Error(mysql_error());

	MovePage("{$PHP_SELF}?mode=Admin.MemList&srhctgr={$srhctgr}&srhstr={$srhstr}&page={$page}");












} else { Error("Invalid Mode."); } if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
?>