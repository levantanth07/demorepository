<?php
$title = (URL::get('cmd')=='edit')?Portal::language('Edit_module_setting'):Portal::language('Add_module_setting');
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><?php echo $title;?></td>
		<td class="form_title_button"><a javascript:void(0) onclick="EditModuleSettingForm.submit();"><img alt="" src="assets/default/images/buttons/save.jpg" class="icon_cmd"/>[[.save.]]</a></td>
		<td class="form_title_button"><a javascript:void(0) onclick="location='<?php echo URL::build_current();?>';"><img alt="" src="assets/default/images/buttons/back.jpg" class="icon_cmd"/>[[.back.]]</a></td>
		<?php if($action=='edit'){?><td class="form_title_button"><a javascript:void(0) onclick="location='<?php echo URL::build_current(array('cmd'=>'delete','id'));?>';"><img alt="" src="assets/default/images/buttons/delete.jpg" class="icon_cmd"/>[[.Delete.]]</a></td><?php }?>
		<td class="form_title_button"><a target="_blank" href="<?php echo URL::build('help_topic',array('id'=>Module::$current->get_help_topic_id()));?>"><img alt="" src="assets/default/images/buttons/help.jpg" class="icon_cmd"/>[[.help.]]</a></td></tr></table>
	<div class="form_content">
	<table width="100%" border="0" cellspacing="4" cellpadding="4">
	  <tr>
		<td colspan="2" align="right" style="width:100%"><a javascript:void(0) onclick="Javascript:Element.toggle('advanced_detail');" class="button_link">Show advanced detail </a></td>
		</tr>
	  <tr>
    <td style="width:100%">
	<?php if(Form::$current->is_error())
		{
		?>
		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>
		<form name="EditModuleSettingForm" method="post" >
		<div class="form_input_label">[[.id.]]:</div>
		<div class="form_input">
			<input name="id" type="text" id="id" style="width:300" >
		<div class="form_input_label">[[.name.]]:</div>
		<div class="form_input">
			<input name="name" type="text" id="name" style="width:300" >
		</div><div class="form_input_label">[[.module_id.]]:</div>
		<div class="form_input">
				<select name="module_id" id="module_id" ></select>
		</div>
		<div class="form_input">
				<select name="style" id="style" ></select>
		</div>
		<div class="form_input_label">[[.description.]]:</div>
		<div class="form_input">
			<textarea name="description" id="description" style="width:100%" rows="20" ></textarea>
		</div><div class="form_input_label">[[.type.]]:</div>
		<div class="form_input">
		<select  name="type" id="type" >
			<option value="TEXT">TEXT</option><option value="INT">INT</option><option value="FLOAT">FLOAT</option><option value="EMAIL">EMAIL</option><option value="COLOR">COLOR</option><option value="FONT_FAMILY">FONT_FAMILY</option><option value="FONT_SIZE">FONT_SIZE</option><option value="FONT_WEIGHT">FONT_WEIGHT</option><option value="TEXTAREA">TEXTAREA</option><option value="RICH_EDITOR">RICH_EDITOR</option><option value="TABLE">TABLE</option><option value="SELECT">SELECT</option><option value="IMAGELIST">IMAGELIST</option><option value="DATE">DATE</option><option value="DATETIME">DATETIME</option><option value="CHECKBOX">CHECKBOX</option><option value="RADIO">RADIO</option><option value="FILE">FILE</option><option value="IMAGE">IMAGE</option><option value="YESNO">YESNO</option>
		</select>
				<script language="javascript">
					selects = document.getElementsByTagName('select');
					selects[selects.length-1].value = '<?php echo URL::get('type');?>';
				</script>
		</div><div class="form_input_label">[[.default_value.]]:</div>
		<div class="form_input">
			<textarea name="default_value" id="default_value" style="width:100%" rows="3" ></textarea>
		</div><div class="form_input_label">[[.value_list.]]:</div>
		<div class="form_input">
			<textarea name="value_list" id="value_list" style="width:100%" rows="15" ></textarea>
		</div><div class="form_input_label">[[.edit_condition.]]:</div>
		<div class="form_input">
			<textarea name="edit_condition" id="edit_condition" style="width:100%" rows="3" ></textarea>
		</div><div class="form_input_label">[[.view_condition.]]:</div>
		<div class="form_input">
			<textarea name="view_condition" id="view_condition" style="width:100%" rows="3" ></textarea>
		</div><div class="form_input_label">[[.extend.]]:</div>
		<div class="form_input">
			<textarea name="extend" id="extend" style="width:100%" rows="3" ></textarea>
		</div><div class="form_input_label">[[.group_name.]]:</div>
		<div class="form_input">
			<input name="group_name" type="text" id="group_name" style="width:300" >
		</div><div class="form_input_label">[[.position.]]:</div>
		<div class="form_input">
			<input name="position" type="text" id="position" style="width:100" >
		</div><div class="form_input_label">[[.group_column.]]:</div>
		<div class="form_input">
			<input name="group_column" type="text" id="group_column" style="width:100" >
		</div><div class="form_input_label">[[.meta.]]:</div>
		<div class="form_input">
			<textarea name="meta" id="meta" style="width:100%" rows="3" ></textarea>
		</div><div class="form_input_label">[[.update_code.]]:</div>
		<div class="form_input">
			<textarea name="update_code" id="update_code" style="width:100%" rows="5" ></textarea>
		</div>
	<input type="hidden" value="1" name="confirm_edit"/>
	</form>
	</td>
  </tr>
</table>
	</div>
</div>