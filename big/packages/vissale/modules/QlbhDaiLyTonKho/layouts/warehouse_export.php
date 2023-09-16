<div class="row">
<div class="col-xs-12 col-sm-12">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left">
					<strong><?php echo Portal::get_setting('company_name');?></strong><br />
					ADD: <?php echo Portal::get_setting('company_address');?><BR>
					Tel: <?php echo Portal::get_setting('company_phone');?> | Fax:<?php echo Portal::get_setting('company_fax');?><br />
					Email: <?php echo Portal::get_setting('email');?> | Website:<?php echo Portal::get_setting('website');?>
				</td>
				<td align="right"><strong>[[.warehouse.]]: [[|warehouse|]]</strong><br><br></td>
			</tr>
		</table>
	</div>
	 <div class="col-xs-12 col-sm-12">
		<div style="text-align:center;">
			<div class="report_title">B&Aacute;O C&Aacute;O XU&#7844;T KHO CHO B&#7896; PH&#7852;N </div>
			<br>
			[[.date_from.]] [[|date_from|]] [[.to.]] [[|date_to|]]<br><br />
		</div>
		<div>
			<table width="100%" class="table table-bordered">
				<tr valign="middle">
					<th colspan="4" class="report_table_header">[[.product.]]</th>
					<th colspan="3" class="report_table_header">[[.detail.]]</th>
				</tr>
				<tr>
				  <th width="20" rowspan="1" class="report_table_header">[[.no.]]</th>
				  <th width="100" rowspan="1" class="report_table_header">[[.code.]]</th>
				  <th width="200" rowspan="1" class="report_table_header">[[.name.]]</th>
				  <th width="100" rowspan="1" class="report_table_header">[[.unit.]]</th>
				  <th width="100" rowspan="1" class="report_table_header">Số lượng</th>
				  <th width="150" rowspan="1" class="report_table_header">[[.price.]]</th>
				  <th width="150" rowspan="1" class="report_table_header">[[.amount.]]</th>
				</tr>
				<!--LIST:products-->
				<tr bgcolor="white">
				  <td align="left" nowrap="nowrap" class="report_table_column"> [[|products.i|]] </td>
				  <td align="left" nowrap class="report_table_column">
						  [[|products.product_code|]]			</td>
					<td align="left" nowrap="nowrap" class="report_table_column"> [[|products.name|]] </td>
					<td nowrap align="center" class="report_table_column">
							[[|products.unit_name|]]					  </td>
						<td align="right" nowrap class="report_table_column">[[|products.quantity|]] </td>
						<td align="right" nowrap="nowrap" class="report_table_column">[[|products.price|]]</td>
						<td align="right" nowrap class="report_table_column">
						[[|products.amount|]]							</td>
				</tr>
				<!--/LIST:products-->
				<tr bgcolor="white">
				  <td colspan="6" align="right" nowrap="nowrap" class="report_table_column"><strong>[[.total.]]</strong></td>
				  <td align="right" nowrap="nowrap" class="report_table_column"><strong>[[|total_amount|]]</strong></td>
			  </tr>
			</table>
		</div>
		<div>
		<table width="100%" border="0" cellspacing="0" cellpadding="5">
		  <tr>
			<td colspan="2" align="left">&nbsp;</td>
		  </tr>
		  <tr>
			<td align="center" width="50%">&nbsp;</td>
			<td align="right"><em>Ng&#224;y&nbsp;[[|day|]]&nbsp;th&#225;ng&nbsp;[[|month|]]&nbsp;n&#259;m&nbsp;[[|year|]]&nbsp;</em></td>
		  </tr>
		  <tr>
			<td align="center"><strong>Ng&#432;&#7901;i l&#7853;p bi&#7875;u </strong></td>
			<td align="center"><strong>Th&#7911; kho </strong> </td>
		  </tr>
		</table>
		</div>
	</div>	
</div>