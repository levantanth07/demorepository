<?php
    $cmd = Url::sget('cmd');
    $ownerGroup = 0;
    $customer = 0;
    if (is_group_owner()) {
        $ownerGroup = 1;
    }

    $adminGroup = Session::get('admin_group');
    if (check_user_privilege('CUSTOMER')) {
        $customer = 1;
    }
    $disable_negative_export = [[=disable_negative_export=]];
    $CMD = URL::get('cmd');
    $HAS_CONFIRMED = $CMD === 'edit' && $this->map['user_confirmed'];
    $confirm = $this->map['user_confirmed'] ?? 0;
    $CHECK_VALIDATE_DATE_DEPLOY = AdminOrders::VALIDATE_DATE_DEPLOY;
    $DISABLE_PRODUCT_PRICE_DATE = AdminOrders::DISABLE_PRODUCT_PRICE_DATE;
    $group_id = Session::get('group_id');
    $isAccountTestValidatePhone = [[=isAccountTestValidatePhone=]];
    $showPhoneType = $isAccountTestValidatePhone ? '' : 'd-none';
    $today = date('Y-m-d');
    $flagDateAdd = 1;
    $flagDateEdit = 1;
    $flagDateDisableAdd = 1;
    $flagDateDisableEdit = 1;
    $created = $this->map['created'];
    $isConfirm = 0;
    $orderId = 0;
    $userCurrentEdit = '';
    $sale_can_self_assigned = [[=sale_can_self_assigned=]];
    $currentLevel = '';
    $currentStatus = '';
    $user_delivered = '';
    $mobile = [[=mobile=]];
    $mobile2 = [[=mobile2=]];
    $allow_update_mobile = [[=allow_update_mobile=]];
    if($CMD == 'edit'){
        $currentLevel = [[=currentLevel=]];
        $currentStatus = (int)[[=currentStatus=]];
        $user_delivered = [[=user_delivered=]];
    }
    if(Url::get('id')){
        $orderId = Url::iget('id');
        $userCurrentEdit = AdminOrdersDB::is_edited($orderId);
    }
    $isEdited = 0;
    if($userCurrentEdit){
        $isEdited = 1;
    }
    if($confirm != 0){
        $isConfirm = 1;
    }
    if($CMD == 'add'){
        if(strtotime($today) >= strtotime($CHECK_VALIDATE_DATE_DEPLOY)){
            $flagDateAdd = 0;
        }
        if(strtotime($today) >= strtotime($DISABLE_PRODUCT_PRICE_DATE)){
            $flagDateDisableAdd = 0;
        }
    }
    if($CMD == 'edit'){
        if(strtotime($created) >= strtotime($CHECK_VALIDATE_DATE_DEPLOY)){
            $flagDateEdit = 0;
        }
        if(strtotime($created) >= strtotime($DISABLE_PRODUCT_PRICE_DATE)){
            $flagDateDisableEdit = 0;
        }
    }
    $orderStatus = $this->map['stt_id'];
?>
<script>
    var cmd = '<?php echo $CMD ?>';
    var disable_negative_export = '<?php echo $disable_negative_export ?>';
    var user_confirmed = '<?php echo $confirm ?>';
    var flagDateAdd = '<?php echo $flagDateAdd ?>';
    var flagDateEdit = '<?php echo $flagDateEdit ?>';
    var flagDateDisableAdd = '<?php echo $flagDateDisableAdd ?>';
    var flagDateDisableEdit = '<?php echo $flagDateDisableEdit ?>';
    var orderId = '<?php echo $orderId ?>';
    var orderStatus = '<?php echo $orderStatus ?>';
    var isConfirm = '<?php echo $isConfirm ?>';
    var isEdited = '<?php echo $isEdited ?>';
    var isAccountTestValidatePhone = '<?php echo $isAccountTestValidatePhone; ?>'; 
    var mobile = '<?php echo $mobile; ?>';
    var mobile2 = '<?php echo $mobile2; ?>';
    var allow_update_mobile = parseInt('<?php echo $allow_update_mobile; ?>');
    const mobile_type_domestic = '<?php echo AdminOrders::MOBILE_TYPE_DOMESTIC; ?>';
    const mobile_type_foreign = '<?php echo AdminOrders::MOBILE_TYPE_FOREIGN; ?>';
</script>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<style>
    #editOrderFormWrapper input[type=text],#editOrderFormWrapper input[type=number],#editOrderFormWrapper select, #editOrderFormWrapper .btn{
        height:28px;
        line-height: 20px;
        padding-top:2px;
        padding-bottom: 5px;
    }
    .clear-fix:after {content: "";clear: both;display: block;}
    .clear-fix {clear: both;display: block;content: ""}
    #box-transport-area .alert ul li {list-style-type: none}
    .product-mask{ background: #CCC; opacity: 0.3; float: left; position: absolute; height: 140px; width: 97%; z-index: 1000;}
    .product-detail{padding:2px;margin-bottom: 10px;background-color:#FFF;border-radius: 3px;}
    .product-detail table{margin:0px;}
    .product-detail table tr td{padding:2px;}
    .product-detail .panel-body{padding:0px;}
    .wrapper {
        overflow: initial;
    }
    .row-match-height {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display:         flex;
        flex-wrap: wrap;
    }
    .row-match-height > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
    .h-100 {
        height: 100%;
    }
    .w-100 {
        width: 100% !important;
    }
    .mb-1 {
        margin-bottom: 1rem
    }
    .row-no-gutters {
        margin-right: 0;
        margin-left: 0;
    }
    .row-no-gutters [class*=col-] {
        padding-right: 0;
        padding-left: 0;
    }

    .button-scroll {
        background: rgb(0, 192, 239);
        position: fixed;
        line-height: 50px;
        width: 50px;
        right: 50px;
        height: 50px;
        color: rgb(248,248,248);
        text-align: center;
        font-size: 22px;
        display: none;
        z-index: 1000;
        padding: 0!important;
    }
    .button-scroll i {
        line-height: 50px
    }
    .button-scroll:hover i {
        color: #FFFFFF
    }
    .scrollToTop {
        bottom: 102px;
    }
    input[disabled] ~ .checkmark {
        background: #fff !important;
        cursor: not-allowed;
    }

    .popover {
        min-width: 300px;
        max-width: 400px;
    }
    .popover-content {
        max-height: 200px;
        overflow-y: auto;
        font-size: 12px;
    }
    
    .callio-call-customer{
        cursor: pointer;
        align-items: center;
        display: flex;
        width: 30px;
        justify-content: center;
        position: absolute;
        top: 0; right: 15px;
        height: 100%;
        background: #6B66C4;
        color: #fff;
        border-radius: 0 3px 3px 0;
    }
    .voip24h-call-customer{
        cursor: pointer;
        align-items: center;
        display: flex;
        width: 30px;
        justify-content: center;
        position: absolute;
        top: 0; right: 15px;
        height: 100%;
        background: #3c8dbc;
        color: #fff;
        border-radius: 0 3px 3px 0;
    }

    .btn-discount-price{
        width: 50%
    }
    .btn-discount-price.active{
        color: #fff;
        background-color: #3276b1;
        border-color: #285e8e;
    }
    #ZoneModal input.btn{-webkit-user-select: initial; -moz-user-select: initial; -ms-user-select: initial; -o-user-select: initial; user-select: initial; }

    .mobile-group {
        display: flex;
    }
    .mobile-group > select {
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }
    .mobile-group > input {
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }
</style>
<?php
    $enable_product_rating = [[=enable_product_rating=]];
    $accept_edit_transport = [[=accept_edit_transport=]];
    $mi_product_class = !empty($enable_product_rating) ? 'col-xs-6' : 'col-xs-6';
    $statusIdsLv3 = [[=statusIdsLv3=]];
?>
<script>
    var province_id = '<?php echo isset([[=city_id=]])?[[=city_id=]]:''; ?>';
    var district_id = '<?php echo isset([[=district_id=]])?[[=district_id=]]:''; ?>';
    var adminGroup = '<?php echo  $adminGroup; ?>';
    var ownerGroup = '<?php echo  $ownerGroup; ?>';
    var customer = '<?php echo  $customer; ?>';

    var currentLevel = '<?php echo  $currentLevel ? $currentLevel : ''; ?>';
    var currentStatus = '<?php echo  $currentStatus ? $currentStatus : ''; ?>';
    var user_delivered = '<?php echo  $user_delivered ? $user_delivered : ''; ?>';
    var strStatusIdsLv3 = '<?php echo  json_encode($statusIdsLv3); ?>';
    var statusIdsLv3 = $.parseJSON(strStatusIdsLv3);
</script>
<div style="display:none">
    <div id="mi_reminder_sample">
		<div class="box box-default" id="input_group_#xxxx#">
            <div id="mask_#xxxx#" style="display:none;z-index:1;width: 100%;height:100%;background: #CCC;opacity: 0.3;position: absolute"></div>
            <div class="box-body">
                <div class="col-xs-6 no-padding">
                    <input  name="mi_reminder[#xxxx#][id]" type="hidden" id="id_#xxxx#" tabindex="-1">
                    <input  name="mi_reminder[#xxxx#][can_del]" type="hidden" id="can_del_#xxxx#" tabindex="-1">
                    <textarea  name="mi_reminder[#xxxx#][note]" rows="3" id="note_#xxxx#" class="form-control multi-edit-text-input" tabindex="1" style="padding:2px;background-color:#fdffd1;"></textarea>
                </div>
                <div class="col-xs-5 no-padding">
                    <input  name="mi_reminder[#xxxx#][appointed_time_display]" type="text" id="appointed_time_display_#xxxx#" class="form-control appointed_time_display" style="padding:2px;height:20px;font-size:11px;color:#ff8c52;">
                    <input  name="mi_reminder[#xxxx#][appointed_time]" type="hidden" id="appointed_time_#xxxx#">
                </div>
                <div class="col-xs-1 no-padding" id="del_#xxxx#">
                    <span class="glyphicon glyphicon-trash" onClick="if(confirm('Bạn có chắc muốn xóa lịch hẹn?')){mi_delete_row(getId('input_group_#xxxx#'),'mi_reminder','#xxxx#','reminder_');}return false;"></span>
                </div>
            </div>
        </div>
	</div>
    <div id="mi_order_product_sample">
        <div id="input_group_#xxxx#" style="background: #EFEFEF;">
            <!--IF:status_cond(![[=can_edit_status=]])-->
            <div class="product-mask" onclick="alert('Đơn đã xử lý vui lòng không thay đổi thông tin.');"></div>
            <!--/IF:status_cond-->
            <div class="product-detail" id="productDetail#xxxx#" style="box-shadow: 1px 1px 5px #999;">
                <div class="row hidden">
                    <div class="col-xs-4">
                        Mã
                    </div>
                    <div class="col-xs-8">
                       <input  name="mi_order_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="form-control" style="text-align:right;" value="(auto)" tabindex="-1" readonly>
                    </div>
                </div>
                <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div class="panel-heading">
                        <div class="row form-inline">
                            <div class="col-xs-5" style="padding-top:5px;">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="badge" style="color:#333;" id="product_index_#xxxx#"></span> Sản phẩm</button>
                                    </div>
                                    <!-- /btn-group -->
                                    <input name="mi_order_product[#xxxx#][product_name]" type="text" id="product_name_#xxxx#" class="form-control text-bold" readonly>
                                    <input name="mi_order_product[#xxxx#][product_id]" type="hidden" id="product_id_#xxxx#" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-7 text-right" style="padding-top:5px;">
                                <select name="mi_order_product[#xxxx#][select_product_id]" style="width: 350px;" class="form-control js-basic-multiple_#xxxx#" id="select_product_id_#xxxx#" tabindex="#xxxx#" onchange="getProduct('#xxxx#')">
                                    <option>-/-Chọn sản phẩm -/-</option>
                                </select>
                                <span class="btn btn-default" onClick="if(confirm('Bạn có chắc muốn xóa sản phẩm?')){mi_delete_row(getId('input_group_#xxxx#'),'mi_order_product','#xxxx#',''); updatePrice(); update_shipping_modal(); event.returnValue=false;}" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa sản phẩm. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="product-properties">
                            <table class="table">
                                <tr>
                                    <td>Mầu sắc</td>
                                    <td>SIZE</td>
                                    <td>Trọng lượng (g)</td>
                                    <td>Giá bán</td>
                                    <td>
                                        <div id="discount-type-[#xxxx#]" class="btn-group" data-toggle="buttons">
                                            <label class="btn btn-sm btn-default btn-discount-price active">
                                                <input type="radio" value="0"> Giảm giá
                                            </label>
                                            <label class="btn btn-sm  btn-default btn-discount-price">
                                                <input type="radio" value="1"> Giảm còn
                                            </label>
                                        </div>
                                    </td>
                                    <td>Số lượng</td>
                                    <td>Kho</td>
                                    <td>Thành tiền
                                        <a data-toggle="tooltip" title="Thành tiền từng sản phẩm = (Giá bán - Giảm giá) * Số lượng"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                                        <div class="tooltip bs-tooltip-top" role="tooltip">
                                          <div class="arrow"></div>
                                          <div class="tooltip-inner">
                                            Thành tiền từng sản phẩm = (Giá bán - Giảm giá) * Số lượng
                                          </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input  name="mi_order_product[#xxxx#][color]" class="form-control" type="text" id="color_#xxxx#" readonly tabindex="-1"></td>
                                    <td><input  name="mi_order_product[#xxxx#][size]" class="form-control" type="text" id="size_#xxxx#" readonly tabindex="-1"></td>
                                    <td><input  name="mi_order_product[#xxxx#][weight]" class="form-control product-weight" type="number" id="weight_#xxxx#" tabindex="#xxxx#"></td>

                                    <?php if($cmd == 'add'): ?>
                                        <?php if($flagDateDisableAdd == 0) : ?>
                                            <td><input  name="mi_order_product[#xxxx#][product_price]" class="form-control text-bold text-right product_price" type="text" id="product_price_#xxxx#" onChange="this.value=numberFormat(this.value);" tabindex="-1"></td>
                                            <input  name="mi_order_product[#xxxx#][product_price_hidden]" class="form-control text-bold text-right product_price_hidden" type="hidden" id="product_price_hidden_#xxxx#" onChange="this.value=numberFormat(this.value);" tabindex="-1" value="">
                                        <?php else : ?>
                                            <td><input  name="mi_order_product[#xxxx#][product_price]" class="form-control text-bold text-right product_price" type="text" id="product_price_#xxxx#" onChange="updateTotalPrice('#xxxx#');this.value=numberFormat(this.value);" tabindex="-1"></td>
                                            <input  name="mi_order_product[#xxxx#][product_price_hidden]" class="form-control text-bold text-right product_price_hidden" type="hidden" id="product_price_hidden_#xxxx#" onChange="updateTotalPrice('#xxxx#');this.value=numberFormat(this.value);" tabindex="-1" value="">
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if($cmd == 'edit'): ?>
                                        <?php if($flagDateDisableEdit == 0) : ?>
                                            <td><input  name="mi_order_product[#xxxx#][product_price]" class="form-control text-bold text-right product_price" type="text" id="product_price_#xxxx#" onChange="this.value=numberFormat(this.value);" tabindex="-1"></td>
                                            <input  name="mi_order_product[#xxxx#][product_price_hidden]" class="form-control text-bold text-right product_price_hidden" type="hidden" id="product_price_hidden_#xxxx#" onChange="this.value=numberFormat(this.value);" tabindex="-1">
                                        <?php else : ?>
                                            <td><input  name="mi_order_product[#xxxx#][product_price]" class="form-control text-bold text-right product_price" type="text" id="product_price_#xxxx#" onChange="updateTotalPrice('#xxxx#');this.value=numberFormat(this.value);" tabindex="-1"></td>
                                            <input  name="mi_order_product[#xxxx#][product_price_hidden]" class="form-control text-bold text-right product_price_hidden" type="hidden" id="product_price_hidden_#xxxx#" onChange="updateTotalPrice('#xxxx#');this.value=numberFormat(this.value);" tabindex="-1">
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <td>
                                        <input  name="mi_order_product[#xxxx#][discount_price]" onChange="this.value=numberFormat(this.value);" class="form-control text-right text-bold discount_amount" type="text" id="discount_price_#xxxx#" tabindex="#xxxx#" tabindex="#xxxx#" value="0">
                                        <input  onChange="this.value=numberFormat(this.value);" class="form-control text-right text-bold discount_remain hidden" type="text" id="remain_price_#xxxx#" tabindex="#xxxx#" tabindex="#xxxx#" value="0">
                                    </td>
                                    <td><input  name="mi_order_product[#xxxx#][qty]" class="form-control text-right text-bold product-quantity" type="text" id="qty_#xxxx#" tabindex="#xxxx#" tabindex="#xxxx#"></td>
                                    <td>
                                        <select  name="mi_order_product[#xxxx#][warehouse_id]" id="warehouse_id_#xxxx#" onchange="getProduct('#xxxx#');" class="form-control">[[|warehouse_id_options|]]</select>
                                        <div class="text-right text-success" style="display: none" id="onHandWrapper_#xxxx#">Tồn: <span id="onHand_#xxxx#">0</span></div></td>
                                    <td><input  name="mi_order_product[#xxxx#][total]" class="form-control text-danger text-bold text-right product_total" type="text" id="total_#xxxx#" readonly tabindex="-1"></td>
                                </tr>
                            </table>
                        </div>
                        <?php if (!empty($enable_product_rating)): ?>
                            <div class="" style="background-color: #EFEFEF;overflow: hidden; margin-top:5px;border-top:1px dotted #CCC;padding:2px;">
                                <div class="col-xs-4">
                                    <div class="row">
                                        <div class="col-xs-6 text-right">
                                            <label>Liệu trình</label>
                                        </div>
                                        <div class="col-xs-6">
                                            <select  name="mi_order_product[#xxxx#][which_process]" class="form-control" style="font-size:14px" id="which_process_#xxxx#" tabindex="#xxxx#">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Liệu trình <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="row">
                                        <div class="col-xs-6 text-right">
                                            <label>NV đánh giá</label>
                                        </div>
                                        <div class="col-xs-6">
                                            <select  name="mi_order_product[#xxxx#][staff_rating]" class="form-control" style="font-size:14px" id="staff_rating_#xxxx#" tabindex="#xxxx#">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 100; $i+=5): ?>
                                                    <option value="<?= $i ?>"><?= $i ?>%</option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="row">
                                        <div class="col-xs-6 text-right">
                                            <label>KH đánh giá</label>
                                        </div>
                                        <div class="col-xs-6">
                                            <select  name="mi_order_product[#xxxx#][customer_rating]" class="form-control" style="font-size:14px" id="customer_rating_#xxxx#" tabindex="#xxxx#">
                                                <option value="">Chọn</option>
                                                <?php for ($i = 0; $i <= 100; $i+=5): ?>
                                                    <option value="<?= $i ?>"><?= $i ?>%</option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container full" id="editOrderFormWrapper">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="<?=Url::build_current()?>">Đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">[[|title|]]</li>
            <li class="pull-right">
                <div class="pull-right">

                </div>
            </li>
        </ol>
    </nav>
    <?=Form::draw_flash_message_error('admin_order_add_or_update_error', ['max-width' => '800px'])?>
    <form name="EditAdminOrdersForm" method="post" autocomplete="off" role="presentation">
        <div class="box box-default">
            <div style="position: sticky;
                width: 100%;
                z-index: 99;
                top: 0;
                background: #fff;
                border-bottom: 1px solid #ccc" class="box-header with-border">
                <div class="row">
                    <div class="col-xs-8 text-left"><h3 class="title"><i class="glyphicon glyphicon-file"></i> [[|title|]]</h3></div>
                    <div class="col-xs-4 text-right">
                        <a class="btn btn-large btn-default" href="javascript:void(0);" onclick="introJs().setOptions({'prevLabel': '<','nextLabel': '>','skipLabel':'Bỏ qua','doneLabel':'Xong'}).start();">
                            <i class="fa fa-question-circle fa-spin"></i> Hướng dẫn sử dụng
                        </a>
                        <?php if($isEdited == 0): ?>
                        <button type="button"  class="btn btn-primary " style="min-width:68px;" onclick="SaveOrder(this);" data-step="<?=((Url::get('cmd')=='add')?'6':'7');?>" data-intro="Lưu đơn hàng"><i class="fa fa-floppy-o"></i> Lưu</button>
                        <input  name="save" type="hidden" id="save" value="">
                        <?php endif;  ?>
                        <!--IF:cond(Url::get('cmd')=='edit')-->
                        <input name="exit_order" type="submit" class="btn btn-danger" style="width:68px" value="Thoát">
                        <!--ELSE-->
                        <input name="exit_order" type="button" onClick="window.close();" class="btn btn-danger" style="width:68px" value="Thoát">
                        <!--/IF:cond-->
                    </div>
                </div>
            </div>
            <div class="box-body bg-gray-light">
                <div class="row">
                    <div class="col-xs-9">
                        <div class="row row-match-height">
                            <div class="col-xs-7">
                                <div class="box box-default box-solid">
                                    <div class="box-header with-border">
                                        <h4 class="box-title"><i class="glyphicon glyphicon-user"></i> Thông tin khách hàng </h4>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body cus-info-content form-inline" style="min-height: 630px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-4">
                                                <div class="form-group">Mã đơn hàng</div>
                                                <div class="form-group text-bold text-danger"><span class="label label-info"><?php echo Url::iget('id')?Url::iget('id'):'Tự động'?></span></div>
                                            </div>
                                            <div class="col-xs-8">
                                                <div class="text-right">
                                                    <div class="form-group">
                                                        Mã vận chuyển
                                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Trường hợp tích hợp đơn vị vận chuyển, 'Mã vận chuyển' sẽ tự động sinh ra khi chuyền về trạng thái chuyển hàng.">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                                                    </div>
                                                    <div class="form-group">
                                                        <input name="postal_code" type="text" id="postal_code" class="form-control" placeholder="Mã vận chuyển">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(Url::iget('customer_id')): ?>
                                        <div class="row mb-05">
                                            <div class="col-xs-4">
                                                <div class="form-group">Mã khách hàng</div>
                                                <div class="form-group text-bold text-danger">
                                                    <span class="label label-info"><?=Url::iget('customer_id')?></span>
                                                </div>
                                            </div>
                                            <div class="col-xs-8"></div>
                                        </div>
                                    <?php endif;?>

                                        <div class="row mb-05 text-warning text-bold">
                                            <div class="col-xs-4"><i class="fa fa-user"></i> Tên khách hàng</div>
                                            <div class="col-xs-8" <?=($cmd=='add')?' data-step="1" data-intro="Nhập tên khách hàng"':''?>>
                                                <input name="customer_name" type="text" id="customer_name" class="form-control" placeholder="Họ và Tên khách hàng" autocomplete="off">
                                                <input name="customer_id" type="hidden" id="customer_id">
                                            </div>
                                        </div>
                                        <div class="row phone mb-05 text-warning text-bold">
                                            <div class="col-xs-4">
                                                <i class="fa fa-phone"></i> Điện thoại
                                                <a data-toggle="tooltip" title="Chỉ được chọn 1 lần và không được thay đổi">
                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                </a> (*)
                                            </div>
                                            <div class="col-xs-8">
                                                <div class="row">
                                                    <div class="col-lg-6 mobile-group" style="padding-right: 0px" <?=($cmd=='add')?'data-step="2" data-intro="Nhập số điện thoại"':''?>>
                                                        <select name="mobiletype" id="mobiletype" data-target="#mobile" class="form-control <?php echo $showPhoneType ?>" style="padding: 0px; font-size: 10px"></select>
                                                        <input name="mobile" type="text" id="mobile" style="font-size:14px;font-weight: bold;color:#0630F5;padding:5px" class="form-control" placeholder="Điện thoại chính" data-tooltip='#jsElMobile1Tooltip' data-type='#mobiletype'>
                                                        <?php if ($cmd=='edit') { ?>
                                                            <?php if (Session::get('callio_payload')) { ?>
                                                                <a class="fa fa-phone callio-call-customer" style="right:0" data-phonenumber="<?=isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : ''?>"></a>
                                                            <?php } elseif (Session::get('voip24h_payload')) { ?>
                                                                <a class="fa fa-phone voip24h-call-customer" data-phonenumber="<?=isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : ''?>"></a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-lg-6 mobile-group" style="padding-left: 5px">
                                                        <select name="mobiletype2" id="mobiletype2" data-target="#mobile2" class="form-control <?php echo $showPhoneType ?>" style="padding: 0px; font-size: 10px"></select>
                                                        <input name="mobile2" type="text" id="mobile2" style="font-size:14px;padding:5px" class="form-control" placeholder="SĐT phụ" data-tooltip='#jsElMobile2Tooltip' data-type='#mobiletype2'/>
                                                        <?php if ($cmd=='edit') { ?>
                                                            <?php if (Session::get('callio_payload')) { ?>
                                                                <a class="fa fa-phone callio-call-customer" data-phonenumber="<?=isset($_REQUEST['mobile2']) ? $_REQUEST['mobile2'] : ''?>"></a>
                                                            <?php } elseif (Session::get('voip24h_payload')) { ?>
                                                                <a class="fa fa-phone voip24h-call-customer" data-phonenumber="<?=isset($_REQUEST['mobile2']) ? $_REQUEST['mobile2'] : ''?>"></a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="col-lg-6 mt-05">
                                                        <span id="jsElMobile1Tooltip" class="text-danger h6"></span>
                                                    </div>
                                                    <div class="col-lg-6 mt-05">
                                                        <span id="jsElMobile2Tooltip" class="text-danger h6"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-4"><i class="fa fa-calendar-o"></i> Ngày sinh</div>
                                            <div class="col-xs-8">
                                                <div class="row">
                                                    <div class="col-lg-6" style="padding-right: 0px">
                                                        <input name="birth_date" type="text" id="birth_date" class="form-control" placeholder="Ngày sinh khách hàng" data-mask>
                                                    </div>
                                                    <div class="col-lg-6" style="padding-left: 5px">
                                                        <select name="gender" id="gender" class="form-control" style="width: 100%"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-4"><i class="fa fa-envelope"></i> Email</div>
                                            <div class="col-xs-8">
                                                <input name="email" type="text" id="email" class="form-control" placeholder="Email khách hàng">
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-4"><i class="fa fa-users" aria-hidden="true"></i> Nhóm khách hàng</div>
                                            <div class="col-xs-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select name="customer_group" id="customer_group" class="form-control" style="width: 100%"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-05" <?=($cmd=='add')?'':'data-step="1" data-intro="Nhập địa chỉ chi tiết: số nhà, đường hoặc số phòng, tòa nhà, thôn xóm..."'?>>
                                            <div class="col-xs-4"><i class="fa fa-location-arrow"></i> Địa chỉ</div>
                                            <div class="col-xs-8">
                                                <textarea name="address" id="address" rows="2" class="form-control" placeholder="Số nhà, tên đường"></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-4">Tỉnh/Thành phố</div>
                                            <div class="col-xs-8">
                                                <div class="input-group">
                                                    <input name="city" type="text" id="city" class="form-control" onclick="$('#ZoneModal').modal();" autocomplete="disabled" readonly>
                                                    <!--IF:status_cond([[=can_edit_status=]])-->
                                                    <span class="input-group-btn" <?=($cmd=='add')?'':'data-step="2" data-intro="Nhập tỉnh thành"'?>>
                                                        <button id="selectZone" onclick="$('#ZoneModal').modal();" type="button" class="btn btn-default"><i class="fa fa-globe"></i> Chọn tỉnh thành</button>
                                                    </span>
                                                    <!--/IF:status_cond-->
                                                </div>
                                                <div class="mt-05">
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
                                                                            <input autocomplete="disabled" name="city_name" <?php if(isset([[=city_name=]])) echo 'value="'.[[=city_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Tỉnh / Thành phố" />
                                                                            <input  type="hidden" name="city_id" id="city_id" <?php if(isset([[=city_id=]])) echo 'value="'.[[=city_id=]].'"'; ?> class="input-id">
                                                                            <ul class="dropdown-menu">
                                                                                <!--LIST:zones-->
                                                                                <li><a data-name="[[|zones.name|]]" data-id="[[|zones.id|]]">[[|zones.name|]]</a></li>
                                                                                <!--/LIST:zones-->
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown" id="district">
                                                                            <input autocomplete="disabled" autocomplete="off"  name="district_name" <?php if(isset([[=district_name=]])) echo 'value="'.[[=district_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Quận / huyện"/>
                                                                            <input  type="hidden" name="district_id" id="district_id" <?php if(isset([[=district_id=]])) echo 'value="'.[[=district_id=]].'"'; ?>" data-name="<?php if(isset([[=district_name=]])) echo [[=district_name=]]; ?>" class="input-id">
                                                                            <ul class="dropdown-menu" ></ul>
                                                                            <?php if(isset([[=city_id=]])){?>
                                                                            <script>
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'province_id':province_id,
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
                                                                        <div class="dropdown" id="ward">
                                                                            <input autocomplete="disabled"  name="ward_name" <?php if(isset([[=ward_name=]])) echo 'value="'.[[=ward_name=]].'"'; ?> class="btn btn-default dropdown-toggle form-control input-name"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" placeholder="Phường / xã" />
                                                                            <input  type="hidden" name="ward_id" id="ward_id" <?php if(isset([[=ward_id=]])) echo 'value="'.[[=ward_id=]].'"'; ?>" class="input-id">
                                                                            <ul class="dropdown-menu" ></ul>
                                                                            <?php if(isset([[=district_id=]]) and isset([[=city_id=]])){?>
                                                                            <script>
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'province_id':province_id,
                                                                                        'district_id':district_id,
                                                                                        'type': 'ward'
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#ward ul').html(content);
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
                                                                    <script>
                                                                        $(document).ready(function(){
                                                                            /**
                                                                             * Gets the cities zone.
                                                                             *
                                                                             * @param      {<type>}  key     The key
                                                                             */
                                                                            function getCitiesZone(key)
                                                                            {
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone_city',
                                                                                        'key': key
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#city-drd ul').html(content);
                                                                                    }
                                                                                });
                                                                            }

                                                                            /**
                                                                             * Gets the districts.
                                                                             *
                                                                             * @param      {<type>}  province_id  The province identifier
                                                                             * @param      {<type>}  key          The key
                                                                             */
                                                                            function getDistricts(province_id, key)
                                                                            {
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'province_id':province_id,
                                                                                        'type': 'district',
                                                                                        'key': key
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#district ul').html(content);
                                                                                    }
                                                                                });
                                                                            }

                                                                            /**
                                                                             * Gets the wards.
                                                                             *
                                                                             * @param      {<type>}  district_id  The district identifier
                                                                             * @param      {<type>}  key          The key
                                                                             */
                                                                            function getWards(district_id, key)
                                                                            {
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'district_id':district_id,
                                                                                        'type': 'ward',
                                                                                        'key': key
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#ward ul').html(content);
                                                                                    }
                                                                                });
                                                                            }
                                                                            let address = $('#address').val();
                                                                            $('#address').keyup(function(){
                                                                                address = $('#address').val();
                                                                            });
                                                                            $('#city-drd ul li a').click(function(){
                                                                                $('#city-drd .input-name').val($(this).attr('data-name'));
                                                                                $('#city-drd .input-id').val($(this).attr('data-id'));
                                                                                $('#ward ul').html('');
                                                                                $("#district .input-name").val('');
                                                                                $("#district .input-id").val('');
                                                                                $("#ward .input-name").val('');
                                                                                $("#ward .input-id").val('');
                                                                                $('#city').val($(this).attr('data-name'));
                                                                                let province_id = $(this).attr('data-id');
                                                                                $.ajax({
                                                                                    method: "POST",
                                                                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                                                                    data : {
                                                                                        'cmd':'get_zone',
                                                                                        'province_id':province_id,
                                                                                        'type': 'district'
                                                                                    },
                                                                                    beforeSend: function(){
                                                                                    },
                                                                                    success: function(content){
                                                                                        $('#district ul').html(content);
                                                                                    }
                                                                                });
                                                                            });
                                                                            $('#city-drd .input-name').focus(function(){
                                                                                getCitiesZone(this.value.trim())
                                                                            })
                                                                            $('#city-drd .input-name').keyup(function(){
                                                                                $("#district .input-name").val('');
                                                                                $("#district .input-id").val('');
                                                                                $("#district ul").html('');
                                                                                $("#ward .input-name").val('');
                                                                                $("#ward .input-id").val('');
                                                                                $("#ward ul").html('');
                                                                                getCitiesZone(this.value.trim())
                                                                            });

                                                                            $('#district .input-name').focus(function(){
                                                                                getDistricts($('#city_id').val(), this.value.trim())
                                                                            })
                                                                            $('#district .input-name').keyup(function(){
                                                                                $("#ward .input-name").val('');
                                                                                $("#ward .input-id").val('');
                                                                                getDistricts($('#city_id').val(), $(this).val())
                                                                            });

                                                                            $('#ward .input-name').focus(function(){
                                                                                getWards($('#district_id').val(), this.value.trim())
                                                                            })
                                                                            $('#ward .input-name').keyup(function(){
                                                                                getWards($('#district_id').val(), $(this).val())
                                                                            });


                                                                            const addressUnits = ['ward', 'district', 'city-drd'];
                                                                            const updateAddress = function(){
                                                                                let dvhc = addressUnits.map(function(addressUnit){
                                                                                    let el = $('#'+addressUnit+' .input-name').get()[0];
                                                                                    return el.dataset.oldValue ? el.dataset.oldValue : el.value
                                                                                })
                                                                                .filter(e => !!e)
                                                                                .join(', ');

                                                                                address = address.replace(', ' + dvhc, '')
                                                                                $('#address').val(address + ', ' + dvhc)
                                                                            }
                                                                            addressUnits.map(function(addressUnit){
                                                                                $('#'+addressUnit+' .input-name').focus(function(){
                                                                                    this.dataset.oldValue = this.value
                                                                                })
                                                                                .focusout(function(){
                                                                                    const matches = $('#'+addressUnit+' .dropdown-menu li').get().filter(e => e.innerText.match(new RegExp('^' + this.value + '$', 'ui')));
                                                                                    if(matches.length){
                                                                                        this.value = matches[0].innerText;
                                                                                        this.dataset.oldValue = this.value
                                                                                    }else{
                                                                                        this.value = this.dataset.oldValue;
                                                                                    }
                                                                                })
                                                                            })
                                                                            $('#ZoneModal').on('hide.bs.modal', function(){
                                                                                updateAddress()
                                                                            })
                                                                        });

                                                                    </script>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">ĐỒNG Ý</button>
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
                                                <label for="note1">Ghi chú chung</label>
                                                <textarea name="note1" id="note1" class="form-control" rows="2" style="background-color: #fdffd1;"></textarea>
                                            </div>
                                            <div class="col-xs-6" style="padding:0;">
                                                <label for="note2">Ghi chú 2</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" id="note2_more">
                                                    <label for="note2_more" id="note2_more_add" style="margin: 0">Mở rộng</label>
                                                    <label for="note2_more" id="note2_more_edit" style="margin: 0; display: none">Sửa</label>
                                                </div>
                                                <textarea name="note2" id="note2" class="form-control" rows="2" style="background-color: #fdffd1;"></textarea>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="info-item clearfix" style="margin-top: 5px;">
                                            <div class="col-xs-12 form-inline no-padding">
                                                <label>Ghi Chú Giao Hàng</label>
                                                <textarea name="shipping_note" id="shipping_note" rows="2" class="form-control"></textarea>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <!--IF:cf_cond([[=accept_edit_transport=]] != 'not-intergrate-shipping')-->
                                        <div class="box">
                                            <div class="box-body row row-no-gutters">
                                                <div class="col-md-4">
                                                    <div class="mb-1">
                                                        <span class="label label-warning">Dịch vụ Viettel Post</span>
                                                    </div>
                                                    <select name="viettel_service" id="viettel_service" class="form-control w-100"></select>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-1">
                                                        <span class="label label-success">Hình thức vận chuyển GHTK</span>
                                                    </div>
                                                    <select name="transport_ghtk" id="transport_ghtk" class="form-control w-100"></select>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-1 text-right">
                                                        <span class="label label-warning">Dịch vụ EMS</span>
                                                    </div>
                                                    <select name="ems_service" id="ems_service" class="form-control w-100"></select>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/IF:cf_cond-->
                                        <div class="box box-primary">
                                            <div class="box-header">
                                                <div class="box-tools">
                                                    <span class="label label-primary"><i class="fa fa-facebook-square"></i> Facebook</span>
                                                </div>
                                                <!-- /.box-tools -->
                                            </div>
                                            <div class="box-body row">
                                                <!--IF:mkt_cond(AdminOrders::$quyen_admin_marketing or AdminOrders::$quyen_marketing)-->
                                                <div class="col-xs-4 no-padding">
                                                    <div class="col-xs-12"> ID bài post</div>
                                                    <div class="col-xs-12 form-group">
                                                        <input name="fb_post_id" type="text" id="fb_post_id" class="form-control">
                                                        <!--IF:cond1(Url::get('fb_post_id'))-->
                                                        <a href="https://www.facebook.com/<?=Url::get('fb_post_id');?>" target="_blank" class="small"><i class="fa fa-facebook-square"></i> Bài viết FB</a>
                                                        <!--/IF:cond1-->
                                                    </div>
                                                </div>
                                                <!--/IF:mkt_cond-->
                                                <!--IF:mkt_cond(AdminOrders::$quyen_admin_marketing or AdminOrders::$quyen_marketing)-->
                                                <div class="col-xs-4 no-padding">
                                                    <div class="col-xs-12">ID FanPage</div>
                                                    <div class="col-xs-12 form-group">
                                                        <input name="fb_page_id" type="text" id="fb_page_id" class="form-control">
                                                        <!--IF:cond2(Url::get('fb_page_id'))--><a href="https://www.facebook.com/<?=Url::get('fb_page_id');?>" target="_blank" class="small"><i class="fa fa-facebook-square"></i> Fan Page</a><!--/IF:cond2-->
                                                    </div>
                                                </div>
                                                <!--/IF:mkt_cond-->
                                                <!--IF:mkt_cond(AdminOrders::$quyen_admin_marketing or AdminOrders::$quyen_marketing)-->
                                                <div class="col-xs-4 no-padding">
                                                    <div class="col-xs-12">ID Fb Khách</div>
                                                    <div class="col-xs-12 form-group">
                                                        <input name="fb_customer_id" type="text" id="fb_customer_id" class="form-control">
                                                        <!--IF:fb_cond([[=fb_customer_id=]])-->
                                                        <div><a class="small" href="https://www.facebook.com/[[|fb_customer_id|]]" target="_blank"> <i class="fa fa-facebook"></i> Facebook Khách hàng</a></div>
                                                        <br>
                                                        <!--/IF:fb_cond-->
                                                        <a class="label label-default" href="https://findmyfbid.com/" target="_blank">Để lấy ID bạn nhấn vào đây</a>
                                                    </div>
                                                </div>
                                                <!--/IF:mkt_cond-->
                                            </div><!-- /.box-body -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="box box-default box-solid h-100">
                                    <div class="box-header">
                                        <h4 class="box-title"><i class="glyphicon glyphicon-list-alt"></i> Thông tin đơn hàng </h4>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body order-info-content" style="min-height: 630px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-4">
                                                <div class="label label-default">
                                                    <input  name="is_top_priority" type="checkbox" id="is_top_priority" <?php echo Url::get('is_top_priority')?'checked':'';?>>
                                                    <label for="is_top_priority">Ưu Tiên</label>
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="label label-default">
                                                      <input  name="is_send_sms" type="checkbox" id="is_send_sms" <?php echo Url::get('is_send_sms')?'checked':'';?>>
                                                      <label for="is_send_sms">Đã SMS</label>
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <div class="label label-default">
                                                      <input  name="is_inner_city" type="checkbox" id="is_inner_city" <?php echo Url::get('is_inner_city')?'checked':'';?>>
                                                      <label for="is_inner_city">Nội thành</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="">
                                            <div class="col-xs-<?=([[=user_delivered_name=]] and [[=user_confirmed_name=]])?'6':'12'?> no-padding">
                                                <!--IF:cond([[=user_confirmed_name=]])-->
                                                <div class="alert alert-default small" style="background-color:#CC6633;opacity:0.6;color:#fff;padding:2px;min-height: 50px;">
                                                    Xác nhận: <strong>[[|user_confirmed_name|]]</strong><br>vào lúc [[|confirmed|]]
                                                </div>
                                                <!--/IF:cond-->
                                            </div>
                                            <div class="col-xs-6 no-padding">
                                                <!--IF:cond([[=user_delivered_name=]])-->
                                                <div class="alert alert-default small" style="background-color:rgb(127, 192, 214);color:#fff;padding:2px;min-height: 50px;">
                                                    Chuyển hàng: <strong>[[|user_delivered_name|]]</strong><br>vào lúc [[|delivered|]]
                                                </div>
                                                <!--/IF:cond-->
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="row mb-05 text-warning text-bold">
                                            <div class="col-xs-5 control-label">Trạng thái</div>
                                            <div id="stt-box" class="col-xs-7 form-inline"  <?=($cmd=='add')?'data-step="3"':'data-step="3"'?> data-intro="Chọn trạng thái đơn hàng">
                                                <div id="stt-btn-custom" class=" w-100 dropdown-toggle" data-toggle="dropdown">
                                                    <span id="stt-color-custom" style="display: inline-block;width:10px;height:10px ;margin-right: 5px;<?php if(isset([[=stt_color=]])) echo 'background-color: '.[[=stt_color=]];else echo 'background-color:'.[[=stt_color_add=]]?>"></span>
                                                    <span id="stt-name-custom">
                                                    <?php if(isset([[=stt_name=]])) echo [[=stt_name=]];else echo 'Chưa xác nhận'; ?>
                                                  </span>
                                                </div>
                                                <!--IF:status_cond([[=can_edit_status=]])-->
                                                <button class="btn btn-warning" style="width: 100%;margin-top:2px;border-radius: 10px;border: 2px solid #ffffff;box-shadow: 1px 2px 3px #999;" type="button" id="btn-change-status" data-toggle="modal" data-target="#orderStatusModal"><i class="fa fa-hand-o-right"></i> Thay đổi trạng thái</button>
                                                <!--/IF:status_cond-->
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="clearfix"></div>

                                            <div class="row mb-05">
                                                <div class="col-xs-5 control-label"> Giao hàng</div>
                                                <div class="col-xs-7 form-inline">
                                                    <select name="shipping_service_id" id="shipping_service_id" class="form-control" style="width:100%;"></select>
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                        <div class="clearfix"></div>

                                        <hr style="margin:5px 0px 5px 0px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label"> Phân loại sản phẩm <a data-toggle="tooltip" title="Chỉ được chọn 1 lần và không được thay đổi">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </a> (*)</div>
                                            
                                            <div class="col-xs-7 form-inline" data-step="4" data-intro="Chọn phân loại sản phẩm cho đơn hàng">
                                                <select name="bundle_id" id="bundle_id" class="form-control" style="width:100%;"></select>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <hr style="margin:5px 0px 5px 0px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-5">
                                                <div class="row">
                                                    <div class="col-xs-5 control-label no-padding-r"> Nguồn (MKT)</div>
                                                    <div class="col-xs-7 form-inline no-padding" data-step="4" data-intro="Chọn nguồn số">
                                                        <select name="source_id" id="source_id" class="form-control" style="width:100%;"></select>
                                                    </div>
                                                 </div>
                                            </div>
                                            <div class="col-xs-7">
                                                <div class="row">
                                                    <div class="col-xs-5 control-label no-padding text-right" style="color: #ff8c52;font-weight: bold;">Loại đơn</div>
                                                    <div class="col-xs-7 form-inline no-padding-left" data-step="5" data-intro="Chọn đơn mới / CSKH / ...">
                                                        <select name="type" id="type" class="form-control" style="width:100%;color:#ff8c52"></select>
                                                    </div>
                                                    <div class="mb-05"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr style="margin:5px 0px 5px 0px;">
                                        <!--IF:cond3($this->map['can_change_created_user'])-->
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Người tạo đơn</div>
                                            <div class="col-xs-7 form-inline">
                                                <select name="user_created" id="user_created" class="form-control" style="width:100%;"></select>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <!--/IF:cond3-->
                                        <!--IF:cond1(Url::get('cmd')=='edit')-->
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Đơn tạo bởi</div>
                                            <div class="col-xs-7 form-inline small">
                                                [[|created_user_name|]]
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr style="margin:5px 0px 5px 0px;">
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Đơn đã chia cho</div>
                                            <div class="col-xs-7 form-inline">
                                                [[|assigned_user_name|]]
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <!--/IF:cond1-->
                                        <!--IF:chia_don_cond([[=quyen_chia_don=]])-->
                                        
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Chia đơn cho</div>
                                            <div class="col-xs-7 form-inline">
                                                <select name="assigned_user_id" id="assigned_user_id" class="form-control select2" style="width:100%;"></select>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        
                                        <!--/IF:chia_don_cond-->
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Nguồn Up Sale</div>
                                            <div class="col-xs-7 form-inline">
                                                <select name="upsale_from_user_id" id="upsale_from_user_id" class="form-control" style="width:100%;"></select>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div style="font-weight:bold;">Bổ sung các trường UCA</div>
                                        <br>
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Nguồn shop</div>
                                            <div class="col-xs-7">
                                                <input type="text" name="source_shop_id" disabled id="source_shop_id" class="form-control" value="<?php echo $this->map['source_shop_id'] ?? '' ?>">
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label"> Nguồn người tạo</div>
                                            <div class="col-xs-7">
                                                <input type="text" name="source_created_user" disabled id="source_created_user" class="form-control" value="<?php echo $this->map['source_created_user'] ?? '' ?>">
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Nguồn người upsale</div>
                                            <div class="col-xs-7">
                                                <input type="text" name="source_upsale" disabled id="source_upsale" class="form-control" value="<?php echo $this->map['source_upsale'] ?? '' ?>">
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <hr>
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Ngày lưu đơn</div>
                                            <div class="col-xs-7 form-inline">
                                                <input type="text" id="saved_date" name="saved_date" <?= ([[=user_delivered=]] && [[=status_level=]] > 2) ? '' : 'disabled' ?> class="form-control datetimepicker_pickdate" style="width: 100%" value="<?=isset($_REQUEST['saved_date']) ? $_REQUEST['saved_date'] : ''?>">
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <div class="row mb-05">
                                            <div class="col-xs-5 control-label">Ngày hẹn giao hàng</div>
                                            <div class="col-xs-7 form-inline">
                                                <input type="text" id="deliver_date" name="deliver_date" <?= ([[=user_delivered=]] && [[=status_level=]] > 2) ? '' : 'disabled' ?> class="form-control datetimepicker_pickdate" style="width: 100%" value="<?=isset($_REQUEST['deliver_date']) ? $_REQUEST['deliver_date'] : ''?>">
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                        <!--IF:cond([[=user_delivered=]] && [[=status_level=]] > 2)-->
                                            <input type="hidden" name="show_saved_date" value="1" />
                                        <!--/IF:cond-->
                                        <div class="clearfix"></div>
                                        <hr>
                                        <div class="row mb-05">
                                            <div class="col-xs-12 form-inline">
                                                <label for="shipping_note">Lý Do Hủy / xem xét lại</label>
                                                <div class="col-xs-12 no-padding">
                                                    <textarea name="cancel_note" id="cancel_note" class="form-control" style="width:100%;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row mb-05 order_not_success">
                                            <div class="col-xs-5 control-label">Lý do đơn chưa thành công</div>
                                            <div class="col-xs-7 form-inline">
                                                <select name="order_not_success" id="order_not_success" class="form-control select2" style="width:100%;"></select>
                                            </div>
                                            <div class="mb-05"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="box box-info box-solid panel-product">
                                    <div class="box-header">
                                        <h4 class="box-title">
                                            <i class="fa fa-shopping-cart"></i> Sản phẩm / Hàng hóa</span>
                                        </h4>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body row">
                                        <div class="col-xs-12">
                                            <div class="multi-item-wrapper">
                                                <div id="mi_order_product_all_elems"></div>
                                            </div>
                                            <br clear="all">
                                            <!--IF:status_cond([[=can_edit_status=]])-->
                                            <div <?=(Url::get('cmd')=='edit')?' data-step="6" data-intro="Thêm sản phẩm hàng hóa"':'';?>>
                                                <input type="button" value="+ THÊM SẢN PHẨM" class="btn btn-info" onclick="mi_add_new_row('mi_order_product');enableProductSelect2(input_count);$('#productDetail'+input_count).css({'background-color':'#c8fcff'});">
                                                <span class="text-warning">Hoặc nhấn <strong>Enter</strong></span>
                                            </div>
                                            <!--/IF:status_cond-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $shipping_address = [[=shipping_address=]];
                                $shipping_info = [[=shipping_info=]];
                                $shipping_panel_class = (URL::get('status_id') == 8 && $accept_edit_transport != 'not-intergrate-shipping') ? '' : "hidden";
                            ?>
                            <div class="col-xs-12">
                                <div class="panel panel-default panel-shipping <?= $shipping_panel_class ?>" id="panel-shipping">
                                    <div class="panel-heading">
                                        <h4>Hướng dẫn tích hợp vận chuyển</h4>
                                    </div>
                                    <div class="panel-body">
                                        <p><label>Bước 1:</label> Nhập trọng lượng của sản phẩm (gram).</p>
                                        <p><label>Bước 2:</label> Nhập chiều rộng, chiều cao, chiều dài kiện hàng (cm). <span class="text-danger">Chỉ bắt buộc nhập nếu chọn đơn vị vận chuyển là "Giao hàng nhanh".</span></p>
                                        <p><label>Bước 3:</label> Chọn địa chỉ lấy hàng.</p>
                                        <p><label>Bước 4:</label> Chọn hãng vận chuyển.</p>
                                        <p><label>Bước 5:</label> Nhấn "Lưu" đơn hàng.</p>
                                        <p class="text-danger">- Nếu chọn đơn vị vận chuyển là "Bưu điện Hà Nội", cần khai báo 'Id tra cứu', 'Mã xác thực' do bưu điện cung cấp tại màn hình <a href="index062019.php?page=admin_group_info" target="_blank">Tùy chỉnh shop</a>. Tab "Kết nối vận chuyển".</p>
                                        <p class="text-danger">- Nếu chọn đơn vị vận chuyển là "Giao hàng nhanh", cần khai báo 'ClientID', 'API key - Token' do GHN cung cấp tại màn hình <a href="index062019.php?page=admin_group_info" target="_blank">Tùy chỉnh shop</a>. Tab "Kết nối vận chuyển - Giao hàng nhanh".</p>
                                    </div>
                                </div>
                                <!-- <div class="alert alert-warning panel-shipping <?= $shipping_panel_class ?>" style="background: rgb(252, 248, 227) !important;color: rgb(138, 109, 59) !important;">
                                    - Lưu ý: Hệ thống sẽ mặc định chiều rộng, chiều cao, chiều dài của kiện hàng là 10 cm. Để hệ thống tính được chi phí vận chuyển đúng cho đơn hàng, quý khách nên khai báo chính xác các tham số này.
                                </div> -->
                                <div class="panel panel-info panel-shipping <?= $shipping_panel_class ?>">
                                    <div class="panel-heading">
                                        <i class="fa fa-truck"></i> Thông tin giao hàng
                                    </div>
                                    <div class="panel-body">
                                        <?php if (empty($shipping_address)): ?>
                                            <div class="add-new-address text-center">
                                                <a href="index062019.php?page=admin-shipping-address&do=list" target="_blank" class="btn btn-primary"><i class="fa fa-map-marker" aria-hidden="true"></i> Thêm địa chỉ lấy hàng</a>
                                            </div>
                                        <?php else: ?>
                                            <div class="info-item">
                                                <div class="col-xs-4 control-label"> <label for="">Trọng lượng sản phẩm/gram (*)</label> <span  data-toggle="tooltip" data-placement="bottom" title="Nếu gói hàng có chiều rộng hoặc chiều dài hoặc chiều cao > 20 cm, phí vận chuyển thực tế sẽ thay đổi theo khối lượng quy đổi . Nhập sai kích thước có thể khiến đơn vị vận chuyển trả hàng hoặc người mua từ chối nhận hàng do phí vận chuyển thực tế cao"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <input name="total_weight" type="number" id="total_weight" class="form-control" placeholder="Tổng trọng lượng (*)" />
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item">
                                                <div class="col-xs-4 control-label"> <label for="">Chiều dài kiện hàng/cm (*)</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <input name="total_length" value="10" type="number" id="total_length" class="form-control" placeholder="Chiều dài (*)" />
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item">
                                                <div class="col-xs-4 control-label"> <label for="">Chiều rộng kiện hàng/cm (*)</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <input name="total_width" value="10" type="number" id="total_width" class="form-control" placeholder="Chiều rộng (*)" />
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item">
                                                <div class="col-xs-4 control-label"> <label for="">Chiều cao kiện hàng/cm (*)</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <input name="total_height" value="5" type="number" id="total_height" class="form-control" placeholder="Chiều cao (*)" />
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item hidden">
                                                <div class="col-xs-4 control-label"> <label for="">Kiểm tra hàng</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <select name="note_code" id="note_code" class="form-control" style="width:100%;"></select>
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                        <?php if (1===2): ?>
                                            <div class="info-item hidden">
                                                <div class="col-xs-4 control-label"> <label for="">Vận chuyển thu tiền hộ</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <select name="is_cod" id="is_cod" class="form-control" style="width:100%;"></select>
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item hidden">
                                                <div class="col-xs-4 control-label"> <label for="">Miễn phí vận chuyển</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <select name="is_freeship" id="is_freeship" class="form-control" style="width:100%;"></select>
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="info-item hidden">
                                                <div class="col-xs-4 control-label"> <label for="">Lấy hàng tại điểm</label></div>
                                                <div class="col-xs-8 form-inline no-padding">
                                                    <select name="pick_option" id="pick_option" class="form-control" style="width:100%;"></select>
                                                </div>
                                                <div class="mb-05"></div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div id="box-shipping-area hidden">
                                                <div class="panel panel-default" style="margin-top: 15px">
                                                    <div class="panel-heading clear-fix">
                                                        Chọn địa chỉ lấy hàng
                                                        <a href="index062019.php?page=admin-shipping-address&do=list" class="btn btn-primary " style="margin-top: 2px;" target="_blank"><i class="fa fa-plus-circle"></i> Thêm mới</a>
                                                    </div>
                                                    <div class="panel-body">
                                                        <?php
                                                            if (!empty($shipping_address)) {
                                                                foreach ($shipping_address as $key => $value) {
                                                                    $checked = "";
                                                                    if (!empty($shipping_info['shipping_address_id'])) {
                                                                        if ($shipping_info['shipping_address_id'] == $value['id']) {
                                                                            $checked = "checked";
                                                                        }
                                                                    } else {
                                                                        if ($value['is_default'] == 1) {
                                                                            $checked = "checked";
                                                                        }
                                                                    }

                                                                    $lb_default = $value['is_default'] == 1 ? '<span class="label label-default">Mặc định</span>' : "";
                                                        ?>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="radio_shipping_address"
                                                                    id="shipping-address-<?= $value['id'] ?>"
                                                                    class = "radio_shipping_address"
                                                                    value="<?= $value['id'] ?>"
                                                                    data-province-id="<?= $value['province_id'] ?>"
                                                                    data-province-name="<?= $value['province_name'] ?>"
                                                                    data-district-id="<?= $value['district_id'] ?>"
                                                                    data-district-name="<?= $value['district_name'] ?>"
                                                                    data-ward-id="<?= $value['ward_id'] ?>"
                                                                    data-ward-name="<?= $value['ward_name'] ?>"
                                                                    <?= $checked ?>
                                                                >
                                                                <b><?= $value['name'] . '('. $value['phone'] .')' ?></b>   <?= $value['address'] .',' . $value['ward_name'] . ',' . $value['district_name'] . ',' . $value['province_name'] ?> <?= $lb_default ?>
                                                            </label>
                                                        </div>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="panel panel-default panel-carrier">
                                                    <div class="panel-heading">Chọn hãng vận chuyển</div>
                                                    <div class="panel-body">
                                                        <?php
                                                            if (Url::get('carrier_id')):
                                                                $costs_config = AdminOrdersConfig::get_list_shipping_costs();
                                                        ?>
                                                            <ul class="list-group">
                                                                <li class="list-group-item text-success" style="font-size: 18px"><i class="fa fa-check-circle" aria-hidden="true"></i> Đã chọn <?= $costs_config[Url::get('carrier_id')]['name'] ?> <a href="index062019.php?page=admin_orders&cmd=manager-shipping&search_text=<?= Url::iget('id') ?>" target="_blank" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i> Xem chi tiết</a></li>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div id="box-shipping-option"></div>
                                            </div>
                                        <?php endif; ?>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="box box-success box-solid">
                            <div class="box-header">
                                <h4 class="box-title"><i class="glyphicon glyphicon-usd"></i> Chi Phí </h4>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body paid-content">
                                <div class="info-item f-left w-100 mb-05">
                                    <div class="col-xs-5 control-label no-padding"> Thành Tiền</div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="price" type="text" id="price" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" readonly="">
                                    </div>
                                </div>
                                <div class="info-item f-left w-100 mb-05">
                                    <div class="col-xs-5 control-label no-padding">
                                        Giảm giá
                                    </div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="discount_price" type="text" id="discount_price" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" onchange="updatePrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="info-item f-left w-100 mb-05">
                                    <div class="col-xs-5 control-label no-padding"> Phí vận chuyển</div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="shipping_price" type="text" id="shipping_price" class="form-control text-right" style="width:100%;height: 25px;font-size:14px;" onchange="updatePrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="info-item f-left w-100 mb-05">
                                    <div class="col-xs-5 control-label no-padding">
                                        Phụ thu
                                    </div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="other_price" type="text" id="other_price" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" onchange="updatePrice();this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="info-item f-left w-100 mb-05">
                                    <div class="col-xs-5 control-label no-padding">
                                        <a href="https://ghn.vn/blogs/tin-tuc-ghn/dich-vu-khai-gia-bao-hiem-hang-hoa-ghn-express" target="_blank" title="Click vào đây để xem diễn giải">Khai giá</a>
                                        <br>
                                        <a title="Bảng khai giá dịch vụ vận chuyển" class="text-danger" style="cursor: pointer;" data-placement="bottom" data-toggle="popover_1" data-trigger="hover" data-content="- Bưu điện HN, VN: x4 cước dịch vụ khách hàng đã thanh toán (có bao gồm thuế GTGT) </br> - BEST <=20.000.000 </br> - EMS <= 20.000.000 (EMS HÓA ĐƠN) </br> - GHN <= 10,000,000 </br> - GHTK <= 20.000.000 </br> - Viettel Post <= 30.000.000 </br> - J&T <= 30.000.000">Lưu ý khai giá</a>
                                        <div class="small text-warning">
                                        </div>
                                    </div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="insurance_value" type="text" id="insurance_value" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" onchange="this.value=numberFormat(this.value);">
                                    </div>
                                </div>
                                <div class="info-item f-left w-100 mb-05">
                                    <hr>
                                    <div class="col-xs-5 control-label no-padding">
                                        <strong>Còn phải trả</strong>
                                    </div>
                                    <div class="col-xs-7 form-inline no-padding">
                                        <input name="total_price" type="text" id="total_price" class="form-control text-right" style="width:100%;font-weight:bold;height: 25px;font-size:14px" readonly="readonly">
                                    </div>
                                </div>
                                <div class="info-item" style="margin-top:50px"></div>
                                <div class="info-item"></div>
                            </div>
                        </div>
                        <div class="box box-primary box-solid">
                            <div class="box-header">
                                <div class="row">
                                <div class="col-xs-7">
                                    <h5 class="box-title">Thanh toán trước </h5>
                                </div>
                                <div class="col-xs-5">
                                    <button class="btn btn-info" type="button" id="btnShowPrepaidPopup"
                                    data-toggle="modal"
                                    data-target="#prepaidModal">Thanh toán</button>
                                </div>
                                </div>
                            </div>

                            <div class="box-body prepaid-content">
                                <input type="hidden" name="prepaidData" id="prepaidData"  value='<?php echo isset($this->map['prepaidData'])? $this->map['prepaidData'] : "[]"?>'>

                                <div class="col-xs-5 control-label no-padding">Trả trước</div>
                                <div class="col-xs-7 form-inline no-padding">
                                    <input  name="prepaid" id="txtPrepaid" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" readonly="" type ="text" value="<?php echo isset($this->map['prepaid'])? $this->map['prepaid']:0 ?>">
                                </div>
                                <div class="col-xs-5 control-label no-padding">Còn nợ</div>
                                <div class="col-xs-7 form-inline no-padding">
                                    <input  name="prepaid-remain" id="txtPrepaidRemain" class="form-control text-right" style="width:100%;height: 25px;font-size:14px" readonly="" type ="text" value="<?php echo isset($this->map['prepaid_remain'])? $this->map['prepaid_remain']: 0 ?>">
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="prepaidModal" tabindex="-1">
                                <div class="modal-dialog modal-sm" style="max-width: 800px">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Thanh toán trước</h4>
                                        </div>
                                        <div class="modal-body" style="max-height: 400px; overflow-x: auto">
                                            <table class="table table-responsive table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Hình thức(*)</th>
                                                    <th>Số tiền(*)</th>
                                                    <th>Ghi chú</th>
                                                    <td></td>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-xs-6 text-right">
                                                <strong>Tổng trả trước: <span id="totalPrepaid">0</span></strong><br>
                                                <strong>Còn nợ:  <span id="totalPrepaidRemain">0</span></strong>
                                            </div>
                                            <div class="col-xs-6">
                                            <div type="button" id="btnClonePrepaidRow" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Thêm
                                            </div>
                                            <div type="button" id="btnSavePrepaid" class="btn btn-success">
                                                <i class="fa fa-floppy-o"></i> Lưu lại
                                            </div>
                                        </div>
                                        <script>
                                            $( document ).ready(function() {
                                                $(document).on('change paste','.prepaid-amount, #insurance_value',function(e){
                                                    let _this = $(this);
                                                    let thisValue = to_numeric(_this.val());

                                                    if (thisValue < 0) {
                                                        alert('Vui lòng nhập giá trị lớn hơn 0');
                                                        _this.val(0);
                                                        return;
                                                    }
                                                    if (isNaN(thisValue)) {
                                                        alert('Ký tự không hợp lệ. Vui lòng nhập số.');
                                                        _this.val(0);
                                                        return;
                                                    }
                                                    _this.val(numberFormat(thisValue))
                                                });
                                                function buildPrepainRow(prepaindType = 2, prepaidAmount = 0, prepaidNote = "", id = ""){
                                                    var row = '<tr class="prepaid-row">';
                                                    row += '<td>'+
                                                            '<input type="hidden" class="prepaid-id" value="'+id+'">'+
                                                            '<select  class="form-control prepaid-type">';
                                                    if(prepaindType === 2)row += '<option value="2" selected>Tiền mặt</option>';
                                                    else row += '<option value="2">Tiền mặt</option>';
                                                    if(prepaindType === 1) row +='<option value="1" selected>Thẻ ATM</option>';
                                                    else row +='<option value="1">Thẻ ATM</option>';
                                                    if(prepaindType === 3) row +='<option value="3" selected>Chuyển khoản</option>';
                                                    else row +='<option value="3">Chuyển khoản</option>';

                                                    row += '</select>'+
                                                        '</td>'+
                                                        ' <td>'+
                                                        '<input class="form-control prepaid-amount" type="text" value="'+numberFormat(prepaidAmount)+'">'+
                                                        '</td>'+
                                                        '<td>'+
                                                            '<input class="form-control prepaid-note" type="text" value="'+prepaidNote+'">'+
                                                        '</td>'+
                                                        '<td>'+
                                                            '<div class="btn btn-danger btnRemoveRow">'+
                                                                '<i class="fa fa-trash-o"></i>'+
                                                            '</div>'+
                                                        '</td>'+
                                                    '</tr>';
                                                    return row;
                                                }

                                                function calculateTotalPrepaid(){
                                                    var total = 0;
                                                    $('.prepaid-amount').each(function(){
                                                        if(is_numeric($(this).val().replace(/,/g,''))){
                                                            var amount = parseInt($(this).val().replace(/,/g,''));
                                                            if(amount > 0) total+= parseInt(amount);
                                                        }
                                                    });
                                                    return total;
                                                }
                                                function parsePrepaidData(){
                                                    var total = 0;
                                                    var dat = [];
                                                    $('.prepaid-row').each(function(){
                                                        var row = $(this);
                                                        var pay_amount =  parseInt(row.find('.prepaid-amount').val().replace(/,/g,''));
                                                        var id =  parseInt(row.find('.prepaid-id').val().replace(/,/g,''));
                                                        var pay_type =  parseInt(row.find('.prepaid-type').val().replace(/,/g,''));
                                                        var note = row.find('.prepaid-note').val();
                                                        if(pay_amount > 0)
                                                        dat.push({pay_type, pay_amount, note, id});
                                                    });
                                                    return dat;
                                                }
                                                function savePrepaidData(){
                                                    var total_prepaid = calculateTotalPrepaid();
                                                    var total_price = parseInt($('#total_price').val().replace(/,/g,''));
                                                    if(total_prepaid <= total_price){
                                                        $('#totalPrepaid').text(numberFormat(parseInt(total_prepaid)));
                                                        $('#totalPrepaidRemain').text(numberFormat(parseInt(total_price - total_prepaid)));

                                                        $('#txtPrepaid').val(numberFormat(parseInt(total_prepaid)));
                                                        $('#txtPrepaidRemain').val(numberFormat(parseInt(total_price) - parseInt(total_prepaid)));

                                                        return parsePrepaidData();
                                                    }else{
                                                        alert("Tổng tiền trả trước đang lớn hơn tổng tiền phải trả!" );
                                                        return false;
                                                    }
                                                }

                                                function updateTotalPrepaid(){
                                                    var total_prepaid = calculateTotalPrepaid();
                                                    var total_price = parseInt($('#total_price').val().replace(/,/g,''));
                                                    if(total_prepaid > 0 && total_price > 0){

                                                        $('#totalPrepaid').text(numberFormat(total_prepaid));
                                                        $('#totalPrepaidRemain').text(numberFormat(total_price - total_prepaid));
                                                        if(total_price < total_prepaid){
                                                            alert('Tổng tiền trả trước đang lớn hơn tổng tiền phải trả. Vui lòng kiểm tra lại.');
                                                        }
                                                    }
                                                }

                                                $('#prepaidModal').on('shown.bs.modal',  function () {
                                                    var data = JSON.parse($("#prepaidData").val());
                                                    if(data.length){
                                                        $('#prepaidModal table tbody').empty();
                                                        data.map(function(dat){
                                                            console.log(dat);
                                                            var row = buildPrepainRow(parseInt(dat.pay_type), parseInt(dat.pay_amount), dat.note, dat.id );
                                                            $('#prepaidModal table tbody').append(row);
                                                        });
                                                        updateTotalPrepaid();
                                                    }else{
                                                        $('#prepaidModal table tbody').empty();
                                                        var row = buildPrepainRow()
                                                        $('#prepaidModal table tbody').append(row);
                                                    }

                                                });
                                                $('#prepaidModal').on('change', '.prepaid-amount', function (event) {
                                                    updateTotalPrepaid()
                                                });

                                                $('body').on('click', "#btnClonePrepaidRow", function(){
                                                    var prepaidRow = $('#prepaidModal table tr:last-child').last()[0];
                                                    var newRow = buildPrepainRow();
                                                    $('#prepaidModal table tbody').append(newRow);
                                                });

                                                $('body').on('click', "#btnSavePrepaid", function(){
                                                    var dat = savePrepaidData();
                                                    if(dat){
                                                        var prepaidData = JSON.stringify(dat);
                                                        $('#prepaidData').val(prepaidData);
                                                        $('#prepaidModal').modal('hide');
                                                    }
                                                });

                                                $('body').on('click', ".btnRemoveRow", function(){
                                                    var row = $(this).closest('tr').remove();
                                                    updateTotalPrepaid();
                                                });


                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box box-warning" style="background-color: #efefef;">
                            <div class="box-header with-border">
                                <h4 class="box-title"><i class="fa fa-calendar"></i> Lịch hẹn </h4>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div id="mi_reminder_all_elems">
                                    <div style="width: 100%;float: left;">
                                        <div class="col-xs-6 no-padding"><label>Nội dung nhắc lịch</label></div>
                                        <div class="col-xs-5 no-padding"><label>Thời gian hẹn</label></div>
                                        <div class="col-xs-1 no-padding"></div>
                                    </div>
                                </div>
                                <div class="add-mi-button"><input class="btn btn-warning btn-sm" type="button" value="+ Thêm" onclick="mi_add_new_row('mi_reminder');$('#appointed_time_display_'+input_count).datetimepicker({format: 'DD/MM/YYYY HH:mm',useCurrent: false});$('#appointed_time_display_'+input_count).on('dp.change', function (e) {$('#appointed_time_'+input_count).val(e.date.unix());});"></div>
                            </div>
                        </div>
                        <?php if($cmd == 'edit'): ?>
                            <div class="box box-danger" id="load_data_history" style="background-color: #efefef;">
                                <div class="text-center"><button type="button" id="view_history_order" class="btn btn-default text-center"> <i class="glyphicon glyphicon-time"></i> Xem lịch sử đơn hàng</button></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
        <input  name="old_status_id" id="old_status_id" type="hidden" value="<?php echo URL::iget('status_id');?>">
        <input  name="refer_url" id="refer_url" type="hidden" value="[[|refer_url|]]">
        <!-- Modal show phone number-->
        <div class="modal fade" id="md-show-phone-number" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="title text-danger text-center"><i class="fa fa-exclamation-circle"></i> Đơn hàng đã tạo từ cùng SĐT</h3>
                    </div>
                    <div class="modal-body">
                        <div class="box">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mã ĐH</th>
                                    <th>Người tạo</th>
                                    <th>Tên KH</th>
                                    <th>SĐT</th>
                                    <th>SĐT Phụ</th>
                                    <th>Loại</th>
                                    <th>Địa chỉ</th>
                                    <th>Tỉnh / thành</th>
                                    <th>Ghi chú chung</th>
                                    <th>Ghi chú 2</th>
                                    <th>*</th>
                                </tr>
                                </thead>
                                <tbody id="show-phone-number">
                                </tbody>
                            </table>
                        </div>
                        <script>

                            <!--IF:admin_cond(AdminOrders::$admin_group or Url::get('cmd')=='add')-->

                            $('#mobile2').change(function() {
                                $('#mobile2').val($('#mobile2').val().trim());
                            });

                            $('#mobile').change(function(){
                                $('#mobile').val($('#mobile').val().trim());
                                let spnb = $('#show-phone-number');
                                $.ajax({
                                    method: "POST",
                                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                                    data : {
                                        'cmd':'get_phone_number',
                                        'phone':$(this).val(),
                                        'order_id': <?php echo Url::iget('id')?Url::iget('id'):0; ?>
                                    },
                                    beforeSend: function(){
                                    },
                                    success: function(content){
                                        if(content != 0){
                                            spnb.html(content);
                                            $('#md-show-phone-number').modal('show');
                                        }
                                        else{
                                            console.log('no result');
                                        }
                                    }
                                });
                            });
                            <!--/IF:admin_cond-->
                        </script>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
            $status_arr_custom = [[=status_arr_custom=]];
            $status_id = (isset([[=stt_id=]]) && [[=stt_id=]]) ? [[=stt_id=]] : 10;
            $status_arr_level = [
                0 => 'Level 0',
                1 => 'Level 1',
                2 => 'Level 2',
                3 => 'Level 3',
                4 => 'Level 4',
                5 => 'Level 5'
            ];
            $text_cancel_button = ($status_id == 9) ? 'Đã hủy đơn hàng' : 'Hủy đơn hàng';
        ?>
        <div id="orderStatusModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-xl" style="width: 1000px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Chọn trạng thái đơn hàng</h4>
                    </div>
                    <div class="modal-body">
                        <div class="status-header text-center">TRẠNG THÁI ĐƠN HÀNG</div>
                        <div class="contain-row">
                            <div class="row row-equal row-no-padding" id="order-status-row">
                                <?php foreach ($status_arr_level as $level => $name_level): ?>
                                    <div class="col-xs-2">
                                        <div class="step-ward-arrow"><?= $name_level ?></div>
                                        <div class="step-ward-box">
                                            <?php
                                            if(!empty($status_arr_custom[$level])):
                                            foreach ($status_arr_custom[$level] as $item_status):
                                            $color_style = !empty($item_status['color']) ? 'style="background: '. $item_status['color'] .'"' : "";
                                            $checked = ($status_id == $item_status['id']) ? 'checked' : "";
                                            $hidden = ($item_status['id'] == 9) ? 'hidden' : "";
                                            ?>
                                                <div class="step-ward-item <?= $hidden ?>">
                                                    <div class="radio-custom">
                                                        <label class="checkcontainer">
                                                            <input type="radio"
                                                                id="status_<?= $item_status['id'] ?>"
                                                                class="order-status"
                                                                name="status_id"
                                                                value="<?= $item_status['id'] ?>"
                                                                data-name="<?= $item_status['name'] ?>"
                                                                data-color="<?= $item_status['color'] ?>"
                                                                <?=(!$HAS_CONFIRMED && $level >= 3) ? 'disabled' : ''?>
                                                                <?= $checked ?>
                                                            >
                                                            <i class="status-color" <?= $color_style; ?>></i>
                                                            <span class="checkmark"></span>
                                                            <?= $item_status['name'] ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; endif;?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--IF:cf_cond([[=user_confirmed_name=]])-->
                        <div class="alert alert-default no-padding-t no-padding-b">
                            Xác nhận bởi <strong>[[|user_confirmed_name|]]</strong> vào lúc [[|confirmed|]]
                        </div>
                        <!--/IF:cf_cond-->
                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="btn-cancel"><?= $text_cancel_button ?></button>
                        <span class="text-warning">Chú ý với trạng thái CHUYỂN HÀNG, CHUYỂN HOÀN, THÀNH CÔNG chỉ chuyển được với đơn đã <strong>xác nhận (chốt đơn)</strong>.</span>
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-status">Đóng</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <input type="hidden" name="reminder_deleted_ids" id="reminder_deleted_ids" value=""/>
    </form>
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

<a href="#" rel="nofollow" class="scrollToTop button-scroll" title="Lên đầu trang"><i class="fa fa-arrow-circle-o-up"></i></a>
<div id="loading"><span class="loader"></span></div>
<script src="packages/core/includes/js/multi_items.js"></script>
<script type="text/javascript" src="assets/standard/js/multiple.select.js"></script>

<link rel="stylesheet" href="assets/vissale/css/jquery-confirm.css">
<script src="assets/vissale/js/jquery-confirm.js"></script>
<?php require_once ROOT_PATH . 'assets/lib/medidoc/index.php';?>
<script>
    var accept_edit_transport = '<?= [[=accept_edit_transport=]] ?>';
    var shipping_options = <?= [[=shipping_options=]] ?>;
    var shipping_option_viettel = <?= [[=shipping_option_viettel=]] ?>;

    Number.prototype.numberFormat = function(decimals, dec_point, thousands_sep) {
        dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
        thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';

        var parts = this.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

        return parts.join(dec_point);
    }

    function handleInputPhoneNumber () {
        this.value = this.value.replace(/[^0-9]/g, '')
    }

    function addInputInvalid (el) {
        el.removeClass('input-valid').addClass('input-invalid');
    }

    function addInputValid (el) {
        el.addClass('input-valid').removeClass('input-invalid');
    }

    function handleValidateMobile(element, isNullable = false) {
        let elMobile = $(element.data('target'));
        handleValidateNumber(elMobile, isNullable);
    }

    function handleValidateNumber(element, isNullable = false) {
        let phoneNumber = String(element.val());
        let elTooltip = $(element.data('tooltip'));
        let elMobileType = $(element.data('type'));

        const mobileType = elMobileType.val();
        let minLength = 10;
        let maxLength = 11;
        let pattern = /^0[1-9][0-9]{8,9}$/g;
        let exPhone = '0987xxxxxx';

        let length = phoneNumber.length;
        if (isNullable && !length) {
            elTooltip.html('');
            addInputValid(element);
            addInputValid(elMobileType);
            return true;
        }//end if

        if (length && phoneNumber.charAt(0) != '0') {
            phoneNumber = '0' + phoneNumber;
        }//end if

        if (mobileType == mobile_type_foreign) {
            pattern = /^00[1-9][0-9]{1,17}$/g;
            minLength = 4;
            maxLength = 20;
            exPhone = '0087xxxxxx';
            if (phoneNumber.charAt(1) != '0') {
                phoneNumber = '0' + phoneNumber;
            }//end if
        } else {
            phoneNumber = phoneNumber.replace(/^0+/g, '0');
        }//end if
    
        element.val(phoneNumber);
        length = phoneNumber.length;
        
        if (length < minLength || length > maxLength) {
            addInputInvalid(element);
            addInputInvalid(elMobileType);
            elTooltip.html(`(*) Vui lòng nhập SĐT từ ${minLength} - ${maxLength} ký tự <br> Bạn đã nhập <b>[ ${length} ]</b> ký tự`);
            return false;
        }//end if

        if (!phoneNumber.match(pattern)) {
            addInputInvalid(element);
            addInputInvalid(elMobileType);
            elTooltip.html(`(*) Số điện thoại không hợp lệ (Ex: ${exPhone})`);
            return false;
        }//end if

        elTooltip.html('');
        addInputValid(element);
        addInputValid(elMobileType);
        return true;
    }

    function checkPhoneNumberType(phoneNumber, elName) {
        let el = $(elName);
        if (phoneNumber.charAt(0) != '0') {
            return;
        }//end if

        if (phoneNumber.charAt(1) == '0') {
            return el.val(mobile_type_foreign).change();
        }//end if
        
        return el.val(mobile_type_domestic).change();
    }

    mi_init_rows('mi_order_product',<?php if(isset($_REQUEST['mi_order_product'])){echo MiString::array2js($_REQUEST['mi_order_product']);}else{echo '[]';}?>);
    mi_init_rows('mi_reminder',<?php if(isset($_REQUEST['mi_reminder'])){echo MiString::array2js($_REQUEST['mi_reminder']);}else{echo '[]';}?>);

    var products_transport = []
    var transport_obj_selected = {}
    var total_weight = 0
    var shipping_address_txt = ''
    var account_transport_selected = "";
    $('.order_not_success').hide();
    if(cmd == 'edit' && user_delivered){
        $('.order_not_success').show();
        if(currentStatus == 5 || currentStatus == 2){
            $('.order_not_success').hide();
        } else if(currentLevel < 3){
            $('.order_not_success').hide();
        }
    }



    $(document).ready(function(){

        if (isAccountTestValidatePhone) {
            checkPhoneNumberType(mobile, '#mobiletype');
            checkPhoneNumberType(mobile2, '#mobiletype2');
            if (cmd == 'add' || allow_update_mobile) {
                $('#mobile').on('keyup paste', handleInputPhoneNumber);
                $('#mobile').on('change', function () {
                    handleValidateNumber($(this));
                });
                $('#mobiletype').on('change', function () {
                    handleValidateMobile($(this));
                });
            }

            $('#mobile2').bind('keyup paste', handleInputPhoneNumber);
            $('#mobile2').bind('change', function () {
                handleValidateNumber($(this), true);
            });
            $('#mobiletype2').bind('change', function () {
                handleValidateMobile($(this), true);
            });
        }

        $('.order-status').on('click',function(){
            let status = $(this).val();
            $.ajax({
                    type: "POST",
                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                    data : {
                        'cmd':'get_status',
                        'statusId': status,
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data){
                            if(cmd == 'edit' && user_delivered){
                                if(data.id == 5 || data.id == 2){
                                    $('.order_not_success').hide();
                                } else if (data.level < 3){
                                    $('.order_not_success').hide();
                                } else {
                                    $('.order_not_success').show();
                                }
                            }
                        }
                        
                    },
                    complete: function() {
                        
                    }
                });
        })

    	$('[data-toggle="popover_1"]').popover({
            html: true,
            content: function() {
              return $('#popover-content').html();
            }
        });

        $('#view_history_order').on('click',function(){
            let data;
            data = {
                'cmd':'view_history_order',
                'order_id':orderId
            }
            $.ajax({
                method: "POST",
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                data : data,
                beforeSend: function(){
                },
                success: function(content){
                    if(content){
                        $('#load_data_history').html(content)
                    }
                    else{
                        alert("No result");
                    }
                }
            });
        })

        // Form tang chieu cao
        $(document).on('keypress','.chieu_cao, .can_nang',function(e){
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        // $(document).on('click','.mo_hoi',function(e){
        //     let ngay_tao = $('#ngay_tao').val();
        //     if(ngay_tao == ''){
        //         $('#ngay_tao').addClass('is-invalid');
        //         return false;
        //     } else {
        //         $('#ngay_tao').removeClass('is-invalid');
        //     }
        // })
    });

    function build_transport_html() {
        $('#box-shipping-area .panel-carrier .panel-body').empty()
        $('#box-shipping-option').empty()
        var html = `
            <table class="table table-bordered" style="margin-bottom: 5px;">
                <thead>
                    <tr class="info">
                        <th></th>
                        <th>Hãng vận chuyển</th>
                        <th>Dịch vụ</th>
                        <th>Thời gian</th>
                        <th>Phí vận chuyển</th>
                        <th>Phí thu hộ</th>
                        <th>Tổng phí</th>
                    </tr>
                </thead>
                <tbody>
        `
        if (products_transport.length) {
            $.each(products_transport, function(i, val) {
                var checked = ''
                if (Object.keys(transport_obj_selected).length) {
                    if (transport_obj_selected.key == val.key) {
                        checked = 'checked'
                        transport_obj_selected = val
                    }
                } else if (val.isDefault == 1) {
                    checked = 'checked'
                    transport_obj_selected = val
                }


                html += `
                    <tr>
                        <td title="`+ val.title +`" class="colAct text-center" style="vertical-align: middle;text-align: left;">
                            <input type="radio" id="carrier-`+ i +`"
                                data-carrierid="`+ val.id +`"
                                data-serviceid="`+val.ServiceId+`"
                                data-totalfee="`+ val.totalFee +`"
                                data-key="`+ val.key +`"
                                class="carrier-radio" name="shipping_carrier_id"
                                value="`+ val.id +`"
                                `+ checked +`>
                        </td>
                        <td>
                            <label for="carrier-`+ i +`"><img title="`+val.title+`" alt="`+ val.title +`" style="max-width: 200px;" src="`+ val.logo +`"></label>
                        </td>
                        <td>
                            <div data-service-id="`+val.ServiceId+`" class="serviceDes" title="`+ val.ServiceName +`">`+ val.ServiceName +`</div>
                        </td>
                        <td>
                            <div class="description">`+ val.expected_time +`</div>
                        </td>
                        <td class="text-right">
                            <span class="shipFee" data-calculate-fee="`+ val.calculatedFee +`">`+ val.calculatedFee.numberFormat() +`</span>
                        </td>
                        <td class="text-right">
                            <span class="codFee" data-codfee="`+ val.CoDFee +`">`+ val.CoDFee.numberFormat() +`</span>
                        </td>
                        <td class="text-right">
                            <span class="totalFee" data-setting="0" data-totalfee="`+ val.totalFee +`">`+ val.totalFee.numberFormat() +`</span>
                        </td>
                    </tr>
                `
            })

            html += `<input type="hidden" name="ServiceId" id="ServiceId" value="`+ transport_obj_selected.ServiceId +`"  />`
        } else {
            html += `<tr><td colspan="7" class="text-center">Không có dữ liệu !</td></tr>`
        }

        html += `
            </tbody>
        </table>
        `
        html += `
            <div class="panel-footer">
                <p style="color: red">Lưu ý: Phí vận chuyển chỉ là tạm tính.</p>
            </div>
        `

        $('#box-shipping-area .panel-carrier .panel-body').html(html)
        var carrier_id_checked = $('.carrier-radio:checked').val();
        build_account_transport(carrier_id_checked)
    }

    function build_account_transport(carrier_id_checked) {
        $('#box-shipping-option').empty()
        var box_shipping_options_html = "";
        if (typeof shipping_options == 'object') {
            var shipping_options_html = ""
            var shipping_options_viettel_html = ""
            var j = 0;
            $.each(shipping_options, function (i, val) {
                var checked = ""; var shipping_option_id_current = "";
                if (val.carrier_id == carrier_id_checked) {
                    if (account_transport_selected !== "") {
                        if (account_transport_selected == val.id) {
                            checked = 'checked'
                            shipping_option_id_current = val.id
                        }
                    } else if (val.is_default == 1) {
                        checked = 'checked'
                        shipping_option_id_current = val.id
                    }

                    shipping_options_html += `
                        <div class="radio">
                            <label>
                                <input name="shipping_option_id" type="radio" ${checked}
                                    id="shipping-option-${val.id}" class="shipping-option-radio"
                                    value="${val.id}" data-type="${val.carrier_id}" data-token="${val.token}"
                                /> ${val.name} (ClientID: ${val.client_id}, Token: ${val.token})
                            </label>
                        </div>
                    `;

                    j++
                }
            })

            if (shipping_options_html != "") {
                let box_shipping_options_html = `
                    <div class="panel panel-default" style="word-break: break-word">
                        <div class="panel-heading">Chọn tài khoản nhà vận chuyển</div>
                        <div class="panel-body">
                            `+ shipping_options_html + `
                            <div>Click vào <a href="/?page=shipping-option" target="_blank">đây</a> để thêm tài khoản nhà vận chuyển.</div>
                        </div>
                    </div>
                `

                $('#box-shipping-option').html(box_shipping_options_html)
            }


        }

        return box_shipping_options_html
    }

    function update_transport_price() {
        var shipping_price = 0
        if (Object.keys(transport_obj_selected).length) {
            shipping_price = parseInt(transport_obj_selected.totalFee)
        }

        $('#shipping_price').val(shipping_price.numberFormat())
        updatePrice()
    }

    var address_obj = {}

    function check_valid_transport() {
        var flag = true
        var return_arr = []
        if (accept_edit_transport != 'accept') {
            return_arr.flag = false

            return return_arr
        }

        var city = $('#city').val();
        if(!city){
            $('#city').focus();
            return_arr.focus = '#city';
            alert('Bạn vui lòng chọn tỉnh thành!');

            return_arr.flag = false;

            return return_arr
        }

        var mobile = $('#mobile').val();
        if(!mobile){
            $('#mobile').focus();
            return_arr.focus = '#mobile';
            alert('Bạn vui lòng nhập số điện thoại chính!');

            return_arr.flag = false;

            return return_arr
        }

        if(isNaN(mobile) == true){
            $('#mobile').focus();
            return_arr.focus = '#mobile';
            alert('Số điện thoại chính phải là số');

            return_arr.flag = false;

            return return_arr
        }

        if(!$('#customer_name').val()){
            $('#customer_name').focus();
            return_arr.focus = '#customer_name';
            alert('Bạn vui lòng nhập tên khách hàng!');

            return_arr.flag = false;

            return return_arr
        }

        if(!$('#address').val()){
            $('#address').focus();
            return_arr.focus = '#address';
            alert('Bạn vui lòng nhập địa chỉ khách hàng!');

            return_arr.flag = false;

            return return_arr
        }

        if (!$('.radio_shipping_address').length) {
            alert('Bạn chưa nhập địa chỉ lấy hàng!')
            $('.panel-shipping').removeClass('hidden')
            $('html, body').animate({
                scrollTop: $('.panel-shipping').offset().top + 10
            }, 1000)

            return_arr.flag = false;

            return return_arr.flag
        } else {
            $('.radio_shipping_address').each(function() {
                if ($(this).is(':checked')) {
                    address_obj.district_id = $(this).data('district-id')
                    address_obj.district_name = $(this).data('district-name')
                    address_obj.province_id = $(this).data('province-id')
                    address_obj.province_name = $(this).data('province-name')
                }
            })
        }

        var to_district_id = parseInt($('#district .input-id').val())
        if (isNaN(to_district_id)) {
            alert("Bạn chưa chọn tỉnh/thành phố ?")
            $('#selectZone').css('border', '1px solid red')

            return_arr.flag = false

            return return_arr.flag
        } else if (!$('#district .input-name').val()) {
            alert("Bạn chưa chọn tỉnh/thành phố ?");
            $('#selectZone').css('border', '1px solid red');

            return_arr.flag = false

            return return_arr.flag
        } else {
            address_obj.to_district_id = to_district_id
            address_obj.to_district_name = $('#district .input-name').val()
            address_obj.to_province_id = parseInt($('#city-drd .input-id').val())
            address_obj.to_province_name = $('#city-drd .input-name').val()
        }

        if (input_count < 101) {
            alert('Bạn vui lòng nhập sản phẩm');

            return_arr.flag = false;

            return return_arr
        } else {
            for (var i = 101; i <= input_count; i++) {
                if (getId('product_id_'+i) !== null && !getId('product_id_'+i).value) {
                    alert('Bạn vui lòng chọn sản phẩm');
                    $('#product_id_'+i).focus();
                    return_arr.focus = '#product_id_'+i;

                    return_arr.flag = false;

                    return return_arr
                }
            }
        }

        var total_weight = to_numeric($('#total_weight').val())
        if (total_weight <= 0) {
            total_weight = get_total_weight_product()
            if (total_weight == 0) {
                alert('Bạn vui lòng nhập tổng trọng lượng đơn hàng!')
                $('.panel-product .product-weight').focus();
                return_arr.focus = '.panel-product .product-weight';
                return_arr.flag = false;

                return return_arr
            } else {
                $('#total_weight').val(total_weight)
            }
        }

        var pick_option = $('#pick_option').val()

        return_arr.flag = flag;
        return_arr.pick_option = pick_option
        return_arr.address_obj = address_obj

        var thanh_tien = parseInt($('#price').val().replace(/,/g, ''))
        var tong_tien = parseInt($('#total_price').val().replace(/,/g, ''))

        <?php if(AdminOrders::$add_deliver_order == 1){?>
        var phi_van_chuyen = parseInt($('#shipping_price').val().replace(/,/g, ''));
        if (isNaN(phi_van_chuyen)) {
            phi_van_chuyen = 0;
        }
        <?php }else{?>
        var phi_van_chuyen = 0;
        <?php }?>


        tong_tien -= phi_van_chuyen
        return_arr.thanh_tien = thanh_tien
        return_arr.tong_tien = tong_tien

        return return_arr
    }

    function get_total_weight_product() {
        var total_weight = 0
        for (var i = 101; i<= input_count; i++) {
            if (getId('product_id_'+i) !== null && getId('product_id_'+i).value) {
                if ($('#id_'+i)) {
                    if (to_numeric($('#weight_'+i).val()) > 0) {
                        var quantity = parseInt($('#qty_' + i).val())
                        if (isNaN(quantity)) {
                            quantity = 1
                            $('#qty_' + i).val(1)
                        }

                        total_weight += (to_numeric($('#weight_'+i).val()) * quantity)
                    }
                }
            }
        }

        return total_weight
    }

    function updateTotalWeight() {
        if ($('#total_weight').length) {
            total_weight = get_total_weight_product()
            $('#total_weight').val(total_weight)
        }
    }

    function update_shipping_modal() {
        if ($('#total_weight').length) {
            total_weight = get_total_weight_product()
            $('#total_weight').val(total_weight)
        }

        status_current = $('input.order-status:checked').val()
        if (status_current != 8) {
            return;
        }

        var valid_transport = check_valid_transport()
        if (!valid_transport.flag) {
            return;
        }

        $('#loading').show()
        var pick_option = valid_transport.pick_option
        var from_district_id = valid_transport.address_obj.district_id
        var from_district_name = valid_transport.address_obj.district_name
        var to_district_id = valid_transport.address_obj.to_district_id
        var to_district_name = valid_transport.address_obj.to_district_name
        var from_province_name = valid_transport.address_obj.province_name
        var to_province_name = valid_transport.address_obj.to_province_name
        var thanh_tien = valid_transport.thanh_tien
        var tong_tien = valid_transport.tong_tien
        var shipping_option = ""
        var Length = $('#total_length').val()
        var Width = $('#total_width').val()
        var Height = $('#total_height').val()
        if ($('.shipping-option-radio:checked').val() != "undefined") {
            shipping_option = $('.shipping-option-radio:checked').val()
        }

        var transport_ghtk = 'road';
        if ($('#transport_ghtk').val()) {
            transport_ghtk = $('#transport_ghtk').val();
        }

        $.ajax({
            type: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'show_modal_shipping',
                'Weight': total_weight,
                'FromDistrictID' : from_district_id,
                'ToDistrictID': to_district_id,
                'FromDistrictName' : from_district_name,
                'ToDistrictName' : to_district_name,
                'ToProvinceName': to_province_name,
                'FromProvinceName': from_province_name,
                'pick_option' : pick_option,
                'shipping_option' : shipping_option,
                "Length": Length,
                "Width": Width,
                "Height": Height,
                "tong_tien": tong_tien,
                "thanh_tien": thanh_tien,
                "transport_ghtk" : transport_ghtk
            },
            dataType: 'json',
            success: function(data) {
                if (data.success === true) {
                    products_transport = data.data
                    build_transport_html()

                    update_transport_price()
                }
            },
            complete: function() {
                $('#loading').hide()
            }
        });
    }

    function update_status_modal_active() {
        $('.order-status').each(function() {
            if ($(this).is(":checked")) {
                $('.step-ward-arrow').removeClass('active')
                $(this).closest('.step-ward-box').siblings('.step-ward-arrow').addClass('active')
                var text_cancel_button = 'Hủy đơn hàng';
                if ($(this).val() == 9) {
                    text_cancel_button = 'Đã hủy đơn hàng';
                }

                $('#btn-cancel').html(text_cancel_button)
            }
        })
    }

    function enableProductSelect2(index){
        $('.js-basic-multiple_'+index).select2({
            ajax: {
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_select2_product',
                data: function (params) {
                    let q = {
                        term: params.term,
                        page: params.page
                    }
                    return q;
                },
                dataType: 'json',
                delay: 250,
                allowClear: true,
                cache: true,
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, ex to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 20) < data.total_count
                        }
                    };
                },
            },
                placeholder: 'Tìm sản phẩm hàng hóa',
                minimumInputLength: 0,
                templateResult: function (p) {
                    return `${p.text}`;
                },
                templateSelection: function (p) {
                    return p.text;
                },
                escapeMarkup: function (m) {
                    return m;
                }
        });
    }

    $(document).ready(function(){
        $('#btn-change-status').click(function() {
            setTimeout(function() {
                $('#order-status-row').animate({
                    scrollTop: $('#order-status-row')[0].scrollHeight - $('#order-status-row')[0].clientHeight
                }, 6000)
            }, 4000)
        })
        
        if(flagDateDisableAdd == 0){
            $('.product_price').attr('readonly',true);
        }

        if(flagDateDisableEdit == 0){
            $('.product_price').attr('readonly',true);
        }

        <?php  if(Url::get('cmd') != 'add' && !AdminOrdersDB::canEditPhoneNumber()){?>
            $('#mobile').attr('readonly',true);
        <?php }?>
        $('#birth_date').inputmask('dd/mm/yyyy', { 'placeholder': '__/__/____' });
        $('.select2').select2({
            dropdownAutoWidth : true
        });
        $('.datetimepicker_pickdate').datetimepicker({
            format: 'DD/MM/YYYY',
        });
        $('.appointed_time_display').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
        });
        var pi = 1;
        for(let i=101;i<=input_count;i++){
            if(getId('product_index_'+i)){
                enableProductSelect2(i);
                $('#product_index_'+i).html(pi);
                pi++;
            }
            if(getId('id_'+i) && getId('appointed_time_'+i)){
                $('#appointed_time_display_'+i).on('dp.change', function (e) {
                    $('#appointed_time_'+i).val(e.date.unix());
                });
                if (ownerGroup == 1 || adminGroup == 1 || customer == 1) {
                    $('#input_group_'+i).css({'border':'1px solid #999'});
                } else {
                    if(getId('can_del_'+i) && getId('can_del_'+i).value != 1){
                        $('#input_group_'+i).css({'background-color':'#CCC'});
                        $('#mask_'+i).show();
                        $('#input_group_'+i).click(function(){
                            alert('Lịch của tài khoản khác tạo');
                        });
                    }else{
                        $('#input_group_'+i).css({'border':'1px solid #999'});
                    }
                }
            }
        }
        enableProductSelect2(input_count);
        //scroll to top
        $('.scrollToTop').hide();
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scrollToTop').fadeIn(300);
            }
            else {
                $('.scrollToTop').fadeOut(300);
            }
        });

        $('.scrollToTop').click(function () {
            $('html, body').animate({scrollTop: 0}, 1000);
            return false;
        });
        // Viettel Post - Load List Inventory (DS Kho)
        /* $(document).on('change', '.shipping-option-radio', function () {
            var type_shipping = $(this).data('type')
            if (type_shipping == "api_viettel_post") {
                var token = $(this).data('token')
                $('#loading').show()
                $.ajax({
                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                    data: {
                        cmd: "get-list-inventory",
                        token: token
                    },
                    success: function(data) {
                        console.log(data)
                    },
                    complete: function() {
                        $('#loading').hide()
                    }
                })
            }
        }) */

        $(document).on('change', '.carrier-radio', function() {
            // $('.shipping-option-radio').trigger('change')
        })

        if(cmd == 'edit'){
            if ($('#bundle_id').val() != 0) {
                $('#bundle_id').attr('disabled',true);

            }//end if
            if (isAccountTestValidatePhone && !allow_update_mobile) {
                $('#mobile').attr('readonly',true);
                $('#mobiletype').attr('disabled',true);
            }//end if
        }
    
        <!--IF:status_cond(![[=can_edit_status=]])-->
        $('#postal_code').attr('readOnly',true);
        $('#address').attr('readOnly',true);
        $('#city').attr('readOnly',true);
        $('#shipping_price').attr('readOnly',true);
        $('#discount_price').attr('readOnly',true);
        $('#other_price').attr('readOnly',true);
        <!--/IF:status_cond-->
        update_status_modal_active()

        $(document).on('change', '.shipping-option-radio', function () {
            account_transport_selected = $(this).val()
            update_shipping_modal()
        })

        

        var status_current = $('input.order-status:checked').val();

        $('#btn-cancel').click(function() {

            $.confirm({
                title: 'Hủy đơn hàng!',
                content: 'Bạn có chắc chắn muốn hủy đơn hàng này?',
                buttons: {
                    confirm: function(){
                        $('#status_9').prop('checked', true)
                        status_current = 9
                        let stt_color_custom = $('#status_9').data('color')
                        let stt_name_custom = $('#status_9').data('name')
                        $('#stt-color-custom').css('background-color', stt_color_custom)
                        $('#stt-name-custom').text(stt_name_custom)
                        if(cmd == 'edit' && user_delivered){
                            $('.order_not_success').hide();
                        }
                        update_status_modal_active();
                    },
                    cancel: function(){
                        $('#orderStatusModal').modal();
                    }
                }
            });
        })

        $('#btn-close-status').click(function() {
            $('#status_' + status_current).prop('checked', true)
            update_status_modal_active()

            return;
        })

        $('.order-status').click(function() {
            if ($(this).is(":checked")) {
                $('.step-ward-arrow').removeClass('active')
                $(this).closest('.step-ward-box').siblings('.step-ward-arrow').addClass('active')

                var new_status_current = $(this).val()
                if (new_status_current == 8) {
                    if (accept_edit_transport == 'accept') {
                        var valid_transport = check_valid_transport()
                        if (!valid_transport.flag) {
                            $('#status_' + status_current).prop('checked', true)
                            update_status_modal_active()
                            $('#orderStatusModal').modal('hide')
                            if ('focus' in valid_transport) {
                                setTimeout(function() {
                                    $(valid_transport.focus).focus()
                                }, 500)
                            }

                            return;
                        }

                        $('.panel-shipping').removeClass('hidden')

                        $('#loading').show()
                        var from_district_id = valid_transport.address_obj.district_id
                        var from_district_name = valid_transport.address_obj.district_name
                        var to_district_id = valid_transport.address_obj.to_district_id
                        var to_district_name = valid_transport.address_obj.to_district_name
                        var from_province_name = valid_transport.address_obj.province_name
                        var to_province_name = valid_transport.address_obj.to_province_name
                        var total_weight = parseInt($('#total_weight').val())
                        var pick_option = valid_transport.pick_option
                        var thanh_tien = valid_transport.thanh_tien
                        var tong_tien = valid_transport.tong_tien
                        var shipping_option = ""
                        var Length = $('#total_length').val()
                        var Width = $('#total_width').val()
                        var Height = $('#total_height').val()
                        if ($('.shipping-option-radio:checked').val() != "undefined") {
                            shipping_option = $('.shipping-option-radio:checked').val()
                        }

                        var transport_ghtk = 'road';
                        if ($('#transport_ghtk').val()) {
                            transport_ghtk = $('#transport_ghtk').val();
                        }

                        $.ajax({
                            type: "POST",
                            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                            data : {
                                'cmd':'show_modal_shipping',
                                'Weight': total_weight,
                                'FromDistrictID' : from_district_id,
                                'ToDistrictID': to_district_id,
                                'FromDistrictName' : from_district_name,
                                'ToDistrictName' : to_district_name,
                                'ToProvinceName': to_province_name,
                                'FromProvinceName': from_province_name,
                                'pick_option' : pick_option,
                                "shipping_option" : shipping_option,
                                "Length": Length,
                                "Width": Width,
                                "Height": Height,
                                "thanh_tien": thanh_tien,
                                "tong_tien": tong_tien,
                                "transport_ghtk" : transport_ghtk
                            },
                            dataType: 'json',
                            success: function(data) {
                                console.log(data);
                                if (data.success === true) {
                                    products_transport = data.data
                                    build_transport_html()
                                    update_transport_price()

                                }
                            },
                            complete: function() {
                                $('#loading').hide();
                                $("html, body").animate({ scrollTop: $('#panel-shipping').offset().top }, 1000)
                            }
                        });
                    }
                } else {
                    products_transport = []
                    $('.panel-shipping').addClass('hidden')
                }

                status_current = new_status_current
                var stt_color_custom = $('#status_' + new_status_current).data('color')
                var stt_name_custom = $('#status_' + new_status_current).data('name')
                $('#stt-color-custom').css('background-color', stt_color_custom)
                $('#stt-name-custom').text(stt_name_custom)

                $('#orderStatusModal').modal('hide')
            }
        })

        let old_district_name = $('#district input.input-name').val()
        $('#district input.input-name').blur(function() {
            var new_district_name = $(this).val()
            if (status_current == 8 && old_district_name != new_district_name) {
                update_shipping_modal()
            }
        })

        $(document).on('click', '.btn-edit-transport', function(e) {
            e.preventDefault()
            $('#modal-shipping-cost').modal('show')
        });

        $('.radio_shipping_address').change(function() {
            if ($(this).is(':checked')) {
                update_shipping_modal()
            }
        });

        $(document).on('change', '.carrier-radio', function() {
            if ($(this).is(":checked")) {
                var key_current = $(this).data('key')
                $.each(products_transport, function(i, val) {
                    if (val.key == key_current) {
                        transport_obj_selected = val
                        $('#ServiceId').val(transport_obj_selected.ServiceId)
                        update_transport_price()
                    }
                })

                let carrier_id_checked = $(this).val()
                build_account_transport(carrier_id_checked)
            }
        });

        $(document).on('click', '.btn-order-edit-transport', function(e) {
            e.preventDefault()
            update_shipping_modal()
        });

        $(document).on('change', '.product-weight, .product-quantity, #total_length, #total_width, #total_height, #pick_option', function() {
            update_shipping_modal()
        })

        $('#user_created').select2();
        <!--IF:cond1((AdminOrders::$admin_group or User::is_admin() or Url::get('cmd')=='add'))-->

        <!--ELSE-->
        $('#source_id').attr('readonly',true);
        <!--/IF:cond1-->
        <?php if(Url::get('status_id')==5 and !AdminOrders::$admin_group){?>
        $('#mobile').attr('readonly',true);
        <?php }?>
        //////////
        //suggestCustomer();
        $(document).keypress(function(e) {
            try{
                if(e.currentTarget.id.match(/note/)){
                    return;
                }

                if(e.which == 13) {
                    mi_add_new_row('mi_order_product');
                    $('.js-basic-multiple').select2();
                    enableProductSelect2(input_count);
                    $('#productDetail'+input_count).css({'background-color':'#c8fcff'});
                    //suggestProduct(input_count);
                    //$('#product_name_'+input_count).focus();
                    return false;
                }
            }catch(e){}
        });
        $('#lich_hen').datepicker({format:'dd/mm/yyyy',language:'vi'});
        <?php if(Url::get('cmd')=='edit' and Url::get('fb_post_id')){?>
        $('#code').attr('readonly',true);
        <?php }?>
        $('#price').change(function(){
            $('#price').val(numberFormat($('#price').val()));
        });
    });

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
                $('#product_id_'+index).val(ui.item.id);
                $('#product_name_'+index).val(ui.item.name);
                getProduct(index);
                return false;
            }
        });
    }

    function suggestCustomer(){
        $("#mobile").autocomplete({
            source:'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_order_by_keyword',
            minChars: 3,
            width: 400,
            matchContains: true,
            autoFill: true,
            cacheLength:10,
            max:100,
            select: function( event, ui ) {
                $('#customer_name').val(ui.item.cunameer_name);
                $('#mobile').val(ui.item.mobile);
                $('#address').val(ui.item.address);
                $('#city').val(ui.item.city);
                return false;
            }
        });
    }

    function checkDuplicated(phone,obj){
        let type = $('#type').val();
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'check_duplicated',
                'phone':phone,
                'order_id': <?php echo Url::iget('id')?Url::iget('id'):0; ?>
            },
            dataType: 'json',
            beforeSend: function(){

            },
            success: function(content){
                if(content.result == '1' && (type==0 || type == 1)){
                    alert('Số điện thoại đã tồn tại. Bạn vui lòng không chọn đơn SALE (số mới)!');
                    $('#type').focus();
                }else{
                    $('#save').val(1);
                    $(obj).html('Đang cập nhật...');
                    $(obj).attr('disabled',true);
                    EditAdminOrdersForm.submit();
                }
            }
        });
    }

    function SaveOrder(obj){
        if(isEdited == 1)
        {
            alert("Bạn chỉ được xem đơn hàng này");
            return false;
        }
        if(!$('#bundle_id').val() || $('#bundle_id').val() == 0){
            $('#bundle_id').focus();
            alert('Bạn vui lòng nhập phân loại sản phẩm!');
            return false;
        }

        let elMobile = $('#mobile');
        let elMobile2 = $('#mobile2');
        let mobile = elMobile.val();
        let mobile2 = elMobile2.val();
        let res_validate = false;
        if (isAccountTestValidatePhone) {
            res_validate = handleValidateNumber(elMobile2, true);
            if(!res_validate){
                return false;
            }//end if

            if (cmd == 'add' || allow_update_mobile) {
                res_validate = handleValidateNumber(elMobile);
            }//end if

            if(!res_validate) {
                return false;
            }
        }//end if

        if(!mobile){
            $('#mobile').focus();
            alert('Bạn vui lòng nhập số điện thoại chính!');
            return false;
        }

        if(isNaN(mobile) == true){
            $('#mobile').focus();
            alert('Số điện thoại chính phải là số');
            return false;
        }

        if(isNaN(mobile2) == true){
            $('#mobile2').focus();
            alert('Số điện thoại phụ phải là số');
            return false;
        }

        if($('#saved_date').val()) {
            let saved_date = $('#saved_date').val();
            saved_date = saved_date.split("/");
            saved_date = new Date(+saved_date[2], saved_date[1] - 1, +saved_date[0]);
            let minDate = new Date(1, 1, 2000);
            if (saved_date instanceof Date === false || minDate.getTime() > saved_date.getTime()) {
                alert('Ngày lưu đơn không đúng');
                return false;
            }
        }
        if($('#deliver_date').val()) {
            let deliver_date = $('#deliver_date').val();
            deliver_date = deliver_date.split("/");
            deliver_date = new Date(+deliver_date[2], deliver_date[1] - 1, +deliver_date[0]);
            let minDate = new Date(1, 1, 2000);
            if (deliver_date instanceof Date === false || minDate.getTime() > deliver_date.getTime()) {
                alert('Ngày hẹn giao hàng không đúng');
                return false;
            }
        }

        let status_current = $('input.order-status:checked').val();
        if(cmd == 'edit' && !user_delivered && status_current != 8 && statusIdsLv3.includes(status_current)){
            alert('Các đơn hàng phải qua trạng thái Chuyển hàng mới chuyển được lên trạng thái lớn hơn');
            return false;
        }
        if(cmd == 'add' && status_current == 7 && flagDateAdd == 0){
            if(!$('#city_id').val()){
                alert('Bạn vui lòng nhập tỉnh thành.');
                return false;
            }
        }
        if(cmd == 'edit' && flagDateEdit == 0){
            if(user_confirmed != 0 ){
                if(!$('#city_id').val()){
                    alert('Bạn vui lòng nhập tỉnh thành.');
                    return false;
                }
            }
            if(user_confirmed == 0 &&  status_current == 7){
                if(!$('#city_id').val()){
                    alert('Bạn vui lòng nhập tỉnh thành.');
                    return false;
                }
            }
        }
        if(cmd == 'add' && status_current == 3){
            alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Đóng hàng');
            obj.removeAttribute('disabled');
            return false;
        }
        if(status_current == 12){
            if(cmd == 'add'){
                alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Xác nhận khách không nghe máy');
                obj.removeAttribute('disabled');
                return false;
            } else if (cmd == 'edit' && isConfirm == 0){
                alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Xác nhận khách không nghe máy');
                obj.removeAttribute('disabled');
                return false;
            }
            
        }
        if(status_current == 13){
            if(cmd == 'add'){
                alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Xác nhận khách phân vân');
                obj.removeAttribute('disabled');
                return false;
            } else if (cmd == 'edit' && isConfirm == 0){
                alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Xác nhận khách phân vân');
                obj.removeAttribute('disabled');
                return false;
            }
            
        }
        if(cmd == 'edit' && status_current == 3 && isConfirm == 0){
            alert('Đơn hàng phải trải qua trạng thái Xác nhận - Chốt đơn mới được lên trạng thái Đóng hàng');
            obj.removeAttribute('disabled');
            return false;
        }
        if (status_current == 7) {//xac nhan
            if($('#type').val()==0){
                $('#type').focus();
                $.notify('Bạn vui lòng nhập phân loại đơn hàng ',{type:'warning'});
                return false;
            }
            if(!$('#gender').val() || $('#gender').val() == '0'){
                alert('Bạn vui lòng nhập giới tính');
                $('#gender').focus();
                return false;
            }
            <!--IF:require_address_cond([[=require_address=]])-->
            if(!$('#city_id').val()){
                alert('Bạn vui lòng nhập tỉnh thành.');
                $('#ZoneModal').modal();
                return false;
            }
            if(!$('#district_id').val()){
                alert('Bạn vui lòng nhập quận huyện.');
                $('#ZoneModal').modal();
                return false;
            }
            if(!$('#ward_id').val()){
                alert('Bạn vui lòng nhập phường xã.');
                $('#ZoneModal').modal();
                return false;
            }
            <!--/IF:require_address_cond-->
            if(checkHasProduct()==false){
                $.notify('Bạn vui lòng nhập sản phẩm hàng hóa!',{type:'warning'});
                return false;
            }
        }
        let total_price = to_numeric($('#total_price').val());
        let txtPrepaid = to_numeric($('#txtPrepaid').val());
        if(txtPrepaid > total_price){
            $.notify('Tổng tiền trả trước đang lớn hơn tổng tiền phải trả. Vui lòng kiểm tra lại.',{type:'warning'});
            obj.removeAttribute('disabled');
            return false;
        }
        if (status_current == 8) {//chuyen hang
            if(accept_edit_transport == 'accept'){
              if (1==2) {
                var valid_transport = check_valid_transport()
                if (!valid_transport.flag) {
                  return;
                } else {
                  var carrier_radio = $('.carrier-radio:checked').val();
                  if (!carrier_radio) {
                    alert('Bạn vui lòng chọn hãng vận chuyển ?');
                    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
                    return;
                  }

                  if (!$('.shipping-option-radio:checked').val()) {
                    alert('Bạn vui lòng chọn tài khoản nhà vận chuyển ?');
                    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
                    return;
                  }
                  if (carrier_radio != 'api_bdhn' && carrier_radio != 'api_ghtk') {
                    var total_width = parseInt($('#total_width').val())
                    var total_height = parseInt($('#total_height').val())
                    var total_length = parseInt($('#total_length').val())
                    if (isNaN(total_width)) {
                      alert('Bạn chưa nhập chiều rộng của gói hàng!')
                      $('#total_width').focus();
                      return;
                    }

                    if (isNaN(total_height)) {
                      alert('Bạn chưa nhập chiều cao của gói hàng!')
                      $('#total_h Emaileight').focus();
                      return;
                    }

                    if (isNaN(total_length)) {
                      alert('Bạn chưa nhập chiều dài của gói hàng!')
                      $('#total_length').focus();
                      return;
                    }
                  }
                }
              }
            }else{
                <!--IF:require_address_cond([[=require_address=]])-->
                if(!$('#city_id').val()){
                    alert('Bạn vui lòng nhập tỉnh thành.');
                    $('#ZoneModal').modal();
                    return false;
                }
                if(!$('#district_id').val()){
                    alert('Bạn vui lòng nhập quận huyện.');
                    $('#ZoneModal').modal();
                    return false;
                }
                if(!$('#ward_id').val()){
                    alert('Bạn vui lòng nhập phường xã.');
                    $('#ZoneModal').modal();
                    return false;
                }
                <!--/IF:require_address_cond-->
                if(checkHasProduct()==false){
                    $.notify('Bạn vui lòng nhập sản phẩm hàng hóa!',{type:'warning'});
                    return false;
                }
            }

        }
        <!--IF:cond([[=no_create_order_when_duplicated=]]==1 and Url::get('cmd')=='add')-->
        checkDuplicated(mobile,obj);
        <!--ELSE-->
        $('#save').val(1);
        $(obj).html('Đang cập nhật...');
        $(obj).attr('disabled',true);
        EditAdminOrdersForm.submit();
        <!--/IF:cond-->
    }

    function checkHasProduct(){
        $has = false;
        for(let i=101;i<=input_count;i++){
            if(getId('product_id_'+i) && getId('product_id_'+i).value>0 && getId('qty_'+i) && getId('qty_'+i).value > 0){
                $has = true;
                break;
            }
        }
        return $has;
    }
    function updatePrice(){
        let total_product_price = 0;
        $('.product_total').each(function(){
            if ($(this).val() != '') {
                total_product_price = total_product_price + to_numeric($(this).val());
            }
        })
        $('#price').val(numberFormat(total_product_price));
        let price = to_numeric(total_product_price);
        let discount_price = $('#discount_price').val()?to_numeric($('#discount_price').val()):0;
        let other_price = $('#other_price').val()?to_numeric($('#other_price').val()):0;
        <?php if(AdminOrders::$add_deliver_order == 1){?>
        let shipping_price = $('#shipping_price').val()?to_numeric($('#shipping_price').val()):0;
        <?php }else{?>
        let shipping_price = 0;
        <?php }?>
        let total1 = price - discount_price + shipping_price;
        total1 += other_price;
        total1a = numberFormat(total1);
        $('#total_price').val(total1a);
        let txtPrepaid = $('#txtPrepaid').val()?to_numeric($('#txtPrepaid').val()):0;
        if(txtPrepaid > total1){
            alert("Tổng tiền trả trước đang lớn hơn tổng tiền phải trả. Vui lòng kiểm tra lại.");
        }
        $('#txtPrepaidRemain').val(numberFormat(total1 - txtPrepaid));
    }


    $(document).on('change','.product-quantity', productPropChanged);
    $(document).on('change','.discount_amount', productPropChanged);

    $(document).on('keypress','.discount_amount, .discount_remain, .prepaid-amount, #insurance_value',function(e){
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    $("#mi_order_product_all_elems").on('click', '.btn-discount-price',function(e){
        var _this = $(this);
        if(!_this.hasClass('active')){
            _this.find('input').attr('checked', true);
            _this.siblings().removeClass("active").find('input').removeAttr('checked');
            let amount = _this.closest('table').find('.discount_amount')
            let remain = _this.closest('table').find('.discount_remain');
            let price  = _this.closest('table').find('.product_price');
            amount.toggleClass("hidden");
            remain.toggleClass("hidden");
            remain.val(numberFormat(to_numeric(price.val() ) - to_numeric(amount.val())));
        }
    });

    $(document).on('keyup paste','.discount_remain', function(){
        let _this = $(this);
        let flag = $(this).parents('.product-detail');
        let price = to_numeric(flag.find('.product_price').val());
        let total = 0;
        let quantity = parseFloat(flag.find('.product_quantity').val());
        if( to_numeric(_this.val()) > price){
            alert('Vui lòng nhập giảm giá nhỏ hơn giá bán, giảm còn nhỏ hơn hoặc bằng giá bán.');
            _this.val(numberFormat(price));
            flag.find('.discount_amount').val(0);
            total = to_numeric(price * quantity);
        }else{
            flag.find('.discount_amount').val(numberFormat(price -  to_numeric(_this.val())));
            total = to_numeric(_this.val()) * quantity;
        }

        flag.find('.product_total').val(numberFormat(total));

        (productPropChanged.bind(this))();
    });

    function productPropChanged(){
        let flag = $(this).parents('.product-detail');
        let quantity = parseFloat(flag.find('.product-quantity').val());
        let discount_am = flag.find('.discount_amount');
        let discount_re = flag.find('.discount_remain');

        let discount_amount = to_numeric(discount_am.val());
        let discount_remain = to_numeric(discount_re.val());

        let price = to_numeric(flag.find('.product_price').val());
        let price_hidden = to_numeric(flag.find('.product_price_hidden').val());
        let total = 0;
        if(!quantity){
            quantity = 0;
        }
        if(!price){
            price = 0;
        }
        if(discount_amount > (price) || discount_remain > price){
            alert('Vui lòng nhập giảm giá nhỏ hơn giá bán, giảm còn nhỏ hơn hoặc bằng giá bán.');
            discount_amount = 0;
            discount_remain = price;
        }

        if(price_hidden){
            total = Math.round((price_hidden  - discount_amount) * quantity, 0);
        } else {
            total = Math.round((price  - discount_amount) * quantity, 0);
        }

        discount_re.val(numberFormat(price - discount_amount));
        discount_am.val(numberFormat(discount_amount));
        flag.find('.product_total').val(numberFormat(total));
        updatePrice();
    }

    function updateDiscountPercent(){
        var publicPrice = to_numeric($('#publish_price').val());
        if(publicPrice){
            var price = to_numeric($('#price').val());
            var discount = ((publicPrice - price)/publicPrice)*100;
            $('#discount').val(discount);
        }
    }


    function updateTotalPrice(index){
        let product_price = to_numeric($('#product_price_'+index).val());
        let discount_amount = to_numeric($('#discount_price_'+index).val());
        let qty = to_numeric($('#qty_'+index).val());
        let product_total = to_numeric($('#total_'+index).val());
        let total = (product_price - discount_amount) * qty;
        let onHand = to_numeric($('#onHand_'+index).html());
        let status = to_numeric($('input.order-status:checked').val());
        let checkDiscount = product_price  - discount_amount;
        if (checkDiscount < 0) {
            alert('Vui lòng nhập giảm giá nhỏ hơn hoặc bằng giá bán');
           //location.reload();
            if(cmd == 'add'){
                $('#discount_price_'+index).val('0')
                $('#total_'+index).val(numberFormat(product_price * qty));
                updatePrice();
            }
            if(cmd == 'edit'){
                location.reload();
            }
           return;
        }
        if(disable_negative_export != 0){
            if(qty>onHand){
                if((status==<?=XAC_NHAN?>  || status==<?=CHUYEN_HANG?> || status==<?=THANH_CONG?>)){
                    alert('⚠️ Bạn không được xuất âm');
                    $('#qty_'+index).val(1);
                    // return false;
                }else{
                    alert('⚠️ Số lượng tồn đang nhỏ hơn số lượng xuất.'+"\n"+' Bạn vui lòng kiểm tra lại!');
                    $('#qty_'+index).focus();
                }
            }
        }
        $('#total_'+index).val(numberFormat(total));
        updatePrice();
    }
    function getProduct(index){
        let product_id = $('#select_product_id_'+index).val();
        let old_product_id = $('#product_id_'+index).val();
        let warehouse_id = $('#warehouse_id_'+index).val();
        let selected_product_id = ((product_id && product_id != '-/-Chọn sản phẩm -/-')?product_id:old_product_id);
        if(selected_product_id){
            $.ajax({
                method: "POST",
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                data : {
                    'cmd':'get_product',
                    'product_id':selected_product_id,
                    'warehouse_id':warehouse_id
                },
                beforeSend: function(){
                },
                success: function(content){
                    let jsonData = $.parseJSON(content);
                    if(content=='FALSE'){
                        alert('ID không tồn tại!');
                    }else{
                        let discount_amount = to_numeric($('#discount_price_'+index).val());
                        let qty_check = $('#qty_'+index).val();
                        let product_price_check = jsonData.price?to_numeric(jsonData.price):0;
                        if(discount_amount > product_price_check){
                            alert('Vui lòng nhập giảm giá nhỏ hơn hoặc bằng giá bán');
                             location.reload();
                            return;
                        }
                        $('#product_id_'+index).val(jsonData.id);
                        $('#product_name_'+index).val(jsonData.name);
                        $('#product_price_'+index).val(jsonData.price?numberFormat(jsonData.price):0);
                        $('#remain_price_'+index).val(jsonData.price?numberFormat(jsonData.price):0);

                        $('#product_price_hidden_'+index).val(jsonData.price?numberFormat(jsonData.price):0);
                        $('#size_'+index).val(jsonData.size);
                        $('#color_'+index).val(jsonData.color);
                        $('#onHandWrapper_'+index).show();
                        $('#onHand_'+index).html(jsonData.on_hand);
                        if(to_numeric($('#qty_'+index).val())<=1){
                            $('#qty_'+index).val(1);
                        }
                        let weight_product = 500;
                        if ('weight' in jsonData) {
                            weight_product = parseInt(jsonData.weight);
                            if (isNaN(weight_product)) {
                                weight_product = 500;
                            }
                        }
                        $('#weight_' + index).val(weight_product);
                        $('#weight_' + index).trigger('change');
                        updateTotalPrice(index);
                    }
                },
                error: function(){
                    alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                }
            });
        }else{
            alert('Bạn vui lòng chọn sản phẩm!');
        }
    }
    function openChatWindow(obj,orderId,fbConversationId){
        window.open('https://admin.tuha.vn/Chat/fromOrder?order_id='+orderId+'&conversation_id='+fbConversationId+'&user_id=[[|md5_user_id|]]&act=do_login','chat','width=600,height=500,top=100,left=400');
    }
</script>
