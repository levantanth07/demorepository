<div class="row">
<div class="col-xs-12 col-sm-12">
<table width="100%" class="table">
	<tr>
		<td align="left">
		  <button type="button" class="btn btn-default" onclick="window.location='index062019.php?page=qlbh_dai_ly_ton_kho&do=the_kho'">Tùy chọn xem báo cáo</button>
		</td>
		<td align="right"><strong>[[.Warehouse.]]: [[|warehouse|]]</strong><br /></td>
	</tr>
</table>
</div>
	<div class="col-xs-12 col-sm-12">
			<div style="text-align:center;">
				<h1 class="report_title">Thẻ kho</h1>
				<p>Từ ngày [[|date_from|]] đến [[|date_to|]]</p>
                <table border="0" cellspacing="0" cellpadding="5">
                  <tr>
                    <td align="left">[[.warehouse.]]: </td>
                    <td align="left">[[|warehouse|]]</td>
                  </tr>
                  <tr>
                    <td align="left">Mã hàng: </td>
                    <td align="left">[[|code|]]</td>
                  </tr>
                  <tr>
                    <td align="left">Mặt hàng: </td>
                    <td align="left">[[|name|]]</td>
                  </tr>
                </table><br />
            </div>
			<div>
				<table width="100%" class="table table-bordered">
					<tr valign="middle" bgcolor="#EFEFEF">
						<th colspan="3">[[.invoice.]]</th>
						<th rowspan="2">[[.note.]]</th>
						<th colspan="3">[[.number.]]</th>
					</tr>
					<tr><th rowspan="1">[[.invoice_date.]]</th>
					<th rowspan="1">[[.import_code.]]</th>
					<th rowspan="1">[[.export_code.]]</th>
					<th rowspan="1">[[.import.]]</th>
					<th rowspan="1">[[.export.]]</th>
					<th rowspan="1">[[.store_remain.]]</th>
					</tr>
					<tr><td align="center">1</td><td align="center">2</td><td align="center">3</td><td align="center">4</td><td align="center">5</td><td align="center">6</td><td align="center">7</td></tr>
					<tr bgcolor="white">
						<td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td><td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td><td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
						<td width="200" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">Tồn đầu kỳ</td>
						<td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
						<td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
						<td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column"><strong>[[|start_remain|]]</strong></td>
					</tr>
					<tr>
						<td colspan="7" class="report_sub_title" align="right"><b>&nbsp;</b></td>
					</tr>
					<!--LIST:products-->
					<tr bgcolor="white">
						<td nowrap align="left" class="report_table_column" width="70">
								[[|products.create_date|]]			</td>
						<td nowrap align="left" class="report_table_column" width="70">
								[[|products.import_invoice_code|]]				  </td><td nowrap align="left" class="report_table_column" width="70">
								[[|products.export_invoice_code|]]
					  </td>
							<td nowrap align="left" class="report_table_column" width="200">
								[[|products.note|]]			</td>
							<td nowrap align="right" class="report_table_column" width="100">
								[[|products.import_number|]]			</td><td nowrap align="right" class="report_table_column" width="100">
								[[|products.export_number|]]
							</td>
							<td nowrap align="right" class="report_table_column" width="100">[[|products.remain|]]						</td>
					</tr>
					<!--/LIST:products-->
					<tr>
						<td colspan="7" class="report_sub_title" align="right"><b>&nbsp;</b></td>
					</tr>
					<tr bgcolor="white">
					  <td align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
					  <td align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
					  <td align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
					  <td align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">T&#7893;ng</td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|import_total|]]</strong></td>
					  <td align="right" nowrap="nowrap" bgcolor="#F1F1F1" class="report_table_column"><strong>[[|export_total|]]</strong></td>
					  <td align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
				  </tr>
					<tr bgcolor="white">
						<td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td><td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td><td width="70" align="left" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
						<td width="200" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">Tồn cuối kỳ</td>
						<td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td>
						<td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column">&nbsp;</td><td width="100" align="right" nowrap bgcolor="#F1F1F1" class="report_table_column"><strong>[[|end_remain|]]</strong></td>
					</tr>
				</table>
			</div>
			<div></div>
		</div>	
</div>