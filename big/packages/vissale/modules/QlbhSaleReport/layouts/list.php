<style>
.search-box{width: 1028px;margin: auto;}
.search-box div{width:150px !important;float:left;margin:2px;padding:2px;}
</style>
<link rel="stylesheet" type="text/css" href="skins/admin/css/jquery.datetimepicker.css"/>
<script src="skins/admin/scripts/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	var isValid;
	jQuery(document).ready(function(){
		jQuery('#start_time').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});
	});
	jQuery(document).ready(function(){
		jQuery('#end_time').datetimepicker({
			format:'d/m/Y',
			formatDate:'d/m/Y',
			defaultDate:'<?php echo date('d/m/Y')?>',
			timepicker:false,
			closeOnDateSelect:true
		});
	});
</script>
<div class="book-supplier-bound">
<div class="content" style="width:100%">
<form name="ListKoRoomForm" id="ListKoRoomForm" method="post">
<div class="search-box">
<div style="width:70px;">Từ ngày: <input name="start_time" type="text" id="start_time" style="width:98%;" /></div>
<div style="width:70px;">Đến ngày: <input name="end_time" type="text" id="end_time" style="width:98%;" /></div>
<div style="text-align:center"><br /><input name="search" type="submit" id="search" value="Tìm kiếm"> <a href="index062019.php?page=qlbh_stock_invoice_report&act=report" style="margin-left:10px;color:#4F2324;"> [Hủy kết quả]</a></div>
<div style="float:right;"><input type="button" value=" In báo cáo" onClick="printWebPart('report_bound');return false;"></div>
<br clear="all" />
</div></form>
<div id="report_bound" style="text-align:center;padding:10px;">
<table width="100%" border="0" cellspacing="0" cellpadding="5" bordercolor="#FFFFFF">
  <tbody>
    <tr style="font-size:12px;">
      <td width="30%" align="left"><h3 style="font-size:20px;">&nbsp;</h3><div></div></td>
      <td>&nbsp;</td>
      <td width="30%" align="right">Giờ in: <?php echo date('H:i\' d/m/Y',time())?></td>
    </tr>
  </tbody>
</table>
<center>
    <h2 style="font-size:24px;margin:20px 0px 5px 0px;">BÁO CÁO BÁN HÀNG</h2>
    <div>
        	<!--IF:cond(Url::get('start_time'))--><i>(Từ ngày <?php echo Url::get('start_time')?></i><!--/IF:cond-->
        <!--IF:cond(Url::get('end_time'))--><i>Đến ngày <?php echo Url::get('end_time')?>)</i><!--/IF:cond-->
    </div>
    <!--IF:cond(Url::get('org_book')==1)-->
    <div>
        Đặt trực tiếp
    </div>
    <!--/IF:cond-->
    <!--IF:cond(Url::get('org_book')==2)-->
    <div>
        Đặt từ Checkin.vn online
    </div>
    <!--/IF:cond-->
    <!--IF:cond(Url::get('org_book')==3)-->
    <div>
        Đặt từ Checkin.vn offline
    </div>
    <!--/IF:cond-->
    <br>
</center>
	<div class="content">
		<table class="table">
			<tr style="font-size:12px;">
			  <th width="1%" align="left">#</th>
			  <th width="50%" align="center">Hàng hóa/Dịch vụ</th>
              <th width="10%" align="center">Số lượng</th>
              <th width="10%" align="center">Đơn vị</th>
              <th width="10%" align="center">Tổng tiền</th>
			  <th width="10%" align="center">Chiết khấu</th>
              <th width="10%" align="center">Thu về</th>
		  </tr>
		  <!--LIST:items-->
			<tr style="font-size:11px;">
			  <td>[[|items.i|]]</td>
				<td align="left">[[|items.name|]]</td>
				<td align="right">[[|items.quantity|]]</td>
				<td align="center">[[|items.unit|]]</td>
				<td align="right"><?php echo System::display_number([[=items.amount=]],false,false);?></td>
				<td align="right"><?php echo System::display_number([[=items.discount=]],false,false);?></td>
                <td align="right"><?php echo System::display_number([[=items.remain=]],false,false);?></td>                
			</tr>
		  <!--/LIST:items-->
          <tr style="font-size:12px;">
			  <td colspan="4" align="right"><strong>Tổng cộng: </strong></td>
			  <td align="right"><strong><?php echo System::display_number([[=total_amount=]],false,false);?></strong></td>
		    <td align="right"><strong><?php echo System::display_number([[=total_discount=]],false,false);?></strong></td>              
		    <td align="right"><strong><?php echo System::display_number([[=total_remain=]],false,false);?></strong></td>
			</tr>
		</table>
	  <div class="paging">[[|paging|]]</div>
	</div>
</div>	
</div>
</div>