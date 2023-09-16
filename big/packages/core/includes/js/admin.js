function switch_display(obj){
	obj = $(obj);
	if(obj.style.display=='none'){
		if(use_double_click == 1){
			obj.style.display='';
		}else{
			obj.style.display='none';
		}
	}else{
		obj.style.display='none';
	}
}
function make_module_title(id, name, type, block_id, region_name,page_id,timer,container_id){
	document.write('\
		<div ondblclick="switch_display(\'module_title_'+block_id+'\')">\
		<div id="module_title_'+block_id+'" class="admin-tasks" style="display:none;" title="Timer:'+timer+'">\
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFCC" style="border:0px solid #3771B0;">\
        <tr>\
		    <td width="100%" align="left" style="line-height:24px;" nowrap>&nbsp;'+name+'</td>\
		    <td><a target="_blank" class="module-btn module-edit-btn" href="?portal='+PORTAL_ID+'&page=module&amp;cmd='+((type=='content')?'edit_content':((type=='HTML')?'edit_html':'edit_code'))+'&amp;id='+id+'" title="S&#7917;a code"></a></td>\
		  	<td><a class="module-btn module-add-btn" href="?portal='+PORTAL_ID+'&page=module&amp;page_id='+page_id+(container_id?'&amp;container_id='+container_id:'')+'&amp;region='+region_name+'&amp;after='+block_id+'&amp;href='+query_string+'" title="Th&#234;m module ti&#7871;p sau"></a></td>\
		  	<td><a class="module-btn module-language-btn" target="_blank" href="?portal='+PORTAL_ID+'&page=package_word&amp;block_id='+block_id+'&amp;module_id='+id+'" title="Thay &#273;&#7893;i ng&#244;n ng&#7919;"></a></td>\
			<td><a class="module-btn module-setting-btn" target="_blank" href="?portal='+PORTAL_ID+'&page=block_setting&amp;block_id='+block_id+'"></a></td>\
			<td><a class="module-btn module-move-up-btn" href="?portal='+PORTAL_ID+'&page=edit_page&move=up&block_id='+block_id+'&href='+query_string+'"></a></td>\
			<td><a class="module-btn module-move-down-btn" href="?portal='+PORTAL_ID+'&page=edit_page&move=down&block_id='+block_id+'&href='+query_string+'"></a></td>\
			<td style="display:none;"><a class="module-btn module-delete-btn" href="?portal='+PORTAL_ID+'&page=edit_page&cmd=delete&id='+block_id+'&href='+query_string+'" title="X&#243;a module"></a></td>\
			<td><a class="module-btn module-close-btn" javascript:void(0) onclick="switch_display(document.getElementById(\'module_title_'+block_id+'\'));" title="&#272;&#243;ng thanh c&#244;ng c&#7909;"></a></td>\
        </tr>\
        </table></div>');
}