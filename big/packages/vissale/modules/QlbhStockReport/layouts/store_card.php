<div class="container">
    <br>
    <div class="panel">
        <div class="panel-header text-right">
            <input type="button" value="Xem theo tuỳ chọn khác" class="btn btn-default btn-lg" onclick="window.location='<?php echo Url::build_current(array('type','do'))?>'">
            <button type="button" class="btn btn-default btn-lg" onclick="printWebPart('invoiceWrapper');"><i class="fa fa-print"></i> IN </button></div>
        <div class="panel-body" id="invoiceWrapper">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left">
                                <div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <div style="text-align:center;">
                        <h1 class="report_title">Thẻ kho</h1>
                        <p>Từ ngày [[|date_from|]] đến [[|date_to|]]</p>
                        <table border="0" cellspacing="0" cellpadding="5">
                            <tr>
                                <td align="left">Kho: </td>
                                <td align="left">[[|warehouse|]]</td>
                            </tr>
                            <tr>
                                <td align="left">Mã hàng: </td>
                                <td align="left">[[|code|]]</td>
                            </tr>
                            <tr>
                                <td align="left">Mặt hàng: </td>
                                <td align="left"> [[|name|]]</td>
                            </tr>
                        </table><br />
                    </div>
                    <div>
                        <table width="100%" class="table table-bordered" border="1" bordercolor="#999" cellspacing="0" cellpadding="0">
                            <tr valign="middle">
                                <th colspan="3">Phiếu</th>
                                <th rowspan="2" class="text-center">Ghi chú</th>
                                <th colspan="3" class="text-center">Số lượng</th>
                            </tr>
                            <tr><th rowspan="1">Ngày tạo</th>
                                <th rowspan="1">Số phiếu nhập</th>
                                <th rowspan="1">Số phiếu xuất</th>
                                <th rowspan="1" class="text-center">Nhập</th>
                                <th rowspan="1" class="text-center">Xuất</th>
                                <th rowspan="1" class="text-center">Tồn</th>
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
                                <td nowrap align="left" class="report_table_column" width="70">[[|products.create_date|]]</td>
                                <td nowrap align="left" class="report_table_column" width="70">[[|products.import_invoice_code|]]</td>
                                <td nowrap align="left" class="report_table_column" width="70">[[|products.export_invoice_code|]]</td>
                                <td nowrap align="left" class="report_table_column" width="200">[[|products.note|]]</td>
                                <td nowrap align="right" class="report_table_column" width="100">[[|products.import_number|]]</td>
                                <td nowrap align="right" class="report_table_column" width="100">[[|products.export_number|]]</td>
                                <td nowrap align="right" class="report_table_column" width="100">[[|products.remain|]]</td>
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
                                <td align="center"><strong>Ng&#432;&#7901;i l&#7853;p bi&#7875;u </strong><p>&nbsp;</p><p>&nbsp;</p></td>
                                <td align="center"><strong>Phụ trách kế toán</strong><p>&nbsp;</p><p>&nbsp;</p></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>