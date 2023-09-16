<div class="survey-bound">
<?php if(MAP['check']==1){?>
	<div class="survey-question">
	<table  width="100%" border="0"  cellpadding="0" cellspacing="0">
    	<tr>
			<td class="survey-question-name">[[|question_name|]]</td>
		</tr>
		<tr>
			<td valign="top" class="survey-options">
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<!--LIST:items-->
					<tr class="survey-answer">
						<td width="1%">
							<?php if (MAP['type']==0){?>
								<input class="option-class" name="survey_id[]" id="survey_id" type="radio" value="[[|items.id|]]">
							<?php }else{?>
								<input class="option-class" name="survey_id[]" id="survey_id" type="checkbox" value="[[|items.id|]]">
							<?php }?>
						</td>
						<td>
							[[|items.name|]]
						</td>
					</tr>
					<!--/LIST:items-->
				</table>
			</td>
		</tr>
	</table>
	</div>
	<div class="survey-button">
	<input type="button" value="[[.vote.]]" class="survey-button" onclick="javascript:window.open('http://<?php echo $_SERVER['HTTP_HOST']?>/<?php echo URL::build('survey');?>&cmd=view&id=[[|survey_id|]]&ids='+survey_list('survey_id[]'),'view_survey','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0, width=500, height=400, left = 10,top = 10');"/>
	<input type="button" value="[[.view_result.]]" class="survey-button" onclick="javascript:window.open('http://<?php echo $_SERVER['HTTP_HOST']?>/<?php echo URL::build('survey');?>&cmd=view&id=[[|survey_id|]]','view_survey','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0, width=500, height=300, left = 10,top = 10');" />
	</div>
	<div class="clear"></div>
	<?php }?>
	<?php if(User::can_admin()){?><div style="padding:10px 0;">[[|button|]] | <a target="_blank" href="<?php echo Url::build('survey_admin');?>">[[.admin_survey.]]</a></div><?php }?>
</div>
<script type="text/javascript">
function survey_list(item_name)
{
	var arr = document.getElementsByName(item_name);
	if (arr.length)
	{
		st='';
		for (i=0;i<arr.length;i++)
		{
			if(arr[i].checked)
			{
				if(st!='')
				{
					st+=',';
				}
				st+=arr[i].value;
			}
		}
		return st;
	}
	else
	{
		if(arr.checked)
		{
			return arr.value;
		}
	}
	return '';
}
</script>