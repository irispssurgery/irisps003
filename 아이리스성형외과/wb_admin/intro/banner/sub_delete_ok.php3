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

$db_table="main_coolsite";
$query="select banner from main_coolsite where no='$no'"; 
$row=$inst->ExecFetch($query);
$banner=$row[banner];

if ($banner){
	@unlink("../../../up_load/banner/$banner");
}

$query="delete from $db_table where no='$no'";
$inst->Execute($query); 
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=sub_list.htm?group_id=$group_id'>");
?>