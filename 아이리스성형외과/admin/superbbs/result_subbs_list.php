<?
	include "../../../CObject.lib";

	$bbscode = new CBbscode();
	$bbs = new CBbs();

	$result_all = $bbscode->listBbsAll();

	$page = new CPaging($result_all);
	$page->setBlock(10);
	$page->listByPage(20);

	$result = $bbscode->listBbs();

	$number = $bbscode->getTotalCount();

	$count = $number - (($PAGE-1)*20);

	if(count($result)==0)
	{
		$list.="<tr>";
		$list.="<td colspan=\"8\" align=\"center\">올라온 게시물이 없습니다.</td>";
		$list.="</tr>";
	}
	else
	{
		foreach($result as $key => $value)
		{
			$totalcount_bbs=$bbscode->getTotalCountByCode($value["bbscode_code"]);

			 $list.="<tr> ";
			 $list.="	  <td width=\"52\" align=\"center\"><input type=\"checkbox\" name=\"bbscode_idx[]\" value=".$value["bbscode_idx"]."></td>";
			 $list.="	  <td width=\"67\" align=\"center\">".$count."</td>";
			  $list.="	  <td width=\"72\" align=\"center\">".$value["site_code"]."</td>";
			  $list.="	  <td width=\"92\" align=\"center\">".$value["bbscode_code"]."</td>";
			 $list.="	  <td width=\"180\"><a href=\"result_subbs_view.html?bbscode_idx=".$value["bbscode_idx"]."&site_code=".$value["site_code"]."\">".$value["bbscode_title"]."</a></td>";
			 $list.="	  <td width=\"108\" align=\"center\">".$value["bbscode_signdate"]."</td>";
			 $list.="	  <td width=\"66\" align=\"center\">".$totalcount_bbs."</td>";
			 $list.="	  <td width=\"105\" align=\"center\">".$site_list[$value["site_code"]]."</td>";
			 $list.="	  <td width=\"105\" align=\"center\"><a href=\"../result_mbbs_list.html?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."\">[이동]</a></td>";
			 $list.="</tr>";

			 $count--;
		}
	}

	$show_page = $page->showPaging();
	
?>