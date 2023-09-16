<style>
    p.name {color: #000; font-weight: bold; margin: 0; font-size: 13px }
    #ReportTable td{white-space: nowrap;} 
    div#tbl-clone {max-height: 400px;}
    #ReportTable  thead, tfoot {position: sticky;top: 0;background: #fff; z-index:  100}
    #ReportTable tfoot{bottom: 0}
    #ReportTable tbody tr td:first-child, #ReportTable tfoot tr td:first-child{position: sticky; left: 0; background: #fff; }
    #ReportTable tbody tr:nth-child(odd) td:first-child{background: #f9f9f9 !important;}
    i.username {color: #999; }
</style>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=ReportForm::TITLE?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <iframe src="" id="ifrmPrint" class="hidden"></iframe>
    <fieldset id="toolbar">
        <div>
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="date_from" name="date_from" class="form-control" placeholder="Từ ngày" value="<?=$this->map['date_from']?>">
                        </div>
                        <div class="form-group">
                            <input type="text" id="date_to" name="date_to" class="form-control" placeholder="đến ngày" value="<?=$this->map['date_to']?>">
                        </div>
                        <div class="form-group">
                            <select id="is_active" name="is_active" class="form-control">
                                <option value="">Tài khoản kích hoạt</option>
                                <option value="1">Tài khoản chưa kích hoạt</option>
                                <option value="2">Tất cả</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control" required oninvalid="this.setCustomValidity('Bạn vui lòng chọn công ty.')" oninput="setCustomValidity('')"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="pull-right">
                            <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                        </div>
                        <div class="pull-right">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <p style="    padding: 2px 0px; margin: 0; font-style: italic; color: red;">
                            Chú ý: Đặt lại bao gồm doanh thu và số lượng các loại đơn Đặt lại 1,2, ...5
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    <div id="reportForm">
                        <style>
                            @media print {
                                #table-action {
                                    display: none
                                }
                            }
                        </style>
                        <!--IF:cond(!empty([[=users=]]))-->
                        <table width="100%" border="0">
                            <tr>
                                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                    <div>Điện thoại: [[|phone|]]</div>
                                    <div>Địa chỉ: [[|address|]]</div></th>
                                <th width="40%" style="text-align: center;"><h2><?=ReportForm::TITLE?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                                <th width="30%" style="text-align: right;">
                                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                                </th>
                            </tr>
                        </table>
                        <div id="tbl-clone" style="overflow: auto;">
                            <table id="ReportTable" width="100%" class="table table-bordered table-striped" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                <thead>
                                    <tr style="font-weight:bold;background:#DDD;">
                                        <tr style="font-weight:bold;background:#DDD;">
                                        <td rowspan="2" class="th-fixed">Nhân viên</td>
                                        <td rowspan="2">Số được chia</td>
                                        <td rowspan="2">Tiếp cận</td>
                                        <td colspan="5" align="center">TỶ LỆ VỀ SỐ</td>
                                        <td colspan="7" align="center" style="background: #bbf2e0;">DOANH THU</td>
                                        <td colspan="2" align="center">TỶ LỆ DOANH THU</td>
                                        <td colspan="<?=(5 + count($this->map['statusList']))?>" align="center">TÌNH TRẠNG SỐ</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Tỷ lệ tiếp cận
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Số tiếp cận/Số được chia">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                        <td>CM/ Tiếp cận<a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Số chốt mới/Số tiếp cận">
                                            <i class="fa fa-question-circle"></i>
                                        </a></td>
                                        <td>CM/ Số được chia<a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Số chốt mới/Số được chia">
                                            <i class="fa fa-question-circle"></i>
                                        </a></td>
                                        <td style="color:#f00;">% Hủy<a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Tổng Số Huỷ/Chốt mới">
                                            <i class="fa fa-question-circle"></i>
                                        </a></td>
                                        <td style="color:#f00;">% Hoàn<a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="Tổng số đơn chuyển Hoàn/Tổng đơn Vận chuyển">
                                            <i class="fa fa-question-circle"></i>
                                        </a></td>
                                        <td style="background: #bbf2e0;">CHỐT MỚI</td>
                                        <td style="background: #bbf2e0;">Chăm sóc</td>
                                        <td style="background: #bbf2e0;">Đặt lại</td>
                                        <td style="background: #bbf2e0;">Tối ưu</td>
                                        <td style="background: #bbf2e0;">Đơn hủy</td>
                                        <td style="background: #bbf2e0;">HOÀN</td>
                                        <td style="background: #bbf2e0;">TỔNG</td>
                                        <td>DTBQ/đơn chốt mới</td>
                                        <td>DTBQ/SĐT</td>
                                        <td style="background: #e1d99b">Chốt mới</td>
                                        <td style="background: #e1d99b">Chăm sóc</td>
                                        <td style="background: #e1d99b">Đặt lại</td>
                                        <td style="background: #e1d99b">Tối ưu</td>
                                        <td align="center"  style="background: #e1d99b">TỔNG</td>
                                        <?php foreach($this->map['statusList'] as $statusID => $status): ?>
                                        <td align="center"><?=$status['name']?></td>
                                        <?php endforeach;?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($this->map['users'] as $userID => $user): ?>
                                <tr>
                                    <td class="th-fixed" style="font-weight: bold;">
                                        <p class="name"><?=$user['full_name']?></p><i class="username"><?=$user['user_name']?></i>
                                    </td>
                                    <td class="text-right"><?=number_format($user['chia']['total_order'])?></td>
                                    <td class="text-right"><?=number_format($user['tiep_can']['total_order'])?></td>
                                    <td class="text-right"><?=number_format($user['rate']['tiep_can'])?> %</td>
                                    <td class="text-right"><?=number_format($user['rate']['chot_moi_tiep_can'])?> %</td>
                                    <td class="text-right"><?=number_format($user['rate']['chot_moi_chia'])?> %</td>
                                    <td class="text-center" style="color:#f00;"><?=number_format($user['rate']['huy'])?>%</td>
                                    <td class="text-center" style="color:#f00;"><?=number_format($user['rate']['hoan'])?>%</td>

                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($user['chot_moi']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($user['cham_soc']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($user['dat_lai']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($user['toi_uu']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?=number_format($user['huy']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?=number_format($user['chuyen_hoan']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?=number_format($user['total_price']) ?></td>

                                    <td class="text-right"><?=number_format($user['revenue_rate']['so_don'])?></td>
                                    <td class="text-right"><?=number_format($user['revenue_rate']['sdt'])?></td>

                                    <td class="text-right" style="background: #e1d99b"><?=number_format($user['chot_moi']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($user['cham_soc']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($user['dat_lai']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($user['toi_uu']['total_order'])?></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?=$user['total_order']?></td>
                                    <?php foreach($this->map['statusList'] as $statusID => $status): ?>
                                    <td align="center" class="col text-right"><?=$user['status'][$statusID]['total_order']?></td>
                                    <?php endforeach;?>
                                </tr>
                                <?php endforeach;?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="th-fixed" style="font-weight: bold;">
                                        Tổng
                                    </td>
                                    <td class="text-right"><?=number_format($this->map['total']['chia']['total_order'])?></td>
                                    <td class="text-right"><?=number_format($this->map['total']['tiep_can']['total_order'])?></td>
                                    <td class="text-right"><?=number_format($this->map['total']['rate']['tiep_can'])?> %</td>
                                    <td class="text-right"><?=number_format($this->map['total']['rate']['chot_moi_tiep_can'])?> %</td>
                                    <td class="text-right"><?=number_format($this->map['total']['rate']['chot_moi_chia'])?> %</td>
                                    <td class="text-center" style="color:#f00;"><?=number_format($this->map['total']['rate']['huy'])?>%</td>
                                    <td class="text-center" style="color:#f00;"><?=number_format($this->map['total']['rate']['hoan'])?>%</td>

                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($this->map['total']['chot_moi']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($this->map['total']['cham_soc']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($this->map['total']['dat_lai']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?=number_format($this->map['total']['toi_uu']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?=number_format($this->map['total']['huy']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?=number_format($this->map['total']['chuyen_hoan']['total_price']) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?=number_format($this->map['total']['total_price']) ?></td>

                                    <td class="text-right"><?=number_format($this->map['total']['revenue_rate']['so_don'])?></td>
                                    <td class="text-right"><?=number_format($this->map['total']['revenue_rate']['sdt'])?></td>

                                    <td class="text-right" style="background: #e1d99b"><?=number_format($this->map['total']['chot_moi']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($this->map['total']['cham_soc']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($this->map['total']['dat_lai']['total_order'])?></td>
                                    <td class="text-right" style="background: #e1d99b"><?=number_format($this->map['total']['toi_uu']['total_order'])?></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?=$this->map['total']['total_order']?></td>
                                    <?php foreach($this->map['statusList'] as $statusID => $status): ?>
                                    <td align="center" class="col text-right"><?=number_format($this->map['total']['status'][$statusID]['total_order'])?></td>
                                    <?php endforeach;?>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!--ELSE-->
                        <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                        <!--/IF:cond-->
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.btn-prev').on('click', function() {
            // var pos = $('#tbl-clone').scrollLeft() - 50;
            $('#tbl-clone').scrollLeft(0);
        })

        $('.btn-next').on('click', function() {
            // var pos = $('#tbl-clone').scrollLeft() + 50;
            var $scrollWidth = $('#tbl-clone')[0].scrollWidth;
            var $scrollLeft = $('#tbl-clone').scrollLeft();

            $('#tbl-clone').scrollLeft($scrollWidth + $scrollLeft);
        })
    });
</script>