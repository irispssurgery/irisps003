<?

	if($bbsadmin)

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



		$db = new CDatabase($dbName, $host, $id, $passwd);*/

	}



	if(!$bbsadmin)

	{

		if(!$user_id)

			OrderLogin("/member_login.html");

	}

	

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





		$board_write.="<tr> ";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\"  width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\"  width=\"39\" height=\"13\"></td>\n";



		if($bbsadmin)

			$board_write.="  <td  bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" value=\"".$user_name."\"></td>\n";

		else

			$board_write.="  <td  bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" value=\"".$user_name."\" readonly></td>\n";



		$user_info=$member->getUser($user_id);





		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <input type=\"text\" name=\"bbs_email\" size=\"30\" value=".$user_info[0]["user_email"].">\n";

		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";



		if(!$bbsadmin)

		{

			$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\">\n";

		}

		else

		{

		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\">&nbsp;<input type=\"checkbox\" name=\"is_notice\" value=\"1\">공지선택\n";

		}



		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\"></textarea>\n";

		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>\n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_fileup.gif\" align=\"absmiddle\" width=\"55\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <input type=\"file\" name=\"bbs_data\" size=\"30\"> 이미지 업로드</td>\n";

		$board_write.=" </tr>";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		/*

		$board_write.="<tr> \n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">비밀번호</td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"20\"></td>\n";

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



		$board_write.="<tr> ";

		//$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"DDDDDD\"><img src=\"./board_img/view_name.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";



		if($bbsadmin)

			$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" ></td>\n";

		else

			$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;&nbsp;<input type=\"text\" name=\"bbs_name\" value=\"".$user_name."\" readonly></td>\n";



		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		$board_write.=" <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_email.gif\" align=\"absmiddle\" width=\"52\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";



		$user_info=$member->getUser($user_id);



		if($bbsadmin)

			$board_write.="    <input type=\"text\" name=\"bbs_email\" value=\"diseol@ebiz-korea.co.kr\" size=\"30\">\n";

		else

			$board_write.="    <input type=\"text\" name=\"bbs_email\"  size=\"30\" value=".$user_info[0]["user_eamil"].">\n";



		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr> \n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_title.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";



		if(!$bbsadmin)

		{

			$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\">\n";

		}

		else

		{

		$board_write.="    <input type=\"text\" name=\"bbs_title\" size=\"50\">&nbsp;<input type=\"checkbox\" name=\"is_notice\" value=\"1\">공지선택\n";

		}



		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		$board_write.="<tr>\n ";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\"><img src=\"./board_img/view_contents.gif\" align=\"absmiddle\" width=\"39\" height=\"13\"></td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <textarea name=\"bbs_content\" cols=\"65\" rows=\"15\"></textarea>\n";

		$board_write.="  </td>\n";

		$board_write.="</tr>\n";

		$board_write.="<tr> ";

        $board_write.="    <td height=\"2\" colspan=\"2\" bgcolor=\"#e6e6e6\"></td>";

        $board_write.="  </tr>";

		/*$board_write.="<tr> \n";

		$board_write.="  <td width=\"145\" align=\"center\" bgcolor=\"".$view_major_color."\">비밀번호</td>\n";

		$board_write.="  <td bgcolor=\"".$view_submajor_color."\">&nbsp;\n";

		$board_write.="    <input type=\"password\" name=\"bbs_passwd\" size=\"20\"></td>\n";

		$board_write.=" </tr>";	*/

	}



	if($bbsadmin)

	{

		$button="<a href=\"result_mbbs_list.html?bbscode_code=".$bbscode_code."&site_code=".$site_code."\"><img src=\"./board_img/btn_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";



		echo "<Script language=\"javascript\">";

		echo "	function checkForm(form)";

		echo "	{";

		echo "		if(!form.bbs_name.value)";

		echo "		{";

		echo "			alert(\"이름을 입력하지 않았습니다.\");";

		echo "			form.bbs_name.focus();";



		echo "			return;";

		echo "		}";





		echo "		if(!form.bbs_email.value)";

		echo "		{";

		echo "			alert(\"이메일을 입력하지 않았습니다.\");";

		echo "			form.bbs_email.focus();";



		echo "			return;";

		echo "		}";



		echo "		if(!form.bbs_title.value)";

		echo "		{";

		echo "			alert(\"제목을 입력하지 않았습니다.\");";

		echo "			form.bbs_title.focus();";



		echo "			return;";

		echo "		}";



		echo "		if(!form.bbs_content.value)";

		echo "		{";

		echo "			alert(\"내용을 입력하지 않았습니다.\");";

		echo "			form.bbs_content.focus();";



		echo "			return;";

		echo "		}";



		/*echo "		if(!form.bbs_passwd.value)";

		echo "		{";

		echo "			alert(\"비밀번호를 입력하지 않았습니다.\");";

		echo "			form.bbs_passwd.focus();";



		echo "			return;";

		echo "		}";*/



		echo "		form.action=\"./proc_bbs_write.html\";";

		echo "		form.submit();";

		echo "	}";

		echo "</Script>";

	}

	else

	{

		$button="<a href=\"".$LOCATION."?bbscode_code=".$bbscode_code."&site_code=".$site_code."\"><img src=\"./board_img/btn_list.gif\" align=\"absmiddle\" border=\"0\"></a>&nbsp;";



		echo "<Script language=\"javascript\">";

		echo "	function checkForm(form)";

		echo "	{";

		echo "		if(!form.bbs_name.value)";

		echo "		{";

		echo "			alert(\"이름을 입력하지 않았습니다.\");";

		echo "			form.bbs_name.focus();";



		echo "			return;";

		echo "		}";



		/*echo "		if(!form.bbs_email.value)";

		echo "		{";

		echo "			alert(\"이메일을 입력하지 않았습니다.\");";

		echo "			form.bbs_email.focus();";



		echo "			return;";

		echo "		}";*/



		echo "		if(!form.bbs_title.value)";

		echo "		{";

		echo "			alert(\"제목을 입력하지 않았습니다.\");";

		echo "			form.bbs_title.focus();";



		echo "			return;";

		echo "		}";



		echo "		if(!form.bbs_content.value)";

		echo "		{";

		echo "			alert(\"내용을 입력하지 않았습니다.\");";

		echo "			form.bbs_content.focus();";



		echo "			return;";

		echo "		}";



		/*echo "		if(!form.bbs_passwd.value)";

		echo "		{";

		echo "			alert(\"비밀번호를 입력하지 않았습니다.\");";

		echo "			form.bbs_passwd.focus();";



		echo "			return;";

		echo "		}";*/



		echo "		form.action=\"./admin/bbs/proc_bbs_write.html?LOCATION=".$LOCATION."\";";

		echo "		form.submit();";

		echo "	}";

		echo "</Script>";

	}

?>