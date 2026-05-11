<?
/*************************************************************

	FSBOARD RSS Feed

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 1, 2007

	Copyright(c)2000-2007 FSBOARD, All Rights Reserved.

*************************************************************/


	include_once "lib.php";

	@ob_end_clean();
	@ob_start();

	if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2')) {
		header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
		header ('Pragma: no-cache');
	}
	else {
		header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
		header ('Pragma: no-cache');
	}

	header ('Expires: '.$lastBuildDate.'');
	header ('Last-Modified: '.$lastBuildDate.'');
	header ('Content-Type: text/xml; charset=euc-kr');

	//last update date
	$lastBuildDate = date('D, d M Y H:i:s').' +0900';


	$allowRss = true; //allow access
	$list_level = true; //permission to see the list
	$feednum = 10; //number of feeds
	$copyright_s = "Copyright(c)2006 fsboard. All right reserved."; //display copyright
	$sitename = $_SERVER["HTTP_HOST"]; //site name
	$sitedescription = "RSS Feed for ".$sitename; //site description
	$banner_image = "http://".$_SERVER["HTTP_HOST"]."/".$FSBOARD_PATH."/img/logo/logo3.gif"; //image for banner 
	$banner_width = "140"; //image width
	$banner_height = "120"; //image height
	$siteurl = "http://".$_SERVER["HTTP_HOST"]."/"; //address of this site
	$webmaster = ""; //webmaster email


	$id = trim($_GET["id"]);
	$_table_id_admin = "_board__admin__";

	$dbConnect = DBConn();

	if($id) {
		$id = StrAddSlashes($id);
		$query = "SELECT * FROM {$_table_id_admin} WHERE boardId='{$id}';";
	}
	else {
		$query = "SELECT * FROM {$_table_id_admin} ORDER BY totalObj DESC;";
	}

	if($result = mysql_query($query)) {
		$i = 0;
		while($rs = mysql_fetch_array($result)) {
			$aidx[] = $rs["aidx"];
			$boardId[] = $rs["boardId"];
			$combinedFileName[] = $rs["combinedFileName"];
			$levelList[] = $rs["levelList"];
			$levelView[] = $rs["levelView"];
			$useRssFeed[] = $rs["useRssFeed"];
			$i++;
		}
		if(!$i) $allowRss = false;
		mysql_free_result($result);
	}
	else $allowRss = false;

	//check to allow feed
	if(!$allowRss) {
		echo "<?xml version=\"1.0\" encoding=\"euc-kr\"?".">\n";
		echo "<rss version=\"2.0\">\n";
		echo "<response>\n";
		echo "<error>1</error>\n";
		echo "<message>해당 게시판은 추출할 수 없습니다.</message>\n";
		echo "</response>\n";
		echo "</rss>";
		exit;
	}

	for($i=0,$till=sizeof($aidx); $i<$till; $i++) {
		if(!$useRssFeed[$i]) continue;

		$query = "SELECT * FROM _board_".$boardId[$i]." ORDER BY idx DESC LIMIT 0,{$feednum};";
		$result = mysql_query($query);
		$ii = 1;
		while($rs = mysql_fetch_array($result)) {
			$r_idx[] = $rs["idx"];
			$r_secret[] = trim($rs["isSecret"]);
			$r_author[] = trim($rs["author"]);
			$r_category[] = trim($rs["category"]);
			$r_subject[] = trim($rs["subject"]);
			$r_memonum[] = $rs["memoNum"];
			$r_regdate[] = $rs["regDate"];
			$r_regdate_x[] = date('D, d M Y H:i:s',$rs["regDate"]).' +0900';
			$r_contents[] = $rs["contents"];

			$r_id[] = $boardId[$i];
			$r_listlevel[] = $levelList[$i];
			$r_viewlevel[] = $levelView[$i];
			$r_combined[] = $combinedFileName[$i];

			$ii++;
		}
	}


	?><?="<?xml version=\"1.0\" encoding=\"euc-kr\"?".">\n"?>
	<!--// RSS2.0 -->
	<rss version="2.0"
		xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
		xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<?if($copyright_s){?><copyright><?=$copyright_s?></copyright><?}?>
		<pubDate><?=$lastBuildDate?></pubDate>
		<lastBuildDate><?=$lastBuildDate?></lastBuildDate>
		<description><![CDATA[<?=$sitedescription?>]]></description>
		<link><?=htmlspecialchars($siteurl)?></link>
		<title><![CDATA[<?=$sitename?>]]></title>
		<?if($banner_image){?><image>
			<url><?=$banner_image?></url>
			<title><![CDATA[<?=$sitename?>]]></title>
			<link><?=$siteurl?></link>
			<height><?=$banner_height?></height>
			<width><?=$banner_width?></width>
			<description><![CDATA[<?=$sitename?>]]></description>
		</image><?}?>
		<?if($webmaster){?><managingEditor><?=$webmaster?></managingEditor>
		<webMaster><?=$webmaster?></webMaster><?}?>
		<language>ko</language><?

	//@rsort($r_regdate);

	for($i=0, $till=sizeof($r_idx); $i<$till; $i++) {

		$memoNum = $r_memonum[$i] ? "(".$r_memonum[$i].")" : "";

		$author = $r_author[$i];
		$subject = $r_subject[$i];
		$contents = $r_contents[$i];

		if($r_listlevel[$i]<10) {
			$author = "비공개";
			$subject = "비공개 게시물입니다";
		}

		if($r_secret[$i]) $contents = "<span style=\"color:red;\">### 비밀글입니다 ###</span>";
		if($r_viewlevel[$i]<10 || $r_listlevel[$i]<10) $contents = "<span style=\"color:red;\">### 내용을 볼수 있는 권한이 없습니다 ###</span>";

		$contents = strip_tags($contents);
		$contents = str_replace("\r\n","<br />",$contents);

		setlocale (LC_TIME,"ko");
		$name_sq = "<br /><br />작성자 : ".$author."<br />작성일자: ".strftime("%Y년 %m월 %d일 %A %p %I:%M:%S",$r_regdate[$i])."";

		$category = $r_category[$i] ? "<category><![CDATA[".$r_category[$i]."]]></category>" : "";

		$board_url = $r_combined[$i] ? $r_combined[$i] : "/".$FSBOARD_PATH."/";
		?>

		<item>
			<title><![CDATA[<?=$subject?> <?=$memoNum?>]]></title>
			<link>http://<?=$_SERVER["HTTP_HOST"]?><?=$board_url?>?id=<?=$r_id[$i]?>&amp;idx=<?=$r_idx[$i]?></link>
			<description><![CDATA[<?=$contents?>]]></description>
			<author><![CDATA[<?=$author?>]]></author>
			<pubDate><?=$r_regdate_x[$i]?></pubDate>
			<slash:comments><?=$r_memonum[$i]?></slash:comments>
			<guid>http://<?=$_SERVER["HTTP_HOST"]?><?=$board_url?>?id=<?=$r_id[$i]?>&amp;idx=<?=$r_idx[$i]?></guid>
			<?=$category?>
		</item>

		<?
	}

	?></channel>
	</rss><?
	exit;
?>