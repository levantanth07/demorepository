<?php /*<footer id="footer" class="bg-animation p-90-60" style="background:url('assets/standard/images/jagged-min.jpg');">
    <div class="container">
        <div class="wg-footer__second-block pl-0 pr-0 pt-4 pb-4">
            <div class="ft-top">
                <div class="row justify-content-center">
                    <div class="item col-lg-2 col-md-2 col-sm-2 text-center">
                        <a href="https://itunes.apple.com/us/app/tuha-boss/id1437087775" target="_blank">
                            TUHA Boss
                            <img src="assets/standard/images/apple_store_icon.png" alt="TUHA Boss" class="img-responsive">
                        </a>
                    </div>
                    <div class="item col-lg-2 col-md-2 col-sm-2 text-center">
                        <a href="https://play.google.com/store/apps/details?id=pal.tuha.mobile" target="_blank">
                            TUHA Boss
                            <img src="https://pages.tuha.vn/assets/img/chplay.png" alt="TUHA Boss" class="img-responsive">
                        </a>
                    </div>
                    <div class="item col-lg-2 col-md-2 col-sm-2 text-center">
                        TUHA Pages
                        <a href="https://play.google.com/store/apps/details?id=pal.tuha.pages" target="_blank"><img src="https://pages.tuha.vn/assets/img/chplay.png" alt="TUHA Boss" class="img-responsive"></a>
                    </div>
                    <div class="item col-lg-6 text-center">
                        <h3 class="title-bold text-center">DANH SÁCH TÀI KHOẢN NGÂN HÀNG GIAO DỊCH</h3>
                        <p class="per text-center">Tên tài khoản: CTY CO PHAN CONG NGHE VA THUONG MAI PAL VIET NAM</p>
                        <p>Ngân Hàng BIDV Chi nhánh Ngọc Khánh Hà Nội</p>
                        <p><strong>STK: 28910000176710</strong></p>
                    </div>
                </div>
            </div>
            <div class="ft-bot">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="tit">Liên hệ tại TPHCM</div>
                        <p><em>hotro@palvietnam.vn</em></p>
                        <p class="pnb"><strong>CÔNG TY CỔ PHẦN CÔNG NGHỆ VÀ THƯƠNG MẠI PAL VIỆT NAM</strong></p>
                        <p>Địa chỉ: 172 Trần Quốc Thảo, Phường 7, Quận 3, TPHCM</p>
                        <p>Tổng đài CSKH: 03.9557.9557 (8h-23h)</p>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div class="tit">Liên hệ tại Hà Nội</div>
                        <p><em>hotro@palvietnam.vn</em></p><!--hotro.Tuha@gmail.com-->
                        <p class="pnb"><strong>CÔNG TY CỔ PHẦN CÔNG NGHỆ VÀ THƯƠNG MẠI PAL VIỆT NAM</strong></p>
                        <p>Địa chỉ: R4-Royal City – 72 Nguyễn Trãi – Thanh Xuân – Hà Nội</p>
                        <p>Tổng đài CSKH: 03.9557.9557 (8h-23h)</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="link">
                        <a href="https://www.facebook.com/tuha.vn/" rel="dofollow" target="_blank"class="label label-primary">Facebook Fan Page</a> |
                        <a href="bai-viet/chinh-sach/dieu-khoan-su-dung-phan-mem-quan-ly-ban-hang-tuha/" class="label label-default">Điều khoản sử dụng</a> |
                        <a href="bai-viet/chinh-sach/chinh-sach-bao-mat-cua-tuha/" class="label label-default">Chính sách bảo mật</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footer-copyright"><div class="container"><div class="row justify-content-end"><div class="col-12 col-sm-6"><p class="small thin white-light sm-ac">Copyrights 2019. All Rights Reserved. Developed by Pal Vietnam</p></div></div></div></div>
*/?>
<!--IF:is_admin(User::is_admin() and DEBUG==1)-->
    <hr>
    <div class="alert" style="background:#CCC;">
        <center>
            <a href="[[|link_structure_page|]]" target="_blank">Structure page</a>
            | <a href="[[|link_edit_page|]]" target="_blank">Edit page</a>
            | <a href="[[|delete_cache|]]" target="_blank">Delete cache</a>
        </center>
        <left>
            <div>Query: [[|total_query|]]</div>
            <div>Time excute: [[|timer|]]</div>
            <div>[[|requests|]]</div>
        </left>
    </div>
<!--/IF:is_admin-->

<style>
    #openValidateUserInfo p{
        margin-bottom: 10px;
        line-height: 1.5;
        font-weight: 400;
    }

    #openValidateUserInfo .alert{
        border-radius: 3px;
    }

    #openValidateUserInfo .alert-warning-custom{
        color: rgb(138, 109, 59) !important;
        background-color: rgb(252, 248, 227) !important;
        border: none;
        margin-top: 10px;
        font-weight: 200;
    }
</style>
<div class="modal fade" id="openValidateUserInfo" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-body" style="font-size: 14px;">
                <div style="padding: 40px 0; font-size: 16px; margin: 0">
                    Phiên làm việc của bạn đã kết thúc. 
                    Hồ sơ thông tin tài khoản chưa đầy đủ, 
                    bạn vui lòng liên hệ lại chủ sở hữu shop 
                    (<?php echo isset($_SESSION['ownerFullName']) ? $_SESSION['ownerFullName'] : '';?> - 
                    <?php echo isset($_SESSION['ownerPhone']) ? $_SESSION['ownerPhone'] : '';?>)
                </div>
                <div class='modal-footer' style="padding: 5px 0px">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php if(isset($_SESSION['openValidateUserInfo']) && $_SESSION['openValidateUserInfo'] == 1):?>
<?php unset($_SESSION['openValidateUserInfo']);?>
<script>
    $('#openValidateUserInfo').modal();
</script>
<?php endif;?>

<?php if(isset($_SESSION['deactiveUser']) && $_SESSION['deactiveUser'] == 1):?>
    <?php unset($_SESSION['deactiveUser']);?>
    <div id="deactiveUser" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm" style="width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title text-success">
                        Phiên làm việc của bạn đã kết thúc
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="">
                        <br>
                        <div class="col-md-12">
                            <label >Tài khoản của bạn đã bị tắt kích hoạt</label>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-success" style="width: 100%" data-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <script>
        $('#deactiveUser').modal();
    </script>
<?php endif;?>


<style>
    #changedPassword p{
        margin-bottom: 10px;
        line-height: 1.5;
        font-weight: 400;
    }

    #changedPassword .alert{
        border-radius: 3px;
    }

    #changedPassword .alert-warning-custom{
        color: rgb(138, 109, 59) !important;
        background-color: rgb(252, 248, 227) !important;
        border: none;
        margin-top: 10px;
        font-weight: 200;
    }
</style>
<div class="modal fade" id="changedPassword" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-body" style="font-size: 14px;">
                <div style="padding: 40px 0; font-size: 16px; margin: 0">
                    Tài khoản của bạn vừa được thay đổi mật khẩu, bạn vui lòng đăng nhập lại!
                </div>
                <div class='modal-footer' style="padding: 5px 0px">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php if(isset($_SESSION['changedPassword']) && $_SESSION['changedPassword'] == 1):?>
<?php unset($_SESSION['changedPassword']);?>
<script>
    $('#changedPassword').modal();
</script>
<?php endif;?>