<?
/*************************************************************
	FSBOARD CSS Skin
*************************************************************/

	//파일타입 헤더 지정
	header ('Content-Type: text/css; charset=euc-kr');


	//공통 레이아웃값 초기화
	$w = trim($_GET["w"]);
	$o = trim($_GET["o"]);
	$a = trim($_GET["a"]);
	$u = trim($_GET["u"]);
	$b = trim($_GET["b"]);
	$i = trim($_GET["i"]);
	$c = trim($_GET["c"]);
	$s = trim($_GET["s"]);
	$g = trim($_GET["g"]);
	$r = trim($_GET["r"]);
	$m = trim($_GET["m"]);

	$width = $w=="" ? "100%" : $w;
	$origin_width = $o=="" ? 0 : $o;
	$align = $a=="" ? "center" : $a;
	$useCategory = $u ? true : false;
	$bgColor = $b ? "transparent" : $b;
	$bgImage = $i ? $i : "";
	$srhctgr = $c;
	$srhstr = $s;
	$ctgrstr = $g;
	$rowctgr = $r;
	$rowmode = $m;

	if(!ereg("%", $width)) { $width."px"; }


/////현재 스킨 공통 포함 스타일
?>
/*<![CDATA[*/

/* 기본 스타일 */
img { border:0; }
form { margin:0; }
input,select,textarea { font-size:9pt; font-family:돋움,Verdana; }
.defstyle { font-size:12px; font-family:돋움, Verdana; color:#222222; }

/* 캘린더 */
.fc_main { background: #DDDDDD; border: 1px solid #000000; font-family: Verdana; font-size: 10px; }
.fc_date { font-family: tahoma; border: 1px solid #D9D9D9;  cursor:pointer; font-size: 8pt; text-align: center;}
.fc_dateHover, TD.fc_date:hover { font-family: tahoma;cursor:pointer; border-top: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-right: 1px solid #999999; border-bottom: 1px solid #999999; background: #E7E7E7; font-size: 8pt; text-align: center; }
.fc_wk {font-family: Verdana; font-size: 11px; text-align: center;}
.fc_wknd { color: #FF0000; font-weight: bold; font-size: 11px; text-align: center;}
.fc_wknd1 { color: blue; font-weight: bold; font-size: 11px; text-align: center;}
.fc_head { background: #000066; color: #FFFFFF; font-weight:bold; text-align: left;  font-size: 11px; }

/* 리스트 스타일 */
#ListFrame a:link, #ListFrame a:visited { color:black; text-decoration:none; }
#ListFrame a:hover, #ListFrame a:active { color:blue; text-decoration:underline; }
a.lnk_list, a:visited.lnk_list { color:#555555; text-decoration:none; font-size:12px; font-family:돋움, Verdana; }
	a:hover.lnk_list, a:active.lnk_list { color:#0000ff; text-decoration:underline; font-size:12px; font-family:돋움, Verdana; }
.list_p { font-family:Tahoma, Verdana, Arial; font-size:11px; color:#222222; }
a.list_ct { font-family:굴림, Verdana; font-size:11px; color:#333333; }
a.list_m { font-family:Verdana; font-size:11px; color:#000000; }
a.list_tb { font-family:Verdana; font-size:10px; color:#555555; }
a.list_btn { font-family:돋움, Verdana; font-size:12px; color:#222222; }
input.txtbox_srh { border:0 solid #e1e1e1; height:15px; }

/* 내용보기 스타일 */
#ViewFrame a:link, #ViewFrame a:visited { color:black; text-decoration:none; }
#ViewFrame a:hover, #ViewFrame a:active { color:blue; text-decoration:underline; }
a.lnk_view, a:visited.lnk_view { color:#555555; text-decoration:none; }
	a:hover.lnk_view, a:active.lnk_view { color:#0000ff; text-decoration:underline; }
a.lnkfile_view, a:visited.lnkfile_view { color:#555555; font-size:10px; font-family:Verdana; text-decoration:none; }
a:hover.lnkfile_view, a:active.lnkfile_view { color:#0000ff; font-size:10px; font-family:Verdana; text-decoration:underline; }
a.lnksl_view, a:visited.lnksl_view { color:#555555; font-size:0.88em; text-decoration:none; }
a:hover.lnksl_view, a:active.lnksl_view { color:#0000ff; font-size:11px; text-decoration:underline; }

/* 코멘트 스타일 */
#WriteFrame a:link, #WriteFrame a:visited { color:black; text-decoration:none; }
#WriteFrame a:hover, #WriteFrame a:active { color:blue; text-decoration:underline; }
a.lnk_memo, a:visited.lnk_memo { font-size:11px; font-family:돋움, Arial; color:#555555; text-decoration:none; }
	a:hover.lnk_memo, a:active.lnk_memo { font-size:11px; font-family:돋움, Arial; color:#0000ff; text-decoration:underline; }
span.memo_d { font-size:10px; font-family:Arial; color:silver; }
input.memo_txtbox1 { border:0; font-size:12px; }
textarea.memo_txtbox2 { border:1px solid #f0f0f0; font-size:12px; }
a.tb_lnk, a:visited.tb_lnk { font-family:Arial; font-size:10px; color:#aaa; text-decoration:none; }
	a:hover.tb_lnk, a:active.tb_lnk { font-size:10px; color:#00f; text-decoration:underline; }
span.tb_r { font-family:Arial; font-size:0.88em; color:silver; }

/* 글쓰기폼 스타일 */
a.lnk_write,a:visited.lnk_write { color:#555555; text-decoration:none; }
	a:hover.lnk_write,a:active.lnk_write { color:#0000ff; text-decoration:underline; }
input.write_txtbox1 { border:1px solid #e0e0e0; }
textarea.write_txtbox2 { border:1px solid #e0e0e0; }
input.write_filebox { border:1px solid #e0e0e0; }
span.write_fl { font-family:돋움, Arial; font-size:11px; }
#subject { width:80%; }


/* 레이아웃 공통 */
div#ListFrame, div#ViewFrame, div#WriteFrame { clear:both; margin:0 auto; text-align:<?=$align?>; background-color:<?=$bgColor?>; <?=($bgImage?"background:url({$bgImage}); ":"")?>font-size:12px; font-family:맑은 고딕, 돋움, Verdana; }
	div#ListLayout, div#ViewLayout, div#WriteLayout { width:<?=$width?>; margin:0<?=($align=="center"?" auto":"")?>; }
	div#ViewLayout { line-height:145%; }
	div#WriteLayout { text-align:left; }


/* 리스트 - 레이아웃 스타일 */
	#ListTop { margin:0; } #ListTop p { margin:0; }
		p.ListTop_Left { float:left; }
		p.ListTop_Right { float:right; font-family:Verdana; font-size:0.88em; }
	#ListTitle { clear:both; height:1.28em; padding:0.6em; border:1px solid #e1e1e1; }
		div#ListTitle h4 { margin:0; font-weight:normal; font-size:1em; text-align:center; }
	div.ListBody { clear:both; height:1.3em; padding:0.7em; border-bottom:1px solid #e1e1e1; }
		div.ListBody:hover { background-color:#fafbf7; }
		div.ListBody p { margin:0; }
		.ListBody1 { float:left; width:3%; text-align:center; } p.ListBody1 input { margin-top:-0.22em; }
		.ListBody2 { float:left; width:6%; text-align:center; } p.ListBody2 { font-family:Verdana; font-size:0.8em; }
		.ListBody3 { float:left; width:7%; text-align:center; }
		.ListBody4 { float:left; width:<?=($useCategory?53:60)?>%; text-align:left; white-space:normal; }
		.ListBody5 { float:left; width:13%; text-align:center; }
		.ListBody6 { float:left; width:12%; text-align:center; } p.ListBody6 { font-family:Verdana; font-size:0.8em; }
		.ListBody7 { float:right; width:6%; text-align:center; } p.ListBody7 { font-family:Verdana; font-size:0.8em; }
	#ListBottom { clear:both; margin:0.3em auto; }
	#ListSearch { clear:both; width:<?=(($srhctgr&&$srhstr)||$ctgrstr||($rowctgr&&$rowmode)?"38.1":"33.9")?>em; height:1.7em; margin:0 auto; }
		#ListSearch p { float:left; margin:0; height:1.5em; }
		p.list_srhtxt { border:1px solid #e1e1e1; }


/* 내용보기 - 레이아웃 스타일 */
	/* 게시물정보 영역 */
	div.ViewRow { position:relative; clear:both; height:1.6em; padding:0.33em; border-top:1px solid #e1e1e1; }
		.ViewLabel { float:left; width:8%; text-align:center; }
		.ViewDivLine { float:left; color:#e1e1e1; margin-right:1em; }
		.ViewRSub { text-align:left; }
			.ViewRSub p { position:absolute; top:0.3em; right:0.1em; margin:0; font-family:Verdana; font-size:0.8em; }
	/* 게시물주소 영역 */
	#ViewPostUrl { margin:0; text-align:right; border-top:1px solid #e1e1e1; }
	/* 게시물내용 영역 */
	#ViewContent { clear:both; margin:0 auto; }
		div#ViewContent_FixVertical { float:left; margin:0; width:0; height:25em; display:inline; }
		div#ViewContent_Main { float:left; margin:0 0.3em 3.2em 0.5em; text-align:left; white-space:normal; text-indent:0; }
		div#ViewContent_FixTail { float:right; margin:0; width:0; height:0; display:inline; }

	/* 사용자코멘트 영역 */
	#ViewCommentLayout { clear:both; margin:0 auto; width:<?=(ereg("%",$width)?"99%":($origin_width-6)."px")?>; white-space:nowrap; }
		/* 태그 영역 */
		#ViewComment_Tag { margin:0 0.5em 0 0.5em; text-align:left; white-space:normal; }
		/* 코멘트 타이틀메뉴 */
		#ViewComment_Title { margin:0; padding:0.44em; text-align:left; border-top:1px solid #e1e1e1; }
		/* 첨부파일 영역 */
		div#PANEL_FILEGROUP {}
			div#ViewComment_File { margin:0em 0 0.8em 0; padding:0.6em; background-color:#fafbf7; border:1px solid #e1e1e1; }
				div#PANEL_ATTACHFILE { width:100%; margin:0 auto; text-align:left; }
					div.AttachedFile { float:left; margin:0.22em; width:49%; white-space:normal; }
		/* 트랙백 영역 */
		div#PANEL_TRACKBACKGROUP {}
			div#ViewComment_Trackback { margin:0em 0 0.8em 0; padding:0.6em; text-align:left; background-color:#fafbf7; border:1px solid #e1e1e1; }
				div#TrackbackUrl { margin:0.1em; font-size:11px; text-align:left; }
				div.Trackback_Blogname { float:left; width:12%; margin-top:0.7em; margin-bottom:0.7em; white-space:normal; }
				div.Trackback_Contents { float:left; width:86%; margin-top:0.7em; margin-bottom:0.7em; white-space:normal; }
					div.PANEL_TRACKBACKCONTENTS { clear:both; margin-left:0.5em; }
						div.Trackback_Title { white-space:normal; }
						div.Trackback_Excerpt { font-size:0.88em; white-space:normal; }
		/* 댓글 영역 */
		div#PANEL_COMMENTGROUP {}
			div#ViewComment_Comment { margin:0em 0 0.8em 0; background-color:#fafbf7; border:1px solid #e1e1e1; }
				/* 댓글 */
					div.Comment_Main { width:100%; margin:auto; padding:0.5em 0 0.5em 0; }
						div.Comment_Content { margin:0 auto; width:100%; text-align:left; white-space:nowrap; }
							div.Comment_Author { float:left; width:12%; margin:5px 0 5px 0; white-space:normal; }
							div.Comment_Excerpt { float:left; width:86%; margin:5px; white-space:normal; }
				/* 댓글 수정폼 */
				div.Comment_EditForm { clear:both; width:100%; margin:0 auto; border-top:1px solid #eeeeee; }
					div.Comment_EditFormTopLayout { float:left; margin:3px 0 2px 0; }
						div.Comment_EditFormTop { float:left; margin:0 0 0 5px; }
						div.Comment_EditFormBtnLayout { float:right; padding:0 5px 0 0; }
							div.Comment_EditFormBtn { white-space:nowrap; }
					div.Comment_EditFormMain { clear:both; margin:0 0 1px 0; }
						div.Comment_EditFormTextbox { width:100%; margin:0 auto; }
				/* 댓글 입력폼 */
				div#Comment_InputForm { clear:both; width:100%; margin:0 auto; }
					div#Comment_InputFormTopLayout { float:left; margin:3px 0 2px 0; }
						div#Comment_InputFormTop { float:left; margin:0 auto; padding:0 0 0 5px; }
						div.Comment_InputFormBtn { float:right; padding:0 5px 0 0; }
					div#Comment_InputFormMain { clear:both; width:100%; margin:0 auto; }
						div#Comment_InputFormTextbox { width:100%; margin:0 0 1px 0; }
	/* 버튼 영역 */
	#ViewBottomBtn { clear:both; margin:0 auto; padding:0.4em; border-top:1px solid #e1e1e1; }
		#ViewBottomBtnLeft { float:left; }
		#ViewBottomBtnRight { float:right; }


/* 글쓰기 - 레이아웃 스타일 */
	div#WriteFormTitle { padding:0.3em; font-weight:bold; border-bottom:1px solid silver; }
	div.WriteFormLabel { float:left; width:14%; padding:0.3em; }
	div.WriteFormInput, div.WriteFormInput_CR { padding:0.3em; }
	div.WriteFormInput_CR { border-bottom:1px solid #e1e1e1; }
	div#WriteFormBtn { margin:0.7em; text-align:center; }
	div.WriteAttachFile { float:left; width:15%; }

/*]]>*/
