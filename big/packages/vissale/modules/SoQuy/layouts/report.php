<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('thu-chi')?>">Thu chi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sổ quỹ</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <form name="OrderProductForm" method="post" class="form-inline" autocomplete="off">
                <div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label class='text-white'>. </label><br />
                                <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                            </div>
                            <div class="form-group col-md-2">
                                <label class='text-white'>. </label><br />
                                <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                            </div>
                            <!--IF:cond(Session::get('account_type')==3 and check_user_privilege('ADMIN_KETOAN'))-->
                            <div class="form-group col-md-3">
                                <label>Chi nhánh: </label><br />
                                <select name="group_id" id="group_id" class="form-control" style='max-width: 150px'></select>
                            </div>
                            <!--/IF:cond-->
                            <div class="form-group col-md-2">
                                <label for="">Phương thức TT</label>
                                <select name="phuong_thuc_thanh_toan" id="phuong_thuc_thanh_toan" class="form-control"></select>
                            </div>
                            <div class="form-group col-md-2">
                                <label class='text-white'>. </label><br />
                                <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="input-group pull-right">
                            <label for="" class='text-white'>.</label>
                            <input type="button" value="In báo cáo" class="btn btn-default" onclick=" ClickHereToPrint('ifrmPrint', 'reportForm');">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body" id="reportForm">
            <style type="text/css">
                .table td.tb-middle {
                    vertical-align: middle;
                }

                @media print {
                    .table td.tb-middle {
                        vertical-align: middle;
                    }
                    .text-center {
                        text-align: center
                    }
                    .row {
                        display: block;
                        clear: both;
                    }
                    .row:before, .row:after {
                        display: table;
                        content: " ";
                        clear: both;
                    }
                    .row:before, .row:after {
                        display: table;
                        content: " ";
                    }
                    .text-right {
                        text-align: right
                    }
                    .col-md-4 {
                        position: relative;
                        min-height: 1px;
                        width: 33%;
                        float: left;
                    }
                    h3 {
                        margin-top: 0px;
                        margin-bottom: 0px;
                    }
                    td {
                        padding-top: 10px;
                        padding-bottom: 10px;
                    }
                }
            </style>
            <!--IF:report_cond(!empty([[=items=]]))-->
            <?php
            $items = [[=items=]];
            $ton_quy = [[=ton_quy=]];
            $total_tonquy = $ton_quy;
            $title = '';
            if (!empty(Url::get('date_from'))) {
                $title .= 'Từ ngày '. date('d-m-Y', Date_Time::to_time($_REQUEST['date_from']));
            }

            if (!empty(Url::get('date_to'))) {
                $title .= ' Đến ngày '. date('d-m-Y', Date_Time::to_time($_REQUEST['date_to']));
            }
            // System::debug($items);
        ?>
            <div class="row" style="margin-bottom: 40px;">
                <div class="col-md-4">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <div class="col-md-4 text-center">
                    <h3>SỔ QUỸ <?= [[=loai_quy=]] ?></h3>
                    <div><?= $title; ?></div>
                </div>
                <div class="col-md-4 text-right">
                    <div>Ban hành theo QĐ số 114TC/QĐ <br> ngày 01-01-1995 của Bộ Tài Chính</div>
                </div>
            </div>
            <table id='grid_data' class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#999">
                <thead>
                <tr>
                    <th rowspan="3" class='tb-middle'><b>Ngày chứng từ</b></th>
                    <th colspan="2" class="text-center"><b>Số chứng từ</b></th>
                    <th class="text-center"><b>Diễn giải</b></th>
                    <th colspan="6" class="text-center"><b>Số tiền</b></th>
                </tr>
                <tr>
                    <th rowspan="2" class="text-center tb-middle"><b>Thu</b></th>
                    <th rowspan="2" class="text-center tb-middle"><b>Chi</b></th>
                    <th rowspan="2" class="text-center tb-middle"><b></b></th>
                    <th class="text-center tb-middle" colspan="4"><b>Thu</b></th>
                    <th class="text-center tb-middle"><b>Chi</b></th>
                    <th class="text-center tb-middle"><b>Tồn quỹ</b></th>
                </tr>
                <tr>
                    <th class="text-center"><b>Tiền mặt</b></th>
                    <th class="text-center"><b>Chuyển khoản</b></th>
                    <th class="text-center"><b>Thẻ</b></th>
                    <th class="text-center"><b>Tổng</b></th>
                    <th></th>
                    <th class="text-right"><strong class='text-red'><?= number_format($ton_quy); ?></strong></th>
                </tr>
                </thead>
                <?php
                $total_thu = 0;
                $total_chi = 0;
                $total_thu_tien_mat = 0;
                $total_thu_chuyenkhoan = 0;
                $total_thu_the = 0;

                foreach($items as $item):
                $tien_mat = ''; $chuyen_khoan = ''; $the = '';
                $bill_recieve_amount=0;
                if ($item['bill_type'] == 1) {
                // Thu
                $total_tonquy += $item['amount'];
                $total_thu += $item['amount'];
                $bill_recieve_amount+=$item['amount'];
                if ($item['payment_type'] == 1) {
                // Tien mat
                $total_thu_tien_mat += $item['amount'];
                $tien_mat = number_format($item['amount']);
                } else if ($item['payment_type'] == 2) {
                // Chuyen khoan
                $total_thu_chuyenkhoan += $item['amount'];
                $chuyen_khoan = number_format($item['amount']);
                } else if ($item['payment_type'] == 3) {
                // The
                $the = number_format($item['amount']);
                $total_thu_the += ($item['amount']);
                }
                } else {
                // Chi
                $total_tonquy -= $item['amount'];
                $total_chi -= $item['amount'];
                }
                ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($item['bill_date'])) ?></td>
                    <td><?= $item['bill_type'] == 1 ? $item['bill_code'] : "" ?></td>
                    <td><?= $item['bill_type'] == 0 ? $item['bill_code'] : "" ?></td>
                    <td><?= $item['note'] ?></td>
                    <td class="text-right"><?= $tien_mat ?></td>
                    <td class="text-right"><?= $chuyen_khoan ?></td>
                    <td class="text-right"><?= $the ?></td>
                    <td class="text-right text-blue"><?= $bill_recieve_amount?number_format($bill_recieve_amount):'';?></td>
                    <td class="text-right text-orange"><?= $item['bill_type'] == 0 ? number_format($item['amount']) : "" ?></td>
                    <td class="text-right"><?= number_format($total_tonquy) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan='10' class='text-white'>x</td>
                </tr>
                <tr>
                    <td><strong>TRONG KỲ</strong></td>
                    <td class="text-center">x</td>
                    <td class="text-center">x</td>
                    <td class="text-center">x</td>
                    <td class="text-right"><strong><?php echo number_format($total_thu_tien_mat) ?></strong></td>
                    <td class="text-right"><strong><?php echo number_format($total_thu_chuyenkhoan) ?></strong></td>
                    <td class="text-right"><strong><?php echo number_format($total_thu_the) ?></strong></td>
                    <td class="text-right text-blue"><strong><?= number_format($total_thu);?></strong></td>
                    <td class="text-right text-orange"><strong><?php echo number_format($total_chi);?></strong></td>
                    <td class="text-right text-gray"><strong><?= number_format($total_thu+$total_chi);?></strong></td>
                </tr>
                <tr>
                    <td><strong>CUỐI KỲ</strong></td>
                    <td class="text-center">x</td>
                    <td class="text-center">x</td>
                    <td class="text-center text-white" colspan='4'>x</td>
                    <td></td>
                    <td class="text-right"><strong><?php //number_format($total_chi) ?></strong></td>
                    <td class="text-right text-red"><strong><?php echo number_format($total_tonquy) ?></strong></td>
                </tr>
            </table>
            <!--ELSE-->
            <div class="alert text-center"><?= [[=title_no_result=]] ?></div>
            <!--/IF:report_cond-->
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                $.fn.datepicker.defaults.format = "dd/mm/yyyy";
                jQuery('#date_from').datepicker();
                jQuery('#date_to').datepicker();
            });
        </script>
        <style>
            .text-white{
                color: #FFF;
            }
        </style>
    </div>
</div>