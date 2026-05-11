<?
	if(!$mode)
	{
		include "../../CObject.lib";
	}
	else
	{
		include "./CObject.lib";
		/*include "../constant/db_config.def";
		include "../class/CDatabase.class";
		include "../class/CBbs.class";
		include "../class/CBbscode.class";
		include "../class/CPaging.class";

		$db = new CDatabase($dbName, $host, $id, $passwd);-*/
	}

	$bbs = new CBbs();

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

	if($is_uploaded_file == "Y")
	{
		$table_width="576";
		$board.="<table width=".$table_width." border=\"0\" bordercolor=".$list_border_color." cellpadding=\"3\" cellspacing=\"0\"  bordercolorlight='silver' bordercolordark='white'>";
		$board.=" <tr> \n";
        $board.="  <td height=\"2\" colspan=\"6\" bgcolor=\"#e6e6e6\"></td>\n";
        $board.="</tr>\n";
		$board.="<tr align=\"center\"> ";
		$board.="  <td width=\"40\" bgcolor=".$list_number_color." class=\"leftline\"><img src=\"./board_img/no.gif\" align=\"absmiddle\"></td>";
		//$board.="  <td width=\"65\" bgcolor=".$list_file_color."><img src=\"./board_img/file.gif\" align=\"absmiddle\"></td>";
		$board.="  <td bgcolor=".$list_title_color." align=\"left\"><img src=\"./board_img/title.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"100\" bgcolor=".$list_name_color."><img src=\"./board_img/name.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"90\" bgcolor=".$list_signdate_color."><img src=\"./board_img/date.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"40\" bgcolor=".$list_hit_color." class=\"rightline\"><img src=\"./board_img/level.gif\" align=\"absmiddle\"></td>";
		$board.="</tr>";
		$board.=" <tr> \n";
        $board.="  <td height=\"2\" colspan=\"6\" bgcolor=\"#e6e6e6\"></td>\n";
        $board.="</tr>\n";

		if($search_word && $search_key)
			$result_all = $bbs->listBbsAll($bbscode_code, $search_word, $search_key);
		else
			$result_all = $bbs->listBbsAll($bbscode_code, $search_word=null, $search_key=null);

		$page = new CPaging($result_all);
		$page->setBlock(BLOCK);
		$page->listByPage(LIST_BY_PAGE);
		
		if($search_word && $search_key)
			$bbs_list = $bbs->listBbs($bbscode_code, $search_word, $search_key);
		else
			$bbs_list = $bbs->listBbs($bbscode_code, $search_word=null, $search_key=null);

		if($search_word && $search_key)
			$total_count = $bbs->getTotalCount($bbscode_code, $search_word, $search_key);
		else
			$total_count = $bbs->getTotalCount($bbscode_code, $search_word=null, $search_key=null);

		$number = $total_count - (($PAGE-1)*LIST_BY_PAGE);

		######################################공지 게시물##################################################
		$notice_result=$bbs->getNoticeBbs($bbscode_code);
		if(count($notice_result)>0)
		{
			foreach($notice_result as $key => $value)
			{
				$board.="<tr> ";
				$board.="  <td align=\"center\"><font color=\"black\">공지</font></td>";
				$board.="  <td ><a href=\"".$LOCATION."?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view&function=notice\"><font color=\"red\">".$value["bbs_title"]."</a>&nbsp;".$new."</td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_signdate"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_hit"]."</font></td>";
				$board.="</tr>";
				$board.="<tr> \n";
		        $board.="  <td height=\"1\" colspan=\"6\" background=\"/img/bg_dot.gif\"></td>\n";
				$board.="</tr>\n";
				
				
			}
		}
		###################################################################################################
		$number=$number-count($notice_result);

		if(count($bbs_list) == 0)
		{
			$board.="<tr>";
			$board.="<td colspan=\"6\" align=\"center\">올라온게시물이 없습니다.</td>";
			$board.="</tr>";
		}
		else
		{
			foreach($bbs_list as $key => $value)
			{
				if(isset($value["bbs_save_data"]) && isset($value["bbs_origin_data"]))
				{
					$file = "<a href=\"./proc_download.html?bbs_save_data=".$value["bbs_save_data"]."&bbs_origin_data=".$value["bbs_origin_data"]."\">파일</a>";
				}
				else
				{
					$file="없음";
				}

				$size=strlen($value["bbs_depth"]);

				#################################53번이후의 게시물에 답변을 달면은 re에붉은색 보울드옵션을준다.##################
				if($size>1)
				{
					for($loop_init=1; $loop_init<=$size; $loop_init++)
					{
						$re.="&nbsp;";
					}
					$re.="<img src=\"/img/img_re.gif\" border=\"0\">";
					$title.="".$value["bbs_title"];
				}
				else
				{
					$title=$value["bbs_title"];
				}

				#################################################################################################################
				$title=cutString($title, 44);

				$board.="<tr> ";
				$board.="  <td align=\"center\"><font color=\"black\">".$number."</font></td>";
				//$board.="  <td align=\"center\">".$file."</td>";
	$board.="  <td><a href=\"".$LOCATION."?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&bbs_idx=".$value["bbs_idx"]."&PAGE=".$PAGE."&is_uploaded_file=".$is_uploaded_file."&mode=view\">".$re."".$title."</a></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_signdate"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_hit"]."</font></td>";
				$board.="</tr>";
				$board.="<tr> \n";
		        $board.="  <td height=\"1\" colspan=\"6\" background=\"/img/bg_dot.gif\"></td>\n";
				$board.="</tr>\n";

				$number--;
				unset($title);
				unset($re);
			}
		}
		$board.="</table>";

		$show_page = $page->showPaging();
	}
	else
	{
		$table_width = "576";
		$board.="<table width=".$table_width." border=\"0\" bordercolor=".$list_border_color." cellpadding=\"3\" cellspacing=\"0\"  bordercolorlight='silver' bordercolordark='white'>";
		$board.=" <tr> \n";
        $board.="  <td height=\"2\" colspan=\"5\" bgcolor=\"#e6e6e6\"></td>\n";
        $board.="</tr>\n";
		$board.="<tr align=\"center\"> ";
		$board.="  <td width=\"40\" bgcolor=\"".$list_number_color."\" class=\"leftline\"><img src=\"./board_img/no.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"340\" bgcolor=\"".$list_title_color."\" align=\"left\"><img src=\"./board_img/title.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"70\" bgcolor=\"".$list_name_color."\"><img src=\"./board_img/name.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"101\" bgcolor=\"".$list_signdate_color."\"><img src=\"./board_img/date.gif\" align=\"absmiddle\"></td>";
		$board.="  <td width=\"44\" bgcolor=\"".$list_hit_color."\" class=\"rightline\"><img src=\"./board_img/level.gif\" align=\"absmiddle\"></td>";
		$board.="</tr>";
		$board.=" <tr> \n";
        $board.="  <td height=\"2\" colspan=\"5\" bgcolor=\"#e6e6e6\"></td>\n";
        $board.="</tr>\n";
		
		if($search_word && $search_key)
			$result_all = $bbs->listBbsAll($bbscode_code, $search_word, $search_key);
		else
			$result_all = $bbs->listBbsAll($bbscode_code, $search_word=null, $search_key=null);

		$page = new CPaging($result_all);
		$page->setBlock(BLOCK);
		$page->listByPage(LIST_BY_PAGE);

		if($search_word && $search_key)
			$bbs_list = $bbs->listBbs($bbscode_code, $search_word, $search_key);
		else
			$bbs_list = $bbs->listBbs($bbscode_code, $search_word=null, $search_key=null);

		if($search_word && $search_key)
			$total_count = $bbs->getTotalCount($bbscode_code, $search_word, $search_key);
		else
			$total_count = $bbs->getTotalCount($bbscode_code, $search_word=null, $search_key=null);

		$number = $total_count - (($PAGE-1)*LIST_BY_PAGE);

		######################################공지 게시물##################################################
		$notice_result=$bbs->getNoticeBbs($bbscode_code);
		if(count($notice_result)>0)
		{
			foreach($notice_result as $key => $value)
			{
				$board.="<tr> ";
				$board.="  <td align=\"center\"><font color=\"black\">공지</font></td>";
				$board.="  <td ><a href=\"".$LOCATION."?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view&function=notice\"><font color=\"red\">".$value["bbs_title"]."</a>&nbsp;".$new."</td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_signdate"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_hit"]."</font></td>";
				$board.="</tr>";
				$board.="<tr> \n";
		        $board.="  <td height=\"1\" colspan=\"6\" background=\"/img/bg_dot.gif\"></td>\n";
				$board.="</tr>\n";
				
				
			}
		}
		###################################################################################################
		$number=$number-count($notice_result);

		if(count($bbs_list) == 0)
		{
			$board.="<tr>";
			$board.="<td colspan=\"5\" align=\"center\">올라온게시물이 없습니다.</td>";
			$board.="</tr>";
		}
		else
		{
			foreach($bbs_list as $key => $value)
			{
				$size=strlen($value["bbs_depth"]);

				#################################53번이후의 게시물에 답변을 달면은 re에붉은색 보울드옵션을준다.##################

					if($size>1)
					{
						for($loop_init=1; $loop_init<=$size; $loop_init++)
						{
							$re.="&nbsp;";
						}
						$re.="<img src=\"/img/img_re.gif\" border=\"0\">";
						$title.="".$value["bbs_title"];
					}
					else
					{
						$title=$value["bbs_title"];
					}

				#################################################################################################################

				$board.="<tr> ";
				$board.="  <td align=\"center\"><font color=\"black\">".$number."</font></td>";
			$board.="  <td >".$re."<a href=\"".$LOCATION."?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&mode=view\">".$title."</a>&nbsp;</td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
		
				if($value["bbs_signdate"]==date("Y-m-d"))
					$board.="  <td align=\"center\"><font color=\"blue\">".$value["bbs_signdate"]."</font></td>";
				else
					$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_signdate"]."</font></td>";

				if($value["bbs_hit"]>=20)
					$board.="  <td align=\"center\"><font color=\"red\">".$value["bbs_hit"]."</font></td>";
				else
					$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_hit"]."</font></td>";

				$board.="</tr>";
				$board.="<tr> \n";
		        $board.="  <td height=\"1\" colspan=\"6\" background=\"/img/bg_dot.gif\"></td>\n";
				$board.="</tr>\n";

				unset($re);
				unset($title);
				unset($new);

				$number--;
			}
		}
		$board.="</table>";

		$show_page = $page->showPaging();
	}

	if($bbs_write == "Y")
	{
		$write_button="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&is_uploaded_file=".$is_uploaded_file."&mode=write\"><img src=\"./board_img/btn_write.gif\" align=\"absmiddle\" border=\"0\"></a>";
	}
?>