<?
/*************************************************************

	FSBOARD Javascript Library 1.2

	Technical contact: saiur@msn.com
	Producer: Junghyun Cho
	Module Made: August 1, 2006
	Last Update: January 1, 2007

	Copyright(c)2000-2007 FSBOARD. All Rights Reserved.

*************************************************************/

	//파일타입 헤더 지정
	header ('Content-Type:text/javascript; charset=euc-kr');

	$xf = trim($_GET["defer"]);
?>
//<![CDATA[
var AJAX = {
	XmlHttp: null,
	create: function () {
		try {
			if (window.XMLHttpRequest) {
				AJAX.XmlHttp = new XMLHttpRequest();
				//일부의 모질라 버전들은 readyState property, 
				//onreadystate event를 지원하지 않으므로. - from xmlextrs
				if(this.XmlHttp.readyState == null) {
					this.XmlHttp.readyState = 1;
					this.XmlHttp.addEventListener("load", function () {
						this.XmlHttp.readyState = 4;
						if(typeof this.XmlHttp.onreadystatechange == "function")
							tmpXmlHtp.onreadystatechange();
					}, false);
				}
			} else {
				AJAX.XmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} catch(e) {
			alert("Your browser does not support XmlHttp objects");
			//throw new Error("Your browser does not support XmlHttp objects");
		}
	}
}

AJAX.openXML =  function (method, url, async, uname, pswd) {
	if(AJAX.XmlHttp != null) {
		if(uname == undefined) {
			AJAX.XmlHttp.open(method, url, async, uname, pswd);
		} else {
			AJAX.XmlHttp.open(method, url, async);
		}
		AJAX.XmlHttp.onreadystatechange = function () {
			if(AJAX.XmlHttp.readyState == 4) {
				if(AJAX.XmlHttp.status == 200) { // 200 은 HTTP에서 성공 i.e) 404 : not found
					AJAX.statusSuccessHandler(AJAX.XmlHttp.responseXML.xml);
				} else {
					AJAX.statusErrorHandler();
					alert('Error while loading!');
				}
			}
		}
	} else {
		alert ("need to create xmlhttp object");
	}
}

AJAX.openText =  function (method, url, async, uname, pswd) {
	if(AJAX.XmlHttp != null) {
		if(uname == undefined) {
			AJAX.XmlHttp.open(method, url, async, uname, pswd);
		} else {
			AJAX.XmlHttp.open(method, url, async);
		}
		AJAX.XmlHttp.onreadystatechange = function () {
			if(AJAX.XmlHttp.readyState == 4) {
				if(AJAX.XmlHttp.status == 200) { // 200 은 HTTP에서 성공 i.e) 404 : not found
					AJAX.statusSuccessHandler(AJAX.XmlHttp.responseText);
				} else {
					AJAX.statusErrorHandler();
					alert('Error while loading!');
				}
			}
		}
	} else {
		alert ("need to create xmlhttp object");
	}
}

AJAX.send = function (content) {
	if (content == undefined)
		AJAX.XmlHttp.send(null);
	else
		AJAX.XmlHttp.send(content);
}

AJAX.setOnReadyStateChange = function (funcname) {
	if (AJAX.XmlHttp) {
		AJAX.XmlHttp.onreadystatechange = funcname;
	} else {
		alert ("need to create xmlhttp object");
	}
}

// status 200 일 때 처리 함수
AJAX.statusSuccessHandler = function (data) {
	alert(data);
}

AJAX.setStatusSuccessHandler = function (funcname) {
	AJAX.statusSuccessHandler = funcname;
}

// status 200 일 때 기본 처리 함수
// setStatusSuccessHandler() 로 대체 가능
AJAX.statusErrorHandler = function (status) {
	AJAX.rtnText = AJAX.XmlHttp.responseText;
}

AJAX.setStatusErrorHandler = function (funcname) {
	AJAX.statusErrorHandler = funcname
}

AJAX.setRequestHeader = function (label, value) {
	AJAX.XmlHttp.setRequestHeader(label, value);
}

function checkAll() {
	try {
		var chk = document.forms.__ctl;

		for(var i=0; i<chk.length; i++) {
			if(chk[i].type == "checkbox" && chk[i].name.indexOf("idx")==0) {
				chk[i].checked = chk[i].checked ? false : true;
			}
		}
	} catch(e) { }
}

function onlyNum(obj) {
	try {
		var val = obj.value;
		var re = /[^0-9\.]/gi;
		obj.value = val.replace(re,"");
		obj.value = obj.value ? parseFloat(obj.value) : 0;
	} catch(e) {}
}

function formResize(obj, val) {
	try {
		if(val<0 && Math.abs(val)>=Math.abs(obj.rows)) {
			alert("더이상 줄일수 없습니다.");
		} else {
			obj.rows += val;
		}
	} catch(e) {}
}

function checkValue(f) {
	var j = f.elements.length;
	var i;
	var re;
	var args;
	var result;
	for (i=0; i<j; i++) {
		if (typeof(f.elements[i].tag) == "undefined") continue;

		args = f.elements[i].tag.split("||", 3);
		if (args[0]=="C") {
			result = eval(args[1]+"(f.elements[i], f.elements[i].value);");
		}
		else if ((args[0]=="M") || ((args[0]=="O")&& (f.elements[i].value.length>0))) {
			re = new RegExp("^" + args[1] + "$", "gi");
			result = re.test(f.elements[i].value);
		}

		if (!result) {
			f.elements[i].focus();
			alert(args[2]);
			return false;
		}
	}
	return true;
}

function ChkStrLength() {
	this.updateChar = function(length_limit,limit_panel) {
		var comment = event.srcElement;
		var length = this.calculate_msglen(comment.value);
		var textlimit = document.getElementById(limit_panel);
		if(textlimit) textlimit.innerHTML = length;
		if(length > length_limit) {
			alert("최대 " + length_limit + "byte이므로 초과된 글자수는 자동으로 삭제됩니다.");
			//comment.value = comment.value.replace(/\r\n$/, "");
			comment.value = this.assert_msglen(comment.value, length_limit, limit_panel);
		}
	}

	this.calculate_msglen = function(message) {
		var nbytes = 0;

		for(i=0; i<message.length; i++) {
			var ch = message.charAt(i);
			if(escape(ch).length > 4) {
				nbytes += 2;
			}
			else if(ch == '\n') {
				if(message.charAt(i-1) != '\r') {
					nbytes += 1;
				}
			}
			else if(ch == '<' || ch == '>') {
				nbytes += 4;
			}
			else {
				nbytes += 1;
			}
		}

		return nbytes;
	}

	this.assert_msglen = function(message, maximum, limit_panel) {
		var inc = 0;
		var nbytes = 0;
		var msg = "";
		var msglen = message.length;
		var textlimit = document.getElementById(limit_panel);

		for(i=0; i<msglen; i++) {
			var ch = message.charAt(i);
			if(escape(ch).length > 4) {
				inc = 2;
			}
			else if (ch == '\n') {
				if(message.charAt(i-1) != '\r') {
					inc = 1;
				}
			}
			else if (ch == '<' || ch == '>') {
				inc = 4;
			}
			else {
				inc = 1;
			}
			if((nbytes + inc) > maximum) {
				break;
			}
			nbytes += inc;
			msg += ch;
		}
		if(textlimit) textlimit.innerHTML = nbytes;
		return msg;
	}
}

function chkCapsLock(e, id) {
	var myKeyCode = 0;
	var myShiftKey = false;
	var myMsg = '<Caps Lock>이 켜져 있습니다.';
	var obj = id ? document.getElementById(id) : '';
	var capsLock = false;

	if(document.all) {
		myKeyCode = e.keyCode;
		myShiftKey = e.shiftKey;
	}
	else {
		myKeyCode = e.which;
		myShiftKey = (myKeyCode==16) ? true : false;
	}

	if((myKeyCode>=65 && myKeyCode<=90) && !myShiftKey) { capsLock = true; }
	else if((myKeyCode>=97 && myKeyCode<=122) && myShiftKey) { capsLock = true; }

	if(capsLock) {
		if(obj) {
			myMsg = myMsg.replace(/[<]/gi,'&lt;');
			myMsg = myMsg.replace(/[>]/gi,'&gt;');
			myMsg = myMsg.replace(/[\n]/gi,'<br />');
			obj.innerHTML = myMsg;
		}
		else window.alert(myMsg);
	}
	else {
		if(obj) obj.innerHTML = '';
	}
}

function sendit() {
	try {
		var args = sendit.arguments;
		var frm = document.forms.__ctl;
		var qstr = '';
		if(args&&frm) {
			if(args.length>0) qstr += "<?=$xf?>?id=" + args[0];
			if(args.length>1) qstr += "&mode=" + args[1];
			if(args.length>2) qstr += "&idx=" + args[2];
			if(args.length>3) qstr += "&page=" + args[3];
			if(args.length>4) qstr += "&srhctgr=" + args[4];
			if(args.length>5) qstr += "&srhstr=" + args[5];
			if(args.length>6) qstr += "&rowctgr=" + args[6];
			if(args.length>7) qstr += "&rowmode=" + args[7];
			if(args.length>8) qstr += "&ctgrstr=" + args[8];
			frm.action = qstr;
			frm.submit();
		}
	} catch(e) { window.alert("ERROR : " + e.number + "\n\n" + e.description); }
}

function controlImage(img_id,img_width) {
	try {
		var maxWidth = img_width < 1 ? document.body.clientWidth-100 : img_width;
		var w = document.getElementById(img_id).width;

		document.getElementById(img_id).style.visibility = "hidden";

		if(w <= 0) {
			time_id = window.setTimeout("controlImage('" + img_id + "')",10);
		}
		else {
			if(w > maxWidth) {
				document.getElementById(img_id).width = maxWidth;
			}
			document.getElementById(img_id).style.visibility = "visible";
		}
	} catch(e) {}
}

function vwimgrzmv(obj,imgsrc) {
	var rz = 0;
	var scrl = 0;
	var rzwidth = obj.width + 5;
	var rzheight = obj.height + 25;
	var mvleft = (window.screen.width - obj.width) / 2;
	var mvtop = (window.screen.height - obj.height - 25) / 2;
	var imgwin = null;
	var ie = navigator.appName.indexOf('Microsoft Internet Explorer')>-1 ? true : false;

	if(obj.height>window.screen.height) {
		rz = 0;
		scrl = 1;
		rzwidth = obj.width + 23;
		rzheight = window.screen.height - 30;
		mvtop = 0;
	}

	if(obj.width>window.screen.width) {
		rz = 0;
		scrl = 1;
		rzwidth = window.screen.width;
		mvleft = 0;
	}

	imgwin = window.open('','_blank','toolbar=0,menubar=0,status=1,scrollbars=' + scrl + ',resizable=' + rz + ',width=0,height=0,left=' + ((window.screen.availWidth-245)/2) + ',top=' + ((window.screen.availHeight-105)/2));
	if(imgwin) {
		imgwin.blur();
		imgwin.moveTo(0,-2000);
		imgwin.resizeTo(0,0);
		imgwin.document.write(("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"><" + "html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"ko\" xml:lang=\"utf-8\"><" + "head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><" + "title>object</" + "title><style type=\"text/css\">body { margin:0; }</style></" + "head>" +
			"<" + "body>" +
			"<img src=\"" + imgsrc.replace('+',' ') + "\" id=\"uploaded_image\" " +
			" onload=\"" +
				"var rz = 0;" +
				"var scrl = 0;" +
				"var rzwidth = this.width + 5;" +
				"var rzheight = this.height + 25 + " + (ie?55:30)  + ";" +
				"var mvleft = (window.screen.width - this.width) / 2;" +
				"var mvtop = (window.screen.height - this.height-25) / 2;" +
				"var imgwin = null;" +
				"if(this.height > window.screen.height) {" +
					"rzwidth = this.width + 23;" +
					"rzheight = window.screen.height - 30;" +
					"mvtop = 0;" +
				"}" +
				"if(this.width > window.screen.width) {" +
					"rz = 1;" +
					"scrl = 1;" +
					"rzwidth = window.screen.width;" +
					"mvleft = 0;" +
				"}" +
				"window.resizeTo(rzwidth, rzheight);" +
				"window.moveTo(mvleft, mvtop);" +
				"window.scrollbars = scrl;" +
				"window.resizable = rz;" +
				"\"" +
			" onerror=\"window.alert('이미지파일이 없거나 파일이름이 잘못되었습니다.\\n파일이름에 공백이 포함되어 있으면 뷰어창에 오류가 발생할수도 있습니다.');self.close();\"" +
			" onclick=\"window.close();\"" +
			" style=\"border:0px;cursor:hand;\" alt=\"클릭하시면 창이 닫힙니다\" />" +
			"<" + "script type=\"text/javascript\">" +
				"function init() {" +
					"var img = document.getElementById('uploaded_image');" +
					"if(img) {" +
						"if(img.width >= window.screen.width -10) {" +
							"img.width = window.screen.width - 10;" +
						"}" +
						"if(img.height >= window.screen.height-50) {" +
							"document.body.scroll='auto';" +
						"}" +
						"document.title = img.width + '*' + img.height;" +
					"}" +
				"}" +
				"window.onLoad = init();" +
			"</" + "script >" +
			"</" + "body></" + "html>"));
		imgwin.status = "Resolution:" + window.screen.width + "x" + window.screen.height;
		imgwin.focus();
	}
}

function playFlash(objId,id,maintainCode,filename,seqNum,width,height) {
	var obj = document.getElementById(objId);
	obj.innerHTML = "<embed src=\"<?=$xf?>?id=" + id + "&amp;mode=filelink&amp;maintainCode=" + maintainCode + "&amp;filename=" + filename + "\" id=\"UPLOADED_FLASH" + seqNum + "\" style=\"width:" + width + "px; height:" + height + "px;\" />";
}

//This code was written by Tyler Akins and has been placed in the
//public domain. It would be nice if you left this header intact.
//Base64 code from Tyler Akins - http://rumkin.com
var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
function encode64(input) {
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;

	do {
		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + 
			keyStr.charAt(enc3) + keyStr.charAt(enc4);
	} while (i < input.length);
	return output;
}
function decode64(input) {
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;

	// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

	do {
		enc1 = keyStr.indexOf(input.charAt(i++));
		enc2 = keyStr.indexOf(input.charAt(i++));
		enc3 = keyStr.indexOf(input.charAt(i++));
		enc4 = keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}
	} while (i < input.length);
	return output;
}
function encode64Han(str) {
	return encode64(escape(str))
}
function decode64Han(str) {
	return unescape(decode64(str))
}

function doBlink() {
	var blink = document.all.tags("BLINK");
	for (var i=0; i<blink.length; i++) {
		blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "";
	}
}

function startBlink() {
	if(document.all) {
		setInterval("doBlink();",500);
	}
}

/*
//IE전용이라서 FireFox에서는 안됨
function window::onload() {
	var csl = new ChkStrLength();
	startBlink();
}
*/

var csl = new ChkStrLength();
window.onload = startBlink;

//]]>
