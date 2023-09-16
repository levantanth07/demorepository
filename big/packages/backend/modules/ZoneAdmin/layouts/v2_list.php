<?php
function genInfo($brand, $type, $id, $data)
{
    $html = '';
    $arrBrand = array(
        'viettel' => 'Viettel Post',
        'ghtk' => 'Giao hàng tiết kiệm',
        'ems' => 'EMS',
        'vnpost' => 'Bưu điện Việt Nam',
        'ghn' => 'Giao hàng nhanh',
        'best' => 'Best Inc',
        'jt' => 'J&T'
    );
    $name = $arrBrand[$brand] ?? $brand;
    if (!empty($data)) {
        $html = '<li class="edit-brand-info box-info-'.$brand.'" id="'.$type.'-'.$id.'-'.$brand.'" data-info-type="'.$type.'" data-info-brand="'.$brand.'" data-info-id="'.$id.'" onclick="showFormInfo(this)"><b>'.$name.'</b>';
        foreach($data as $key => $value) {
            $html .= '<br>' . $key . ': ' . $value;
        }
        $html .= '</li>';
    }
    return $html;
}
?>
    <style>
        #tbl-zone .nav {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        #tbl-zone .nav li {
            display: inline-block;
            margin: 2px 5px;
            padding: 8px;
            border: 1px solid silver;
            border-radius: 15px;
            cursor: pointer;
        }
        .modal {
            overflow: unset;
            overflow-y: unset;
        }
        .box-info-viettel {
            color: silver;
        }
        .box-info-ghtk {
            color: silver;
        }
        .box-info-ems {
            color: silver;
        }
        .box-info-vnpost {
            color: silver;
        }
        .box-info-ghn {
            color: silver;
        }
        .box-info-best {
            color: silver;
        }
    </style>
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
			document.ListCategoryForm.submit();
		}
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<form method="post" name="SearchCategoryForm" id="SearchCategoryForm">
    <fieldset id="toolbar">
        <div id="toolbar-title">
            <!--IF:cond([[=district_name=]])-->
            Quản lý quận huyện [[|district_name|]] / <a href="<?=Url::build_current(['cmd'=>'v2', 'province_id'=>[[=province_id=]]])?>">[[|province_name|]]</a><span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            <!--ELSE-->
            Quản lý tỉnh thành [[|province_name|]]<span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            <!--/IF:cond-->
        </div>
    </fieldset>
</form>
<br>
<fieldset id="toolbar">
    <div>
        <div class="pull-left" style="margin: 10px">
            <label class="checkbox-inline">
                <input type="checkbox" id="show_viettel">Viettel Post
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_ghtk">Giao hàng tiết kiệm
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_ems">EMS
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_vnpost">Bưu điện Việt Nam
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_ghn">Giao hàng nhanh
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_best">Best Inc
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="show_jt" checked>J&T
            </label>
        </div>
        <div class="pull-right">
            <div class="form-group">
                <!--IF:district_id_cond(!Url::iget('district_id'))-->
                Chọn tỉnh thành
                <select name="province_id" id="province_id" onchange="changeProvince()" class="form-control" style="width: 300px"></select>
                <!--/IF:district_id_cond-->
                <!--IF:cond(Url::iget('district_id'))-->
                <td> <a href="<?=Url::build_current(['district_id','cmd'=>'add_ward_v2'])?>" class="btn btn-success"><span title="new"> </span>Thêm phường xã mới</a></td>
                <!--/IF:cond-->
            </div>
            <!--<a class="btn btn-default" href="https://partner.viettelpost.vn/v2/categories/listProvince/" target="_blank"> <i class="fa fa-search"></i> Check tỉnh / thành của Viettel post</a>
            <a class="btn btn-default" href="https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=1" target="_blank"> <i class="fa fa-search"></i> Check quận / huyện của Viettel post</a>
            -->
        </div>
    </div>
    <!--<form name="ListCategoryForm" method="post" class="form-inline">-->
    <div class="table-responsive">
        <table class="table bordered-table table-striped" id="tbl-zone">
            <thead>
            <tr valign="middle" bgcolor="<?php echo Portal::get_setting('crud_list_item_bgcolor','#F0F0F0');?>" style="line-height:20px">
                <!--<th width="1%" title="Chọn tất cả"><input type="checkbox" value="1" id="Category_all_checkbox" onclick="select_all_checkbox(this.form,'Category',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />-->
                <th>STT</th>
                <th>Tên thường gọi</th>
                <th>Thông tin thêm</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php $i=1;?>
                <!--LIST:items-->
            <?php $level =1;?>
            <tr>
                <!--<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Category',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="Category_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />-->
                <td width="1%" nowrap="nowrap">
                    <?php if($level==1){echo $i++;}?>
                </td>
                <td align="left" nowrap <?php if(User::can_edit(false,ANY_CATEGORY) and Url::sget('page')!='portal_category'){?> <?php }?>>
                    <span class="page_indent">&nbsp;</span>
                    <!--IF:cond(Url::get('province_id'))-->
                    <span id="district_name_[[|items.id|]]">
						[[|items.district_name|]]
					</span>
                    <!--ELSE-->
                    <!--IF:cond_(Url::get('district_id'))-->
                    <span id="ward_name_[[|items.id|]]">
						[[|items.ward_name|]]
					</span>
                    <!--ELSE-->
                    <span id="province_name_[[|items.id|]]">
						[[|items.province_name|]]
					</span>
                    <!--/IF:cond_-->
                    <!--/IF:cond-->
                </td>
                <td align="left" <?php if(User::can_edit(false,ANY_CATEGORY) and Url::sget('page')!='portal_category'){?> <?php }?>>
                    <!--<span class="page_indent">&nbsp;</span>-->
                    <!--<a id="quick_edit_[[|items.id|]]" href="#" title="Quick edit" onClick="enableEdit('[[|items.id|]]');return false"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>-->
                    <!--IF:cond(Url::get('province_id'))-->
                    <span id="viettel_name_[[|items.id|]]">
                        <?php
                        $district_id = [[=items.district_id=]];
                        $district_info = [[=items.district_info=]];
                        $district_info = json_decode($district_info, true);
                        ?>
                        <ul class="nav">
                            <? if (isset($district_info['viettel'])): ?>
                                <?= genInfo('viettel', 'district', $district_id, $district_info['viettel']) ?>
                            <? endif; ?>
                            <? if (isset($province_info['ghtk'])): ?>
                                <?= genInfo('ghtk', 'district', $district_id, $district_info['ghtk']) ?>
                            <? endif; ?>
                            <? if (isset($district_info['ems'])): ?>
                                <?= genInfo('ems', 'district', $district_id, $district_info['ems']) ?>
                            <? endif; ?>
                            <? if (isset($district_info['vnpost'])): ?>
                                <?= genInfo('vnpost', 'district', $district_id, $district_info['vnpost']) ?>
                            <? endif; ?>
                            <? if (isset($district_info['ghn'])): ?>
                                <?= genInfo('ghn', 'district', $district_id, $district_info['ghn']) ?>
                            <? endif; ?>
                            <? if (isset($district_info['best'])): ?>
                                <?= genInfo('best', 'district', $district_id, $district_info['best']) ?>
                            <? endif; ?>
                            <? if (isset($district_info['jt'])): ?>
                                <?= genInfo('jt', 'district', $district_id, $district_info['jt']) ?>
                            <? endif; ?>
                        </ul>
					</span>
                    <!--ELSE-->
                    <!--IF:cond__(Url::get('district_id'))-->
                    <?php
                    $ward_id = [[=items.ward_id=]];
                    $ward_info = [[=items.ward_info=]];
                    $ward_info = json_decode($ward_info, true);
                    ?>
                    <ul class="nav">
                        <? if (isset($ward_info['viettel'])): ?>
                            <?= genInfo('viettel', 'ward', $ward_id, $ward_info['viettel']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['ghtk'])): ?>
                            <?= genInfo('ghtk', 'ward', $ward_id, $ward_info['ghtk']) ?>
                        <? endif; ?>
                        <? if (isset($ward_info['ems'])): ?>
                            <?= genInfo('ems', 'ward', $ward_id, $ward_info['ems']) ?>
                        <? endif; ?>
                        <? if (isset($ward_info['vnpost'])): ?>
                            <?= genInfo('vnpost', 'ward', $ward_id, $ward_info['vnpost']) ?>
                        <? endif; ?>
                        <? if (isset($ward_info['ghn'])): ?>
                            <?= genInfo('ghn', 'ward', $ward_id, $ward_info['ghn']) ?>
                        <? endif; ?>
                        <? if (isset($ward_info['best'])): ?>
                            <?= genInfo('best', 'ward', $ward_id, $ward_info['best']) ?>
                        <? endif; ?>
                        <? if (isset($ward_info['jt'])): ?>
                            <?= genInfo('jt', 'ward', $ward_id, $ward_info['jt']) ?>
                        <? endif; ?>
                    </ul>
                    <!--ELSE-->
                    <?php
                    $province_id = [[=items.province_id=]];
                    $province_info = [[=items.province_info=]];
                    $province_info = json_decode($province_info, true);
                    ?>
                    <ul class="nav">
                        <? if (isset($province_info['viettel'])): ?>
                            <?= genInfo('viettel', 'province', $province_id, $province_info['viettel']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['ghtk'])): ?>
                            <?= genInfo('ghtk', 'province', $province_id, $province_info['ghtk']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['ems'])): ?>
                            <?= genInfo('ems', 'province', $province_id, $province_info['ems']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['vnpost'])): ?>
                            <?= genInfo('vnpost', 'province', $province_id, $province_info['vnpost']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['ghn'])): ?>
                            <?= genInfo('ghn', 'province', $province_id, $province_info['ghn']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['best'])): ?>
                            <?= genInfo('best', 'province', $province_id, $province_info['best']) ?>
                        <? endif; ?>
                        <? if (isset($province_info['jt'])): ?>
                            <?= genInfo('jt', 'province', $province_id, $province_info['jt']) ?>
                        <? endif; ?>
                    </ul>
                    <!--/IF:cond__-->
                    <!--/IF:cond-->
                </td>
                <td>
                <td class="text-right">
                    <!--IF:cond(!Url::get('province_id') and !Url::get('district_id'))-->
                    <a class="btn btn-warning btn-sm" href="<?=Url::build_current(['cmd'=>'v2', 'province_id'=>[[=items.province_id=]]])?>">Sửa quận huyện</a>
                    <!--ELSE-->
                    <!--IF:cond_(Url::get('district_id'))-->
                    <!--<a class="btn btn-warning btn-sm" href="<?=Url::build_current(['cmd'=>'edit_ward','district_id','id'=>[[=items.id=]]])?>">Sửa</a>-->
                    <!--<a onclick="if(!confirm('Bạn có chắc chắn muốn xóa?')){return false;}" class="btn btn-danger btn-sm" href="<?=Url::build_current(['cmd'=>'delete_ward','district_id','id'=>[[=items.id=]]])?>">Xóa</a>-->
                    <!--ELSE-->
                    <!--<a class="btn btn-success btn-sm" href="<?=Url::build_current(['district_id'=>[[=items.district_id=]],'cmd'=>'edit_district'])?>">Sửa</a>-->
                    <a class="btn btn-warning btn-sm" href="<?=Url::build_current(['cmd'=>'v2', 'district_id'=>[[=items.district_id=]]])?>">Sửa phường xã</a>
                    <!--/IF:cond_-->
                    <!--/IF:cond-->
                </td>
            </tr>
            <!--/LIST:items-->
            </tbody>
        </table>
    </div>
    <input type="hidden" name="cmd" value="" id="cmd"/>
    <!--</form>-->
    <div id="box-modal-edit"></div>
</fieldset>
<?php
$cmdEms = "update_ems_province";
if (Url::get('province_id')) {
$cmdEms = "update_ems_district";
} else if (Url::get('district_id')) {
$cmdEms = "update_ems_ward";
}
?>
<script>
	$(document).ready(function() {
		$('#show_viettel').click(function() {
			$('.box-info-viettel').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_ghtk').click(function() {
			$('.box-info-ghtk').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_ems').click(function() {
			$('.box-info-ems').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_vnpost').click(function() {
			$('.box-info-vnpost').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_ghn').click(function() {
			$('.box-info-ghn').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_best').click(function() {
			$('.box-info-best').css("color", (this.checked) ? "black" : "silver");
		});
		$('#show_jt').click(function() {
			$('.box-info-jt').css("color", (this.checked) ? "black" : "silver");
		});
	});

	function showFormInfo(item) {
		let info_id = $(item).data('info-id');
		let info_type = $(item).data('info-type');
		let info_brand = $(item).data('info-brand');

		//get_form_info_v2
		let uri = "/index062019.php?page=zone_admin&cmd=get_form_info_v2&type=" + info_type + '&id=' + info_id + '&brand=' + info_brand;
		$.get(uri, function(html) {
			$(html).appendTo('body').modal({
				escapeClose: false,
				clickClose: false,
				showClose: true
			});
		});
	}
	function changeProvince(){
		let province_id = document.getElementById("province_id").value;
		window.location.href = "/index062019.php?page=zone_admin&cmd=v2&province_id=" + province_id;
	}
	function enableEdit(id){
		let value = jQuery('#viettel_name_'+id).html();
		value = $.trim(value);
		jQuery('#viettel_name_'+id).html('<input  name="quick_edit_name_'+id+'" type="text" id="quick_edit_name_'+id+'" value="'+value+'" class="form-control" onchange="upateEdit(this.value,'+id+');">');
		jQuery('#quick_edit_'+id).hide();
		jQuery('#quick_cancel_'+id).show();
	}
	function enableEmsEdit(id){
		let value = jQuery('#ems_code_'+id).html();
		value = $.trim(value);
		jQuery('#ems_code_'+id).html('<input  name="quick_edit_ems_code_'+id+'" type="text" id="quick_edit_ems_code_'+id+'" value="'+value+'" class="form-control" onchange="upateEmsEdit(this.value,'+id+');">');
		jQuery('#quick_edit_ems_'+id).hide();
		// jQuery('#quick_cancel_'+id).show();
	}
	function upateEmsEdit(value,id){
		$.ajax({
			method: "POST",
			url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
			data : {
				'cmd':'<?= $cmdEms ?>',
				'zone_id':id,
				'value':value
			},
			beforeSend: function(){
				//jQuery('#update-alert').show();
			},
			success: function(content){
				content = $.trim(content);
				$('#viettel_province_name'+id).html(content);
				//jQuery('#update-alert').hide();
				//jQuery('#quick_edit_'+id).show();
				//jQuery('#quick_cancel_'+id).hide();
			},
			error: function(){
				alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
			}
		});
	}
	function upateEdit(value,id){
		$.ajax({
			method: "POST",
			url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
			data : {
				'cmd':'<?=Url::get('province_id')?'update_district_name':'update_name'?>',
				'zone_id':id,
				'value':value
			},
			beforeSend: function(){
				//jQuery('#update-alert').show();
			},
			success: function(content){
				content = $.trim(content);
				$('#viettel_province_name'+id).html(content);
				//jQuery('#update-alert').hide();
				//jQuery('#quick_edit_'+id).show();
				//jQuery('#quick_cancel_'+id).hide();
			},
			error: function(){
				alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
			}
		});
	}
</script>
