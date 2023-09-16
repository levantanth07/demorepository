<script>
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ManageAdvertisment.submit();
	}
	jQuery(document).ready(function(){
		jQuery('#start_time').datepicker({ yearRange: '2008:2020' });
		jQuery('#end_time').datepicker({ yearRange: '2008:2020' });
	});
</script>
<fieldset id="toolbar">
	<div id="toolbar-title">[[.manage_advertisment.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span></div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_edit(false,ANY_CATEGORY)){?><td id="toolbar-save"  align="center"><a onclick="ManageAdvertisment.submit();"> <span title="Save"> </span> [[.Save.]] </a> </td><?php }?>
		   <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="Cancel"> </span> [[.Cancel.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="ManageAdvertisment" method="post">
  <div class="row">
			<div class="col-md-12">
				<table width="100%" cellspacing="0" cellpadding="2"  border="1" bordercolor="#E7E7E7" align="center">
						<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
						  <td>[[.region.]]</td>
						  <td><a>Vị trí</a></td>
				 		 </tr>
						<tr>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
				  </tr>
						<tr>
							<td><select name="region" id="region" class="select-large"></select></td>
							<td><input name="position" type="text" id="position" class="input-large"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
							<td height="25" style="border-right:1px solid #ECE9D8">[[.start_time.]]</td>
							<td>[[.end_time.]]</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><input name="start_time" type="text" id="start_time" size="20"  class="input-large"></td>
							<td><input name="end_time" type="text" id="end_time" size="20" class="input-large"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp; </td>
						</tr>
						<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
							<td colspan="2">[[.list_advertisment.]]</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="width:100%;height:300px;overflow:auto">
									<!--LIST:items-->
										<div style="float:left;width:200px;margin:8px;border:1px solid #E7E7E7;padding:2px;padding-bottom:0px;">
											<div>
											<?php
												if(preg_match_all('/.swf/',[[=items.image_url=]],$matches)){
													echo '<embed src="'.[[=items.image_url=]].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="70" height="70"></embed>';
												}else{
													echo '<img src="'.[[=items.image_url=]].'" height="70" onerror="this.src=\'assets/default/images/no_image.gif\'">';
												}
											?>
											</div>
											<div>
											<?php if([[=items.id=]] == Url::get('item_id')){?>
												<input name="item_list_[[|items.id|]]" type="checkbox" id="item_list_[[|items.id|]]" checked="checked">
											<?php }else{?>
												<input name="item_list_[[|items.id|]]" type="checkbox" id="item_list_[[|items.id|]]">
											<?php }?>
												<span><?php echo String::display_sort_title([[=items.name=]],2);?></span>
											</div>
										</div>
									<!--/LIST:items-->
								</div>
							</td>
						</tr>
					</table>
			</div>
			<div class="col-md-4 hide"><select name="categories[]"<?php if(!URL::get('id')) echo ' size="20"  multiple="multiple" style="width:290px;height:510px;border:1px solid #E7E7E7;padding-left:10px;"';?> style="width:290px;" id="categories[]"></select>
				<?php if(Url::get('category_id')){?><script>document.getElementById("categories[]").value = '<?php echo Url::get('category_id');?>';</script><?php }?>
     	</div>
    </div>
	</form>
</fieldset>	