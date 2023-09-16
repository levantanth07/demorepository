<h2>IN ĐƠN HÀNG (Vui lòng chọn kích thước in(paper size): Letter)<button class="btn btn-default pull-right" onclick="window.close();"> Đóng lại</button> <button class="btn btn-warning pull-right" onclick="printWebPart('orderList');"> IN ĐƠN HÀNG</button></h2>
<hr>
<section class="content" style="width: 595px;border:1px solid #000;margin:20px;">
    <div id="orderList" class="printableArea" style='background: #FFF;'>
            <?php $i=0?>
            <!--LIST:items-->
            <!--IF:cond($i > 0 and $i%1==0)-->
            <div style="page-break-after: always;"></div>
            <!--/IF:cond-->
            <div style="padding:5px;margin:0px 5px 20px 0px;background: #FFF;overflow: hidden;border:1px dotted #333;">
                <table width="100%" border="0" cellpadding="5" cellspacing="2"  bgcolor="#FFF" style='border-collage:collage;'>
                    <tr>
                        <td width="110">
                            <!--IF:cond([[=group_image_url=]])-->
                            <img class="graph-img" src="[[|group_image_url|]]" alt=""  style="width:100%">
                            <!--/IF:cond-->
                        </td>
                        <td width="345">
                            [[|note1|]]<!-- Trường hợp nhân viên giao hàng và khách hàng gặp bất cứ vấn đề gì liên quan đến đơn hàng, vui lòng liên hệ trực tiếp tới số Hotline: 0971 500 683 để được hỗ trợ -->
                        </td>
                        <td width="30%">
                            <div>Mã đơn hàng: [[|items.code|]]</div>
                            <div></div>
                            <div style="padding-top:10px;text-align: center;">
                                <img src="assets/lib/php-barcode-master/barcode.php?text=[[|items.bar_code|]]">
                                <br>
                                <center>[[|items.bar_code|]]</center>
                            </div>
                        </td>
                    </tr>
                </table>
                <table width="100%" border="1" cellpadding="10" cellspacing="0" bordercolor="#999" bgcolor="#FFF" style='border-collage:collage !important;'>
                    <tr>
                        <td width="50%">
                            <h3 style="margin:0px 0px 5px 0px;font-weight:bold;font-size:16px;">THÔNG TIN NGƯỜI GỬI</h3>
                            <div><strong>Họ tên:</strong> [[|print_name|]]</div>
                            <div>Điện thoại: [[|print_phone|]]</div>
                            <div>Địa chỉ: [[|print_address|]] </div>
                            <div>Mã BĐ: [[|items.postal_code|]]</div>
                        </td>
                        <td width="50%">
                            <h3 style="margin:0px 0px 5px 0px;font-weight:bold;font-size:16px;">THÔNG TIN CƯỚC PHÍ</h3>
                            <div>Thu COD: <span style="font-size:20px;font-weight:bold"><?php echo System::display_number([[=items.total_price=]]);?> VNĐ</></div>
                            <div>Ghi chú: <em>[[|items.shipping_note|]]</em></div>
                        </td>
                    </tr>
                    <tr style="vertical-align: top;">
                        <td width="50%">
                            <h3 style="margin:0px 0px 5px 0px;font-weight:bold;font-size:16px;">THÔNG TIN NGƯỜI NHẬN</h3>
                            <div><strong>Họ tên:</strong> [[|items.customer_name|]]</div>
                            <div>Điện thoại: [[|items.mobile|]]</div>
                            <div>Địa chỉ: [[|items.address|]] [[|items.city|]]</div>
                        </td>
                        <td width="50%">
                            <h3 style="margin:0px 0px 5px 0px;font-weight:bold;font-size:16px;">THÔNG TIN HÀNG HOÁ</h3>
                            <div>
                                <table width="100%" border="0">
                                    <tr>
                                        <th width="90%" style="border-bottom:1px dashed #000;">Sản phẩm</th>
                                        <th width="10%" style="border-bottom:1px dashed #000;">SL</th>
                                    </tr>
                                    <!--LIST:items.detail_products-->
                                    <tr>
                                        <td>[[|items.detail_products.code|]]_[[|items.detail_products.name|]]<!--IF:cond([[=items.detail_products.size=]])-->_[[|items.detail_products.size|]]<!--/IF:cond--></td>
                                        <td>[[|items.detail_products.qty|]]</td>
                                    </tr>
                                    <!--/LIST:items.detail_products-->
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                <?php $i++?>
            </div>
            <!--/LIST:items-->
    </div>
</section>

<script type="text/javascript" src="assets/standard/js/jquery.PrintArea.js"></script>

<script></script>
<script>
    $(document).ready(function(){
        $("#printButton").click(function(){
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = { mode : mode, popClose : close};
            $("div.printableArea").printArea( options );
        });
    });
</script>