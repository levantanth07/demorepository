<style>
    body {
        height: auto;
    }
    .content {
        min-height: 250px;
        padding: 15px;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: rgb(255, 255, 255);
        border-top: 3px solid rgb(210, 214, 222);
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .box.box-solid {
        border-top: 0;
    }
    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }
    .donhang-search-form {
        margin-bottom: 15px;
    }
    .btn-default .badge {
        color: rgb(255, 255, 255);
        background-color: rgb(51, 51, 51);
    }
    .page-bottom {
        border-top: 1px solid rgb(241, 241, 241);
        padding-top: 10px;
    }
    .float-right {
        float: right
    }
    ul.list-group li:nth-child(2n) {
        background: rgb(245, 245, 245)
    }
    .img {
        float: left;
        width: 60px;
        height: 60px;
        margin: 10px;
    }
    .text-report {
        float: left;
        width: calc(100% - 120px);
        color: rgb(51, 51, 51);
    }
    .text-head {
        color: rgb(0, 136, 255);
        padding: 7px;
        font-size: 16px;
        padding-left: 0px;
    }
    .box-body div {
        font-size: 12px;
    }
    .report-item {
        border: 1px solid rgb(226, 230, 232);
        margin-bottom: 15px;
        height: 80px;
        padding: 0px;
        overflow: hidden;
        border-radius: 5px;
    }
    .box-body .row > * {
        margin-bottom: 15px;
    }
    .img i {
        font-size: 52px;
    }
</style>
<?php
    $quyen_admin_marketing       = [[=quyen_admin_marketing=]];
    $quyen_marketing             = [[=quyen_marketing=]];
    $quyen_bc_doanh_thu_mkt      = [[=quyen_bc_doanh_thu_mkt=]];  
    $quyen_gandon                = [[=quyen_gandon=]];
    $quyen_chia_don              = [[=quyen_chia_don=]];
    $quyen_bc_doanh_thu_nv       = [[=quyen_bc_doanh_thu_nv=]];
    $is_account_group_manager    = [[=is_account_group_manager=]];
    $quyen_xem_bc_bxh_vinh_danh  = [[=quyen_xem_bc_bxh_vinh_danh=]];
    $quyen_xem_bc_doi_nhom       = [[=quyen_xem_bc_doi_nhom=]];
    $quyen_ke_toan               = [[=quyen_ke_toan=]];
    $quyen_van_don               = [[=quyen_van_don=]];
    $is_account_group_department = [[=is_account_group_department=]];
    $xem_khoi_bc_marketing       = [[=xem_khoi_bc_marketing=]];
    $xem_khoi_bc_sale            = [[=xem_khoi_bc_sale=]];
    $xem_khoi_bc_chung           = [[=xem_khoi_bc_chung=]];
    $xem_khoi_bc_truc_page       = [[=xem_khoi_bc_truc_page=]];
?>
<div id="page">
    <section class="content-header">
        <h1 class="page-title"><?= [[=title=]]?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php if(is_system_user() || check_system_user_permission('xembaocao')){?>
                            <div class="box box-success">
                                <div class="box-header">
                                    <h3 class="box-title">Báo cáo hệ thống</h3>
                                    <div class="box-tools">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'system_dashboard'])?>">
                                                <div class="img">
                                                    <i class="fa fa-sitemap"></i>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Bảng xếp hạng HKD</h4></div>
                                                    <div>Theo dõi Bảng xếp hạng HKD.</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?php if (Privilege::xemBcDoanhThuHeThong()):?>
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'system_revenue'])?>">
                                                <div class="img">
                                                    <i class="fa fa-sitemap"></i>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Bảng xếp hạng hệ thống</h4></div>
                                                    <div>Theo dõi bảng xếp hạng hệ thống.</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif;?>

                                    <?php if(is_system_user()):?>
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'cost'])?>">
                                                <div class="img">
                                                    <i class="fa fa-sitemap"></i>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Báo cáo tỷ lệ doanh thu ước chừng</h4></div>
                                                    <div>Báo cáo tỷ lệ doanh thu ước chừng</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif;?>

                                    <?php if(is_system_user()):?>
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'cost_mkt'])?>">
                                                <div class="img">
                                                    <i class="fa fa-sitemap"></i>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Báo cáo CPQC theo hệ thống</h4></div>
                                                    <div>Báo cáo CPQC theo hệ thống</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'vaccination'])?>">
                                                <div class="img">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-nurse" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-user-nurse fa-w-14 fa-2x" style="max-height: 52px;"><path fill="#3c8dbc" d="M319.41,320,224,415.39,128.59,320C57.1,323.1,0,381.6,0,453.79A58.21,58.21,0,0,0,58.21,512H389.79A58.21,58.21,0,0,0,448,453.79C448,381.6,390.9,323.1,319.41,320ZM224,304A128,128,0,0,0,352,176V65.82a32,32,0,0,0-20.76-30L246.47,4.07a64,64,0,0,0-44.94,0L116.76,35.86A32,32,0,0,0,96,65.82V176A128,128,0,0,0,224,304ZM184,71.67a5,5,0,0,1,5-5h21.67V45a5,5,0,0,1,5-5h16.66a5,5,0,0,1,5,5V66.67H259a5,5,0,0,1,5,5V88.33a5,5,0,0,1-5,5H237.33V115a5,5,0,0,1-5,5H215.67a5,5,0,0,1-5-5V93.33H189a5,5,0,0,1-5-5ZM144,160H304v16a80,80,0,0,1-160,0Z" class=""></path></svg>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Báo cáo tiêm chủng Covid 19</h4></div>
                                                    <div>Báo cáo tiêm chủng Covid 19.</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="report-item">
                                            <a href="<?=Url::build('dashboard',['do'=>'vaccination_chart'])?>">
                                                <div class="img">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="syringe" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-syringe fa-w-16 fa-2x" style="max-height: 52px;"><path fill="#3c8dbc" d="M201.5 174.8l55.7 55.8c3.1 3.1 3.1 8.2 0 11.3l-11.3 11.3c-3.1 3.1-8.2 3.1-11.3 0l-55.7-55.8-45.3 45.3 55.8 55.8c3.1 3.1 3.1 8.2 0 11.3l-11.3 11.3c-3.1 3.1-8.2 3.1-11.3 0L111 265.2l-26.4 26.4c-17.3 17.3-25.6 41.1-23 65.4l7.1 63.6L2.3 487c-3.1 3.1-3.1 8.2 0 11.3l11.3 11.3c3.1 3.1 8.2 3.1 11.3 0l66.3-66.3 63.6 7.1c23.9 2.6 47.9-5.4 65.4-23l181.9-181.9-135.7-135.7-64.9 65zm308.2-93.3L430.5 2.3c-3.1-3.1-8.2-3.1-11.3 0l-11.3 11.3c-3.1 3.1-3.1 8.2 0 11.3l28.3 28.3-45.3 45.3-56.6-56.6-17-17c-3.1-3.1-8.2-3.1-11.3 0l-33.9 33.9c-3.1 3.1-3.1 8.2 0 11.3l17 17L424.8 223l17 17c3.1 3.1 8.2 3.1 11.3 0l33.9-34c3.1-3.1 3.1-8.2 0-11.3l-73.5-73.5 45.3-45.3 28.3 28.3c3.1 3.1 8.2 3.1 11.3 0l11.3-11.3c3.1-3.2 3.1-8.2 0-11.4z" class=""></path></svg>
                                                </div>
                                                <div class="text-report">
                                                    <div class="text-head"><h4 class="title">Biểu đồ tiêm chủng Covid 19</h4></div>
                                                    <div>Biểu đồ tiêm chủng Covid 19.</div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <?php if([[=quyen_xem_bc_bxh_vinh_danh=]] == true): ?>
                                <div class="box box-success">
                                    <div class="box-header">
                                        <h3 class="box-title">Báo cáo BXH Shop</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a target="_blank" href="<?=Url::build('dashboard')?>&do=report_month">
                                                    <div class="img">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Xếp hạng Vinh danh theo Tháng</h4></div>
                                                        <div>Xếp hạng Vinh danh theo Tháng.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a target="_blank" href="<?=Url::build('dashboard')?>&do=report_day">
                                                    <div class="img">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Xếp hạng Vinh danh theo Ngày</h4></div>
                                                        <div>Xếp hạng Vinh danh theo Ngày.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(
                                (Session::get('account_type')==3 and Session::get('admin_group'))
                                or $xem_khoi_bc_sale or $quyen_bc_doanh_thu_nv 
                                or $quyen_xem_bc_doi_nhom or $is_account_group_manager 
                                or $is_account_group_department
                                or checkPermissionAccess(['BC_DOANH_THU_NV','CSKH','BUNGDON2','BUNGDON_NHOM','CHIADON','cs','CUSTOMER','ADMIN_MARKETING','BC_BXH_VINH_DANH','admin_ketoan','XUAT_EXCEL','KE_TOAN','BUNGDON','HCNS','ADMIN_CS','ADMIN_KHO','QUYEN_GIAM_SAT','VAN_DON','BC_DOANH_THU_MKT','BC_DOANH_THU_NV','XUATKHO','GANDON'])
                            ): ?>
                                <a name="sale"></a>
                                <div class="box box-success">
                                    <div class="box-header">
                                        <h3 class="box-title">Báo cáo sale</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if((Session::get('account_type')==3 and Session::get('admin_group'))): ?>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=doanh_thu_tong">
                                                        <div class="img">
                                                            <i class="fa fa-lemon-o"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo doanh thu tổng</h4></div>
                                                            <div>Theo dõi doanh thu tổng.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($quyen_bc_doanh_thu_nv or $quyen_xem_bc_doi_nhom or $is_account_group_manager or $is_account_group_department or $xem_khoi_bc_sale or [[=quyen_cskh=]]): ?>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=doanh_thu_nv">
                                                        <div class="img">
                                                            <i class="fa fa-money"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo doanh thu nhân viên</h4></div>
                                                            <div>Theo dõi chi tiết doanh thu nhân viên theo tháng.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($xem_khoi_bc_sale or $quyen_van_don or $quyen_gandon or $quyen_ke_toan or $is_account_group_manager or [[=quyen_cskh=]]): ?>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=reason_fail">
                                                        <div class="img">
                                                            <i class="fa fa-money"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo Lý do đơn chuyển hàng chưa thành công</h4></div>
                                                            <div>Theo dõi chi tiết doanh thu nhân viên theo tháng.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($xem_khoi_bc_sale or AdminReport::$quyen_bc_doanh_thu_nv or [[=quyen_cskh=]]): ?>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=report">
                                                        <div class="img">
                                                            <i class="fa fa-compass"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo doanh thu theo trạng thái</h4></div>
                                                            <div>Theo dõi chi tiết doanh thu theo trạng thái các tháng.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=ty_le_chot_don">
                                                        <div class="img">
                                                            <i class="fa fa-signal"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo tỷ lệ chốt đơn hàng</h4></div>
                                                            <div>Theo dõi tỷ lệ chốt đơn hàng theo ngày.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=tong_hop_sale">
                                                        <div class="img">
                                                            <i class="fa fa-table"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo tổng hợp sale</h4></div>
                                                            <div>Tổng hợp tình hình số chia, tỷ lệ chốt, tổng doanh thu, tỷ lệ doanh thu của sale</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=cskh">
                                                        <div class="img">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">Báo cáo đánh giá CSKH</h4></div>
                                                            <div>Tổng hợp tình hình CSKH, số đơn, doanh số.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=kho_so_sale">
                                                    <div class="img">
                                                        <i class="fa fa-list"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo kho số Sale</h4></div>
                                                        <div>Theo dõi kho số Sale theo nhân viên, thời gian.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($quyen_admin_marketing or $quyen_marketing or $quyen_bc_doanh_thu_mkt or $xem_khoi_bc_marketing): ?>
                                <a name="marketing">
                                <div class="box box-success">
                                    <div class="box-header">
                                        <h3 class="box-title">Báo cáo Marketing</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if($quyen_admin_marketing or $quyen_marketing or $xem_khoi_bc_marketing): ?>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=adv_money">
                                                        <div class="img">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">
                                                                    Báo cáo chi phí quảng cáo theo khung giờ</h4></div>
                                                            <div>Theo dõi chi phí quảng cáo chi tiết theo khung giờ.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=adv_money_new">
                                                        <div class="img">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">
                                                                    Báo cáo chi phí quảng cáo theo khung giờ (Mới)</h4></div>
                                                            <div>Theo dõi chi phí quảng cáo chi tiết theo khung giờ.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="report-item">
                                                    <a href="<?=Url::build('dashboard')?>&do=adv_money_day">
                                                        <div class="img">
                                                            <i class="fa fa-clock-o"></i>
                                                        </div>
                                                        <div class="text-report">
                                                            <div class="text-head"><h4 class="title">
                                                                    Báo cáo chi phí quảng cáo theo ngày</h4></div>
                                                            <div>Theo dõi chi phí quảng cáo chi tiết theo ngày.</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=kho_so_mkt">
                                                    <div class="img">
                                                        <i class="fa fa-desktop"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo kho số Marketing</h4></div>
                                                        <div>Theo dõi kho số Marketing theo nhân viên, thời gian.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=doanh_thu_mkt">
                                                    <div class="img">
                                                        <i class="fa fa-dot-circle-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu Marketing</h4></div>
                                                        <div>Theo dõi doanh thu của Marketing theo thời gian, nhóm tài khoản.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=kho_so&act=chart">
                                                    <div class="img">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Biểu đồ kho số Marketing</h4></div>
                                                        <div>Biểu đồ kho số theo tháng của năm.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=doanh_thu_upsale">
                                                    <div class="img">
                                                        <i class="fa fa-plus-square"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu Upsale</h4></div>
                                                        <div>Theo dõi doanh thu của Upsale theo thời gian, nhóm tài khoản.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=mkt_by_source">
                                                    <div class="img">
                                                        <i class="fa fa-globe"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo Marketing theo nguồn/kênh</h4></div>
                                                        <div>Theo dõi doanh thu Marketing theo nguồn</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=post">
                                                    <div class="img">
                                                        <i class="fa fa-facebook-square"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo đơn hàng theo bài post</h4></div>
                                                        <div>Theo dõi đơn hàng theo bài post.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($quyen_chia_don or $xem_khoi_bc_truc_page): ?>
                                <a name="Báo cáo trực page, tình trạng xử lý và chia đơn">
                                <div class="box box-success">
                                    <div class="box-header">
                                        <h3 class="box-title">Báo cáo trực page, tình trạng xử lý và chia đơn</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="index062019.php?page=on_page_report">
                                                    <div class="img">
                                                        <i class="fa fa-female"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo trực page</h4></div>
                                                        <div>Theo dõi tình trạng trực page của nhân viên.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=order_action">
                                                    <div class="img">
                                                        <i class="fa fa-book"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo xử lý đơn hàng của nhân viên</h4></div>
                                                        <div>Theo dõi tình trạng xử lý đơn hàng của nhân viên.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=not_action">
                                                    <div class="img">
                                                        <i class="fa fa-folder-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo đơn hàng chưa xử lý</h4></div>
                                                        <div>Theo dõi tình trạng đơn hàng chưa xử lý.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=kho_so&sale=1">
                                                    <div class="img">
                                                        <i class="fa fa-list"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo kho số Sale</h4></div>
                                                        <div>Theo dõi kho số Sale theo nhân viên, thời gian.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(Session::get('admin_group') or $xem_khoi_bc_chung): ?>
                                <a name="Báo cáo chung">
                                <div class="box box-success">
                                    <div class="box-header">
                                        <h3 class="box-title">Báo cáo chung</h3>
                                        <div class="box-tools">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=zone">
                                                    <div class="img">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Biểu đồ thống kê bán hàng theo tỉnh thành</h4></div>
                                                        <div>So sánh tỷ trọng bán hàng theo tỉnh thành.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=chart">
                                                    <div class="img">
                                                        <i class="fa fa-bar-chart-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Biểu đồ doanh thu nhân viên</h4></div>
                                                        <div>Theo dõi doanh thu nhân viên theo tháng dưới dạng biểu đồ.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=pie">
                                                    <div class="img">
                                                        <i class="fa fa-dropbox"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Biểu đồ trạng thái đơn hàng</h4></div>
                                                        <div>Theo dõi trạng thái đơn hàng theo tháng.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=by_date">
                                                    <div class="img">
                                                        <i class="fa fa-briefcase"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu theo ngày</h4></div>
                                                        <div>Theo dõi chi tiết doanh thu theo ngày.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=transport">
                                                    <div class="img">
                                                        <i class="fa fa-plane"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo chi phí vận chuyển</h4></div>
                                                        <div>Theo dõi chi phí vận chuyển, tỷ lệ chuyển hoàn</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=order_product">
                                                    <div class="img">
                                                        <i class="fa fa-inbox"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Bảng kê sản phẩm hàng hóa</h4></div>
                                                        <div>Theo dõi sản phẩm trong kho.</div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="report-item">
                                                <a href="<?=Url::build('dashboard')?>&do=order_product&has_revenue=1">
                                                    <div class="img">
                                                        <i class="fa fa-moon-o"></i>
                                                    </div>
                                                    <div class="text-report">
                                                        <div class="text-head"><h4 class="title">Báo cáo doanh thu sản phẩm, hàng hóa</h4></div>
                                                        <div>Theo dõi doanh thu sản phẩm, hàng hóa.</div>
                                                    </div>
                                                </a>
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
    </section>
</div>
