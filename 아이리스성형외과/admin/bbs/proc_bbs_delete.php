<?
	include "../../CObject.lib";

	$bbs = new CBbs();

	if($bbsadmin)
	{
		$result = $bbs->DeleteBbs($bbscode_code, $bbs_idx, $is_uploaded_file);

		if(!$result)
		{
			echo "<Script language=\"javascript\">";
			echo "	alert(\"삭제하던중 오류가 발생하였습니다.\");";
			echo "	history.back();";
			echo "</Script>";

			exit;
		}
		if($bbsadmin)
			Redirect("result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."");
		else
			Redirect("result_bbs_list.html?bbscode_code=".$bbscode_code."&mode=list&site_code=".$site_code."");
	}
	else
	{
		echo "<Script language=\"javascript\">";
		echo " alert(\"다른사람의 게시물을 삭제할수 없습니다.\");";
		echo " history.back();";
		echo "</Script>";
	}
?>