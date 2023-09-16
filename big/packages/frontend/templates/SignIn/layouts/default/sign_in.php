<form name="SignInForm" id="SignInForm" method="post">
<div class="sign-in-bound">
	<div class="sign-in-error"><?php echo Form::error_messages();?></div>
	<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td class="sign-in-field" width="1%" nowrap="nowrap"><label>[[.user_name.]] : </label></td>
            <td><input name="user_id" type="text" id="user_id" style="width:150px;" tabindex="1" value="<?php if(isset($_COOKIE['forgot_user'])){echo substr($_COOKIE['forgot_user'],0,strpos($_COOKIE['forgot_user'],'_'));}?>"/></td>
		</tr>
        <tr>
            <td class="sign-in-field" width="1%" nowrap="nowrap"><label>[[.password.]] : </label></td>
<td><input name="password" type="password" id="password" tabindex="2" style="width:150px;" value="<?php if(isset($_COOKIE['forgot_user'])){echo substr($_COOKIE['forgot_user'],strpos($_COOKIE['forgot_user'],'_')+1);}?>"/></td>		</tr>
		<tr>
			<td style="padding-left:0px;"><label for="save_password">[[.Remember_me.]]</label></td>
            <td><input name="save_password" type="checkbox" id="save_password" value="1" /></td>
		</tr>
		<tr>
			<td colspan="2" class="sign-in-button"><input type="submit" value="[[.sign_in.]]" tabindex="3" /></td>
		</tr>
	</table>
</div>
</form>