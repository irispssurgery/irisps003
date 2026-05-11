<? 
ob_start(); 
session_start(); 
session_cache_limiter('private'); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
 
include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
include "../../auth_admin.php3"; 
$inst->Connect(); 

$all_list_access   = implode($_POST["list_access"], ",");
$all_list_view     = implode($_POST["list_view"], ",");

$all_list_write    = implode($_POST["list_write"], ",");
$all_list_reply    = implode($_POST["list_reply"], ",");
$all_file_attach   = implode($_POST["file_attach"], ",");
$all_file_download = implode($_POST["file_download"], ",");

if ($db_table) 
{

	$update_qry  = " update main_board_cfg set ". 
			    "  passwd         = '$passwd' ". 
				" , board_type    = '$board_type' ". 
				" , boardname     = '$boardname' ". 
				" , cool_use      = '$cool_use' ". 
				" , cool_no       = '$cool_no' ". 
				" , new_use       = '$new_use' ". 
				" , new_time      = '$new_time' ". 
				" , title_length  = '$title_length' ". 
				" , title_color   = '$title_color' ". 
				" , content_color = '$content_color' ". 
				" , board_length  = '$board_length' ". 
				" , pagecount     = '$pagecount' ". 
				" , block         = '$block' ". 
				" , use_html      = '$use_html' ". 
				" , use_secret    = '$use_secret' ". 
				" , use_vod		  = '$use_vod' ". 
				" , use_nick	  = '$use_nick' ". 
				" , use_phone	  = '$use_phone' ". 

				" , list_access   = '$all_list_access' ". 
				" , list_view     = '$all_list_view' ". 
				" , list_write    = '$all_list_write' ". 
				" , list_reply    = '$all_list_reply' ". 
				" , file_attach   = '$all_file_attach' ".
				" , file_download = '$all_file_download' ". 
				" , mail_notice   = '$mail_notice' ". 
				" , mail_admin    = '$mail_admin' ". 
		        " , ip_admit	  = '$ip_admit' ". 
		        " , admin_id	  = '$admin_user_id' ". 
		        " , title_img	  = '$title_img' ". 
		        " , memo_admit	  = '$memo_admit' ". 
		        " , skin   	      = 'skin1' ". 
		        " , category      = '$category' ". 
		        " , cautionWord   = '$cautionWord' ". 
		        " , cautionip     = '$cautionip' ". 

		        " where db='$db_table'"; 
	$inst->Execute($update_qry);  
	$inst->Disconnect(); 
	echo "<meta http-equiv='refresh' content='0;url=./modify.htm?db=$db_table'>"; 
}else{ 
	echo(" 
		<script> 
			window.alert('수정할 table이  존제하자 않습니다..'); 
		    history.go(-1) 
	    </script> 
	"); 
} 
?>