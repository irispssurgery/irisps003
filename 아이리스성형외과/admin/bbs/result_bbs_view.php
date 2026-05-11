<?
	if(!$mode)
	{
		include "../../CObject.lib";
	}
	else
	{
		include getenv("DOCUMENT_ROOT")."CObject.lib";
		/*include "../constant/db_config.def";
		include "../class/CDatabase.class";
		include "../class/CBbs.class";
		include "../class/CBbscode.class";
		include "../class/CPaging.class";

		$db = new CDatabase($dbName, $host, $id, $passwd);*/
	}
	
	/*if($bbscode_code!="BBS3")
	{
		if(!$user_id)
			OrderLogin("../login/form_login_write.html");
	}*/

	$bbs = new CBbs();

	$result_hit = $bbs->IncreaseHit($bbscode_code, $bbs_idx);

	$info = $bbs->InfoBbs($bbscode_code);

	while(list($key, $value) = each($info))
	{
		$list_border_color = $value["list_border_color"];
		$view_major_color = $value["view_major_color"];
		$view_submajor_color = $value["view_submajor_color"];
		$bbs_reply = $value["bbs_reply"];
		$bbs_modify = $value["bbs_modify"];
		$bbs_delete = $value["bbs_delete"];
	}

	if($is_uploaded_file == "Y")
	{
		$result = $bbs->View($bbscode_code, $bbs_idx);

		foreach($result as $key => $value)
		{
			$bbs_idx = $value["bbs_idx"];
			$bbs_name = $value["bbs_name"];
			$bbs_userid = $value["bbs_userid"];
			$bbs_email = $value["bbs_email"];
			$bbs_title = $value["bbs_title"];
			$bbs_content = $value["bbs_content"];
			$bbs_save_data = $value["bbs_save_data"];
			$bbs_origin_data = $value["bbs_origin_data"];
		}

		if($bbs_save_data && $bbs_origin_data)
		{
			$file = "<a href=\"./admin/bbs/proc_download.html?bbs_save_data=".$bbs_save_data."&bbs_origin_data=".$bbs_origin_data."&site_code=".$site_code."\">".$bbs_origin_data."</a>";
		}
		else
		{
			$file = "파일없음";
		}

		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"90\" bgcolor=\"DDDDDD\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\"  width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;<font color=\"black\">".$bbs_name."</font></td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		/*$board_write.="<tr> ";
		$board_write.=" <td width=\"90\"  bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <font color=\"black\">".$bbs_email."</font>";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";*/
		$board_write.="<tr> ";
		$board_write.="  <td width=\"90\" bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <font color=\"black\">".$bbs_title."</font>";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"90\" bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" height=\"300\" bgcolor=\"".$view_submajor_color."\" valign=\"top\">";

		if($bbs_save_data)
		{
			$board_write.="    <img src=\"/pds.data/".$bbs_save_data."\" width=\"490\">";
			$board_write.="    <font color=\"black\">".$bbs_content."</font>";
		}
		else
		{
			$board_write.="    <font color=\"black\">".$bbs_content."</font>";
		}

		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		//$board_write.="  <td width=\"90\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>";
		$board_write.="  <td width=\"90\" bgcolor=\"DDDDDD\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    ".$file."";
		$board_write.=" </tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
	}
	else
	{
		$info = $bbs->InfoBbs($bbscode_code);

		while(list($key, $value) = each($info))
		{
			$list_border_color = $value["list_border_color"];
			$view_major_color = $value["view_major_color"];
			$view_submajor_color = $value["view_submajor_color"];
		}

		$result = $bbs->View($bbscode_code, $bbs_idx);

		foreach($result as $key => $value)
		{
			$bbs_idx = $value["bbs_idx"];
			$bbs_name = $value["bbs_name"];
			$bbs_userid = $value["bbs_userid"];
			$bbs_email = $value["bbs_email"];
			$bbs_title = $value["bbs_title"];
			$bbs_content = $value["bbs_content"];
			$bbs_save_data = $value["bbs_save_data"];
			$bbs_origin_data = $value["bbs_origin_data"];
		}
		
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		//$board_write.="  <td width=\"90\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"90\" bgcolor=\"DDDDDD\" class=\"text_title\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;<font color=\"black\">".$bbs_name."</font></td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		/*$board_write.="<tr> ";
		$board_write.=" <td width=\"90\" bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <font color=\"black\">".$bbs_email."</font>";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";*/
		$board_write.="<tr> ";
		$board_write.="  <td width=\"90\" bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <font color=\"black\">".$bbs_title."</font>";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"90\" bgcolor=\"".$view_major_color."\"><div style=\"margin-left:10px;margin-top:5px;margin-bottom:5px;\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></div></td>";
		$board_write.="  <td width=\"494\" bgcolor=\"".$view_submajor_color."\" height=\"300\" valign=\"top\">";
		if($bbs_save_data)
		{
			$board_write.="    <img src=\"/pds.data/".$bbs_save_data."\" width=\"490\">";
			$board_write.="    <font color=\"black\">".$bbs_content."</font>";
		}
		else
		{
			$board_write.="    <font color=\"black\">".$bbs_content."</font>";
		}
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
	}

	if($bbsadmin)
	{
		$button.= "<a href=\"result_mbbs_list.html?bbscode_code=".$bbscode_code."&PAGE=".$PAGE."&site_code=".$site_code."\"><img src=\"./but/but_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
		$button.="<a href=\"form_bbs_modify.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."&PAGE=".$PAGE."\"><img src=\"./but/but_mod.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
		$button.="<a 	href=\"form_bbs_reply.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."&PAGE=".$PAGE."\"><img src=\"./but/reply.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
		$button.="<a href=\"proc_bbs_delete.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&bbsadmin=".BBSADMIN."&PAGE=".$PAGE."\"><img src=\"./but/del.gif\" align=\"absmiddle\" border=\"0\"></a>";
	}
	else
	{
		$button.="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&mode=list&PAGE=".$PAGE."\"><img src=\"./board_img/btn_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";

			if($bbs_modify == "Y")
			{
				$button.="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&PAGE=".$PAGE."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&mode=modify\"><img src=\"./board_img/btn_modify.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
			}

			if($bbs_reply == "Y")
			{
				$button.="<a 	href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&PAGE=".$PAGE."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&mode=reply\"><img src=\"./board_img/btn_reply.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";

			}
			
			if($bbs_delete == "Y")
			{
				$button.="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."&PAGE=".$PAGE."&bbs_idx=".$bbs_idx."&is_uploaded_file=".$is_uploaded_file."&mode=delete&LOCATION=".$LOCATION."\"><img src=\"./board_img/btn_delete.gif\" align=\"absmiddle\" border=\"0\"></a>";
			}
	}
?>