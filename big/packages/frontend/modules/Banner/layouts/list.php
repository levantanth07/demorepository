<link href="assets/standard/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/standard/css/font-awesome.min.css" rel="stylesheet">
<link href="assets/standard/css/animate.min.css" rel="stylesheet">
<link href="assets/standard/css/main.css?v=28102019" rel="stylesheet">
<script src="assets/standard/js/jquery.js"></script>
<script src="assets/standard/js/bootstrap.min.js"></script>
<script src="assets/standard/js/jquery.isotope.min.js"></script>
<script src="assets/standard/js/main.js"></script>
<script src="assets/standard/js/wow.min.js"></script>
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.cookie.js"></script>
<link href="assets/standard/css/media.css" rel="stylesheet">

<div class="top-bar">
  <div class="container">
      <div class="row">
          <div class="col-xs-8">
              <div class="top-number">
                  <!--IF:cond(User::is_login())-->
                  <?php
                  $href = Url::build('admin_orders');
                  if(User::can_admin(MODULE_GROUPSSYSTEM,false)){
                      $href = Url::build('admin-shop',['status'=>1]);
                      //"/index062019.php?page=admin-shop&status=1";
                  }
                  if(Session::get('admin_group')){
                      if(Session::get('account_type') == 3){
                          $href = Url::build('report');
                      }else{
                          $href = Url::build('admin_orders');
                      }
                  }
                  ?>
                  <a href="<?=$href?>" rel="nofollow" title="sử dụng QLBH" class="btn btn-sm btn-default">
                      <i class="fa fa-sign-in"></i> Vào phần mềm
                  </a>
                  <!--ELSE-->
                  <a class="btn btn-sm btn-default" href="dang-nhap/" rel="nofollow" title="Đăng nhập QLBH">
                      <i class="fa fa-sign-in"></i> Đăng nhập
                  </a> 
                  <!-- <a class="btn btn-sm btn-default" href="dang-nhap/?do=dang-ky" rel="nofollow" title="Đăng ký tài khoản QLBH">
                      <i class="fa fa-user"></i> Đăng ký
                  </a> -->
                  <!--/IF:cond-->
              </div>
          </div>
          <div class="col-xs-4">
             <div class="social">
                  <ul class="social-share">
                      <li><a href="https://itunes.apple.com/us/app/tuha-boss/id1437087775" target="_blank"><i class="fa fa-apple"></i></a></li>
                      <li><a href="https://play.google.com/store/apps/details?id=pal.tuha.mobile" target="_blank" title="Ứng dụng TUHA Boss trên Android"><i class="fa fa-android"></i></a></li>
                  </ul>
             </div>
          </div>
      </div>
  </div><!--/.container-->
</div><!--/.top-bar-->
<div class="logo-block">
    <!-- container -->
    <div class="container">
        <div class="row">
            <!-- col-md-2 -->
            <div class="col-md-3 col-sm-4">
                <a href="" rel="nofollow" title="Trang chủ">
                    <img src="/assets/standard/images/tuha_logo.png?v=03122021" alt="<?php echo Portal::get_setting('site_name')?>" onClick="window.location='';" height="60">
                </a>
            </div><!-- col-md-2 /- -->
            <!-- col-md-4 -->
            <div class="col-md-6 col-sm-8 pull-right row">
                <div class="col-md-6 col-sm-6 col-sm-offset-2 col-md-offset-2 call-us">
                    <img src="assets/standard/images/mobile-icon.png" alt="mobile-icon">
                    <p>
                        CSKH - Mua hàng <span><?=Portal::get_setting('hot_line')?></span>
                    </p>
                </div><!-- col-md-7 /- -->
                <div class="cart col-md-4 col-sm-4 text-right ow-padding-left">
                    <p>
                        <i class="fa fa-phone-square"></i> Tư vấn: 08.4444.3333
                    </p>
                    <p>
                        Tối ưu kinh doanh online
                    </p>
                </div><!-- col-md-5 /- -->
            </div><!-- col-md-4 /- -->
        </div>
    </div>
</div>
<div class="menu-block">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-default col-md-9">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        [[|item_ul_categories|]]
                    </ul>
                </div>
            </nav><!--/nav-->
            <div class="col-md-3 quote">
                <!--IF:cond(User::is_login())-->
                <?php
                $href = 'index062019.php?page=admin_orders';
                if(User::can_admin(MODULE_GROUPSSYSTEM,false)){
                    $href = "/index062019.php?page=admin-shop&status=1";
                }
                if(Session::get('admin_group')){
                    if(Session::get('account_type') == 3){
                        $href = "/index062019.php?page=report";
                    }else{
                        $href = "/index062019.php?page=admin_orders";
                    }
                }
                ?>
                <a href="<?=$href?>" rel="nofollow" title="sử dụng QLBH"><i class="fa fa-sign-in"></i> Vào phần mềm</a>
                <!--ELSE-->
                <a href="https://app.tuha.vn/dang-nhap/?do=dang-ky" rel="nofollow" title="Đăng ký sử dụng phần mềm QLBH"> <i class="fa fa-hand-o-right"></i> Đăng ký miễn phí</a>
                <!--/IF:cond-->
            </div>
        </div>
    </div>
</div>
<style>
    #promotionModal .modal-xl{box-shadow: none;background: none;}
    #promotionModal .modal-content{border-radius:0px;margin:0px;padding:0px; background: none; background-image: url('assets/standard/images/newletter_bg.png') !important;background-repeat: no-repeat;background-size: contain;}
    #promotionModal .title{color:#fff;}
    #promotionModal .desc{color:#fff;}
    @media (min-width: 1200px) {
        .modal-xl {
            width: 750px;
        }
}
</style>
<div class="modal fade" id="promotionModal" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row" style="padding-top: 100px;">
                    <div class="col-md-4">
                        <form name="BannerForm" id="BannerForm" method="post">
                            <h3 class="title">Nhận ưu đãi</h3>
                            <div class="form-group">
                                <input name="full_name" type="text" id="full_name" class="form-control" placeholder="Họ và tên">
                            </div>
                            <div class="form-group">
                                <input name="phone" type="text" id="phone" class="form-control" placeholder="Số điện thoại">
                            </div>
                            <div class="form-group">
                                <input name="send" type="button" id="send" class="btn btn-success" value="Nhận ưu đãi">
                                <a href="#" class="btn pull-right" onclick="$('#promotionModal').modal('toggle');return false;" style="color:#ff711a;">x Đóng</a>
                            </div>
                            <p><br></p>
                            <p><br></p>
                            <p><br></p>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
jQuery(document).ready(function(e) {
    $('.nav.navbar-nav >li').each(function(index, element) {
        if($(this).attr('page') == '<?php echo DataFilter::removeXSSinHtml($this->map['page']);?>'){
            if(typeof(jQuery(this).attr('name_id'))=='undefined'){
                $(this).addClass('active');
            }else if(!jQuery(this).attr('name_id')){
                $(this).addClass('active');
            }else{
                if($(this).attr('name_id') == '[[|category_name_id|]]'){
                    $(this).addClass('active');
                }
            }
        }
    });
    <?php $pages_arr = array('trang-tin','xem-trang-tin');
    if(( Url::get('page') and in_array(Url::get('page'),$pages_arr)) and $_SERVER['HTTP_HOST']!='big.shopal.vn' ) {?>
    var expired_cookie = $.cookie('send_contact');
    if (expired_cookie && expired_cookie ==1) {

    }else{
        $("#promotionModal").modal();
        $('#send').click(function(){
            if($('#full_name').val()==''){
                alert('Quý khách vui lòng nhập họ và tên!');
                $('#full_name').focus();
                return false;
            }
            let phoneno = /^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;
            if(!phone.value.match(phoneno)) {
                alert('Quý khách vui lòng nhập số điện thoại đúng định dạng!');
                $('#phone').focus();
                return false;
            }
            $(this).val('Đang xử lý...');
            $(this).attr('disabled',true);
            $.cookie('send_contact', 1, {
                path: '/',
                expires: 1
            });
            BannerForm.submit();
        });
    }
    <?php }?>
});
</script>
<!-- Start of  Zendesk Widget script -->
<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=f2e5ab29-06e3-4144-be93-8cefefe2a14f"> </script>
<!-- End of  Zendesk Widget script -->