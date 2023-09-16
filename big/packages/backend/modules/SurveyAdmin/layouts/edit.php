<script src="packages/core/includes/js/multi_items.js" type="text/javascript"></script>
<span style="display:none;">
	<span id="mi_survey_options_sample">
		<span id="input_group_#xxxx#" style="width:100%;text-align:left;">
			<input  name="mi_survey_options[#xxxx#][id]" type="hidden" id="id_#xxxx#">
			<!--LIST:languages-->
			<span class="multi_input">
				<textarea  name="mi_survey_options[#xxxx#][name_[[|languages.id|]]]" style="width:285px;height:20px;" id="name_[[|languages.id|]]_#xxxx#"></textarea>
			</span>
			<!--/LIST:languages-->
			<span class="multi_input">
					<input  name="mi_survey_options[#xxxx#][position]" style="width:60px;" type="text" id="position_#xxxx#">
			</span><span class="multi_input">
					<input  name="mi_survey_options[#xxxx#][count]" style="width:60px;" type="text" id="count_#xxxx#">
			</span>
			<span class="multi_input"><span style="width:20;">
				<img src="assets/default/images/buttons/delete.gif" onClick="mi_delete_row($('input_group_#xxxx#'),'mi_survey_options','#xxxx#');if(document.all)event.returnValue=false; else return false;" style="cursor:pointer;"/>
			</span></span><br>
		</span>
	</span>
</span>
<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked && this.id=='PublisherForm_checkbox')
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd)
	{
		document.EditSurveyAdminForm.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.content_manage_system.]]</legend>
	<div id="toolbar-title">[[.survey_admin.]]</div>
	<div id="toolbar-content"  align="right">
	<table>
	  <tbody>
		<tr>
		  <?php if(User::can_edit(false,ANY_CATEGORY)){?><td id="toolbar-save"  align="center"><a onclick="make_cmd('save')"> <span title="Save"> </span> [[.Save.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current();?>"> <span title="Cancel"> </span> [[.Cancel.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error())
		{
		?>
		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>
		<form name="EditSurveyAdminForm" method="post" >
	<table cellspacing="0" width="100%"><tr><td>
				<div class="tab-pane-1" id="tab-pane-survey">
				<!--LIST:languages-->
				<div class="tab-page" id="tab-page-survey-[[|languages.id|]]">
					<h2 class="tab">[[|languages.name|]]</h2>
					<div class="form-input-label">[[.name.]] (<span class="require">*</span>)</div>
					<div class="form_input">
							<input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" style="width:300px">
					</div><div class="form-input-label">[[.question.]]</div>
					<div class="form_input">
							<textarea id="question_[[|languages.id|]]" name="question_[[|languages.id|]]" cols="75" rows="10" style="width:99%; height:200px;overflow:hidden"></textarea><br />
					</div>
				</div>
				<!--/LIST:languages-->
				</div>
				</td></tr></table>
		<div class="form-input-label">[[.multi_is_select.]]:
		<input name="multi" id="multi" type="checkbox" value="1" <?php echo (URL::get('multi')?'checked':'');?>>
		</div>
		<?php
	if(URL::get('cmd')!='delete')
	{
	?>
		<fieldset>
			<legend>[[.survey_options.]]</legend>
			<div id="mi_survey_options_all_elems">
				<span>
					<!--LIST:languages-->
					<span class="multi-input-header" style="float:left"><span style="width:285px;float:left;">[[.name.]]([[|languages.name|]])</span></span>
					<!--/LIST:languages-->
					<span class="multi-input-header">
					<span style="width:60px;">[[.position.]]</span></span><span class="multi-input-header"><span style="width:60px;margin-left:20px">[[.count.]]</span></span>
					<span class="multi-input-header"><span style="width:20px;">&nbsp;</span></span>
					<br>
				</span>
				<br>
			</div>
			<input type="button" value="[[.Add.]]" onclick="mi_add_new_row('mi_survey_options');">
		</fieldset>
		<?php
	}
	?>
	<input type="hidden" value="1" name="confirm_edit"/>
	</form>
</fieldset>
<script type="text/javascript">
	mi_init_rows('mi_survey_options',
	<?php if(isset($_REQUEST['mi_survey_options']))
	{
		echo String::array2js($_REQUEST['mi_survey_options']);
	}
	else
	{
		echo '{}';
	}
	?>
);
</script>
