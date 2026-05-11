<?
	include "../../CObject.lib";
	
	$bbs = new CBbs();

	$data["bbscode_code"] = $HTTP_POST_VARS["bbscode_code"];
	$data["bbs_passwd"] = $HTTP_POST_VARS["bbs_passwd"];
	$data["bbs_name"] = $HTTP_POST_VARS["bbs_name"];
	$data["site_code"] = $HTTP_POST_VARS["site_code"];

	if($HTTP_POST_VARS["bbs_email"])
		$data["bbs_email"] = addslashes($HTTP_POST_VARS["bbs_email"]);

	$data["bbs_title"] = addslashes($HTTP_POST_VARS["bbs_title"]);
	$data["bbs_content"] = addslashes($HTTP_POST_VARS["bbs_content"]);
	$data["bbs_signdate"] = date("Y-m-d");
	

	if($is_notice=="1")
		$data["is_notice"]="Y";
	else
		$data["is_notice"]="N";

	if(is_uploaded_file($bbs_data))
	{
		$data["bbs_save_data"] = $bbs_data;
		$data["bbs_origin_data"] = $bbs_data_name;
	}

	$result = $bbs->Insert($data);

	if(!$result)
	{		
		echo "<Script language=\"javascript\">";
		echo "	alert(\"저장하던중 오류가 발생하였습니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}	

	if($bbsadmin)
		Redirect("result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."");
	else
		Redirect("../../".$LOCATION."?bbscode_code=".$bbscode_code."&mode=list&site_code=".$site_code."");
?>