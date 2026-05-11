<?include './db_info.php'?>
<?include './manager.php'?>
<link rel='stylesheet' type='text/css' href='./root.css'>

<script language=javascript src='./manager.js'></script>
<script language=javascript src='./db_manager.js'></script>

<?
switch ($action)
{
	case "table_property" :
	table_property();
	break;

	case "table_view" :
//	table_view();
table_header();
	break;
	case "table_excel" :

	break;

	case "table_modify" :
//	table_modify_form();
//	break;

	case "" :
//	table_list();
//	break;
}
?>