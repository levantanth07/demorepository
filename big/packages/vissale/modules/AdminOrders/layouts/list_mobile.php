<style>
    #columnOptionModal  .list-group .list-group-item{padding:5px 10px 5px 10px !important;min-height: 30px;}
</style>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
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
                <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
            </ol>
        </nav>
        <div class="">
            <div class="box box-default">
                <div class="box-header">
                    <div class="box-title">
                        <i class="glyphicon glyphicon-search"></i> Tìm kiếm [[|group_name|]]
                    </div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Số điện thoại hoặc mã (Tối thiểu [[|min_search_phone_number|]] số)" onchange="ReloadList(1);">
                            </div>
                            <div class="form-group">
                                <input name="customer_name" type="text" id="customer_name" placeholder="Họ tên khách hàng" onchange="ReloadList(1);" class="form-control">
                            </div>
                            <div class="form-group">
                                <select name="item_per_page" id="item_per_page" class="form-control" onchange="ReloadList(1);"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <!--IF:cond1([[=quyen_chia_don=]] and (AdminOrders::$group_id != 2284 and AdminOrders::$group_id != 5021))-->
                                <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target='#assignOrderModal'><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> Chia đơn nhanh </button>
                                <!--/IF:cond1-->
                                <!--IF:cond2(Session::get('admin_group'))-->
                                <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target='#columnOptionModal'><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> Tùy chọn cột </button>
                                <!--/IF:cond2-->
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" style="width: 100%;margin-bottom: 10px;" onclick="$('#dateSelection').toggle();">
                        <i class="fa fa-calendar"></i> Xem tùy chọn ngày
                    </button>
                    <div class="box box-info" id="dateSelection" style="display: none;">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_chia_checkbox" class="no-padding small"><input name="ngay_chia_checkbox" type="checkbox" id="ngay_chia_checkbox" onclick="ReloadList(1);"> Ngày chia:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_ngay_chia_tu_ngay'>
                                                                <input name="ngay_chia_from" type="text" id="ngay_chia_from" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function (){
                                                                    $('#datetimepicker_ngay_chia_tu_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_ngay_chia_den_ngay'>
                                                                <input name="ngay_chia_to" type="text" id="ngay_chia_to" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_ngay_chia_den_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_tao_checkbox" class="no-padding small"><input name="ngay_tao_checkbox" type="checkbox" id="ngay_tao_checkbox" onclick="ReloadList(1);" checked> Ngày tạo:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
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
                                                        <div class="col-md-12">
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
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_xn_checkbox" class="no-padding small"><input name="ngay_xn_checkbox" type="checkbox" id="ngay_xn_checkbox" onclick="$('#ngay_tao_checkbox').prop('checked',!$(this).is(':checked'));$('.status-checkbox').prop('checked',false);ReloadList(1);"> Ngày xác nhận:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
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
                                                        <div class="col-md-12">
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
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_chuyen_kt_checkbox" class="no-padding small"><input name="ngay_chuyen_kt_checkbox" type="checkbox" id="ngay_chuyen_kt_checkbox" onclick="ReloadList(1);"> Ngày chuyển KT:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_nckt_tu_ngay'>
                                                                <input name="ngay_chuyen_kt_from" type="text" id="ngay_chuyen_kt_from" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_nckt_tu_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_nckt_den_ngay'>
                                                                <input name="ngay_chuyen_kt_to" type="text" id="ngay_chuyen_kt_to" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_nckt_den_ngay').datetimepicker({
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
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_chuyen_checkbox" class="no-padding small"><input name="ngay_chuyen_checkbox" type="checkbox" id="ngay_chuyen_checkbox" onclick="ReloadList(1);"> Ngày chuyển hàng:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_nc_tu_ngay'>
                                                                <input name="ngay_chuyen_from" type="text" id="ngay_chuyen_from" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_nc_tu_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_nc_den_ngay'>
                                                                <input name="ngay_chuyen_to" type="text" id="ngay_chuyen_to" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_nc_den_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_thanh_cong_checkbox" class="no-padding small"><input name="ngay_thanh_cong_checkbox" type="checkbox" id="ngay_thanh_cong_checkbox" onclick="ReloadList(1);"> Ngày thành công:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_tc_tu_ngay'>
                                                                <input name="ngay_thanh_cong_from" type="text" id="ngay_thanh_cong_from" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_tc_tu_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_tc_den_ngay'>
                                                                <input name="ngay_thanh_cong_to" type="text" id="ngay_thanh_cong_to" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_tc_den_ngay').datetimepicker({
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
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <label for="ngay_thu_tien_checkbox" class="no-padding small"><input name="ngay_thu_tien_checkbox" type="checkbox" id="ngay_thu_tien_checkbox" onclick="ReloadList(1);"> Ngày thu tiền:</label>
                                                </div>
                                                <div class="panel-body" style="padding: 5px 15px 5px 15px">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_t_tu_ngay'>
                                                                <input name="ngay_thu_tien_from" type="text" id="ngay_thu_tien_from" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_t_tu_ngay').datetimepicker({
                                                                        format: 'DD/MM/YYYY'
                                                                    }).on('dp.change', function (ev) {ReloadList(1);});
                                                                });
                                                            </script>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class='input-group date' id='datetimepicker_tt_den_ngay'>
                                                                <input name="ngay_thu_tien_to" type="text" id="ngay_thu_tien_to" class="form-control dxeEditArea" />
                                                                <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(function () {
                                                                    $('#datetimepicker_tt_den_ngay').datetimepicker({
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Trạng thái đơn hàng <a href="#" class="small" onclick="jQuery('.status-checkbox').prop('checked',true);ReloadList(1);return false;">Chọn tất cả</a> | <a href="#" class="small" onclick="$('.status-checkbox').prop('checked',false);ReloadList(1);return false;">Bỏ chọn tất cả</a>
                                </div>
                                <div class="panel-body">
                                    <div style="border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;max-height: 150px;overflow: auto;">
                                        <div class="status">
                                            <?php $c=1;?>
                                            <!--LIST:status-->
                                            <div class="col-ms-2 btn btn-default" style="<?=[[=status.color=]]?'border-left: 5px solid '.[[=status.color=]].';'.' color:'.[[=status.color=]].'; text-shadow:1px 1px 1px #FFF; ':'border-left: 5px solid #CCC'?>"><label for="status_[[|status.id|]]"><input  name="status[]" type="checkbox" id="status_[[|status.id|]]" class="status-checkbox" style="margin-bottom:2px;font-size:20px;" <?php echo ([[=status.id=]]==10)?'checked':'';?> value="[[|status.id|]]" onchange="ReloadList(1);"> [[|status.name|]]</label></div>
                                            <?php $c++;?>
                                            <!--/LIST:status-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div>
                                <div class="form-group">
                                    <select name="source_id" id="source_id" class="form-control" onchange="ReloadList(1);"></select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-truck" aria-hidden="true"></i> Giao hàng</label>
                                    <select class="form-control" name="shipping_services[]" onchange="ReloadList(1);">
                                        <option value="">-Chọn-</option>
                                        <!--LIST:shipping_services-->
                                        <option id="shipping_services_[[|shipping_services.id|]]" value="[[|shipping_services.id|]]" >[[|shipping_services.name|]]</option>
                                        <!--/LIST:shipping_services-->
                                    </select>
                                    <script>
                                        $(document).ready(function() {
                                            //class="js-basic-multiple"
                                            //$('.js-basic-multiple').select2();
                                        });
                                    </script>
                                </div>
                                <div class="form-group">
                                    <label>Nội thành</label>
                                    <div class="ct">
                                        <select  name="is_inner_city" id="is_inner_city" onchange="ReloadList(1);" class="form-control">
                                            <option>--/--</option>
                                            <option value="0">Bỏ lọc</option>
                                            <option value="1">Lọc</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Loại đơn</label>
                                    <select name="type" id="type" class="form-control" onchange="ReloadList(1);"></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
                                        <div class=" col-ms-2 phan-loai" data-toggle="tooltip" title="Mã hàng hóa / sản phẩm">
                                            <label>Mã hàng </label>
                                            <div class="form-group">
                                                <input name="product_code" type="text" id="product_code" class="form-control" style="border-radius: 3px; height: 33px;border:1px solid #CCC;" onchange="ReloadList(1);">
                                            </div>
                                        </div>
                                        <div class=" col-ms-2 ">
                                            <label>Phân loại </label>
                                            <div class="form-group">
                                                <select name="bundle_id" id="bundle_id" class="form-control" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                        <div class=" col-ms-2 <?=[[=account_group_is_empty=]]?' hidden':''?>">
                                            <label>Nhóm tk</label>
                                            <div class="form-group">
                                                <select name="account_group_id" id="account_group_id" class="form-control" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <!--IF:system_cond([[=account_type=]]==3 or Session::get('master_group_id'))-->
                                            <span class="title">Công ty</span>
                                            <select name="search_group_id" id="search_group_id" class="form-control" onchange="ReloadList(1);"></select>
                                            <!--ELSE-->
                                            <span class="title">Nhân viên</span>
                                            <select name="search_account_id" id="search_account_id" class="form-control" onchange="ReloadList(1);"></select>
                                            <!--/IF:system_cond-->
                                        </div>
                                        <div class="col-xs-4">
                                            <span class="title">Marketing</span>
                                            <div class="form-group">
                                                <select name="mkt_account_id" id="mkt_account_id" class="form-control" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-4">
                                            <p class="title">Sale</p>
                                            <div class="form-group">
                                                <select name="assigned_account_id" id="assigned_account_id" class="form-control" onchange="ReloadList(1);" title="Sale được chia" data-toggle="tooltip"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-inline">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group pull-right">
                                            <select name="page_id" id="page_id" class="form-control" onchange="ReloadList(1);" style="max-width: 300px;"></select>
                                        </div>
                                        <div class="form-group pull-right">
                                            <input name="fb_post_id" type="text" id="fb_post_id" class="form-control" onchange="ReloadList(1);" style="max-width: 200px;border-radius: 3px; height: 33px;border:1px solid #CCC" placeholder="ID bài viết FB">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding:0px 0px 5px 0px;">
                <!--IF:cond([[=quyen_admin_marketing=]] or [[=quyen_marketing=]])-->
                <a href="index062019.php?page=admin_orders&cmd=waiting_list">Landing Page</a> |
                <a href="index062019.php?page=admin_orders&cmd=import_excel&v=3" class="text-bold">Import Excel</a> |
                <!--/IF:cond-->
                <!--IF:cond([[=quyen_admin_ke_toan=]])-->
                <a href="#" onclick="exportShipExcel();return false;"><span class="label label-success"><i class="fa fa-truck" aria-hidden="true"></i> Xuất excel vận chuyển</span></a>
                <!--/IF:cond-->
            </div>
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="btn-group" role="group">
                                <a style="width: 33%" href="<?=Url::build('admin_orders',['cmd'=>'add']);?>" target="_blank" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Tạo đơn</a>
                                <!--IF:cond([[=quyen_admin_marketing=]] or [[=quyen_marketing=]])-->
                                <a style="width: 33%" href="index062019.php?page=fb_setting" class="btn btn-primary" data-toggle="tooltip" title="Đồng bộ Fanpage từ Vichat"><i class="fa fa-facebook"></i> Đồng bộ FB</a>
                                <!--/IF:cond-->
                                <!--IF:excel_cond(User::is_admin() or Session::get('admin_group') or [[=quyen_xuat_excel=]])-->
                                <a style="width: 33%" href="#" class="btn btn-success" onclick="$('#exportExcelModal').modal();return false;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Excel ĐH</a>
                                <!--/IF:excel_cond-->
                            </div>
                            <div class="btn-group" role="group">
                                <a style="width: 40%" href="#" class="btn btn-warning" data-toggle="modal" data-target='#printModal'><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Thao tác đơn </a>
                                <a style="width: 60%" href="#" class="btn btn-danger" data-toggle="modal" data-target='#printModal' onclick="jQuery('.order-checkbox').prop('checked',true);jQuery('#ListAdminOrdersForm_all_checkbox').prop('checked',true);updateSelectedIds();"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Thao tác ĐH hiển thị</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-tabs-custom tab-info">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab">Danh sách đơn hàng <span class="totalOrder badge"></span></a></li>
                    <!--IF:admin_group_cond(Session::get('admin_group'))-->
                    <li><a class="text-warning" style="color:#ff8c52;font-weight: bold;" href="<?php echo Url::build_current(array('cmd'=>'quick_edit'));?>">Chỉnh sửa nhanh</a></li>
                    <!--/IF:admin_group_cond-->
                    <!--IF:cond([[=time_to_refesh_order=]])-->
                    <li class="pull-right small text-danger">
                        Cập nhật lại đơn hàng sau <span class="label label-warning" id="CountdownClock"></span> <i class="fa fa-clock-o"></i>
                    </li>
                    <!--/IF:cond-->
                </ul>
                <div class="tab-content">
                    <div id="module_<?php echo Module::block_id(); ?>" style="min-height: 100px;">
                        <div style="padding:20px 0px;color:#999;text-align: center;text-shadow: 1px 1px #FFF;font-size:16px;color:#000">Vui lòng chạm vào <a href="#" onclick="ReloadList(1);return false;" class="btn btn-warning">ĐÂY</a> để xem danh sách đơn hàng mặc định!</div>
                    </div>
                </div>
            </div>
        </div>
        <!--modal print-->
        <div class="modal fade print" id="printModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        Thao tác với <span class="totalOrder badge"></span> đơn hàng được chọn
                    </div>
                    <div class="modal-body">
                        <div class="bor">
                            <div><strong>Chọn khổ giấy bản in</strong></div>
                            <div class="clearfix"></div>
                            <?php foreach ($paper_sizes as $k => $size): ?>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="paper_size" value="<?= $k ?>" <?= ($k == $paper_size_default) ? 'checked' : "" ?>>
                                        <?= $size['name'] ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <?php if (Session::get('admin_group') || User::is_admin()): ?>
                                <a href="<?=Url::build('print-templates');?>" target="_blank" class="text-underline" style="text-decoration: underline;"><i class="fa fa-print"></i> Quản lý mẫu in</a>
                            <?php endif; ?>
                        </div>
                        <!--IF:cd_cond([[=quyen_chia_don=]] or [[=is_account_group_manager=]])-->
                        <ul class="nav nav-tabs" style="margin-top:5px;">
                            <li class="active"><a data-toggle="tab" href="#ass_accounts">Chia đơn sale</a></li>
                            <li><a data-toggle="tab" href="#ass_mkt_accounts">Gán cho MKT</a></li>
                            <li><a data-toggle="tab" href="#ass_groups">Theo nhóm</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="ass_accounts" class="tab-pane fade in active">
                                <div class="input-group">
                                    <select name="ass_account_id" id="ass_account_id" class="form-control"></select>
                                    <span class="input-group-btn">
                                <input class="btn btn-primary" type="button" onclick="assignOrders(0);" value="Thực hiện">
                        </span>
                                </div>
                            </div>
                            <div id="ass_mkt_accounts" class="tab-pane fade">
                                <div class="input-group">
                                    <select name="ass_mkt_account_id" id="ass_mkt_account_id" class="form-control"></select>
                                    <span class="input-group-btn">
                          <input class="btn btn-primary" type="button" onclick="assignMktOrders();" value="Thực hiện">
                        </span>
                                </div>
                            </div>
                            <div id="ass_groups" class="tab-pane fade">
                                <div class="input-group">
                                    <select name="ass_account_group_id" id="ass_account_group_id" class="form-control"></select>
                                    <span class="input-group-btn">
                                    <input class="btn btn-warning" type="button" onclick="assignOrders(1);" value="Thực hiện">
                                </span>
                                </div>
                            </div>
                        </div>
                        <!--/IF:cd_cond-->
                        <!--IF:xk_cond([[=quyen_xuat_kho=]])-->
                        <div id="warehouseWrapper" class="input-group" style="display:none;border:5px solid #FF0;">
                            <select name="warehouse_id" id="warehouse_id" class="form-control"></select>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="createExInvoice();jQuery('#printModal').modal('toggle');">Tạo phiếu Xuất kho</button>
                            </span>
                            <span class="input-group-btn">
                                <button class="btn btn-danger" type="button" onclick="jQuery('#warehouseWrapper').hide();">Huỷ</button>
                            </span>
                        </div>
                        <!--/IF:xk_cond-->
                        <!--IF:tao_nhom_cond([[=is_account_group_manager=]] or [[=quyen_marketing=]])-->
                        <div id="add_group_form" class="text-center" style="display:none;padding:5px;border:1px solid #999;margin-top:5px;">
                            <h4>Tạo nhóm </h4><input name="group_name" type="text" id="group_name" class="form-control" placeholder="Nhập tên ">
                            <input name="add_group" type="button" value="Thêm" onclick="addGroup();">
                            <input name="add_group" type="button" value="Đóng" onclick="jQuery('#add_group_form').hide();">
                        </div>
                        <!--/IF:tao_nhom_cond-->
                        <div id="change_status" class="text-center" style="display:none;padding:5px;border:1px solid #999;margin-top:5px;">
                            <h4>Đổi trạng thái</h4><select name="status_id" id="status_id" class="form-control"></select>
                            <input name="add_group" type="button" value=" Đổi " class="btn btn-warning" onclick="changeStatus(false);">
                            <input name="add_group" type="button" value="Đóng" class="btn btn-default" onclick="$('#change_status').hide();">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group text-center" role="group">
                            <!--IF:tao_nhom_cond([[=is_account_group_manager=]] or [[=quyen_marketing=]])-->
                            <button type="button" class="btn btn-default" onclick="$('#add_group_form').show();"> + Tạo Nhóm </button>
                            <!--/IF:tao_nhom_cond-->
                            <!--IF:cond([[=quyen_xuat_kho=]])-->
                            <button type="button" class="btn btn-default" onclick="if(confirm('Bạn có chắc chắn chuyển sang trạng thái chuyển hàng')){changeStatus(8);jQuery(this).html('Đang<br> xử lý...');}"> <i class="fa fa-truck"></i> Chuyển hàng</button>
                            <!--/IF:cond-->
                            <!--IF:print_cond([[=quyen_xuat_kho=]] or [[=quyen_xuat_excel=]] or [[=quyen_ke_toan=]])-->
                            <button type="button" class="btn btn-default" onclick="directPrint();"><i class="fa fa-print"></i> IN ĐƠN</button>
                            <!--/IF:print_cond-->
                        </div>
                        <div class="btn-group text-center" role="group">
                            <!--IF:excel_cond([[=quyen_xuat_excel=]])-->
                            <button type="button" class="btn btn-success" style="color:#FFF;" onclick="$('#exportExcelModal').modal();">Xuất Excel</button>
                            <!--/IF:excel_cond-->
                            <button class="btn btn-default btn-link" type="button" id="btn-change-status" data-toggle="modal" data-target="#orderStatusModal">Chuyển trạng thái</button>
                            <!--IF:xk_cond([[=quyen_xuat_kho=]])-->
                            <button type="button" class="btn btn-default" onclick="selectWarehouse();">Xuất Kho</button>
                            <!--/IF:xk_cond-->
                        </div>
                        <div class="btn-group text-center" role="group">
                            <button type="button" class="btn btn-default hide" onclick="alert('Đang trong quá trình cập nhật!');">Excel Phiếu <br> Xuất Kho</button>
                            <!--IF:owner_cond(Session::get('admin_group') and is_group_owner())-->
                            <button onclick="deleteForeverOrder();" type="button" class="btn btn-warning" data-toggle="tooltip" title="Đơn hàng và tất cả thông tin liên quan sẽ bị xóa và không thể khôi phục!"><i class="fa fa-trash-o"></i> Xóa <span class="totalOrder badge"></span> đơn?</button>
                            <!--/IF:owner_cond-->
                            <button type="button" class="btn btn-danger" data-dismiss="modal" style="color:#FFF;"><i class="fa fa-minus-circle"></i> Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Change Order Status -->
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
<!--IF:cond11([[=quyen_chia_don=]])-->
<form name="assignOrderForm" method="post">
    <div class="modal fade" id="assignOrderModal" tabindex="-1" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title">Chia đơn nhanh</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <!--IF:cond([[=account_type=]]==TONG_CONG_TY)-->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Kho số</label><br>
                                    <select name="phone_store_id" id="phone_store_id" class="form-control" onchange="updateTotalNotAssigned(jQuery('#ass_bundle_id').val(),jQuery('#ass_source_id').val())"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Chọn phân loại sản phẩm</label><br>
                                    <select name="ass_bundle_id" id="ass_bundle_id" class="form-control" onchange="updateTotalNotAssigned(this.value,jQuery('#ass_source_id').val())"></select>
                                </div>
                            </div>
                            <!--ELSE-->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Chọn phân loại sản phẩm</label><br>
                                    <select name="ass_bundle_id" id="ass_bundle_id" class="form-control" onchange="updateTotalNotAssigned(this.value,jQuery('#ass_source_id').val())"></select>
                                </div>
                            </div>
                            <!--/IF:cond-->
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Chọn nguồn</label><br>
                                    <select name="ass_source_id" id="ass_source_id" class="form-control" onchange="updateTotalNotAssigned(jQuery('#ass_bundle_id').val(),this.value)"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Nhập số đơn cần chia</label>
                                    <input  name="assigned_total" type="text" id="assigned_total" class="form-control" value="[[|total_not_assigned_order|]]" onchange="if(this.value>[[|total_not_assigned_order|]]){alert('Có tối đa [[|total_not_assigned_order|]] số tồn');this.value=[[|total_not_assigned_order|]];}" placeholder="Nhập số đơn cần chia">
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning-custom">* Tổng <span id="total_not_assigned_order" style="color:#336699; font-weight: bold;">[[|total_not_assigned_order|]]</span> đơn chưa được gán sẽ tự động chia đều cho tất cả các tài khoản được đặt tùy chọn gán đơn.</div>
                        <label><i class="fa fa-users"></i> Chọn sale (Giữ shift hoặc ctrl để chọn nhiều tài khoản)</label>
                        <select name="all_ass_account_id[]" id="all_ass_account_id" class="form-control" multiple="" data-toggle="tooltip" title="Áp dụng với nhiều nhân viên" style="height: 200px;width:100%;"></select>
                        <hr>
                        <label>Nhóm tài khoản</label>
                        <select name="account_group_id" id="account_group_id" class="form-control"></select>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <input  name="autoAssignOrder" type="submit" id="autoAssignOrder" class="btn btn-success btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Order" value=" + Gán đơn">
                </div>
            </div>
        </div>
    </div>
</form>
<!--/IF:cond11-->
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
<div class="modal fade modal-default" id="orderRevisionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="$('#quickEditModal').modal('toggle');">&times;</button>
                <h4 class="modal-title">Lịch sử đơn hàng <span id="orderId" class="text-info"></span></h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#orderRevisionTab">Hiện tại</a></li>
                </ul>

                <div class="tab-content">
                    <div id="orderRevisionTab" class="tab-pane fade in active">
                        <p id="orderRevisionModalContent"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="$('#quickEditModal').modal('toggle');">x Đóng</button>
            </div>
        </div>
    </div>
</div>
<div id="quickEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" style="width: 1000px;height: 600px;overflow: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="freeOrder();">×</button>
                <h4 class="modal-title"><i class="fa fa-file-text"></i> Sửa nhanh đơn hàng #<span id="noteOrderId"></span></h4>
            </div>
            <div class="modal-body" id="quickEditBodyWrapper">

            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button class="btn btn-default" onclick="viewOrderRevision($('#noteOrderId').html(),false);"> <i class="glyphicon glyphicon-time"></i> Lịch sử đơn hàng</button>
                </div>
                <button onclick="saveQuickEditOrder();" type="button" class="btn btn-primary" data-dismiss="modal">+ Lưu</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="freeOrder();">x Đóng</button>
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
                    <select  name="export_type" id="export_type" class="form-control">
                        <option value="">Theo đơn hàng</option>
                        <option value="1">Theo sản phẩm</option>
                    </select>
                    <hr>
                    <div class="row">
                        <div class="col-ms-6">
                            <button type="button" onclick="exportExcel('ALL');" class="btn btn-warning" style="width: 100%"><i class="fa fa-download"></i> Xuất tất cả <span id="totalOrderByListLabel badge"></span></button>
                        </div>
                        <div class="col-ms-6">
                            <button type="button" onclick="exportExcel('SELECTED');" class="btn btn-success" style="width: 100%"><i class="fa fa-download"></i> Xuất đơn được chọn <span class="totalOrder badge"></span></button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
                changeStatus(false);
                $('#orderStatusModal').modal('hide');
            }
        });

        $('#orderStatusModal').on('hidden.bs.modal', function (e) {
            $('.order-status').prop('checked', false)
        });

        $('#btn-cancel').click(function() {
            $.confirm({
                title: 'Hủy đơn hàng!',
                content: 'Bạn có chắc chắn muốn hủy đơn hàng được chọn?',
                buttons: {
                    confirm: function(){
                        changeStatus(9);
                    },
                    cancel: function(){
                        $('#orderStatusModal').modal();
                    }
                }
            });
        });
        $('#btn-change-status').click(function() {
            setTimeout(function() {
                $('#order-status-row').animate({
                    scrollTop: $('#order-status-row')[0].scrollHeight - $('#order-status-row')[0].clientHeight
                }, 6000)
            }, 4000)
        });

        $('#btn-save-status').click(function() {
            changeStatus(false);
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
            'fb_post_id':jQuery('#fb_post_id').val()?jQuery('#fb_post_id').val():'',
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
        if(jQuery('#ngay_thanh_cong_checkbox').is(':checked')==true){
            myData['ngay_thanh_cong_from'] = jQuery('#ngay_thanh_cong_from').val();
            myData['ngay_thanh_cong_to'] = jQuery('#ngay_thanh_cong_to').val();
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
        if($('#is_inner_city').val()==1){
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
            'load_ajax':'1',
            'keyword':jQuery('#keyword').val(),
            'c_n':jQuery('#customer_name').val(),
            'page_id':jQuery('#page_id').val()?jQuery('#page_id').val():'',
            'fb_post_id':jQuery('#fb_post_id').val()?jQuery('#fb_post_id').val():'',
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

        if(jQuery('#ngay_thanh_cong_checkbox').is(':checked')==true){
            myData['ngay_thanh_cong_from'] = jQuery('#ngay_thanh_cong_from').val();
            myData['ngay_thanh_cong_to'] = jQuery('#ngay_thanh_cong_to').val();
        }
        if(jQuery('#ngay_thu_tien_checkbox').is(':checked')==true){
            myData['ngay_thu_tien_from'] = jQuery('#ngay_thu_tien_from').val();
            myData['ngay_thu_tien_to'] = jQuery('#ngay_thu_tien_to').val();
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
        if($('#is_inner_city').val()==1){
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
    function changeStatus(status){
        if ($('#checked_order').val() == '') {
            alert('Bạn chưa chọn đơn hàng để thao tác.')

            return;
        }

        if(status==false){
            // status =  jQuery('#status_id').val();
            status = $('input.order-status:checked').val();
            if (status == undefined) {
                alert('Bạn chưa chọn trạng thái để cập nhật.')
                return;
            }
        }

        if (accept_edit_transport == 'accept' && status == 8) {
            $('#new_ids').val(jQuery('#checked_order').val())
            $('#new-transport-form').submit()
            return;
        }

        $.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'change_status',
                'status':status,
                'ids':$('#checked_order').val(),
                block_id:blockId
            },
            beforeSend: function(){

            },
            success: function(content){
                //alert(content);
                content = $.trim(content);
                if(content=='FALSE'){
                    alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                } else if(content=='TRUE'){
                    alert('Bạn đã thay đổi trạng thái thành công');
                    $('#add_group_form').hide();
                    $('#printModal').modal('toggle');
                    ReloadList(1);
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
                            jQuery('#printModal').modal('toggle');
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
                            jQuery('#printModal').modal('toggle');
                            ReloadList(1);
                        } else if(content=='TRUE'){
                            alert('Bạn đã gán đơn hàng thành công');
                            jQuery('#printModal').modal('toggle');
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
                        jQuery('#printModal').modal('toggle');
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
        let exportType = $('#export_type').val();
        if(type=='SELECTED'){
            let ids = $('#checked_order').val();
            excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&ids='+ids+'&export_type='+exportType;
        }else{
            if (url1) {
                excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&cond='+url1+'&export_type='+exportType;
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
    function freeOrder(){
        let orderId = $('#noteOrderId').html();
        $.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'free_order',
                'order_id':orderId,
                block_id:blockId
            }
        });
    }
    function saveQuickEditOrder(){
        let orderId = $('#noteOrderId').html();
        let note1 = $('#note1').val();
        let note2 = $('#note2').val();
        $.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'update_quick_order_info',
                'order_id':orderId,
                'note1':note1,
                'note2':note2,
                block_id:blockId
            },
            beforeSend: function(){
                jQuery('#quickEditBodyWrapper').html('Đang xử lý...');
            },
            success: function(content){
                eval('r='+content);
                if(r.result == 'TRUE'){
                    $.notify('Cập nhật nhanh đơn hàng #'+orderId+' thành công!');
                }else{
                    $.notify('Không có ghi chú nào được thay đổi!');
                }
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function showQuickEditModal(orderId){
        $('#noteOrderId').html(orderId);
        $.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'get_quick_order_info',
                'order_id':orderId,
                block_id:blockId
            },
            beforeSend: function(){
                jQuery('#quickEditBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                jQuery('#quickEditBodyWrapper').html(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
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
    function viewOrderRevision(orderId,old){
        $('#quickEditModal').modal('toggle');
        let data;
        data = {
            'cmd':'get_order_history',
            'order_id': orderId
        }
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : data,
            beforeSend: function(){
            },
            success: function(content){
                $('#orderRevisionModal').modal({'backdrop':'static','keyboard':false});
                if(content != 0){
                    $('#orderId').html(orderId);
                    $('#orderRevisionModalContent').html(content);
                }
                else{
                    console.log('no result');
                }
            }
        });
    }
</script>