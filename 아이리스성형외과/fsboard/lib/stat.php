<?
/*************************************************************

	FSBOARD Stat Manager 1.0	

	Technical contact: jin3728@paran.com
	Producer: SeongJin Seo
	Module Made: August 1, 2009
	Last Update: August 5, 2009

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
	$sDateYear  = trim($_GET["sDateYear"]);
	$sDateMonth = trim($_GET["sDateMonth"]);

	if ($sDateYear  == "") $sDateYear = date("Y");
	if ($sDateMonth == "") $sDateMonth = date("m");
	
	$strNowDate = $sDateYear . "-" & $sDateMonth . "-01";

	$sDateS = trim($_POST["sDateS"]);
	if ($sDateS == ""){
		$sDateS = date("Y") . "-" . date("m") . "-01";
	}

	$sDateE = trim($_POST["sDateE"]);
	if ($sDateE == ""){
		$sDateE =  date("Y-m-d");
	}

	$sd = date("w", mktime(0,0,0,$this_month,1,$this_year)); //요일 구하기 (num)
	$ed   = date("t", mktime(0,0,0,$this_month,1,$this_year)); //마지막날 구하기 

	//기본사용변수
	$width = "98%";
	$align = "center";
	$MemDefaultLevel = sizeof($mem_part_element) - 1; //회원가입시 기본 레벨
	$MemId = $_SESSION["MemId"];

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("stat.php",$_SERVER["PHP_SELF"])) ? true : false;

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

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("{$FSBOARD_PATH}/lib/setup.php?mode=Login&nav=List"); exit; }

	$pagesize = 25;
	$divpage = 10;

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	/**************************************************
	 모드별 처리
	**************************************************/

	//////////////////////////////////////////////////////////////////////////////////////////ADMIN.리스트

	$max = 0;
	$sum_count = 0;

	if($MODE == "Stat.Year") {
		$query = "select SUBSTRING(wb_date,1,4) as wb_year, SUM(wb_count) as cnt 
					from wb_count_sum
					where wb_date between '2009-01-01' and '2009-12-31'
					group by wb_year
					order by wb_year desc";

		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$numrows = mysql_num_rows($result);

			members_head();

			echo "
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<form name=\"theForm\" method=\"post\">
					<tr>
						<td>
						    <LABEL FOR=\"strSearchType1\" style=\"cursor:hand\">월별 접속통계</LABEL>
						    <select name=\"sDateYear\" id=\"sDateYear\" style=\"width:80\">
							    <option value=\"2009\" SELECTED>2009 년</option>
						    </select>
						    <a href=\"javascript:;\" onclick=\"OnStatSearch();return false;\"><img src=\"/fsboard/img/btn/search.gif\" border=\"0\" /></a>
						
						</td>
					</tr>
				</form>
				</table>
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">날 짜</td>
						<td width=\"100\">접속수</td>
						<td width=\"100\">기간평균</td>
						<td width=\"100\">비율</td>
						<td>그래프</td>
					</tr>
			";
			if($numrows) {

				while($rs = mysql_fetch_array($result)) {
					$wb_year = $rs["wb_year"];
					$cnt	 = $rs["cnt"];

					echo "
					<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
						<td>{$wb_year}</td>
						<td>{$cnt}</td>
						<td>{$intAvrCount}</td>
						<td>
							<table border=\"0\">
								<tr>
									<td width=\"50%\">{$per}%</td>
									<td width=\"50%\">　{$str}</td>
								</tr>
							</table>
						</td>
						<td align=\"left\"><img src=\"$FSBOARD_PATH/img/clip/grp1.gif\" width=\"$per_img\" height=\"10\"></td>
					</tr>
					";
				}
			}
			echo "
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">합 계</td>
						<td width=\"100\">{$intTotalCount}</td>
						<td width=\"100\">{$intAvrCount}</td>
						<td width=\"100\">100%</td>
						<td></td>
					</tr>
				</table>
			";

			members_foot();
			mysql_free_result($result);

		}
					
	}else if($MODE == "Stat.Month") {

		$query = "select SUBSTRING(wb_date,1,7) as wb_month, SUM(wb_count) as cnt,
					(select sum(wb_count) From wb_count_sum where wb_date between '$sDateYear-01-01' and '$sDateE') totalcount
					from wb_count_sum
					where wb_date between '$sDateYear-01-01' and '$sDateE'
					group by wb_month
					order by wb_month asc";  

		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$numrows = mysql_num_rows($result);

			members_head();

			echo "
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<form name=\"theForm\" method=\"post\">
					<tr>
						<td>
						    <LABEL FOR=\"strSearchType1\" style=\"cursor:hand\">월별 접속통계</LABEL>
						    <select name=\"sDateYear\" id=\"sDateYear\" style=\"width:80\">
							    <option value=\"2009\" SELECTED>2009 년</option>
						    </select>
						    <a href=\"javascript:;\" onclick=\"OnStatSearch();return false;\"><img src=\"/fsboard/img/btn/search.gif\" border=\"0\" /></a>
						
						</td>
					</tr>
				</form>						
				</table>
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">날 짜</td>
						<td width=\"100\">접속수</td>
						<td width=\"100\">기간평균</td>
						<td width=\"100\">비율</td>
						<td>그래프</td>
					</tr>
			";
			if($numrows) {
				while($rs = mysql_fetch_array($result)) {
					$wb_month	   = $rs["wb_month"];
					$cnt		   = $rs["cnt"];
					$intTotalCount = $rs["totalcount"];

					if ($intTotalCount == "0") {
						$intAvrCount = "0";
						$per = "0";
					}else{
						$intAvrCount = round($intTotalCount / $numrows,2);
						$per = (int)(100 * ($cnt/$intTotalCount));
					}
					$intTotalCount=number_format($intTotalCount);
					
					if ($intAvrCount < $cnt) {
						$str="<font color=\"#0000FF\">↑</font>";
					}else{
						$str="<font color=\"#FF0000\">↓</font>";
					}

					$per_img=$per*3;

					echo "
					<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
						<td>{$wb_month}</td>
						<td>{$cnt}</td>
						<td>{$intAvrCount}</td>
						<td>
							<table border=\"0\">
								<tr>
									<td width=\"50%\">{$per}%</td>
									<td width=\"50%\">　{$str}</td>
								</tr>
							</table>
						</td>
						<td align=\"left\"><img src=\"$FSBOARD_PATH/img/clip/grp1.gif\" width=\"$per_img\" height=\"10\"></td>
					</tr>
					";
				}
			}
			echo "
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">합 계</td>
						<td width=\"100\">{$intTotalCount}</td>
						<td width=\"100\">{$intAvrCount}</td>
						<td width=\"100\">100%</td>
						<td></td>
					</tr>
				</table>
			";

			members_foot();
			mysql_free_result($result);

		}

	} else if($MODE == "Stat.Day") {
		$query = "select wb_date, wb_count as cnt, 
					(select sum(wb_count) From wb_count_sum where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed') totalcount
					from wb_count_sum
					where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed'
					order by wb_date asc"; 
		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$numrows = mysql_num_rows($result);

			members_head();

			echo "
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<form name=\"theForm\" method=\"post\">
					<tr>
						<td>
						    <LABEL FOR=\"strSearchType1\" style=\"cursor:hand\">일별 접속통계</LABEL>
						    <select name=\"sDateYear\" id=\"sDateYear\" style=\"width:80\">
							    <option value=\"2009\" SELECTED>2009 년</option>
						    </select>
						    <select name=\"sDateMonth\" id=\"sDateMonth\" style=\"width:80\">
			";
			for ($i=1;$i<=12;$i++){
				$i = str_pad($i, 2, 0, STR_PAD_LEFT);
				if (Trim($i)==TRim($sDateMonth)) {
					$strselected="SELECTED";
				}else{
					$strselected="";
				}
			echo "
							    <option value=\"$i\" $strselected>$i 월</option>
			";
			}
			echo "
							</select>
						    <a href=\"javascript:;\" onclick=\"OnStatSearch();return false;\"><img src=\"/fsboard/img/btn/search.gif\" border=\"0\" /></a>
						
						</td>
					</tr>
				</form>
				</table>
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">날 짜</td>
						<td width=\"100\">접속수</td>
						<td width=\"100\">기간평균</td>
						<td width=\"100\">비율</td>
						<td>그래프</td>
					</tr>
			";
			if($numrows) {

				while($rs = mysql_fetch_array($result)) {
					$wb_date = $rs["wb_date"];
					$cnt	  = $rs["cnt"];
					$intTotalCount = $rs["totalcount"];

					if ($intTotalCount == "0") {
						$intAvrCount = "0";
						$per = "0";
					}else{
						$intAvrCount = round($intTotalCount / $numrows,2);
						$per = (int)(100 * ($cnt/$intTotalCount));
					}
					$intTotalCount=number_format($intTotalCount);
					
					if ($intAvrCount < $cnt) {
						$str="<font color=\"#0000FF\">↑</font>";
					}else{
						$str="<font color=\"#FF0000\">↓</font>";
					}

					$per_img=$per*3;

					echo "
					<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
						<td>{$wb_date}</td>
						<td>{$cnt}</td>
						<td>{$intAvrCount}</td>
						<td>
							<table border=\"0\">
								<tr>
									<td width=\"50%\">{$per}%</td>
									<td width=\"50%\">　{$str}</td>
								</tr>
							</table>
						</td>
						<td align=\"left\"><img src=\"$FSBOARD_PATH/img/clip/grp1.gif\" width=\"$per_img\" height=\"10\"></td>
					</tr>
					";
				}
			}
			echo "
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">합 계</td>
						<td width=\"100\">{$intTotalCount}</td>
						<td width=\"100\">{$intAvrCount}</td>
						<td width=\"100\">100%</td>
						<td></td>
					</tr>
				</table>
			";

			members_foot();
			mysql_free_result($result);

		}

	} else if($MODE == "Stat.Time") {
		$query = "select SUBSTRING(wb_time,1,2) as wb_hour, count(wb_seq) as cnt,
					(select sum(wb_count) From wb_count_sum where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed') totalcount
					from wb_count
				    where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed'
				    group by wb_hour
				    order by wb_hour";

		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$numrows = mysql_num_rows($result);

			members_head();

			echo "
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<form name=\"theForm\" method=\"post\">
					<tr>
						<td>
						    <LABEL FOR=\"strSearchType1\" style=\"cursor:hand\">시간별 접속통계</LABEL>
						    <select name=\"sDateYear\" id=\"sDateYear\" style=\"width:80\">
							    <option value=\"2009\" SELECTED>2009 년</option>
						    </select>
						    <select name=\"sDateMonth\" id=\"sDateMonth\" style=\"width:80\">
			";
			for ($i=1;$i<=12;$i++){
				$i = str_pad($i, 2, 0, STR_PAD_LEFT);
				if (Trim($i)==TRim($sDateMonth)) {
					$strselected="SELECTED";
				}else{
					$strselected="";
				}
			echo "
							    <option value=\"$i\" $strselected>$i 월</option>
			";
			}
			echo "
							</select>
						    <a href=\"javascript:;\" onclick=\"OnStatSearch();return false;\"><img src=\"/fsboard/img/btn/search.gif\" border=\"0\" /></a>
						
						</td>
					</tr>
				</form>
				</table>
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">날 짜</td>
						<td width=\"100\">접속수</td>
						<td width=\"100\">기간평균</td>
						<td width=\"100\">비율</td>
						<td>그래프</td>
					</tr>
			";
			if($numrows) {

				while($rs = mysql_fetch_array($result)) {
					$wb_hour = $rs["wb_hour"];
					$cnt	  = $rs["cnt"];
					$intTotalCount = $rs["totalcount"];

					if ($intTotalCount == "0") {
						$intAvrCount = "0";
						$per = "0";
					}else{
						$intAvrCount = round($intTotalCount / $numrows,2);
						$per = (int)(100 * ($cnt/$intTotalCount));
					}
					$intTotalCount=number_format($intTotalCount);
					
					if ($intAvrCount < $cnt) {
						$str="<font color=\"#0000FF\">↑</font>";
					}else{
						$str="<font color=\"#FF0000\">↓</font>";
					}

					$per_img=$per*3;

					echo "
					<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
						<td>{$wb_hour}</td>
						<td>{$cnt}</td>
						<td>{$intAvrCount}</td>
						<td>
							<table border=\"0\">
								<tr>
									<td width=\"50%\">{$per}%</td>
									<td width=\"50%\">　{$str}</td>
								</tr>
							</table>
						</td>
						<td align=\"left\"><img src=\"$FSBOARD_PATH/img/clip/grp1.gif\" width=\"$per_img\" height=\"10\"></td>
					</tr>
					";
				}
			}
			echo "
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">합 계</td>
						<td width=\"100\">{$intTotalCount}</td>
						<td width=\"100\">{$intAvrCount}</td>
						<td width=\"100\">100%</td>
						<td></td>
					</tr>
				</table>
			";

			members_foot();
			mysql_free_result($result);

		}

	} else if($MODE == "Stat.Week") {
		$weekday = array ('월', '화', '수', '목', '금', '토', '일');
		$query = "select WEEKDAY(wb_date) as weekday_date, SUM(wb_count) as cnt,
					(select sum(wb_count) From wb_count_sum where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed') totalcount		
					from wb_count_sum
					where wb_date between '$sDateYear-$sDateMonth-01' and '$sDateYear-$sDateMonth-$ed'
					group by weekday_date
					order by weekday_date";

		$result = mysql_query($query) or Error(mysql_error());

		if($result) {
			$numrows = mysql_num_rows($result);

			members_head();

			echo "
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"0\" cellspacing=\"0\" class=\"defstyle\">
				<form name=\"theForm\" method=\"post\">
					<tr>
						<td>
						    <LABEL FOR=\"strSearchType1\" style=\"cursor:hand\">요일별 접속통계</LABEL>
						    <select name=\"sDateYear\" id=\"sDateYear\" style=\"width:80\">
							    <option value=\"2009\" SELECTED>2009 년</option>
						    </select>
						    <select name=\"sDateMonth\" id=\"sDateMonth\" style=\"width:80\">
			";
			for ($i=1;$i<=12;$i++){
				$i = str_pad($i, 2, 0, STR_PAD_LEFT);
				if (Trim($i)==TRim($sDateMonth)) {
					$strselected="SELECTED";
				}else{
					$strselected="";
				}
			echo "
							    <option value=\"$i\" $strselected>$i 월</option>
			";
			}
			echo "
							</select>
						    <a href=\"javascript:;\" onclick=\"OnStatSearch();return false;\"><img src=\"/fsboard/img/btn/search.gif\" border=\"0\" /></a>
						
						</td>
					</tr>
				</form>
				</table>
				<table width=\"{$width}\" align=\"{$align}\" cellpadding=\"2\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;\" class=\"defstyle\">
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">날 짜</td>
						<td width=\"100\">접속수</td>
						<td width=\"100\">기간평균</td>
						<td width=\"100\">비율</td>
						<td>그래프</td>
					</tr>
			";
			if($numrows) {

				while($rs = mysql_fetch_array($result)) {
					$weekday_date = $rs["weekday_date"];
					$cnt		  = $rs["cnt"];
					$intTotalCount = $rs["totalcount"];

					if ($intTotalCount == "0") {
						$intAvrCount = "0";
						$per = "0";
					}else{
						$intAvrCount = round($intTotalCount / $numrows,2);
						$per = (int)(100 * ($cnt/$intTotalCount));
					}
					$intTotalCount=number_format($intTotalCount);
					
					if ($intAvrCount < $cnt) {
						$str="<font color=\"#0000FF\">↑</font>";
					}else{
						$str="<font color=\"#FF0000\">↓</font>";
					}

					$per_img=$per*3;

					echo "
					<tr align=\"center\" onmouseover=\"this.bgColor='#F7F7F7';\" onmouseout=\"this.bgColor='';\" height=\"28\">
						<td>{$weekday[$weekday_date]}</td>
						<td>{$cnt}</td>
						<td>{$intAvrCount}</td>
						<td>
							<table border=\"0\">
								<tr>
									<td width=\"50%\">{$per}%</td>
									<td width=\"50%\">　{$str}</td>
								</tr>
							</table>
						</td>
						<td align=\"left\"><img src=\"$FSBOARD_PATH/img/clip/grp1.gif\" width=\"$per_img\" height=\"10\"></td>
					</tr>
					";
				}
			}
			echo "
					<tr align=\"center\" bgcolor=\"#FBFAF7\" height=\"28\">
						<td width=\"100\">합 계</td>
						<td width=\"100\">{$intTotalCount}</td>
						<td width=\"100\">{$intAvrCount}</td>
						<td width=\"100\">100%</td>
						<td></td>
					</tr>
				</table>
			";

			members_foot();
			mysql_free_result($result);

		}

	} else if($MODE == "Stat.Os") {
		$sql = "select * from wb_count
					where wb_date between '2009-01-01' and '2009-08-31'";

	} else if($MODE == "Stat.Browser") {
		$sql = "select * from wb_count
					where wb_date between '2009-01-01' and '2009-08-31'";
	} else if($MODE == "Stat.Domain") {
		$sql = "select * from wb_count
					where wb_date between '2009-01-01' and '2009-08-31'";
	}


	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";
?>


<script language="javascript">

	function OnStatSearch(){
		document.theForm.action = "/fsboard/lib/stat.php?mode=<?=$MODE?>";
		document.theForm.submit();
	}

	function OnPageMove(str){
		document.theForm.action = "/fsboard/lib/stat.php?mode=<?=$MODE?>&intPage=" + str;
		document.theForm.submit();
	}

	function OnStatSearch(){
		document.theForm.action = "/fsboard/lib/stat.php?mode=<?=$MODE?>";
		document.theForm.submit();
	}

	function OnSearchDate(sy, sm, sd, ey, em, ed){

		if (sm.length == 1){sm = "0" + sm;}
		if (sd.length == 1){sd = "0" + sd;}
		if (em.length == 1){em = "0" + em;}
		if (ed.length == 1){ed = "0" + ed;}

		document.all['sDateS'].value = sy + "-" + sm + "-" + sd;
		document.all['sDateE'].value = ey + "-" + em + "-" + ed;

	}

</script>