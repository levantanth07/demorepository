<style>
	th,td{padding:5px !important;}
	.print-title{display:none;}
</style>
<style media="print">
	.print-title{display:block;}
</style>

<fieldset id="toolbar">
<form  name="ManageOrderForm" id="ManageOrderForm" method="post">
<div class="list">
<div class="title">
    <h4>Danh sách đặt hàng</h4>
</div><!--End .title-->
<div class="m-list">
<table border="1" cellspacing="0" cellpadding="5" bordercolor="#CCC">
  <tr bgcolor="#EFEFEF">
    <td>&nbsp;</td>
    <td>ID</td>
    <td>Chi tiết</td>
    <td>Họ và tên</td>
    <td>Email</td>
    <td>Điện thoại</td>
    <td>CMT/HC</td>
    <td>Quốc tịch</td>
    <td>Tổng tiền</td>
    <td align="center">TT</td>
    <td><input type="button" value="In hoặc PDF" style="width:100%;" onclick="printWebPart('printable_area');" /></td>
  </tr>
  <tr>
    <td>Tìm kiếm</td>
    <td><input name="id" type="text" id="id" style="width:100px" /></td>
    <td><input name="detail" type="text" id="detail" style="width:100px" /></td>
    <td><input name="full_name" type="text" id="full_name" style="width:100px" /></td>
    <td><input name="email" type="text" id="email" style="width:100px" /></td>
    <td><input name="phone" type="text" id="phone" style="width:100px" /></td>
    <td><input name="passport" type="text" id="passport" style="width:100px" /></td>
    <td><select name="nationality_id" id="nationality_id"></select></td>
    <td>&nbsp;</td>
    <td align="center"><input class="excel-opt" name="paid" type="checkbox" value="1" id="paid" /></td>
    <td><input type="submit" value="Tìm kiếm" style="width:100%;" /></td>
  </tr>
  <tr>
    <td align="center">Tất cả <input class="excel-opt" name="check_all" type="checkbox" value="id" id="check_all" title="Chọn tất cả" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="id" id="excel_opt[]" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="detail" id="excel_opt[]" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="full_name" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="email" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="phone"/></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="passport"/></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="nationality" id="excel_opt[]"/></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="total" /></td>
    <td align="center"><input class="excel-opt" name="excel_opt[]" type="checkbox" value="paid" id="excel_opt[]" /></td>
    <td><input name="export_excel" type="submit" value="Export excel" style="width:100%;" /></td>
  </tr>
</table><br />
<div id="printable_area">
<div class="print-title">
    <h4>Danh sách hóa đơn dịch vụ</h4>
</div><!--End .title-->
<table width="100%" border="1" cellspacing="0" cellpadding="5" bordercolor="#CCCCCC">
  <tr bgcolor="#EFEFEF">
    	<th width="5%" align="center">ID</th>
    	<th width="15%">Chi tiết đơn hàng</th>
    	<th width="80" align="left" valign="middle" bordercolor="#E7E7E7" bgcolor="#F0F0F0"><a>Ngày đặt</a></th>
    	<th width="15%">Người đặt</th>
        <th width="100" align="center">CMT/HC</th>
        <th width="200" align="center">Quốc tịch</th>
    	<th width="20%" align="center">Email</th>
    	<th align="center">Điện thoại</th>
        <th width="10%" align="center">Tổng tiền</th>
        <th width="100" align="center">Thanh toán</th>
        <?php if(User::can_delete(false,ANY_CATEGORY)){?><th width="50" align="center" class="print-hide">&nbsp;</th><?php }?>
    </tr>
    <!--LIST:orders-->
     <tr>
    	<td align="center">[[|orders.id|]]</td>
    	<td><pre>[[|orders.detail|]]</pre></td>
    	<td><?php echo date('h\h:i d/m/Y',[[=orders.time=]]);?></td>
    	<td><strong>[[|orders.full_name|]]</strong>
  	  </td>
        <td align="center">[[|orders.passport|]]</td>
        <td align="center">[[|orders.nationality|]]</td>
        <td>[[|orders.email|]]</td>        
		<td align="center">[[|orders.phone|]]</td>
        <td align="right">[[|orders.total|]]$</td>
    	<td align="center"><?php echo ([[=orders.paid=]] == 1)?' Đã TT ':' <strong>Chưa</strong> ';?></td>
    	<?php if(User::can_delete(false,ANY_CATEGORY)){?><td align="right" class="print-hide"><a href="<?php echo Url::build_current(array('cmd'=>'delete','id'=>[[=orders.id=]]));?>">Xóa</a></td><?php }?>
     </tr>
    <!--/LIST:orders-->
    <tr>
    	<td align="center">&nbsp;</td>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="right">Tổng thanh toán</td>
		<td align="right"><strong>[[|total|]]</strong>$</td>
    	<td align="center">&nbsp;</td>
    	<?php if(User::can_delete(false,ANY_CATEGORY)){?><td align="right" class="print-hide">&nbsp;</td><?php }?>
    </tr>
</table>
</div>
</div>
</div>
</form>
</fieldset>
<script>
jQuery(document).ready(function(){
	jQuery('#check_all').click(function(){
		if(!($check = jQuery(this).attr('checked'))){
			$check = false;
		}
		
		jQuery('.excel-opt').attr('checked',$check);
	});
});
</script>