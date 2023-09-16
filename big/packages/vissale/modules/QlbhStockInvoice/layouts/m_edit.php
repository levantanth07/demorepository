<script type="text/javascript" src="skins/admin/scripts/bootstrapValidator.min.js"></script>
<script type="text/javascript">
	var product_arr = <?php echo MiString::array2js([[=products=]]);?>;
</script>
<span style="display:none">
	<span id="mi_product_sample">
		<div id="input_group_#xxxx#" style="text-align:left;float:left;width:100%;">
			<input  name="mi_product[#xxxx#][id]" type="hidden" id="id_#xxxx#">
			<span class="multi-edit-input"><input  name="mi_product[#xxxx#][product_code]" style="width:60px;" type="text" id="product_code_#xxxx#" onblur="getProductFromCode('#xxxx#',this.value);" class="form-control" AUTOCOMPLETE=OFF></span>
			<span class="multi-edit-input"><input  name="mi_product[#xxxx#][product_name]" style="width:120px;" type="text" readonly class="form-control" id="product_name_#xxxx#" tabindex="-1"></span>
			<span class="multi-edit-input"><input  name="mi_product[#xxxx#][quantity]" style="width:50px;" type="text" id="quantity_#xxxx#" class="form-control" onchange="updatePaymentPrice('#xxxx#');"></span>
      		<span class="multi-edit-input"><input  name="mi_product[#xxxx#][unit]" style="width:60px;" type="text" id="unit_#xxxx#" readonly class="form-control" tabindex="-1"><input  name="mi_product[#xxxx#][unit_id]" type="hidden" id="unit_id_#xxxx#" readonly class="form-control" tabindex="-1"></span>
			<span class="multi-edit-input"><input  name="mi_product[#xxxx#][price]" style="width:85px;text-align:right;" type="text" id="price_#xxxx#" class="form-control" readonly onchange="updatePaymentPrice('#xxxx#');"></span>
            <span class="multi-edit-input" style="width:25px;text-align:center"><input  name="mi_product[#xxxx#][free]" type="checkbox" class="form-control" style="width:20px;" id="free_#xxxx#" tabindex="-1" onClick="updateTotalPayment();"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][discount]" style="width:80px;text-align:right;color:#FF0004;" type="text" id="discount_#xxxx#" class="form-control" onchange="this.value = numberFormat(this.value);updateTotalPayment();"></span>
			<span class="multi-edit-input"><input  name="mi_product[#xxxx#][payment_price]" style="width:100px;text-align:right;" type="text" id="payment_price_#xxxx#" readonly class="form-control"  tabindex="-1"></span>			
			<span class="multi-edit-input" style="border:0px;">
				<span style="width:20px;border:0px;">
				<img src="<?php echo Portal::template('core');?>/images/buttons/delete.gif" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','group_');updateTotalPayment();if(document.all)event.returnValue=false; else return false;" style="cursor:pointer;border:0px;"/></span></span><br>
		</div>
	</span>
</span>
<form  name="EditQlbhStockInvoiceForm" method="post" id="EditQlbhStockInvoiceForm">
<input  name="group_deleted_ids" id="group_deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
<div class="row">
  <div class="col-xs-10 col-sm-10">
  	<h2>Xuất bán hàng	</h2>
  </div>
  <div class="col-xs-2 col-sm-2" style="padding-top:20px;">
    <a href="<?php echo Url::build_current(array('type'));?>"  class="btn btn-default">DS PX</a>
  </div>
</div>
<hr>
<div class="row">
    <?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
    <div class="col-xs-12 col-sm-12">
      <table width="100%" class="table table-bordered">
        <tr>
          <td width="20%"><label for="create_date">[[.date.]] (*):</label></td>
          <td class="form-group"><input name="create_date" type="text" id="create_date" class="form-control" readonly data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF></td>
          <td align="right" width="20%"><label for="bill_number">Số phiếu (*):</label></td>
          <td align="right"><input name="bill_number" type="text" id="bill_number" class="form-control" readonly></td>
        </tr>
        <tr>
          <td><label for="deliver_name">Người xuất:</label></td>
          <td><input name="deliver_name" type="text" id="deliver_name" class="form-control"></td>
          <td align="right"><label for="receiver_name">Người nhận:</label></td>
          <td align="right"><input name="receiver_name" type="text" id="receiver_name" class="form-control" /></td>
        </tr>
        <tr valign="top">
          <td class="control-label"><label for="note">Di&#7877;n gi&#7843;i:</label></td>
          <td colspan="3"><textarea name="note" id="note" class="form-control"></textarea></td>
        </tr>
      </table>
    </div>
    <div class="col-xs-12 col-sm-12">
    	<div class="form-group">
          <h3>Bán cho cửa hàng</h3>
					<select name="shop_id" id="shop_id" class="form-control"></select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12">
      <h3>[[.products.]]</h3>
        <span id="mi_product_all_elems" style="text-align:left;width:100%;float:left;">
          <span style="width:100%;float:left;">
            <span class="multi-edit-input header" style="width:60px;">[[.code.]]</span>
            <span class="multi-edit-input header" style="width:120px;">[[.name.]]</span>
            <span class="multi-edit-input header" style="width:50px;">SL</span>
            <span class="multi-edit-input header" style="width:60px;">[[.unit.]]</span>
            <span class="multi-edit-input header price" style="width:85px;text-align:center">[[.price.]]</span>
            <span class="multi-edit-input header price" style="width:25px;text-align:center">KM</span>            
            <span class="multi-edit-input header price" style="width:80px;text-align:center">Chiết khấu</span>            
            <span class="multi-edit-input header price" style="width:100px;text-align:center">[[.amount.]]</span>
            <span class="multi-edit-input header no-border no-bg" style="width:20px;"></span>
          </span><br clear="all" />
        </span><br clear="all" />
        <div style="padding-top:5px;"><input type="button" value="[[.add_product.]]" onclick="mi_add_new_row('mi_product');my_autocomplete();" class="btn btn-sm btn-info"></div>
   	</div>
    
    <div class="col-xs-12 col-sm-12" style="padding:5px;text-align:right;">
      <label for="total_amount">Tổng đơn hàng: <input name="tong_truoc_chiet_khau" type="text" id="tong_truoc_chiet_khau" readonly style="width:150px;text-align:right;height:25px;" class="form-control"></label>
   	</div>
    <div class="col-xs-12 col-sm-12" style="padding:5px;text-align:right;">
      <label for="total_discount">Tổng chiết khấu: <input name="total_discount" type="text" id="total_discount" readonly style="width:150px;text-align:right;height:25px;" class="form-control"></label>
   	</div>
    <div class="col-xs-12 col-sm-12" style="padding:5px;text-align:right;">
      <label for="total_amount">[[.total_payment.]]:<input name="total_amount" type="text" id="total_amount" readonly style="width:150px;text-align:right;height:25px;" class="form-control"></label>
   	</div>
    <div class="col-xs-12 col-sm-12" style="padding:0px 10px 10px 10px;text-align:right;">
    	<input name="save" type="submit" value="[[.Save.]]" class="btn btn-lg btn-primary" style="width:100px;">
    </div>
</div>
</form>	
<link rel="stylesheet" type="text/css" href="skins/admin/css/jquery.datetimepicker.css"/>
<script src="skins/admin/scripts/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		/*jQuery('#create_date').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});*/
		jQuery('#EditQlbhStockInvoiceForm').bootstrapValidator({
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				create_date: {
					validators: {
						notEmpty: {
							message: 'Bạn phải nhập ngày'
						}
					}
				},
				shop_id: {
					validators: {
						notEmpty: {
							message: 'Bạn chưa chọn cửa hàng'
						}
					}
				}
			}
		}).on('success.form.bv', function (e) {
			checkProduct(e);
		});
	});
	mi_init_rows('mi_product',<?php echo isset($_REQUEST['mi_product'])?MiString::array2js($_REQUEST['mi_product']):'{}';?>);
	//jQuery("#create_date").mask("99/99/9999");
	function updatePaymentPrice(prefix){
		getId('quantity_'+prefix).value = numberFormat(getId('quantity_'+prefix).value);
		getId('price_'+prefix).value = numberFormat(getId('price_'+prefix).value);
		var discount =  0;
		getId('payment_price_'+prefix).value =  to_numeric(getId('price_'+prefix).value)*to_numeric(getId('quantity_'+prefix).value);
		getId('payment_price_'+prefix).value = numberFormat(getId('payment_price_'+prefix).value);
		if(getId('payment_price_'+prefix).value == 'NaN'){
			getId('payment_price_'+prefix).value = 0;
		}
		updateTotalPayment();
	}
	
	function updateTotalPayment(){
		var total_payment = 0;
		var total_payment_no_discount = 0;		
		var total_discount = 0;
		for(var i=101;i<=input_count;i++){
			if(typeof(jQuery("#payment_price_"+i).val())!='undefined'){
				quantity = to_numeric(getId('quantity_'+i).value);
				if(getId('free_'+i) && getId('free_'+i).checked == true){
					price = to_numeric(getId('price_'+i).value);
					payment_price = 0;
					total_payment_no_discount += payment_price;
					discount = 0;
					getId('discount_'+i).value = 0;
				}else{
					price = to_numeric(getId('price_'+i).value);
					payment_price = quantity*price;
					total_payment_no_discount += payment_price;
					if(typeof(jQuery("#discount_"+i).val())!='undefined'){
						discount = to_numeric(getId('discount_'+i).value);
						total_discount += discount;					
					}
					payment_price = payment_price - discount;
					getId('discount_'+i).value = numberFormat(getId('discount_'+i).value);
				}
				jQuery("#payment_price_"+i).val(numberFormat(payment_price));
				total_payment += payment_price;
			}
		}
		jQuery("#tong_truoc_chiet_khau").val((total_payment!='NaN')?numberFormat(total_payment_no_discount):'0');
		jQuery("#total_amount").val((total_payment!='NaN')?numberFormat(total_payment):'0');
		jQuery("#total_discount").val(numberFormat(total_discount));
	}
	updateTotalPayment();
	function getProductFromCode(id,value){
		if(typeof(product_arr[value])=='object'){
			getId('product_name_'+id).value = product_arr[value]['name'];
			getId('unit_'+id).value = product_arr[value]['unit'];
			getId('unit_id_'+id).value = product_arr[value]['unit_id'];
			getId('price_'+id).value = numberFormat(product_arr[value]['price']);
			getId('product_name_'+id).className = 'form-control';
		}else{
			//getId('name_'+id).className = 'notice';
			if(value){
				getId('product_name_'+id).value = 'Mặt hàng không tồn tại';
				getId('product_name_'+id).className = 'form-control not-existed';
			}else{
				getId('product_name_'+id).value = '';
			}
			getId('unit_'+id).value = '';
			getId('price_'+id).value = '';
		}
	}
	function checkProduct(e){
		$return = true;
		if(getId('product_code_'+input_count)){
			if(to_numeric(getId('quantity_'+input_count)) <= 0){
				$return = false;	
			}
		}else{
			$return = false;
		}
		if($return){
			jQuery('input[type="submit"]').val('Đang xử lý...');
			jQuery('input[type="submit"]').attr('disabled',true);
		}else{
			alert('Bạn chưa nhập mặt hàng...!');
			jQuery('input[type="submit"]').attr('disabled',false);
			e.preventDefault();
		}
	}
</script>
<link rel="stylesheet" href="skins/admin/css/autocomplete.css" type="text/css" />
<script type="text/javascript" src="skins/admin/scripts/autocomplete.js"></script>
<script type="text/javascript">
	function my_autocomplete(){
		jQuery("#product_code_"+input_count).autocomplete({
			source:'get_product.php',
			select: function( event, ui ) {getProductFromCode(input_count,ui.item.value);}
		});
	}
</script>
