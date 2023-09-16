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
    <link rel="stylesheet" href="assets/lib/DataTables/datatables.min.css"  type="text/css" />
    <script type="text/javascript" src="assets/lib/DataTables/datatables.min.js"></script>
	<fieldset id="toolbar" style="background-color: #fff;">
		<form class="form-inline" style="padding:0;" id="fixed_filter" action="/<?php echo Url::build_current(array('cmd'=>'search'));?>">
			<input name="page" type="hidden" value="thuchi" />
			<input name="page_no" type="hidden" />
			<input name="type" type="hidden" />
			<input name="cmd" type="hidden" value="search" />
			<input name="view_type" type="hidden" value="<?php echo $_REQUEST['view_type']; ?>" />
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
            <!--IF:real_revenue(Url::get('type')=='receive')-->
            <div class="form-group">
                <select name="real_revenue" id="real_revenue" class="form-control"></select>
            </div>
            <!--/IF:real_revenue-->
			<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tìm phiếu</button>
		</form>
		<form name="ThuChiModule" method="post" action="?page=thuchi">
			<div class="list-item" style="padding:15px; ">
				<div class="row">
					<div class="col-lg-12">
						<table class="table table-hover table-striped table-fixed" id="billListTable" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;" border="1" bordercolor="#E7E7E7" align="center">
                            <thead>
                                <tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
                                    <th width="2    0px" align="left"><a>#STT</a></th>
                                    <th width="30px" title="[[.check_all.]]">
                                        <input type="checkbox" value="1" id="ThuChiModule_all_checkbox" onclick="select_all_checkbox(this.form,'ThuChiModule',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>>
                                    </th>
                                    <th width="200px" align="left">Mã Phiếu</th>
                                    <th width="100px" align="center" class="text-center">Tổng cộng</th>
                                    <th width="100px" align="left">Thời Gian</th>
                                    <th width="100px" align="left">Người Tạo</th>
                                    <th width="100px" align="left">Người Nộp</th>
                                    <th width="110px" align="left" nowrap="">Người Nhận</th>
                                    <th width="50px" align="left">Người Xóa</th>
                                    <th width="50px" align="left">Ngày Xóa</th>
                                </tr>
                            </thead>

							<tbody>
								<?php $i=1; ?>
								<!--LIST:items-->
								<tr data_id='[[|items.id|]]'>
									<th width="50px" align="left">[[|items.index|]]</th>
									<td width="30px"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ThuChiModule',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" class="ThuChiModule_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
									<td width="200px" align="left">
                                            <strong>[[|items.bill_number|]]</strong><br>
                                        <span class="small">[[|items.note|]]</span>
                                        <!--IF:cond([[=items.group_name=]])-->
                                        <br><span class="small text-info">[[|items.group_name|]]</span>
                                        <!--/IF:cond-->
                                    </td>
                                    <td width="100px" align="right"><strong class="text-danger"><?php echo System::display_number([[=items.total_amount=]])?></strong></td>
                                    <td width="100px" align="left"><?php echo Date_Time::to_common_date([[=items.bill_date=]])?></td>
                                    <td width="100px" align="left">[[|items.created_full_name|]]</td>
                                    <td width="100px" align="left">
                                        [[|items.payment_full_name|]]<br/>
                                        <!--IF:team_payment([[=items.bill_type=]]=='receive')-->
                                            <span class='small'>
                                               - <?php echo $this->map['team_id_list'][[[=items.team_id=]]]; ?>
                                            </span>
                                        <!--/IF:team_payment-->
                                    </td>
                                    <td width="110px" align="left">
                                        [[|items.received_full_name|]] <br/>
                                        <!--IF:team_received([[=items.bill_type=]]=='pay')-->
                                            <span class='small'>
                                               - <?php echo $this->map['team_id_list'][[[=items.team_id=]]]; ?>
                                            </span>
                                        <!--/IF:team_received-->
                                    </td>
                                    <td width="50px" align="left">
                                        [[|items.deleted_account_id|]]
                                    </td>
                                    <td width="50px" align="left">
                                        <?php echo date("Y/m/d H:i'", [[=items.del=]]); ?>
                                    </td>
							    </tr>
							<?php $i++; ?>
							<!--/LIST:items-->
							<tr>
								<td align="right">Tổng</td>
								<td align="right"></td>
								<td align="right"></td>
                                <td align="right"><strong class="text-danger"><?php echo System::display_number([[=total_amount=]])?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
							</tr>
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
<div style="#height:8px">

</div>
</fieldset>
<script>
	jQuery(document).ready(function(){
	    jQuery('#from_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
	    jQuery('#to_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
    });

    jQuery(document).ready( function () {
        jQuery('#billListTable').DataTable({
            fixedHeader: true,
            paging: false,
            scrollY: 600,
            "searching": false,
            "ordering": false,
            "info":     false
        });
    } );
</script>