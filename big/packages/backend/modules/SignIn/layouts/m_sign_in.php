<div class="login cont">
  <div class="title"><h2>Đăng nhập</h2></div>
	<div class="body">
  <div class="error"><?php echo Form::error_messages();?></div>
  	<form name="SignInForm" id="SignInForm" method="post">
    <div>
        <label for="user_id">Tên đăng nhập:</label>
        <input name="user_id" type="text" id="user_id" tabindex="1" value="<?php if(isset($_COOKIE['forgot_user'])){echo substr($_COOKIE['forgot_user'],0,strpos($_COOKIE['forgot_user'],'_'));}?>"/>
    </div>
    <div>
        <label for="password">Mật khẩu :</label>
        <input name="password" type="password" id="password" tabindex="2" value="<?php if(isset($_COOKIE['forgot_user'])){echo substr($_COOKIE['forgot_user'],strpos($_COOKIE['forgot_user'],'_')+1);}?>"/>
    </div>
    <div>
        <input name="save_password" type="checkbox" id="save_password" value="1" /><label for="save_password"><small>Nhớ mật khẩu</small></label>
    </div>
   	<div class="button">
        <input type="submit" value=" Đăng nhập " tabindex="3" />
    </div>
    </form>
  </div>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#user_id').focus();
});
</script>