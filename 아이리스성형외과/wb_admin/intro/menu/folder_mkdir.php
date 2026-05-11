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

$table = "km_menu";

if(!$folder){ jst_alert('메뉴 이름이 존재하지 않습니다.');exit;}
$folder = msg_add($folder);

if($depth==1 && $seq==0){
	
	$sort_sql   = "select if(MAX(SORT) IS NULL,1,MAX(sort)+1) as sort from $table";
	$sort = $inst->ExecValue($sort_sql); 
	if (!$sort) $sort='0';

	$check_sql   = "select count(*) from $table where flag='D' and vod_menu='$folder'";
	$check_dir = $inst->ExecValue($check_sql); 
	if (!$check_dir) $check_dirt='0';

	if($check_dir==0){
		$menu_dir="intro/sub".$sort."/";
		$dir="../../../intro/sub".$sort;
		$dir_image="../../../intro/sub".$sort."/image";

		$nowtime = time();
		$query = "insert into $table (seq,sort,depth,menu_name,menu_dir,menu_file,flag, reg_date) values('','$sort','$depth','$folder','$menu_dir','$menu_file','D','$nowtime')";

		if ($depth == 1){		//폴더 생성
			if(!is_dir($dir)){ // 동일 디렉토리검사
				$r1 = mkdir("$dir",0755); //디렉토리 생성
				$r2 = exec("chmod 707 $dir"); // 퍼미션 변경
			}

			if(!is_dir($dir_image)){ // 동일 디렉토리검사
				$r1 = mkdir("$dir_image",0755); //디렉토리 생성
				$r2 = exec("chmod 707 $dir_image"); // 퍼미션 변경
			}

			//기본 파일 생성
			$sub_left=$dir."/sub_left.htm";
			$fp = fopen($sub_left , "w");
			fwrite($fp, stripslashes(str_replace("\r\n", "\n", "")));
			fclose($fp);

			@chmod($pwd.$file , 0707);

			//기본 서브 페이지 복사
			$sub_page=$dir."/sub_page.htm";
			@copy("../../../config/sub_page.htm",$sub_page);
			@chmod($sub_page, 0707);
		}else{					//파일 생성
			$savefile  = $dir."/".$file_name;
			
			if (is_file($savefile)) getLink("","이미 존재하는 파일입니다.        ",-1);

			$fp = fopen($savefile , "w");
			fclose($fp);

			@chmod($savefile , 0707);
		}	

	}else{
		jst_alert('같은 이름의 메뉴가 있습니다');
		exit;
	}
}else{
	$sort_sql   = "select sort from $table where seq='$seq'";
	$sort = $inst->ExecValue($sort_sql); 
	if (!$sort) $sort='0';

	$check_sql   = "select count(*) from $table where flag='D' and vod_menu='$folder' and reply='$seq'";
	$check_dir = $inst->ExecValue($check_sql); 
	if (!$check_dir) $check_dirt='0';

	if($check_dir==0){
		$menu_dir="intro/sub".$sort."/";
		$dir="../../../intro/sub".$sort;
		$savefile  = $dir."/".$file_name;

		$nowtime = time();
		$query = "insert into $table (seq,sort,reply,depth,menu_name,menu_dir,menu_file,page_access,page_type,site_header,site_footer,flag,reg_date) values('','$sort','$seq','$depth', '$folder','$menu_dir','$file_name','1','html','sub_header.htm','sub_fotter.htm','D','$nowtime')";

		if (is_file($savefile)) getLink("","이미 존재하는 파일입니다. 파일명을 변경해 주세요        ",-1);
		$fp = fopen($savefile , "w");
		fclose($fp);
		@chmod($savefile , 0707);
	}else{
		jst_alert('같은 이름의 메뉴가 있습니다');
		exit;
	}	
}

$inst->Execute($query);  

?>
<script>
	opener.location.reload();
	self.close();
</script>