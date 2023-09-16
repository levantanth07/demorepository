<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_bundle_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
      <span class="multi-edit-input" style="width:80px;"><input  name="mi_bundle[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:80px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
      <span class="multi-edit-input"><input  name="mi_bundle[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#" readonly></span>
		</div>
    <br clear="all">
	</span>
</span>

<div class="container-fluid" style="padding: 30px 0">
    <div class="row">
        <div class="col-xs-12">
            <form name="EditAdminUnitsForm" method="post" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px"><?=URL::get('cmd')=='delete' ? 'Xóa đơn vị' : ' Quản lý đơn vị ';?></strong>
                        <div class="pull-right">
                            <div style="display: inline-flex;padding: 0 20px;"><b>Ghi chú: </b><div style="display:  inline-block;width: 30px;height: 20px;background: #dbf4fd;margin-left: 20px;margin-right: 20px;box-shadow: 1px 2px 3px 0 #00000075;"></div>
                            <b>Đơn vị chuẩn hóa</b></div>
                            
                        </div>
                    </div>

                    <div class="panel-body form-inline">
                        <?=Form::$current->is_error() && Form::$current->error_messages();?>
						<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
						<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
						<input name="confirm_edit" type="hidden" value="1" />
						<input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
						<div class="multi-item-wrapper">
							 <div id="mi_bundle_all_elems">
								<div>
								  <span class="multi-edit-input header" style="width:80px;">ID</span>
								  <span class="multi-edit-input header" style="width:250px;">Tên phân loại sản phẩm</span>
								  <br clear="all">
								</div>
						   </div>
						</div>
						<div>[[|paging|]]</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
const ROW_CALLBACKS = {
    disable_edit_standardized: function(itemElement, data, rowID){
        const STANDARDIZED = parseInt(data.standardized);
        let idEl = itemElement.querySelector('input[id^=id]');
        let nameEl = itemElement.querySelector('input[id^=name]');

        if(STANDARDIZED){
            idEl.style.background = '#dbf4fd';
            nameEl.style.background = '#dbf4fd';
        }
    }
}
mi_init_rows(
	'mi_bundle',
	<?php if(isset($_REQUEST['mi_bundle'])){echo MiString::array2js($_REQUEST['mi_bundle']);}else{echo '[]';}?>,
	ROW_CALLBACKS
);
</script>
