<?php $title='Báo cáo thay đổi trạng thái';?>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <input name="order_id" type="text" id="order_id" class="form-control" placeholder="Mã đơn hàng">
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>
                        <a href="#" onclick="printWebPart('reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row" style="overflow: auto;width: 100%;">
                <div class="col-md-12" id="reportForm">
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;"><h2><?=$title?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <!--IF:report_cond(!empty([[=reports=]]))-->
                    <table width="100%" class="table table-bordered" bordercolor="#CCC" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Điện thoại chính</th>
                            <th>Điện thoại phụ</th>
                            <th>Ghi chú</th>
                            <th>Trạng thái</th>
                            <th>NV tạo</th>
                            <th>Tên sản phẩm</th>
                            <th>Tổng tiền</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1;$total_amount=0;?>
                        <!--LIST:reports-->
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td>[[|reports.id|]]</td>
                            <td>[[|reports.customer_name|]]</td>
                            <td>[[|reports.mobile|]]</td>
                            <td>[[|reports.mobile2|]]</td>
                            <td class="small">
                                <div>[[|reports.note1|]]</div>
                                <div>[[|reports.note2|]]</div>
                            </td>
                            <td>
                                    <?php
                                    $cond = ' 
                                        order_revisions.order_id = '.[[=reports.id=]].'
                                        and before_order_status_id > 0
                                    ';
                                    $order_revisions = DashboardDB::get_order_revision_by_cond($cond);
                                    if(!empty($order_revisions)){
                                        foreach ($order_revisions as $k=>$v){
                                            echo '<div class="small text-danger">'.$v['before_order_status'].' -> '.$v['order_status'].'<br>Bởi <strong>'.$v['user_created_name'].'</strong> lúc '.date('H:i\' d/m/Y',strtotime($v['created'])).'</div>';
                                        }
                                    }else{
                                        echo '<div class="small">'.[[=reports.status_name=]].'<br>Bởi <strong>'.[[=reports.user_created=]].'</strong> lúc '.date('H:i\' d/m/Y',strtotime([[=reports.created=]])).'</div>';
                                    }
                                    ?>
                            </td>
                            <td class="small">[[|reports.user_created|]]<br><?=date('H:i\' d/m/Y',strtotime([[=reports.created=]]));?></td>
                            <td>
                                <?php
                                $products = AdminOrdersDB::get_order_product([[=reports.id=]]);
                                $product_str = '';
                                $show_product_detail = false;
                                $j = 0;
                                foreach($products as $k=>$v){
                                    if($show_product_detail){
                                        $product_str .= (($j>0)?'<br> ':'').$v['qty'].' '.$v['code'].' - '.$v['name'].''.($v['size']?' size '.$v['size'].'':'').''.($v['color']?' mầu '.$v['color'].'':'');
                                    }else{
                                        $product_str .= (($j>0)?'<br> ':'').$v['qty'].' '.$v['name'].''.($v['size']?' size '.$v['size'].'':'').''.($v['color']?' mầu '.$v['color'].'':'');
                                    }
                                    $j++;
                                }
                                echo $product_str;
                                ?>
                            </td>
                            <td class="text-right"><?php $total_amount +=intval([[=reports.total_price=]]); echo System::display_number([[=reports.total_price=]])?></td>
                        </tr>
                        <!--/LIST:reports-->
                        </tbody>
                    </table>
                    <div style="padding:5px;text-align:right;">Tổng: <strong><?=System::display_number($total_amount);?>/[[|total|]] đơn</strong></div>
                    <br>
                    <div class="text-right">[[|paging|]]</div>
                    <!--ELSE-->
                    <?php if(!Url::get('view_report')){?>
                        <div class="alert alert-warning-custom text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <?php }else{?>
                        <div class="alert text-center">Chưa có dữ liệu phù hợp.</div>
                    <?php }?>
                    <!--/IF:report_cond-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>