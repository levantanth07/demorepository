<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked)
			{
				status = true;
			}
		});	
		return status;
	}
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListCategoryForm.submit();
	}
</script>
<form method="post" name="SearchCategoryForm">
<fieldset id="toolbar">
	<div id="toolbar-title">
		Quản lý phân loại hàng hóa
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<?php 
		if(URL::get('cmd')=='delete' and User::can_delete(false,ANY_CATEGORY)){?> 
		 	<td id="toolbar-trash"  align="center"><a onclick="$('cmd').cmd='delete';ListCategoryForm.submit();" > <span title="Trash"> </span> [[.Trash.]] </a> </td>
			<td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
		<?php 
		}else{ 
		if(User::can_add(false,ANY_CATEGORY)){?>
				<td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>#"> <span title="New"> </span> [[.New.]] </a> </td>
			<?php }?>
		<?php if(User::can_delete(false,ANY_CATEGORY)){?>
		 <td id="toolbar-trash"  align="center"><a  onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> [[.Trash.]] </a> </td>
		<?php }
		}?>
		</tr>
	  </tbody>
	</table>
    </div>
</fieldset>
</form>	
<br>
<fieldset id="toolbar">
	<form name="ListCategoryForm" method="post">
		<a name="top_anchor"></a>		
		<table class="table">
		<thead>
				<tr valign="middle" bgcolor="<?php echo Portal::get_setting('crud_list_item_bgcolor','#F0F0F0');?>" style="line-height:20px">
				<th width="1%" title="[[.check_all.]]">
					<input type="checkbox" value="1" id="Category_all_checkbox" onclick="select_all_checkbox(this.form,'qlbh_product_category',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
				<th nowrap align="left">
					<a title="[[.sort.]]" href="<?php echo URL::build_current(((URL::get('order_by')=='category.name_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'category.name_'.Portal::language()));?>" >
					<?php if(URL::get('order_by')=='category.name_'.Portal::language()) echo '<img alt="" src="skins/default/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>
					[[.name.]]					</a>				</th>
				<!--IF:cond1(Url::sget('page')=='portal_category')--><th nowrap align="left"><a>[[.type.]]</a></th><!--/IF:cond1-->
				<th nowrap align="left">
					<a href="<?php echo URL::build_current(((URL::get('order_by')=='category.status' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'category.status'));?>" title="[[.sort.]]">
					<?php if(URL::get('order_by')=='category.status') echo '<img alt="" src="skins/default/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>
					[[.status.]]					</a>				</th>
				<?php if(User::can_edit(false,ANY_CATEGORY))
				{?>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<?php }?>
			</tr>
			</thead>
			<tbody>
			<!--LIST:items-->
			<?php $onclick = 'location=\''.URL::build_current().'&cmd=edit&id='.urlencode([[=items.id=]]).'\';"';?>
			<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],[[=just_edited_ids=]])))){ echo '#F7F7F7';} else {echo 'white';}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if([[=items.i=]]%2){echo 'background-color:#F9F9F9';}?>" id="Category_tr_[[|items.id|]]">
				<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'qlbh_product_category',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="Category_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />
				<td align="left" nowrap onclick="window.location='<?php echo Url::build_current().'&cmd=edit&id='.[[=items.id=]];?>'" <?php if(User::can_edit(false,ANY_CATEGORY)){?> <?php }?>>
						[[|items.indent|]]
						[[|items.indent_image|]]
						<span class="page_indent">&nbsp;</span>
						[[|items.name|]]</td>
				<!--IF:cond1(Url::sget('page')=='portal_category')--><td>[[|items.type|]]</td><!--/IF:cond1-->
				<td nowrap align="left">
						[[|items.status|]]					</td>
				<?php if(User::can_edit(false,ANY_CATEGORY))
				{?><td width="24px" align="center">[[|items.move_up|]]</td>
				<td width="24px" align="center">[[|items.move_down|]]</td>
				<?php }?>
			</tr>
			<!--/LIST:items-->
			</tbody>
		</table>
		<input type="hidden" name="cmd" value="" id="cmd"/>
		<!--IF:delete(URL::get('cmd')=='delete')-->
		<input type="hidden" name="confirm" value="1" />
			<!--/IF:delete-->
</form>
		
</fieldset>