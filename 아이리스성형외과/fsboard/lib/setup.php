<?
/*************************************************************

	FSBOARD Setup Manager 1.0

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 11, 2007

	Copyright(c)2000-2007 FSBOARD. All rights reserved.

*************************************************************/


/**************************************************
 Setup 초기화
**************************************************/

	//라이브러리 포함
	include_once $INC_PATH."lib.php";

	//변수 초기화
	$MODE = trim($_GET["mode"]);
	$EXEC = trim($_GET["exec"]);

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("setup.php",$_SERVER["PHP_SELF"])) ? true : false;

	//DB연결
	if($MODE) DbConn();




/**************************************************
 전용 함수
**************************************************/

	//관리자상단 기본내용
	function setup_head() {
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n"; //XHTML 1.0 표준
		echo "<html>\n<head>\n<title>Free Style Board</title>\n";
		echo "<style type=\"text/css\">\nimg { border:0; }
			img { border:0; }
			.defstyle { font-size:12px; font-family:돋움, Verdana; }
			a.lnk_def:link, a.lnk_def:visited { color:#000; text-decoration:none; }
			a.lnk_def:hover, a.lnk_def:active { color:#00f; text-decoration:underline; }
			</style>\n";
		echo "\n</head>\n<body>\n";
	}

	//관리자하단 기본내용
	function setup_foot() {
		echo "\n</body>\n</html>";
	}




/**************************************************
 모드별 처리
**************************************************/

if(!$MODE || $MODE == "") { //////////////////////////////////////////////////////////////////////도움말

	//디렉토리 검사
	if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH)) $msg1 = "※ 게시판이 설치된 디렉토리 이름이 잘못되었습니다. 게시판이 설치된 디렉토리를 확인해 주세요.<br /><br />";
	if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH."/data")) $msg2 = "※ 첨부파일업로드 디렉토리 이름이 잘못되었습니다. 첨부파일업로드 디렉토리를 확인해 주세요.<br /><br />";

	//첨부파일디렉토리 권한 체크
	$perms = fileperms($_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH."/data"); // 707:16839, 757:16879, 777:16895
	if($perms!=16839 && $perms!=16879 && $perms!=16895) $msg3 = "※ 첨부파일업로드 디렉토리에 쓰기권한이 없습니다. Telnet이나 FTP에서 권한을 조정해 주세요.<br /><br />";

	//라이브러리디렉토리 권한 체크
	$perms = fileperms($FSLIB_PATH);
	if($perms!=16839 && $perms!=16879 && $perms!=16895) $msg4 = "※ 라이브러리 디렉토리에 쓰기권한이 없습니다. Telnet이나 FTP에서 권한을 조정해 주세요.<br /><br />";

	/////포스팅되었을 경우
	if($_SERVER["REQUEST_METHOD"]=="POST" && !file_exists("{$FSLIB_PATH}/dbcon.php")) {
		if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");

		$virginity = trim($_GET["virginity"]);

		$db_rootpasswd = trim($_POST["db_rootpasswd"]);
		$db_hostname = trim($_POST["db_hostname"]);
		$db_name = trim($_POST["db_name"]);
		$db_loginid = trim($_POST["db_loginid"]);
		$db_loginpasswd = trim($_POST["db_loginpasswd"]);
		$db_loginpasswd2 = trim($_POST["db_loginpasswd2"]);
		$xhost = intval($_POST["xhost"]);

		/////MySQL root 암호를 이용한 자동생성일 경우
		if($virginity == "pure" && !file_exists("{$FSLIB_PATH}/dbcon.php")) {
			if(!$db_rootpasswd) Error("MySQL root 암호를 입력해 주세요.");
			if(!$db_hostname) Error("생성할 DB 호스트 주소를 입력해 주세요.");
			if(!$db_name) Error("생성할 DB 이름을 입력해 주세요.");
			if(!$db_loginid) Error("생성할 DB 아이디를 입력해 주세요.");
			if(!$db_loginpasswd) Error("생성할 DB 암호를 입력해 주세요.");
			if(!$db_loginpasswd2) Error("암호 확인을 입력해 주세요.");

			if($db_loginpasswd != $db_loginpasswd2) Error("암호와 암호확인이 일치하지 않습니다.");

			//MySQL root 계정으로 DB연결
			if(!$dbConnect) $dbConnect = @mysql_connect($db_hostname,"root",$db_rootpasswd) or Error("DB 연결중 에러가 발생했습니다");

			//데이터베이스 생성
			$query = "CREATE DATABASE {$db_name};";
			mysql_query($query,$dbConnect) or Error(mysql_error());
			mysql_select_db($db_name, $dbConnect) or Error(mysql_error());

			//퀴리문에 사용할 변수 전처리
			$db_hostname = StrAddSlashes($db_hostname);
			$db_name = StrAddSlashes($db_name);
			$db_loginid = StrAddSlashes($db_loginid);
			$db_loginpasswd = StrAddSlashes($db_loginpasswd);

			//MySQL 계정 생성
			$query = "GRANT ALL ON {$db_name}.* TO {$db_loginid}@{$db_hostname} IDENTIFIED BY '{$db_loginpasswd}';";
			@mysql_query($query,$dbConnect) or Error("DB 계정생성에 실패하였습니다.");

			//MySQL 외부접속 가능하도록 설정
			if($xhost === 1) {
				$query = "GRANT ALL PRIVILEGES ON {$db_name}.* TO {$db_loginid}@'%' IDENTIFIED BY '{$db_loginpasswd}';";
				@mysql_query($query,$dbConnect) or Error("MySQL 외부접속 설정 실패");
			}

			//MySQL 계정 적용
			$query = "FLUSH PRIVILEGES;";
			@mysql_query($query,$dbConnect) or Error("MySQL 플러시 실패");

			/////dbcon.php 파일 생성
			$file = @fopen("dbcon.php","w") or Error("dbcon.php 파일을 생성할수 없습니다.<br /><br />라이브러리 디렉토리의 퍼미션을 조정해 주세요");
			@fwrite($file,"<"."?\n{$db_hostname}\n{$db_loginid}\n{$db_loginpasswd}\n{$db_name}\n?".">\n") or Error("dbcon.php 파일을 생성할수 없습니다.<br /><br />디렉토리의 퍼미션을 조정해 주세요.");
			@fclose($file);
			@chmod("dbcon.php",0646);

		}
		/////수동으로 미리 DB 및 계정을 생성한 경우
		else {
			if(!$db_hostname) Error("생성할 DB 호스트 주소를 입력해 주세요.");
			if(!$db_name) Error("DB 이름을 입력해 주세요.");
			if(!$db_loginid) Error("DB 아이디를 입력해 주세요.");
			if(!$db_loginpasswd) Error("DB 암호를 입력해 주세요.");

			//DB연결 테스트
			if(!$dbConnect) $dbConnect = @mysql_connect($db_hostname,$db_loginid,$db_loginpasswd) or Error("DB 연결중 에러가 발생했습니다.<br /><br />DB접속 아이디/암호 등을 정확히 입력했는지 확인해 주세요.");

			/////dbcon.php 파일 생성
			$file = @fopen("dbcon.php","w") or Error("dbcon.php 파일을 생성할수 없습니다.<br /><br />라이브러리 디렉토리의 퍼미션을 조정해 주세요");
			@fwrite($file,"<?\n{$db_hostname}\n{$db_loginid}\n{$db_loginpasswd}\n{$db_name}\n?>\n") or Error("dbcon.php 파일을 생성할수 없습니다.<br /><br />디렉토리의 퍼미션을 조정해 주세요.");
			@fclose($file);
			@chmod("dbcon.php",0646);
		}

		//관리자계정 생성페이지로 이동
		MovePage("{$PHP_SELF}?mode=CreateAdmin");
		exit();
	}

	setup_head();

	/////도움말 구성
	echo "
		<table width=\"95%\" align=\"center\" cellpadding=\"5\" cellspacing=\"0\" style=\"font-size:9pt;\">
			<tr>
				<td align=\"center\">
					<b style=\"font-family:Times New Roman;font-size:15px;\">FSBOARD 1.1.0 (PHP/MySQL)</b>
				</td>
			</tr>
			<tr>
				<td>
					<table width=\"100%\" border=\"1\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;font-family:돋움;\" cellpadding=\"10\" cellspacing=\"0\">
						<tr>
							<td>

								<table width=\"100%\" style=\"line-height:120%;font-family:Verdana, Dotum;\">
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 프롤로그 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											- 만든사람 : 조정현 &lt;<a href=\"mailto:sariu@msn.com\">saiur@msn.com</a>&gt; &nbsp; (주)발해 <a href=\"http://www.ubalhae.com/\" onclick=\"window.open(this.href,'_blank'); return false;\">www.ubalhae.com</a><br />
											- 제작일자 : October 7, 2006<br />
											- 마지막 수정일자 : December 12, 2006<br />

											- Copyright 저작권 표시를 가리거나 수정 또는 삭제하지 않는 전제하에 개인 또는 상업사이트 및 기업등에서 자유로이 사용할수 있습니다.<br />
											- 이 프로그램의 소스를 어떠한 형태로든 수정,가공,변형해서 사용이 가능하나 소스 수정 후 재배포는 금지합니다.(스킨 제외)<br />

										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 특성 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											- ASP로 제작 배포한 fsboard2를 PHP버전으로 컨버전 및 업그레이드 형태로 제작함<br />
											- 테마별 스킨 기능 (게시판, 자료실, 방명록, 갤러리, 기타 등등 기능별 스킨 제작 가능)<br />
											- 주 엔진 파일에서의 논리연산을 통한 실행으로 파일수 최소화<br />
											- 관리자 환경설정 모드에서 설치된 게시판별로 다양한 각종 환경설정 셋팅 가능 (모양, 권한, 기본문구 등)<br />
											- 디자인페이지와 분리된 코드 삽입 가능<br />
											- 관리자 암호로 로그인 없이 관리 가능<br />
											- 보안 및 에러처리<br />
											- 페이지별 세부 기능<br />
											- Mozilla/FireFox/Opera/Netscape 지원<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 설치 및 설정 방법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 설치전 서버환경을 체크합니다.<br />
											&nbsp; - <b>GNU/Linux</b>, 또는 <b>Microsoft WindowsServer</b><br />
											&nbsp; - <b>Apache 2.0</b> 이상, 또는 <b>IIS 5.0</b> 이상<br />
											&nbsp; - <b>PHP 4.4</b> 이상<br />
											&nbsp; - <b>MySQL 4.1</b> 이상 <span style=\"color:red;\">(반드시 <b>4.1</b>이상이어야 하고 문자셋은 <b>euckr</b>이어야 합니다.)</span><br />
											<br />
											&nbsp; - <b>개발환경</b><br />
											&nbsp; &nbsp; Linux 2.4.21-27.ELsmp #1 SMP Wed Dec 1 21:59:02 EST 2004 i686 i686 i386 GNU/Linux<br />
											&nbsp; &nbsp; Apache/2.0.55 (Unix) DAV/2 PHP/4.4.1 mod_jk2/2.0.4<br />
											&nbsp; &nbsp; PHP 4.4.1 (cli) (built: Jul 22 2006 13:40:24) Zend Engine v1.3.0<br />
											&nbsp; &nbsp; MySQL Ver 14.7 Distrib 4.1.15, for pc-linux-gnu (i686) using readline 4.3<br />
											<br />
											&nbsp; - <b>테스트 환경</b><br />
											&nbsp; &nbsp; Microsoft Windows XP Professional SP2<br />
											&nbsp; &nbsp; Microsoft Internet Explorer 6.0 SP1<br />
											&nbsp; &nbsp; Microsoft Internet Explorer 7.0<br />
											<br />
											&nbsp; - <b>설정 사항</b><br />
											&nbsp; &nbsp; php.ini 파일에서 session.auto_start = 0 으로 되어 있어야 세션 사용에 문제가 없습니다..<br />
											&nbsp; &nbsp; php.ini 파일에서 magic_quote_gpc = Off 으로 되어 있어야 문자열이 불필요하게 이스케이프 되는 현상이 없습니다.<br />
											&nbsp; &nbsp; MySQL은 서브쿼리문을 사용하였으므로 반드시 4.1 이상을 사용해야 합니다.<br />
											<br />
											&nbsp; <span style=\"color:red\">※ 위의 개발환경 및 테스트환경 이하 버전의 환경에서는 테스트하지 않았으므로 제대로 동작하지 않을수 있습니다.</span><br />
											<br />
											<br />
											2. fsboard.tar.gz 파일을 사이트디렉토리에 업로드한 후 압축을 해제합니다.<br />
											&nbsp; &nbsp; 또는 압축을 해제한후 사이트디렉토리에 FTP전송으로 업로드 합니다.<br />
											<br />
											<br />
											3. 게시판이 업로드된 디렉토리에서 <b style=\"color:red;\">lib/</b> 디렉토리와 <b style=\"color:red;\">data/</b> 디렉토리의 권한을 반드시 <b style=\"color:red;\">707</b> 또는 <b style=\"color:red;\">757</b> 또는 <b style=\"color:red;\">777</b> 로 변경합니다.<br />
											&nbsp; &nbsp; <span style=\"color:red\">디렉토리의 권한 설정을 변경하지 않으면 설치를 계속 진행할 수 없게 됩니다.</span><br />
											<br />
											<br />
											4. 브라우저 주소창에 <b><u>http://계정/fsboard/index.php</u></b> 를 입력합니다.<br />
											<br />
											<br />
											5. 도움말 페이지가 나타나면 <b>맨아래쪽</b>의 입력란에 해당 내용을 입력합니다.<br />
											&nbsp; &nbsp; <span style=\"color:red;\">DB 셋팅 과정은 편의상 <b>두가지 방법</b>이 제공되며 해당되는 부분에만 입력합니다.</span><br />
											<br />
											&nbsp; - <b>MySQL의 root 계정의 암호를 알고 있는 경우</b><br />
											&nbsp; &nbsp; &nbsp; 게시판에 필요한 DB 생성 및 설정이 자동으로 이루어집니다.<br />
											&nbsp; &nbsp; &nbsp; 해당 내용들을 정확히 입력한 후 확인을 클릭합니다.<br />
											<br />
											&nbsp; - <b>수동으로 DB 및 DB 계정을 미리 생성한 경우</b><br />
											&nbsp; &nbsp; &nbsp; DB 및 DB접근 계정을 MySQL에 이미 먼저 생성했을 경우에 해당합니다.<br />
											&nbsp; &nbsp; &nbsp; MySQL root계정의 접근이 불가능할때 서버에 미리 DB 및 DB계정을 생성한 후 해당내용을 입력합니다.<br />
											&nbsp; &nbsp; &nbsp; 입력하기전에 반드시 계정이 먼저 생성되어 있어야하며 입력정보가 생성된 정보와 일치해야 합니다.<br />
											<br />
											<br />
											6. 위의 과정에서 이상이 없으면 관리자 생성 페이지로 이동됩니다.<br />
											&nbsp; &nbsp; 관리자의 기본정보를 입력합니다. 관리자의 아이디와 암호는 알아채기 어려운 내용으로 입력하세요.<br />
											&nbsp; &nbsp; 여기서 관리자는 <b>웹에서 게시판을 관리하는 계정</b>입니다.<br />
											&nbsp; &nbsp; 확인을 클릭하면 새 게시판 생성 페이지로 이동됩니다.<br />
											<br />
											<br />
											7. 새 게시판 생성 폼으로 이동되면 새로 만들 게시판의 정보를 입력합니다.<br />
											&nbsp; &nbsp; 빨간색으로 체크된 부분을 주의 깊게 확인하고 확인을 클릭합니다.<br />
											<br />
											<br />
											8. 이상이 없을 경우 새로 생성된 게시판으로 이동되며 게시판의 주소는<br />
											&nbsp; &nbsp; <u>http://계정/fsboard/index.php?id=게시판아이디</u> 와 같은 링크로 사용할수 있게 됩니다.<br />
											<br />
											<br />
											9. 생성된 게시판 목록에서 왼쪽 윗부분의 <b>Admin</b> 을 클릭하여 게시판의 환경설정을 수정합니다.<br />
											<br />
											<br />
											10. 게시판을 추가/관리 하려면 게시판 목록 왼쪽 윗부분의 <b>Setup</b> 을 클릭합니다.<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 권한설정 방법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 사용권한은 레벨 1부터 지정한 n개까지로 구분되며 <b><u>레벨숫자가 작을수록 높은 권한</u></b>을 갖게 됩니다.<br />
											<br />
											2. 레벨 1은 관리자 레벨로서 게시판설정이나 회원정보를 보거나 설정할수 있습니다.<br />
											<br />
											3. 레벨 2부터 n까지는 가입후 인증된 회원의 레벨로서 관리자가 인증한 이후에 관리자가 정한 권한을 사용할수 있는 레벨입니다.<br />
											<br />
											4. 레벨 n은 모든 방문자에게 해당하는 임시 레벨입니다.<br />
											<br />
											5. 사용권한별 레벨 사용예<br />
											&nbsp; &nbsp; - 모든 방문자에게 글쓰기 권한을 줄 경우: 글쓰기권한 레벨 -> n으로 설정<br />
											&nbsp; &nbsp; - 가입한 회원에게만 글쓰기 권한을 줄 경우: 글쓰기권한 레벨 -> n-1로 설정<br />
											&nbsp; &nbsp; - 관리자만 글쓰기가 가능한 공지게시판일 경우: 글쓰기권한 레벨 -> 1로 설정<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 게시판을 디자인파일에 포함시켜 실행하는 방법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 게시판이 설치된 디렉토리 이외의 외부디렉토리 파일에서 게시판을 포함해서 실행할수 있습니다.<br />
											&nbsp; &nbsp; <span style=\"color:green\">이 방법을 사용할 경우 디자인과 게시판의 완전 분리가 가능하며 유지보수가 수월한 장점이 있습니다.</span><br />
											<br />
											<br />
											2. 포함하고자 하는 외부 파일에 다음과 같은 간단한 소스를 추가합니다.(아래의 내용은 예제입니다)<br />
											&nbsp; &nbsp; 설치된 게시판의 링크 주소가 <u>http://127.0.0.1/fsboard/index.php?id=<b style=\"background-color:yellow\">test</b></u> 이며,<br />
											&nbsp; &nbsp; <u>http://127.0.0.1/html/<span style=\"background-color:cyan\">test1.php</span></u> 에 포함하고자 할 경우<br />
											&nbsp; &nbsp; http://127.0.0.1/html/<u><span style=\"background-color:cyan\">test1.php</span></u> 파일을 열어 디자인과 어울리는 적당한 위치에 소스를 추가 합니다.<br />
											&nbsp; &nbsp; 디자인된 <span style=\"background-color:cyan\">test1.php</span> 파일의 내용이 아래와 같을 경우,<br />
											<br />
											<table>
												<tr>
													<td width=\"10\"></td>
													<td style=\"padding:20px;border:1px solid silver;font-family:Verdana;font-size:11px;\">
														<span style=\"color:blue\">&lt;html><br />
														&lt;head><br />
														&lt;title><span style=\"color:black\">test</span>&lt;/title><br />
														&lt;/head><br />
														&lt;body><br />
														&lt;table width=\"600\" border=\"1\"><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><br />
														&nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color:black\">테스트 게시판</span><br />
														&nbsp; &nbsp; &lt;/td><br />
														&nbsp; &lt;/tr><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><br />
														&nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color:green\">&lt!-- 게시판이 들어갈 자리 --&gt;</span><br />
														&nbsp; &nbsp; &lt;/td><br />
														&nbsp; &lt;/tr><br />
														&lt;/table><br />
														&lt;/body><br />
														&lt;/html></span><br />
													</td>
												</tr>
											</table>
											<br />
											&nbsp; &nbsp; 위의 소스에서 아래와 같은 구문을 추가합니다.<br />
											<br />
											<table>
												<tr>
													<td width=\"10\"></td>
													<td style=\"padding:20px;border:1px solid silver;font-family:Verdana;font-size:11px;\">
														<span style=\"color:maroon\">&lt?</span> <span style=\"color:red\">ob_start</span><span style=\"color:maroon\">();</span> <span style=\"color:maroon\">?&gt;</span><br />
														<span style=\"color:blue\">&lt;html><br />
														&lt;head><br />
														&lt;title><span style=\"color:black\">test</span>&lt;/title><br />
														&lt;/head><br />
														&lt;body><br />
														&lt;table width=\"600\" border=\"1\"><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><br />
														&nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color:black\">테스트 게시판</span><br />
														&nbsp; &nbsp; &lt;/td><br />
														&nbsp; &lt;/tr><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><br />
														&nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color:green\">&lt!-- 게시판이 들어갈 자리 --&gt;</span><br />
														&nbsp; &nbsp; &nbsp; &nbsp; <span style=\"color:maroon\">&lt;? <span style=\"color:blue\">if</span>(!<span style=\"color:blue\">\$</span><span style=\"color:mediumseagreen\">_GET</span>[<span style=\"color:magenta\">\"id\"</span>]) { <span style=\"color:red\">header</span>(<span style=\"color:magenta\">\"location:\$PHP_SELF?id=<b style=\"color:black;background-color:yellow\">test</b>\"</span>);<span style=\"color:red\">exit</span>; } <span style=\"color:blue\">else</span> { <span style=\"color:blue\">include</span> <span style=\"color:magenta\">\"../fsboard/index.php\"</span>;<span style=\"color:red\">ob_end_flush</span>(); ?&gt;</span><br />
														&nbsp; &nbsp; &lt;/td><br />
														&nbsp; &lt;/tr><br />
														&lt;/table><br />
														&lt;/body><br />
														&lt;/html></span><br />
													</td>
												</tr>
											</table>
											<br />
											&nbsp; &nbsp; 위와 같이 작성하면 브라우저에서 <u>http://127.0.0.1/html/test1.php</u> 파일만 실행해도<br />
											&nbsp; &nbsp; <u>http://127.0.0.1/fsboard/index.php?id=test</u> 의 내용이 같이 포함되어 나타납니다.<br />
											<br />
											&nbsp; &nbsp <span style=\"color:red\">※ 게시판이 포함되는 페이지의 최상단에 반드시 <b>&lt? ob_start(); ?&gt;</b> 가 추가 되어야 합니다.</span>
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 최근게시물 적용 방법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 첫페이지에 게시물의 최근글들을 보여지게 하려 할 경우 함수 호출을 통해 간편하게 다룰수 있습니다.<br />
											index.php 파일에 필요한 라이브러리 파일을 포함시키고, 적절한 위치에 함수를 호출하면 됩니다.<br />
											<br />
											<br />
											2. <b>lib/</b> 디렉토리의 <b>lib.php</b> 와 <b>latest.php</b> 파일들을 최근게시물이 나타나게할 페이지 상단에 추가하고,<br />
											&nbsp; &nbsp; 최근게시물이 나타나게할 위치에 LatestList()함수를 호출합니다.<br />
											<br />
											&nbsp; - LatestList()함수 설명<br />
											&nbsp; &nbsp; 형식: LatestList( [게시판ID], [목록수], [제목글자제한수], [링크파일명] )<br />
											&nbsp; &nbsp; [게시판ID] - 불러올 게시판 아이디<br />
											&nbsp; &nbsp; [목록수] - 최근 게시물 리스트의 갯수<br />
											&nbsp; &nbsp; [제목글자제한수] - 제목의 최대길이 지정<br />
											&nbsp; &nbsp; [링크파일명] - 외부파일에 포함할 경우 외부파일명. 외부파일에 포함 안할경우 빈따옴표만 입력해도 무관<br />
											<br />
											&nbsp; - (예) http://127.0.0.1/index.php 파일에<br />
											&nbsp; &nbsp; http://127.0.0.1/fsboard/index.php?id=<span style=\"background-color:yellow\">test1</span><br />
											&nbsp; &nbsp; http://127.0.0.1/fsboard/index.php?id=<span style=\"background-color:yellow\">test2</span><br />
											&nbsp; &nbsp; http://127.0.0.1/fsboard/index.php?id=<span style=\"background-color:yellow\">test3</span><br />
											&nbsp; &nbsp; http://127.0.0.1/fsboard/index.php?id=<span style=\"background-color:yellow\">test4</span><br />
											<br />
											&nbsp; &nbsp; 설치된 게시판들이 위와 같을때 각 게시판들의 최근 게시물들을<br />
											&nbsp; &nbsp; 각각 <span style=\"background-color:chartreuse\">5줄</span>씩 긴제목은 <span style=\"background-color:peachpuff\">15글자</span>에서 자르고자 할 경우<br />
											&nbsp; &nbsp; index.php 파일의 내용의 예는 다음과 같습니다.<br />
											<br />
											<table>
												<tr>
													<td width=\"10\"></td>
													<td style=\"padding:20px;border:1px solid silver;font-family:Verdana;font-size:11px;\">
														<span style=\"color:blue\"><span style=\"color:maroon\">&lt;?<br />
														ob_start();<br />
														include \"fsboard/lib/lib.php\";<br />
														include \"fsboard/lib/latest.php\";<br />
														?&gt;</span><br />
														&lt;html><br />
														&lt;head><br />
														&lt;title>test&lt;/title><br />
														&lt;/head><br />
														&lt;body><br />
														&lt;table width=\"800\" border=\"1\"><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test1\"</span>, <span style=\"background-color:chartreuse\">5</span>, <span style=\"background-color:peachpuff\">15</span>, \"\") ?&gt;</span>&lt;/td><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test2\"</span>, <span style=\"background-color:chartreuse\">5</span>, <span style=\"background-color:peachpuff\">15</span>, \"\") ?&gt;</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test3\"</span>, <span style=\"background-color:chartreuse\">5</span>, <span style=\"background-color:peachpuff\">15</span>, \"\") ?&gt;</span>&lt;/td><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test4\"</span>, <span style=\"background-color:chartreuse\">5</span>, <span style=\"background-color:peachpuff\">15</span>, \"\") ?&gt;</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&lt;/table><br />
														&lt;/body><br />
														&lt;/html><br />
														<span style=\"color:maroon\">&lt;?<br />
														ob_end_flush();<br />
														if(\$dbConnect) { @mysql_close(\$dbConnect); }<br />
														?&gt;</span><br /></span>
													</td>
												</tr>
											</table>
											<br />
											&nbsp; &nbsp; 이 게시판들이 각각 아래의 파일들에 포함되어 실행될 경우<br />
											<br />
											&nbsp; &nbsp; http://127.0.0.1<span style=\"background-color:cyan\">/html/test1.php</span><br />
											&nbsp; &nbsp; http://127.0.0.1<span style=\"background-color:cyan\">/html/test2.php</span><br />
											&nbsp; &nbsp; http://127.0.0.1<span style=\"background-color:cyan\">/html/test3.php</span><br />
											&nbsp; &nbsp; http://127.0.0.1<span style=\"background-color:cyan\">/html/test4.php</span><br />
											<br />
											<table>
												<tr>
													<td width=\"10\"></td>
													<td style=\"padding:20px;border:1px solid silver;font-family:Verdana;font-size:11px;\">
														<span style=\"color:blue\"><span style=\"color:maroon\">&lt;?<br />
														ob_start();<br />
														include \"fsboard/lib/lib.php\";<br />
														include \"fsboard/lib/latest.php\";<br />
														?&gt;</span><br />
														&lt;html><br />
														&lt;head><br />
														&lt;title>test&lt;/title><br />
														&lt;/head><br />
														&lt;body><br />
														&lt;table width=\"800\" border=\"1\"><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test1\"</span>, 5, 15, <span style=\"background-color:cyan\">\"/html/test1.php\"</span>) ?&gt;</span>&lt;/td><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test2\"</span>, 5, 15, <span style=\"background-color:cyan\">\"/html/test2.php\"</span>) ?&gt;</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test3\"</span>, 5, 15, <span style=\"background-color:cyan\">\"/html/test3.php\"</span>) ?&gt;</span>&lt;/td><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;?= LatestList(<span style=\"background-color:yellow\">\"test4\"</span>, 5, 15, <span style=\"background-color:cyan\">\"/html/test4.php\"</span>) ?&gt;</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&lt;/table><br />
														&lt;/body><br />
														&lt;/html><br />
														<span style=\"color:maroon\">&lt;?<br />
														ob_end_flush();<br />
														if(\$dbConnect) { @mysql_close(\$dbConnect); }<br />
														?&gt;</span><br /></span>
													</td>
												</tr>
											</table>
											<br />
											&nbsp; - 디자인을 수정하려면 LatestList()함수의 내용을 변형하면 됩니다. 이미지 파일을 사용할 경우 절대경로로 저정하세요.<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 외부로그인 적용 방법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 페이지 상단에 lib/ 디렉토리의 lib.php 파일을 포함시킵니다.<br />
											<br />
											2. 외부로그인이 나타나게 하려는 부분에 lib/ 디렉토리의 outlogin.php 파일을 포함시킵니다.<br />
											<br />
											<table>
												<tr>
													<td width=\"10\"></td>
													<td style=\"padding:20px;border:1px solid silver;font-family:Verdana;font-size:11px;\">
														<span style=\"color:blue\"><span style=\"color:maroon\">&lt;?<br />
														ob_start();<br />
														include \"fsboard/lib/lib.php\";<br />
														?&gt;</span><br />
														&lt;html><br />
														&lt;head><br />
														&lt;title><span style=\"color:black\">test</span>&lt;/title><br />
														&lt;/head><br />
														&lt;body><br />
														&lt;table width=\"600\" border=\"1\"><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:black\">로 그 인</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&nbsp; &lt;tr><br />
														&nbsp; &nbsp; &lt;td><span style=\"color:maroon\">&lt;? include \"fsboard/lib/outlogin.php\"; ?&gt;</span>&lt;/td><br />
														&nbsp; &lt;/tr><br />
														&lt;/table><br />
														&lt;/body><br />
														&lt;/html></span><br />
													</td>
												</tr>
											</table>
											<br />
											3. 디자인을 수정하려면 outlogin.php 파일의 내용에서 로그인하기전 부분과 로그인 이후 부분을 수정합니다.<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 스킨 제작법 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 스킨을 별도로 제작하기에는 소스가 다소 복잡하지만 스킨수정만으로 게시판형태 이외에 원하는 프로그램으로의 제작이 가능합니다.<br />
											<br />
											2. skin/ 디렉토리에서 null/ 디렉토리를 다른이름으로 skin/ 디렉토리에 복사합니다.<br />
											&nbsp; &nbsp; - 디렉토리명은 스킨이름이 되므로 알기 쉽게 정하는 것이 좋습니다.<br />
											&nbsp; &nbsp; - 스킨의 이름을 testskin 이라고 정했을 경우 skin/testskin/ 디렉토리를 만들고 skin/null/ 의 파일들을 skin/testskin/ 으로 복사합니다.<br />
											<br />
											3. 새로 만들 스킨폴더의 파일들의 내용을 용도에 맞게 디자인이나 코드를 수정합니다.<br />
											&nbsp; &nbsp; - 코드의 내용중 ...//////////end of preprocess 부분까지는 전처리 과정이고, 이후 부분부터 디자인 부분입니다.<br />
											&nbsp; &nbsp; - 버튼을 변경하려면 전처리 부분에서 버튼설정 주석 부분에서 \$btnXXX 변수 부분을 수정합니다.<br />
											<br />
											4. 스킨 디렉토리의 파일들에 대한 설명입니다.<br />
											&nbsp; &nbsp; - list.php : 게시판의 목록을 출력하는 파일입니다.<br />
											&nbsp; &nbsp; - view.php : 게시물의 내용을 출력해 주는 파일입니다.<br />
											&nbsp; &nbsp; - view_multi.php : 한꺼번에 여러개의 게시물을 볼때 출력해주는 파일입니다.<br />
											&nbsp; &nbsp; - write.php : 새 글 작성 및 수정 폼을 출력하는 파일입니다.<br />
											&nbsp; &nbsp; - style.php : 현재 스킨에 공통으로 포함되는 스타일 파일입니다. 별도의 공통추가 사항이 있을 경우 이 파일에 추가하여 이용할수 있습니다.<br />
											<br />
											5. 기본으로 포함된 스킨들에 대한 설명입니다.<br />
											&nbsp; &nbsp; - balhae_board/ : 일반적인 게시판 형태의 스킨입니다.<br />
											&nbsp; &nbsp; - balhae_photo/ : 포토갤러리 게시판 형태의 스킨입니다.<br />
											&nbsp; &nbsp; - balhae_authboard/ : 일반적인 게시판 형태이지만 관리자가 인증한 글만 보이게 하는 스킨입니다.<br />
											&nbsp; &nbsp; - balhae_schedule/ : 달력형태로 출력되는 일정관리 스킨입니다.<br />
											&nbsp; &nbsp; - _null/ : 처음 개발당시 디자인을 전혀 고려하지 않은 기본 게시판 형태의 스킨입니다.<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"50\"></td>
									</tr>
									<tr>
										<td><b style=\"font-size:11pt\">ㅡ 주의사항 ㅡ</b></td>
									</tr>
									<tr>
										<td>
											<br />
											1. 여러개의 게시판 사용시 각 게시판은 독립된 형태로 운영되며 관리자ID와 암호등도 모두 다르게 설정할수 있으므로 관리에 주의해야 합니다.<br />
											&nbsp; &nbsp; - 새 게시판 생성시 각 게시판의 관리자 아이디와 암호는 처음 설치시 입력한 아이디와 암호로 자동 저장됩니다.<br />
											&nbsp; &nbsp; - 암호 및 중요정보는 <span style=\"color:red\">MD5로 암호화</span> 되어 저장 되어 복원할수 없으므로 반드시 잘 기억해 두어야 합니다.<br />
											<br />
											2. 소스는 어떤형태로든 수정 또는 변형해서 사용 가능하나 소스 수정후 재배포는 절대 허용하지 않습니다.(스킨은 제외)<br />
											<br />
											3. 외부 저작권 표시 제거는 허용되나 내부 저작권 표시를 제거하고 사용할수 없습니다.<br />
											<br />
											4. 이 프로그램을 사용하면서 발생하는 모든 문제에 대한 책임은 전적으로 사용자에게 있습니다.<br />
											<br />
											5. 게시판의 명칭은 다음 같이 정의 됩니다.<br />
											&nbsp; - FSBoard : File System Board 의 줄임표현으로 ASP와 파일시스템을 이용해 제작배포된 게시판입니다.<br />
											&nbsp; - fsboard : free style board 의 줄임표현으로 ASP와 MS-SQL을 이용해 제작된 게시판입니다.<br />
											&nbsp; - FSBOARD : Free Style Board 의 줄임표현으로 PHP와 MySQL을 이용해 제작된 게시판입니다.<br />
											<br />
										</td>
									</tr>
									<tr>
										<td height=\"10\"></td>
									</tr>
								</table>

							</td>
						</tr>
						<tr>
							<td align=\"center\">
	";

	//설치가 완료된 이후 '새게시판 생성' 메뉴를 보임
	if(file_exists("$FSLIB_PATH/dbcon.php")) {
		echo "<a href=\"{$PHP_SELF}?mode=CreateAdmin\">[새 게시판 생성]</a> &nbsp;";
	}

	echo "
								<a href=\"javascript:window.history.go(-2);\">[이전 페이지]</a>
							</td>
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td height=\"30\"></td>
			</tr>
	";

	if(!file_exists("$FSLIB_PATH/dbcon.php")) {
		echo "
			<tr>
				<td align=\"center\">

					<table width=\"550\">
						<tr>
							<td>
		";
		if($msg1 || $msg2 || $msg3 || $msg4) {
			echo "<span style=\"color:red;\">{$msg1} {$msg2} {$msg3} {$msg4}</span>"; //퍼미션 에러 메시지
		}
		echo "
							</td>
						</tr>
					</table>

				</td>
			</tr>
			<tr>
				<td align=\"center\">

					<table width=\"100%\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;font-family:돋움;\" cellpadding=\"10\" cellspacing=\"0\">
					<form name=\"__ctl_cd\" method=\"post\" action=\"{$PHP_SELF}?virginity=pure\">
						<tr>
							<td align=\"center\">
								<table width=\"450\" style=\"border:1px solid silver;\">
									<tr>
										<th colspan=\"2\" height=\"30\" align=\"center\" style=\"border-bottom:1px solid silver;\">MySQL root 계정의 암호를 알고 있을 경우</th>
									</tr>
									<tr>
										<td colspan=\"2\" height=\"10\"></td>
									</tr>
									<tr>
										<td width=\"30%\" align=\"right\">MySQL root 암호</td>
										<td><input type=\"password\" size=\"30\" name=\"db_rootpasswd\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">DB 호스트</td>
										<td><input type=\"text\" size=\"30\" name=\"db_hostname\" value=\"localhost\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">생성할 DB 이름</td>
										<td><input type=\"text\" size=\"30\" name=\"db_name\" maxlength=\"64\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">생성할 DB 아이디</td>
										<td><input type=\"text\" size=\"30\" name=\"db_loginid\" maxlength=\"16\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">생성할 DB 암호</td>
										<td><input type=\"password\" size=\"30\" name=\"db_loginpasswd\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">암호 확인</td>
										<td><input type=\"password\" size=\"30\" name=\"db_loginpasswd2\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">외부접속 허용</td>
										<td><input type=\"checkbox\" name=\"xhost\" value=\"1\" checked=\"checked\" />3306포트를 통한 외부접속 허용</td>
									</tr>
									<tr>
										<td colspan=\"2\" align=\"center\" height=\"40\">
											<input type=\"submit\" value =\"  확 인  \" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</form>
					</table>

				</td>
			</tr>
			<tr>
				<td>

					<table width=\"100%\" bordercolor=\"#E0E0E0\" style=\"border-collapse:collapse;font-family:돋움;\" cellpadding=\"10\" cellspacing=\"0\">
					<form name=\"__ctl_ct\" method=\"post\" action=\"{$PHP_SELF}?virginity=lose\">
						<tr>
							<td align=\"center\">

								<table width=\"450\" style=\"border:1px solid silver;\">
									<tr>
										<th colspan=\"2\" height=\"30\" align=\"center\" style=\"border-bottom:1px solid silver;\">수동으로 DB 및 DB계정을 미리 생성한 경우</th>
									</tr>
									<tr>
										<td colspan=\"2\" height=\"10\"></td>
									</tr>
									<tr>
										<td align=\"right\">DB 호스트</td>
										<td><input type=\"text\" size=\"30\" name=\"db_hostname\" value=\"localhost\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td width=\"30%\" align=\"right\">DB 이름</td>
										<td><input type=\"text\" size=\"30\" name=\"db_name\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">DB 아이디</td>
										<td><input type=\"text\" size=\"30\" name=\"db_loginid\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td align=\"right\">DB 암호</td>
										<td><input type=\"password\" size=\"30\" name=\"db_loginpasswd\" style=\"border:1px solid silver;width:250;\" /></td>
									</tr>
									<tr>
										<td colspan=\"2\" align=\"center\" height=\"40\">
											<input type=\"submit\" value=\"  확 인  \" />
										</td>
									</tr>
								</table>

							</td>
						</tr>
					</form>
					</table>

				</td>
			</tr>
		";
	}
	else {
		echo "
			<tr>
				<td></td>
			</tr>
		";
	}

	echo "
			<tr>
				<td height=\"50\"></td>
			</tr>
			<tr>
				<td align=\"center\"><span style=\"font-size:11px;font-family:Verdana;\">Copyright(c)2000-2006 fsboard. All rights reserved.<br />Powered by <a href=\"mailto:saiur@msn.com\">Junghyun Cho</a>, <a href=\"http://ubalhae.com/\" onclick=\"window.open(this.href,'_blank'); return false;\">Balhae Co.,Ltd.</a></span></td>
			</tr>
		</table>
	";

	setup_foot();













} else if($MODE == "CreateAdmin") { ////////////////////////////////////////////////////////////////////// 관리자정보 생성

	//기본 DB테이블이 존재하는지 검사
	$checkAdminTable = SeekTable($_table_id_admin);
	$checkCommentTable = SeekTable($_table_id_comment);
	$checkTrackbackTable = SeekTable($_table_id_trackback);
	$checkTagcloudTable = SeekTable($_table_id_tagcloud);
	$checkMemberTable = SeekTable($_table_id_members);
	$checkMessageTable = SeekTable($_table_id_messages);

	//게시판 관리 테이블이 존재하지 않을 경우
	if(!$checkAdminTable) {
		//게시판 관리테이블 생성 쿼리
		$query = "
			CREATE TABLE `{$_table_id_admin}` (
				`aidx` int(10) unsigned NOT NULL auto_increment,
				`boardId` varchar(255) default NULL,
				`boardName` varchar(255) default NULL,
				`combinedFileName` varchar(255) default NULL,
				`adminIDs` text,
				`adminPasswd` varchar(255) default NULL,
				`skin` varchar(255) default NULL,
				`todayCount` int(10) unsigned default NULL,
				`totalCount` int(10) unsigned default NULL,
				`totalObj` int(10) unsigned default NULL,
				`allowNoticeNum` smallint(5) unsigned default NULL,
				`noticeNum` smallint(5) unsigned default NULL,
				`bgColor` varchar(15) default NULL,
				`bgImage` varchar(255) default NULL,
				`width` smallint(5) unsigned default NULL,
				`pageSize` smallint(5) unsigned default NULL,
				`divPage` smallint(5) unsigned default NULL,
				`subjectLimit` smallint(5) unsigned default NULL,
				`authorLimit` smallint(5) unsigned default NULL,
				`contentLimit` smallint(5) unsigned default NULL,
				`align` varchar(31) default NULL,
				`headFile` varchar(255) default NULL,
				`tailFile` varchar(255) default NULL,
				`headMsg` text,
				`tailMsg` text,
				`writeFrmDefMsg` text,
				`editMode` varchar(31) default NULL,
				`useListAtView` tinyint(1) default NULL,
				`useReply` tinyint(1) default NULL,
				`useTrackback` tinyint(1) default NULL,
				`useRssFeed` tinyint(1) default NULL,
				`useMemo` tinyint(1) default NULL,
				`useAutoLink` tinyint(1) default NULL,
				`usePreview` tinyint(1) default NULL,
				`useSiteLink1` tinyint(1) default NULL,
				`useSiteLink2` tinyint(1) default NULL,
				`useSecret` tinyint(1) default NULL,
				`useBlockSpam` tinyint(1) default NULL,
				`useBlockAnyLink` tinyint(1) default NULL,
				`useHideButtons` tinyint(1) default NULL,
				`useViewClientIp` tinyint(1) default NULL,
				`useViewClientInfo` tinyint(1) default NULL,
				`useExecFile` tinyint(1) default NULL,
				`useRszImg` tinyint(1) default NULL,
				`imgRszWidth` smallint(5) unsigned default NULL,
				`useNewIcon` smallint(6) default NULL,
				`termNewIcon` smallint(6) default NULL,
				`useHtml` varchar(15) default NULL,
				`allowEmbedFileExts` text,
				`allowTags` text,
				`useAttachFile` tinyint(1) default NULL,
				`fileMaxLimit` int(5) unsigned default NULL,
				`fileMaxNum` smallint(5) unsigned default NULL,
				`allowExts` text,
				`dataPath` varchar(255) default NULL,
				`useCategory` tinyint(1) default NULL,
				`categories` text,
				`useWordFilter` tinyint(1) default NULL,
				`badWords` text,
				`levelList` smallint(5) unsigned default NULL,
				`levelView` smallint(5) unsigned default NULL,
				`levelSecret` smallint(5) unsigned default NULL,
				`levelWrite` smallint(5) unsigned default NULL,
				`levelReply` smallint(5) unsigned default NULL,
				`levelMemoWrite` smallint(5) unsigned default NULL,
				`levelNoticeWrite` smallint(5) unsigned default NULL,
				`levelUseHtml` smallint(5) unsigned default NULL,
				`levelDelete` smallint(5) unsigned default NULL,
				`regDate` int(11) unsigned default NULL,
				`editDate` int(11) unsigned default NULL,
				`currDate` int(11) unsigned default NULL,
				PRIMARY KEY  (`aidx`)
			);
		";
		mysql_query($query) or Error(mysql_error());

		//코멘트테이블이 존재하지 않을 경우
		if(!$checkTrackbackTable) {
			//코멘트 테이블 생성 쿼리
			$query = "
				CREATE TABLE `{$_table_id_comment}` (
					`cidx` int(10) unsigned NOT NULL auto_increment,
					`boardId` varchar(255) default NULL,
					`objNum` int(10) unsigned default NULL,
					`isMember` varchar(255) default NULL,
					`name` varchar(255) default NULL,
					`e_mail` varchar(255) default NULL,
					`passwd` varchar(255) default NULL,
					`regDate` int(11) unsigned default NULL,
					`editDate` int(11) unsigned default NULL,
					`ipReg` varchar(30) default NULL,
					`ipEdit` varchar(30) default NULL,
					`usrAgentReg` varchar(255) default NULL,
					`usrAgentEdit` varchar(255) default NULL,
					`comments` longtext,
					PRIMARY KEY  (`cidx`)
				);
			";
			mysql_query($query) or Error(mysql_error());
		}

		//트랙백테이블이 존재하지 않을 경우
		if(!$checkTrackbackTable) {
			//트랙백 테이블 생성 쿼리
			$query = "
				CREATE TABLE `{$_table_id_trackback}` (
					`tidx` int(10) unsigned NOT NULL auto_increment,
					`boardId` varchar(255) default NULL,
					`objNum` int(10) unsigned default NULL,
					`tb_url` varchar(255) default NULL,
					`tb_title` varchar(255) default NULL,
					`tb_blog_name` varchar(255) default NULL,
					`tb_excerpt` longtext,
					`tb_regdate` int(11) unsigned default NULL,
					PRIMARY KEY  (`tidx`)
				);
			";
			mysql_query($query) or Error(mysql_error());
		}

		//태그클라우드테이블이 존재하지 않을 경우
		if(!$checkTagcloudTable) {
			//태그클라우드 테이블 생성 쿼리
			$query = "
				CREATE TABLE `{$_table_id_tagcloud}` (
					`idx` int(10) unsigned NOT NULL auto_increment,
					`strtag` varchar(255) default NULL,
					`boardId` varchar(255) default NULL,
					`memberId` varchar(255) default NULL,
					`freqRate` int(10) unsigned default NULL,
					`usedate` int(11) unsigned default NULL,
					PRIMARY KEY  (`idx`)
				);
			";
			mysql_query($query) or Error(mysql_error());
		}

		//회원테이블이 존재하지 않을 경우
		if(!$checkMemberTable) {
			//회원 테이블 생성 쿼리
			$query = "
				CREATE TABLE `{$_table_id_members}` (
					`idx` int(10) unsigned NOT NULL auto_increment,
					`mem_id` varchar(255) default NULL,
					`mem_passwd` varchar(255) default NULL,
					`mem_adminlevel` smallint(5) unsigned default NULL,
					`mem_level` smallint(5) unsigned default NULL,
					`mem_grade` varchar(31) default NULL,
					`mem_part` varchar(31) default NULL,
					`mem_auth` varchar(7) default NULL,
					`mem_name` varchar(31) default NULL,
					`mem_nickname` varchar(127) default NULL,
					`mem_idsn` varchar(255) default NULL,
					`mem_email` varchar(255) default NULL,
					`mem_homepage` varchar(255) default NULL,
					`mem_zipcode` varchar(7) default NULL,
					`mem_addr1` varchar(255) default NULL,
					`mem_addr2` varchar(255) default NULL,
					`mem_telnum` varchar(15) default NULL,
					`mem_hpnum` varchar(15) default NULL,
					`mem_job` varchar(31) default NULL,
					`mem_hobby` varchar(15) default NULL,
					`mem_birthday` varchar(15) default NULL,
					`mem_question` varchar(255) default NULL,
					`mem_answer` varchar(255) default NULL,
					`mem_mailing` tinyint(1) default NULL,
					`mem_picture` varchar(255) default NULL,
					`mem_imgmark` varchar(255) default NULL,
					`mem_imgname` varchar(255) default NULL,
					`mem_intro` longtext,
					`mem_regdate` int(11) unsigned default NULL,
					`mem_editdate` int(11) unsigned default NULL,
					`mem_latestdate` int(11) unsigned default NULL,
					`mem_faildate` int(11) unsigned default NULL,
					`mem_ip_reg` varchar(31) default NULL,
					`mem_ip_edit` varchar(31) default NULL,
					`mem_ip_login` varchar(31) default NULL,
					`mem_ip_failed` varchar(31) default NULL,
					`mem_loginnum` int(10) unsigned default '0',
					`mem_loginfailed` smallint(5) unsigned default '0',
					`mem_mileage` int(10) unsigned default '0',
					`public_id` tinyint(1) default NULL,
					`public_name` tinyint(1) default NULL,
					`public_email` tinyint(1) default NULL,
					`public_homepage` tinyint(1) default NULL,
					`public_addr` tinyint(1) default NULL,
					`public_telnum` tinyint(1) default NULL,
					`public_hpnum` tinyint(1) default NULL,
					`public_job` tinyint(1) default NULL,
					`public_hobby` tinyint(1) default NULL,
					`public_birthday` tinyint(1) default NULL,
					`public_picture` tinyint(1) default NULL,
					`public_intro` tinyint(1) default NULL,
					`public_regdate` tinyint(1) default NULL,
					`public_latestdate` tinyint(1) default NULL,
					`public_all` tinyint(1) default NULL,
					PRIMARY KEY  (`idx`)
				);
			";
			mysql_query($query) or Error(mysql_error());
		}

		//쪽지테이블이 존재하지 않을 경우
		if(!$checkMessageTable) {
			//쪽지 테이블 생성 쿼리#################################################################추후작업
			$query = "

			";
			//mysql_query($query) or Error(mysql_error());
		}

		$_SESSION["virgin"] = true;
	}
	else {
		if($checkMemberTable) { //회원테이블이 존재할 경우
			$query = "SELECT * FROM ".$_table_id_members." WHERE mem_level<=1;"; //관리자 정보 가져옴
			$result = mysql_query($query) or Error(mysql_error());
			if($result) {
				$numrows = mysql_num_rows($result);
			}
			if($numrows) {
				$_SESSION["virgin"] = false;
				MovePage($_SERVER["PHP_SELF"]."?mode=CreateBoard");
				exit;
			}
			else {
				$_SESSION["virgin"] = true; //처음실행인지 확인할때 사용
			}
		}
		else $_SESSION["virgin"] = true;
	}


	setup_head();

	echo "
		<br />
		<br />
		<table width=\"400\" align=\"center\" style=\"border:1px solid #E0E0E0;\" cellpadding=\"3\" cellspacing=\"0\"><!-- 최초실행시 관리자 정보 입력 폼 -->
		<form name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=CreateAdminExec\">
			<tr>
				<td colspan=\"2\" align=\"center\" height=\"28\" style=\"border-bottom:1px solid #E0E0E0;\"><b>관리자 정보 생성</b></td>
			</tr>
			<tr>
				<td align=\"center\" height=\"160\" style=\"padding:0px;\">
					<table width=\"100%\" cellpadding=\"3\" cellspacing=\"0\" style=\"border:0px solid silver;\">
						<tr>
							<td width=\"140\" align=\"right\">관리자 아이디:</td>
							<td><input type=\"text\" size=\"25\" name=\"mem_id\" value=\"admin\" style=\"border:1px solid #E0E0E0;width:170px;\" /></td>
						</tr>
						<tr>
							<td align=\"right\">관리자 암호:</td>
							<td><input type=\"password\" \"25\" name=\"mem_passwd\" style=\"border:1px solid #E0E0E0;width:170px;\" /></td>
						</tr>
						<tr>
							<td align=\"right\">암호 확인:</td>
							<td><input type=\"password\" size=\"25\" name=\"mem_passwd2\" style=\"border:1px solid #E0E0E0;width:170px;\" /></td>
						</tr>
						<tr>
							<td align=\"right\">관리자 이름:</td>
							<td><input type=\"text\" size=\"25\" name=\"mem_name\" value=\"관리자\" style=\"border:1px solid #E0E0E0;width:170px;\" /></td>
						</tr>
						<tr>
							<td align=\"right\">관리자 이메일:</td>
							<td><input type=\"text\" size=\"25\" name=\"mem_email\" style=\"border:1px solid #E0E0E0;width:170px;\" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<table width=\"400\" align=\"center\">
			<tr>
				<td colspan=\"2\" align=\"center\"><a href=\"javascript:document.forms['__ctl1'].submit();\"><img src=\"/{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a> <a href=\"javascript:window.history.back();\"><img src=\"/{$FSBOARD_PATH}/img/btn/cancel.gif\"  alt=\"취소\" /></a></td>
			</tr>
		</form>
		</table>
		<br />
		<br />
		<br />
	";

	setup_foot();







} else if($MODE == "CreateAdminExec") { ////////////////////////////////////////////////////////////////////// 관리자정보 생성 처리

	//외부입력 방지
	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");
	if($_SERVER["REQUEST_METHOD"]=="GET") Error("잘못된 접근입니다.");

	$mem_id = trim($_POST["mem_id"]);
	$mem_passwd = trim($_POST["mem_passwd"]);
	$mem_passwd2 = trim($_POST["mem_passwd2"]);
	$mem_name = trim($_POST["mem_name"]);
	$mem_email = trim($_POST["mem_email"]);

	$mem_level = 1;
	$mem_ip = $_SERVER["REMOTE_HOST"];
	$mem_regdate = mktime();

	if(!$mem_id) Error("관리자 아이디를 입력해 주세요.");
	if(!$mem_passwd) Error("관리자 암호를 입력해 주세요.");
	if(!$mem_passwd2) Error("암호확인을 입력해 주세요.");
	if(!$mem_name) Error("관리자 이름을 입력해 주세요.");
	if(!$mem_email) Error("관리자 이메일을 입력해 주세요.");

	if($mem_passwd!=$mem_passwd2) Error("암호와 암호확인이 일치하지 않습니다");
	else $mem_passwd = md5($mem_passwd);

	//처음실행일 경우
	if($_SESSION["virgin"]) {
		$query = "INSERT INTO ".$_table_id_members." (
				mem_id,
				mem_passwd,
				mem_level,
				mem_name,
				mem_email,
				mem_regdate,
				mem_latestdate,
				mem_ip_reg,
				mem_ip_login
			) VALUES (
				'{$mem_id}',
				'{$mem_passwd}',
				{$mem_level},
				'{$mem_name}',
				'{$mem_email}',
				{$mem_regdate},
				{$mem_regdate},
				'{$mem_ip_reg}',
				'{$mem_ip_reg}'
			);";

		mysql_query($query) or Error(mysql_error());

		$_SESSION["MemId"] = $mem_id;
		$_SESSION["MemPasswd"] = $mem_passwd;
		$_SESSION["MemLevel"] = $mem_level;
		$_SESSION["MemName"] = $mem_name;
		$_SESSION["Host"] = $_SERVER["HTTP_HOST"];

		MovePage($_SERVER["PHP_SELF"]."?mode=CreateBoard");
		exit;
	}
	else { Error("잘못된 접근입니다."); }

















} else if($MODE == "Login") { ////////////////////////////////////////////////////////////////////// 관리자 로그인

	$mem_id = trim($_POST["mem_id"]);
	$mem_passwd = trim($_POST["mem_passwd"]);
	$nav = trim($_GET["nav"]);
	if(!$nav) $nav = "CreateBoard";

	if($mem_id && $mem_passwd) {
		$mem_passwd = md5($mem_passwd);
		$query = "SELECT * FROM ".$_table_id_members." WHERE mem_id='{$mem_id}' AND mem_passwd='{$mem_passwd}';";
		$result = mysql_query($query) or Error(mysql_error());
		if($result) {
			$numrows = mysql_num_rows($result);
			if(!$numrows) { Error("관리자 정보가 일치하지 않습니다"); exit; }
			else {
				$rs = mysql_fetch_array($result);
				$mem_level = $rs["mem_level"];
				if($mem_level=="" || $mem_level>1) { Error("관리자 권한이 아닙니다."); exit; }
				else {
					$_SESSION["MemId"] = $rs["mem_id"];
					$_SESSION["MemPasswd"] = $rs["mem_passwd"];
					$_SESSION["MemLevel"] = $rs["mem_level"];
					$_SESSION["MemName"] = $rs["mem_name"];
					$_SESSION["Host"] = $_SERVER["HTTP_HOST"];
					
					MovePage("/wb_admin");
					//MovePage($_SERVER["PHP_SELF"]."?mode={$nav}");
				}
			}
			mysql_free_result($result);
		}
	}
	else {
		setup_head();

		/*echo "
			<br />
			<br />
			<table width=\"350\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\" style=\"border:1px solid #E0E0E0; font-size:9pt;\">
			<form name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=Login&nav={$nav}\">
				<tr>
					<th colspan=\"2\" align=\"center\" height=\"28\" style=\"border-bottom:1px solid #E0E0E0;\">관리자 로그인</td>
				</tr>
				<tr>
					<td align=\"center\">
						<table cellpadding=\"3\" cellspacing=\"0\">
							<tr>
								<tr>
									<td align=\"right\">아이디</td>
									<td><input type=\"text\" size=\"25\" name=\"mem_id\" style=\"border:1px solid #E0E0E0;width:180px;\" /></td>
								</tr>
								<tr>
									<td align=\"right\">암호</td>
									<td><input type=\"password\" size=\"25\" name=\"mem_passwd\" style=\"border:1px solid #E0E0E0;width:180px;\" onkeypress=\"if(event.keyCode==13){document.forms['__ctl1'].submit();}\" /></td>
								</tr>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=\"2\" align=\"center\"><a href=\"javascript:document.forms['__ctl1'].submit();\"><img src=\"/{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a> <a href=\"javascript:window.history.back();\"><img src=\"/$FSBOARD_PATH/img/btn/cancel.gif\" alt=\"취소\" /></a></td>
				</tr>
			</form>
			</table>
			<br />
			<br />
		";*/

		echo "
			<form name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=Login&nav={$nav}\">
			<table width='100%' border='0' cellspacing='0' cellpadding='0'>
			  <tr> 
				<td height='186' valign='bottom' align='center'> <img src='/fsboard/img/adm_login/login_img_01.jpg' width='663' height='73'></td>
			  </tr>
			  <tr>
				<td height='243' bgcolor='E7E5D7'>
				  <table width='663' border='0' cellspacing='0' cellpadding='0' align='center'>
					<tr> 
					  <td height='243' bgcolor='#FFFFFF' width='310'><img src='/fsboard/img/adm_login/login_img_03.jpg' width='310' height='244'></td>
					  <td height='243'> 
						<table border='0' cellspacing='0' cellpadding='0' background='/fsboard/img/adm_login/login_img_04.jpg' height='244'>
						  <tr> 
							<td height='91' width='337'>&nbsp;</td>
							<td height='91' rowspan='2'><img src='/fsboard/img/adm_login/login_img_05.jpg' width='16' height='244'></td>
						  </tr>
						  <tr> 
							<td> 
							
							  <table border='0' cellspacing='0' cellpadding='0' align='center'>
								<tr> 
								  <td width='83'><img src='/fsboard/img/adm_login/id.jpg' width='83' height='18'></td>
								  <td> 
									<input id='mem_id' style='BORDER-RIGHT: #A8A292 1px solid; BORDER-TOP: #A8A292 1px solid; BORDER-LEFT: #A8A292 1px solid; BORDER-BOTTOM: #A8A292 1px solid; FONT-SIZE: 10pt; width: 148px; HEIGHT: 18px' size=15 name=mem_id>
								  </td>
								  <td rowspan='2' width='72' align='right'> 
									<a href=\"javascript:document.forms['__ctl1'].submit();\"><img height=58 width=58 src='/fsboard/img/adm_login/login.jpg' name='image'>
								  </td>
								</tr>
								<tr> 
								  <td><img src='/fsboard/img/adm_login/pw.jpg' width='83' height='18'></td>
								  <td> 
									<input id=mem_passwd style='BORDER-RIGHT: #A8A292 1px solid; BORDER-TOP: #A8A292 1px solid; BORDER-LEFT: #A8A292 1px solid; BORDER-BOTTOM: #A8A292 1px solid; FONT-SIZE: 10pt; width: 148px; HEIGHT: 18px' type=password size=15 name=mem_passwd onkeypress=\"if(event.keyCode==13){document.forms['__ctl1'].submit();}\">
								  </td>
								</tr>
							  </table>
							  
							</td>
						  </tr>
						</table>
					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			</table>
			<table width='631' border='0' cellspacing='0' cellpadding='0' align='center'>
			  <tr> 
				<td height='67' bgcolor='#FFFFFF'>&nbsp;</td>
			  </tr>
			</table>
			<div align='center'><img src='/fsboard/img/adm_login/login_img_02.jpg' width='663' height='48'></div>
			</form>
		";

		setup_foot();
	}

















} else if($MODE == "CreateBoard") { ////////////////////////////////////////////////////////////////////// 게시판 생성
	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	//관리자만 접근 허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage($_SERVER["PHP_SELF"]."?mode=Login"); exit; }

	setup_head();

	echo "
		<br />
		<form name=\"__ctl1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=CreateBoardExec\" style=\"margin:0;\">
		<table align=\"center\" cellpadding=\"5\" cellspacing=\"0\" style=\"width:600px; font-size:9pt;\">
			<tr>
				<td colspan=\"2\" align=\"center\" bgcolor=\"#FAFBF7\" style=\"border:1px solid #E0E0E0;height:20px;\"><img src=\"/{$FSBOARD_PATH}/img/clip/doc2.gif\" alt=\"icon\" /> <b>새 게시판 생성</b></td>
			</tr>
			<tr>
				<td align=\"right\" width=\"10%\" style=\"border-bottom:1px solid #E0E0E0;\" nowrap><b>게시판 아이디</b> <img src=\"/{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /></td>
				<td style=\"border-bottom:1px solid #E0E0E0;\"><input type=\"text\" size=\"40\" name=\"boardId\" style=\"border:1px solid #E0E0E0;\" /> <nobr>(영문,숫자 또는 이들의 조합, <span style=\"color:red;\">언더바(_) 이외의 특수문자는 사용불가</span>)</nobr></td>
			</tr>
			<tr>
				<td align=\"right\" style=\"border-bottom:1px solid #E0E0E0;\" nowrap><b>게시판 이름</b> <img src=\"/{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /></td>
				<td style=\"border-bottom:1px solid #E0E0E0;\"><input type=\"text\" size=\"40\" name=\"boardName\" style=\"border:1px solid #E0E0E0;\" /> <nobr>(알아보기 쉬운 한글 이름, 관리용 이름)</nobr></td>
			</tr>
			<tr>
				<td align=\"right\" style=\"border-bottom:1px solid #E0E0E0;\" nowrap><b>스킨</b> <img src=\"/{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /></td>
				<td style=\"border-bottom:1px solid #E0E0E0;\">
					<select name=\"skin\">
						<option value=\"\">스킨선택</option>
	";
	$skin_dir = dir($_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH."/skin/");
	while($entry=$skin_dir->read()) {
		if($entry!="." && $entry!="..") {
			if(is_dir($_SERVER["DOCUMENT_ROOT"]."/".$FSBOARD_PATH."/skin/".$entry))
				echo "<option value=\"{$entry}\">$entry</option>\n";
		}
	}
	echo "
					</select> <nobr>(게시판 특성에 맞게 선택, 일반게시판 포토게시판 등)</nobr>
				</td>
			</tr>
			<tr>
				<td align=\"center\" style=\"border-bottom:1px solid #E0E0E0;\">게시판이 들어갈<br />디자인파일명</td>
				<td style=\"border-bottom:1px solid #E0E0E0;\">
					<input type=\"text\" size=\"50\" name=\"combinedFileName\" style=\"border:1px solid #E0E0E0;width:100%;\" />
					<nobr>(게시판과 결합되어 보여지는 디자인파일명)</nobr>
				</td>
			</tr>
			<tr>
				<td align=\"center\" style=\"border-bottom:1px solid #E0E0E0;\">현재 게시판<br />관리자 암호</td>
				<td style=\"border-bottom:1px solid #E0E0E0;\">
					<table>
						<tr>
							<td colspan=\"2\"><nobr>[비로그인 상태에서 수정/삭제 등의 관리 암호]</nobr></td>
						</tr>
						<tr>
							<td nowrap>암호</td>
							<td><input type=\"password\" size=\"40\" name=\"adminPasswd1\" style=\"border:1px solid #E0E0E0;\" /> <nobr>(4자리 이상, 영문/숫자/조합 / <span style=\"color:red;\">입력하지 않으면 현재 로그인정보로 저장</span>)</nobr></td>
						</tr>
						<tr>
							<td nowrap>확인</td>
							<td><input type=\"password\" size=\"40\" name=\"adminPasswd2\" style=\"border:1px solid #E0E0E0;\" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=\"center\" style=\"border-bottom:1px solid #E0E0E0;\">현재 게시판<br />관리자 아이디</td>
				<td style=\"border-bottom:1px solid #E0E0E0;\">
					<textarea rows=\"2\" cols=\"80\" name=\"adminIDs\" style=\"border:1px solid #E0E0E0;width:100%;\"></textarea><br />
					<nobr>(관리자 아이디 목록. 쉼표(,)로 구분 / <span style=\"color:red;\">비워두면 현재 관리자만 관리 가능</span>)</nobr>
				</td>
			</tr>
			<tr>
				<td align=\"center\" style=\"border-bottom:1px solid #E0E0E0;\">방문자 카운터</td>
				<td style=\"border-bottom:1px solid #E0E0E0;\">
					<table>
						<tr>
							<td>오늘:</td>
							<td><input type=\"text\" size=\"20\" name=\"todayAccess\" value=\"0\" style=\"border:1px solid #E0E0E0;\" /> (필요시에만 변경)</td>
						</tr>
						<tr>
							<td>전체:</td>
							<td><input type=\"text\" size=\"20\" name=\"totalAccess\" value=\"0\" style=\"border:1px solid #E0E0E0;\" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table align=\"center\" style=\"width:600px; font-size:9pt;\">
			<tr>
				<td><a href=\"".$_SERVER["PHP_SELF"]."?mode=List\">게시판 관리</a></td>
				<td align=\"right\"><img src=\"/{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /> <b>표시</b>는 반드시 확인하세요</td>
			</tr>
			<tr>
				<td colspan=\"2\" style=\"border-top:1px solid #e0e0e0; text-align:center;\">
					<a href=\"javascript:document.forms['__ctl1'].submit();\"><img src=\"/{$FSBOARD_PATH}/img/btn/submit.gif\" alt=\"확인\" /></a>
					<a href=\"javascript:window.history.back();\"><img src=\"/{$FSBOARD_PATH}/img/btn/cancel.gif\" alt=\"취소\" /></a>
				</td>
			</tr>
		</table>
		</form>
		<br />
		<br />
	";

	setup_foot();

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";
















} else if($MODE == "CreateBoardExec") { ////////////////////////////////////////////////////////////////////// 게시판 생성 처리

	//외부입력 방지
	if(!eregi($_SERVER["HTTP_HOST"], $_SERVER["HTTP_REFERER"])) Error("잘못된 접근입니다.");
	if($_SERVER["REQUEST_METHOD"] == 'GET' ) Error("잘못된 접근입니다.");

	$boardId = StrAddSlashes(trim($_POST["boardId"])); //게시판아이디
	$boardName = StrAddSlashes(trim($_POST["boardName"])); //게시판이름
	$skin = StrAddSlashes(trim($_POST["skin"])); //스킨
	$adminPasswd1 = trim($_POST["adminPasswd1"]);
	$adminPasswd2 = trim($_POST["adminPasswd2"]);
	$adminIDs = StrAddSlashes(trim($_POST["adminIDs"]));
	$combinedFileName = StrAddSlashes(trim($_POST["combinedFileName"]));
	$todayAccess = StrAddSlashes(trim($_POST["todayAccess"]));
	$totalAccess = StrAddSlashes(trim($_POST["totalAccess"]));

	//필수요소 입력 확인
	if(IsBlank($boardId)) Error("게시판 아이디를 입력해 주세요");
	if(IsBlank($boardName)) Error("게시판 이름을 입력해 주세요");
	if(IsBlank($skin)) Error("스킨을 선택해 주세요");

	//새 게시판의 DB테이블 이름
	$boardId1 = "_board_".$boardId;
	$boardId2 = "_board_cmt_".$boardId;

	//DB테이블 이름이 중복되는지 검사
	if(SeekTable($boardId1) || SeekTable($board2)) Error("게시판 아이디 {$boardId} (은)는 이미 존재합니다.<br /><br />다른 이름을 사용해 주세요.");

	//관리자 암호 검사
	if($adminPasswd1 != $adminPasswd2) Error("관리자 암호와 암호확인이 일치하지 않습니다.");
	$adminPasswd = $adminPasswd1&&$adminPasswd2 ? md5($adminPasswd1) : $_SESSION["MemPasswd"];

	//게시판 카운터
	$todayAccess = intval($todayAccess);
	$totalAccess = intval($totalAccess);

	/////새 게시판의 기본설정
	$totalObj				= 0; //게시물수
	$allowNoticeNum			= 5; //공지글 허용수
	$noticeNum				= 0; //공지글 수
	$bgColor				= ""; //배경색
	$bgImage				= ""; //배경 이미지
	$width					= 99; //게시판 폭
	$pageSize				= 15; //페이지당 개시물수
	$divPage				= 10; //페이지 바로가기수
	$subjectLimit			= 40; //목록에서 제목 글자수
	$authorLimit			= 8; //목록에서 작성자 글자수
	$contentLimit			= 200; //목록에서 미리보기 글자수
	$align					= ""; //게시판 프레임 정렬
	$headFile				= ""; //상단 포함 파일
	$tailFile				= ""; //하단 포함 파일
	$headMsg				= ""; //상단 포함 내용
	$tailMsg				= ""; //하단 포함 내용
	$writeFrmDefMsg			= ""; //글쓰기시 기본 내용
	$editMode				= "text"; //글쓰기시 기본 편집 모드
	$useListAtView			= 1; //내용 아래에 목록 보기 사용
	$useReply				= 1; //답변 기능 사용
	$useTrackback			= 1; //트랙백 기능 사용
	$useRssFeed				= 1; //RSS피드 기능 사용
	$useMemo				= 1; //댓글 기능 사용
	$useAutoLink			= 1; //자동링크 사용
	$usePreview				= 1; //목록에서 미리보기 사용
	$useSiteLink1			= 0; //관련링크1 사용
	$useSiteLink2			= 0; //관련링크2 사용
	$useSecret				= 1; //비밀글 기능 사용
	$useBlockSpam			= 1; //스팸차단 사용
	$useBlockAnyLink		= 1; //무단링크방지 사용
	$useHideButtons			= 1; //불필요한 버튼 숨기기 사용
	$useViewClientIp		= 1; //작성자 IP주소보기 사용
	$useViewClientInfo		= 0; //작성자 시스템정보 보기 사용
	$useExecFile			= 1; //내용에서 실행 가능한 첨부파일 보기 사용
	$useRszImg				= 1; //내용에서 큰이미지 자동 줄이기 사용
	$imgRszWidth			= 600; //큰이미지 자동으로 줄일 폭
	$useNewIcon				= 2; //새글에 new 표시 사용
	$termNewIcon			= 1; //new 표시 기간
	$useHtml				= "part"; //글작성시 HTML허용 여부
	$allowEmbedFileExts		= "gif,jpg,jpeg,bmp,png,swf,wma,wmv,mpg,mpeg,avi,mp3,mid"; //내용에서 첨부파일 자동으로 실행시 허용할 확장자
	$allowTags				= "!--,br,table,tbody,tr,td,img,b,strong,center,a,p,font,ul,li,hr,span,h1,h2,h3,h4,h5"; //허용할 HTML 태그
	$useAttachFile			= 1; //첨부파일 기능 사용
	$fileMaxLimit			= 10485760; //업로드 파일 용량
	$fileMaxNum				= 30; //업로드 파일 갯수
	$allowExts				= "txt,hwp,doc,xls,ppt,pdf,zip,alz,gif,jpg,jpeg,bmp,png,swf,wma,wmv,mpg,mpeg,avi,mp3,mid,swf,exe,msi,ttf"; //업로드 가능한 확장자
	$dataPath				= "/data"; //첨부파일 저장 디렉토리
	$useCategory			= 0; //카테고리 기능 사용
	$categories				= "질문,답변,건의,강좌,일반"; //카테고리 목록
	$useWordFilter			= 1; //불량단어 필터링 사용
	$badWords				= "8억,개새끼,소새끼,미친새끼,병신,지랄,염병,씨팔,씨부랄,십팔,니기미,지랄,찌랄,쌍년,쌍놈,빙신,니기미,잡놈,벼엉신,바보새끼,씹새,씨발,시벌,씨벌,떠그랄,좆,추천인,추천id,추천아이디,추/천/인,쉐이,등신,싸가지,미친놈,미친넘,죽습니다,님아,님들아,씨밸넘,븅신"; //필터링할 불량 단어리스트
	$levelList				= 10; //목록 접근 레벨
	$levelView				= 10; //내용 접근 레벨
	$levelSecret			= 1; //비밀글 보기 레벨
	$levelWrite				= 10; //글쓰기 허용 레벨
	$levelReply				= 10; //답글 쓰기 레벨
	$levelMemoWrite			= 10; //댓글 쓰기 레벨
	$levelNoticeWrite		= 1; //공지글 쓰기 레벨
	$levelUseHtml			= 10; //HTML 사용 레벨
	$levelDelete			= 1; //삭제 레벨
	$regDate				= mktime(); //생성일
	$currDate				= $regDate; //오늘날짜 확인

	$query = "INSERT INTO {$_table_id_admin} (
			boardId,
			boardName,
			combinedFileName,
			adminIDs,
			adminPasswd,
			skin,
			todayCount,
			totalCount,

			totalObj,
			allowNoticeNum,
			noticeNum,
			bgColor,
			bgImage,
			width,
			pageSize,
			divPage,
			subjectLimit,
			authorLimit,
			contentLimit,
			align,
			headFile,
			tailFile,
			headMsg,
			tailMsg,
			writeFrmDefMsg,
			editMode,
			useListAtView,
			useReply,
			useTrackback,
			useRssFeed,
			useMemo,
			useAutoLink,
			usePreview,
			useSiteLink1,
			useSiteLink2,
			useSecret,
			useBlockSpam,
			useBlockAnyLink,
			useHideButtons,
			useViewClientIp,
			useViewClientInfo,
			useExecFile,
			useRszImg,
			imgRszWidth,
			useNewIcon,
			termNewIcon,
			useHtml,
			allowEmbedFileExts,
			allowTags,
			useAttachFile,
			fileMaxLimit,
			fileMaxNum,
			allowExts,
			dataPath,
			useCategory,
			categories,
			useWordFilter,
			badWords,
			levelList,
			levelView,
			levelSecret,
			levelWrite,
			levelReply,
			levelMemoWrite,
			levelNoticeWrite,
			levelUseHtml,
			levelDelete,
			regDate,
			currDate

		) VALUES (

			'$boardId',
			'$boardName',
			'$combinedFileName',
			'$adminIDs',
			'$adminPasswd',
			'$skin',
			$todayAccess,
			$totalAccess,

			$totalObj,
			$allowNoticeNum,
			$noticeNum,
			'$bgColor',
			'$bgImage',
			$width,
			$pageSize,
			$divPage,
			$subjectLimit,
			$authorLimit,
			$contentLimit,
			'$align',
			'$headFile',
			'$tailFile',
			'$headMsg',
			'$tailMsg',
			'$writeFrmDefMsg',
			'$editMode',
			$useListAtView,
			$useReply,
			$useTrackback,
			$useRssFeed,
			$useMemo,
			$useAutoLink,
			$usePreview,
			$useSiteLink1,
			$useSiteLink2,
			$useSecret,
			$useBlockSpam,
			$useBlockAnyLink,
			$useHideButtons,
			$useViewClientIp,
			$useViewClientInfo,
			$useExecFile,
			$useRszImg,
			$imgRszWidth,
			$useNewIcon,
			$termNewIcon,
			'$useHtml',
			'$allowEmbedFileExts',
			'$allowTags',
			$useAttachFile,
			$fileMaxLimit,
			$fileMaxNum,
			'$allowExts',
			'$dataPath',
			$useCategory,
			'$categories',
			$useWordFilter,
			'$badWords',
			$levelList,
			$levelView,
			$levelSecret,
			$levelWrite,
			$levelReply,
			$levelMemoWrite,
			$levelNoticeWrite,
			$levelUseHtml,
			$levelDelete,
			$regDate,
			$currDate
		);
	";
	mysql_query($query) or Error(mysql_error());

	//게시판 DB테이블 쿼리
	$query = "
		CREATE TABLE `{$boardId1}` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`objProperty` varchar(10) default NULL,
			`isNotice` tinyint(1) default NULL,
			`isSecret` tinyint(1) default NULL,
			`isMember` varchar(255) default NULL,
			`docType` varchar(10) default NULL,
			`author` varchar(255) default NULL,
			`e_mail` varchar(255) default NULL,
			`homeUrl` varchar(255) default NULL,
			`subject` varchar(255) default NULL,
			`passwd` varchar(255) default NULL,
			`category` varchar(127) default NULL,
			`tag_ls` varchar(255) default NULL,
			`regDate` int(11) unsigned default NULL,
			`editDate` int(11) unsigned default NULL,
			`memoLatestDate` int(10) unsigned default NULL,
			`memoNum` int(10) unsigned default NULL,
			`tbNum` int(10) unsigned default NULL,
			`readNum` int(10) unsigned default NULL,
			`voteNum` int(10) unsigned default NULL,
			`ipReg` varchar(25) default NULL,
			`ipEdit` varchar(25) default NULL,
			`usrAgentReg` varchar(255) default NULL,
			`usrAgentEdit` varchar(255) default NULL,
			`ref` int(10) unsigned default NULL,
			`reStep` int(10) unsigned default NULL,
			`reLevel` int(10) unsigned default NULL,
			`siteLink1` varchar(255) default NULL,
			`siteLink2` varchar(255) default NULL,
			`siteLinkCount1` int(10) default NULL,
			`siteLinkCount2` int(10) default NULL,
			`tbLink` varchar(255) default NULL,
			`fileName1` varchar(255) default NULL,
			`fileName2` varchar(255) default NULL,
			`fileName3` varchar(255) default NULL,
			`fileName4` varchar(255) default NULL,
			`fileName5` varchar(255) default NULL,
			`fileName6` varchar(255) default NULL,
			`fileName7` varchar(255) default NULL,
			`fileName8` varchar(255) default NULL,
			`fileName9` varchar(255) default NULL,
			`fileName10` varchar(255) default NULL,
			`fileName11` varchar(255) default NULL,
			`fileName12` varchar(255) default NULL,
			`fileName13` varchar(255) default NULL,
			`fileName14` varchar(255) default NULL,
			`fileName15` varchar(255) default NULL,
			`fileName16` varchar(255) default NULL,
			`fileName17` varchar(255) default NULL,
			`fileName18` varchar(255) default NULL,
			`fileName19` varchar(255) default NULL,
			`fileName20` varchar(255) default NULL,
			`fileName21` varchar(255) default NULL,
			`fileName22` varchar(255) default NULL,
			`fileName23` varchar(255) default NULL,
			`fileName24` varchar(255) default NULL,
			`fileName25` varchar(255) default NULL,
			`fileName26` varchar(255) default NULL,
			`fileName27` varchar(255) default NULL,
			`fileName28` varchar(255) default NULL,
			`fileName29` varchar(255) default NULL,
			`fileName30` varchar(255) default NULL,
			`fileDownload1` int(10) unsigned default NULL,
			`fileDownload2` int(10) unsigned default NULL,
			`fileDownload3` int(10) unsigned default NULL,
			`fileDownload4` int(10) unsigned default NULL,
			`fileDownload5` int(10) unsigned default NULL,
			`fileDownload6` int(10) unsigned default NULL,
			`fileDownload7` int(10) unsigned default NULL,
			`fileDownload8` int(10) unsigned default NULL,
			`fileDownload9` int(10) unsigned default NULL,
			`fileDownload10` int(10) unsigned default NULL,
			`fileDownload11` int(10) unsigned default NULL,
			`fileDownload12` int(10) unsigned default NULL,
			`fileDownload13` int(10) unsigned default NULL,
			`fileDownload14` int(10) unsigned default NULL,
			`fileDownload15` int(10) unsigned default NULL,
			`fileDownload16` int(10) unsigned default NULL,
			`fileDownload17` int(10) unsigned default NULL,
			`fileDownload18` int(10) unsigned default NULL,
			`fileDownload19` int(10) unsigned default NULL,
			`fileDownload20` int(10) unsigned default NULL,
			`fileDownload21` int(10) unsigned default NULL,
			`fileDownload22` int(10) unsigned default NULL,
			`fileDownload23` int(10) unsigned default NULL,
			`fileDownload24` int(10) unsigned default NULL,
			`fileDownload25` int(10) unsigned default NULL,
			`fileDownload26` int(10) unsigned default NULL,
			`fileDownload27` int(10) unsigned default NULL,
			`fileDownload28` int(10) unsigned default NULL,
			`fileDownload29` int(10) unsigned default NULL,
			`fileDownload30` int(10) unsigned default NULL,
			`contents` longtext,
			`tel_num` varchar(50) default NULL,
			`hp_num` varchar(50) default NULL,
			`start_date` varchar(255) default NULL,
			`end_date` varchar(255) default NULL,
			`other01` varchar(255) default NULL,
			`other02` varchar(255) default NULL,
			`other03` varchar(255) default NULL,
			`other04` varchar(255) default NULL,
			`other05` varchar(255) default NULL,
			`other06` varchar(255) default NULL,
			`other07` varchar(255) default NULL,
			`other08` varchar(255) default NULL,
			`other09` varchar(255) default NULL,
			`other10` varchar(255) default NULL,
			PRIMARY KEY  (`idx`)
		);
	";
	mysql_query($query) or Error(mysql_error());

	if(!$combinedDesign) {
		MovePage("/{$FSBOARD_PATH}/{$_fsMainExecFile}?id={$boardId}");
	}
	else {
		MovePage($_SERVER["PHP_SELF"]."?mode=List");
	}

 } else if($MODE == "List") { ////////////////////////////////////////////////////////////////////// 설치된 게시판 리스트

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_head.php";

	
	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];

	if ($MODE_ADMIN=="admin"){
		$MemId = $mem_id;
		$MemLevel = $MemLevel;	
	}
	//if(!$MemId || $MemLevel>1) { MovePage($_SERVER["PHP_SELF"]."?mode=Login&nav=List"); exit; }

	$width = "98%";
	$align = "center";

	$pagesize = 20;
	$divpage = 10;

	$srhctgr = trim($_GET["srhctgr"] ? $_GET["srhctgr"] : $_POST["srhctgr"]);
	$srhstr = trim($_GET["srhstr"] ? $_GET["srhstr"] : $_POST["srhstr"]);

	$page = intval($_GET["page"]);
	if(!$page) $page = 1;

	$query = "SELECT * FROM {$_table_id_admin} ";
	if($srhctgr&&$srhstr) $query .= " WHERE {$srhctgr} LIKE '%".str_replace("'","\\'",$srhstr)."%' ";

	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		$totalObj = mysql_num_rows($result);
		mysql_free_result($result);
	}

	$totalpage = intval(($totalObj - 1) / $pagesize) + 1;
	if($page>=1) $sequence = $totalObj - ($pagesize * ($page - 1));

	$query .= " ORDER BY aidx DESC LIMIT ".(($page-1)*$pagesize).",{$pagesize};";
	$result = mysql_query($query) or Error(mysql_error());

	if($result) {
		$numrows = mysql_num_rows($result);

		setup_head();

		echo "
		<style type=\"text/css\">
		.brdls {
			font-size:12px;
			font-family:Tahoma;
			border-collapse:collapse;
		}
		.titlebar {
			border-top:1px solid silver;
			border-bottom:1px solid silver;
		}
		.num {
			font-family:Verdana;
			font-size:11px;
		}
		.ustr {
			font-family:돋움;
			font-sizt:11px;
		}
		.estr {
			font-family:Tahoma;
			font-size:11px;
		}
		.txtbox1 {
			border:1px solid #E0E0E0;
			font-size:12px;
			font-family:굴림,Verdana;
			height:20px;
		}
		</style>
		<table width=\"{$width}\" align=\"{$align}\" class=\"brdls\">
		<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?mode=List\">
			<tr>
				<td><img src=\"/{$FSBOARD_PATH}/img/clip/this.gif\" alt=\"icon\" /> ".($srhctgr&&$srhstr?"검색된 게시판":"설치된 게시판")." : <b>{$numrows}</b></td>
				<td align=\"right\"><a href=\"".$_SERVER["PHP_SELF"]."?mode=CreateBoard\" class=\"lnk_def\"><img src=\"/{$FSBOARD_PATH}/img/clip/docn.gif\" alt=\"icon\" style=\"vertical-align:middle;\" /> 새 게시판 생성</a>&nbsp;</td>
			</tr>
		</table>
		<table width=\"{$width}\" align=\"{$align}\" class=\"brdls\" cellpadding=\"1\" cellspacing=\"0\" border=\"1\" bordercolor=\"#E0E0E0\">
			<tr align=\"center\" bgcolor=\"#FAFBF7\">
				<td>번호</td>
				<td align=\"left\" style=\"padding-left:5px;\">
					-게시판 아이디<br />
					-게시판 이름
				</td>
				<td>스킨</td>
				<td>
					-공지글수<br />
					-게시글수
				</td>
				<td>
					-사용여부-<br />
					답변/댓글/비밀
				</td>
				<td>
					-사용여부-<br />
					HTML/자료실/카테고리
				</td>
				<td>
					-보기레벨-<br />
					리스트/내용
				</td>
				<td>
					-쓰기레벨-<br />
					새글/답변/댓글
				</td>
				<td>
					-오늘방문<br />
					-전체방문
				</td>
				<td>
					-생성일자<br />
					-수정일자
				</td>
				<td>관리</td>
				<td>삭제</td>
			</tr>
		";

		if($numrows) {
			$i = 1;
			while($rs = mysql_fetch_array($result)) {
				$aidx = $rs["aidx"];
				$boardId = $rs["boardId"];
				$boardName = $rs["boardName"];
				$skin = $rs["skin"];
				$totalObj = $rs["totalObj"];
				$noticeNum = $rs["noticeNum"];
				$useReply = $rs["useReply"];
				$useMemo = $rs["useMemo"];
				$useSecret = $rs["useSecret"];
				$useHtml = $rs["useHtml"];
				$useAttachFile = $rs["useAttachFile"];
				$useCategory = $rs["useCategory"];
				$levelList = $rs["levelList"];
				$levelView = $rs["levelView"];
				$levelWrite = $rs["levelWrite"];
				$levelReply = $rs["levelReply"];
				$levelMemoWrite = $rs["levelMemoWrite"];
				$levelNoticeWrite = $rs["levelNoticeWrite"];
				$levelUseHtml = $rs["levelUseHtml"];
				$levelDelete = $rs["levelDelete"];
				$levelSecret = $rs["levelSecret"];
				$todayCount = $rs["todayCount"];
				$totalCount = $rs["totalCount"];
				$regDate = $rs["regDate"];
				$editDate = $rs["editDate"];
				$currDate = $rs["currDate"];

				$use_reply = $useReply ? "○" : "×";
				$use_memo = $useMemo ? "○" : "×";
				$use_secret = $useSecret ? "○" : "×";
				if($useHtml=="part") $use_html="부분허용"; else if($useHtml=="block") $use_html="허용안함"; else if($useHtml=="permit") $use_html="전부허용"; else $useHtml="완전허용";
				$use_attachfile = $useAttachFile ? "○" : "×";
				$use_category = $useCategory ? "○" : "×";

				$todayCount = date("Ymd")!=date("Ymd",$currDate) ? 0 : $todayCount;

				if($regDate) $reg_date = date("Y.m.d",$regDate);
				if($editDate) $edit_date = date("Y.m.d",$editDate);

				echo "
			<tr align=\"center\" onmouseover=\"this.bgColor='#FAFAFA';\" onmouseout=\"this.bgColor='';\">
				<td>$sequence</td>
				<td align=\"left\" style=\"padding-left:5px;\" class=\"estr\">
					<a href=\"/$FSBOARD_PATH/index.php?id={$boardId}\" onclick=\"window.open(this.href,'_blank'); return false;\" class=\"lnk_def\">
					$boardId</a><br />
					$boardName
				</td>
				<td class=\"estr\">$skin</td>
				<td align=\"right\" style=\"padding-right:5px;\" class=\"num\">
					<span style=\"color:gray;\">$noticeNum</span><br />
					$totalObj
				</td>
				<td class=\"ustr\">
					$use_reply <span style=\"color:silver;\">/</span>
					$use_memo <span style=\"color:silver;\">/</span>
					$use_secret
				</td>
				<td class=\"ustr\">
					$use_html <span style=\"color:silver;\">/</span>
					$use_attachfile <span style=\"color:silver;\">/</span>
					$use_category
				</td>
				<td class=\"num\">
					$levelList <span style=\"color:silver;\">/</span>
					$levelView
				</td>
				<td class=\"num\">
					$levelWrite <span style=\"color:silver;\">/</span>
					$levelReply <span style=\"color:silver;\">/</span>
					$levelMemoWrite
				</td>

				<td align=\"right\" style=\"padding-right:5px;\" class=\"num\">
					$todayCount<br />
					<span style=\"color:gray;\">$totalCount</span>
				</td>
				<td class=\"estr\">
					<span style=\"color:gray;\">$reg_date</span><br />
					$edit_date
				</td>
				<td>
					<a href=\"/{$FSBOARD_PATH}/{$_fsMainExecFile}?id={$boardId}&mode=admin\"><img src=\"/{$FSBOARD_PATH}/img/clip/admin.gif\" alt=\"관리\" /></a>
				</td>
				<td>
					<a href=\"".$_SERVER["PHP_SELF"]."?mode=DeleteBoardNameOnly&aidx={$aidx}&boardId={$boardId}\"><img src=\"/{$FSBOARD_PATH}/img/clip/xbutton_small.gif\" alt=\"게시판 이름만 목록에서 제거\" /></a>
					<a href=\"".$_SERVER["PHP_SELF"]."?mode=RemoveBoard&aidx={$aidx}&boardId={$boardId}\"><img src=\"/{$FSBOARD_PATH}/img/clip/xbutton.gif\" alt=\"게시판 완전 삭제\" /></a>
				</td>
			</tr>
				";

				$i++;
				$sequence--;
			}
		}
		else {
			if($srhctgr&&$srhstr) {
				echo"
			<tr>
				<td colspan=\"12\" align=\"center\" height=\"30\">검색된 게시판이 없습니다.</td>
			</tr>
				";
			} else {
				echo "
			<tr>
				<td colspan=\"12\" align=\"center\" height=\"30\">설치된 게시판이 없습니다.</td>
			</tr>
				";
			}
		}
		mysql_free_result($result);

		echo "
		</table>
		<table width=\"{$width}\" align=\"{$align}\" class=\"defstyle\">
			<tr>
				<td align=\"center\">".NavPage($page,$divpage,$totalpage,"mode=List".($srhctgr&&$srhstr?"&srhctgr={$srhctgr}&srhstr={$srhstr}":"")," class=\"num\""," <img src=\"/{$FSBOARD_PATH}/img/clip/arrow_left.gif\" alt=\"<\" style=\"vertical-align:middle;\" /> , <img src=\"/{$FSBOARD_PATH}/img/clip/arrow_right.gif\" alt=\">\" style=\"vertical-align:middle;\" /> "," , ")."</td>
			</tr>
			<tr>
				<td align=\"center\">
					<select name=\"srhctgr\" class=\"defstyle\">
						<option value=\"boardId\">게시판 ID</option>
						<option value=\"boardName\">게시판 이름</option>
					</select><input type=\"text\" size=\"20\" name=\"srhstr\" class=\"txtbox1\" /><input type=\"image\" src=\"/$FSBOARD_PATH/img/btn/search.gif\" />
					".($srhctgr&&$srhstr?"<a href=\"".$_SERVER["PHP_SELF"]."?mode=List\"><img src=\"/{$FSBOARD_PATH}/img/btn/list.gif\" alt=\"목록\" style=\"vertical-align:middle;\" /></a>":"")."
				</td>
			</tr>
		";
		/*if(!$combinedDesign) {
			echo "
			<tr>
				<td align=\"center\" height=\"50\">
					<a href=\"".$_SERVER["PHP_SELF"]."?mode=CreateBoard\" class=\"lnk_def\"><img src=\"/{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" style=\"vertical-align:middle;\" />게시판생성</a>
					&nbsp;
					<a href=\"/$FSBOARD_PATH/lib/members.php?mode=Admin.MemList\" class=\"lnk_def\"><img src=\"/{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" style=\"vertical-align:middle;\" />회원관리</a>
					&nbsp;
					<a href=\"/$FSBOARD_PATH/lib/logout.php\" class=\"lnk_def\"><img src=\"/{$FSBOARD_PATH}/img/clip/doc3.gif\" alt=\"icon\" />로그아웃</a>
				</td>
			</tr>
			";
		}*/
		echo "
		</form>
		</table>
		";

		setup_foot();
	}

	include $_SERVER["DOCUMENT_ROOT"]."/wb_admin/admin_bottom.php";


} else if($MODE == "RemoveBoard") { ////////////////////////////////////////////////////////////////////// 게시판 제거

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("/"); exit; }

	$aidx = $_GET["aidx"];
	$boardId = $_GET["boardId"];
	
	setup_head();
	echo "
		<br />
		<br />
		<table width=\"450\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\" bgcolor=\"#FFFFFF\" style=\"border:1px solid #E1E1E1;\" class=\"defstyle\">
			<tr>
				<td align=\"center\" height=\"25\" bgcolor=\"#FAFBF7\" style=\"font-family:Verdana\"><b>ATTENTION</b></td>
			</tr>
			<tr>
				<td height=\"100\" align=\"center\">
					삭제하려는 게시판이 <b>$boardId</b> 이(가) 맞는지 한번 더 확인해 보세요.<br />
					한번 삭제된 데이터는 다시 복구할 수 없습니다.<br />
					삭제하려면 <b>확인</b>을 클릭하세요.<br />
					<br />
				</td>
			</tr>
		</table>
		<table width=\"400\" align=\"center\">
			<tr>
				<form>
				<td align=\"center\">
					<img src=\"/{$FSBOARD_PATH}/img/btn/submit.gif\" onclick=\"if(confirm('게시판 `$boardId`(을)를 정말 제거하시겠습니까?')){window.location.href='?mode=RemoveBoardExec&aidx={$aidx}&boardId={$boardId}'; }else{window.history.back();}\" alt=\"확인\" style=\"cursor:hand;\" />
					<img src=\"/{$FSBOARD_PATH}/img/btn/cancel.gif\" onclick=\"window.history.back();\" alt=\"취소\" style=\"cursor:hand;\" />
				</td>
				</form>
			</tr>
		</table>
	";
	setup_foot();

















} else if($MODE == "RemoveBoardExec") { ////////////////////////////////////////////////////////////////////// 게시판 제거 처리

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("/"); exit; }

	$aidx = $_GET["aidx"];
	$boardId = $_GET["boardId"];

	$_table_id_board = "_board_".$boardId;
	//$_table_id_comment = "_board_cmt_".$boardId;

	if(!SeekTable($_table_id_board)) { Error("삭제하려는 DB테이블 ".$_table_id_board." 이(가) 존재하지 않습니다."); exit; }
	//if(!SeekTable($_table_id_comment)) { Error("삭제하려는 DB테이블 ".$_table_id_comment." 이(가) 존재하지 않습니다."); exit; }

	$query = "SELECT dataPath FROM {$_table_id_admin} WHERE aidx={$aidx} AND boardId='{$boardId}';";
	$result = mysql_query($query) or Error(mysql_error());
	if($result) {
		if(mysql_num_rows($result)) {
			$rs = mysql_fetch_row($result);
			$dataPath = $rs[0];
			$dataPath = $_SERVER["DOCUMENT_ROOT"]."/{$FSBOARD_PATH}{$dataPath}/{$boardId}";
			if(is_dir($dataPath)) {
				RemoveDir($dataPath); //첨부파일 및 첨부파일디렉토리 삭제
			}
		}
		else {
			Error("게시판 관리정보가 없습니다.");
			exit;
		}
		mysql_free_result($result);
	}
	else {
		Error("게시판 관리 정보를 가져올수 없습니다.");
		exit;
	}

	//게시판 관리 테이블에서 게시판 정보 제거
	$query = "DELETE FROM {$_table_id_admin} WHERE boardId='{$boardId}' AND aidx={$aidx};";
	mysql_query($query) or Error(mysql_error());

	//게시판 테이블 제거
	$query = "DROP TABLE ".$_table_id_board.";";
	mysql_query($query) or Error(mysql_error());

	MovePage($_SERVER["PHP_SELF"]."?mode=List");

















} else if($MODE == "DeleteBoardNameOnly") { ////////////////////////////////////////////////////////////////////// 게시판 이름만 제거

	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("/"); exit; }

	$aidx = $_GET["aidx"];
	$boardId = $_GET["boardId"];

	echo "
		<script type=\"text/javascript\">
		<!--
		if(!confirm('게시판의 관리정보 및 게시판의 이름만 리스트에서 삭제되고\\n실제 DB테이블은 데이터베이스에 계속남아있게 됩니다.\\n게시판은 정상적으로 사용할수 없게 됩니다.\\n\\n게시판 목록에서 게시판의 이름만 삭제하시겠습니까?')) {
			window.history.back();
		}
		else {
			window.location.href = '?mode=DeleteBoardNameOnlyExec&aidx={$aidx}>&boardId={$boardId}';
		}
		// -->
		</script>
	";



















} else if($MODE == "DeleteBoardNameOnlyExec") { ////////////////////////////////////////////////////////////////////// 게시판 이름만 제거처리


	//관리자만 접근허용
	$MemId = $_SESSION["MemId"];
	$MemLevel = $_SESSION["MemLevel"];
	if(!$MemId || $MemLevel>1) { MovePage("/"); exit; }

	//게시판 관리 테이블에서 게시판 정보 제거
	$query = "DELETE FROM {$_table_id_admin} WHERE boardId='{$boardId}' AND aidx={$aidx};";
	mysql_query($query) or Error(mysql_error());

	MovePage($_SERVER["PHP_SELF"]."?mode=List");




















} else { Error("Invalid Mode."); } if($dbConnect) { @mysql_close($dbConnect); unset($dbConnect); }
?>