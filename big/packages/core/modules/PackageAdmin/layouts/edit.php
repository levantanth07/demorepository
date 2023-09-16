<script language="javascript" type="text/javascript" src="packages/core/includes/js/init_tinyMCE.js"></script>
<script language="javascript" type="text/javascript">
	init_simple_rich_editor("<?php echo String::language_field_list('description');?>");
</script>
<script src="assets/default/css/tabs/tabpane.js" type="text/javascript"></script>
<span style="display:none">
</span>
<?php
$title = (URL::get('cmd')=='edit')?Portal::language('edit_title'):Portal::language('add_title');
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?><div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="assets/default/images/buttons/<?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?></td><td class="form_title_button"><a href="javascript:void(0)" onclick="EditPackageAdminForm.submit();"><img src="assets/default/images/buttons/save_button.gif" style="text-align:center"/><br />[[.save.]]</a></td><td>&nbsp;</td>
		<td class="form_title_button">
			<a href="javascript:void(0)" onclick="location='<?php echo URL::build_current();?>';"><img src="assets/default/images/buttons/go_back_button.gif"/><br />[[.back.]]</a></td>
		<?php if($action=='edit'){?><td class="form_title_button">
			<a href="javascript:void(0)" onclick="location='<?php echo URL::build_current(array('cmd'=>'delete','id'));?>';"><img src="assets/default/images/buttons/delete_button.gif" alt=""/><br />[[.Delete.]]</a></td><?php }?>
		<td class="form_title_button">
			<a  href="<?php echo URL::build('default');?>"><img src="assets/default/images/buttons/frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
	<div class="form_content">
<?php if(Form::$current->is_error())
		{
		?>		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>		<form name="EditPackageAdminForm" method="post">
	<table cellspacing="0" width="100%"><tr><td>
				<div class="tab-pane-1" id="tab-pane-package">
				<!--LIST:languages-->
				<div class="tab-page" id="tab-page-package-[[|languages.id|]]">
					<h2 class="tab">[[|languages.name|]]</h2>
					<div class="form_input_label">[[.title.]]:</div>
					<div class="form_input">
							<input name="title_[[|languages.id|]]" type="text" id="title_[[|languages.id|]]" style="width:400">
					</div>
					<div class="form_input_label">[[.type.]]:</div>
					<div class="form_input"><select name="type" id="type"></select></div>
					<div class="form_input_label">[[.description.]]:</div>
					<div class="form_input">
							<textarea name="description_[[|languages.id|]]" id="description_[[|languages.id|]]" style="width:100%" rows="10"></textarea><br />
					</div>
				</div>
				<!--/LIST:languages-->
				</div>
				</td></tr></table>
		<div class="form_input_label">[[.parent_name.]]:</div>
		<div class="form_input"><select name="parent_id"></select></div>
		<div class="form_input_label">[[.name.]]:</div>
		<div class="form_input">
			<input name="name" type="text" id="name" style="width:200">
		</div>
	<input type="hidden" value="1" name="confirm_edit"/>
	</form>
	</div>
</div>