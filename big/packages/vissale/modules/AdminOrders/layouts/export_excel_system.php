<style type="text/css" id="stylesheet">
    .date-option {display: flex; padding: 0;}
    .date-option .col.w-25:not(:last-child):after {content: '';width: 10px;display: flex;height: 100%;}
    .date-option .col.w-25 {display: flex; }
    .disabledbutton {pointer-events: none;opacity: 0.4;}
</style>

<div class="container full">
    <div id="page">
        <section class="content-header clearfix">
            <h1 class="page-title pull-left">Export excel đơn hàng hệ thống</h1>
        </section>
        <section class="content">
            <div id="content">
                <div class="box box-solid">
                    <div class="box-body">
                        <form class="form-inline donhang-search-form" method="post" id="donhang-search-form" autocomplete="off">
                            <input name="page" type="hidden" value="<?= $_GET['page'] ?>" />
                            <input name="page_no" type="hidden" />
                            <input name="do" type="hidden" value="search" />
                            <div class="row">
                                <div class="box-body">
                                    <div class="option-top col-md-12">
                                        <div class="col-md-12 no-padding">
                                            <span class="title"><i class="fa fa-phone-squaRe" aria-hidden="true"></i> 
                                                Tiêu chí xuất excel (các tiêu chi tìm kiếm không quá 2 tháng)
                                            </span>
                                        </div>
                                        <div class="col-xs-12 col-md-12 date-option">
                                            <div class="col w-25">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" style="padding: 2px 5px 2px 5px">
                                                        <label for="ngay_tao_checkbox" class="no-padding small"><input name="ngay_tao_checkbox" type="checkbox" id="ngay_tao_checkbox" onclick="ReloadList(1);" value="1" <?= (isset($_REQUEST['ngay_tao_checkbox']) || [[=total=]] == -1)?'checked':'' ?>>
                                                            Ngày tạo
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được tạo">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tu_ngay'>
                                                                    <input name="ngay_tao_from" type="text" id="ngay_tao_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_den_ngay'>
                                                                    <input name="ngay_tao_to" type="text" id="ngay_tao_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_chia_checkbox" class="no-padding small">
                                                            <input name="ngay_chia_checkbox" type="checkbox" id="ngay_chia_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_chia_checkbox'])?'checked':'' ?>>
                                                            Ngày chia
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày data số/đơn hàng được chia cho sale xử lý">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_ngay_chia_tu_ngay'>
                                                                    <input name="ngay_chia_from" type="text" id="ngay_chia_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_ngay_chia_den_ngay'>
                                                                    <input name="ngay_chia_to" type="text" id="ngay_chia_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_xn_checkbox" class="no-padding small">
                                                            <input name="ngay_xn_checkbox" type="checkbox" id="ngay_xn_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_xn_checkbox'])?'checked':'' ?>>
                                                            Ngày chốt
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được hàng được chốt">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_xn_tu_ngay'>
                                                                    <input name="ngay_xn_from" type="text" id="ngay_xn_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_xn_den_ngay'>
                                                                    <input name="ngay_xn_to" type="text" id="ngay_xn_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_chuyen_kt_checkbox" class="no-padding small">
                                                            <input name="ngay_chuyen_kt_checkbox" type="checkbox" id="ngay_chuyen_kt_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_chuyen_kt_checkbox'])?'checked':'' ?>>
                                                            Đóng hàng
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đóng hàng (không tính với trạng thái có tên là kế toán mà do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_nckt_tu_ngay'>
                                                                    <input name="ngay_chuyen_kt_from" type="text" id="ngay_chuyen_kt_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_nckt_den_ngay'>
                                                                    <input name="ngay_chuyen_kt_to" type="text" id="ngay_chuyen_kt_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_chuyen_checkbox" class="no-padding small">
                                                            <input name="ngay_chuyen_checkbox" type="checkbox" id="ngay_chuyen_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_chuyen_checkbox'])?'checked':'' ?>>
                                                            Chuyển hàng
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Chuyển hàng (không tính với trạng thái do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_nc_tu_ngay'>
                                                                    <input name="ngay_chuyen_from" type="text" id="ngay_chuyen_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_nc_den_ngay'>
                                                                    <input name="ngay_chuyen_to" type="text" id="ngay_chuyen_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_chuyen_hoan_checkbox" class="no-padding small">
                                                            <input name="ngay_chuyen_hoan_checkbox" type="checkbox" id="ngay_chuyen_hoan_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_chuyen_hoan_checkbox'])?'checked':'' ?>>
                                                            Chuyển hoàn
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Chuyển hoàn (không tính với trạng thái do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_chuyen_hoan_from'>
                                                                    <input name="ngay_chuyen_hoan_from" type="text" id="ngay_chuyen_hoan_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_chuyen_hoan_to'>
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
                                                            <input name="ngay_thanh_cong_checkbox" type="checkbox" id="ngay_thanh_cong_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_thanh_cong_checkbox'])?'checked':'' ?>>
                                                            Thành công
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái thành công (không tính với trạng thái do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tc_tu_ngay'>
                                                                    <input name="ngay_thanh_cong_from" type="text" id="ngay_thanh_cong_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tc_den_ngay'>
                                                                    <input name="ngay_thanh_cong_to" type="text" id="ngay_thanh_cong_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_thu_tien_checkbox" class="no-padding small">
                                                            <input name="ngay_thu_tien_checkbox" type="checkbox" id="ngay_thu_tien_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_thu_tien_checkbox'])?'checked':'' ?>>
                                                            Thu tiền
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đã thu tiền (không tính với trạng thái do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_t_tu_ngay'>
                                                                    <input name="ngay_thu_tien_from" type="text" id="ngay_thu_tien_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tt_den_ngay'>
                                                                    <input name="ngay_thu_tien_to" type="text" id="ngay_thu_tien_to" class="form-control dxeEditArea" />
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
                                                        <label for="ngay_tra_hang_checkbox" class="no-padding small">
                                                            <input name="ngay_tra_hang_checkbox" type="checkbox" id="ngay_tra_hang_checkbox" onclick="ReloadList(1);" value="1" <?= isset($_REQUEST['ngay_tra_hang_checkbox'])?'checked':'' ?>>
                                                            Trả hàng
                                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Ngày đơn hàng được chuyển về trạng thái Đã trả hàng về kho (không tính với trạng thái do shop tự tạo)">
                                                                <i class="fa fa-question-circle"></i>
                                                            </a>
                                                        </label>
                                                    </div>
                                                    <div class="panel-body" style="padding: 5px 5px 5px 5px">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tra_hang_from'>
                                                                    <input name="ngay_tra_hang_from" type="text" id="ngay_tra_hang_from" class="form-control dxeEditArea" />
                                                                    <span class="input-group-addon" data-toggle="tooltip" title="Chọn ngày"><i class="fa fa-calendar"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class='input-group date datetimepicker_from' id='datetimepicker_tra_hang_to'>
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
                                </div>
                            </div>
                            <div class="row">
                                <div class="box-body">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="col-md-7 no-padding">
                                            <span class="title"><i class="fa fa-phone-squaRe" aria-hidden="true"></i> Chọn loại xuất excel</span>
                                            <div class="ct">
                                                <input type="hidden" name="num" id="num" value="0">
                                                <select name="export_type" id="export_type" class="form-control" onchange="ReloadList(1);"></select>
                                                <button type="submit" class="btn btn-primary" id="btnSubmit" onclick="document.getElementById('num').value = 0"> <i class="fa fa-search"></i> Xuất Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--IF:cond([[=total=]]>0)-->
                            <div class="row">
                                <div class="box-body" style="padding-left: 25px">
                                    Tìm thấy [[|total|]] bản ghi. Tải 
                                    <?php for ($i = 1; $i <= [[=loop=]]; $i++): ?>
                                    <button type="button" class="btn btn-sm btn-default" onclick="document.getElementById('num').value = '<?= $i ?>';document.getElementById('donhang-search-form').submit();">File số <?= $i ?></button>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <!--/IF:cond-->
                        </form>
                        <div class="table-responsive">

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
    $(function() {
	    $('.datetimepicker_from').datetimepicker({
		    format: 'DD/MM/YYYY'
	    });
	    
        $('.btn-reset').click(function() {
            $('#donhang-search-form')[0].reset()
            $('#donhang-search-form').submit()
        })

        $('#start_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        
        $('#end_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });

	    $('#donhang-search-form').on('submit', function() {
		    document.getElementById("btnSubmit").disabled = true;
		    setTimeout(function(){ document.getElementById("btnSubmit").disabled = false }, 5000);
	    });
    });

    function ReloadList(page) {

    }
</script>
