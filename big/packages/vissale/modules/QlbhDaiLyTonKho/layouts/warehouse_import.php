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
				<td align="right"><strong>[[.supplier.]]: [[|supplier|]]</strong><br />
				  <br>
		</td>
			</tr>
		</table>
	</div>
  <div class="col-xs-12 col-sm-12">
			<div style="text-align:center;">
				<div class="report_title">B&Aacute;O C&Aacute;O NH&#7852;P KHO T&#7914; NH&Agrave; CUNG C&#7844;P </div>
				<br>
				[[.date_from.]] [[|date_from|]] [[.to.]] [[|date_to|]]<br><br />
			</div>
			<div>
				<table width="100%" class="table table-bordered">
					<tr valign="middle">
						<th colspan="5" align="center">[[.product.]]</th>
						<th colspan="3" align="center">[[.detail.]]</th>
					</tr>
					<tr>
					  <th width="20" rowspan="1">[[.no.]]</th>
					  <th width="100" rowspan="1">[[.bill_number.]]</th>
					  <th width="100" rowspan="1">[[.code.]]</th>
					  <th width="200" rowspan="1">[[.name.]]</th>
					  <th width="100" rowspan="1">[[.unit.]]</th>
					  <th width="100" rowspan="1">[[.quantity.]]</th>
					  <th width="150" rowspan="1">[[.price.]]</th>
					  <th width="150" rowspan="1">[[.amount.]]</th>
					</tr>
					<?php $create_date = '';?>
					<!--LIST:products-->
					<?php if($create_date != [[=products.create_date=]]){$create_date = [[=products.create_date=]];?>
					<tr bgcolor="#EFEFEF">
						<td colspan="7" class="category-group">[[|products.create_date|]]</td>
						<td class="category-group" align="right"><strong><?php echo System::display_number([[=arr_by_date=]][[[=products.create_date=]]]);?></strong></td>
					</tr>
					<?php }?>
					<tr bgcolor="white">
					  <td align="left" nowrap="nowrap" class="report_table_column"> [[|products.i|]] </td>
					  <td align="left" nowrap="nowrap" class="report_table_column"> [[|products.bill_number|]] </td>
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
					<!--IF:back_products([[=back_products=]])-->
					<tr>
						<td colspan="8"><strong>H&agrave;ng h&oacute;a xu&#7845;t tr&#7843; l&#7841;i </strong></td>
					</tr>
					<?php $create_date = '';?>
					<!--LIST:back_products-->
					<?php if($create_date != [[=back_products.create_date=]]){$create_date = [[=back_products.create_date=]];?>
					<tr bgcolor="#EFEFEF">
						<td colspan="7" class="category-group">[[|back_products.create_date|]]</td>
						<td class="category-group" align="right"><strong>(<?php echo System::display_number([[=back_arr_by_date=]][[[=back_products.create_date=]]]);?>)</strong></td>
					</tr>
					<?php }?>
					<tr bgcolor="white">
					  <td align="left" nowrap="nowrap" class="report_table_column"> [[|back_products.i|]] </td>
					  <td align="left" nowrap="nowrap" class="report_table_column"> [[|back_products.bill_number|]] </td>
					  <td align="left" nowrap class="report_table_column">
							  [[|back_products.product_code|]]			</td>
						<td align="left" nowrap="nowrap" class="report_table_column"> [[|back_products.name|]] </td>
						<td nowrap align="center" class="report_table_column">
								[[|back_products.unit_name|]]					  </td>
							<td align="right" nowrap class="report_table_column">([[|back_products.quantity|]])</td>
							<td align="right" nowrap="nowrap" class="report_table_column">[[|back_products.price|]]</td>
							<td align="right" nowrap class="report_table_column">
							([[|back_products.amount|]])</td>
					</tr>
					<!--/LIST:back_products-->
					<!--/IF:back_products-->
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng c&#7897;ng </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|total_amount|]]</strong></td>
				  </tr>
				  <!--IF:total_before_tax([[=commission=]] or [[=shipping_fee=]])-->
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng tr&#432;&#7899;c thu&#7871; </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|total_before_tax|]]</strong></td>
				  </tr>
				  <!--/IF:total_before_tax-->
				  <!--IF:commission([[=commission=]])-->
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng tri&#7871;t kh&#7845;u [[|commission|]]% </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|total_commission|]]</strong></td>
				  </tr>
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng sau khi tr&#7915; tri&#7871;t kh&#7845;u </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|total_after_commission|]]</strong></td>
				  </tr>
				  <!--/IF:commission-->
				   <!--IF:shipping_fee([[=shipping_fee=]])-->
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>Ph&iacute; v&#7853;n chuy&#7875;n </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|shipping_fee|]]</strong></td>
				  </tr>
				   <!--/IF:shipping_fee-->
				   <!--IF:grand_total([[=commission=]] or [[=shipping_fee=]])--><tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng sau khi tr&#7915; VAT v&#224; h&#432;&#7903;ng tri&#7871;t kh&#7845;u </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|total_before_tax_commission|]]</strong></td>
				  </tr>
					<tr bgcolor="white">
					  <td colspan="7" align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>T&#7893;ng thanh to&aacute;n </strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|grand_total|]]</strong></td>
				  </tr>
				  <!--/IF:grand_total-->
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