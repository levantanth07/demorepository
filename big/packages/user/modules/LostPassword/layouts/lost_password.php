<div class="container">
  <div class="row">
    <div class="col-md-12">
        <div class="wrap">
            <br>
            <div class="text-center"><img src="https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021" alt="tuha.vn" onclick="if (!window.__cfRLUnblockHandlers) return false; window.location='';" height="60"></div>
            <p class="form-title">Khôi phục mật khẩu</p>
            <?php if(addslashes(Url::get('action'))=='success'){?>
            <center><div class="lost-password-notice" style="padding:20px 10px 10px 20px;font-size:16px;color:#fff;">Mẩu khẩu đã được gửi vào email của Quý khách. Quý khách vui lòng check email để khôi phục mật khẩu.</div></center>
            <?php }elseif(addslashes(Url::get('r'))=='true'){?>
            <center><div class="lost-password-notice" style="padding:20px 10px 10px 20px;font-size:20px;">Quý khách khôi phục mật khẩu thành công.<br>Nhấn vào <a class="btn btn-default" href="dhss-dang-nhap.html"><strong>đây</strong></a> để đăng nhập.</div></center>
            <?php }else{?>
                <div align="center"><?php echo Form::$current->error_messages();?></div>
                <form name="LostPasswordForm" method="post">
                    <div class="form-group text-center">
                        <input name="email" type="text" id="email" class="form-control" placeholder="Nhập email theo tài khoản của Quý khách cần khôi phục"><br>
                        <input name="get_password" type="submit" value=" + Xác nhận" id="get_password" class="btn btn-lg btn-success">
                    </div>
                </form>		
            <?php }?>
        </div>
     </div>
  </div>
</div>     