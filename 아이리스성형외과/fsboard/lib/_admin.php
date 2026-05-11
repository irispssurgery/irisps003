<?
/*************************************************************
	FSBOARD Admin Set
*************************************************************/


if(!$admin_included) die("잘못된 접근입니다.");
//if(!$isAdmin) Error("잘못된 접근입니다.");

//폼전송되었는지 체크
$isPostBack = $_POST["isPostBack"];


/////폼전송일경우
if($mode=="adminsave"&&$isPostBack) {
	if(!eregi($HTTP_HOST,$HTTP_REFERER)) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다."); //GET전송 방지

	$boardName			= StrAddSlashes(trim($_POST["boardName"]));
	$combinedFileName	= StrAddSlashes(trim($_POST["combinedFileName"]));
	$skin				= StrAddSlashes(trim($_POST["skin"]));
	$adminPasswd1		= trim($_POST["adminPasswd1"]);
	$adminPasswd2		= trim($_POST["adminPasswd2"]);
	$adminIDs			= StrAddSlashes(trim($_POST["adminIDs"]));
	$todayAccess		= StrAddSlashes(trim($_POST["todayAccess"]));
	$totalAccess		= StrAddSlashes(trim($_POST["totalAccess"]));

	$bgImage			= StrAddSlashes(trim($_POST["bgImage"]));
	$bgColor			= StrAddSlashes(trim($_POST["bgColor"]));
	$width				= StrAddSlashes(trim($_POST["width"]));
	$subjectLimit		= StrAddSlashes(trim($_POST["subjectLimit"]));
	$authorLimit		= StrAddSlashes(trim($_POST["authorLimit"]));
	$contentLimit		= StrAddSlashes(trim($_POST["contentLimit"]));
	$pageSize			= StrAddSlashes(trim($_POST["pageSize"]));
	$divPage			= StrAddSlashes(trim($_POST["divPage"]));
	$allowNoticeNum		= trim($_POST["allowNoticeNum"]);
	$align				= trim($_POST["align"]);

	$headFile			= StrAddSlashes(trim($_POST["headFile"]));
	$tailFile			= StrAddSlashes(trim($_POST["tailFile"]));
	$headMsg			= StrAddSlashes(trim($_POST["headMsg"]));
	$tailMsg			= StrAddSlashes(trim($_POST["tailMsg"]));
	$writeFrmDefMsg		= StrAddSlashes(trim($_POST["writeFrmDefMsg"]));

	$useListAtView		= trim($_POST["useListAtView"]);
	$useMemo			= trim($_POST["useMemo"]);
	$useReply			= trim($_POST["useReply"]);
	$useTrackback		= trim($_POST["useTrackback"]);
	$useRssFeed			= trim($_POST["useRssFeed"]);
	$useAutoLink		= trim($_POST["useAutoLink"]);
	$usePreview			= trim($_POST["usePreview"]);
	$useSiteLink1		= trim($_POST["useSiteLink1"]);
	$useSiteLink2		= trim($_POST["useSiteLink2"]);
	$useSecret			= trim($_POST["useSecret"]);
	$useBlockSpam		= trim($_POST["useBlockSpam"]);
	$useBlockAnyLink	= trim($_POST["useBlockAnyLink"]);
	$useHideButtons		= trim($_POST["useHideButtons"]);
	$useViewClientIp	= trim($_POST["useViewClientIp"]);
	$useViewClientInfo	= trim($_POST["useViewClientInfo"]);
	$editMode			= trim($_POST["editMode"]);
	$useHtml			= trim($_POST["useHtml"]);
	$allowTags			= StrAddSlashes(trim($_POST["allowTags"]));
	$useAttachFile		= trim($_POST["useAttachFile"]);
	$fileMaxLimit		= trim($_POST["fileMaxLimit"]);
	$fileMaxNum			= trim($_POST["fileMaxNum"]);
	$allowExts			= StrAddSlashes(trim($_POST["allowExts"]));
	$dataPath			= StrAddSlashes(trim($_POST["dataPath"]));
	$useExecFile		= trim($_POST["useExecFile"]);
	$allowEmbedFileExts	= StrAddSlashes(trim($_POST["allowEmbedFileExts"]));
	$useNewIcon			= trim($_POST["useNewIcon"]);
	$termNewIcon		= trim($_POST["termNewIcon"]);
	$useRszImg			= trim($_POST["useRszImg"]);
	$imgRszWidth		= StrAddSlashes(trim($_POST["imgRszWidth"]));
	$useCategory		= trim($_POST["useCategory"]);
	$categories			= StrAddSlashes(trim($_POST["categories"]));
	$useWordFilter		= trim($_POST["useWordFilter"]);
	$badWords			= StrAddSlashes(trim($_POST["badWords"]));

	$levelList			= trim($_POST["levelList"]);
	$levelView			= trim($_POST["levelView"]);
	$levelSecret		= trim($_POST["levelSecret"]);
	$levelWrite			= trim($_POST["levelWrite"]);
	$levelReply			= trim($_POST["levelReply"]);
	$levelMemoWrite		= trim($_POST["levelMemoWrite"]);
	$levelNoticeWrite	= trim($_POST["levelNoticeWrite"]);
	$levelUseHtml		= trim($_POST["levelUseHtml"]);
	$levelDelete		= trim($_POST["levelDelete"]);


	$applyAll_bgImage			= trim($_POST["applyAll_bgImage"]);
	$applyAll_bgColor			= trim($_POST["applyAll_bgColor"]);
	$applyAll_width				= trim($_POST["applyAll_width"]);
	$applyAll_subjectLimit		= trim($_POST["applyAll_subjectLimit"]);
	$applyAll_authorLimit		= trim($_POST["applyAll_authorLimit"]);
	$applyAll_contentLimit		= trim($_POST["applyAll_contentLimit"]);
	$applyAll_pageSize			= trim($_POST["applyAll_pageSize"]);
	$applyAll_divPage			= trim($_POST["applyAll_divPage"]);
	$applyAll_allowNoticeNum	= trim($_POST["applyAll_allowNoticeNum"]);
	$applyAll_align				= trim($_POST["applyAll_align"]);

	$applyAll_headFile			= trim($_POST["applyAll_headFile"]);
	$applyAll_tailFile			= trim($_POST["applyAll_tailFile"]);
	$applyAll_headMsg			= trim($_POST["applyAll_headMsg"]);
	$applyAll_tailMsg			= trim($_POST["applyAll_tailMsg"]);
	$applyAll_writeFrmDefMsg	= trim($_POST["applyAll_writeFrmDefMsg"]);

	$applyAll_useListAtView		= trim($_POST["applyAll_useListAtView"]);
	$applyAll_useMemo			= trim($_POST["applyAll_useMemo"]);
	$applyAll_useReply			= trim($_POST["applyAll_useReply"]);
	$applyAll_useTrackback		= trim($_POST["applyAll_useTrackback"]);
	$applyAll_useRssFeed		= trim($_POST["applyAll_useRssFeed"]);
	$applyAll_useAutoLink		= trim($_POST["applyAll_useAutoLink"]);
	$applyAll_usePreview		= trim($_POST["applyAll_usePreview"]);
	$applyAll_useSiteLink1		= trim($_POST["applyAll_useSiteLink1"]);
	$applyAll_useSiteLink2		= trim($_POST["applyAll_useSiteLink2"]);
	$applyAll_useSecret			= trim($_POST["applyAll_useSecret"]);
	$applyAll_useBlockSpam		= trim($_POST["applyAll_useBlockSpam"]);
	$applyAll_useBlockAnyLink	= trim($_POST["applyAll_useBlockAnyLink"]);
	$applyAll_useHideButtons	= trim($_POST["applyAll_useHideButtons"]);
	$applyAll_useViewClientIp	= trim($_POST["applyAll_useViewClientIp"]);
	$applyAll_useViewClientInfo	= trim($_POST["applyAll_useViewClientInfo"]);
	$applyAll_editMode			= trim($_POST["applyAll_editMode"]);
	$applyAll_useHtml			= trim($_POST["applyAll_useHtml"]);
	$applyAll_allowTags			= trim($_POST["applyAll_allowTags"]);
	$applyAll_useAttachFile		= trim($_POST["applyAll_useAttachFile"]);
	$applyAll_fileMaxLimit		= trim($_POST["applyAll_fileMaxLimit"]);
	$applyAll_fileMaxNum		= trim($_POST["applyAll_fileMaxNum"]);
	$applyAll_allowExts			= trim($_POST["applyAll_allowExts"]);
	$applyAll_dataPath			= trim($_POST["applyAll_dataPath"]);
	$applyAll_useExecFile		= trim($_POST["applyAll_useExecFile"]);
	$applyAll_allowEmbedFileExts= trim($_POST["applyAll_allowEmbedFileExts"]);
	$applyAll_useNewIcon		= trim($_POST["applyAll_useNewIcon"]);
	$applyAll_termNewIcon		= trim($_POST["applyAll_termNewIcon"]);
	$applyAll_useRszImg			= trim($_POST["applyAll_useRszImg"]);
	$applyAll_imgRszWidth		= trim($_POST["applyAll_imgRszWidth"]);
	$applyAll_useCategory		= trim($_POST["applyAll_useCategory"]);
	$applyAll_categories		= trim($_POST["applyAll_categories"]);
	$applyAll_useWordFilter		= trim($_POST["applyAll_useWordFilter"]);
	$applyAll_badWords			= trim($_POST["applyAll_badWords"]);

	$applyAll_levelList			= trim($_POST["applyAll_levelList"]);
	$applyAll_levelView			= trim($_POST["applyAll_levelView"]);
	$applyAll_levelSecret		= trim($_POST["applyAll_levelSecret"]);
	$applyAll_levelWrite		= trim($_POST["applyAll_levelWrite"]);
	$applyAll_levelReply		= trim($_POST["applyAll_levelReply"]);
	$applyAll_levelMemoWrite	= trim($_POST["applyAll_levelMemoWrite"]);
	$applyAll_levelNoticeWrite	= trim($_POST["applyAll_levelNoticeWrite"]);
	$applyAll_levelUseHtml		= trim($_POST["applyAll_levelUseHtml"]);
	$applyAll_levelDelete		= trim($_POST["applyAll_levelDelete"]);
	$applyMode = trim($_POST["applyMode"]);


	//잘못된값 검사
	//

	//관리자 암호 검사
	if($adminPasswd1&&$adminPasswd2) $adminPasswd = md5($adminPasswd1);
	if($adminPasswd1 != $adminPasswd2) Error("관리자 암호와 암호확인이 일치하지 않습니다.");

	if(!$todayAccess) $todayAccess = $todayCount; //오늘카운터값이 없으면 원래값 가져옴
	if(!$totalAccess) $totalAccess = $totalCount; //전체카운터값이 없으면 원래값 가져옴

	//잘못지정된 숫자값 검사
	if(intval($width)<1) $width = 100;
	if(intval($subjectLimit)<0) $subjectLimit = 0;
	if(intval($authorLimit)<0) $authorLimit = 0;
	if(intval($contentLimit)<0) $contentLimit = 0;
	if(intval($pageSize)<1) $pageSize = 15;
	if(intval($divPage)<1) $divPage = 10;

	//체크박스인 필드값 설정
	$useListAtView		= $useListAtView		? 1 : 0;
	$useMemo			= $useMemo				? 1 : 0;
	$useReply			= $useReply				? 1 : 0;
	$useTrackback		= $useTrackback			? 1 : 0;
	$useRssFeed			= $useRssFeed			? 1 : 0;
	$useAutoLink		= $useAutoLink			? 1 : 0;
	$usePreview			= $usePreview			? 1 : 0;
	$useSiteLink1		= $useSiteLink1			? 1 : 0;
	$useSiteLink2		= $useSiteLink2			? 1 : 0;
	$useSecret			= $useSecret			? 1 : 0;
	$useBlockSpam		= $useBlockSpam			? 1 : 0;
	$useBlockAnyLink	= $useBlockAnyLink		? 1 : 0;
	$useHideButtons		= $useHideButtons		? 1 : 0;
	$useViewClientIp	= $useViewClientIp		? 1 : 0;
	$useViewClientInfo	= $useViewClientInfo	? 1 : 0;
	$useAttachFile		= $useAttachFile		? 1 : 0;
	$useExecFile		= $useExecFile			? 1 : 0;
	$useRszImg			= $useRszImg			? 1 : 0;
	$useCategory		= $useCategory			? 1 : 0;
	$useWordFilter		= $useWordFilter		? 1 : 0;

	//첨부파일 디렉토리 설정
	if(!is_dir($FSBOARD_ROOT.$dataPath)) {
		@mkdir($FSBOARD_ROOT.$dataPath,0777);
		@chmod($FSBOARD_ROOT.$dataPath,0756);
	}


	/////현재 게시판 및 선택된 게시판 적용항목
	$query = "UPDATE {$_table_id_admin} SET
			bgImage				= '$bgImage',
			bgColor				= '$bgColor',
			width				= $width,
			subjectLimit		= $subjectLimit,
			authorLimit			= $authorLimit,
			contentLimit		= $contentLimit,
			pageSize			= $pageSize,
			divPage				= $divPage,
			allowNoticeNum		= $allowNoticeNum,
			align				= '$align',

			headFile			= '$headFile',
			tailFile			= '$tailFile',
			headMsg				= '$headMsg',
			tailMsg				= '$tailMsg',
			writeFrmDefMsg		= '$writeFrmDefMsg',

			useListAtView		= $useListAtView,
			useMemo				= $useMemo,
			useReply			= $useReply,
			useTrackback		= $useTrackback,
			useRssFeed			= $useRssFeed,
			useAutoLink			= $useAutoLink,
			usePreview			= $usePreview,
			useSiteLink1		= $useSiteLink1,
			useSiteLink2		= $useSiteLink2,
			useSecret			= $useSecret,
			useBlockSpam		= $useBlockSpam,
			useBlockAnyLink		= $useBlockAnyLink,
			useHideButtons		= $useHideButtons,
			useViewClientIp		= $useViewClientIp,
			useViewClientInfo	= $useViewClientInfo,
			editMode			= '$editMode',
			useHtml				= '$useHtml',
			allowTags			= '$allowTags',
			useAttachFile		= $useAttachFile,
			fileMaxLimit		= $fileMaxLimit,
			fileMaxNum			= $fileMaxNum,
			allowExts			= '$allowExts',
			dataPath			= '$dataPath',
			useExecFile			= $useExecFile,
			allowEmbedFileExts	= '$allowEmbedFileExts',
			useNewIcon			= $useNewIcon,
			termNewIcon			= $termNewIcon,
			useRszImg			= $useRszImg,
			imgRszWidth			= $imgRszWidth,
			useCategory			= $useCategory,
			categories			= '$categories',
			useWordFilter		= $useWordFilter,
			badWords			= '$badWords',

			levelList			= $levelList,
			levelView			= $levelView,
			levelSecret			= $levelSecret,
			levelWrite			= $levelWrite,
			levelReply			= $levelReply,
			levelMemoWrite		= $levelMemoWrite,
			levelNoticeWrite	= $levelNoticeWrite,
			levelUseHtml		= $levelUseHtml,
			levelDelete			= $levelDelete,
			editDate			= ".mktime();

	switch($applyMode) {
		case "all" : //전체게시판 적용
			$query .= ";";
			break;
		case "define" : //선택된 항목만 적용
			$aidxEndNum = intval($_POST["aidxEndNum"]);
			if($aidxEndNum>1) {
				$chk = false;
				$query .= " WHERE ";
				for($i=1; $i<=$aidxEndNum-1; $i++) {
					${"aidx_".$i} = trim($_POST["aidx_".$i]);
					if(${"aidx_".$i}) {
						$query .= " aidx=".${"aidx_".$i}." OR ";
						$chk = true;
					}
				}
				if(!$chk) Error("설정을 적용할 게시판이 선택되지 않았습니다.");
				$query = substr($query,0,strlen(trim($query))-2); //남는 OR문 제거
				$query .= ";";
			}
			break;
		case "this" : //현재게시판만 적용
			$query .= " WHERE aidx={$aidx};";
			break;
		default :
			$query .= " WHERE aidx={$aidx};";
			break;
	}

	//echo "$query<br /><br />";
	mysql_query($query) or Error(mysql_error());


	/////현재 게시판 적용항목
	$query = "UPDATE {$_table_id_admin} SET
			boardName			= '{$boardName}',
			combinedFileName	= '{$combinedFileName}',
			skin				= '{$skin}',
			adminPasswd			= '{$adminPasswd}',
			adminIDs			= '{$adminIDs}',
			todayCount			= '{$todayAccess}',
			totalCount			= '{$totalAccess}'
			WHERE aidx={$aidx};";

	//echo "$query<br /><br />";
	mysql_query($query) or Error(mysql_error());


	/////개별 모두적용항목
	$query = "";

	if($applyAll_bgImage			) $query .= " bgImage				= '$bgImage', ";
	if($applyAll_bgColor			) $query .= " bgColor				= '$bgColor', ";
	if($applyAll_width				) $query .= " width					= $width, ";
	if($applyAll_subjectLimit		) $query .= " subjectLimit			= $subjectLimit, ";
	if($applyAll_authorLimit		) $query .= " authorLimit			= $authorLimit, ";
	if($applyAll_contentLimit		) $query .= " contentLimit			= $contentLimit, ";
	if($applyAll_pageSize			) $query .= " pageSize				= $pageSize, ";
	if($applyAll_divPage			) $query .= " divPage				= $divPage, ";
	if($applyAll_allowNoticeNum		) $query .= " allowNoticeNum		= $allowNoticeNum, ";
	if($applyAll_align				) $query .= " align					= '$align', ";

	if($applyAll_headFile			) $query .= " headFile				= '$headFile', ";
	if($applyAll_tailFile			) $query .= " tailFile				= '$tailFile', ";
	if($applyAll_headMsg			) $query .= " headMsg				= '$headMsg', ";
	if($applyAll_tailMsg			) $query .= " tailMsg				= '$tailMsg', ";
	if($applyAll_writeFrmDefMsg		) $query .= " writeFrmDefMsg		= '$writeFrmDefMsg', ";

	if($applyAll_useListAtView		) $query .= " useListAtView			= $useListAtView, ";
	if($applyAll_useMemo			) $query .= " useMemo				= $useMemo, ";
	if($applyAll_useReply			) $query .= " useReply				= $useReply, ";
	if($applyAll_useTrackback		) $query .= " useTrackback			= $useTrackback, ";
	if($applyAll_useRssFeed			) $query .= " useRssFeed			= $useRssFeed, ";
	if($applyAll_useAutoLink		) $query .= " useAutoLink			= $useAutoLink, ";
	if($applyAll_usePreview			) $query .= " usePreview			= $usePreview, ";
	if($applyAll_useSiteLink1		) $query .= " useSiteLink1			= $useSiteLink1, ";
	if($applyAll_useSiteLink2		) $query .= " useSiteLink2			= $useSiteLink2, ";
	if($applyAll_useSecret			) $query .= " useSecret				= $useSecret, ";
	if($applyAll_useBlockSpam		) $query .= " useBlockSpam			= $useBlockSpam, ";
	if($applyAll_useBlockAnyLink	) $query .= " useBlockAnyLink		= $useBlockAnyLink, ";
	if($applyAll_useHideButtons		) $query .= " useHideButtons		= $useHideButtons, ";
	if($applyAll_useViewClientIp	) $query .= " useViewClientIp		= $useViewClientIp, ";
	if($applyAll_useViewClientInfo	) $query .= " useViewClientInfo		= $useViewClientInfo, ";
	if($applyAll_editMode			) $query .= " editMode				= '$editMode', ";
	if($applyAll_useHtml			) $query .= " useHtml				= $useHtml, ";
	if($applyAll_allowTags			) $query .= " allowTags				= '$allowTags', ";
	if($applyAll_useAttachFile		) $query .= " useAttachFile			= $useAttachFile, ";
	if($applyAll_fileMaxLimit		) $query .= " fileMaxLimit			= $fileMaxLimit, ";
	if($applyAll_fileMaxNum			) $query .= " fileMaxNum			= $fileMaxNum, ";
	if($applyAll_allowExts			) $query .= " allowExts				= '$allowExts', ";
	if($applyAll_dataPath			) $query .= " dataPath				= '$dataPath', ";
	if($applyAll_useExecFile		) $query .= " useExecFile			= $useExecFile, ";
	if($applyAll_allowEmbedFileExts	) $query .= " allowEmbedFileExts	= '$allowEmbedFileExts', ";
	if($applyAll_useNewIcon			) $query .= " useNewIcon			= $useNewIcon, ";
	if($applyAll_termNewIcon		) $query .= " termNewIcon			= $termNewIcon, ";
	if($applyAll_useRszImg			) $query .= " useRszImg				= $useRszImg, ";
	if($applyAll_imgRszWidth		) $query .= " imgRszWidth			= $imgRszWidth, ";
	if($applyAll_useCategory		) $query .= " useCategory			= $useCategory, ";
	if($applyAll_categories			) $query .= " categories			= '$categories', ";
	if($applyAll_useWordFilter		) $query .= " useWordFilter			= $useWordFilter, ";
	if($applyAll_badWords			) $query .= " badWords				= '$badWords', ";

	if($applyAll_levelList			) $query .= " levelList				= $levelList, ";
	if($applyAll_levelView			) $query .= " levelView				= $levelView, ";
	if($applyAll_levelSecret		) $query .= " levelSecret			= $levelSecret, ";
	if($applyAll_levelWrite			) $query .= " levelWrite			= $levelWrite, ";
	if($applyAll_levelReply			) $query .= " levelReply			= $levelReply, ";
	if($applyAll_levelMemoWrite		) $query .= " levelMemoWrite		= $levelMemoWrite, ";
	if($applyAll_levelNoticeWrite	) $query .= " levelNoticeWrite		= $levelNoticeWrite, ";
	if($applyAll_levelUseHtml		) $query .= " levelUseHtml			= $levelUseHtml, ";
	if($applyAll_levelDelete		) $query .= " levelDelete			= $levelDelete, ";

	if($query) {
		$query = substr($query,1,strlen(trim($query))-1); //쿼리문에서 뒷부분의 남는 쉼표(,)제거
		$query = "UPDATE {$_table_id_admin} SET {$query};";
		//echo "$query<br /><br />";exit;
		mysql_query($query) or Error(mysql_error());
	}

	//if($combinedDesign) MovePage($PHP_SELF); //디자인 포함된 페이지로 이동
	//else MovePage($_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}");	//디자인 없는 페이지 이동

	MovePage("/fsboard/lib/setup.php?mode=List");

}








/////관리자 설정폼
if($mode=="admin"&&!$isPostBack) {
	$query = "SELECT * FROM {$_table_id_admin} WHERE boardId='{$id}' AND aidx={$aidx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$rs = mysql_fetch_array($result);

		$aidx				= $rs["aidx"];
		$boardId			= $rs["boardId"];
		$boardName			= $rs["boardName"];
		$combinedFileName	= $rs["combinedFileName"];
		$adminIDs			= $rs["adminIDs"];				$adminIDs = strip_tags($adminIDs);
		$adminPasswd		= $rs["adminPasswd"];
		$skin				= $rs["skin"];
		$todayCount			= $rs["todayCount"];
		$totalCount			= $rs["totalCount"];
		$totalObj			= $rs["totalObj"];
		$allowNoticeNum		= $rs["allowNoticeNum"];
		$noticeNum			= $rs["noticeNum"];
		$bgColor			= $rs["bgColor"];
		$bgImage			= $rs["bgImage"];
		$width				= $rs["width"];
		$pageSize			= $rs["pageSize"];
		$divPage			= $rs["divPage"];
		$subjectLimit		= $rs["subjectLimit"];
		$authorLimit		= $rs["authorLimit"];
		$contentLimit		= $rs["contentLimit"];
		$align				= $rs["align"];
		$headFile			= $rs["headFile"];
		$tailFile			= $rs["tailFile"];
		$headMsg			= $rs["headMsg"];				$headMsg = $headMsg;
		$tailMsg			= $rs["tailMsg"];				$tailMsg = $tailMsg;
		$writeFrmDefMsg		= $rs["writeFrmDefMsg"];		$writeFrmDefMsg = StripHtmlChars($writeFrmDefMsg);
		$editMode			= $rs["editMode"];
		$useListAtView		= $rs["useListAtView"];
		$useReply			= $rs["useReply"];
		$useTrackback		= $rs["useTrackback"];
		$useRssFeed			= $rs["useRssFeed"];
		$useMemo			= $rs["useMemo"];
		$useAutoLink		= $rs["useAutoLink"];
		$usePreview			= $rs["usePreview"];
		$useSiteLink1		= $rs["useSiteLink1"];
		$useSiteLink2		= $rs["useSiteLink2"];
		$useSecret			= $rs["useSecret"];
		$useBlockSpam		= $rs["useBlockSpam"];
		$useBlockAnyLink	= $rs["useBlockAnyLink"];
		$useHideButtons		= $rs["useHideButtons"];
		$useViewClientIp	= $rs["useViewClientIp"];
		$useViewClientInfo	= $rs["useViewClientInfo"];
		$useExecFile		= $rs["useExecFile"];
		$useRszImg			= $rs["useRszImg"];
		$imgRszWidth		= $rs["imgRszWidth"];
		$useNewIcon			= $rs["useNewIcon"];
		$termNewIcon		= $rs["termNewIcon"];
		$useHtml			= $rs["useHtml"];
		$allowEmbedFileExts	= $rs["allowEmbedFileExts"];	$allowEmbedFileExts = StripHtmlChars($allowEmbedFileExts);
		$allowTags			= $rs["allowTags"];				$allowTags = StripHtmlChars($allowTags);
		$useAttachFile		= $rs["useAttachFile"];
		$fileMaxLimit		= $rs["fileMaxLimit"];
		$fileMaxNum			= $rs["fileMaxNum"];
		$allowExts			= $rs["allowExts"];
		$dataPath			= $rs["dataPath"];
		$useCategory		= $rs["useCategory"];
		$categories			= $rs["categories"];			$categories = StripHtmlChars($categories);
		$useWordFilter		= $rs["useWordFilter"];
		$badWords			= $rs["badWords"];				$badWords = StripHtmlChars($badWords);
		$levelList			= $rs["levelList"];
		$levelView			= $rs["levelView"];
		$levelSecret		= $rs["levelSecret"];
		$levelWrite			= $rs["levelWrite"];
		$levelReply			= $rs["levelReply"];
		$levelMemoWrite		= $rs["levelMemoWrite"];
		$levelNoticeWrite	= $rs["levelNoticeWrite"];
		$levelUseHtml		= $rs["levelUseHtml"];
		$levelDelete		= $rs["levelDelete"];
		$regDate			= $rs["regDate"];
		$editDate			= $rs["editDate"];
		$currDate			= $rs["currDate"];

		mysql_free_result($result);
	} else Error("데이터가 없습니다.");


	//버튼설정
	$btnSubmit = "<a href=\"javascript:sendit('{$id}','adminsave');\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a>";
	$btnCancel = "<a href=\"javascript:window.history.go(-2);\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>";
	$btnList = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("list")."\"><img src=\"{$FSBOARD_PATH}/img/btn/list.gif\" alt=\"목록\" /></a>";
	$btnSync = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("sync")."\" class=\"deflnk\">SYNC</a>";
	$btnSetupList = "<a href=\"".$_SERVER["PHP_SELF"]."?".QryStr("setup")."\" class=\"deflnk\">SETUP</a>";

	//$frmWidth = "600";
	$frmWidth = "95%";






////////////////////////////////////////////////////////////////////////////////////////////////////End of preprocess
ContentTop();
?>






<style type="text/css">
.defstyle { font-size:12px; font-family:돋움, Dottum, Verdana; color:#000000; border-collapse:collapse; }
.deflnk { text-decoration: none; font-size: 11px; font-family: Tahoma,Verdana; }
.titlebar { background-color:#fafb7; color:#000000; padding:3px; font-weight:bold; }
.divbar1 { text-align:left; padding:3px 3px 3px 5px; }
.divbar2 {}
.txtbox { border:1px solid #e1e1e1; background-color:#fdfefc; }
.txtbox1 { border:1px solid #e1e1e1; background-color:#fdfefc; width:100%; }
.txtbox2 { border:1px solid #e1e1e1; background-color:#fdfefc; width:100%; word-break:break-all; }
</style>
<table width="<?=$frmWidth?>" align="center" cellpadding="0" cellspacing="0" class="defstyle">
	<tr>
		<td valign="bottom">
			<? echo "{$btnSubmit} {$btnList} &nbsp;Session: <b>".$_SESSION["MemId"]."</b>, Articles: <b>{$totalObj}</b>"; ?>
		</td>
		<td align="right" valign="bottom">
<?
	echo "Today: <b>{$todayCount}</b>, Total: <b>{$totalCount}</b> &nbsp; ";
	//echo " {$btnSync} {$btnSetupList} ";
?>
		</td>
	</tr>
</table>

<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 관리자 설정</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">DB테이블</td>
		<td class="divbar2">
			<table class="defstyle">
				<tr>
					<td><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /> 게시판 아이디</td>
					<td>&nbsp; &nbsp; <b style="color:black;"><?=$id?></td>
				</tr>
				<tr>
					<td><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /> 게시판 DB테이블명</td>
					<td>&nbsp; <b style="color:black;"><?=$_table_id_board?></td>
				</tr>
				<tr>
					<td><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /> 코멘트 DB테이블명</td>
					<td>&nbsp; <b style="color:silver;"><?=$_table_id_comment?></td>
				</tr>
				<tr>
					<td><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /> 트랙백 DB테이블명</td>
					<td>&nbsp; <b style="color:silver;"><?=$_table_id_trackback?></td>
				</tr>
				<tr>
					<td><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /> 태그 DB테이블명</td>
					<td>&nbsp; <b style="color:silver;"><?=$_table_id_tagcloud?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="divbar1">게시판 이름</td>
		<td class="divbar2"><input type="text" size="40" name="boardName" class="txtbox" value="<?=$boardName?>" /> <nobr>(관리용 이름. 현재 게시판에만 적용됨)</nobr></td>
	</tr>
	<tr>
		<td class="divbar1">스킨</td>
		<td class="divbar2">
			<select name="skin">
				<option value="">스킨선택</option>
<?
/*
	$skin_dir = dir($FSBOARD_ROOT."/skin/");
	while($entry=$skin_dir->read()) {
		if($entry!="." && $entry!="..") {
			if(is_dir("$FSBOARD_ROOT/skin/{$entry}"))
				echo "<option value=\"{$entry}\"".($skin==$entry?" selected=\"selected\"":"").">{$entry}</option>\n";
		}
	}
*/
	$i = 0;
	$skin_list = array();
	$skin_dir = dir($FSBOARD_ROOT."/skin/");
	while($entry=$skin_dir->read()) {
		$skin_list[$i] = $entry;
		$i++;
	}
	sort($skin_list);
	for($i=0; $i<count($skin_list); $i++) {
		if($skin_list[$i]!="." && $skin_list[$i]!="..") {
			if(is_dir("$FSBOARD_ROOT/skin/".$skin_list[$i]))
				echo "<option value=\"".$skin_list[$i]."\"".($skin==$skin_list[$i]?" selected=\"selected\"":"").">".$skin_list[$i]."</option>\n";
		}
	}
?>
			</select> <nobr>(게시판 특성에 맞게 선택(자료실 포토게시판 등), 현재 게시판에만 적용됨)</nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">현재 게시판<br />관리자 암호</td>
		<td class="divbar2">
			<table class="defstyle">
				<tr>
					<td colspan="2"><nobr><img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> 비로그인 상태에서 수정/삭제 등의 관리 암호(현재 게시판에만 적용됨)</nobr></td>
				</tr>
				<tr>
					<td nowrap>암호</td>
					<td><input type="password" size="40" name="adminPasswd1" class="txtbox" /> <nobr>(4자리 이상, 영문/숫자/조합, 입력하지 않으면 이전암호 저장)</nobr></td>
				</tr>
				<tr>
					<td nowrap>확인</td>
					<td><input type="password" size="40" name="adminPasswd2" class="txtbox" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="divbar1">현재 게시판<br />관리자 아이디</td>
		<td class="divbar2">
			<textarea rows="2" cols="80" name="adminIDs" class="txtbox2"><?=$adminIDs?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].adminIDs,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].adminIDs,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(관리자 아이디 목록. 쉼표(,)로 구분. 현재 게시판에만 적용됨)</nobr> <a href="<?=$FSBOARD_PATH?>/lib/members.php?mode=Admin.MemList" onclick="window.open(this.href,'_blank'); return false;"><img src="<?=$FSBOARD_PATH?>/img/clip/doc3.gif" alt="icon" /><b>회원관리</b></a>
		</td>
	</tr>
	<tr>
		<td class="divbar1">게시판이 들어가는 디자인파일명</td>
		<td class="divbar2">
			<input type="text" size="50" name="combinedFileName" class="txtbox1" value="<?=$combinedFileName?>" /><br />
			<nobr>(게시판과 결합되어 보여지는 디자인파일명, 예: /intro/design.html)</nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">방문자수 바꾸기</td>
		<td class="divbar2">
			<table class="defstyle">
				<tr>
					<td>오늘:</td>
					<td><input type="text" size="20" name="todayAccess" class="txtbox" /> <nobr>(현재 게시판에만 적용됨. 입력하지 않으면 이전값 사용)</nobr></td>
				</tr>
				<tr>
					<td>전체:</td>
					<td><input type="text" size="20" name="totalAccess" class="txtbox" /> <nobr>(현재 게시판에만 적용됨. 입력하지 않으면 이전값 사용)</nobr></td>
				</tr>
			</table>
		</td>
	</tr>
</table>


<br />
<br />


<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 게시판 기본 설정</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">게시판 배경색</td>
		<td class="divbar2"><input type="text" size="15" name="bgColor" class="txtbox" value="<?=$bgColor?>" /> <nobr>(비워두면 지정안함)</nobr> <nobr><input type="checkbox" name="applyAll_bgColor" id="applyAll_bgColor" value="1" /><label for="applyAll_bgColor">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">배경이미지</td>
		<td class="divbar2"><input type="text" size="40" name="bgImage" class="txtbox1" value="<?=$bgImage?>" /><br /><nobr>(경로 포함 파일명, 예: /images/back.gif)</nobr> <nobr><input type="checkbox" name="applyAll_bgImage" id="applyAll_bgImage" value="1" /><label for="applyAll_bgImage">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">게시판 가로크기</td>
		<td class="divbar2"><input type="text" size="5" name="width" class="txtbox" value="<?=$width?>" /> <nobr>(픽셀단위, 100이하는 퍼센트 단위)</nobr> <nobr><input type="checkbox" name="applyAll_width" id="applyAll_width" value="1" /><label for="applyAll_width">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">제목 글자 제한</td>
		<td class="divbar2"><input type="text" size="5" name="subjectLimit" class="txtbox" value="<?=$subjectLimit?>" /> <nobr>(목록에서 지정된 길이 이상의 제목글은 ... 로 나머지 표시, 0은 사용안함)</nobr> <nobr><input type="checkbox" name="applyAll_subjectLimit" id="applyAll_subjectLimit" value="1" /><label for="applyAll_subjectLimit">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">이름 글자 제한</td>
		<td class="divbar2"><input type="text" size="5" name="authorLimit" class="txtbox" value="<?=$authorLimit?>" /> <nobr>(목록에서 지정된 길이 이상의 작성자는 ... 로 나머지 표시, 0은 사용안함)</nobr> <nobr><input type="checkbox" name="applyAll_authorLimit" id="applyAll_authorLimit" value="1" /><label for="applyAll_authorLimit">모든 게시판에 적용<nobr></td>
	</tr>
	<tr>
		<td class="divbar1">내용 글자 제한</td>
		<td class="divbar2"><input type="text" size="5" name="contentLimit" class="txtbox" value="<?=$contentLimit?>" /> <nobr>(목록에서 지정된 길이 이상의 미리보기 내용글은 ... 로 나머지 표시, 0은 사용안함)</nobr> <nobr><input type="checkbox" name="applyAll_contentLimit" id="applyAll_contentLimit" value="1" /><label for="applyAll_contentLimit">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">페이지당 게시물수</td>
		<td class="divbar2"><nobr><input type="text" size="3" name="pageSize" class="txtbox" value="<?=$pageSize?>" />개</nobr> <nobr>(목록에서 보여지는 게시물의 제목 라인 줄수)</nobr> <nobr><input type="checkbox" name="applyAll_pageSize" id="applyAll_pageSize" value="1" /><label for="applyAll_pageSize">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">페이지당 페이지수</td>
		<td class="divbar2"><nobr><input type="text" size="3" name="divPage" class="txtbox" value="<?=$divPage?>" />개</nobr> <nobr>(목록에서 보여지는 페이지의 바로가기 개수)</nobr> <nobr><input type="checkbox" name="applyAll_divPage" id="applyAll_divPage" value="1" /><label for="applyAll_divPage">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">공지 게시물수</td>
		<td class="divbar2">
			<select name="allowNoticeNum">
<?for($i=1;$i<=10;$i++){ echo "<option value=\"{$i}\"".($allowNoticeNum==$i?" selected=\"selected\"":"").">{$i}개</option>\n"; }?>
			</select> <nobr>(목록에 보여지는 공지 게시물의 제목 수)</nobr> <nobr><input type="checkbox" name="applyAll_allowNoticeNum" id="applyAll_allowNoticeNum" value="1" /><label for="applyAll_allowNoticeNum">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">전체 정렬 방식</td>
		<td class="divbar2">
			<nobr><input type="radio" name="align" value="center"<?=($align=="center"?" checked=\"checked\"":"")?> />가운데</nobr>
			<nobr><input type="radio" name="align" value="left"<?=($align=="left"?" checked=\"checked\"":"")?> />왼쪽</nobr>
			<nobr><input type="radio" name="align" value="right"<?=($align=="right"?" checked=\"checked\"":"")?> />오른쪽</nobr>
			<nobr><input type="checkbox" name="applyAll_align" id="applyAll_align" value="1" /><label for="applyAll_align">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
</table>


<br />
<br />


<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 게시판에 포함될 내용 설정</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">상단 포함파일</td>
		<td class="divbar2"><input type="text" size="80" name="headFile" class="txtbox1" value="<?=$headFile?>" /><br /><nobr>(게시판 상단에 포함될 파일명 절대경로, 예: /inc/head.html)</nobr> <nobr><input type="checkbox" name="applyAll_headFile" id="applyAll_headFile" value="1" /><label for="applyAll_headFile">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">하단 포함파일</td>
		<td class="divbar2"><input type="text" size="80" name="tailFile" class="txtbox1" value="<?=$tailFile?>" /><br /><nobr>(게시판 하단에 포함될 파일명 절대경로, 예: /inc/tail.html)</nobr> <nobr><input type="checkbox" name="applyAll_tailFile" id="applyAll_tailFile" value="1" /><label for="applyAll_tailFile">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">상단 메시지</td>
		<td class="divbar2">
			<textarea rows="3" cols="80" name="headMsg" class="txtbox2"><?=StripHtmlChars($headMsg)?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].headMsg,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].headMsg,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(게시판 상단에 표시되는 내용, 텍스트 또는 태그, 65KB 이하)</nobr> <nobr><input type="checkbox" name="applyAll_headMsg" id="applyAll_headMsg" value="1" /><label for="applyAll_headMsg">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">하단 메시지</td>
		<td class="divbar2">
			<textarea rows="3" cols="80" name="tailMsg" class="txtbox2"><?=StripHtmlChars($tailMsg)?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].tailMsg,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].tailMsg,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(게시판 상단에 표시되는 내용, 텍스트 또는 태그, 65KB 이하)</nobr> <nobr><input type="checkbox" name="applyAll_tailMsg" id="applyAll_tailMsg" value="1" /><label for="applyAll_tailMsg">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">기본 본문 메시지</td>
		<td class="divbar2">
			<textarea rows="3" cols="80" name="writeFrmDefMsg" class="txtbox2"><?=$writeFrmDefMsg?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].writeFrmDefMsg,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].writeFrmDefMsg,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(글쓰기 내용에 기본으로 포함되는 메시지, 65KB 이하)</nobr> <nobr><input type="checkbox" name="applyAll_writeFrmDefMsg" id="applyAll_writeDefMsg" value="1" /><label for="applyAll_writeDefMsg">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
</table>


<br />
<br />


<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 게시판 기능 설정</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">전체목록 출력</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useListAtView" value="1"<?=($useListAtView?" checked=\"checked\"":"")?> />글내용 보기에서 아래에 전체 목록 표시</nobr> <nobr><input type="checkbox" name="applyAll_useListAtView" id="applyAll_useListAtView" value="1" /><label for="applyAll_useListAtView">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">댓글 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useMemo" value="1"<?=($useMemo?" checked=\"checked\"":"")?> />댓글 기능 사용</nobr> <nobr>(내용에 간단한 답글을 메모할수 있는 기능)</nobr> <nobr><input type="checkbox" name="applyAll_useMemo" id="applyAll_useMemo" value="1" /><label for="applyAll_useMemo">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">답변 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useReply" value="1"<?=($useReply?" checked=\"checked\"":"")?> />답변글 쓰기 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useReply" id="applyAll_useReply" value="1" /><label for="applyAll_useReply">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">트랙백 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useTrackback" value="1"<?=($useTrackback?" checked=\"checked\"":"")?> />트랙백 보내기 및 받기 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useTrackback" id="applyAll_useTrackback" value="1" /><label for="applyAll_useTrackback">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">RSS 피드</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useRssFeed" value="1"<?=($useRssFeed?" checked=\"checked\"":"")?> />RSS 2.0 피드 제공 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useRssFeed" id="applyAll_useRssFeed" value="1" /><label for="applyAll_useRssFeed">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">자동링크</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useAutoLink" value="1"<?=($useAutoLink?" checked=\"checked\"":"")?> />글 내용보기 및 댓글에서 자동링크 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useAutoLink" id="applyAll_useAutoLink" value="1" /><label for="applyAll_useAutoLink">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">미리보기</td>
		<td class="divbar2"><nobr><input type="checkbox" name="usePreview" value="1"<?=($usePreview?" checked=\"checked\"":"")?> />미리보기 기능 사용</nobr> <nobr>(제목에 마우스커서를 올리면 간단하게 내용이 표시됨)</nobr> <nobr><input type="checkbox" name="applyAll_usePreview" id="applyAll_usePreview" value="1" /><label for="applyAll_usePreview">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">사이트링크 1</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useSiteLink1" value="1"<?=($useSiteLink1?" checked=\"checked\"":"")?> />관련 사이트링크 기능 사용</nobr> <nobr>(특수게시판에서는 다른용도로 사용)</nobr> <nobr><input type="checkbox" name="applyAll_useSiteLink1" id="applyAll_useSiteLink1" value="1" /><label for="applyAll_useSiteLink1">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">사이트링크 2</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useSiteLink2" value="1"<?=($useSiteLink2?" checked=\"checked\"":"")?> />관련 사이트링크 기능 사용</nobr> <nobr>(특수게시판에서는 다른용도로 사용)</nobr> <nobr><input type="checkbox" name="applyAll_useSiteLink2" id="applyAll_useSiteLink2" value="1" /><label for="applyAll_useSiteLink2">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">비밀글 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useSecret" value="1"<?=($useSecret?" checked=\"checked\"":"")?> />비밀글 기능 사용</nobr> <nobr>(관리자와 암호를 아는 사람만 볼수 있음)</nobr> <nobr><input type="checkbox" name="applyAll_useSecret" id="applyAll_useSecret" value="1" /><label for="applyAll_useSecret">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">스팸방지 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useBlockSpam" value="1"<?=($useBlockSpam?" checked=\"checked\"":"")?> />스팸글 차단 기능 사용</nobr> <nobr>(글등록후 30초이상 지나야만 다음 글쓰기 가능)</nobr> <nobr><input type="checkbox" name="applyAll_useBlockSpam" id="applyAll_useBlockSpam" value="1" /><label for="applyAll_useBlockSpam">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">무단링크방지 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useBlockAnyLink" value="1"<?=($useBlockAnyLink?" checked=\"checked\"":"")?> />무단링크 방지 기능 사용</nobr> <nobr>(다른 사이트에서 자료를 무단으로 링크 거는것을 방지)</nobr> <nobr><input type="checkbox" name="applyAll_useBlockAnyLink" id="applyAll_useBlockAnyLink" value="1" /><label for="applyAll_useBlockAnyLink">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">버튼 숨김</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useHideButtons" value="1"<?=($useHideButtons?" checked=\"checked\"":"")?> />권한이 없는 불필요한 버튼 모두 숨김</nobr> <nobr><input type="checkbox" name="applyAll_useHideButtons" id="applyAll_useHideButtons" value="1" /><label for="applyAll_useHideButtons">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">작성자 IP정보</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useViewClientIp" value="1"<?=($useViewClientIp?" checked=\"checked\"":"")?> />글내용에서 작성자의 IP주소를 표시</nobr> <nobr><input type="checkbox" name="applyAll_useViewClientIp" id="applyAll_useViewClientIp" value="1" /><label for="applyAll_useViewClientIp">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">작성자 시스템정보</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useViewClientInfo" value="1"<?=($useViewClientInfo?" checked=\"checked\"":"")?> />글내용에서 작성자의 시스템 정보를 표시</nobr> <nobr><input type="checkbox" name="applyAll_useViewClientInfo" id="applyAll_useViewClientInfo" value="1" /><label for="applyAll_useViewClientInfo">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">NEW 표시 방법</td>
		<td class="divbar2">
			<nobr><input type="radio" name="useNewIcon" value="2"<?=$useNewIcon==2?" checked=\"checked\"":"";?> />24시간 단위</nobr>
			<nobr><input type="radio" name="useNewIcon" value="1"<?=$useNewIcon==1?" checked=\"checked\"":"";?> />자정(0시) 단위</nobr>
			<nobr><input type="radio" name="useNewIcon" value="0"<?=$useNewIcon==0?" checked=\"checked\"":"";?> />사용안함</nobr>
			<nobr><input type="checkbox" name="applyAll_useNewIcon" id="applyAll_useNewIcon" value="1" /><label for="applyAll_useNewIcon">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">NEW 표시 기간</td>
		<td class="divbar2">
			<select name="termNewIcon">
<?
for($i=1; $i<=365; $i++) {
	echo "<option value=\"$i\"".($termNewIcon==$i?" selected=\"selected\"":"").">{$i}일</option>\n";
}
?>
			</select> <nobr>(목록에서 NEW 표시 지속기간, 게시글 및 댓글)</nobr>
			<nobr><input type="checkbox" name="applyAll_termNewIcon" id="applyAll_termNewIcon" value="1" /><label for="applyAll_termNewIcon">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">글쓰기 모드</td>
		<td class="divbar2">
			<select name="editMode">
				<option value="text"<?=($editMode=="text"?" selected=\"selected\"":"")?>>텍스트</option>
				<option value="html"<?=($editMode=="html"?" selected=\"selected\"":"")?>>HTML</option>
				<option value="br"<?=($editMode=="br"?" selected=\"selected\"":"")?>>HTML+&lt;br></option>
				<option value="editor"<?=($editMode=="editor"?" selected=\"selected\"":"")?>>웹에디터</option>
			</select> <nobr>(글쓰기시 내용부분의 기본설정 문서모드)</nobr>
			<nobr><input type="checkbox" name="applyAll_editMode" id="applyAll_editMode" value="1" /><label for="applyAll_editMode">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">HTML사용</td>
		<td class="divbar2">
			<nobr><input type="radio" name="useHtml" id="usehtml1" value="part"<?=($useHtml=="part"?" checked=\"checked\"":"")?> /><label for="usehtml1" title="허용할 태그의 항목만 허용합니다">부분허용</label></nobr>
			<nobr><input type="radio" name="useHtml" id="usehtml2" value="permit"<?=($useHtml=="permit"?" checked=\"checked\"":"")?> /><label for="usehtml2" title="이벤트 스크립트를 제외한 모든 태그를 허용합니다.">모두허용</label></nobr>
			<nobr><input type="radio" name="useHtml" id="usehtml3" value="block"<?=($useHtml=="block"?" checked=\"checked\"":"")?> /><label for="usehtml3" title="모든 태그를 허용하지 않습니다.">모두막기</label></nobr>
			<nobr><input type="radio" name="useHtml" id="usehtml4" value="perfect"<?=($useHtml=="perfect"?" checked=\"checked\"":"")?> /><label for="usehtml4" title="태그 필터링을 하지 않습니다.">완전허용</label></nobr>
			<nobr><input type="checkbox" name="applyAll_useHtml" id="applyAll_useHtml" value="1" /><label for="applyAll_useHtml">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">허용 HTML태그</td>
		<td class="divbar2">
			<img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> <span style="color:red;">허용한 HTML태그 이외에의 HTML태그는 내용에 그대로 보여집니다</span>
			<textarea rows="3" cols="80" name="allowTags" class="txtbox2"><?=$allowTags?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].allowTags,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].allowTags,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(내용에 허용할 태그 목록, 쉼표(,)로 구분)</nobr> <nobr><input type="checkbox" name="applyAll_allowTags" id="applyAll_allowTags" value="1" /><label for="applyAll_allowTags">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">파일업로드 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useAttachFile" value="1"<?=($useAttachFile?" checked=\"checked\"":"")?> />자료실 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useAttachFile" id="applyAll_useAttachFile" value="1" /><label for="applyAll_useAttachFile">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">파일용량 제한</td>
		<td class="divbar2">
			<select name="fileMaxLimit">
<?
for($i=0; $i<=1000; $i++) {
	if($i>100 && $i%10) continue;
	echo "<option value=\"".pow(2,20)*$i."\"".($fileMaxLimit==pow(2,20)*$i?" selected=\"selected\"":"").">".($i==0?"올릴수없음":"{$i}MB")."</option>\n";
}
?>
			</select> <nobr>(최대한 올릴수 있는 파일의 용량)</nobr>
			<nobr><input type="checkbox" name="applyAll_fileMaxLimit" id="applyAll_fileMaxLimit" value="1" /><label for="applyAll_fileMaxLimit">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">파일 업로드수 제한</td>
		<td class="divbar2">
			<select name="fileMaxNum">
<?
for($i=1; $i<=30; $i++) {
	echo "<option value=\"$i\"".($fileMaxNum==$i?" selected=\"selected\"":"").">{$i}개</option>\n";
}
?>
			</select> <nobr>(동시에 업로드할수 있는 파일의 갯수)</nobr>
			<nobr><input type="checkbox" name="applyAll_fileMaxNum" id="applyAll_fileMaxNum" value="1" /><label for="applyAll_fileMaxNum">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">업로드 허용 확장자</td>
		<td class="divbar2">
			<img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> <span style="color:red;">허용한 확장명 이외의 파일은 업로드할 수 없습니다.</span>
			<textarea rows="2" cols="80" name="allowExts" class="txtbox2"><?=$allowExts?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].allowExts,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].allowExts,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(첨부파일 업로드시 허용할 확장자 목록, 쉼표(,)로 구분, <span style="color:green;">비워두면 전부허용</span>)</nobr> <nobr><input type="checkbox" name="applyAll_allowExts" id="applyAll_allowExts" value="1" /><label for="applyAll_allowExts">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">업로드 경로</td>
		<td class="divbar2"><input type="text" size="80" name="dataPath" class="txtbox1" value="<?=$dataPath?>" /><br /><nobr>(파일이 저장될 디렉토리의 웹 절대경로), 변경시 해당 디렉토리의 권한설정 필요)</nobr> <nobr><input type="checkbox" name="applyAll_dataPath" id="applyAll_dataPath" value="1" /><label for="applyAll_dataPath">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">파일 자동실행</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useExecFile" value="1"<?=($useExecFile?" checked=\"checked\"":"")?> />글내용에서 첨부파일 자동실행(이미지, 동영상, 음악파일 등)</nobr> <nobr><input type="checkbox" name="applyAll_useExecFile" id="applyAll_useExecFile" value="1" /><label for="applyAll_useExecFile">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">파일 자동실행<br />허용 확장자</td>
		<td class="divbar2">
			<textarea rows="2" cols="80" name="allowEmbedFileExts" class="txtbox2"><?=$allowEmbedFileExts?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].allowEmbedFileExts,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].allowEmbedFileExts,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(글내용에서 자동실행 파일의 허용 확장자, 쉼표(,)로 구분, <span style="color:green;">비워두면 전부허용</span>)</nobr> <nobr><input type="checkbox" name="applyAll_allowEmbedFileExts" id="applyAll_allowEmbedFileExts" value="1" /><label for="applyAll_allowEmbedFileExts">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">이미지 보정</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useRszImg" value="1"<?=($useRszImg?" checked=\"checked\"":"")?> />글내용에서 큰이미지 크기를 자동으로 줄임</nobr> <nobr><input type="checkbox" name="applyAll_useRszImg" id="applyAll_useRszImg" value="1" /><label for="applyAll_useRszImg">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">이미지 보정크기</td>
		<td class="divbar2"><nobr><input type="text" size="5" name="imgRszWidth" class="txtbox" value="<?=$imgRszWidth?>" />px</nobr> <nobr>(글내용에서 큰이미지의 WITH를 자동 조절할 사이즈, 0은 게시판 WITH에 자동)</nobr> <nobr><input type="checkbox" name="applyAll_imgRszWidth" id="applyAll_imgRszWidth" value="1" /><label for="applyAll_imgRszWidth">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">카테고리 기능</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useCategory" value="1"<?=($useCategory?" checked=\"checked\"":"")?> />카테고리 기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useCategory" id="applyAll_useCategory" value="1" /><label for="applyAll_useCategory">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">카테고리 목록</td>
		<td class="divbar2">
			<img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> <span style="color:red;">카테고리 분류가 목록에 나타납니다.</span><br />
			<textarea rows="3" cols="80" name="categories" class="txtbox2"><?=$categories?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].categories,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].categories,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(사용할 카테고리명, 쉼표(,)로 구분)</nobr> <nobr><input type="checkbox" name="applyAll_categories" id="applyAll_categories" value="1" /><label for="applyAll_categories">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">불량단어 필터링</td>
		<td class="divbar2"><nobr><input type="checkbox" name="useWordFilter" value="1"<?=($useWordFilter?" checked=\"checked\"":"")?> />불량단어 필터링기능 사용</nobr> <nobr><input type="checkbox" name="applyAll_useWordFilter" id="applyAll_useWordFilter" value="1" /><label for="applyAll_useWordFilter">모든 게시판에 적용</label></nobr></td>
	</tr>
	<tr>
		<td class="divbar1">불량단어 목록</td>
		<td class="divbar2">
			<textarea rows="3" cols="80" name="badWords" class="txtbox2"><?=$badWords?></textarea><br />
			<nobr><a href="javascript:formResize(document.forms['__ctl'].badWords,3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_down.gif" alt="텍스트영역 늘리기" /></a><a href="javascript:formResize(document.forms['__ctl'].badWords,-3);"><img src="<?=$FSBOARD_PATH?>/img/clip/dbl_arrow_up.gif" alt="텍스트영역 줄이기" /></a></nobr>
			<nobr>(글쓰기시 걸러낼 불량단어 목록, 쉼표(,)로 구분)</nobr> <nobr><input type="checkbox" name="applyAll_badWords" id="applyAll_badWords" value="1" /><label for="applyAll_badWords">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
</table>


<br />
<br />


<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 게시판 권한 설정</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">레벨별 권한</td>
		<td class="divbar2">
			<img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> <span style="color:red;">레벨범위는 1~<?=sizeof($mem_part_element)?> 이며 숫자가 작을수록 높은 권한입니다.</span><br />
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" valign="bottom" height="25"><img src="<?=$FSBOARD_PATH?>/img/clip/lock.gif" alt="Lock" /> <b>보기 권한</td>
	</tr>
	<tr>
		<td class="divbar1">목록보기 권한</td>
		<td class="divbar2">
			<select name="levelList">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelList==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(게시판 목록보기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelList" id="applyAll_levelList" value="1" /><label for="applyAll_levelList">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">내용보기 권한</td>
		<td class="divbar2">
			<select name="levelView">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelView==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(게시판 내용보기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelView" id="applyAll_levelView" value="1" /><label for="applyAll_levelView">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" valign="bottom" height="25"><img src="<?=$FSBOARD_PATH?>/img/clip/lock.gif" alt="Lock" /> <b>쓰기 권한</b></td>
	</tr>
	<tr>
		<td class="divbar1">글쓰기 권한</td>
		<td class="divbar2">
			<select name="levelWrite">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelWrite==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(글쓰기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelWrite" id="applyAll_levelWrite" value="1" /><label for="applyAll_levelWrite">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">답변쓰기 권한</td>
		<td class="divbar2">
			<select name="levelReply">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelReply==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(답변글쓰기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelReply" id="applyAll_levelReply" value="1" /><label for="applyAll_levelReply">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">댓글쓰기 권한</td>
		<td class="divbar2">
			<select name="levelMemoWrite">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelMemoWrite==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(댓글쓰기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelMemoWrite" id="applyAll_levelMemoWrite" value="1" /><label for="applyAll_levelMemoWrite">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">HTML사용권한</td>
		<td class="divbar2">
			<select name="levelUseHtml">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelUseHtml==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(글쓰기시 HTML사용 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelUseHtml" id="applyAll_levelUseHtml" value="1" /><label for="applyAll_levelUseHtml">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" valign="bottom" height="25"><img src="<?=$FSBOARD_PATH?>/img/clip/lock.gif" alt="Lock" /> <b>관리 권한</b></td>
	</tr>
	<tr>
		<td class="divbar1">삭제 권한</td>
		<td class="divbar2">
			<select name="levelDelete">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelDelete==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(모든글의 삭제 권한레벨)</nobr>
			<input type="checkbox" name="applyAll_levelDelete" id="applyAll_levelDelete" value="1" /><label for="applyAll_levelDelete">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">비밀글보기 권한</td>
		<td class="divbar2">
			<select name="levelSecret">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelSecret==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> (비밀글보기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelSecret" id="applyAll_levelSecret" value="1" /><label for="applyAll_levelSecret">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
	<tr>
		<td class="divbar1">공지사항쓰기 권한</td>
		<td class="divbar2">
			<select name="levelNoticeWrite">
<?for($i=sizeof($mem_part_element); $i>=1; $i--) { echo "<option value=\"{$i}\"".($levelNoticeWrite==$i?" selected=\"selected\"":"").">".$i." - ".$mem_part_element[$i-1]."</option>\n"; }?>
			</select> <nobr>(공지사항쓰기 권한레벨)</nobr>
			<nobr><input type="checkbox" name="applyAll_levelNoticeWrite" id="applyAll_levelNoticeWrite" value="1" /><label for="applyAll_levelNoticeWrite">모든 게시판에 적용</label></nobr>
		</td>
	</tr>
</table>


<br />
<br />


<table width="<?=$frmWidth?>" align="center" border="1" bordercolor="#E1E1E1" cellpadding="3" cellspacing="1" class="defstyle">
	<tr>
		<td colspan="2" align="center" class="titlebar"><img src="<?=$FSBOARD_PATH?>/img/clip/doc2.gif" alt="icon" /> 설정 저장 방법</td>
	</tr>
	<tr>
		<td width="16%" class="divbar1">게시판 선택</td>
		<td class="divbar2">

			<table width="100%" cellpadding="0" cellspacing="0" class="defstyle">
				<tr>
<?
$query = "SELECT aidx,boardId,boardName FROM {$_table_id_admin} ORDER BY aidx ASC;";
$result = mysql_query($query) or Error(mysql_error());
if($result) {
	$i = 1;
	while($rs=mysql_fetch_array($result)) {
		$a_idx = $rs["aidx"];
		$board_id = $rs["boardId"];
		$board_name = $rs["boardName"];
		if($aidx==$a_idx) { $checked=" checked=\"checked\""; $b1="<b>"; $b2="</b>"; } else { $checked=""; $b1=""; $b2=""; }
		echo "<td nowrap><input type=\"checkbox\" name=\"aidx_{$i}\" id=\"bid{$i}\" value=\"{$a_idx}\" onclick=\"document.getElementById('applyMode2').checked=true;\"{$checked} /><label for=\"bid{$i}\">{$b1}{$board_id}".($board_name?"({$board_name})":"")."{$b2}</label></td>\n";
		if($i%2==0) echo "\n</tr>\n<tr>\n";
		$i++;
	}
	mysql_free_result($result);
	echo "<input type=\"hidden\" name=\"aidxEndNum\" value=\"{$i}\" />\n";
}
?>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td class="divbar1">저장방법 선택</td>
		<td class="divbar2">
			<input type="hidden" name="isPostBack" value="1" />
			<span style="color:red;"><img src="<?=$FSBOARD_PATH?>/img/clip/alert.gif" alt="Alert" /> 개별적으로 <b>모든 게시판에 적용</b>이 선택된 항목은 아래의 저장방법에 상관없이 그대로 모두 적용됩니다.</span><br />
			<nobr><input type="radio" name="applyMode" id="applyMode1" value="all" onclick="if(!window.confirm('현재상태의 환경설정이 설치된 모든게시판에 적용됩니다.\n일부게시판은 정상적으로 보이지 않을수 있으므로 게시판별로 따로 설정하기를 권장합니다.\n\n\'현재 설정을 설치된 모든게시판에 적용\'에 체크하시겠습니까?')){document.forms['__ctl'].applyMode[2].checked=true;}" /><label for="applyMode1">현재 설정을 설치된 모든 게시판에 적용(비추천)</label></nobr><br />
			<nobr><input type="radio" name="applyMode" id="applyMode2" value="define" /><label for="applyMode2">현재 설정을 선택한 게시판에만 적용</label></nobr><br />
			<nobr><input type="radio" name="applyMode" id="applyMode3" value="this" checked="checked" /><label for="applyMode3">현재 설정을 현재 게시판에만 적용</label></nobr><br />
		</td>
	</tr>
	<tr>
		<td colspan="2" style="font-family:Verdana; text-align:right;">
			<? echo $editDate ? "Last Update: <strong>".date("Y-m-d H:i:s",$editDate)."</strong>" : "Created: <strong>".date("Y-m-d H:i:s",$regDate)."</strong>"; ?>
		</td>
	</tr>
</table>


<br />


<table width="<?=$frmWidth?>" align="center" class="defstyle">
	<tr>
		<td align="center">
			<?echo "$btnSubmit $btnCancel $btnList";?>
		</td>
	</tr>
</table>










<?
}

if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
?>