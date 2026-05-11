<?
ob_start();
session_start();
session_cache_limiter('private');

extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);


include "../../../config/dbconn.php3";
include "../../auth_admin.php3";
$inst->Connect();

if ($db_table)
{
	$del_qry  = "delete from main_board_cfg where db='$db_table'";
	$inst->Execute($del_qry); 

	$del_qry2 = "delete from ezboard where db='$db_table'";
	$inst->Execute($del_qry2); 

	system("rm -rf ../../../up_board/$db_table"); 

	$inst->Disconnect();

	echo ("
		<meta http-equiv='refresh' content='0;url=list.htm'>
	");

}else{
	echo("
		<script>
			window.alert('삭제할 TABLE이 존재하지 않습니다.');
		    history.go(-1)
	    </script>
	");
	exit;
}
?>