<?
	include "./CObject.lib";

	$data["hope_date"]=$hope_year."-".$hope_month."-".$hope_day;
	$data["hope_time"]=$hope_time;
	$data["book_name"]=$book_name;
	$data["book_phone"]=$book_phone;
	$data["book_email"]=$book_email;
	$data["book_content"]=$book_content;


	$insert_result=$register->mInsert($data);

	if(!$insert_result)
	{
		Error("예약접수를 하던중 오류가 발생하였습니다.");
	}
	else
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"정상적으로 에약접수가 완료되었습니다.\");";
		echo "	location.href='./online.html';";
		echo "</Script>";
	}
?>