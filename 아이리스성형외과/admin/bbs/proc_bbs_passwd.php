<?
	include getenv("DOCUMENT_ROOT")."/project.lib";

	$bbs = new CBbs();

	$passwd = $bbs->getPasswd($bbs_idx);

	if($bbs_passwd != $passwd)
		Error("비밀번호가 일치하지 않았습니다.");

	if($mode == "MODIFY")
	{
		echo "<Script language=\"javascript\">";
		echo "	opener.location.href=\"".$page_modify_path."?bbscode_code=".$bbscode_code."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."\";";
		echo "	self.close();";
		echo "</Script>";

		exit;
	}
	else
	{
		echo "<Script language=\"javascript\">";
		echo "	opener.location.href=\"/bbs/proc_bbs_delete1.html?bbscode_code=".$bbscode_code."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."\";";
		echo "	self.close();";
		echo "</Script>";
	}
?>