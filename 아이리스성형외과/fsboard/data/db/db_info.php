<?
$DB[kind]="mysql";
$DB[host]="localhost";
$DB[name]="dwho";
$DB[user]="root";
$DB[pass]="!@dwho@!";

function isConnectDb($host,$user,$pass)
{
	return mysql_connect($host, $user, $pass);
}
function isSelecteDb($name,$con)
{
	return mysql_select_db($name, $con);
}
function db_query($sql , $con)
{
	return mysql_query($sql , $con);
}
function db_fetch_array($que)
{
	return @mysql_fetch_array($que);
}
function db_num_rows($que)
{
	return mysql_num_rows($que);
}
function db_error()
{
	return mysql_error();
}

$DB_CONNECT = isConnectDb($DB[host],$DB[user],$DB[pass]);
$DB_USEMYDB = isSelecteDb($DB[name],$DB_CONNECT);
$MYSQL_DB   = $DB[name];
?>
