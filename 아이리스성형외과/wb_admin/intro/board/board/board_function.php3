<? 
ob_start();
session_start();
extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);

$table="main_board_cfg"; 
$query="select * from $table where db='$db'"; 
$row=$inst->ExecFetch($query); 
if ($row) 
{ 
	$db=$row[db];  
	$board_type=$row[board_type];  
	$boardname=$row[boardname];  
	$cool_use=$row[cool_use];  
	$cool_no=$row[cool_no];  
	$new_use=$row[new_use];  
	$new_time=$row[new_time];  
	$title_img=$row[title_img];  
	$title_length=$row[title_length];  
	$title_color=$row[title_color];  
	$content_color=$row[content_color];  
	$board_length=$row[board_length];  		
	$pagecount=$row[pagecount];  
	$block=$row[block];  
	$use_html=$row[use_html];
	$use_xedit=$row[use_xedit];  
	$use_secret=$row[use_secret];  
	$list_reply=$row[list_reply];  
	$list_access=$row[list_access];  
	$list_view=$row[list_view];  
	$list_write=$row[list_write]; 
	$mail_notice=$row[mail_notice];  
	$mail_admin=$row[mail_admin]; 
	$skin=$row[skin]; 
	$color1=$row[color1]; 
	$color2=$row[color2]; 
	$ip_admit=$row[ip_admit]; 
	$memo_admit=$row[memo_admit]; 
	$is_secret=$row[use_secret];
}

if (!$search){ 
	$total_query="select count(no) from ezboard where db='$db'"; 
	$total=$inst->ExecFetch_Var($total_query); 
}else{ 
	if ($mtitle && !$mcontent && !$mname) { 
      $where_option = "where ($mtitle like '%$search%')";
    } else if (!$mtitle && $mcontent && !$mname) {
      $where_option = "where ($mcontent like '%$search%')";
    } else if (!$mtitle && !$mcontent && $mname) {
      $where_option = "where ($mname like '%$search%')";
    } else if ($mtitle && $mcontent && !$mname) {
      $where_option = "where ($mtitle like '%$search%' or  $mcontent like '%$search%')";
    } else if ($mtitle && !$mcontent && $mname) {
      $where_option = "where ($mtitle like '%$search%' or  $mname like '%$search%')";
    } else if (!$mtitle && $mcontent && $mname) {
      $where_option = "where ($mcontent like '%$search%' or  $mname like '%$search%')";
    } else if ($mtitle && $mcontent && $mname) {
      $where_option = "where ($mtitle like '%$search%' or $mcontent like '%$search%' or  $mname like '%$search%')";
    }
	$total_query="select count(no) from ezboard $where_option and db='$db'"; 		
	$total=$inst->ExecFetch_Var($total_query); 
} 
$pages=ceil($total/$pagecount); 
$totalpage=ceil($pages); 
 
if ($totalpage=='0') $totalpage=1; 
if($page<=0) $page=1; 
 
$pg=$page; 
$num=$total-(($page-1)*$pagecount); 
$query_limit = ($pg-1)*$pagecount; 
 
$to_date=date('Y-m-d'); 
 
#################### 게시판에 쓰이는 각종 function #################### 
#function New_Use($new_use,$date1,$new_time)             // new 표시 
#function Step_Ref($step,$ref,$color,$id)                // 답변 표시
#function Alink($db,$skin)  //관리자모드 링크시 쓰임.
#function View($del,$title,$db,$no,$id,$pg,$title_color,$skin) // 제목클릭 
#function Cool_use_Hit($cool_use,$cool_no,$hit,$color)   // 조회수표시 
#function Pageing($page,$block,$totalpage,$db)           // pageing 
#function Del_Yes($del,$name,$email)                     // color값,작성자 
#################### 게시판에 쓰이는 각종 function #################### 
 
 
function admit_Yes($admit,$name,$email) 
{ 
	if ($admit=='no'){ 
		$color='gray'; 
	}else{ 
		$color=''; 
	} 
	if ($admit=='no'){ 
			$admit_font = "<font color='$color'>$name</font>"; 
	}else{ 
			$admit_font = "<a href=mailto:$email>$name</a>"; 
	} 
 
	$array1 = array($color,$admit_font); 
	return $array1; 
} 
 
function New_Use($new_use,$date1,$new_time) 
{ 
	if ($new_use!='no') 
	{ 
		$year=date(Y); 
		$month=date(n); 
		$today=date(j);   
 
		$today_time = mktime(0,0,0,$month, $today, $year);  
		$write_day=explode("-",$date1); 
				 
		$write_time = mktime(0,0,0,$write_day[1],$write_day[2],$write_day[0]);  
		$new_use_time = $write_time + 86400 * ($new_time-1); 
		$array1 = array($today_time,$new_use_time); 
		return $array1; 
	} 
}

function Step_Ref($step,$ref,$color,$id) 
{ 
	if ($step=='0'){ 
		if ($ref=='0') { 
			$step_ref = "<font color='$color'>$id</font>"; 
		} 
	}else{ 
		 $step_ref = " "; 
	} 
	return $step_ref; 
} 

function Alink($db,$skin) 
{ 
 
	echo ("  
	<a href='$PHPSELF?mode=admin&skin=$skin&db=$db' onmouseover=\"window.status='관리자 모드';return true;\" onmouseout=\"window.status='';\"> 
	<font color='gray'>[ Admin Mode ]</font></a>  
		"); 
} 
 
function View($title,$db,$no,$pg,$title_color,$font_size,$is_secret,$skin,$search,$mtitle,$mcontent,$mname) 
{ 
	if ($is_secret=='no'){
		echo (" 
			<a href='$PHPSELF?mode=view&db=$db&no=$no&page=$pg&skin=$skin&search=$search&mtitle=$mtitle&mcontent=$mcontent&mname=$mname' onmouseover=\"window.status='글 읽 기';return true;\" onmouseout=\"window.status='';\"><font color='$title_color' size='$font_size'>$title</font></a>
		");
	}else{
		echo (" 
			<a href='$PHPSELF?mode=secret&db=$db&no=$no&page=$pg&skin=$skin&search=$search&mtitle=$mtitle&mcontent=$mcontent&mname=$mname' onmouseover=\"window.status='비 밀 글';return true;\" onmouseout=\"window.status='';\"><font color='$title_color'>$title</font></a>&nbsp;<img src=../board/images/X.gif border='0'> 
		");
	}
}
function go_it_View($title,$db,$no,$pg,$title_color,$font_size,$is_secret,$skin,$search,$mtitle,$mcontent,$mname,$go_it) 
{ 
	if ($is_secret=='no'){
		echo (" 
			<a href='$PHPSELF?mode=view&go_it=$go_it&db=$db&no=$no&page=$pg&skin=$skin&search=$search&mtitle=$mtitle&mcontent=$mcontent&mname=$mname' onmouseover=\"window.status='글 읽 기';return true;\" onmouseout=\"window.status='';\"><font color='$title_color' size='$font_size'>$title</font></a>
		");
	}else{
		echo (" 
			<a href='$PHPSELF?mode=secret&go_it=$go_it&db=$db&no=$no&page=$pg&skin=$skin&search=$search&mtitle=$mtitle&mcontent=$mcontent&mname=$mname' onmouseover=\"window.status='비 밀 글';return true;\" onmouseout=\"window.status='';\"><font color='$title_color'>$title</font></a>&nbsp;<img src=../board/images/X.gif border='0'> 
		");
	}
}


function Cool_use_Hit($cool_use,$cool_no,$hit,$color) 
{ 
	if ($cool_use!='no' && $hit>=$cool_no){ 
		$Color = "<font color=red>$hit</font>"; 
	}else{ 
        $Color = "<font color='$color'>$hit</font></td>"; 
	}		 
	return $Color; 
} 
 
function File_Up_Board($no,$db,$filename,$filesize)
{
	if ($filename){		
		$filenames=strtolower($filename);
		$file = explode(".", $filenames);
		$disk=uploadicon($file[1]);

		$fileupboard="<a href='../../board/download.htm?no=$no&db=$db&filename=$filename' onmouseover=\"window.status='자 료 받 기';return true;\" onmouseout=\"window.status='';\"><img src='../../board/icon/$disk' border=0 width='16' height='16' alt='$filesize' valign='absbottom'> $filename</a>";
	 }else{
		 $fileupboard="-";
	 }
	 return $fileupboard;
}
?>