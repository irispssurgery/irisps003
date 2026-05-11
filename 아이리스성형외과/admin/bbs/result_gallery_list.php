<?
	include getenv("DOCUMENT_ROOT")."/CObject.lib";

	if(!$PAGE)
		$PAGE=1;


	//$resultAll=$bbs->listBbsAllGallery($code, $search_word, $search_key);

	$resultAll = $bbs->listBbsAll($bbscode_code, $search_word, $search_key);
	$page=new CPaging($resultAll);
	$page->setBlock(10);
	$page->listByPage(9);

	if($search_word && $search_key)		
		$bbs_list = $bbs->listBbsAllGallery($bbscode_code, $search_word, $search_key);
	else
		$bbs_list = $bbs->listBbsAllGallery($bbscode_code, $search_word=null, $search_key=null);

	$info_bbs = $bbs->InfoBbs($bbscode_code);

	foreach($info_bbs as $key => $value)
	{
		$list_border_color = $value["list_border_color"];
		$title_image = $value["title_image"];
		$tail_image = $value["tail_image"];
		$is_uploaded_file = $value["is_uploaded_file"];
		$list_number_color = $value["list_number_color"];
		$list_file_color = $value["list_file_color"];
		$list_title_color = $value["list_title_color"];
		$list_name_color = $value["list_name_color"];
		$list_signdate_color = $value["list_date_color"];
		$list_hit_color = $value["list_hit_color"];
		$bbs_write = $value["bbs_write"];
		$bbs_modify = $value["bbs_modify"];
		$bbs_reply = $value["bbs_reply"];
		$bbs_delete = $value["bbs_delete"];
		$link_color = $value["link_color"];
		$unlink_color = $value["unlink_color"];
	}

	$total_count=count($bbs_list);

	if(count($resultAll)<=0)
	{
		$gallery_list.="<table width=\"200\" border=\"0\" height=\"200\">\n";
		$gallery_list.="  <tr>\n";
		$gallery_list.="	<td>등록된 이미지가 없습니다.</td>\n";
		$gallery_list.="  </tr>\n";
		$gallery_list.="</table>\n";
	}
	else
	{
		$gallery_list.="<table width=\"200\" border=\"0\" height=\"200\">\n";

		for($i=0; $i<$total_count; $i++)
		{	
			if($i%3==0)
				$gallery_list.="<tr>\n";

			$gallery_list.="<td align=\"center\">\n";
			$gallery_list.="<table width=\"200\" border=\"0\" height=\"200\">\n";
			$gallery_list.="  <tr>\n";

			if(!$bbs_list[$i]["bbs_save_data"])
				$gallery_list.="	<td height=\"180\" align=\"center\"><a href=\"".$LOCATION."?bbscode_code=".$bbs_list[$i]["bbscode_code"]."&site_code=".$bbs_list[$i]["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$bbs_list[$i]["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view&function=notice&m_code=".$m_code."\"><img src=\"images/noimage.gif\" width=\"120\" height=\"160\"></a></td>\n";
			else
				$gallery_list.="	<td height=\"180\" align=\"center\"><a href=\"".$LOCATION."?bbscode_code=".$bbs_list[$i]["bbscode_code"]."&site_code=".$bbs_list[$i]["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$bbs_list[$i]["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view&function=notice&m_code=".$m_code."\"><img src=\"/pds.data/".$bbs_list[$i]["bbs_save_data"]."\" width=\"120\" height=\"160\" border=\"0\"></a></td>\n";

			$bbs_title=cutString($bbs_list[$i]["bbs_title"], 24);

			$gallery_list.="  </tr>\n";
			$gallery_list.="  <tr>\n";
			$gallery_list.="	<td align=\"center\"><a href=\"".$LOCATION."?bbscode_code=".$bbs_list[$i]["bbscode_code"]."&site_code=".$bbs_list[$i]["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$bbs_list[$i]["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view&function=notice&m_code=".$m_code."\">".$bbs_title."</a></td>\n";
			$gallery_list.="  </tr>\n";	
			$gallery_list.="</table>\n";
			$gallery_list.="</td>\n";
		
			if($i%3==2)
				$gallery_list.="</tr>\n";
		}

		$gallery_list.="</table>\n";
	}

	//if($bbs_write == "Y")
	//{
		$write_button="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&is_uploaded_file=".$is_uploaded_file."&mode=write\"><img src=\"./board_img/btn_write.gif\" align=\"absmiddle\" border=\"0\"></a>";
	//}
?>