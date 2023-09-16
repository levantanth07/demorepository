<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script src="assets/lib/daterangepicker/daterangepicker.min.js"></script>
<link href="assets/lib/daterangepicker/daterangepicker.css" rel="stylesheet" />
<script src="/packages/core/includes/js/helper.js"></script>
<style type="text/css" id="stylesheet">
    i#reset_duplicate_filter {width: 15px; display: flex; align-items: center; margin-left: auto; }
    i#reset_duplicate_filter path {fill: red; }
    #orderRevisionTab .text-warning {word-break: break-word; }
    .date-option {display: flex; padding: 0;}
    .date-option .col.w-25:not(:last-child):after {content: '';width: 10px;display: flex;height: 100%;}
    .date-option .col.w-25 {display: flex; }
    .export-notice {font-size: 12px; padding: 2px; border-radius: 3px; color: #333; }
</style>
<!-- Main content -->
<?php
    $paper_sizes = [[=paper_sizes=]];
    $paper_size_default = [[=paper_size_default=]];
    $can_change_status = (AdminOrders::$admin_group
        or AdminOrders::$quyen_ke_toan
        or AdminOrders::$quyen_admin_ke_toan
        or AdminOrders::$quyen_van_don
        or AdminOrders::$quyen_admin_marketing
    )?true:false;
    $warehouse = [[=arrWarehouse=]];
?>
<script>
   var warehouse = '<?php echo json_encode($warehouse) ?>';
</script>
<div class="container full">
    <form name="ListAdminOrdersForm" method="post" id="ListAdminOrdersForm" target="_blank">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-dark">
                <li class="breadcrumb-item"><a href="/">QLBH</a></li>
                <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>

            </ol>
        </nav>
        <div class="panel">
            <div id="order-list" class="box box-solid box-default">
                <div class="box-header">
                    <div class="row" style="display: flex;margin: 0;flex-direction: row;width: 100%;">
                        <div style="display: flex; flex-grow: 1; align-items: center; ">
                            <div class="form-inline">
                                <!-- Search SDT -->
                                <div class="form-group">
                                    <input name="term_sdt" type="text" id="term_sdt" class="form-control" style="height: 30px;font-size:13px;border-radius: 3px;margin-right:2px;width: 150px;" placeholder="SDT (Tối thiểu [[|min_search_phone_number|]] số)" onchange="ReloadList(1);">
                                </div>

                                <!-- Search Ma Don Hang -->
                                <div class="form-group">
                                    <input name="term_order_id" type="text" id="term_order_id" class="form-control" style="height: 30px;font-size:13px;border-radius: 3px;margin-right:2px;width: 150px;" placeholder="Mã đơn hàng" onchange="ReloadList(1);">
                                </div>

                                <!-- Search Ma Van Don -->
                                <div class="form-group">
                                    <input name="term_ship_id" type="text" id="term_ship_id" class="form-control" style="height: 30px;font-size:13px;border-radius: 3px;margin-right:2px;width: 150px;" placeholder="Mã vận đơn" onchange="ReloadList(1);">
                                </div>

                                <div class="form-group">
                                    <input name="customer_name" type="text" id="customer_name" placeholder="Họ tên khách hàng" onchange="ReloadList(1);" class="form-control"  style="height: 30px;font-size:13px;border-radius: 3px;margin-right:2px;width: 150px;">
                                </div>
                                <div class="form-group">
                                    <input name="customer_id" type="text" id="customer_id" placeholder="Mã khách hàng" onkeyup="this.value = this.value.replaceAll(/[\D]+/g, '')" onchange="onChangeCustomerID(this)" class="form-control"  style="height: 30px;font-size:13px;border-radius: 3px;margin-right:2px;width: 150px;">
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; height: fit-content; align-items: center; ">
                            <!--IF:cond1([[=quyen_chia_don=]] and (AdminOrders::$group_id != 2284 and AdminOrders::$group_id != 5021))-->
                            <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target='#assignOrderModal' onclick="updateTotalNotAssigned($('#ass_bundle_id').val(),$('#ass_source_id').val(),$('#ass_groups_id').val());"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> Chia đơn nhanh </button>
                            <!--/IF:cond1-->
                            <!--IF:cond2(Session::get('admin_group'))-->
                            <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target='#columnOptionModal'><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> Tùy chọn cột </button>
                            <!--/IF:cond2-->
                            <select name="item_per_page" id="item_per_page" class="form-control"  style="height:30px;width: 120px;" onchange="ReloadList(1);"></select>
                            <button class="btn btn-default thu-gon" type="button"><i class="glyphicon glyphicon-minus-sign"></i></button>
                        </div>
                    </div>
                </div>
                <div class="box-body" style="padding: 10px 0px 10px 0px;">
                    <div class="option-top col-md-12">
                        <div class="col-xs-12 col-md-12 date-option">
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_tao_checkbox" class="no-padding small"><input name="ngay_tao_checkbox" type="checkbox" id="ngay_tao_checkbox" onclick="ReloadList(1);" checked>
                                            Ngày tạo
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được tạo">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_tao_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    })
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_tao_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_chia_checkbox" class="no-padding small">
                                            <input name="ngay_chia_checkbox" type="checkbox" id="ngay_chia_checkbox" onclick="ReloadList(1);">
                                            Ngày chia
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được chia cho sale xử lý">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chia_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chia_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_xn_checkbox" class="no-padding small">
                                            <input name="ngay_xn_checkbox" type="checkbox" id="ngay_xn_checkbox" onclick="$('#ngay_tao_checkbox').prop('checked',!$(this).is(':checked'));$('.status-checkbox').prop('checked',false);ReloadList(1);">
                                            Ngày chốt
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được hàng được chốt">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_xn_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_xn_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_chuyen_kt_checkbox" class="no-padding small">
                                            <input name="ngay_chuyen_kt_checkbox" type="checkbox" id="ngay_chuyen_kt_checkbox" onclick="ReloadList(1);">
                                            Chuyển đóng hàng
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đóng hàng (không tính với trạng thái có tên là kế toán mà do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chuyen_kt_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chuyen_kt_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_chuyen_checkbox" class="no-padding small">
                                            <input name="ngay_chuyen_checkbox" type="checkbox" id="ngay_chuyen_checkbox" onclick="ReloadList(1);">
                                            Ngày chuyển hàng
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Chuyển hàng (không tính với trạng thái do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chuyen_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_chuyen_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_chuyen_hoan_checkbox" class="no-padding small">
                                            <input name="ngay_chuyen_hoan_checkbox" type="checkbox" id="ngay_chuyen_hoan_checkbox" onclick="ReloadList(1);">
                                            Ngày chuyển hoàn
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Chuyển hoàn (không tính với trạng thái do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class='input-group date' id='datetimepicker_chuyen_hoan_from'>
                                                    <input name="ngay_chuyen_hoan_from" type="text" id="ngay_chuyen_hoan_from" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class='input-group date' id='datetimepicker_chuyen_hoan_to'>
                                                    <input name="ngay_chuyen_hoan_to" type="text" id="ngay_chuyen_hoan_to" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_thanh_cong_checkbox" class="no-padding small">
                                            <input name="ngay_thanh_cong_checkbox" type="checkbox" id="ngay_thanh_cong_checkbox" onclick="ReloadList(1);">
                                            Ngày thành công
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái thành công (không tính với trạng thái do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_thanh_cong_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_thanh_cong_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_thu_tien_checkbox" class="no-padding small">
                                            <input name="ngay_thu_tien_checkbox" type="checkbox" id="ngay_thu_tien_checkbox" onclick="ReloadList(1);">
                                            Ngày thu tiền
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đã thu tiền (không tính với trạng thái do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_thu_tien_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
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
                                                        }).on('dp.change', function (ev) {
	                                                        if($('#ngay_thu_tien_checkbox').is(':checked')==true) {
		                                                        ReloadList(1);
	                                                        }
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col w-25">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                        <label for="ngay_tra_hang_checkbox" class="no-padding small">
                                            <input name="ngay_tra_hang_checkbox" type="checkbox" id="ngay_tra_hang_checkbox" onclick="ReloadList(1);">
                                            Ngày trả hàng về Kho
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đã trả hàng về kho (không tính với trạng thái do shop tự tạo)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </label>
                                    </div>
                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class='input-group date' id='datetimepicker_tra_hang_from'>
                                                    <input name="ngay_tra_hang_from" type="text" id="ngay_tra_hang_from" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class='input-group date' id='datetimepicker_tra_hang_to'>
                                                    <input name="ngay_tra_hang_to" type="text" id="ngay_tra_hang_to" class="form-control dxeEditArea" />
                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <p class="title small">Trạng thái đơn hàng <a href="#" class="small" onclick="$('.status-checkbox').prop('checked',true);ReloadList(1);return false;">Chọn tất cả</a> | <a href="#" class="small" onclick="$('.status-checkbox').prop('checked',false);ReloadList(1);return false;">Bỏ chọn tất cả</a></p>
                        <div class="col-xs-12 col-md-12" style="padding-top:5px;padding-bottom: 5px;margin-bottom:5px;border-top: 1px dotted #999;border-bottom: 1px dotted #999;background-color: #EFEFEF !important;max-height: 150px;overflow: auto;">
                            <div class="status">
                                <?php $c=1;?>
                                <!--LIST:status-->
                                <div class="col-xs-2 btn btn-default" style="<?=[[=status.color=]]?'border-left: 5px solid '.[[=status.color=]].';'.' color:'.[[=status.color=]].'; text-shadow:1px 1px 1px #FFF; ':'border-left: 5px solid #CCC'?>"><label for="list_status_[[|status.id|]]"><input  name="status[]" type="checkbox" id="list_status_[[|status.id|]]" class="status-checkbox" style="margin-bottom:2px;font-size:20px;" <?php echo ([[=status.id=]]==10)?'checked':'';?> value="[[|status.id|]]" onchange="ReloadList(1);"> [[|status.name|]]</label></div>
                                <?php $c++;?>
                                <!--/LIST:status-->
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12" style="font-size:11px;padding-top:5px;padding-bottom: 5px;margin-bottom:5px;margin-top:5px;border-bottom: 1px dotted #999;">
                            <div class="col-md-7 no-padding">
                                <div class="row">
                                    <div class="col-xs-2 no-padding">
                                        <span class="title"><i class="fa fa-phone-squaRe" aria-hidden="true"></i> Nguồn shop</span>
                                        <div class="ct">
                                            <select name="source_shop_id" id="source_shop_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 no-padding">
                                        <span class="title"><i class="fa fa-phone-squaRe" aria-hidden="true"></i> Nguồn Marketing</span>
                                        <div class="ct">
                                            <select name="source_id" id="source_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 no-padding">
                                        <span class="title"><i class="fa fa-truck" aria-hidden="true"></i> Giao hàng</span>
                                        <div class="ct">
                                            <select class="form-control" name="shipping_services[]" onchange="ReloadList(1);">
                                                <option value="">-Chọn-</option>
                                                <!--LIST:shipping_services-->
                                                <option id="shipping_services_[[|shipping_services.id|]]" value="[[|shipping_services.id|]]" >[[|shipping_services.name|]]</option>
                                                <!--/LIST:shipping_services-->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 no-padding">
                                        <span class="title"><i class="fa fa-location-arrow" aria-hidden="true"></i> Tỉnh/thành</span>
                                        <div class="input-group">
                                            <button class="btn btn-default text-bold dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                                Chọn lọc
                                            </button>
                                            <div class="dropdown-menu">
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <select name="city_id" id="city_id" onchange="ReloadList(1);" class="form-control select2"></select>
                                                    </li>
                                                    <li class="list-group-item text-center">
                                                        <label for="is_inner_city"><input  name="is_inner_city" type="checkbox" id="is_inner_city" onchange="ReloadList(1);"> Nội thành</label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 no-padding">
                                        <p class="title">Loại đơn</p>
                                        <div class="ct">
                                            <select name="type" id="type" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding" data-toggle="tooltip" title="Mã hàng hóa / sản phẩm">
                                        <span class="title">Mã hàng </span>
                                        <div class="form-group">
                                            <input name="product_code" type="text" id="product_code" class="form-control" placeholder="mã sp1, mã sp2" style="border-radius: 3px; height: 33px;border:1px solid #CCC;" onchange="ReloadList(1);">
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding">
                                        <span class="title">Phân loại </span>
                                        <div class="form-group">
                                            <select name="bundle_id" id="bundle_id" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding">
                                        <span class="title">Nhóm KH </span>
                                        <div class="form-group">
                                            <select name="customer_group_id" id="customer_group_id" class="form-control"
                                                    onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>
                                    <div class=" col-xs-2 phan-loai no-padding">
                                            <span class="title">In đơn hàng</span>
                                            <div class="form-group">
                                                <PRINTED_ORDER></PRINTED_ORDER>
                                            </div>
                                        </div>
                                    <div class="col-xs-2 phan-loai no-padding">
                                        <span class="title" style="display: flex;">
                                            Trùng đơn
                                            <i id="reset_duplicate_filter" style="display: none; cursor: pointer;" title="Reset">
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="retweet-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-retweet-alt fa-w-20 fa-2x"><path d="M392.402 383.598C404.359 395.555 395.891 416 378.981 416H120c-13.255 0-24-10.745-24-24V192H48c-21.361 0-32.045-25.895-16.971-40.971l80-80c9.373-9.372 24.568-9.372 33.941 0l80 80C240.074 166.134 229.319 192 208 192h-48v160h202.056c7.82 0 14.874 4.783 17.675 12.084a55.865 55.865 0 0 0 12.671 19.514zM592 320h-48V120c0-13.255-10.745-24-24-24H261.019c-16.91 0-25.378 20.445-13.421 32.402a55.865 55.865 0 0 1 12.671 19.514c2.801 7.302 9.855 12.084 17.675 12.084H480v160h-48c-21.313 0-32.08 25.861-16.971 40.971l80 80c9.374 9.372 24.568 9.372 33.941 0l80-80C624.041 345.9 613.368 320 592 320z" class=""></path></svg>
                                            </i>
                                        </span>
                                        <div class="form-group">
                                            <DUPLICATE_FILTER></DUPLICATE_FILTER>
                                        </div>

                                    </div>


                                    <div class="col-xs-2 phan-loai no-padding">
                                        <p class="title">Kho</p>
                                        <div class="form-group">
                                            <select name="warehouse_id_filter" id="warehouse_id_filter" class="form-control" onchange="ReloadList(1);"></select>
                                        </div>
                                    </div>

                                    <div class="col-xs-2 no-padding">
                                        <p class="title">Nhà Mạng</p>
                                        <div class="form-group">
                                                <select name="home_network[]" id="home_network" multiple="multiple" class="multiple-select-home-network" style="display: none;"  onchange="ReloadList(1);">
                                                    [[|home_network|]]
                                                </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-4 filter-price">
                                        <span class="title">
                                            Khoảng giá trị đơn hàng
                                        </span>
                                        <div class="input-wrapper">
                                            <div class="min"> <input class="form-control" value="0"/> </div>
                                            <div class="max"> <input class="form-control" value=""/> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row">
                                    <div class=" col-xs-2 phan-loai no-padding<?=[[=account_group_is_empty=]]?' hidden':''?>">
                                        <span class="title">Nhóm tk</span>
                                        <div class="form-group">
                                            <!-- <select name="account_group_id" id="account_group_id" class="form-control" onchange="ReloadList(1);"></select> -->
                                            <select name="account_group_id[]" id="account_group_id" multiple="multiple" class="multiple-select-account" style="display: none;"  onchange="ReloadList(1);">
                                                    [[|account_group_id_option|]]
                                                </select>
                                        </div>
                                    </div>
                                    <div id="un_load_data_checkbox">
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <!--IF:system_cond([[=account_type=]]==3 or Session::get('master_group_id'))-->
                                            <span class="title">Công ty</span>
                                            <div class="form-group">
                                                <select name="search_group_id" id="search_group_id" class="form-control"
                                                        onchange="ReloadList(1);"></select>
                                            </div>
                                            <!--ELSE-->
                                            <span class="title">Nhân viên</span>
                                            <div class="form-group">
                                                <select name="search_account_id" id="search_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                            <!--/IF:system_cond-->
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <span class="title">Marketing</span>
                                            <div class="form-group">
                                                <select name="mkt_account_id" id="mkt_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <p class="title">Sale</p>
                                            <div class="form-group">
                                                <select name="assigned_account_id" id="assigned_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"
                                                        title="Sale được chia" data-toggle="tooltip"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <p class="title">NV chốt</p>
                                            <div class="form-group">
                                                <select name="confirmed_account_id" id="confirmed_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"
                                                        title="Nhân viên chốt đơn" data-toggle="tooltip"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <span class="title">Nguồn UpSale</span>
                                            <div class="form-group">
                                                <select name="upsale_account_id" id="upsale_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Using checkbox -->
                                    <div id="load_data_checkbox"  style="display: none;">
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <!--IF:system_cond([[=account_type=]]==3 or Session::get('master_group_id'))-->
                                            <span class="title">Công ty</span>
                                            <div class="form-group">
                                                <select name="search_group_id" id="search_group_id" class="form-control"
                                                        onchange="ReloadList(1);"></select>
                                            </div>
                                            <!--ELSE-->
                                            <span class="title">Nhân viên</span>
                                            <div class="form-group">
                                                <select name="all_search_account_id" id="all_search_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                            <!--/IF:system_cond-->
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <span class="title">Marketing</span>
                                            <div class="form-group">
                                                <select name="all_mkt_account_id" id="all_mkt_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <p class="title">Sale</p>
                                            <div class="form-group">
                                                <select name="all_assigned_account_id" id="all_assigned_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"
                                                        title="Sale được chia" data-toggle="tooltip"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <p class="title">NV chốt</p>
                                            <div class="form-group">
                                                <select name="all_confirmed_account_id" id="all_confirmed_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"
                                                        title="Nhân viên chốt đơn" data-toggle="tooltip"></select>
                                            </div>
                                        </div>
                                        <div class=" col-xs-2 phan-loai no-padding">
                                            <span class="title">Nguồn UpSale</span>
                                            <div class="form-group">
                                                <select name="all_upsale_account_id" id="all_upsale_account_id"
                                                        class="form-control select2" onchange="ReloadList(1);"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-3 phan-loai pull-right">
                                        <div class="form-check" style="margin-top: 20px">
                                          <label class="form-check-label text-right">
                                            <input type="checkbox" class="form-check-input" id="checkbox" name="checkbox" value="1"> Hiển thị toàn bộ NV
                                          </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-3 no-padding pull-right">
                                        <span class="title">
                                            Khoảng ngày hẹn giao
                                        </span>
                                        <div class="form-group">
                                            <input type="text" id="deliver_date_filter" name="deliver_date_filter" class="form-control" value="" style="height: 34px; border: 1px solid #d2d6de" onfocusout="validateDate()" />
                                        </div>
                                    </div>
                                    <div class="col-xs-3 no-padding pull-right">
                                        <span class="title">
                                            Khoảng ngày lưu đơn
                                        </span>
                                        <div class="form-group">
                                            <input type="text" id="saved_date_filter" name="saved_date_filter" class="form-control" value="" style="height: 34px; border: 1px solid #d2d6de" onfocusout="validateDate()" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <div class="form-inline">
                                <div class="load_data" style="display: none;">

                                </div>

                                <div class="form-group" data-step="2" data-intro="Tạo đơn hàng mới">
                                    <a href="Javascript:;" onclick="window.open('index062019.php?page=admin_orders&cmd=add')" class="btn btn-info"><i class="fa fa-pencil-square-o fa-spin" aria-hidden="true"></i> Tạo đơn mới</a>
                                </div>
                                <!--IF:cond(AdminOrders::$quyen_ke_toan or AdminOrders::$quyen_admin_ke_toan or AdminOrders::$quyen_van_don or AdminOrders::$quyen_chia_don or [[=is_account_group_manager=]] or AdminOrders::$quyen_admin_marketing or [[=quyen_in_don=]])-->
                                <div class="form-group" data-step="3" data-intro="Thao tác với đơn hàng theo danh sách tích chọn ở bên dưới">
                                    <a href="#" class="btn btn-warning" data-toggle="modal" data-target='#printModal'><span class="glyphicon glyphicon-print" aria-hidden="true"></span>Thao tác đơn hàng được chọn </a>
                                </div>
                                <div class="form-group">
                                    <a href="#" class="btn btn-danger" data-toggle="modal" data-target='#printModal' onclick="$('.order-checkbox').prop('checked',true);$('#ListAdminOrdersForm_all_checkbox').prop('checked',true);updateSelectedIds();"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Thao tác tất cả đơn hàng hiển thị</a>
                                </div>
                                <!--/IF:cond-->
                                <div class="form-group">
                                    <!--IF:excel_cond([[=quyen_xuat_excel=]] or [[=isOwner=]])-->
                                    <a href="#" class="btn btn-success" onclick="$('#exportExcelModal').modal();return false;"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Excel</a>
                                    <!--/IF:excel_cond-->
                                </div>
                                <div class="form-group">
                                    <!--IF:cond([[=quyen_admin_marketing=]] or [[=quyen_marketing=]])-->
                                    <a href="index062019.php?page=fb_setting" class="btn btn-primary" data-toggle="tooltip" title="Đồng bộ Fanpage từ Vichat"><i class="fa fa-facebook"></i> Đồng bộ</a>
                                    <!--/IF:cond-->
                                    <!--IF:excel_cond([[=quyen_xuat_excel=]] or [[=isOwner=]])-->
                                    <a href="#" class="btn btn-success" onclick="sendMailToCarrier();return false;"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Gửi Email cho NVC</a>
                                    <!--/IF:excel_cond-->
                                    <?php if (AdminOrdersDB::permissionSendEmailPrint()): ?>
                                        <button type="button" class="btn btn-success sendMailFilePrintOrder" onclick="sendMailFilePrintOrder();">Gửi email file in đơn</button>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group pull-right">
                                    <select name="page_id" id="page_id" class="form-control" onchange="ReloadList(1);" style="max-width: 200px;"></select>
                                </div>
                                <div class="form-group pull-right">
                                    <input name="fb_post_id" type="text" id="fb_post_id" class="form-control" onchange="ReloadList(1);" style="max-width: 150px;border-radius: 3px; height: 33px;border:1px solid #CCC" placeholder="ID bài viết FB">
                                </div>
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
                    <!--IF:cond([[=quyen_admin_marketing=]] or [[=quyen_marketing=]])-->
                    <li><a href="index062019.php?page=admin_orders&cmd=waiting_list">Landing Page</a></li>
                    <li><a href="index062019.php?page=admin_orders&cmd=import_excel&v=3" class="text-bold">Import Excel</a></li>
                    <!--/IF:cond-->
                    <!--IF:cond([[=quyen_xuat_excel=]] or [[=isOwner=]])-->
                    <li><a href="#" onclick="$('#mdlExportExcelCarrier').modal();return false;"><span class="label label-success"><i class="fa fa-truck" aria-hidden="true"></i> Xuất excel vận chuyển</span></a></li>
                    <!--/IF:cond-->
                    <!--IF:cond([[=time_to_refesh_order=]])-->
                    <li class="pull-right small text-danger text-right">
                        Cập nhật lại đơn hàng sau <span class="label label-warning" id="CountdownClock"></span> <i class="fa fa-clock-o"></i><br>
                        <i>Không chọn điều kiện lọc ngày tháng sẽ hiển thị DS đơn 6 tháng gần nhất</i>
                    </li>
                    <!--ELSE-->
                    <li class="pull-right small text-danger text-right">
                        <i>Không chọn điều kiện lọc ngày tháng sẽ hiển thị DS đơn 6 tháng gần nhất</i>
                    </li>
                    <!--/IF:cond-->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="module_<?php echo Module::block_id(); ?>">
                        <!--IF:cond([[=device=]]=='DESKTOP')-->
                        <div style="padding:20px 0px;color:#999;text-align: center;text-shadow: 1px 1px #FFF;font-size:16px;color:#333">Vui lòng nhấn <a href="#" class="btn btn-sm btn-default" onclick="ReloadList(1);return false;" data-step="1" data-intro="Nhấn vào đây để xem danh sách đơn hàng"> <i class="fa fa-play-circle"></i> Enter</a> để xem danh sách đơn hàng mặc định!</div>
                        <!--ELSE-->
                        <div style="padding:20px 0px;color:#999;text-align: center;text-shadow: 1px 1px #FFF;font-size:16px;color:#000">Vui lòng chạm vào <a href="#" onclick="ReloadList(1);return false;" class="btn btn-warning" data-step="1" data-intro="Nhấn vào đây để xem danh sách đơn hàng">ĐÂY</a> để xem danh sách đơn hàng mặc định!</div>
                        <!--/IF:cond-->
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
                                <a href="index062019.php?page=print-templates" target="_blank" class="text-underline" style="text-decoration: underline;"><i class="fa fa-print"></i> Quản lý mẫu in</a>
                            <?php endif; ?>
                        </div>
                        <!--IF:cd_cond([[=quyen_chia_don=]] or [[=is_account_group_manager=]])-->
                        <ul class="nav nav-tabs" style="margin-top:5px;">
                            <li style="padding: 10px 15px 15px 0">Chia đơn cho</li>
                            <li class="active"><a data-toggle="tab" href="#ass_accounts">SALE</a></li>
                            <li><a data-toggle="tab" href="#ass_mkt_accounts">MKT</a></li>
                            <li><a data-toggle="tab" href="#ass_upsale">UPSALE</a></li>
                            <li><a data-toggle="tab" href="#ass_groups">NHÓM</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="ass_accounts" class="tab-pane fade in active">
                                <div class="input-group">
                                    <select name="ass_account_id" id="ass_account_id" class="form-control select2"></select>
                                    <span class="input-group-btn">
                                <input class="btn btn-primary" type="button" onclick="assignOrders(0);" value="Thực hiện">
                        </span>
                                </div>
                            </div>
                            <div id="ass_mkt_accounts" class="tab-pane fade">
                                <div class="input-group">
                                    <select name="ass_mkt_account_id" id="ass_mkt_account_id" class="form-control select2"></select>
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
                            <div id="ass_upsale" class="tab-pane fade">
                                <div class="input-group">
                                    <select name="ass_upsale_id" id="ass_upsale_id" class="form-control select2"></select>
                                    <span class="input-group-btn">
                                    <input class="btn btn-primary" type="button" onclick="assignUpSaleOrders();" value="Thực hiện">
                                </span>
                                </div>
                            </div>
                        </div>
                        <!--/IF:cd_cond-->
                        <!--IF:xk_cond([[=quyen_xuat_kho=]])-->
                        <div id="warehouseWrapper" class="input-group" style="display:none;border:5px solid #FF0;">
                            <select name="warehouse_id" id="warehouse_id" class="form-control"></select>
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" onclick="createExInvoice();$('#printModal').modal('toggle');">Tạo phiếu Xuất kho</button>
                    </span>
                            <span class="input-group-btn">
                        <button class="btn btn-danger" type="button" onclick="$('#warehouseWrapper').hide();">Huỷ</button>
                    </span>
                        </div>
                        <!--/IF:xk_cond-->
                        <!--IF:tao_nhom_cond([[=is_account_group_manager=]] or [[=quyen_marketing=]])-->
                        <div id="add_group_form" class="text-center" style="display:none;padding:5px;border:1px solid #999;margin-top:5px;">
                            <h4>Tạo nhóm </h4><input name="group_name" type="text" id="group_name" class="form-control" placeholder="Nhập tên ">
                            <input name="add_group" type="button" value="Thêm" onclick="addGroup();">
                            <input name="add_group" type="button" value="Đóng" onclick="$('#add_group_form').hide();">
                        </div>
                        <!--/IF:tao_nhom_cond-->
                        <div id="change_status" class="text-center" style="display:none;padding:5px;border:1px solid #999;margin-top:5px;">
                            <h4>Đổi trạng thái</h4><select name="status_id" id="status_id" class="form-control"></select>
                            <input name="add_group" type="button" value=" Đổi " class="btn btn-warning" onclick="changeStatus(false);">
                            <input name="add_group" type="button" value="Đóng" class="btn btn-default" onclick="$('#change_status').hide();">
                        </div>
                    </div>
                    <div class="modal-footer order-action-buttons">
                        <div class="row text-center">
                            <!-- <div class="col-xs-4">
                                <!--IF:tao_nhom_cond([[=is_account_group_manager=]] or [[=quyen_marketing=]])-->
                                <button type="button" class="btn btn-default" onclick="$('#add_group_form').show();"> + Tạo Nhóm </button>
                                <!--/IF:tao_nhom_cond-->
                            </div> -->
                            <div class="col-xs-4">
                                <!--IF:cond([[=quyen_xuat_kho=]])-->
                                <button type="button" class="btn btn-default" onclick="if(confirm('Bạn có chắc chắn chuyển sang trạng thái chuyển hàng')){changeStatus(8);$(this).html('Đang<br> xử lý...');}"> <i class="fa fa-truck"></i> Chuyển hàng</button>
                                <!--/IF:cond-->
                            </div>
                            <div class="col-xs-4">
                                <!--IF:print_cond([[=quyen_in_don=]] or [[=isOwner=]])-->
                                <button type="button" class="btn btn-default" onclick="$('#mdlPrintOrder').modal()"><i class="fa fa-print"></i> IN ĐƠN</button>
                                <!--/IF:print_cond-->
                            </div>
                            <div class="col-xs-4">
                                <!--IF:xk_cond([[=quyen_xuat_kho=]])-->
                                <button type="button" class="btn btn-default" onclick="selectWarehouse();">Xuất Kho</button>
                                <!--/IF:xk_cond-->
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-xs-4">
                                <!--IF:excel_cond([[=quyen_xuat_excel=]] or [[=isOwner=]])-->
                                <button type="button" class="btn btn-success" style="color:#FFF;" onclick="$('#exportExcelModal').modal();">Xuất Excel</button>
                                <!--/IF:excel_cond-->
                            </div>
                            <div class="col-xs-4">
                                <!--IF:status_cond($can_change_status)-->
                                <button class="btn btn-link" type="button" id="btn-change-status" data-toggle="modal" data-target="#orderStatusModal">Chuyển trạng thái</button>
                                <!--ELSE-->
                                <span class="small" style="color:#f00;">
                                    Chỉ quản lý shop, kế toán, quản lý marketing, vận đơn mới có quyền chuyển trạng thái.
                                </span>
                                <!--/IF:status_cond-->
                            </div>

                            <div class="col-xs-4">
                                <?php if (AdminOrdersDB::permissionSendEmailPrint()): ?>
                                    <button type="button" class="btn btn-default sendMailFilePrintOrder" onclick="sendMailFilePrintOrder();">Gửi email file in đơn</button>
                                <?php endif; ?>
                            </div>

                        </div>
                        <div class="row text-center">
                            <!--<div class="col-xs-4">
                                <button type="button" class="btn btn-default hide" onclick="alert('Đang trong quá trình cập nhật!');">Excel Phiếu <br> Xuất Kho</button>
                            </div>-->
                            <div class="col-xs-4">
                                <!--IF:excel_cond([[=quyen_xuat_excel=]] or [[=isOwner=]])-->
                                <button type="button" class="btn btn-success" style="color:#FFF;"
                                        onclick="sendMailToCarrier()">Gửi email cho NVC
                                </button>
                                <!--/IF:excel_cond-->
                            </div>
                            <div class="col-xs-4">
                                <!--IF:excel_cond([[=isAdmin=]] || [[=isOwner=]])-->
                                <button type="button" class="btn btn-link" id="assign-customer-group-btn">Gán nhóm KH
                                </button>
                                <!--/IF:excel_cond-->
                            </div>
                            <!-- <div class="col-xs-4">
                                <!--IF:excel_cond([[=isAdmin=]] || [[=isOwner=]])-->
                                <button type="button" class="btn btn-link" id="assign-bundle-btn">Gán Phân loại SP
                                </button>
                                <!--/IF:excel_cond-->
                            </div> -->
                            <div class="col-xs-4">
                                <!--IF:owner_cond(Session::get('admin_group') and is_group_owner())-->
                                <button onclick="deleteForeverOrder();" type="button" class="btn btn-warning" data-toggle="tooltip" title="Đơn hàng và tất cả thông tin liên quan sẽ bị xóa và không thể khôi phục!"><i class="fa fa-trash-o"></i> Xóa <span class="totalOrder badge"></span> đơn?</button>
                                <!--/IF:owner_cond-->
                            </div>

                            <div class="col-xs-4">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" style="color:#FFF;"><i class="fa fa-minus-circle"></i> Đóng</button>
                            </div>
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
                        <h3 class="modal-title">Tùy chọn cột hiển thị</h3>
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
        <!--modal in don-->
        <div id="mdlPrintOrder" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm" style="width: 450px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title text-success">
                            In đơn
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <!--IF:system_cond_hide_phone_number([[=show_phone_number_print_order=]]==1)-->
                            <br>
                            <input type="checkbox" name="getFullPhoneNumberPrintOrder" id="getFullPhoneNumberPrintOrder"> Không ẩn số điện thoại
                            <!--ELSE-->
                            <br>
                            <input type="checkbox" disabled="disabled"> Không ẩn số điện thoại
                            <!--/IF:system_cond_hide_phone_number-->
                            <hr>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="button" class="btn btn-success" style="width: 100%" onclick="directPrint();"><i class="fa fa-print"></i> Xác nhận in</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!--end modal in don-->
    </form>
</div>
<!--IF:cond11([[=quyen_chia_don=]])-->
<form name="assignOrderForm" method="post">
    <div class="modal fade" id="assignOrderModal" tabindex="-1" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title text-primary"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> <strong>Chia đơn nhanh</strong></div>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <!--IF:cond([[=account_type=]]==TONG_CONG_TY)-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <label>Kho số</label><br>
                                    <select name="phone_store_id" id="phone_store_id" class="form-control" onchange="updateTotalNotAssigned($('#ass_bundle_id').val(),$('#ass_source_id').val(),$('#ass_groups_id').val())"></select>
                                </div>
                            </div>
                        </div>
                        <!--/IF:cond-->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Chọn nhóm tài khoản tạo đơn</label>
                                    <select name="ass_groups_id" type="text" id="ass_groups_id" class="form-control" onchange="updateTotalNotAssigned($('#ass_bundle_id').val(),$('#ass_source_id').val(),this.value)">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <label>Chọn phân loại sản phẩm</label><br>
                                    <select name="ass_bundle_id" id="ass_bundle_id" class="form-control" onchange="updateTotalNotAssigned(this.value,$('#ass_source_id').val(),$('#ass_groups_id').val())"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <label>Chọn nguồn</label><br>
                                    <select name="ass_source_id" id="ass_source_id" class="form-control" onchange="updateTotalNotAssigned($('#ass_bundle_id').val(),this.value,$('#ass_groups_id').val())"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <label>Tiêu chí chia</label>
                                    <select  name="assign_option" type="text" id="assign_option" class="form-control">
                                        <option value="">Ưu tiên số mới</option>
                                        <option value="1">Ưu tiên số cũ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <label>Số đơn cần chia</label>
                                    <input type="hidden" id="max_assigned_total" value="0">
                                    <input  name="assigned_total" type="text" id="assigned_total" class="form-control" value="[[|total_not_assigned_order|]]" onchange="if(to_numeric(this.value)>to_numeric($('#max_assigned_total').val())){alert('Có tối đa '+$('#max_assigned_total').val()+' số tồn');this.value=$('#max_assigned_total').val();}" placeholder="Nhập số đơn cần chia">
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning-custom">* Tổng <span id="total_not_assigned_order" style="color:#336699; font-weight: bold;">[[|total_not_assigned_order|]]</span> đơn chưa được gán sẽ tự động chia đều cho tất cả các tài khoản được đặt tùy chọn gán đơn.</div>
                        <label><i class="fa fa-users"></i> Chọn sale (Giữ shift hoặc ctrl để chọn nhiều tài khoản)</label>
                        <select name="all_ass_account_id[]" id="all_ass_account_id" class="form-control" multiple="" data-toggle="tooltip" title="Áp dụng với nhiều nhân viên" style="height: 200px;width:100%;"></select>
                        <hr>
                        <label>Chọn nhóm tài khoản được gán đơn</label>
                        <select name="assigned_account_group_id" id="assigned_account_group_id" class="form-control"></select>
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
                    <button class="btn btn-default HAS_SHOW_HISTORY_ORDER" onclick="viewOrderRevision($('#noteOrderId').html(),false);"> <i class="glyphicon glyphicon-time"></i> Lịch sử đơn hàng</button>
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
                <div class="">
                    <select  name="export_type" id="export_type" class="form-control">
                        <option value="">Theo đơn hàng</option>
                        <option value="1">Theo sản phẩm</option>
                    </select>
                    <!--IF:system_cond_hide_phone_number([[=show_phone_number_excel_order=]]==1)-->
                    <br>
                    <input type="checkbox" name="getFullPhoneNumber" id="getFullPhoneNumber"> Không ẩn số điện thoại

                    <!--ELSE-->
                    <br>
                    <input type="checkbox" disabled="disabled"> Không ẩn số điện thoại
                    <!--/IF:system_cond_hide_phone_number-->
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('ALL');" class="btn btn-warning" style="width: 100%"><i class="fa fa-download"></i> Xuất tất cả <span id="totalOrderByListLabel badge"></span></button>
                            <div class="export-notice"><b>Lưu ý:</b> Xuất tối đa 2000 đơn kể từ trang hiện tại</div>
                        </div>
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('SELECTED');" class="btn btn-success" style="width: 100%"><i class="fa fa-download"></i> Xuất đơn được chọn <span class="totalOrder badge"></span></button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Gán phân oại sản phẩm, khách hàng-->
<div id="assignBundleCustomerGroupModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-success" type="assign-customer-group">
                    Gán nhóm khách hàng
                </h4>
                <h4 class="modal-title text-success" type="assign-bundle">
                    Gán Phân loại sản phẩm
                </h4>
            </div>
            <div class="modal-body">
                <div class="flex" type="assign-customer-group">
                    <assign-customer-group></assign-customer-group>
                    <button type="button" class="btn btn-primary">Thực hiện</button>
                </div>
                <div class="flex" type="assign-bundle">
                    <assign-bundle></assign-bundle>
                    <button type="button" class="btn btn-primary">Thực hiện</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal gui excel nha van chuyen -->
<div id="mdlSendMailToCarrier" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-success">
                    Gửi email cho nhà vận chuyển
                </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <select  name="slSendToCarrier" id="slSendToCarrier" class="form-control">
                        <option value="">Chọn nhà vận chuyển</option>
                    </select>
                    <!--IF:system_cond_hide_phone_number([[=show_phone_number_excel_order=]]==1)-->
                    <br>
                    <input type="checkbox" name="getFullPhoneNumberCarrier" id="getFullPhoneNumberCarrier"> Không ẩn số điện thoại
                    <!--ELSE-->
                    <br>
                    <input type="checkbox" disabled="disabled"> Không ẩn số điện thoại
                    <!--/IF:system_cond_hide_phone_number-->
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('ALL', 'send_mail_carrier');" class="btn btn-warning" style="width: 100%"><i class="fa fa-download"></i> Gửi tất cả <span id="totalOrderByListLabel badge"></span></button>
                            <div class="export-notice"><b>Lưu ý:</b> Gửi tối đa 2000 đơn kể từ trang hiện tại</div>
                        </div>
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('SELECTED', 'send_mail_carrier');" class="btn btn-success" style="width: 100%"><i class="fa fa-download"></i> Gửi đơn được chọn <span class="totalOrder badge"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<input type="hidden" id="hasDataCarrier" name="hasDataCarrier" value="0">
<!-- end modal gui excel nha van chuyen -->
<!-- modal gui file in don -->
<div id="sendMailFilePrintOrder" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 650px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-success">
                    Gửi email danh sách File In đơn cho Bộ phận kho
                </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <div class="row">
                        <div class="col-xs-4">
                            <select  name="SendSizeEmailWarehouse" id="SendSizeEmailWarehouse" class="form-control">
                                <option value="0" selected="">Khổ ngang</option>
                                <option value="1">Khổ dọc</option>
                            </select>
                            <input type="hidden" name="" id="sizeWarehouseCheck" value="">
                        </div>
                        <div class="col-xs-4">
                            <select  name="SendEmailWarehouse" id="SendEmailWarehouse" class="form-control">
                                <option value="" data-email="">Chọn kho</option>
                            </select>
                            <input type="hidden" name="" id="emailWarehouseCheck" value="">
                            <input type="hidden" name="" id="nameWarehouseCheck" value="">
                        </div>
                        <div class="col-xs-4">
                            <select  name="SendEmailFileCarrier" id="SendEmailFileCarrier" class="form-control">
                                <option value="">Chọn nhà vận chuyển</option>
                            </select>
                            <input type="hidden" name="" id="nameCarrierCheck" value="">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6">
                            <?php if (AdminOrders::$show_phone_number_print_order == 1 || is_group_owner()): ?>
                            <input type="checkbox" name="getFullPhone" id="getFullPhone" value="0"> Không ẩn số điện thoại
                            <?php endif; ?>
                        </div>
                        <div class="col-xs-6">
                            <?php if ((AdminOrders::$quyen_xuat_kho && AdminOrders::$quyen_indon) || is_group_owner()): ?>
                            <input type="checkbox" name="attachStockInvoice" id="attachStockInvoice" value="0"> Gửi kèm Phiếu Xuất Kho
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12">
                            <button type="button" onclick="sendMailPrint('SELECTED', 'send_mail_file');" class="btn btn-success sendMailPrint" style="width: 100%"><i class="fa fa-download"></i> Gửi đơn được chọn <span class="totalOrder badge"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style>
    #btn-send-mail-warehouse{position: relative; }
    #btn-send-mail-warehouse img{width: 34px; height: 34px; position: absolute; left: 0; top: 0; display: none;}
</style>

<!-- end modal gui excel nha van chuyen -->
<!--modal xuat excel van chuyen-->
<div id="mdlExportExcelCarrier" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="width: 450px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title text-success">
                    Xuất excel vận chuyển
                </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <!--IF:system_cond_hide_phone_number([[=show_phone_number_excel_order=]]==1)-->
                    <br>
                    <input type="checkbox" name="getFullPhoneNumberExportExcelCarrier" id="getFullPhoneNumberExportExcelCarrier"> Không ẩn số điện thoại
                    <!--ELSE-->
                    <br>
                    <input type="checkbox" disabled="disabled"> Không ẩn số điện thoại
                    <!--/IF:system_cond_hide_phone_number-->
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('ALL', 'export_excel_carrier');" class="btn btn-warning" style="width: 100%"><i class="fa fa-download"></i> Xuất tất cả <span id="totalOrderByListLabel badge"></span></button>
                            <div class="export-notice"><b>Lưu ý:</b> Gửi tối đa 2000 đơn kể từ trang hiện tại</div>
                        </div>
                        <div class="col-xs-6">
                            <button type="button" onclick="exportExcel('SELECTED', 'export_excel_carrier');" class="btn btn-success" style="width: 100%"><i class="fa fa-download"></i> Xuất đơn được chọn <span class="totalOrder badge"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--end modal xuat excel van chuyen-->

<link rel="stylesheet" href="assets/vissale/css/jquery-confirm.css">
<script src="assets/vissale/js/jquery-confirm.js"></script>
<script src="assets/vissale/js/jquery.countdown.min.js"></script>
<!-- tham khao: https://craftpip.github.io/jquery-confirm/ -->
<script>
    // Số page hiện tại
    let CURRENT_PAGE = 1;

    const DUPLICATE_FILTER = JSHELPER.render.select({
        data: {0: 'Chọn', 1: 'Số trùng', 2: 'Số thường'},
        selectAttrs: {name: 'duplicate', id: 'duplicate', class: 'form-control', onchange: "ReloadList(1);"},
        selected: '<?=URL::iget('duplicate')?>'
    }).mount('DUPLICATE_FILTER');

    const PRINTED_ORDER = JSHELPER.render.select({
        data: {0: 'Tất cả', 1: 'Đơn đã in', 2: 'Đơn chưa in'},
        selectAttrs: {name: 'printed', id: 'printed', class: 'form-control', onchange: "ReloadList(1);"},
        selected: '<?=URL::iget('duplicate')?>'
    }).mount('PRINTED_ORDER');

    /**
     * Returns an integer representation of the object.
     *
     * @return     {<type>}  Integer representation of the object.
     */
    String.prototype.toInt = function(){
        return parseInt(this.replace(/\D/g, '')) || 0;
    };

    // Bien này sẽ được dùng trong list_ajax để lưu trữ thông tin thống kê đơn hàng
    let ORDERS_STATISTICS = {};
    /**
     * Loads an order duplicate.
     *
     * @param      {<type>}  element  The element
     */
    const loadOrderDuplicateID = {};
    const loadOrderDuplicate  = function(element)
    {
        $('select#duplicate').prop('selectedIndex',0);
        loadOrderDuplicateID.order_id = element.dataset.id;
        loadOrderDuplicateID.order_id_duplicate = element.dataset.content;
        $('.popover ').hide();
        ReloadList();
        $('#reset_duplicate_filter').css('display', '');
    }

    // Lọc theo ngày xử lí đơn hàng
    const FILTER_DATES = {
        ngay_chuyen_hoan_checkbox: ['#datetimepicker_chuyen_hoan_from', '#datetimepicker_chuyen_hoan_to'],
        ngay_tra_hang_checkbox: ['#datetimepicker_tra_hang_from', '#datetimepicker_tra_hang_to']
    }

    $.each(FILTER_DATES, function(checkbox, inputSelectors){
        $(inputSelectors.join(',')).datetimepicker({format: 'DD/MM/YYYY'})
        .on('dp.change', function(){
            console.log($('#' + checkbox))
            $('#' + checkbox).is(':checked') && ReloadList(1)
        });
    })

    // Lọc giá đơn hàng
    let FILTER_PRICE;
    $(function(){
        FILTER_PRICE = (function(){
            $('#stylesheet').append(`
                .filter-price input.form-control {font-size: 13px;padding: 0 0px;flex-grow: 1;border-radius: 0;text-align: right;border-color: #d2d6de !important;border-color: #d2d6de !important;border-right: 0;}
                .filter-price .min, .filter-price .max {display: flex;align-items: center;width: 130px;flex-shrink: 0;}
                .filter-price .min{margin-right: 15px}
                .filter-price .min:before, .filter-price .max:before {content: 'từ';height: 34px;display: flex;padding: 0 3px;align-items: center;background: #f4f4f4;border-top-left-radius: 3px;border-bottom-left-radius: 3px;font-size: 13px; font-weight: bold}
                .filter-price .max:before {content: 'đến';}
                .filter-price .min:after, .filter-price .max:after {content: '.000đ';right: 0px;height: 34px;display: flex;padding: 0 3px 0 0;align-items: center;border-top-right-radius: 3px;border-bottom-right-radius: 3px;font-size: 13px; border: 1px solid #d2d6de; border-left: 0; line-height: 1; color: #999;}
                .input-wrapper{display: flex}
                .filter-price .error {color: red; font-weight: bold}
                .filter-price {z-index: 9;}
                `)

            const MAXIMUM_PRICE = 10000000,
                MINIMUM_PRICE = 0,
                maxEl = $('.filter-price .max input'),
                minEl = $('.filter-price .min input'),

                /**
                 * Determines if minimum gt maximum.
                 *
                 * @return     {boolean}  True if minimum gt maximum, False otherwise.
                 */
                isMinGtMax = () => minEl.val().toInt() - maxEl.val().toInt() > 0,

                /**
                 * { lambda_description }
                 *
                 * @param      {Array}   el
                 * @return     {<type>}
                 */
                elInValid = (...el) => el.map(e => e.addClass('error')),

                /**
                 * { lambda_description }
                 *
                 * @param      {Array}   el
                 * @return     {<type>}
                 */
                elValid = (...el) => el.map(e => e.removeClass('error')),

                /**
                 * Called on keyup.
                 */
                onInput = function() {
                    const val = this.value.toInt();

                    this.value = val.toString().split(/(?=(?:\d{3})+(?:\.|$))/g).join('.');

                    if(isMinGtMax() || val > MAXIMUM_PRICE || val < MINIMUM_PRICE){
                        return elInValid(minEl, maxEl)
                    }

                    elValid(minEl, maxEl)
                },

                /**
                 * Called on change.
                 *
                 * @return     {<type>}
                 */
                onChange = function(){
                    const val = this.value.toInt();

                    if(isMinGtMax() || val > MAXIMUM_PRICE || val < MINIMUM_PRICE){
                        elInValid(minEl, maxEl)
                    }else{
                        elValid(minEl, maxEl)
                    }

                    ReloadList(0)
                }

            minEl.on('input', onInput)
            maxEl.on('input', onInput)

            minEl.change(onChange)
            maxEl.change(onChange)

            return {
                values: () => ({
                    min_price: minEl.val().toInt() * 1000,
                    max_price: maxEl.val().toInt() * 1000
                })
            }
        })()
    })

    $(function(){
        $('#reset_duplicate_filter').click(function(){
            delete loadOrderDuplicateID['order_id'];
            delete loadOrderDuplicateID['order_id_duplicate'];
            $('select#duplicate').prop('selectedIndex',0);
            $('#reset_duplicate_filter').css('display', 'none');
            ReloadList();
        })
    })

    $(function(){
        $('#stylesheet').append(`
            #assignBundleCustomerGroupModal select{ margin-right: 15px !important}
            [type="assign-bundle"],[type="assign-customer-group"]{display:  none}
            .assign-customer-group [type="assign-customer-group"]{ display: flex }
            .assign-bundle [type="assign-bundle"]{ display: flex}
        `);
        const modal = $('#assignBundleCustomerGroupModal');
        const ASSIGN_CUSTOMER_GROUP = JSHELPER.render.select({
            HTML_COLUMN: 'name',
            data: {'': 'Chọn nhóm KH', '-1': 'Bỏ chia/ bỏ gán', ...<?=json_encode($this->map['customer_groups'] ? $this->map['customer_groups'] : [])?>},
            selectAttrs: {class: 'form-control'},
            selected: ''
        }).mount('assign-customer-group');

        const ASSIGN_BUNDLE = JSHELPER.render.select({
            HTML_COLUMN: 'name',
            data: {'': 'Chọn phân loại SP', '-1': 'Bỏ chia/ bỏ gán', ...<?=json_encode($this->map['bundles'] ? $this->map['bundles'] : [])?>},
            selectAttrs: {class: 'form-control'},
            selected: ''
        }).mount('assign-bundle');

        modal.on('hide.bs.modal', function(){
            modal.removeClass('assign-customer-group')
            modal.removeClass('assign-bundle')
        })

        let orderIDs = [];
        modal.on('show.bs.modal', function(){
            orderIDs = $('[name="selected_ids[]"]:checked').get().map(e => parseInt(e.value) || 0);
        })

        $('#assign-customer-group-btn').click(function(){
            modal.addClass('assign-customer-group');
            modal.modal('show');
        })

        $('#assign-bundle-btn').click(function(){
            modal.addClass('assign-bundle');
            modal.modal('show');
        })


        $('[type="assign-bundle"] button').click(async function(){
            if(!orderIDs.length){
                return alert('Vui lòng chọn đơn hàng !');
            }

            if(!ASSIGN_BUNDLE.value){
                return alert('Vui lòng phân loại SP !');
            }

            if(!confirm('Bạn chắc chắn muốn' + (ASSIGN_BUNDLE.value < 0 ? ' hủy' : '') + '  gán phân loại sản phẩm cho ' + orderIDs.length + ' đơn hàng ?')){
                return;
            }

            const prefix = ASSIGN_BUNDLE.value < 0 ? 'Hủy gán' : 'Gán';
            try{
                let response = await $.post('/index062019.php?page=admin_orders', {
                    form_block_id: blockId,
                    cmd: 'assign_bundle',
                    order_ids: orderIDs.join(','),
                    bundle: ASSIGN_BUNDLE.value
                })

                switch(response.status){
                    case 'success':
                        alert(prefix + ' phân loại sản phẩm thành công')
                        modal.modal('toggle');
                        $('#printModal').modal('toggle');
                        ReloadList(1);
                        break;

                    default:
                        alert(prefix + ' phân loại sản phẩm thất bại')
                }
            }catch(e){
                alert('SERVER ERROR!')
            }
        })

        $('[type="assign-customer-group"] button').click(async function(){
            if(!orderIDs.length){
                return alert('Vui lòng chọn đơn hàng !');
            }

            if(!ASSIGN_CUSTOMER_GROUP.value){
                return alert('Vui lòng nhóm KH !');
            }

            if(!confirm('Bạn chắc chắn muốn' + (ASSIGN_CUSTOMER_GROUP.value < 0 ? ' hủy' : '') + ' gán nhóm khách hàng cho ' + orderIDs.length + ' đơn hàng ?')){
                return;
            }

            const prefix = ASSIGN_CUSTOMER_GROUP.value < 0 ? 'Hủy gán' : 'Gán';
            try{
                let response = await $.post('/index062019.php?page=admin_orders', {
                    form_block_id: blockId,
                    cmd: 'assign_customer_group',
                    order_ids: orderIDs.join(','),
                    customer_group: ASSIGN_CUSTOMER_GROUP.value
                })

                switch(response.status){
                    case 'success':
                        alert(prefix + ' nhóm khách hàng thành công')
                        modal.modal('toggle');
                        $('#printModal').modal('toggle');
                        ReloadList(1);
                        break;

                    default:
                        alert(prefix + ' nhóm khách hàng thất bại')
                }
            }catch(e){
                alert('SERVER ERROR!')
            }
        })
    })


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

    let IS_ALL = false;
    $(document).ready(function(){
        $('.select2').select2({
            dropdownAutoWidth : true
        });
        $('.order-status').click(function() {
            if ($(this).is(":checked")) {
                $('.step-ward-arrow').removeClass('active')
                $(this).closest('.step-ward-box').siblings('.step-ward-arrow').addClass('active');
                changeStatus(false);
                $('#orderStatusModal').modal('hide');
            }
        });
        $('.multiple-select-account').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            nonSelectedText: 'Chọn nhóm',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });

         $('.multiple-select-home-network').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            nonSelectedText: 'Chọn nhà mạng',
        });

        $('#orderStatusModal').on('hidden.bs.modal', function (e) {
            $('.order-status').prop('checked', false)
        });

        $('#checkbox').on('click',function(){
            if($("#checkbox").is(':checked')){
                IS_ALL = true;
                $('#load_data_checkbox').css({'display':'block'});
                $('#un_load_data_checkbox').css({'display':'none'});

                $("#assigned_account_id").val('').trigger('change')
                $("#search_account_id").val('').trigger('change')
                $("#mkt_account_id").val('').trigger('change')
                $("#confirmed_account_id").val('').trigger('change')
                $("#upsale_account_id").val('').trigger('change')
            }else{
                IS_ALL = false;
                $('#load_data_checkbox').css({'display':'none'});
                $('#un_load_data_checkbox').css({'display':'block'});

                $("#all_assigned_account_id").val('').trigger('change')
                $("#all_search_account_id").val('').trigger('change')
                $("#all_mkt_account_id").val('').trigger('change')
                $("#all_confirmed_account_id").val('').trigger('change')
                $("#all_upsale_account_id").val('').trigger('change')
            }

            ReloadList();
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
        /*$('#autoAssignOrder').on('click', function() {
            var $this = $(this);
            $this.val('Vui lòng đợi.. .');
            setTimeout(function() {
                $this.val('reset');
            }, 8000);
        });*/
        $('#item-list .bor').css({'height':'450px'});
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
        let assigned_account_id = '';
        let search_account_id = '';
        let mkt_account_id = '';
        let confirmed_account_id = '';
        let upsale_account_id = '';

        if(!IS_ALL){
            assigned_account_id = $('#assigned_account_id').val();
            search_account_id = $('#search_account_id').val();
            mkt_account_id = $('#mkt_account_id').val();
            confirmed_account_id = $('#confirmed_account_id').val();
            upsale_account_id = $('#upsale_account_id').val();
        }else{
            assigned_account_id = $('#all_assigned_account_id').val().replace(/\s/g, '').split('(')[0];
            search_account_id = $('#all_search_account_id').val().replace(/\s/g, '').split('(')[0];
            mkt_account_id = $('#all_mkt_account_id').val().replace(/\s/g, '').split('(')[0];
            confirmed_account_id = $('#all_confirmed_account_id').val().replace(/\s/g, '').split('(')[0];
            upsale_account_id = $('#all_upsale_account_id').val().replace(/\s/g, '').split('(')[0];
        }

        var myData = {
            'load_ajax':'1',
            term_sdt:$('#term_sdt').val(),
            term_order_id:$('#term_order_id').val(),
            term_ship_id:$('#term_ship_id').val(),
            'c_n':$('#customer_name').val(),
            'customer_id':$('#customer_id').val(),
            'printed':$('#printed').val(),
            'page_id':$('#page_id').val()?$('#page_id').val():'',
            'fb_post_id':$('#fb_post_id').val()?$('#fb_post_id').val():'',
            'source_id':$('#source_id').val()?$('#source_id').val():'',
            'source_shop_id':$('#source_shop_id').val()?$('#source_shop_id').val():'',
            'type':$('#type').val()?$('#type').val():'',
            'bundle_id':$('#bundle_id').val()?$('#bundle_id').val():'',
            'product_code':$('#product_code').val()?$('#product_code').val():'',
            'customer_group_id':$('#customer_group_id').val()?$('#customer_group_id').val():'',
            'search_account_id':search_account_id,
            'mkt_account_id':mkt_account_id,
            upsale_account_id: upsale_account_id,
            'assigned_account_id':assigned_account_id,
            'confirmed_account_id':confirmed_account_id,
            'account_group_id':$('#account_group_id').val()?$('#account_group_id').val():'',
            'home_network':$('#home_network').val()?$('#home_network').val():'',
            'search_group_id':$('#search_group_id').val()?$('#search_group_id').val():'',
            'item_per_page':$('#item_per_page').val()?$('#item_per_page').val():'',
            'duplicate':$('#duplicate').val()?$('#duplicate').val():'',
            ...FILTER_PRICE.values(),
            block_id:blockId
        };

        if($('#ngay_tao_checkbox').is(':checked')==true){
            myData['ngay_tao_to'] = $('#ngay_tao_to').val();
            myData['ngay_tao_from'] = $('#ngay_tao_from').val();
        }
        if($('#ngay_chia_checkbox').is(':checked')==true){
            myData['ngay_chia_to'] = $('#ngay_chia_to').val();
            myData['ngay_chia_from'] = $('#ngay_chia_from').val();
        }
        if($('#ngay_xn_checkbox').is(':checked')==true){
            myData['ngay_xn_from'] = $('#ngay_xn_from').val();
            myData['ngay_xn_to'] = $('#ngay_xn_to').val();
        }
        if($('#ngay_chuyen_kt_checkbox').is(':checked')==true){
            myData['ngay_chuyen_kt_from'] = $('#ngay_chuyen_kt_from').val();
            myData['ngay_chuyen_kt_to'] = $('#ngay_chuyen_kt_to').val();
        }
        if($('#ngay_chuyen_checkbox').is(':checked')==true){
            myData['ngay_chuyen_from'] = $('#ngay_chuyen_from').val();
            myData['ngay_chuyen_to'] = $('#ngay_chuyen_to').val();
        }
        if($('#ngay_thanh_cong_checkbox').is(':checked')==true){
            myData['ngay_thanh_cong_from'] = $('#ngay_thanh_cong_from').val();
            myData['ngay_thanh_cong_to'] = $('#ngay_thanh_cong_to').val();
        }

        if($('#ngay_chuyen_hoan_checkbox').is(':checked')==true){
            myData['ngay_chuyen_hoan_from'] = $('#ngay_chuyen_hoan_from').val();
            myData['ngay_chuyen_hoan_to'] = $('#ngay_chuyen_hoan_to').val();
        }

        if($('#ngay_tra_hang_checkbox').is(':checked')==true){
            myData['ngay_tra_hang_from'] = $('#ngay_tra_hang_from').val();
            myData['ngay_tra_hang_to'] = $('#ngay_tra_hang_to').val();
        }


        myData['shipping_services'] = '';
        <!--LIST:shipping_services-->
        if($('#shipping_services_[[|shipping_services.id|]]').is(':checked')){
            myData['shipping_services'] += (myData['shipping_services']?',':'')+[[|shipping_services.id|]];
        }
        <!--/LIST:shipping_services-->
        myData['status'] = '';
        <!--LIST:status-->
        if($('#list_status_[[|status.id|]]').is(':checked')){
            myData['status'] += (myData['status']?',':'')+[[|status.id|]];
        }
        <!--/LIST:status-->
        //myData['is_inner_city'] = '';
        if($('#is_inner_city').val()==1){
            myData['is_inner_city'] = $('#is_inner_city').val();
        }
        if($('#order_by').val()!=''){
            myData['order_by'] = $('#order_by').val();
        }
        if($('#order_by_dir').val()!=''){
            myData['order_by_dir'] = $('#order_by_dir').val();
        }
        return myData;
    }
    var t;
    function ReloadList(pageNo){
        // Giữ lại thông tin số page hiện tại
        CURRENT_PAGE = pageNo

        let assigned_account_id = '';
        let search_account_id = '';
        let mkt_account_id = '';
        let confirmed_account_id = '';
        let upsale_account_id = '';

        if(!IS_ALL){
            assigned_account_id = $('#assigned_account_id').val();
            search_account_id = $('#search_account_id').val();
            mkt_account_id = $('#mkt_account_id').val();
            confirmed_account_id = $('#confirmed_account_id').val();
            upsale_account_id = $('#upsale_account_id').val();
        }else{
            assigned_account_id = $('#all_assigned_account_id').val().replace(/\s/g, '').split('(')[0];
            search_account_id = $('#all_search_account_id').val().replace(/\s/g, '').split('(')[0];
            mkt_account_id = $('#all_mkt_account_id').val().replace(/\s/g, '').split('(')[0];
            confirmed_account_id = $('#all_confirmed_account_id').val().replace(/\s/g, '').split('(')[0];
            upsale_account_id = $('#all_upsale_account_id').val().replace(/\s/g, '').split('(')[0];
        }

        var myData = {
            'load_ajax':'1',
            term_sdt:$('#term_sdt').val(),
            term_order_id:$('#term_order_id').val(),
            term_ship_id:$('#term_ship_id').val(),
            'c_n':$('#customer_name').val(),
            'customer_id':$('#customer_id').val(),
            'printed':$('#printed').val(),
            'page_id':$('#page_id').val()?$('#page_id').val():'',
            'fb_post_id':$('#fb_post_id').val()?$('#fb_post_id').val():'',
            'source_id':$('#source_id').val()?$('#source_id').val():'',
            'source_shop_id':$('#source_shop_id').val()?$('#source_shop_id').val():'',
            'customer_group_id':$('#customer_group_id').val()?$('#customer_group_id').val():'',
            'type':$('#type').val()?$('#type').val():'',
            'page_no':pageNo,
            'bundle_id':$('#bundle_id').val()?$('#bundle_id').val():'',
            'product_code':$('#product_code').val()?$('#product_code').val():'',
            'account_group_id':$('#account_group_id').val()?$('#account_group_id').val():'',
            'home_network':$('#home_network').val()?$('#home_network').val():'',
            'search_account_id':search_account_id,
            'mkt_account_id':mkt_account_id,
            upsale_account_id: upsale_account_id,
            'assigned_account_id':assigned_account_id,
            'confirmed_account_id':confirmed_account_id,
            'search_group_id':$('#search_group_id').val()?$('#search_group_id').val():'',
            'item_per_page':$('#item_per_page').val()?$('#item_per_page').val():'',
            'warehouse_id_filter':$('#warehouse_id_filter').val()?$('#warehouse_id_filter').val():'',
            'duplicate':$('#duplicate').val()?$('#duplicate').val():'',
            block_id:blockId
        };

        myData = {...myData, ...loadOrderDuplicateID, ...ORDERS_STATISTICS,...FILTER_PRICE.values()};

        if($('#ngay_tao_checkbox').is(':checked')==true) {
	        myData['ngay_tao_to'] = $('#ngay_tao_to').val();
	        myData['ngay_tao_from'] = $('#ngay_tao_from').val();

	        let checkDiff = checkDateValid(myData['ngay_tao_from'], myData['ngay_tao_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_tao_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_tao_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_tao_from'], myData['ngay_tao_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_tao_from'] = checkDiff;
		    //     document.getElementById("ngay_tao_from").value = checkDiff;
	        // }
        }
        if($('#ngay_chia_checkbox').is(':checked')==true){
            myData['ngay_chia_to'] = $('#ngay_chia_to').val();
            myData['ngay_chia_from'] = $('#ngay_chia_from').val();

	        let checkDiff = checkDateValid(myData['ngay_chia_from'], myData['ngay_chia_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_chia_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_chia_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_chia_from'], myData['ngay_chia_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_chia_from'] = checkDiff;
		    //     document.getElementById("ngay_chia_from").value = checkDiff;
	        // }
        }
        if($('#ngay_xn_checkbox').is(':checked')==true){
            myData['ngay_xn_from'] = $('#ngay_xn_from').val();
            myData['ngay_xn_to'] = $('#ngay_xn_to').val();

	        let checkDiff = checkDateValid(myData['ngay_xn_from'], myData['ngay_xn_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_xn_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_xn_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_xn_from'], myData['ngay_xn_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_xn_from'] = checkDiff;
		    //     document.getElementById("ngay_xn_from").value = checkDiff;
	        // }
        }
        if($('#ngay_chuyen_kt_checkbox').is(':checked')==true){
            myData['ngay_chuyen_kt_from'] = $('#ngay_chuyen_kt_from').val();
            myData['ngay_chuyen_kt_to'] = $('#ngay_chuyen_kt_to').val();

	        let checkDiff = checkDateValid(myData['ngay_chuyen_kt_from'], myData['ngay_chuyen_kt_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_chuyen_kt_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_chuyen_kt_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_chuyen_kt_from'], myData['ngay_chuyen_kt_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_chuyen_kt_from'] = checkDiff;
		    //     document.getElementById("ngay_chuyen_kt_from").value = checkDiff;
	        // }
        }
        if($('#ngay_chuyen_checkbox').is(':checked')==true){
            myData['ngay_chuyen_from'] = $('#ngay_chuyen_from').val();
            myData['ngay_chuyen_to'] = $('#ngay_chuyen_to').val();

	        let checkDiff = checkDateValid(myData['ngay_chuyen_from'], myData['ngay_chuyen_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_chuyen_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_chuyen_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_chuyen_from'], myData['ngay_chuyen_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_chuyen_from'] = checkDiff;
		    //     document.getElementById("ngay_chuyen_from").value = checkDiff;
	        // }
        }

        if($('#ngay_thanh_cong_checkbox').is(':checked')==true){
            myData['ngay_thanh_cong_from'] = $('#ngay_thanh_cong_from').val();
            myData['ngay_thanh_cong_to'] = $('#ngay_thanh_cong_to').val();

	        let checkDiff = checkDateValid(myData['ngay_thanh_cong_from'], myData['ngay_thanh_cong_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_thanh_cong_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_thanh_cong_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_thanh_cong_from'], myData['ngay_thanh_cong_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_thanh_cong_from'] = checkDiff;
		    //     document.getElementById("ngay_thanh_cong_from").value = checkDiff;
	        // }
        }
        if($('#ngay_thu_tien_checkbox').is(':checked')==true){
            myData['ngay_thu_tien_from'] = $('#ngay_thu_tien_from').val();
            myData['ngay_thu_tien_to'] = $('#ngay_thu_tien_to').val();

	        let checkDiff = checkDateValid(myData['ngay_thu_tien_from'], myData['ngay_thu_tien_to']);
	        if (!checkDiff) {
		        alert('Vui lòng chọn thời gian chính xác!');
		        document.getElementById("ngay_thu_tien_to").value = moment().format('DD/MM/YYYY');
		        document.getElementById("ngay_thu_tien_from").value = moment().subtract(3, "days").format('DD/MM/YYYY');
	        }

	        // let checkDiff = checkDateDiff(myData['ngay_thu_tien_from'], myData['ngay_thu_tien_to']);
	        // if (checkDiff) {
		    //     alert('Vui lòng chọn thời gian xem đơn hàng trong tối đa 6 tháng!');
		    //     myData['ngay_thu_tien_from'] = checkDiff;
		    //     document.getElementById("ngay_thu_tien_from").value = checkDiff;
	        // }
        }

        if($('#ngay_chuyen_hoan_checkbox').is(':checked')==true){
            myData['ngay_chuyen_hoan_from'] = $('#ngay_chuyen_hoan_from').val();
            myData['ngay_chuyen_hoan_to'] = $('#ngay_chuyen_hoan_to').val();
        }

        if($('#ngay_tra_hang_checkbox').is(':checked')==true){
            myData['ngay_tra_hang_from'] = $('#ngay_tra_hang_from').val();
            myData['ngay_tra_hang_to'] = $('#ngay_tra_hang_to').val();
        }

        myData['shipping_services'] = '';
        <!--LIST:shipping_services-->
        if($('#shipping_services_[[|shipping_services.id|]]').is(':checked')){
            myData['shipping_services'] += (myData['shipping_services']?',':'')+[[|shipping_services.id|]];
        }
        <!--/LIST:shipping_services-->
        myData['status'] = '';
        <!--LIST:status-->
        if($('#list_status_[[|status.id|]]').is(':checked')){
            myData['status'] += (myData['status']?',':'')+[[|status.id|]];
        }
        <!--/LIST:status-->
        /*myData['dau_so'] = '';
        for(var i = 1;i<=6;i++){
            if($('#dau_so_'+i).is(':checked')){
                myData['dau_so'] += (myData['dau_so']?',':'')+$('#dau_so_'+i).val();
            }
        }*/
        //myData['is_inner_city'] = '';
        if($('#city_id').val()!=''){
            myData['city_id'] = $('#city_id').val();
        }
        if($('#is_inner_city').is(':checked')){
            myData['is_inner_city'] = 1;
        }
        if($('#order_by').val()!=''){
            myData['order_by'] = $('#order_by').val();
        }
        if($('#order_by_dir').val()!=''){
            myData['order_by_dir'] = $('#order_by_dir').val();
        }
        if($('#saved_date_filter').val()!=''){
            let saved_date = $('#saved_date_filter').val();
            let from = saved_date.substring(0, 10);
            let to = saved_date.substring(13, 23);

            var fromParts = from.split("/");
            var fromObject = new Date(+fromParts[2], fromParts[1] - 1, +fromParts[0]);
            var toParts = to.split("/");
            var toObject = new Date(+toParts[2], toParts[1] - 1, +toParts[0]);

            if (fromObject instanceof Date && toObject instanceof Date && fromParts[0] < 32 && toParts[0] < 32 && fromParts[1] < 13 && toParts[1] < 13) {
                if ((toObject.getTime() - fromObject.getTime()) < 2678400001) {
                    myData['saved_date_from'] = from;
                    myData['saved_date_to'] = to;
                } else {
                    $('#saved_date_filter').val('');
                    document.getElementById("saved_date_filter").style.border = "1px solid #d2d6de";
                }
            } else {
                $('#saved_date_filter').val('');
                document.getElementById("saved_date_filter").style.border = "1px solid #d2d6de";
            }
        }
        if($('#deliver_date_filter').val()!=''){
            let deliver_date = $('#deliver_date_filter').val();
            let from = deliver_date.substring(0, 10);
            let to = deliver_date.substring(13, 23);

            var fromParts = from.split("/");
            var fromObject = new Date(+fromParts[2], fromParts[1] - 1, +fromParts[0]);
            var toParts = to.split("/");
            var toObject = new Date(+toParts[2], toParts[1] - 1, +toParts[0]);

            if (fromObject instanceof Date && toObject instanceof Date && fromParts[0] < 32 && toParts[0] < 32 && fromParts[1] < 13 && toParts[1] < 13) {
                if ((toObject.getTime() - fromObject.getTime()) < 2678400001) {
                    myData['deliver_date_from'] = from;
                    myData['deliver_date_to'] = to;
                } else {
                    $('#deliver_date_filter').val('');
                    document.getElementById("deliver_date_filter").style.border = "1px solid #d2d6de";
                }
            } else {
                $('#deliver_date_filter').val('');
                document.getElementById("deliver_date_filter").style.border = "1px solid #d2d6de";
            }
        }
        <?php if($ids=Url::get('ids')){?>
        myData = {
            'load_ajax':'1',
            'ids':'<?php echo $ids;?>',
            'item_per_page':1000,
            block_id:blockId
        };
        <?php }?>
        if (typeof t !== 'undefined') {
            clearTimeout(t);
        }
        $( "#module_"+blockId ).html('<div id="item-list" style="height:450px;padding:20px;"><div class="overlay text-info">\n' +
            '        <div class="spin-loader"></div> \n' +
            '      </div></div>');
        t = setTimeout(function (){
            $.ajax({
                method: "POST",
                url: 'form.php',
                data : myData,
                beforeSend: function(){

                },
                success: function(content){
                    content = $.trim(content);
                    $( "#module_"+blockId ).html(content);
                    $('#item-list .bor').css({'height':'450px'});
                },
                error: function(){
                    alert('Lỗi tải danh sách đơn hàng. Bạn vui lòng kiểm tra lại kết nối!');
                    location.reload();
                }
            });
        },1200);
    }
    function validateDate(){
        if($('#saved_date_filter').val()!=''){
            let saved_date = $('#saved_date_filter').val();
            let from = saved_date.substring(0, 10);
            let to = saved_date.substring(13, 23);

            var fromParts = from.split("/");
            var fromObject = new Date(+fromParts[2], fromParts[1] - 1, +fromParts[0]);
            var toParts = to.split("/");
            var toObject = new Date(+toParts[2], toParts[1] - 1, +toParts[0]);

            if (fromObject instanceof Date && toObject instanceof Date && fromParts[0] < 32 && toParts[0] < 32 && fromParts[1] < 13 && toParts[1] < 13) {
                if ((toObject.getTime() - fromObject.getTime()) < 2678400001) {
                    document.getElementById("saved_date_filter").style.border = "1px solid #d2d6de";
                } else {
                    document.getElementById("saved_date_filter").style.border = "1px solid red";
                }
            } else {
                document.getElementById("saved_date_filter").style.border = "1px solid red";
            }
        }
        if($('#deliver_date_filter').val()!=''){
            let deliver_date = $('#deliver_date_filter').val();
            let from = deliver_date.substring(0, 10);
            let to = deliver_date.substring(13, 23);

            var fromParts = from.split("/");
            var fromObject = new Date(+fromParts[2], fromParts[1] - 1, +fromParts[0]);
            var toParts = to.split("/");
            var toObject = new Date(+toParts[2], toParts[1] - 1, +toParts[0]);

            if (fromObject instanceof Date && toObject instanceof Date && fromParts[0] < 32 && toParts[0] < 32 && fromParts[1] < 13 && toParts[1] < 13) {
                if ((toObject.getTime() - fromObject.getTime()) < 2678400001) {
                    document.getElementById("deliver_date_filter").style.border = "1px solid #d2d6de";
                } else {
                    document.getElementById("deliver_date_filter").style.border = "1px solid red";
                }
            } else {
                document.getElementById("deliver_date_filter").style.border = "1px solid red";
            }
        }
    }
    function checkDateValid(dateFromString, dateToString){
	    let dateFromParts = dateFromString.split("/");
	    let dateToParts = dateToString.split("/");
        if (dateFromParts.length == 3  && dateFromParts.length == 3) {
	        let strDateTo = dateToParts[1]+"/"+dateToParts[0]+"/"+dateToParts[2];
	        let strDateFrom = dateFromParts[1]+"/"+dateFromParts[0]+"/"+dateFromParts[2];

	        let date1 = new Date(strDateFrom);
	        let date2 = new Date(strDateTo);
	        if (!isNaN(date1.getTime()) && !isNaN(date2.getTime())) {
	        	if (date1.getTime() > 1286748610000 && date2.getTime() > 1286748610000) {
	        		if (date2.getTime() >= date1.getTime()) {
				        return true;
                    }
                }
            }
        }
	    return false;
    }
    function checkDateDiff(dateFromString, dateToString){
	    let dateFromParts = dateFromString.split("/");
	    let strDateFrom = dateFromParts[1]+"/"+dateFromParts[0]+"/"+dateFromParts[2];
	    //
	    let dateToParts = dateToString.split("/");
	    let strDateTo = dateToParts[1]+"/"+dateToParts[0]+"/"+dateToParts[2];

	    let date1 = new Date(strDateFrom);
	    let date2 = new Date(strDateTo);
	    let months = ((date2.getFullYear() - date1.getFullYear()) * 12) + (date2.getMonth() - date1.getMonth());

	    if (months > 5) {
		    date1.setMonth(date2.getMonth() - 5);
		    let dd = date1.getDate();
		    let mm = date1.getMonth()+1;
		    let yyyy = date2.getFullYear();
		    if (date1.getMonth() > date2.getMonth()) yyyy -= 1;
		    if(dd<10) { dd='0'+dd; }
		    if(mm<10) { mm='0'+mm; }
		    return "01/" + mm + "/" + yyyy;
	    }
	    return false;
    }
    function addGroup(){
        jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'add_group',
                'value':$('#group_name').val(),
                block_id:blockId
            },
            beforeSend: function(){
            },
            success: function(content){
                content = jQuery.trim(content);
                if(content=='EMPTY'){
                    alert('Bạn vui lòng nhập tên nhóm');
                } else if(content=='TRUE'){
                    alert('Bạn đã thêm  nhóm thành công');
                    $('#add_group_form').hide();
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

        if (accept_edit_transport == 'accept' && status == 8) {
            $('#new_ids').val($('#checked_order').val())
            $('#new-transport-form').submit()
            return;
        }

        if(status==false){
            // status =  $('#status_id').val();
            status = $('input.order-status:checked').val();
            if (status == undefined) {
                alert('Bạn chưa chọn trạng thái để cập nhật.')
                return;
            }
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
            success: function(response){
                if(typeof response !== 'object'){
                    alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                } else if(!response.data){
                    alert('Bạn đã thay đổi trạng thái thành công');
                    ReloadList(1);
                }else{
                    alert(response.data);
                    ReloadList(1);
                }

                $('#add_group_form').hide();
                $('#printModal').modal('toggle');
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function selectOne(obj){
        $('#SendEmailWarehouse').empty();
        $('#SendEmailFileCarrier').empty();
        $all =$('#ListAdminOrdersForm_all_checkbox').is(':checked');
        if($all && $(obj).is(':checked')==false){
            $('#ListAdminOrdersForm_all_checkbox').prop('checked',false);
        }
        $('.totalOrder').html($('.order-checkbox').length);
        updateSelectedIds();
    }
    function selectAll(obj){
        $checked = $(obj).is(':checked')?true:false;
        $('.order-checkbox').prop('checked',$checked);
        updateSelectedIds();
    }
    function updateSelectedIds(){
        $('#checked_order').val('');
        let $check = '';
        let $i=0;
        $('.order-checkbox').each(function(){
            if($(this).is(':checked')) {
                $check += ($check?',':'')+$(this).val();
                $i++;
            }
        });
        $('#checked_order').val($check);
        $('.totalOrder').html($i);
    }
    function directPrint(){
        if($('#checked_order').val()){
            $('#act').val('print');
            ListAdminOrdersForm.submit();
            //$('#ids').val();
            //window.location='<?php echo Url::build_current(array('act'=>'print'));?>&ids='+$('#checked_order').val();
        }else{
            alert('Bạn vui lòng chọn đơn hàng để in');
            return false;
        }
    }
    function assignOrders(group){
        if($('#checked_order').val()){
            if(group==1){// chia theo nhóm
                if(!$('#ass_account_group_id').val()){
                    alert('Bạn vụi lòng chọn nhóm để gán');
                    $('#ass_account_group_id').focus();
                    return false;
                }
                jQuery.ajax({
                    method: "POST",
                    url: 'form.php',
                    data : {
                        'cmd':'assign_order_by_group',
                        'account_group_id':$('#ass_account_group_id').val(),
                        'ids':$('#checked_order').val(),
                        block_id:blockId
                    },
                    beforeSend: function(){

                    },
                    success: function(content){
                        content = jQuery.trim(content);
                        if(content=='TRUE'){
                            alert('Bạn đã gán đơn hàng thành công');
                            $('#printModal').modal('toggle');
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
                //console.log($('#ass_account_id').val());
                if(!$('#ass_account_id').val()){
                    alert('Bạn vụi lòng chọn nhân viên để gán');
                    $('#ass_account_id').focus();
                    return false;
                }
                jQuery.ajax({
                    method: "POST",
                    url: 'form.php',
                    data : {
                        'cmd':'assign_order',
                        'account_id':$('#ass_account_id').val(),
                        'ids':$('#checked_order').val(),
                        block_id:blockId
                    },
                    beforeSend: function(){

                    },
                    success: function(content){
                        content = jQuery.trim(content);
                        if(content=='FALSE'){

                            alert('Đã xảy ra lỗi. Vui lòng kiểm tra lại.');
                        } else if(content=='CANCEL_ASSIGNMENT'){
                            alert('Bạn đã hủy gán đơn!');
                            $('#printModal').modal('toggle');
                            ReloadList(1);
                        } else if(content=='TRUE'){
                            alert('Bạn đã gán đơn hàng thành công');
                            $('#printModal').modal('toggle');
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
        if($('#checked_order').val()){
            if(!$('#ass_mkt_account_id').val()){
                alert('Bạn vụi lòng chọn nhân viên marketing để gán');
                $('#ass_mkt_account_id').focus();
                return false;
            }
            jQuery.ajax({
                method: "POST",
                url: 'form.php',
                data: {
                    'cmd': 'assign_mkt_order',
                    'account_id': $('#ass_mkt_account_id').val(),
                    'ids': $('#checked_order').val(),
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
                        alert($('#ass_mkt_account_id').val() == -1 ? 'Bạn đã hủy gán đơn thành công!' : 'Bạn đã gán đơn hàng thành công cho marketing');
                        $('#printModal').modal('toggle');
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
    /**
     * Gán upsale cho danh sách đơn hàng
     *
     * @return     {boolean}  { description_of_the_return_value }
     */
    function assignUpSaleOrders(){
        const orderIDs = $('#checked_order').val();
        if(!orderIDs){
            return alert('Bạn vui lòng chọn đơn hàng để thao tác');
        }

        const upsaleSelected = $('#ass_upsale_id').val();
        if(!upsaleSelected){
            $('#ass_mkt_account_id').focus();
            return alert('Bạn vụi lòng chọn nhân viên marketing để gán');
        }

        $.post('/form.php', {
                cmd: 'assign_upsale_order',
                account_id: upsaleSelected,
                ids: orderIDs,
                block_id: blockId
        })
        .done(function(res){
            if(typeof res != 'object'){
                return alert('Server Error !');
            }

            switch(res.status){
                case 'success':
                    alert(upsaleSelected == -1 ? 'Bạn đã HỦY gán upsale thành công!' : 'Bạn đã gán upsale thành công!');
                    $('#printModal').modal('toggle');
                    ReloadList(1);
                    break;

                default:
                    return alert((upsaleSelected == -1 ? 'HỦY ' : '') + 'Gán upsale thất bại, vui lòng kiểm tra lại hoặc liên hệ bộ phận hỗ trợ !');
                    break;
            }
        })
        .fail(function(errorMsg){
            return alert('Server Error !');
        })
    }
    function replaceAll(find, replace, str)
    {
        while( str.indexOf(find) > -1)
        {
            str = str.replace(find, replace);
        }
        return str;
    }

    function sendMailToCarrier() {
        var hasDataCarrier = $('#hasDataCarrier').val();
        if(hasDataCarrier == 0) {
            $('#slSendToCarrier').empty();
            $('#slSendToCarrier').append($('<option></option>').attr('value','').text('Chọn nhà vận chuyển'));
            $.ajax({
                url: 'form.php',
                type: 'POST',
                data: {
                    'cmd': 'getListCarrier',
                    'block_id': blockId
                },
                dataType: 'json',
                success: function (data) {
                    $.each(data,function(key,item){
                        $('#slSendToCarrier').append($('<option></option>').attr('value',item.carrierKey).text(item.name));
                    });
                    $('#hasDataCarrier').val(1)
                    $('#mdlSendMailToCarrier').modal();
                }
            });
        } else {
            $('#mdlSendMailToCarrier').modal();
        }
    }

    $('#SendEmailWarehouse').on('change',function(){
        var email = $(this).find(':selected').attr('email');
        var name = $(this).find(':selected').text();
        $('#emailWarehouseCheck').val(email)
        $('#nameWarehouseCheck').val(name)
        $('.sendMailPrint').prop('disabled', false);
    })
    $('#SendEmailFileCarrier').on('change',function(){
        var name = $(this).find(':selected').text();
        $('#nameCarrierCheck').val(name)
        $('.sendMailPrint').prop('disabled', false);
    })
    function sendMailFilePrintOrder() {
        $('#sendMailFilePrintOrder').modal();
        $('#SendEmailWarehouse').empty();
        $.each(JSON.parse(warehouse),function(key,item){
            $('#SendEmailWarehouse').append($('<option></option>').attr('value',item.id).attr('email',item.email).text(item.name));
        });
        $('#SendEmailFileCarrier').empty();
        $('#SendEmailFileCarrier').append($('<option></option>').attr('value','').text('Chọn nhà vận chuyển'));
        $.ajax({
            url: 'form.php',
            type: 'POST',
            data: {
                'cmd': 'getListCarrier',
                'block_id': blockId
            },
            dataType: 'json',
            success: function (data) {
                $.each(data,function(key,item){
                    $('#SendEmailFileCarrier').append($('<option></option>').attr('value',item.carrierKey).text(item.name));
                });
                $('#sendMailFilePrintOrder').modal();
            }
        });
    }
    $('.sendMailPrint').on('click',function(){

    })

    function delay(t, v) {
       return new Promise(function(resolve) {
           setTimeout(resolve.bind(null, v), t)
       });
    }

    function createFormData(obj) {
        return Object.keys(obj).reduce((res, key) => {
            return res.append(key, obj[key]), res;
        }, new FormData());
    }
    async function sendMailPrint(type, actionType = ''){
        var checkWarehouse = $('#SendEmailWarehouse').val();
        var checkCarrier = $('#SendEmailFileCarrier').val();
        var nameCarrierCheck = $('#nameCarrierCheck').val();
        var emailWarehouseCheck = $("#emailWarehouseCheck").val();
        var getWarehouseName = $('#nameWarehouseCheck').val();
        var checkSize = $('#SendSizeEmailWarehouse').val();
        var checkPhone = 0;
        if($("#getFullPhone").is(':checked')){
            checkPhone = 1;
        }
        var size = $('input[name="paper_size"]:checked').val();
        if (emailWarehouseCheck == '') {
            alert('Kho chưa cài đặt email. Vui lòng cài đặt email trước khi gửi file.');
            return false;
        }
        if (checkWarehouse == '') {
            alert('Chưa chọn Kho. Vui lòng chọn Kho trước khi gửi file.');
            return false;
        }
        if (checkCarrier == '') {
            alert('Chưa chọn Nhà vận chuyển. Vui lòng chọn Nhà vận chuyển trước khi gửi file.');
            return false;
        }
        if(type=='SELECTED'){
            if(!$('#checked_order').val()){
                alert('Chưa có đơn hàng nào được chọn. Bạn vui lòng chọn đơn hàng trước khi gửi file.');
                return false;
            }

            let totalOrder = $('.totalOrder').html();
            if (totalOrder > 100) {
                alert('Bạn vui lòng gửi tối đa 100 đơn!')
                return;
            }
            var ids = $('#checked_order').val();
            $('.sendMailPrint').prop('disabled', true);
            if (!confirm("File In đơn sẽ gửi cho Kho "+getWarehouseName+". Bạn có chắc chắn thực hiện không?")) {
                return $('.sendMailPrint').prop('disabled', false);
            }

            try{
                // show loading
                $('#btn-send-mail-warehouse img').show();

                const url = 'form.php?block_id=<?php echo Module::block_id(); ?>';
                const options = {
                    method: "POST",
                    body: createFormData({
                        'cmd': 'processSendFileEmailToWarehouse',
                        'block_id': blockId,
                        'ids' : ids,
                        'checkPhone' : checkPhone,
                        'paperSize' : size
                    })
                };

                let rawPage = await fetch(url, options).then(res => res.text());

                if (rawPage === 'FALSE') {
                    const msg = 'Bạn vui lòng gửi tối đa 100 đơn!';
                    alert(msg);
                    throw msg;
                }
                $('.load_data').html(rawPage);

                await delay(2000);

                let dataSendMail = $('.load_data').html();
                if (dataSendMail) {
                    $('.load_data').html('');
                }

                let invoicePreviewUrl = '/index062019.php?page=qlbh_xuat_kho&cmd=pdf&order_ids=' + ids + '&warehouse_id=' + checkWarehouse;
                let invoice = $('input#attachStockInvoice').is(':checked') ? await fetch(invoicePreviewUrl).then(res => res.text()) : '';

                const _url = 'form.php?block_id=<?php echo Module::block_id(); ?>';
                const _options = {
                    method: 'POST',
                    body: createFormData({
                        'cmd': 'sendFileEmailToWarehouse',
                        'block_id': blockId,
                        'dataSendMail':dataSendMail,
                        'invoice': invoice,
                        'email' : emailWarehouseCheck,
                        'nameCarrier' : nameCarrierCheck,
                        'checkPhone' : checkPhone,
                        'idCarrier' : checkCarrier,
                        'checkSize': checkSize,
                        'ids' : ids,
                        'warehouseName': getWarehouseName,
                        'warehouseId' : checkWarehouse,
                        'paperSize' : size
                    })
                };
                let response = await fetch(_url, _options).then(res => res.text());

                alert('Hệ thống đã gửi email cho kho, bạn vui lòng đợi trong giây lát');

                $('#sendMailFilePrintOrder').modal('toggle');
                $('.sendMailPrint').prop('disabled', false);
                ReloadList(1);

            }catch(e){
                console.log(e);
            }
            // hide loading
            $('#btn-send-mail-warehouse img').hide();
        }
    }
    function exportExcel(type, actionType = ''){
        if(type=='SELECTED'){
            if(!$('#checked_order').val()){
                alert('Chưa có đơn hàng nào được chọn. Bạn vui lòng chọn đơn hàng trước khi xuất excel.');
                return false;
            }
        }
        let sendToCarrier = $('#slSendToCarrier').val();
        if(actionType == 'send_mail_carrier'){
            if(sendToCarrier == ''){
                alert('Chưa chọn Nhà vận chuyển. Vui lòng chọn Nhà vận chuyển trước khi gửi email.');
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
        let getFullPhoneNumber = 0;
        let idGetFullPhoneNumber = 'getFullPhoneNumber';
        let exportType = $('#export_type').val();
        if(actionType == 'send_mail_carrier') {
            idGetFullPhoneNumber += 'Carrier';
            exportType = '';
        }else if(actionType == 'export_excel_carrier'){
            idGetFullPhoneNumber += 'ExportExcelCarrier';
            exportType = '';
        }
        if ($('#'+idGetFullPhoneNumber).is(':checked')) {
            getFullPhoneNumber = 1;
        }
        if(type=='SELECTED'){
            let ids = $('#checked_order').val();
            if(actionType == 'send_mail_carrier') {
                excel_href = '<?php echo Url::build_current(array('cmd'=>'send_mail_carrier'));?>&ids=' + ids + '&export_type=' + exportType + '&getFullPhoneNumber=' + getFullPhoneNumber+'&sendToCarrier='+sendToCarrier;
                $('.btn').attr('disabled','disabled');
                $.ajax({
                    url:excel_href,
                    type:'get',
                    dataType:'json',
                    success:function(data){
                        alert(data.message);
                        $('.btn').removeAttr('disabled');
                        $('#mdlSendMailToCarrier').modal('hide');
                    }
                });
                return;
            }else if(actionType == 'export_excel_carrier'){
                excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel_carrier'));?>&ids=' + ids  + '&getFullPhoneNumber=' + getFullPhoneNumber;
            }else{
                excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&ids=' + ids + '&export_type=' + exportType + '&getFullPhoneNumber=' + getFullPhoneNumber;
            }
        }else{
            if (url1) {
                let itemPerPage = parseInt($('#item_per_page').val()) || 15;

                if(actionType == 'send_mail_carrier') {
                    excel_href = '<?php echo Url::build_current(array('cmd'=>'send_mail_carrier'));?>&cond=' + url1 + '&export_type=' + exportType + '&export_type=' + exportType + '&getFullPhoneNumber=' + getFullPhoneNumber+'&sendToCarrier='+sendToCarrier;
                    excel_href += '&offset=' + ((CURRENT_PAGE - 1) * itemPerPage);
                    $('.btn').attr('disabled','disabled');
                    $.ajax({
                        url:excel_href,
                        type:'get',
                        dataType:'json',
                        success:function(data){
                            alert(data.message);
                            $('.btn').removeAttr('disabled');
                            $('#mdlSendMailToCarrier').modal('hide');
                        }
                    });
                    return;
                }else if(actionType == 'export_excel_carrier'){
                    excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel_carrier'));?>&cond=' + url1 + '&export_type=' + exportType + '&getFullPhoneNumber=' + getFullPhoneNumber;
                }else{
                    excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel'));?>&cond=' + url1 + '&export_type=' + exportType + '&export_type=' + exportType + '&getFullPhoneNumber=' + getFullPhoneNumber;
                }
                excel_href += '&offset=' + ((CURRENT_PAGE - 1) * itemPerPage);
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
        $('#warehouseWrapper').show();
    }
    function createExInvoice(){
        let ids = $('#checked_order').val();
        if(!ids){
            alert('Bạn vui lòng chọn đơn hàng để thực hiện xuất kho');
            return false;
        }
        let warehouse_id = $('#warehouse_id').val();
        if(warehouse_id){
            <?php if(![[=quyen_xuat_kho=]]){?>
            alert('Bạn không có quyền tạo phiếu xuất kho');
            <?php }else{?>
            if ($ids = $('#checked_order').val()) {
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
                $('#quickEditBodyWrapper').html('Đang xử lý...');
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
                $('#quickEditBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                $('#quickEditBodyWrapper').html(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    function getVichatHistory(fb_conversation_id,page_id){
        if(!fb_conversation_id || !page_id){
            $('#chatBodyWrapper').html('Dữ liệu cập nhật từ ngày 22/07/2018 mới xem được lịch sử chat.');
            return false;
        }
        jQuery.ajax({
            method: "POST",
            url: '<?=Url::build_current()?>',
            data : {
                'cmd':'get_vichat_history',
                'fb_conversation_id':fb_conversation_id,
                'page_id':page_id,
                block_id:blockId
            },
            beforeSend: function(){
                $('#chatBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                //alert(content);
                $('#chatBodyWrapper').html(content);
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
                url: '<?=Url::build_current()?>&cmd=get_dupplicates',
                data : {
                    'mobile':mobile,
                    'order_id':orderId,
                    block_id:blockId
                },
                beforeSend: function(){
                    $(obj).html('Đang tải ...');
                },
                success: function(content){
                    //alert(content);
                    $(obj).html(content);
                },
                error: function(){
                    //alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
                }
            });
        }
    }
    function deleteForeverOrder(){
        if(confirm('Đơn hàng và tất cả thông tin liên quan sẽ bị xóa và không thể khôi phục. Bạn có chắc muốn xóa không?')){
            if($('#checked_order').val()){
                $('#act').val('del_order');
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
            let format = '%H:%M:%S\'';
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
    .select2{width: 100% !important;}
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
                    //console.log(el.lang);
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
        let data = $('#sortable').sortable("serialize");
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
                if(content){
                    $('#orderRevisionModal').modal({'backdrop':'static','keyboard':false});
                    $('#orderId').html(orderId);
                    $('#orderRevisionModalContent').html(content);
                }
                else{
                    console.log('no result');
                    $('#orderId').html('');
                    $('#orderRevisionModalContent').html('');
                }
            }
        });
    }

    /**
     * Called on change customer id.
     *
     * @param      {<type>}  el      { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    function onChangeCustomerID(el)
    {
        if((el.value && el.value > 0) || el.value == ''){
            return ReloadList(1);
        }

        alert('Vui lòng nhập mã khách hàng > 0');
    }
    function updateTotalNotAssigned(bundle_id,sourceId,groupsId){
        // trường hợp account tổng
        let phoneStoreId = 0;
        <!--IF:cond([[=account_type=]]==3)-->
        phoneStoreId = $('#phone_store_id').val();
        <!--/IF:cond-->
        $.ajax({
            method: "POST",
            url: '<?=Url::build_current()?>&cmd=get_not_assigned_order_by_source',
            data : {
                'source_id':sourceId,
                'bundle_id':bundle_id,
                'groupsId':groupsId,
                'phone_store_id':phoneStoreId,
                'block_id':blockId
            },
            beforeSend: function(){
                //$('#chatBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                content = content.trim();
                $('#total_not_assigned_order').html(content);
                $('#assigned_total').val(content);
                $('#max_assigned_total').val(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
</script>
<script type="text/javascript">
$(function() {
    $('input[name="saved_date_filter"]').daterangepicker({
        autoUpdateInput: false,
        opens: "left",
        drops: "up",
        maxSpan: {
            "days": 31
        },
        locale: {
            "applyLabel": "Áp dụng",
            "cancelLabel": "Huỷ",
            "fromLabel": "Từ",
            "toLabel": "Đến",
            "daysOfWeek": [
                "CN",
                "T2",
                "T3",
                "T4",
                "T5",
                "T6",
                "T7"
            ],
            "monthNames": [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12"
            ]
        }
    });
    $('input[name="saved_date_filter"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        ReloadList(1);
    });
    $('input[name="saved_date_filter"]').on('cancel.daterangepicker', function(ev, picker) {
        let checkReload = false;
        if($('#saved_date_filter').val()!=''){
            checkReload = true;
        }
        $(this).val('');
        if (checkReload) {
            ReloadList(1);
        }
    });

    $('input[name="deliver_date_filter"]').daterangepicker({
        autoUpdateInput: false,
        opens: "left",
        drops: "up",
        maxSpan: {
            "days": 31
        },
        locale: {
            "applyLabel": "Áp dụng",
            "cancelLabel": "Huỷ",
            "fromLabel": "Từ",
            "toLabel": "Đến",
            "daysOfWeek": [
                "CN",
                "T2",
                "T3",
                "T4",
                "T5",
                "T6",
                "T7"
            ],
            "monthNames": [
                "Tháng 1",
                "Tháng 2",
                "Tháng 3",
                "Tháng 4",
                "Tháng 5",
                "Tháng 6",
                "Tháng 7",
                "Tháng 8",
                "Tháng 9",
                "Tháng 10",
                "Tháng 11",
                "Tháng 12"
            ]
        }
    });
    $('input[name="deliver_date_filter"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        ReloadList(1);
    });
    $('input[name="deliver_date_filter"]').on('cancel.daterangepicker', function(ev, picker) {
        let checkReload = false;
        if($('#deliver_date_filter').val()!=''){
            checkReload = true;
        }
        $(this).val('');
        if (checkReload) {
            ReloadList(1);
        }
    });
});
</script>
