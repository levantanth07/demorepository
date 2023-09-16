<div style="display:none">
	<div id="mi_bundle_sample">
        <div id="mi_bundle_#xxxx#">
            <div id="input_group_#xxxx#" class="row" style="padding-bottom:5px;margin-bottom:5px;">
                <div class="col-xs-1 hidden">
                   <input  type="checkbox" id="_checked_#xxxx#" tabindex="-1">
                </div>
                <div class="col-xs-2">
                    <input  name="mi_bundle[#xxxx#][id]" type="text" id="id_#xxxx#" class="form-control"  value="(auto)" tabindex="-1" readonly>
                </div>
                <div class="col-xs-3">
                    <input  name="mi_bundle[#xxxx#][name]" class="form-control" type="text" id="name_#xxxx#" readonly>
                </div>
                <div class="col-xs-3">
                    <input  name="mi_bundle[#xxxx#][name2]" class="form-control" type="text" id="name2_#xxxx#" readonly>
                </div>
                <div class="col-xs-3">
                    <select name="mi_bundle[#xxxx#][ref_id]" class="form-control" type="text" id="ref_id_#xxxx#">[[|bundle_options|]]</select>
                </div>
            </div>
        </div>
	</div>
</div>

<div class="container-fluid" style="padding: 30px 0">
    <div class="row">
        <div class="col-xs-12">
            <form name="EditAdminBundlesForm" id="EditAdminBundlesForm" method="post" enctype="multipart/form-data" class="form-inline">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px"><?=URL::get('cmd')=='delete' ? 'Xóa phân loại sản phẩm' : ' Quản lý phân loại sản phẩm ';?></strong>
                        <div class="pull-right">
                            <div style="display: inline-flex;padding: 0 20px;"><b>Ghi chú: </b><div style="display:  inline-block;width: 30px;height: 20px;background: #dbf4fd;margin-left: 20px;margin-right: 20px;box-shadow: 1px 2px 3px 0 #00000075;"></div>
                            <b>Phân loại chuẩn hóa</b></div>
                            <div class="form-group">
                                <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Tìm kiếm" onchange="EditAdminBundlesForm.submit();">
                            </div>
                        </div>
                    </div>
                    <div class="panel-body form-inline">
                        <table align="right">
                            <tbody>
                                <tr>
                                    <td id="toolbar-save" align="center"><a onclick="EditAdminBundlesForm.submit();">
                                        <span title="Edit"> </span> Ghi lại </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?=Form::$current->is_error() && Form::$current->error_messages();?>
                        <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                        <div class="multi-item-wrapper">
                            <div id="mi_bundle_all_elems">
                                <div class="row">
                                    <div class="col-xs-1 hidden"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_bundle',this.checked);"></div>
                                    <div class="col-xs-2"><label>ID</label></div>
                                    <div class="col-xs-3"><label>Tên phân loại</label></div>
                                    <div class="col-xs-3"><label>Tên cũ</label></div>
                                    <div class="col-xs-3"><label>Nhóm phân loại hệ thống</label></div>
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
$(document).keypress(function(e) {
    if(e.which == 13) {
        return;
        mi_add_new_row('mi_bundle');
        $('#mi_bundle_'+input_count).css({'background-color':'#c8fcff'});
        return false;
    }
});
</script>
