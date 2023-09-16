<style>
 .small-padding{margin-bottom:5px;}
 .small-padding div{padding:2px  !important;margin:0px !important;}
  .small-padding div label{white-space:nowrap !important;display:block !important ;}
</style>
<div class="row">
  <div class="col-xs-8 col-sm-8">
  	<h2>[[|title|]]</h2>
  </div>
  <?php if(User::can_add(false,ANY_CATEGORY)){?>
  <div class="col-xs-2 col-sm-2" style="padding-top:23px;">
    <input class="btn btn-default" type="button" value="Thêm" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'add','type'));?>'">
	</div>	     
	<?php }?>    
  <?php if(User::can_delete(false,ANY_CATEGORY)){?>
   <div class="col-xs-2 col-sm-2" style="padding-top:23px;">
  	<input class="btn btn-default" type="button" value="[[.delete.]]" id="delete_button">
   </div>
	<?php }?>
</div>
<hr>
<div class="row">
<form name="ListQlbhStockInvoiceForm" method="post">
    <div class="col-xs-12 col-sm-12 small-padding">
        <div class="col-xs-2 col-sm-2">
          <label for="bill_number">Số Phiếu:</label> 
          <input name="bill_number" type="text" id="bill_number" class="form-control">
        </div>
        <div class="col-xs-2 col-sm-2">
        <label for="note">[[.description.]]:</label> 
        <input name="note" type="text" id="note" class="form-control">
        </div>
        <div class="col-xs-2 col-sm-2">
       <label for="create_date_from">[[.date_from.]]:</label> 
        <input name="create_date_from" type="text" id="create_date_from" class="form-control">
        </div>
         <div class="col-xs-2 col-sm-2">
        <label for="create_date_to">[[.date_to.]]:</label> 
        <input name="create_date_to" type="text" id="create_date_to" class="form-control">
        </div>
        <div class="col-xs-3 col-sm-3">
        <label for="shop_id">Cửa hàng:</label>
        <select name="shop_id" id="shop_id"  class="form-control" style="padding:5px;"></select>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
		<table width="100%" class="table table-striped">
			<thead>
			  <th width="1%"><input type="checkbox" id="all_item_check_box"></th>
			  <th width="1%">[[.order_number.]]</th>
			  <th width="5%" align="left">[[.create_date.]]</th>
			  <th width="10%" align="left">Số phiếu</th>
			  <th width="10%" align="left">Người xuất</th>
			  <th width="10%" align="left">Người nhận</th>
			  <th width="20%" align="left">[[.description.]]</th>
			  <th width="25%" align="left"><!--IF:cond(Url::get('type')=='IMPORT')-->[[.supplier.]]<!--/IF:cond--></th>
			  <th width="1%">&nbsp;</th>
			  <th width="1%">&nbsp;</th>
		      <th width="1%">&nbsp;</th>
		  </tr>
      </thead>
      <tbody>
		  <!--LIST:items-->
			<tr <?php echo ([[=items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
			  <td><input name="item_check_box[]" type="checkbox" class="item-check-box" value="[[|items.id|]]" /></td>
			  <td>[[|items.i|]]</td>
				<td>[[|items.create_date|]]</td>
				<td>[[|items.bill_number|]]</td>
				<td>[[|items.deliver_name|]]</td>
				<td>[[|items.receiver_name|]]</td>
				<td>[[|items.note|]]</td>
				<td><!--IF:cond(Url::get('type')=='IMPORT')-->[[|items.supplier_name|]]<!--/IF:cond--></td>
				<td><a target="_blank" href="<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>" title="[[.view_bill.]]"><img src="skins/default/images/search-icon.png"></a></td>
				<td><a href="<?php echo Url::build_current(array('cmd'=>'edit','type','id'=>[[=items.id=]]));?>"><img src="skins/default/images/buttons/edit.png" /></a></td>
			   <td><a class="delete-one-item" href="<?php echo Url::build_current(array('cmd'=>'delete','type','id'=>[[=items.id=]]));?>"><img src="skins/default/images/buttons/delete.gif"></a></td>
			</tr>
		  <!--/LIST:items-->			
      </tbody>
		</table>
    </div>
  <br />
		<div class="paging">[[|paging|]]</div>
	<input name="cmd" type="hidden" value="">
</form>	
</div>
<script type="text/javascript">
	jQuery("#create_date_from").datepicker();
	jQuery("#create_date_to").datepicker();
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