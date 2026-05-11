<?
	include "../../../fsboard/lib/lib.php";

	//DB¿¬°á
	$dbConnect = DbConn();

	$mode = trim($_GET["mode"]);
	$exec = trim($_GET["exec"]);
	$idx  = trim($_GET["idx"]);
					
	if ($exec=='del'){ 
		$query="delete from sanhak where idx=$idx";
		mysql_query($query) or Error(mysql_error());

		MovePage("list.php?mode={$mode}&page={$page}");
	}

	if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
?>