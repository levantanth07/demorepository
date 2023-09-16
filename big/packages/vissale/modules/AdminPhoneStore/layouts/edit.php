<script src="packages/core/includes/js/multi_items.js"></script>
<div style="display:none">
	<div id="mi_bundle_sample">
		<div id="input_group_#xxxx#" class="row" style="border-bottom:1px solid #CCC;padding-bottom:5px;margin-bottom:5px;">
            <div class="col-xs-1">
               <input  type="checkbox" id="_checked_#xxxx#" tabindex="-1">
            </div>
            <div class="col-xs-1">
                <input  name="mi_bundle[#xxxx#][id]" type="text" id="id_#xxxx#" class="form-control"  value="(auto)" tabindex="-1" readonly>
            </div>
            <div class="col-xs-4">
                <input  name="mi_bundle[#xxxx#][name]" class="form-control" type="text" id="name_#xxxx#">
            </div>
            <div class="col-xs-2">
                <input  name="mi_bundle[#xxxx#][total_group]" class="form-control" type="text" id="total_group_#xxxx#" readonly>
            </div>
            <div class="col-xs-3">
                <a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_bundle','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
            </div>
		</div>
	</div>
</div>
<?php $title = (URL::get('cmd')=='delete')?'Xóa kho':' Quản lý kho số';?>
<br>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h3 class="title"><?php echo $title;?></h3>
        </div>
        <div class="col-md-4 text-right">
            <a onclick="EditAdminPhoneStoreForm.submit();" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Ghi lại </a>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <form name="EditAdminPhoneStoreForm" method="post" enctype="multipart/form-data">
                <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                <div class="multi-item-wrapper">
                    <div id="mi_bundle_all_elems">
                        <div class="row">
                            <div class="col-xs-1"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_bundle',this.checked);"> All</div>
                            <div class="col-xs-1"><label>ID</label></div>
                            <div class="col-xs-4"><label>Tên kho số</label></div>
                            <div class="col-xs-2"><label>Tổng số cty</label></div>
                            <div class="col-xs-3"><label>Xóa</label></div>
                        </div>
                    </div>
                </div>
                <div class="text-right"><input type="button" value="+ Thêm kho" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_bundle');"></div>
                <div class="pt">[[|paging|]]</div>
                <hr>
                <input  name="confirm_edit" type="hidden" value="1" />
                <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
            </form>
        </div>
    </div>
</div>
<script>
mi_init_rows('mi_bundle',<?php if(isset($_REQUEST['mi_bundle'])){echo MiString::array2js($_REQUEST['mi_bundle']);}else{echo '[]';}?>);
</script>
