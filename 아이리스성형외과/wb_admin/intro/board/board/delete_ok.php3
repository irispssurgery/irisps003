<?
ob_start();
session_start();
session_cache_limiter('private');

extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);
extract($HTTP_SESSION_VARS);
extract($HTTP_COOKIE_VARS);

include "../../../../config/dbconn.php3"; 
include "../../../../config/function.php3"; 
include "../../../auth_admin.php3";

$inst->Connect();

include "./board_function.php3";

$query="select passwd,filename1,vod_link from ezboard where db='$db' and no=$no"; 
$row=$inst->ExecFetch($query);
$user_passwd=$row[passwd];
$filename=$row[filename1];
$vod_link=$row[vod_link];

$query="select passwd from main_board_cfg where db='$db'"; 
$row=$inst->ExecFetch($query);
$admin_passwd=$row[passwd];


$query="delete from ezboard where db='$db' and no=$no";
$inst->Execute($query);		

if($filename){
	unlink("../../../../up_board/$db/$filename");
}		

if($vod_link){
	unlink("$file_path1/$vod_link");
}
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm?db=$db&page=$page&go_it=$go_it'>");
?>