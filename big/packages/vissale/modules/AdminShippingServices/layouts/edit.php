<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_ss_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
			<span class="multi-edit-input" style="width:40px;"><input  name="mi_ss[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
			<span class="multi-edit-input"><input  name="mi_ss[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
			<span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;" id="del_#xxxx#" ><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_ss','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa hình thức giao hàng':' Quản lý hình thức giao hàng';?>
<section class="content">
<div class="row">
	<fieldset id="toolbar">
		<div class="col-xs-8">
            <h3 class="title"><?php echo $title;?></h3>
        </div>
        <div class="col-xs-4 text-right">
            <a class="btn btn-primary" onclick="EditAdminShippingServicesForm.submit();"> Lưu </a>
        </div>
	  </div>
	</fieldset>
    <br>
	<fieldset id="toolbar">
	<form name="EditAdminShippingServicesForm" method="post" enctype="multipart/form-data">
	<table class="table">
		<tr valign="top">
		<td>
		<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
		<table width="100%" cellpadding="5" cellspacing="0">
		<?php if(Form::$current->is_error())
		{
		?><tr valign="top">
		<td><?php echo Form::$current->error_messages();?></td>
		</tr>
		<?php
		}
		?>
	  <tr valign="top">
			<td>
			<div class="multi-item-wrapper">
		  <div id="mi_ss_all_elems">
			<div>
				<span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_ss',this.checked);"></span>
				<span class="multi-edit-input header" style="width:40px;">ID</span>
				<span class="multi-edit-input header" style="width:252px;">Hình thức giao hàng</span>
				<span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
				<br clear="all">
			</div>
		  </div>
			</div>
		<br clear="all">
		<hr>
			<div><input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_ss');"></div>
			<div>[[|paging|]]</div>
		<hr>
			</td>
		</tr>
		</table>
		<input name="confirm_edit" type="hidden" value="1" />
		<input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
		</td>
	</tr>
	</table>
	</form>
	</fieldset>
</div>
</section>
<script>
mi_init_rows('mi_ss',<?php if(isset($_REQUEST['mi_ss'])){echo MiString::array2js($_REQUEST['mi_ss']);}else{echo '[]';}?>);
	for(var i=101;i<=input_count;i++){
		/*
		if(getId('id_'+i) && (getId('is_system_'+i).value == 1)){
			jQuery('#name_'+i).attr('readonly',true);
			jQuery('#del_'+i).html('x');
			jQuery('#input_group_'+i).click(function(){alert('Trạng thái mặc định, quý khách vui lòng không sửa xoá ');return false;})
		}*/
	}
</script>
