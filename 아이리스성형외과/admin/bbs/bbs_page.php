<?
	include "project.lib";

	$bbscode = new CBbscode();

	$result = $bbscode->listBbsAll();
	
	foreach($result as $key => $value)
	{
		$bbs_list.="<tr align=\"center\">";
		$bbs_list.="<td><a href=\"result_bbs_list.html?bbscode_code=".$value["bbscode_code"]."\">".$value["bbscode_title"]."</a></td>";
	    $bbs_list.="</tr>";
	}
?>