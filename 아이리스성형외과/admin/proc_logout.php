<?
	include "../CObject.lib";
	
	if(!session_unregister("user_id") || !session_unregister("user_name"))
	{
		Error("로그아웃을 하던중 오류가 발생하였습니다.");
	}
	else
	{
		echo "<Script language=\"javascript\">";
		echo "	top.document.location.href=\"index.html\"";
		echo "</Script>";
	}
?>