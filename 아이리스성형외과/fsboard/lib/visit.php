<?
//DB연결
$dbConnect = DbConn();

// 컴퓨터의 아이피와 쿠키에 저장된 아이피가 다르다면 테이블에 반영함
if (get_cookie('visit_ip') != $_SERVER['REMOTE_ADDR']) {
    set_cookie("visit_ip", $_SERVER['REMOTE_ADDR'], 86400); // 하루동안 저장

	$query = "SELECT max(wb_seq) as max_wb_seq FROM wb_count";
	$tmp_result = mysql_query($query) or Error(mysql_error());
	$rs = mysql_fetch_row($tmp_result);
	$wb_seq = (int)$rs[0] + 1;
	mysql_free_result($tmp_result);


    $query = "insert into wb_count  (wb_seq, wb_ref_url, wb_agent, wb_date, wb_time, wb_ip_address)
              values ('$wb_seq','$_SERVER[HTTP_REFERER]','$_SERVER[HTTP_USER_AGENT]','$time_ymd','$time_his','$_SERVER[REMOTE_ADDR]')";

	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
        $query = " insert wb_count_sum ( wb_count, wb_date) values ( 1, '$time_ymd' ) ";
		$result = mysql_query($query);
        
        // DUPLICATE 오류가 발생한다면 이미 날짜별 행이 생성되었으므로 UPDATE 실행
        if (!$result) {
            $query = " update wb_count_sum set wb_count = wb_count + 1 where wb_date = '$time_ymd' ";
			$result = mysql_query($query) or Error(mysql_error());
        }
	}        

}

    // 오늘
    $query = " select wb_count as cnt from wb_count_sum where wb_date = '$time_ymd'";
	$wb_today = @mysql_fetch_row(mysql_query($query));
	//if(!$wb_today[0]) die("<error>1</error>\n<message>Wrong Parameter : id</message>\n</response>");


    // 어제
    $query = "select wb_count as cnt from wb_count_sum where wb_date = DATE_SUB('$time_ymd', INTERVAL 1 DAY) ";
    //$wb_yesterday = @mysql_fetch_row(mysql_query($query));
	//if(!$wb_yesterday[0]) die("<error>1</error>\n<message>Wrong Parameter : id</message>\n</response>");

    // 최대
    $query = "select max(wb_count) as cnt from wb_count_sum";
    //$wb_max = @mysql_fetch_row(mysql_query($query));
	//if(!$wb_max[0]) die("<error>1</error>\n<message>Wrong Parameter : id</message>\n</response>");

    // 전체
    $query = "select sum(wb_count) as total from wb_count_sum"; 
    $wb_sum = @mysql_fetch_row(mysql_query($query));
	//if(!$wb_sum[0]) die("<error>1</error>\n<message>Wrong Parameter : id</message>\n</response>");

    $visit = "오늘:$wb_today[0],어제:$wb_yesterday[0],최대:$wb_max[0],전체:$wb_sum[0]";

	if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

?>