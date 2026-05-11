<?
	if(!$mode)
	{
		include "../../CObject.lib";
	}
	else
	{
		include getenv("DOCUMENT_ROOT")."/CObject.lib";
		/*include "../constant/db_config.def";
		include "../class/CDatabase.class";
		include "../class/CBbs.class";
		include "../class/CBbscode.class";
		include "../class/CPaging.class";
		include "../class/CAdmin.class";
		include "function/common.func";

		$db = new CDatabase($dbName, $host, $id, $passwd);
		$ins_admin= new CAdmin();*/
	}

	
	if(!$user_id)
		OrderLogin("/member_login.html");

	
	$bbs = new CBbs();

	if($is_uploaded_file=="Y")
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
			$bbs_name = $value["bbs_name"];
			$bbs_idx = $value["bbs_idx"];
			$bbs_title = $value["bbs_title"];
		}

		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" value=".$user_name." readonly></td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";

		//$board_write.="    <input type=\"text\" name=\"bbs_email\" value=\"\" size=\"30\">";

		if($bbsadmin)
			$board_write.="    <input type=\"text\" name=\"bbs_email\" value=\"cardmaster@phone-china.com\" size=\"30\">\n";
		else
			$board_write.="    <input type=\"text\" name=\"bbs_email\"  size=\"30\">\n";

		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"55\" value=\"".$bbs_title."\" maxlength=\"70\">";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\"></textarea>";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <input type=\"file\" name=\"bbs_data\" size=\"40\">";
		$board_write.=" </tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		/*
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">й?</td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"20\">";
		$board_write.=" </tr>";*/
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
			$bbs_name = $value["bbs_name"];
			$bbs_idx=$value["bbs_idx"];
			$bbs_title = $value["bbs_title"];
			$bbs_content=$value["bbs_content"];
		}

		$board_write.="<tr> ";
		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" value=".$user_name." readonly></td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";


		//$board_write.="    <input type=\"text\" name=\"bbs_email\" value=\"\" size=\"30\">";

		if($bbsadmin)
			$board_write.="    <input type=\"text\" name=\"bbs_email\" value=\"cardmaster@phone-china.com\" size=\"30\">\n";
		else
			$board_write.="    <input type=\"text\" name=\"bbs_email\"  size=\"30\">\n";


		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"55\" value=\"".$bbs_title."\" maxlength=\"70\">";
		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";

		if($bbscode_code=="BBS1" || $bbscode_code=="BBS20" || $bbsadmin)
			$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\">".$bbs_content."</textarea>";
		else
			$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\"></textarea>";

		$board_write.="  </td>";
		$board_write.="</tr>";
		$board_write.="<tr> ";
        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";
        $board_write.="  </tr>";
		/*
		$board_write.="<tr> ";
		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">й?</td>";
		$board_write.="  <td width=\"439\" bgcolor=\"".$view_submajor_color."\">&nbsp;";
		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"20\">";
		$board_write.=" </tr>";*/
	}

  if($bbsadmin)
  {
	  $button.="<a href=\"result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."&PAGE=".$PAGE."\"><img src=\"./but/but_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
	  $button.="<a href=\"javascript:checkForm(document.bbs_reply);\"><img src=\"./but/but_ok.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
	  $button.="<a href=\"javascript:document.bbs_reply.reset();\"><img src=\"./but/but_can.gif\" align=\"absmiddle\" border=\"0\"></a>";
  }
  else
  {
	  $button.="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&mode=list&site_code=".$site_code."&PAGE=".$PAGE."\"><img src=\"./board_img/btn_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
	  $button.="<a href=\"javascript:checkForm(document.bbs_reply);\"><img src=\"./board_img/btn_ok.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";
	  $button.="<a href=\"javascript:document.bbs_reply.reset();\"><img src=\"./board_img/btn_cancle.gif\" align=\"absmiddle\" border=\"0\"></a>";
  }
?>