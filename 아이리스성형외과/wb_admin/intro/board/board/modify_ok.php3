<?
ob_start();
session_start();
extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);
extract($HTTP_SESSION_VARS);
extract($HTTP_ENV_VARS);

include "../../../../config/dbconn.php3"; 
include "../../../../config/function.php3"; 
include "../../../auth_admin.php3";

$inst->Connect();

include "board_function.php3"; 

	$ip=getenv("REMOTE_ADDR");

	$query="select * from ezboard where db='$db' and no=$no and id='$id' and ref='$ref' and step='$step'"; 
	$row=$inst->ExecFetch($query);

	$user_passwd=md5(trim($row[passwd]));
	$old_filename1=trim($row[filename1]);
	$old_filesize1=trim($row[filesize1]);
	$old_filename2=trim($row[filename2]);
	$old_filesize2=trim($row[filesize2]);

	$query="select passwd from main_board_cfg where db='$db'"; 
	$row=$inst->ExecFetch($query);
	$admin_passwd=md5($row[passwd]);
	
	if ($use_xedit=='yes' || $use_xedit=='1') {
		$tabckeck = 'yes';
	}else{
		$tabcheck = 'no';
		$memo=Antiterror($memo); 
	}

	$title=addslashes(trim($title));
	$memo=addslashes(trim($memo));


	$user_name=$name;
	$user_pw=md5($passwd);

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
	
	function uniq_name($fileName) {
		$dot = strrpos($fileName,".");
		$noext = substr($fileName,0, $dot);
		$ext = substr($fileName,$dot+1);
		$seq = 0;
		do {
			$savename = "${noext}_${seq}.${ext}";
			$seq++;
		} while (is_file($destprefix.$savename));

		return $savename;
	}

	if ($filelist){
		$filelist=explode("\n",$filelist);
		$flist1="";
		$i=0;
		while (list($key,$val)=each($filelist)){
			$flist=explode("\t",$val);
			$flist_name=$flist[0] . "\t" . $flist[1] . "\r";
			$flist_sep=explode("\t",$flist_name);
			$f_n[]=uniq_name($flist_sep[0]);	//파일명
			$f_s[]=$flist_sep[1];	//파일사이즈
			$i++;
		}
	}

	//echo "$f_n[0]";
	$file_path="../../../../up_board/$db";
	
	if($file_name[0] && $file_size[0]>0){
		$file_name1=$id.'_'.$file_name[0];
		$file_size1=$file_size[0];
		if($userfile != none){
			copy($file_tmp_name[0],"$file_path/$file_name1");
		}
		unlink($file_tmp_name[0]);
	}else{
		$file_name1=$old_filename1;
		$file_size1=$old_filesize1;
	}


	//기존의 첨부파일 삭제
	if ($localfilesave!="on"){
//		if($old_filename1) unlink($file_path."/".$old_filename1);
//		if($old_filename2) unlink($file_path."/".$old_filename2);
	}

	if(!strcmp($user_pw,$user_passwd) || !strcmp($user_pw,$admin_passwd)){
		if ($localfilesave=="on"){
			$query="update ezboard set name='$user_name:::admin',email='$email',title='$title',title_color='$title_color',font_size='$font_size',content='$memo',vod_link='$vod_link',ip='$ip',list_del='no',ximages='$xedit_images' where db='$db' and id='$id' and ref='$ref' and step='$step'";
		}else{
			$query="update ezboard set name='$user_name:::admin',email='$email',title='$title',title_color='$title_color',font_size='$font_size',content='$memo',vod_link='$vod_link',ip='$ip',list_del='no',ximages='$xedit_images',filename1='$file_name1',filesize1='$file_size1',filename2='$file_name2',filesize2='$file_size2' where db='$db' and id='$id' and ref='$ref' and step='$step'";
		}
		$inst->Execute($query); 

	}else{
		echo("
			<script>
				window.alert('비밀번호가 틀립니다. 다시 입력하세요.')
				history.go(-1)
			</script>
		");
		exit;
	}
$inst->Disconnect();
echo("<meta http-equiv='refresh' content='0;url=list.htm?db=$db&page=$page&go_it=$go_it'>");
?>