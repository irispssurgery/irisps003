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

		include "../class/CAdmin.class";

		include "../class/CBbs.class";

		include "../class/CBbscode.class";

		include "../class/CPaging.class";

		include "function/common.func";

		

		$db = new CDatabase($dbName, $host, $id, $passwd);

		$ins_admin= new CAdmin();*/

	}

	

	$bbs = new CBbs();



	$is_admin = $ins_admin->isAdmined($user_id);



	if(!$bbsadmin)

	{

		if($is_admin<=0)

		{

			$writer=$bbs->getWriteName($bbs_idx);



			if($user_name!=$writer)

				Error("이 게시물의 작성자가 아니십니다.");

		}

	}



	if($is_uploaded_file == "Y")

	{

		$body ="<body bgcolor=\"#FFFFFF\" onload=\"init();\">";

		$info = $bbs->InfoBbs1($bbscode_code);



		while(list($key, $value) = each($info))

		{

			$list_border_color = $value["list_border_color"];

			$view_major_color = $value["view_major_color"];

			$view_submajor_color = $value["view_submajor_color"];

		}

		

		$result = $bbs->View1($bbscode_code, $bbs_idx);



		foreach($result as $key => $value)

		{

			$bbs_name = $value["bbs_name"];

			$bbs_userid =  $value["bbs_userid"];

			$bbs_email = $value["bbs_email"];

			$bbs_title = $value["bbs_title"];

			$bbs_content = $value["bbs_content"];

			$bbs_save_data = $value["bbs_save_data"];

			$bbs_origin_data = $value["bbs_origin_data"];

			$bbs_passwd = $value["bbs_passwd"];

		}



		$board_write.="<tr> ";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">이름</td>";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" size=\"30\" value=\"".$bbs_name."\" readonly></td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <input type=\"text\" name=\"bbs_email\" size=\"30\" value=".$bbs_email.">";

		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";



		//$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"35\" value=\"".$bbs_title."\">";



		if(!$bbsadmin)

		{

			$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\" value=\"".$bbs_title."\">\n";

		}

		else

		{

		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\" value=\"".$bbs_title."\">&nbsp;<input type=\"checkbox\" name=\"is_notice\" value=\"1\">공지사항 으로 선택\n";

		}



		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\">".stripslashes($bbs_content)."</textarea>";

		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <input type=\"file\" name=\"bbs_data\" size=\"25\">&nbsp;";

		$board_write.="	   <input type=\"checkbox\" name=\"is_file\" onclick=\"chan();\">파일보존";

		$board_write.=" </tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		/*

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">비밀번호</td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"25\" value=".$bbs_passwd.">&nbsp;</td>";

		$board_write.=" </tr>";*/

	}

	else

	{

		$body ="<body bgcolor=\"#FFFFFF\">";

		$info = $bbs->InfoBbs1($bbscode_code);



		while(list($key, $value) = each($info))

		{

			$list_border_color = $value["list_border_color"];

			$view_major_color = $value["view_major_color"];

			$view_submajor_color = $value["view_submajor_color"];

		}



		$result = $bbs->View1($bbscode_code, $bbs_idx);



		foreach($result as $key => $value)

		{

			$bbs_name = $value["bbs_name"];

			$bbs_userid =  $value["bbs_userid"];

			$bbs_email = $value["bbs_email"];

			$bbs_title = $value["bbs_title"];

			$bbs_content = $value["bbs_content"];

			$bbs_passwd = $value["bbs_passwd"];

		}

		

		$board_write.="<tr> ";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" size=\"30\" value=".$bbs_name." readonly></td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <input type=\"text\" name=\"bbs_email\" size=\"30\" value=".$bbs_email.">";

		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";



		if(!$bbsadmin)

		{

			$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\" value=\"".$bbs_title."\">\n";

		}

		else

		{

		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\" value=\"".$bbs_title."\">&nbsp;<input type=\"checkbox\" name=\"is_notice\" value=\"1\">공지사항 으로 선택\n";

		}



		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\">".stripslashes($bbs_content)."</textarea>";

		$board_write.="  </td>";

		$board_write.="</tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		/*

		$board_write.="<tr> ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">비밀번호</td>";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;";

		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"25\" value=".$bbs_passwd.">&nbsp;</td>";

		$board_write.=" </tr>";*/

	}

	

	if($bbsadmin)

	{

	  $button.="<a href=\"result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."&PAGE=".$PAGE."\"><img src=\"./but/but_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;\n";

	  $button.="<a href=\"javascript:checkForm(document.bbs_modify);\"><img src=\"./but/but_ok.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;\n";

	  $button.="<a href=\"javascript:document.bbs_modify.reset();\"><img src=\"./but/but_can.gif\" align=\"absmiddle\" border=\"0\"></a>\n";

	}

	else

	{

	  $button.="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&mode=list&site_code=".$site_code."\"><img src=\"./board_img/btn_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;\n";

	  $button.="<a href=\"javascript:checkForm(document.bbs_modify);\"><img src=\"./board_img/btn_ok.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;\n";

	  $button.="<a href=\"javascript:document.bbs_modify.reset();\"><img src=\"./board_img/btn_cancle.gif\" align=\"absmiddle\" border=\"0\"></a>\n";

	}

?>