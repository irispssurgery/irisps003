<?
	include getenv("DOCUMENT_ROOT")."/project.lib";

	$bbscode = new CBbscode();

	$account = $bbscode->getAccount($bbscode_idx);

	if($bbscode_id == $account["bbscode_id"] && $bbscode_passwd == $account["bbscode_passwd"])
	{
		echo "<Script language=\"javascript\">";
		echo "	opener.location.href=\"result_mbbs_list.html?bbscode_code=".$bbscode_code."\";";
		echo "	self.close();";
		echo "</Script>";

		exit;
	}
	else
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"해당 게시판의 관리자가 아닙니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}
?>