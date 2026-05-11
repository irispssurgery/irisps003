<?
/*************************************************************
	FSBOARD Auto Register
*************************************************************/

// 게시물 자동 입력 스크립트



include "lib/lib.php";

$id = trim($_GET["id"]);

if(!$id) { header("location:?id=test3");exit(); }

DBConn();


for($a=0; $a<10000; $a++) {
	$query = "SELECT max(idx) FROM _board_{$id};";
	$result = mysql_query($query) or Error(mysql_error);
	if($result) {
		$rs = mysql_fetch_row($result);
		$seqNum = intval($rs[0]);
	}
	else { Error("DB에러"); exit; }

	$seqNum = !$seqNum ? 1 : $seqNum+=1;
	$rndstr = GetRandomString(40);


	$objProperty = "";
	$isNotice = 0;
	$isSecret = 0;
	$isMember = 0;
	$docType = "text";
	$author = "admin";
	$e_mail = "saiur@msn.com";
	$homeUrl = "http://ubalhae.com/";
	$subject = "테스트용 {$seqNum}번째 게시물 {$rndstr}";
	$passwd = md5("qkfgo1237^^*");
	$category = "";
	$regDate = mktime();
	$editDate = 0;
	$memoLatestDate = 0;
	$memoNum = 0;
	$readNum = 0;
	$voteNum = 0;
	$ipReg = $_SERVER["REMOTE_ADDR"];
	$ipEdit = "";
	$usrAgentReg = $_SERVER["HTTP_USER_AGENT"];
	$usrAgentEdit = "";
	$ref = $seqNum;
	$reStep = 0;
	$reLevel = 0;
	$siteLink1 = "";
	$siteLink2 = "";
	$contents = "{$seqNum}번째 게시물\n\n테스트중......\n\n랜덤 문자 ".GetRandomString(64);


	$query = "
			INSERT INTO _board_{$id} (
				objProperty,
				isNotice,
				isSecret,
				isMember,
				docType,
				author,
				e_mail,
				homeUrl,
				subject,
				passwd,
				category,
				regDate,
				editDate,
				memoLatestDate,
				memoNum,
				readNum,
				voteNum,
				ipReg,
				ipEdit,
				usrAgentReg,
				usrAgentEdit,
				ref,
				reStep,
				reLevel,
				siteLink1,
				siteLink2,
	";
	for($i=1; $i<=30; $i++) {
		$query .= "
				fileName{$i}, fileDownload{$i},
		";
	}
	$query .= "
			contents) VALUES (
				'{$objProperty}',
				{$isNotice},
				{$isSecret},
				'{$isMember}',
				'{$docType}',
				'{$author}',
				'{$e_mail}',
				'{$homeUrl}',
				'{$subject}',
				'{$passwd}',
				'{$category}',
				{$regDate},
				{$editDate},
				{$memoLatestDate},
				{$memoNum},
				{$readNum},
				{$voteNum},
				'{$ipReg}',
				'{$ipEdit}',
				'{$usrAgentReg}',
				'{$usrAgentEdit}',
				{$ref},
				{$reStep},
				{$reLevel},
				'{$siteLink1}',
				'{$siteLink2}',
	";
	for($i=1; $i<=30; $i++) {
		$query .= "
				'', 0,
		";
	}
	$query .= "
				'$contents'
			);
	";

	//echo $query;
	mysql_query($query) or Error(mysql_error());
	mysql_query("UPDATE _board__admin__ SET totalObj=totalObj+1 WHERE boardId='{$id}';") or Error(mysql_error());

	echo "<font style='font-size:5pt;'>. </font> ";
	flush();
}



if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

echo "<br>$seqNum";
?>

inserted complete.

<!--
<script language="JavaScript">
function autoReload() {
	var seqNum = <?=$seqNum?>;
	if(seqNum>1000) {
		return false;
	} else {
		window.location.reload();
	}
}
setTimeout("autoReload()", 1);
</script>
-->