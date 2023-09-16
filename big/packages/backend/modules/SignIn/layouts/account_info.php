
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

<div class="stars">
    <div class="small"></div>
    <div class="medium"></div>
    <div class="big"></div>
</div>

<div class="container-login100">
    <div class="wrap-login100 p-l-25 p-r-25 p-t-0 p-b-20">
        <div class=text-center wrap">
            <h3 style='font-size: 46px;color:#FFF;text-align: center;'><img src="assets/standard/images/tuha_logo.png?v=03122021" alt="" width="100"></h3>
            <table class="table">
            <tr>
              <td>[[.welcome.]] : <a target="_blank" href="index062019.php?page=trang-ca-nhan"><b><?php echo Session::get('user_id')?></b></a></td>
            </tr>
            <?php if(User::is_login()){?>
            <tr>
            </tr>
            <?php }?>
            <tr>
              <td><a href="index062019.php?page=admin_orders" class="btn btn-success btn-lg">Vào phần mềm</a>
            <tr>
              <td><a class="sign-in-link" href="<?php echo URL::build('sign_out');?>&href=?<?php echo urlencode($_SERVER['QUERY_STRING'])?>">Thoát</a></td>
            </tr>
          </table>
        </div>
    </div>
</div>

<?php 
    // include_once ROOT_PATH . 'packages/backend/modules/SignIn/layouts/modal.php'; 
?>
<script src="assets/standard/js/bootstrap.min.js"></script>
<script src="assets/vissale/js/jquery.validate.min.js"></script>
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