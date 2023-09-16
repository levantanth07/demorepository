<fieldset id="toolbar">
	<div id="toolbar-info">[[.statistic.]]</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td>&nbsp;</td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="25%" valign="top">
		<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
		<tr>
			<td width="60%" style="background-color:#F0F0F0"><b>[[.user_online.]]</b></td>
			<td width="40%"><b>[[|user_online|]]</b></td>
		  </tr>
			  <tr>
			<td width="60%" style="background-color:#F0F0F0"><b>[[.date_page_view.]]</b></td>
			<td width="40%"><b>[[|date_page_view|]]</b></td>
		  </tr>
		<tr>
			<td width="60%" style="background-color:#F0F0F0"><b>[[.month_page_view.]]</b></td>
			<td width="40%"><b>[[|month_page_view|]]</b></td>
		  </tr>
		 <tr>
			<td width="60%" style="background-color:#F0F0F0"><b>[[.year_page_view.]]</b></td>
			<td width="40%"><b>[[|year_page_view|]]</b></td>
		  </tr>
		   <tr>
			<td width="60%" style="background-color:#F0F0F0"><b>[[.total_page_view.]]</b></td>
			<td width="40%"><b>[[|total_page_view|]]</b></td>
		  </tr>
	  </table>
	</td>
	<td valign="top">&nbsp;</td>
    <td width="75%" valign="top">
		<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
		  <tr style="background-color:#F0F0F0">
		  	<th align="center">
        <form name="Statistic" method="post">
        <div class="row">
          <div class="col-lg-12">
            <div class="input-group">
              <select name="month" class="form-control" id="month"></select>
              <span class="input-group-btn"></span>
              <select name="year" class="form-control" id="year"></select>
              <span class="input-group-btn">
                <input name="view" type="submit"  value="Lọc" id="view" class="btn btn-default bt-sm">
              </span>
            </div>
          </div>
        </div>
        <script>
          document.getElementById('month').value = '<?php echo Url::get('month',date('m'));?>';
          document.getElementById('year').value = '<?php echo Url::get('year',date('Y'));?>';
        </script>
        </form>
			</th>
		 </tr>
		 <tr>
		 	<td valign="bottom" align="center">
			 <div id="placeholder" style="width:90%;height:300px;"></div>
				<script src="assets/standard/js/jquery.flot.min.js"></script>
				<script language="javascript" type="text/javascript">
				jQuery(function () {
					var data = [];
						<!--LIST:data-->
					   data.push([<?php echo intval([[=data.id=]]);?>, [[|data.amount|]]]);
						<!--/LIST:data-->
					var plot = jQuery.plot(
						$("#placeholder"),
						   [{ data: data, label: "<?php echo Portal::language('page_view_day_in_month').' '.Url::get('month',date('m')).' '.Portal::language('year').' '.Url::get('year',date('Y'));?>"}],{
							 lines: { show: true },
							 points: { show: true},
							 selection: { mode: "xy" },
							 grid: {
								hoverable: true
								,clickable: true
								,color: '#000000'
								,borderWidth:0.4
							 },
							 yaxis: {tickSize:(([[|max|]]/300)*100),min:0, max: ([[|max|]]+10) },
							 xaxis: {tickSize:1,min:0,max:<?php echo cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y')); ?>}
							}
					);
					function showTooltip(x, y, contents) {
						jQuery('<div id="tooltip">' + contents + '</div>').css( {
							position: 'absolute',
							display: 'none',
							top: y + 5,
							left: x + 5,
							border: '1px solid #fdd',
							padding: '2px',
							'background-color': '#fee',
							opacity: 0.80
						}).appendTo("body").fadeIn(200);
					}
					var previousPoint = null;
					jQuery("#placeholder").bind("plothover", function (event, pos, item) {
						jQuery("#x").text(pos.x);
						jQuery("#y").text(pos.y);
							if (item) {
								if (previousPoint != item.datapoint) {
									previousPoint = item.datapoint;
									jQuery("#tooltip").remove();
									var x = item.datapoint[0],
										y = item.datapoint[1];
									showTooltip(item.pageX, item.pageY,
												item.series.label = "<?php echo Portal::language('page_view');?>" + y +'(<?php echo Portal::language('date');?>:'+x+')');
								}
							}else{
								jQuery("#tooltip").remove();
								previousPoint = null;
							}
					});
				});
				</script>
			</td>
		 </tr>
		</table>
	</td>
  </tr>
</table>
 <table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	<tr bgcolor="#F0F0F0">
		<th align="left"><a>[[.stt.]]</a></th>
		<th align="left" nowrap><a>[[.user_id.]]</a></th>
		<th align="left"><a>[[.Ip.]]</a></th>
		<th align="left"><a>[[.Client.]]</a></th>
		<th align="left"><a>[[.country.]]</a></th>
		<th align="left"><a>[[.time.]]</a></th>
		<th align="left"><a>[[.on_page.]]</a></th>
	</tr>
	<?php $i = 0;?>
	<!--LIST:list-->
	<tr <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>">
		<td><?php echo $i++;?></td>
		<td><strong>[[|list.user_id|]]</strong></td>
		<td>[[|list.ip|]]</td>
		<td><div style="width:500px;overflow:auto">[[|list.client|]]</div></td>
		<td>[[|list.country|]]</td>
		<td><?php echo date('H\h:i\' d/m/Y',[[=list.time=]]);?></td>
		<td>
			<div  style="height:70px;overflow:auto;width:380px;">
			<!--LIST:list.pages-->
			<div>[[|list.pages.page|]]</div>
			<!--/LIST:list.pages-->
			</div>
		</td>
	</tr>
	<!--/LIST:list-->
</table>
<div style="#height:8px"></div>
</fieldset>
