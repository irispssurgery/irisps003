<? 
ob_start(); 
session_start(); 
session_cache_limiter('private'); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
  
include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
include "../../auth_admin.php3"; 
 
$date1=date("Y-m-d"); 
$inst->Connect(); 

$all_list_access = "";
$all_list_view   = "";
$all_list_write  = ""; 
$all_list_reply  = "";
$all_file_attach = ""; 
$all_file_download  = "";

$limit_i = count($list_access);
if ($limit_i > 0 ){
	$all_list_access = get_Access($list_access,$limit_i);
}
$limit_j = count($list_view);
if ($limit_j > 0 ){
	$all_list_view = get_Access($list_view,$limit_j);
}
$limit_k = count($list_write);
if ($limit_k > 0 ){
	$all_list_write = get_Access($list_write,$limit_k);
}
$limit_l = count($list_reply);
if ($limit_l > 0 ){
	$all_list_reply = get_Access($list_reply,$limit_l);
}

$limit_m = count($file_attach);
if ($limit_m > 0 ){
	$all_file_attach = get_Access($file_attach,$limit_m);
}
$limit_n = count($file_download);
if ($limit_n > 0 ){
	$all_file_download = get_Access($file_download,$limit_n);
}

//게시판 등록여부를 체크한다.
$query ="SELECT db FROM main_board_cfg where db='$table_name'";
$exit_db=$inst->ExecFetch($query);

if ($exit_db){
		echo ("
			<script>
			window.alert('이미 등록된 게시판입니다. 다른 테이블명을 사용하세요!')
			history.go(-1)
			</script>
		");
		exit;
}else{ 

	$passwd1 = md5(passwd);


	$query  = " insert into main_board_cfg ". 
            " ( ". 
            " db,board_type,passwd,boardname,cool_use,use_secret,use_vod,use_nick ". 
			" ,cool_no,new_use,new_time,title_length,title_color ". 
			" ,content_color,board_length,pagecount,block,use_xedit,use_html,use_phone,list_reply,list_access ". 
			" ,list_view,list_write,file_attach,file_download,mail_notice,mail_admin,date1 ". 
			" ,ip_admit,memo_admit,admin_id,title_img,skin,color1,color2,color3,category ".
			" ) values ( ". 
            " '$table_name','$board_type','$passwd','$boardname','$cool_use','$use_secret','$use_vod','$use_nick' ". 
			" ,$cool_no,'$new_use','$new_time',$title_length,'$title_color' ". 
			" ,'$content_color','$board_length','$pagecount','$block','no','$use_html','$use_phone','$all_list_reply','$all_list_access' ". 
			" ,'$all_list_view','$all_list_write','$all_file_attach','$all_file_download','$mail_notice', '$mail_admin','$date1','$ip_admit','$memo_admit','$admin_user_id','$title_img','$skin','$color1','$color2','$color3','$category' )"; 

	$inst->Execute($query);  

	$query  = " CREATE TABLE if not exists ezboard ( ".                  
				"  no int(11) NOT NULL auto_increment, ". 
				"  db varchar(11) NOT NULL, " . 
				"  id int(11) NOT NULL, " . 
				"  ref tinyint(3) NOT NULL, " . 
				"  step tinyint(3) NOT NULL, " . 
				"  name varchar(30) NOT NULL, ". 
				"  passwd varchar(80) NOT NULL, ". 
				"  email varchar(50), ". 
				"  email_check char(1), ". 
				"  title varchar(100) NOT NULL, ". 
				"  content text NOT NULL, ".
				"  vod_link varchar(100) NOT NULL, ". 
				"  date1 date NOT NULL, ". 
				"  date2 varchar(10) NOT NULL, ". 
				"  ip varchar(15) NOT NULL, ". 
				"  hit varchar(5) NOT NULL, ". 
				"  down_hit varchar(5) NOT NULL default '0', ".
				"  tabcheck char(3) NOT NULL, ". 
				"  filename1 varchar(100) , ". 
				"  filesize2 varchar(10) , ". 
				"  filename2 varchar(100) , ". 
				"  filesize2 varchar(10), ". 
				"  list_del char(3) default 'no', ".			//글을 삭제한 경우 yes
				"  list_top char(3) default 'no', ".			//리스트 상단에 올 경우 yes
				"  is_secret char(3) default 'no', ".			//비공개 글일경우 yes
				"  ximages varchar(200), ".						//xedit 사용시
				"  filelist varchar(200), ".					//xfile 사용시
				"  PRIMARY KEY (no)) "; 

	mkdir("../../../up_board/$table_name",0777); // 추후 디렉토리에 따라서 수정 필요 
	chmod("../../../up_board/$table_name",0777);  // 추후 디렉토리에 따라서 수정 필요
	
if ($board_type=="gallery"){
	mkdir("../../../up_board/$table_name/thumbs",0777); // 추후 디렉토리에 따라서 수정 필요 
	chmod("../../../up_board/$table_name/thumbs",0777);  // 추후 디렉토리에 따라서 수정 필요
}
	$file_path = "../../../up_board";
	$copy_file1 = $file_path."/index.html";
	$copy_file2 = $file_path."/".$table_name."/index.html";

	copy($copy_file1,$copy_file2);
}
$inst->Disconnect(); 
 
echo "<meta http-equiv='refresh' content='0;url=list.htm'>"; 
?>