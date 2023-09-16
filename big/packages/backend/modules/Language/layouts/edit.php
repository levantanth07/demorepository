<fieldset id="toolbar">
	<div id="toolbar-info">
		[[.language.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a href="javascript:void(0)" onclick="EditLanguageForm.submit();" > <span title="save"> </span> [[.save.]] </a> </td>
		 <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
		</tr>
	  </tbody>
	</table>
  </div>
</fieldset>
<br>
<fieldset id="toolbar">
<table class="table">
	  <tr>
    <td>
<?php if(Form::$current->is_error())
		{
		?>		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>
		<form name="EditLanguageForm" method="post" enctype="multipart/form-data">
		<div class="form-group">
    	<label>[[.code.]]:</label>
			<input name="code" type="text" id="code" class="form-control">
		</div>
		<div class="form-group">
    	<label>[[.name.]]:</label>
			<input name="name" type="text" id="name" class="form-control">
		</div>
    <div class="form-group">
    	<label>[[.default.]]:</label>
			<input  name="default" type="checkbox" id="default" value="1">
		</div>
    <div class="form-group">
    	<label>[[.active.]]:</label>
			<input  name="active" type="checkbox" id="active" value="1">
		</div>
		<div class="form_input_label">[[.icon_url.]]:</div>
		<div class="form_input">
			<input type="hidden" value="1" name="delete_icon_url" id="delete_icon_url"/>
			<?php if(Url::get('icon_url')){?><img src="<?php echo Url::get('icon_url');?>" id="image_url"><img src="assets/default/images/buttons/delete.gif" onclick="document.getElementById('image_url').src='';document.getElementById('delete_icon_url').value='0'"><?php }?><input name="icon_url" type="file"  id="icon_url"/>
		</div>
	<input type="hidden" value="1" name="confirm_edit"/>
	</form>
	</td>
	</tr>
	</table>
	<br>
</fieldset>
<script>
	jQuery(document).ready(function(e) {
    jQuery('#default').attr('checked',<?php echo Url::get('default')?'true':'false'?>);
		jQuery('#active').attr('checked',<?php echo Url::get('active')?'true':'false'?>);
  });
</script>
