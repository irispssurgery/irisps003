<?
	include "../../../CObject.lib";

	$bbscode = new CBbscode();

	if(gettype($HTTP_POST_VARS["bbscode_idx"]) == "array")
	{
		$result = $bbscode->Erase($HTTP_POST_VARS["bbscode_idx"]);
	}
	else
	{
		$idx[]=$HTTP_GET_VARS["bbscode_idx"];
		$result = $bbscode->Erase($idx);
	}

	if(!$result)
	{
		echo "<Script langauge=\"javascript\">";
		echo "	alert(\"삭제하던중 오류가 발생하였습니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}

	Redirect("result_subbs_list.html");
?>