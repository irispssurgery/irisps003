<?
	include "./CObject.lib";

	$data["user_id"]=$HTTP_POST_VARS["user_id"];
	$data["user_pwd"]=$HTTP_POST_VARS["user_pwd1"];
	$data["user_name"]=$HTTP_POST_VARS["user_name"];
	$data["zipcode"]=$HTTP_POST_VARS["zipcode"];
	$data["address"]=$HTTP_POST_VARS["address1"];
	$data["detail_address"]=$HTTP_POST_VARS["address2"];
	$data["telephone"]=$HTTP_POST_VARS["telephone"];
	$data["email"]=$HTTP_POST_VARS["email"];
	$data["phone"]=$HTTP_POST_VARS["phone"];

	$result=$member->mInsert($data);

	if(!$result)
	{
		Error("회원가입을 하던중 오류가 발생하였습니다.");
	}
	else
	{
		echo "<Script language=\"javascript\">\n";
		echo "	alert(\"회원가입이 정상적으로 이루워졌습니다.\");";
		echo "	location.href='./member_login.html';";
		echo "</Script>";
	}
?>