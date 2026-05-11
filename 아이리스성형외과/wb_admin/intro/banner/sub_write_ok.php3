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

/*금지	
    $pattern=',';
	if(ereg($pattern, $title)) {
      echo("                                                                                          
		<script>                                                                                
			window.alert(', 를 제목에 사용하실수 없습니다.');                                
            history.go(-1)                                                                          
        </script>                                                                               
		");     
		exit;
	}
*/
$pattern1=',';
$pattern2='ㆍ';
$title=ereg_replace($pattern1, $pattern2, $title);
$date1=date("Y-m-d");
$title=addslashes(trim($title));

$query="select no from main_coolsite order by no desc";

$row=$inst->ExecFetch($query);
$no=$row[no];
if($row != 0){
   	$no+=1;
}else{
   	$no=1;
}
foreach ($HTTP_POST_FILES as $file){
	$file_name=$file['name'];
	$file_tmp_name=$file['tmp_name'];
	$file_size=$file['size'];
	$file_type=$file['type'];
}

if($file_name){
    $filename=$file_name;
	$filesize=$file_size;
	$filename=$no.$filename;
	
	if($file_tmp_name != none){
		$up_popup_path = $HOME_PATH."up_load";
		$Up_Banner = $UP_FOLDER_HOME."/banner/".$filename;
		copy($file_tmp_name,$Up_Banner);
		unlink($file_tmp_name);
	}
}

$query="insert into main_coolsite(title,homepage,date1,banner,seq,group_id) values ('$title','$homepage','$date1','$filename',$seq,'$group_id')";

//echo $query."<hr>";
$inst->Execute($query); 
$inst->Disconnect();

echo("<meta http-equiv='refresh' content='0;url=sub_list.htm?group_id=$group_id''>");
?>
