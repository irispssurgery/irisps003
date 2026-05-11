<?
preg_match("/([a-zA-Z0-9_.]+)$/", $PHP_SELF, $f1);
preg_match("/([a-zA-Z0-9_.]+)$/", __FILE__, $f2);
if($f1[1] == $f2[1]) exit;

$lnum = 30;
$recnum = 20;
$imgdir = $imgpath = "./image";
if (!$p) $p = 1;
$orderby = $orderby == 'ASC' ? 'DESC' : 'ASC';



function remove_remarks($sql) 
{
	$i = 0; 
	while($i < strlen($sql)) { 
		if($sql[$i] == "#" and ($sql[$i-1] == "\n" or $i==0)) { 
			$j=1;
			while($sql[$i+$j] != "\n") $j++;
			$sql = substr($sql,0,$i) . substr($sql,$i+$j);
		}
		$i++;
	}
	return($sql);
}
function split_sql_file($sql, $delimiter) 
{
	$sql = trim($sql);
	$char = "";
	$last_char = "";
	$ret = array();
	$in_string = true;

	for($i=0; $i<strlen($sql); $i++) {
		$char = $sql[$i];

		if($char == $delimiter && !$in_string) {
			$ret[] = substr($sql, 0, $i);
			$sql = substr($sql, $i + 1);
			$i = 0;
			$last_char = "";
		}

		if($last_char == $in_string && $char == ")")  $in_string = false;
		if($char == $in_string && $last_char != "\\") $in_string = false;
		elseif(!$in_string && ($char == "\"" || $char == "'") && ($last_char != "\\")) $in_string = $char;
		$last_char = $char;
	}

	if (!empty($sql)) $ret[] = $sql;
	return($ret);
}
function get_table_def($db, $table, $ctrn)
{
	$table = "ezboard";
	global $drop;
	$schema_create = "";
	if(!empty($drop)){
		$schema_create .= "DROP TABLE IF EXISTS $table;$ctrn";
	}
	$schema_create .= "CREATE TABLE $table ($ctrn";

	$result = mysql_db_query($db, "SHOW FIELDS FROM $table");
	while ($row = mysql_fetch_array($result))
	{
		$schema_create .= "   $row[Field] $row[Type]";
		if (!empty($row["Default"]))
		{
			$schema_create .= " DEFAULT '$row[Default]'";
		}
		if ($row["Null"] != "YES")
		{
			$schema_create .= " NOT NULL";
		}
		if ($row["Extra"] != "")
		{
			$schema_create .= " $row[Extra]";
		}
		$schema_create .= ",$ctrn";
	}
	$schema_create = ereg_replace(",".$ctrn."$", "", $schema_create);
	$result = mysql_db_query($db, "SHOW KEYS FROM $table");

	while ($row = mysql_fetch_array($result)){

		$kname=$row['Key_name'];
		if (($kname != "PRIMARY") && ($row['Non_unique'] == 0)) $kname="UNIQUE|$kname";
		if(!is_array($index[$kname])){
			$index[$kname] = array();
		}
		$index[$kname][] = $row['Column_name'];
	}
	while(list($x, $columns) = @each($index)){
		$schema_create .= ",$ctrn";
		if($x == "PRIMARY"){
			$schema_create .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
		}
		else if (substr($x,0,6) == "UNIQUE") {
			$schema_create .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
		}
		else {
			$schema_create .= "   KEY $x (" . implode($columns, ", ") . ")";
		}
	}
	$schema_create .= "$ctrn)";
	return (stripslashes($schema_create));
} 


function get_excel_content($db,$table,$SQL)
{
	$db="webroin";
//	$result = mysql_db_query($db, "SELECT * FROM ezboard where db='$table' $SQL");
	$result = mysql_db_query($db, "SELECT * FROM ezboard where db='$table'");

	print "<TABLE BORDER=1 CELLSPACING=1 CELLPADDING=1 WIDTH=100%>\r\n";
		
	print "<TR BGCOLOR=GOLD ALIGN=CENTER>\r\n";
	for($j = 0; $j < mysql_num_fields($result); $j++) {
		print "<TD width=80 height=16><B>".mysql_field_name($result,$j)."</B></TD>\r\n";
	}
	print "</TR>\r\n";

	while ($row = mysql_fetch_row($result)) {
		print "<TR>\r\n";
		for($j = 0; $j < mysql_num_fields($result); $j++) {
			print "<TD width=80 height=16>".stripslashes($row[$j])."</TD>\r\n";

		}
		print "</TR>\r\n";
	}
	print "</TABLE>\r\n";
	print "</HTML>";
}

function get_table_content($db, $table,$my_handler,$SQL)
{
	
//	$result = mysql_db_query($db, "SELECT * FROM $table $SQL");
	$result = mysql_db_query($db, "SELECT * FROM ezboard where db='$table' $SQL");
	$i = 0;
	while ($row = mysql_fetch_row($result))
	{
		set_time_limit(60);
//		$schema_insert = "INSERT INTO  $table VALUES(";
		$schema_insert = "INSERT INTO  ezboard VALUES(";
		for ($j=0; $j<mysql_num_fields($result);$j++)
		{
			if (!isset($row[$j]))
			{
				$schema_insert .= " NULL,";
			}
			elseif ($row[$j] != "")
			{
				$schema_insert .= " '".addslashes($row[$j])."',";
			}
			else 
			{
				$schema_insert .= " '',";
			}
		}
		$schema_insert = ereg_replace(",$", "", $schema_insert);
		$schema_insert .= ")";
		$my_handler(trim($schema_insert));
		$i++;
	}
	return (true);
}


function my_handler($sql_insert)
{
	global $asfile,$ctrn;
	echo "$sql_insert;$ctrn";
}


function table_header()
{
	global $THIS_FILE,$imgdir,$table,$MYSQL_DB;
?>
	<TABLE WIDTH=100% CELLSPACING=0 CELLPADDING=2 border=1>
<TR HEIGHT=30>
<!--	
	<TD>
	<INPUT TYPE=BUTTON VALUE='DB관리자' onclick="location.href='<?ECHO$THIS_FILE;?>';">
	<FONT COLOR=RED>
	* 사용중인 디비 : <A HREF='<?ECHO$THIS_FILE;?>'><FONT COLOR=BLUE><U><?ECHO $MYSQL_DB;?></U></FONT></A> , 
	선택된 테이블 : <B><FONT COLOR=BLUE><U><?ECHO $table;?></U></FONT></B>
	</FONT>
	</TD>
-->

	<TD ALIGN=left>
	<INPUT TYPE=BUTTON VALUE='속성' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','property');">
	<INPUT type=button value='보기' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','view');">
	<INPUT type=button value='추가' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','add');">
	<INPUT type=button value='삭제' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','delete');">
	<INPUT type=button value='비우기' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','empty');">
	<INPUT TYPE=BUTTON VALUE='구조백업' onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','dump_struct');">
	<INPUT TYPE=BUTTON VALUE='완전백업'  onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','dump_all');">
	<INPUT TYPE=BUTTON VALUE='엑셀백업'  onclick="TableSkill('<?ECHO$THIS_FILE;?>','1','<?ECHO$table?>','dump_excel');">
	</TD>
	</TR>
	</TABLE>

<?
}
function table_property()
{
	global $THIS_FILE,$imgdir,$table,$MYSQL_DB;
	?>

	<?table_header();?>

	<TABLE BGCOLOR=C0C0C0 WIDTH=100% CELLSPACING=1 CELLPADDING=1>
	<TR ALIGN=CENTER BGCOLOR=C8D696 HEIGHT=23>
	<TD><B>필드</B></TD>
	<TD><B>종류</B></TD>
	<TD><B>길이</B></TD>
	<TD><B>보기</B></TD>
	<TD><B>Null</B></TD>
	<TD><B>기본값</B></TD>
	<TD><B>추가</B></TD>
	<TD><B>실행</B></TD>
	</TR>

	<?
//	$i = 0; $result = mysql_db_query($MYSQL_DB, "SHOW FIELDS FROM $table");
	$i = 0; $result = mysql_db_query($MYSQL_DB, "SHOW FIELDS FROM ezboard");
	while ($row = mysql_fetch_array($result)) :$i++;

		$field_name = $row[0];
		$field_kind = explode("(", $row[1]);
		$field_length = explode(")", $field_kind[1]);
		$field_view = trim($field_length[1]);
		$field_null = $row[2];
		$field_default = $row[4];
		$field_atinc = trim($row[5]);
		$field_key = $row[3];
	?>	
		<FORM ACTION='<?ECHO$parent_file;?>' name='table_property_<?ECHO$i;?>'>
		<input type=hidden name=action value=''>
		<input type=hidden name=table value='<?ECHO$table;?>'>
		<input type=hidden name=field value='<?ECHO$field_name;?>'>

		<TR BGCOLOR=WHITE>

		<TD><input type=text name=field_name size=15 value='<?ECHO$field_name;?>'></TD>

		<TD><input type=text name=field_type size=14 value='<?ECHO strtoupper($field_kind[0]);?>'></TD>

		<TD><input type=text name=field_length size=3 value='<?ECHO$field_length[0];?>'></TD>
		<TD>
		<SELECT NAME=field_view>
		<OPTION VALUE=''></OPTION>
		<OPTION VALUE='BINARY'<?if($field_view=='binary'):?> selected<?endif;?>>BINARY</OPTION>
		<OPTION VALUE='UNSIGNED'<?if($field_view=='unsigned'):?> selected<?endif;?>>UNSIGNED</OPTION>
		<OPTION VALUE='UNSIGNED ZEROFILL'<?if($field_view=='unsigned zerofill'):?> selected<?endif;?>>UNSIGNED ZEROFILL</OPTION>
		</SELECT>	
		</TD>
		<TD>
		<SELECT NAME=field_null>
		<OPTION VALUE=''<?if($field_null):?> selected<?endif;?>>YES</OPTION>
		<OPTION VALUE='1'<?if(!$field_null):?> selected<?endif;?>>NO</OPTION>
		</SELECT>
		</TD>

		<TD><input type=text name=field_default size=10 value='<?ECHO$field_default;?>'></TD>

		<TD>
		<SELECT NAME=field_atinc>
		<OPTION VALUE='auto_increment'<?if($field_atinc):?> selected<?endif;?>>auto_increment</OPTION>
		<OPTION VALUE=''<?if(!$field_atinc):?> selected<?endif;?>></OPTION>
		</SELECT>	
		</TD>

		<TD ALIGN=CENTER BGCOLOR=FFFFF1 NOWRAP>
		
		<INPUT TYPE=BUTTON VALUE='변경' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','alter');">
		<INPUT TYPE=BUTTON VALUE='삭제' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','delete');">
		


		<?if($field_key != 'PRI'):?>
		<INPUT TYPE=BUTTON VALUE='기본' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','primary');"<?if(strstr($field_kind[0],"text")):?> disabled<?endif;?>>
		<?else:?>
		<INPUT TYPE=BUTTON VALUE='기본' onclick="TablePropertyChk1('table_property_<?ECHO$i;?>','primary');" style="background:gold;">
		<?endif;?>

		<?if($field_key != 'MUL'):?>
		<INPUT TYPE=BUTTON VALUE='IDX' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','index');"<?if(strstr($field_kind[0],"text")):?> disabled<?endif;?>>
		<?else:?>
		<INPUT TYPE=BUTTON VALUE='IDX' onclick="TablePropertyChk1('table_property_<?ECHO$i;?>','index');" style="background:#E17AE2">
		<?endif;?>

		<?if($field_key != 'UNI'):?>
		<INPUT TYPE=BUTTON VALUE='고유' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','unique');"<?if(strstr($field_kind[0],"text")):?> disabled<?endif;?>>
		<?else:?>
		<INPUT TYPE=BUTTON VALUE='고유' onclick="TablePropertyChk1('table_property_<?ECHO$i;?>','unique');" style="background:#56D9FA;">
		<?endif;?>

		<INPUT TYPE=BUTTON VALUE='Fulltext' onclick="TablePropertyChk('table_property_<?ECHO$i;?>','fulltext');"<?if(!strstr($field_kind[0],"text") && !strstr($field_kind[0],"varchar")):?> disabled<?endif;?>>
		</TD>

		</TR>
		</FORM>

	<?endwhile;?>

	<FORM ACTION='<?ECHO$parent_file;?>' name='table_property'>
	<input type=hidden name=action value='field_alter'>
	<input type=hidden name=new value='yes'>
	<input type=hidden name=table value='<?ECHO$table;?>'>
	<TR BGCOLOR=#FFDF0B>
	<TD><input type=text name=field size=15 value=''></TD>
	<TD>
	<SELECT NAME="field_type">
	<OPTION VALUE=''></OPTION>
	<OPTION VALUE=TINYINT>TINYINT</OPTION>
	<OPTION VALUE=SMALLINT>SMALLINT</OPTION>
	<OPTION VALUE=MEDIUMINT>MEDIUMINT</OPTION>
	<OPTION VALUE=INT>INT</OPTION>
	<OPTION VALUE=BIGINT>BIGINT</OPTION>
	<OPTION VALUE=FLOAT>FLOAT</OPTION>
	<OPTION VALUE=DOUBLE>DOUBLE</OPTION>
	<OPTION VALUE=DECIMAL>DECIMAL</OPTION>
	<OPTION VALUE=DATE>DATE</OPTION>
	<OPTION VALUE=DATETIME>DATETIME</OPTION>
	<OPTION VALUE=TIMESTAMP>TIMESTAMP</OPTION>
	<OPTION VALUE=TIME>TIME</OPTION>
	<OPTION VALUE=YEAR>YEAR</OPTION>
	<OPTION VALUE=CHAR>CHAR</OPTION>
	<OPTION VALUE=VARCHAR>VARCHAR</OPTION>
	<OPTION VALUE=TINYBLOB>TINYBLOB</OPTION>
	<OPTION VALUE=TINYTEXT>TINYTEXT</OPTION>
	<OPTION VALUE=TEXT>TEXT</OPTION>
	<OPTION VALUE=BLOB>BLOB</OPTION>
	<OPTION VALUE=MEDIUMBLOB>MEDIUMBLOB</OPTION>
	<OPTION VALUE=MEDIUMTEXT>MEDIUMTEXT</OPTION>
	<OPTION VALUE=LONGBLOB>LONGBLOB</OPTION>
	<OPTION VALUE=LONGTEXT>LONGTEXT</OPTION>
	<OPTION VALUE=ENUM>ENUM</OPTION>
	<OPTION VALUE=SET>SET</OPTION>
	</SELECT>
	</TD>
	<TD><input type=text name=field_length size=3 value=''></TD>
	<TD>
	<SELECT NAME=field_view>
	<OPTION VALUE=''></OPTION>
	<OPTION VALUE='BINARY'>BINARY</OPTION>
	<OPTION VALUE='UNSIGNED'>UNSIGNED</OPTION>
	<OPTION VALUE='UNSIGNED ZEROFILL'>UNSIGNED ZEROFILL</OPTION>
	</SELECT>	
	</TD>
	<TD>
	<SELECT NAME=field_null>
	<OPTION VALUE=''>YES</OPTION>
	<OPTION VALUE='1' selected>NO</OPTION>
	</SELECT>
	</TD>

	<TD><input type=text name=field_default size=10 value=''></TD>

	<TD>
	<SELECT NAME=field_atinc>
	<OPTION VALUE='auto_increment'>auto_increment</OPTION>
	<OPTION VALUE='' selected></OPTION>
	</SELECT>	
	</TD>

	<TD NOWRAP align=center>
	<SELECT NAME=field_nav STYLE='width:187;'>
	<OPTION VALUE='->'>테이블의 마지막</OPTION>
	<OPTION VALUE='<-'>테이블의 처음</OPTION>
	<?$result1 = mysql_db_query($MYSQL_DB, "SHOW FIELDS FROM $table");while ($row1 = mysql_fetch_array($result1)) :?>
	<OPTION VALUE='<?ECHO$row1[0];?>'><?ECHO$row1[0];?> 다음에</OPTION>
	<?endwhile;?>
	</SELECT>
	<INPUT TYPE=BUTTON VALUE='필드추가' onclick="TablePropertyChk('table_property','add');">
	</TD>

	</TR>
	</FORM>

	</TABLE>

<?
}
function getPageLinkMysql($lnum,$p,$tpage,$imgpath)
{	
	$g_q = "<script>function getPageGo(n){location.href='".str_replace("&p=$p","",getQueryStringMysql())."&p='+n;}</script>\n";
	$g_p1 = "<IMG src='$imgpath/prev1.gif' border='0' ALIGN=ABSMIDDLE>";
	$g_p2 = "<IMG src='$imgpath/prev2.gif' border='0' ALIGN=ABSMIDDLE>";
	$g_n1 = "<IMG src='$imgpath/next1.gif' border='0' ALIGN=ABSMIDDLE>";
	$g_n2 = "<IMG src='$imgpath/next2.gif' border='0' ALIGN=ABSMIDDLE>";
	$g_cn = "<IMG src='$imgpath/cutln.gif' border='0' ALIGN=ABSMIDDLE>";
	if($p < $lnum+1){$g_q .= $g_p1;}else{//$pp = intval(($p-1)/$lnum)*$lnum;
	if(!($p%$lnum)) {$pp = ((intval($p/$lnum)-1)*$lnum)-($lnum-1);}else {$pp = (intval($p/$lnum)*$lnum)-($lnum-1);}	
	$g_q .= "<A HREF='javascript:getPageGo($pp)';>$g_p2</A>";}
	$g_q .= $g_cn;
	$l = $term = $lnum;$f = 1;
	while ($f <= $tpage) {if (($f <= $p) && ($p <= $l)){
	if ($l <= $tpage)
	for ($page = $f; $page <= $l; $page++)
	($page == $p)? $g_q .= "<FONT COLOR=RED>$page</FONT>$g_cn" : $g_q .= "<A HREF='javascript:getPageGo($page)';>$page</A>$g_cn";
	else
	for ($page = $f; $page <= $tpage; $page++)
	($page == $p)? $g_q .= "<FONT COLOR=RED>$page</FONT>$g_cn" : $g_q .= "<A HREF='javascript:getPageGo($page)';>$page</A>$g_cn";
	}$f = $f + $term; $l = $l + $term;}
	if($tpage < $lnum || $tpage < $page) {$g_q .= $g_n1;}else{$np = $page; $g_q .= "<A HREF='javascript:getPageGo($np)';>$g_n2</A>";}
	return $g_q;
}
function getQueryStringMysql()
{
	global $PHP_SELF, $HTTP_GET_VARS, $HTTP_POST_VARS;
	if($HTTP_POST_VARS) $method = $HTTP_POST_VARS; $method_type = "post";
	if($HTTP_GET_VARS)  $method = $HTTP_GET_VARS;  $method_type = "get";
	if($method) while (list($key,$no) = each($method)) if(!strstr("method_type",$key)) $query .= $key."=".$no."&";
	return basename($PHP_SELF)."?".$query."method_type=".$method_type;
}
function table_list()
{
	global $THIS_FILE,$imgdir,$imgpath,$p,$DB_CONNECT,$lnum,$recnum,$DB;
	$status_result = mysql_query("SHOW TABLE STATUS", $DB_CONNECT);
?>
<TABLE WIDTH=100% CELLSPACING=1 CELLPADDING=1 BGCOLOR=C0C0C0>

<FORM NAME=table_list method=post action="<?ECHO$THIS_FILE;?>">
<INPUT TYPE=HIDDEN NAME=action VALUE=''>
<INPUT TYPE=HIDDEN NAME=p VALUE='<?ECHO$p;?>'>
<TR HEIGHT=25 BGCOLOR=C8D696 ALIGN=CENTER>
<TD> </TD>
<TD> <B>NO</B> </TD>
<TD> <B>TABLE</B> </TD>
<TD> <B>ROWS</B> </TD>
<TD> <B>용량(KB)</B> </TD>
<TD> <B>테이블타입</B> </TD>
<TD> <B>저장형식</B> </TD>
<TD> <B>속성</B> </TD>
<TD> <B>백업</B> </TD>
<TD> <B>실행</B> </TD>
</TR>

<?
while ( $rows = mysql_fetch_assoc($status_result) ) :
$table_array[] = "$rows[Name]|$rows[Rows]|".round(($rows['Data_length'] + $rows['Index_length'])/1024,2)."|$rows[Type]|$rows[Row_format]|";
$sum_size = $sum_size + ($rows['Data_length'] + $rows['Index_length']);
$sum_record = $sum_record + $rows['Rows'];
endwhile;
$data_num = sizeof($table_array);
$tpage = intval($data_num/$recnum);
if(intval($data_num/$recnum) > 0) { $tpage++; }
?>

<?for($i=($p-1)*$recnum;$i<=($p-1)*$recnum+$recnum-1;$i++) : $row = explode("|", $table_array[$i]);?>
<?if(($data_num-$i) > 0):?>
	<TR ALIGN=CENTER BGCOLOR=EFEFEF>

	<TD><INPUT TYPE=checkbox NAME="multi<?ECHO$i;?>" VALUE="<?ECHO$row[0];?>"></TD>

	<TD> <?ECHO($data_num-$i);?> </TD>

	<TD BGCOLOR=white>
	<INPUT TYPE=TEXT NAME=new_table_<?ECHO$i;?> value='<?ECHO$row[0];?>' SIZE=25 style='font-weight:bold;'>
	<INPUT TYPE=BUTTON VALUE='변경' onclick="TableNameChange('<?ECHO$THIS_FILE;?>','<?ECHO $row[0];?>',document.table_list.new_table_<?ECHO$i;?>.value,'<?ECHO$p;?>');">
	</TD>

	<TD BGCOLOR=white ALIGN=center>
	<?ECHO$row[1];?>
	</TD>

	<TD BGCOLOR=white ALIGN=center>
	<?ECHO $row[2];?>
	</TD>

	<TD BGCOLOR=white ALIGN=center>
	<?ECHO$row[3];?>
	</TD>

	<TD BGCOLOR=white ALIGN=center>
	<?ECHO$row[4];?>
	</TD>

	<TD ALIGN=center BGCOLOR=white>
	<INPUT TYPE=BUTTON VALUE='속성' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','property');">
	</TD>

	<TD ALIGN=center BGCOLOR=white>
	<INPUT TYPE=BUTTON VALUE='구조' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','dump_struct');">
	<INPUT TYPE=BUTTON VALUE='완전'  onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','dump_all');"<?if(!$row[1]):?> disabled<?endif;?>>
	<INPUT TYPE=BUTTON VALUE='엑셀'  onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','dump_excel');"<?if(!$row[1]):?> disabled<?endif;?>>
	</TD>
	
	<TD ALIGN=center BGCOLOR=white>
	<INPUT type=button value='보기' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','view');"<?if(!$row[1]):?> disabled<?endif;?>>
	<INPUT type=button value='추가' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','add');">
	<INPUT type=button value='삭제' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','delete');">
	<INPUT type=button value='비움' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','<?ECHO$row[0]?>','empty');"<?if(!$row[1]):?> disabled<?endif;?>>
	</TD>

	</TR>
<?endif;?>
<?endfor;?>
</TABLE>

<TABLE WIDTH=100% CELLSPACING=0 CELLPADDING=0>
<TR HEIGHT=40>
<TD>

<IMG SRC='<?ECHO$imgdir;?>/arrow_ltr.gif'>
<INPUT TYPE=BUTTON VALUE='모두체크' onclick="TableCheck('check');">
<INPUT TYPE=BUTTON VALUE='모두취소' onclick="TableCheck('cancel');">
<INPUT TYPE=BUTTON VALUE='선택반전' onclick="TableCheck('reverse');">

<IMG SRC='./image/blank.gif' WIDTH=30 HEIGHT=1>

<SELECT NAME='selected_table' onchange="TableCheck('change');">
<OPTION VALUE=''>선택한 것을:</OPTION>
<OPTION VALUE='mt_delete'>삭제</OPTION>
<OPTION VALUE='mt_empty'>비우기</OPTION>
<OPTION VALUE='mt_optimize'>테이블 최적화</OPTION>
<OPTION VALUE='mt_repair'>테이블 복구</OPTION>
</SELECT>

</TD>
<TD ALIGN=RIGHT>
<?if($data_num > $recnum):?>
<B><?ECHO getPageLinkMysql($lnum,$p,$tpage,"../bbs/lib/module/page/image");?></B>
<?endif;?>
</TD>
</TR>
</FORM>
</TABLE>





<table cellpadding=10>
<tr>
<td>

<P>
<form action='<?ECHO$PHP_SELF;?>' name=db_form>
<INPUT TYPE=HIDDEN NAME=action VALUE=create_table>
<INPUT TYPE=TEXT NAME='new_table'> <INPUT TYPE=SUBMIT VALUE=' 새 TABLE생성 '>
+ 총레코드수 : <?ECHO number_format($sum_record);?>개, 사용량 : <?ECHO number_format(intval($sum_size/1024));?>Kbytes
<P>
<B>- 새 TABLE생성 </B>: 현재 사용중인 <?=strtoupper($DB[kind])?> DB내에 새로운 TABLE을 생성합니다.<BR>
</FORM>

<form method='post' enctype='multipart/form-data' action='<?ECHO$PHP_SELF;?>'>
<INPUT TYPE=BUTTON VALUE='데이터베이스 완전백업' onclick="TableSkill('<?ECHO$THIS_FILE;?>','<?ECHO$p;?>','','db_all');">
<INPUT TYPE=HIDDEN NAME=action VALUE=upload>
<INPUT TYPE=FILE NAME=sql_file>
<INPUT TYPE=SUBMIT VALUE=' 데이터 INSERT '>
</FORM>
<B>- 데이터베이스 완전백업 </B>: 현재 사용중인 <?=strtoupper($DB[kind])?> DB내의 모든 데이터를 한번에 .sql파일로 백업받습니다.<BR>
<B>- 데이터 INSERT </B>: 백업받은 .sql 파일의 모든 데이터를 현재의 <?=strtoupper($DB[kind])?> DB내에 복구할때 사용합니다.<BR>
<BR>

<INPUT TYPE=BUTTON VALUE='킴스보드7 테이블 재생성' onclick="javascript:location.href='./TABLE_INSTALL.php'"> : 킴스보드7 에 필요한 테이블을 재 생성합니다.(이미 있을경우는 제외됨)

<P>
</td>
</tr>
</table>

<?
}

function table_view()
{
	global $THIS_FILE,$p,$MYSQL_DB,$DB_CONNECT,$table,$imgdir,$recnum,$lnum,$imgpath;
	global $keyword,$where,$sort,$orderby,$recn,$search_type,$cutlen;

	if ($recn) $recnum = $recn;

	if ($keyword && $where) {
		$keyword = str_replace(" ", ",", $keyword);
		$keyword_split = explode("," , $keyword);
		if ($keyword_split[1]) {
			for($j = 0; $j < sizeof($keyword_split); $j++) {
				$WHEREIS = "$WHEREIS $where LIKE '%".trim($keyword_split[$j])."%'";
				if($j < sizeof($keyword_split)-1) {
					$WHEREIS = "$WHEREIS $search_type ";
				}
			}
			$WHEREIS = "WHERE $WHEREIS";
		}
		else {
			$WHEREIS = "WHERE $where LIKE '%$keyword%'";
		}
	}
	if ($sort) {
		$st = "ORDER BY $sort $orderby";
	}


//	$data_row = mysql_fetch_array(mysql_query("SELECT count(*) FROM ezboard where db='$table' $WHEREIS", $DB_CONNECT));
	$data_row = mysql_fetch_array(mysql_query("SELECT count(*) FROM ezboard where db='$table' $WHEREIS", $DB_CONNECT));
	$data_num = $data_row[0];
	$tpage = intval($data_num/$recnum);
	if(intval($data_num/$recnum) > 0) { $tpage++; }
	$start_num = ($p-1)*$recnum;

//	$table_data = mysql_query("SELECT * FROM $table $WHEREIS $st LIMIT $start_num,$recnum",$DB_CONNECT);
	$table_data = mysql_query("SELECT * FROM ezboard where db='$table' $WHEREIS $st LIMIT $start_num,$recnum",$DB_CONNECT);
	$field_num = mysql_num_fields($table_data);
?>
	<?table_header();?>


	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=5>
	<FORM ACTION='<?ECHO$THIS_FILE;?>' METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=action VALUE=table_view>
	<INPUT TYPE=HIDDEN NAME=table VALUE="<?ECHO$table;?>">
	<INPUT TYPE=HIDDEN NAME=p VALUE="<?ECHO$p;?>">
	<INPUT TYPE=HIDDEN NAME=cutlen VALUE="<?ECHO$cutlen;?>">
	<TR HEIGHT=25 BGCOLOR=C8D696 ALIGN=CENTER>
		<TD COLSPAN=2 nowrap>
		<SELECT NAME=recn onchange="this.form.submit();">
		<OPTION VALUE=10<?if($recnum=="10"):?> selected<?endif;?>>10</OPTION>
		<OPTION VALUE=20<?if($recnum=="20"):?> selected<?endif;?>>20</OPTION>
		<OPTION VALUE=30<?if($recnum=="30"):?> selected<?endif;?>>30</OPTION>
		<OPTION VALUE=50<?if($recnum=="50"):?> selected<?endif;?>>50</OPTION>
		<OPTION VALUE=100<?if($recnum=="100"):?> selected<?endif;?>>100</OPTION>
		<OPTION VALUE=200<?if($recnum=="200"):?> selected<?endif;?>>200</OPTION>
		<OPTION VALUE=500<?if($recnum=="500"):?> selected<?endif;?>>500</OPTION>
		</SELECT>
		<?if(!$cutlen):?>
		<A HREF="<?ECHO$THIS_FILE;?>?action=table_view&table=<?ECHO$table;?>&sort=<?ECHO $val;?>&p=<?ECHO$p;?>&where=<?ECHO$where;?>&sort=<?ECHO$sort;?>&orderby=<?ECHO$orderby;?>&search_type=<?ECHO$search_type;?>&recn=<?ECHO$recnum;?>&keyword=<?ECHO$keyword;?>&cutlen=1"><IMG SRC='<?ECHO$imgdir;?>/fulltext.gif' BORDER=0 align=absmiddle alt='모두보이기'></A>
		<?else:?>
		<A HREF="<?ECHO$THIS_FILE;?>?action=table_view&table=<?ECHO$table;?>&sort=<?ECHO $val;?>&p=<?ECHO$p;?>&where=<?ECHO$where;?>&sort=<?ECHO$sort;?>&orderby=<?ECHO$orderby;?>&search_type=<?ECHO$search_type;?>&recn=<?ECHO$recnum;?>&keyword=<?ECHO$keyword;?>"><IMG SRC='<?ECHO$imgdir;?>/cuttext.gif' BORDER=0 align=absmiddle alt='일부보이기'></A>
		<?endif;?>
		</TD>

	<?for($j = 0; $j < $field_num; $j++):$val=mysql_field_name($table_data,$j);?>
		<TD>
		<A HREF='<?ECHO$THIS_FILE;?>?action=table_view&table=<?ECHO$table;?>&sort=<?ECHO $val;?>&p=<?ECHO$p;?>&where=<?ECHO$where;?>&sort=<?ECHO$val;?>&orderby=<?ECHO$orderby;?>&search_type=<?ECHO$search_type;?>&recn=<?ECHO$recnum;?>&keyword=<?ECHO$keyword;?>'>
		<FONT COLOR=BLUE><B><?ECHO $val;?></B></FONT>
		</A>
		</TD>
	<?endfor;?>

	</TR>
	<? $gque = mysql_field_name($table_data,0); ?>
	<?while ($list = mysql_fetch_array($table_data)):$i = 0;?>
	<TR BGCOLOR="<?if($i%2):?>#F7FBFF<?else:?>#EFF3F7<?endif;?>">
		<TD NOWRAP> <A HREF="<?ECHO$THIS_FILE;?>?action=table_modify&table=<?ECHO$table;?>&where=<?ECHO$gque;?>&value=<?ECHO$list[0];?>&p=<?ECHO$p;?>"><FONT COLOR=BLUE>수정</FONT></A> </TD>
		<TD NOWRAP> <A style='cursor:hand;' onclick="return RecordDelCheck('<?ECHO$THIS_FILE;?>','<?ECHO$table;?>','<?ECHO$gque;?>','<?ECHO$list[0];?>','<?ECHO$p;?>');"><FONT COLOR=BLUE>삭제</FONT></A> </TD>

		<?for($j = 0; $j < $field_num; $j++):?>
			<?if(!$cutlen):?>
			<TD WRAP><?ECHO htmlspecialchars(substr($list[$j],0,50));?></TD>
			<?else:?>
			<TD WRAP><?ECHO htmlspecialchars($list[$j]);?></TD>
			<?endif;?>
		<?endfor;?>

	</TR>

	<?$i++; endwhile;?>
	
	</TABLE>

	<TABLE WIDTH=100% CELLSPACING=0 CELLPADDING=0>
	<TR>
	<TD WIDTH=100% BGCOLOR=C8D696>
	<TABLE WIDTH=980>
	<TR>
	<TD ALIGN=CENTER>

	<?if($data_num > $recnum):?>
	<B><?ECHO getPageLinkMysql($lnum,$p,$tpage,"../bbs/lib/module/page/image");?></B>
	<?endif;?>

	</TD>
	</TR>
	</TABLE>
	</TD>
	</TR>
	</TABLE>

	<TABLE WIDTH=980>
	<TR>
	<TD ALIGN=CENTER WIDTH=100% HEIGHT=40>


	<SELECT NAME=where>
	<OPTION VALUE=''>검색필드 선택</OPTION>
	<?for($j = 0; $j < $field_num; $j++): $val = mysql_field_name($table_data,$j);?>
		<OPTION VALUE="<?ECHO $val;?>"<?if($val == $where):?> selected<?endif;?>><?ECHO $val;?></OPTION>
	<?endfor;?>
	</SELECT>

	<INPUT TYPE=TEXT NAME=keyword VALUE="<?ECHO $keyword;?>">

	<SELECT NAME=search_type>
	<OPTION VALUE='OR'<?if($search_type=="OR"):?> selected<?endif;?>>OR</OPTION>
	<OPTION VALUE='AND'<?if($search_type=="AND"):?> selected<?endif;?>>AND</OPTION>
	</SELECT>
	
	<INPUT TYPE=SUBMIT VALUE=" SEARCH ">
	<INPUT TYPE=BUTTON VALUE=" 초기화 " onclick="location.href='<?ECHO$THIS_FILE;?>?action=table_view&table=<?ECHO$table;?>';">
	* 복수검색시 공백이나 콤마(,)로 구분

	</TD>
	</TR>

	</FORM>
	</TABLE>

<?
}



function table_modify_form()
{
	global $THIS_FILE,$MYSQL_DB,$DB_CONNECT,$table,$imgdir,$imgpath,$where,$value,$p;
	
	if ($where && $value) $list = mysql_fetch_array(mysql_query("SELECT * FROM $table WHERE $where='$value'" , $DB_CONNECT));
?>
	<?table_header();?>

	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=5 WIDTH=100%>

	<FORM ACTION='<?ECHO$parent_file;?>' method=post>
	<input type=hidden name=action value='record_update'>
	<input type=hidden name=ggg_table value='<?ECHO $table;?>'>
	<input type=hidden name=ggg_where value='<?ECHO $where;?>'>
	<input type=hidden name=ggg_value value='<?ECHO $value;?>'>

	<TR HEIGHT=25 BGCOLOR=C8D696 ALIGN=CENTER>
	<TD><B>필드</B></TD>
	<TD><B>종류</B></TD>
	<TD><B>함수</B></TD>
	<TD><B>값</B></TD>
	</TR>

	<?
	$i = 0; $result = mysql_db_query($MYSQL_DB, "SHOW FIELDS FROM $table");
	while ($row = mysql_fetch_array($result)) :

		$field_name = $row[0];
		$field_kind = explode(" ", $row[1]);
		$field_kind1 = explode("(", $row[1]);
		$field_length = explode(")", $field_kind1[1]);
		$field_null = $row[2];
		$field_default = $row[4];
		$field_atinc = trim($row[5]);
		$field_key = $row[3];
	?>	

		<TR BGCOLOR="<?if($i%2):?>#EFEFEF<?else:?>#DFDFDF<?endif;?>">
		<TD><?ECHO $field_name;?></TD>
		<TD><?ECHO $field_kind[0];?></TD>
		<TD>
            <SELECT NAME='ggg_f_<?ECHO$field_name?>'>
                <OPTION VALUE=''></OPTION>
                <OPTION VALUE='ASCII'>ASCII</OPTION>
                <OPTION VALUE='CHAR'>CHAR</OPTION>
                <OPTION VALUE='SOUNDEX'>SOUNDEX</OPTION>
                <OPTION VALUE='LCASE'>LCASE</OPTION>
                <OPTION VALUE='UCASE'>UCASE</OPTION>
                <OPTION VALUE='NOW'>NOW</OPTION>
                <OPTION VALUE='PASSWORD'>PASSWORD</OPTION>
                <OPTION VALUE='MD5'>MD5</OPTION>
                <OPTION VALUE='ENCRYPT'>ENCRYPT</OPTION>
                <OPTION VALUE='RAND'>RAND</OPTION>
                <OPTION VALUE='LAST_INSERT_ID'>LAST_INSERT_ID</OPTION>
                <OPTION VALUE='COUNT'>COUNT</OPTION>
                <OPTION VALUE='AVG'>AVG</OPTION>
                <OPTION VALUE='SUM'>SUM</OPTION>
                <OPTION VALUE='CURDATE'>CURDATE</OPTION>
                <OPTION VALUE='CURTIME'>CURTIME</OPTION>
                <OPTION VALUE='FROM_DAYS'>FROM_DAYS</OPTION>
                <OPTION VALUE='FROM_UNIXTIME'>FROM_UNIXTIME</OPTION>
                <OPTION VALUE='PERIOD_ADD'>PERIOD_ADD</OPTION>
                <OPTION VALUE='PERIOD_DIFF'>PERIOD_DIFF</OPTION>
                <OPTION VALUE='TO_DAYS'>TO_DAYS</OPTION>
                <OPTION VALUE='UNIX_TIMESTAMP'>UNIX_TIMESTAMP</OPTION>
                <OPTION VALUE='USER'>USER</OPTION>
                <OPTION VALUE='WEEKDAY'>WEEKDAY</OPTION>
                <OPTION VALUE='CONCAT'>CONCAT</OPTION>
            </SELECT>		
		</TD>
		<TD>
		
		<?if(!strstr($field_kind[0], "text")):?>
		<INPUT TYPE=TEXT NAME="<?ECHO $field_name;?>" SIZE="<?if ($field_length[0] < 20){ECHO $field_length[0];}else{ECHO "65";}?>" VALUE="<?ECHO $list[$i];?>">
		<?else:?>
		<TEXTAREA NAME="<?ECHO $field_name;?>" ROWS=7 COLS=50 style='overflow:auto;width:470;'><?ECHO stripslashes($list[$i]);?></TEXTAREA>
		<?endif;?>

		</TD>
		</TR>
	<?$i++;endwhile;?>

	</TABLE>

	<P ALIGN=CENTER>
	<?if($where):?>
	<INPUT TYPE=RADIO NAME=ggg_save_type VALUE='update' checked> 현재레코드를 수정
	<INPUT TYPE=RADIO NAME=ggg_save_type VALUE=''>새레코드(열)에 삽입
	<INPUT TYPE=SUBMIT VALUE=' 실 행 '>
	<INPUT TYPE=BUTTON onclick="history.go(-1);" VALUE='취소'>
	<?else:?>
	<INPUT TYPE=SUBMIT VALUE='새레코드(열) 삽입' style="width:200;height:50;font-weight:bold;">
	<INPUT TYPE=BUTTON onclick="history.go(-1);" VALUE='취소' style="width:100;height:50;font-weight:bold;">
	<?endif;?>
	</P>

	</FORM>
<?
}
if ($action == 'table_name_change')
{
	if ($old_name != trim($new_name)) {
		@mysql_query("ALTER TABLE $old_name RENAME ".trim($new_name), $DB_CONNECT); 
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'table_delete')
{
	@mysql_query("DROP TABLE $table", $DB_CONNECT);
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'table_empty')
{
	@mysql_query("TRUNCATE TABLE $table", $DB_CONNECT);
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'table_dump')
{
	if ($db_dump == 'all') {
		header("Content-disposition: filename=${MYSQL_DB}_bakup.sql");
	}
	else {
		header("Content-disposition: filename=$table.sql");
	}
	header("Content-type: application/octetstream");
	header("Pragma: no-cache");
	header("Expires: 0");

	if (eregi("Win",$HTTP_USER_AGENT)) {
		$ctrn = "\r\n";
	} else {
		$ctrn = "\n";
	}
	@set_time_limit(600);

	if ($db_dump == 'all') {
		$i = 0;
		$tables = mysql_list_tables($MYSQL_DB);
		$num_tables = mysql_num_rows($tables);
		while ($i < $num_tables) {
			$table = mysql_tablename($tables, $i);

			print "#---------------------------------------------------------$ctrn";
			print "# 호스트: localhost 선택한 디비 : $MYSQL_DB$ctrn";
			print "# 테이블 네임 : $table$ctrn";
			print "# --------------------------------------------------------$ctrn$ctrn";
			print get_table_def($MYSQL_DB, $table, $ctrn).";$ctrn";

			print "$ctrn$ctrn";
			print "#---------------------------------------------------------$ctrn";
			print "# $table INSERT DATA$ctrn";
			print "# --------------------------------------------------------$ctrn$ctrn";
			get_table_content($MYSQL_DB, $table, "my_handler",urldecode(stripslashes($SQL)));

			$i++;
		}
	}
	else {

		print "#---------------------------------------------------------$ctrn";
		print "# 호스트: localhost 선택한 디비 : $MYSQL_DB$ctrn";
		print "# 테이블 네임 : $table$ctrn";
		print "# --------------------------------------------------------$ctrn$ctrn";
		print get_table_def($MYSQL_DB, $table, $ctrn).";$ctrn";
		if ( $dump_type == 'dump_all') {
			print "$ctrn$ctrn";
			print "#---------------------------------------------------------$ctrn";
			print "# $table INSERT DATA$ctrn";
			print "# --------------------------------------------------------$ctrn$ctrn";
			get_table_content($MYSQL_DB, $table, "my_handler",urldecode(stripslashes($SQL)));
		}
	}
	exit;
}





if($action == 'table_excel')
{
	header( "Content-type: application/vnd.ms-excel" );  
	header( "Content-Disposition: attachment; filename=$table.xls" ); 
	header( "Content-Description: PHP4 Generated Data" );
	?>
	<HTML>
	<HEAD>
	<META http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<STYLE type="text/css">
	body,table,tr,td { font-size: 9pt; font-family: gullim; line-height: 1.0;}
	</STYLE>

	<BODY LEFTMARGIN=0 TOPMARGIN=0>
	<?get_excel_content($MYSQL_DB, $table , urldecode(stripslashes($SQL)));?>
	</BODY>
	</HTML>
	<?
	exit;
}

if ($action == 'field_alter') {

	if ( $field_default || $field_default == "0" ){
		$field_default_str = "DEFAULT '$field_default'";
	}
	if ( $field_null ) {
		$field_null_str = "NOT NULL";
	}
	if ( $field_type != 'TEXT' && $field_type != 'MEDIUMTEXT') {
		$field_length_str = "( $field_length )";
	}
	else {
		$field_default_str = "";

	}

	if ($new == 'yes') {
		if ($field_nav == '<-') {
			$field_nav_str = "FIRST";
		}
		else if ($field_nav == '->') {
			$field_nav_str = "";
		}
		else {
			$field_nav_str = "AFTER `$field_nav`";
		}
		$my_query = "ALTER TABLE `$table` 
		ADD `$field` $field_type$field_length_str $field_view
		$field_default_str $field_null_str $field_atinc $field_nav_str";
		@mysql_query($my_query, $DB_CONNECT);
	}
	else {
		
		$my_query = "ALTER TABLE `$table` 
		CHANGE `$field` `$field_name` $field_type$field_length_str $field_view
		$field_default_str $field_null_str $field_atinc";
		
		@mysql_query($my_query, $DB_CONNECT);
	}

	getLink($parent_file."?action=table_property&table=".$table,"","");
}

if ($action == 'field_delete') {
	@mysql_query("ALTER TABLE `$table` DROP `$field`", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_primary') {
	@mysql_query("ALTER TABLE `$table` DROP PRIMARY KEY , ADD PRIMARY KEY (`$field`)", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_index') {
	@mysql_query("ALTER TABLE `$table` ADD INDEX (`$field`) ", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_unique') {
	@mysql_query("ALTER TABLE `$table` ADD UNIQUE (`$field`) ", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_fulltext') {
	@mysql_query("ALTER TABLE `$table` ADD FULLTEXT (`$field`) ", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_primary1') {
	@mysql_query("ALTER TABLE `$table` DROP PRIMARY KEY", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}
if ($action == 'field_index1') {
	@mysql_query("ALTER TABLE `$table` DROP INDEX `$field`", $DB_CONNECT);
	getLink($parent_file."?action=table_property&table=".$table,"","");
}

if ($action == 'mt_delete') {
	while(list($key,$value) = each($HTTP_POST_VARS)){
		if(substr($key,0,5) == 'multi') {
			@mysql_query("DROP TABLE `$value`;", $DB_CONNECT);
		}
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'mt_empty') {
	while(list($key,$value) = each($HTTP_POST_VARS)){
		if(substr($key,0,5) == 'multi') {
			@mysql_query("TRUNCATE TABLE `$value`", $DB_CONNECT);
		}
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'mt_optimize') {
	while(list($key,$value) = each($HTTP_POST_VARS)){
		if(substr($key,0,5) == 'multi') {
			@mysql_query("OPTIMIZE TABLE `$value`", $DB_CONNECT);
		}
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'mt_repair') {
	while(list($key,$value) = each($HTTP_POST_VARS)){
		if(substr($key,0,5) == 'multi') {
			@mysql_query("REPAIR TABLE `$value`", $DB_CONNECT);
		}
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'create_table') {
	if ($new_table && !strstr($new_table, " ")) {
		mysql_query("CREATE TABLE $new_table (
		UID INT(10) DEFAULT '0' PRIMARY KEY NOT NULL AUTO_INCREMENT)", $DB_CONNECT);
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'upload') {
	if(ereg("^php[0-9A-Za-z_.-]+$", basename($sql_file))) {
		$sql_query = fread(fopen($sql_file, "r"), filesize($sql_file));
		if (get_magic_quotes_runtime() == 1) $sql_query = stripslashes($sql_query);
	}
	$sql_query = trim($sql_query);

	if($sql_query != "") {
		$sql_query = remove_remarks($sql_query);
		$pieces    = split_sql_file($sql_query,";");

		if (count($pieces) == 1 && !empty($pieces[0])) {
		$sql_query = addslashes(trim($pieces[0]));
		}
	}

	if(mysql_select_db($MYSQL_DB)) {
		for ($i=0; $i<count($pieces); $i++) {
			$sql = trim($pieces[$i]);
			if(!empty($sql) and $sql[0] != "#") $result = mysql_query($sql) or mysql_error();
			if (!isset($reload) && eregi('^CREATE TABLE (.+)', $pieces[$i])) $reload = "true";
		}
	}
	getLink($parent_file."?p=".$p,"","");
}
if ($action == 'record_delete') {
	@mysql_query("DELETE FROM $table WHERE $wehre='$value'",$DB_CONNECT);
	getLink($parent_file."?action=table_view&table=".$table."&p=".$p,"","");
}
if ($action == 'record_update')
{
	if($ggg_save_type == 'update') {
		while(list($key,$val) = each($HTTP_POST_VARS)) {
			if (substr($key,0,4) != 'ggg_' && $key != 'action') {
				if (${ggg_f_.$key}) {
					$sq .= "$key=${ggg_f_.$key}(".addslashes($val)."), "; 
				}
				else {
					$sq .= "$key='".addslashes($val)."', "; 
				}
			}
		}
		$query = "UPDATE $ggg_table SET ".substr(stripslashes($sq),0,strlen(stripslashes($sq))-2)." WHERE $ggg_where='$ggg_value'";
		@mysql_query($query,$DB_CONNECT);
	}
	else {
		$i = 0;
		while(list($key,$val) = each($HTTP_POST_VARS)) {
			if (substr($key,0,4) != 'ggg_' && $key != 'action') {
				if ($i == 0) {$ggg_where = $key; $ggg_value = $val; }
				$sq .= "$key, ";
				if (${ggg_f_.$key}) {
					$sq1 .= "${ggg_f_.$key}(".addslashes($val)."), "; 
				}
				else {
					$sq1 .= "'".addslashes($val)."', ";
				}
				$i++;
			}
		}
		@mysql_query("INSERT INTO $ggg_table (".substr(stripslashes($sq),0,strlen(stripslashes($sq))-2).") VALUES (".substr($sq1,0,strlen($sq1)-2).")",$DB_CONNECT);
	}
	
	getLink($parent_file."?action=table_modify&table=".$ggg_table."&where=".$ggg_where."&value=".$ggg_value."&p=".$p,"","");
}

?>