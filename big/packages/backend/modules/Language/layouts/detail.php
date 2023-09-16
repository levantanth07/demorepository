<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('detail_title');
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form-title"><img src="<?php echo Portal::template('core').'/images/buttons/';?><?php echo $action;?>_button.gif" align="absmiddle" alt=""/>&nbsp;<?php echo $title;?></td><?php if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="LanguageForm.submit();" class="button-medium-delete">[[.Delete.]]</a></td><?php }else{ if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>" class="button-medium-edit">[[.Edit.]]</a></td><?php } if(User::can_delete()){?><td class="form_title_button">
				<a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>" class="button-medium-delete">[[.Delete.]]</a></td><?php }}?>
				<td class="form_title_button"><a href="<?php echo URL::build_current(array(
	));?>" class="button-medium-back">[[.back.]]</a></td>
				<td class="form_title_button">
				<a  href="<?php echo URL::build('default');?>" class="button-medium-home">Trang ch&#7911;</a></td></tr></table>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
  </tr>
  	<tr>
		<td class="form_detail_label">[[.code.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|code|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name|]]
		</td>
	</tr>
	</table>
	<?php
	if(URL::get('cmd')!='delete')
	{
	?>	<?php
	}
	?>	<!--IF:delete(URL::get('cmd')=='delete')-->
	<form name="LanguageForm" method="post">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
	</div>
</div>
