<div class="row">
  <div class="col-md-12">
  	<h3>Phiếu xuất</h3>
    	<div class="row">
            <div class="col-md-12">
            <form name="ListQlbhInvoiceLogForm" method="post">
                <div class="col-xs-2 col-sm-2">
                  <input name="bill_number" type="text" id="bill_number" class="form-control" placeholder="Số Phiếu">
                </div>
                <div class="col-xs-2 col-sm-2">
                <input name="create_date_from" type="text" id="create_date_from" class="form-control date" placeholder="Từ ngày" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF/>
                </div>
                 <div class="col-xs-2 col-sm-2">
                <input name="create_date_to" type="text" id="create_date_to" class="form-control date" placeholder="Đến ngày" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF/>
                </div>
                <div class="col-xs-2 col-sm-2">
                <label for="search">&nbsp;</label>
                <input type="submit" name="search" class="btn btn-sm btn-default" value="OK" />
                </div>
             </form>
            </div>
         </div><br>
        <table class="table">
          <thead>
          <tr>
            <th width="1%">[[.order_number.]]</th>
            <th width="10%" align="left">Tạo lúc</th>
            <th width="10%" align="left">[[.bill_number.]]</th>
            <th width="10%" align="left">[[.deliver.]]</th>
            <th width="10%" align="left">[[.receiver.]]</th>
            <th width="20%" align="left">[[.description.]]</th>
            <th width="10%" align="right">Tổng tiền</th>
            <th width="10%" align="left">Tài khoản</th>
            <th width="1%">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          <?php $total = 0;?>
          <!--LIST:ex_items-->
          <tr <?php echo ([[=ex_items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
            <td>[[|ex_items.i|]]</td>
            <td><?php echo date('H:i\' d/m/Y',[[=ex_items.time=]]);?></td>
            <td>[[|ex_items.bill_number|]]</td>
            <td>[[|ex_items.deliver_name|]]</td>
            <td>[[|ex_items.receiver_name|]]</td>
            <td>[[|ex_items.note|]]</td>
            <td align="right"><?php $total += [[=ex_items.total_amount=]];echo System::display_number([[=ex_items.total_amount=]]);?></td>
            <td>[[|ex_items.user_id|]]</td>
            <td><a target="_blank" href="<?php echo Url::build('qlbh_stock_invoice',array('cmd'=>'view','id'=>[[=ex_items.id=]],'type'=>[[=ex_items.type=]]));?>" title="[[.view_bill.]]"><img src="skins/default/images/search-icon.png"></a></td>
            </tr>
          <!--/LIST:ex_items-->			
          <tr>
            <td colspan="6" align="right">&nbsp;</td>
            <td align="right"><span style="color:#F00;font-weight:bold;"><?php echo System::display_number($total);?></span></td>
            <td>&nbsp;</td>
            </tr>
          </tbody>
        </table>
      <br />
      <div class="paging">[[|ex_paging|]]</div>
  </div>
</div>
<link rel="stylesheet" type="text/css" href="skins/admin/css/jquery.datetimepicker.css"/>
<script src="skins/admin/scripts/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	jQuery('#create_date_from').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});
	jQuery('#create_date_to').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true			
		});	
	jQuery("#delete_button").click(function (){
		ListQlbhStockInvoiceForm.cmd.value = 'delete';
		ListQlbhStockInvoiceForm.submit();
	});
	jQuery(".delete-one-item").click(function (){
		if(!confirm('[[.are_you_sure.]]')){
			return false;
		}
	});
	jQuery("#all_item_check_box").click(function (){
		var check  = this.checked;
		jQuery(".item-check-box").each(function(){
			this.checked = check;
		});
	});
</script>