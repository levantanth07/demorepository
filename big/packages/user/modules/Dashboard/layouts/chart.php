<?php
$notes = [[=notes=]];
?>
<style>
    .vinhDanhBoard .box-warning{
        opacity: 0.8;
        border: 0px;
        border-radius: 5px;
    }
    .vinhDanhBoard .box-header{
        background-image: linear-gradient(to right, #fcb045, #fd1d1d);
    }
    .vinhDanhBoard .row-1{
        font-weight: bold;
        background-image: linear-gradient(to right, #B7F8DB , #FFF) !important;
        font-size:14px;
        color:#ff8c52;
    }
    .vinhDanhBoard .row-1 td:first-child{

    }
    .vinhDanhBoard .row-2{
        font-weight: bold;
        background-color: #dfffd0 !important;
        font-size:14px;
    }
    .vinhDanhBoard .row-3{
        font-weight: bold;
        background-color: #ecffeb !important;
        font-size:14px;
    }
    .vinhDanhBoard .row-4,.vinhDanhBoard .row-5,.vinhDanhBoard .row-6,.vinhDanhBoard .row-7,.vinhDanhBoard .row-8,.vinhDanhBoard .row-9,.vinhDanhBoard .row-10{
        font-weight: bold;
        color:#666;
    }
    .vinhDanhBoard .table tr td{height:20px !important;line-height:20px}
    .top2{margin-top:30px;height: 195px;color:#333;padding-top:100px;font-weight: bold;background: url('assets/vissale/images/medal_2.png') no-repeat; background-size: contain;}
    .top1{height: 195px;color:#333;padding-top:100px;font-weight: bold;background: url('assets/vissale/images/medal_1.png') no-repeat; background-size: contain;}
    .top3{margin-top:30px;height: 195px;color:#333;padding-top:100px;font-weight: bold;background: url('assets/vissale/images/medal_3.png') no-repeat; background-size: contain;}
    .top-wrapper{
        width: 100%;height: 195px;
    }
    @media screen and (max-width: 720px){
        .top1{padding-top:60px;font-size:12px;height: 155px;}
        .top2{padding-top:60px;font-size:12px;height: 155px;}
        .top3{padding-top:60px;font-size:12px;height: 155px;}
        .top-wrapper{
            width: 100%;height: 155px;
        }
    }
</style>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">Dashboard</li>
            <li class="pull-right hidden-xs">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid box-default">
                        <form name="ReportForm" method="post" class="form-inline">
                            <div class="row box-body">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                                    </div>
                                    <div class="form-group">
                                        <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                                    </div>
                                    <button type="submit" class="btn btn-warning"> <i class="fa fa-search"></i> Tìm kiếm</button>
                                    <button type="button" onclick="window.location='<?=Url::build_current(['act'=>'refresh'])?>'" class="btn btn-default"><i class="fa fa-retweet"></i> Làm mới</button>
                                    <div class="pull-right text-warning small">Chú ý các dữ liệu tổng hợp sau 10 phút update lại một lần!</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-12 statistics">
                            <div class="row">
                                <div class="tt_cmt col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> Tổng nhân sự</span>
                                            <div class="info-box-number" id="TotalPreOrder">[[|tong_nhan_su|]]
                                                <span class="small <?=([[=tong_admin=]]>=5)?'text-danger':'text-primary'?>">(Quản lý shop: [[|tong_admin|]])</span>
                                            </div>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Tổng số tài khoản (Không tính tài khoản không kích hoạt)">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt_inbox col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-truck" aria-hidden="true"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text"> Đơn đang vận chuyển</span>
                                            <span class="info-box-number" id="TotalOrder">[[|tong_don_van_chuyen|]]</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Tống số đơn đang vận chuyển trong khoảng thời gian xem">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="don_xac_nhan col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-copy" aria-hidden="true"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text"> Đơn Xác Nhận / Được Chia </span>
                                            <span class="info-box-number" id="TotalOrderRevenue">[[|don_xac_nhan|]]/[[|tong_so_duoc_chia|]]</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Tổng các đơn được xác nhận / Tổng số đã đuợc chia">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="tt_ds col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-money" aria-hidden="true"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text"> Doanh Số / DS trừ hoàn </span>
                                            <span class="info-box-number" id="ConvertRate">[[|doanh_so_xuat_di|]]<span class="small text-success">tr.đ</span> / [[|doanh_so_tru_hoan|]]<span class="small text-success">tr.đ</span></span>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Doanh thu trên các đơn xác nhận và doanh số trừ hoàn">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                    <!-- /.info-box -->
                                </div>
                                <div class="ty_le_chot col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-check-circle" aria-hidden="true"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text"> Tỉ Lệ Chốt </span>
                                            <span class="info-box-number" id="TotalWaittingGet">[[|ty_le_chot|]]</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Tỉ Lệ Chốt">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="don_chua_xu_ly col-md-4 col-sm-6 col-sm-6">
                                    <div class="info-box">
                                        <span class="info-box-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text"> Chưa Xử Lý </span>
                                            <span class="info-box-number" id="TotalGet">[[|don_chua_xu_ly|]]</span>
                                            <div class="more_tooltip" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Tổng đơn hàng đang ở trạng thái chưa xác nhận">
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="panel">
                            <div class="row panel-body">
                                <script src="https://code.highcharts.com/highcharts.js"></script>
                                <script src="https://code.highcharts.com/modules/data.js"></script>
                                <script src="https://code.highcharts.com/modules/exporting.js"></script>
                                <!--IF:cond(Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$xem_khoi_bc_chung)-->
                                <div class="col-sm-6">
                                    <div class="vinhDanhBoard" style="min-width: 310px; min-height: 400px; margin: 0 auto">
                                        <div class="box box-success box-solid">
                                            <div class="box-header with-border">
                                                <div class="title"><i class="fa fa-certificate"></i> Bảng xếp hạng sale<button type="button" class="btn btn-sm btn-default pull-right" onclick="updateRank('SALE',$('.loading'));"><i class="fa fa-play"></i> Cập nhật</button></div>
                                            </div>
                                            <div class="box-body" id="topSaleContainer" style="overflow: auto;height: 300px;">
                                                <?php $i=1?>
                                                <div class="loading fa fa-spinner" style="display: none;"></div>
                                                <table data-toggle="table" class="display table table-bordered" id="topSaleTable">
                                                    <tr>
                                                        <th data-field="order">STT</th>
                                                        <th data-field="name">Nhân sự</th>
                                                        <th data-field="qty" class="text-center">SL</th>
                                                        <th data-field="revenue" class="text-right">đ</th>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/IF:cond-->
                                <!--IF:cond(Dashboard::$quyen_marketing or Dashboard::$xem_khoi_bc_chung)-->
                                <div class="col-sm-6">
                                    <div class="vinhDanhBoard" style="min-width: 310px; min-height: 400px; margin: 0 auto">
                                        <div class="box box-primary box-solid">
                                            <div class="box-header with-border">
                                                <div class="title"><i class="fa fa-certificate"></i> Bảng xếp hạng Marketing<button type="button" class="btn btn-sm btn-default pull-right" onclick="updateRank('MKT',$('.mkt-loading'));"><i class="fa fa-play"></i> Cập nhật</button></div>
                                            </div>
                                            <div class="box-body" id="topMktContainer" style="overflow: auto;height: 300px;">
                                                <?php $i=1?>
                                                <div class="mkt-loading fa fa-spinner" style="display: none;"></div>
                                                <table data-toggle="table" class="display table table-bordered" id="topMktTable">
                                                    <tr>
                                                        <th data-field="order">STT</th>
                                                        <th data-field="name">Nhân sự</th>
                                                        <th data-field="qty" class="text-center">SL</th>
                                                        <th data-field="revenue" class="text-right">đ</th>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/IF:cond-->
                                <!--IF:cond(Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$xem_khoi_bc_chung)-->
                                <div class="col-sm-6">
                                    <div id="productReportContainer" style="min-height: 300px; margin: 0 auto"></div>
                                    <script>
                                        Highcharts.chart('productReportContainer', {
                                            chart: {
                                                plotBackgroundColor: null,
                                                plotBorderWidth: null,
                                                plotShadow: false,
                                                type: 'pie'
                                            },
                                            title: {
                                                text: 'Tỷ trọng sản phẩm / dịch vụ'
                                            },
                                            subtitle: {
                                                text: ''
                                            },
                                            tooltip: {
                                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                                            },
                                            plotOptions: {
                                                pie: {
                                                    allowPointSelect: true,
                                                    cursor: 'pointer',
                                                    dataLabels: {
                                                        enabled: true
                                                    },
                                                }
                                            },
                                            series: [{
                                                name: 'Brands',
                                                colorByPoint: true,
                                                data: [
                                                    <!--LIST:products-->
                                                    {
                                                        name: '[[|products.name|]] - [[|products.total_price|]]tr',
                                                        y: [[|products.total_price|]],
                                                sliced: true,
                                                selected: true
                                            },
                                                <!--/LIST:products-->
                                            ]
                                        }]
                                        });
                                    </script>
                                </div>
                                <!--/IF:cond-->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="staffReportContainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <!--IF:admin_cond(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung)-->
                                <script>
                                    Highcharts.chart('staffReportContainer', {
                                        chart: {
                                            type: 'line'
                                        },
                                        title: {
                                            text: "Biểu đồ doanh thu tháng"
                                        },
                                        subtitle: {
                                            text: ''
                                        },
                                        xAxis: {
                                            categories: [
                                                <!--LIST:revenue_by_month-->
                                                '[[|revenue_by_month.mon|]]',
                                                <!--/LIST:revenue_by_month-->
                                            ]
                                        },
                                        yAxis: [
                                            {
                                                title: {
                                                    text: 'Doanh thu đơn vị triệu đồng'
                                                }
                                            }
                                        ],
                                        plotOptions: {
                                            area: {
                                                colors:'#f00',
                                                fillColor: {
                                                    linearGradient: {
                                                        x1: 0,
                                                        y1: 0,
                                                        x2: 0,
                                                        y2: 1
                                                    },
                                                    stops: [
                                                        [0, Highcharts.getOptions().colors[0]],
                                                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                                    ]
                                                },
                                                marker: {
                                                    radius: 2
                                                },
                                                lineWidth: 1,
                                                states: {
                                                    hover: {
                                                        lineWidth: 1
                                                    }
                                                },
                                                threshold: null
                                            }
                                        },
                                        series: [{
                                            type: 'area',
                                            color: {
                                                radialGradient: { cx: 0.5, cy: 0.5, r: 0.5 },
                                                stops: [
                                                    [0, '#fdffd1'],
                                                    [1, '#ff562f']
                                                ]
                                            },
                                            name: 'Doanh thu',
                                            data: [
                                                <!--LIST:revenue_by_month-->
                                                [[|revenue_by_month.total_amount|]],
                                            <!--/LIST:revenue_by_month-->
                                        ]
                                    }]
                                    });
                                </script>
                                <!--ELSE-->
                                <script>
                                    Highcharts.chart('staffReportContainer', {
                                        chart: {
                                            type: 'line'
                                        },
                                        title: {
                                            text: "Biểu đồ doanh thu <?php echo [[=full_name=]] ?>"
                                        },
                                        subtitle: {
                                            text: ''
                                        },
                                        xAxis: {
                                            categories: [
                                                <!--LIST:chart_per-->
                                                '[[|chart_per.date|]]',
                                                <!--/LIST:chart_per-->
                                            ]
                                        },
                                        yAxis: [
                                            {
                                                title: {
                                                    text: 'Doanh thu đơn vị triệu đồng'
                                                }
                                            }
                                        ],
                                        plotOptions: {
                                            area: {
                                                fillColor: {
                                                    linearGradient: {
                                                        x1: 0,
                                                        y1: 0,
                                                        x2: 0,
                                                        y2: 1
                                                    },
                                                    stops: [
                                                        [0, Highcharts.getOptions().colors[0]],
                                                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                                    ]
                                                },
                                                marker: {
                                                    radius: 2
                                                },
                                                lineWidth: 1,
                                                states: {
                                                    hover: {
                                                        lineWidth: 1
                                                    }
                                                },
                                                threshold: null
                                            }
                                        },
                                        series: [{
                                            type: 'area',
                                            name: 'Doanh thu',
                                            data: [
                                                <!--LIST:chart_per-->
                                                [[|chart_per.turnover|]],
                                            <!--/LIST:chart_per-->
                                        ]
                                    }]
                                    });
                                </script>
                                <!--/IF:admin_cond-->
                            </div>
                            <!--IF:cond(Dashboard::$quyen_bc_doanh_thu_nv or Dashboard::$xem_khoi_bc_chung)-->
                            <div class="col-sm-6">
                                <div id="doanhThuReportContainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                <?php
                                $subtitle = 'Từ ' . [[=date_from=]] . ' đến ' . [[=date_to=]];
                                ?>
                                <script>
                                    Highcharts.chart('doanhThuReportContainer', {
                                        chart: {
                                            zoomType: 'x'
                                        },
                                        title: {
                                            text: 'Biểu đồ doanh thu theo ngày'
                                        },
                                        subtitle: {
                                            text: ''
                                        },
                                        xAxis: {
                                            categories: <?= [[=chart_tong_hop_date=]] ?>
                                        },
                                        yAxis: {
                                            title: {
                                                text: 'Doanh thu (VNĐ)'
                                            }
                                        },
                                        legend: {
                                            enabled: false
                                        },
                                        plotOptions: {
                                            area: {
                                                fillColor: {
                                                    linearGradient: {
                                                        x1: 0,
                                                        y1: 0,
                                                        x2: 0,
                                                        y2: 1
                                                    },
                                                    stops: [
                                                        [0, Highcharts.getOptions().colors[0]],
                                                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                                    ]
                                                },
                                                marker: {
                                                    radius: 2
                                                },
                                                lineWidth: 1,
                                                states: {
                                                    hover: {
                                                        lineWidth: 1
                                                    }
                                                },
                                                threshold: null
                                            }
                                        },
                                        series: <?= [[=chart_tong_hop=]] ?>
                                    });

                                </script>
                            </div>
                            <!--/IF:cond-->
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <?php if(1==1){//Dashboard::$quyen_cskh?>
                    <div class="box box-warning box-solid">
                        <div class="box-header">
                            <h4 class="box-title"><i class="fa fa-calendar"></i> Lịch hẹn hôm nay</h4>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <!--IF:cond_(sizeof([[=schedules=]])>0)-->
                                <!--LIST:schedules-->
                                <li><a href="<?=Url::build('customer',[ 'cid' => ([[=schedules.customer_id=]]), 'do' => 'view' ] )?>#lichhen" title="[[|schedules.note|]]"> <?=MiString::display_sort_title([[=schedules.note=]],5)?> <span class="pull-right badge"><?=date('H:i\' d/m',[[=schedules.appointed_time=]])?></span></a></li>
                                <!--/LIST:schedules-->
                                <!--ELSE-->
                                <div class="text-center"><a href="<?=Url::build('lich-hen',['cmd'=>'today_schedule'])?>" class="btn btn-default btn-sm"> Lịch hẹn hôm nay</a></div>
                                <!--/IF:cond_-->
                            </ul>
                        </div>
                    </div>
                    <?php }//end quyen_cskh?>
                    <!--IF:cond(Dashboard::$admin_group or Dashboard::$xem_khoi_bc_chung)-->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h4 class="box-title">Lịch sử đăng nhập</h4>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <ul class="products-list product-list-in-box">
                                <!--LIST:logins-->
                                <li class="item">
                                    <span class="label label-default">[[|logins.account_id|]]</span> <span class="direct-chat-timestamp small">đăng nhập lúc <?php echo date('H:i\' d/m/Y',[[=logins.time=]])?></span>
                                </li>
                                <!--/LIST:logins-->
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <a href="<?=Url::build('login-history')?>" class="uppercase">Xem tất cả</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <div class="box box-danger" style="border-color: rgb(243, 156, 18)">
                        <div class="box-header with-border">
                            <h4 class="box-title">Ghi chú</h4>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="max-height: 400px; overflow-y: scroll;">
                            <?php if (!empty($notes)): ?>
                            <?php foreach ($notes as $note): ?>
                            <div class="box-google-keep" title="Click để xem chi tiết">
                                <div class="text-ellipsis">
                                    <?php if (!empty($note['title'])): ?>
                                    <h4><?= $note['title'] ?></h4>
                                    <?php endif; ?>
                                            <?= $note['content'] ?>
                                </div>
                                <div class="masonry-footer text-right">Đã chỉnh sửa <?= date('d-m-Y H:i:s', strtotime($note['updated_at'])) ?></div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                                <div class="text-center">Chưa có ghi chú nào.</div>
                            <?php endif; ?>
                        </div>
                        <div class="box-footer text-center">
                            <a href="<?=Url::build('notes')?>" class="uppercase" target="_blank">Xem tất cả</a>
                        </div>
                    </div>
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h4 class="box-title">Lịch sử hoạt động</h4>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <ul class="products-list product-list-in-box">
                                <!--LIST:logs-->
                                <li class="item small" title="[[|logs.description|]]">
                                    <?php echo MiString::display_sort_title([[=logs.title=]],5)?> <span class="label label-warning pull-right">[[|logs.user_id|]]</span>
                                    <span class="direct-chat-timestamp pull-right"><?php echo date('H:i\' d/m/Y',[[=logs.time=]])?></span>
                                </li>
                                <!--/LIST:logs-->
                            </ul>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-center">
                            <a href="<?=Url::build('log')?>" class="uppercase">Xem tất cả</a>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                    <!--/IF:cond-->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ghi chú</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.box-google-keep').click(function() {
            $('#noteModal .modal-body').empty().html($(this).find('.text-ellipsis').html())

            $('#noteModal').modal('show')
        })
    });
    function updateRank(code,Loading) {
        let doSth;
        let table
        switch (code) {
            case 'SALE':
                doSth = 'get_top_sales';
                table = 'topSaleTable';
                break;
            case 'MKT':
                doSth = 'get_top_mkts';
                table = 'topMktTable';
                break;
        }
        $.ajax({
            method: "POST",
            url: 'work-auth/dashboard.php',
            dataType: 'json',
            data : {
                'do':doSth,
                'date_from':$('#date_from').val(),
                'date_to':$('#date_to').val()
            },
            beforeSend: function(){
                $('#'+table+' tr:not(:first)').children().remove();
                Loading.show();
            },
            success: function(mydata){
                let c=1;
                let totalRevenue = 0;
                let totalQty = 0;
                let Pos = 1;
                for(let i in mydata){
                    let Revenue = to_numeric(mydata[i].revenue);
                    totalRevenue +=Revenue;
                    let Qty = to_numeric(mydata[i].qty);
                    totalQty +=Qty;
                    $('#'+table).append('<tr class="row-'+c+'"><td>'+((c==1)?'<i class="fa fa-trophy"></i>':Pos)+'</td><td>'+mydata[i].name+'</td><td class="text-center">'+Qty+'</td><td class="text-right">'+Revenue.toFixed(2)+'</td></tr>');
                    c++;
                    Pos++;
                }
                $('#'+table).append('<tr class="text-bold"><td></td><td>Tổng</td><td class="text-center">'+totalQty+'</td><td class="text-right">'+totalRevenue.toFixed(2)+'</td></tr>');
                Loading.hide();
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
</script>