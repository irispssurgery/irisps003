<?
	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";


/******************** 인증정보 ********************/
$sms_url = "http://sslsms.cafe24.com/sms_remain.php"; // 전송요청 URL
$sms['user_id'] = base64_encode("webroinsms"); // SMS 아이디
$sms['secure'] = base64_encode("fea4a8fe746a2e7da835748d00304a26 ") ;//인증키
$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

$host_info = explode("/", $sms_url);
$host = $host_info[2];
$path = $host_info[3]."/".$host_info[4];
srand((double)microtime()*1000000);
$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

// 헤더 생성
$header = "POST /".$path ." HTTP/1.0\r\n";
$header .= "Host: ".$host."\r\n";
$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

// 본문 생성
foreach($sms AS $index => $value){
    $data .="--$boundary\r\n";
    $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
    $data .= "\r\n".$value."\r\n";
    $data .="--$boundary\r\n";
}
$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

$fp = fsockopen($host, 80);

if ($fp) {
    fputs($fp, $header.$data);
    $rsp = '';
    while(!feof($fp)) {
        $rsp .= fgets($fp,8192);
    }
    fclose($fp);
    $msg = explode("\r\n\r\n",trim($rsp));
    $Count = $msg[1]; //잔여건수
    echo $Count;
}
else {
    echo "Connection Failed";
}




	$displayPageSize = 20;
	$page = 1;

    $query = "select sum(idx)from _smsresult_";
    $total = @mysql_fetch_row(mysql_query($query));

	$sequenceNum = $total[0] - ($displayPageSize * ($page - 1));



?>


                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td> 
                              <table width="100%" align="center" cellpadding="0" cellspacing="0" border="1" bordercolor="#E0E0E0" style="border-collapse:collapse;" class="defstyle">
                                <tr> 
                                  <td width="40" align="center" height="30"><b><font color="#000000">NO</font></b></td>
                                  <td width="100" align="center"><b>문자내용</b></td>
                                  <td align="center"><b>이름</b></td>
                                  <td align="center"><b>전화번호</b></td>
                                  <td width="70" align="center"><b>결과</b></td>
                                </tr>
<?
		$i = 0;
		$query = "SELECT * FROM _smsresult_ ORDER BY idx";
		$result  = mysql_query($query) or Error(mysql_error());
		while($rs = mysql_fetch_array($result)) {
			$sms_msg		  = $rs["sms_msg"];
			$sms_receive_name = $rs["sms_receive_name"];
			$sms_hp			  = $rs["sms_hp"];
			$sms_result_code  = $rs["sms_result_code"];
			$sms_date		  = $rs["sms_date"];
?>
								<tr> 
                                  <td height="30" align="center" width="40"><?=$sequenceNum;?></td>
                                  <td align="left"><?=$sms_msg?></td>
                                  <td align="center" width=""><?=$sms_receive_name?></td>
                                  <td align="center"><?=$sms_hp?></td>
                                  <td align="center"><?=$sms_result_code?></td>
                                </tr>
<?
			$sequenceNum--;
			$i++;
		}
		mysql_free_result($result);
?>
                              </table>
                            </td>
                          </tr>
                        </table>



<!-- admin bottom ----------------------------------------------------------------------------------->
<?
	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";
?>