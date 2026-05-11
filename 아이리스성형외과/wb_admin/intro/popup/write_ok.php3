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


/*
//=============메뉴 추가=======================
if (($sd_2 > 0) && ($sd_2 < 10)) {
	$sd_2 = '0'.$sd_2;
}
if (($sd_3 > 0) && ($sd_3 < 10)) {
	$sd_3 = '0'.$sd_3;
}
if (($ed_2 > 0) && ($ed_2 < 10)) {
	$ed_2 = '0'.$ed_2;
}
if (($ed_3 > 0) && ($ed_3 < 10)) {
	$ed_3 = '0'.$ed_3;
}
$start_date=$sd_1.'-'.$sd_2.'-'.$sd_3;
$end_date=$ed_1.'-'.$ed_2.'-'.$ed_3;
*/
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

$date1=date("Y-m-d");
$title=addslashes(trim($title));

$query="select no from main_open_win order by no desc";

$row=$inst->ExecFetch($query);
$no=$row[no];
if($row != 0){
   	$no+=1;
}else{
   	$no=1;
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


/*
foreach ($HTTP_POST_FILES as $file){
	$file_name=$file['name'];
	$file_tmp_name=$file['tmp_name'];
	$file_size=$file['size'];
	$file_type=$file['type'];
	 
}


	if ($file_name){
		$file_name=$file_name;
		$file_size=$file_size;
		$file_name=$file_name;
			if($userfile != none){
				$up_popup_path = $HOME_PATH."up_load";
				//Up_Load($up_popup_path,"up_popup");
				$Up_Folder = $UP_FOLDER_HOME."/popup/".$file_name;
				copy($file_tmp_name,$Up_Folder);
				unlink($file_tmp_name);
			}
	}
*/

	if($file_name[0] && $file_size[0]>0){
		include "../../../config/fileSave.htm";
	}

	if($file_name[1] && $file_size[1]>0){
		include "../../../config/fileSave1.htm";
	}


$title=addslashes(trim($title));
//$content=addslashes(trim($content));
$query="insert into main_open_win(no,open,title,filename,filename2,content,top,left1,width,height,start_date,end_date,date1,image_use,title_color,font_size,url,target,popup_zone) values ('$no','$open','$title','$mfile_name1','$mfile_name2','$content','$top','$left1','$width','$height','$start_date','$end_date','$date1','$res','$title_color','$font_size','$url','$target','$popup_zone')";
//echo $query;
$inst->Execute($query); 
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=list.htm'>");
?>