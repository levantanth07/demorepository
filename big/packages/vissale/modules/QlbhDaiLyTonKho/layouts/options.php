<script type="text/javascript">
	product_arr = <?php echo MiString::array2js([[=product_arr=]]);?>;
</script>
<div class="row">
  <div class="col-xs-12 col-sm-12">
  <h2>[[|title|]]</h2>
  </div>
  <div class="col-xs-12 col-sm-12">
  <form name="WarehouseImportReportOptionsForm" method="post" onsubmit="return checkDate();">
  <?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
    <div class="content">
          <table class="table">
            <tr>
              <td align="left" bgcolor="#EFEFEF"><strong>CH&#7884;N KHO&#7842;NG TH&#7900;I GIAN</strong></td>
            </tr>
            <tr>
              <td align="center"><label for="date_from">Từ ngày: <input name="date_from" type="text" id="date_from" tabindex="1" class="form-control" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF></label></td>
            </tr>
            <tr>
              <td align="center"><label for="date_to">Đến ngày: <input name="date_to" type="text" id="date_to" tabindex="2" class="form-control" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF></label></td>
            </tr>
          </table>	 
      <!--IF:cond(Url::get('page')=='qlbh_import_report')-->
      <table class="table">
         <!--IF:move_cond(Url::get('move_product'))--> 
        <tr>
          <td align="right">Đại lý: </td>
          <td align="left">
          <select name="to_warehouse_id" id="to_warehouse_id" class="form-control"></select></td>
        </tr>
        <tr>
         <!--ELSE-->
         <tr>
           <td width="50%" align="right">Ph&iacute; v&#7853;n truy&#7875;n: </td>
           <td align="left"><input name="shipping_fee" type="text" id="shipping_fee" size="10" maxlength="10" /> VND</td>
          </tr>
         <tr>
           <td align="right">Tri&#7871;t kh&#7845;u: </td>
              <td align="left"><input name="commission" type="text" id="commission" size="4" maxlength="3" value="0"> %</td>
            </tr>
              <tr>
                <td align="right">Nh&agrave; cung c&#7845;p: </td>
                <td align="left">
                <select name="supplier_id" id="supplier_id"></select></td>
            </tr>
        <tr>
        <!--/IF:move_cond--> 
          <td colspan="2" align="center"><input name="import" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
          </tr>
      </table>
      <!--/IF:cond-->
      
      <!--IF:cond(Url::get('page')=='qlbh_dai_ly_ton_kho' and !Url::get('do'))-->
      <table class="table">
            <tr>
              <td bgcolor="#EFEFEF"><strong>B&Aacute;O C&Aacute;O NH&#7852;P XU&#7844;T T&#7890;N </strong></td>
            </tr>
            <tr>
              <td align="center"><input name="store_remain" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
            </tr>
          </table>
      <!--/IF:cond-->
      <!--IF:cond(Url::get('page')=='qlbh_dai_ly_ton_kho' and Url::get('do')=='the_kho')-->
      <table class="table">
            <tr>
              <td bgcolor="#EFEFEF"><strong>TH&#7866; KHO (S&#7892; KHO)</strong></td>
            </tr>
            <tr>
              <td align="center">
                <label for="code">Mã sản phẩm: <input name="code" type="text" id="code" tabindex="3" AUTOCOMPLETE=OFF class="form-control"></label></td>
            </tr>
            <tr>
            	<td align="center"><input name="store_card" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
            </tr>
          </table>
      <!--/IF:cond-->
      <!--IF:cond(Url::get('page')=='qlbh_export_report')-->
      <table class="table">
         <tr>
              <td align="center"><input name="export" type="submit" value="Xem báo cáo" tabindex="-1" class="btn btn-primary"></td>
            </tr>
          </table>
      <!--/IF:cond-->
      <h3>&nbsp;</h3>
      <h3>&nbsp;</h3>
    </div>
  </form>	
  </div>
</div>
<link rel="stylesheet" href="skins/admin/css/autocomplete.css" type="text/css" />
<script type="text/javascript" src="skins/admin/scripts/autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="skins/admin/css/jquery.datetimepicker.css"/>
<script src="skins/admin/scripts/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		my_autocomplete();
		jQuery('#date_from').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});
		jQuery('#date_to').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});	
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
			alert('[[.You_have_to_input_time.]]');
			return false;
		}
		if(!(getId('warehouse_id').value)){
			alert('[[.You_have_to_select_warehouse.]]');
			return false;
		}
	}
</script>