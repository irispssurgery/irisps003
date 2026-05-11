<?

	include "../../CObject.lib";


	$bbs = new CBbs();



	$info_bbs = $bbs->InfoBbs($HTTP_GET_VARS["bbscode_code"]);



	while(list($key, $value) = each($info_bbs))

	{

		$list_border_color = $value["list_border_color"];

		$title_image = $value["title_image"];

		$tail_image = $value["tail_image"];

		$is_uploaded_file = $value["is_uploaded_file"];

		$list_select_color = $value["list_select_color"];

		$list_number_color = $value["list_number_color"];

		$list_file_color = $value["list_file_color"];

		$list_title_color = $value["list_title_color"];

		$list_name_color = $value["list_name_color"];

		$list_signdate_color = $value["list_date_color"];

		$list_hit_color = $value["list_hit_color"];

	}

	

	if($is_uploaded_file == "Y")

	{

		$table_width="730";

		$board.="<table width=".$table_width." border=\"1\" bordercolor=".$list_border_color." cellpadding=\"3\" cellspacing=\"0\" bordercolorlight='silver' bordercolordark='white'>";

		$board.="<tr align=\"center\"> ";

		$board.="  <td width=\"30\" bgcolor=".$list_select_color.">선택</td>";
		$board.="  <td width=\"30\" bgcolor=".$list_select_color.">공지</td>";

		$board.="  <td width=\"57\" bgcolor=".$list_number_color.">순번</td>";

		$board.="  <td width=\"65\" bgcolor=".$list_file_color.">파일</td>";

		$board.="  <td width=\"253\" bgcolor=".$list_title_color.">제목</td>";

		$board.="  <td width=\"120\" bgcolor=".$list_name_color.">이름</td>";

		$board.="  <td width=\"111\" bgcolor=".$list_signdate_color.">날짜</td>";

		$board.="  <td width=\"54\" bgcolor=".$list_hit_color.">조회수</td>";

		$board.="</tr>";



		if($search_word && $search_key)

			$result_all = $bbs->listBbsAll($bbscode_code, $search_word, $search_key);

		else

			$result_all = $bbs->listBbsAll($bbscode_code, $search_word=null, $search_key=null);



		$page = new CPaging($result_all);

		$page->setBlock(10);

		$page->listByPage(10);

		

		if($search_word && $search_key)

			$bbs_list = $bbs->listBbs($bbscode_code, $search_word, $search_key);

		else

			$bbs_list = $bbs->listBbs($bbscode_code, $search_word=null, $search_key=null);



		if($search_word && $search_key)

			$total_count = $bbs->getTotalCount($bbscode_code, $search_word, $search_key);

		else

			$total_count = $bbs->getTotalCount($bbscode_code, $search_word=null, $search_key=null);



		$number = $total_count - (($PAGE-1)*10);

		######################################공지 게시물##################################################
		$notice_result=$bbs->getNoticeBbs($bbscode_code);
		$notice_count=count($notice_result);
		if(count($notice_result)>0)
		{
			foreach($notice_result as $key => $value)
			{
				if(isset($value["bbs_save_data"]) && isset($value["bbs_origin_data"]))

				{

					$file = "<a href=\"proc_download.html?bbs_save_data=".$value["bbs_save_data"]."&bbs_origin_data=".$value["bbs_origin_data"]."\">파일</a>";

				}

				else

				{

					$file="없음";

				}

				$board.="<tr> ";
				$board.="  <td align=\"center\"><font color=\"black\"><input type=\"checkbox\" name=\"bbs_idx[]\" value=".$value["bbs_idx"]."></font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">공지</font></td>";
				$board.="  <td align=\"center\">".$notice_count."</td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\"><a href=\"result_bbs_view.html?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."\">".$value["bbs_title"]."</a>&nbsp;".$new."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_name"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_signdate"]."</font></td>";
				$board.="  <td align=\"center\"><font color=\"black\">".$value["bbs_hit"]."</font></td>";
				$board.="</tr>";
				$board.="<tr> \n";
		        $board.="  <td height=\"1\" colspan=\"8\" background=\"/img/bg_dot.gif\"></td>\n";
				$board.="</tr>\n";
				
				$notice_count--;
			}
		}
		###################################################################################################

$number=$number-count($notice_result);

		if(count($bbs_list) == 0)

		{

			$board.="<tr bgcolor=ffffff>";

			$board.="<td colspan=\"8\" align=\"center\">올라온게시물이 없습니다.</td>";

			$board.="</tr>";

		}

		else

		{

			foreach($bbs_list as $key => $value)

			{

				if(isset($value["bbs_save_data"]) && isset($value["bbs_origin_data"]))

				{

					$file = "<a href=\"proc_download.html?bbs_save_data=".$value["bbs_save_data"]."&bbs_origin_data=".$value["bbs_origin_data"]."\">파일</a>";

				}

				else

				{

					$file="없음";

				}



				$board.="<tr> ";

				$board.="  <td align=\"center\"><input type=\"checkbox\" name=\"bbs_idx[]\" value=".$value["bbs_idx"]."></td>";

				if($value["is_notice"]=="Y")
					$board.="  <td align=\"center\">Y</td>";
				else
					$board.="  <td align=\"center\">N</td>";

				$board.="  <td align=\"center\">".$number."</td>";

				$board.="  <td align=\"center\">".$file."</td>";

	$board.="  <td ><a href=\"result_bbs_view.html?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&PAGE=".$PAGE."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."\">".$value["bbs_title"]."</a></td>";

				$board.="  <td align=\"center\"><a href=\"mailto:".$value["bbs_email"]."\">".$value["bbs_name"]."</a></td>";

				$board.="  <td align=\"center\">".$value["bbs_signdate"]."</td>";

				$board.="  <td align=\"center\">".$value["bbs_hit"]."</td>";

				$board.="</tr>";



				$number--;

			}

		}

		$board.="</table>";



		$show_page = $page->showPaging();

	}

	else

	{

		$table_width = "665";

		$board.="<table width=".$table_width." border=\"1\" bordercolor=".$list_border_color." cellpadding=\"3\" cellspacing=\"0\" bordercolorlight='silver' bordercolordark='white'>";

		$board.="<tr align=\"center\"> ";

		$board.="  <td width=\"30\" bgcolor=\"".$list_select_color."\">선택</td>";
		$board.="  <td width=\"30\" bgcolor=\"".$list_select_color."\">공지</td>";
		$board.="  <td width=\"57\" bgcolor=\"".$list_number_color."\">순번</td>";

		$board.="  <td width=\"253\" bgcolor=\"".$list_title_color."\">제목</td>";

		$board.="  <td width=\"120\" bgcolor=\"".$list_name_color."\">이름</td>";

		$board.="  <td width=\"111\" bgcolor=\"".$list_signdate_color."\">날짜</td>";

		$board.="  <td width=\"54\" bgcolor=\"".$list_hit_color."\">조회수</td>";

		$board.="</tr>";



	

		if($search_word && $search_key)

			$result_all = $bbs->listBbsAll($bbscode_code, $search_word, $search_key);

		else

			$result_all = $bbs->listBbsAll($bbscode_code, $search_word=null, $search_key=null);



		$page = new CPaging($result_all);

		$page->setBlock(10);

		$page->listByPage(10);



		if($search_word && $search_key)

			$bbs_list = $bbs->listBbs($bbscode_code, $search_word, $search_key);

		else

			$bbs_list = $bbs->listBbs($bbscode_code, $search_word=null, $search_key=null);



		if($search_word && $search_key)

			$total_count = $bbs->getTotalCount($bbscode_code, $search_word, $search_key);

		else

			$total_count = $bbs->getTotalCount($bbscode_code, $search_word=null, $search_key=null);



		$number = $total_count - (($PAGE-1)*10);



		######################################공지 게시물##################################################

		$notice_result=$bbs->getNoticeBbs($HTTP_GET_VARS["bbscode_code"]);

		if(count($notice_result)>0)

		{

			foreach($notice_result as $key => $value)

			{

				


				$board.="<tr> ";

				$board.="  <td align=\"center\"><input type=\"checkbox\" name=\"bbs_idx[]\" value=".$value["bbs_idx"]."></td>";	

				$board.="  <td align=\"center\">공지사항</td>";

				$board.="  <td ><a href=\"result_bbs_view.html?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."&PAGE=".$PAGE."\">".$value["bbs_title"]."</a></td>";

				$board.="  <td align=\"center\"><a href=\"mailto:".$value["bbs_email"]."\">".$value["bbs_name"]."</a></td>";

				$board.="  <td align=\"center\">".$value["bbs_signdate"]."</td>";

				$board.="  <td align=\"center\">".$value["bbs_hit"]."</td>";

				$board.="</tr>";



				$number--;

			}

		}

		

		###################################################################################################



		######################################일반 게시물##################################################

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

				$size=strlen($value["bbs_depth"]);



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



				$board.="<tr> ";

				$board.="  <td align=\"center\"><input type=\"checkbox\" name=\"bbs_idx[]\" value=".$value["bbs_idx"]."></td>";	

				$board.="  <td align=\"center\">".$number."</td>";

			$board.="  <td ><a href=\"result_bbs_view.html?bbscode_code=".$value["bbscode_code"]."&site_code=".$value["site_code"]."&bbs_idx=".$value["bbs_idx"]."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."&PAGE=".$PAGE."\">".$value["bbs_title"]."</a></td>";

				$board.="  <td align=\"center\"><a href=\"mailto:".$value["bbs_email"]."\">".$value["bbs_name"]."</a></td>";

				$board.="  <td align=\"center\">".$value["bbs_signdate"]."</td>";

				$board.="  <td align=\"center\">".$value["bbs_hit"]."</td>";

				$board.="</tr>";



				$number--;

			}

		}

		$board.="</table>";



		$show_page = $page->showPaging();

		##################################################################################################

	}

?>