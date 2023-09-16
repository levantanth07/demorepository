<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<div class="container">
    <br>
    <div class="panel">
        <div class="panel-body" id="invoiceWrapper">
            <div style="width:100%;padding:10px 0;text-align:center;font-size:14px;float:left;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="left">
                            <div>Đơn vị: [[|group_name|]]</div>
                            <div>Điện thoại: [[|phone|]]</div>
                            <div>Địa chỉ: [[|address|]]</div>
                        </td>
                        <td align="right">
                            Số:     <br />
                            Ngày: [[|day|]]/[[|month|]]/[[|year|]]
                        </td>
                    </tr>
                </table>
            </div><br clear="all">
            <div style="text-align:left;">
                <div style="text-align:center;font-size:14px;">
                    <div style="">
                        <div style="text-indent:0px;vertical-align:top;font-size:16px;text-transform:uppercase;font-weight:bold;">[[|title|]]</div>
                        <div>
                            <table width="100%">
                                <tr valign="top">
                                    <td width="70%" style="font-size:12px;text-align:left">
                                        <!--IF:cond(Url::get('type')=='IMPORT' or [[=supplier_id=]])-->
                                        [[|supplier_name|]]<br>
                                        <!--ELSE-->
                                        [[|warehouse_name|]] <br>
                                        <!--/IF:cond-->
                                        Người giao: [[|deliver_name|]]<br />
                                        Địa chỉ (Bộ phận): [[|deliver_address|]]<br />
                                        Người nhận: [[|receiver_name|]]<br />
                                        Địa chỉ: [[|receiver_address|]]<br />
                                        </td>
                                    <td width="30%" align="right" nowrap="nowrap"  style="font-size:12px;">Nh&acirc;n vi&ecirc;n: [[|staff_name|]]<br /></td>
                                </tr>
                                <tr valign="top">
                                    <td style="font-size:12px;text-align:left">Di&#7877;n gi&#7843;i:
                                        <!--IF:cond([[=note=]])--><em><?php echo str_replace(',',', ',[[=note=]])?></em><!--ELSE-->...<!--/IF:cond--></td>
                                    <td align="right" nowrap="nowrap"  style="font-size:12px;">&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                        <div style="padding:2px 2px 2px 2px;text-align:left;">
                            &nbsp;
                        </div>
                        <div style="text-align:left;">
                            <table width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#CCC">
                                <tr>
                                    <th width="5%" scope="col" class="text-center">STT</th>
                                    <th width="15%" class="text-center" scope="col">T&ecirc;n SP, HH <br /></th>
                                    <th width="10%" class="text-center" scope="col">M&atilde; </th>
                                    <th width="7%" class="text-center" scope="col">Đơn vị</th>
                                    <!--IF:import_cond(Url::get('type')=='IMPORT')-->
                                    <th width="7%" class="text-center" scope="col">HSD</th>
                                    <!--/IF:import_cond-->
                                    <th width="10%" class="text-center" scope="col">Kho</th>
                                    <th width="5%"  class="text-center" scope="col">Số lượng</th>
                                    <!--IF:cond([[=has_price=]] and Url::get('type')=='EXPORT')-->
                                    <th width="5%" scope="col" class="text-center">Đơn giá</th>
                                    <th width="10%" scope="col" class="text-center">Thành tiền</th>
                                    <!--/IF:cond-->
                                </tr>
                                <tr>
                                    <td class="text-center">A</td>
                                    <td class="text-center">B</td>
                                    <td class="text-center">C</td>
                                    <td class="text-center">D</td>
                                    <!--IF:import_cond(Url::get('type')=='IMPORT')-->
                                    <td class="text-center">E</td>
                                    <td class="text-center">F</td>
                                    <!--ELSE-->
                                    <td class="text-center">E</td>
                                    <!--/IF:import_cond-->
                                    <td class="text-center">F</td>
                                    <!--IF:cond([[=has_price=]] and Url::get('type')=='EXPORT')-->
                                    <td class="text-center">G</td>
                                    <td class="text-center">H</td>
                                    <!--/IF:cond-->
                                </tr>
                                <!--LIST:products-->
                                <tr>
                                    <td class="text-center"><?=++$i?></td>
                                    <td align="left" style="padding:0 0 0 10px;">[[|products.product_name|]]</td>
                                    <td align="center" nowrap="nowrap">[[|products.product_code|]]</td>
                                    <td class="text-center">[[|products.unit_name|]]</td>
                                    <!--IF:import_cond(Url::get('type')=='IMPORT')-->
                                    <td class="text-center"><?php echo Date_Time::to_common_date([[=products.expired_date=]])?></td>
                                    <!--/IF:import_cond-->
                                    <td align="left">[[|products.warehouse_name|]]</td>
                                    <td class="text-center">[[|products.quantity|]]</td>
                                    <!--IF:cond([[=has_price=]] and Url::get('type')=='EXPORT')-->
                                    <td class="text-right">[[|products.price_fmt|]]</td>
                                    <td class="text-right">[[|products.payment_amount_fmt|]]</td>
                                    <!--/IF:cond-->
                                </tr>
                                <!--/LIST:products-->
                                <?php for($i=0;$i<=10;$i++){?><tr>
                                <td>&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <!--IF:import_cond(Url::get('type')=='IMPORT')-->
                                <td class="text-center">&nbsp;</td>
                                <!--/IF:import_cond-->
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <!--IF:cond([[=has_price=]] and Url::get('type')=='EXPORT')-->
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <!--/IF:cond-->
                            </tr>
                                <?php
                                if($i==1)
                                {
                                echo '<div style="display:none;page-break-after:always;">';
                                }
                                }?><tr>
                                    <td>&nbsp;</td>
                                    <td class="text-center text-bold">Tổng cộng </td>
                                    <td class="text-center">x</td>
                                    <td class="text-center">x</td>
                                    <!--IF:import_cond(Url::get('type')=='IMPORT')-->
                                    <td class="text-center">x</td>
                                    <!--/IF:import_cond-->
                                    <td class="text-center">x</td>
                                    <td class="text-center">x</td>
                                    <!--IF:cond([[=has_price=]] and Url::get('type')=='EXPORT')-->
                                    <td class="text-center">x</td>
                                    <td class="text-right text-bold">[[|total_amount_fmt|]]</td>
                                    <!--/IF:cond-->
                                </tr>
                            </table>
                        </div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="5">
                            <tr>
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                <td colspan="2" align="right"><em>Ng&#224;y&nbsp;[[|day|]]&nbsp;th&#225;ng&nbsp;[[|month|]]&nbsp;n&#259;m&nbsp;[[|year|]]&nbsp;</em></td>
                            </tr>

                            <!-- Tong tien -->
                            <tr>
                                <td colspan="4" class="text-left">
                                <!--IF:cond(([[=has_price=]] and Url::get('type')=='EXPORT') || Url::get('type')=='IMPORT')-->
                                Tổng số tiền (Viết bằng chữ): 
                                <i><?=ucfirst(NumberToText::parse([[=total_amount=]]))?> đồng</i>
                                <!--/IF:cond-->
                                </td>
                            </tr>
                            <!-- So chung tu -->
                            <tr><td colspan="4" class="text-left">Số chứng từ: [[|original_documents_number|]]<br></td></tr>

                            <tr>
                                <td align="center" width="25%">Th&#7911; tr&#432;&#7903;ng &#273;&#417;n v&#7883;<br />
                                    <em>(K&#253;, h&#7885; t&#234;n)</em></td>
                                <td width="25%" align="center">Ng&#432;&#7901;i giao h&agrave;ng<br />
                                    <em>(K&#253;, h&#7885; t&#234;n)</em></td>
                                <td width="25%" align="center"><span style="width:25%;text-align:center;">Th&#7911; kho<br />
							<em>(K&#253;, h&#7885; t&#234;n)</em></span></td>
                                <td width="25%" align="center"><span style="width:25%;text-align:center;">Ng&#432;&#7901;i nh&#7853;n h&agrave;ng<br />
							<em>(K&#253;, h&#7885; t&#234;n)</em></span></td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
</div>
</div>
</html>
<?=die();?>