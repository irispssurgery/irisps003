<?
/*************************************************************
	FSBOARD Latest List Assemble Parts
*************************************************************/


function LatestList($id,$lineNum,$subjectLimit,$returnUrl="") {

	global $dbConnect, $FSBOARD_PATH;

	//if(!ob_get_contents()) { echo "페이지 상단에 ob_start() 함수가 호출되지 않았습니다."; return; }
	if(!defined("lib_included")) { echo "lib.php 파일의 include가 필요합니다."; return; }
	if(!$dbConnect) DBConn();

	if(!$id) Error("최근글 게시판 ID가 지정되지 않음");
	else $tableId = "_board_{$id}";

	if(!intval($subjectLimit)) $subjectLimit = 25;
	if(!intval($lineNum)) $lineNum = 5;



	if(!$returnUrl) {
		$query = "SELECT combinedFileName FROM _board__admin__ WHERE boardId='{$id}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_row($result);
			$combinedFileName = $rs[0];
			mysql_free_result($result);
		}
		$returnUrl = $combinedFileName ? $combinedFileName : "/{$FSBOARD_PATH}/index.php";
	}

	$query = "SELECT idx,subject,memoNum,regDate FROM {$tableId} ORDER BY idx DESC LIMIT {$lineNum};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);

		$str = "";
		if($numrows) {
			$i = 1;
			while($rs = mysql_fetch_array($result)) {
				$idx = $rs["idx"];
				$subject = $rs["subject"];
				$memoNum = $rs["memoNum"];
				$regDate = $rs["regDate"];

				$viewUrl = "{$returnUrl}?id={$id}&idx={$idx}";

				$subject = CutStr($subject,$subjectLimit);
				$subject = StripHtmlChars($subject);
				$subject = "<a href='{$viewUrl}'>{$subject}</a>";

				$new_icon = mktime() - $regDate <= 86400 ? "<img src='/$FSBOARD_PATH/img/clip/new.gif' border=0>" : "";

				$memo_num = $memoNum ? "[$memoNum]" : "";

				$reg_date = date("Y.m.d", $regDate);

				//최근 목록 출력부분
				$str .= "
							<tr>
							  <td width='10' height='22' align='left'><img src='images/news_icon_01.gif' width='9' height='9' align='absmiddle'></td>
							  <td width='200' class='text_black_12px' align='left'>{$subject}{$new_icon}</td>
							  <td width='50' align='center' class='text_gray_11px'>[{$reg_date}]</td>
							</tr>
				";
				if($i<$lineNum) {
					$str .= "
					";
				}
				$i++;
			}
		} else $str .= "등록된 글이 없습니다.";

		$str .= "";

		mysql_free_result($result);
	}
	return $str;
}




function LatestList1($id,$lineNum,$subjectLimit,$returnUrl="") {

	switch ($id){
		case "sub0202_board":
		  $returnUrl = "sub2/sub2_2.html";
		  break;
	}

	$returnUrl = "/intro/".$returnUrl;
	global $dbConnect, $FSBOARD_PATH;

	//if(!ob_get_contents()) { echo "페이지 상단에 ob_start() 함수가 호출되지 않았습니다."; return; }
	if(!defined("lib_included")) { echo "lib.php 파일의 include가 필요합니다."; return; }
	if(!$dbConnect) DBConn();

	if(!$id) Error("최근글 게시판 ID가 지정되지 않음");
	else $tableId = "_board_{$id}";

	if(!intval($subjectLimit)) $subjectLimit = 25;
	if(!intval($lineNum)) $lineNum = 5;



	if(!$returnUrl) {
		$query = "SELECT combinedFileName FROM _board__admin__ WHERE boardId='{$id}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_row($result);
			$combinedFileName = $rs[0];
			mysql_free_result($result);
		}
		$returnUrl = $combinedFileName ? $combinedFileName : "/{$FSBOARD_PATH}/index.php";
	}

	$query = "SELECT idx,subject,memoNum,regDate FROM {$tableId} ORDER BY idx DESC LIMIT {$lineNum};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);

		$str = "";
		if($numrows) {
			$i = 1;
			while($rs = mysql_fetch_array($result)) {
				$idx = $rs["idx"];
				$subject = $rs["subject"];
				$memoNum = $rs["memoNum"];
				$regDate = $rs["regDate"];

				$viewUrl = "{$returnUrl}?id={$id}&idx={$idx}";

				$subject = CutStr($subject,$subjectLimit);
				$subject = StripHtmlChars($subject);
				$subject = "<a href='{$viewUrl}'>{$subject}</a>";

				$new_icon = mktime() - $regDate <= 86400 ? "<img src='/$FSBOARD_PATH/img/clip/new.gif' border=0>" : "";

				$memo_num = $memoNum ? "[$memoNum]" : "";

				$reg_date = date("Y.m.d", $regDate);

				//최근 목록 출력부분
				$str .= "{$subject}{$new_icon}{$memo_num}<br>";
				if($i<$lineNum) {
					$str .= "
					";
				}
				$i++;
			}
		} else $str .= "등록된 글이 없습니다.";

		$str .= "";

		mysql_free_result($result);
	}
	return $str;
}



function LatestPhoto1($id,$lineNum,$subjectLimit,$returnUrl="",$x=104,$y=85) {
	global $dbConnect, $FSBOARD_PATH;

	//if(!ob_get_contents()) { echo "페이지 상단에 ob_start() 함수가 호출되지 않았습니다."; return; }
	if(!defined("lib_included")) { echo "lib.php 파일의 include가 필요합니다."; return; }
	if(!$dbConnect) DBConn();

	if(!$id) Error("최근글 게시판 ID가 지정되지 않음");
	else $tableId = "_board_{$id}";

	if(!intval($subjectLimit)) $subjectLimit = 25;
	if(!intval($lineNum)) $lineNum = 5;
	if(!$returnUrl) {
		$query = "SELECT combinedFileName FROM _board__admin__ WHERE boardId='{$id}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_row($result);
			$combinedFileName = $rs[0];
			mysql_free_result($result);
		}
		$returnUrl = $combinedFileName ? $combinedFileName : "/{$FSBOARD_PATH}/index.php";
	}

	$query = "SELECT idx,subject,memoNum,regDate,fileName1 FROM {$tableId} ORDER BY idx DESC LIMIT {$lineNum};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		$str = "<table width='90%' border='0' cellspacing='0' cellpadding='0'><tr>";
		if($numrows) {
			$numrows_count=1;
			while($rs = mysql_fetch_array($result)) {
				$idx       = $rs["idx"];
				$subject   = $rs["subject"];
				$memoNum   = $rs["memoNum"];
				$regDate   = $rs["regDate"];
				$fileName1 = $rs["fileName1"];

				$viewUrl   = "intro/sub6/sub6_8.html?id={$id}&idx={$idx}&mode=view";

				$subject = CutStr($subject,$subjectLimit);
				$subject = StripHtmlChars($subject);
				$subject = "<a href='{$viewUrl}'>{$subject}</a>";

				$new_icon = mktime() - $regDate <= 86400 ? "<img src='/images/new.gif' border=0>" : "";

				$memo_num = $memoNum ? "[$memoNum]" : "";
				$reg_date = date("Y-m-d", $regDate);
				if($fileName1) {
					if(eregi("\.jpg|\.gif|\.bmp|\.png",$fileName1)) {
						//$photo_file = "/{$FSBOARD_PATH}/data/{$id}/{$fileName1}";
						$photo_file = "{$returnUrl}?id={$id}&mode=filelink&maintainCode=".md5(session_id())."&filename=".urlencode($fileName1);
					}
				}
				$photo_file = !$fileName1 ? "/{$FSBOARD_PATH}/img/no_image.gif" : $photo_file;

				//최근 이미지 출력부분
/*
					$str .="

                            <td width='104' align='center' valign='middle'><img src='image/gallery_1.gif' width='104' height='85'></td>
                            <td align='center' valign='middle'><img src='image/gallery_line.gif' width='2' height='85'></td>


					";

*/

				$str .= "
					<td valign='top' width='104' height='85' align='left'>
						<table style='border:1px solid #E0E0E0;'>
							<tr>
								<td align='center' valign='top' width='104' heigh='85'><a href='{$viewUrl}'><img src='{$photo_file}' width='{$x}' height='{$y}' border=0 onMouseOver=\"this.className='imgover';\" onMouseOut=\"this.className='imgbase';\" class='imgbase'></a></td>
				";
if (($numrows_count%2)!=0){
				$str .= "
			                    <td align='center' valign='middle'><img src='image/gallery_line.gif' width='2' height='85'></td>
				";
}
				$str .= "
							</tr>
						</table>
					</td>
				";
			$numrows_count++;
			} // end while

		} else $str .= "등록된 내용이 없습니다.";
		//$str .= "</tr></table>";
		$str .= "</tr></table><style type='text/css'><!--\n.imgbase{border:1px solid #E0E0E0;} .imgover{border:1px solid black;filter:alpha(opacity=60);}\n--></style>";

		mysql_free_result($result);
	}
	return $str;
}

function LatestPhoto($id,$lineNum,$subjectLimit,$returnUrl="",$x=80,$y=80) {
	global $dbConnect, $FSBOARD_PATH;

	//if(!ob_get_contents()) { echo "페이지 상단에 ob_start() 함수가 호출되지 않았습니다."; return; }
	if(!defined("lib_included")) { echo "lib.php 파일의 include가 필요합니다."; return; }
	if(!$dbConnect) DBConn();

	if(!$id) Error("최근글 게시판 ID가 지정되지 않음");
	else $tableId = "_board_{$id}";

	if(!intval($subjectLimit)) $subjectLimit = 25;
	if(!intval($lineNum)) $lineNum = 5;
	if(!$returnUrl) {
		$query = "SELECT combinedFileName FROM _board__admin__ WHERE boardId='{$id}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$rs = mysql_fetch_row($result);
			$combinedFileName = $rs[0];
			mysql_free_result($result);
		}
		$returnUrl = $combinedFileName ? $combinedFileName : "/{$FSBOARD_PATH}/index.php";
	}

	$query = "SELECT idx,subject,memoNum,regDate,fileName1 FROM {$tableId} ORDER BY idx DESC LIMIT {$lineNum};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);

		$str = "<table width=''><tr>";
		if($numrows) {
			while($rs = mysql_fetch_array($result)) {
				$idx       = $rs["idx"];
				$subject   = $rs["subject"];
				$memoNum   = $rs["memoNum"];
				$regDate   = $rs["regDate"];
				$fileName1 = $rs["fileName1"];
				$viewUrl   = "{$returnUrl}?id={$id}&idx={$idx}";

				$subject = CutStr($subject,$subjectLimit);
				$subject = StripHtmlChars($subject);
				$subject = "<a href='{$viewUrl}'>{$subject}</a>";

				$new_icon = mktime() - $regDate <= 86400 ? "<img src='/images/new.gif' border=0>" : "";

				$memo_num = $memoNum ? "[$memoNum]" : "";
				$reg_date = date("Y-m-d", $regDate);
				if($fileName1) {
					if(eregi("\.jpg|\.gif|\.bmp|\.png",$fileName1)) {
						//$photo_file = "/{$FSBOARD_PATH}/data/{$id}/{$fileName1}";
						$photo_file = "{$returnUrl}?id={$id}&mode=filelink&maintainCode=".md5(session_id())."&filename=".urlencode($fileName1);
					}
				}
				$photo_file = !$fileName1 ? "/{$FSBOARD_PATH}/img/no_image.gif" : $photo_file;

				//최근 이미지 출력부분
				$str .= "
					<td valign='top'>
						<table width='".($width+10)."' style='border:1px solid #E0E0E0;'>
							<tr>
								<td align='center' valign='top'><a href='{$viewUrl}'><img src='{$photo_file}' width='{$x}' height='{$y}' border=0 onMouseOver=\"this.className='imgover';\" onMouseOut=\"this.className='imgbase';\" class='imgbase'></a></td>
							</tr>
							<tr>
								<td>{$subject}{$new_icon}{$memo_num}</td>
							</tr>
						</table>
					</td>
				";
			}
		} else $str .= "등록된 내용이 없습니다.";

		$str .= "</tr></table><style type='text/css'><!--\n.imgbase{border:1px solid #E0E0E0;} .imgover{border:1px solid black;filter:alpha(opacity=60);}\n--></style>";

		mysql_free_result($result);
	}
	return $str;
}
?>