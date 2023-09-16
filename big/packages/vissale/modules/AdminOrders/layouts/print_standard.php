<style>
table { page-break-inside:auto } 
tr{ page-break-inside:avoid; page-break-after:auto }
@media all {.page-break{ display: none;} 
@media print {
    .page-break { display: block; page-break-before: always; color:#F00;}
    div{font-size:20px;}
}
</style>
<section class="content">
    <h2>IN ĐƠN HÀNG <button class="btn btn-default pull-right" onclick="window.close();"> Đóng lại</button> <button class="btn btn-warning pull-right" onclick="printWebPart('orderList');"> IN </button></h2>
    <div id="orderList" style='padding: 10px;overflow:hidden;'>
        <?php $bi=0;?>
        <!--LIST:items-->
        <div style="min-height:350px;width:45%;float:left;margin:10px 10px 10px 0px;background: #FFF;overflow: hidden;">
            <table rules="rows" width="100%" border="1" cellpadding="5" cellspacing="2" bordercolor="#000" bgcolor="#FFF" style='border-collage:collage;'>
                <tr>
                    <td colspan="2">
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <h3 style="margin:0px;"><?php echo $bi+1;?>. [[|full_name|]]</h3>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%">
                        <div style="width:100%;height:120px;overflow:hidden;">
                            <div>Mã đơn hàng: #[[|items.id|]]</div>
                            <div>Sản phẩm:
                                <?php $i=0?>
                                <!--LIST:items.detail_products-->
                                <?php echo $i?', ':''?> [[|items.detail_products.qty|]] [[|items.detail_products.name|]] ([[|items.detail_products.code|]]) <!--IF:cond([[=items.detail_products.size=]])-->size [[|items.detail_products.size|]]<!--/IF:cond-->
                                <?php $i++;?>
                                <!--/LIST:items.detail_products-->
                            </div>
                        </div>
                    </td>
                    <td width="50%">
                        <div style="width:100%;height:120px;overflow:hidden;">
                            <div>Người nhận: [[|items.customer_name|]]</div>
                            <div>ĐC: [[|items.address|]]/[[|items.city|]]</div>
                        </div>    
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>Tổng thu: <strong><?php echo System::display_number([[=items.total_price=]]);?></strong></div>
                    </td>
                    <td>
                        <div>Số điện thoại: [[|items.mobile|]]</div>
                    </td>
                </tr>
                <!--IF:cond([[=items.shipping_note=]])-->
                <tr>
                    <td colspan=2>
                        <div>[[|items.shipping_note|]]</div>
                    </td>
                </tr>
                <!--/IF:cond-->
            </table><br>
        </div>
        <?php if($bi%4==0 and $bi>0){?>
        <div class="page-break" style="page-break-before: always;">---</div>
        <?php }?>
        <?php $bi++;?>
        <!--/LIST:items-->
    </div>
</section>