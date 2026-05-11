<?
	include "../../../CObject.lib";
	
	$bbscode = new CBbscode();

	$data["bbscode_id"] = $HTTP_POST_VARS["bbscode_id"];
	$data["bbscode_passwd"] = $HTTP_POST_VARS["bbscode_passwd"];
	$data["bbscode_title"] = $HTTP_POST_VARS["bbscode_title"];
	$data["list_border_color"] = $HTTP_POST_VARS["list_border_color"];
	$data["list_select_color"] = $HTTP_POST_VARS["list_select_color"];
	$data["list_number_color"] = $HTTP_POST_VARS["list_number_color"];
	$data["list_title_color"] = $HTTP_POST_VARS["list_title_color"];
	$data["list_name_color"] = $HTTP_POST_VARS["list_name_color"];
	$data["list_file_color"] = $HTTP_POST_VARS["list_file_color"];
	$data["list_date_color"] = $HTTP_POST_VARS["list_date_color"];
	$data["list_hit_color"] = $HTTP_POST_VARS["list_hit_color"];
	$data["link_color"] = $HTTP_POST_VARS["link_color"];
	$data["unlink_color"] = $HTTP_POST_VARS["unlink_color"];
	$data["view_major_color"] = $HTTP_POST_VARS["view_major_color"];
	$data["view_submajor_color"] = $HTTP_POST_VARS["view_submajor_color"];
	$data["is_uploaded_file"] = $HTTP_POST_VARS["is_uploaded_file"];
	$data["site_code"] = $HTTP_POST_VARS["site_code"];
	$data["bbs_write"] = $HTTP_POST_VARS["bbs_write"];
	$data["bbs_modify"] = $HTTP_POST_VARS["bbs_modify"];
	$data["bbs_reply"] = $HTTP_POST_VARS["bbs_reply"];
	$data["bbs_delete"] = $HTTP_POST_VARS["bbs_delete"];
	$data["title_image"] = $HTTP_POST_VARS["title_image"];
	$data["tail_image"] = $HTTP_POST_VARS["tail_image"];
	$data["bbscode_signdate"] = date("Y-m-d");

	$result = $bbscode->Insert($data);

	if(!$result)
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"저장하던중 오류가 발생하였습니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}

	Redirect("result_subbs_list.html");
?>