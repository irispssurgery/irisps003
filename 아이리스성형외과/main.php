<?
	include "./CObject.lib";

	$news_result=$bbs->getBbsByCount("BBS1", 5);

	if(count($news_result)<=0)
	{
		$news_list.="<tr> \n";
		$news_list.="	<td height=\"20\" colspan=\"2\" align=\"center\"><font color=\"#CCCCCC\">\n";
		$news_list.="	  </font><a href=\"#\"><font color=\"CBC3B2\">\n";
		$news_list.="	  등록된 게시물이 없습니다.</font></a></td>\n";
		$news_list.="  </tr>\n";
	}
	else
	{
		foreach($news_result as $key => $value)
		{
			$title=cutString($value["bbs_title"], 20);

			$news_list.="<tr> \n";
			$news_list.="	<td height=\"20\"><font color=\"#CCCCCC\"><img src=\"images/news_icon_01.gif\" width=\"9\" height=\"9\" align=\"absmiddle\"> \n";
			$news_list.="	  </font><a href=\"news.html?mode=view&bbs_idx=".$value["bbs_idx"]."&bbscode_code=".$value["bbscode_code"]."&is_uploaded_file=".$value["is_uploaded_file"]."\"><font color=\"CBC3B2\">\n";
			$news_list.="	  ".$title."</font></a></td>\n";
			$news_list.="	<td height=\"20\"><font color=\"938B77\">".$value["bbs_signdate"]."</font></td>\n";
			$news_list.="  </tr>\n";
		}
	}

	$column_result=$bbs->getBbsByCount("BBS2", 5);

	if(count($column_result)<=0)
	{
		$column_list.="<tr> \n";
		$column_list.="	<td height=\"20\" colspan=\"2\" align=\"center\"><font color=\"#CCCCCC\">\n";
		$column_list.="	  </font><a href=\"#\"><font color=\"CBC3B2\">\n";
		$column_list.="	  등록된 게시물이 없습니다.</font></a></td>\n";
		$column_list.="  </tr>\n";
	}
	else
	{
		foreach($column_result as $key => $value)
		{
			$title=cutString($value["bbs_title"], 20);

			$column_list.="<tr> \n";
			$column_list.="	<td height=\"20\"><font color=\"#CCCCCC\"><img src=\"images/news_icon_01.gif\" width=\"9\" height=\"9\" align=\"absmiddle\"> \n";
			$column_list.="	  </font><a href=\"column.html?mode=view&bbs_idx=".$value["bbs_idx"]."&bbscode_code=".$value["bbscode_code"]."&is_uploaded_file=".$value["is_uploaded_file"]."\"><font color=\"CBC3B2\">\n";
			$column_list.="	  ".$title."</font></a></td>\n";
			$column_list.="	<td height=\"20\"><font color=\"938B77\">".$value["bbs_signdate"]."</font></td>\n";
			$column_list.="  </tr>\n";
		}
	}

	$photo_result=$bbs->getBbsByCount("BBS5", 3);

	if(count($photo_result)<=0)
	{
		$photo_list.="<tr> \n";
		$photo_list.="	<td height=\"20\" colspan=\"2\" align=\"center\"><font color=\"#CCCCCC\">\n";
		$photo_list.="	  </font><a href=\"#\"><font color=\"000000\">\n";
		$photo_list.="	  등록된 게시물이 없습니다.</font></a></td>\n";
		$photo_list.="  </tr>\n";
	}
	else
	{
		foreach($photo_result as $key => $value)
		{
			$photo_list.="<td> \n";
			$photo_list.="<table width=\"90\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"102\">\n";
			$photo_list.="  <tr> \n";
			$photo_list.="	<td height=\"8\"><img src=\"images/photo_box_top.gif\" width=\"90\" height=\"8\"></td>\n";
			$photo_list.="  </tr>\n";
			$photo_list.="  <tr> \n";
			$photo_list.="	<td background=\"images/photo_box_bg.gif\" align=\"center\"><a href=\"photo.html?mode=view&bbs_idx=".$value["bbs_idx"]."&bbscode_code=".$value["bbscode_code"]."&is_uploaded_file=".$value["is_uploaded_file"]."\"><img src=\"/pds.data/".$value["bbs_save_data"]."\" width=\"74\" height=\"86\" border=\"0\"></a></td>\n";
			$photo_list.="  </tr>\n";
			$photo_list.="  <tr> \n";
			$photo_list.="	<td height=\"8\"><img src=\"images/photo_box_down.gif\" width=\"90\" height=\"8\"></td>\n";
			$photo_list.="  </tr>\n";
			$photo_list.="</table>\n";
		    $photo_list.="</td>\n";
		}
	}
?>
