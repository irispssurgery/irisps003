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
$db_table="main_banner_group";
$db_table1="main_banner";

$query="select banner from $db_table1 where group_id='$no'"; 
$row=$inst->ExecFetch($query);
$filename=$row[banner];

	if($filename){
		unlink("../../../up_banner/$filename");
	}		


$query="delete from $db_table where no='$no'";
$inst->Execute($query); 

$query="delete from $db_table1 where group_id='$no'";
$inst->Execute($query); 
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm'>");
?>


