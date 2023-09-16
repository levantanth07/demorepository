<?php
    $type = Url::get('type');
?>
<div class="container">
    <br>
    <div class="box box-info">
        <form name="ListQlbhStockInvoiceForm" method="post" class="form-inline">
            <div class="box-header with-border">
                <h3 class="box-title"> [[|title|]]</h3>
                <div class="box-tools pull-right">
                    <?php if(Session::get('group_id')){?>
                        <input type="button" value="Thêm phiếu" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'add','type'));?>'" class="btn btn-primary">
                    <?php }?>
                    <?php if(Session::get('group_id')){?>
                        <input type="button" value="Xuất nội bộ" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'add','type'=>'EXPORT','move_product'=>1));?>'" class="btn btn-success">
                    <?php }?>
                    <?php if(Session::get('group_id')){?>
                        <input type="button" value="Xoá" id="delete_button" class="btn btn-danger">
                    <?php }?>
                    <button type="button" onclick="exportExcel('<?php echo Url::get('type')?>');" class="btn btn-success"><i class="fa fa-download"></i> Xuất excel</button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <input name="bill_number" type="text" id="bill_number" style="width: 150px" class="form-control" placeholder="Số phiếu">
                        </div>
                        <div class="form-group">
                            <input name="note" type="text" id="note" class="form-control" style="width: 150px" placeholder="Diễn giải">
                        </div>
                        <div class="form-group">
                            <input name="create_date_from" type="text" id="create_date_from" class="form-control" style="width:150px" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="create_date_to" type="text" id="create_date_to" class="form-control" style="width:150px" placeholder="Đến ngày">
                        </div>
                        <div class="form-group">
                            <select name="supplier_id" id="supplier_id" class="form-control" style="width: 150px"></select>
                        </div>
                        <?php if(Url::get('type')=='EXPORT') : ?>
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control" style="width: 150px">
                                [[|user_id_list|]]
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <select name="warehouse_id" id="warehouse_id" class="form-control" style="width: 150px"></select>
                        </div>
                        <div class="form-group">
                            <input  name="search" type="submit" id="search" class="btn btn-primary btn-sm" value="Tìm kiếm">
                        </div>
                    </div>
                </div>
                <hr>
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th width="1%"><input type="checkbox" id="all_item_check_box"></th>
                            <th width="1%">#</th>
                            <th>Ngày tạo</th>
                            <th width="10%" align="left">Số phiếu</th>
                            <th width="10%" align="left">Người xuất</th>
                            <th width="10%" align="left">Người nhận</th>
                            <th width="20%" align="left">Diễn giải</th>
                            <!--IF:cond(Url::get('type')=='IMPORT')--><th width="20%" align="left">Nhà cung cấp</th><!--/IF:cond-->
                            <?php if($type == 'EXPORT'): ?>
                            <th width="30%" align="center">Tổng tiền</th>
                            <?php endif; ?>
                            <th width="1%">&nbsp;</th>
                            <th width="1%">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $total = 0; $i = 0;?>
                    <!--LIST:items-->
                    <tr <?php echo ([[=items.id=]]==Url::iget('just_edited_id'))?' bgcolor="#FFFF99"':'';?>>
                        <td><input name="item_check_box[]" type="checkbox" class="item-check-box" value="[[|items.id|]]"></td>
                        <td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.i|]]</td>
                        <td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.create_date|]]</td>
                        <td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.bill_number|]]</td>
                        <td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.deliver_name|]]</td>
                        <td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.receiver_name|]]</td>
                        <td><div style="float:left;width:300px;overflow:auto;max-height: 60px">[[|items.note|]]</div></td>
                        <!--IF:cond(Url::get('type')=='IMPORT')--><td style="cursor:pointer;" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>'">[[|items.supplier_name|]]</td><!--/IF:cond-->
                        <?php if($type == 'EXPORT'): ?>
                        <td align="right"><?php $total += [[=items.total_amount=]];echo System::display_number([[=items.total_amount=]]);?></td>
                        <?php endif; ?>
                        <td><a class="btn btn-default btn-sm" href="<?php echo Url::build_current(array('cmd'=>'view','id'=>[[=items.id=]],'type'));?>" title="[[.view_bill.]]">Xem</a></td>
                        <td><a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('cmd'=>'edit','type','id'=>[[=items.id=]]));?>">Sửa</a></td>
                    </tr>
                    <?php $i++; ?>
                    <!--/LIST:items-->
                    <?php if($type == 'EXPORT'): ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td colspan="<?=(Url::get('type')=='IMPORT')?7:6?>" align="right" style="color:#F00;font-weight:bold;">Tổng tiền</td>
                        <td align="right" style="color:#F00;font-weight:bold;"><?php echo System::display_number($total);?> / <?php echo $i; ?> phiếu</td>
                        <td>&nbsp;</td>
                        <td nowrap="nowrap">&nbsp;</td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <hr>
                <div class="paging">[[|paging|]]</div>
            </div>
            <input name="cmd" type="hidden" value="">
        </div>
    </form>
</div>
<script type="text/javascript">
    function exportExcel(type){
        var query = {
            bill_number: $('#bill_number').val()?$('#bill_number').val():'',
            note: $('#note').val()?$('#note').val():'',
            user_id: $('#user_id').val()?$('#user_id').val():'',
            create_date_from: $('#create_date_from').val()?$('#create_date_from').val():'',
            create_date_to: $('#create_date_to').val()?$('#create_date_to').val():'',
            supplier_id: $('#supplier_id').val()?$('#supplier_id').val():'',
            warehouse_id: $('#warehouse_id').val()?$('#warehouse_id').val():'',
            type : type
        }
        var excel_href = '<?php echo Url::build_current(array('cmd'=>'export_excel_import'));?>&'+$.param(query);
        window.location=excel_href;
    }
	jQuery(document).ready(function(){
		$.fn.datepicker.defaults.format = "dd/mm/yyyy";
		jQuery('#create_date_from').datepicker();
		jQuery('#create_date_to').datepicker();
		jQuery("#delete_button").click(function (){
			ListQlbhStockInvoiceForm.cmd.value = 'delete';
			ListQlbhStockInvoiceForm.submit();
		});
		jQuery(".delete-one-item").click(function (){
			if(!confirm('[[.are_you_sure.]]')){
				return false;
			}
		});
		jQuery("#all_item_check_box").click(function (){
			var check  = this.checked;
			jQuery(".item-check-box").each(function(){
				this.checked = check;
			});
		});
	});
</script>