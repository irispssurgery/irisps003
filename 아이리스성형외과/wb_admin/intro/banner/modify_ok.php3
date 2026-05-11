<?
ob_start(); 
session_start(); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
extract($HTTP_SESSION_VARS); 
 
include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
 
include "../../auth_admin.php3";

$inst->Connect(); 
	$date1=date("Y-m-d");
    $title=addslashes(trim($title));

	
	$query="update main_banner_group set title='$title' where no='$no'";
	
	
	$inst->Execute($query); 
	$inst->disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm'>");