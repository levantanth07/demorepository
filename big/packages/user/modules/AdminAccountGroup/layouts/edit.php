<script src="packages/core/includes/js/multi_items.js"></script>
<script src="packages/core/includes/js/common.js"></script>
<style>
    .multi-edit-input input, select{
        width: auto;
        padding: 2px;
        margin: auto !important;
        border: 1px solid #CCC;
        height: 28px;
        background: #FFF;}
</style>
<span style="display:none">
	<span id="mi_account_group_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
            <span class="multi-edit-input hidden" style="width:40px;"><input  name="mi_account_group[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input"><input  name="mi_account_group[#xxxx#][name]" style="width:300px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
            <span class="multi-edit-input">
                <select name="mi_account_group[#xxxx#][admin_user_id]" style="width:100%;display: none" class="form-control multiselect" id="admin_user_id_#xxxx#">[[|admin_user_ids_options|]]</select>
            </span>
            <span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_account_group','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php $title = (URL::get('cmd')=='delete')?'Xóa nhóm tài khoản':' Quản lý đội / nhóm theo tài khoản';?>
<br>
<div class="container">
<div class="row">
	<div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3>
            <div class="box-tools pull-right">
                <a onclick="EditAdminaccount_groupForm.submit();" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Ghi lại </a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <form name="EditAdminaccount_groupForm" method="post" enctype="multipart/form-data">
                        <table class="table">
                            <tr valign="top">
                                <td>
                                    <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                                    <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                                    <div class="multi-item-wrapper">
                                        <div id="mi_account_group_all_elems">
                                            <div>
                                                <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_account_group',this.checked);"></span>
                                                <span class="multi-edit-input header hidden" style="width:40px;">ID</span>
                                                <span class="multi-edit-input header" style="width:302px;">Tên nhóm</span>
                                                <span class="multi-edit-input header" style="width:302px;">Trưởng nhóm</span>
                                                <span class="multi-edit-input header" style="width:45px;text-align:center">Xóa</span>
                                                <br clear="all">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div><input type="button" value="+ Thêm" class="btn btn-success btn-sm" onclick="mi_add_new_row('mi_account_group');"></div>
                                    <div>[[|paging|]]</div>
                                    <input  name="confirm_edit" type="hidden" value="1" />
                                    <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-warning">
                                <div class="box-header">
                                    <h4 class="box-title">Trưởng nhóm sale yêu cầu:</h4>
                                </div>
                                <div class="box-body">
                                    - Có quyền sale<br>
                                    - Thuộc vào nhóm cần quản lý
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="box box-warning">
                                <h4 class="box-title">Trưởng nhóm marketing yêu cầu:</h4>
                                <div class="box-body">
                                    - Có quyền marketing<br>
                                    - Thuộc vào nhóm cần quản lý
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="box box-warning">
                                <h4 class="box-title">Chú ý:</h4>
                                <div class="box-body">
                                    - 1 Người có thể quản lý nhiều nhóm 1 lúc
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
</div>
<script>
    var initData = <?=(isset($_REQUEST['mi_account_group']))?MiString::array2js($_REQUEST['mi_account_group']):'[]';?>;
    mi_init_rows('mi_account_group',initData);
    $('.multiselect').multiselect(
        {
            enableFiltering:true,
            buttonWidth: '300px',
            maxHeight: 200
        }
    );
</script>
