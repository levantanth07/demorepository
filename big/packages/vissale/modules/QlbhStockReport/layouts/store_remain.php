<style>
    .tableFixHead tr th { 
        position: sticky; top: 0; z-index: 1; 
    }
    table  { 
        border-collapse: collapse; width: 100%; 
    }
    .tableFixHead tr th { 
        background:#DDD; 
    }
    .th-fixed {
        background: rgb(221, 221, 221);
        position: sticky;
        left: -11px;
        top: auto;
        white-space: normal;
        min-width: 150px;
    }
</style>


<div class="container">
    <br>
    <div class="panel">
        <div class="panel-header text-right">
            <input type="button" value="Xem theo tuỳ chọn khác" class="btn btn-default btn-lg" onclick="window.location='<?php echo Url::build_current(array('type','do'))?>'">
            <button type="button" class="btn btn-default btn-lg" onclick="printWebPart('invoiceWrapper');"><i class="fa fa-print"></i> IN </button>
        </div>
        <div class="panel-body" id="invoiceWrapper">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <table width="100%">
                        <tr>
                            <td align="left">
                                <div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div>
                            </td>
                            <td align="right"><strong>Kho: [[|warehouse|]]</strong><br />
                            </td>
                        </tr>
                    </table>
                    <div style="width:100%;" >
                        <div style="padding:2px;">
                            <div class="report_title" align="center"><h2>[[|title|]]</h2></div>
                            <div>
                                <table width="100%">
                                    <tr valign="top">
                                        <td style="font-size:12px;text-align:center;"><br />
                                            T&#7915; ng&agrave;y [[|date_from|]] &#273;&#7871;n ng&agrave;y [[|date_to|]]
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div style="padding:2px 2px 2px 2px;text-align:left;">
                                &nbsp;
                            </div>
                            <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                                <table width="100%" class="table table-bordered tableFixHead" border="1" bordercolor="#333" cellspacing="0" cellpadding="0">
                                    <!-- <thead style="position: sticky; top: 0; z-index: 1"> -->
                                        <tr style="position: sticky; top: 0; z-index: 1">
                                            <th width="5%" align="left" scope="col">STT</th>
                                            <th width="20%" align="left" scope="col">M&atilde; h&agrave;ng <br /></th>
                                            <th width="20%" align="left" scope="col">T&ecirc;n v&#7853;t t&#432; h&agrave;ng h&oacute;a </th>
                                            <th width="10%" align="center" scope="col">&#272;VT</th>
                                            <th align="center" scope="col">T&#7891;n &#273;&#7847;u k&#7923; </th>
                                            <th align="center" scope="col">Nh&#7853;p trong k&#7923; </th>
                                            <th align="center" scope="col">Xu&#7845;t trong k&#7923; </th>
                                            <th scope="col" align="center">T&#7891;n cu&#7889;i k&#7923; </th>
                                        </tr>
                                    <!-- </thead> -->
                                    <?php 
                                        $category = '';
                                        $start_term_quantity_total = 0;
                                        $import_number_total = 0;
                                        $export_number_total = 0;
                                        $remain_number_total = 0;
                                    ?>
                                    <!--LIST:products-->
                                    <?php if($category != [[=products.category_id=]] ){$category=[[=products.category_id=]];?>
                                        <!-- <tr>
                                            <td colspan="10" class="category-group">[[|products.category_id|]]</td>
                                        </tr> -->
                                    <?php }?>
                                    <?php
                                          $start_term_quantity_total += [[=products.start_term_quantity=]];
                                          $import_number_total += [[=products.import_number=]];
                                          $export_number_total += [[=products.export_number=]];
                                          $remain_number_total += [[=products.remain_number=]];
                                    ?>
                                    <tbody>
                                        <tr>
                                            <td align="left">[[|products.stt|]]</td>
                                            <td align="left">[[|products.product_code|]]</td>
                                            <td align="left">[[|products.name|]]</td>
                                            <td align="center">[[|products.unit|]]</td>
                                            <td align="right"><?php echo System::display_number(round([[=products.start_term_quantity=]],2));?></td>
                                            <td align="right"><?php echo System::display_number(round([[=products.import_number=]],2));?></td>
                                            <td align="right"><?php echo System::display_number(round([[=products.export_number=]],2));?></td>
                                            <td align="right"><?php echo System::display_number(round([[=products.remain_number=]],2));?></td>
                                        </tr>
                                    </tbody>
                                    <!--/LIST:products-->
                                    <!-- <tfoot> -->
                                        <tr style="position: sticky; left: 0; background: #DDD;  bottom: 0;" >
                                            <td colspan="4" align="center"><strong>Tổng</strong></td>
                                            <td align="right"><strong><?php echo System::display_number(round($start_term_quantity_total,2));?></strong></td>
                                            <td align="right"><strong><?php echo System::display_number(round($import_number_total,2));?></strong></td>
                                            <td align="right"><strong><?php echo System::display_number(round($export_number_total,2));?></strong></td>
                                            <td align="right"><strong><?php echo System::display_number(round($remain_number_total,2));?></strong></td>
                                        </tr>
                                    <!-- </tfoot> -->
                                </table>
                            </div>
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
</div>