<?
/*************************************************************
	FSBOARD Display Error Message
*************************************************************/

	if(ereg("_error.php",$_SERVER["PHP_SELF"])) die("잘못된 접근입니다.");

	if(!$msg) $msg = trim($_GET["msg"]);
	$msg = StrAddSlashes($msg);
	$msg = StripSlashes($msg);

	if(!$FSBOARD_PATH) {
		$tmpFile = realpath(__FILE__);
		$FSLIB_PATH = $tmpFile ? eregi_replace("_error.php","",$tmpFile) : "";
		$FSBOARD_PATH = str_replace("/lib/","",$FSLIB_PATH);
		$FSBOARD_PATH = substr($FSBOARD_PATH, strlen($FSBOARD_PATH)-strpos(strrev($FSBOARD_PATH),"/"), strlen($FSBOARD_PATH));
		$FSBOARD_PATH = "/".$FSBOARD_PATH;
	}

	if(!$align) $align = "center";

	$msg = str_replace("\\n","<br />",$msg);
	if(!$msg) $msg = "알수 없는 오류입니다.";

	$btnPageLink = $url ? "<a href=\"{$url}\"><img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" border=\"0\" /></a>" : "<a href=\"javascript:window.history.back();\"><img src=\"{$FSBOARD_PATH}/img/btn/back.gif\" border=\"0\" /></a>";
	$btnList = !$url && $id ? "<a href=\"".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list\"><img src=\"{$FSBOARD_PATH}/img/btn/list.gif\" border=\"0\" /></a>" : "";

	ContentTop();
?>
<style type="text/css">
#ErrMsg, #ErrMsg_Btn { clear:both; width:35em; font-size:13px; font-family:돋움, Dottum, Verdana; color:#555555; }
#ErrMsg { margin:0em auto; text-align:center; border:1px solid #f0f0f0; }
	#ErrMsg_Title { padding:0.5em; font-family:Verdana; font-weight:bold; background-color:#fafbf7; border-bottom:1px solid #f0f0f0; }
	#ErrMsg_Body { margin:1.7em; }
		#ErrMsg_Body p { margin:0; }
#ErrMsg_Btn { margin:0.7em auto; text-align:center; }
</style>
<br />
<br />
<div id="ErrMsg">
	<div id="ErrMsg_Title">MESSAGE</div>
	<div id="ErrMsg_Body">
		<p>
			<?=$msg?>
		</p>
	</div>
</div>
<div id="ErrMsg_Btn"><?=$btnPageLink?> <?=$btnList?></div>
<br />
<br />
<br />
<?
/*
echo <<<err_msg
	<br />
	<table width="{$width}" align="{$align}">
		<tr>
			<td align="center">

				<table width="400" border="0" bordercolor="#F0F0F0" style="border-collapse:collapse;border:1px solid #EEEEEE;">
					<tr height="28">
						<td align="center" style="border:1px solid #EEEEEE;background-color:#FAFBF7;">
							<b style="font-family:Verdana;">MESSAGE</b>
						</td>
					</tr>
					<tr>
						<td align="center" style="padding:5px; border-bottom:1px solid #EEEEEE;">
							<br />
							{$msg}
							<br />
							<br />
						</td>
					</tr>
				</table>
				<table width="400">
					<tr>
						<td align="center" height="40">
							{$btnPageLink}
							{$btnList}
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
	<br />
	<br />
	<br />
	<br />
err_msg;
*/
	ContentBottom();
?>