<?
/*************************************************************

	FSBOARD PHP 1.0beta (Web Bulletin Board System)

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: November 5, 2007

	Copyright(c)2000-2007 FSBOARD. All Rights Reserved.

*************************************************************/



if(eregi(":\/\/",$INC_PATH)||eregi("\.\.",$INC_PATH)) $INC_PATH ="./";

include_once $INC_PATH."lib/lib.php";
include_once $INC_PATH."lib/conf.php";

//print_r($_SERVER);











if($mode == "list" || $mode == "view" || $mode == "search" || $mode == "") { //////////////////////////////////////////////////////////////////////list & view

	/////자동로그인 처리
	if($_COOKIE["amid"] && $_COOKIE["pswd"] && !$MemId) {
		MovePage($_SERVER["PHP_SELF"]."?id=".$id."&mode=login");
	}

	/////카운터 처리
	if(!$_COOKIE["count_".$id]) { //쿠키정보 가져옴
		//@setcookie("count_{$id}","1",time()+60*60*24,"/");
		@setcookie("count_".$id,"1",mktime(0,0,0,date("m"),date("d")+1,date("Y")),"/");
		mysql_query("UPDATE {$_table_id_admin} SET totalCount=totalCount+1 WHERE aidx={$aidx};") or Error(mysql_error()); $totalCount++;
		if(date("Ymd",$currDate)==date("Ymd")) { //오늘인지 확인
			mysql_query("UPDATE {$_table_id_admin} SET todayCount=todayCount+1 WHERE aidx={$aidx};") or Error(mysql_error()); $todayCount++;
		}
		else {
			mysql_query("UPDATE {$_table_id_admin} SET todayCount=1, currDate=".mktime()." WHERE aidx={$aidx};") or Error(mysql_error()); $todayCount=1;
		}
	}

	/////내용보기일 경우
	if($mode == "view" || $idx) {
		if($view_level) { //권한 체크
			$view_included = true;
			include $INC_PATH."skin/".$skin."/view.php";
			@flush();
		}
		else {
			if($_SESSION["MemId"]) {
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("글 내용을 볼수 있는 권한이 없습니다."));
			}
			else {
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=login");
			}
		}
	}

	/////목록보기이거나 내용에서 목록보기 사용일경우
	if($mode == "list" || $mode == "search" || $mode == "" || ($mode=="view" && $useListAtView)) {
		if($list_level) { //권한 체크
			$list_included = true;
			
			//스킨이 스케줄 일 경우 view 에서 리스트는 안나오게 : 나중에 관리자 설정 기능 추가
			If ($mode=="view"){
				switch ($skin){
					case "div_schedule_list":
						break;
					case "div_schedule":
						break;
					case "div_schedule_fair":
						break;
					default:
						include $INC_PATH."skin/".$skin."/list.php";
				}
			}else{
						include $INC_PATH."skin/".$skin."/list.php";
			}
		}
		else {
			if($_SESSION["MemId"]) {
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("게시판 리스트를 볼수 있는 권한이 없습니다."));
			}
			else {
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=login");
			}
		}
	}














} else if($mode == "write" || $mode == "edit" || $mode == "reply") { //////////////////////////////////////////////////////////////////////write & edit

	/////수정일경우
	if($mode == "edit") {
		$auth_passwd = $_POST["auth_passwd"];
		if(!$auth_passwd) {
			MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=edit&idx={$idx}&page={$page}");
			exit;
		}
		else {
			if(!$MemId) $auth_passwd = md5($auth_passwd);
		}

		//암호 가져오기
		$query = "SELECT passwd FROM ".$_table_id_board." WHERE idx={$idx};";
		$result = mysql_query($query) or Error(mysql_error());
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("유효하지 않은 데이터입니다.");
		$rs = mysql_fetch_row($result);
		if($result) $passwd = $rs[0];

		if($auth_passwd!=$passwd && md5($auth_passwd)!=$passwd && $auth_passwd!=$adminPasswd && !$isAdmin) {
			Error("암호가 일치하지 않습니다.");
		}
	}

	if($write_level) { //글쓰기권한 체크
		if($mode=="reply"&&!$reply_level) { //답변권한 체크
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("답변을 작성할수 있는 권한이 없습니다."));
		}

		$write_included = true;
		include $INC_PATH."skin/".$skin."/write.php";
	}
	else MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("글을 작성할수 있는 권한이 없습니다."));













} else if($mode == "writesave" || $mode == "editsave") { //////////////////////////////////////////////////////////////////////writesave & editsave

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	//게시물 수정일 경우
	if($mode=="editsave") {
		if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

		$query = "SELECT passwd, ";
		for($i=1; $i<=$fileMaxNum; $i++) $query .= "fileName{$i}, ";
		$query .= "isNotice,tbLink,tag_ls FROM ".$_table_id_board." WHERE idx={$idx};";

		$result = mysql_query($query) or Error(mysql_error());
		$numrows = mysql_num_rows($result);
		$rs = mysql_fetch_row($result);

		if(!$numrows) Error("게시물이 없거나 삭제 되었습니다.");

		$origin_passwd = $rs[0]; //이전 암호 가져옴
		for($i=1; $i<=$fileMaxNum; $i++) ${"fileName".$i} = $rs[$i]; //이전 파일명 가져옴
		$origin_isNotice = $rs[$fileMaxNum+1]; //공지글여부 가져옴
		$origin_tbLink = $rs[$fileMaxNum+2]; //트랙백주소 가져옴
		$origin_tag_ls = $rs[$fileMaxNum+3]; //태그리스트 가져옴

		mysql_free_result($result);
	}
	//새글작성, 답변작성일 경우
	else {
		if(!$write_level) Error("글쓰기 권한이 없습니다.");

		$query = "SELECT idx,ipReg,regDate FROM ".$_table_id_board." ORDER BY idx DESC LIMIT 1";
		$result = mysql_query($query) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		$seqNum = intval($rs[0]); //마지막글 고유번호
		$ipReg = $rs[1]; //마지막글 IP주소
		$regDate = $rs[2]; //마지막글 등록일자
		mysql_free_result($result);

		if($useBlockSpam&&!$isAdmin) { //스팸방지기능 사용일경우
			//동일 IP에서 30초이내 글쓰기 금지
			if($ipReg==$_SERVER["REMOTE_ADDR"]&&(mktime()-$regDate)<30) {
				//Error("스팸글을 방지하기 위해 동일한 IP주소에서는 <br />이전 글쓰기 이후 30초가 지난후에 글쓰기가 가능합니다.");
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("스팸글을 방지하기 위해 동일한 IP주소에서는\\n이전 글쓰기 이후 30초가 지난 후에 글쓰기가 가능합니다."));
			}
		}

		if(!$seqNum) $seqNum = 1; else $seqNum++;
	}

	//REQUEST 파싱
	$objProperty = StrAddSlashes(trim($_POST["objProperty"]));
	$isNotice = StrAddSlashes(trim($_POST["isNotice"]));
	$isSecret = StrAddSlashes(trim($_POST["isSecret"]));

	$passwd = trim($_POST["passwd"]);
	$author = StrAddSlashes(trim($_POST["author"]));
	$e_mail = StrAddSlashes(trim($_POST["e_mail"]));
	$homeUrl = StrAddSlashes(trim($_POST["homeUrl"]));
	$category = StrAddSlashes(trim($_POST["category"]));
	$tag_ls = StrAddSlashes(trim($_POST["tag_ls"]));
	$docType = StrAddSlashes(trim($_POST["docType"]));
	$subject = StrAddSlashes(trim($_POST["subject"]));
	$contents = StrAddSlashes(trim($_POST["contents"]));
	$siteLink1 = StrAddSlashes(trim($_POST["siteLink1"]));
	$siteLink2 = StrAddSlashes(trim($_POST["siteLink2"]));
	$tbLink = StrAddSlashes(trim($_POST["tbLink"]));
	$other01 = StrAddSlashes(trim($_POST["other01"]));
	$other02 = StrAddSlashes(trim($_POST["other02"]));
	$other03 = StrAddSlashes(trim($_POST["other03"]));
	$other04 = StrAddSlashes(trim($_POST["other04"]));
	$other05 = StrAddSlashes(trim($_POST["other05"]));
	$other06 = StrAddSlashes(trim($_POST["other06"]));
	$other07 = StrAddSlashes(trim($_POST["other07"]));
	$other08 = StrAddSlashes(trim($_POST["other08"]));
	$other09 = StrAddSlashes(trim($_POST["other09"]));
	$other10 = StrAddSlashes(trim($_POST["other10"]));


	if(IsBlank($passwd)&&$mode!="editsave"&&!$MemId&&!$isAdmin) Error("암호를 입력해 주세요.");
	if(IsBlank($author)) Error("이름을 입력해 주세요.");
	if(IsBlank($subject)) Error("제목을 입력해 주세요.");
	if(IsBlank($contents)) Error("내용을 입력해 주세요.");

	if(!$docType) $docType = "text";

	$isMember = $MemId;
	$regDate = mktime();
	$editDate = 0;
	$memoLatestDate = 0;
	$memoNum = 0;
	$tbNum = 0;
	$readNum = 0;
	$voteNum = 0;
	$ipReg = $_SERVER["REMOTE_ADDR"];
	$ipEdit = "";
	$usrAgentReg = StrAddSlashes(substr(trim($_SERVER["HTTP_USER_AGENT"]),0,250));
	$usrAgentEdit = "";

	$isNotice = $noticewrite_level ? (!$isNotice ? 0 : 1) : 0;
	$isSecret = $useSecret ? ($isSecret ? 1 : 0) : 0;

	if($MemId||$isAdmin&&!$passwd) {
		$passwd = $MemPasswd;
	}
	else {
		if($mode!="editsave") $passwd = md5($passwd);
	}

	$homeUrl = ((!eregi("http\:\/\/",$homeUrl))&&$homeUrl) ? "http://".$homeUrl : ($homeUrl=="http://" ? "" : $homeUrl);
	$siteLink1 = ((!eregi("http\:\/\/",$siteLink1))&&$siteLink1) ? "http://".$siteLink1 : ($siteLink1=="http://" ? "" : $siteLink1);
	$siteLink2 = ((!eregi("http\:\/\/",$siteLink2))&&$siteLink2) ? "http://".$siteLink2 : ($siteLink2=="http://" ? "" : $siteLink2);
	if($tbLink=="http://") $tbLink = "";

	//카테고리리스트에 포함된 카테고리인지 검사
	if($category && !eregi($category, $categories)) Error("잘못된 접근입니다.");

	//이메일주소가 있을 경우 패턴 및 MX검사
	if($e_mail) { if(!mail_mx_check($e_mail)) Error("이메일주소가 올바르지 않습니다."); }

	//홈페이지주소가 있을 경우 홈페이지주소 패턴 검사
	if($homeUrl) { if(!IsHomepage($homeUrl)) Error("홈페이지 주소가 형식에 맞지 않습니다."); }


	//트랙백 주소가 있을 경우 처리
	if($tbLink && $useTrackback) {
		if(!$idx) {
			if($result = mysql_query("SELECT max(idx) FROM ".$_table_id_board.";")) {
				$rs = mysql_fetch_row($result);
				$tb_idx = $rs[0] + 1;
			}
		} else $tb_idx = $idx;

		$url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id={$id}&amp;idx={$tb_idx}";
		$blog_name = $boardName;//$_SERVER["HTTP_HOST"];
		$title = trim($_POST["subject"]);
		$excerpt = trim($_POST["contents"]);

		if(($tbLink!=$origin_tbLink) && ereg("http://",$tbLink)) {
			//트랙백주소로 트랙백핑 보내기
			$res = send_tb($tbLink, $url, $title, $blog_name, $excerpt);
			if(!$res) {
				//트랙백 전송 실패시 트랙백주소 비움
				$tbLink = "";
			}
			else $tb_error_str = "";
		}
	}


	/////태그 사용빈도 업데이트
	if($tag_ls) {
		if(strlen($tag_ls)>255) Error("사용자 태그의 길이가 너무 깁니다.");

		$tag_ls = str_replace(" ", "", $tag_ls);
		$tagls = explode(",", $tag_ls);

		$origin_tagls = explode(",", $origin_tag_ls);

		for($i=0,$till=sizeof($tagls);$i<$till;$i++) {
			$tagls[$i] = trim($tagls[$i]);

			if(strlen($tagls[$i]) > 32) Error("사용자 태그의 길이가 너무 깁니다.");

			//사용중인 태그 확인
			$query = "SELECT * FROM ".$_table_id_tagcloud." WHERE strtag='".$tagls[$i]."' AND boardId='".$id."' ";
			if($MemId) $query .= " AND memberId='".$MemId."' ";
			$result = mysql_query($query);
			if($result) {
				$numrows = mysql_num_rows($result);
				mysql_free_result($result);
			}
			if($numrows) {
				//게시물 수정일 경우 중복 태그 확인
				if($mode == "editsave") {
					$overlap = false;
					for($j=0, $till1=sizeof($origin_tagls); $j<$till1; $j++) {
						if($origin_tagls[$j] == $tagls[$i]) {
							$overlap = true;
							break;
						}
					}
				}
				else $overlap = false;

				//중복 태그가 없으면 사용률 및 사용날짜 업데이트
				if(!$overlap) {
					$query = "UPDATE ".$_table_id_tagcloud." SET freqRate=freqRate+1,usedate=".mktime()." WHERE strtag='".$tagls[$i]."' AND boardId='".$id."' ";
					if($MemId) $query .= " AND memberId='".$MemId."' ";
					mysql_query($query) or Error(mysql_error());
				}
			}
			else {
				$query = "INSERT INTO ".$_table_id_tagcloud." (strtag,boardId,memberId,freqRate,usedate) VALUES ('".$tagls[$i]."','".$id."','".$MemId."',1,".mktime().");";
				mysql_query($query) or Error(mysql_error());
			}
		}
	}


	/////답변글일경우
	if(strpos($_SERVER["HTTP_REFERER"], "mode=reply")&&$idx) {
		if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");
		$ref = intval($_POST["ref"]);
		$reStep = intval($_POST["reStep"]);
		$reLevel = intval($_POST["reLevel"]);

		if(!$ref) Error("답변코드가 잘못되었습니다.");

		$query = "SELECT ref,reStep,reLevel FROM ".$_table_id_board." WHERE idx={$idx}";
		$result = mysql_query($query) or Error(mysql_error());
		$numrows = mysql_num_rows($result);

		if(!$numrows) Error("게시물이 없거나 삭제되었습니다.");

		if($result) {
			$rs = mysql_fetch_row($result);
			$ref = intval($rs[0]);
			$reStep = intval($rs[1]);
			$reLevel = intval($rs[2]);
			mysql_free_result($result);
		}

		$query = "UPDATE ".$_table_id_board." SET reStep=reStep+1 WHERE ref={$ref} AND reStep>{$reStep};";
		mysql_query($query) or Error(mysql_error());

		$reStep++;
		$reLevel++;
	}
	/////처음글쓰기일 경우
	else {
		$ref = $seqNum;
		$reStep = 0;
		$reLevel = 0;
	}


	/////파일 업로드
	$file_tmpn = array();
	$file_name = array();
	$file_size = array();
	$file_fwpn = array();

	//php.ini에 설정된 업로드허용량과 관리자설정에서 설정한 업로드허용량 비교
	$sysFileMaxLimit = intval(substr(ini_get("upload_max_filesize"),0,-1)) * 1024 * 1024;
	if($fileMaxLimit>$sysFileMaxLimit) $fileMaxLimit = $sysFileMaxLimit;

	for($i=0; $i<$fileMaxNum; $i++) {
		if($_FILES["attachFile".($i+1)]) {
			$file_tmpn[$i] = $_FILES["attachFile".($i+1)]["tmp_name"];
			$file_name[$i] = $_FILES["attachFile".($i+1)]["name"];
			$file_size[$i] = $_FILES["attachFile".($i+1)]["size"];
		}

		if($file_size[$i]>0 && $file_tmpn[$i]) {

			if(!is_uploaded_file($file_tmpn[$i])) Error("정상적인 방법으로 업로드 해주세요");
			$file_size[$i] = filesize($file_tmpn[$i]);

			//업로드 용량 제한
			if($fileMaxLimit<$file_size[$i]&&!$isAdmin) {
				//Error("파일 업로드는 ".GetFileSize($fileMaxLimit)." 까지 가능합니다");
				MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("파일 업로드는 ".GetFileSize($fileMaxLimit)."까지 가능합니다."));
			}

			if($file_size[$i]>0) {

				//업로드 금지
				//if(eregi("\.inc",$file_name[$i])||eregi("\.phtm",$file_name[$i])||eregi("\.htm",$file_name[$i])||eregi("\.shtm",$file_name[$i])||eregi("\.ztx",$file_name[$i])||eregi("\.php",$file_name[$i])||eregi("\.dot",$file_name[$i])||eregi("\.asp",$file_name[$i])||eregi("\.cgi",$file_name[$i])||eregi("\.pl",$file_name[$i])) Error("HTML, PHP 관련파일은 업로드할수 없습니다");
				//if(substr($file_name[$i],0,1)=='.'||eregi("\.inc",$file_name[$i])||eregi("\.phtm",$file_name[$i])||eregi("\.htm",$file_name[$i])||eregi("\.shtm",$file_name[$i])||eregi("\.ztx",$file_name[$i])||eregi("\.php",$file_name[$i])||eregi("\.dot",$file_name[$i])||eregi("\.asp",$file_name[$i])||eregi("\.cgi",$file_name[$i])||eregi("\.pl",$file_name[$i])) Error("Html, PHP 관련파일은 업로드할수 없습니다");
				if(substr($file_name[$i],0,1)=='.') Error("파일명이 점(.)으로 시작되는 파일은 업로드 할수 없습니다.");

				//허용확장자 검사
				if($allowExts) {
					$temp = explode(".",$file_name[$i]);
					$s_point = count($temp)-1;
					$upload_check = $temp[$s_point];
					if(!eregi($upload_check,$allowExts)||!$upload_check) {
						//Error("파일 업로드는 $allowExts 확장자만 가능합니다");
						MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("파일 업로드는 {$allowExts} 확장자만 가능합니다."));
					}
				}

				$file_tmpn[$i] = eregi_replace("\\\\","\\",$file_tmpn[$i]);
				//$file_name[$i] = str_replace(" ","_",$file_name[$i]);

				if(!is_dir($FSDATA_ROOT."/".$id)) { //디렉토리 검사
					@mkdir($FSDATA_ROOT."/".$id,0777);
					@chmod($FSDATA_ROOT."/".$id,0757);
				}

				$file_fwpn[$i] = GetUniqueName($file_name[$i], "{$FSDATA_ROOT}/{$id}/"); //중복파일 검사
				if(!move_uploaded_file($file_tmpn[$i], $file_fwpn[$i])) Error("파일업로드가 제대로 되지 않았습니다");
				@chmod($file_fwpn[$i],0646);
			}
		}
	}


	/////게시물 수정일 경우
	if($mode=="editsave") {
		$delAttachFile = array();
		if($MemId) {
			$passwd = $origin_passwd;
		}
		else {
			$passwd = !$passwd ? $origin_passwd : md5($passwd); //새암호가 없으면 이전암호로 대체
		}
		$editDate = mktime(); //수정일자
		$ipEdit = $_SERVER["REMOTE_ADDR"]; //수정 IP주소
		$usrAgentEdit = StrAddSlashes(substr(trim($_SERVER["HTTP_USER_AGENT"]),0,250)); //수정환경

		$query = "UPDATE ".$_table_id_board." SET
				objProperty = '{$objProperty}',
				isNotice = {$isNotice},
				isSecret = {$isSecret},
				docType = '{$docType}',
				e_mail = '{$e_mail}',
				homeUrl = '{$homeUrl}',
				subject = '{$subject}',
				category = '{$category}',
				tag_ls = '{$tag_ls}',
				editDate = '{$editDate}',
				ipEdit = '{$ipEdit}',
				usrAgentEdit = '{$usrAgentEdit}',
				siteLink1 = '{$siteLink1}',
				siteLink2 = '{$siteLink2}',
				tbLink = '{$tbLink}',
				tel_num = '{$tel_num}',
				hp_num = '{$hp_num}',
				start_date = '{$start_date}',
				end_date = '{$end_date}',
				other01 = '{$other01}',
				other02 = '{$other02}',
				other03 = '{$other03}',
				other04 = '{$other04}',
				other05 = '{$other05}',
				other06 = '{$other06}',
				other07 = '{$other07}',
				other08 = '{$other08}',
				other09 = '{$other09}',
				other10 = '{$other10}', ";
		for($i=0; $i<$fileMaxNum; $i++) {
			$delAttachFile[$i] = intval($_POST["delAttachFile".($i+1)]);
			//파일 삭제만 할 경우
			if($delAttachFile[$i]) {
				if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".($i+1)}))
					unlink("{$FSDATA_ROOT}/{$id}/".${"fileName".($i+1)}); //파일 삭제
				$query .= "fileName".($i+1)." = '', "; //파일이름 삭제
			}

			if($file_name[$i]) { //수정 업로드된 파일이 있을경우
				$query .= "fileName".($i+1)." = '".StrAddSlashes($file_name[$i])."', "; //파일이름 변경
				if(${"fileName".($i+1)}) { //이전 파일이 존재할 경우
					if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".($i+1)}))
						unlink("{$FSDATA_ROOT}/{$id}/".${"fileName".($i+1)}); //이전 파일 삭제
				}
			}
		}

		if(!$MemId) $query .= " passwd = '{$passwd}', author = '{$author}', ";

		$query .= " contents = '{$contents}' WHERE idx={$idx};";
		//echo $query;exit;

		//게시물 수정
		mysql_query($query) or Error(mysql_error());

		if($isNotice&&!$origin_isNotice) { //일반글에서 공지글로 수정되었을 경우 공지글수 증가
			$query = "UPDATE {$_table_id_admin} SET noticeNum=noticeNum+1 WHERE boardId='{$id}' AND aidx={$aidx};";
			mysql_query($query) or Error(mysql_error());
		}

		if(!$isNotice&&$origin_isNotice) { //공지글에서 일반글로 수정되었을 경우 공지글수 감소
			$query = "UPDATE {$_table_id_admin} SET noticeNum=noticeNum-1 WHERE boardId='{$id}' AND aidx={$aidx};";
			mysql_query($query) or Error(mysql_error());
		}

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

		if($tb_error_str) {
			$msg = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('트랙백 전송중 에러가 발생하였습니다\\n\\n{$tb_error_str}');window.location.href='".$_SERVER["PHP_SELF"]."?".QryStr("view",1)."&amp;idx={$idx}&amp;page={$page}"."';\n//]]>\n</script>";
			echo $combinedDesign ? "<html><head>{$msg}</head><body></body></html>" : $msg;
			exit;
		}
		else {
			MovePage($_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}");
		}
	}


	/////처음 글쓰기일 경우
	if($mode=="writesave") {
		$query = "INSERT INTO ".$_table_id_board." (
				objProperty,
				isNotice,
				isSecret,
				isMember,
				docType,
				author,
				e_mail,
				homeUrl,
				subject,
				passwd,
				category,
				tag_ls,
				regDate,
				editDate,
				memoLatestDate,
				memoNum,
				tbNum,
				readNum,
				voteNum,
				ipReg,
				ipEdit,
				usrAgentReg,
				usrAgentEdit,
				ref,
				reStep,
				reLevel,
				siteLink1,
				siteLink2,
				tbLink,
				tel_num,
				hp_num,
				start_date,
				end_date,
				other01,
				other02,
				other03,
				other04,
				other05,
				other06,
				other07,
				other08,
				other09,
				other10,";
		for($i=1; $i<=$fileMaxNum; $i++) {
			$query .= "fileName{$i}, fileDownload{$i}, ";
		}
		$query .= "contents) VALUES (
				'{$objProperty}',
				{$isNotice},
				{$isSecret},
				'{$isMember}',
				'{$docType}',
				'{$author}',
				'{$e_mail}',
				'{$homeUrl}',
				'{$subject}',
				'{$passwd}',
				'{$category}',
				'{$tag_ls}',
				{$regDate},
				{$editDate},
				{$memoLatestDate},
				{$memoNum},
				{$readNum},
				{$tbNum},
				{$voteNum},
				'{$ipReg}',
				'{$ipEdit}',
				'{$usrAgentReg}',
				'{$usrAgentEdit}',
				{$ref},
				{$reStep},
				{$reLevel},
				'{$siteLink1}',
				'{$siteLink2}',
				'{$tbLink}',
				'{$more_link}',
				'{$tel_num}',
				'{$hp_num}',
				'{$end_date}',
				'{$other01}',
				'{$other02}',
				'{$other03}',
				'{$other04}',
				'{$other05}',
				'{$other06}',
				'{$other07}',
				'{$other08}',
				'{$other09}',
				'{$other10}',";

		for($i=0; $i<$fileMaxNum; $i++) {
			$query .= "'".StrAddSlashes($file_name[$i])."', 0, ";
		}
		$query .= "'{$contents}');";
		//echo $query;

		//게시물 삽입
		mysql_query($query) or Error(mysql_error());
		$idx = mysql_insert_id();

		//게시물 갯수 증가
		$query = "UPDATE {$_table_id_admin} SET totalObj=totalObj+1";
		if($isNotice) $query .= ", noticeNum=noticeNum+1 "; //공지글일경우 공지글수 증가를 포함시킴
		$query .= " WHERE boardId='{$id}' AND aidx={$aidx};";
		mysql_query($query) or Error(mysql_error());

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

		if($tb_error_str) {
			$msg = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('트랙백 전송중 에러가 발생하였습니다\\n\\n{$tb_error_str}');window.location.href='".$_SERVER["PHP_SELF"]."?id={$id}&amp;mode=list';\n//]]>\n</script>";
			echo $combinedDesign ? "<html><head>{$msg}</head><body></body></html>" : $msg;
			exit;
		}
		else {
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=list");
		}
	}













} else if($mode == "remove") { //////////////////////////////////////////////////////////////////////remove

	$auth_passwd = $_POST["auth_passwd"];
	if(!$auth_passwd) {
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=remove&idx={$idx}&page={$page}");
		exit;
	}
	else {
		if(!$MemId) $auth_passwd = md5($auth_passwd);
	}

	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	//원래 게시물에서 암호 가져옴
	$query = "SELECT passwd FROM ".$_table_id_board." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	$numrows = mysql_num_rows($result);
	if(!$numrows) Error("유효하지 않은 데이터입니다.");
	$rs = mysql_fetch_row($result);
	if($result) $passwd = $rs[0];

	//삭제암호 인증 확인
	if($auth_passwd!=$passwd && md5($auth_passwd)!=$passwd && $auth_passwd!=$adminPasswd && !$isAdmin) {
		Error("암호가 일치하지 않습니다.");
	}

	//삭제할 게시물의 정보를 가져옴
	$query = "SELECT * FROM ".$_table_id_board." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	$numrows = mysql_num_rows($result);
	$rs = mysql_fetch_array($result);

	if(!$numrows) Error("게시물이 없거나 이미 삭제되었습니다.");

	/////첨부파일 삭제
	if($result) {
		$isNotice = $rs["isNotice"]; //공지글 여부
		for($i=1; $i<=$fileMaxNum; $i++) {
			${"fileName".$i} = $rs["fileName".$i]; //첨부파일 이름 가져옴
			if(${"fileName".$i}) { //첨부파일 삭제
				if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".$i})) {
					unlink("{$FSDATA_ROOT}/{$id}/".${"fileName".$i}); //첨부파일 삭제
				}
			}
		}
		mysql_free_result($result);
	}

	/////게시물 삭제
	$query = "DELETE FROM ".$_table_id_board." WHERE idx={$idx};";
	mysql_query($query) or Error(mysql_error());

	/////게시물 갯수 감소
	$query = "UPDATE {$_table_id_admin} SET totalObj=totalObj-1";
	if($isNotice) $query .= ", noticeNum=noticeNum-1 "; //공지글일 경우 공지글수 감소
	$query .= " WHERE boardId='{$id}' AND aidx={$aidx};";
	mysql_query($query) or Error(mysql_error());

	/////댓글 삭제
	$query = "DELETE FROM ".$_table_id_comment." WHERE boardId='{$id}' AND objNum={$idx};";
	mysql_query($query) or Error(mysql_error());

	/////트랙백 기록 삭제
	$query = "DELETE FROM ".$_table_id_trackback." WHERE boardId='{$id}' AND objNum={$idx};";
	mysql_query($query) or Error(mysql_error());

	MovePage($_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}");












} else if($mode == "saveMemo") { //////////////////////////////////////////////////////////////////////saveMemo

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");
	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	if(!$memowrite_level) Error("댓글쓰기 권한이 없습니다.");

	$passwd = trim($_POST["passwd"]);
	$name = StrAddSlashes(trim($_POST["name"]));
	$e_mail = StrAddSlashes(trim($_POST["e_mail"]));
	$comments = StrAddSlashes(trim($_POST["comments"]));

	if($MemId&&!$passwd) { //로그인상태이면
		$passwd = $MemPasswd; //회원암호 사용
	}
	else {
		$passwd = md5($passwd);
	}

	$usrAgentReg = StrAddSlashes(substr(trim($_SERVER["HTTP_USER_AGENT"]),0,250));

	//스팸방지기능 사용일경우
	if($useBlockSpam&&!$isAdmin) {
		$query = "SELECT ipReg,regDate FROM ".$_table_id_comment." ORDER BY cidx DESC LIMIT 1";
		$result = mysql_query($query) or Error(mysql_error());
		$rs = mysql_fetch_row($result);
		$ipReg = $rs[0]; //마지막글 IP주소
		$regDate = $rs[1]; //마지막글 등록일자
		mysql_free_result($result);

		//동일 IP에서 30초이내 글쓰기 금지
		if($ipReg==$REMOTE_ADDR&&(mktime()-$regDate)<30) {
			//Error("스팸글을 방지하기 위해 동일한 IP주소에서는 <br />이전 글쓰기 이후 30초가 지난후에 글쓰기가 가능합니다.");
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("스팸글을 방지하기 위해 동일한 IP주소에서는\\n이전 글쓰기 이후 30초가 지난 후에 글쓰기가 가능합니다."));
		}
	}

	if(IsBlank($passwd)) Error("암호를 입력해 주세요.");
	if(IsBlank($name)) Error("이름을 입력해 주세요");
	if(IsBlank($comments)) Error("메모내용을 입력해 주세요.");

	$query = "INSERT INTO ".$_table_id_comment." (
			boardId,
			objNum,
			isMember,
			name,
			e_mail,
			passwd,
			regDate,
			editDate,
			ipReg,
			ipEdit,
			usrAgentReg,
			usrAgentEdit,
			comments
		) VALUES (
			'{$id}',
			{$idx},
			'{$MemId}',
			'{$name}',
			'{$e_mail}',
			'{$passwd}',
			".mktime().",
			0,
			'{$_SERVER["REMOTE_ADDR"]}',
			'',
			'{$usrAgentReg}',
			'',
			'{$comments}');";

	mysql_query($query) or Error(mysql_error());
	$cidx = mysql_insert_id();

	//댓글수 증가
	$query = "UPDATE ".$_table_id_board." SET memoNum=memoNum+1,memoLatestDate=".mktime()." WHERE idx={$idx};";
	mysql_query($query) or Error(mysql_error());

	if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }


	MovePage($_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}");














} else if($mode == "editMemo") { //////////////////////////////////////////////////////////////////////editMemo

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");
	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	$cidx = $_GET["cidx"];
	if($cidx) $cidx = intval($cidx); else Error("메모글 코드가 잘못되었습니다.");

	${"passwd_".$cidx} = StrAddSlashes(trim($_POST["passwd_{$cidx}"]));
	$name = StrAddSlashes(trim($_POST["name_{$cidx}"]));
	$e_mail = StrAddSlashes(trim($_POST["e_mail_{$cidx}"]));
	$comments = StrAddSlashes(trim($_POST["comments_{$cidx}"]));
	$usrAgentEdit = StrAddSlashes(substr(trim($_SERVER["HTTP_USER_AGENT"]),0,250));

	if(IsBlank(${"passwd_".$cidx})&&!$MemId&&!$isAdmin) Error("암호를 입력해 주세요.");
	if(IsBlank($name)) Error("이름을 입력해 주세요.");
	if(IsBlank($comments)) Error("메모내용을 입력해 주세요.");

	//이전 암호 가져옴
	$query = "SELECT isMember,passwd FROM ".$_table_id_comment." WHERE boardId='{$id}' AND cidx={$cidx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) Error("유효하지 않은 데이터입니다.");
		$rs = mysql_fetch_array($result);
		$isMember = $rs[0];
		$passwd = $rs[1];
		mysql_free_result($result);
	}
	if(!$isMember) $isMember = $PHPSESSID; //비로그인 상태에서 비회원 수정시 인증되는 현상 방지

	if($passwd!=md5(${"passwd_".$cidx}) && md5(${"passwd_".$cidx})!=$adminPasswd && $isMember!=$MemId && !$isAdmin) {
		//Error("암호가 일치하지 않습니다.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("암호가 일치하지 않습니다."));
	}
	else {
		$query = "UPDATE ".$_table_id_comment." SET
				name = '{$name}',
				e_mail = '{$e_mail}',
				editDate = ".mktime().",
				ipEdit = '{$_SERVER["REMOTE_ADDR"]}',
				usrAgentEdit = '{$usrAgentEdit}',
				comments = '{$comments}'
			WHERE cidx={$cidx} AND boardId='{$id}' AND objNum={$idx};";

		mysql_query($query) or Error(mysql_error());

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

		MovePage($_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}");
	}











} else if($mode == "removeMemo") { //////////////////////////////////////////////////////////////////////removeMemo

	$auth_passwd = $_POST["auth_passwd"];
	if(!$auth_passwd) {
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=removeMemo&idx={$idx}&cidx={$cidx}&page={$page}");
		exit;
	}
	else {
		if(!$MemId) $auth_passwd = md5($auth_passwd);
	}

	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	$cidx = $_GET["cidx"];
	if($cidx) $cidx = intval($cidx); else Error("메모글 코드가 잘못되었습니다.");

	$query = "SELECT passwd FROM ".$_table_id_comment." WHERE boardId='{$id}' AND objNum={$idx} AND cidx={$cidx};";
	$result = mysql_query($query) or Error(mysql_error());
	$numrows = mysql_num_rows($result);
	if(!$numrows) Error("유효하지 않은 데이터입니다.");
	$rs = mysql_fetch_row($result);
	if($result) $passwd = $rs[0];

	if($auth_passwd!=$passwd && md5($auth_passwd)!=$passwd && $auth_passwd!=$adminPasswd && !$isAdmin) {
		//Error("암호가 일치하지 않습니다.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("암호가 일치하지 않습니다."));
	}
	else {
		//메모글 숫자 감소
		$query = "UPDATE ".$_table_id_board." SET memoNum=memoNum-1 WHERE idx={$idx};";
		mysql_query($query) or Error(mysql_error());

		//메모글 삭제
		$query = "DELETE FROM ".$_table_id_comment." WHERE boardId='{$id}' AND objNum={$idx} AND cidx={$cidx};";
		mysql_query($query) or Error(mysql_error());

		MovePage($_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}");
	}




} else if($mode == "sendSMS") { //////////////////////////////////////////////////////////////////////sendSMS

	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");
	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	$sms_receive_name = StrAddSlashes(trim($_POST["sms_receive_name"]));
	$sms_msg = StrAddSlashes(trim($_POST["msg"]));

	$usrAgentReg = StrAddSlashes(substr(trim($_SERVER["HTTP_USER_AGENT"]),0,250));


    /******************** 인증정보 ********************/
    $sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
	// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
    $sms['user_id'] = base64_encode("webroinsms"); //SMS 아이디.
    $sms['secure'] = base64_encode("fea4a8fe746a2e7da835748d00304a26") ;//인증키
    $sms['msg'] = base64_encode(stripslashes($_POST['msg']));

    $sms['rphone'] = base64_encode($_POST['rphone']);
    $sms['sphone1'] = base64_encode($_POST['sphone1']);
    $sms['sphone2'] = base64_encode($_POST['sphone2']);
    $sms['sphone3'] = base64_encode($_POST['sphone3']);
    $sms['rdate'] = base64_encode($_POST['rdate']);
    $sms['rtime'] = base64_encode($_POST['rtime']);
    $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
    $sms['returnurl'] = base64_encode($_POST['returnurl']);
    $sms['testflag'] = base64_encode($_POST['testflag']);
    $sms['destination'] = base64_encode($_POST['destination']);
    $returnurl = $_POST['returnurl'];
    $sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);
    $sms['repeatNum'] = base64_encode($_POST['repeatNum']);
    $sms['repeatTime'] = base64_encode($_POST['repeatTime']);
    $nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

    $host_info = explode("/", $sms_url);
    $host = $host_info[2];
    $path = $host_info[3]."/".$host_info[4];

    srand((double)microtime()*1000000);
    $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
    //print_r($sms);

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
        $rMsg = explode(",", $msg[1]);
        $Result= $rMsg[0]; //발송결과
        $Count= $rMsg[1]; //잔여건수

        //발송결과 알림
        if($Result=="success") {
            $alert = "성공";
            $alert .= " 잔여건수는 ".$Count."건 입니다.";
        }
        else if($Result=="reserved") {
            $alert = "성공적으로 예약되었습니다.";
            $alert .= " 잔여건수는 ".$Count."건 입니다.";
        }
        else if($Result=="3205") {
            $alert = "잘못된 번호형식입니다.";
        }

		else if($Result=="0044") {
            $alert = "스팸문자는발송되지 않습니다.";
        }

        else {
            $alert = "[Error]".$Result;
        }
    }
    else {
        $alert = "Connection Failed";
    }
    /******************** 인증정보 ********************/

    if($nointeractive=="1" && ($Result!="success" && $Result!="Test Success!" && $Result!="reserved") ) {
        echo "<script>alert('".$alert ."')</script>";
    }
    else if($nointeractive!="1") {
        echo "<script>alert('".$alert ."')</script>";
    }
    echo "<script>location.href='".$returnurl."';</script>";
	
	if ($Result=="success" Or $Result=="reserved"){
		$query = "INSERT INTO ".$_table_id_sms." (
				sms_code,
				sms_receive_name,
				sms_msg,
				sms_hp,
				sms_result_code,
				sms_date
			) VALUES (
				'{$sms_code}',
				'{$sms_receive_name}',
				'{$sms_msg}',
				'{$rphone}',
				'{$Result}',
				".mktime().");";
		
		mysql_query($query) or Error(mysql_error());
		$cidx = mysql_insert_id();


		//결과 저장
		$query = "UPDATE ".$_table_id_board." SET other07='{$other07}',other08='{$other08}' WHERE idx={$idx};";
		mysql_query($query) or Error(mysql_error());

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }


		MovePage($_SERVER["PHP_SELF"]."?".QryStr("list")."&idx={$idx}&page={$page}");
	}else{
		exit;
	}

} else if($mode == "removeTrackback") { //////////////////////////////////////////////////////////////////////removeTrackback

	if(!$auth_passwd) {
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=removeTrackback&idx={$idx}&tidx={$tidx}&page={$page}");
		exit;
	}
	else {
		if(!$MemId) $auth_passwd = md5($auth_passwd);
	}

	if($idx) $idx = intval($idx); else Error("게시물 코드가 잘못되었습니다.");

	$tidx = $_GET["tidx"];
	if($tidx) $tidx = intval($tidx); else Error("트랙백 코드가 잘못되었습니다.");

	if($auth_passwd!=$adminPasswd && !$isAdmin) {
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("암호가 일치하지 않습니다."));
	}
	else {
		//트랙백 갯수 숫자 감소
		$query = "UPDATE ".$_table_id_board." SET tbNum=tbNum-1 WHERE idx={$idx};";
		mysql_query($query) or Error(mysql_error());

		//트랙백 삭제
		$query = "DELETE FROM ".$_table_id_trackback." WHERE boardId='{$id}' AND objNum={$idx} AND tidx={$tidx};";
		mysql_query($query) or Error(mysql_error());

		MovePage($_SERVER["PHP_SELF"]."?".QryStr("view")."&idx={$idx}&page={$page}");
	}











} else if($mode == "download") { //////////////////////////////////////////////////////////////////////download
	@ob_end_clean();

	$maintainCode = trim($_GET["maintainCode"]);

	if($useBlockAnyLink) {
		if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) die();
		if($maintainCode != md5(session_id())) die();
	}

	if(!$view_level) Error("권한이 없습니다.");

	$filenum = trim($_GET["filenum"]);
	$filename = trim($_GET["filename"]);

	$filenum = intval($filenum);
	$filename = StrAddSlashes($filename);

	//식별자 검사
	if(!$filenum||!$filename||!$idx) Error("다운받으려는 파일정보가 잘못되었습니다.");

	if(file_exists("{$FSDATA_ROOT}/{$id}/{$filename}")) {
		//다운로드수 증가시킴
		$query = "UPDATE ".$_table_id_board." SET fileDownload{$filenum}=fileDownload{$filenum}+1 WHERE fileName{$filenum}='{$filename}' AND idx={$idx};";
		mysql_query($query) or Error(mysql_error());

		//헤더 보내기전인 경우
		if(!headers_sent()) {
			//로컬 파일경로
			$filepath = "{$FSDATA_ROOT}/{$id}/{$filename}";

			//header("location:{$filepath}");
			header("Cache-control: private");

			//헤더 설정
			if (eregi("(MSIE 5.5|MSIE 6.0|MSIE 7.0)", $_SERVER["HTTP_USER_AGENT"])) {
				header("Content-type:application/octet-stream");
				header("Content-Length:".filesize($filepath));
				header("Content-Disposition:attachment;filename=".basename($filename));
				header("Content-Transfer-Encoding:binary");
				header("Pragma:no-cache");
				header("Expires:0");
			} else {
				header("Content-type:file/unknown");
				header("Content-Length:".filesize($filepath));
				header("Content-Disposition:attachment; filename=".basename($filepath));
				header("Content-Description:PHP3 Generated Data");
				header("Pragma: no-cache");
				header("Expires: 0");
			}

			//파일 강제 다운로드
			if (is_file($filepath)) {
				$fp = fopen($filepath, "rb");
				if (!fpassthru($fp)) fclose($fp);
				clearstatcache();
			} else {
				Error("해당 파일이나 경로가 존재하지 않습니다.");
			}
		}
		else {
			//웹 파일경로
			$filepath = "{$FSDATA_PATH}/{$id}/".urlencode($filename);
			$filepath = str_replace("+"," ",$filepath);

			//파일 링크
			$lnk = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location.href='{$filepath}';\n//]]>\n</script>";
			echo $combinedDesign ? "<html><head>{$lnk}</head><body></body></html>" : $lnk;
		}

		if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
	}
	else Error("파일이 존재하지 않습니다.");
	exit;











} else if($mode == "filelink") { //////////////////////////////////////////////////////////////////////fileLink

	@ob_end_clean();

	$nav = trim($_GET["nav"]);
	$filename = trim($_GET["filename"]);
	$maintainCode = trim($_GET["maintainCode"]);

	if($useBlockAnyLink) {
		//if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) die();
		if($maintainCode != md5(session_id())) die();
	}

	$filepath = "{$FSDATA_PATH}/{$id}/".urlencode($filename);
	$filepath = str_replace("+"," ",$filepath);

	if(!headers_sent()) {
		header("location:{$filepath}");
	}
	else {
		@ob_end_clean();
		@header("location:{$filepath}");
		$lnk = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location.href='{$filepath}';\n//]]>\n</script>";
		echo $combinedDesign ? "<html><head>{$lnk}</head><body></body></html>" : $lnk;
	}
	exit;









} else if($mode == "sitelink") { //////////////////////////////////////////////////////////////////////sitelink

	$idx = intval($_GET["idx"]);
	$lnknum = intval($_GET["lnknum"]);

	//사이트링크 정보 가져옴
	$query = "SELECT siteLink1,siteLink2,siteLinkCount1,siteLinkCount2 FROM ".$_table_id_board." WHERE idx={$idx};";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$rs = mysql_fetch_array($result);
		$siteLink1 = $rs["siteLink1"]; //링크주소1
		$siteLink2 = $rs["siteLink2"]; //링크주소2
		$siteLinkCount1 = $rs["siteLinkCount1"]; //링크 클릭수1
		$siteLinkCount2 = $rs["siteLinkCount2"]; //링크 클릭수2
		mysql_free_result($result);
	}

	//쿼리변수 초기화
	$query = "";

	//사이트링크1일 경우
	if($lnknum==1) {
		$lnk = trim($siteLink1);
		if($siteLink1) $query = "UPDATE ".$_table_id_board." SET siteLinkCount1 = ".($siteLinkCount1+1)." WHERE idx={$idx};";
	}

	//사이트링크2일 경우
	if($lnknum==2) {
		$lnk = trim($siteLink2);
		if($siteLink2) $query = "UPDATE ".$_table_id_board." SET siteLinkCount2 = ".($siteLinkCount2+1)." WHERE idx={$idx};";
	}

	//클릭수 증가
	if($query) mysql_query($query) or Error(mysql_error());

	if(IsHomepage($lnk)) {
		MovePage($lnk); //홈페이지 패턴에 맞으면 사이트링크로 이동
	}
	else {
		echo "{$lnk}"; //홈페이지 패턴이 아니면 링크를 그냥 뿌림
	}
	exit;










} else if($mode == "rss.xml") { //////////////////////////////////////////////////////////////////////RSS Feed

	@ob_end_clean();

	if (!empty($_SERVER['SERVER_SOFTWARE']) && strstr($_SERVER['SERVER_SOFTWARE'], 'Apache/2')) {
		header ('Cache-Control: no-cache, pre-check=0, post-check=0, max-age=0');
		header ('Pragma: no-cache');
	}
	else {
		header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
		header ('Pragma: no-cache');
	}

	header ('Expires: '.$lastBuildDate.'');
	header ('Last-Modified: '.$lastBuildDate.'');
	header ('Content-Type: text/xml; charset=euc-kr');

	//마지막 업데이트일자
	$lastBuildDate = date('D, d M Y H:i:s').' +0900';

	$allowRss = $useRssFeed; //접근허용
	$feednum = $pageSize; //피드 수
	//$copyright_s = "Copyright(c)2006 fsboard. All right reserved."; //저작권 표시
	$sitename = $boardName; //사이트명
	$sitedescription = "RSS Feed for ".$boardName; //사이트 설명
	$banner_image = ""; //배너이미지
	$banner_width = ""; //배너이미지 폭
	$banner_height = ""; //배너이미지 높이
	$siteurl = $combinedFileName ? "http://".$_SERVER["HTTP_HOST"].(strpos($combinedFileName,"/")>0?"/":"").$combinedFileName :
		(IsRewrite() ? "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/".$id : "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/".$_fsMainExecFile."?id=".$id); //사이트 주소
	$webmaster = ""; //관리자 이메일

	//RSS 허용 확인
	if(!$allowRss || !$list_level) {
		echo "<?xml version=\"1.0\" encoding=\"euc-kr\"?".">\n";
		echo "<rss version=\"2.0\">";
		echo "<response>";
		echo "<error>1</error>";
		echo "<message>해당 게시판은 추출할 수 없습니다.</message>";
		echo "</response>";
		echo "</rss>";
		exit;
	}

	$query = "SELECT * FROM ".$_table_id_board." ORDER BY idx DESC LIMIT 0,{$feednum};";
	$result = mysql_query($query);
	$i = 1;
	while($rs = mysql_fetch_array($result)) {
		$r_idx[] = $rs["idx"];
		$r_secret[] = $rs["isSecret"];
		$r_doctype[] = $rs["docType"];
		$r_author[] = htmlspecialchars(trim($rs["author"]));
		$r_category[] = htmlspecialchars(trim($rs["category"]));
		$r_subject[] = htmlspecialchars(trim($rs["subject"]));
		$r_memonum[] = $rs["memoNum"];
		$r_regdate[] = $rs["regDate"];
		$r_regdate_x[] = date('D, d M Y H:i:s',$rs["regDate"]).' +0900';
		$r_contents[] = trim($rs["contents"]);

		for($j=1; $j<=30; $j++) {
			${"r_file".$j}[] = $rs["fileName".$j];
		}
		$i++;
	}


	?><?="<?xml version=\"1.0\" encoding=\"euc-kr\"?>\n"?>
	<!--// RSS2.0 -->
	<rss version="2.0"
		xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/"
		xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<?if($copyright_s){?><copyright><?=$copyright_s?></copyright><?}?>
		<pubDate><?=$lastBuildDate?></pubDate>
		<lastBuildDate><?=$lastBuildDate?></lastBuildDate>
		<description><![CDATA[<?=$sitedescription?>]]></description>
		<link><?=htmlspecialchars($siteurl)?></link>
		<title><![CDATA[<?=$sitename?>]]></title>
		<?if($banner_image){?><image>
			<url><?=$banner_image?></url>
			<title><![CDATA[<?=$sitename?>]]></title>
			<link><?=$siteurl?></link>
			<height><?=$banner_height?></height>
			<width><?=$banner_width?></width>
			<description><![CDATA[<?=$sitename?>]]></description>
		</image><?}?>
		<?if($webmaster){?><managingEditor><?=$webmaster?></managingEditor>
		<webMaster><?=$webmaster?></webMaster><?}?>
		<language>ko</language><?

	@rsort($r_regdate);
	for($i=0, $till=sizeof($r_idx); $i<$till; $i++) {

		$memoNum = $r_memonum[$i] ? "(".$r_memonum[$i].")" : "";

		$contents = $r_contents[$i];
		if($r_secret[$i]) $contents = "<span style=\"color:red;\">[비밀글입니다]</span>";
		if(!$view_level) $contents = "<span style=\"color:red;\">[내용을 볼수 있는 권한이 없습니다]</span>";
		if($r_doctype[$i]=="text" || $r_doctype[$i]=="br") $contents = str_replace("\r\n","<br />",$contents);

		setlocale (LC_TIME,"ko");
		$name_sq = "<br /><br />작성자 : ".$r_author[$i]."<br />작성일자: ".strftime("%Y년 %m월 %d일 %A %p %I:%M:%S",$r_regdate[$i])."";

		$category = $r_category[$i] ? "<category><![CDATA[".$r_category[$i]."]]></category>" : "";

		$fileName = "";
		for($j=1; $j<=30; $j++) {
			$tmp = ${"r_file".$j}[$i];
			if($tmp && !$r_secret[$i]) {
				$tmp = str_replace("%2F","/",htmlspecialchars(urlencode($tmp)));
				$f_ext = substr(strrchr($tmp, '.'), 1);

				if(eregi("jpg|png|gif|jpeg|bmp",$f_ext)) {
					${"fileName".$j} = "<img src=\"http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=".$id."&amp;mode=filelink&amp;maintainCode=".md5(session_id())."&amp;filename=".$tmp."\" alt=\"첨부사진\" /><br /><br />";
				}
				else {
					${"fileName".$j} = "<a href=\"http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=".$id."&amp;mode=download&amp;maintainCode=".md5(session_id())."&amp;idx=".$r_idx[$i]."&amp;filenum=".$j."&amp;filename=".$tmp."\">".$tmp."</a><br /><br />";
				}

				$fileName .= ${"fileName".$j};
				$fileSize = @filesize($FSDATA_ROOT."/{$id}/{$tmp}");

				//$enclosure[$j-1] ="<enclosure url=\"http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=test&amp;mode=download&amp;idx=".$r_idx[$i]."&amp;filenum=".$j."&amp;filename=".$tmp."\" length=\"".$fileSize."\"";
				$enclosure[$j-1] ="<enclosure url=\"{$FSDATA_PATH}/{$id}/{$tmp}\" length=\"".$fileSize."\"";
				if(eregi("jpg|jpeg",$f_ext)) $enclosure[$j-1] .= " type=\"image/jpeg\" />";
				elseif(eregi("gif",$f_ext)) $enclosure[$j-1] .= " type=\"image/gif\" />";
				elseif(eregi("png",$f_ext)) $enclosure[$j-1] .= " type=\"image/png\" />";
				elseif(eregi("bmp",$f_ext)) $enclosure[$j-1] .= " type=\"image/bmp\" />"; 
				elseif(eregi("zip|rar|alz",$f_ext)) $enclosure[$j-1] .= " type=\"application/zip\" />"; 
				elseif(eregi("exe|hwp|psd|fla",$f_ext)) $enclosure[$j-1] .= " type=\"application/octet-stream\" />"; 
				elseif(eregi("pdf",$f_ext)) $enclosure[$j-1] .= " type=\"application/pdf\" />"; 
				elseif(eregi("ppt",$f_ext)) $enclosure[$j-1] .= " type=\"application/vnd.ms-powerpoint\" />"; 
				elseif(eregi("txt",$f_ext)) $enclosure[$j-1] .= " type=\"text/plain\" />"; 
				elseif(eregi("xls",$f_ext)) $enclosure[$j-1] .= " type=\"application/vnd.ms-excel\" />";
				elseif(eregi("swf",$f_ext)) $enclosure[$j-1] .= " type=\"application/x-shockwave-flash\" />";
				elseif(eregi("asf|wma|wmv",$f_ext)) $enclosure[$j-1] .= " type=\"video/x-ms-asf\" />";
				elseif(eregi("asf|wma|wmv",$f_ext)) $enclosure[$j-1] .= " type=\"video/x-ms-asf\" />";
				else $enclosure[$j-1] = "";
			}
			else $enclosure[$j-1] = "";
		}

		$document_link = IsRewrite() ? "http://".$_SERVER["HTTP_HOST"].$FSBOARD_PATH."/".$id."/".$r_idx[$i] : "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?id=".$id."&amp;idx=".$r_idx[$i];
		?>

		<item>
			<title><![CDATA[<?=$r_author[$i]?> - <?=$r_subject[$i]?> <?=$memoNum?>]]></title>
			<link><?=$document_link?></link>
			<description><![CDATA[<?=$fileName.$contents.$name_sq?>]]></description>
			<author><?=$r_author[$i]?></author>
			<pubDate><?=$r_regdate_x[$i]?></pubDate>
			<slash:comments><?=$r_memonum[$i]?></slash:comments>
			<guid>http://<?=$_SERVER["HTTP_HOST"]?><?=$_SERVER["PHP_SELF"]?>?id=<?=$id?>&amp;idx=<?=$r_idx[$i]?></guid>
			<?=$category?>
			<?for($j=0; $j<30; $j++) { echo $enclosure[$j]; }?>
		</item>

		<?
	}
	?></channel>
	</rss><?
	exit;










} else if($mode == "tagcloud") { //////////////////////////////////////////////////////////////////////Tag Cloud

	$order = StrAddSlashes(trim($_GET["order"]));

	if($combinedDesign && !headers_sent()) MovePage($_fsMainExecFile."?id=".$id."&mode=tagcloud&order=".$order);

	if(!headers_sent()) {
		ob_end_clean();

		$order = $order == "latest" ? "usedate" : "freqRate";

		$query = "SELECT * FROM ".$_table_id_tagcloud." WHERE boardId='".$id."' ORDER BY ".$order." DESC  LIMIT 0, 20;";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$numrows = mysql_num_rows($result);
			if($numrows) {
				while($rs = mysql_fetch_array($result)) {
					$strtag = $rs["strtag"];
					$freqRate = $rs["freqRate"];
					$usedate = $rs["usedate"];

					if($freqRate<5) {
						$fontsize = 10;
						$fontcolor = "#000";
						$fontweight = "normal";
						$bgcolor = "#fff";
					}
					else if($freqRate>=5 && $freqRate<10) {
						$fontsize = 12;
						$fontcolor = "#000";
						$fontweight = "bold";
						$bgcolor = "#fff";
					}
					else if($freqRate>=10) {
						$fontsize = 14;
						$fontcolor = "#fff";
						$fontweight = "bolder";
						$bgcolor = "0d0";
					}
					else {
						$fontsize = 9;
						$fontcolor = "#000";
						$fontweight = "normal";
						$bgcolor = "#fff";
					}
					echo "<a href=\"{$_fsMainExecFile}?id={$id}&amp;srhctgr=1000&amp;srhstr=".urlencode($strtag)."\" style=\"text-decoration:none;\"><span style=\"font-size:{$fontsize}px; color:{$fontcolor}; font-weight:{$fontweight}; background-color:{$bgcolor}\">{$strtag}</span></a>\n";
				}
			}
			else {
				echo "<span style=\"font-size:13px;\">등록된 태그가 없습니다</span>";
			}
			mysql_free_result($result);
		}
	}










} else if($mode == "atsign") { //////////////////////////////////////////////////////////////////////@ Charactor image

	if(!headers_sent()) {
		header('Content-type: image/gif');
		ob_end_clean();
		echo "\x47\x49\x46\x38\x39\x61\x0a\x00\x08\x00\x80\x00\x00";
		echo chr(hexdec($_GET['color'][0].$_GET['color'][1]));
		echo chr(hexdec($_GET['color'][2].$_GET['color'][3]));
		echo chr(hexdec($_GET['color'][4].$_GET['color'][5]));
		echo "\xff\xff\xff\x21\xf9\x04\x01\x0a\x00\x01\x00\x2c\x00\x00";
		echo "\x00\x00\x0a\x00\x08\x00\x00\x02\x11\x8c\x81\x60\xab\xec";
		echo "\x91\xe0\x91\xb2\x29\x4b\x6d\x7c\xab\xc3\x1d\x14\x00\x3b";
		exit();
	}










} else if($mode == "error") { //////////////////////////////////////////////////////////////////////error

	include $INC_PATH."lib/_error.php";













} else if($mode == "authenticate") { //////////////////////////////////////////////////////////////////////authenticatelogin

	$login_included = true;
	include $INC_PATH."lib/_login.php";




} else if($mode == "login") { //////////////////////////////////////////////////////////////////////login

	if($MemId) MovePage($_SERVER["PHP_SELF"]."?id=".$id);

	$auth_id = trim($_POST["auth_id"]);
	$auth_passwd = trim($_POST["auth_passwd"]);
	$auto_auth = trim($_POST["auto_auth"]);
	$referer = trim($_POST["referer"]);
	$numrows = 0;

	//자동로그인 쿠키정보 가져오기
	if($_COOKIE["amid"] && $_COOKIE["pswd"]) {
		$auth_id = trim($_COOKIE["amid"]);
		$auth_passwd = trim(StrAddSlashes($_COOKIE["pswd"]));
		$autoAuth = true;
	}

	if(!$auth_passwd) {
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav={$mode}&idx={$idx}&page={$page}");
	}
	else {
		$auth_id = StrAddSlashes($auth_id);
		if(!$MemId && !$autoAuth) $auth_passwd = md5($auth_passwd);
	}

	//유효값 확인
	if(eregi("[^a-zA-Z0-9_]", $auth_id)) {
		Error("입력값에 유효하지 않은 문자가 포함되어 있습니다.");
	}

	//접근가능 확인
	$query = "SELECT mem_ip_failed,mem_loginfailed,mem_faildate FROM ".$_table_id_members." WHERE mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."' AND mem_id='".$auth_id."';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		if(mysql_num_rows($result)) {
			$rs = mysql_fetch_array($result);
			$mem_ip_failed = $rs["mem_ip_failed"];
			$mem_loginfailed = $rs["mem_loginfailed"];
			$mem_faildate = $rs["mem_faildate"];

			if($mem_loginfailed>10 && (mktime()-$mem_faildate)<60*60) {
				Error("로그인을 10번이상 실패하여 한시간 동안 로그인이 금지 되었습니다.<br /><br />이후 부터는 한 실패할때마다 한시간씩 로그인이 제한 됩니다<br /><br />무작위 해킹공격을 방지하기 위함이므로 양해 바랍니다.<br /><br />다음 로그인 가능 시간까지 남은 시간 : ".intval((60*60-(mktime()-$mem_faildate))/60)."분");
			}
		}
		mysql_free_result($result);
	}

	//아이디 존재여부 확인
	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$auth_id}';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$numrows = mysql_num_rows($result);
		mysql_free_result($result);
	}
	if(!$numrows) {
		if($autoAuth) {
			ExpireCookie("amid");
			ExpireCookie("pswd");
		}

		//Error("존재하지 않는 아이디입니다.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("존재하지 않는 아이디입니다."));
	}
	else $numrows = 0;

	//회원 정보 확인
	$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$auth_id}' AND mem_passwd='{$auth_passwd}';";
	$result = mysql_query($query);
	if($result) {
		$numrows = mysql_num_rows($result);
		if(!$numrows) {
			if($autoAuth) {
				ExpireCookie("amid");
				ExpireCookie("pswd");
			}

			//로그인 실패 내용 기록
			$query = "UPDATE ".$_table_id_members." SET mem_ip_failed='".$_SERVER["REMOTE_ADDR"]."', mem_loginfailed=mem_loginfailed+1, mem_faildate=".mktime()." WHERE mem_id='{$auth_id}';";
			mysql_query($query) or Error(mysql_error());

			//Error("암호가 일치하지 않습니다.");
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("암호가 일치하지 않습니다."));
		}

		$rs = mysql_fetch_array($result);
		$mem_id = $rs["mem_id"];
		$mem_passwd = $rs["mem_passwd"];
		$mem_level = $rs["mem_level"];
		$mem_name = $rs["mem_name"];
		$mem_nickname = $rs["mem_nickname"];
		$mem_latestdate = $rs["mem_latestdate"];
		$mem_faildate = $rs["mem_faildate"];
		$mem_loginfailed = $rs["mem_loginfailed"];

		mysql_free_result($result);

	} else Error("로그인에 실패하였습니다.");

	if(empty($mem_id)||empty($mem_passwd)||empty($mem_level)) {
		Error("회원정보가 잘못되었습니다.");
	}
	else {
		if($auth_id==$mem_id && $auth_passwd==$mem_passwd && !empty($mem_level)) {
			if($auto_auth=="1") {
				//자동로그인 쿠키값 저장(한달)
				@setcookie("amid",$auth_id,mktime(0,0,0,date("m")+1,date("d"),date("Y")),"/");
				@setcookie("pswd",$auth_passwd,mktime(0,0,0,date("m")+1,date("d"),date("Y")),"/");
			}

			$_SESSION["MemId"] = $mem_id;
			$_SESSION["MemPasswd"] = $mem_passwd;
			$_SESSION["MemLevel"] = $mem_level;
			$_SESSION["MemName"] = $mem_nickname ? $mem_nickname : $mem_name;
			$_SESSION["Host"] = $_SERVER["HTTP_HOST"];

			$query = "UPDATE ".$_table_id_members." SET mem_latestdate=".mktime().", mem_ip_login='".$_SERVER["REMOTE_ADDR"]."', mem_loginnum=mem_loginnum+1, mem_loginfailed=0 WHERE mem_id='".$mem_id."';";
			mysql_query($query) or Error(mysql_error());

			$url = !$referer ? $_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}" : $referer;

			if($mem_loginfailed) {
				$msg = "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.alert('{$mem_id}님 정상적으로 로그인 되었습니다.\\n\\n".date("Y-m-d H:i:s",$mem_faildate)." 에 로그인을 {$mem_loginfailed} 번 실패하였습니다.');window.location.href='{$url}';\n//]]>\n</script>\n";
				echo !$combinedDesign ? "<html><head>{$msg}</head><body></body></html>" : $msg;
				exit;
			}
			else {
				MovePage($url);
				exit;
			}
		}
		else Error("회원 정보가 일치하지 않습니다.");
	}













} else if($mode == "logout") { //////////////////////////////////////////////////////////////////////logout
	
	//변수 소멸
	unset($MemId);
	unset($MemPasswd);
	unset($MemLevel);
	unset($MemName);

	//세션변수 비움
	$_SESSION["MemId"] = "";
	$_SESSION["MemPasswd"] = "";
	$_SESSION["MemLevel"] = "";
	$_SESSION["MemName"] = "";

	//자동로그인 쿠키 만료
	ExpireCookie("amid");
	ExpireCookie("pswd");

	//세션 파괴
	session_destroy();
	//session_unset();

	//외부지정 로그인 파일 포함
	//include $INC_PATH."logout.php";

	//페이지 복귀
	$url = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : $_SERVER["PHP_SELF"]."?".QryStr("list");
	MovePage($url);














} else if($mode == "multiview") { //////////////////////////////////////////////////////////////////////multiview

	if(!eregi($_SERVER["HTTP_HOST"],$_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다."); //외부입력 방지
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");
	if(!$view_level) Error("내용을 볼수 있는 권한이 없습니다.");

	$view_included = true;
	$chk = false;

	$query = "SELECT * FROM ".$_table_id_board." WHERE ";
	for($i=0; $i<$pageSize; $i++) {
		${"idx".$i} = $_POST["idx".$i];
		if(${"idx".$i}) {
			$query .= " idx=".${"idx".$i}." OR ";
			$chk = true; //넘어온 idx값이 있는지 체크
		}
	}
	$query = substr($query,0,strlen(trim($query))-2); //남는 OR문 제거
	$query .= " ORDER BY idx DESC;";

	if(!$chk) {
		//Error("게시물이 선택되지 않았습니다.<br />여러개의 내용을 한꺼번에 보시려면 체크박스에서 선택해주세요.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("게시물이 선택되지 않았습니다.\\n\\n여러개의 내용을 한꺼번에 보시려면 체크박스에서 선택해 주세요."));
	}
	else {
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			if(mysql_num_rows($result)) {
				ContentTop();
				include $INC_PATH."skin/".$skin."/view_multi.php"; //스킨 파일
				ContentBottom();
			}
		}
	}













} else if($mode == "multidelete") { //////////////////////////////////////////////////////////////////////multidelete

	$str = "";
	$chk = 0;
	for($i=0; $i<$pageSize; $i++) {
		${"idx".$i} = intval($_POST["idx".$i]);
		if(${"idx".$i}) {
			$str .= "&idx{$i}=".${"idx".$i};
			$chk++; //pageSize만큼 넘어온 idx값이 있는지 체크
		}
	}

	//넘어온 idx값이 한개도 없을 경우
	if(!$chk) {
		//Error("게시물이 선택되지 않았습니다.<br /><br />삭제하고자하는 게시물을 체크박스에서 선택해주세요.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("게시물이 선택되지 않았습니다.\\n\\n삭제하고자 하는 게시물을 체크박스에 선택해 주세요."));
	}

	$auth_passwd = $_POST["auth_passwd"];
	if(!$auth_passwd) { //인증 암호가 없으면 인증페이지로 이동
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=multidelete&page={$page}{$str}");
		exit;
	}
	else {
		if(!$MemId) $auth_passwd = md5($auth_passwd);
	}

	if($auth_passwd!=$adminPasswd && !$isAdmin) {
		Error("암호가 일치하지 않습니다.");
	}
	else {
		$chk = 0;
		$str = " WHERE (";
		for($i=0; $i<$pageSize; $i++) {
			${"idx".$i} = intval($_POST["idx".$i]);
			if(${"idx".$i}) {
				$str .= " idx=".${"idx".$i}." OR ";
				$chk++; //인증페이지를 거친후 넘어온 idx값이 있는지 체크
			}
		}
		$str = trim($str);
		$str = substr($str,0,strlen($str)-2); //남는 OR문 제거
		$str .= ")";

		if(!$chk) { Error("삭제할 게시물이 선택되지 않았습니다."); exit; }
		else {
			$query = "SELECT * FROM ".$_table_id_board." {$str}";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$chkNotice = 0;
				while($rs = mysql_fetch_array($result)) {
					for($i=1; $i<=$fileMaxNum; $i++) {
						${"fileName".$i} = $rs["fileName".$i]; //첨부파일 이름 가져옴
						if(${"fileName".$i}) { //첨부파일 삭제
							if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".$i})) {
								unlink("{$FSDATA_ROOT}/{$id}/".${"fileName".$i}); //첨부파일 삭제
							}
						}
					}
					if($rs["isNotice"]) $chkNotice++; //공지사항글인지 체크
				}
				mysql_free_result($result);
			}

			//관련 코멘트 모두 삭제
			$query = "DELETE FROM ".$_table_id_comment." ".str_replace("idx=","objNum=",$str)." AND boardId='{$id}';";
			mysql_query($query) or Error(mysql_error());

			//관련 트랙백 모두 삭제
			$query = "DELETE FROM ".$_table_id_trackback." ".str_replace("idx=","objNum=",$str)." AND boardId='{$id}';";
			mysql_query($query) or Error(mysql_error());

			//게시물 삭제
			$query = "DELETE FROM ".$_table_id_board." {$str};";
			mysql_query($query) or Error(mysql_error());

			//관리테이블의 게시물 갯수 감소
			$query = "UPDATE {$_table_id_admin} SET totalObj=totalObj-{$chk} ";
			if($chkNotice) $query .= ", noticeNum=noticeNum-{$chkNotice} "; //공지글이 포함되어 있을 경우 공지글 갯수 감소
			$query .= "WHERE boardId='{$id}' AND aidx={$aidx};";
			mysql_query($query) or Error(mysql_error());

			MovePage($_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}");
		}
	}













} else if($mode == "multimoveobjs") { //////////////////////////////////////////////////////////////////////moveobjs

	$str = "";
	$chk = 0;
	for($i=0; $i<$pageSize; $i++) {
		${"idx".$i} = intval($_POST["idx".$i]);
		if(${"idx".$i}) {
			$str .= "&idx{$i}=".${"idx".$i};
			$chk++; //pageSize만큼 넘어온 idx값이 있는지 체크
		}
	}

	//넘어온 idx값이 한개도 없을 경우
	if(!$chk) {
		//Error("게시물이 선택되지 않았습니다.<br /><br />이동하고자하는 게시물을 체크박스에서 선택해주세요.");
		MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("게시물이 선택되지 않았습니다.\\n\\n이동하고자 하는 게시물을 체크박스에 선택해 주세요."));
	}

	$auth_passwd = $_POST["auth_passwd"];
	if(!$auth_passwd) { //인증 암호가 없으면 인증페이지로 이동
		MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=multimoveobjs&page={$page}{$str}");
		exit;
	}
	else {
		if(!$MemId) $auth_passwd = md5($auth_passwd);
	}

	if($auth_passwd!=$adminPasswd && !$isAdmin) {
		Error("암호가 일치하지 않습니다.");
	}
	else {
		$targetBoardId = StrAddSlashes(trim($_POST["targetBoardId"]));

		if(!$targetBoardId) Error("이동할 대상게시판이 지정되지 않았습니다.");
		if($targetBoardId == $id) {
			//Error("같은 게시판으로는 이동할수 없습니다.");
			MovePage($_SERVER["PHP_SELF"]."?id={$id}&mode=error&msg=".urlencode("같은 게시판으로는 이동할수 없습니다."));
		}

		$chk = 0;
		$str = " WHERE ";
		for($i=0; $i<$pageSize; $i++) {
			${"idx".$i} = intval($_POST["idx".$i]);
			if(${"idx".$i}) {
				$str .= " idx=".${"idx".$i}." OR ";
				$chk++; //인증페이지를 거친후 넘어온 idx값이 있는지 체크
			}
		}
		$str = trim($str);
		$str = substr($str,0,strlen($str)-2); //남는 OR문 제거
		$str .= ";";

		if(!$chk) { Error("이동할 게시물이 선택되지 않았습니다."); exit; }
		else {
			$query = "SELECT * FROM ".$_table_id_board." {$str}";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$chkNotice = 0;
				while($rs = mysql_fetch_array($result)) {
					for($i=1; $i<=$fileMaxNum; $i++) {
						${"fileName".$i} = $rs["fileName".$i]; //첨부파일 이름 가져옴

						//첨부파일 이동
						if(${"fileName".$i}) {
							if(file_exists("{$FSDATA_ROOT}/{$id}/".${"fileName".$i})) {
								//이동대상 디렉토리 검사
								if(!is_dir($FSDATA_ROOT."/".$targetBoardId)) {
									@mkdir($FSDATA_ROOT."/".$targetBoardId,0777);
									@chmod($FSDATA_ROOT."/".$targetBoardId,0757);
								}

								//중복파일이 있는지 검사
								${"file_name".$i} = ${"fileName".$i};
								${"file_name".$i} = GetUniqueName(${"file_name".$i}, "{$FSDATA_ROOT}/{$targetBoardId}/");

								//파일 복사
								copy("{$FSDATA_ROOT}/{$id}/".${"fileName".$i}, ${"file_name".$i});

								//원래파일 삭제
								unlink("{$FSDATA_ROOT}/{$id}/".${"fileName".$i});
							}
						}
					}
					if($rs["isNotice"]) $chkNotice++; //공지사항글인지 체크
				}
				mysql_free_result($result);
			}

			$tableId_targetBoard = "_board_".$targetBoardId;

			for($i=$pageSize-1; $i>=0; $i--) {
				if(${"idx".$i}) {
					//게시물 복사
					$query = "INSERT INTO {$tableId_targetBoard} (SELECT
							(SELECT max(idx) FROM {$tableId_targetBoard})+1,
							objProperty,
							isNotice,
							isSecret,
							isMember,
							docType,
							author,
							e_mail,
							homeUrl,
							subject,
							passwd,
							category,
							tag_ls,
							regDate,
							editDate,
							memoLatestDate,
							memoNum,
							tbNum,
							readNum,
							voteNum,
							ipReg,
							ipEdit,
							usrAgentReg,
							usrAgentEdit,
							ref,
							reStep,
							reLevel,
							siteLink1,
							siteLink2,
							siteLinkCount1,
							siteLinkCount2,
							tbLink,";
					for($j=1; $j<=30; $j++) {
						$query .= "fileName{$j}, ";
					}
					for($j=1; $j<=30; $j++) {
						$query .= "fileDownload{$j}, ";
					}
					$query .= "contents FROM ".$_table_id_board." WHERE idx=".${"idx".$i}.");";
					mysql_query($query) or Error(mysql_error());

					//이동된 게시물의 고유번호를 구함
					$query = "SELECT idx FROM {$tableId_targetBoard} ORDER BY idx DESC LIMIT 1;";
					$result = mysql_query($query) or Error(mysql_error());
					if($result) {
						$rs = mysql_fetch_row($result);
						mysql_free_result($result);
						$new_idx = $rs[0];
					}

					//복사된 게시물에서 공지와 관련된 isNotice와 답글과 관련된 ref,reStep,reLevel 등을 기본상태로 지정
					$query = "UPDATE {$tableId_targetBoard} SET isNotice=0, ref={$new_idx}, reStep=0, reLevel=0 WHERE idx={$new_idx};";
					mysql_query($query) or Error(mysql_error());

					//댓글 이동
					$query = "UPDATE ".$_table_id_comment." SET boardId='{$targetBoardId}', objNum={$new_idx} WHERE boardId='{$id}' AND objNum=".${"idx".$i}.";";
					mysql_query($query) or Error(mysql_error());

					//트랙백 이동
					$query = "UPDATE ".$_table_id_trackback." SET boardId='{$targetBoardId}', objNum={$new_idx} WHERE boardId='{$id}' AND objNum=".${"idx".$i}.";";
					mysql_query($query) or Error(mysql_error());

					//원본게시물 삭제
					$query = "DELETE FROM ".$_table_id_board." WHERE idx=".${"idx".$i}.";";
					mysql_query($query) or Error(mysql_error());

				}
			}

			//원본 게시판 게시물 갯수 맞춤
			$query = "UPDATE {$_table_id_admin} SET totalObj=(SELECT count(*) FROM ".$_table_id_board.")";
			if($chkNotice) $query .= ", noticeNum=noticeNum-{$chkNotice} "; //공지글일경우 공지글 갯수 맞춤
			$query .= " WHERE boardId='{$id}' AND aidx={$aidx};";
			mysql_query($query) or Error(mysql_error());

			//이동된 게시판 게시물 갯수 맞춤
			$query = "UPDATE {$_table_id_admin} SET totalObj=(SELECT count(*) FROM {$tableId_targetBoard}) WHERE boardId='{$targetBoardId}';";
			mysql_query($query) or Error(mysql_error());

			//원래게시판으로 복귀
			MovePage($_SERVER["PHP_SELF"]."?".QryStr("list")."&page={$page}");
		}
	}












} else if($mode == "vote") { //////////////////////////////////////////////////////////////////////vote

	//추천 - 추후 작업












} else if($mode == "boardadmin") { //////////////////////////////////////////////////////////////////////admin

	if(!$isAdmin) { //관리자권한이 없을 경우
		$auth_id = StrAddSlashes(trim($_POST["auth_id"]));
		$auth_passwd = trim($_POST["auth_passwd"]);

		if(!$auth_id || !$auth_passwd) { //인증아이디와 암호가 없으면 인증페이지로 이동
			MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=admin&idx={$idx}&page={$page}");
		}
		else {
			$auth_passwd = md5($auth_passwd);
			
		}

		//유효값 확인
		if(eregi("[^a-zA-Z0-9_]", $auth_id)) {
			Error("입력값에 유효하지 않은 문자가 포함되어 있습니다.");
		}

		if(ereg($auth_id,$adminIDs) && $auth_passwd==$adminPasswd) { //관리자 정보 검사
			$isAdmin = true;
		}
		else {
			//회원테이블 검사
			$query = "SELECT idx,mem_id,mem_passwd,mem_level,mem_name FROM ".$_table_id_members." WHERE mem_id='{$auth_id}' AND mem_passwd='{$auth_passwd}';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				if($numrows<1) Error("관리자 정보가 일치하지 않습니다.");

				$rs = mysql_fetch_array($result);
				$mem_idx = $rs["mem_idx"];
				$mem_id = $rs["mem_id"];
				$mem_passwd = $rs["mem_passwd"];
				$mem_level = $rs["mem_level"];
				$mem_name = $rs["mem_name"];
				mysql_free_result($result);

				if(!empty($mem_id) && !empty($mem_passwd) && !empty($mem_level) && $auth_id==$mem_id && $auth_passwd==$mem_passwd) {
					//관리레벨 확인 레벨1,2,3
					if($mem_level>3) Error("관리자 권한이 없습니다.");

					//회원 로그인 처리
					$_SESSION["MemId"] = $mem_id;
					$_SESSION["MemPasswd"] = $mem_passwd;
					$_SESSION["MemLevel"] = $mem_level;
					$_SESSION["MemName"] = $mem_name;
					$isAdmin = true;
				}
			} else Error("Auth Failed.");
		}
	}

	$admin_included = true;
	include $INC_PATH."lib/_admin.php";




} else if($mode == "admin") { //////////////////////////////////////////////////////////////////////admin
	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	if(!$isAdmin) { //관리자권한이 없을 경우
		$auth_id = StrAddSlashes(trim($_POST["auth_id"]));
		$auth_passwd = trim($_POST["auth_passwd"]);

		if(!$auth_id || !$auth_passwd) { //인증아이디와 암호가 없으면 인증페이지로 이동
			MovePage($_SERVER["PHP_SELF"]."?".QryStr("authenticate")."&nav=admin&idx={$idx}&page={$page}");
		}
		else {
			$auth_passwd = md5($auth_passwd);
			
		}

		//유효값 확인
		if(eregi("[^a-zA-Z0-9_]", $auth_id)) {
			Error("입력값에 유효하지 않은 문자가 포함되어 있습니다.");
		}

		if(ereg($auth_id,$adminIDs) && $auth_passwd==$adminPasswd) { //관리자 정보 검사
			$isAdmin = true;
		}
		else {
			//회원테이블 검사
			$query = "SELECT idx,mem_id,mem_passwd,mem_level,mem_name FROM ".$_table_id_members." WHERE mem_id='{$auth_id}' AND mem_passwd='{$auth_passwd}';";
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
				if($numrows<1) Error("관리자 정보가 일치하지 않습니다.");

				$rs = mysql_fetch_array($result);
				$mem_idx = $rs["mem_idx"];
				$mem_id = $rs["mem_id"];
				$mem_passwd = $rs["mem_passwd"];
				$mem_level = $rs["mem_level"];
				$mem_name = $rs["mem_name"];
				mysql_free_result($result);

				if(!empty($mem_id) && !empty($mem_passwd) && !empty($mem_level) && $auth_id==$mem_id && $auth_passwd==$mem_passwd) {
					//관리레벨 확인 레벨1,2,3
					if($mem_level>3) Error("관리자 권한이 없습니다.");

					//회원 로그인 처리
					$_SESSION["MemId"] = $mem_id;
					$_SESSION["MemPasswd"] = $mem_passwd;
					$_SESSION["MemLevel"] = $mem_level;
					$_SESSION["MemName"] = $mem_name;
					$isAdmin = true;
				}
			} else Error("Auth Failed.");
		}
	}

	$admin_included = true;
	include "lib/_admin.php";




	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";



} else if($mode == "adminsave") { //////////////////////////////////////////////////////////////////////adminsave

	$admin_included = true;
	include $INC_PATH."lib/_admin.php";










} else if($mode == "setup") { //////////////////////////////////////////////////////////////////////setup

	MovePage("{$FSBOARD_PATH}/lib/setup.php?id={$id}&mode=List");











} else if($mode == "sync") { //////////////////////////////////////////////////////////////////////sync

	if(!$isAdmin) MovePage($_SERVER["PHP_SELF"]."?id=".$id."&mode=login");

	$query = "SELECT count(*) FROM ".$_table_id_board.";";
	$result = mysql_query($query) or Error(mysql_error());
	$rs = mysql_fetch_row($result);
	mysql_free_result($result);

	if($rs[0]) {
		$query = "UPDATE {$_table_id_admin} SET totalObj={$rs[0]} WHERE aidx={$aidx};";
		mysql_query($query) or Error(mysql_error());
	}

	$query = "SELECT count(*) FROM ".$_table_id_board." WHERE isNotice=1;";
	$result = mysql_query($query) or Error(mysql_error());
	$rs = mysql_fetch_row($result);
	mysql_free_result($result);

	if($rs[0]) {
		$query = "UPDATE {$_table_id_admin} SET noticeNum={$rs[0]} WHERE aidx={$aidx};";
		mysql_query($query) or Error(mysql_error());
	}

	$url = $_SERVER["HTTP_REFERER"] ? $_SERVER["HTTP_REFERER"] : $_SERVER["PHP_SELF"]."?".QryStr("admin");
	MovePage($url);
	exit;










} else { echo "Invalid mode"; exit; } //if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }

if($topcontent_included) ContentBottom();
?>
