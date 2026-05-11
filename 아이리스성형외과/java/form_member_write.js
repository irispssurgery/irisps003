function checkFormMember()
{
	var form=document.member;

	if(!form.user_id_member.value)
	{
		alert("아이디를 입력하여 주십시요.");
		form.user_id_member.focus();

		return false;
	}

	if(!form.user_pwd.value)
	{
		alert("비밀번호를 입력하여 주십시요.");
		form.user_pwd.focus();

		return false;
	}

	if(!form.passwd_confirm.value)
	{
		alert("비밀번호 확인을 위해 한번더 입력하여 주십시요.");
		form.passwd_confirm.focus();

		return false;
	}

	if(form.user_pwd.value != form.passwd_confirm.value)
	{
		alert("비밀번호가 일치하지 않습니다\n다시 입력하여 주십시요");
		form.user_pwd.focus();

		return false;
	}

	if(!form.user_name.value)
	{
		alert("이름을 입려하여 주십시요.");
		form.user_name.focus();

		return false;
	}

	if(!form.zipcode.value)
	{
		alert("우편번호를 입려하여 주십시요.");
		form.zipcode.focus();

		return false;
	}

	if(!form.address.value)
	{
		alert("주소를 입력하여 주십시요.");
		form.address.focus();

		return false;
	}

	if(!form.detail_address.value)
	{
		alert("상세주소를 입력하여 주십시요.");
		form.detail_address.focus();

		return false;
	}

	if(!form.telephone1.value)
	{
		alert("휴대폰 번호를 입력하여 주십시요.");
		form.telephone1.focus();

		return false;
	}

	if(!form.telephone2.value)
	{
		alert("휴대폰 번호를 입력하여 주십시요.");
		form.telephone2.focus();

		return false;
	}

	if(!form.telephone3.value)
	{
		alert("휴대폰 번호를 입력하여 주십시요.");
		form.telephone3.focus();

		return false;
	}

	if(!form.phone1.value)
	{
		alert("연락처를 입력하여 주십시요.");
		form.phone1.focus();

		return false;
	}

	if(!form.phone2.value)
	{
		alert("연락처를 입력하여 주십시요.");
		form.phone2.focus();

		return false;
	}

	if(!form.phone3.value)
	{
		alert("연락처를 입력하여 주십시요.");
		form.phone3.focus();

		return false;
	}

	if(!form.user_email.value)
	{
		alert("이메일을 입력하여 주십시요.");
		form.user_email.focus();

		return false;
	}
}

function checkUser()
{
	var form=document.member;

	if(!form.user_id_member.value)
	{
		alert("아이디를 입력하여 주십시요.");
		form.user_id_member.focus();

		return ;
	}
	else
	{
		user_id=form.user_id_member.value;
		window.open("./member/form_zipcode_write.html?user_id="+user_id, "", "width=370, height=130");
	}
}

function searchZipcode()
{
	window.open("./member/form_address_write.html","","width=343, height=300, scrollbars=yes");
}