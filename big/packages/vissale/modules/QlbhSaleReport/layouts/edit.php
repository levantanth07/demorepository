<div class="edit-car-rental-bound">
<form name="EditQlbhSaleReportForm" method="post">
	<div id="book_toolbar">
		<div>
			<fieldset id="toolbar">
				<div id="toolbar-title">
					[[|title|]]
				</div>
				<div id="toolbar-content" align="right">
				<table>
				  <tbody>
					<tr>
					  <td id="toolbar-save"  align="center"><a onclick="EditQlbhSaleReportForm.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
					  <td id="toolbar-back"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="New"> </span> [[.Back.]] </a> </td>
					</tr>
				  </tbody>
				</table>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="content">
		<?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
		<fieldset>
			<table border="0" cellspacing="0" cellpadding="2">
				<tr>
                  <td class="label">Dịch vụ / hàng hóa(*):</td>
				  <td><input name="name" type="text" id="name" style="width:200px;" /></td>
			  </tr>
				<tr>
                  <td class="label">[[.price.]] (*):</td>
				  <td><input name="price" type="text" id="price" style="width:200px;" />
				    VND</td>
                    <tr>
                  <td class="label">Đơn vị (*):</td>
				  <td><input name="unit" type="text" id="unit" style="width:100px;" />
				    VND</td>
			  </tr>
				<tr style="display:none">
				  <td class="label">Cho phép đặt online</td>
				  <td><input name="online" type="checkbox" id="online" value="1" /></td>
				  </tr>
			</table>
	  </fieldset>
	</div>
</form>
</div>
<script>
	getId('online').checked = <?php echo Url::get('online')?'true':'false'?>;
</script>