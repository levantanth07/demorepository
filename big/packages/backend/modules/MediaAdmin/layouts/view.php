<fieldset id="toolbar">
	<legend><?php echo Portal::language(Url::get('page'));?></legend>
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
  		  <td id="toolbar-list"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'slide_list'));?>"> <span title="[[.Slide_list.]]"> </span> [[.Slide_list.]] </a> </td>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'make_slide'));?>#"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
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
					<th width="55%" align="center"><a>[[.Effect_slide.]]</a></th>
					<th width="45%" align="center">
						<div align="center" style="float:left;padding-left:45%"><a>[[.html_code.]]</a></div>
						<div align="right">
							<select name="slide_id" id="slide_id" class="inputbox" size="1" onchange="location='<?php echo Url::build_current();?>&cmd=view_slide&slide_id='+this.value;"></select>
						</div>
					</th>
				</tr>
		  </thead>
				<tbody>
					<tr>
					  <td  valign="top" width="50%">
					  	<div style="width:100%;height:99%;min-height:320px;overflow:auto;padding-left:5px;">
							<?php eval('?>'.[[=html=]].'<?php ');?>
						</div>
					  </td>
						<td valign="top"  width="50%">
						<textarea style="width:99%;height:99%;min-height:320px;overflow:auto;padding-left:5px;"><?php echo htmlentities([[=html=]]);?></textarea>
					 </td>
					</tr>
				</tbody>
	  </table>
	<input type="hidden" name="cmd" value="view_slide" id="cmd"/>
  </form>
</fieldset>