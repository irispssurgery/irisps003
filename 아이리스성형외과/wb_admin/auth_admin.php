<?
ob_start(); 
@session_start(); 
 
extract($HTTP_GET_VARS); 
extract($HTTP_POST_VARS); 
extract($HTTP_SESSION_VARS);

####### 관리자 영역 #######
if(!$_SESSION["MemId"] || $_SESSION["MemLevel"]>1){

	echo("
		<script>
			window.alert('관리자 영역입니다.');
			parent.top.location.href='/fsboard/lib/setup.php?mode=Login' 
	    </script>
	");
	exit;
}
?>
