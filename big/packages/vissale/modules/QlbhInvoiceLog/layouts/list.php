<div class="row">
  <div class="col-md-6">
  	<h3>Phiếu nhập</h3>
    <div class="scrollable">
    <table class="table">
      <thead>
      <tr>
        <th width="1%">[[.order_number.]]</th>
        <th width="5%" align="left">Tạo lúc</th>
        <th width="10%" align="left">[[.bill_number.]]</th>
        <th width="10%" align="left">[[.deliver.]]</th>
        <th width="10%" align="left">[[.receiver.]]</th>
        <th width="20%" align="left">[[.description.]]</th>
        <th width="25%" align="left">Tài khoản</th>
        <th width="1%">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      <!--LIST:items-->
      <tr <?php echo ([[=items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
        <td >[[|items.i|]]</td>
        <td ><?php echo date('H:i\' d/m/Y',[[=items.time=]]);?></td>
        <td >[[|items.bill_number|]]</td>
        <td >[[|items.deliver_name|]]</td>
        <td >[[|items.receiver_name|]]</td>
        <td >[[|items.note|]]</td>
        <td >[[|items.user_id|]]</td>
        <td><a target="_blank" href="<?php echo Url::build('qlbh_stock_invoice',array('cmd'=>'view','id'=>[[=items.id=]],'type'=>[[=items.type=]]));?>" title="[[.view_bill.]]"><img src="skins/default/images/search-icon.png"></a></td>
        </tr>
      <!--/LIST:items-->			
      </tbody>
    </table>
    </div>
    <br />
    <div class="paging">[[|paging|]]</div>
  </div>
  <div class="col-md-6">
  	<h3>Phiếu xuất</h3>
        <table class="table">
          <thead>
          <tr>
            <th width="1%">[[.order_number.]]</th>
            <th width="5%" align="left">Tạo lúc</th>
            <th width="10%" align="left">[[.bill_number.]]</th>
            <th width="10%" align="left">[[.deliver.]]</th>
            <th width="10%" align="left">[[.receiver.]]</th>
            <th width="20%" align="left">[[.description.]]</th>
            <th width="25%" align="left">Tài khoản</th>
            <th width="1%">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          <!--LIST:ex_items-->
          <tr <?php echo ([[=ex_items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
            <td>[[|ex_items.i|]]</td>
            <td><?php echo date('H:i\' d/m/Y',[[=ex_items.time=]]);?></td>
            <td>[[|ex_items.bill_number|]]</td>
            <td>[[|ex_items.deliver_name|]]</td>
            <td>[[|ex_items.receiver_name|]]</td>
            <td>[[|ex_items.note|]]</td>
            <td>[[|ex_items.user_id|]]</td>
            <td><a target="_blank" href="<?php echo Url::build('qlbh_stock_invoice',array('cmd'=>'view','id'=>[[=ex_items.id=]],'type'=>[[=ex_items.type=]]));?>" title="[[.view_bill.]]"><img src="skins/default/images/search-icon.png"></a></td>
            </tr>
          <!--/LIST:ex_items-->			
          </tbody>
        </table>
      <br />
      <div class="paging">[[|ex_paging|]]</div>
  </div>
</div>