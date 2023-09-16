
<link rel="stylesheet" type="text/css" href="assets/vissale/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/animate/animate.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/css-hamburgers/hamburgers.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/vendor/select2/select2.min.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/util.css">
<link rel="stylesheet" type="text/css" href="assets/vissale/login/css/main.css?v=29062021">
<link rel="stylesheet" type="text/css" href="assets/standard/css/bootstrap.min.css">
<style>
    .login100-form-title {
    font-size: 16px;
    font-weight: 600;
    color: #4a4a4a;
    line-height: 1.2;
    text-transform: uppercase;
    text-align: center;
    /*width: 100%;*/
    display: inline-block;
}
</style>
<div class="stars">
    <div class="small"></div>
    <div class="medium"></div>
    <div class="big"></div>
</div>
<div class="container-login100">
    <div class="wrap-login100 p-l-28 p-r-28 p-t-50 p-b-30">
        <form name="SignInOtherAccountForm" id="SignInOtherAccountForm" method="post" class="login100-form validate-form">
            <div class="p-b-10 wrap-input100 text-center">
                <span class="login100-form-title active">Đăng nhập bằng tài khoản khác</span>
            </div>
            
            <div class="logo-image p-b-20" style="width: 100%;display: flex;justify-content: center;justify-items: center;align-items: center;">
                <a href="https://big.shopal.vn">
                    <img src="assets/standard/images/tuha_logo.png?v=03122021" alt="Logo">
                </a>
            </div>
            <div class="wrap-input100 validate-input">
                <input type="text" class="input100 valid'?>" name="username" placeholder="Nhập tài khoản muốn truy cập">
                    <i class="anticon anticon-user">
                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="user" aria-hidden="true"><path d="M858.5 763.6a374 374 0 00-80.6-119.5 375.63 375.63 0 00-119.5-80.6c-.4-.2-.8-.3-1.2-.5C719.5 518 760 444.7 760 362c0-137-111-248-248-248S264 225 264 362c0 82.7 40.5 156 102.8 201.1-.4.2-.8.3-1.2.5-44.8 18.9-85 46-119.5 80.6a375.63 375.63 0 00-80.6 119.5A371.7 371.7 0 00136 901.8a8 8 0 008 8.2h60c4.4 0 7.9-3.5 8-7.8 2-77.2 33-149.5 87.8-204.3 56.7-56.7 132-87.9 212.2-87.9s155.5 31.2 212.2 87.9C779 752.7 810 825 812 902.2c.1 4.4 3.6 7.8 8 7.8h60a8 8 0 008-8.2c-1-47.8-10.9-94.3-29.5-138.2zM512 534c-45.9 0-89.1-17.9-121.6-50.4S340 407.9 340 362c0-45.9 17.9-89.1 50.4-121.6S466.1 190 512 190s89.1 17.9 121.6 50.4S684 316.1 684 362c0 45.9-17.9 89.1-50.4 121.6S557.9 534 512 534z"></path></svg>
                </i>
            </div>

            <button class="btn btn-primary" type="submit">Đăng nhập</button>
        </form>
    </div>

</div>


<script src="assets/standard/js/bootstrap.min.js"></script>
<script src="assets/vissale/js/jquery.validate.min.js"></script>
<script !src="">
    $("#SignInOtherAccount").validate({
        rules: {
            username: {
                required: true,
            },
        },
        messages: {
            username: {
                required: "Tên đăng nhập không được để trống",
            }
        }
    });
</script>