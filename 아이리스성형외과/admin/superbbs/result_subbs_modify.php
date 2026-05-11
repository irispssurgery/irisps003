<?
	include "../../../CObject.lib";

	$bbscode = new CBbscode();

	$result = $bbscode->View($HTTP_GET_VARS["bbscode_idx"]);

	foreach($result as $key => $value)
	{
		$bbscode_idx=$value["bbscode_idx"];
		$bbscode_code=$value["bbscode_code"];
		$bbscode_id=$value["bbscode_id"];
		$bbscode_passwd=$value["bbscode_passwd"];
		$bbscode_title=$value["bbscode_title"];
		$list_border_color=$value["list_border_color"];
		$list_select_color=$value["list_select_color"];
		$list_number_color=$value["list_number_color"];
		$list_title_color=$value["list_title_color"];
		$list_name_color=$value["list_name_color"];
		$list_file_color=$value["list_date_color"];
		$list_date_color=$value["list_date_color"];
		$list_hit_color=$value["list_hit_color"];
		$link_color=$value["link_color"];
		$unlink_color=$value["unlink_color"];
		$view_major_color=$value["view_major_color"];
		$view_submajor_color=$value["view_submajor_color"];
		$is_uploaded_file = $value["is_uploaded_file"];
		$bbs_write=$value["bbs_write"];
		$bbs_modify=$value["bbs_modify"];
		$bbs_reply=$value["bbs_reply"];
		$bbs_delete=$value["bbs_delete"];
		$title_image=$value["title_image"];
		$tail_image=$value["tail_image"];
	}


	if($is_uploaded_file == "Y")
	{
		$file_checked="<input type=\"radio\" name=\"is_uploaded_file\" value=\"Y\" checked>사용";
		$file_checked.="<input type=\"radio\" name=\"is_uploaded_file\" value=\"N\">사용하지 않음";
	}
	else
	{
		$file_checked="<input type=\"radio\" name=\"is_uploaded_file\" value=\"Y\">사용";
		$file_checked.="<input type=\"radio\" name=\"is_uploaded_file\" value=\"N\" checked>사용하지 않음";
	}
	
	if($bbs_write == "Y")
	{
		$write_checked="<input type=\"radio\" name=\"bbs_write\" value=\"Y\" checked>허가&nbsp;";
		$write_checked.="<input type=\"radio\" name=\"bbs_write\" value=\"N\">불허 ";
	}
	else
	{
		$write_checked="<input type=\"radio\" name=\"bbs_write\" value=\"Y\">허가&nbsp;";
		$write_checked.="<input type=\"radio\" name=\"bbs_write\" value=\"N\" checked>불허 ";
	}

	
	if($bbs_modify == "Y")
	{
		$modify_checked="<input type=\"radio\" name=\"bbs_modify\" value=\"Y\" checked>허가&nbsp;";
		$modify_checked.="<input type=\"radio\" name=\"bbs_modify\" value=\"N\">불허 ";
	}
	else
	{
		$modify_checked="<input type=\"radio\" name=\"bbs_modify\" value=\"Y\">허가&nbsp;";
		$modify_checked.="<input type=\"radio\" name=\"bbs_modify\" value=\"N\" checked>불허 ";
	}

	if($bbs_reply == "Y")
	{
		$reply_checked="<input type=\"radio\" name=\"bbs_reply\" value=\"Y\" checked>허가&nbsp;";
		$reply_checked.="<input type=\"radio\" name=\"bbs_reply\" value=\"N\">불허 ";
	}
	else
	{
		$reply_checked="<input type=\"radio\" name=\"bbs_reply\" value=\"Y\">허가&nbsp;";
		$reply_checked.="<input type=\"radio\" name=\"bbs_reply\" value=\"N\" checked>불허 ";
	}

	if($bbs_delete == "Y")
	{
		$delete_checked="<input type=\"radio\" name=\"bbs_delete\" value=\"Y\" checked>허가&nbsp;";
		$delete_checked.="<input type=\"radio\" name=\"bbs_delete\" value=\"N\">불허 ";
	}
	else
	{
		$delete_checked="<input type=\"radio\" name=\"bbs_delete\" value=\"Y\">허가&nbsp;";
		$delete_checked.="<input type=\"radio\" name=\"bbs_delete\" value=\"N\" checked>불허 ";
	}
?>