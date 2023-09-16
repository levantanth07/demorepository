<script src="<?php echo Portal::template('core');?>/css/tabs/tabpane.js" type="text/javascript"></script>
<script src="packages/core/modules/ModuleAdmin/edit_module.js" type="text/javascript"></script>
<script src="packages/core/includes/js/multi_items.js" type="text/javascript"></script>
<?php
$title = (URL::get('cmd')=='edit_code')?Portal::language('edit_code_title'):Portal::language('edit_code_title');
$action = (URL::get('cmd')=='edit_code')?'edit':'add';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);
?><div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="<?php echo Portal::template('core').'/images/buttons/';?><?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?> module [[|name|]]</td><td class="form_title_button"><a javascript:void(0) onclick="EditModulecontentAdminForm.submit();"><img src="<?php echo Portal::template('core').'/images/buttons/';?>save_button.gif" style="text-align:center"/><br />[[.save.]]</a></td><td>&nbsp;</td>
		<td class="form_title_button">
			<a javascript:void(0) onclick="location='<?php echo URL::build_current();?>';"><img src="<?php echo Portal::template('core').'/images/buttons/';?>go_back_button.gif"/><br />[[.back.]]</a></td>
		<?php if($action=='edit'){?><td class="form_title_button">
			<a javascript:void(0) onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'));?>';"><img src="<?php echo Portal::template('core').'/images/buttons/';?>edit_button.gif"/><br />[[.Edit.]]</a></td><?php }?>
		<td class="form_title_button">
			<a target="_blank" href="<?php echo URL::build('help',array('id'=>Module::$current->data['module_id'],'href'=>'?'.$_SERVER['QUERY_STRING']));?>"><img src="<?php echo Portal::template('core').'/images/buttons/';?>frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
	<div class="form_content">
<?php if(Form::$current->is_error())
		{
		?>		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>		<form name="EditModulecontentAdminForm" method="post">
		<table cellspacing="0" width="100%"><tr><td>
			<div class="tab-pane-1" id="tab-pane-module">
				<div class="tab-page" id="tab-page-module-code">
					<h2 class="tab">Code</h2>
					<div class="form_input">
						<textarea name="code" id="code" style="width:100%;font-family:'Courier New', Courier, monospace;font-size:16px" rows="25" onkeydown="if(edit_code_keypress(this)){ if(document.all)event.returnValue=false;else return false;}">[[|code|]]</textarea><br />
					</div>
				</div>
				<div class="tab-page" id="tab-page-module-layout">
					<h2 class="tab">Layout</h2>
					<div class="form_input">
						<textarea name="layout" id="layout" style="width:100%;font-family:'Courier New', Courier, monospace;font-size:16px" rows="25" onkeydown="if(edit_code_keypress(this)){ if(document.all)event.returnValue=false;else return false;}">[[|layout|]]</textarea><br />
					</div>
				</div>
			</div>
		</td></tr></table>
		<input type="hidden" value="1" name="confirm_edit"/>
	</form>
	</div>
</div>