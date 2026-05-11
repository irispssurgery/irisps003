<?
	$mime_form="From : ".$user_name."<".$user_email.">\n";
	$mime_form.="MIME-Version : 1.0\n";
	$mime_form.="Content-Type: text/html; charset=euc-kr\n";
	$mime_form.="Content-Transfer-Encoding : 8bit\n";

	$mime_form.="<html>\n";
	$mime_form.="<head>\n";
	$mime_form.="<title>Untitled Document</title>\n";
	$mime_form.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">\n";
	$mime_form.="<style type=\"text/css\">\n";
	$mime_form.="<!--\n";
	$mime_form.="table {  font-family: \"굴림체\"; font-size: 9pt}\n";
	$mime_form.="-->\n";
	$mime_form.="</style>\n";
	$mime_form.="</head>\n";

	$mime_form.="<body bgcolor=\"#FFFFFF\">\n";
	$mime_form.="<table width=\"500\" border=\"0\" cellpadding=\"3\" cellspacing=\"3\">\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td width=\"109\" bgcolor=\"#E8E8E8\" height=\"25\" align=\"center\">제 목</td>\n";
	$mime_form.="	<td width=\"375\" height=\"25\">&nbsp;".$title."</td>\n";
	$mime_form.="  </tr>\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td width=\"109\" bgcolor=\"#E8E8E8\" height=\"25\" align=\"center\">이 름</td>\n";
	$mime_form.="	<td width=\"375\" height=\"25\">&nbsp;".$user_name."</td>\n";
	$mime_form.="  </tr>\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td width=\"109\" bgcolor=\"#E8E8E8\" height=\"25\" align=\"center\">메 일</td>\n";
	$mime_form.="	<td width=\"375\" height=\"25\">&nbsp;".$user_email."</td>\n";
	$mime_form.="  </tr>\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td width=\"109\" bgcolor=\"#E8E8E8\" height=\"25\" align=\"center\">연락처</td>\n";
	$mime_form.="	<td width=\"375\" height=\"25\">&nbsp;".$user_tel."</td>\n";
	$mime_form.="  </tr>\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td width=\"109\" bgcolor=\"#E8E8E8\" height=\"25\" align=\"center\">상담사</td>\n";

	if($doctor=="sikbmw95@chollian.net")
		$doctor_name="서일경 원장님";
	else
		$doctor_name="경희상 원장님";

	$mime_form.="	<td width=\"375\" height=\"25\">&nbsp;".$doctor_name."</td>\n";

	$mime_form.="  </tr>\n";
	$mime_form.="  <tr>\n";
	$mime_form.="	<td height=\"25\" width=\"109\" bgcolor=\"#E8E8E8\" align=\"center\">내 용</td>\n";
	$mime_form.="	<td height=\"25\" width=\"375\">&nbsp;".nl2br($content)."</td>\n";
	$mime_form.="  </tr>\n";
	$mime_form.="</table>\n";
	$mime_form.="</body>\n";
	$mime_form.="</html>\n";

	if(!mail($doctor, $user_name."님께서 ".$doctor_name."께 보낸 메일입니다.", "", $mime_form))
		Error("전송하던중 오류가 발생하였습니다.");
	else
		$bFlag=true;


	if($bFlag)
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"정상적으로 메일이 전송되었습니다.\");";
		echo "	location.href=\"contact.html\";";
		echo "</Script>";
	}
?>