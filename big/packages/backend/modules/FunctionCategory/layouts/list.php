<form method="post" name="SearchCategoryForm">
<table class="table">
	<tr>
		<td width="35%" class="form-title">[[.list_all_function_of_system.]]</td>
		<td width="1%" align="right"><a href="<?php echo URL::build_current(array('cmd'=>'export_cache'));?>" class="button-medium-export">[[.cache.]]</a></td>
		<?php
		if(URL::get('cmd')=='delete' and User::can_delete(false,ANY_CATEGORY)){?>
		<td width="1%"><a onclick="$('cmd').cmd='delete';ListCategoryForm.submit();"  class="button-medium-delete">[[.Delete.]]</a></td>
		<td width="1%"><a href="<?php echo URL::build_current();?>"  class="button-medium-back">[[.back.]]</a></td>
		<?php
		}else{
		if(User::can_add(false,ANY_CATEGORY)){?>
		<td width="1%"><a href="<?php echo URL::build_current(array('cmd'=>'add'));?>"  class="button-medium-add">[[.Add.]]</a></td>
		<?php }?>
		<?php if(User::can_delete(false,ANY_CATEGORY)){?>
		<td width="1%"><a href="javascript:void(0)" onclick="ListCategoryForm.cmd.value='delete';ListCategoryForm.submit();"  class="button-medium-delete">[[.Delete.]]</a></td>
		<?php }
		}?>
	</tr>
</table>
</form>
<form name="ListCategoryForm" method="post">
	<a name="top_anchor"></a>
	<table class="table">
		<thead>
			<tr valign="middle" bgcolor="<?php echo Portal::get_setting('crud_list_item_bgcolor','#E6E6E6');?>" style="line-height:20px">
				<th width="20" title="[[.check_all.]]" align="left">
				<input type="checkbox" value="1" id="Category_all_checkbox" onclick="select_all_checkbox(this.form,'Category',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
				<th width="532" align="left" nowrap>
				<a title="[[.sort.]]" href="<?php echo URL::build_current(((URL::get('order_by')=='function.name_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'function.name_'.Portal::language()));?>" >
				<?php if(URL::get('order_by')=='function.name_'.Portal::language()) echo '<img alt="" src="assets/default/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>
				[[.name.]]					</a>				</th>
				<th width="95" align="left" nowrap>[[.status.]]</th>
				<th width="95" align="left" nowrap>Mở cửa sổ mới</th>
				<th width="30" align="left" nowrap>				</th>
				<?php if(User::can_edit(false,ANY_CATEGORY))
				{?>
				<th width="30">&nbsp;</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			<?php $i=0;?>
			<!--LIST:items-->
			<?php $onclick = 'location=\''.URL::build_current().'&cmd=edit&id='.urlencode([[=items.id=]]).'\';"';?>
			<tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#F7F7F7'));?> style="background:<?php echo ([[=items.status=]]=='HIDE')?'#CCC':'#FFF';?>;" id="Category_tr_[[|items.id|]]">
				<td width="20" align="left"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Category',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="Category_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />
				<td align="left" nowrap onclick="window.location='<?php echo Url::build_current().'&cmd=edit&id='.[[=items.id=]];?>'">
				[[|items.indent|]]
				[[|items.indent_image|]]
				<span class="page_indent">&nbsp;</span>
				[[|items.name|]]</td>
				<td align="left" nowrap onclick="window.location='<?php echo Url::build_current().'&cmd=edit&id='.[[=items.id=]];?>'">[[|items.status|]]</td>
				<th><?php echo [[=items.open_new_window=]]?'Có':'Không';?></th>
				<td align="left" nowrap <?php echo $onclick;?>>[[|items.move_up|]]</td>
				<td width="30" align="center">[[|items.move_down|]]</td>
			</tr>
			<!--/LIST:items-->
		</tbody>
	</table>
	<table width="100%">
		<tr>
			<td width="100%">
			[[.select.]]:&nbsp;
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.ListCategoryForm,'Category',true,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_all.]]</a>&nbsp;
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.ListCategoryForm,'Category',false,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_none.]]</a>
			<a href="javascript:void(0)" onclick="select_all_checkbox(document.ListCategoryForm,'Category',-1,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_invert.]]</a>
			</td>
			<td>
			<a name="bottom_anchor" href="#top_anchor"><img alt="" src="assets/default/images/top.gif" title="[[.top.]]" border="0" alt="[[.top.]]"></a>
			</td>
		</tr>
	</table>
	<input type="hidden" name="cmd" value="" id="cmd"/>
	<!--IF:delete(URL::get('cmd')=='delete')-->
	<input type="hidden" name="confirm" value="1" />
	<!--/IF:delete-->
</form>
