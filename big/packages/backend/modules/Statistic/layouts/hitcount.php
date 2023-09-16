<h2>Thống kê lượt xem</h2>
<fieldset id="toolbar">
	<div class="row">
	<div id="toolbar-content" class="col-md-12">
	<form name="HitcountForm" method="post">
    <div class="input-group">
      <button type="button" class="btn btn-secondary">Từ ngày</button>
      <span class="input-group-btn"></span>
      <input name="date_from" type="text" id="date_from" class="form-control">
      <span class="input-group-btn"></span>
      <button type="button" class="btn btn-secondary">Đến ngày </button>
      <span class="input-group-btn"></span>
      <input name="date_to" type="text" id="date_to" class="form-control">
      <span class="input-group-btn"></span>
      <select name="search_user_id" id="search_user_id" class="form-control" size="1" onchange="document.HitcountForm.submit();"></select>
      <span class="input-group-btn"></span>
      <input type="submit" value="Xem" class="btn btn-default">
    </div>
  </form>
	</div>
  </div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<table class="table table-bordered">
  <tr style="background-color:#F0F0F0">
	<th width="3%" align="left"><a>#</a></th>
	<th width="50%" align="left"><a>Tin bài</a></th>
	<th width="20%" align="left"><a>Danh mục</a></th>
	<th width="10%" align="left">Ngày đăng</th>
	<th width="5%" align="center"><a>By</a></th>
	<th width="10%" align="left">Lượt xem</th>
	</tr>
  <!--LIST:items-->
  <tr <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if([[=items.indexs=]]%2){echo 'background-color:#F9F9F9';}?>">
	<td>[[|items.indexs|]]</td>
	<td>[[|items.name|]]</td>
	<td>[[|items.category_name|]]</td>
	<td><?php echo date('H:i\' d/m/Y',[[=items.time=]])?></td>
	<td align="center">[[|items.user_id|]]</td>
	<td align="right">[[|items.hitcount|]]</td>
	</tr>
  <!--/LIST:items-->
 </table> 	
 <div class="paging">[[|paging|]]</div>
<div class="alert alert-success text-right">(<?php echo System::display_number([[=total_hitcount=]]);?> lượt xem / <?php echo System::display_number([[=total=]]);?> tin bài - TB <?php echo [[=total=]]?System::display_number([[=total_hitcount=]]/[[=total=]]):'';?> lượt xem/bài)</div>
</fieldset>
<script>
jQuery(document).ready(function(){
	jQuery('#date_from').datepicker();
	jQuery('#date_to').datepicker();
});

</script>