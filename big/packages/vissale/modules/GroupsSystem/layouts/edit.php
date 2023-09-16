<script src="packages/core/includes/js/multi_items.js"></script>
<link href="assets/lib/select2/select2.min.css?v=10042021" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>

<style>
    .multi-edit-input input, select{width: auto}
    .multiselect-container>li>a>label{padding: 3px 20px 3px 20px}
    .multi-item-group label input{
        margin-right: 8px !important;
    }
</style>

<div style="display:none">
	<div id="mi_account_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
			<span class="multi-edit-input" style="width:40px;"><input  name="mi_account[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
			<span class="multi-edit-input"><input  name="mi_account[#xxxx#][account_id]" style="width:200px;" class="multi-edit-text-input" type="text" id="account_id_#xxxx#" onchange="getUserId('#xxxx#');"><input  name="mi_account[#xxxx#][user_id]" class="multi-edit-text-input" type="hidden" id="user_id_#xxxx#"></span>
<!--			<span class="multi-edit-input"><select  name="mi_account[#xxxx#][role]" style="width:120px;" class="multi-edit-text-input" id="role_#xxxx#">[[|role_options|]]</select></span>-->
            <span class="multi-edit-input">
                <select multiple="multiple" name="mi_account[#xxxx#][permissions][]" class="multi-edit-text-input multiple-select" id="permissions_#xxxx#" style="display: none;">
                    [[|permissions_options|]]
                </select>
            </span>
			<span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;" id="del_#xxxx#" ><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_account','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</div>
</div>
<fieldset id="toolbar">
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a href="javascript:void(0)" onclick="EditCategoryForm.submit();" > <span title="save"> </span> Ghi lại </a> </td>
        <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> Danh sách </a> </td>
		</tr>
	  </tbody>
	</table>
    </div>
</fieldset>
<br>
<fieldset id="toolbar">
		<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
		<form name="EditCategoryForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="confirm_edit" value="1" />
		 <div class="col-md-7">
        <?php $i=0;?>
        <div class="tab-content">
				<div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="info_tab">
          <h3></h3>
          <div class="form-group">
          	<label>Tên hệ thống / công ty (<span class="require">*</span>)</label>
						<input name="name" type="text" id="name" class="form-control">
					</div>
          <div class="form-group hide">
            <label>Alias (url) (<span class="require">*</span>)</label>
            <input name="name_id" type="text" id="name_id" class="form-control">
          </div>
          <div class="form-group hide">
						<label>Mô tả</label>					
						<textarea id="description" name="description" cols="30" rows="5" class="form-control"><?php echo Url::get('description','');?></textarea><br />
					</div>
					<div class="form-group">
						<h4>Công ty trực thuộc (<?php echo sizeof([[=groups=]])?>)</h4>
						<ul class="list-group">
                            <?php $i=0?>
							<!--LIST:groups-->
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong><?=++$i?>. [[|groups.name|]]</strong> / <a class="text-info" href="<?=Url::build_current(['cmd'=>'edit','id'=>[[=groups.system_group_id=]]]);?>">[[|groups.group_system_name|]]</a>
                                    </div>
                                    <div class="col-md-6">
                                        Sở hữu: <span class="label label-default">[[|groups.code|]]</span>, [[|groups.total_user|]] <i class="fa fa-user"></i>, [[|groups.total_order|]] <i class="fa fa-files-o"></i>
                                        <?=(([[=groups.expired_date=]]!='0000-00-00 00:00:00' and strtotime([[=groups.expired_date=]]) < strtotime(date('Y-m-d')))?'<span class="text-danger">':'<span class="text-info">').(([[=groups.expired_date=]]!='0000-00-00 00:00:00')?date('d/m/Y',strtotime([[=groups.expired_date=]])):'Không thời hạn').'</span>'?>
                                        <br>
                                        Gói: <b>[[|groups.package_name|]]</b>,<br>
                                        Max users: <b>[[|groups.user_counter|]]</b>, Max pages: <b>[[|groups.page_counter|]]</b>
                                    </div>
                                </div>
                            </li>
							<!--/LIST:groups-->
						</ul>
					</div>
				</div>
        <?php $i++;?>
				</div>
		</div>
		<div class="col-md-5">
			<div>
        <table class="table">
          <tr>
            <td width="30%">Hệ thống cha</td>
            <td><?=$this->map['selectbox']?></td>
          </tr>
          <tr>
            <td valign="top">Ảnh đại diện</td>
            <td>
              <input name="icon_url" type="file" id="icon_url" class="form-control"><div id="delete_icon_url"><?php if(Url::get('icon_url') and file_exists(Url::get('icon_url'))){?>[<a href="<?php echo Url::get('icon_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('icon_url')));?>" onclick="jQuery('#delete_icon_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
            </td>
          </tr>
          <tr class="hide">
            <td valign="top">Ảnh lớn</td>
            <td>
              <input name="image_url" type="file" id="image_url" class="form-control"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
            </td>
          </tr>
        </table>
			</div>
			<div>
				<div class="multi-item-wrapper" style="overflow: auto; min-height: 300px">
				  <div id="mi_account_all_elems" style="width: 700px;">
						<div>
							<span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_account',this.checked);"></span>
							<span class="multi-edit-input header" style="width:40px;">ID</span>
							<span class="multi-edit-input header" style="width:200px;">Tài khoản</span>
							<!--<span class="multi-edit-input header" style="width:120px;">Vai trò</span>-->
							<span class="multi-edit-input header" style="width:370px;">Phân quyền</span>
							<span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
							<br clear="all">
						</div>
				  </div>
				</div>
				<hr>
					<div><input type="button" value="Thêm" id="add-row" class="btn btn-warning btn-sm" onclick="clickAddNewRow()"></div>
				<hr>
                <p>
                    Tra cứu đơn hàng: Xem thông tin đơn hàng trong hệ thống<br>
                    Tra cứu hồ sơ nhân viên: Xem thông tin nhân viên trong hệ thống<br>
                    Xuất excel: Xuất file excel đơn hàng trong hệ thống theo 2 mẫu sản phẩm và đơn hàng<br>
                    Xem báo cáo: Xem các báo cáo hệ thống
                </p>
			</div>
            <!-- <div>
                <div class="multi-item-wrapper">
                  <div id="mi_rank_all_elems">
                        <div>
                            <span class="multi-edit-input header" style="width:252px;padding: 0 10px !important;height: 50px; display: flex; align-items: center">Rank</span>
                            <span class="multi-edit-input header" style="width:152px;padding: 0 10px !important;height: 50px; display: flex; align-items: center">Doanh thu tối thiểu<br>(Đv: triệu đồng)</span>
                            <span class="multi-edit-input header" style="width:60px;padding: 0 10px !important;height: 50px; display: flex; align-items: center">action</span>
                            <br clear="all">
                        </div>
                        <div id="items"></div>
                  </div>
                </div>
                <hr>
                    <div>
                    <input type="button" value="Thêm" id="add-row-rank" class="btn btn-warning btn-sm">
                    <input type="button" value="Lưu Rank" id="save-rank" class="btn btn-primary btn-sm">
                </div>
                <hr>
            </div> -->
		</div>
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
        
	</form>
</fieldset>
<script>
    rows = 0;
	mi_init_rows('mi_account',<?php if(isset($_REQUEST['mi_account'])){echo MiString::array2js($_REQUEST['mi_account']);}else{echo '[]';}?>, {a: function (a,b,c) {
        var pers = b.permissions.split(",");
        var selId = document.getElementById("permissions_" + c);
        var permissions = '<?= [[=permissions=]] ?>';
        permissions = JSON.parse(permissions);
        pers.forEach(function (per) {
            let pos = permissions.indexOf(per);
            if (pos > -1) { selId.options[pos].selected = true; }
        });
    }});

    function clickAddNewRow() {
        const div = mi_add_new_row('mi_account');
        var selId = $(div).find('select').last()[0];
        selId.options[0].selected = true;
        $(div).find('.btn-group').remove();
        $(div).find('select').last().multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '370px',
            maxHeight: 200,
            numberDisplayed: 4,
            nonSelectedText: 'Phân Quyền',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    }

    function getUserId(index){
        username = jQuery('#account_id_'+index).val();
        jQuery.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'get_user_id',
                'account_id':username
            },
            beforeSend: function(){

            },
            success: function(content){
                eval("var rs="+content);
                if(rs.id!='NOT_EXISTED'){
                    jQuery('#user_id_'+index).val(rs.id);
                }else{
                    jQuery('#account_id_'+index).val('');
                    jQuery('#user_id_'+index).val('');
                    alert('Tài khoản không tồn tại');
                }
            },
            error: function(){
                alert('Có lỗi xảy ra, bạn vui lòng thực hiện lại.');
            }
        });
    }

    function generate_row(object){
        return `
            <div>
                <div class="multi-item-group">
                    <input name="id" type="hidden" value="${object.id}">
                    <span class="multi-edit-input">
                        <input name="rank_name" style="width:250px;" class="multi-edit-text-input" type="text" value="${object.rank_name}">
                    </span>
                    <span class="multi-edit-input">
                        <input id="revenue_min_${rows++}" name="revenue_min" style="width:152px;" class="multi-edit-text-input" type="text" value="${format_number(object.revenue_min)}" onkeyup="on_key_up_revenue_min(this)">
                    </span>
                    <div class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;">
                        <button style="width: auto;" rankid="${object.id}" type="button" class="btn btn-default btn-sm glyphicon glyphicon-trash" onclick="delete_rank(event)"></button>
                    </div>
                </div>
                <br clear="all">
            </div>`;
    }

    function on_key_up_revenue_min(element){
        document.querySelector("#" + element.getAttribute('id')).value = format_number(element.value)
    }

    function format_number(string){
        // giả sử chuôĩ đầu vào ứng với element.value là " 12,213.23sdf.41234,1234"
        // chúng ta cần định dạng lại chuỗi này bằng cách loại bỏ hết các kí tự 
        // không phải là số, dấu chấm ".", dấu phẩy ",". Sau đó cắt chuỗi nhận được 
        // với các số liên tiếp, đến dấu chấm nếu có, rồi đến 2 số nếu có tức là ta 
        // sẽ nhận được chuỗi 12213.23
        return (string+"") // Ép kiểu input thành String
                .trim() // "12,213.23sdf.41234,1234"
                .replace(/[^\d\.]/g, '') // "12213.23.412341234"
                .replace(/(\d*\.\d{0,2}).*/, '$1') // "12213.23"
                .split('.') // ["12213"], ["23"]
                .map(function(e, i){
                    if(i) return e;

                    return e.split('') // [1,2,2,1,3]
                                .reverse() // [3,1,2,2,1]
                                .map(function(e, i){
                                    return (i+1)%3 == 0 ? ',' + e  : e
                                }) // [3,1,",2",2,1]
                                .reverse() // [1,2,",2",1,3]
                                .join('') // 12,213
                })
                .join(".") // 12,213.23
                .replace(/^,/, ''); // neu ,445,332 => 445,332
    }

    function delete_rank(e){
        console.log(e);
        e.stopPropagation();
        if(!e.target.getAttribute('rankid')){
            var wrap = e.target.parentNode.parentNode.parentNode.parentNode;
            wrap.parentNode.removeChild(wrap);
            return;
        }
        var url = window.location.pathname+'?page=groups_system&cmd=delete_rank';
        var postData = {
            id: e.target.getAttribute('rankid')
        };
        $.post(url, postData)
            .done(function(e){
                switch(e.status){
                    case 'DELETE_SUCCESS':
                        render_rows(e.ranks);

                    break;
                    case 'DELETE_ERROR':
                        alert('DELETE_ERROR')
                    break;

                    default:
                        alert(e.status)
                }
            })
            .error(function(err){
                console.log(err)
            });
    }


	jQuery(document).ready(function(e) {
        jQuery('#name').change(function(){
			jQuery.ajax({
				method: "POST",
				url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				data : {
					'cmd':'get_name_id',
					'name':jQuery(this).val()
				},
				beforeSend: function(){
					
				},
				success: function(content){
					jQuery('#name_id').val(content);
				},
				error: function(){
					//custom_alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
				}
			});
			
		});
		jQuery('#name_2').change(function(){
			jQuery.ajax({
				method: "POST",
				url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				data : {
					'cmd':'get_name_id',
					'name':jQuery(this).val()
				},
				beforeSend: function(){
					
				},
				success: function(content){
					jQuery('#name_id_2').val(content);
				},
				error: function(){
					//custom_alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
				}
			});
			
		});

        $('#add-row-rank').click(function(){
            $('#items').append($(generate_row({rank_name: '', revenue_min: '', id: ''})));
        });

        $('#save-rank').click(function(){
            var data = {insert: [], update: [] };
            $('#mi_rank_all_elems .multi-item-group').each(function(i, e) {
                var rank = {
                    rank_name: e.querySelector('input[name="rank_name"]').value,
                    revenue_min: e.querySelector('input[name="revenue_min"]').value
                };
                
                var id = e.querySelector('input[name="id"]').value;
                if(id > 0){
                    rank['id'] = id;
                    data.update.push(rank);
                }else{
                    data.insert.push(rank);
                }
            });

            var url = window.location.pathname+'?page=groups_system&cmd=insert_or_update_rank';
            var postData = {
                data: JSON.stringify(data)
            };
            $.post(url, postData)
                .done(function(e){
                    switch(e.status){
                        case 'INSERT_ERROR':

                        break;
                        case 'DATA_INVALID':

                        break;
                        case 'USER_NOT_FOUND':

                        break;
                        case 'SYSTEM_GROUP_NOT_FOUND':

                        break;
                        case 'UPDATE_SUCCESS':
                            render_rows(e.ranks);

                        break;
                        case 'UPDATE_ERROR':

                        break;
                        case 'INSERT_SUCCESS':
                            render_rows(e.ranks);
                        break;
                    }
                })
                .error(function(err){
                    console.log(err)
                });
        })
  });
    $(document).ready(function(){
        $(".js-example-placeholder-single").select2({
            placeholder: "Nhập tên hệ thống",
            allowClear: true
        });
	    $('.multiple-select').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '370px',
            maxHeight: 200,
            numberDisplayed: 4,
            nonSelectedText: 'Phân Quyền',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    })
</script>
