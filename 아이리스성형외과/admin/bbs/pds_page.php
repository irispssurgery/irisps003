<?
	include "project.lib";

	$bbscode = new CBbscode();

	$result = $bbscode->listPdsAll();
	
	foreach($result as $key => $value)
	{
		$pds_list.="<tr align=\"center\">";
		$pds_list.="<td><a href=\"result_bbs_list.html?bbscode_code=".$value["bbscode_code"]."\">".$value["bbscode_title"]."</a></td>";
	    $pds_list.="</tr>";
	}
?>