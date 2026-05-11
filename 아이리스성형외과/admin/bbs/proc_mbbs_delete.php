<?
	include "../../CObject.lib";

	$bbs = new CBbs();

	$result = $bbs->DeleteBbsAll($bbscode_code, $bbs_idx);

	if(!$result)
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"삭제하던중 오류가 발생하였습니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}

	Redirect("result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."");

?>