<?
/*************************************************************
	FSBOARD ListPage Skin Module
*************************************************************/


	if(!$list_included) exit; //list.php 직접 접근 막기

	$displayPageSize = $pageSize; //한페이지에 표시되는 게시물의 수

	//쿼리정렬 방식
	$rowctgr = $_GET["rowctgr"];
	$rowmode = $_GET["rowmode"];

	$view_idx = $idx; //내용보기일경우 현재글번호를 기록해둠

	if(!$srhctgr) {
		$srhctgr0 = trim($_POST["srhctgr0"]);
		$srhctgr1 = trim($_POST["srhctgr1"]);
		$srhctgr2 = trim($_POST["srhctgr2"]);
		$srhctgr3 = trim($_POST["srhctgr3"]);

		//검색카테고리 조합
		$srhctgr .= $srhctgr0 ? 1 : 0;
		$srhctgr .= $srhctgr1 ? 1 : 0;
		$srhctgr .= $srhctgr2 ? 1 : 0;
		$srhctgr .= $srhctgr3 ? 1 : 0;
	}
	else {
		//검색카테고리 분리
		for($i=0,$till=strlen($srhctgr); $i<$till; $i++) { ${"srhctgr".$i} = intval(substr($srhctgr,$i,1)); }
	}

	if($srhstr&&strpos($srhstr," ")) $srhstring = explode(" ",$srhstr);

	$query = "SELECT * FROM ".$_table_id_board." ";

	/////검색모드
	if(($srhctgr0||$srhctgr1||$srhctgr2||$srhctgr3)&&$srhstr) { //검색모드인지 조건 검사

		//다중검색 조건연산 지정
		$optr = trim($_GET["optr"]?$_GET["optr"]:$_POST["optr"]);
		$optr = $optr=="AND"?" AND ":" OR ";

		$query .= " WHERE "; //조건절 추가

		//댓글 검색 포함을 위한 쿼리
		if($srhctgr1||$srhctgr3) $query2 = "SELECT DISTINCT objNum FROM ".$_table_id_comment." WHERE boardId='{$id}' AND ";

		if($srhctgr0) {
			if($srhstring) { //검색어가 여러개일 경우
				for($i=0,$till=sizeof($srhstring); $i<$till; $i++) {
					if($i==0) {
						$query .= " (";
					}
					$query .= " tag_ls LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //사용자태그 검색
					if($i==($till-1)) {
						$query .= ") ";
					}
					else {
						$query .= $optr; //검색문자 추가조건 연결
					}
				}
			}
			else { //검색어가 한개일 경우
				$query .= " tag_ls LIKE '%".StrAddSlashes($srhstr)."%' "; //태그 검색
			}
			if($srhctgr1||$srhctgr2||$srhctgr3) $query .= " OR "; //검색카테고리 추가조건 연결
		}
		if($srhctgr1) { //작성자카테고리
			if($srhstring) { //검색어가 여러개일 경우
				for($i=0,$till=sizeof($srhstring); $i<$till; $i++) {
					if($i==0) {
						$query .= " (";
						$query2.= " (";
					}
					$query .= " author LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //작성자 검색
					$query2.= " name   LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //댓글 작성자 검색
					if($i==($till-1)) {
						$query .= ") ";
						$query2.= ") ";
					}
					else {
						$query .= $optr; //검색문자 추가조건 연결
						$query2.= $optr; //댓글 검색문자 추가조건 연결
					}
				}
			}
			else { //검색어가 한개일 경우
				$query .= " author LIKE '%".StrAddSlashes($srhstr)."%' "; //작성자 검색
				$query2.= " name   LIKE '%".StrAddSlashes($srhstr)."%' ";
			}
			if($srhctgr2||$srhctgr3) $query .= " OR "; //검색카테고리 추가조건 연결
			if($srhctgr3) $query2 .= " OR ";
		}
		if($srhctgr2) { //제목카테고리
			if($srhstring) { //검색어가 여러개일 경우
				for($i=0,$till=sizeof($srhstring); $i<$till; $i++) {
					if($i==0) {
						$query .= " (";
					}
					$query .= " subject LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //제목 검색
					if($i==($till-1)) {
						$query .= ") ";
					}
					else {
						$query .= $optr; //검색문자 추가조건 연결
					}
				}
			}
			else { //검색어가 한개일 경우
				$query .= " subject LIKE '%".StrAddSlashes($srhstr)."%' "; //제목 검색
			}
			if($srhctgr3) $query .= " OR "; //검색카테고리 추가조건 연결
		}
		if($srhctgr3) { //내용카테고리
			if($srhstring) { //검색어가 여러개일 경우
				for($i=0,$till=sizeof($srhstring); $i<$till; $i++) {
					if($i==0) {
						$query .= " (";
						$query2.= " (";
					}
					$query .= " contents LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //내용검색
					$query2.= " comments LIKE '%".StrAddSlashes($srhstring[$i])."%' "; //댓글 내용 검색
					if($i==($till-1)) {
						$query .= ") ";
						$query2.= ") ";
					}
					else {
						$query .= $optr; //검색문자 추가조건 연결
						$query2.= $optr; //댓글 검색문자 추가조건 연결
					}
				}
			}
			else { //검색어가 한개일경우
				$query .= " contents LIKE '%".StrAddSlashes($srhstr)."%' "; //내용검색
				$query2.= " comments LIKE '%".StrAddSlashes($srhstr)."%' ";
			}
		}

		//댓글 추가검색
		if($query2) {
			$result = mysql_query($query2) or Error(mysql_error());
			if($result) {
				$i = 0;
				$str = "";
				while($rs=mysql_fetch_row($result)) {
					$str .= " idx=".$rs[0]." {$optr} "; //검색된 댓글이 있으면 게시물의 고유번호로 주쿼리문에 포함시킴
					$i++;
				}
				if($i) {
					$query .= " OR (".$str;
					$query = substr($query,0,strlen(trim($query))-3); //남는 조건문 제거
					$query .= ") ";
				}
				mysql_free_result($result);
			}
		}

		$noticeNum = 0; //검색모드이면 공지글을 포함하지 않음

		//검색된 게시물수
		$result = mysql_query(str_replace("SELECT * ","SELECT count(*) ",$query)) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		$totalObj = $rs[0];
		mysql_free_result($result);

	}
	/////카테고리 모드
	else if($ctgrstr) {
		$ctgrstr = StrAddSlashes($ctgrstr);
		$query .= " WHERE category='{$ctgrstr}' ";
		$noticeNum = 0; //카테고리모드이면 공지글을 포함하지 않음

		//검색된 카테고리 게시물수
		$result = mysql_query(str_replace("SELECT * ","SELECT count(*) ",$query)) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		$totalObj = $rs[0];
		mysql_free_result($result);

	}
	/////비검색 모드
	else {
		if($noticeNum > $allowNoticeNum) $noticeNum = $allowNoticeNum; //허용된 공지글갯수 보다 실제 공지글이 더 많을 경우 허용된 갯수만큼만 설정
		if($allowNoticeNum>0 && $noticeNum>0) {
			if($pageSize>$noticeNum) {
				$displayPageSize = ($pageSize>$noticeNum && $page==1) ? $pageSize - $noticeNum : $pageSize; //한페이지 목록 갯수에서 공지글 갯수만큼 빼기
				$notInNoticeNum = $page>1 ? $noticeNum : 0; //2페이지 이상에서는 공지글 갯수만큼 밀어내기
			}
			else $noticeNum = 0; //공지글이 한페이지 게시물수보다 많으면 공지글을 포함하지 않음
		}
	}


	$totalObjWithNotice = $totalObj + $noticeNum; //공지글 포함 총게시물수
	$totalPage = intval(($totalObjWithNotice - 1) / $pageSize) + 1; //총 페이지수

	//게시물 넘버링 시작번호
	//if($page==1) $sequenceNum = $totalObj - ($displayPageSize * ($page - 1)); else $sequenceNum = $totalObjWithNotice - ($displayPageSize * ($page - 1));
	$sequenceNum = $totalObjWithNotice - ($displayPageSize * ($page - 1));

	//if($page>=1) $sequenceNum = $totalObj - ($pageSize * ($page - 1)); //게시물 순번 시작번호

	if($totalPage >= $page) {
		if($rowctgr&&$rowmode) { //사용자 정렬
			if($rowmode=="asc" || $rowmode=="desc") $current_rowmode = $rowmode; //사용자 정렬방식 유지
			else Error("정렬 방법이 잘못되었습니다.");
			$query .= " ORDER BY {$rowctgr} {$rowmode} LIMIT ".(($page-1)*$displayPageSize-$notInNoticeNum).",{$displayPageSize};";
		}
		else if($srhctgr&&$srhstr) { //검색모드일 경우 기본정렬
			$query .= " ORDER BY idx DESC LIMIT ".(($page-1)*$pageSize).",{$pageSize};";
		}
		else {
			if($useReply) {
				//답변모드 정렬
				//$query .= " ORDER BY ref DESC, reStep ASC LIMIT ".(($page-1)*$pageSize).",{$pageSize};";
				$query .= " ORDER BY ref DESC, reStep ASC LIMIT ".(($page-1)*$displayPageSize-$notInNoticeNum).",{$displayPageSize};";
			}
			else {
				//기본모드 정렬
				$query .= " ORDER BY idx DESC LIMIT ".(($page-1)*$displayPageSize-$notInNoticeNum).",{$displayPageSize};";
			}
		}
	}
	else Error("페이지 범위가 초과되었습니다.");

	$rowmode = !$rowmode||$rowmode=="desc" ? "asc" : "desc"; //사용자 정렬방식 변경


	//카테고리 사용일 경우
	if($useCategory&&$categories) {
		$categoryElement = explode(",",$categories); //카테고리 분리
		$menuCategory = " <select onchange=\"category_change(this);\"><option value=\"\">카테고리</option>";
		for($i=0,$till=sizeof($categoryElement); $i<$till; $i++) {
			$menuCategory .= "<option value=\"".str_replace("\"","&#34;",$categoryElement[$i])."\"".($categoryElement[$i]==$category?" selected=\"selected\"":"").">{$categoryElement[$i]}</option>";
		}
		$menuCategory .= "</select>";
	}

	//정렬모드 링크
	$rowIdx = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=idx&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"번호 오름차순/내림차순 정렬\">";
	$rowCtgr = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=category&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"카테고리 오름차순/내림차순 정렬\">";
	$rowSubject = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=subject&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"제목 오름차순/내림차순 정렬\">";
	$rowAuthor = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=author&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"작성자 오름차순/내림차순 정렬\">";
	$rowRegDate = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=regDate&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"등록일 오름차순/내림차순 정렬\">";
	$rowRead = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;srhctgr={$srhctgr}&amp;srhstr={$srhstr}&amp;page={$page}&amp;rowctgr=readNum&amp;rowmode={$rowmode}\" class=\"lnk_list\" title=\"조회수 오름차순/내림차순 정렬\">";



	/////버튼
	$btnPath = "$FSBOARD_PATH/img/btn"; //버튼 경로

	$btnRead = "<a href=\"javascript:sendit('{$id}','multiview','',{$page},'{$srhctgr}','{$srhstr}','{$rowctgr}','{$rowmode}','{$ctgrstr}');\" title=\"선택된 게시물 읽기\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Read</span></a>";

	if(!$MemId) {
		$btnLogin = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("login",1)."&amp;page={$page}\" title=\"로그인\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Login</span></a>";
		$btnMemJoin = "<a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=join','','width=570,height=550,resizable=1,scrollbars=1')\" title=\"회원가입\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Join</span></a>";
	} else {
		$btnLogout = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("logout",1)."&amp;page={$page}\" title=\"로그아웃\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Logout</span></a>";
		$btnMemModify = "<a href=\"javascript:void(0);\" onclick=\"window.open('{$FSBOARD_PATH}/lib/members.php?mode=modify&amp;mem_id={$MemId}','','width=570,height=550,resizable=1,scrollbars=1')\" title=\"회원정보수정\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Modify</span></a>";
	}

	if($isAdmin || !$useHideButtons) {
		$btnAdmin = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("admin",1)."&amp;page={$page}\" title=\"게시판 관리\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Admin</span></a>";
		$btnDelete = "<a href=\"javascript:sendit('{$id}','multidelete','',{$page},'{$srhctgr}','{$srhstr}','{$rowctgr}','{$rowmode}','{$ctgrstr}');\" title=\"게시물 다중 삭제\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Delete</span></a>";
	}
	if($MemLevel<=3 || !$useHideButtons) {
		$btnMove = "<a href=\"javascript:sendit('{$id}','multimoveobjs','',{$page},'{$srhctgr}','{$srhstr}','{$rowctgr}','{$rowmode}','{$ctgrstr}');\" title=\"게시물 다중 이동\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Move</span></a>";
	}
	if($MemLevel<=1) $btnSetup = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("setup",1)."&amp;page={$page}\" onclick=\"window.open(this.href,'_blank'); return false;\" title=\"관리자 설정\"><span style=\"font-size:11px;font-family:Arial;\" class=\"lnk_list\">Setup</span></a>";

	if($write_level || !$useHideButtons) $btnWrite = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("write",1)."&amp;page={$page}\" class=\"list_btn\"><img src=\"{$btnPath}/write.gif\" alt=\"글쓰기\" /></a>";

	$btnSrh = "<a href=\"javascript:search();\"><img src=\"{$FSBOARD_PATH}/img/btn/search.gif\" alt=\"검색\" /></a>";
	$btnSrhList = $srhctgr&&$srhstr||$mode=="search"||$rowctgr&&$rowmode||$ctgrstr ? "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list\"><img src=\"{$FSBOARD_PATH}/img/btn/list.gif\" alt=\"검색하기전 목록\" /></a>" : "";
	$btnList = $mode=="view" ? "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list\" class=\"lnk_list\"><img src=\"{$btnPath}/list.gif\" alt=\"리스트\" /></a>" : "";

	if($useRssFeed) {
		$btnRss = IsRewrite() ? "<a href=\"".$FSBOARD_PATH."/rss/".$id."\">" : "<a href=\"".$FSBOARD_PATH."/?id={$id}&amp;mode=rss.xml\">";
		$btnRss .= "<img src=\"{$FSBOARD_PATH}/img/clip/rss.gif\" alt=\"RSS 2.0 Feeds (XML)\" /></a>";
	}






////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
if(!$view_included) { ContentTop(); }






	echo "
	<div id=\"ListFrame\">
		<div id=\"ListLayout\">
			<div id=\"ListTop\">
				<p class=\"ListTop_Left\">
					{$btnDelete}
					{$btnLogin}
					{$btnLogout}
					{$btnMemJoin}
					{$btnMemModify}
				</p>
				<p class=\"ListTop_Right\">
					게시물: <strong>".$totalObj."</strong> &nbsp;
					페이지: <strong>".$page."</strong>/<strong>".$totalPage."</strong> &nbsp;
					오늘: <strong>".$todayCount."</strong> &nbsp;
					전체: <strong>".$totalCount."</strong>
					{$btnRss}
					{$menuCategory}
				</p>
			</div>
			<div id=\"ListTitle\">
				<h4 class=\"ListBody1\"><a href=\"javascript:checkAll();\"><img src=\"".$FSBOARD_PATH."/img/clip/check.gif\" alt=\"전체 선택/해제\" /></a></h4>
				<h4 class=\"ListBody2\">".$rowIdx."번호</a></h4>
				".($useCategory?"<h4 class=\"ListBody3\">".$rowCtgr."분류</a></h4>":"")."
				<h4 class=\"ListBody4\">".$rowSubject."제 &nbsp;목</a></h4>
				<h4 class=\"ListBody5\">".$rowAuthor."작성자</a></h4>
				<h4 class=\"ListBody6\">".$rowRegDate."작성일</a></h4>
				<h4 class=\"ListBody7\">".$rowRead."조회</a></h4>
			</div>
	";

	$i = 0;

	/////공지사항 리스트 출력
	if($allowNoticeNum>0 && $noticeNum>0 && $page==1 && !intval($srhctgr) && !$srhstr && !$ctgrstr) {
		$nquery = "SELECT * FROM ".$_table_id_board." WHERE isNotice=1 ORDER BY idx DESC LIMIT 0, ".$noticeNum.";";
		$nresult  = mysql_query($nquery) or Error(mysql_error());
		while($nrs = mysql_fetch_array($nresult)) {
			$idx = $nrs["idx"];
			$isMember = $nrs["isMember"];
			$subject = $nrs["subject"];
			$category = $nrs["category"];
			$author = $nrs["author"];
			$regDate = $nrs["regDate"];
			$readNum = $nrs["readNum"];
			$memoNum = $nrs["memoNum"];
			$tbNum = $nrs["tbNum"];
			$fileName1 = $nrs["fileName1"];

			if($usePreview&&!$isSecret) {
				$contents = $nrs["contents"];
				$lines = count(explode("\n",$contents)); //내용 라인수
				if($contentLimit) $contents = CutStr($contents,$contentLimit);
				$contents = str_replace("\"","&#34;",$contents);
				if($lines>1) $contents .= "\n\n Included $lines lines";
			} else $contents = "";

			$subject = StripHtmlChars($subject);
			if($subjectLimit) $subject = CutStr($subject,$subjectLimit);
			$subject = "<strong><a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx=".$idx."&amp;page=".$page."\" title=\"".$contents."\" class=\"lnk_list\">".$subject."</a></strong>";

			//댓글 갯수 표시
			if($memoNum) {
				$iconMemoNum = "<span class=\"list_m\">[$memoNum]</span>";
				if((mktime() - $nrs["memoLatestDate"]) <= $nterm) $iconMemoNum = "<strong>".$iconMemoNum."</strong>";
			}
			else $iconMemoNum = "";

			//트랙백 갯수 표시
			if($tbNum) {
				$iconTbNum = "<span class=\"list_tb\">(".$tbNum.")</span>";
			}
			else $iconTbNum = "";

			//카테고리 사용일 경우 표시
			if($category && $useCategory) $category = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;ctgrstr=".urlencode($category)."&amp;page=1\" title=\"카테고리 검색\" class=\"list_ct\">{$category}</a>";
			if(!$category && $useCategory) $category = "<span style=\"color:silver\">없음</span>";

			//첨부파일이 있을 경우 표시
			$iconFile = $fileName1 ? FileTypeIcon($fileName1) : "";

			//작성자 표시
			$author = MemberName($isMember,$author,$authorLimit,$e_mail,$homeUrl);

			//새글에 new표시
			$icoNew = ((mktime() - $regDate) <= $nterm) ? "<img src=\"{$FSBOARD_PATH}/img/clip/new_sr.gif\" alt=\"최근글\" />" : "";

			//작성일자
			$reg_date = date("Y.m.d",$regDate);

			//조회수
			$readNum = NumColor($readNum);

			echo "
			<div class=\"ListBody\">
				<p class=\"ListBody1\"><input type=\"checkbox\" id=\"idx{$i}\" name=\"idx{$i}\" value=\"{$idx}\" /></p>
				<p class=\"ListBody2\">".($idx==$view_idx?"<img src=\"{$FSBOARD_PATH}/img/clip/arrow5.gif\" alt=\"현재글\" style=\"vertical-align:baseline;\" />":"<img src=\"{$FSBOARD_PATH}/img/clip/notice.gif\" alt=\"공지글\" style=\"vertical-align:baseline;\" />")."</p>
				".($useCategory?"<p class=\"ListBody3\">".$category."</p>":"")."
				<p class=\"ListBody4\">".$subject.$iconFile.$icoNew." ".$iconMemoNum." ".$iconTbNum."</p>
				<p class=\"ListBody5\">".$author."</p>
				<p class=\"ListBody6\">".$reg_date."</p>
				<p class=\"ListBody7\">".$readNum."</p>
			</div>

			";
			$sequenceNum--;
			$i++;
		}
		mysql_free_result($nresult);
	}

	/////게시물 리스트 출력
	if($result=mysql_query($query)) {
		while($rs=mysql_fetch_array($result)) {
			$idx = $rs["idx"];
			$isSecret = $rs["isSecret"];
			$isMember = $rs["isMember"];
			$author = $rs["author"];
			$e_mail = $rs["e_mail"];
			$homeUrl = $rs["homeUrl"];
			$subject = $rs["subject"];
			$category = $rs["category"];
			$regDate = $rs["regDate"];
			$readNum = $rs["readNum"];
			$memoNum = $rs["memoNum"];
			$tbNum = $rs["tbNum"];
			$reLevel = $rs["reLevel"];
			$fileName1 = $rs["fileName1"];

			if($usePreview&&!$isSecret) {
				$contents = $rs["contents"];
				$lines = count(explode("\n",$contents)); //내용 라인수
				if($contentLimit) $contents = CutStr($contents,$contentLimit);
				$contents = str_replace("\"","&#34;",$contents);
				if($lines>1) $contents .= "\n\n Included ".$lines." lines";
			} else $contents = "";

			//비밀글일 경우 표시
			$iconLock = $isSecret ? "<img src=\"".$FSBOARD_PATH."/img/clip/lock.gif\" alt=\"비밀글\" style=\"vertical-align:middle;\" />" : "";

			//작성자 표시
			$author = MemberName($isMember,$author,$authorLimit,$e_mail,$homeUrl);

			//답글일 경우 표시
			if($useReply) $iconRe = $reLevel ? "<img src=\"".$FSBOARD_PATH."/img/clip/re_arrow.gif\" alt=\"답글\" style=\"margin-left:".($reLevel*4)."px;\" /> " : "";

			//$subject = strip_tags($subject,"");
			//$subject = eregi_replace("<([^>]|\n)*>","",$subject);
			if($subjectLimit) $subject = CutStr($subject,$subjectLimit);
			$subject = StripHtmlChars($subject);
			if($idx==$view_idx) $subject = "<u>".$subject."</u>";
			if(substr($srhctgr,2,1)&&$srhstr) {
				if($srhstring) {
					for($j=0,$till=sizeof($srhstring); $j<$till; $j++)
						$subject = str_replace($srhstring[$j],"<span style=\"font-weight:bold; background-color:yellow;\">".$srhstring[$j]."</span>",$subject);
				}
				else $subject = str_replace($srhstr,"<span style=\"font-weight:bold; background-color:yellow;\">{$srhstr}</span>",$subject);
			}
			$subject = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx=".$idx."&amp;page=".$page."\" title=\"".$contents."\" class=\"lnk_list\">".$subject."</a>";
			$subject = $iconRe.$subject;

			//댓글 갯수 표시
			if($memoNum) {
				$iconMemoNum = "<span class=\"list_m\">[$memoNum]</span>";
				if((mktime() - $rs["memoLatestDate"]) <= $nterm) $iconMemoNum = "<strong>".$iconMemoNum."</strong>";
			}
			else $iconMemoNum = "";

			//트랙백 갯수 표시
			if($tbNum) {
				$iconTbNum = "<span class=\"list_tb\">($tbNum)</span>";
			}
			else $iconTbNum = "";

			//카테고리 사용일 경우 표시
			if($category && $useCategory) $category = "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list&amp;ctgrstr=".urlencode($category)."&amp;page=1\" title=\"카테고리 검색\" class=\"list_ct\">{$category}</a>";
			if(!$category && $useCategory) $category = "<span style=\"color:silver;\">없음</span>";

			//첨부파일이 있을 경우 표시
			$iconFile = $fileName1 ? FileTypeIcon($fileName1) : "";

			//새글에 new표시
			$icoNew = ((mktime() - $regDate) <= $nterm) ? "<img src=\"".$FSBOARD_PATH."/img/clip/new_sr.gif\" alt=\"최근글\" />" : "";

			//작성일자
			$reg_date = date("Y.m.d",$regDate);

			//조회수
			$readNum = NumColor($readNum);

			echo "
			<div class=\"ListBody\">
				<p class=\"ListBody1\"><input type=\"checkbox\" id=\"idx{$i}\" name=\"idx{$i}\" value=\"{$idx}\" /></p>
				<p class=\"ListBody2\">".($idx==$view_idx?"<img src=\"{$FSBOARD_PATH}/img/clip/arrow5.gif\" alt=\"현재글\" style=\"vertical-align:baseline;\" />":$sequenceNum)."</p>
				".($useCategory?"<p class=\"ListBody3\">{$category}</p>":"")."
				<p class=\"ListBody4\">{$subject}{$iconLock}{$iconFile}{$icoNew} {$iconMemoNum} {$iconTbNum}</p>
				<p class=\"ListBody5\">{$author}</p>
				<p class=\"ListBody6\">{$reg_date}</p>
				<p class=\"ListBody7\">{$readNum}</p>
			</div>
			";
			$sequenceNum--;
			$i++;
		}
		if(!$i) {
			if($srhctgr&&$srhstr) {
				echo "
			<div class=\"ListBody\"><p style=\"text-align:center\">\"<b>{$srhstr}</b>\" (으)로 검색된 결과가 없습니다</p></div>
				";
			}
			else {
				echo "
			<div class=\"ListBody\"><p style=\"text-align:center\">등록된 글이 없습니다</p></div>
				";
			}
		}

		mysql_free_result($result);
	}
?>

			<div id="ListBottom">
				<!-- 페이징 --><?=NavPage($page,$divPage,$totalPage,QryStr("list")," class=\"list_p\"","[&laquo;],[&raquo;]","[,]")?>
			</div>

			<div id="ListSearch">
				<!-- 검색 -->
				<p>
<?
/*
					$sc1_0 = "{$FSBOARD_PATH}/img/btn/sc1_off.gif";
					$sc1_1 = "{$FSBOARD_PATH}/img/btn/sc1_on.gif";
					$sc2_0 = "{$FSBOARD_PATH}/img/btn/sc2_off.gif";
					$sc2_1 = "{$FSBOARD_PATH}/img/btn/sc2_on.gif";
					$sc3_0 = "{$FSBOARD_PATH}/img/btn/sc3_off.gif";
					$sc3_1 = "{$FSBOARD_PATH}/img/btn/sc3_on.gif";

					$img_sc1_0 = "<img src=\"{$sc1_0}\" id=\"sc1\" alt=\"이름 선택/해제\" />";
					$img_sc1_1 = "<img src=\"{$sc1_1}\" id=\"sc1\" alt=\"이름 선택/해제\" />";
					$img_sc2_0 = "<img src=\"{$sc2_0}\" id=\"sc2\" alt=\"제목 선택/해제\" />";
					$img_sc2_1 = "<img src=\"{$sc2_1}\" id=\"sc2\" alt=\"제목 선택/히제\" />";
					$img_sc3_0 = "<img src=\"{$sc3_0}\" id=\"sc3\" alt=\"내용 선택/해제\" />";
					$img_sc3_1 = "<img src=\"{$sc3_1}\" id=\"sc3\" alt=\"내용 선택/해제\" />";
*/
?>
<!--
					<input type="hidden" id="srhctgr1" name="srhctgr1" value="<?echo $srhctgr&&$srhstr?($srhctgr1?1:0):0;?>" />
					<input type="hidden" id="srhctgr2" name="srhctgr2" value="<?echo $srhctgr&&$srhstr?($srhctgr2?1:0):1;?>" />
					<input type="hidden" id="srhctgr3" name="srhctgr3" value="<?echo $srhctgr&&$srhstr?($srhctgr2?1:0):1;?>" />
					<a href="javascript:void(0);" onclick="schk(1);"><?echo $srhctgr&&$srhstr?($srhctgr1?$img_sc1_1:$img_sc1_0):$img_sc1_0;?></a>
					<a href="javascript:void(0);" onclick="schk(2);"><?echo $srhctgr&&$srhstr?($srhctgr2?$img_sc2_1:$img_sc2_0):$img_sc2_1;?></a>
					<a href="javascript:void(0);" onclick="schk(3);"><?echo $srhctgr&&$srhstr?($srhctgr3?$img_sc3_1:$img_sc3_0):$img_sc3_1;?></a>
-->
					<input type="checkbox" id="srhctgr2" name="srhctgr2" id="srhctgr2" value="1"<?echo $srhctgr&&$srhstr?($srhctgr2?" checked=\"checked\"":""):" checked=\"checked\"";?> /><label for="srhctgr2">제목</label>
					<input type="checkbox" id="srhctgr3" name="srhctgr3" id="srhctgr3" value="1"<?echo $srhctgr&&$srhstr?($srhctgr3?" checked=\"checked\"":""):"";?> /><label for="srhctgr3">내용</label>
					<input type="checkbox" id="srhctgr1" name="srhctgr1" id="srhctgr1" value="1"<?echo $srhctgr&&$srhstr?($srhctgr1?" checked=\"checked\"":""):"";?> /><label for="srhctgr1">이름</label>
					<input type="checkbox" id="srhctgr0" name="srhctgr0" id="srhctgr0" value="1"<?echo $srhctgr&&$srhstr?($srhctgr0?" checked=\"checked\"":""):"";?> /><label for="srhctgr0">태그</label>
				</p>
				<p class="list_srhtxt">
					<!--input type="checkbox" id="optr" name="optr" id="optr" value="AND"<?echo $optr?($optr==" AND "?" checked=\"checked\"":""):" checked=\"checked\"";?>><label for="optr" title="검색어가 여러개일 경우 체크하면 AND검색, 체크하지 않으면 OR검색을 합니다.<?="\n\n"?>검색어를 여러개 입력하려면 공백으로 구분하세요.">AND</label-->
					<input type="text" size="15" id="srhstr" name="srhstr" value="<?=$srhstr?>" onkeypress="if(window.event.keyCode==13){search();return;}" class="txtbox_srh" title="검색어를 여러개 사용할경우 공백으로 구분해 주세요." />
				</p>
				<p>
					<select id="optr" name="optr" title="검색어가 여러개일 경우 적용됩니다.<?="\n"?>검색어를 여러개 입력하려면 공백으로 구분하세요." class="defstyle">
						<option<?echo $optr?($optr==" AND "?" selected=\"selected\"":""):" selected=\"selected\"";?>>AND</option>
						<option<?echo $optr==" OR "?" selected=\"selected\"":"";?>>OR</option>
					</select>
				</p>
				<p>
					<?echo "{$btnSrh}{$btnSrhList}";?>
				</p>
				<!-- 검색 -->
			</div>
			<div style="clear:both; text-align:right;">
					<?=($btnList." ".$btnWrite)?>
			</div>

		</div>
	</div>

<script type="text/javascript">
//<![CDATA[
/*
var sc1_0 = new Image(); sc1_0.src = "<?=$sc1_0?>";
var sc1_1 = new Image(); sc1_1.src = "<?=$sc1_1?>";
var sc2_0 = new Image(); sc2_0.src = "<?=$sc2_0?>";
var sc2_1 = new Image(); sc2_1.src = "<?=$sc2_1?>";
var sc3_0 = new Image(); sc3_0.src = "<?=$sc3_0?>";
var sc3_1 = new Image(); sc3_1.src = "<?=$sc3_1?>";
*/

function schk(n) {
	try {
		var sc = document.getElementById("__ctl").elements["srhctgr" + n];
		var ci = document.getElementById("sc" + n);

		sc.value = sc.value=="0" ? "1" : "0";
		ci.src = sc.value=="0" ? eval("sc" + n + "_0.src") : eval("sc" + n + "_1.src");
	} catch(e) { window.alert("에러 : " + e.number + "\n" + e.description); }
}

function search() {
	try {
		var frm = document.getElementById("__ctl");
		/*
		if(frm.srhctgr1.checked == false && frm.srhctgr2.checked == false && frm.srhctgr3.checked == false) {
			window.alert("제목,내용,이름 중 하나 이상 선택해 주세요.");
			frm.srhctgr2.focus();
			return;
		}
		*/
		if(frm.srhctgr1.value=="0" && frm.srhctgr2.value=="0" && frm.srhctgr3.value=="0") {
			window.alert("제목,내용,이름 중 하나 이상 선택해 주세요.");
			return;
		}
		if(!frm.srhstr.value) {
			window.alert("검색어를 입력해 주세요");
			frm.srhstr.focus();
			return;
		}
		sendit("<?=$id?>","search");
		return;
	} catch(e) { window.alert("에러 : " + e.number + "\n" + e.description); }
}

function category_change(obj) {
	try {
		var myindex = obj.selectedIndex;
		var ctgrstr = obj.options[myindex].value;
		window.location.href = "<?=($_SERVER["PHP_SELF"]."?id={$id}&amp;mode={$list}&amp;ctgrstr=\"+ctgrstr+\"&amp;page=1")?>";
		return true;
	} catch(e) {}
}
//]]>
</script>
