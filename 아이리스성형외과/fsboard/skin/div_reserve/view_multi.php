<?
/*************************************************************
	FSBOARD MultiViewPage Skin Module
*************************************************************/

	if(!$view_included) exit; //view_multi.php 직접 접근 막기

	if($categories) { $categoryElement = explode(",",$categories); $categoryElementCount = Array(); }

	$board_link = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=".$id;

	echo "
		<div style=\"clear:both; margin:0px; auto; font-size:13px; text-align:{$align};\">
			<div style=\"width:{$width}; text-align:center;\">
				<div style=\"position:relative; margin-bottom:20px; padding:1.4em 1.6em; background-color:#FFFFEE; text-align:left; border:1px solid #E1E1E1;\">
					<h3 style=\"margin-bottom:-0.9em;\">".$boardName."</h3>
					<p style=\"font-size:0.9em; text-indent:0.3em;\"><a href=\"".$board_link."\">".$board_link."</a></p>
					<ul style=\"position:absolute; right:1.6em; top:3.9em; margin:0 auto; list-style:none;\">
						<li style=\"float:left; margin-left:1.9em; font-size:0.9em;\"><a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list\">리스트</a></li>
						<li style=\"float:left; margin-left:1.9em; font-size:0.9em;\"><a href=\"".$_SERVER["PHP_SElF"]."?id={$id}&amp;mode=write\">글쓰기</a></li>
					</ul>
				</div>
				<div style=\"width:100%; float:left; margin-left:0.2em; margin-bottom:1em; font-size:1.1em; text-align:left;\">
	";

	while($rs = mysql_fetch_array($result)) {
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
		$siteLinkCount1 = $rs["siteLinkCount1"]; //사용자 링크주소 카운트1
		$siteLinkCount2 = $rs["siteLinkCount2"]; //사용자 링크주소 카운트2
		$isFile = false;
		for($i=1; $i<=$fileMaxNum; $i++) {
			${"fileName".$i} = $rs["fileName".$i]; //첨부 파일명 n
			${"fileDownload".$i} = $rs["fileDownload".$i]; //첨부 파일다운로드 횟수 n
			if(${"fileName".$i}) $isFile = true;
		}
		$contents = $rs["contents"]; //글내용

		//비밀글은 제외시킴
		if($isSecret) continue;

		//작성자 표시
		$author = CutStr($author,30);
		$author = StripHtmlChars($author);

		$subject = CutStr($subject,255);
		$subject = StripHtmlChars($subject);
		$reg_date = date("l, d F, Y h:i:sa",$regDate);
		if($editDate) $edit_date = date("l, d F, Y h:i:sa",$editDate);


		//불량단어 감추기
		if($useWordFilter&&$badWords) {
			$badWords = str_replace(",","|",$badWords); //"([\_\-\./~@?=%&! ]+)"
			$contents = eregi_replace($badWords,"**",$contents);
		}

		$contents = htmlspecialchars($contents);
		$contents = str_replace("&amp;#","&#",$contents); //euc-kr 완성형에 없는 한글때문에..
		$contents = nl2br($contents);
		if(substr($srhctgr,2,1)&&$srhstr) $contents = str_replace($srhstr,"<b style=\"background-color:yellow;\">$srhstr</b>",$contents);
		if($useAutoLink) $contents = AutoLink($contents);

		//작성자 IP주소 보이기
		if($useViewClientIp) {
			$ip_reg = $isAdmin ? PrvtIp($ipReg,"","") : PrvtIp($ipReg,$isMember,$MemId);
			if($editDate) $ip_edit = $isAdmin ? PrvtIp($ipReg,"","") : PrvtIp($ipEdit,$isMember,$MemId);
		}
		else {
			$ip_reg = "";
			$ip_edit = "";
		}

		if($categoryElement) {
			for($i=0,$till=count($categoryElement); $i<$till; $i++) {
				if($categoryElement[$i]==$category) $categoryElementCount[$i]++;
			}
		}

		/////버튼설정
		$btnPath = "$FSBOARD_PATH/img/btn"; //버튼 경로

		$btnView = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}\" class=\"lnk_view\">확인</a>&nbsp;";
		if(!$isMember || $isMember==$MemId || $isAdmin || !$useHideButtons) {
			$btnEdit = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("edit")."&idx={$idx}&page={$page}\" class=\"lnk_view\">수정</a>&nbsp;";
			$btnRemove = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("remove")."&idx={$idx}&page={$page}\" class=\"lnk_view\">삭제</a>&nbsp;";
		}
		$btnList = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}\" class=\"lnk_view\">목록</a>&nbsp;";
		if($useReply&&$reply_level || !$useHideButtons) $btnReply = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("reply")."&idx={$idx}&ref={$ref}&reStep={$reStep}&reLevel={$reLevel}&page={$page}\" class=\"lnk_view\">답변</a>&nbsp;";
		if($write_level || !$useHideButtons) $btnWrite = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("write")."&page={$page}\" class=\"lnk_view\">글쓰기</a>&nbsp;";

		?>
		<h4 style="margin-bottom:-1.1em; padding-top:0.3em; border-top:1px solid #E0E0E0;"><a href="<?=$_SERVER["PHP_SELF"]?>?id=<?=$id?>&idx=<?=$idx?>"><?=$subject?></a></h4>
		<p style="font-size:0.88em; color:#AAA; text-indent:0.5em">작성자:<?=$author?>, 작성일:<?=$reg_date?>, 조회:<?=NumColor($readNum)?> <a href="<?=$_SERVER["PHP_SELF"]?>?id=<?=$id?>&idx=<?=$idx?>"><img src="<?=$FSBOARD_PATH?>/img/clip/arrow2.gif" alt="내용보기" /></a></p>
		<div style="padding-left:1em;">
		<?
		if($useExecFile) {
			for($i=1; $i<=$fileMaxNum; $i++) {
				if(eregi("\.jpg|\.jepg|\.gif|\.bmp|\.png",${"fileName"}.$i))
				echo ExecFile(${"fileName".$i},"{$FSDATA_PATH}/{$id}",$i,$imgRszWidth,$allowEmbedFileExts);
			}
		}
		echo $contents;
		?>
		</div>

		<br />

		<?
		//첨부파일이 있을 경우
		if($isFile) {
			echo "<ul>";
			for($i=1; $i<=$fileMaxNum; $i++) {
				if(${"fileName".$i}) {
					if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".$i})) {
						$fileSize = filesize("{$FSDATA_ROOT}/{$id}/".${"fileName".$i});
						$fileSize = GetFileSize($fileSize);

						$attachFile = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&mode=download&idx={$idx}&filenum={$i}&filename=".urlencode(${"fileName".$i})."\" class=\"lnkfile_view\"><strong>".${"fileName".$i}."</strong>";
						echo "<li style=\"margin-left:-0.8em;\">{$attachFile} ({$fileSize}{$fileUnit}) download:".${"fileDownload".$i}."</a></li>";
					}
				}
			}
			echo "</ul>";
		}


		//코멘트가 있을 경우
		if($memoNum>0) {
			$query = "SELECT * FROM ".$_table_id_comment." WHERE boardId='{$id}' AND objNum={$idx};";
			$result_cmt = mysql_query($query) or Error(mysql_error());
			if($result_cmt) {
				$i = 0;
				while($rs=mysql_fetch_array($result_cmt)) {
					$cidx = $rs["cidx"];
					$isMember_cmt = $rs["isMember"];
					$name = $rs["name"];
					$e_mail = $rs["e_mail"];
					$regDate = $rs["regDate"];
					$comments = $rs["comments"];
					$origin_comments = $comments;

					$name = StripHtmlChars($name);
					$reg_date = date("l, d F, Y h:i:sa",$regDate);

					if($useWordFilter&&$badWords) $comments = eregi_replace($badWords,"**",$comments);
					$comments = StripHtmlChars($comments);
					if($useAutoLink) $comments = AutoLink($comments);

					$iconNew = ((mktime() - $regDate) <= $nterm) ? "<img src=\"{$FSBOARD_PATH}/img/clip/new_sr.gif\" alt=\"NEW\" />" : "";
					$lines = count(explode("\n",$origin_comments)); //내용 라인수
					if($lines<3) $lines = 3;

					echo "
						<ul style=\"margin:0.88em; padding-left:1em; padding-right:1em; background-color:#fafbf7; font-size:0.88em;\">
							<p style=\"margin:0; padding-top:0.7em; font-weight:bold;\">{$name} {$iconNew}</p>
							<p style=\"margin:0; padding-left:0.5em; font-size:0.9em; color:#aaa;\">{$reg_date}</p>
							<p style=\"margin:1em; padding:0 0 0.7em 0.5em;\">".str_replace("\n","<br />",$comments)."</p>
						</ul>
					";
					$i++;
				}
				mysql_free_result($result_cmt);
			}
		}

		//버튼
		echo "<div style=\"clear:both; margin:0.5em 0em 6em 0em; padding:0.5em; font-size:13px; text-align:right; border-top:1px solid #E0E0E0;\">{$btnView} {$btnEdit} {$btnRemove} {$btnReply} {$btnList} {$btnWrite}</div>";

		flush();





	}
	mysql_free_result($result);

	echo "
				</div>
				<!--
				<div style=\"width: 18%; float: right; font-size: 1em; text-align:left;\">
					<h4 style=\"margin-top:0.1em; margin-bottom:0.1em;\">Categories</h4>
	";
	if($categories) {
		for($i=0,$till=count($categoryElement); $i<$till; $i++) {
			if($categoryElementCount[$i]) echo "<div>".$categoryElement[$i]." (".$categoryElementCount[$i].")</div>";
		}
	}
	echo "
					<input type=\"text\" size=\"16\" /><input type=\"button\" value=\" 검색 \" />
				</div>
				-->
			</div>
		</div>
	";






?>