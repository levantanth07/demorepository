<script>
	function check_selected(){
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked) {
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
<div class="panel">
    <fieldset id="toolbar">
        <div class="col-xs-8">
            <h3 class="title">Quản lý nhóm quyền <span>[ <?php echo Url::get('cmd','list');?> ]</span></h3>
        </div>
        <div class="col-xs-4 text-right">
            <table align="right">
                <tbody>
                <tr>
                    <td id="toolbar-move"  align="center"></td>
                    <td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> Thêm </a> </td>
                    <td id="toolbar-trash"  align="center"><a onclick="if(confirm('Bạn có chắc muốn xóa')){if(check_selected()){make_cmd('delete')}else{alert('Bạn phải chọn ít nhất một bản ghi để xóa');}}"> <span title="Trash"> </span> Xóa </a> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br>
    <fieldset id="toolbar">
        <form name="NewsAdmin" method="post">
            <table class="table table-bordered table-hover">
                <thead>
                <tr valign="middle" style="line-height:20px">
                    <th width="1%" title="[[.check_all.]]">
                        <input type="checkbox" value="1" id="NewsAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'NewsAdmin',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                    <th width="15%" align="left" nowrap><a href="<?php echo Url::build_current(array('search','category_id','author','status','ob'=>'name_id','d'=>(Url::get('d')=='DESC')?'ASC':'DESC'));?>">Tên</a></th>
                    <th width="20%" align="left" nowrap>Quyền</th>
                    <th width="45%" align="left" nowrap>Trạng thái</th>
                    <th width="2%" align="left" nowrap><a>Sửa</a></th>
                </tr>
                </thead>
                <tbody>
                <!--LIST:items-->
                <tr bgcolor="#fff">
                    <td width="1%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'NewsAdmin',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="NewsAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
                    <td  align="left">[[|items.name|]]</td>
                    <td  align="left">[[|items.roles|]]</td>
                    <td  align="left">[[|items.role_status|]]</td>
                    <td align="left" nowrap width="2%"><a href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit','page_no','search_category_id'));?>" class="btn btn-default"><img src="assets/default/images/buttons/button-edit.png"></a></td>
                </tr>
                <!--/LIST:items-->
                </tbody>
            </table>
            <table class="table">
                <tr>
                    <td width="18%">Tổng: [[|total|]]</td>
                    <td width="31%">[[|paging|]]</td>
                </tr></table>
            <table width="100%" class="table_page_setting">
            </table>
            <input type="hidden" name="cmd" value="" id="cmd">
        </form>
    </fieldset>
</div>