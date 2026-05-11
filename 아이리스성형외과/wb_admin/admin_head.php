<?
/*************************************************************

	FSBOARD Setup Manager 1.0

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 11, 2007

	Copyright(c)2000-2007 FSBOARD. All rights reserved.

*************************************************************/


/**************************************************
 Setup 초기화
**************************************************/

	//라이브러리 포함
	$INC_PATH="/fsboard/lib/";
	include_once $_SERVER["DOCUMENT_ROOT"]."/fsboard/lib/lib.php";

	//변수 초기화
	$MODE = trim($_GET["mode"]);
	$EXEC = trim($_GET["exec"]);

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("setup.php",$_SERVER["PHP_SELF"])) ? true : false;

	//DB연결
	if($MODE) DbConn();

	include_once "auth_admin.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" href="/wb_admin/css/css.css" type="text/css">
</head>

<body bgcolor="#165E90" text="#000000" leftmargin="0" topmargin="0">
<table width="1000" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td background="/wb_admin/image/top_bg.gif"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="206"><img src="/wb_admin/image/admin_logo.gif" width="206" height="77"></td>
          <td valign="bottom" style="padding-bottom:5px"> 
            <table border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="105"><a href="/wb_admin/index.htm"><img src="/wb_admin/image/menu01.gif" width="105" height="60"border="0"></a></td>
                <td width="105"><a href="/wb_admin/intro/sms/list.php?mode=Admin.Manage"><img src="/wb_admin/image/menu02.gif" width="105" height="60"border="0"></a></td>
<!--
                <td width="105"><a href="/wb_admin/intro/menu/menu_list.htm?pres=1"><img src="/wb_admin/image/menu07.gif" width="105" height="60"border="0" /></a></td>
-->
                <td width="105"><a href="/fsboard/lib/setup.php?mode=List"><img src="/wb_admin/image/menu03.gif" width="105" height="60"border="0"></a></td>
                <td width="105"><a href="/fsboard/lib/members.php?mode=Admin.MemList"><img src="/wb_admin/image/menu04.gif" width="105" height="60"border="0"></a></td>
                <td width="105"><a href="/fsboard/lib/stat.php?mode=Stat.Month"><img src="/wb_admin/image/menu05.gif" width="105" height="60"border="0"></a></td>
                <td width="105"><a href="/fsboard/lib/logout.php"><img src="/wb_admin/image/menu06.gif" width="105" height="60" border="0"></a></td>
              </tr>
            </table>
          </td>
          <td width="5"><img src="/wb_admin/image/top_right.gif" width="5" height="77"></td>
        </tr>
      </table>
    </td>
  </tr>
<?			
	if($mode == "List" Or $mode=="CreateBoard" Or $mode=="admin"){
		$title_str="게시판관리";
		$left_title_img = "left_board.gif";
		$left_file = $_SERVER["DOCUMENT_ROOT"]."/wb_admin/intro/board/left.php";
	}else if ($mode=="Admin.MemList" Or $mode=="Admin.MemEdit") { 
		$title_str="회원관리";
		$left_title_img = "left_member.gif";
		$left_file = $_SERVER["DOCUMENT_ROOT"]."/wb_admin/intro/member/left.php";
	}else if ($mode=="Stat.Month" Or $mode=="Stat.Day" Or $mode=="Stat.Week" Or $mode=="Stat.Time" Or $mode=="Stat.Os" Or $mode=="Stat.Browser" Or $mode=="Stat.Log"){ 
		$title_str="접속통계";
		$left_file = $_SERVER["DOCUMENT_ROOT"]."/wb_admin/intro/statistic/left.php";
		$left_title_img = "left_stat.gif";
	}else if ($mode=="Admin.Manage") { 
		$title_str="SMS";
		$left_title_img = "left_manage.gif";
		$left_file = $_SERVER["DOCUMENT_ROOT"]."/wb_admin/intro/manage_left.php";
	}else{
		$title_str="관리자홈";
		$left_file = $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_left.php";
		$left_title_img = "left_title.gif";
	}
?>	
  <tr> 
    <td background="/wb_admin/image/top_bg2.gif" height="30"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="7"><img src="/wb_admin/image/top_left.gif" width="7" height="30"></td>
          <td> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="220">&nbsp;</td>
                <td><img src='/wb_admin/image/dot.gif' width='4' height='4' border='0' absmiddle='0' vspace='3' > 
                  홈 &gt; 홈페이지관리 &gt; <?=$title_str?></td>
                <td width="360" valign="top">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td width="7"><img src="/wb_admin/image/top_right2.gif" width="7" height="30"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <td background="/wb_admin/image/top_left.gif" width="7"></td>
          <td bgcolor="#FFFFFF" valign="top" style="padding-top:10px"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="200" height="400" valign="top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td align="center"><img src="/wb_admin/image/<?=$left_title_img?>" width="180" height="70"></td>
                    </tr>
                    <tr> 
                      <td>&nbsp;</td>
                    </tr>
                    <tr> 
					  <!-- left menu -->
                      <td align="center"> 
                        <table border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td><img src="/wb_admin/image/left_menu_top.gif" width="182" height="5"></td>
                          </tr>
                          <tr> 
                            <td background="/wb_admin/image/left_menu_bg.gif" align="center" style="padding-top:5px; padding-bottom:5px"> 
                             <?
								include $left_file;
							 ?>
                            </td>
                          </tr>
                          <tr> 
                            <td><img src="/wb_admin/image/left_menu_bottom.gif" width="182" height="5"></td>
                          </tr>
                        </table>
                      </td>
					  <!-- left menu -->
                    </tr>
                    <tr> 
                      <td align="center">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td>&nbsp;</td>
                    </tr>
                  </table>
                </td>
                <td width="1" bgcolor="#E9E9E9"></td>
                <td valign="top" style="padding-left:10px;padding-right:10px">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="text_black_16px_bold" style="padding-bottom:3px"><img src="/wb_admin/image/title_point_bluegreen.gif" width="16" height="16" align="absmiddle"> <?=$title_str?></td>
                    </tr>
                    <tr> 
                      <td bgcolor="#D4D4D4" height="1"></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                    </tr>				
					<tr> 
                      <td> 
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr valign="top"> 
                            <td width='100%' height='550'>