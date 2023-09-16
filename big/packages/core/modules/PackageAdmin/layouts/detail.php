<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('detail_title');
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="<?php echo Portal::template('core').'/images/buttons/';?><?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?></td><?php if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="PackageAdminForm.submit();"><img src="<?php echo Portal::template('core').'/images/buttons/';?>delete_button.gif" style="text-align:center" alt=""/><br />[[.Delete.]]</a></td><?php }else{ if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>edit.jpg" style="text-align:center" alt=""/><br />[[.Edit.]]</a></td><?php } if(User::can_delete()){?><td class="form_title_button">
				<a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>delete_button.gif" alt=""/><br />[[.Delete.]]</a></td><?php }}?>
				<td class="form_title_button"><a target="_blank" href="<?php echo URL::build_current(array('cmd'=>'export','package_id'=>URL::get('id')));?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>export_button.gif" style="text-align:center"/><br />[[.export.]]</a></td>
				<td class="form_title_button"><a href="<?php echo URL::build_current(array('name'=>isset($_GET['name'])?$_GET['name']:''));?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>go_back_button.gif" style="text-align:center" alt=""/><br />[[.back.]]</a></td>
				<td class="form_title_button">
				<a  href="<?php echo URL::build('default');?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
  </tr>
  	<tr>
		<td class="form_detail_label">[[.name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.title.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|title|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.description.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|description|]]
		</td>
	</tr>
	</table>
	<?php
	if(URL::get('cmd')!='delete')
	{
	?>	<!--IF:related([[=module_related_fields=]])-->
	<div class="form_related_fields_area">
		<div class="form_related_fields_title">[[.module_related.]]</div>
		<!--LIST:module_related_fields-->
		<div class="form_related_field"><a href="<?php echo URL::build('module');?>&id=[[|module_related_fields.id|]]">[[|module_related_fields.name|]]</a></div>
		<!--/LIST:module_related_fields-->
	</div>
	<!--/IF:related--> <!--IF:related([[=edit_page_related_fields=]])-->
	<div class="form_related_fields_area">
		<div class="form_related_fields_title">[[.edit_page_related.]]</div>
		<!--LIST:edit_page_related_fields-->
		<div class="form_related_field"><a href="<?php echo URL::build('edit_page');?>&id=[[|edit_page_related_fields.id|]]">[[|edit_page_related_fields.name|]]</a></div>
		<!--/LIST:edit_page_related_fields-->
	</div>
	<!--/IF:related-->
	<?php
	}
	?>	<!--IF:delete(URL::get('cmd')=='delete')-->
	<form name="PackageAdminForm" method="post">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
	</div>
</div>