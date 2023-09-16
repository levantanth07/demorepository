<style>
 .small-padding{margin-bottom:5px;}
 .small-padding div{padding:2px  !important;margin:0px !important;}
 .small-padding div label{white-space:nowrap !important;display:block !important ;}
 .small-padding .form-control.date{}
</style>
<div class="row">
  <div class="col-xs-9 col-sm-9">
  	<h2>[[|title|]]</h2>
  </div>
  <div class="col-xs-3 col-sm-3" style="padding-top:20px;">
  <?php if(User::can_add(false,ANY_CATEGORY)){?>
    <input class="btn btn-default" type="button" value="Thêm" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'add','type'));?>'">     
	<?php }?>    
  <?php if(User::can_delete(false,ANY_CATEGORY)){?>
  	<input class="btn btn-default" type="button" value="[[.delete.]]" id="delete_button">
	<?php }?>
  </div>
</div>
<hr>
<div class="row">
<form name="ListQlbhInvoiceLogForm" method="post">
    <div class="col-xs-12 col-sm-12 small-padding">
        <div class="col-xs-2 col-sm-2">
          <label for="bill_number">Số Phiếu:</label> 
          <input name="bill_number" type="text" id="bill_number" class="form-control">
        </div>
        <!--<div class="col-xs-2 col-sm-2">
        <label for="note">[[.description.]]:</label> 
        <input name="note" type="text" id="note" class="form-control">
        </div> -->
        <div class="col-xs-2 col-sm-2">
       <label for="create_date_from">[[.date_from.]]:</label> 
        <input name="create_date_from" type="text" id="create_date_from" class="form-control date" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF/>
        </div>
         <div class="col-xs-2 col-sm-2">
        <label for="create_date_to">[[.date_to.]]:</label> 
        <input name="create_date_to" type="text" id="create_date_to" class="form-control date" data-date-format="DD/MM/YYYY" AUTOCOMPLETE=OFF/>
        </div>
        <div class="col-xs-3 col-sm-2">
        <label for="shop_id">Cửa hàng:</label>
        <select name="shop_id" id="shop_id"  class="form-control" style="padding:5px;"></select>
        </div>
        <div class="col-xs-2 col-sm-2">
        <label for="search">&nbsp;</label>
        <input type="submit" name="search" class="btn btn-sm btn-default" value="OK" />
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
		<table width="100%" class="table table-striped">
			<thead>
			  
			  <th width="1%">[[.order_number.]]</th>
			  <th width="1%" align="left">[[.create_date.]]</th>
			  <th width="10%" align="left">[[.bill_number.]]</th>
			  <th width="10%" align="left">[[.deliver.]]</th>
			  <th width="10%" align="left">[[.receiver.]]</th>
			  <th width="20%" align="center">Tổng tiền</th>
			  <th width="25%" align="left">Cửa hàng</th>
			  <th width="3%">&nbsp;</th>
		  </tr>
      <tbody>
      	<?php $total = 0;?>
        <!--LIST:items-->
        <tr <?php echo ([[=items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
          <td>[[|items.i|]]</td>
          <td>[[|items.create_date|]]</td>
          <td>[[|items.bill_number|]]</td>
          <td>[[|items.deliver_name|]]</td>
          <td>[[|items.receiver_name|]]</td>
          <td align="right"><?php $total += [[=items.total_amount=]];echo System::display_number([[=items.total_amount=]]);?></td>
          <td>[[|items.shop_name|]]</td>
          <td nowrap="nowrap"><a href="<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>" class="btn btn-sm btn-info" title="[[.view_bill.]]">Xem</a>
          <a href="<?php echo Url::build_current(array('cmd'=>'edit','type','id'=>[[=items.id=]]));?>" class="btn btn-sm btn-warning">Sửa</a>
          <a href="<?php echo Url::build_current(array('cmd'=>'delete','type','id'=>[[=items.id=]]));?>" class="btn btn-sm btn-danger">Xóa</a></td>
          </tr>
        <!--/LIST:items-->			
        <tr>
          <td>&nbsp;</td>
          <td colspan="4" align="right" style="color:#F00;font-weight:bold;">Tổng tiền</td>
          <td align="right" style="color:#F00;font-weight:bold;"><?php echo System::display_number($total);?></td>
          <td>&nbsp;</td>
          <td nowrap="nowrap">&nbsp;</td>
        </tr>
      </tbody>
		</table>
    </div>
  <br />
		<div class="paging">[[|paging|]]</div>
	<input name="cmd" type="hidden" value="">
</form>	
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#create_date_from').datetimepicker({pickTime: false});
		jQuery('#create_date_to').datetimepicker({pickTime: false});
	});
	jQuery("#delete_button").click(function (){
		ListQlbhInvoiceLogForm.cmd.value = 'delete';
		ListQlbhInvoiceLogForm.submit();
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