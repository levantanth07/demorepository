<div style="padding-left:5px;">
<?php if(MAP['check']==1){?>
	<table  width="100%"  cellpadding="2" cellspacing="2">
		<tr>
			<td style="font-weight:bold;">[[|question_name|]]</td>
		</tr>
		<!--LIST:items-->
		<tr>
			<td width="100%" valign="top">
			<?php if (MAP['type']==0)
			{?>
				<input class="option-class" name="survey_id[]" id="survey_id" type="radio" value="[[|items.id|]]">&nbsp;&nbsp;[[|items.name|]]
			<?php }
			else
			{?>
				<input class="option-class" name="survey_id[]" id="survey_id" type="checkbox" value="[[|items.id|]]">&nbsp;&nbsp;[[|items.name|]]
			<?php }?>
			</td>
		</tr>
		<!--/LIST:items-->
	</table>
	<div>
		<input type="button" value="[[.view_result.]]" onclick="javascript:window.open('<?php echo URL::build('survey');?>&cmd=view&id=[[|survey_id|]]','view_survey','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0, width=400, height=300, left = 10,top = 10');" <?php if(PORTAL_ID=='#baobacninh'){?>class="banner-bound-frame-top-button" <?php }?>/>
		<input type="button" value="[[.vote.]]" onclick="javascript:window.open('<?php echo URL::build('survey');?>&cmd=view&id=[[|survey_id|]]&ids='+survey_list('survey_id[]'),'view_survey','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0, width=400, height=400, left = 10,top = 10');" <?php if(PORTAL_ID=='#baobacninh'){?>class="banner-bound-frame-top-button" <?php }?>/>
	</div>
	<?php }?>
	<?php if(EstoreLib::estore_can_edit()){?>[[|button|]] | <a target="_blank" href="<?php echo Url::build('survey_admin');?>">[[.admin_survey.]]</a><?php }?>
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