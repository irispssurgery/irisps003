<?
	include "./CObject.lib";

	$result=$member->isLogin($HTTP_POST_VARS["user_id1"], $HTTP_POST_VARS["user_pwd1"]);

	if($result<=0)
	{
		Error("등록된 회원정보가 존재하지 않습니다.");
	}
	else
	{
		$user_name=$member->getName($user_id1);
		$user_id=$user_id1;
		session_register("user_id");
		session_registeR("user_name");

		echo "<Script language=\"javascript\">";
		echo "	alert(\"정상적으로 로그인이 되었습니다.\");";
		echo "</Script>";
		
		Redirect("./main.html");
	}
?>