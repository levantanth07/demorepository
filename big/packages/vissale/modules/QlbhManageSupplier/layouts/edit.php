<span style="display:none">
	<span id="mi_supplier_sample">
	  <div id="input_group_#xxxx#" class="multi-item-group">
          <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
          <span class="multi-edit-input hidden"><input  name="mi_supplier[#xxxx#][id]" type="text" id="id_#xxxx#" class="form-control" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
          <span class="multi-edit-input"><input  name="mi_supplier[#xxxx#][code]" style="width:200px;" class="form-control" type="text" id="code_#xxxx#" onchange="CheckDuplicatedCode(this);"></span>
          <span class="multi-edit-input"><input  name="mi_supplier[#xxxx#][name]" style="width:200px;" class="form-control" type="text" id="name_#xxxx#"></span>
          <span class="multi-edit-input"><input  name="mi_supplier[#xxxx#][phone]" style="width:150px;" class="form-control" type="text" id="phone_#xxxx#"></span>
          <span class="multi-edit-input"><input  name="mi_supplier[#xxxx#][address]" style="width:300px;" class="form-control" type="text" id="address_#xxxx#"></span>
          <span class="multi-edit-input"><select  name="mi_supplier[#xxxx#][zone_id]" style="width:150px;" id="zone_id_#xxxx#">[[|zone_id_options|]]</select></span>
          <span  class="btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_supplier','#xxxx#','');event.returnValue=false;"> <i class="fa fa-trash-o"></i> </span>
	  </div><br clear="all">
	</span>
</span>
<?php 
$title = (URL::get('cmd')=='delete')?'Xóa nhà cung cấp':'Danh sách nhà cung cấp';?>
<div class="container"><br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3>
            <div class="box-tools">
                <a href="javascript:void(0)" onclick="EditQlbhManageSupplierForm.submit();"  class="btn btn-primary"><i class="fa fa-floppy-o"></i> Lưu</a>
            </div>
        </div>
        <div class="box-body">
            <form name="EditQlbhManageSupplierForm" method="post" enctype="multipart/form-data">
                <table cellspacing="0" width="100%" class="multi-item-table">
                    <tr valign="top">
                        <td>
                            <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                            <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                            <?php if(Form::$current->is_error()){?><div><?php echo Form::$current->error_messages();?></div><?php }?>
                            <table class="table">
                                <tr valign="top">
                                    <td>
                                        <div class="multi-item-wrapper">
                                  <span id="mi_supplier_all_elems">
                                    <span style="white-space:nowrap;">
                                      <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_supplier',this.checked);"></span>
                                      <span class="multi-edit-input header hidden" style="width:40px;">ID</span>
                                      <span class="multi-edit-input header" style="width:202px;">Mã nhà cung cấp</a></span>
                                      <span class="multi-edit-input header" style="width:202px;">Tên nhà cung cấp</a></span>
                                      <span class="multi-edit-input header" style="width:152px;">Điện thoại</a></span>
                                      <span class="multi-edit-input header" style="width:302px;">Địa chỉ</a></span>
                                      <span class="multi-edit-input header" style="width:152px;">Tỉnh thành</a></span>
                                      <span class="multi-edit-input header no-border no-bg" style="width:20px;">&nbsp;</span>
                                      <br clear="all">
                                    </span>
                                  </span>
                                        </div>
                                        <br clear="all">
                                        <div style="padding:5px;"><input type="button" value="   Thêm mới   " onclick="mi_add_new_row('mi_supplier');" class="btn btn-warning"></div>
                                        <div>[[|paging|]]</div>
                                    </td>
                                </tr>
                            </table>
                            <input name="confirm_edit" type="hidden" value="1" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<script src="packages/core/includes/js/multi_items.js"></script>
<script>
mi_init_rows('mi_supplier',<?php if(isset($_REQUEST['mi_supplier'])){echo MiString::array2js($_REQUEST['mi_supplier']);}else{echo '[]';}?>);
</script>
<script>
jQuery(document).ready(function(){
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i)){
			
		}
	}
});
function CheckDuplicatedCode(obj){
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i)){
			if(getId('code_'+i).id != obj.id && getId('code_'+i).value == obj.value){
				alert('Trùng mã "' + obj.value +'". Bạn vui lòn chọn mã khác');
				obj.value = '';
				obj.focus();
			}
		}
	}
}
</script>

