<?php $title = 'Khai báo kho';System::set_page_title($title);?>
<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3>
            <?php if (is_group_owner()): ?> <p class="text-danger">( Lưu ý: Khi cài đặt email cho các đơn vị kho, tài khoản có quyền in đơn có thể gửi email file in nhiều đơn hàng tới email của kho. )</p><?php endif; ?>
            <div class="box-tools pull-right">
                <form method="post" name="SearchQlbhWarehouseForm">
                    <?php
                    if(URL::get('cmd')=='delete'){?>
                        <a onclick="$('cmd').cmd='delete';ListQlbhWarehouseForm.submit();"  class="button-medium-delete">[[.Delete.]]</a>
                        <a href="<?php echo URL::build_current();?>"  class="button-medium-back">[[.back.]]</a>
                        <?php
                    }else{
                        if(Session::get('admin_group')){?>
                            <a href="<?php echo URL::build_current(array('cmd'=>'add'));?>" class="btn btn-primary btn-sm">Thêm mới</a>
                        <?php }?>
                        <?php if(Session::get('admin_group')){?>
                            <a href="javascript:void(0)" onclick="if(!confirm('Bạn có chắc chắn không')){return false};ListQlbhWarehouseForm.cmd.value='delete';ListQlbhWarehouseForm.submit();"  class="btn btn-danger btn-sm">Xoá</a>
                        <?php }
                    }?>
                </form>
            </div>
        </div>
        <div class="box-body">
            <div class="panel">
                <div class="panel-body">
                    <form name="ListQlbhWarehouseForm" method="post">
                        <!--IF:cond(URL::get('selected_ids'))--><div class="notice"><br />[[.selected_list_to_delete.]]</div><br /><!--/IF:cond-->
                        <table class="table table-bordered">
                            <thead>
                            <tr class="table-header">
                                <th width="1%" title="[[.check_all.]]">
                                    <input type="checkbox" value="1" id="QlbhWarehouse_all_checkbox" onclick="select_all_checkbox(this.form,'QlbhWarehouse',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
                                <th nowrap align="left">
                                    <a title="[[.sort.]]" href="<?php echo URL::build_current(((URL::get('order_by')=='qlbh_warehouse.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'qlbh_warehouse.name'));?>" >
                                        <?php if(URL::get('order_by')=='qlbh_warehouse.name') echo '<img alt="" src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>Danh sách kho</a></th>
                                <?php if (is_group_owner()): ?>
                                    <th>Email gửi file in đơn</th>
                                <?php endif; ?>
                                <?php if(Session::get('admin_group')) {?>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                <?php }?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=0;?>
                            <!--LIST:items-->
                            <?php $onclick = 'location=\''.URL::build_current().'&cmd=edit&id='.urlencode([[=items.id=]]).'\';"';?>
                            <tr bgcolor="<?php if(true){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#FFFFFF');} else {echo Portal::get_setting('crud_item_bgcolor','#FFFFFF');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#F7F7F7'));?> style="cursor:hand;" id="QlbhWarehouse_tr_[[|items.id|]]">
                                <td><!--IF:cond([[=items.structure_id=]]!=ID_ROOT)--><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'QlbhWarehouse',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="QlbhWarehouse_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>><!--/IF:cond--></td />
                                <td nowrap align="left" onclick="window.location='<?php echo Url::build_current().'&cmd=edit&id='.[[=items.id=]];?>'">
                                    [[|items.indent|]]
                                    [[|items.indent_image|]]
                                    <span class="page_indent">&nbsp;</span>
                                    [[|items.name|]]
                                    <?= ([[=items.is_default=]])?' <span class="text-bold">(Kho bán hàng)</span>':'';?>
                                </td>
                                <?php if (is_group_owner()): ?>
                                    <td onclick="window.location='<?php echo Url::build_current().'&cmd=edit&id='.[[=items.id=]];?>'">[[|items.email|]]</td>
                                <?php endif; ?>
                                <td width="24px" align="center">[[|items.move_up|]]</td>
                                <td width="24px" align="center">[[|items.move_down|]]</td>
                            </tr>
                            <!--/LIST:items-->
                            </tbody>
                        </table>
                        <input type="hidden" name="cmd" value="" id="cmd"/>
                        <!--IF:delete(URL::get('cmd')=='delete')-->
                        <input type="hidden" name="confirm" value="1" />
                        <!--/IF:delete-->
                        <div class="alert alert-warning-custom">
                            * Chú ý: nếu không chọn kho nào là kho chính (bán hàng) thì <strong>Kho tổng</strong> sẽ là kho chính (bán hàng).
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>