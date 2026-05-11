<?
	include getenv("DOCUMENT_ROOT")."/CObject.lib";

	if(!$PAGE)
		$PAGE=1;

	if($mode=="del")
	{
		$del_result=$register->mDeleteByidx($idx);
	}

	if($mode=="accept")
	{
		$accept_result=$register->mAccepRegister($idx);
	}

	if($mode=="cancel")
	{
		$cancel_result=$register->mCancelRegister($idx);
	}
	
	$resultAll=$register->mListAll();
	$page=new CPaging($resultAll);
	$page->setBlock(10);
	$page->listByPage(10);

	$result=$register->mListPart();
	
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
				$book_list.="<td height=\"25\" align=\"center\"><b><font color=\"red\"><a href=\"javascript:cancel(".$value["idx"].");\">접수완료</a></font></b></td>\n";
			else
				$book_list.="<td height=\"25\" align=\"center\"><a href=\"javascript:accept(".$value["idx"].");\">신청중</a></td>\n";

			$book_list.="<td height=\"25\" align=\"center\">&nbsp;<a href=\"javascript:mDel(".$value["idx"].");\">[삭제]</a></td>\n";
			$book_list.="</tr>\n";

			$number--;
		}
	}
?>