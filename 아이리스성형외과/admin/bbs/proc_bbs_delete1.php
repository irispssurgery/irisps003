<?
	if(!$mode)
	{
		include "../../CObject.lib";
	}
	else
	{
		include getenv("DOCUMENT_ROOT")."/CObject.lib";
		/*include "../constant/db_config.def";
		include "../constant/common.def";
		include "../class/CDatabase.class";
		include "../class/CBbs.class";
		include "../class/CBbscode.class";
		include "../class/CPaging.class";
		include "../class/CAdmin.class";
		include "../function/common.func";

		$db = new CDatabase($dbName, $host, $id, $passwd);
		$ins_admin= new CAdmin();*/
	}

	$bbs = new CBbs();

	$is_admin = $ins_admin->isAdmined($user_id);
	
	if($is_admin<=0)
	{
		$writer=$bbs->getWriteName($bbs_idx);

		if($user_name!=$writer)
			Error("이 게시물의 작성자가 아니십니다.");
	}

	$result = $bbs->bbsDelete($bbscode_code, $bbs_idx, $is_uploaded_file);

	if(!$result)
		Error("삭제하던중 오류가 발생하였습니다.");


	Redirect("../../".$LOCATION."?bbscode_code=".$bbscode_code."&mode=list&site_code=".$site_code."");
?>