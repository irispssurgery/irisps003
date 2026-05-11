function TableNameChange(url,old_name,new_name,p)
{
	var msg  = '';
	if (old_name == new_name)
	{
		msg += '\n테이블명이 변경되지 않았습니다.                        \n\n';
		msg += '------------------------------------------\n';
		msg += '원래 테이블명 : '+old_name+'\n';
		msg += '변경 테이블명 : '+new_name+'\n';
		msg += '------------------------------------------\n';
		
		alert(msg);

		return false;
	}
	else {
		msg += '\n테이블명을 다음과 같이 변경합니다.                        \n\n';
		msg += '------------------------------------------\n';
		msg += '원래 테이블명 : '+old_name+'\n';
		msg += '변경 테이블명 : '+new_name+'\n';
		msg += '------------------------------------------\n\n';
		msg += '정말로 변경하시겠습니까?\n';

		if(!confirm(msg)) {
			return false;
		}
		else {
			location.href = url+'?action=table_name_change&old_name='+old_name+'&new_name='+new_name+'&p='+p;
		}
	}
}
function TableCheck(type)
{
	var f = document.table_list;
	var i;
	var chk = 0;
	
	for(i = 0; i < f.length; i++) {
		if (f[i].type == 'checkbox')
		{
			if (type == 'check')
			{
				f[i].checked = true;
			}
			if (type == 'cancel')
			{
				f[i].checked = false;
			}
			if (type == 'reverse')
			{
				f[i].checked = !f[i].checked;
			}
			if(f[i].checked == true) {
				chk++;
			}
		}
	}

	if (type == 'change' && f.selected_table.value)
	{
		if (!chk)
		{
			alert('\n테이블이 선택되지 않았습니다.              \n');
			f.selected_table.value = "";
			return false;
		}
		if (!confirm('\n정말로 실행하시겠습니까?            \n'))
		{
			f.selected_table.value = "";
			return false;
		}
		f.action.value = f.selected_table.value;
		f.submit();
	}
}

//'add_dbmanager.php','1','kimsbod7_a_freeboard_dat','dump_all'

function TableSkill(url,p,table,type)
{
	switch (type)
	{
		case "property" :
			location.href = url+'?action=table_property&table='+table;
		break;

		case "dump_struct" :
			location.href = url+'?action=table_dump&table='+table+'&dump_type='+type+'&p='+p;
		break;

		case "dump_all" :
			location.href = url+'?action=table_dump&table='+table+'&dump_type='+type+'&p='+p;
		break;

		case "dump_excel" :
			location.href = url+'?action=table_excel&table='+table+'&dump_type='+type+'&p='+p;
		break;

		case "db_all" :
			location.href = url+'?action=table_dump&db_dump=all';
		break;

		case "view" :
			location.href = url+'?action=table_view&table='+table;
		break;

		case "add" :
			location.href = url+'?action=table_modify&table='+table;
		break;

		case "delete" :
			if (!confirm('\n테이블을 삭제하면 복구가 불가능합니다.\n\n정말로 ['+table+']테이블을 삭제하시겠습니까?             \n'))
			{
				return false;
			}
			location.href = url+'?action=table_delete&table='+table+'&p='+p;

		break;
	
		case "empty" :
			if (!confirm('\n테이블을 비우면 복구가 불가능합니다.\n\n정말로 ['+table+']테이블을 비우시겠습니까?             \n'))
			{
				return false;
			}
			location.href = url+'?action=table_empty&table='+table+'&p='+p;
		break;
	}
}

function TablePropertyChk(fs,type)
{
	var f = eval("document."+fs);
	
	if (type == 'add')
	{
		if(!f.field.value) {
			alert('\n필드네임을 입력해 주십시오.           \n');
			f.field.focus();
			return false;
		}
		if(!f.field_type.value) {
			alert('\n필드타입을 선택해 주십시오.           \n');
			f.field_type.focus();
			return false;
		}
		if(!confirm('\n정말로 필드를 추가하시겠습니까?           \n')) {
			return false;
		}
		f.submit();
	}

	if (type == 'alter')
	{
		if(!confirm('\n정말로 변경하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_alter";
		f.submit();
	}
	
	if (type == 'delete')
	{

		if(!confirm('\n정말로 삭제하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_delete";
		f.submit();
	}

	if (type == 'primary')
	{

		if(!confirm('\n정말로 Primary Key로 설정하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_primary";
		f.submit();
	}
	if (type == 'index')
	{

		if(!confirm('\n정말로 Index Key로 설정하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_index";
		f.submit();
	}
	if (type == 'unique')
	{

		if(!confirm('\n정말로 Unique Key로 설정하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_unique";
		f.submit();
	}
	if (type == 'fulltext')
	{

		if(!confirm('\n정말로 Fulltext Key로 설정하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_fulltext";
		f.submit();
	}
}

function TablePropertyChk1(fs,type)
{
	var f = eval("document."+fs);

	if (type == 'primary')
	{

		if(!confirm('\n정말로 Primary Key에서 제거하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_primary1";
		f.submit();
	}
	if (type == 'index')
	{

		if(!confirm('\n정말로 Index Key에서 제거하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_index1";
		f.submit();
	}
	if (type == 'unique')
	{

		if(!confirm('\n정말로 Unique Key에서 제거하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_index1";
		f.submit();
	}
	if (type == 'fulltext')
	{

		if(!confirm('\n정말로 Fulltext Key에서 제거하시겠습니까?           \n')) {
			return false;
		}
		f.action.value = "field_index1";
		f.submit();
	}
}

function RecordDelCheck(file,table,where,value,p)
{
		if(!confirm('\n삭제된 데이터는 복구가 불가능합니다.                \n\n정말로 삭제하시겠습니까?           \n')) {
			return false;
		}
		location.href  = file+'?action=record_delete&table='+table+'&wehre='+where+'&value='+value+'&p='+p;
}