<script>
	function make_cmd(cmd)
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked == true)
			{
				status = true;
			}
		});
		if(!status)
		{
			alert('<?php echo Portal::language('You_must_select_atleast_item');?>');
			return status;
		}
		jQuery('#cmd').val(cmd);
		document.MakeSlideMediaAdminForm.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.manage_media.]]</legend>
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-save"  align="center"><a onclick="make_cmd('make_slide');"> <span title="[[.Save.]]"> </span> [[.Save.]] </a> </td><?php }?>
  		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-list"  align="center"><a  href="<?php echo Url::build_current(array('cmd'=>'slide_list'));?>"> <span title="[[.Slide_list.]]"> </span> [[.Slide_list.]] </a> </td><?php }?>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="MakeSlideMediaAdminForm" method="post">
		<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
			<thead>
				<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
					<th colspan="2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						  <tr>
							<th width="55%" align="right"><a>[[.Make_slide.]]</a>&nbsp;([[.select_image_to_make_slide.]])</th>
							<th width="45%" align="right"><select name="category_id" id="category_id" class="inputbox" size="1" onchange="location='<?php echo Url::build_current();?>&cmd=make_slide&category_id='+this.value;"></select></th>
						  </tr>
						</table>
					</th>
			    </tr>
		  </thead>
				<tbody>
					<tr>
						<td  valign="top" width="320">
							<div id="panel_1" style="margin-top:8px;border: 1px solid #E7E7E7;">
							<span>[[.Parameters_properties.]]</span>
							<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
								<!--LIST:languages-->
								<tr>
									<td align="right"><?php echo Portal::language('name_slide'.[[=languages.id=]]);?> (<span style="color:#FF0000" class="require">*</span>)</td>
									<td align="left"><input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="input-large" size="17"></td>
								</tr>
								<!--/LIST:languages-->
								<tr>
									<td align="right" valign="top">[[.options.]]</td>
									<td>,[[.property.]]:[[.value.]],[[.property.]]:[[.value.]]<br><textarea name="option" style="width:200px;height:100px;" id="option"></textarea></td>
								</tr>
								<tr>
									<td align="right">[[.Effect.]]</td>
									<td align="left"><select name="effect" id="effect" class="select-large"></select></td>
								</tr>
							</table>
						</div>
						</td>
						<td valign="top">
						<div style="width:100%;height:300px;overflow:auto;padding-left:5px;">
							<!--LIST:items-->
								<div style="float:left;width:78px;margin:8px;border:1px solid #E7E7E7;padding:2px;padding-bottom:0px;">
									<div align="center">
									<?php
										if(preg_match_all('/.swf/',[[=items.image_url=]],$matches))
										{
											echo '<embed src="'.[[=items.image_url=]].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="78" height="70"></embed>';
										}
										else
										{
											echo '<img src="'.[[=items.image_url=]].'" width="78" height="70" onerror="this.src=\'assets/default/images/no_image.gif\'">';
										}
									?>
									</div>
									<div>
										<input name="selected_ids[]" type="checkbox" id="selected_ids" value="[[|items.id|]]" <?php if(isset([[=items.item_id=]])){echo 'checked="checked"';}?>>
										<span><?php echo String::display_sort_title([[=items.name=]],2);?></span>
									</div>
								</div>
							<!--/LIST:items-->
						</div>						</td>
					</tr>
				</tbody>
	  </table>
	<table  width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;#width:99%" align="center">
	  <tr>
		<td>
			[[.select.]]:&nbsp;
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.MakeSlideMediaAdminForm,'MakeSlideMediaAdmin',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.MakeSlideMediaAdminForm,'MakeSlideMediaAdmin',false,'#FFFFEC','white');">[[.select_none.]]</a>
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.MakeSlideMediaAdminForm,'MakeSlideMediaAdmin',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
		</td>
	</table>
	<input type="hidden" name="cmd" value="make_slide" id="cmd"/>
  </form>
  <div style="#height:8px;"></div>
</fieldset>