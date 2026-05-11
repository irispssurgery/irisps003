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
	$ip=getenv("REMOTE_ADDR");

	if ($use_xedit=='yes' || $use_xedit=='1') {
		$tabckeck = 'yes';
	}else{
		$tabcheck = 'no';
		$memo=Antiterror($memo); 
	}

	$title=addslashes(trim($title));
	$memo=addslashes(trim($memo));
 
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
	
	$file_path="../../../../up_board/$db";
	
	if($file_name[0] && $file_size[0]>0){
		$file_name1=$id.'_'.$file_name[0];
		$file_size1=$file_size[0];
		if($userfile != none){
			copy($file_tmp_name[0],"$file_path/$file_name1");
		}
		unlink($file_tmp_name[0]);
	}

	if($file_name[1] && $file_size[1]>0){
		$file_name2=$id.'_'.$file_name[1];
		$file_size2=$file_size[1];
		if($userfile1 != none){
			copy($file_tmp_name[1],"$file_path/$file_name2");
		}
		unlink($file_tmp_name[1]);
	}
	
	$date1=date("Y-m-d");
	$date2=date("A g:i");
	
	if (!$user_id){
		$user_name=$name;
		$user_pw=md5($passwd);
	}

	$query="select id,ref,step from ezboard where db='$db' and id='$id' and ref='$ref' order by step desc";
	$row=$inst->ExecFetch($query);
	if ($row){
		$ids=$row[id];
		$reff=$row[ref];
		$steps=$row[step];
	}
	$refs=$reff+1;
	$steps=$step+1;		
	
	$query="update ezboard set step=step+1 where db='$db' and id='$id' and step > '$step'";
	$row=$inst->Execute($query);

	$query="insert into ezboard(db,id,ref,step,name,email,title,title_color,font_size,date1,date2,ip,hit,tabcheck,passwd,filename1,filesize1,filename2,filesize2,filelist,content,list_del,list_top,list_top1,ximages) values ('$db',$id,$refs,$steps,'$user_name:::admin','$email','$title','$title_color','$font_size','$date1','$date2','$ip','1','$tabcheck','$user_pw','$file_name1','$file_size1','$file_name2','$file_size2','$flist1','$memo','no','no','no','$xedit_images')";

	$inst->Execute($query); 
$inst->disconnect();
echo("<meta http-equiv='refresh' content='0;url=list.htm?db=$db&page=$page&go_it=$go_it'>");
?>