<?
	include "../../CObject.lib";

	$mime_form="From : <webmaster@irisbeauty.net>\n";
	$mime_form.="MIME-Version : 1.0\n";
	$mime_form.="Content-Type: text/html; charset=euc-kr\n";
	$mime_form.="Content-Transfer-Encoding : 8bit\n";
	$mime_form.=$title;
	$mime_form.=$content;

	$user_list=$member->userListAll();

	foreach($user_list as $key => $value)
	{
		if(!mail($value["email"], "아이리스 성형외과에서 ".$value["user_name"]."님께 보내드리는 메일입니다.", "", $mime_form))
			Error("전송하던중 오류가 발생하였습니다.");
		else
			$bFlag=true;
	}


	if($bFlag)
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"정상적으로 메일이 전송되었습니다.\");";
		echo "	location.href=\"../maill.html\";";
		echo "</Script>";
	}
?>