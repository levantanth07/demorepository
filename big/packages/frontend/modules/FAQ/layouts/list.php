<form id="faqForm" name="faqForm" method="post">
<div class="faq-bound">
	<?php if(Form::$current->is_error()) echo Form::$current->error_messages();?>
	<!--IF:cond(Url::get('cmd')=='success')-->
	<div style="font-size:16px; padding-bottom:15px; font-weight:bold;" align="center">[[.Send_your_question_success.]]</div>
	<!--/IF:cond-->
	<div class="faq-send-bound">
		<div class="faq-send-question-name">
			<label for="user_name" style="vertical-align:top; display:block; padding-right:5px; float:left; font-weight:bold;">[[.Guest_name.]] :</label>
			<input type="text" name="user" id="user" style="width:250px; margin-right:5px;" /><br />
			<label for="question_name" style="vertical-align:top; display:block; float:left; width:100px; font-weight:bold;">[[.Question.]] :</label>
			<textarea name="question_name" id="question_name" style="width:99%; height:150px;" /></textarea>
		</div>
        <div style="padding-top:5px;">
            <span style="float:left; margin-right:5px;"><img id="imgCaptcha" src="capcha.php" /></span>
            <span class="register-verify-confirm-code" style="float:left"><input name="verify_confirm_code" type="text" id="verify_confirm_code" class="verify-confirm-code" style="width:103px; margin-right:5px;" maxlength="4"/></span>
            <div style="padding-left:10px; font-weight:bold; padding-bottom:5px; float:right"><input name="send_question" type="submit" id="send_question" value="[[.Send_question.]]" /></div>
		</div>
	</div>
	<div class="clear"></div>
	<div style="padding-top:10px;">
	<?php $i = 1;?>
	<!--LIST:item-->
	<div class="faq-content">
		<div class="faq-name"><?php echo $i; $i++;?>.<a href="<?php echo Url::build('xem-hoi-dap',array('name_id'=>[[=item.name_id=]]),REWRITE); ?>">[[|item.name|]]</a></div>
	</div>
	<!--/LIST:item-->
	</div>
	<div class="clear"></div>
</div>
<div class="news-list-paging">[[|paging|]]</div>
</form>
<script type="text/javascript">
jQuery(function(){
	jQuery('#user').focus();
	jQuery('#faqForm').validate({
		success: function(label) {
			label.text("Ok!").addClass("success");
		},
		rules: {
			user: {
				required: true
			},
			question_name:{
				required: true
			},
			verify_confirm_code: {
				required: true,
				minlength: 4,
				remote : 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=check_ajax'
			}
		},
		messages: {
			user: {
				required: '[[.user_is_required.]]'
			},
			question_name: {
				required: '[[.content_is_required.]]',
				maxlength: '[[.please_enter_at_smaller_or_with_1000_characters.]]'
			},
			verify_confirm_code: {
				required: '[[.verity_confirm_code_is_required.]]',
				minlength: '[[.verity_confirm_code_is_smaller_4.]]',
				remote: '[[.verify_confirm_code_is_invalid.]]'
			}
		}
	});
});
</script>
