<? session_start();
//include "../../lib/function.php";
//if(!$_SESSION[cookie_id]){ jst_alert('장시간 사용하지 않아서 강제 자동 로그아웃 되었습니다.'); jst_link('../'); exit;}

include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
//include "../../auth_admin.php3";
$inst->Connect();

function jst_alert($msg){ jst("alert('$msg')");}
function msg_add($msg){ return addslashes(strip_tags(chop(eregi_replace('<script','&lt;script',htmlspecialchars($msg, ENT_QUOTES))))); }
function msg_strip($msg){ return stripslashes($msg); }

$table = "km_vod_menu";

if(!$file_name){ jst_alert('파일 이름이 존재하지 않습니다.');exit;}
$file_name=msg_add($file_name);

$check_sql   = "select count(*) from $table where flag='$flag' and vod_menu='$file_name' and depth='$depth'";

$check_dir = $inst->ExecValue($check_sql); 
if (!$check_dir) $check_dirt='0';

if($check_dir!=0){
	jst_alert('같은 이름이 존재합니다.');
	exit;
}else{
	$query = "update $table set vod_menu='$file_name' where seq='$seq'";
	$inst->Execute($query);  
}
?>

<script>
	opener.location.reload();
	self.close();
</script>