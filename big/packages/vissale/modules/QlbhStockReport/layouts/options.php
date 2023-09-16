<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<div class="container">
    <br>
    <div class="box box-info box-solid">
        <div class="box-header with-border"><h2 class="box-title">[[|title|]]</h2></div>
        <div class="box-body">
            <div class="col-xs-12">
                <form name="WarehouseImportReportOptionsForm" method="post" onsubmit="return checkDate();">
                    <?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
                    <div class="content text-center">
                        <table class="table">
                            <tr>
                                <td><label for="date_from">Từ ngày: <input name="date_from" type="text" id="date_from" tabindex="1" class="form-control" autocomplete="off"></label></td>
                                <td align="center"><label for="date_to">Đến ngày: <input name="date_to" type="text" id="date_to" tabindex="2" class="form-control" autocomplete="off"></label></td>
                                <!--IF:cond(Url::get('do')!='im_report')-->
                                <td align="center"><label for="">Kho hàng: <select name="warehouse_id" id="warehouse_id" class="form-control"></select></label></td>
                                <!--/IF:cond-->
                            </tr>
                        </table><br />
                        <!--IF:cond(Url::get('do')=='im_report')-->
                        <table class="table">
                            <!--IF:move_cond(Url::get('move_product'))-->
                            <tr>
                                <td colspan="2" class="text-center">Đại lý: <select name="to_warehouse_id" id="to_warehouse_id" class="form-control"></select></td>
                            </tr>
                            <tr>
                                <!--ELSE-->
                            <tr class="hide">
                                <td width="50%" align="right">Phí vận truyển: </td>
                                <td align="left"><input name="shipping_fee" type="text" id="shipping_fee" size="10" maxlength="10" /> VND</td>
                            </tr>
                            <tr class="hide">
                                <td align="right">Chiết khấu: </td>
                                <td align="left"><input name="commission" type="text" id="commission" size="4" maxlength="3" value="0"> %</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    Nhà cung cấp: <select name="supplier_id" id="supplier_id" class="form-control"></select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center"><input name="import" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
                            </tr>
                            <!--/IF:move_cond-->
                        </table>
                        <!--/IF:cond-->
                        <br />
                        <!--IF:cond(!Url::get('do'))-->
                        <table class="table">
                            <tr>
                                <td align="center"><input name="store_remain" type="submit" value="XEM BÁO CÁO NHẬP XUẤT TỒN" tabindex="-1" class="btn btn-primary"></td>
                            </tr>
                        </table>
                        <!--/IF:cond-->
                        <!--IF:cond(Url::get('do')=='store_card')-->
                        <hr>
                        <div class="box box-default box-solid">
                            <div class="box-header">
                                XEM BÁO CÁO THEO SẢN PHẨM (THẺ KHO)
                            </div>
                            <div class="box-body">
                                <select name="product_id" id="product_id" tabindex="3" class="form-control"></select>
                                <br><br>
                                <input name="store_card" type="submit" value="XEM THẺ KHO" tabindex="-1" class="btn btn-primary">
                            </div>
                        </div>
                        <!--/IF:cond-->
                        <!--IF:cond(Url::get('do')=='ex_report')-->
                        <table class="table">
                            <tr>
                                <td align="center"><input name="export" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
                            </tr>
                        </table>
                        <!--/IF:cond-->
                        <br />
                        <h3>&nbsp;</h3>
                        <h3>&nbsp;</h3>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
        $('#product_id').select2();
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
		jQuery('#date_from').datepicker();
		jQuery('#date_to').datepicker();

        my_autocomplete();
	});
	function my_autocomplete()
	{
		jQuery("#code").autocomplete({
			source: 'get_product.php',
			selectFirst:false
		});
	}
	function checkDate(){
		if(!(getId('date_from').value && getId('date_to').value)){
			alert('Bạn phải nhập khoảng ngày để xem báo cáo');
			return false;
		}
		if(!(getId('warehouse_id').value)){
			alert('Bạn vui lòng chọn kho');
			return false;
		}
	}
</script>