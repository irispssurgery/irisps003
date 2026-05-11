<?
/*************************************************************
	FSBOARD MainViewPage Skin Module
*************************************************************/


	if(!$view_included) exit; //view.php 직접 접근 막기

	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	$query = "SELECT * FROM ".$_table_id_board." WHERE idx={$idx}";
	$result = mysql_query($query) or Error(mysql_error());

	if($result) {
		if(!mysql_num_rows($result)) Error("데이터가 없거나 이미 삭제 되었습니다.");

		$rs = mysql_fetch_array($result);
		$idx = $rs["idx"]; //고유 번호
		$objProperty = $rs["objProperty"]; //게시물 속성
		$isSecret = $rs["isSecret"]; //비밀글 여부
		$isMember = $rs["isMember"]; //작성자 회원 여부
		$docType = $rs["docType"]; //게시물 문서 타입
		$author = $rs["author"]; //작성자
		$e_mail = $rs["e_mail"]; //이메일
		$homeUrl = $rs["homeUrl"]; //홈페이지 주소
		$subject = $rs["subject"]; //게시물 제목
		$passwd = $rs["passwd"]; //게시물 암호
		$category = $rs["category"]; //게시물 카테고리
		$tag_ls = $rs["tag_ls"]; //관련태그
		$regDate = $rs["regDate"]; //작성일자
		$editDate = $rs["editDate"]; //수정일자
		$memoLatestDate = $rs["memoLatestDate"]; //마지막 댓글올린 날짜
		$memoNum = $rs["memoNum"]; //댓글갯수
		$tbNum = $rs["tbNum"]; //트랙백갯수
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
		$siteLinkCount1 = $rs["siteLinkCount1"]; //사용자 링크주소 카운트1
		$siteLinkCount2 = $rs["siteLinkCount2"]; //사용자 링크주소 카운트2

		$isFile = false;
		$ii = 0;
		for($i=1; $i<=$fileMaxNum; $i++) {
			${"fileName".$i} = $rs["fileName".$i]; //첨부 파일명 n
			${"fileDownload".$i} = $rs["fileDownload".$i]; //첨부 파일다운로드 횟수 n
			if(${"fileName".$i}) { $isFile = true; $ii++; }
		}
		$attachedFileNum = $ii; //첨부된 파일수
		$maintainCode = md5(session_id());

		$contents = $rs["contents"]; //글내용

		mysql_free_result($result);

		//비밀글일 경우
		if($isSecret) {
			$view_permission = !empty($MemId) && !empty($isMember) && $MemId==$isMember ? true : false;

			if(!$secret_level && !$view_permission) { //비밀글보기권한이 없을 경우
				$auth_passwd = trim($_POST["auth_passwd"]);
				if(!$auth_passwd) {
					MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=view&idx={$idx}&page={$page}"); //인증페이지 이동
				}
				else {
					if(!$MemId) $auth_passwd = md5($auth_passwd);
				}

				if($auth_passwd==$passwd || $auth_passwd==$adminPasswd) $view_permission = true;
				else {
					$auth_passwd = md5($auth_passwd);
					if($auth_passwd==$passwd || $auth_passwd==$adminPasswd) {
						$view_permission = true;
					}
					else {
						//Error("암호가 일치하지 않습니다.");
						MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("암호가 일치하지 않습니다."));
					}
				}
			}
			else $view_permission = true;
		}
		else $view_permission = true;

		if(!$view_permission) {
			//Error("글의 내용을 볼수 있는 권한이 없습니다.");
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("글의 내용을 볼수 있는 권한이 없습니다."));
		}

		//작성자 표시
		$author = MemberName($isMember,$author,30,$e_mail,$homeUrl);

		$subject = CutStr($subject,255);
		$subject = StripHtmlChars($subject);
		$reg_date = date("Y-m-d H:i:s",$regDate);
		if($editDate) $edit_date = date("Y-m-d H:i:s",$editDate);


		//시스템 정보
		$usrAgentReg = usr_agent($usrAgentReg,1).", ".usr_agent($usrAgentReg,2);
		$usrAgentEdit = usr_agent($usrAgentEdit,1).", ".usr_agent($usrAgentEdit,2);


		//불량단어 감추기
		if($useWordFilter&&$badWords) {
			$badWords = str_replace(",","|",$badWords); //"([\_\-\./~@?=%&! ]+)"
			$contents = ereg_replace($badWords, "＊＊", $contents);
		}

		if($docType=="text") { //텍스트 타입일 경우
			$contents = htmlspecialchars($contents);
			$contents = str_replace("&amp;#","&#",$contents); //euc-kr 완성형에 없는 한글때문에.. 젠장
			$contents = nl2br($contents);

			//검색일 경우 검색어를 강조
			if(substr($srhctgr,3,1)&&$srhstr) {
				$srhstring = explode(" ",$srhstr);
				if($srhstring) {
					for($j=0,$till=sizeof($srhstring); $j<$till; $j++)
						$contents = str_replace($srhstring[$j],"<b style=\"background-color:yellow;\">".$srhstring[$j]."</b>",$contents);
				}
				else $contents = str_replace($srhstr,"<b style=\"background-color:yellow;\">{$srhstr}</b>",$contents);
			}

			if($useAutoLink) $contents = AutoLink($contents);
			$contents = str_replace("  ","&nbsp; ",$contents);
			$contents = str_replace("	","&nbsp; &nbsp; ", $contents);
		}
		else {
			//허용HTML태그가 있을 경우
			if($useHtml=="part"&&$allowTags) {
				$allowtag = explode(",",$allowTags);
				$contents = str_replace("<","&lt;",$contents); //우선 HTML태그를 모두 벗김
				for($i=0,$till=count($allowtag); $i<$till; $i++) {
					//허용한 HTML태그만 원상복귀
					$contents = eregi_replace("&lt;".$allowtag[$i],"<".$allowtag[$i],$contents);
					$contents = eregi_replace("&lt;/".$allowtag[$i].">","</".$allowtag[$i].">",$contents);
				}
			}

			//HTML태그 금지일 경우
			if($useHtml=="block") {
				$contents = StripHtmlChars($contents);
				$contents = str_replace("&amp;#","&#",$contents);
				$contents = nl2br($contents);
			}

			//HTML완전허용이 아닐경우 스크립트 이벤트 실행 방지
			if($useHtml!="perfect") {
				$contents = eregi_replace("javascript\:|vbscript\:|view\-source\:| onload| onunload| onabort| onerror| onclick| ondbl| onmouse| onkey| onfocus| onblur| onresize| onscroll| onchange| onselect"," //",$contents);
			}

			if($docType=="br") { //HTML+<br /> 타입일 경우
				$contents = nl2br($contents);
			}
		}

		//작성자 IP주소 보이기
		if($useViewClientIp) {
			$ip_reg = $isAdmin ? PrvtIp($ipReg,"","") : PrvtIp($ipReg,$isMember,$MemId);
			if($editDate) $ip_edit = $isAdmin ? PrvtIp($ipEdit,"","") : PrvtIp($ipEdit,$isMember,$MemId);
		}
		else {
			$ip_reg = "";
			$ip_edit = "";
		}

		//이전글,다음글
		$next_idx=0; $nsubject="";
		$prev_idx=0; $psubject="";
		if(!$srhctgr&&!$srhstr) {
			$query = "SELECT max(idx),min(idx) FROM ".$_table_id_board."";
			$tmp_result = mysql_query($query) or Error(mysql_error());
			$rs = mysql_fetch_row($tmp_result);
			$last_idx = (int)$rs[0];
			$first_idx = (int)$rs[1];
			mysql_free_result($tmp_result);

			//다음글
			if($idx<$last_idx) {
				for($i=$idx; $i<=$last_idx; $i++) {
					$query = "SELECT idx,subject FROM ".$_table_id_board." WHERE idx=".($i+1);
					$tmp_result = mysql_query($query) or Error(mysql_error());
					if($tmp_result) {
						$rs = mysql_fetch_row($tmp_result);
						if($rs[0]) {
							$next_idx = $rs[0];
							$nsubject = $rs[1];
							$nsubject = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx={$next_idx}&amp;page={$page}\" class=\"lnk_view\">{$nsubject}</a>";
							break;
						}
						mysql_free_result($tmp_result);
					}
				}
			}

			//이전글
			if($idx>$first_idx) {
				for($i=$idx; $i>1; $i--) {
					$query = "SELECT idx,subject FROM ".$_table_id_board." WHERE idx=".($i-1);
					$tmp_result = mysql_query($query) or Error(mysql_error());
					if($tmp_result) {
						$rs = mysql_fetch_row($tmp_result);
						if($rs[0]) {
							$prev_idx = $rs[0];
							$psubject = $rs[1];
							$psubject = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx={$prev_idx}&amp;page={$page}\" class=\"lnk_view\">{$psubject}</a>";
							break;
						}
						mysql_free_result($tmp_result);
					}
				}
			}
		}

		//조회수 처리
		if(($ipReg?$ipReg:$ipEdit)!=$_SERVER["REMOTE_ADDR"]) { //작성글과 아이피가 다르면
			if(!$_SESSION["read"]) { //조회세션이 비어있으면
				$_SESSION["read"] = "$id/$idx"; //세션생성
				//조회수 증가
				mysql_query("UPDATE ".$_table_id_board." SET readNum=readNum+1 WHERE idx={$idx}") or Error(mysql_error());
			}
			else { //조회세션이 있으면
				if(!ereg("$id/$idx",$_SESSION["read"])) { //조회기록이 없으면
					$_SESSION["read"] .= "_$id/$idx"; //조회기록 추가
					//조회수 증가
					mysql_query("UPDATE ".$_table_id_board." SET readNum=readNum+1 WHERE idx={$idx}") or Error(mysql_error());
				}
			}
		}
	}


	//게시물 주소 만들기
	if(!$combinedDesign && IsRewrite()) {
		$article_srl = "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/".$id."/".$idx;
	}
	else {
		$article_srl = str_replace("index.php","","http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=".$id."&amp;idx=".$idx);
	}


	//트랙백 주소 만들기
	$tb_url = IsRewrite() ? "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/tb/".$id."/".$idx : "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/?id=".$id."&amp;mode=trackback&amp;idx=".$idx;

	//관련태그 링크 만들기
	$usrtag = explode(",",$tag_ls);
	$tag_list = "";
	for($i=0,$till=sizeof($usrtag);$i<$till;$i++) {
		$tag = trim($usrtag[$i]);
		$tag_list .= "<a href=\"{$PHP_SELF}?id={$id}&amp;mode=list&amp;srhctgr=1000&amp;srhstr=".urlencode($tag)."\" class=\"lnk_memo\" title=\"관련태그 검색\">".$tag."</a>";
		if($i<$till-1) $tag_list .= ", ";
	}


	/////버튼설정
	$btnPath = $FSBOARD_PATH."/img/btn"; //버튼 경로

	if(!$isMember || $isMember==$MemId || $isAdmin || !$useHideButtons) { //비회원,해당회원,관리자일 경우에만 수성/삭제 버튼 보임
		$btnEdit = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("edit",1)."&amp;idx={$idx}&amp;page={$page}\"><img src=\"$btnPath/modify.gif\" alt=\"수정\" /></a>";
		$btnRemove = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("remove",1)."&amp;idx={$idx}&amp;page={$page}\"><img src=\"$btnPath/delete.gif\" alt=\"삭제\" /></a>";
	}
	$btnList = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("list",1)."&amp;page={$page}\"><img src=\"{$btnPath}/list.gif\" alt=\"리스트\" /></a>";

	if($write_level || !$useHideButtons) $btnWrite = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("write",1)."&amp;page={$page}\"><img src=\"{$btnPath}/write.gif\" alt=\"글쓰기\" /></a>";
	if($useReply&&$reply_level || !$useHideButtons) $btnReply = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("reply",1)."&amp;idx={$idx}&amp;ref={$ref}&amp;reStep={$reStep}&amp;reLevel={$reLevel}&amp;page={$page}\"><img src=\"{$btnPath}/reply.gif\" alt=\"답변\" /></a>";

	if($prev_idx) $btnPrev = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx={$prev_idx}&amp;page={$page}\"><img src=\"{$btnPath}/prev.gif\" alt=\"이전\" /></a>";
	if($next_idx) $btnNext = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx={$next_idx}&amp;page={$page}\"><img src=\"{$btnPath}/next.gif\" alt=\"다음\" /></a>";






////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
ContentTop();
?>








<div id="ViewFrame">
	<div id="ViewLayout">
		<div class="ViewRow">
			<div class="ViewLabel">작성자</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub">
				<?=$author;?>
				<p>Line:<?=count(explode("\n",$contents));?>, Type:<?=$docType?>, Read:<?=NumColor($readNum)?></p>
			</div>
		</div>
		<div class="ViewRow">
			<div class="ViewLabel">작성일</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><?echo $reg_date.($useViewClientIp?" from {$ip_reg}":""); if($useViewClientInfo) { echo " ({$usrAgentReg})"; }?></div>
		</div>
<?if($editDate):?>
		<div class="ViewRow">
			<div class="ViewLabel">수정일</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><?echo $edit_date.($useViewClientIp?" from {$ip_edit}":""); if($useViewClientInfo) { echo " ({$usrAgentEdit})"; }?></div>
		</div>
<?endif;?>
<?if($siteLink1&&$siteLink1!="http://"):?>
		<div class="ViewRow">
			<div class="ViewLabel">Link 1</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><a href="javascript:void(0);" onclick="window.open('<?=$_SERVER["PHP_SELF"]?>?id=<?=$id?>&amp;mode=sitelink&amp;idx=<?=$idx?>&amp;lnknum=1');" class="lnksl_view"><?=$siteLink1?></a> <span style="font-size:11px;" title="SiteLink1 클릭횟수">(<?=intval($siteLinkCount1)?>)</span></div>
		</div>
<?endif;?>
<?if($siteLink2&&$siteLink2!="http://"):?>
		<div class="ViewRow">
			<div class="ViewLabel">Link 2</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><a href="javascript:void(0);" onclick="window.open('<?=$_SERVER["PHP_SELF"]?>?id=<?=$id?>&amp;mode=sitelink&amp;idx=<?=$idx?>&amp;lnknum=2');" class="lnksl_view"><?=$siteLink2?></a> <span style="font-size:11px;" title="SiteLink2 클릭횟수">(<?=intval($siteLinkCount2)?>)</span></div>
		</div>
<?endif;?>
		<div class="ViewRow">
			<div class="ViewLabel">제 &nbsp; 목</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><?=($category?"<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;ctgrstr=".urlencode($category)."&amp;page=1\" title=\"카테고리 검색\">[{$category}]</a> ":"")?><b><?=$subject?></b></div>
		</div>
		<div id="ViewPostUrl"><a href="javascript:void(0);" onclick="clipboardcopy('<?=$article_srl?>',1);" class="tb_lnk" title="게시물 주소복사"><?=$article_srl?></a></div>

		<div id="ViewContent">
			<div id="ViewContent_FixVertical"></div>
			<div id="ViewContent_Main">
<?
if($useExecFile) {
	for($i=1; $i<=$fileMaxNum; $i++) {
		echo ExecFile(${"fileName".$i},"{$FSDATA_PATH}/{$id}",$i,$imgRszWidth,$allowEmbedFileExts);
	}
}
echo "\n<script type=\"text/javascript\">\n//<![CDATA[\n</script>\n".$contents."\n<script type=\"text/javascript\">\n//-->\n//]]>\n</script>\n";
?>

			</div>
			<div id="ViewContent_FixTail"></div>
		</div>

		<br />

<!-- Start 태그 / 첨부파일 / 트랙백 / 코멘트 -->
		<div id="ViewCommentLayout">
<?
if($tag_ls) {
	echo "
			<!-- 태그 리스트 -->
			<div id=\"ViewComment_Tag\">
				<img src=\"{$FSBOARD_PATH}/img/clip/tag.gif\" alt=\"태그\" style=\"vertical-align:middle;\" /> {$tag_list}
			</div>
	";
}

if($memoNum || $tbNum || $isFile) {
	echo "
			<div id=\"ViewComment_Title\">
	";
	if($memoNum > 0) {
		echo "
					<a href=\"javascript:ctlPnl('PANEL_COMMENTGROUP');\" class=\"lnk_memo\" title=\"댓글 보기/숨기기\">코멘트 (<b>{$memoNum}</b>)</a>
		";
		if($tbNum>0 || $isFile) {
			echo "
					&nbsp;<span style=\"color:silver;\">|</span>&nbsp;
			";
		}
	}
	if($tbNum > 0) {
		echo "
					<a href=\"javascript:ctlPnl('PANEL_TRACKBACKGROUP');\" class=\"lnk_memo\" title=\"엮인글 보기/숨기기\">트랙백 (<b>{$tbNum}</b>)</a>
		";
	}
	if($isFile) {
		if($tbNum > 0) {
			echo "
					&nbsp;<span style=\"color:silver;\">|</span>&nbsp;
			";
		}
		echo "
					<a href=\"javascript:ctlPnl('PANEL_FILEGROUP');\" class=\"lnk_memo\" title=\"첨부파일 보기/숨기기\">첨부파일 (<b>{$attachedFileNum}</b>)</a>
		";
	}
	echo "
			</div>
	";
}

/////첨부파일
if($isFile) {
	echo "
			<!-- 첨부파일 -->
			<div id=\"PANEL_FILEGROUP\">
				<div id=\"ViewComment_File\">
					<div id=\"PANEL_ATTACHFILE\">
	";
	for($i=1; $i<=$fileMaxNum; $i++) {
		if(${"fileName".$i}) {
			if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".$i})) {
				$fileSize = filesize("{$FSDATA_ROOT}/{$id}/".${"fileName".$i});
				$fileSize = GetFileSize($fileSize);

				$attachFile = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=download&amp;idx={$idx}&amp;maintainCode={$maintainCode}&amp;filenum={$i}&amp;filename=".urlencode(${"fileName".$i})."\" class=\"lnkfile_view\" title=\"".${"fileName".$i}."\"><b>".${"fileName".$i}."</b>";
				echo "
							<div class=\"AttachedFile\">".FileTypeIcon(${"fileName".$i})." {$attachFile} ({$fileSize}{$fileUnit}), Download:".${"fileDownload".$i}."</a></div>
				";
				//if($i%2==0 && $i<$fileMaxNum) { echo "<div style=\"clear:both;\"></div>"; }
			}
		}
	}
	echo "
						<div style=\"clear:both;\"></div>
					</div>
				</div>
			</div>
	";
}


if($useTrackback || $tbNum>0) {
	echo "
			<!-- 트랙백 -->
			<div id=\"PANEL_TRACKBACKGROUP\">
				<div id=\"ViewComment_Trackback\">
	";
}
/////트랙백
if($useTrackback) {
	echo "

					<div id=\"TrackbackUrl\">트랙백 주소 : <a href=\"javascript:void(0);\" onclick=\"clipboardcopy('{$tb_url}',0);\" class=\"tb_lnk\" title=\"트랙백 주소복사\">{$tb_url}</a></div>
			";
}
if($tbNum>0) {
	$query = "SELECT * FROM ".$_table_id_trackback." WHERE boardId='{$id}' AND objNum={$idx} ORDER BY tidx ASC;";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$i = 0;
		while($rs=mysql_fetch_array($result)) {
			$tidx = $rs["tidx"];
			$tb_url = $rs["tb_url"];
			$tb_title = $rs["tb_title"];
			$tb_blog_name = $rs["tb_blog_name"];
			$tb_excerpt = $rs["tb_excerpt"];
			$tb_regdate = $rs["tb_regdate"];

			$tb_title = htmlspecialchars(CutStr($tb_title,100));
			$tb_excerpt = StripHtmlChars(CutStr(trim(strip_tags($tb_excerpt)),300,"...."));
			$tb_excerpt = str_replace("&nbsp;","",$tb_excerpt);
			$tb_excerpt = str_replace("\r\n\r\n\r\n","\r\n",$tb_excerpt);
			$tb_excerpt = str_replace("\r\n\r\n","\r\n",$tb_excerpt);
			$tb_excerpt = str_replace("\n\n\n","\n",$tb_excerpt);
			$tb_excerpt = nl2br($tb_excerpt);
			$tb_reg_date = date("Y-m-d H:i:s",$tb_regdate);
			$iconNew = ((mktime() - $tb_regdate) <= $nterm) ? "<img src=\"{$FSBOARD_PATH}/img/clip/new_sr.gif\" alt=\"NEW\" />" : "";

			if($isAdmin || !$useHideButtons) {
				//관리자일경우에만 삭제 버튼 보임
				$btnRmTb = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("removeTrackback",1)."&amp;idx={$idx}&amp;tidx={$tidx}\"><img src=\"{$FSBOARD_PATH}/img/clip/xbutton_small.gif\" alt=\"삭제\" /></a>";
			}
			else $btnRmTb = "";

			echo "
					<div style=\"".(!$useTrackback&&$i==0?"":"border-top:1px dotted #ccc;")."\">
						<div class=\"Trackback_Blogname\"><a href=\"{$tb_url}\" onclick=\"window.open(this.href,'_blank'); return false;\" class=\"lnksl_view\">{$tb_blog_name}</a></div>
						<div class=\"Trackback_Contents\">
							<div class=\"PANEL_TRACKBACKCONTENTS\">
								<div class=\"Trackback_Title\"><a href=\"{$tb_url}\" onclick=\"window.open(this.href,'_blank'); return false;\" class=\"lnksl_view\"><b>{$tb_title}</b></a> {$iconNew}</div>
								<div class=\"Trackback_Excerpt\">{$tb_excerpt} <span class=\"tb_r\">{$tb_reg_date}</span> {$btnRmTb}</div>
							</div>
						</div>
						<div style=\"clear:both;\"></div>
					</div>
			";
			$i++;
		}
	}
}
if($useTrackback || $tbNum>0) {
	echo "
				</div>
			</div>
	";
}

if($memowrite_level || $memoNum>0) {
	echo "
			<!-- 코멘트 -->
			<div id=\"PANEL_COMMENTGROUP\">
				<div id=\"ViewComment_Comment\">
	";
}

/////코멘트
$btnSaveMemo = "<a href=\"javascript:postMemo(document.getElementById('__ctl'),'btnSaveMemo');\" class=\"lnk_memo\">확인</a>";

if($memoNum>0) {
	$query = "SELECT * FROM ".$_table_id_comment." WHERE boardId='{$id}' AND objNum={$idx} ORDER BY cidx ASC;";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$i = 0;
		while($rs=mysql_fetch_array($result)) {
			$cidx = $rs["cidx"];
			$isMember_cmt = $rs["isMember"];
			$name = $rs["name"];
			$e_mail = $rs["e_mail"];
			$regDate = $rs["regDate"];
			$comments = $rs["comments"];
			$origin_name = $name;
			$origin_comments = $comments;

			//작성자 표시
			$name = MemberName($isMember_cmt,$name,$authorLimit,$e_mail,"");

			$reg_date = date("Y-m-d h:i",$regDate);

			if($useWordFilter&&$badWords) $comments = eregi_replace($badWords,"＊＊",$comments);
			$comments = StripHtmlChars($comments);
			$comments = str_replace("  ","&nbsp; ",$comments);
			$comments = str_replace("	","&nbsp; &nbsp; ",$comments);
			if(substr($srhctgr,3,1)&&$srhstr) {
				if($srhstring) {
					for($j=0,$till=sizeof($srhstring); $j<$till; $j++)
						$comments = str_replace($srhstring[$j],"<b style=\"background-color:yellow;\">".$srhstring[$j]."</b>",$comments);
				} else $comments = str_replace($srhstr,"<b style=\"background-color:yellow;\">{$srhstr}</b>",$comments);
			}
			if($useAutoLink) $comments = AutoLink($comments);

			$iconNew = ((mktime() - $regDate) <= $nterm) ? "<img src=\"{$FSBOARD_PATH}/img/clip/new_sr.gif\" alt=\"NEW\" />" : "";
			$lines = count(explode("\n",$origin_comments)); //내용 라인수
			$lines = $lines<3 ? 6 : $lines + 3;

			//버튼설정
			$btnEditSaveMemo = "<a href=\"javascript:sendit('{$id}','editMemo&amp;cidx={$cidx}',{$idx},{$page},'{$srhctgr}','".str_replace("'","&#39;",$srhstr)."','{$rowctgr}','$rowmode','".str_replace("'","&#39;",$ctgrstr)."');\" class=\"lnk_memo\">확인</a>";
			if(!$isMember_cmt || $isMember_cmt==$MemId || $isAdmin || !$useHideButtons) {
				//비회원,해당회원,관리자일경우에만 수정/삭제 버튼 보임
				$btnEditMemo = "<a href=\"javascript:ModifyMemo({$i},'edit');\"><img src=\"{$FSBOARD_PATH}/img/clip/doc1.gif\" alt=\"수정\" /></a>";
				$btnEditMemoCancel = "<a href=\"javascript:ModifyMemo({$i},'cancel')\" class=\"lnk_memo\">취소</a>";
				$btnRemoveMemo = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("removeMemo",1)."&amp;idx={$idx}&amp;cidx={$cidx}\"><img src=\"{$FSBOARD_PATH}/img/clip/xbutton_small.gif\" alt=\"삭제\" /></a>";
			}
			else {
				$btnEditMemo = "";
				$btnEditMemoCancel = "";
				$btnRemoveMemo = "";
			}
			if($memowrite_level || !$useHideButtons) {
//				$btnReplyMemo = "<a ><img src=\"{$FSBOARD_PATH}/img/clip/doc5.gif\" alt=\"답글\" /></a>";
			}
			else {
				$btnReplyMemo = "";
			}

			echo "
					<div id=\"memoCmt_{$i}\" style=\"clear:both; margin:5px; display:block;".($i==0?"":"border-top:1px dotted #ccc;")."\">
						<div class=\"Comment_Main\">
							<div class=\"Comment_Content\">
								<div class=\"Comment_Author\">{$name}</div>
								<div class=\"Comment_Excerpt\">".nl2br($comments)." {$iconNew} <span class=\"memo_d\">{$reg_date} {$btnReplyMemo} {$btnEditMemo} {$btnRemoveMemo}</span></div>
							</div>
						</div>
					</div>

					<!-- 코멘트 수정 -->
					<div id=\"editCmt_{$i}\" style=\"display:none;\">
						<div class=\"Comment_EditForm\">
							<div class=\"Comment_EditFormTopLayout\">
								<div class=\"Comment_EditFormTop\">
									<a href=\"javascript:formResize(document.getElementById('comments_{$cidx}'),2);\"><img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_down.gif\" alt=\"글쓰기 영역 늘리기\" style=\"vertical-align:middle;\" /></a> &nbsp;
			";
			if(($MemId&&$MemId==$isMember_cmt)||$isAdmin) {
				echo "
									<input type=\"hidden\" id=\"name_{$cidx}\"  name=\"name_{$cidx}\" value=\"".str_replace("\"","&#34;",$origin_name)."\" />
									<input type=\"hidden\" id=\"e_mail_{$cidx}\" name=\"e_mail_{$cidx}\" value=\"".str_replace("\"","&#34;",$e_mail)."\" />
				";
			} else {
				echo "
									암호<input type=\"password\" size=\"12\" id=\"passwd_{$cidx}\" name=\"passwd_{$cidx}\" class=\"memo_txtbox1\" />
									이름<input type=\"text\" size=\"12\" id=\"name_{$cidx}\" name=\"name_{$cidx}\" value=\"".str_replace("\"","&#34;",$origin_name)."\" class=\"memo_txtbox1\" />
									메일<input type=\"text\" size=\"12\" id=\"e_mail_{$cidx}\" name=\"e_mail_{$cidx}\" value=\"".str_replace("\"","&#34;",$e_mail)."\" class=\"memo_txtbox1\" />
				";
			}
			echo "
								</div>
								<div class=\"Comment_EditFormBtnLayout\">
									<div class=\"Comment_EditFormBtn\">{$btnEditSaveMemo} {$btnEditMemoCancel}</div>
								</div>
							</div>
							<div class=\"Comment_EditFormMain\">
								<div class=\"Comment_EditFormTextbox\"><textarea rows=\"{$lines}\" cols=\"70\" id=\"comments_{$cidx}\" name=\"comments_{$cidx}\" id=\"comments_{$cidx}\" style=\"width:99%;overflow:auto;\" class=\"memo_txtbox2\">".StripHtmlChars($origin_comments)."</textarea></div>
							</div>
						</div>
					</div>
			";
			$i++;
		}
		mysql_free_result($result);
	}
}

/////코멘트 입력 사용
if($useMemo && $memowrite_level) {
	echo "
					<!-- 코멘트 입력 -->
					<div id=\"Comment_InputForm\" style=\"".($memoNum?"border-top:1px solid #EEEEEE;":"")."\">
						<div id=\"Comment_InputFormTopLayout\">
							<div id=\"Comment_InputFormTop\">
								<a href=\"javascript:formResize(document.getElementById('comments'),2);\"><img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_down.gif\" alt=\"글쓰기 영역 늘리기\" /></a><a href=\"javascript:formResize(document.getElementById('comments'),-2);\"><img src=\"{$FSBOARD_PATH}/img/clip/dbl_arrow_up.gif\" alt=\"글쓰기 영역 줄이기\" /></a>&nbsp;
	";
	if($MemId) {
		echo "
								<input type=\"hidden\" id=\"name\" name=\"name\" value=\"".str_replace("\"","&#34;",$MemName)."\" />
								<input type=\"hidden\" id=\"e_mail\" name=\"e_mail\" value=\"".str_replace("\"","&#34;",$MemEmail)."\" />
		";
	} else {
		echo "
								암호<input type=\"password\" size=\"12\" id=\"passwd\" name=\"passwd\" class=\"memo_txtbox1\" />
								이름<input type=\"text\" size=\"12\" id=\"name\" name=\"name\" class=\"memo_txtbox1\" />
								메일<input type=\"text\" size=\"12\" id=\"e_mail\" name=\"e_mail\" class=\"memo_txtbox1\" />
		";
	}
	echo "
							</div>
							<div id=\"btnSaveMemo\" class=\"Comment_InputFormBtn\">{$btnSaveMemo}</div>
						</div>
						<div id=\"Comment_InputFormMain\">
							<div id=\"Comment_InputFormTextbox\"><textarea rows=\"7\" cols=\"60\" id=\"comments\" name=\"comments\" id=\"comments\" style=\"width:99%; overflow:auto;\" class=\"memo_txtbox2\"></textarea></div>
						</div>
					</div>
	";
}
if($memowrite_level || $memoNum>0) {
	echo "
				</div>
			</div>
	";
}
?>
		</div>
<!-- End of 태그 / 첨부파일 / 트랙백 / 코멘트 -->


<!-- Begin 이전글/다음글 -->
<?if($next_idx):?>
		<div class="ViewRow">
			<div class="ViewLabel">다음글</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><?=$nsubject?></div>
		</div>
<?endif;if($prev_idx):?>
		<div class="ViewRow">
			<div class="ViewLabel">이전글</div>
			<div class="ViewDivLine">|</div>
			<div class="ViewRSub"><?=$psubject?></div>
		</div>
<?endif;?>
<!-- End of 이전글/다음글 -->


<!-- Begin 버튼 -->
<div id="ViewBottomBtn">
	<div id="ViewBottomBtnLeft"><?echo "{$btnEdit} {$btnRemove} {$btnReply}";?></div>
	<div id="ViewBottomBtnRight"><?echo "{$btnPrev} {$btnNext} {$btnList} {$btnWrite}";?></div>
</div>
<!-- End of 버튼 -->

	</div>
</div>


<br />
<br />


<script type="text/javascript">
//<![CDATA[
var tempSeq = 0;
function ModifyMemo(seq,vmode) {
	var memocmt = document.getElementById("memoCmt_" + seq);
	var editcmt = document.getElementById("editCmt_" + seq);

	if(vmode=="edit") {
		if(document.getElementById("memoCmt_" + tempSeq) && document.getElementById("memoCmt_" + tempSeq).style.display=="none") document.getElementById("memoCmt_" + tempSeq).style.display = "block";
		if(document.getElementById("editCmt_" + tempSeq) && document.getElementById("editCmt_" + tempSeq).style.display!="none") document.getElementById("editCmt_" + tempSeq).style.display = "none";
		tempSeq = seq;

		memocmt.style.display = "none";
		editcmt.style.display = "block";
	}
	else {
		memocmt.style.display = "block";
		editcmt.style.display = "none";
	}
}

function chkField(frm) { //메모 필드 체크
	try {
		if(!frm.comments.value) {
			alert('메모 내용을 입력하세요');
			frm.comments.focus();
			return false;
		}
<?if(!$MemId) {?>
		if(!frm.passwd.value) {
			alert('패스워드를 입력하세요');
			frm.passwd.focus();
			return false;
		}
		if(!frm.name.value) {
			alert('이름을 입력하세요');
			frm.name.focus();
			return false;
		}
		if(frm.e_mail.value) {
			if(!chkEmail(frm.e_mail.value)) {
				alert("이메일 주소를 정확히 입력하세요.");
				frm.e_mail.focus();
				return false;
			}
		}
<?}?>
	} catch(e) { window.alert("에러(" + e.number + "): " + e.description); return; }

	return true;
} 

function chkEmail(strObj) { //이메일 패턴 체크
	var email = strObj.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi); 
	if(!email)
		return false;
	return true;
}

function postMemo(frm,btnId) { //메모글 전송
	var chk = chkField(frm);

	if(chk) {
		document.getElementById(btnId).style.visibility = "hidden";
		<?echo "sendit('{$id}','saveMemo',{$idx},{$page},'{$srhctgr}','".str_replace("'","&#39;",$srhstr)."','{$rowctgr}','$rowmode','".str_replace("'","&#39;",$ctgrstr)."');";?>

	}
	else return;
}

function clipboardcopy(str,k) {
	var t = k ? "게시물" : "트랙백";
	var wcds = null;
	var cfm = window.confirm(t + "주소를 클립보드에 복사하시겠습니까?");
	if(cfm) {
		if(document.all) {
			wcds = window.clipboardData.setData("Text",str);
			if(wcds) window.alert(t+" 주소가 클립보드에 복사되었습니다.");
			else window.alert("클립보드에 엑세스 할 수 있도록 허용 해야만 주소가 복사됩니다.");
		}
		else {
			window.alert("IE 이외의 브라우저에서는 클립보드에 자동복사를 할수 없습니다.\n\n주소를 직접 드래그로 선택해서 복사해주세요.");
		}
	}
	return;
}

function ctlPnl(n) {
	var obj = document.getElementById(n);
	if(obj) obj.style.display = obj.style.display=="none" ? "inline" : "none";
}
//]]>
</script>
