<?
ob_start();
session_start();
session_cache_limiter('private');

extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);
extract($HTTP_SESSION_VARS);
extract($HTTP_COOKIE_VARS);

include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
include "../../auth_admin.php3";
$inst->Connect();


if ($start_date > $end_date) {
 echo("
   <script>
    window.alert('게재기간 설정이 잘못 되었습니다. 다시 확인 바랍니다.')
    history.go(-1)
   </script>
 ");
   exit;
}
if ($upfile) 
{   
	
	$pattern='(^[!@#$%^&*()~`+=;_0-9a-zA-Z-]+([!@#$%^&*()~`+=;_0-9a-zA-Z-]+)*[.][0-9a-zA-Z]+([0-9a-zA-Z]+)*$)';
	
	if(!ereg("$pattern", $upfile)) {
		echo("                                                                                          
		<script>                                                                                
			window.alert('파일형식이 올바르지 않습니다.');                                
            history.go(-1)                                                                          
        </script>                                                                               
		");     
		exit;
	}
}
    $table="main_open_win";
	$query = "select * from $table where no=$no";
	$row=$inst->ExecFetch($query);
	if ($row){
		$pic1=$row[filename];
		$pic2=$row[filename2];
	}


$db="popup";
$file_path = $HOME_PATH."/up_load/$db";

foreach ($HTTP_POST_FILES as $file){
	$file_name[]=$file['name'];
	$file_tmp_name[]=$file['tmp_name'];
	$file_size[]=$file['size'];
	$file_type[]=$file['type'];

	$filename = explode(".", $file['name']);
	$extension=trim($filename[sizeof($filename)-1]);
		
	if($file['name']){
		if( $extension == 'inc' || $extension == 'php' || $extension == 'php3' || $extension == 'asp' || $extension == 'htm' || $extension == 'html' || $extension == 'cgi' || $extension == 'pl'){
			echo("
				<script>
					window.alert( ' Html, PHP 관련파일은 업로드할수 없습니다 !!!' )
					history.go(-1)
				</script>
			");
			exit;
		}
	}
}

	if($file_name[0] && $file_size[0]>0){
		include "../../../config/fileSave.htm";
	}else{
		$mfile_name1 = 	$pic1;
	}

	if($file_name[1] && $file_size[1]>0){
		include "../../../config/fileSave1.htm";
	}else{
		$mfile_name2 = $pic2;
	}

if ($res=='y'){ // 이미지
   $width=$width;
   $height=$height;   
}else{ // text
   if ($d_file=='y'){ $file_name=""; }
}


$date1=date("Y-m-d");
$title=addslashes(trim($title));	
		$query="update main_open_win set title='$title',open='$open',content='$content',filename='$mfile_name1',filename2='$mfile_name2',top='$top',left1='$left1',width='$width',height='$height',start_date='$start_date',end_date='$end_date',image_use='$res',font_size='$font_size',title_color='$color',url='$pop_url',target='$pop_target',popup_zone='$popup_zone' where no='$no'";

	$inst->Execute($query); 
	$inst->disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm'>");
?>