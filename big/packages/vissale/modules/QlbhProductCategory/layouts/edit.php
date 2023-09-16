<fieldset id="toolbar">
	<div id="toolbar-title">
		Cập nhật danh mục sản phẩm
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a onclick="EditCategoryForm.submit();" > <span title="save"> </span> [[.save.]] </a> </td>
		 <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
		</tr>
	  </tbody>
	</table>
    </div>
</fieldset>
<br>
<fieldset id="toolbar">
		<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
		<form name="EditCategoryForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="confirm_edit" value="1" />
		<table class="table">
		<tr>
		  <td valign="top">
			<table  cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#F9F9F9;border:1px solid #D5D5D5" align="center">
			<tr>
				<td>
				<div class="tab-pane-1" id="tab-pane-category">
				<div class="tab-page" id="tab-page-category-1">
					<h2 class="tab">...</h2>
					<div class="form_input_label">[[.name.]] (<span class="require">*</span>)</div>
					<div class="form_input">
						<input name="name" type="text" id="name" style="width:50%" >
					</div>
					<div class="form_input_label">[[.brief.]]</div>
					<div class="form_input">
						<textarea id="brief" name="brief" cols="75" rows="20" style="width:99%; height:200px;overflow:hidden"><?php echo Url::get('brief');?></textarea><br />
						<script>simple_mce('brief');</script>	
					</div>
					<div class="form_input_label">[[.description.]]</div>
					<div class="form_input">
						<textarea id="description" name="description" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('description');?></textarea><br />
						<script>advance_mce('description');</script>					
					</div>
				</div>
				</div>
				</td>
			   </tr>
			</table>				
		</td>
		<td valign="top" style="width:320px;">
		<table width="100%" style="border: 1px dashed silver;" cellpadding="4" cellspacing="2">
				<tr>
					<td><strong>[[.Status.]]</strong></td>
					<td><?php echo Url::get('status','0');?></td>				
				</tr>				
				<tr>
					<td><strong>[[.Total_item.]]</strong></td>
					<td><?php echo Url::get('total_item','0');?></td>
				</tr>
				<tr>
					<td><strong>[[.Created.]]</strong></td>
					<td><?php echo date('h\h:i d/m/Y',Url::get('time',time()));?></td>					
				</tr>
				<tr>
					<td><strong>[[.Modified.]]</strong></td>
					<td><?php echo Url::get('last_time_update')?date('h\h:i d/m/Y',Url::get('last_time_update')):'Not modified';?></td>
				</tr>				
				</table>
		<div id="panel_1" style="margin-top:8px;">
			<span>[[.Parameters_article.]]</span>
			<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9" style="margin-top:2px;">
				<tr>
					<td>[[.parent_name.]]</td>
					<td><select name="parent_id" id="parent_id" class="select-large"></select></td>				
				</tr>
				<tr>
					<td>[[.url.]]</td>
					<td><input name="url" type="text" id="url" class="input-large"></td>				
				</tr>
				<tr>
					<td>[[.status.]]</td>
					<td><select name="status" id="status"  class="select"></select></td>				
				</tr>
				<tr>
					<td valign="top">[[.icon_url.]]</td>
					<td>
						<input name="icon_url" type="file" id="icon_url" class="file" size="18"><div id="delete_icon_url"><?php if(Url::get('icon_url') and file_exists(Url::get('icon_url'))){?>[<a href="<?php echo Url::get('icon_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('icon_url')));?>" onclick="jQuery('#delete_icon_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>					</td>				
				</tr>
			</table>
		  </div>
		</td>
		</tr>
		</table>
		<input name="extra_type" type="hidden" id="extra_type">
	</form>
</fieldset>