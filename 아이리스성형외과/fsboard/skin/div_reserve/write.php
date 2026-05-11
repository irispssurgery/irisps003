<?
/*************************************************************
	FSBOARD WritePage Skin Module
*************************************************************/

	/*
	FCKeditor 버전업시 유지해야할 사항 (현재 적용 버전 FCKeditor2.4.3)

		/fckconfig.js
			FCKConfig.ToolbarSets["Basic2"]
			FCKConfig.FontNames = '굴림;돋움;바탕;궁서;맑은 고딕;
			_FileBrowserLanguage = 'php'
			_QuickUploadLanguage = 'php'

		/editor/filemanager/connectors/php/config.php
			$ConfigIsEnabled = true
			$ConfigUserFilesPath = '/fsboard/data/__FCKeditor'

		/editor/skins/default/fck_eidtor.css
			#efefde -> white
			#xEditingArea { border-right:#696969 1px solid; border-bottom:#696969 1px solid; border-left:#696969 1px solid; }
	*/

	if(!$write_included) exit; //write.php 직접 접근 막기

	$MSIE = ereg("MSIE",$_SERVER["HTTP_USER_AGENT"]);
	$title = "글 쓰 기";
	

	//게시물 수정일 경우
	if($mode=="edit") {
		if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

		$query = "SELECT * FROM ".$_table_id_board." WHERE idx={$idx}";
		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			if(!mysql_num_rows($result)) Error("게시물이 없거나 이미 삭제되었습니다.");

			$rs = mysql_fetch_array($result);

			$idx = $rs["idx"]; //고유 번호
			$objProperty = $rs["objProperty"]; //게시물 속성
			$isSecret = $rs["isSecret"]; //비밀글 여부
			$isNotice = $rs["isNotice"]; //공지글 여부
			$isMember = $rs["isMember"]; //작성자 회원 여부
			$docType = $rs["docType"]; //게시물 문서 타입
			$author = $rs["author"]; //작성자
			$e_mail = $rs["e_mail"]; //이메일
			$homeUrl = $rs["homeUrl"]; //홈페이지 주소
			$subject = $rs["subject"]; //게시물 제목
			$passwd = $rs["passwd"]; //게시물 암호
			$category = $rs["category"]; //게시물 카테고리
			$tag_ls	= $rs["tag_ls"]; //사용자 태그
			$regDate = $rs["regDate"]; //작성일자
			$editDate = $rs["editDate"]; //수정일자
			$memoLatestDate = $rs["memoLatestDate"]; //마지막 댓글올린 날짜
			$memoNum = $rs["memoNum"]; //댓글갯수
			$readNum = $rs["readNum"]; //조회수
			$voteNum = $rs["voteNum"]; //추천수
			$ipReg = $rs["ipReg"]; //게시물 등록 IP주소
			$ipEdit = $rs["ipEdit"]; //게시물 수정 IP주소
			$usrAgentReg = $rs["usrAgentReg"]; //등록 환경
			$usrAgentEdit = $rs["usrAgentEdit"]; //수정 환경
			$ref = $rs["ref"]; //답글 참조 번호
			$reStep = $rs["reStep"]; //답글 스텝
			$reLevel = $rs["reLevel"]; //답글 레벨
			$siteLink1 = $rs["siteLink1"]; //사용자 링크주소1
			$siteLink2 = $rs["siteLink2"]; //사용자 링크주소2
			$tbLink = $rs["tbLink"]; //트랙백 주소
			$isFile = false;
			for($i=1; $i<=$fileMaxNum; $i++) {
				${"fileName".$i} = $rs["fileName".$i]; //첨부 파일명 n
				${"fileDownload".$i} = $rs["fileDownload".$i]; //첨부 파일다운로드 횟수 n
				if(${"fileName".$i}) $isFile = true;
			}
			$contents = $rs["contents"]; //글내용

			mysql_free_result($result);

			$author = str_replace("\"","&#32;",$author);
			$e_mail = str_replace("\"","&#32;",$e_mail);
			$homeUrl = str_replace("\"","&#32;",$homeUrl);
			$subject = str_replace("\"","&#32;",$subject);
			$siteLink1 = str_replace("\"","&#32;",$siteLink1);
			$siteLink2 = str_replace("\"","&#32;",$siteLink2);
			$contents = StripHtmlChars($contents);

			$other01 = Trim($rs["other01"]); //연락처
			$other02 = Trim($rs["other02"]); //연락처
			$other03 = Trim($rs["other03"]); //연락처
			$other04 = Trim($rs["other04"]); //예약날짜
			$other05 = Trim($rs["other05"]); //예약시간
			$other06 = Trim($rs["other06"]); //예약시간
			$other07 = Trim($rs["other07"]); //예약시간
			$other08 = Trim($rs["other08"]); //예약시간
		}

		$title = "글 수 정";
		$writeFrmDefMsg = "";
	}

	//답변글 등록일 경우
	if($mode=="reply") {
		if(!$useReply) {
			//Error("이 게시판은 답변기능을 사용하지 않습니다.");
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("이 게시판은 답변기능을 사용하지 않습니다."));
		}

		if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

		$ref = intval($_GET["ref"]);
		$reStep = intval($_GET["reStep"]);
		$reLevel = intval($_GET["reLevel"]);

		if(!$ref) Error("답변코드가 잘못되었습니다.");

		$query = "SELECT author,subject,contents,ref,reStep,reLevel FROM ".$_table_id_board." WHERE idx={$idx}";
		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$rs = mysql_fetch_array($result);

			$author = $rs["author"];
			$subject = $rs["subject"];
			$contents = $rs["contents"];
			$ref = intval($rs["ref"]);
			$reStep = intval($rs["reStep"]);
			$reLevel = intval($rs["reLevel"]);

			$author = str_replace("\"","&#34;",$author);
			$subject = str_replace("\"","&#34;",$subject);
			//$subject = "답변:".$subject;
			$contents = strip_tags($contents);
			$contents = "[{$author} 님의 글]\n".$contents;
			$contents = str_replace("\n","\n &gt; ",$contents);
			$contents = "\n\n{$contents}";

			$author = "";

			mysql_free_result($result);
		}

		$title = "답변글 쓰기";
		$writeFrmDefMsg = "";
	}

	//회원로그인상태이면
	if($MemId) {
		$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$MemId}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_array($result);

			$mem_id = $rs["mem_id"];
			$mem_nickname = $rs["mem_nickname"];
			$mem_name = $mem_nickname ? $mem_nickname : $rs["mem_name"];
			$mem_email = $rs["mem_email"];
			$mem_homepage = $rs["mem_homepage"];
			mysql_free_result($result);
		}
	}




	//버튼 설정
	$btnPath = "$FSBOARD_PATH/img/btn"; //버튼 경로

	$btnSubmit = "<a href=\"javascript:uploading();\" class=\"lnk_write\"><img src=\"{$btnPath}/submit.gif\" alt=\"확인\" /></a>";
	$btnCancel = "<a href=\"javascript:window.history.go(".($MemId&&$mode=="edit"?"-2":"-1").");\" class=\"lnk_write\"><img src=\"{$btnPath}/cancel.gif\" alt=\"취소\" /></a>";
	$btnList = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("list",1)."&amp;page={$page}\" class=\"lnk_write\"><img src=\"{$btnPath}/list.gif\" alt=\"목록\" /></a>";






////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
ContentTop();
?>





<!-- 글쓰기 폼 -->
<div id="WriteFrame">
	<div id="WriteLayout">
		<div id="WriteFormTitle"><?=$title?></div>
		<input type="hidden" id="isSecret" name="isSecret" value="1" />
		<input type="hidden" id="other07" name="other07" value="no" />
		<input type="hidden" id="other08" name="other08" value="대기" />
<?if(!$MemId&&!$isAdmin):?>
		<div class="WriteFormLabel">암호</div>
		<div class="WriteFormInput_CR"><input type="password" size="30" id="passwd" name="passwd" class="write_txtbox1" /></div>

		<div class="WriteFormLabel">이름</div>
		<div class="WriteFormInput_CR"><input type="text" size="30" id="author" name="author" class="write_txtbox1" value="<?=$author?>" /></div>

		<div class="WriteFormLabel">이메일</div>
		<div class="WriteFormInput_CR"><input type="text" size="60" id="e_mail" name="e_mail" class="write_txtbox1" value="<?=$e_mail?>" /></div>
<?else:?>
		<input type="hidden" id="author" name="author" value="<?=$mem_name?>" />
		<input type="hidden" id="e_mail" name="e_mail" value="<?=$mem_email?>" />
		<input type="hidden" id="homeUrl" name="homeUrl" value="<?=$mem_homepage?>" />
<?endif;?>
		<div class="WriteFormLabel">선택</div>
		<div class="WriteFormInput_CR">
<?
if($useCategory&&$categories) { //카테고리 사용할 경우
	$categoryElement = explode(",",$categories); //카테고리 분리

	echo "
					<select id=\"category\" name=\"category\">
						<option value=\"\">카테고리</option>
		";
	for($i=0,$till=sizeof($categoryElement); $i<$till; $i++) {
		echo "
						<option value=\"".str_replace("\"","&#34;",$categoryElement[$i])."\"".($categoryElement[$i]==$category?" selected=\"selected\"":"").">{$categoryElement[$i]}</option>
		";
	}
	echo "
					</select>
	";
}

if($useHtml!="block"&&$usehtml_level) {
	echo "
					<select id=\"docType\" name=\"docType\" onchange=\"chg_doctype(this)\">
						<option value=\"text\"".($docType=="text"?" selected=\"selected\"":"").">텍스트 타입</option>
						<option value=\"html\"".($docType=="html"?" selected=\"selected\"":"").">HTML 타입</option>
						<option value=\"br\"".($docType=="br"?" selected=\"selected\"":"").">HTML+&lt;br /&gt;</option>
						<option value=\"editor\"".($docType=="editor"?" selected=\"selected\"":"").">웹에디터</option>
					</select>
	";
}


//공지사항 체크
if($isAdmin || $noticewrite_level) { echo "<input type=\"checkbox\" id=\"isNotice\" name=\"isNotice\" id=\"isNotice\" value=\"1\"".($isNotice?" checked=\"checked\"":"")." /><label for=\"isNotice\">공지</label>"; }
?>
					&nbsp;
					<span id="pnlBtnCtlSz" style="display:none;"><a href="javascript:formResize(document.getElementById('contents'),3);"><img src="<?=$FSBOARD_PATH."/img/clip/dbl_arrow_down.gif"?>" alt="글쓰기 영역 늘리기" style="vertical-align:middle;" /></a><a href="javascript:formResize(document.getElementById('contents'),-3)"><img src="<?=$FSBOARD_PATH."/img/clip/dbl_arrow_up.gif"?>" alt="글쓰기 영역 줄이기" style="vertical-align:middle;" /></a></span>
		</div>
		<div class="WriteFormLabel">연 락 처</div>
		<div class="WriteFormInput_CR"><input type="text" size="4" maxlength="4" id="other01" name="other01" class="write_txtbox1" value="<?=$other01?>" />-<input type="text" size="4" maxlength="4" id="other02" name="other02" class="write_txtbox1" value="<?=$other02?>" />-<input type="text" size="4" maxlength="4" id="other03" name="other03" class="write_txtbox1" value="<?=$other03?>" /></div>
		<div class="WriteFormLabel">예약일시</div>
		<div class="WriteFormInput_CR">
					<input type="text" name="other04" id="other04" class="write_txtbox1" value="<?=$other04?>"  onClick="Calendar_D(document.all.other04);"  size="10" maxlength="10" readonly class="i2"> <img src="<?=$FSBOARD_PATH."/img/clip/calender.gif"?>" align=absmiddle onClick="Calendar_D(document.all.other04);" style="cursor:hand;">  
					<select name="other05" size="1">
						<option value="00" <? if($other05=="00"){?>selected<?}?>>00</option>					
						<option value="01" <? if($other05=="01"){?>selected<?}?>>01</option>
						<option value="02" <? if($other05=="02"){?>selected<?}?>>02</option>
						<option value="03" <? if($other05=="03"){?>selected<?}?>>03</option>
						<option value="04" <? if($other05=="04"){?>selected<?}?>>04</option>
						<option value="05" <? if($other05=="05"){?>selected<?}?>>05</option>
						<option value="06" <? if($other05=="06"){?>selected<?}?>>06</option>
						<option value="07" <? if($other05=="07"){?>selected<?}?>>07</option>
						<option value="08" <? if($other05=="08"){?>selected<?}?>>08</option>
						<option value="09" <? if($other05=="09"){?>selected<?}?>>09</option>
						<option value="10" <? if($other05=="10"){?>selected<?}?>>10</option>
						<option value="11" <? if($other05=="11"){?>selected<?}?>>11</option>
						<option value="12" <? if($other05=="12"){?>selected<?}?>>12</option>
						<option value="13" <? if($other05=="13"){?>selected<?}?>>13</option>
						<option value="14" <? if($other05=="14"){?>selected<?}?>>14</option>
						<option value="15" <? if($other05=="15"){?>selected<?}?>>15</option>
						<option value="16" <? if($other05=="16"){?>selected<?}?>>16</option>
						<option value="17" <? if($other05=="17"){?>selected<?}?>>17</option>
						<option value="18" <? if($other05=="18"){?>selected<?}?>>18</option>
						<option value="19" <? if($other05=="19"){?>selected<?}?>>19</option>
						<option value="20" <? if($other05=="20"){?>selected<?}?>>20</option>
						<option value="21" <? if($other05=="21"){?>selected<?}?>>21</option>
						<option value="22" <? if($other05=="22"){?>selected<?}?>>22</option>
						<option value="23" <? if($other05=="23"){?>selected<?}?>>23</option>
					 </select> 시
					<select name="other06" size="1">
						<option value="00" <? if($other06=="00"){?>selected<?}?>>00</option>					
						<option value="05" <? if($other06=="05"){?>selected<?}?>>05</option>					
						<option value="10" <? if($other06=="10"){?>selected<?}?>>10</option>
						<option value="15" <? if($other06=="15"){?>selected<?}?>>15</option>
						<option value="20" <? if($other06=="20"){?>selected<?}?>>20</option>
						<option value="25" <? if($other06=="25"){?>selected<?}?>>25</option>
						<option value="30" <? if($other06=="30"){?>selected<?}?>>30</option>
						<option value="35" <? if($other06=="35"){?>selected<?}?>>35</option>
						<option value="40" <? if($other06=="40"){?>selected<?}?>>40</option>
						<option value="45" <? if($other06=="45"){?>selected<?}?>>45</option>
						<option value="50" <? if($other06=="50"){?>selected<?}?>>50</option>
						<option value="55" <? if($other06=="55"){?>selected<?}?>>55</option>
					 </select> 분
			</div>
		<div class="WriteFormLabel">예약제목</div>
		<div class="WriteFormInput_CR"><input type="text" size="70" id="subject" name="subject" class="write_txtbox1" value="<?=$subject?>" /></div>

		<div id="_Textarea">
			<textarea cols="90" rows="27" id="_DataTextarea" name="contents" style="width:100%;overflow:auto;" class="write_txtbox2"><?echo $writeFrmDefMsg.$contents;?></textarea>
		</div>
		<div id="_FCKeditor" style="display:none;">
			<textarea id="_DataFCKeditor" name="contents" cols="80" rows="27" disabled="disabled"></textarea>

		</div>


<?
if($useAttachFile) { //파일첨부기능
	for($i=1; $i<=$fileMaxNum; $i++) {
		echo "
		<div id=\"file{$i}\" class=\"FILEFIELDROW\" style=\"display:".($i<2?"inline":(${"fileName".$i}?"inline":"none")).";\">
			<div class=\"WriteFormLabel\">파일첨부 {$i}</div>
			<div class=\"WriteFormInput_CR\">
				<img src=\"".(${"fileName".$i}&&eregi("\.gif|\.jpg|\.jpeg|\.bmp|\.png",${"fileName".$i}) ? "{$PHP_SELF}?id={$id}&amp;mode=filelink&amp;filename=".urlencode(${"fileName".$i}) : "{$FSBOARD_PATH}/img/clip/blank.gif")."\" onload=\"if(this.src.indexOf('blank.gif')){this.style.display='none';}\" onError=\"this.style.display='none';\" onclick=\"vwimgrzmv(this,this.src);\" alt=\"첨부이미지 미리보기\" style=\"width:22px;height:18px;vertical-align:middle;cursor:hand;\"
				/><input type=\"file\" size=\"45\" id=\"attachFile{$i}\" name=\"attachFile{$i}\" onChange=\"chkfile();\" class=\"write_filebox\" /><span class=\"write_fl\"><nobr>(".GetFileSize($fileMaxLimit)." 이하)</nobr></span>";
		if($i==1&&$fileMaxNum>1) {
			echo "
				<span id=\"lblFileBtn\"><nobr><a href=\"javascript:vf(1,1);\"><img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_down.gif\" alt=\"↓\" style=\"vertical-align:middle;\" />추가</a></nobr></span>";
		}
		if(${"fileName".$i}) {
			echo "
				<div class=\"WriteAttachFile\"></div>
				<div><img src=\"{$FSBOARD_PATH}/img/clip/clip_blue.gif\" alt=\"clip\" style=\"vertical-align:middle;\" />첨부파일{$i} : <a href=\"{$PHP_SELF}?id={$id}&amp;mode=filelink&amp;filename=".urlencode(${"fileName".$i})."\">".${"fileName".$i}."</a> <input type=\"checkbox\" id=\"delAttachFile{$i}\" name=\"delAttachFile{$i}\" id=\"delAttachFile{$i}\" value=\"1\" /><label for=\"delAttachFile{$i}\">삭제</label></div>";
		}
		echo "
			</div>
		</div>
		";
	}
}
?>

		<div id="WriteFormBtn"><?echo "<div id=\"btns\">{$btnSubmit} {$btnCancel} {$btnList}</div>"; //버튼 출력?></div>
	</div>
</div>


<!-- 전송 로딩 메시지 -->
<div id="blind_form" style="position:absolute; left:0px; top:0px; width:0px; height:0px; z-index:10; visibility:hidden;">
	<div style="width:100%; height:100%; background-color:#FFFFFF; filter:alpha(opacity=80);">
		<div id="loadingmsg" style="position:absolute; left:-100px; top:-100px; width:300px; height:30px; white-space:nowrap; visibility:hidden; z-index:11;">
			<b><blink>자료를 등록중입니다. 잠시만 기다려 주세요...</blink></b>
		</div>
	</div>
</div>


<?
//답변쓰기일 경우
if($mode=="reply") echo "<input type=\"hidden\" id=\"ref\" name=\"ref\" value=\"{$ref}\"><input type=\"hidden\" id=\"reStep\" name=\"reStep\" value=\"{$reStep}\" /><input type=\"hidden\" id=\"reLevel\" name=\"reLevel\" value=\"{$reLevel}\" />";

/*
if($usehtml_level) {
	//글수정일 경우
	if($mode=="edit") echo "<script type=\"text/javascript\">\n//<![CDATA[\nvar dtype='{$docType}'; if(dtype=='editor'){document.getElementById('docType').options[3].selected=true;if(!bitUseEditorNow){wysiwyg_editor();bitUseEditorNow = true;}}else{btnctlsz(0);}\n//]]>\n</script>";

	//새글쓰기일 경우
	else {
		switch($editMode) { //관리에서 지정한 편집상태 설정
			case "html" :
				echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry {btnctlsz(1); document.getElementById('docType').options[1].selected=true;} catch(e) {}\n//]]>\n</script>";
				break;
			case "br" :
				echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry {btnctlsz(2); document.getElementById('docType').options[2].selected=true;} catch(e) {}\n//]]>\n</script>";
				break;
			case "editor" :
				echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry {btnctlsz(3); document.getElementById('docType').options[3].selected=true;} catch(e) {} if(!bitUseEditorNow){wysiwyg_editor();bitUseEditorNow = true;}\n//]]>\n</script>";
				break;
			default :
				echo "<script type=\"text/javascript\">\n//<![CDATA[\ntry {btnctlsz(0); document.getElementById('docType').options[0].selected=true;} catch(e) {}\n//]]>\n</script>";
				break;
		}
	}
}
*/
?>





<script type="text/javascript" src="<?=$FSBOARD_PATH?>/fckeditor/fckeditor.js"></script>
<script type="text/javascript">
//<![CDATA[
//////////FCKeditor Related Script
//document.write('<' + 'scr' + 'ipt type="text/javascript" src="<?=$FSBOARD_PATH?>/fckeditor/fckeditor' + '.js"><' + '/' + 'scr'+'ipt>');
function Toggle() {
	// Try to get the FCKeditor instance, if available.
	var oEditor ;
	if ( typeof( FCKeditorAPI ) != 'undefined' )
		oEditor = FCKeditorAPI.GetInstance( '_DataFCKeditor' ) ;

	// Get the _Textarea and _FCKeditor DIVs.
	var eTextareaDiv	= document.getElementById( '_Textarea' ) ;
	var eFCKeditorDiv	= document.getElementById( '_FCKeditor' ) ;

	// If the _Textarea DIV is visible, switch to FCKeditor.
	if ( eTextareaDiv.style.display != 'none' ) {
		// If it is the first time, create the editor.
		if ( !oEditor ) {
			CreateEditor() ;
		}
		else {
			// Set the current text in the textarea to the editor.
			oEditor.SetHTML( document.getElementById('_DataTextarea').value ) ;
		}

		// Switch the DIVs display.
		eTextareaDiv.style.display = 'none' ;
		eFCKeditorDiv.style.display = '' ;

		// This is a hack for Gecko 1.0.x ... it stops editing when the editor is hidden.
		if ( oEditor && !document.all ) {
			if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
				oEditor.MakeEditable() ;
		}

		// Switch the TEXTAREAs disablable.
		document.getElementById('_DataFCKeditor').disabled = false;
		document.getElementById('_DataTextarea').disabled = true;
	}
	else {
		// Set the textarea value to the editor value.
		document.getElementById('_DataTextarea').value = oEditor.GetXHTML() ;

		// Switch the DIVs display.
		eTextareaDiv.style.display = '' ;
		eFCKeditorDiv.style.display = 'none' ;

		// Switch the TEXTAREAs disablable.
		document.getElementById('_DataFCKeditor').disabled = true;
		document.getElementById('_DataTextarea').disabled = false;
	}
}

function CreateEditor() {
	// Copy the value of the current textarea, to the textarea that will be used by the editor.
	document.getElementById('_DataFCKeditor').value = document.getElementById('_DataTextarea').value ;

	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	var sBasePath = document.location.pathname.substring(0,document.location.pathname.lastIndexOf('_samples')) ;
	sBasePath = "<?=$FSBOARD_PATH?>/fckeditor/"

	// Create an instance of FCKeditor (using the target textarea as the name).
	var oFCKeditor = new FCKeditor( '_DataFCKeditor' ) ;
	oFCKeditor.BasePath = sBasePath ;
	oFCKeditor.Width = '100%' ;
	oFCKeditor.Height = document.getElementById( '_DataTextarea' ).rows * 15 + 50 ;
	oFCKeditor.ToolbarSet = "Basic3"
	oFCKeditor.ReplaceTextarea() ;
}

// The FCKeditor_OnComplete function is a special function called everytime an
// editor instance is completely loaded and available for API interactions.
function FCKeditor_OnComplete( editorInstance ) {
	// Enable the switch button. It is disabled at startup, waiting the editor to be loaded.
	//document.getElementById('_BtnSwitchTextarea').disabled = false ;
}

function PrepareSave() {
	// If the textarea isn't visible update the content from the editor.
	if ( document.getElementById( '_Textarea' ).style.display == 'none' ) {
		var oEditor = FCKeditorAPI.GetInstance( '_DataFCKeditor' ) ;
		document.getElementById( '_DataTextarea' ).value = oEditor.GetXHTML() ;
	}
}
//////////End of FCKeditor related


function chg_doctype(obj) {
	var pnlBtnCtlSz = document.getElementById('pnlBtnCtlSz');

	if(obj.options[3].selected===true) {
		pnlBtnCtlSz.style.display = 'none';
		Toggle();
	}
	else {
		pnlBtnCtlSz.style.display = 'inline';
		if(document.getElementById( '_FCKeditor' ).style.display != 'none') {
			Toggle();
		}
	}
}


function uploading(e) {
	//var frm = document.forms["__ctl"];
	var frm = document.getElementById("__ctl");
	var chk = chkField(frm);

	if(chk) {
		document.getElementById("btns").style.visibility = "hidden";

		for(var i=1; i<frm.length; i++) {
			if(frm[i].options)
				frm[i].style.visibility = "hidden";
		}

		blindLayer = document.getElementById("blind_form");
		msgLayer = document.getElementById("loadingmsg");
		document.body.scrollTop = 0;

		blindLayer.style.width = document.body.clientWidth * 2 + "px";
		blindLayer.style.height = document.body.clientHeight * 2 + "px";
		blindLayer.style.visibility = "visible";

		if(document.all) {
			msgLayer.style.posLeft = (document.body.clientWidth-300)/2;
			msgLayer.style.posTop = (document.body.clientHeight-300)/2;
		}
		else {
			msgLayer.style.left = Math.abs((document.body.clientWidth-300)/2) + "px";
			msgLayer.style.top = Math.abs((document.body.clientHeight-300)/2) + "px";
		}
		msgLayer.style.visibility = "visible";

		<?echo "sendit('{$id}','".($mode=="edit"?"editsave":"writesave")."'".($mode=="edit"||$mode=="reply"?",{$idx},{$page},'{$srhctgr}','".str_replace("'","&#39;",$srhstr)."','{$rowctgr}','{$rowmode}','".str_replace("'","&#39;",$ctgrstr)."'":"").");";?>

	} else
		return;
}

function chkField(frm) {
<?if(!$MemId&&!$isAdmin):?>
<?if($mode!="edit") {?>
	if(!frm.passwd.value) {
		alert('암호를 입력하세요');
		frm.passwd.focus();
		return false;
	}
<?}?>
	if(!frm.author.value) {
		alert('이름을 입력하세요');
		frm.author.focus();
		return false;
	}
	if(frm.e_mail.value) {
		if(!chkEmail(frm.e_mail.value)) {
			alert("이메일 주소를 정확히 입력하세요.");
			frm.e_mail.focus();
			return false;
		}
	}
<?endif;?>
	if(!frm.other01.value) {
		alert('연락처를 입력하세요');
		frm.other01.focus();
		return false;
	}

	if(!frm.other02.value) {
		alert('연락처를 입력하세요');
		frm.other02.focus();
		return false;
	}

	if(!frm.other03.value) {
		alert('연락처를 입력하세요');
		frm.other03.focus();
		return false;
	}

	if(!frm.other04.value){
		alert('예약일자를 입력하세요');
		frm.other04.focus();
		return false;
	}else{
		var reserve_date = frm.other04.value.split('-'); 

		var today = new Date();
		var chk1 = new Date(today.getYear(),today.getMonth() + 1,today.getDate());
		var chk2 = new Date(reserve_date[0],reserve_date[1],reserve_date[2]); 

		b = Math.floor((chk2.getTime() - chk1.getTime()));
		
		if(b <= 0) {
			alert('익일 예약은 전화로만 가능합니다.');
			frm.other04.focus();
			return false;
		}
	}

	if(!frm.subject.value) {
		alert('제목을 입력하세요');
		frm.subject.focus();
		return false;
	}

	if(document.getElementById('_Textarea').style.display == 'none') {
		var oEditor;
		if(typeof(FCKeditorAPI)!='undefined') { oEditor = FCKeditorAPI.GetInstance('_DataFCKeditor'); }
		if(!oEditor.GetXHTML()) {
			alert('내용을 입력해 주세요.');
			return false;
		}
	}
	else {
		if(!document.getElementById('_DataTextarea').value) {
			alert('내용을 입력해 주세요.');
			return false;
		}
	}

	return true;
} 

function chkEmail(strObj) {
	var email = strObj.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi); 
	if(!email)
		return false;
	return true;
}

function chkUrl(strObj) {
	var homepage = strObj.match(/([\w]+)/gi); //첫글자는 영문자나 숫자
	if(!homepage)
		return false;
	return true;
}

function vf(seq,sw) {
	i = seq;
	try {
		if(sw) {
			if(i<=<?=$fileMaxNum?>) {
				document.getElementById(eval("'file" + i + "'")).style.display = "inline";
				i++;
				setTimeout(eval("'vf(" + i + "," + sw + ")'"),10);
			}
			document.getElementById("lblFileBtn").innerHTML = "<a href=\"javascript:vf(<?=$fileMaxNum?>,0);\"><img src=\"<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif\" alt=\"↑\" style=\"vertical-align:middle;\" />숨김</a>";
		}
		else {
			if(i>1) {
				document.getElementById(eval("'file" + i + "'")).style.display = "none";
				i--;
				setTimeout(eval("'vf(" + i + "," + sw + ")'"),10);
			}
			document.getElementById("lblFileBtn").innerHTML = "<a href=\"javascript:vf(1,1);\"><img src=\"<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif\" alt=\"↓\" style=\"vertical-align:middle;\" />추가</a>";
		}
	} catch(e) { window.alert("에러 : " + e.number + "\n" + e.description); }
}

function chkfile() {
	return;//ie7에서 로컬파일 접근이 막힌 관계로 그냥 리턴할수밖에 없음.. 제길!

	try {
		var obj = event.srcElement;
		var fname = obj.value;
		var pimg = obj.parentElement.children[0];

		if((/(.jpg|.jpeg|.jpe|.gif|.bmp|.png)$/i).test(fname)) {
			pimg.style.display = "inline";
			pimg.width = 22;
			pimg.height = 18;
			pimg.align = "absmiddle";
			pimg.src = fname;
			pimg.alt = "미리보기";
		}
		else {
			pimg.style.display = "none";
		}
	} catch(e) { window.alert("에러 : " + e.number + "\n" + e.description); }
}

function btnctlsz(n) {
	try {
		var b = document.getElementById("pnlBtnCtlSz");
		b.style.display = n!=3 ? "inline" : "none";
	} catch(e) {}
}


function initwf() {
	var pnlBtnCtlSz = document.getElementById('pnlBtnCtlSz');
	var dt = document.getElementById('docType');

	<?if($mode=="write" || $mode=="reply") {?>
	var dtmode = '<?=$editMode?>';

	for(var i=0; i<dt.options.length; i++) {
		if(dt.options[i].value==dtmode) dt.options[i].selected = true;
	}
	<?}?>

	if(dt.options[3].selected == true) {
		pnlBtnCtlSz.style.display = 'none';
		Toggle();
	}
	else {
		<?if($editMode=="editor") {?>
		pnlBtnCtlSz.style.display = 'none';
		dt.options[3].selected = true;
		Toggle();
		<?}else{?>
		pnlBtnCtlSz.style.display = 'inline';
		<?}?>
	}
}

window.onLoad = initwf();
//]]>
</script>
