<script type="text/javascript">
jQuery(function(){
	jQuery('#full_name').focus();
	jQuery('#reset').click(function(){
		jQuery('#full_name').focus();
	});
	jQuery('#SendContactUsForm').validate({
		success: function(label) {
			label.text("Ok!").addClass("success");
		},
		rules: {
			full_name: {
				required: true
			},
			address:{
				required: true
			},
			email: {
				required: true,
				email: true
			},
			home_phone:{
				required: true
			},
			content: {
				required: true,
				maxlength: 1000
			},
			verify_comfirm_code: {
				required: true,
				minlength: 4,
				remote : 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=check_ajax'
			}
		},
		messages: {
			full_name: {
				required: '[[.full_name_is_required.]]'
			},
			address:{
				required: '[[.address_is_required.]]'
			},
			email: {
				required: '[[.email_is_required.]]',
				email: '[[.incorrect_email_format.]]'
			},
			home_phone:{
				required: '[[.phone_is_required.]]'
			},
			content: {
				required: '[[.content_is_required.]]',
				maxlength: '[[.please_enter_at_smaller_or_with_1000_characters.]]'
			},
			verify_comfirm_code: {
				required: '[[.verity_comfirm_code_is_required.]]',
				minlength: '[[.verity_comfirm_code_is_smaller_4.]]',
				remote: '[[.verify_comfirm_code_is_invalid.]]'
			}
		}
	});
});
</script>
<div class="contact-us-bound">
<?php echo Form::$current->error_messages();?>
<form name="SendContactUsForm" method="post" id="SendContactUsForm" action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
    <div style="line-height:20px;padding-bottom:10px;">
    	<?php
			if(isset($_SESSION['services_list']))
			{
				foreach($_SESSION['services_list'] as $key=>$value)
				{
					echo '<div style="padding-left:10px; color:#000000;"> - <a href="'.Url::build('service_detail',array('id'=>$value['id'],'name'=>$value['name']),'',true).'" style="color:#EFC739;">'.$value['name'].'</a></div>';
				}
			}
		?>
    </div>
    <div class="contact-us-require-field">(<span class="contact-us-star">*</span>) : [[.required_field.]]</div>
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="contact-us-field" width="1%" nowrap="nowrap"><label for="full_name">[[.full_name.]]<span class="contact-us-star"> *</span></label></td>
			<td align="left" class="contact-us-input-text"><input name="full_name" type="text" id="full_name" style="width:60%;" class="new_input" maxlength="100"></td>
		</tr>
		<tr>
			<td class="contact-us-field" width="1%" nowrap="nowrap"><label for="full_name">[[.address.]]<span class="contact-us-star"> *</span></label></td>
			<td align="left" class="contact-us-input-text"><input name="address" type="text" id="address"  style="width:60%;" class="new_input" maxlength="255"></td>
		</tr>
		<tr>
			<td class="contact-us-field" width="1%" nowrap="nowrap"><label for="full_name">[[.phone.]]<span class="contact-us-star"> *</span></label></td>
			<td align="left" class="contact-us-input-text"><input name="home_phone" type="text" id="home_phone" style="width:60%;" class="new_input" maxlength="50"></td>
		</tr>
		<tr>
			<td class="contact-us-field" width="1%" nowrap="nowrap"><label for="full_name">[[.email.]]<span class="contact-us-star"> *</span></label></td>
			<td align="left" class="contact-us-input-text"><input name="email" type="text" id="email" style="width:60%;" class="new_input" maxlength="255"></td>
		</tr>
	</table>
	<div class="contact-us-field">
		<label class="contact-us-field" for="content">[[.Content.]]<span class="contact-us-star"> *</span></label>
		<span class="contact-us-content"><textarea name="content" id="content" style="width:100%;height:100px; margin-bottom:5px;"></textarea></span>
	</div>
	<div class="contact-us-comfirm-code">
		<label for="verify_comfirm_code" style="float:left;">[[.verify_comfirm_code.]]<span class="contact-us-star">*</span></label>
		<span style="float:left; margin-right:5px; padding-left:40px;"><img id="imgCaptcha" src="capcha.php" /></span>
		<span class="register-verify-comfirm-code"><input name="verify_comfirm_code" type="text" id="verify_comfirm_code" style="width:103px;" maxlength="4"/></span>
		<div id="checkComfirmCode"></div>
	</div>
	<div style="padding:10px 0 5px;"><input name="submit" type="submit" id="submit" value=" [[.send_contact.]] " class="contact-us-button"><input name="reset" type="reset" id="reset" value=" [[.reset.]] " class="contact-us-button"></div>
	<?php if(User::can_admin()){?>
	<a href="<?php echo Url::build_current(array('cmd'=>'list'));?>">[[.View_all_contacts.]]</a>
	<?php }?>
</form>
</div>