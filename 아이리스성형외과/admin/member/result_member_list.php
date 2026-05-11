<?
	include getenv("DOCUMENT_ROOT")."/CObject.lib";

	if(!$PAGE)
		$PAGE=1;

	if($mode=="del")
	{
		$del_result=$member->mDeleteMember($idx);
	}
	
	$resultAll=$member->userListAll();
	$page=new CPaging($resultAll);
	$page->setBlock(10);
	$page->listByPage(10);

	$result=$member->userListPart();
	
	$number=count($resultAll)-(($PAGE-1)*10);
	
	if($number<=0)
	{
		$member_list.="<tr bgcolor=#FFFFFF>\n";
		$member_list.="<td height=\"25\" colspan=\"8\" align=\"center\">등록된 회원이 없습니다.</td>\n";
		$member_list.="</tr>\n";
	}
	else
	{
		foreach($result as $key => $value)
		{
			$member_list.="<tr bgcolor=#FFFFFF>\n";
			$member_list.="<td height=\"25\" align=\"center\">&nbsp;".$number."</td>\n";
			$member_list.="<td height=\"25\" align=\"center\">&nbsp;<a href=\"./result_member_view.html?user_id=".$value["user_id"]."\">".$value["user_id"]."<a></td>\n";
			$member_list.="<td height=\"25\" align=\"center\">&nbsp;".$value["user_name"]."</td>\n";
			$member_list.="<td height=\"25\">".$value["address"]."&nbsp;".$value["detail_address"]."</td>\n";
			$member_list.="<td height=\"25\" align=\"center\">&nbsp;".$value["email"]."</td>\n";
			$member_list.="<td height=\"25\" align=\"center\">&nbsp;".$value["telephone"]."</td>\n";
			$member_list.="<td height=\"25\" align=\"center\"><a href=\"javascript:mDel(".$value["idx"].");\">[삭제]</a></td>\n";
			$member_list.="</tr>\n";

			$number--;
		}
	}
?>