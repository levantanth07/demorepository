<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js#xfbml=1&version=v2.12&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your customer chat code -->
<div class="fb-customerchat"
  attribution="setup_tool"
  page_id="<?php echo Portal::get_setting('fb_page_id',false,'#default');?>"
  logged_in_greeting="Quý khách cần hỗ trợ gì không ạ?"
  logged_out_greeting="Quý khách cần hỗ trợ gì không ạ?">
</div>
<link rel="stylesheet" type="text/css" href="assets/vissale/login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/animate/animate.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/css-hamburgers/hamburgers.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/select2/select2.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/util.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/main.css?v=10062019">

<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,700" rel="stylesheet">
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-t-0 p-b-20 p-l-50 p-r-50" style="width:797px;">
            <form name="SignInForm" id="SignInForm" method="post" class="validate-form">
                <div class="row">
                    <div class="col-xs-12 p-t-25">
                        <div class="text-center">
                            <h1 class="alert alert-info text-center full-width">
                                Mở shop mới trên QLBH
                            </h1>
                            Tin tưởng, Uy tín, Hài lòng, An tâm!
                            <hr>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">Sử dụng miễn phí trong vòng 7 ngày.</div>
                <?php echo Form::error_messages();?>
                <div class="wrap-input100 validate-input m-b-16">
                    <input name="shop_name" type="text" id="shop_name" tabindex="1"class="input100" placeholder="Tên shop của Quý Khách"/>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <span class="lnr lnr-home"></span>
                    </span>
                </div>
                <div class="wrap-input100 validate-input m-b-16">
                    <input name="full_name" type="text" id="full_name" tabindex="2" class="input100" placeholder="Họ và tên của Quý Khách"/>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <span class="lnr lnr-user"></span>
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="wrap-input100 validate-input m-b-16">
                            <input name="user_id" type="text" id="user_id" tabindex="3" class="input100" placeholder="Tài khoản đăng nhập" data-toggle="tooltip" title="Quý khách vui lòng không nhập  dấu cách, số hoặc ký tự đặc biệt để làm tên tài khoản" autocomplete="off">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <span class="lnr lnr-users"></span>
                            </span>
                        </div>
                        <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
                            <input name="password" type="password" id="password" tabindex="4" value="<?php if(isset($_COOKIE['forgot_user'])){echo substr($_COOKIE['forgot_user'],strpos($_COOKIE['forgot_user'],'_')+1);}?>" class="input100" placeholder="Mật khẩu">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <span class="lnr lnr-lock"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="wrap-input100 validate-input m-b-16" data-validate="Valid email is required: ex@abc.xyz">
                            <input name="email" type="text" id="email" tabindex="5" class="input100" placeholder="Email"/>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <span class="lnr lnr-envelope"></span>
                            </span>
                        </div>
                        <div class="wrap-input100 validate-input m-b-16" data-validate="Valid email is required: ex@abc.xyz">
                            <input name="phone" type="text" id="phone" tabindex="6" class="input100" placeholder="Điện thoại"/>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <span class="lnr lnr-phone"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="wrap-input100 validate-input m-b-16" data-validate="Valid email is required: ex@abc.xyz">

                        </div>
                    </div>
                </div>
                <div class="container-login100-form-bt">
                    <div class="row">
                        <div class="col-sm-2">
                            <?php include("captcha/captcha.php");
                            $_SESSION['captcha'] = simple_php_captcha();?>
                            <img src="<?php echo $_SESSION['captcha']['image_src'];?>" height="50" alt="Captcha">
                        </div>
                        <div class="col-sm-4">
                            <input  name="captcha" id="captcha" maxlength="5" placeholder="Mã bảo vệ" class="input100" type="text" autocomplete=off >
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <span class="lnr lnr-lock"></span>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="login100-form-btn register">
                                ĐĂNG KÝ
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-t-10 text-center">
                    <div><label>Quý khách có tài khoản rồi vui lòng nhấn</label> <a href="?page=dang-nhap" class="btn btn-warning">Đăng nhập.</a></div>
                </div>
                <hr>
                <div class="container-login100-form-btn">Cảm ơn Quý Khách!</div>
                <input name="do" type="hidden" id="do">
                <input name="ref" type="hidden" id="ref">
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(e) {
        $('[data-toggle="tooltip"]').tooltip();
        $('.ls-modal').on('click', function(e){
            window.open($(this).attr('href'),'register','width=600,height=600,top=0,right=0');
            e.preventDefault();
        });
    });
</script>