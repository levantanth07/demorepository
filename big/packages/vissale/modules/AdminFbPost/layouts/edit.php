<script src="packages/core/includes/js/multi_items.js"></script>
<div style="display:none">
	<div id="mi_product_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:40px;"><input  name="mi_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][post_id]" style="width:300px;" class="multi-edit-text-input" type="text" id="post_id_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][description]" style="width:250px;" class="multi-edit-text-input" type="text" id="description_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][page_id]" style="width:200px;" class="multi-edit-text-input" type="text" id="page_id_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][bundle_id]" style="width:150px;" class="multi-edit-text-input" id="bundle_id_#xxxx#" tabindex="#xxxx#">[[|bundles_options|]]</select></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][gearman_worker]" style="width:150px;" class="multi-edit-text-input" id="gearman_worker_#xxxx#" tabindex="-1">[[|mkt_options|]]</select></span>
            <span class="multi-edit-input no-border" id="delete_#xxxx#" style="width:40px;text-align:center;padding-top:5px;"><a href="#" class="btn btn-default btn-sm" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','');event.returnValue=false;" alt="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
        </div>
	</div>
</div>
<hr color="#FFF" size="1">
<?php
$title = (URL::get('cmd')=='delete')?'Xóa khai báo Facebook':' Quản lý khai báo Facebook';?>
<div class="container full">
    <div class="box box-default">
        <div class="box-header">
            <div class="box-title">
                <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo $title;?>
            </div>
            <div class="box-tools">
                <a onclick="if(checkValidate()){EditAdminFbPostForm.submit();}" class="btn btn-primary">Ghi lại </a>
                <a href="#" class="btn btn-danger" onClick="mi_delete_selected_row('mi_product');return false;"> <span title="Xoá"></span> Xóa </a>
            </div>
        </div>
        <div class="box-body">
            <form  name="EditAdminFbPostForm" method="post">
                <div>
                    <input name="keyword" type="text" id="keyword" placeholder="Tìm id bài post hoặc id page" style="width: 300px;"> <input name="search" type="submit" value="Tìm kiếm">
                </div>
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
                                            <div id="mi_product_all_elems">
                                                <div style="width: 1200px;overflow: auto;">
                                                    <span class="multi-edit-input header" style="width:40px;">ID</span>
                                                    <span class="multi-edit-input header" style="width:302px;">Mã ID bài post</span>
                                                    <span class="multi-edit-input header" style="width:252px;">Tên bài post </span>
                                                    <span class="multi-edit-input header" style="width:202px;">ID page </span>
                                                    <span class="multi-edit-input header" style="width:152px;">Loại sản phẩm</span>
                                                    <span class="multi-edit-input header" style="width:152px;">Tài khoản MKT</span>
                                                    <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
                                                    <br clear="all">
                                                </div>
                                            </div>
                                        </div>
                                        <br clear="all">
                                        <hr>
                                        <div>([[|total|]] bài post) / <input type="button" value=" + Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_product');"> (Sau khi thêm bạn nhớ nhấn nút Ghi lại)</div>
                                        <hr>
                                        <div>[[|paging|]]</div>
                                    </td>
                                </tr>
                            </table>
                            <input  name="confirm_edit" type="hidden"  value="1" />
                            <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<script>
mi_init_rows('mi_product',<?php if(isset($_REQUEST['mi_product'])){echo MiString::array2js($_REQUEST['mi_product']);}else{echo '[]';}?>);

function checkValidate(){
    for(var i=100;i<=input_count;i++){
        if(getId('page_id_'+i) && !getId('page_id_'+i).value){
            alert('Bạn vui lòng nhập ID page');
            getId('page_id_'+i).focus();
            return false;
        }
        if(getId('bundle_id_'+i) && !getId('bundle_id_'+i).value){
            alert('Bạn vui lòng nhập phân loại sản phẩm');
            getId('bundle_id_'+i).focus();
            return false;
        }
        if(getId('gearman_worker_'+i) && !getId('gearman_worker_'+i).value){
            alert('Bạn vui lòng nhập tài khoản mkt');
            getId('gearman_worker_'+i).focus();
            return false;
        }
    }
    return true;
}
jQuery(document).ready(function(){
        //////////
        //suggestCustomer();
        $(document).keypress(function(e) {
            if(e.which == 13) {
              mi_add_new_row('mi_product');
              return false;
            }
        });
    }
);  
</script>
