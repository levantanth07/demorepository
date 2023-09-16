<?php if(MAP['check']==1)
{?>
<div id="view-survey" class="view-survey-bound" style="position:relative;">
	<div class="view-survey-question">[[|question|]]</div>
	<div class="view-survey-total-number">[[.total_number.]] : [[|num|]]</div>
	<hr>
	<div>
	<ul>
	<!--LIST:items-->
	<li class="view-survey-item">
			<div class="view-survey-label" style="color:[[|items.color|]]">[[|items.name|]] [[|items.count|]] [[.voting_paper.]]</div>
			<table width="100%" cellpadding="2" cellspacing="2">
				<tr>
					<td bgcolor="[[|items.color|]]" width="[[|items.width|]]%"></td>
					<td>[[|items.percent|]]%</td>
				</tr>
			</table>
	</li>
	<!--/LIST:items-->
	</ul>
	</div>
	<?php }
	else
	{?>
	<div class="view-survey-notice">[[.no_result.]]</div>
	<?php }?>
	<hr>
	<div class="view-survey-close-button"><input type="button" value="[[.close.]]" onclick="window.close();"></div>
</div>
<script type="text/javascript">
	//window.resizeTo( $('view-survey').offsetWidth+30, $('view-survey').offsetHeight+60);
</script>