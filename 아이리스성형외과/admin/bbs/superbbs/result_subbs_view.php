<?
	include "../../../CObject.lib";

	$bbscode = new CBbscode();

	$result = $bbscode->View($HTTP_GET_VARS["bbscode_idx"]);

	foreach($result as $key => $value)
	{
		$bbscode_idx=$value["bbscode_idx"];
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
		$h_image=$value["title_image"];
		$t_image=$value["tail_image"];
		$site_code=$value["site_code"];
	}
	
	if($h_image)
		$title_image = $h_image;
	else
		$title_image ="이미지 없음";
	
	if($t_image)
		$tail_image = $t_image;
	else
		$tail_image = "이미지 없음";

	if($is_uploaded_file == "Y")
		$file="허락";
	else
		$file="불허";
	
	if($bbs_write=="Y")
		$write="허락";
	else
		$write="불허";

	if($bbs_modify=="Y")
		$modify="허락";
	else
		$modify="불허";

	if($bbs_reply=="Y")
		$reply="허락";
	else
		$reply="불허";

	if($bbs_delete=="Y")
		$delete="허락";
	else
		$delete="불허";
?>