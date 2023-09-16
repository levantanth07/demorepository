<?php 
$title = (URL::get('cmd')=='edit')?'Sửa danh mục phân loại':'Thêm danh mục phân lại khách hàng';
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<div class="container">
	<fieldset id="toolbar"><div id="title_region"></div></fieldset>
	<div class="list">
		<div><?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?></div>
		<form name="EditCrmCustomerGroupForm" method="post" >
		<table class="table">
			<tr>
				<td><div class="form_input_label">Danh mục cha:</div></td>
				<td><div class="form_input"><select name="parent_id" id="parent_id" class="form-control"></select></div></td>
			</tr>
			<tr>	
				<td><div class="form_input_label">Tên:</div></td>
				<td><div class="form_input"><input name="name" type="text" id="name" class="form-control"></div></td>
			</tr>
		</table>
		<input type="hidden" value="1" name="confirm_edit">
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#title_region').show();
		jQuery('#title_region').html('<table width="100%"><tr><td width="70%"><h3><?php echo $title;?></h3><\/td>\
		<td class="text-right" width="30%"><a href="#a" class="btn btn-primary" onclick="EditCrmCustomerGroupForm.submit();return false;">Ghi lại<\/a> <a href="#" class="btn btn-default" onclick="location=\'<?php echo URL::build_current();?>\';return false;">Quay lại<\/a><?php if($action=='edit'){?><a href="#a" class="btn btn-danger" onclick="location=\'<?php echo URL::build_current(array('cmd'=>'delete','id'));?>\';">Xóa<\/a><?php }?><\/td>\
		<\/td><\/tr><\/table>');
	});
</script>
	
