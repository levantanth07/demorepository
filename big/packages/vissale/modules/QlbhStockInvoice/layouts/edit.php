<?php
    $type = Url::get('type');
?>
<script type="text/javascript" src="assets/admin/scripts/bootstrapValidator.min.js"></script>
<script type="text/javascript">
    var product_arr = <?php echo MiString::array2js([[=products=]]);?>;
</script>
<span style="display:none">
    <div id="mi_product_sample">
        <div id="input_group_#xxxx#">
            <div style="width: 100%;float: left">
                <input  name="mi_product[#xxxx#][id]" type="hidden" id="id_#xxxx#">
                <span class="multi-edit-input"><input  name="mi_product[#xxxx#][product_code]" style="width:120px;" type="text" id="product_code_#xxxx#" onblur="getProductFromCode('#xxxx#',this.value);" class="form-control" AUTOCOMPLETE=OFF" tabindex="#xxxx#"></span>
                <span class="multi-edit-input"><input  name="mi_product[#xxxx#][product_name]" style="width:220px;" type="text" class="form-control" id="product_name_#xxxx#" tabindex="#xxxx#" autocomplete="off"></span>
                <?php if($type == 'EXPORT') : ?>
                    <span class="multi-edit-input"><input  name="mi_product[#xxxx#][quantity]" style="width:100px;" type="text" id="quantity_#xxxx#" class="form-control" onchange="updatePaymentPrice('#xxxx#');" tabindex="#xxxx#"></span>
                <?php elseif($type == 'IMPORT') : ?>
                    <span class="multi-edit-input"><input  name="mi_product[#xxxx#][quantity]" style="width:100px;" type="text" id="quantity_#xxxx#" class="form-control" tabindex="#xxxx#"></span>
                <?php endif; ?>
                    <!--IF:import_cond(Url::get('type') == 'IMPORT')-->
                <span class="multi-edit-input"><input  name="mi_product[#xxxx#][expired_date]" style="width:120px;text-align:center;" type="text" id="expired_date_#xxxx#" class="form-control" tabindex="#xxxx#"></span>
                    <!--/IF:import_cond-->
                <span class="multi-edit-input">
                    <input  name="mi_product[#xxxx#][unit]" style="width:70px;" type="text" id="unit_#xxxx#" readonly class="form-control" tabindex="-1">
                    <input  name="mi_product[#xxxx#][unit_id]" style="width:70px;" type="hidden" id="unit_id_#xxxx#" readonly class="form-control" tabindex="-1">
                </span>
                <?php if($type == 'EXPORT') : ?>
                <span class="multi-edit-input"><input  name="mi_product[#xxxx#][price]" style="width:100px;text-align:right;" type="text" id="price_#xxxx#" class="form-control" onchange="updatePaymentPrice('#xxxx#');"></span>
                <span class="multi-edit-input"><input  name="mi_product[#xxxx#][payment_price]" style="width:100px;text-align:right;" type="text" id="payment_price_#xxxx#" readonly class="form-control"  tabindex="-1"></span>
                <?php endif; ?>
                <span class="multi-edit-input"><select  name="mi_product[#xxxx#][warehouse_id]" style="width:150px;" id="warehouse_id_#xxxx#" tabindex="#xxxx#">[[|warehouse_options|]]</select></span>
                    <!--IF:cond(Url::get('move_product'))-->
                  <span class="multi-edit-input"><select  name="mi_product[#xxxx#][to_warehouse_id]" style="width:150px;" id="to_warehouse_id_#xxxx#" onChange="checkWarehouse();" tabindex="#xxxx#"><option value="">Kho xuất</option>[[|warehouse_options|]]</select></span>
                    <!--/IF:cond-->
                <?php if($type == 'EXPORT') : ?>
                    <span class="btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','group_');updateTotalPayment();if(document.all)event.returnValue=false; else return false;"><i class="fa fa-trash-o"></i></span>
                <?php elseif($type == 'IMPORT') : ?>
                    <span class="btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','group_');if(document.all)event.returnValue=false; else return false;"><i class="fa fa-trash-o"></i></span>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</span>
<div class="container">
    <br>
    <div class="box box-info">
        <form name="EditQlbhStockInvoiceForm" id="EditQlbhStockInvoiceForm" method="post">
            <input  name="group_deleted_ids" id="group_deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>"  tabindex="-1">
            <div class="box-header">
                <h2 class="box-title">
                    <!--IF:move_cond(Url::get('move_product'))-->
                    Xuất nội bộ
                    <!--ELSE-->
                    [[|title|]]
                    <!--/IF:move_cond-->
                </h2>
                <div class="box-tools pull-right">
                    <a href="#" class="btn btn-default" data-toggle="modal" data-target="#ImportExcelModal"><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span> Import Excel </a>
                    <button type="button" onclick="window.location='<?php echo Url::build_current(array('type'));?>'"  class="btn btn-default"><i class="fa fa-th-list"></i> Danh sách phiếu</button>
                    <!--IF:cond(Url::get('cmd')=='edit')-->
                    <a href="<?=Url::build_current(['cmd'=>'view','id','type'])?>" class="btn btn-default"><i class="fa fa-file-text-o"></i> Xem phiếu</a>
                    <!--/IF:cond-->
                    <button name="save" type="submit" class="btn btn-primary" tabindex="-1">
                        <i class="fa fa-floppy-o"></i> Lưu
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="content" style="background:#FFFFFF;padding:10px;">
                    <?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
                    <fieldset>
                        <table width="100%" class="table table-bordered">
                            <tr>
                                <td>Ngày tạo (*):</td>
                                <td class="form-group">
                                    <div class="input-group date">
                                        <input name="create_date" type="text" id="create_date" class="form-control">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-th"></span>
                                        </div>
                                    </div>
                                </td>
                                <td align="right"><span>Số phiếu (*):</span></td>
                                <td class="form-group" align="right"><input name="bill_number" type="text" id="bill_number" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>Người giao:</td>
                                <td class="form-group"><input name="deliver_name" type="text" id="deliver_name" class="form-control"></td>
                                <td align="right"><span>Địa chỉ (Bộ phận):</span></td>
                                <td align="right" class="form-group"><input name="deliver_address" type="text" id="deliver_address"  class="form-control"/></td>
                            </tr>
                            <tr>
                                <td>Người nhận:</td>
                                <td class="form-group"><input name="receiver_name" type="text" id="receiver_name" class="form-control"></td>
                                <td align="right"><span>Địa chỉ:</span></td>
                                <td align="right" class="form-group"><input name="receiver_address" type="text" id="receiver_address"  class="form-control"/></td>
                            </tr>
                            <tr valign="top">
                                <td>Diễn giải:</td>
                                <td colspan="3"><textarea name="note" id="note" class="form-control"></textarea></td>
                            </tr>
                            <?php if($type == 'EXPORT') : ?>
                            <tr valign="top">
                                <td>Tổng số tiền(Viết bằng chữ):</td>
                                <td colspan="3"><textarea name="total_payment_text" id="total_payment_text" class="form-control" readonly></textarea></td>
                            </tr>
                        <?php endif; ?>
                            <tr valign="top">
                                <td>Số chứng từ gốc kèm theo:</td>
                                <td colspan="3"><textarea name="original_documents_number" id="original_documents_number" class="form-control"></textarea></td>
                            </tr>
                        </table>
                    </fieldset><br />
                    <!--IF:move_cond(!Url::get('move_product'))-->
                    <!--IF:cond(Url::get('type')=='EXPORT')-->
                    <fieldset>
                        <table class="table table-bordered">
                            <td align="right" width="30%">Tr&#7843; l&#7841;i nh&agrave; cung c&#7845;p
                                <input name="get_back_supplier" type="checkbox" id="get_back_supplier" value="1" onclick="toogleSupplierSelect(this);" /></td>
                            <td align="left"><div id="supplier_select_bound"><label for="supplier_id">[[.supplier.]]:
                                        <select name="supplier_id" id="supplier_id" class="form-control"></select></label>
                                </div></td>
                            </tr>
                        </table>
                    </fieldset>
                    <!--ELSE-->
                    <fieldset>
                        <table class="table">
                            <td align="left">Nhà cung cấp:</td>
                            <td><select name="supplier_id" id="supplier_id" class="form-control"></select></td>
                            </tr>
                        </table>
                    </fieldset>
                    <!--/IF:cond-->
                    <!--/IF:move_cond-->
                    <div class="box box-info box-solid">
                        <div class="box-header">
                            <div class="box-title">Sản phẩm hàng hoá</div>
                        </div>
                        <div class="box-body">
                            <div id="mi_product_all_elems">
                                <div style="width: 100%;float: left;">
                                    <span class="multi-edit-input header" style="width:122px;">Mã SP</span>
                                    <span class="multi-edit-input header" style="width:222px;">Tên sản phẩm</span>
                                    <span class="multi-edit-input header" style="width:102px;">Số lượng</span>
                                    <!--IF:import_cond(Url::get('type') == 'IMPORT')-->
                                    <span class="multi-edit-input header" style="width:122px;">HSD</span>
                                    <!--/IF:import_cond-->
                                    <span class="multi-edit-input header" style="width:72px;">Đơn vị</span>
                                    <?php if($type == 'EXPORT') : ?>
                                        <span class="multi-edit-input header price" style="width:102px;">Giá</span>
                                        <span class="multi-edit-input header price" style="width:102px;">Thành tiền</span>
                                    <?php endif; ?>
                                    <span class="multi-edit-input header " style="width:152px;">Kho</span>
                                    <!--IF:cond(Url::get('move_product'))-->
                                    <span class="multi-edit-input header" style="width:152px;">Đến kho</span>
                                    <!--/IF:cond-->
                                    <span class="multi-edit-input header no-border no-bg" style="width:20px;"></span>
                                </div>
                            </div>
                            <br clear="all">
                            <input type="button" value=" + Thêm sản phẩm" onclick="mi_add_new_row('mi_product');suggestProduct(input_count);" class="btn btn-sm btn-warning" tabindex="0">
                            <div class="alert alert-warning-custom">
                                Nhập tối đa 400 sản phẩm
                            </div>
                        </div>
                        <?php if($type == 'EXPORT') : ?>
                            <fieldset id="total_payment_bound">
                                <div style="text-align:right;float:right;">
                                    <strong>Tổng thanh toán:</strong> <input name="total_amount" type="text" id="total_amount" readonly style="width:100px;text-align:right;border:0px;border-bottom:1px solid #CCCCCC;font-weight:bold;color:#000000;" tabindex="-1"><br />
                                </div>
                            </fieldset>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
<script type="text/javascript">
    var isValid;
    jQuery(document).ready(function(){
        $(document).keypress(function(e) {
            if(e.which == 13) {
                mi_add_new_row('mi_product');
                suggestProduct(input_count);
                $('#product_name_'+input_count).focus();
                return false;
            }
        });
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#create_date').datepicker();
        jQuery('#EditQlbhStockInvoiceForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                create_date: {
                    validators: {
                        notEmpty: {
                            message: 'Bạn phải nhập ngày'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (e) {
            checkProduct(e);
        });
        //my_autocomplete();
    });
    mi_init_rows('mi_product',<?php echo isset($_REQUEST['mi_product'])?MiString::array2js($_REQUEST['mi_product']):'{}';?>);
    //jQuery("#create_date").mask("99/99/9999");
    updateTotalPayment();
    function updatePaymentPrice(prefix){
        getId('quantity_'+prefix).value = numberFormat(getId('quantity_'+prefix).value);
        getId('price_'+prefix).value = numberFormat(getId('price_'+prefix).value);
        var discount =  0;
        getId('payment_price_'+prefix).value =  to_numeric(getId('price_'+prefix).value)*to_numeric(getId('quantity_'+prefix).value);
        getId('payment_price_'+prefix).value = numberFormat(getId('payment_price_'+prefix).value);
        if(getId('payment_price_'+prefix).value == 'NaN'){
            getId('payment_price_'+prefix).value = 0;
        }
        updateTotalPayment();
    }
    function updateTotalPayment(){
        var total_payment = 0;
        for(var i=101;i<=input_count;i++){
            jQuery('#expired_date_'+i).datepicker();
            if(typeof(jQuery("#payment_price_"+i).val())!='undefined'){
                total_payment += stringToNumber(jQuery("#payment_price_"+i).val());
            }
        }

        updateTotalPaymentText(total_payment);
        jQuery("#total_amount").val((total_payment!='NaN')?numberFormat(total_payment):'0');
    }
    
    /**
     * { function_description }
     *
     * @param      {<type>}  num     The number
     * @return     {string}  { description_of_the_return_value }
     */
    function updateTotalPaymentText(num)
    {   
        num = parseInt(num);
        if(!num) return '';
        let text = num.toVNText();
        $('#total_payment_text').val(text.charAt(0).toUpperCase() + text.slice(1) + ' đồng');
    }

    function getProductFromCode(id,value){
        if(typeof(product_arr[value])=='object'){
            getId('product_name_'+id).value = product_arr[value]['name'];
            getId('unit_'+id).value = product_arr[value]['unit'];
            getId('unit_id_'+id).value = product_arr[value]['unit_id'];
            getId('price_'+id).value = numberFormat(product_arr[value]['price']);
            //getId('product_name_'+id).className = 'form-control';
        }else{
            //getId('name_'+id).className = 'notice';
            if(value){
                getId('product_name_'+id).value = 'Mặt hàng không tồn tại';
                alert('Bạn vui lòng nhập mã sản phẩm tồn tại trong hệ thống');
                getId('product_name_'+id).className = 'form-control not-existed';
            }else{
                getId('product_name_'+id).value = '';
            }
            getId('unit_'+id).value = '';
            getId('price_'+id).value = '';
        }
    }
    <!--IF:type_cond(Url::get('type')=='EXPORT')-->
    //jQuery("#total_payment_bound").hide();
    <!--IF:get_back_supplier(Url::get('supplier_id'))-->
    jQuery("#total_payment_bound").show();
    jQuery("#supplier_select_bound").show();
    getId('get_back_supplier').checked = true;
    <!--ELSE-->
    jQuery("#supplier_select_bound").hide();
    <!--/IF:get_back_supplier-->
    <!--ELSE-->
    jQuery("#supplier_select_bound").show();
    <!--/IF:type_cond-->
    function toogleSupplierSelect(obj){
        if(obj.checked == true){
            jQuery("#supplier_select_bound").show();
            jQuery("#total_payment_bound").show();
            getId("price").style.display = '';
        }else{
            jQuery("#supplier_select_bound").hide();
            jQuery("#total_payment_bound").hide();
        }
    }
    function my_autocomplete()
    {
        jQuery('#expired_date_'+input_count).datepicker();
        jQuery("#product_code_"+input_count).autocomplete({
            source:'get_product.php',
            select: function( event, ui ) {getProductFromCode(input_count,ui.item.value);}
        });
    }
    function checkProduct(e){
        $return = checkWarehouse(e);
        if(getId('product_code_'+input_count)){
            if(to_numeric(getId('quantity_'+input_count).value) <= 0){
                $return = false;
            }
        }else{
            $return = false;
        }
        if($return){
            jQuery('input[type="submit"]').val('Đang xử lý...');
            jQuery('input[type="submit"]').attr('disabled',true);
        }else{
            alert('Bạn chưa nhập mặt hàng...!');
            jQuery('input[type="submit"]').attr('disabled',false);
            e.preventDefault();
            var $form = $(e.target);
            $form.data('bootstrapValidator').disableSubmitButtons(false);
        }
    }
    function checkWarehouse(e){
        for(var i=101;i<=input_count;i++){
            if(getId('to_warehouse_id_'+i) && !getId('to_warehouse_id_'+i).value){
                alert('Bạn chưa chọn kho xuất');
                return false;
            }
            if(getId('to_warehouse_id_'+i) && getId('warehouse_id_'+i).value == getId('to_warehouse_id_'+i).value){
                alert('Kho xuất phải khác kho tổng!');
                getId('to_warehouse_id_'+i).value = 0;
                return false;
            }
        }
        return true;
    }
    function suggestProduct(index){
        jQuery("#product_name_"+index).autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_suggest_product&type=<?php echo Url::get('type');?>',
            minChars: 3,
            width: 223,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                jQuery('#product_code_'+index).val(ui.item.code);
                jQuery('#product_name_'+index).val(ui.item.name);
                getProductFromCode(index,ui.item.code);
                return false;
            }
        });
    }
</script>
