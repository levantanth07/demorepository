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
		document.CrmCustomerNote.submit();
	}
</script>
<?php System::set_page_title([[=title=]]); ?>
<fieldset id="toolbar">
    <div class="col-md-8">
        <h3 class="title"><i class="fa fa-file-text text-orange"></i> [[|title|]]</h3>
    </div>
    <div class="col-md-4">
        <div class="pull-right">
            <?php if(URL::get('cid')) { ?>
                <a class="btn btn-primary"
                   href="<?php echo Url::build_current(array('cmd'=>'add', 'cid'=> URL::get('cid')));?>"
                   role="button"> <i class="glyphicon glyphicon-plus"></i> Thêm
                </a>
            <?php  } ?>
        </div>
    </div>

</fieldset>
<div class="panel-default panel" style="background-color: #fff">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-11">
                <form class="form-inline" style="padding: 15px 0 0 0;" action="/<?php echo Url::build_current(array('cmd'=>'search'));?>">
                    <input name="page" type="hidden" value="ghi-chu-khach-hang" />
                    <input name="page_no" type="hidden" />
                    <input name="cmd" type="hidden" value="search" />
                    <div class="form-group col-md-3">
                        <input name="customer_text" type="text" id="customer_text" class="form-control" placeholder="Nhập tên KH, SĐT, ID ...">
                    </div>
                    <div class="form-group">
                        <input name="from_date" type="text" id="from_date"
                               autocomplete="off" class="form-control" placeholder="Từ ngày">
                    </div>
                    <div class="form-group">
                        <input name="to_date" type="text" id="to_date"
                               autocomplete="off" class="form-control" placeholder="Đến ngày">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tìm</button>
                </form>
                <form name="CrmCustomerNote" method="post" action="index062019.php?page=thuchi">
                    <div class="list-item" style="padding:15px; ">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-hover table-striped" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
                                    <thead>
                                    <tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
                                        <th width="1%" align="left"><a>#STT</a></th>
                                        <th width="1%" title="[[.check_all.]]">
                                            <input type="checkbox" value="1" id="CrmCustomerNote_all_checkbox" onclick="select_all_checkbox(this.form,'CrmCustomerNote',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>>
                                        </th>
                                        <th width="2%" align="left">ID</th>
                                        <th width="5%" align="center" class="text-center">Thời gian</th>
                                        <th width="10%" align="center" class="text-center">Tên KH</th>
                                        <th width="10%" align="left" class="text-center">SĐT KH</th>
                                        <th width="10%" align="center" class="text-center">Cảm Xúc KH</th>
                                        <th width="10%" align="center" class="text-center">Nội Dung</th>
                                        <th width="10%" align="center" class="text-center">Người Tạo</th>
                                        <th width="2%" align="left"><a>Sửa</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1; ?>
                                    <!--LIST:items-->
                                    <tr id="customer-note-[[|items.id|]]">
                                        <th width="1%" align="left"><a><?php echo  $i; ?></a></th>
                                        <td width="1%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'CrmCustomerNote',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" class="CrmCustomerNote_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
                                        <td align="left" width="2%"> [[|items.id|]] </td>
                                        <td align="center" width="5%">[[|items.created_time|]]</td>
                                        <td width="10%" align="center">
                                            <a href="<?php echo Url::build('customer',[
                                                'cid' => [[=items.customer_id=]],
                                                'do' => 'view'
                                            ]
                                        )?>">
                                                <strong class="text-danger">
                                                    [[|items.customer_name|]]
                                                </strong>
                                            </a>
                                        </td>
                                        <td width="10%" align="left"> [[|items.customer_mobile|]] </td>
                                        <td width="10%" align="center">[[|items.emotion|]]</td>
                                        <td width="10%" align="left"> <span class="small">[[|items.content|]]</span> </td>
                                        <td width="10%" align="center">[[|items.created_user_name|]]</td>
                                        <td align="left" width="2%">
                                            <?php if (Session::get('admin_group') || CrmCustomerNoteDB::can_edit([[=items.id=]])) { ?>
                                            <a href="<?php echo Url::build_current(array('cid'=>[[=items.customer_id=]],'nid'=>md5([[=items.id=]] . CATBE),
                                            'cmd'=>'edit'));?>">
                                                <img src="assets/default/images/buttons/button-edit.png">
                                            </a>
                                            <?php } ?>
                                        </td>
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
            </div>
            <div class="col-md-1">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a class="btn-danger btn btn-lg" href="<?php echo URL::build('lich-hen', ['cid']);?>" title="Đặt lịch hẹn">
                            <i class="fa fa-calendar"></i></a>
                    </li>
                    <li class="list-group-item">
                        <a class="btn-warning btn btn-lg" href="<?php echo URL::build('ghi-chu-khach-hang', ['cid']);?>" title="Ghi chú">
                            <i class="fa fa-file-text"></i></a>
                    </li>
                    <li class="list-group-item">
                        <a class="btn-success btn btn-lg" href="<?php echo URL::build('lich-su-cuoc-goi', ['cid']);?>" title="Lịch sử cuộc gọi">
                            <i class="fa fa-phone-square"></i></a>
                    </li>
                    <li class="list-group-item">
                        <a class="btn-info btn btn-lg" href="<?php echo URL::build('benh-ly', ['cid']);?>" title="Bệnh lý">
                            <i class="fa fa-flask"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
	jQuery(document).ready(function(){
	jQuery('#from_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
	jQuery('#to_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
	});
</script>
