<script>
	function check_selected()
	{
		var status = false;
		$('form :checkbox').each(function(e){
			if(this.checked)
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd)
	{
		$('#cmd').val(cmd);
		document.ThuChiModule.submit();
	}
</script>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thu chi</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box">
        <fieldset id="toolbar">
            <div id="toolbar-title">
                [[|title|]] [[|turn_card_code|]]
            </div>
            <div id="toolbar-content" align="right" style="margin-right: 11px; margin-top: 10px;">
                <table align="right">
                    <tbody>
                    <tr>
                        <?php if( $this->map['accounting_privilege'] ) { ?>
                            <td id="toolbar-new-receive"  align="center">
                                <a class="btn btn-info"
                                   href="<?php echo Url::build_current(array('cmd'=>'add', 'type'=>'receive','cid'));?>"
                                   role="button"> <i class="glyphicon glyphicon-plus"></i>Thêm Phiếu Thu
                                </a>
                                <a class="btn btn-warning"
                                   href="<?php echo Url::build_current(array('cmd'=>'add', 'type'=>'pay','cid'));?>"
                                   role="button"><i class="glyphicon glyphicon-plus"></i> Thêm Phiếu Chi
                                </a>
                                <a class="btn btn-default"
                                   href="<?php echo Url::build_current(array('view_type'=>'recycle_bin','cid'));?>"
                                   role="button"><i class="glyphicon glyphicon-trash"></i> Thùng rác
                                </a>
                                <?php if( $this->map['deletable']){?>
                                    <a role="button" class="btn-danger btn"
                                       onclick="if(confirm ('<?php echo 'Bạn có chắc chắn muốn xóa?';?>')) { if(check_selected())
                                               {make_cmd('delete')} else {alert('<?php echo 'Bạn có chắc chắn muốn xóa?';?>'); }}">
                                        <span class="glyphicon glyphicon-remove"></span> Xóa
                                    </a>
                                <?php }?>
                            </td>
                        <?php } ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
        <fieldset id="toolbar" style="background-color: #fff;">
            <form class="form-inline" style="padding:0;" id="fixed_filter" action="/<?php echo Url::build_current(array('cmd'=>'search'));?>">
                <input name="page" type="hidden" value="thu-chi" />
                <input name="page_no" type="hidden" />
                <input name="type" type="hidden" />
                <input name="cid" type="hidden" />
                <input name="cmd" type="hidden" value="search" />
                <input name="view_type" type="hidden" id="view_type">
                <!--IF:cond(Session::get('account_type')==3 and check_user_privilege('ADMIN_KETOAN'))-->
                <div class="form-group col-md-2">
                    <select name="group_id" id="group_id" class="form-control" style='max-width: 100%'></select>
                </div>
                <!--/IF:cond-->
                <div class="form-group col-md-2">
                    <input name="search_bill_text" type="text" id="search_bill_text" class="form-control" placeholder="Nhập mã phiếu, tên tài khoản, người nhận ...">
                </div>
                <div class="form-group">
                    <input name="from_bill_date" type="text" id="from_bill_date"
                           autocomplete="off" class="form-control" placeholder="Từ ngày">
                </div>
                <div class="form-group">
                    <input name="to_bill_date" type="text" id="to_bill_date"
                           autocomplete="off" class="form-control" placeholder="Đến ngày">
                </div>
                <div class="form-group">
                    <select name="team_id" id="team_id" class="form-control"></select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tìm phiếu</button>
            </form>
            <form name="ThuChiModule" method="post" action="?page=thu-chi">
                <input name="type" type="hidden" />
                <div class="list-item" style="padding:15px; ">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-hover table-striped table-fixed" id="billListTable" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;" border="1" bordercolor="#E7E7E7" align="center">
                                <thead>
                                <tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
                                    <th align="left"><a>#STT</a></th>
                                    <th title="[[.check_all.]]">
                                        <input type="checkbox" value="1" id="ThuChiModule_all_checkbox" onclick="select_all_checkbox(this.form,'ThuChiModule',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>>
                                    </th>
                                    <th align="left">Mã Phiếu</th>
                                    <th align="left">Tiền mặt</th>
                                    <th align="left">Thanh toán thẻ</th>
                                    <th align="left">Thanh toán CK</th>
                                    <th align="center" class="text-center">Tổng cộng</th>
                                    <th align="left"><i class='fa fa-clock-o'></i> Thời Gian</th>
                                    <th align="left">Người Tạo</th>
                                    <th align="left">Người Nộp</th>
                                    <th align="left" nowrap="">Người Nhận</th>
                                    <th align="left">Sửa</th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php $i=1; ?>
                                <!--LIST:items-->
                                <tr data_id="[[|items.id|]]">
                                    <th align="left">[[|items.index|]]</th>
                                    <td ><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ThuChiModule',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" class="ThuChiModule_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
                                    <td align="left">
                                        <strong>[[|items.bill_number|]]</strong>

                                        <!--IF:cond([[=items.turn_card_id=]])-->
                                        <br><span class="small text-info">card#[[|items.turn_card_id|]]</span>
                                        <!--/IF:cond-->
                                        <!--IF:cond([[=items.order_id=]])-->
                                        <br><span class="small text-info">order#[[|items.order_id|]]</span>
                                        <!--/IF:cond-->

                                        <br><span class="small">[[|items.note|]]</span>
                                        <!--IF:cond([[=items.group_name=]])-->
                                        <br><span class="small text-info">[[|items.group_name|]]</span>
                                        <!--/IF:cond-->
                                    </td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=items.cash_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=items.card_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=items.bank_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=items.total_amount=]])?></strong></td>
                                    <td align="left">
                                        <strong><?php echo Date_Time::to_common_date([[=items.bill_date=]])?></strong>
                                        <small class='small' style='color:#c7c3c3'>Tạo lúc:<br/> <?=date("d/m/Y H:i'", [[=items.created_time=]]);?></small>
                                    </td>
                                    <td align="left">[[|items.created_full_name|]]</td>
                                    <td align="left">
                                        [[|items.payment_full_name|]]<br/>
                                        <!--IF:team_payment([[=items.bill_type=]]=='receive')-->
                                        <span class='small'>
                                               - <?php echo $this->map['team_id_list'][[[=items.team_id=]]]; ?>
                                            </span>
                                        <!--/IF:team_payment-->
                                    </td>
                                    <td align="left">
                                        [[|items.received_full_name|]] <br/>
                                        <!--IF:team_received([[=items.bill_type=]]=='pay')-->
                                        <span class='small'>
                                               - <?php echo $this->map['team_id_list'][[[=items.team_id=]]]; ?>
                                            </span>
                                        <!--/IF:team_received-->
                                    </td>
                                    <td align="left">
                                        <?php if( $this->map['editable'] ) { ?>
                                        <a class="btn btn-warning" href="<?php echo Url::build_current(array('cid','type'=>[[=items.bill_type=]],
                                        'id'=>User::encode_password([[=items.id=]]),
                                        'cmd'=>'edit','page_no','search_category_id'));?>">
                                            Sửa
                                        </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                                <!--/LIST:items-->
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td align="right">Tổng</td>
                                    <td align="right"></td>
                                    <td align="right"></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=total_cash_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=total_card_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=total_bank_amount=]])?></strong></td>
                                    <td align="right"><strong class="text-danger"><?php echo System::display_number([[=total_amount=]])?></strong></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr class="hidden">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Tổng Thu: <strong class="text-blue">[[|receive|]]</strong></td>
                                    <td>Tổng Chi: <strong class="text-blue">[[|pay|]]</strong></td>
                                    <td>Tồn Quỹ: <strong class="text-danger">[[|left_amount|]]</strong></td>
                                </tr>
                                </tfoot>
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
            <div style="#height:8px">

            </div>
        </fieldset>
    </div>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery('#from_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        jQuery('#to_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
    });
</script>
<link rel="stylesheet" href="assets/lib/DataTables/datatables.min.css"  type="text/css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.9/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script>
    jQuery(document).ready( function () {
        var buttons = [];
        /*if (account_type!=0) {
            buttons = ['excel'];
        }*/
        jQuery('#billListTable').DataTable({
            fixedHeader: true,
            paging: false,
            scrollY: 600,
            "searching": false,
            "ordering": false,
            "info":     false,
            dom: 'Bfrtip',
            buttons: buttons
        });
    } );
</script>