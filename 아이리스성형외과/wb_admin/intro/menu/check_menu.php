<?
ob_start(); 
session_start(); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
extract($HTTP_SESSION_VARS);

include "../../../config/dbconn.php3"; 
include "../../../config/function.php3"; 
$inst->Connect();
?>
<HTML>
<HEAD>
<TITLE>  ID 등록 여부 Check  </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" href="member.css" type="text/css">
<link rel="stylesheet" type="text/css" href="../../css/css.css">
<script language="javascript">
   <!--
   function replace_id() {
      opener.document.join.id.select();
      self.close();
   }
   //-->
</script>
</HEAD>

<BODY>
<?

$query ="SELECT menu_file FROM km_menu where sort='$sort' and menu_file='$file_name'";
$row=$inst->ExecFetch($query);
if ($row){
	$strMsg    = "파일명 [".$file_name."] 은 등록되어 파일명 있는 입니다.";
	$intReturn = "0";
}else{
	$strMsg    = "파일명 [".$file_name."] 는 사용가능한 파일명입니다.";
	$intReturn = "1";
}
$inst->Disconnect();
?>

<script language=javascript>
	alert('<?=$strMsg?>');
	window.returnValue='<?=$intReturn?>';
	self.close();
</script>

</BODY>
</HTML>
