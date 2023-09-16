<style>
@media print {
    .page-break { page-break-inside: avoid;}
}
</style>
<section class="content">
    <h2>IN ĐƠN HÀNG (Vui lòng chọn kích thước in(paper size): Letter)<button class="btn btn-default pull-right" onclick="window.close();"> Đóng lại</button> <button class="btn btn-warning pull-right" onclick="printWebPart('orderList');"> IN </button></h2>
    <div id="orderList">
        <?php $bi=0;?>
        <!--LIST:items-->
        <div style="width:48%;float:left;margin:0px 5px 8px 0px;background: #FFF;overflow: hidden;">
            <table rules="rows" width="100%" border="1" cellpadding="5" cellspacing="2" bordercolor="#000" bgcolor="#FFF" style='border-collage:collage;'>
                <tr>
                    <td width="80%">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <h4 style="margin:0px;height: 18px;overflow: hidden;">[[|print_name|]]</h4>
                            </div>
                        </div>
                    </td>
                    <td width="20%" align="right">
                        No. <?php echo $bi+1;?>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <div style="width:100%;height:145px;overflow:hidden;padding:2px;">
                            <div>Mã đơn hàng: #[[|items.id|]]</div>
                            <div>Sản phẩm:
                                <?php $i=0?>
                                <!--LIST:items.detail_products-->
                                <?php echo $i?', ':''?> [[|items.detail_products.qty|]] [[|items.detail_products.name|]] <!--IF:cond([[=items.detail_products.size=]])-->size [[|items.detail_products.size|]]<!--/IF:cond-->
                                <?php $i++;?>
                                <!--/LIST:items.detail_products-->
                            </div>
                            <div style="width:100%;height:130px;overflow:hidden;">
                                <div>Người nhận: [[|items.customer_name|]]</div>
                                <div>ĐC: [[|items.address|]]/[[|items.city|]]</div>
                                <div>Ghi chú: [[|items.shipping_note|]]</div>
                            </div>    
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <div style="text-align: left;width:33%;float:left;">Tổng: <strong><?php echo System::display_number([[=items.total_price=]]);?></strong></div>
                        <div style="text-align: right;width:63%;float:right;white-space: nowrap;">Tel: [[|items.mobile|]]</div>
                    </td>
                </tr>
            </table><br>
        </div>
        <?php if($bi%8==0 and $bi>0){?>
        <div class="page-break"> </div>
        <?php }?>
        <?php $bi++;?>
        <!--/LIST:items-->
    </div>
</section>