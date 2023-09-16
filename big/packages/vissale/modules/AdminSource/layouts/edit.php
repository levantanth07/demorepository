<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
		<span id="mi_order_source_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<span class="multi-edit-input" style="width:20px;" id="checkbox_#xxxx#">
				<input  type="checkbox" id="_checked_#xxxx#" tabindex="-1">
			</span>
			<span class="multi-edit-input" style="width:40px;">
				<input  name="mi_order_source[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly><input  name="mi_order_source[#xxxx#][group_id]" type="hidden" id="group_id_#xxxx#" class="multi-edit-text-input" tabindex="-1">
			</span>
			<span class="multi-edit-input">
				<input  name="mi_order_source[#xxxx#][name2]" style="width:250px;" class="multi-edit-text-input" type="text" id="name2_#xxxx#" disabled>
			</span>
			<span class="multi-edit-input">
				<input  name="mi_order_source[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#">
			</span>
			<span class="multi-edit-input">
				<input  name="mi_order_source[#xxxx#][default_select]" style="width:80px;" class="multi-edit-text-input" type="checkbox" id="default_select_#xxxx#">
			</span>
			<span class="multi-edit-input" style="width:250px;">
				<select name="mi_order_source[#xxxx#][ref_id]" class="form-control" type="text" id="ref_id_#xxxx#">[[|systemSources|]]</select>
			</span>
      		<span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;" id="del_#xxxx#"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_order_source','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    	<br clear="all">
	</span>
</span>
<?php 
	$title = (URL::get('cmd')=='delete')?'Xóa nguồn đơn hàng':' Quản lý Nguồn đơn hàng';
?>
<section class="content">
<div class="row">
	<fieldset id="toolbar">
		<div id="toolbar-title">
			<?php echo $title;?>
		</div>
		<div id="toolbar-content">
		<table align="right">
		  <tbody>
			<tr>
			  	<td id="toolbar-save" align="center"><a onclick="EditAdminSourceForm.submit();">
					<span title="Edit"> </span> Ghi lại </a>
				</td>
			</tr>
		  </tbody>
		</table>
	  </div>
	</fieldset><br>
	<fieldset id="toolbar">
	<form name="EditAdminSourceForm" method="post" enctype="multipart/form-data">
	<table class="table">
		<tr valign="top">
		<td>
		<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
		<table class="table">
	  <tr valign="top">
			<td>
			<div class="multi-item-wrapper">
		  <div id="mi_order_source_all_elems">
			<div>
			  <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_order_source',this.checked);"></span>
			  <span class="multi-edit-input header" style="width:40px;">ID</span>
			  <span class="multi-edit-input header" style="width:250px;">Tên cũ</span>
			  <span class="multi-edit-input header" style="width:250px;">Tên mới</span>
			  <span class="multi-edit-input header" style="width:80px;">Mặc định</span>
			  <span class="multi-edit-input header" style="width:250px;">Nguồn hệ thống</span>
			  <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
			  <br clear="all">
			</div>
		  </div>
			</div>
		<br clear="all">
		<hr>
			<!--IF:cond([[=allowAddAdminSource=]])-->
			<div>
				<input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_order_source');">
			</div>
			<!--/IF:cond-->
			<div>[[|paging|]]</div>
			<hr>
			</td>
		</tr>
		</table>
		<input  name="confirm_edit" type="hidden" value="1" />
		<input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
		</td>
	</tr>
	</table>
	</form>
	</fieldset>
</div>
</section>
<script>
	const allowAddAdminSource = '[[|allowAddAdminSource|]]';
	mi_init_rows('mi_order_source',<?php if(isset($_REQUEST['mi_order_source'])){echo MiString::array2js($_REQUEST['mi_order_source']);}else{echo '[]';}?>);
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i) && (getId('group_id_'+i).value == 0)){
			$('#input_group_'+i).css('background-color', '#dbf4fd');
			jQuery('#id_'+i).css('background-color', '#dbf4fd');
			jQuery('#name_'+i).attr('readonly',true).css('background-color', '#dbf4fd');
			jQuery('#name2_'+i).attr('readonly',true).css('background-color', '#dbf4fd');
			jQuery('#ref_id_'+i).attr('disabled',true).css('background-color', '#dbf4fd');
			jQuery('#default_select_'+i).attr('disabled',true).css('background-color', '#dbf4fd');
			jQuery('#checkbox_'+i).html('x').css('background-color', '#dbf4fd');
			jQuery('#del_'+i).html('x').css('background-color', '#dbf4fd');
			jQuery('#name_'+i).click(function(){alert('Nguồn hệ thống, quý khách vui lòng không sửa xoá ');return false;})
		}
	}
	
	$(document).ready(function () {
		if (!allowAddAdminSource) {
			$('.multi-edit-text-input').attr("readonly", true);
			$('span[id^="checkbox_"]').html('x');
			$('span[id^="del_"]').html('x');
		}//end if
	});
</script>
