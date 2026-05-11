function numOnly(obj,frm,isCash){
    //입력필드의 입력값이 숫자 입력가능.
    //사용예 : <input type="text" name="text" onKeyUp="javascript:numOnly(this,document.폼이름,true);">
    //세자리 콤마 사용시 true , 숫자만 입력 시 false
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        if (obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") {
            returnValue += obj.value.charAt(i);
        } else {
            returnValue += "";
        }
    }
    if (isCash) {
        obj.value = cashReturn(returnValue);
        return;
    }
    obj.focus();
    obj.value = returnValue;
}

function numOnlyTime(obj, frm) {
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
         if (obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") {
              returnValue += obj.value.charAt(i);
         } else {
              returnValue += "";
         }
    }
    if (parseInt(returnValue,10) >= 24) returnValue = "";
    obj.focus();
    obj.value = returnValue;
}

function numOnlyDay(obj, frm) {
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        if (obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") {
            returnValue += obj.value.charAt(i);
        } else {
            returnValue += "";
        }
    }
    if (parseInt(returnValue, 10) == 0 || parseInt(returnValue, 10) >= 32 || !returnValue) returnValue = "01";
    obj.focus();
    obj.value = returnValue;
}

function isNumber(str) {
    if (str) {
        if (str.search(/[^0-9]/g) == -1)
            return true;
        else
            return false;
    }
    else
        return false;
}


function korOnly(str) {
    var strLength = str.length;
    var i;
    var Unicode;
    for (var i = 0; i < strLength; i++) {
        Unicode = str.charCodeAt(i);
        if (!(44032 <= Unicode && Unicode <= 55203)) return false;
    }
    return true;
}

function engOnlyUp1(obj) {
    //영문 ,_,-, 숫자 만이 입력
    //사용예 : <input type="text" name="text" onKeyUp="javascript:engOnly(this);">
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        var isNum = false;
        if ((obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ||
            (obj.value.charAt(i) >= "a" &&  obj.value.charAt(i) <= "z") ||
            (obj.value.charAt(i) >= "A" && obj.value.charAt(i) <= "Z") ||
            obj.value.charAt(i) == "_" || obj.value.charAt(i) == "-") {
            returnValue += obj.value.charAt(i);
        }
    }
    obj.value = returnValue;
}

function engOnlyUp2(obj) {
    //space, -, _, 숫자, ., 영문만 입력되게한다.
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        var isNum = false;
        if ((obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ||
            (obj.value.charAt(i) >= "a" &&  obj.value.charAt(i) <= "z") ||
            (obj.value.charAt(i) >= "A" && obj.value.charAt(i) <= "Z") ||
            obj.value.charAt(i) == " " || obj.value.charAt(i) == "_" ||
            obj.value.charAt(i) == "-" || obj.value.charAt(i) == ".") {
            returnValue += obj.value.charAt(i);
        }
    }
    obj.value = returnValue;
}

function engOnlyUp3(obj) {
    //숫자, 영문 대문만 입력되게한다.
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        var isNum = false;
        if ((obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ||
            (obj.value.charAt(i) >= "a" &&  obj.value.charAt(i) <= "z") ||
            (obj.value.charAt(i) >= "A" && obj.value.charAt(i) <= "Z")) {
            returnValue += obj.value.charAt(i);
        }
    }
    obj.value = returnValue.toUpperCase();
}

function engOnlyUp4(obj) {
    //숫자, 영문 소문만 입력되게한다.
    if (event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39) return;
    var returnValue = "";
    for (var i = 0; i < obj.value.length; i++) {
        var isNum = false;
        if ((obj.value.charAt(i) >= "0" && obj.value.charAt(i) <= "9") ||
            (obj.value.charAt(i) >= "a" &&  obj.value.charAt(i) <= "z") ||
            (obj.value.charAt(i) >= "A" && obj.value.charAt(i) <= "Z")) {
            returnValue += obj.value.charAt(i);
        }
    }
    obj.value = returnValue.toLowerCase();
}



function cashReturn(numValue) {
    //numOnly함수에 마지막 파라미터를 true로 주고 numOnly를 부른다.
    var cashReturn = "";
    for (var i = numValue.length-1; i >= 0; i--) {
        cashReturn = numValue.charAt(i) + cashReturn;
        if (i != 0 && i % 3 == numValue.length % 3) cashReturn = "," + cashReturn;
    }
    return cashReturn;
}

function removeComma(cash) {
    //콤마를 없애준다.
    //사용법 : document.폼이름.필드이름.value = removeComma(document.폼이름.필드이름.value);
    var returnValue = "";
    for (var i = 0; i < cash.length; i++) {
        if (cash.charAt(i) != ",") {
            returnValue += cash.charAt(i);
        }
    }
    return returnValue;
}

function zero_fill(inputvalue,demandLength){
    //12 --> 0012 처럼 만들기
    var spaceValue = "";
    for (var i = 0; i < demandLength - inputvalue.length; i++) {
        spaceValue += "0";
    }
    return spaceValue + inputvalue;
}

function space_fill(inputvalue,demandLength){
    var spaceValue = "";
    for (var i = 0; i < demandLength - inputvalue.length; i++){
        spaceValue += " ";
    }
    return spaceValue + inputvalue;
}

function removeLeftZero(inputValue){
    //왼쪽 0 없애기
    var zeroIdx = 0;
    for (var i = 0; i < inputValue.length; i++) {
        if (inputValue.charAt(i) != "0" && inputValue.charAt(i) != ",") {
            break;
        }
        else zeroIdx++;
    }
    return inputValue.substring(zeroIdx);
}

function isValidEmail(str) {
    var pattern = /^[-_a-zA-Z0-9]+@[\.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
    return (pattern.test(str)) ? true : false;
}

function nohanMail(str) {
    var email = str.split("@")
    if (email[1] == "hanmail.net") return false;
    else if (email[1] == "daum.net") return false;
    else return true;
}

// ID, PW 체크
function is_IDPW_Old(id_pw) {
    if (id_pw.length < 4 || id_pw.length > 10) {
        return false;
    }
    for (var i = 0; i < id_pw.length; i++) {
        var chr = id_pw.substr(i, 1);
        if((chr < '0' || chr > '9') && (chr < 'a' || chr > 'z')) {
            return false;
        }
    }
    return true;
}

function is_IDPW_New(id_pw){
    if (id_pw.length < 4 || id_pw.length > 10) {
        return false;
    }
    var j = k = 0;
    for (var i = 0; i < id_pw.length; i++) {
        var chr = id_pw.substr(i, 1);
        if ((chr < '0' || chr > '9') && (chr < 'a' || chr > 'z')) {
            return false;
        }
        if (chr >= '0' && chr <= '9') j++;
        if (chr >= 'a' && chr <= 'z') k++;
    }
    return true;
}

// 주민등록번호 체크
function check_jm_bh(jm_bh1, jm_bh2) {
    var tot = 0, result = 0, re = 0, se_arg = 0;
    var chk_num = "";
    chk_jm_bh = jm_bh1 + jm_bh2;
    if (chk_jm_bh.length != 13) {
        return false;
    }
    else {
        for (var i = 0; i < 12; i++) {
            if (isNaN(chk_jm_bh.substr(i, 1))) return false;
            se_arg = i;
            if (i >= 8) se_arg = i - 8;
            tot = tot + Number(chk_jm_bh.substr(i, 1)) * (se_arg + 2)
        }
        if (chk_num != "err") {
            re = tot % 11;
            result = 11 - re;
            if (result >= 10) result = result - 10;
            if (result != Number(chk_jm_bh.substr(12, 1))) return false;
            if ((Number(chk_jm_bh.substr(6, 1)) < 1) || (Number(chk_jm_bh.substr(6, 1)) > 4)) return false;
        }
    }
    return true;
}

function next(str, order, size) {     // 주민번호 valid check , 자동 다음 폼 이동
    // PHP version difference??
    socno1ord = 1;
    socno2ord = socno1ord+1;
    if (str.elements[0].name == "PHPSESSID") {
        order++;		
        socno1ord = 2;
        socno2ord = socno1ord+1;
    }

    nex = order + 1;
    num = str.elements[order].value;
    siz = num.length;
    numFlag = Number(num);
    if (!numFlag && siz > 1 && num != '00' &&  num != '000') {
        alert('숫자를 넣어주세요');
        str.elements[order].select();
        str.elements[order].focus();
        return false;
    }

    if (siz == size) {
        if (order == socno1ord) str.elements[nex].focus();
        return true;
    }

    if (order == socno2ord && siz == 1) {
        if (num < 1 || num > 4) {
            alert('잘못된 주민번호 형식입니다');
            str.elements[order].select();
            str.elements[order].focus();
            return false;
        }
    }
}

// 우편번호 자동입력
function OpenZipcode(form, zip01, zip02, add01, add02) {
    window.open("/intro/sub07/search_add.asp?form=" + form + "&zip01=" + zip01 + "&zip02=" + zip02 + "&add01=" + add01 + "&add02=" + add02 + "","","width=420,height=300,top=100,left=100");
}


// 아디디체크 팝업창
function goCheckID(form,userid) {
    window.open("/intro/sub07/check_id.asp?form=" + form + "&userid=" + userid + "","","width=335,height=200,top=100,left=100");
}

// 해당(주민등록번호)길이가 되면 다음으로 커서 이동
function Move_Check(obj01, obj02, len) {
    if (obj01.value.length == len) {
        obj02.focus();
    }
}

// 실직적 사업자 등록번호 확인 함수
function Real_IsComNo(num) {
    var reg = /([0-9]{3})-?([0-9]{2})-?([0-9]{5})/;
    if (!reg.test(num)) return false;
    num = RegExp.$1 + RegExp.$2 + RegExp.$3;
    var cVal = 0;
    for (var i = 0; i < 8; i++) {
        var cKeyNum = parseInt(((_tmp = i % 3) == 0) ? 1 : (_tmp == 1)? 3 : 7);
        cVal += (parseFloat(num.substring(i,i+1)) * cKeyNum) % 10;
    }
    var li_temp = parseFloat(num.substring(i,i+1)) * 5 + '0';
    cVal += parseFloat(li_temp.substring(0,1)) + parseFloat(li_temp.substring(1,2));
    return (parseInt(num.substring(9, 10)) == 10 - (cVal % 10) % 10);
}

// 구라적 사업자 등록번호 확인 함수
function IsComNo(fname01,len01,fname02,len02,fname03,len03,mess) {
    var num01 = fname01.value;
    var num02 = fname02.value;
    var num03 = fname03.value;
    if (!isNumber(num01) || num01.length!=len01) {
        alert(mess + "의 첫번째란은 '" + len01 + "'자리의 정수여야 합니다!");
        fname01.focus();
        fname01.select();
        return false;
    }
    if (!isNumber(num02) || num02.length!=len02) {
        alert(mess + "의 두번째란은 '" + len02 + "'자리의 정수여야 합니다!");
        fname02.focus();
        fname02.select();
        return false;
    }
    if (!isNumber(num03) || num03.length!=len03) {
        alert(mess + "의 세번째란은 '" + len03 + "'자리의 정수여야 합니다!");
        fname03.focus();
        fname03.select();
        return false;
    }
    return true;
}

// 각종 전화번호(휴대폰),팩스번호
function IsPhoneNo(fname01, len01, fname02, len02, fname03, len03, mess) {
    var num01 = fname01.value;
    var num02 = fname02.value;
    var num03 = fname03.value;
    if (len01 == 9) {
        if (num01!='010' && num01!='011' && num01!='016' && num01!='017' && num01!='018' && num01!='019') {
            alert(mess + "의 첫번째란은 010,011,016,017,018,019 중의 하나여야 합니다!");
            fname01.focus();
            fname01.select();
            return false;
        }
    } else {
        if (!isNumber(num01) || num01.length < len01) {
            alert(mess + "의 첫번째란은 '" + len01 + "'자리 이상의 정수여야 합니다!");
            fname01.focus();
            fname01.select();
            return false;
        }
    }
    if (!isNumber(num02) || num02.length < len02) {
        alert(mess + "의 두번째란은 '" + len02 + "'자리 이상의 정수여야 합니다!");
        fname02.focus();
        fname02.select();
        return false;
    }
    if (!isNumber(num03) || num03.length < len03) {
        alert(mess + "의 세번째란은 '" + len03 + "'자리 이상의 정수여야 합니다!");
        fname03.focus();
        fname03.select();
        return false;
    }
    return true;
}


// 날짜입력
function IsDateAll(fname01, fname02, fname03, mess) {
    var num01 = fname01.value;
    var num02 = fname02.value;
    var num03 = fname03.value;
    if (!isNumber(num01) || num01.length != 4 || parseInt(num01, 10) < 1900 || parseInt(num01, 10) > 2100) {
        alert(mess + "의 '년'은 4자리로 1900~2100사이의 정수여야 합니다!");
        fname01.focus();
        fname01.select();
        return false;
    }
    if (!isNumber(num02) || parseInt(num02, 10) < 1 || parseInt(num02, 10) > 12) {
        alert(mess + "의 '월'은 1~12사이의 정수여야 합니다!");
        fname02.focus();
        fname02.select();
        return false;
    }
    if (!isNumber(num03) || parseInt(num03, 10) < 1 || parseInt(num03, 10) > 31) {
        alert(mess + "의 '일'은 1~31사이의 정수여야 합니다!");
        fname03.focus();
        fname03.select();
        return false;
    }
    return true;
}

function Int_Round(num,round_num){
    tmp_num1 = num * Math.pow(10, round_num);
    // 가공된 숫자를 반올림
    tmp_num2 = Math.round(tmp_num1);
    // 역순으로 다시 가공
    result = tmp_num2 / Math.pow(10, round_num); 
    return result;
}

// Show Flash
function ShowFlash(url, width, height) {
    document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="' + width + '" height="' + height + '" VIEWASTEXT>');
    document.write('<param name="movie" value="' + url + '">');
    document.write('<param name="quality" value="high">');
    document.write('<param name="wmode" value="transparent">');
    document.write('<param name="menu" value="false">');
    document.write('<embed src="' + url + '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' + width + '" height="' + height + '"></embed>');
    document.write('</object>');
}

/*
동영상,플래시 사용방법

설명 :
	<script type="text/javascript">
	//함수 호출
	setem = new setEmbed();

	// 플래시일경우 'flash', 동영상일경우 'movie'
	// 두번째 인자는 파일경로
	// 세번째, 네번째는 각각 넓이(width), 높이(height)
	// 네번째는 object 이름 (name값)
	//setem.init('구분자','소스경로','넓이','높이','이름');
	setem.init('flash','swf/menu.swf','638','96','');

	//파람(param)값이 존재할 경우
	//setem.parameter('네임값','속성');
	setem.parameter('wmode','transparent');

	//소스를 화면에 디스플레이
	setem.show();
	</script>

	현재 파일은 <head> </head>사이 또는 include 파일 상단에
	<script language="javascript" src="object_embed.js"></script>
	와 같이 표기

예 :

	<script type="text/javascript">
	setem = new setEmbed();
	setem.init('flash','swf/main.swf','988','347','');
	setem.parameter('wmode','transparent');
	setem.show();
	</script>

사용법 : 플래시나 동영상 파일을 사용할 경우 <object> 부터 </object>까지를 위의
자바스크립트로 대신한다.
*/
function setEmbed() {
    var obj = new String;
    var parameter = new String;
    var embed = new String;
    var html = new String;
    var allParameter = new String;
    var clsid = new String;
    var codebase = new String;
    var pluginspace = new String;
    var embedType = new String;
    var src = new String;
    var width = new String;
    var height = new String;
    var name = new String;

    // n : name, s : source, w : width, h : height//

    this.init = function(getType, s, w, h, n) {
                    if (getType == "flash") {
                        clsid = "D27CDB6E-AE6D-11cf-96B8-444553540000";
                        codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0";
                        pluginspage = "http://www.macromedia.com/go/getflashplayer";
                        embedType = "application/x-shockwave-flash";
                        parameter += "<param name='movie' value='"+ s + "'>\n";
                        parameter += "<param name='quality' value='high'>\n";
                    }
                    else if (getType == "movie") {
                        clsid = "CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6";
                        codebase = "http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715";
                        pluginspage = "http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/";
                        embedType = "application/x-mplayer2";
                        parameter += "<param name='filename' value='"+ s + "'>\n";
                    }
                    src = s;
                    width = w;
                    height = h;
                    name = n;
                }

    this.parameter = function(parm, value) {
                         parameter += "<param name='"+parm +"' value='"+ value + "'>\n";
                         allParameter += " "+parm + "='"+ value+"'";
                     }

    this.show = function() {
                    if (clsid) {
                        obj = "<object classid=\"clsid:"+ clsid +"\" codebase=\""+ codebase +"\" width='"+ width +"' height='"+ height +"' name='"+ name +"'>\n";
		    }
                    embed = "<embed src='" + src + "' pluginspage='"+ pluginspage + "' type='"+ embedType + "' width='"+ width + "' height='"+ height + "' name='"+ name + "' "+ allParameter +" ></embed>\n";
                    if (obj) {
                        embed += "</object>\n";
                    }
                    html = obj + parameter + embed;
                    document.write(html);
                }
}


// Menu Over Fix

function menu(img_id,img_src,subm,sub_img_id,sub_img_src){
 eval(img_id + ".src='image/" + img_src + ".gif';");
 
 if (subm != ""){
  eval("document.all['" + subm + "'].style.display = '';");
 }
 
 if (sub_img_id != "" && sub_img_src != ""){
  eval(sub_img_id + ".src='image/" + sub_img_src + ".gif';");
 }
}

function menu2(img_id,img_src,subm,sub_img_id,sub_img_src){
 eval(img_id + ".src='../image/" + img_src + ".gif';");
 
 if (subm != ""){
  eval("document.all['" + subm + "'].style.display = '';");
 }
 
 if (sub_img_id != "" && sub_img_src != ""){
  eval(sub_img_id + ".src='image/" + sub_img_src + ".gif';");
 }
}


function schedule_show() {
	var schedule = document.getElementById("scheduleopen").style;
	schedule.display = "block";
	
	document.onmousedown = function(evt){
		if(navigator.userAgent.indexOf("MSIE")==-1){
			mouse_event(evt)
		}else{
			mouse_event(event)
		}
	}
	
}


//Main_Layer.asp
function mouse_event(evt) {

	var schedule = document.getElementById("scheduleopen").style;
	if (schedule.display == "block")
	{
		var loginx1 = ((document.body.clientWidth - 900) / 2) + 210
		var loginy1 = 135
		var loginx2 = loginx1 + 360
		var loginy2 = 350
		

		if(navigator.userAgent.indexOf("MSIE")==-1){
			//mystr+="페이지 X좌표 : " + evt.pageX + "\n";
			//alert("페이지 X좌표 : "+evt.pageX);
			if ((evt.pageX < loginx1) || (evt.pageX > loginx2) || (evt.pageX < loginy1) || (evt.pageX > loginy2))
			{
				schedule.display="none";
			}
		}else{
			//if ((event.clientX < loginx1 || event.clientX > loginx2) || (event.clientY < loginy1 || event.clientY > loginy2))
			if ((event.clientX < loginx1) || (event.clientX > loginx2) || (event.clientY < loginy1) || (event.clientY > loginy2))
			{
				schedule.display="none";
			}
		}
	}

}