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
		document.CrmCustomerSchedule.submit();
	}
</script>
<?php System::set_page_title([[=title=]]); ?>
<div class="container full" style="min-height: 600px;"><br>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="title"><i class="fa fa-calendar text-danger"></i> [[|title|]]</h3>
            <div class="pull-right">
                <?php if(URL::get('cid')) { ?>
                    <a class="btn btn-success"
                       href="<?php echo Url::build_current(array('cmd'=>'add', 'cid'=> URL::get('cid')));?>"
                       role="button"> <i class="glyphicon glyphicon-plus"></i> Thêm
                    </a>
                <?php  } ?>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-11">
                    <form class="form-inline"  action="/<?php echo Url::build_current(array('cmd'=>'search'));?>">
                        <input name="page" type="hidden" value="lich-hen" />
                        <input name="page_no" type="hidden" />
                        <input name="branch_id" type="hidden" />
                        <input name="status_id" type="hidden" />
                        <input name="page_no" type="hidden" value='1' />
                        <input name="cmd" type="hidden" value="search" />
                        <div class="form-group">
                            <input name="customer_text" type="text" id="customer_text" class="form-control" placeholder="Nhập tên KH, SĐT, ID ...">
                        </div>
                        <div class="form-group col-md-3" style='padding: 0;'>
                            <div class='input-group'><input name="from_date" type="text" id="from_date" style='width: 130px;display: inline-block;'
                                   autocomplete="off" class="form-control" placeholder="Từ ngày">
                                   <input name="to_date" type="text" id="to_date" style='width: 130px;display: inline-block;'
                                   autocomplete="off" class="form-control" placeholder="Đến ngày"></div>
                        </div>
                        <div class="form-group col-md-2 hidden">
                            <select name="branch_id" id="branch_id" class="form-control" style='max-width: 100%;'></select>
                        </div>
                        <div class="form-group">
                            <select name="status_id" id="status_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="schedule_type" id="schedule_type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control"></select>
                        </div>
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm</button>
                    </form>
                    <hr>
                    <form name="CrmCustomerSchedule" method="post" action="<?=Url::build('lich-hen')?>">
                        <div class="list-item">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-hover table-striped table-bordered" width="100%">
                                        <thead>
                                            <tr valign="middle">
                                                <th width="1%" align="left"><a>STT</a></th>
                                                <th align="center" class="text-center">Tên KH</th>
                                                <th align="left" class="text-center">SĐT KH</th>
                                                <th align="center" class="text-center">TG khách hẹn</th>
                                                <th align="center" class="text-center">TG khách đến</th>
                                                <th align="left">Trạng Thái</th>
                                                <th align="center" class="text-center">NV phụ trách</th>
                                                <th align="center" class="text-center">Nội dung</th>
                                                <th align="center" class="text-center">Người Tạo</th>
                                                <th align="left">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1+ ( page_no()-1) * $this->map['item_per_page']; ?>
                                        <!--LIST:items-->
                                        <tr id="schedule-id-[[|items.id|]]">
                                            <th width="1%" align="left">
                                                <a><?php echo  $i;?></a>
                                            </th>
                                            <td align="center"> <a href="<?php echo Url::build('customer',[ 'cid' => ([[=items.customer_id=]]), 'do' => 'view' ] )?>#lichhen"> <strong class="text-bold"> [[|items.customer_name|]] </strong> </a> </td>
                                            <td align="left"> [[|items.customer_mobile|]] </td>
                                            <td align="center">[[|items.appointed_time_display|]]</td>
                                            <td align="center">
                                            <!--IF:late_schedule(empty([[=items.arrival_time=]]) && [[=items.appointed_time=]] < time())-->
                                                <span class="text-red text-bold">Trễ hẹn</span>
                                            <!--ELSE-->
                                                [[|items.arrival_time_display|]]
                                            <!--/IF:late_schedule-->
                                            </td>
                                            <td align="center"> <?php echo $this->map['status'][[[=items.status_id=]]] ?> </td>
                                            <td align="center">[[|items.staff_name|]]</td>
                                            <td align="left">
                                                <span class="small">[[|items.note|]]</span>
                                            </td>
                                            <td align="center">
                                                [[|items.created_user_name|]]
                                                <span class="small">
                                                <br>[[|items.created_time|]]
                                                </span>
                                            </td>
                                            <td align="left" nowrap="">
                                                <?php if ( check_user_privilege('CUSTOMER') || Session::get('admin_group') || is_group_owner() || [[=items.created_user_id=]] == get_user_id()): ?>
                                                        <a class="btn btn-sm btn-default" href="<?php echo Url::build_current(array('cid'=>([[=items.customer_id=]]),'sid'=>md5([[=items.id=]] . CATBE),
                                                'cmd'=>'edit'));?>">
                                                    <i class="fa fa-pencil-square"></i>
                                                </a>
                                                <a class="btn btn-sm btn-default" onclick="if(!confirm('Xóa sẽ không khôi phục được, bạn có chắc chắn muốn xóa không?')){return false;}" href="<?php echo Url::build_current(array('cid','sid'=>md5([[=items.id=]] . CATBE),
                                                'cmd'=>'delete'));?>" title="Xóa lịch hẹn">
                                                    <i class="fa fa-minus-circle"></i>
                                                </a>
                                                <?php endif; ?>
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
</div>
<script>
	jQuery(document).ready(function(){
        jQuery('#from_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        jQuery('#to_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
	});
</script>
