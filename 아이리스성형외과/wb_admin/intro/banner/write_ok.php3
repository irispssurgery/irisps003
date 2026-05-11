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

$query="select no from main_banner_group order by no desc";

$row=$inst->ExecFetch($query);
$no=$row[no];
if($row != 0){
   	$no+=1;
}else{
   	$no=1;
}


$query="insert into main_banner_group(title,date1) values ('$title','$date1')";

$inst->Execute($query); 
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm'>");
?>