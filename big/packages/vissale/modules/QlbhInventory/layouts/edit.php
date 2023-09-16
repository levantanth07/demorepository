<script type="text/javascript" src="packages/core/includes/js/multi_items.js"></script>
<script type="text/javascript">
	var product_arr = <?php echo MiString::array2js([[=products=]]);?>;
</script>
<span style="display:none">
	<span id="mi_product_group_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<input  name="mi_product_group[#xxxx#][id]" type="hidden" id="id_#xxxx#">
			<span class="multi-edit-input"><input  name="mi_product_group[#xxxx#][product_code]" style="width:100px;" type="text" id="code_#xxxx#" class="form-control" onblur="getProductFromCode('#xxxx#',this.value);" AUTOCOMPLETE=OFF></span>
			<span class="multi-edit-input"><input  name="mi_product_group[#xxxx#][product_name]" style="width:200px;" type="text" readonly class="form-control" id="name_#xxxx#" tabindex="-1"></span>
			<span class="multi-edit-input"><input  name="mi_product_group[#xxxx#][opening_stock]" style="width:100px;" type="text" id="opening_stock_#xxxx#" class="form-control"></span>
            <span class="multi-edit-input"><input  name="mi_product_group[#xxxx#][unit]" style="width:50px;" type="text" id="unit_#xxxx#" readonly tabindex="-1" class="form-control"></span>
			<span class="multi-edit-input"><img src="<?php echo Portal::template('core');?>/images/buttons/delete.gif" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product_group','#xxxx#','group_');if(document.all)event.returnValue=false; else return false;" style="cursor:pointer;"/></span>
		</div>
        <br clear="all">
	</span>
</span>
<div class="product-bill-bound">
<form name="EditQlbhInventoryForm" method="post">
	<input  name="group_deleted_ids" id="group_deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
	<table class="table">
		<tr>
        	<td width="80%" class="form-title">[[|title|]]</td>
            <td width="20%" align="right" nowrap="nowrap">
				<a href="#" class="btn btn-default" data-toggle="modal" data-target="#ImportExcelModal"><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span> Import Excel </a>
            	<input name="save" type="submit" value="Ghi lại" class="btn btn-primary">
				<a href="<?php echo Url::build_current(array('type'));?>"  class="btn btn-default">Quay lại</a>
            </td>
        </tr>
    </table>
	<div class="row">
		<?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
		<fieldset>
        <div class="col-lg-3">
          <div class="input-group">
           <span class="input-group-addon">Kho</span><select name="warehouse_id" id="warehouse_id" class="form-control"></select>
          </div>
        </div>
      </fieldset><br />
		 <div class="col-lg-12">
            <div id="mi_product_group_all_elems" style="text-align:left;width:100%;">
            	<span style="white-space:nowrap;">
                <span class="multi-edit-input header" style="width:102px;">Mã mặt hàng</span>
                <span class="multi-edit-input header" style="width:202px;">Tên mặt hàng</span>
                <span class="multi-edit-input header" style="width:102px;">Tồn đầu kỳ</span>
                <span class="multi-edit-input header" style="width:50px;">Đơn vị</span>
                </span><br clear="all">
            </div>
            <input type="button" value="Thêm mới" onclick="mi_add_new_row('mi_product_group');myAutocomplete(input_count);" class="btn btn-info">
		</div>
	</div>
</form>	
</div>

<div class="modal fade" id="ImportExcelModal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form name="ImportExcelForm" id="ImportExcelForm" method="post" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Import tồn đầu kỳ từ Excel</h4>
				</div>
				<div class="modal-body">
					<div class="pd10 text-center">----------------Chọn file Excel-----------------</div><br>
					<div class="error"><?php echo Form::error_messages();?></div>
					File mẫu: <a title="Tải về file mẫu" href="assets/vissale/images/excel_sp_mau.xlsx" target="_blank"><img src="assets/vissale/images/file_excel_sp_mau.png" width="90%" alt=""></a>
					<hr>
					<div class="form-group">
						<input  name="excel_file" type="file" id="excel_file" required>
					</div>
				</div>
				<div class="modal-footer text-center">
					<input name="importExcelBtn" type="submit" class="btn btn-primary" value="Import">
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	function getProductFromCode(id,value){
		if(getId('code_'+id)){
			if(typeof(product_arr[value])=='object'){
				getId('name_'+id).value = product_arr[value]['name'];
				getId('unit_'+id).value = product_arr[value]['unit'];
				getId('name_'+id).className = '';
			}else{
				//getId('name_'+id).className = 'notice';
				if(value){
					getId('name_'+id).value = 'Sản phẩm không tồn tại';
				}else{
					getId('name_'+id).value = '';
				}
				getId('unit_'+id).value = '';
			}
		}
	}
	function myAutocomplete(id)
	{
		jQuery("#code_"+id).autocomplete({
			source:'get_product.php',
			minChars: 3,
			width: 223,
			matchContains: true,
			autoFill: true,
			cacheLength:10,
			max:100,
			select: function( event, ui ) {
				jQuery('#code_'+id).val(ui.item.id);
			}
		});
	}
	mi_init_rows('mi_product_group',<?php echo isset($_REQUEST['mi_product_group'])?MiString::array2js($_REQUEST['mi_product_group']):'{}';?>);
</script>
