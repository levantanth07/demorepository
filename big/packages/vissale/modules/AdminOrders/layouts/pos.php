<script>
    var THANH_CONG = 5;
</script>
<div style="display:none">
    <div id="mi_order_product_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:300px;">
                <input  name="mi_order_product[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="multi-edit-text-input" style="text-align:right;" value="(auto)" tabindex="-1" readonly>
                <input  name="mi_order_product[#xxxx#][product_name]" type="text" class="multi-edit-text-input" id="product_name_#xxxx#" tabindex="#xxxx#" onchange="getProduct('#xxxx#');" placeholder="Nhập để chọn sản phẩm">
                <input  name="mi_order_product[#xxxx#][product_id]" type="hidden" class="multi-edit-text-input" id="product_id_#xxxx#" tabindex="#xxxx#">
            </span>
            <span class="multi-edit-input" style="width:80px;"><input  name="mi_order_product[#xxxx#][qty]" class="multi-edit-text-input" type="number" id="qty_#xxxx#" onChange="updateTotalPrice();" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:150px;"><input  name="mi_order_product[#xxxx#][product_price]" class="multi-edit-text-input align-right" type="text" id="product_price_#xxxx#" onChange="updateTotalPrice();this.value=numberFormat(this.value);"></span>
            <span class="multi-edit-input" style="width:150px;"><input  name="mi_order_product[#xxxx#][total]" class="multi-edit-text-input align-right" type="text" id="total_#xxxx#" readonly tabindex="-1"></span>
            <span class="multi-edit-input no-border btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_order_product','#xxxx#','product');updateTotalPrice();event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa sản phẩm. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash text-red" aria-hidden="true"></span></span>
        </div>
    </div>
    <div id="mi_staff_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:180px;">
                <input  name="mi_staff[#xxxx#][staff_name]" class="multi-edit-text-input small" type="text" id="staff_name_#xxxx#" tabindex="#xxxx#" placeholder="Nhập ít nhất một chữ để tìm kiếm">
                <input  name="mi_staff[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="multi-edit-text-input" value="(auto)" tabindex="-1" readonly>
            </span>
            <span class="multi-edit-input" style="width:100px;">
              <input  name="mi_staff[#xxxx#][staff_code]" type="text" class="multi-edit-text-input" id="staff_code_#xxxx#" tabindex="#xxxx#" readonly="">
              <input  name="mi_staff[#xxxx#][staff_id]" type="hidden" class="multi-edit-text-input" id="staff_id_#xxxx#" tabindex="#xxxx#">
            </span>
            <span class="multi-edit-input" style="width:80px;"><input  name="mi_staff[#xxxx#][discount_amount]" class="multi-edit-text-input align-right" type="text" id="discount_amount_#xxxx#" onChange="this.value=numberFormat(this.value);"></span>
            <span class="multi-edit-input" style="width:100px;"><select  name="mi_staff[#xxxx#][role]" class="multi-edit-text-input" id="role_#xxxx#" tabindex="#xxxx#"><option value="1">Kỹ thuật</option><option value="2">Tư vấn</option></select></span>
            <span class="multi-edit-input text-center" style="width:80px;padding-left:30px;"><input  name="mi_staff[#xxxx#][real_revenue]" class="multi-edit-text-input" type="checkbox" checked readonly="" id="real_revenue_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:200px;">
                <select  name="mi_staff[#xxxx#][product_id]" class="multi-edit-text-input small" id="product_id_#xxxx#" tabindex="#xxxx#">[[|staff_product_id_options|]]</select>
            </span>
            <span class="multi-edit-input no-border btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_staff','#xxxx#','');updateTotalPrice();event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash text-red" aria-hidden="true"></span></span>
        </div>
    </div>
    <div id="mi_payment_sample">
        <div id="input_group_#xxxx#" class="row">
            <div class="col-xs-4">
                <input  name="mi_payment[#xxxx#][amount]" class="form-control text-right" type="text" id="amount_#xxxx#" onChange="updateTotalPayment();this.value=numberFormat(this.value);" tabindex="-1">
                <input  name="mi_payment[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="form-control" value="(auto)" tabindex="-1" readonly>
            </div>
            <div class="col-xs-4"><select  name="mi_payment[#xxxx#][payment_type]" class="form-control" id="payment_type_#xxxx#">[[|payment_type_options|]]</select></div>
            <div class="col-xs-3"><select  name="mi_payment[#xxxx#][bill_type]" class="form-control" id="bill_type_#xxxx#"><option value="1">Thu tiền</option></select></div>
            <div class="col-xs-1" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_payment','#xxxx#','payment_');updateTotalPayment();event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa lượt thanh toán. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash text-red" aria-hidden="true"></span></div>
        </div>
    </div>
    <div id="mi_refund_sample">
        <div id="input_group_#xxxx#" class="row">
            <div class="col-xs-4">
                <input  name="mi_refund[#xxxx#][amount]" class="form-control text-right" type="text" id="amount_#xxxx#" onChange="updateTotalPayment();this.value=numberFormat(this.value);" tabindex="-1">
                <input  name="mi_refund[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="form-control" value="(auto)" tabindex="-1" readonly>
            </div>
            <div class="col-xs-4"><select  name="mi_refund[#xxxx#][payment_type]" class="form-control" id="payment_type_#xxxx#">[[|payment_type_options|]]</select></div>
            <div class="col-xs-3"><select  name="mi_refund[#xxxx#][bill_type]" class="form-control" id="bill_type_#xxxx#"><option value="0">Trả lại tiền</option></select></div>
            <div class="col-xs-1" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_refund','#xxxx#','payment_');updateTotalPayment();event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa lượt thanh toán. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash text-red" aria-hidden="true"></span></div>
        </div>
    </div>
    <div id="mi_image_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:150px;">
                <img src=""  id="img_image_url_#xxxx#" class="img-responsive">
            </span>
            <span class="multi-edit-input" style="width:280px;"><input  name="image_url_#xxxx#" class="multi-edit-text-input" type="file" id="image_url_#xxxx#" tabindex="#xxxx#" onchange="PreviewImage('image_url_#xxxx#','img_image_url_#xxxx#')"><input  name="mi_image[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="multi-edit-text-input" style="text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input" style="width:280px;"><input  name="mi_image[#xxxx#][description]" class="multi-edit-text-input" type="text" id="description_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input no-border btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_image','#xxxx#','image_');event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa sản phẩm. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash text-red" aria-hidden="true"></span></span>
        </div>
    </div>
</div>
<br>
<div class="container full">
    <form name="EditAdminProcessForm" method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="box box-default">
            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-file"></i> [[|title|]]</h3>
                <div class="box-tools pull-right">
                    <!--IF:business_model_cond([[=business_model=]] and ([[=stt_id=]] != 5 and [[=stt_id=]] != 9))-->
                    <button id="checkoutBtn" type="button"  class="btn btn-success hidden" style="font-weight: bold;" onclick="SaveOrder(this,0,1);" title="Đơn hàng thành công"><i class="glyphicon glyphicon-log-out"></i> HOÀN THÀNH LIỆU TRÌNH (F7)</button>
                    <!--/IF:business_model_cond-->
                    <!--IF:save_cond( [[=is_deleted=]]!=1 )-->
                    <button type="button"  class="btn btn-primary " onclick="SaveOrder(this,1,0);"><i class="glyphicon glyphicon-floppy-disk"></i> Lưu</button>
                    <!--/IF:save_cond-->
                    <!--IF:edit_cond(Url::get('id'))-->
                    <button type="button" class="btn btn-warning" onclick="window.open('<?php echo Url::build_current(array('ids'=>Url::iget('id'),'act'=>'print','print_layout'=>'pos'))?>');"> <i class="glyphicon glyphicon-print"></i> IN</button>
                    <!--/IF:edit_cond-->
                    <input  name="save" type="hidden" id="save" value="">
                    <input  name="stay" type="hidden" id="stay" value="">
                    <input  name="checkout" type="hidden" id="checkout" value="">
                    <input name="exit_order" type="submit" class="btn btn-danger" value="Thoát">
                    <input name="order_code" type="hidden" id="order_code" class="form-control">
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(Form::$current->is_error()) {?><?php echo Form::$current->error_messages();?><?php } ?>
                                <div class="panel panel-info">
                                    <div class="panel-heading"><h4> Sản phẩm / dịch vụ </h4></div>
                                    <div class="panel-body row" style="min-height: 100px;">
                                        <div class="col-md-12" style="overflow-x: auto;">
                                            <div class="multi-item-wrapper" style="min-width: 830px;">
                                              <span id="mi_order_product_all_elems">
                                                  <span style="white-space:nowrap;">
                                                      <span class="multi-edit-input header" style="width:300px;">Tên sản phẩm / dịch vụ</span>
                                                      <span class="multi-edit-input header" style="width:80px;">Số lượng</span>
                                                      <span class="multi-edit-input header text-right" style="width:150px;">Đơn giá</span>
                                                      <span class="multi-edit-input header text-right" style="width:150px;">Thành tiền</span>
                                                      <span class="multi-edit-input header" style="width:40px;">Xóa</span>
                                                      <br clear="all">
                                                  </span>
                                              </span>
                                            </div>
                                            <br clear="all">
                                            <div style="padding:5px 0px 5px 0px;">
                                                <button type="button" class="btn btn-default" title="Thêm sản phẩm dịch vụ" onclick="mi_add_new_row('mi_order_product');suggestProduct(input_count);"><i class="glyphicon glyphicon-plus-sign"></i> Thêm sản phẩm dịch vụ</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i> Thông tin khách hàng </h4>
                                    </div>
                                    <div class="panel-body cus-info-content form-inline" style="min-height: 360px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-3">Tên khách hàng</div>
                                            <div class="col-xs-9">
                                                <input name="customer_name" type="text" id="customer_name" class="form-control" autocomplete="off" autocorrect="off" spellcheck="off" placeholder="Họ và Tên khách hàng">
                                                <input name="customer_id" type="hidden" id="customer_id" readonly="">
                                                <div id="viewCustomer">
                                                    <!--IF:cond(Url::iget('customer_id'))-->
                                                    <div style="padding:5px 0px 5px 0px;"><a href="<?=Url::build('customer')?>&cid=<?=Url::iget('customer_id')?>&do=view" target="_blank"><i class="glyphicon glyphicon-search"></i> XEM</a></div>
                                                    <!--/IF:cond-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row phone mb-05">
                                            <div class="col-xs-3">Số điện thoại</div>
                                            <div class="col-xs-9">
                                                <input name="mobile" type="text" id="mobile" style="font-size:14px;font-weight: bold;color:#0630F5" class="form-control" placeholder="Nhập số điện thoại để tìm khách hàng">
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-3">Địa chỉ</div>
                                            <div class="col-xs-9">
                                                <textarea name="address" id="address" rows="2" class="form-control" placeholder="Số nhà, tên đường" readonly></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-3">Tỉnh/TP</div>
                                            <div class="col-xs-9">
                                                <input name="city" type="text" id="city" class="form-control" readonly>
                                                <div class="mt-05 hidden">
                                                    <button id="selectZone" onclick="$('#ZoneModal').modal();" type="button" class="btn btn-default">Chọn tỉnh thành</button>
                                                    <!-- Modal -->

                                                    <div class="modal fade" id="ZoneModal" role="dialog">
                                                        <div class="modal-dialog">
                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title">Chọn Địa chỉ</h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row w-100">
                                                                        <div class="dropdown" id="city-drd">
                                                                            <input <?php if(isset([[=city_name=]])) echo 'value="'.[[=city_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Tỉnh / Thành phố" />
                                                                            <input  type="hidden" name="city_id" id="city_id" <?php if(isset([[=city_id=]])) echo 'value="'.[[=city_id=]].'"'; ?> class="input-id">
                                                                            <ul class="dropdown-menu">
                                                                                <!--LIST:zones-->
                                                                                <li><a data-name="[[|zones.name|]]" data-name_id="[[|zones.name_id|]]" data-id="[[|zones.id|]]">[[|zones.name|]]</a></li>
                                                                                <!--/LIST:zones-->
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown" id="district">
                                                                            <input <?php if(isset([[=district_name=]])) echo 'value="'.[[=district_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Quận / huyện"/>
                                                                            <input  type="hidden" name="district_id" id="district_id" <?php if(isset([[=district_id=]])) echo 'value="'.[[=district_id=]].'"'; ?>" data-name="<?php if(isset([[=district_name=]])) echo [[=district_name=]]; ?>" class="input-id">
                                                                            <ul class="dropdown-menu" ></ul>
                                                                            <div class="btn add-dtr" id="add-dtr">
                                                                                <i class="fa fa-plus-square"></i>
                                                                            </div>
                                                                            <?php if(isset([[=city_name_id=]])){?>
                                                                            <script>
                                                                                var name_id = '<?php echo [[=city_name_id=]]; ?>';
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'name_id':name_id,
                                                                                        'type': 'district'
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#district ul').html(content);
                                                                                    }
                                                                                });
                                                                            </script>
                                                                            <?php }?>
                                                                        </div>
                                                                        <div class="dropdown" id="country">
                                                                            <input <?php if(isset([[=ward_name=]])) echo 'value="'.[[=ward_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Phường / xã" />
                                                                            <input  type="hidden" name="ward_id" id="ward_id" <?php if(isset([[=ward_id=]])) echo 'value="'.[[=ward_id=]].'"'; ?>" class="input-id">
                                                                            <ul class="dropdown-menu" ></ul>
                                                                            <div class="btn add-ctr" id="add-ctr">
                                                                                <i class="fa fa-plus-square"></i>
                                                                            </div>
                                                                            <?php if(isset([[=district_name_id=]])){?>
                                                                            <script>
                                                                                var name_id = '<?php echo [[=district_name_id=]]; ?>';
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'name_id':name_id,
                                                                                        'type': 'country'
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#country ul').html(content);
                                                                                    }
                                                                                });
                                                                            </script>
                                                                            <?php }?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="add-district w-100">
                                                                        <div class="form-group w-100">
                                                                            <label  style="font-size: 15px;font-weight: normal;"></label>
                                                                            <p>Tên Quận / Huyện</p>
                                                                            <input type="text" class="form-control" name="district-name" id='ip-name-add-district'>
                                                                            <div clas="btn btn-default" id="btn-sm-add-district"  style="display: inline-block;border:1px solid #222;margin-top:20px;padding:5px 10px;cursor: pointer;">Thêm</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="add-country w-100">
                                                                        <div class="form-group w-100">
                                                                            <label  style="font-size: 15px;font-weight: normal;"></label>
                                                                            <p>Tên Quận / Huyện</p>
                                                                            <input type="text" class="form-control" name="country-name" id='ip-name-add-country'>
                                                                            <div clas="btn btn-default" id="btn-sm-add-country"  style="display: inline-block;border:1px solid #222;margin-top:20px;padding:5px 10px;cursor: pointer;">Thêm</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                                                                    <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button> -->
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="info-item" style="margin:5px 0px;">
                                            <div class="col-xs-6" style="padding:0;">
                                                <label for="note">Ghi chú chung</label>
                                                <textarea name="note1" id="note1" class="form-control" rows="2"></textarea>
                                            </div>
                                            <div class="col-xs-6" style="padding:0;">
                                                <label for="htcs">Ghi chú 2</label>
                                                <textarea name="note2" id="note2" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="info-item clearfix" style="margin-top: 5px;">
                                            <div class="col-xs-12 form-inline no-padding">
                                                <label>Ghi Chú Giao Hàng</label>
                                                <textarea name="shipping_note" id="shipping_note" rows="2" class="form-control"></textarea>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div class="panel-heading"><h4><i class="glyphicon glyphicon-usd"></i> Chi Phí </h4></div>
                            <div class="panel-body paid-content">
                                <div class="form-group">
                                    <div class="col-xs-5"> Tổng tiền hàng</div>
                                    <div class="col-xs-7">
                                        <input name="price" type="text" id="price" class="form-control text-right"form-group readonly="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5" style="color:#F23FFF;">
                                        Giảm giá <input name="discount_rate" type="number" id="discount_rate" max="100" style="width:50px;border-radius: 10px;color:#F23FFF;">%
                                    </div>
                                    <div class="col-xs-7">
                                        <input name="discount_price" type="text" id="discount_price" style="color:#F23FFF;" class="form-control text-right" onchange="updateTotalPrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="form-group hidden">
                                    <div class="col-xs-5"> Phí vận chuyển</div>
                                    <div class="col-xs-7">
                                        <input name="shipping_price" type="text" id="shipping_price" class="form-control text-right"form-group onchange="updateTotalPrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5">
                                        Phụ thu
                                    </div>
                                    <div class="col-xs-7">
                                        <input name="other_price" type="text" id="other_price" class="form-control text-right"form-group onchange="updateTotalPrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5">
                                        <strong>Khách cần trả</strong>
                                    </div>
                                    <div class="col-xs-7">
                                        <input name="total_price" type="text" id="total_price" class="form-control text-right" style="width:100%;font-weight:bold;" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5">
                                        Khách đã trả
                                    </div>
                                    <div class="col-xs-7">
                                        <input name="total_payment" type="text" id="total_payment" readonly="" class="form-control text-right" style="width:100%;border:0px;border-bottom: 3px solid #00A7FF !important;font-size:16px;color:#00A7FF;font-weight: bold;" onchange="updateTotalPrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5">
                                        <span class="label label-default"><?=[[=status_name=]]?></span>
                                    </div>
                                    <div class="col-xs-7 text-right">
                                        <a style="width: 100%" href="#" onclick="$('#paymentModal').modal();return false;" class="btn btn-info" title="Thu tiền"><i class="fa fa-money"></i> Thu tiền</a>
                                        <!--IF:checked_out_cond([[=status_id=]]!=5 and [[=status_id=]]!=9)-->
                                        <hr>
                                        <button onclick="$('#status_id').val(THANH_CONG);SaveOrder(this,1,0);" style="width: 100%" type="button" class="btn btn-success btn-lg" data-dismiss="modal"> <i class="fa fa-shopping-cart"></i> Thanh toán</button>
                                        <!--IF:edit_cond(Url::get('id'))-->
                                        <a style="width: 100%" href="#" onclick="$('#refundModal').modal();return false;" class="btn btn-warning" title="Trả gói"><i class="fa fa fa-pause"></i> Hoàn tiền</a>
                                        <!--/IF:edit_cond-->
                                        <!--/IF:checked_out_cond-->
                                    </div>
                                </div>
                                <div class="info-item" style="margin-top:50px"></div>
                                <div class="info-item"></div>
                            </div>
                            <div class='panel-footer'>
                                <div class="form-group">
                                    <div class="col-xs-12 text-right">
                                        <!--IF:cancel_cond( AdminOrders::$is_owner and Url::get('id'))-->
                                        <button type='button' class='btn btn-danger btn-xs' onclick="SaveOrder(this,0,2)">Hủy đơn</button>
                                        <input type='hidden' name='cancel' id='cancel'>
                                        <!--/IF:cancel_cond-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-danger">
                            <div class="panel-heading"><h4><i class="glyphicon glyphicon-time"></i> Lịch sử đơn hàng </h4></div>
                            <div class="panel-body row">
                                <div class="col-md-12" style="height: 440px !important;overflow-y:auto;">
                                    <!--IF:his_cond(Url::get('cmd')=='edit')-->
                                    <div class="page">
                                        <div class="page__demo">
                                            <div class="main-container page__container">
                                                <div class="timeline" id="result">
                                                    <!--LIST:order_revisions-->
                                                    <div class="timeline__group">
                                                        <span class="timeline__year"><?php echo date('d/m/Y',[[=order_revisions.id=]]) ?></span>
                                                        <!--LIST:order_revisions.arr-->
                                                        <div class="timeline__box">
                                                            <div class="timeline__date" >
                                                          <span class="timeline__month" >
                                                              <?php if(![[=order_revisions.arr.before_order_status=]]) echo '<i class="fa fa-folder-open"></i>';else echo '<i class="fa fa-info-circle"></i>'; ?>

                                                          </span>
                                                            </div>
                                                            <div class="timeline__post">
                                                                <div class="timeline__content">
                                                                    <div class="panel panel-default small">
                                                                        <div class="panel-heading">
                                                                            [[|order_revisions.arr.user_created_name|]]
                                                                            <div class="ml-auto" style="float:right;">
                                                                                <i class="fa fa-clock-o"></i>
                                                                                <?php echo date('H:i:s',strtotime([[=order_revisions.arr.created=]])) ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="panel-body" style="padding:5px;">
                                                                            <?php if([[=order_revisions.arr.before_order_status=]]){ ?>
                                                                                Chuyển trạng thái từ: <strong>[[|order_revisions.arr.before_order_status|]]</strong> thành <strong>[[|order_revisions.arr.order_status|]]</strong>
                                                                            <?php }else{ ?>
                                                                                <div>[[|order_revisions.arr.data|]]</div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/LIST:order_revisions.arr-->
                                                    </div>
                                                    <!--/LIST:order_revisions-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--/IF:his_cond-->
                                    </div>
                                    <style>
                                        #lich_su_do_hang .modal-body{
                                            height: 600px;
                                            overflow-y: scroll;
                                        }
                                    </style>
                                    <script>
                                        $(document).ready(function(){
                                            var scroll = 1;
                                            var page = 1;
                                            var lsdh = $('#lich_su_do_hang .modal-body');
                                            lsdh.on('scroll', function() {
                                                if(scroll==1){
                                                    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                                                        $.ajax({
                                                            method: 'POST',
                                                            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                            data : {
                                                                'cmd':'get_order_history',
                                                                'page':page+1,
                                                                'id':'<?php echo  DataFilter::removeXSSinHtml(Url::iget('id'));?>'
                                                            },
                                                            beforeSend: function(){
                                                                page = page + 1;
                                                                scroll = 0;
                                                            },
                                                            success: function(content){
                                                                if(content==0){

                                                                }else{
                                                                    //console.log(content);
                                                                    $('#result').append(content);
                                                                    scroll = 1;
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input  name="image_deleted_ids" id="image_deleted_ids" type="hidden" value="<?php echo URL::get('image_deleted_ids');?>">
            <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
            <input  name="payment_deleted_ids" id="payment_deleted_ids" type="hidden" value="<?php echo URL::get('payment_deleted_ids');?>">
            <input  name="refer_url" id="refer_url" type="hidden" value="[[|refer_url|]]">
            <div class="modal fade" id="thong_tin_bo_sung" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="chatModalLabel">Thông tin bổ sung </h4>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th>Phí tư vấn</th>
                                    <th>Chi phí chốt</th>
                                    <th>Liệu trình</th>
                                    <th>Lịch hẹn</th>
                                </tr>
                                <tr>
                                    <td><input name="san_pham" type="text" id="san_pham" class="form-control" style="width:100%;"></td>
                                    <td><input name="phi_tu_van" type="text" id="phi_tu_van" class="form-control text-right" style="width:100%;" onchange="updateTotalPrice();this.value=numberFormat(this.value);"></td>
                                    <td><input name="chi_phi_chot" type="text" id="chi_phi_chot" class="form-control text-right" style="width:100%;" onchange="updateTotalPrice();this.value=numberFormat(this.value);"></td>
                                    <td><input name="lieu_trinh" type="text" id="lieu_trinh" class="form-control" style="width:100%;"></td>
                                    <td><input name="lich_hen" type="text" id="lich_hen" class="form-control" style="width:100%;"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal show phone number-->
            <div class="modal fade" id="md-show-phone-number" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="bor">
                                <h3 class="text-center" style="margin-top:3px;color:red;">ĐƠN HÀNG ĐÃ SỬ DỤNG</h3>

                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã ĐH</th>
                                        <th>Tên KH</th>
                                        <th>SĐT</th>
                                        <th>Địa chỉ</th>
                                        <th>Tỉnh / thành</th>
                                        <th>*</th>
                                    </tr>
                                    </thead>
                                    <tbody id="show-phone-number">
                                    </tbody>
                                </table>
                            </div>
                            <script>
                                $('#mobile').keyup(function(){
                                    var spnb = $('#show-phone-number');
                                    $.ajax({
                                        method: "POST",
                                        url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                        data : {
                                            'cmd':'get_phone_number',
                                            'phone':$(this).val(),
                                            'order_id': <?php echo Url::get('id')?Url::get('id'):0; ?>
                                        },
                                        beforeSend: function(){
                                        },
                                        success: function(content){
                                            if(content!=0){
                                                spnb.html(content);
                                                $('#md-show-phone-number').modal('show');
                                            }
                                            else{
                                                console.log('no result');
                                            }
                                        }
                                    });
                                });

                            </script>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="paymentModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-sm modal-default" style="width:700px !important">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"><i class="glyphicon glyphicon-usd"></i> Thu tiền</h4>
                        </div>
                        <div class="modal-body">
                            <div class="multi-item-wrapper">
                                <div id="mi_payment_all_elems">
                                    <div class="row">
                                        <div class="col-xs-4"><label>Tiền thanh toán</label></div>
                                        <div class="col-xs-4"><label>Phương thức thanh toán</label></div>
                                        <div class="col-xs-3"><label>Loại TT</label></div>
                                        <div class="col-xs-1"><label>Xóa</label></div>
                                        <br clear="all">
                                    </div>
                                </div>
                            </div>
                            <br clear="all">
                            <div style="padding:5px 0px 5px 0px;">
                                <button type="button" class="btn btn-default" title="Nhập tiền" onclick="mi_add_new_row('mi_payment');$('#amount_'+input_count).val($('#totalGuestHaveToPay').html());updateTotalPayment();"><i class="glyphicon glyphicon-plus-sign"></i> Nhập tiền</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-xs-8 text-left">
                                    Số tiền cần thanh toán: <a href="#" onClick="return false;" class="label label-default" id="totalGuestHaveToPay"><strong><?php echo Url::get('total_price');?></strong></a>
                                </div>
                                <div class="col-xs-4 text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"> <i class="fa fa-times-circle-o"></i> Xong</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div id="refundModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-sm modal-default" style="width:700px !important">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"><i class="glyphicon glyphicon-usd"></i> Trả gói</h4>
                        </div>
                        <div class="modal-body">
                            <div class="multi-item-wrapper">
                                <div id="mi_refund_all_elems">
                                    <div class="row">
                                        <div class="col-xs-4"><label>Tiền trả lại</label></div>
                                        <div class="col-xs-4"><label>Phương thức thanh toán</label></div>
                                        <div class="col-xs-3"><label>Loại TT</label></div>
                                        <div class="col-xs-1"><label>Xóa</label></div>
                                        <br clear="all">
                                    </div>
                                </div>
                            </div>
                            <br clear="all">
                            <div style="padding:5px 0px 5px 0px;">
                                <button type="button" class="btn btn-default" title="Thêm dòng" onclick="mi_add_new_row('mi_refund');"><i class="glyphicon glyphicon-plus-sign"></i> Thêm</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-xs-8"></div>
                                <div class="col-xs-4 text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">XONG</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
        </div>
        <select name="source_id" id="source_id" class="form-control" style="display: none;"></select>
        <select name="type" id="type" class="form-control" style="display: none;"></select>
        <input name="cmd" type="hidden" id="cmd">
        <input name="status_id" type="text" id="status_id">
    </form>
</div>
<div class="modal fade" id="lich_su_do_hang" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="chatModalLabel">Lịch sử đơn </h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<div id="chatModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" style="width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Chat với Khách hàng</h4>
            </div>
            <div class="modal-body" id="chatBodyWrapper">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script src="packages/core/includes/js/multi_items.js"></script>
<script type="text/javascript" src="assets/standard/js/multiple.select.js"></script>
<script>
    mi_init_rows('mi_order_product',<?php if(isset($_REQUEST['mi_order_product'])){echo MiString::array2js($_REQUEST['mi_order_product']);}else{echo '[]';}?>);
    mi_init_rows('mi_staff',<?php if(isset($_REQUEST['mi_staff'])){echo MiString::array2js($_REQUEST['mi_staff']);}else{echo '[]';}?>);
    mi_init_rows('mi_payment',<?php if(isset($_REQUEST['mi_payment'])){echo MiString::array2js($_REQUEST['mi_payment']);}else{echo '[]';}?>);
    mi_init_rows('mi_refund',<?php if(isset($_REQUEST['mi_refund'])){echo MiString::array2js($_REQUEST['mi_refund']);}else{echo '[]';}?>);
    mi_init_rows('mi_image',<?php if(isset($_REQUEST['mi_image'])){echo MiString::array2js($_REQUEST['mi_image']);}else{echo '[]';}?>);
    for(var i=101;i<=input_count;i++){
        if($('#id_'+i) && $('product_id_'+i)){
            suggestProduct(i);
            if($('#product_id_'+i).val() && $('#id_'+i).val() != '(auto)'){
                $('#qty_'+i).attr('readOnly',true);
                $('#qty_'+i).click(function(){
                    alert('Bạn không đuợc sửa đổi số luợng');
                });
            }
        }
    }
    updateTotalPrice();
    updateTotalPayment();
    $(document).ready(function(){
        $('#created').datetimepicker({
            defaultDate: new Date(),
            format: 'YYYY-MM-DD H:m'
        });
        <?php if(Url::get('cmd')=='edit'){?>
        $('#turn_card_code').attr('readonly',true);
        <?php }else{?>
        suggestCardNumber();
        <?php }?>
        ////
        $('#stt-box ul li').click(function(){
            var name = $(this).attr('data-name');
            var id = $(this).attr('data-id');
            var color = $(this).attr('data-color');
            $('#stt-color').css('background-color',color);
            $('#stt-name').text(name);
            $('#status_id').val(id);
        });
        ////
        //$('#applied_date').datepicker({format:'yyyy-mm-dd',language:'vi'});
        //$('#expired_date').datepicker({format:'yyyy-mm-dd',language:'vi'});
        <?php if(Url::get('status_id')==5 and !Session::get('admin_group')){?>
        $('#mobile').attr('readonly',true);
        <?php }?>
        //////////
        $(this).on( "keydown", function( e ) {
            var keyCode = e.keyCode || e.which;
            //console.log(keyCode);
            if(keyCode == 117) {
                $('#paymentModal').modal();
            }
            <!--IF:business_model_cond([[=business_model=]] and ([[=stt_id=]] != 5 and [[=stt_id=]] != 9))-->
            if(keyCode == 120) {
                SaveOrder(getId('checkoutBtn'),0,1);
            }
            <!--/IF:business_model_cond-->
            if(keyCode == 13) {
                mi_add_new_row('mi_order_product');
                suggestProduct(input_count);
                return false;
            }
        });
        $('#lich_hen').datepicker({format:'dd/mm/yyyy',language:'vi'});
        <?php if(Url::get('cmd')=='edit' and Url::get('fb_post_id')){?>
        $('#code').attr('readonly',true);
        <?php }?>
        $('#price').change(function(){
            $('#price').val(numberFormat($('#price').val()));
        });
        suggestCustomer();
        //updateDiscountPercent();
        $('#discount_rate').change(function(){
            if(to_numeric($(this).val())>100){
                $(this).val(100);
            }
            updateTotalPrice();
        });
    });
    function checkProduct(){
        let $return = false;
        for(let i=101;i<=input_count;i++){
            if(getId('id_'+i) && getId('product_id_'+i)){
                if($('#product_id_'+i).val()){
                    $return = true;
                }
            }
        }
        return $return;
    }
    function checkStaff(){
        var $return = false;
        for(var i=101;i<=input_count;i++){
            if(getId('id_'+i) && getId('staff_id_'+i)){
                if($('#staff_id_'+i).val()){
                    $return = true;
                }
            }
        }
        return $return;
    }
    function suggestCustomer(){
        $("#mobile").autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_order_by_keyword',
            minChars: 3,
            width: 500,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                $('#customer_id').val(ui.item.id);
                $('#customer_name').val($.trim(ui.item.customer_name));
                $('#mobile').val($.trim(ui.item.mobile));
                $('#address').val($.trim(ui.item.address));
                $('#city').val($.trim(ui.item.city));
                $('#city_id').val($.trim(ui.item.city_id));
                $('#discount_rate').val($.trim(ui.item.discount_rate));

                if($.trim(ui.item.discount_rate)>0){
                    $("#discount_rate").animate({zoom: '150%'}, "slow",function() {
                        $(this).animate({zoom: '100%'}, "slow");
                    });
                    $("#note").text('Áp dụng: thẻ VIP' + ui.item.vip_name + ' áp dụng từ ' + ui.item.start_date + ' đến ngày ' + ui.item.end_date);
                }
                updateTotalPrice();
                $('#viewCustomer').html('<div style="padding:5px 0px 5px 0px;"><a href="?page=customer&do=view&cid='+ui.item.encodeId+'&do=view" target="_blank"><i class="glyphicon glyphicon-search"></i> XEM</a> | <a href="#" onClick="emptyCustomer();return false;"><i class="glyphicon glyphicon-trash text-red"></i> XÓA</a></div>');
                return false;
            }
        });
        $("#customer_name").autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=suggest_customers',
            minChars: 3,
            width: 500,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                $('#customer_id').val(ui.item.id);
                $('#customer_name').val($.trim(ui.item.customer_name));
                $('#mobile').val($.trim(ui.item.mobile));
                $('#address').val($.trim(ui.item.address));
                $('#city').val($.trim(ui.item.city));
                $('#city_id').val($.trim(ui.item.city_id));
                $('#discount_rate').val($.trim(ui.item.discount_rate));
                if($.trim(ui.item.discount_rate)>0){
                    $("#discount_rate").animate({zoom: '150%'}, "slow",function() {
                        $(this).animate({zoom: '100%'}, "slow");
                    });
                }
                updateTotalPrice();
                $('#viewCustomer').html('<div style="padding:5px 0px 5px 0px;"><a href="<?=Url::build('customer')?>&do=view&cid='+ui.item.id+'&do=view" target="_blank"><i class="glyphicon glyphicon-search"></i> XEM</a> | <a href="#" onClick="emptyCustomer();return false;"><i class="glyphicon glyphicon-trash text-red"></i> XÓA</a></div>');
                return false;
            },
            focus: function (event, ui) {
                event.preventDefault();
            },
            open: function(){
                $('.ui-autocomplete').css('width', '341px'); // HERE
            }
        });
    }
    function emptyCustomer(){
        $('#customer_id').val(0);
        $('#customer_name').val('');
        $('#mobile').val('');
        $('#address').val('');
        $('#city').val('');
        $('#city_id').val(0);
        $('#viewCustomer').html('');
        $('#discount_rate').val(0);
        updateTotalPrice();
    }
    function SaveOrder(obj,stay,checkout){
        //if(checkout)
        {
            if(checkProduct()==false){
                alert('Bạn vui lòng nhập Sản phẩm / dịch vụ khách sử dụng...');
                return false;
            }
            //if(checkStaff()==false)
            {
                //alert('Bạn vui lòng nhập nhân viên thực hiện (kỹ thuật hoặc tư vấn)...');
                //return false;
            }
        }

        if (checkout == 2){
            let check = confirm('Bạn có chắc chắn sẽ hủy đơn hàng '+jQuery(`#order_code`).val()+'\nPhiếu thu-chi hủy theo đơn hàng.');
            if (!check) return false;
            jQuery(`#cancel`).val(1);
        }

        let mobile = $('#mobile').val();
        //var mobile2 = $('#mobile2').val();
        if(isNaN(mobile) == true){
            $('#mobile').focus();
            alert('Số điện thoại chính phải là số');
            return false;
        }
        $('#save').val(1);
        $('#stay').val(stay);
        $('#checkout').val(checkout);
        $(obj).html('Đang cập nhật...');
        $(obj).attr('disabled',true);
        EditAdminProcessForm.submit();
    }
    function updateTotalPrice(){
        //var price = to_numeric($('#price').val());
        let total_product_price = 0;
        for(var i=101;i<=input_count;i++){
            if(getId('id_'+i) && getId('product_price_'+i)){
                let qty = to_numeric($('#qty_'+i).val());
                let price = to_numeric($('#product_price_'+i).val());
                //var discount_rate = to_numeric($('#discount_rate_'+i).val());
                //var discount_amount = to_numeric($('#discount_amount_'+i).val());
                let total = price*qty;// -  discount_amount - (price*qty*discount_rate/100);
                $('#total_'+i).val(numberFormat(total));
                total_product_price += total;
            }
        }
        $('#price').val(numberFormat(total_product_price));
        let price = to_numeric(total_product_price);
        let discount_price = $('#discount_price').val()?to_numeric($('#discount_price').val()):0;
        let discount_rate = to_numeric($('#discount_rate').val());
        if(discount_rate){
            discount_price = price*discount_rate/100;
            $('#discount_price').val(numberFormat(discount_price));
        }
        let other_price = $('#other_price').val()?to_numeric($('#other_price').val()):0;
        let shipping_price = $('#shipping_price').val()?to_numeric($('#shipping_price').val()):0;
        let total1 = price - discount_price + shipping_price;
        total1 += other_price;
        total1 = numberFormat(total1);
        $('#total_price').val(total1);
        updateTotalPayment();
    }
    function updateDiscountPercent(){
        var publicPrice = to_numeric($('#publish_price').val());
        if(publicPrice){
            var price = to_numeric($('#price').val());
            var discount = ((publicPrice - price)/publicPrice)*100;
            $('#discount').val(discount);
        }
    }
    function suggestProduct(index){
        $("#product_name_"+index).autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_suggest_product',
            minChars: 3,
            width: 223,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                //console.log(ui.item);
                $('#product_id_'+index).val(ui.item.id);
                $('#product_name_'+index).val(ui.item.name);
                getProduct(index);
                return false;
            }
        });
    }
    function checkExistedProduct(index){
        let $return = true;
        for(let i=101;i<=input_count;i++){
            if(index != i && getId('id_'+i) && getId('product_id_'+i) && getId('product_id_'+index)){
                if(getId('product_id_'+i).value == getId('product_id_'+index).value){
                    $return = false;
                    break;
                }
            }
        }
        return $return;
    }
    function getProduct(index){
        let product_id = $('#product_id_'+index).val();
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'get_product',
                'product_id':product_id
            },
            beforeSend: function(){
            },
            success: function(content){
                content = $.trim(content);
                if(content == 'FALSE'){
                    alert('ID không tồn tại!');
                    $('#product_name_'+index).val('');
                    $('#product_id_'+index).val(0);
                    $('#total_'+index).val(0);
                    updateTotalPrice();
                    //console.log(product_id);
                }else{
                    let jsonData = $.parseJSON(content);
                    if(checkExistedProduct(index)){
                        $('#product_price_'+index).val(jsonData.price?numberFormat(jsonData.price):0);
                        //$('#size_'+index).val(jsonData.size);
                        //$('#color_'+index).val(jsonData.color);
                        if(to_numeric($('#qty_'+index).val())<=1){
                            $('#qty_'+index).val(1);
                        }
                        updateTotalPrice();
                    }else{
                        alert(jsonData.name + " đã được được thêm từ trước rồi.");
                        $('#product_name_'+index).val('');
                        $('#product_id_'+index).val(0);
                        $('#total_'+index).val(0);
                    }
                }
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function openChatWindow(obj,orderId,fbConversationId){
        window.open('https://admin.tuha.vn/Chat/fromOrder?order_id='+orderId+'&conversation_id='+fbConversationId+'&user_id=[[|md5_user_id|]]&act=do_login','chat','width=600,height=500,top=100,left=400');
    }
    function suggestStaff(index){
        $("#staff_name_"+index).autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_suggest_staff',
            minChars: 3,
            width: 223,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                //console.log(ui.item);
                $('#staff_id_'+index).val(ui.item.id);
                $('#staff_code_'+index).val(ui.item.code);
                $('#staff_name_'+index).val(ui.item.name);
                updateProductSelect(index);
                return false;
            }
        });
    }
    function checkUsedQty(index,obj){
        var maxQty = to_numeric($('#qty_'+index).val());
        var usedValue = to_numeric(obj.value);
        if(usedValue>maxQty){
            obj.value = maxQty;
            $.notify(
                {message: 'Số lượng sử dụng không được quá số lượng tối đa!' },
                {
                    type: 'danger',
                    placement: {
                        from: "bottom",
                        align: "center"
                    },
                    position: 'absolute',
                    offset: 20,
                    spacing: 10,
                    z_index: 1031,
                    delay: 5000,
                    timer: 1000
                }
            );
        }
    }
    function suggestCardNumber(){
        $("#turn_card_code").autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_turn_card_code',
            minChars: 3,
            width: 223,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                //console.log(ui.item);
                $('#turn_card_id').val(ui.item.id);
                $('#turn_card_code').val(ui.item.turn_card_code);
                $('#sold_date').val(ui.item.sold_date);
                $('#expired_date').val(ui.item.expired_date);
                var productsRows = {};
                productsRows = jQuery.parseJSON(ui.item.services);
                //console.log(productsRows);
                mi_init_rows('mi_order_product',productsRows);
                //////fill thông tin khách hàng/////
                $('#customer_id').val(ui.item.customer_id);
                $('#customer_name').val(ui.item.customer_name);
                $('#mobile').val(ui.item.mobile);
                $('#address').val(ui.item.address);
                $('#city').val(ui.item.city);
                $('#city_id').val(ui.item.city_id);
                ///////./end ///////////////////////
                return false;
            }
        });
    }
    function updateTotalPayment(){
        let total = 0;
        for(var i=101;i<=input_count;i++){
            if(getId('id_'+i) && getId('amount_'+i) && getId('payment_type_'+i)){
                let amount = 0;
                if(getId('bill_type_'+i).value==1){
                    amount = to_numeric($('#amount_'+i).val());
                    total += amount;
                }else{
                    amount = to_numeric($('#amount_'+i).val());
                    total = total - amount;
                    $('#input_group_'+i).css({'background':'#FBEE9E'});
                }
            }
        }
        $('#total_payment').val(numberFormat(total));
        $('#totalGuestHaveToPay').html(numberFormat(to_numeric($('#total_price').val()) - total));
    }
    function updateProductSelect(index){
        let newOptions = {};
        for(let i=101;i<=input_count;i++) {
            if (getId('id_'+i) && getId('product_id_'+i) && getId('product_name_'+i)){
                newOptions[getId('product_id_'+i).value] = getId('product_name_'+i).value;
            }
        }

        let $el = $('#product_id_'+index);
        let prevValue = $el.val();
        $el.empty();
        $.each(newOptions, function(key, value) {
            $el.append($('<option></option>').attr('value', key).text(value));
            if (value === prevValue){
                $el.val(value);
            }
        });
        $el.trigger('change');
        //$('#product_id_'+index)
        //$("#selectBox option[value='option1']").remove();
        //$("#selectBox").append('<option value="option6">option6</option>');
    }
</script>