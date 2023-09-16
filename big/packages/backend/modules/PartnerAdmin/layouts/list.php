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
		document.PartnerAdmin.submit();
	}
</script>
<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">
                Quản lý đối tác <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </h3>
            <div class="box-tools pull-right">
                <?php if(User::can_add(false,ANY_CATEGORY)){?><a class="btn btn-warning" href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <i class="fa fa-plus"></i> [[.New.]] </a><?php }?>
                <?php if(User::can_delete(false,ANY_CATEGORY)){?><a class="btn btn-danger" onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <i class="fa fa-trash-o"></i> [[.delete.]] </a><?php }?>
            </div>
        </div>
        <div class="box-body">
            <form name="PartnerAdmin" method="post" class="form-inline">
                <a name="top_anchor"></a>
                <table class="table">
                    <tr>
                        <td width="100%">
                            <div class="form-group">
                                <label>[[.Filter.]]:</label>
                            </div>
                            <div class="form-group">
                                <input name="search" type="text" id="search"  class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-default" onclick="document.PartnerAdmin.submit();"> <i class="fa fa-search"></i> Tìm</button>
                                <a href="<?=Url::build_current()?>" class="btn btn-default"><i class="fa fa-undo"></i> Bỏ tìm</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr valign="middle">
                        <th width="1%" align="left" ><a>#</a></th>
                        <th width="1%" title="[[.check_all.]]">
                            <input type="checkbox" value="1" id="PartnerAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'PartnerAdmin',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                        <th width="40%" align="left">Tên đối tác</th>
                        <th width="10%" class="text-center">Vị trí (1)</th>
                        <th width="20%" align="left">Ảnh</th>
                        <th width="10%" align="left">Trạng thái</th>
                        <?php if(User::can_edit(false,ANY_CATEGORY))
                        {?>
                            <th width="2%" align="left" >[[.edit.]]</th>
                        <?php }?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=0; $total = [[=total=]];?>
                    <!--LIST:items-->
                    <tr id="PartnerAdmin_tr_[[|items.id|]]">
                        <th width="1%" align="left" ><a><?php echo ++$i;?></a></th>
                        <td width="1%">
                            <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'PartnerAdmin',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="PartnerAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>>
                        </td>
                        <td align="left" >[[|items.name|]]</td>
                        <td align="center" >[[|items.position|]]</td>
                        <td  align="left" ><img style="width:100px;" src="[[|items.small_thumb_url|]]" alt=""></td>
                        <td>[[|items.status|]]</td>

                        <?php if(User::can_edit(false,ANY_CATEGORY))
                        {?>
                            <td align="left"  width="2%"><a class="btn btn-default btn-sm" href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit'));?>">Sửa</a></td>
                        <?php }?>
                    </tr>
                    <!--/LIST:items-->
                    </tbody>
                </table>
                <table class="table">
                    <tr>
                        <td width="48%" align="left">
                            [[.select.]]:&nbsp;
                            <a class="label label-default" href="javascript:void(0)" onclick="select_all_checkbox(document.PartnerAdmin,'PartnerAdmin',true,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_all.]]</a>&nbsp;
                            <a class="label label-default" href="javascript:void(0)" onclick="select_all_checkbox(document.PartnerAdmin,'PartnerAdmin',false,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_none.]]</a>
                            <a class="label label-default" href="javascript:void(0)" onclick="select_all_checkbox(document.PartnerAdmin,'PartnerAdmin',-1,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_invert.]]</a>		</td>
                        <td width="18%">&nbsp;[[.display.]]
                            <select name="item_per_page" class="select" style="width:50px" size="1" onchange="document.PartnerAdmin.submit( );" id="item_per_page" ></select>&nbsp;[[.of.]]&nbsp;[[|total|]]</td>
                        <td width="31%" class="text-right">[[|paging|]]</td>
                    </tr>
                </table>
                <div class="alert alert-warning-custom">
                    (1) Vị trí càng lớn càng ưu tiên hiển thị.
                </div>
                <input type="hidden" name="cmd" value="" id="cmd"/>
            </form>
        </div>
    </div>
</div>