<style type="text/css">
    .multi-item-group.del{background: #CCC;}
    .multi-item-group.del input,.multi-item-group.del select{background: #CCC;}
    .multi-item-group input:read-only {outline: none; background: #e8e8e8 !important; }
    .multi-edit-input.header{height:30px;}
    .multi-edit-input{vertical-align: bottom;height:90px;}
</style>
<script src="packages/core/includes/js/multi_items.js"></script>
<script src="packages/core/includes/js/common.js"></script>

<link rel="stylesheet" href="assets/vissale/css/jquery-confirm.css">
<script src="assets/vissale/js/jquery-confirm.js"></script>
<div style="display:none">
	<div id="mi_product_sample">
        <div id="input_group_#xxxx#" class="multi-item-group" style="width: 1295px;overflow-x: auto;">
            <span class="multi-edit-input hidden" style="width:40px;"><input  name="mi_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input" style="width:82px;">
                <img id="img_image_url_#xxxx#" src="assets/standard/images/no_product_image.png" width="80" height="60"><br>
                <input  name="image_url_#xxxx#"  id="image_url_#xxxx#" title="Bạn vui lòng tải ảnh <= 1Mb" data-toggle="tooltip" onchange="PreviewImage('image_url_#xxxx#', 'img_image_url_#xxxx#');$('#img_image_url_#xxxx#').css({'border':'1px solid #f00'});" style="width:100%;" class="multi-edit-text-input" type="file" tabindex="#xxxx#">
            </span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][code]" style="width:120px;" class="multi-edit-text-input" type="text" id="code_#xxxx#" tabindex="#xxxx#" placeholder="Ví dụ: SP01" onchange="checkDuplicated('#xxxx#');"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][name]" style="width:200px;font-size:14px;color:#337ab7" class="multi-edit-text-input" type="text" id="name_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][price]" style="width:100px;color:#118009;font-weight: bold;" class="multi-edit-text-input text-right" type="text" id="price_#xxxx#" tabindex="#xxxx#"></span>
            <!-- <span class="multi-edit-input"><input name="mi_product[#xxxx#][import_price]" style="width:100px;color:#4e54c8;font-weight: bold;" class="multi-edit-text-input text-right" type="text" id="import_price_#xxxx#" tabindex="#xxxx#"></span> -->
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][weight]" style="width:93px;" class="multi-edit-text-input text-center input-weight" type="text" id="weight_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][color]" style="width:100px;" class="multi-edit-text-input" type="text" id="color_#xxxx#" tabindex="-1"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][size]" style="width:100px;" class="multi-edit-text-input" type="text" id="size_#xxxx#" tabindex="-1"></span>
            <input  name="mi_product[#xxxx#][standardized]" style="width:100px;" class="multi-edit-text-input" type="hidden" id="standardized_#xxxx#" tabindex="-1">
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][bundle_id]" style="width:120px;" class="multi-edit-text-input form-control" id="bundle_id_#xxxx#" tabindex="-1">[[|bundle_options|]]</select></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][unit_id]" style="width:100px;" class="multi-edit-text-input form-control" id="unit_id_#xxxx#" tabindex="-1">[[|unit_options|]]</select></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][total_order]" style="width:50px;" class="multi-edit-text-input" type="text" id="total_order_#xxxx#" readonly="" tabindex="-1"></span>
            <span class="multi-edit-input" id="hide_#xxxx#" style="width:45px;text-align:center;padding-top:5px;" title="Tính năng ẩn giúp ẩn sản phẩm khỏi danh sách chọn khi tạo đơn hàng hay ngừng kinh doanh"><input  name="mi_product[#xxxx#][del]" type="checkbox" id="del_#xxxx#"></span>
            <span class="multi-edit-input" id="delete_#xxxx#" style="width:40px;text-align:center;padding-top:5px;"><button type="button" class="btn btn-default btn-sm" title="Nhấn xoá xong bạn nhớ lưu lại" onClick="$.confirm({title: 'XÓA SẢN PHẨM !',content: 'Bạn có chắc chắn muốn xóa sản phẩm này ?<br> (Hãy chú ý có thể ảnh hưởng tới đơn hàng)',buttons: {confirm: function(){mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','');},cancel: function(){/*ko xoa*/;}}});return false;" alt="Xóa"><i class="fa fa-trash-o"></i></button></span>
        </div>
	</div>
</div>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa sản phẩm':' Quản lý sản phẩm';?>
<div class="container full">
    <br>
    <form  name="EditAdminProductsForm" method="post" enctype="multipart/form-data">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo $title;?></h3>
                <div class="box-tools pull-right">
                    <i class="fa fa-search"></i> <input name="keyword" type="text" id="keyword" placeholder="Tìm sản phẩm"> <input class="btn btn-default btn-sm" name="search" type="submit" value="Tìm kiếm">
                    <?php if(!EditAdminProductsForm::$isOBD && empty(EditAdminProductsForm::$system['f0'])):?>
                    <a href="#" class="btn btn-default" data-toggle="modal" data-target="#ImportExcelModal"><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span> Import Excel </a>
                    <?php endif;?>
                    <a href="<?=Url::build_current(['do'=>'export_excel'])?>" class="btn btn-default"><span class="glyphicon fa fa-cloud-download" aria-hidden="true"></span> Export Excel </a>
                    <a onclick="if(checkValidate()){EditAdminProductsForm.submit();}" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Lưu</a>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs">
                    <li <?php echo !Url::get('del')?' class="active"':'';?>><a href="<?php echo Url::build_current(array('keyword'))?>">Danh sách sản phẩm hàng hoá</a></li>
                    <li <?php echo Url::get('del')?' class="active"':'';?>><a style="color:#f00;" href="<?php echo Url::build_current(array('keyword','del'=>1))?>">Sản phẩm ngừng kinh doanh (Ẩn)</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active">
                        <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                        <?php if(Form::$current->is_error())
                        {
                            ?>
                            <div><?php echo Form::$current->error_messages();?></div>
                            <?php
                        }
                        ?>
                        <div class="multi-item-wrapper">
                            <div id="mi_product_all_elems">
                                <div style="width: 1305px;overflow-x: auto;">
                                    <span class="multi-edit-input header hidden" style="width:40px;">ID</span>
                                    <span class="multi-edit-input header" style="width:82px;">Ảnh</span>
                                    <span class="multi-edit-input header" style="width:122px;">Mã SP</span>
                                    <!--<span class="multi-edit-input header" style="width:152px;">Mã SP & Size</span>-->
                                    <span class="multi-edit-input header" style="width:202px;">Tên sản phẩm </span>
                                    <span class="multi-edit-input header" style="width:102px;">Giá bán</span>
                                    <!-- <span class="multi-edit-input header" style="width:102px;">Giá vốn</span> -->
                                    <span class="multi-edit-input header" style="width:95px;">K.L (Gram)(*)</span>
                                    <span class="multi-edit-input header" style="width:102px;">Mầu</span>
                                    <span class="multi-edit-input header" style="width:102px;">Size</span>
                                    <span class="multi-edit-input header" style="width:122px;">Phân Loại</span>
                                    <span class="multi-edit-input header" style="width:102px;">Đơn vị</span>
                                    <span class="multi-edit-input header" style="width:52px;">Số ĐH</span>
                                    <span class="multi-edit-input header" style="width:45px;color:#F00 !important;">Ẩn</span>
                                    <span class="multi-edit-input header" style="width:45px;">Xóa</span>
                                    <br clear="all">
                                </div>
                            </div>
                        </div>
                        <br clear="all">
                        <hr>
                        <div>([[|total|]] sản phẩm)</div>
                        <!-- <div>([[|total|]] sản phẩm) / <input type="button" value="+ Thêm sản phẩm" class="btn btn-warning btn-sm" onclick="mi_add_new_row('mi_product');"> (Sau khi thêm bạn nhớ nhấn nút <strong>Lưu</strong>)</div> -->
                        <hr>
                        <div>[[|paging|]]</div>

                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Diễn giải</h3>
                                <div class="box-tools pull-right">
                                    <!-- Buttons, labels, and many other things can be placed here! -->
                                    <!-- Here is a label for example -->
                                    <span class="label label-warning">Chú ý</span>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                - <strong>Số ĐH</strong>: Số lượng đơn hàng đã thêm sản phẩm không phân biệt trạng thái đơn hàng.<br>
                                - Chỉ <span class="label label-danger">xoá</span> được sản phẩm khi Số ĐH = 0
                                <div class="error" style="color:#f00;"> - Tính năng ẩn giúp ẩn sản phẩm khỏi danh sách chọn khi tạo đơn hàng hay ngừng kinh doanh.</div>
                            </div>
                            <!-- /.box-body -->

                            <!-- box-footer -->
                        </div>
                        <!-- /.box -->
                        <input  name="confirm_edit" type="hidden"  value="1" />
                        <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                    </div>
                </div>
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
                    <h4 class="modal-title">Import sẩn phẩm từ Excel</h4>
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
<script>
    const IS_OBD = 'true';
    const ROW_CALLBACKS = {
        disable_edit_standardized: function(itemElement, data, rowID){
            const STANDARDIZED = parseInt(data.standardized);
            let codeEl = itemElement.querySelector('input[id^=code]');
            let nameEl = itemElement.querySelector('input[id^=name]');
            let delEl = itemElement.querySelector('input[id^=del]');
            let bundleEl = itemElement.querySelector('select[id^=bundle]');
            let unitEl = itemElement.querySelector('select[id^=unit]');
            // let costPriceEl = itemElement.querySelector('input[id^=import_price]');
            let weightEl = itemElement.querySelector('input[id^=weight]');
            
            codeEl.readOnly = true;
            nameEl.readOnly = true;
            bundleEl.disabled = true;
            unitEl.disabled = true;
            weightEl.disabled = IS_OBD;
            // costPriceEl.disabled = IS_OBD;
            // if(STANDARDIZED){
            //     delEl.value = data.del
            //     // delEl.type = 'hidden';
            // }
        }
    }
    mi_init_rows(
        'mi_product',
        <?=json_encode(isset($_REQUEST['mi_product']) ? $_REQUEST['mi_product'] : [])?>,
        ROW_CALLBACKS
    );
    for(var i=100;i<=input_count;i++){
        if(getId('total_order_'+i)){
            if (getId('total_order_'+i).value > 0) {
                $('#delete_'+i).css({'display':'none'});
            } else {
                $('#delete_'+i).css({'display':'block'});
            }
            <!--IF:cond(!Url::get('del'))-->
                getId('delete_'+i).innerHTML = 'x';
            <!--/IF:cond-->
        }
        if(getId('del_'+i) && getId('del_'+i).checked){
            //jQuery('#input_group_'+i).addClass('multi-item-group del');
        }
    }
    function checkValidate(){
        for(let i=101;i<=input_count;i++){
            if(getId('code_'+i) && !getId('code_'+i).value){
                alert('Bạn vui lòng nhập mã sản phẩm');
                getId('code_'+i).focus();
                return false;
            }
            if(getId('name_'+i) && !getId('name_'+i).value){
                alert('Bạn vui lòng nhập tên sản phẩm');
                getId('name_'+i).focus();
                return false;
            }else{
                if(getId('name_'+i) && $('#name_'+i).val() && $('#name_'+i).val().length > 255 ){
                    $.notify('Bạn vui lòng nhập tên sản phẩm ("<strong>'+$('#name_'+i).val()+'</strong>") không quá 255 ký tự.',{type: 'danger'});
                    $('#name_'+i).focus();
                    return false;
                }
            }
	        if(1===2 && getId('standardized_'+i) && !getId('standardized_'+i).value) { // standardized
		        if(getId('weight_'+i) && !getId('weight_'+i).value){
			        alert('Bạn vui lòng nhập trọng lượng sản phẩm');
			        getId('weight_'+i).focus();
			        return false;
		        } else {
			        if(getId('weight_'+i) && !isNaN(getId('weight_'+i).value) && parseInt(getId('weight_'+i).value) < 0){
				        $.notify('Bạn vui lòng nhập trọng lượng phải có giá trị dương.',{type: 'danger'});
				        getId('weight_'+i).focus();
				        return false;
			        }
		        }
            }
        }
        return true;
    }
    jQuery(document).ready(function(){
            //////////
            //suggestCustomer();
            $(document).keypress(function(e) {
                if(e.which == 13) {
                  mi_add_new_row('mi_product', false, ROW_CALLBACKS);
                  return false;
                }
    
                $('.input-weight').on('change', function(e) {
                    $(e.target).val($(e.target).val().replace(/[^\d]/g, ''))
                })
                $('.input-weight').on('keypress', function(e) {
                    keys = ['0','1','2','3','4','5','6','7','8','9']
                    return keys.indexOf(event.key) > -1
                })
    
            });
        }
    );
    function checkDuplicated(index){
        if(getId('code_'+index) && getId('code_'+index).value){
            let product_id = $('#id_'+index).val();
            let code = getId('code_'+index).value;
            $.ajax({
                method: "POST",
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                data: {
                    'do': 'check_duplicated',
                    'code': code,
                    'product_id':product_id
                },
                dataType : 'json',
                beforeSend: function () {
                },
                success: function (content) {
                    if(content.duplicated===1) {
                        alert('Mã sản phẩm "'+code+'" đã tồn tại. Bạn vui lòng chọn mã khác.');
                        getId('code_'+index).value  = '';
                    }
                },
                error: function () {
                    alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                }
            });
        }
    }
</script>
