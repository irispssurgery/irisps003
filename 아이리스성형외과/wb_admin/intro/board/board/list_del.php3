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

for($i=0; $i < count($no); $i++) 
{	
	if($no[$i]) 
	{    
		
		$query="select id,passwd,filename1,filename2 from ezboard  where db='$db' and no=$no[$i]";
		$row=$inst->ExecFetch($query);
		$id=$row[id];
		$filename1=$row[filename1];
		$filename2=$row[filename2];
		
		$query="delete from ezboard  where db='$db' and no=$no[$i]";
		$inst->Execute($query);		
		
		if($filename1){
			unlink("../../../../up_board/$db/$filename1");
		}
		if($filename2){
			unlink("../../../../up_board/$db/$filename2");
		}
  } 
}

$inst->Disconnect();
echo ("<meta http-equiv='Refresh' content='0; URL=list.htm?db=$db&page=$page'>");
?>