<?
	include getenv("DOCUMENT_ROOT")."/CObject.lib";

	$isBook=$register->mLoginBook($book_name, $book_phone);

	if(!$isBook)
	{
		echo "<Script language=\"javascript\">";
		echo "	alert(\"예약접수 사항 데이타작 조회되지 않았습니다.\");";
		echo "	history.back();";
		echo "</Script>";

		exit;
	}

	if(!$PAGE)
		$PAGE=1;

	$resultAll=$register->mListAllEach($book_name, $book_phone);
	$page=new CPaging($resultAll);
	$page->setBlock(10);
	$page->listByPage(10);

	$result=$register->mListPartEach($book_name, $book_phone);
	
	$number=count($resultAll)-(($PAGE-1)*10);
	
	if($number<=0)
	{
		$book_list.="<tr>\n";
		$book_list.="<td height=\"25\" colspan=\"8\" align=\"center\">등록된 접수사항 없습니다.</td>\n";
		$book_list.="</tr>\n";
	}
	else
	{
		foreach($result as $key => $value)
		{
			$book_list.="<tr>\n";
			$book_list.="<td height=\"25\" align=\"center\">".$value["regsigndate"]."</td>\n";
			$book_list.="<td height=\"25\" align=\"center\">&nbsp;".$value["hope_date"]."<a></td>\n";
			$book_list.="<td height=\"25\" align=\"center\">&nbsp;".$value["book_name"]."</td>\n";
			$book_list.="<td height=\"25\" align=\"center\">".$value["address"]."&nbsp;".$value["book_phone"]."</td>\n";

			if($value["is_book"]=="Y")
				$book_list.="<td height=\"25\" align=\"center\"><b><font color=\"red\">접수완료></font></b></td>\n";
			else
				$book_list.="<td height=\"25\" align=\"center\">신청중</td>\n";

			$book_list.="</tr>\n";

			$number--;
		}
	}
?>