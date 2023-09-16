<script src="assets/default/css/tabs/tabpane.js" type="text/javascript"></script>
<?php
$title = (URL::get('cmd')=='edit')?Portal::language('function_edit'):Portal::language('function_add');
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title($title);?>
<table class="table">
	<tr>
		<td width="100%" class="form-title"><?php echo $title;?></td>
		<td width="" align="right"><a class="button-medium-save" onclick="EditCategoryForm.submit();">[[.save.]]</a></td>
        <td><a class="button-medium-back" onclick="location='<?php echo URL::build_current();?>';">[[.back.]]</a></td>
		<?php if($action=='edit' and User::can_delete(false,ANY_CATEGORY)){?>
		<td><a class="button-medium-delete" onclick="location='<?php echo URL::build_current(array('cmd'=>'delete','id'));?>';">[[.Delete.]]</a></td>
		<?php }?>
	</tr>
</table>
<table>
	<tr>
		<td style="width:100%" valign="top">
			<?php if(Form::$current->is_error())
			{
			?>
			<strong>B&#225;o l&#7895;i</strong><br>
			<?php echo Form::$current->error_messages();?><br>
			<?php
			}
			?>
			<form name="EditCategoryForm" method="post" enctype="multipart/form-data">
			<input type="hidden" name="confirm_edit" value="1" />
			<table  class="table"><tr><td>
			<div class="tab-pane-1" id="tab-pane-category">
			<!--LIST:languages-->
			<div class="tab-page" id="tab-page-category-[[|languages.id|]]">
			<h2 class="tab">[[|languages.name|]]</h2>
			<div class="form_input_label">[[.name.]]:</div>
			<div class="form_input">
			<input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="form-control" >
			</div>
			<div class="form_input_label">[[.description.]]:</div>
			<div class="form_input">
			<textarea name="description_[[|languages.id|]]" id="description_[[|languages.id|]]"  class="form-control" rows="10" ></textarea><br />
			</div>
			<div class="form_input_label">[[.group_name.]]:</div>
			<div class="form_input">
			<input name="group_name_[[|languages.id|]]" type="text" id="group_name_[[|languages.id|]]"  class="form-control" >
			</div>
			</div>
			<!--/LIST:languages-->
			</div>
			</td></tr></table>
			<div class="form_input_label">[[.parent_name.]]:</div>
			<div class="form_input">
			<select name="parent_id" id="parent_id" class="form-control"></select></div>
			<div class="form_input_label">[[.url.]]:</div>
			<div class="form_input">
			<input name="url" type="text" id="url" class="form-control">
			</div>
			<div class="form_input_label">[[.status.]]:</div>
			<div class="form_input">
			<select name="status" id="status" class="form-control"></select>
			</div>
				<div class="form_input_label">Mở cửa sổ mới :</div>
				<div class="form_input">
					<select name="open_new_window" id="open_new_window" class="form-control"></select>
				</div>
				<div class="form_input_label">[[.icon_url.]]:</div>
			<div class="form_input">
			<!--<img style="padding:0 0 0 0" id="img_icon_url" src="<?php echo URL::get('icon_url')?URL::get('icon_url'):Portal::template('cms').'/images/spacer.gif';?>" height="100" width="120" border="0">-->
			<input name="delete_icon_url" type="hidden" id="delete_icon_url">
			<input name="icon_url" type="text" id="icon_url" class="form-control">
		   <!--onchange="$('img_icon_url').src='file:///'+this.value;" -->
			</div>
			</form>
			</td>
		<td valign="top">
		</td>
	</tr>
</table>
