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
  page_id="<?php echo Portal::get_setting('fb_page_id', false, '#default'); ?>"
  logged_in_greeting="Quý khách cần hỗ trợ gì không ạ?"
  logged_out_greeting="Quý khách cần hỗ trợ gì không ạ?">
</div>

<link rel="stylesheet" type="text/css" href="assets/vissale/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/animate/animate.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/css-hamburgers/hamburgers.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/select2/select2.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/util.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/main.css?v=29062021">
<link rel="stylesheet" type="text/css" href="assets/standard/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/css/app.css?d=08062022">

<div class="stars">
    <div class="small"></div>
    <div class="medium"></div>
    <div class="big"></div>
</div>

<div class="container-login100">
    <div class="wrap-login100 p-l-25 p-r-25 p-t-0 p-b-20">
        <form name="SignInForm" id="SignInForm" method="post" class="login100-form validate-form">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
            <div id="slogan" class="alert alert-warning full-width text-center">
                <div class="back">
                    <a href="https://big.shopal.vn">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrow-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-arrow-left fa-w-14 fa-2x"><path fill="#ccc" d="M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z" class=""></path></svg>
                    </a>
                </div>
                <p>Tin Tưởng, Uy tín, Hài lòng, An tâm !</p>
            </div>
            <div class="alert alert-warning" style="display: none;">
                Thông báo: <br>
                Server sẽ tạm dừng hoạt động lúc 00h ngày 27.12 để nâng cấp hệ thống đảm bảo ổn định hơn. <br>
                Thời gian dự kiến hoạt động trở lại: 2h sáng ngày 27.12<br>
                Kính mong quý khách thông cảm!
            </div>

            <div class="logo-image p-b-20" style="width: 100%;display: flex;justify-content: center;justify-items: center;align-items: center;">
                <a href="https://big.shopal.vn">
                    <img src="assets/standard/images/tuha_logo.png?v=03122021" alt="Logo">
                </a>
            </div>

            <div class="container-fluid" style="flex-grow: 1;">
                <?php if(Form::$current->is_error()) echo Form::$current->error_messages();?>
            </div>
            <div class="wrap-input100 validate-input">
                <input class="input100<?=Form::$current->is_error() ? ' error' : ' valid'?>" type="text" name="user_id" id="user_id" placeholder="Tên đăng nhập">
                    <i class="anticon anticon-user">
                        <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="user" aria-hidden="true"><path d="M858.5 763.6a374 374 0 00-80.6-119.5 375.63 375.63 0 00-119.5-80.6c-.4-.2-.8-.3-1.2-.5C719.5 518 760 444.7 760 362c0-137-111-248-248-248S264 225 264 362c0 82.7 40.5 156 102.8 201.1-.4.2-.8.3-1.2.5-44.8 18.9-85 46-119.5 80.6a375.63 375.63 0 00-80.6 119.5A371.7 371.7 0 00136 901.8a8 8 0 008 8.2h60c4.4 0 7.9-3.5 8-7.8 2-77.2 33-149.5 87.8-204.3 56.7-56.7 132-87.9 212.2-87.9s155.5 31.2 212.2 87.9C779 752.7 810 825 812 902.2c.1 4.4 3.6 7.8 8 7.8h60a8 8 0 008-8.2c-1-47.8-10.9-94.3-29.5-138.2zM512 534c-45.9 0-89.1-17.9-121.6-50.4S340 407.9 340 362c0-45.9 17.9-89.1 50.4-121.6S466.1 190 512 190s89.1 17.9 121.6 50.4S684 316.1 684 362c0 45.9-17.9 89.1-50.4 121.6S557.9 534 512 534z"></path></svg>
                    </i>
            </div>
            <div class="wrap-input100 validate-input input-password">
                <input class="input100<?=Form::$current->is_error() ? ' error' : ' valid'?>" type="password" name="password" id="password" placeholder="Mật khẩu">
                <i class="anticon anticon-lock">
                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true"><path d="M832 464h-68V240c0-70.7-57.3-128-128-128H388c-70.7 0-128 57.3-128 128v224h-68c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V496c0-17.7-14.3-32-32-32zM332 240c0-30.9 25.1-56 56-56h248c30.9 0 56 25.1 56 56v224H332V240zm460 600H232V536h560v304zM484 701v53c0 4.4 3.6 8 8 8h40c4.4 0 8-3.6 8-8v-53a48.01 48.01 0 10-56 0z"></path></svg>
                </i>
                <i class="icon-eye icon-right">
                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                        <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                    </svg>
                </i>
                <i class="icon-eye hide icon-right">
                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                        <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                    </svg>
                </i>
            </div>

            <?php if ([[=is_show_captcha=]]) : ?>
            <div class="wrap-input100" style="flex-direction: row;">
                <div>
                    <?php include "captcha/captcha.php";
$_SESSION['captcha'] = simple_php_captcha();?>
                    <img src="<?php echo $_SESSION['captcha']['image_src']; ?>" height="50" alt="Captcha">
                </div>
                <div class="validate-input">
                    <input  name="captcha" id="captcha" maxlength="5" placeholder="Mã bảo vệ" class="input100" type="text" autocomplete=off >
                    <span class="focus-input100"></span>
                    <i class="anticon anticon-lock">
                        <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true"><path d="M832 464h-68V240c0-70.7-57.3-128-128-128H388c-70.7 0-128 57.3-128 128v224h-68c-17.7 0-32 14.3-32 32v384c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V496c0-17.7-14.3-32-32-32zM332 240c0-30.9 25.1-56 56-56h248c30.9 0 56 25.1 56 56v224H332V240zm460 600H232V536h560v304zM484 701v53c0 4.4 3.6 8 8 8h40c4.4 0 8-3.6 8-8v-53a48.01 48.01 0 10-56 0z"></path></svg>
                    </i>
                </div>
            </div>
        <?php endif; ?>
            <div class="container-login100-form-btn">
                <button type="submit" class="login100-form-btn">
                    Đăng nhập
                </button>
            </div>
            <?php if ([[=is_show_captcha=]]) : ?>
            <p style="margin: 10px 0 0 0; font-size: 12px; text-align: justify;">
                Vì lý do đảm bảo <strong>an toàn</strong> cao nhất cho hệ thống, QLBH thêm xác nhận <strong>mã bảo vệ</strong> khi đăng nhập. Quý khách vui lòng nhập chính xác mã bảo vệ
            </p>
            <?php endif;?>
            <hr>
            <div class="container-login100-form-btn">Cảm ơn bạn!</div>
            <div class="text-center w-full p-t-55 hidden">
                <span style="display:inline-block;width: 20px;"></span>
                <a class="txt1 bo1 hov1" href="#" style="onclick="return false;">
                    Quên mật khẩu => Vui lòng liên hệ quản lý shop của bạn để được cấp lại.
                </a>
            </div>
        </form>
    </div>
</div>

<?php 
    // include_once ROOT_PATH . 'packages/backend/modules/SignIn/layouts/modal.php'; 
?>
<script src="assets/standard/js/bootstrap.min.js"></script>
<script src="assets/vissale/js/jquery.validate.min.js"></script>
<script src="assets/vissale/js/jquery.validate.min.js"></script>
<script src="assets/vissale/js/app.js?d=08062022"></script>
<script !src="">
    $("#SignInForm").validate({
        rules: {
            user_id: {
                required: true,
            },
            password: {
                required: true,
            },
            captcha: {
                required: true,
                maxlength: 5
            },
        },
        messages: {
            user_id: {
                required: "Tên đăng nhập không được để trống",
            },
            password: {
                required: "Mật khẩu không được để trống",
            },
            captcha: {
                required: "Capcha không được để trống",
                maxlength: "Capcha tối đa 5 ký tự",
            }
        }
    });
</script>
<?php if(isset($_SESSION['openValidateUserInfo']) && $_SESSION['openValidateUserInfo'] == 1):?>
<?php unset($_SESSION['openValidateUserInfo']);?>
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
<script>
    $('#openValidateUserInfo').modal();
</script>
<?php endif;?>

<?php if(isset($_SESSION['deactiveUser']) && $_SESSION['deactiveUser'] == 1):?>
    <?php unset($_SESSION['deactiveUser']);?>
    <div id="deactiveUser" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
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

<?php if(isset($_SESSION['changedPassword']) && $_SESSION['changedPassword'] == 1):?>
<?php unset($_SESSION['changedPassword']);?>
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
<script>
    $('#changedPassword').modal();
</script>
<?php endif;?>