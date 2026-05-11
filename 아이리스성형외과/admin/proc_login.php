<?
	include "../CObject.lib";

	$is_admin = $ins_admin->isAdmin($HTTP_POST_VARS["admin_id"], $HTTP_POST_VARS["admin_pwd"]);

	if($is_admin<=0)
	{
		Error("관리자가 아닙니다.");	
	}
	else
	{
		$user_id=$admin_id;
		$user_name="운영자";
		session_register("user_id");
		session_register("user_name");
		Redirect("main.html");
	}
?>