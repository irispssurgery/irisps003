<?
/**************************************************
 Members 초기화
**************************************************/

	//라이브러리 포함
	include_once $INC_PATH."lib.php";

	//게시판 웹 절대경로
	$FSBOARD_PATH = "/".$FSBOARD_PATH;

	//현재 실행파일의 디자인적용 확인
	$combinedDesign = (!ereg("agree.php",$_SERVER["PHP_SELF"])) ? true : false;

	//변수초기화
	$MODE = trim($_GET["mode"]);
	$CHK = trim($_GET["chk"]);

	//DB연결
	$dbConnect = DbConn();

/**************************************************
 전용 함수
**************************************************/

	//관리자 상단 기본내용
	function members_head() {
		global $combinedDesign, $FSBOARD_PATH;
		echo !$combinedDesign ? DclrDocType() : fslib();
		echo "
		<style type=\"text/css\">
		img { border:0; }
		.defstyle { font-size:12px; font-family:돋움, Verdana; }
		a.lnk_def:link, a.lnk_def:visited { color:#000; text-decoration:none; }
		a.lnk_def:hover, a.lnk_def:active { color:#00f; text-decoration:underline; }
		.txtbox { border:1px solid #E0E0E0; height:20px; background-color:#FBFAF7; font-size:12px; }
		.txtbox1 { border:1px solid #E0E0E0; height:20px; background-color:#FBFAF7; width:100%; font-size:12px; }
		.txtbox2 { border:1px solid #DBDBDB; background-color:#FBFAF7; font-size:12px; width:99%; overflow:auto; }
		</style>
		";
		echo "<script type=\"text/javascript\" src=\"".$FSBOARD_PATH."/lib/javascript.php\"></script>\n";
		if(!$combinedDesign) {
			echo "\n</head>\n<body>\n";
		}
		
	}

	//관리자 하단 기본내용
	function members_foot() {
		echo "\n</body>\n</html>";
	}


	$btnSubmit = "<img src=\"{$FSBOARD_PATH}/img/btn/submit.gif\" onclick=\"sendfrm();\" onkeypress=\"enterCheck(this,event);\" title=\"확인\" style=\"cursor:hand;\" />";
	$btnCancel = "<a href=\"{$btnCancel}\"><img src=\"{$FSBOARD_PATH}/img/btn/cancel.gif\" title=\"취소\" /></a>";


	members_head();
	
	if($mode == "agree") { 
?>
		<style type="text/css">
		#member_join_layout { float:left; width:<?=$width?>; margin:0 auto; padding:0.28em; font-size:9pt; }
			#stipulation { height:13em; padding:0.5em; overflow-y:auto; border:1px solid #e7e7e7; }
				#stipulation h4 { margin:0 auto; font-size:1.11em; text-align:center; }
				#stipulation h5 { margin-bottom:0.3em; font-size:1em; }
				#stipulation ul { margin:0; padding-left:1em; padding-bottom:0.3em; }
			#title_img {clear:both;}
			#stipulation1 { clear:both; height:13em; padding:0.5em; overflow-y:auto; border:1px solid #e7e7e7; }
				#stipulation1 h4 { margin:0 auto; font-size:1.11em; text-align:center; }
				#stipulation1 h5 { margin-bottom:0.3em; font-size:1em; }
				#stipulation1 ul { margin:0; padding-left:1em; padding-bottom:0.3em; }
			#stipulation_chk { float:left; height:4em;}
			#member_join_main { clear:both; padding-bottom:2.5em; border-top:1px solid #e7e7e7; border-bottom:1px solid #e7e7e7; }
				.member_join_cr { clear:both; margin:0.1em; background-color:#fff; border-bottom:1px solid #e1e1e1; }
				.member_join_el { clear:both; margin:0.1em; }
					.member_join_mleft { float:left; width:7.5em; padding:0.4em; }
					.member_join_mright { float:left; padding:0.4em; }
			#member_join_btn { clear:both; margin:0.5em; text-align:center; }
		</style>
		<form id="__ctl1" name="__ctl1" method="post" enctype="multipart/form-data" action="<?=$_SERVER["PHP_SELF"]?>?mode=ssn_chk">
		<input type="hidden" name="chk" value="<?=$chk?>">
		<div id="member_join_layout">
			<div id="title_img"><img src="image/join_text1.gif" alt="회원가입약관" /></div>
			<div id="stipulation">
				<strong>제1장 총 칙</strong><br>
				<br>
				제1조 (목적)<br>
				<br>
				본 약관은 대우병원에서 제공하는 인터넷 관련 정보서비스(이하 &quot;서비스&quot;라 합니다)를 
				이용함에 있어 그 이용조건 및 절차와 대우병원 이용자의 권리, 의무 및 책임 등에 관한 필요한 사항을 
				규정함을 목적으로 합니다.<br>
				<br>
				제2조 (약관의 효력과 변경)<br>
				<br>
				① 본 약관의 내용은 서비스 화면에 게시하거나 기타의 방법으로 회원에게 공지함으로써 효력을 발생합니다. 
				<br>
				② 본 약관의 내용은 서비스의 일부 화면 또는 기타 방법등에 의하여 이를 공지하거나 그 내용을 사이트에 
				방문하는 회원에게 통지함으로써 그 효력이 발생됩니다.<br>
				③ 대우병원은 운영 상 중요한 사유가 있을 경우 약관을 변경할 수 있으며, 변경된 약관은 제2항과 같은 
				방법으로 공지함으로써 효력을 발생합니다. <br>
				④ 회원은 변경된 약관에 동의하지 않을 시 회원탈퇴를 요청할 수 있으며, 변경된 약관의 효력발생일 이후에도 
				서비스를 계속 사용할 경우 약관의 변경사항에 동의한 것으로 간주됩니다.<br>
				<br>
				제3조 (약관 외 준칙)<br>
				<br>
				본 약관에 명시되지 않은 사항은 공공기관의 개인정보 보호에 관한 법률, 전기통신기본법, 전기통신사업법, 
				<br>
				정보통신윤리위원회 심의규정, 정보통신 윤리강령, 정보통신망 이용촉진 및 정보보호 등에 관한 법률 및 기타 
				관련 법령, 대우병원이 별도로 정한 지침 등의 규정에 따릅니다. <br>
				제4조 (용어의 정의)<br>
				본 약관에서 사용하는 용어의 정의는 다음과 같습니다.<br>
				이 약관에서 사용하는 용어의 정의는 다음과 같습니다. 
				<p>1. 회원 : 대우병원과 서비스 이용에 관한 계약을 체결한 자 <br>
				  2. 아이디(ID) : 회원 식별과 회원의 서비스 이용을 위하여 회원이 선정하고 대우병원이 승인하는 
				  문자와 숫자의 조합 <br>
				  3. 비밀번호 : 회원이 통신상의 자신의 비밀을 보호하기 위해 선정한 문자와 숫자의 조합 <br>
				  4. 해지 : 대우병원 또는 회원이 서비스 이용 이후 그 이용계약을 종료 시키는 의사표시 </p>
				<br>
				<br>
				<p><strong>제2장 서비스 이용계약 </strong><br>
				  <br>
				  제1조 이용 계약의 성립 <br>
				  <br>
				  1. 이용계약은 회원의 이용신청에 대하여 대우병원이 승낙함으로써 성립합니다. <br>
				  2. 회원에 가입하여 서비스를 이용하고자 하는 희망자는 대우병원에서 요청하는 개인신상정보를 제공해야 
				  합니다. <br>
				  3. 이용계약은 회원 1인당 1개의 ID로 체결하는 것을 원칙으로 합니다. <br>
				  제2조 이용신청 이용신청은 온라인상의 가입신청 양식에 준합니다. <br>
				  제3조 이용신청의 승낙 <br>
				  대우병원은 회원이 제2장 제1조에서 정한 모든 사항을 정확히 기재하여 이용신청을 하였을 때 승낙합니다. 
				  <br>
				  제4조 이용신청의 불승낙 </p>
				<p>1. 회사는 다음에 해당하는 이용신청에 대하여 등록을 거부하거나 등록 후에라도 회원에게 고지하지 않고 
				  회원정보를 수정 또는 삭제할 수 있습니다.<br>
				  1) 다른 사람의 명의를 사용하여 신청하였을 경우<br>
				  2) 이용신청시 필요내용을 허위로 기재하여 신청하였을 경우 <br>
				  3) 사회의 안녕질서 및 미풍양속을 저해할 목적으로 신청하였을 경우 <br>
				  4) 기타 이용신청자의 귀책사유로 이용승낙이 곤란한 경우 </p>
				<p>2. 회사는 다음에 해당하는 이용신청에 대하여 승낙 제한 사유가 해소될 때까지 승낙을 하지 않을 수 
				  있습니다.<br>
				  1) 회사가 설비의 여유가 없는 경우<br>
				  2) 회사의 기술상 지장이 있는 경우 <br>
				  3) 기타 회사가 필요하다고 인정되는 경우 </p>
				<p>3. 대우병원은 이용신청이 불승낙 되거나 승낙을 제한하는 경우에는 이를 이용신청자에게 알려야 합니다. 
				</p>
				<p>제5조 계약사항의 변경 회원은 이용신청시 기재한 사항이 변경되었을 경우에는 온라인 수정을 해야 합니다.<br>
				  제6조 회원정보의 공유 <br>
				  1. 대우병원은 더 좋은 서비스를 위하여 타 업체와 제휴, 인수, 분사, 합병 시 회원의 정보는 
				  공유될 수 있다.<br>
				  2. 각종 경품이 제공되는 이벤트의 경우, 경품 협찬사와의 협의에 의해 당첨자 등록정보를 공유할 수 
				  있습니다. <br>
				  3. 1항, 2항의 사유가 발생할 경우 회사는 회원에게 해당 사실을 공지해야 합니다. </p>
				<p>제7조 추가적인 회원정보의 사용 <br>
				  대우병원은 회원이 건강서비스, 온라인 상담 등을 이용시 제공하는 회원의 추가 정보(회원의 개인 일정, 
				  거래내역 등)를 관리용, 통계용의 정보로만 사용합니다. </p>
				<p>제8조 정보의 제공 <br>
				  대우병원은 회원이 서비스 이용 및 회사의 각종 행사 또는 정보서비스에 대해서는 전자우편이나 서신우편 
				  등의 방법으로 회원들에게 제공할 수 있습니다. </p>
				<p>제9조 개인정보의 보호<br>
				  1. 이용자의 개인정보를 수집할 때 반드시 당해 이용자의 동의를 받습니다.<br>
				  2. 개인정보를 내부 관리용, 통계용 및 제2장 제6조 이외의 용도로 이용하거나 이용자의 동의 없는 
				  제3자에게 제공, 분실, 도난, 유출, 변조시 그에 따른 이용자의 피해에 대한 모든 책임은 회사가 집니다. 
				</p>
				<br>
				<br>
				<p><strong>제3장 서비스 이용</strong><br>
				  <br>
				  제1조 서비스 이용 <br>
				  <br>
				  서비스 이용은 회사의 업무상 또는 기술상 특별한 지장이 없는 한 연중무휴, 1일 24시간을 원칙으로 
				  합니다. 단, 정기점검 등 서비스 개선을 위하여 대우병원에서 필요하다고 인정되는 때에는 미리 공지한 
				  후 서비스가 일시 중지될 수 있습니다. </p>
				<p>제2조 서비스 제공의 중지 <br>
				  <br>
				  대우병원은 다음 항에 해당하는 경우 서비스의 제공을 중지할 수 있습니다.<br>
				  1. 설비의 보수 등을 위하여 부득이한 경우 <br>
				  2. 전기통신사업법에 규정된 기간통신사업자가 전기통신서비스를 중지하는 경우<br>
				  3. 기타 귀사가 서비스를 제공할 수 없는 사유가 발생한 경우 </p>
				<p>제3조 파일정보의 소거 및 회원 권한의 삭제 <br>
				  <br>
				  1. 대우병원은 서비스용 설비의 용량에 여유가 없다고 판단되면 필요에 따라 회원의 정보 및 신상정보를 
				  삭제할 수 있습니다.<br>
				  2. 대우병원은 서비스 운영상 또는 보안에 문제가 있다고 판단되는 회원의 정보 및 신상정보를 사전통지 
				  없이 검색할 수 있습니다. <br>
				  3. 제1항의 경우에 대우병원은 해당 사항을 사전에 서비스 또는 전자우편을 통하여 공지합니다. </p>
				<br>
				<br>
				<p><strong>제4장 서비스 사용 제한 및 계약 해지 </strong></p>
				<p>제1조 서비스 사용 제한 </p>
				<p>1. 회원은 서비스의 사용에 있어서 다음 각 호에 해당되지 않도록 하여야 하며, 이에 해당하는 경우 
				  서비스 사용을 제한할 수 있습니다. <br>
				  1) 다른 회원의 아이디(ID)를 부정 사용하는 행위 <br>
				  2) 범죄행위를 목적으로 하거나 기타 범죄행위와 관련된 행위 <br>
				  3) 선량한 풍속, 기타 사회질서를 해하는 행위 <br>
				  4) 타인의 명예를 훼손하거나 모욕하는 행위 <br>
				  5) 타인의 지적재산권 등의 권리를 침해하는 행위 <br>
				  6) 해킹행위 또는 컴퓨터바이러스의 유포행위 <br>
				  7) 타인의 의사에 반하여 광고성 정보 등 일정한 내용을 지속적으로 전송하는 행위 <br>
				  8) 서비스의 안전적인 운영에 지장을 주거나 줄 우려가 있는 일체의 행위 <br>
				  9) 기타 관계법령에 위배되는 행위 <br>
				</p>
				<p>제2조 계약 해지</p>
				<p>1. 회원이 이용계약을 해지하고자 하는 때에는 본인이 서비스 또는 전자우편을 통하여 해지하고자 하는 
				  날의 1일전까지(단, 해지일이 법정공휴일인 경우 공휴일 개시 2일전까지) 이를 회사에 신청하여야 합니다.<br>
				  2. 대우병원은 회원이 제4장 제1조의 내용을 위반하고, 대우병원 소정의 기간 이내에 이를 해소하지 
				  아니하는 경우 서비스 이용계약을 해지할 수 있습니다.<br>
				  3. 대우병원은 제2항에 의해 해지된 회원이 다시 이용신청을 하는 경우 일정기간 그 승낙을 제한할 
				  수 있습니다. </p>
				<br>
				<br>
				<p><strong>제5장 책 임</strong><br>
				  <br>
				  제1조 회원의 의무<br>
				  1. 회원아이디(ID) 및 비밀번호에 관한 모든 관리의 책임은 회원에게 있습니다. <br>
				  2. 회원아이디(ID) 및 비밀번호는 대우병원은 사전승낙 없이는 다른 사람에게 양도, 임대, 대여할 
				  수 없습니다.<br>
				  3. 자신의 회원아이디(ID)가 부정하게 사용된 경우, 회원은 반드시 대우병원에 그 사실을 통보해야 
				  합니다. <br>
				  4. 회원은 이용신청서의 기재내용 중 변경된 내용이 있는 경우 서비스를 통하여 그 내용을 회사에 통지하여야 
				  합니다.<br>
				  5. 회원은 이 약관 및 관계법령에서 규정한 사항을 준수해야 합니다.</p>
				<p>제2조 회사의 의무 </p>
				<p>1. 대우병원은 제3장 제1조 및 제2조에서 정한 경우를 제외하고 이 약관에서 정한 바에 따라 
				  회원이 신청한 서비스 제공 개시일에 서비스를 이용할 수 있도록 합니다.<br>
				  2. 대우병원은 이 약관에서 정한 바에 따라 계속적, 안정적으로 서비스를 제공할 의무가 있습니다. 
				  단, 부득이한 경우로 회원 개인정보의 손실이 발생했을 지라도 대우병원은 전혀 책임지지 않습니다. 
				  <br>
				  3. 대우병원은 회원의 개인신상정보를 본인의 승낙 없이 타인에게 누설, 배포하지 않습니다. 단, 
				  전기통신관련법령 등 관계법령에 의해 국가기관 등의 요구가 있는 경우에는 그러하지 않습니다.<br>
				  4. 대우병원은 회원으로부터 제기되는 의견이나 불만이 정당하다고 인정되는 경우에는 즉시 처리해야 
				  합니다. 다만 즉시 처리가 곤란한 경우에는 회원에게 그 사유와 처리일정을 통보하여야 합니다. <br>
				  제3조 게시물 또는 내용물의 삭제<br>
				  대우병원은 서비스의 게시물 또는 내용물이 제4장 제1조의 규정에 위반되거나 대우병원은 소정의 
				  게시기간을 초과하는 경우 사전 통지나 동의 없이 이를 제할 수 있습니다. <br>
				  제6장 손해배상 및 면책조항</p>
				<p>제1조 손해배상<br>
				  <br>
				  대우병원은 서비스 이용과 관련하여 회원에게 어떠한 손해가 발생하더라도 동 손해가 회사의 중대한 과실에 
				  의한 경우를 제외하고 이에 대하여 책임을 지지 않습니다. <br>
				  제2조 면책조항 <br>
				  1. 대우병원은 천재지변 또는 이에 준하는 불가항력으로 인하여 서비스를 제공할 수 없는 경우에는 
				  서비스 제공에 관한 책임이 면제됩니다.<br>
				  2. 대우병원은 회원의 귀책사유로 인한 서비스 이용의 장애에 대하여 책임을 지지 않습니다. <br>
				  3. 대우병원은 회원이 서비스를 이용하여 기대하는 손익이나 서비스를 통하여 얻은 자료로 인한 손해에 
				  관하여 책임을 지지 않습니다. <br>
				  4. 대우병원은 회원이 서비스에 게재한 정보, 자료, 사실의 신뢰도, 정확성 등 내용에 관하여는 
				  책임을 지지 않습니다. <br>
				  5. 회원 아이디(ID)와 비밀번호의 관리 및 이용상의 부주의로 인하여 발생되는 손해 또는 제3자에 
				  의한 부정사용 등에 대한 책임은 모두 회원에게 있습니다. <br>
				  6. 회원이 제4장 제1조, 기타 이 약관의 규정을 위반함으로 인하여 대우병원이 회원 또는 제3자에 
				  대하여 책임을 부담하게 되고, 이로써 대우병원에게 손해가 발생하게 되는 경우, 이 약관을 위반한 
				  회원은 대우병원에게 발생하는 모든 손해를 배상하여야 하며, 동 손해로 부터 대우병원은 면책시켜야 
				  합니다.</p>
				<br>
				<br>
				<p><strong>제6장 기 타</strong><br>
				  <br>
				  제1조 약관의 변동 <br>
				  <br>
				  약관의 변동에 대해서 대우병원은 반드시 본 서비스의 홈페이지를 통하여 최소 1주일동안 회원에게 공지해야 
				  합니다. <br>
				  제2조 분쟁의 해결 <br>
				  1. 서비스 이용과 관련하여 대우병원과 회원사이에 분쟁이 발생한 경우, 쌍방간에 분쟁의 해결을 위해 
				  성실히 협의한 후가 아니면 제소할 수 없습니다.<br>
				  2. 제1항의 규정에도 불구하고, 동 분쟁으로 인하여 소송이 제기될 경우 동 소송은 대우병원의 해당 
				  소재지를 관할하는 관할법원으로 합니다. <br>
				  본 약관에서 정하지 아니한 사항과 본 약관의 해석에 관하여는 국내 관련 법령과 기타 상관습에 의합니다.</p>
				<p><br>
				  부 칙<br>
				  제1조(시행일) <br>
				  본 약관은 2006년 11월1일부터 시행합니다.<br>
				</p>
			</DIV>	
			<div id="stipulation_chk"><label for="agreement"><input type="checkbox" id="agreement" name="agreement" value="" />회원약관에 동의</label></div>
							  

			<div id="title_img"><img src="image/join_text2.gif" alt="개인정보취급방침" /></div>
			<div id="stipulation1">
				<strong>1. 개인정보의 수집 목적 및 이용 목적</strong><br>
				<br>
				대우병원은 이용자 여러분들이 양질의 서비스와 컨텐츠를 제공받을 수 있도록 회원님의 개인정보를 수집, 
				이용하고 있습니다. 홈페이지를 통해 한의원의 정보를 무료로 제공해 드리 수집한 정보를 바탕으로 회원님들의 
				관심정보를 분석, 연구하여 회원님들에게 더욱 가치있는 맞춤 서비스를 제공하고자 합니다. <br> <br>
				회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.. <br>
				ο 회원 관리 <br>
				회원제 서비스 이용에 따른 본인확인 , 개인 식별 , 불량회원의 부정 이용 방지와 비인가 사용 방지 , 가입 의사 확인 , 불만처리 등 민원처리
				<br>
				<br>
				<strong>2. 개인정보 수집 항목</strong><br>
				<br>
				대우병원은 회원가입, 상담, 서비스 신청 등등을 위해 아래와 같은 개인정보를 수집하고 있습니다.<br>

				ο 수집항목 : 이름 , 생년월일 , 성별 , 로그인ID , 비밀번호 , 비밀번호 질문과 답변 , 자택 전화번호 , 자택 주소 , 휴대전화번호 , 이메일 , 직업 , 주민등록번호 , 접속 로그 , 쿠키 , 접속 IP 정보<br>
				ο 개인정보 수집방법 : 홈페이지(회원가입,상담게시판,게시판 등) 
				<br>
				<br>
				<strong>3. 개인정보의 이용 및 보유 기간</strong><br>
				<br>
				회원님의 동의를 통해 수집된 개인정보는 회원님이 ‘대우병원 홈페이지’에서 서비스를 받는 동안 대우병원 
				홈페이지가 지속적으로 보유하며, 회원님께 대우병원 홈페이지에서의 서비스를 지원하기 위하여 이용하게 됩니다. 
				회원님의 탈퇴요청 및 자격 상실 시에는 즉시 삭제되며, 어떤 이유나 방법으로도 재생되거나, 이용할 수 없도록 
				처리됩니다. 
			</DIV>
			<div id="stipulation_chk"><label for="agreement1"><input type="checkbox" id="agreement1" name="agreement1" value="" />회원약관에 동의</label></div>

			<div id="member_join_btn"><?echo $btnSubmit." ".$btnCancel;?></div>

		</div>
		</form>
		<script type="text/javascript">
		//<![CDATA[
		function sendfrm() {
			var frm = document.forms['__ctl1'];
			if(!frm.agreement.checked) {
				window.alert("회원약관을 반드시 모두 읽어 보시고 동의해 주세요.");
				frm.agreement.focus();
				document.body.scrollTop = 0;
				return;
			}else if(!frm.agreement1.checked) {
				window.alert("개인정보 수집 및 이용을  반드시 모두 읽어 보시고 동의해 주세요.");
				frm.agreement1.focus();
				document.body.scrollTop = 0;
				return;
			}
			else {
				if(checkValue(document.forms['__ctl1'])) frm.submit();
			}
		}

		/*/Netscape일 경우 레이어가 깨지는 현상때문에 숨김
		if(window.navigator.appName.indexOf('Netscape')==0) {
			document.getElementById('agreement').checked = true;
			document.getElementById('stipulation').style.display = 'none';
			document.getElementById('stipulation_chk').style.display = 'none';
		}*/

		//]]>
		</script>

<?
	}else if($mode == "ssn_chk"){	
?>
		<style type="text/css">
		#title_img {clear:both;}

		</style>
		<div id="title_img"><img src="image/join_t2.gif" alt="회원님의 이름과 주민등록번호는 실명확인을 목적으로 사용하고 있으며, 동의없이 제3자에게 공개하지 않습니다." /></div>
		<div id="title_img"><img src="image/join_text3.gif" alt="회원가입확인" /></div>
		<div id="outer" style="height:154px">
			<div id="outer-line">
				<div id="outer-pad" style="height:144px">
					<table width="580" style="margin:29px 0 0 11px" border="0">
					<form name="frmChk" method="post"  enctype="multipart/form-data" action="member_2.html?mode=join">
						<tr valign="top" height="36">
							<td><img src="<?=$FSBOARD_PATH?>/img/mem/login_find_head1.gif" title="이름" style="margin-top:8px" /></td>
							<td><input type="text" style="width:120px;" class="input-login" id="mem_name" name="mem_name" maxlength="20" /></td>
							<td><img src="<?=$FSBOARD_PATH?>/img/mem/login_find_head2.gif" title="주민등록번호" style="margin-top:8px" /></td>
							<td><input type="text" id="mem_idsn1" name="mem_idsn1" maxlength="6" style="width:58px;" class="input-login" /><span style="color:#000000"> - </span><input type="password" style="width:94px;" class="input-login" id="mem_idsn2" name="mem_idsn2" maxlength="7" onKeyPress="if(event.keyCode==13){authChk();}" /></td>
							<td><a href="javascript:authChk();" onkeypress="enterCheck(this,event);" ><img src="<?=$FSBOARD_PATH?>/img/mem/btn_confirm.gif" style="margin-top:1px" title="확인" /></td>
						</tr>
						<tr valign="top">
							<td align="right"><img src="<?=$FSBOARD_PATH?>/img/mem/login_t.gif" style="margin-top:1px" title="주의" /></td>
							<td colspan="4">
								<p>개정 &quot;주민등록법&quot;에 의해 타인의 주민등록번호를 부정사용하는 자는 3년 이하의 징역 또는 1천만원. 이하의 벌금이 부과될 수 있습니다. <em>관련법률: 주민등록법 제37조(벌칙) 제9호(시행일 2006.09.24)</em></p>
								<p>만약, 타인의 주민번호를 도용하여 온라인 회원 가입을 하신 이용자분들은 지금 즉시 명의 도용을 중단하시길 바랍니다.</p>
							</td>
						</tr>
					</form>
					</table>
				</div>
			</div>	
		</div>

		<script type="text/javascript">
			function authChk(){
				var frm=document.forms["frmChk"];
				if(!frm.mem_name.value){
					alert("이름을 입력해 주세요.");
					frm.mem_name.focus();
				}else if(!frm.mem_idsn1.value){
					alert("주민등록번호를 입력해 주세요.");
					frm.mem_idsn1.focus();
				}else if(!frm.mem_idsn2.value){
					alert("주민등록번호를 입력해 주세요.");
					frm.mem_idsn2.focus();
				}else{
					frm.submit();
				} 
			}
		</script>
<?
	}

		members_foot();
?>