<script>
	function check_selected(){
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked)
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd){
		jQuery('#cmd').val(cmd);
		document.NewsAdmin.submit();
	}
</script>
<fieldset id="toolbar">
	<div id="toolbar-title">
		Quản lý nội dung <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table align="right">
	  <tbody>
		<tr>
      <!--IF:user_admin(User::is_admin())-->
			<td id="toolbar-move"  align="center">
    	    </td>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> Thêm </a> </td><?php }?>
		  <?php if(User::can_delete(false,ANY_CATEGORY)){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> Xóa </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
  </div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="NewsAdmin" method="post" action="?page=news_admin">
		<a name="top_anchor"></a>
    <div class="row">
    	<div class="col-md-6">
      	<div class="input-group">
          <input name="search" type="text" id="search" class="form-control" placeholder="Nhập từ khóa tìm kiếm">
          <span class="input-group-btn">
          <button onclick="document.NewsAdmin.submit();" class="btn btn-default">Tìm kiếm</button>
          </span>
        </div>
      </div>
      <div class="col-md-6">
      	<div class="input-group" style="width:100%;">
      	<select name="search_category_id" class="form-control"  id="search_category_id" onchange="document.NewsAdmin.submit();"></select>
        <span class="input-group-btn" style="width:0px;"></span>
        <select name="status" id="status" class="form-control" onchange="document.NewsAdmin.submit();"></select>
        </div>
      </div>
    </div><br>
		<table class="table">
	  <thead>
					<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
					<th width="1%" align="left" nowrap><a>#</a></th>
					<th width="1%" title="[[.check_all.]]">
					  <input type="checkbox" value="1" id="NewsAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'NewsAdmin',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
					<th width="40%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'name_id','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.name.]]</a></th>
					<th width="2%" align="center" nowrap="nowrap">Xem tin</th>
					<th width="2%" align="left" nowrap="nowrap">&nbsp;</th>
					<th width="2%" align="left" nowrap="nowrap"><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'status','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.status.]]</a></th>
					<th width="1%" align="left" nowrap="nowrap"><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'publish','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">Duyệt</a></th>
					<th width="6%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'position','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.positon.]]</a><img src="assets/default/images/cms/menu/filesave.png" onclick="jQuery('#cmd').val('update_position');document.NewsAdmin.submit();" style="cursor:pointer"></th>
					<th width="12%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'category_id','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.category_name.]]</a></th>
					<th width="7%" align="left" nowrap><a>[[.user_id.]]</a></th>
					<th width="4%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'time','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.date.]]</a></th>
					<th width="5%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'hitcount','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.hitcount.]]</a></th>
					<th width="4%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'id','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">[[.id.]]</a></th>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<th width="2%" align="left" nowrap><a>[[.edit.]]</a></th>
					<?php }?>
				</tr>
		  </thead>
				<tbody>
				<!--LIST:items-->
				<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],[[=just_edited_ids=]])))){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#FFFFDD');} else {echo Portal::get_setting('crud_item_bgcolor','white');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if([[=items.index=]]%2){echo 'background-color:#F9F9F9';}?>" id="NewsAdmin_tr_[[|items.id|]]">
					<th width="1%" align="left" nowrap><a>[[|items.index|]]</a></th>
					<td width="1%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'NewsAdmin',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="NewsAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
					<td  align="left">[[|items.name|]]&nbsp;&nbsp;<?php if([[=items.total_comment=]]>0){?><a  style="color:#FF0000" href="<?php echo Url::build('manage_comment',array('item_id'=>[[=items.id=]]));?>">[[[|items.total_comment|]]]</a><img src="assets/default/images/cms/comment.gif"><?php }?></td>
					<td align="center"><a target="_blank" href="bai-viet/[[|items.category_name_id|]]/[[|items.name_id|]]/" title="Xem tin">Xem</a></td>
					<td align="left" nowrap="nowrap"><img src="[[|items.small_thumb_url|]]" width="50"></td>
					<td align="left" nowrap="nowrap">[[|items.status|]] </td>
					<td align="center" nowrap="nowrap">
          	<!--IF:can_admin_cond(User::can_admin(false,ANY_CATEGORY))-->
            <a href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit','page_no','category_id'));?>">
            <!--/IF:can_admin_cond-->
					  <!--IF:cond([[=items.publish=]])-->
					  <img src="assets/default/images/buttons/check.gif" width="11" height="11" />
					  <!--ELSE-->
            <!--IF:can_admin_cond(User::can_admin(false,ANY_CATEGORY))-->
					  [Duyệt]
            <!--/IF:can_admin_cond-->
  					<!--/IF:cond-->
            <!--IF:can_admin_cond(User::can_admin(false,ANY_CATEGORY))-->
					  </a>
            <!--/IF:can_admin_cond-->
            <!--IF:cond([[=items.publish=]] and [[=items.publisher=]])--><br /><strong>[[|items.publisher|]]</strong><br />[[|items.published_time|]]<!--/IF:cond-->
            </td>
					<td align="left"><input name="position_[[|items.id|]]" type="text" id="position_[[|items.id|]]" value="[[|items.position|]]" class="form-control"></td>
					<td align="left">[[|items.categories|]]</td>
					<td align="left" nowrap>[[|items.user_id|]]</td>
					<td align="left" nowrap><?php echo date('h\h:i d/m/Y',[[=items.time=]]);?></td>
					<td align="left" nowrap>[[|items.hitcount|]]</td>
					<td align="left" nowrap>[[|items.id|]]</td>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<td align="left" nowrap width="2%"><a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit','page_no','search_category_id'));?>">Sửa</a></td>
					<?php }?>
				</tr>
				<!--/LIST:items-->
				</tbody>
	  </table>
		<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;height:8px;#width:99%" align="center">
			<tr>
			<td width="30%" align="left">
			<td width="18%">&nbsp;<a>[[.display.]]</a>
			  <select name="item_per_page" id="item_per_page" class="select" style="width:50px" size="1" onchange="document.NewsAdmin.submit( );" ></select>&nbsp;[[.of.]]&nbsp;[[|total|]]</td>
			<td width="31%">[[|paging|]]</td>
</tr></table>
			<table width="100%" class="table_page_setting">
	</table>
		<input type="hidden" name="cmd" value="" id="cmd">
  </form>
  <div style="#height:8px"></div>
</fieldset>