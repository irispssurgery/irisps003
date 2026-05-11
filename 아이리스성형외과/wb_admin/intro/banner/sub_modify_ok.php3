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

foreach ($HTTP_POST_FILES as $file){
	$file_name=$file['name'];
	$file_tmp_name=$file['tmp_name'];
	$file_size=$file['size'];
	$file_type=$file['type'];
}
 $pattern1=',';
 $pattern2='¤ý';

 $title=ereg_replace($pattern1, $pattern2, $title);


	$query="select banner from main_coolsite where no='$no'"; 
	$row=$inst->ExecFetch($query);
	$user_filename=$row[banner];

	if ($file_name){        
		$filename=$file_name;	    
		$filesize=$file_size;	    
		$filename=$no.$filename;
		//$filename=$banner_name;
		//$filesize=$banner_size;
		//$filename=$no.$filename;

		if ($user_filename){

			$Up_Banner = $UP_FOLDER_HOME."/banner/".$user_filename;
			unlink($Up_Banner);
		}
		if($file_tmp_name != none){
			$Up_Banner = $UP_FOLDER_HOME."/banner/".$filename;
			copy($file_tmp_name,$Up_Banner);
			unlink($file_tmp_name);
		}

	$query="update main_coolsite set title='$title',homepage='$homepage',banner='$filename',seq=$seq,group_id='$group' where no='$no'";	
	}else{

	$query="update main_coolsite set title='$title',homepage='$homepage',seq=$seq,group_id='$group' where no='$no'";
	}
	
	$inst->Execute($query); 
	$inst->disconnect();

echo("<meta http-equiv='refresh' content='0;url=sub_list.htm?group_id=$group'>");
?>



