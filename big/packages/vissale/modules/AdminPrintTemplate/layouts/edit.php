<style>
	.img-print-template{
	}
	.img-print-template img{
		max-width: 100%;
	}
</style>
<fieldset id="toolbar">
    <div class="row">
        <div class="col-xs-8">
            <h3 class="title"><i class="fa fa-print"></i> Quản lý mẫu in đơn hàng <strong>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit'){echo 'Sửa';}?> ]</span></h3>
        </div>
        <div class="col-xs-4 text-right">
            <?php if (Session::get('admin_group')) { ?>
                <a class="btn btn-primary" onclick="EditAdminPrintTemplate.submit();"> <span title="Edit"> </span> Ghi lại </a>
            <?php } ?>
            <?php if (Session::get('admin_group')) { ?>
                <a class="btn btn-default" href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="New"> </span> Quay lại </a>
            <?php } ?>
        </div>
    </div>
</fieldset>
<br clear="all"/>
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditAdminPrintTemplate" id="EditAdminPrintTemplate" method="post" enctype="multipart/form-data">
		<div class="content">
			<div class="row">
				<div class="col-lg-3"></div>
				<div class="col-lg-6">
					<div class="form-controll">
						<div class="form-group">
						    <div class="bor img-print-template">
						    	<div class="form-group">
						    		<label>Loại mẫu in</label>
						    		<select name="template" id="template" class="form-control"></select>
						    	</div>
						    	<div class="form-group">
							    	<label for="exampleInputEmail1">Tên mẫu in</label>
							    	<input name="print_name" type="text" id="print_name" class="form-control" style="font-size:18px;"/>
							    </div>
							    <div class="form-group">
							    	<label for="exampleInputEmail1">Địa chỉ</label>
							    	<input name="print_address" type="text" id="print_address" class="form-control"/>
							    </div>
							    <div class="form-group">
							    	<label for="exampleInputEmail1">Điện thoại</label>
							    	<input name="print_phone" type="text" id="print_phone" class="form-control"/>
							    </div>
						    </div>
						</div>
						<div class="form-group">
						    <label for="exampleInputEmail1">Ưu tiên</label>
						    <input  name="set_default" type="checkbox" id="set_default" value="1"> 
						    <script>
						    	jQuery(document).ready(function(){
						    		<?php if(Url::get('set_default')){?>
						    			jQuery('#set_default').attr('checked',true);
						    		<?php }?>
						    	});
						    </script>
						</div>
					</div>
				</div>
				<div class="col-lg-3"></div>
			</div>
		</div>
	</form>
</fieldset>