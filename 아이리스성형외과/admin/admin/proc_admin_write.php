<?
	include "../../CObject.lib";

	$data["admin_id"]=$HTTP_POST_VARS["admin_id"];
	$data["admin_pwd"]=$HTTP_POST_VARS["admin_pwd"];
	$data["admin_email"]=$HTTP_POST_VARS["admin_email"];

	$result=$ins_admin->CAdminSet($data);

	if(!$result)
		Error("관리자를 추가하던중 오류가 발생하였습니다.");
	else
		Redirect("result_admin_list.html");
?>