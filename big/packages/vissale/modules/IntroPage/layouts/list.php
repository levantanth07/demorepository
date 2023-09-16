<style>
    .widget-user-2 .box-footer {
        height: 220px;
        overflow: auto;
    }

    .widget-user-header {
        height: 100px;
    }

    .widget-user-image i {
        font-size: 52px;
    }

    .box-widget .fa-chevron-right {
        color: #fff;
        font-size: 50px;
        margin-top: 25px;
        margin-right: 5px;
    }
</style>

<div id="page">
    <section class="content-header">
        <h1 class="page-title">[[|title|]]</h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-yellow">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-desktop"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">MARKETING</h3>
                                    <h5 class="widget-user-desc">Các tính năng thuộc về marketing</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a target="_blank" data-step="3" data-intro="Thêm mới đơn hàng hoặc data số" target="_blank" href="<?= Url::build('admin_orders', ['cmd' => 'add']) ?>">Nhập data số <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('admin_orders', ['cmd' => 'import_excel']) ?>">Import data số <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('admin_source') ?>">Quản lý nguồn data số (kênh quảng cáo) <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('adv_money') ?>">Khai báo tiền quảng cáo <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" href="https://pages.tuha.vn/">Tương tác qua Facebook với khách hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('admin_fb_post') ?>">Gán Marketing vào bài viết hoặc Fan page <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('report') ?>#marketing">Xem báo cáo marketing <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-blue">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-female"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">TRỰC PAGE</h3>
                                    <h5 class="widget-user-desc">Các tính năng thuộc về trực page</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li target="_blank" data-step="4" data-intro="Danh sách đơn hàng"><a href="<?= Url::build('admin_orders') ?>">Danh sách đơn hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('on_page_report') ?>">Báo cáo trực page<i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('dashboard', ['do' => 'not_action']) ?>">Báo cáo đơn hàng chưa xử lý <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('dashboard', ['do' => 'order_action']) ?>">Báo cáo xử lý đơn hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" data-step="5" data-intro="Sử dụng QLBH pages để tương tác với khách hàng quan Facebook" href="https://pages.tuha.vn/">Tương tác qua Facebook với khách hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-aqua-active">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-headphones"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">SALE</h3>
                                    <h5 class="widget-user-desc">Các tính năng thuộc về sale</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="<?= Url::build('admin_orders') ?>">Danh sách đơn hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" href="<?= Url::build('admin_orders', ['cmd' => 'add']) ?>">Nhập data số <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" data-step="6" data-intro="Xem báo cáo sale" href="<?= Url::build('report') ?>#sale">Báo cáo sale <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-aqua-gradient">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-truck"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">VẬN ĐƠN</h3>
                                    <h5 class="widget-user-desc">Các tính năng thuộc về quản lý vận đơn</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="<?= Url::build('admin_shipping_service') ?>">Quản lý hình thức giao hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('admin-shipping-address') ?>">Khai báo địa chỉ lấy hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" data-step="7" data-intro="Theo dõi đơn vận chuyển" href="<?= Url::build('admin_orders', ['cmd' => 'manager-shipping']) ?>">Đơn vận chuyển <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('admin_orders', ['cmd' => 'shipping-processing']) ?>">Trạng thái kết nối giữa QLBH và đơn vị Vận chuyển <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-green">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-home"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">QUẢN LÝ KHO</h3>
                                    <h5 class="widget-user-desc">Nhập xuất, báo cáo nhập xuất, thẻ kho</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="<?= Url::build('qlbh_stock_report') ?>">Báo cáo nhập xuất tồn <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('qlbh_stock_report', ['do' => 'store_card']) ?>">Thẻ kho (sổ kho) <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a target="_blank" data-step="1" data-intro="Khai báo sản phẩm hàng hóa" href="<?= Url::build('product_admin') ?>">Khai báo sản phẩm hàng hóa <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('qlbh_warehouse') ?>">Khai báo kho <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('qlbh_nhap_kho', ['cmd' => 'add', 'type' => 'IMPORT']) ?>">Nhập hàng vào kho <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('qlbh_nhap_kho', ['cmd' => 'add', 'type' => 'EXPORT']) ?>">Xuất kho<i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <i class="fa fa-chevron-right pull-right"></i>
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-fuchsia">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">KẾ TOÁN</h3>
                                    <h5 class="widget-user-desc">Các tính năng hỗ trợ bộ phận kế toán</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="<?=Url::build('import_ma_buu_dien')?>" target="_blank">Import file excel bưu điện / vận chuyển <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'doanh_thu_nv'])?>" target="_blank">Báo cáo doanh thu sale <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'doanh_thu_mkt'])?>" target="_blank">Báo cáo doanh thu marketing <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'order_product','has_revenue'=>1])?>" target="_blank">Báo cáo doanh thu sản phẩm, hàng hóa <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('print-templates')?>" target="_blank">Setup mẫu in <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'cost'])?>" target="_blank">Báo cáo Tỷ lệ doanh thu ước chừng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard')?>&do=adv_money_day" target="_blank">Báo cáo Chi phí quảng cáo theo ngày <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'cost_mkt'])?>" target="_blank">Báo cáo Chi phí quảng cáo theo hệ thống <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'system_dashboard'])?>" target="_blank">Báo cáo Xếp hạng HKD <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?=Url::build('dashboard',['do'=>'system_revenue'])?>" target="_blank">Báo cáo Xếp hạng hệ thống <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header" style="background: #f1a899;color:#fff;">
                                    <div class="widget-user-image pull-left">
                                        <i class="fa fa-heart-o"></i>
                                    </div>
                                    <!-- /.widget-user-image -->
                                    <h3 class="widget-user-username">CSKH - UPSALE</h3>
                                    <h5 class="widget-user-desc">Các tính năng thuộc về CSKH</h5>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="<?= Url::build('admin_orders'); ?>">Danh sách đơn hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('customer'); ?>">Danh sách khách hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('customer-group'); ?>">Quản lý phân loại khách hàng <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('lich-hen'); ?>">Lịch hẹn <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                        <li><a href="<?= Url::build('lich-hen', ['do' => 'today']); ?>">Lịch hẹn hôm nay <i class="fa fa-arrow-circle-o-right pull-right"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>