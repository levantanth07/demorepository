<table cellpadding="5" cellspacing="0" width="100%" class="sign-in-bound" style="margin: 0px 0 0 20px;">
    <tr>
        <td width="46%" class="sign-in-welcome">[[.welcome.]] : <a target="_blank" href="personal.html"><b style="color:#009EE7;"><?php echo Session::get('user_id')?></b></a></td>
    </tr>
	<?php if(User::can_view(MODULE_NEWSADMIN,ANY_CATEGORY)){?>
    <tr>
        <td class="sign-in-welcome"><a target="_blank" href="news_admin.html">[[.manage_content.]]</a></td>
    </tr>
	<?php }?>
	<?php if(User::can_view(MODULE_USERADMIN,ANY_CATEGORY)){?>
    <tr>
        <td class="sign-in-welcome"><a target="_blank" href="user_admin.html">[[.manage_user.]]</a></td>
    </tr>
	<?php }?>
	<?php if(User::can_view(MODULE_SETTING,ANY_CATEGORY)){?>
    <tr>
        <td class="sign-in-welcome"><a target="_blank" href="setting.html">[[.config_your_site.]]</a></td>
    </tr>
	<?php }?>
	<?php if(User::can_view(MODULE_MANAGECONTACT,ANY_CATEGORY)){?>
	<tr>
		<td align="sign-in-welcome"><a style="font-size: 11px;color: #323232;" target="_blank" href="manage_contact.html">[[.manage_contact.]]</a></td>
	</tr>
	<?php }?>
    <tr>
        <td class="sign-in-welcome"><a class="sign-in-link" href="<?php echo URL::build('sign_out');?>&href=?<?php echo urlencode($_SERVER['QUERY_STRING'])?>">[[.logout.]]</a></td>
    </tr>
</table>