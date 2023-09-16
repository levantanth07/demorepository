<script>
    function check_selected()
    {
        var status = false;
        jQuery('form :checkbox').each(function(e){
            if(this.checked)
            {
                status = true;
            }
        });
        return status;
    }
    function make_cmd(cmd)
    {
        jQuery('#cmd').val(cmd);
        document.CustomerDebitReport.submit();
    }
</script>
<fieldset id="toolbar">
    <div id="toolbar-title">
        Quản lý công nợ
    </div>
    <div id="toolbar-content" align="right" style="margin-right: 11px; margin-top: 10px;">
        <table align="right">
            <tbody>
            <tr>
            </tr>
            </tbody>
        </table>
    </div>
</fieldset>
<br>


<fieldset id="toolbar">
    <div class="container">
        <div class="row">
            <form class="form-inline" style="padding: 15px 0 0 0;" action="/<?php echo Url::build_current(array('cmd'=>'search'));?>">
                <input name="page" type="hidden" value="bao-cao-cong-no" />
                <input name="page_no" type="hidden" />
                <input name="cmd" type="hidden" value="list" />
                <input name="action" type="hidden" value="search" />
                <!--IF:cond(Session::get('account_type')==3 and check_user_privilege('ADMIN_KETOAN'))-->
                <select name="group_id" id="group_id" class="form-control"></select>
                <!--/IF:cond-->
                <div class="form-group col-md-3">
                    <input name="customer_id" type="text" id="customer_id" autocomplete="off" class="form-control" placeholder="Nhập mã KH">
                </div>
                <div class="form-group">
                    <input name="applied_date" type="text" id="applied_date" autocomplete="off" class="form-control" placeholder="Từ ngày">
                </div>
                <div class="form-group">
                    <input name="expired_date" type="text" id="expired_date" autocomplete="off" class="form-control" placeholder="Đến ngày">
                </div>
                <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Tìm khách hàng</button>
                <button type="button" class="btn btn-warning" onclick="ClickHereToPrint('ifrmPrint', 'printBox')"><i class="glyphicon glyphicon-print"></i> IN</button>
            </form>
        </div>
        <iframe src="" id="ifrmPrint" class="hidden"></iframe>
        <div class="row" id="printBox">
            <h2 class="text-center" style="margin-buttom: 10px;">CÔNG NỢ KHÁCH HÀNG</h2>
            <form name="CustomerDebitReport" method="post" action="index062019.php?page=thuchi">
                <div class="list-item" style="padding:15px; ">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
                                <thead>
                                <tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px" class="text-center">
                                    <th width="1%" align="left" nowrap><a>#STT</a></th>
                                    <th width="10%" align="left" nowrap>Khách Hàng</th>
                                    <th width="10%" align="left" nowrap>Số Điện Thoại</th>
                                    <th width="10%" align="center" style="text-align: center;" nowrap>Tổng Tiền</th>
                                    <th width="10%" align="left" nowrap style="text-align: center;">Đã Trả</th>
                                    <th width="10%" align="left" nowrap style="text-align: center;">Còn Lại</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; ?>
                                <!--LIST:orders-->
                                <tr>
                                    <th width="1%" align="left" nowrap><a><?php echo  $i; ?></a></th>
                                    <td align="left" nowrap>
                                        [[|orders.name|]]
                                    </td>
                                    <td align="left" nowrap>
                                        [[|orders.mobile|]]
                                    </td>
                                    <td width="10%" align="right" nowrap class="text-right">
                                        <strong class="text-danger">[[|orders.total_price|]]</strong></td>
                                    <td width="10%" align="left" nowrap>
                                        <div class="amount text-danger" style="margin-bottom: 10px; text-align: right">
                                            <strong>[[|orders.paid_total|]]</strong>
                                        </div>
                                        <table cellspacing="0" class="table small" style="border: 1px solid #f4f4f4; margin-bottom: 0; width: 100%">
                                            <thead>
                                            <tr class="text-center">
                                                <th style="border: 1px solid #f4f4f4; text-align: center">Tiền mặt</th>
                                                <th style="border: 1px solid #f4f4f4; text-align: center"">Chuyển Khoản</th>
                                                <th style="border: 1px solid #f4f4f4; text-align: center"">Thẻ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="text-right text-danger" align="right">
                                                <td style="border: 1px solid #f4f4f4"><strong>[[|orders.cash|]]</strong></td>
                                                <td style="border: 1px solid #f4f4f4"><strong>[[|orders.bank_transfer|]]</strong></td>
                                                <td style="border: 1px solid #f4f4f4"><strong>[[|orders.card|]]</strong></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td width="10%" align="right" nowrap class="amount text-danger">
                                        <strong>[[|orders.total_debt|]]</strong>
                                    </td>
                                </tr>

                                <?php $i++; ?>
                                <!--/LIST:orders-->
                                <tr>
                                    <td colspan="7"></td>
                                </tr>
                                <tr>
                                    <td colspan=""></td>
                                    <td colspan=""><strong>Tổng</strong></td>
                                    <td colspan=""><strong></strong></td>
                                    <td colspan="" align="right">
                                        <strong class="text-danger">[[|all_orders_price|]]</strong>
                                    </td>
                                    <td colspan="" align="right">
                                        <strong class="text-danger">[[|all_orders_paid|]]</strong>
                                    </td>
                                    <td colspan="" align="right">
                                        <strong class="text-danger">[[|all_orders_debt|]]</strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="paging">
                    <div class="row" style="display: flex;justify-content: center;">
                        [[|paging|]]
                    </div>
                </div>
                <input type="hidden" name="cmd" value="" id="cmd">
            </form>
        </div>
        <div style="#height:8px"></div>
    </div>
</fieldset>

<script>
    jQuery(document).ready(function(){
        jQuery('#applied_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        jQuery('#expired_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
    });
</script>