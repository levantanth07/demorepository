<fieldset id="toolbar">
	<div id="toolbar-personal">[[.manage_contact.]]</div>
	<div id="toolbar-content" align="right">
		<table>
			<tbody>
			<tr>
				<td>&nbsp;</td>
			</tr>
			</tbody>
		</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<?php $i=1;?>
	<!--LIST:items-->
	<div style="margin-top:5px;"></div>
	<form name="ContactList" method="post">
		<table class="table">
			<tr>
				<td width="1%" valign="top">
					<?php echo $i++;?>/
				</td>
				<td width="90%" valign="top" style="line-height:18px;;">
					<b>[[.full_name.]]:</b>&nbsp;<strong>[[|items.name|]]</strong> ~ <a >[[|items.portal_id|]]</a><br />
					<!--IF:cond([[=items.time=]])--><b>[[.date_send.]]</b> :&nbsp;<?php echo date('H:i d/m/Y',[[=items.time=]]);?> <br />
					<!--/IF:cond-->
					<!--IF:cond([[=items.phone=]])--><b>[[.phone.]]:</b>&nbsp;[[|items.phone|]] <br />
					<!--/IF:cond-->
					<!--IF:cond([[=items.address=]])--><b>[[.address.]]:</b> &nbsp;[[|items.address|]]<br>
					<!--/IF:cond-->
					<!--IF:cond([[=items.email=]])--><b>[[.email.]]:</b> <a href="mailto:[[|items.email|]]">&nbsp;[[|items.email|]]</a><br>
					<!--/IF:cond-->
					<!--IF:cond([[=items.email=]])--><b>Giới tính:</b> <a>&nbsp;<?php if([[=items.genders=]]==1) echo 'Nam';else echo 'Nữ'; ?></a><br>
					<!--/IF:cond-->

				</td>
				<td valign="top" nowrap="nowrap" align="right">
					<!--IF:cond([[=items.is_check=]]==1)-->
					[[.checked.]]&nbsp;|
					<!--ELSE-->
					<span style="color:#FF0000">[[.uncheck.]]</span> |
					<!--/IF:cond-->
					<a href="javascript:void(0)" onClick="change_display_status(getId('div_[[|items.id|]]'),this);">[[.detail.]]</a>&nbsp;|
					<a href="<?php echo URL::build_current(array('cmd'=>'delete'));?>&id=[[|items.id|]]" title="Xóa" class="btn btn-danger btn-sm">Xóa</a>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="center">
					<div id="div_[[|items.id|]]" style="display:none;">
						<table class="table table-bordered" style="border-top:5px solid #CCC;">
							<tr>
								<td width="42%" align="left"><b>[[.content.]]</b></td>
								<td colspan="2" align="right">
									[[.is_check.]]&nbsp; <input  name="confirm_[[|items.id|]]"  type="checkbox" <?php if([[=items.is_check=]]==1){ echo 'checked="checked"';}?> value="1" onclick="location='<?php echo Url::build_current(array('cmd'=>'check','id'=>[[=items.id=]]));?>'">
									<!--[[.answer.]]&nbsp;<a><img src="skins/default/images/buttons/right.jpg">--></a>
								</td>
							</tr>
							<tr>
								<td colspan="3" >[[|items.content|]]</td>
							</tr>
							<tr>
								<td  align="left">&nbsp;</td>
								<td colspan="2"  align="left"><b>[[.email.]]</b></td>
							</tr>
							<tr>
								<td  align="left">&nbsp;</td>
								<td colspan="2"  >[[|items.email|]]</td>
							</tr>
							<tr>
								<td width="33%"  align="left"><b>[[.phone.]]</b></td>
								<td width="25%"  align="left"><b>[[.name_sender.]]</b></td>
							</tr>
							<tr>
								<td >[[|items.phone|]]</td>
								<td >[[|items.name|]]</td>
							</tr>

							<tr>
								<td  align="left"><b>Giới tính</b></td>
								<td colspan="2"  align="left"><b>Nơi học THCS/THPT</b></td>
							</tr>
							<tr>
								<td  align="left"><?php if([[=items.genders=]]==1)echo 'Nam';else echo 'Nữ'; ?></td>
								<td colspan="2"  >[[|items.thpt|]]</td>
							</tr>


							<tr>
								<td  align="left"><b>Ngày sinh</b></td>
								<td colspan="2"  align="left"><b>Số CMND</b></td>
							</tr>
							<tr>
								<td  align="left">[[|items.birth|]]</td>
								<td colspan="2"  >[[|items.cmnd|]]</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</table>
		<!--/LIST:items-->
	</form>
	<table width="100%" cellpadding="6" cellspacing="0">
		<tr>
			<td class="pt">[[|paging|]]&nbsp;</td>
		</tr>
	</table>
</fieldset>
<script language="javascript">
	function change_display_status(obj, detail_div){
		if(obj.style.display=='none'){
			obj.style.display='';
			detail_div.innerHTML='[[.close.]]';
			<!--LIST:items-->
			if('div_[[|items.id|]]' != obj.id){
				getId('div_[[|items.id|]]').style.display = 'none';
			}
			<!--/LIST:items-->
		}else{
			obj.style.display='none';
			detail_div.innerHTML='[[.detail.]]';
		}
	}
</script>