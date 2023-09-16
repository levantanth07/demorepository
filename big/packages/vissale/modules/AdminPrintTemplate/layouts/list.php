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
		document.AdminPrintTemplate.submit();
	}
</script>
<fieldset id="toolbar">
    <div class="row">
        <div class="col-xs-8">
            <h3 class="title"><i class="fa fa-print"></i> Quản lý Mẫu in đơn hàng</h3>
        </div>
        <div class="col-xs-4 text-right">
            <?php if (Session::get('admin_group')) { ?>
                <a class="btn btn-primary"
                        href="<?php echo Url::build_current(array('cmd' => 'add')); ?>"> <span title="New"> </span>
                    Thêm </a>
            <?php } ?>
            <?php if (Session::get('admin_group')) { ?>
                <a  class="btn btn-danger"
                    onclick="if(confirm('<?php echo 'Bạn có chắc chắn muốn xóa?'; ?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo 'Bạn có chắc chắn muốn xóa?'; ?>');}}">
                    <span title="Trash"> </span> Xóa </a>
            <?php } ?>
        </div>
    </div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="AdminPrintTemplate" method="post" action="index062019.php?page=admin_print_template">
		<div class="list-item" style="padding:15px; ">
			<div class="row">
				<div class="col-lg-12">
					<table class="table" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
				  		<thead>
							<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
								<th width="1%" align="left" nowrap><a>#</a></th>
								<th width="40%" align="left" nowrap>Tên</th>
								<th width="3%" align="left" nowrap></th>
								<?php if(Session::get('admin_group')){?>
								<th width="2%" align="left" nowrap><a>Sửa</a></th>
                                <th width="1%" title="[[.check_all.]]">
                                    <input type="checkbox" value="1" id="AdminPrintTemplate_all_checkbox" onclick="select_all_checkbox(this.form,'AdminPrintTemplate',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>>
                                </th>
								<?php }?>
							</tr>
					  	</thead>
						<tbody>
							<?php $i=1; ?>
							<!--LIST:items-->
							<tr>
								<th width="1%" align="left" nowrap><a><?php echo  $i; ?></a></th>
								<td align="left" nowrap>
									<strong>[[|items.print_name|]]</strong>
									<div class="small">[[|items.template|]]</div>
								</td>
								<th width="1%" align="left" nowrap><a><?php if([[=items.set_default=]]==1){ echo 'Mẫu ưu tiên';} ?></a></th>
								<?php if(Session::get('admin_group')){?>
									<td align="left" nowrap width="2%">
										<a class="btn btn-default" href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit','page_no','search_category_id'));?>">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
									</td>
                                    <td width="1%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'AdminPrintTemplate',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="AdminPrintTemplate_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
								<?php }?>
							</tr>
							<?php $i++; ?>
							<!--/LIST:items-->
						</tbody>
				  	</table>
				</div>
			</div>
		</div>
	  <div class="paging">
	  	<div class="row" style="display: flex;justify-content: center;">
	  		[[|paging|]]
	  	</div>
	  </div>
		<input type="hidden" name="cmd" value="" id="cmd">
  </form>

  <div style="#height:8px"></div>

</fieldset>