<h2>Báo cáo lượt bài đăng tháng <?php echo Url::get('month')?></h2>
<fieldset id="toolbar">
	<form name="ReportForm" method="post">
  <div class="input-group">
      <button type="button" class="btn btn-secondary">Tháng</button>
      <span class="input-group-btn"></span>
      <select name="month" id="month" class="form-control"></select>
      <span class="input-group-btn"></span>
      <button type="button" class="btn btn-secondary">Năm </button>
      <span class="input-group-btn"></span>
      <select name="year" id="year" class="form-control"></select>
      <span class="input-group-btn"></span>
      <input type="submit" value="Xem" class="btn btn-default">
 </div>
 </form>
</fieldset>
<fieldset id="toolbar">
	<table width="100%" border="1" cellspacing="0" cellpadding="5">
  <tbody>
  	<!--LIST:reports-->
    <tr <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#DDD;"':'';?>>
      <td>[[|reports.name|]]</td>
      <!--LIST:dates-->
      <td><?php echo $this->map['reports']['current'][[[=dates.id=]]];?></td>
      <!--/LIST:dates-->
    </tr>
    <!--/LIST:reports-->
  </tbody>
</table>

</fieldset>