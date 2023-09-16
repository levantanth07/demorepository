<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<!-- Main content -->
<?php
    $paper_sizes = [[=paper_sizes=]];
    $paper_size_default = [[=paper_size_default=]];
?>
<div class="container full">
    <form name="ListAdminOrdersForm" method="post" id="ListAdminOrdersForm" target="_blank">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-dark">
                <li class="breadcrumb-item"><a href="/">QLBH</a></li>
                <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">POS</li>
                <li class="pull-right">
                    <div class="pull-right">
                        
                    </div>
                </li>
            </ol>
        </nav>
        <div class="panel">
            <div id="order-list" class="box box-solid box-default">
                <div class="box-header">
                    <div class="row pn no-padding">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <div class="col-xs-2 no-padding">
                                    <label><i class="glyphicon glyphicon-search"></i> Tìm kiếm [[|group_name|]]</label>
                                </div>
                                <div class="col-xs-10 no-padding form-inline">
                                    <div class="form-group">
                                        <input name="keyword" type="text" id="keyword" class="form-control" style="height: 30px;font-size:14px;border-radius: 3px;margin-right:2px;min-width: 255px;" placeholder="Số điện thoại hoặc mã (Tối thiểu [[|min_search_phone_number|]] số)" onchange="ReloadList(1);">
                                    </div>
                                    <div class="form-group">
                                        <input name="customer_name" type="text" id="customer_name" placeholder="Họ tên khách hàng" onchange="ReloadList(1);" class="form-control"  style="height: 30px;font-size:14px;border-radius: 3px;margin-right:2px;max-width: 135px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right form-inline">
                            <select name="item_per_page" id="item_per_page" class="form-control"  style="height:30px;width: 120px;" onchange="ReloadList(1);"></select>
                            <button class="btn btn-default thu-gon" type="button"><i class="glyphicon glyphicon-minus-sign"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body" style="padding: 10px 0px 10px 0px;">
                    <div class="option-top col-md-12">
                        <div class="col-xs-12 col-md-12 date-option">
                            <div class="col w-50">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="title small">Trạng thái đơn hàng <a href="#" class="small" onclick="jQuery('.status-checkbox').prop('checked',true);ReloadList(1);return false;">Chọn tất cả</a> | <a href="#" class="small" onclick="jQuery('.status-checkbox').prop('checked',false);ReloadList(1);return false;">Bỏ chọn tất cả</a></p>
                                        <div class="status">
                                            <div class="col-xs-4 btn btn-default" style="border-left: 5px solid #66CC66;color:#66CC66; text-shadow: 1px 0px #333;">
                                                <label for="status_10"><input  name="status[]" type="checkbox" id="status_10" class="status-checkbox" style="margin-bottom:2px;font-size:20px;" checked value="10" onchange="ReloadList(1);">Đơn đang xử lý</label>
                                            </div>
                                            <div class="col-xs-4 btn btn-default" style="border-left: 5px solid #996699;color:#996699; text-shadow: 1px 0px #333;">
                                                <label for="status_5"><input  name="status[]" type="checkbox" id="status_5" class="status-checkbox" style="margin-bottom:2px;font-size:20px;" value="5" onchange="ReloadList(1);">Hoàn thành</label>
                                            </div>
                                            <div class="col-xs-4 btn btn-default" style="border-left: 5px solid #CCC;'.' color:#999; text-shadow: 1px 0px #999;">
                                                <label for="status_9"><input  name="status[]" type="checkbox" id="status_9" class="status-checkbox" style="margin-bottom:2px;font-size:20px;" value="9" onchange="ReloadList(1);"> Hủy</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label for="ngay_tao_checkbox" class="no-padding"><input name="ngay_tao_checkbox" type="checkbox" id="ngay_tao_checkbox" onclick="ReloadList(1);" checked> Ngày tạo:</label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class='input-group date' id='datetimepicker_tu_ngay'>
                                                    <input name="ngay_tao_from" type="text" id="ngay_tao_from" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function (){
                                                        $('#datetimepicker_tu_ngay').datetimepicker({
                                                            format: 'DD/MM/YYYY'
                                                        }).on('dp.change', function (ev) {ReloadList(1);});
                                                    });
                                                </script>
                                            </div>
                                            <div class="col-md-6">
                                                <div class='input-group date' id='datetimepicker_den_ngay'>
                                                    <input name="ngay_tao_to" type="text" id="ngay_tao_to" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function () {
                                                        $('#datetimepicker_den_ngay').datetimepicker({
                                                            format: 'DD/MM/YYYY'
                                                        }).on('dp.change', function (ev) {ReloadList(1);});
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label for="ngay_xn_checkbox" class="no-padding"><input name="ngay_xn_checkbox" type="checkbox" id="ngay_xn_checkbox" onclick="$('#ngay_tao_checkbox').prop('checked',!$(this).is(':checked'));$('.status-checkbox').prop('checked',false);ReloadList(1);"> Ngày hoàn thành:</label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class='input-group date' id='datetimepicker_xn_tu_ngay'>
                                                    <input name="ngay_xn_from" type="text" id="ngay_xn_from" class="form-control dxeEditArea" />

                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function () {
                                                        $('#datetimepicker_xn_tu_ngay').datetimepicker({
                                                            format: 'DD/MM/YYYY'
                                                        }).on('dp.change', function (ev) {ReloadList(1);});
                                                    });
                                                </script>
                                            </div>
                                            <div class="col-md-6">
                                                <div class='input-group date' id='datetimepicker_xn_den_ngay'>
                                                    <input name="ngay_xn_to" type="text" id="ngay_xn_to" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <script type="text/javascript">
                                                    $(function () {
                                                        $('#datetimepicker_xn_den_ngay').datetimepicker({
                                                            format: 'DD/MM/YYYY'
                                                        }).on('dp.change', function (ev) {ReloadList(1);});
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" style="padding-top:5px;padding-bottom: 5px;margin-bottom:5px;margin-top:5px;border-bottom: 1px dotted #999;">
                            <div class="col-md-5 no-padding">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <p class="title"><i class="fa fa-phone-squaRe" aria-hidden="true"></i> Nguồn đơn hàng</p>
                                        <div class="ct">
                                            <select name="source_id" id="source_id" class="js-basic-multiple" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <p class="title">Loại đơn</p>
                                        <div class="ct">
                                            <select name="type" id="type" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function() {
                                        $('.js-basic-multiple').select2();
                                    });
                                </script>
                            </div>
                            <div class="col-md-7 no-padding">
                                <div class="ct">
                                    <div class=" col-xs-2 phan-loai" data-toggle="tooltip" title="Mã hàng hóa / sản phẩm">
                                        <span>Mã hàng </span><br>
                                        <div class="form-group">
                                            <input name="product_code" type="text" id="product_code" class="form-control" style="border-radius: 3px; height: 33px;border:1px solid #CCC;" onchange="ReloadList(1);">
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding-r">
                                        <span >Phân loại </span><br>
                                        <div class="form-group">
                                            <select name="bundle_id" id="bundle_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding-r">
                                        <span>Sale</span><br>
                                        <div class="form-group">
                                            <select name="assigned_account_id" id="assigned_account_id" class="form-control" onchange="ReloadList(1);" title="Sale được chia" data-toggle="tooltip"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding-r">
                                        <!--IF:system_cond([[=account_type=]]==3 or Session::get('master_group_id'))-->
                                        <span>Công ty</span><br>
                                        <div class="form-group">
                                            <select name="search_group_id" id="search_group_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                        <!--ELSE-->
                                        <span>Nhân viên</span><br>
                                        <div class="form-group">
                                            <select name="search_account_id" id="search_account_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                        <!--/IF:system_cond-->
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding-r">
                                        <span>Marketing</span><br>
                                        <div class="form-group">
                                            <select name="mkt_account_id" id="mkt_account_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding-r">
                                        <span>Nhóm</span><br>
                                        <div class="form-group">
                                            <select name="account_group_id" id="account_group_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <div class="form-inline">
                                <div class="form-group">
                                    <a href="index062019.php?page=admin_orders&cmd=pos" target="_blank" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Tạo đơn mới</a>
                                </div>
                                <div class="form-group pull-right">
                                    <select name="page_id" id="page_id" class="form-control" onchange="ReloadList(1);" style="max-width: 300px;"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-tabs-custom tab-info">
                <ul class="nav nav-tabs">
                    <li class="hide"><a href="<?php echo Url::build_current(array('cmd'=>'quick_edit'));?>">Chỉnh sửa nhanh</a></li>
                    <li class="active"><a data-toggle="tab">Danh sách đơn hàng <span class="totalOrder badge"></span></a></li>
                    <!--IF:cond([[=time_to_refesh_order=]])-->
                    <li class="pull-right small text-danger">
                        Cập nhật lại đơn hàng sau <span id="CountdownClock"></span> <i class="fa fa-clock-o"></i>
                    </li>
                    <!--/IF:cond-->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="module_<?php echo Module::block_id(); ?>">
                        <!--IF:cond([[=device=]]=='DESKTOP')-->
                        <div style="padding:20px 0px;color:#999;text-align: center;text-shadow: 1px 1px #FFF;font-size:16px;color:#333">Vui lòng nhấn <a href="#" onclick="ReloadList(1);return false;"><strong>Enter</strong></a> để xem danh sách đơn hàng mặc định!</div>
                        <!--ELSE-->
                        <div style="padding:20px 0px;color:#999;text-align: center;text-shadow: 1px 1px #FFF;font-size:16px;color:#000">Vui lòng chạm vào <a href="#" onclick="ReloadList(1);return false;" class="btn btn-warning">ĐÂY</a> để xem danh sách đơn hàng mặc định!</div>
                        <!--/IF:cond-->
                    </div>
                </div>
            </div>
        </div>
        <!--modal ma buu dien-->
        <div class="modal fade" id="ma-buu-dien" tabindex="-1" role="dialog" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Excepturi, ipsum!</h1>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $status_arr_custom = [[=status_arr_custom=]];
        $status_arr_level = [
            0 => 'Level 0',
            1 => 'Level 1',
            2 => 'Level 2',
            3 => 'Level 3',
            4 => 'Level 4',
            5 => 'Level 5'
        ];
    ?>
        <div id="orderStatusModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-xl" style="width: 1000px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Bảng chuyển trạng thái đơn hàng</h4>
                    </div>
                    <div class="modal-body">
                        <div class="status-header text-center">TRẠNG THÁI ĐƠN HÀNG</div>

                        <div class="contain-row">
                            <div class="row row-equal row-no-padding" id="order-status-row">
                                <?php foreach ($status_arr_level as $level => $name_level): ?>
                                    <div class="col-md-2">
                                        <div class="step-ward-arrow"><?= $name_level ?></div>
                                        <div class="step-ward-box">
                                            <?php
                                            if(!empty($status_arr_custom[$level])):
                                                foreach ($status_arr_custom[$level] as $item_status):
                                                    $color_style = !empty($item_status['color']) ? 'style="background: '. $item_status['color'] .'"' : "";
                                                    $hidden = ($item_status['id'] == 9) ? 'hidden' : "";
                                                    ?>
                                                    <div class="step-ward-item <?= $hidden ?>">
                                                        <div class="radio-custom">
                                                            <label class="checkcontainer">
                                                                <input type="radio"
                                                                       id="status_<?= $item_status['id'] ?>"
                                                                       class="order-status"
                                                                       name="order_status"
                                                                       value="<?= $item_status['id'] ?>"
                                                                       data-name="<?= $item_status['name'] ?>"
                                                                       data-color="<?= $item_status['color'] ?>"
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
                                <?php endforeach;?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger float-left" data-dismiss="modal" id="btn-cancel">Hủy đơn hàng</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Modal Change Order Status -->
        <!--IF:cond2(User::is_admin() or Session::get('admin_group'))-->
        <!-- xu ly sap xep cot don hang -->
        <div class="modal modal-default fade in" id="columnOptionModal" tabindex="-1" role="dialog" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title">Tùy chọn cột hiển thị và xuất excel</h3>
                    </div>
                    <div class="modal-body" style="height: 400px;overflow: auto;">
                        <ul class="list-group" id="sortable">
                            <?php $i=1;?>
                            <!--LIST:all_columns-->
                            <!--IF:cond([[=all_columns.selected=]])-->
                            <li class="list-group-item ui-state-default" id="column_<?=$i;?>" lang="[[|all_columns.name|]]:[[|all_columns.id|]]">
                                <i class="fa fa-arrows-v"></i> <i class="fa fa-align-justify"></i> <input  name="orders_column[]" type="checkbox" id="column_[[|all_columns.id|]]" onclick="if(this.checked){$('#column_<?=$i;?>').attr('lang','[[|all_columns.name|]]:[[|all_columns.id|]]')}else{$('#column_<?=$i;?>').attr('lang','')} activeOrderColumns();" value="[[|all_columns.name|]]:[[|all_columns.id|]]" <?php echo [[=all_columns.selected=]]?'checked':''; ?>> [[|all_columns.name|]]
                            </li>
                            <!--ELSE-->
                            <li class="list-group-item" id="column_<?=$i;?>" lang="">
                                <i class="fa fa-arrows-v"></i> <i class="fa fa-align-justify"></i> <input  name="orders_column[]" type="checkbox" id="column_[[|all_columns.id|]]" onclick="if(this.checked){$('#column_<?=$i;?>').attr('lang','[[|all_columns.name|]]:[[|all_columns.id|]]')}else{$('#column_<?=$i;?>').attr('lang','')} activeOrderColumns();" value="[[|all_columns.name|]]:[[|all_columns.id|]]" <?php echo [[=all_columns.selected=]]?'checked':''; ?>> [[|all_columns.name|]]
                            </li>
                            <!--/IF:cond-->
                            <?php $i++;?>
                            <!--/LIST:all_columns-->
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-left">[[|last_edited_info|]]</div>
                        <div class="pull-right text-warning">
                            Kéo thả để đổi vị trí cột
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/IF:cond2-->
        <input  name="checked_all_orders" type="hidden" id="checked_all_orders" value="1">
        <input  name="checked_order" type="hidden" id="checked_order" value="">
        <input  name="act" type="hidden" id="act" value="">
        <input  name="order_by" type="hidden" id="order_by" value="">
        <input  name="order_by_dir" type="hidden" id="order_by_dir" value="ASC">
    </form>
</div>
<div class="hidden">
    <form action="index062019.php?page=admin_orders&cmd=multi-shipping" id="new-transport-form" method="POST">
        <input type="hidden" name="new_ids" id="new_ids" value="">
    </form>
</div>
<div id="chatModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" style="width: 1000px;height: 600px;overflow: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Hội thoại của Khách hàng</h4>
            </div>
            <div class="modal-body" id="chatBodyWrapper">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="exportExcelModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-success">
                    Xuất excel
                </h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <button type="button" onclick="exportExcel('ALL');" class="btn btn-warning"><i class="fa fa-download"></i> Xuất tất cả</button>
                    <button type="button" onclick="exportExcel('SELECTED');" class="btn btn-success"><i class="fa fa-download"></i> Xuất theo đơn được chọn <span class="totalOrder badge"></span></button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<link rel="stylesheet" href="/packages/vissale/modules/AdminOrders/css/common.css"> <!-- box doi Trang thai -->
<link rel="stylesheet" href="assets/vissale/css/jquery-confirm.css">
<script src="assets/vissale/js/jquery-confirm.js"></script>
<script src="assets/vissale/js/jquery.countdown.min.js"></script>
<!-- tham khao: https://craftpip.github.io/jquery-confirm/ -->
<script>
    var timeToRefreshOrder = parseInt([[|time_to_refesh_order|]]);
    var blockId = <?php echo Module::block_id(); ?>;
    var accept_edit_transport = '<?= [[=accept_edit_transport=]] ?>';
    <!--IF:cond(Url::get('keyword'))-->
    $('.status-checkbox').prop('checked',false);
    $('#ngay_tao_checkbox').attr('checked',false);
    $('#keyword').val('<?php echo Url::get('keyword')?>');
    ReloadList(1);
    <!--/IF:cond-->
    if(timeToRefreshOrder>0){
        let now = new Date();
        now = now.setHours(now.getHours(),now.getMinutes()+timeToRefreshOrder);
        updateCountdown(now);
        setInterval("ReloadList(1);", 1000*60*timeToRefreshOrder);// 5 phut tai lai mot lan
    }
    $(document).ready(function(){
        $('.order-status').click(function() {
            if ($(this).is(":checked")) {
                $('.step-ward-arrow').removeClass('active')
                $(this).closest('.step-ward-box').siblings('.step-ward-arrow').addClass('active');
                $('#orderStatusModal').modal('hide');
            }
        });

        $('#orderStatusModal').on('hidden.bs.modal', function (e) {
            $('.order-status').prop('checked', false)
        });
        $('#btn-change-status').click(function() {
            setTimeout(function() {
                $('#order-status-row').animate({
                    scrollTop: $('#order-status-row')[0].scrollHeight - $('#order-status-row')[0].clientHeight
                }, 6000)
            }, 4000)
        });

        $('#orderListTable').DataTable( {
            fixedHeader: true,
            paging: false,
            scrollY: 300,
            "searching": false
        });

        $(document).keypress(function(e) {
            if(e.which == 13) {
                if (event.target.classList.contains('allow-enter')) {
                    return;
                }
                ReloadList(1);
                return false;
            }
        });
        /*jQuery('#autoAssignOrder').on('click', function() {
            var $this = jQuery(this);
            $this.val('Vui lòng đợi.. .');
            setTimeout(function() {
                $this.val('reset');
            }, 8000);
        });*/
        jQuery('#item-list .bor').css({'height':'450px'});
        $('.thu-gon').click(function () {
            if($("#order-list").css('height')=='100px'){
                $("#order-list").css({'height':'auto'});
                $(this).html('<i class="glyphicon glyphicon-minus-sign"></i>');
            }else{
                $("#order-list").css({'height':'100px','overflow':'hidden'});
                $(this).html('<i class="glyphicon glyphicon-plus-sign"></i>');
            }
        });
    });
    function getOrderDataUrl(){
        var myData = {
            'load_ajax':'1',
            'keyword':jQuery('#keyword').val(),
            'c_n':jQuery('#customer_name').val(),
            'page_id':jQuery('#page_id').val()?jQuery('#page_id').val():'',
            'source_id':$('#source_id').val()?$('#source_id').val():'',
            'type':$('#type').val()?$('#type').val():'',
            'bundle_id':jQuery('#bundle_id').val()?jQuery('#bundle_id').val():'',
            'product_code':jQuery('#product_code').val()?jQuery('#product_code').val():'',
            'search_account_id':jQuery('#search_account_id').val()?jQuery('#search_account_id').val():'',
            'mkt_account_id':jQuery('#mkt_account_id').val()?jQuery('#mkt_account_id').val():'',
            'assigned_account_id':jQuery('#assigned_account_id').val()?jQuery('#assigned_account_id').val():'',
            'account_group_id':jQuery('#account_group_id').val()?jQuery('#account_group_id').val():'',
            'search_group_id':jQuery('#search_group_id').val()?jQuery('#search_group_id').val():'',
            'item_per_page':jQuery('#item_per_page').val()?jQuery('#item_per_page').val():'',
            block_id:blockId
        };

        if(jQuery('#ngay_tao_checkbox').is(':checked')==true){
            myData['ngay_tao_to'] = jQuery('#ngay_tao_to').val();
            myData['ngay_tao_from'] = jQuery('#ngay_tao_from').val();
        }
        if(jQuery('#ngay_chia_checkbox').is(':checked')==true){
            myData['ngay_chia_to'] = jQuery('#ngay_chia_to').val();
            myData['ngay_chia_from'] = jQuery('#ngay_chia_from').val();
        }
        if(jQuery('#ngay_xn_checkbox').is(':checked')==true){
            myData['ngay_xn_from'] = jQuery('#ngay_xn_from').val();
            myData['ngay_xn_to'] = jQuery('#ngay_xn_to').val();
        }
        if(jQuery('#ngay_chuyen_kt_checkbox').is(':checked')==true){
            myData['ngay_chuyen_kt_from'] = jQuery('#ngay_chuyen_kt_from').val();
            myData['ngay_chuyen_kt_to'] = jQuery('#ngay_chuyen_kt_to').val();
        }
        if(jQuery('#ngay_chuyen_checkbox').is(':checked')==true){
            myData['ngay_chuyen_from'] = jQuery('#ngay_chuyen_from').val();
            myData['ngay_chuyen_to'] = jQuery('#ngay_chuyen_to').val();
        }
        myData['shipping_services'] = '';
        <!--LIST:shipping_services-->
        if(jQuery('#shipping_services_[[|shipping_services.id|]]').is(':checked')){
            myData['shipping_services'] += (myData['shipping_services']?',':'')+[[|shipping_services.id|]];
        }
        <!--/LIST:shipping_services-->
        myData['status'] = '';
        <!--LIST:status-->
        if(jQuery('#status_[[|status.id|]]').is(':checked')){
            myData['status'] += (myData['status']?',':'')+[[|status.id|]];
        }
        <!--/LIST:status-->
        //myData['is_inner_city'] = '';
        if(jQuery('#is_inner_city').is(':checked')){
            myData['is_inner_city'] = jQuery('#is_inner_city').val();
        }
        if(jQuery('#order_by').val()!=''){
            myData['order_by'] = jQuery('#order_by').val();
        }
        if(jQuery('#order_by_dir').val()!=''){
            myData['order_by_dir'] = jQuery('#order_by_dir').val();
        }
        return myData;
    }
    function ReloadList(pageNo){
        //pageNo = jQuery('#page_no').val()?jQuery('#page_no').val():pageNo;
        var myData = {
            'cmd':'list_pos',
            'load_ajax':'1',
            'keyword':jQuery('#keyword').val(),
            'c_n':jQuery('#customer_name').val(),
            'page_id':jQuery('#page_id').val()?jQuery('#page_id').val():'',
            'source_id':$('#source_id').val()?$('#source_id').val():'',
            'type':$('#type').val()?$('#type').val():'',
            'page_no':pageNo,
            'bundle_id':jQuery('#bundle_id').val()?jQuery('#bundle_id').val():'',
            'product_code':jQuery('#product_code').val()?jQuery('#product_code').val():'',
            'search_account_id':jQuery('#search_account_id').val()?jQuery('#search_account_id').val():'',
            'mkt_account_id':jQuery('#mkt_account_id').val()?jQuery('#mkt_account_id').val():'',
            'assigned_account_id':jQuery('#assigned_account_id').val()?jQuery('#assigned_account_id').val():'',
            'account_group_id':jQuery('#account_group_id').val()?jQuery('#account_group_id').val():'',
            'search_group_id':jQuery('#search_group_id').val()?jQuery('#search_group_id').val():'',
            'item_per_page':jQuery('#item_per_page').val()?jQuery('#item_per_page').val():'',
            block_id:blockId
        };
        if(jQuery('#ngay_tao_checkbox').is(':checked')==true){
            myData['ngay_tao_to'] = jQuery('#ngay_tao_to').val();
            myData['ngay_tao_from'] = jQuery('#ngay_tao_from').val();
        }
        if(jQuery('#ngay_chia_checkbox').is(':checked')==true){
            myData['ngay_chia_to'] = jQuery('#ngay_chia_to').val();
            myData['ngay_chia_from'] = jQuery('#ngay_chia_from').val();
        }
        if(jQuery('#ngay_xn_checkbox').is(':checked')==true){
            myData['ngay_xn_from'] = jQuery('#ngay_xn_from').val();
            myData['ngay_xn_to'] = jQuery('#ngay_xn_to').val();
        }
        if(jQuery('#ngay_chuyen_kt_checkbox').is(':checked')==true){
            myData['ngay_chuyen_kt_from'] = jQuery('#ngay_chuyen_kt_from').val();
            myData['ngay_chuyen_kt_to'] = jQuery('#ngay_chuyen_kt_to').val();
        }
        if(jQuery('#ngay_chuyen_checkbox').is(':checked')==true){
            myData['ngay_chuyen_from'] = jQuery('#ngay_chuyen_from').val();
            myData['ngay_chuyen_to'] = jQuery('#ngay_chuyen_to').val();
        }
        myData['shipping_services'] = '';
        <!--LIST:shipping_services-->
        if(jQuery('#shipping_services_[[|shipping_services.id|]]').is(':checked')){
            myData['shipping_services'] += (myData['shipping_services']?',':'')+[[|shipping_services.id|]];
        }
        <!--/LIST:shipping_services-->
        myData['status'] = '';
        <!--LIST:status-->
        if(jQuery('#status_[[|status.id|]]').is(':checked')){
            myData['status'] += (myData['status']?',':'')+[[|status.id|]];
        }
        <!--/LIST:status-->
        /*myData['dau_so'] = '';
        for(var i = 1;i<=6;i++){
            if(jQuery('#dau_so_'+i).is(':checked')){
                myData['dau_so'] += (myData['dau_so']?',':'')+jQuery('#dau_so_'+i).val();
            }
        }*/
        //myData['is_inner_city'] = '';
        if(jQuery('#is_inner_city').is(':checked')){
            myData['is_inner_city'] = jQuery('#is_inner_city').val();
        }
        if(jQuery('#order_by').val()!=''){
            myData['order_by'] = jQuery('#order_by').val();
        }
        if(jQuery('#order_by_dir').val()!=''){
            myData['order_by_dir'] = jQuery('#order_by_dir').val();
        }
        <?php if($ids=Url::get('ids')){?>
        var myData = {
            'load_ajax':'1',
            'ids':'<?php echo $ids;?>',
            'item_per_page':1000,
            block_id:blockId
        };
        <?php }?>
        $.ajax({
            method: "POST",
            url: 'form.php',
            data : myData,
            beforeSend: function(){
                $( "#module_"+blockId ).html('<div id="item-list" style="height:450px;padding:20px;"><div class="overlay text-info">\n' +
                    '        <i class="fa fa-refresh fa-spin"></i> Đang tải ... \n' +
                    '      </div></div>');
            },
            success: function(content){
                content = $.trim(content);
                $( "#module_"+blockId ).html(content);
                jQuery('#item-list .bor').css({'height':'450px'});
            },
            error: function(){
                alert('Lỗi tải danh sách đơn hàng. Bạn vui lòng kiểm tra lại kết nối!');
                location.reload();
            }
        });
    }
    function addGroup(){
        jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'add_group',
                'value':jQuery('#group_name').val(),
                block_id:blockId
            },
            beforeSend: function(){
            },
            success: function(content){
                content = jQuery.trim(content);
                if(content=='EMPTY'){
                    alert('Bạn vui lòng nhập tên nhóm');
                } else if(content=='TRUE'){
                    alert('Bạn đã thêm nhóm thành công');
                    jQuery('#add_group_form').hide();
                }
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function selectOne(obj){
        $all =jQuery('#ListAdminOrdersForm_all_checkbox').is(':checked');
        if($all && jQuery(obj).is(':checked')==false){
            jQuery('#ListAdminOrdersForm_all_checkbox').prop('checked',false);
        }
        jQuery('.totalOrder').html(jQuery('.order-checkbox').length);
        updateSelectedIds();
    }
    function selectAll(obj){
        $checked = jQuery(obj).is(':checked')?true:false;
        jQuery('.order-checkbox').prop('checked',$checked);
        updateSelectedIds();
    }
    function updateSelectedIds(){
        jQuery('#checked_order').val('');
        let $check = '';
        let $i=0;
        jQuery('.order-checkbox').each(function(){
            if(jQuery(this).is(':checked')) {
                $check += ($check?',':'')+jQuery(this).val();
                $i++;
            }
        });
        jQuery('#checked_order').val($check);
        jQuery('.totalOrder').html($i);
    }
    function directPrint(){
        if(jQuery('#checked_order').val()){
            jQuery('#act').val('print');
            ListAdminOrdersForm.submit();
            //jQuery('#ids').val();
            //window.location='<?php echo Url::build_current(array('act'=>'print'));?>&ids='+jQuery('#checked_order').val();
        }else{
            alert('Bạn vui lòng chọn đơn hàng để in');
            return false;
        }
    }
    function assignOrders(group){
        if(jQuery('#checked_order').val()){
            if(group==1){// chia theo nhóm
                if(!jQuery('#ass_account_group_id').val()){
                    alert('Bạn vụi lòng chọn nhóm để gán');
                    jQuery('#ass_account_group_id').focus();
                    return false;
                }
                jQuery.ajax({
                    method: "POST",
                    url: 'form.php',
                    data : {
                        'cmd':'assign_order_by_group',
                        'account_group_id':jQuery('#ass_account_group_id').val(),
                        'ids':jQuery('#checked_order').val(),
                        block_id:blockId
                    },
                    beforeSend: function(){

                    },
                    success: function(content){
                        content = jQuery.trim(content);
                        if(content=='TRUE'){
                            alert('Bạn đã gán đơn hàng thành công');
                            ReloadList(1);
                        }else{
                            console.log(content);
                            alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                        }
                    },
                    error: function(){
                        alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                    }
                });
            }else{// chia cá nhân
                //console.log(jQuery('#ass_account_id').val());
                if(!jQuery('#ass_account_id').val()){
                    alert('Bạn vụi lòng chọn nhân viên để gán');
                    jQuery('#ass_account_id').focus();
                    return false;
                }
                jQuery.ajax({
                    method: "POST",
                    url: 'form.php',
                    data : {
                        'cmd':'assign_order',
                        'account_id':jQuery('#ass_account_id').val(),
                        'ids':jQuery('#checked_order').val(),
                        block_id:blockId
                    },
                    beforeSend: function(){

                    },
                    success: function(content){
                        content = jQuery.trim(content);
                        if(content=='FALSE'){
                            console.log(content);
                            alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                        } else if(content=='CANCEL_ASSIGNMENT'){
                            alert('Bạn đã hủy gán đơn!');
                            ReloadList(1);
                        } else if(content=='TRUE'){
                            alert('Bạn đã gán đơn hàng thành công');
                            ReloadList(1);
                        }
                    },
                    error: function(){
                        alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                    }
                });
            }
        }else{
            alert('Bạn vui lòng chọn đơn hàng để thao tác');
            return false;
        }
    }
    function assignMktOrders(){
        if(jQuery('#checked_order').val()){
            if(!jQuery('#ass_mkt_account_id').val()){
                alert('Bạn vụi lòng chọn nhân viên marketing để gán');
                jQuery('#ass_mkt_account_id').focus();
                return false;
            }
            jQuery.ajax({
                method: "POST",
                url: 'form.php',
                data: {
                    'cmd': 'assign_mkt_order',
                    'account_id': jQuery('#ass_mkt_account_id').val(),
                    'ids': jQuery('#checked_order').val(),
                    block_id: blockId
                },
                beforeSend: function () {

                },
                success: function (content) {
                    //alert(content);
                    content = jQuery.trim(content);
                    if (content == 'FALSE') {
                        alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                    } else if (content == 'TRUE') {
                        alert('Bạn đã gán đơn hàng thành công cho marketing');
                        ReloadList(1);
                    }
                },
                error: function () {
                    alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                }
            });
        }else{
            alert('Bạn vui lòng chọn đơn hàng để thao tác');
            return false;
        }
    }
    function replaceAll(find, replace, str)
    {
        while( str.indexOf(find) > -1)
        {
            str = str.replace(find, replace);
        }
        return str;
    }
    function exportExcel(type){
        if(type=='SELECTED'){
            if(!$('#checked_order').val()){
                alert('Chưa có đơn hàng nào được chọn. Bạn vui lòng chọn đơn hàng trước khi xuất excel.');
                return false;
            }
        }
        let url = getOrderDataUrl();
        let url1 = JSON.stringify(url);
        url1 =replaceAll('":"','=',url1);
        url1 =replaceAll('","','&',url1);
        url1 =replaceAll('":','=',url1);
        url1 =replaceAll(',"','&',url1);
        url1 =replaceAll('{"','',url1);
        url1 =replaceAll('"}','',url1);
        let excel_href = '';
        if(type=='SELECTED'){
            let ids = $('#checked_order').val();
            excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&ids='+ids;
        }else{
            if (url1) {
                excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&cond='+url1;
            }
        }
        window.location=excel_href;
    }
    function exportShipExcel(){
        let url = getOrderDataUrl();
        let url1 = JSON.stringify(url);
        url1 =replaceAll('":"','=',url1);
        url1 =replaceAll('","','&',url1);
        url1 =replaceAll('":','=',url1);
        url1 =replaceAll(',"','&',url1);
        url1 =replaceAll('{"','',url1);
        url1 =replaceAll('"}','',url1);
        if (url1) {
            window.location='<?php echo Url::build_current(array('cmd'=>'export_ship_excel'));?>&cond='+url1;
        }
    }
    function selectWarehouse(){
        jQuery('#warehouseWrapper').show();
    }
    function createExInvoice(){
        let ids = jQuery('#checked_order').val();
        if(!ids){
            alert('Bạn vui lòng chọn đơn hàng để thực hiện xuất kho');
            return false;
        }
        let warehouse_id = jQuery('#warehouse_id').val();
        if(warehouse_id){
            <?php if(![[=quyen_xuat_kho=]]){?>
            alert('Bạn không có quyền tạo phiếu xuất kho');
            <?php }else{?>
            if ($ids = jQuery('#checked_order').val()) {
                var url = '<?php echo Url::build('qlbh_xuat_kho',array('cmd'=>'add'));?>&from_order_ids=' + $ids + '&warehouse_id=' + warehouse_id;
                var win;
                if (!win) {
                    win = window.open(url);
                }else{
                    win.focus();
                }
                //window.open();
            } else {
                alert('Bạn vui lòng chọn đơn hàng');
                return false;
            }
            <?php }?>
        }else{
            alert('Bạn vui lòng chọn kho trước khi tạo phiếu');
        }
    }
    function updateSelectedRow(obj,id,$bg_color){
        if(obj.bgColor==$bg_color){
            obj.bgColor = '#D8EC9C';
            $( '#checkbox_'+id ).prop( "checked", true );
        }else{
            obj.bgColor = $bg_color;
            $( '#checkbox_'+id ).prop( "checked", false );
        }
        updateSelectedIds();
    }
    function getVichatHistory(fb_conversation_id,page_id){
        if(!fb_conversation_id || !page_id){
            jQuery('#chatBodyWrapper').html('Dữ liệu cập nhật từ ngày 22/07/2018 mới xem được lịch sử chat.');
            return false;
        }
        jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'get_vichat_history',
                'fb_conversation_id':fb_conversation_id,
                'page_id':page_id,
                block_id:blockId
            },
            beforeSend: function(){
                jQuery('#chatBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                //alert(content);
                jQuery('#chatBodyWrapper').html(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function getDuplicatedPhone(orderId,mobile){
        var obj = getId('duplicate_note_'+orderId);
        alert(obj);
        if(obj){
            jQuery.ajax({
                method: "POST",
                url: 'form.php',
                data : {
                    'cmd':'get_dupplicates',
                    'mobile':mobile,
                    'order_id':orderId,
                    block_id:blockId
                },
                beforeSend: function(){
                    jQuery(obj).html('Đang tải ...');
                },
                success: function(content){
                    //alert(content);
                    jQuery(obj).html(content);
                },
                error: function(){
                    //alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                }
            });
        }
    }
    function updateTotalNotAssigned(bundle_id,sourceId){
        // trường hợp account tổng
        let phoneStoreId = 0;
        <!--IF:cond([[=account_type=]]==3)-->
        phoneStoreId = $('#phone_store_id').val();
        <!--/IF:cond-->
        jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'get_not_assigned_order_by_source',
                'source_id':sourceId,
                'bundle_id':bundle_id,
                'phone_store_id':phoneStoreId,
                'block_id':blockId
            },
            beforeSend: function(){
                //jQuery('#chatBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                //alert(content);
                content = content.trim();
                jQuery('#total_not_assigned_order').html(content);
                jQuery('#assigned_total').val(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function deleteForeverOrder(){
        if(confirm('Đơn hàng và tất cả thông tin liên quan sẽ bị xóa và không thể khôi phục. Bạn có chắc muốn xóa không?')){
            if(jQuery('#checked_order').val()){
                jQuery('#act').val('del_order');
                ListAdminOrdersForm.submit();
            }else{
                alert('Bạn chưa chọn đơn hàng để xóa.');
                return false;
            }
        }
    }
    function updateCountdown(expiredTime){
        $('#CountdownClock').countdown(expiredTime)
        .on('update.countdown', function(event) {
            let format = '%M:%S\'';
            if(event.offset.totalDays > 0) {
                format = '%-d day%!d ' + format;
            }
            if(event.offset.weeks > 0) {
                format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
        })
        .on('finish.countdown', function(event) {
            let now = new Date();
            now = now.setHours(now.getHours(),now.getMinutes()+5);
            updateCountdown(now);
        });
    }
</script>
<style>
    #columnOptionModal .ui-sortable-helper{border:1px dotted #0b97c4;background-color: #EFEFEF;cursor: move}
    #columnOptionModal .ui-sortable-handle:hover{border:1px dotted #0b97c4;background-color: #EFEFEF;cursor: move}
    #columnOptionModal  .list-group .list-group-item{padding:5px 10px 5px 10px !important;min-height: 30px;}
</style>
<script src="assets/vissale/js/jquery-sortable-min.js"></script>
<script>
    $( function() {
        reOrderColumns();
    } );
    function reOrderColumns(){
        $( "#sortable" ).sortable({
            axis: 'y',
            update: function (event, ui) {
                let data = $( this ).sortable( "serialize");
                let columns = $(this).children().get().map(function(el) {
                    console.log(el.lang);
                    return el.lang
                }).join(",");
                $.ajax({
                    url:'/index062019.php?page=admin_orders&cmd=change_order_column_position&columns='+columns,
                    data: data,
                    type: 'POST',
                    success: function(r){
                        r = $.trim(r);
                        if(r == 'DONE'){
                            ReloadList(1);
                        }
                    },
                    error: function(){
                        alert('Lỗi sắp xếp cột. Bạn vui lòng kiểm tra lại kết nối!');
                        location.reload();
                    }
                });
            }
        });
    }
    function activeOrderColumns(){
        let data = $('#sortable').sortable( "serialize");
        let columns = $('#sortable').children().get().map(function(el) {
            return el.lang
        }).join(",");
        $.ajax({
            url:'/index062019.php?page=admin_orders&cmd=change_order_column_position&columns='+columns,
            data: data,
            type: 'POST',
            success: function(r){
                r = $.trim(r);
                if(r == 'DONE'){
                    ReloadList(1);
                }
            },
            error: function(){
                alert('Lỗi sắp xếp cột. Bạn vui lòng kiểm tra lại kết nối!');
                location.reload();
            }
        });
    }
</script>