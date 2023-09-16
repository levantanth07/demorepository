<?php
    $items = [[=items=]];
    $total = [[=total=]];
    $item_per_page = [[=item_per_page=]];
    $message = [[=message=]];
?>
<style>
    .tableFixHead          { overflow: auto; height: 100px; }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

/* Just common table stuff. Really. */
table  { border-collapse: collapse; width: 100%; }
th, td { padding: 8px 16px; }
th     { background:#eee; }
.scroll>table{
    min-width: 1626px;
}
</style>
<?php if($message == '') : ?>
    <?php if(sizeof($items) > 0): ?>
        <div class="row">
            <div class="col-md-2">
                <div class="input-group">
                    <span class="input-group-addon" style="border: none;" id="total">Tổng : <strong><?php echo $total; ?></strong></span>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php endif; ?>
<form name="ListUserAdminInforForm">
    <div class="scroll" style="
  height: 800px;
  overflow: scroll;">
    <table class="table table-bordered tableFixHead">
        <thead>
            <tr valign="middle">
                <th width="1%" class="text-center">STT</th>
                <th width="9%" class="text-center">HKD</th>
                <th width="10%" class="text-center">Tên TK</th>
                <th width="8%" class="text-center">Họ và tên</th>
                <th width="8%" class="text-center">Địa chỉ thường trú</th>
                <th width="8%" class="text-center">SĐT</th>
                <th width="5%" class="text-center">CMTND/Căn cước</th>
                <th width="25%" class="text-center">Ảnh Hồ sơ</th>
                <th width="8%" class="text-center">Ngày tạo</th>
                <th width="5%" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php  if ($message != '') : ?>
                <td class="text-center text-danger" colspan="9"><?php echo $message; ?></td>
            <?php else : ?>
            <?php if(sizeof($items) > 0): ?>
                <?php foreach($items as $key => $value): ?>
                    <?php $owner = $value['code'] == $value['account_id']?true:false;?>
                    <tr bgcolor="" valign="middle" id="">
                        <td class="text-center">
                            <?php echo $value['index'];?>
                        </td>
                        <td class="text-left">
                            <div class="small">
                                <?php echo $value['group_name']; ?>  | <?php echo $value['master_group']; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <p class="text-bold"><?php echo $value['account_id'] ?></p>
                            <?php if($value['active']): ?>
                                <span class="label label-success">Đang hoạt động</span><br>
                                <p></p>
                            <?php else: ?>
                                <span class="label label-default">Dừng hoạt động</span><br>
                                <p></p>
                            <?php endif; ?>
                            <?php if($owner): ?>
                                <div class="text-danger">(Sở hữu)</div>
                                <p></p>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="text-bold"><?php echo $value['full_name'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="small"><?php echo $value['address'] ? $value['address'] .',' : '' ?> <?php echo $value['zone_name'] ?> </span>
                        </td>
                        <td class="text-center">
                            <span class="text-bold"><?php echo ModifyPhoneNumber::hidePhoneNumber($value['phone_number'],4) ?></span>
                            <br>
                            <?php if($value['ids']): ?>
                                <?php
                                    $arrIds = explode(',', $value['ids']);
                                    $ids = [];
                                    foreach ($arrIds as $k => $v) {
                                        $ids[(int)$v] = (int)$v;
                                    }
                                    if(in_array((int)$value['id'], $ids)){
                                        unset($ids[$value['id']]);
                                    }
                                    $strIds = implode(', ', $ids); 
                                ?>
                                <span class="btn btn-danger btn-sm view" data-phone="<?php echo $value['phone_number'] ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $strIds; ?>">Xem trùng</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="text-bold"><?php echo ModifyPhoneNumber::hidePhoneNumber($value['identity_card'],4) ?></span>
                        </td>
                        <td align="left">
                            <?php
                                $ho_so_xin_viec         = '';
                                $so_ho_khau             = '';
                                $hop_dong_hop_tac       = '';
                                $ban_cam_ket            = '';
                                $arrUserImage = explode(',',$value['user_image']);
                            ?>
                            <div class="row">
                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <div class="checkbox">
                                      <label><input disabled="" type="checkbox" value="" <?php if(!empty($value['identity_card_front'])) : ?> checked="" <?php endif; ?> >CMTND/Căn cước mặt trước</label>
                                    </div>
                                    <div class="checkbox">
                                      <label><input disabled="" type="checkbox" value="" <?php if(!empty($value['identity_card_back'])) : ?> checked="" <?php endif; ?>>CMTND/Căn cước mặt sau</label>
                                    </div>
                                    <div class="checkbox disabled">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::SO_HO_KHAU, $arrUserImage)) : ?> checked="" <?php endif; ?>>Sổ hộ khẩu</label>
                                    </div>
                                    <div class="checkbox disabled">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::GIAY_KHAM_SUC_KHOE, $arrUserImage)) : ?> checked="" <?php endif; ?>>Giấy khám SK A3</label>
                                    </div>
                                    <div class="checkbox disabled">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::KHAI_SINH, $arrUserImage)) : ?> checked="" <?php endif; ?>>Giấy khai sinh</label>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <div class="checkbox">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::HO_SO_XIN_VIEC, $arrUserImage)) : ?> checked="" <?php endif; ?>>Hồ sơ xin việc</label>
                                    </div>
                                    <div class="checkbox">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::HOP_DONG_HOP_TAC, $arrUserImage)) : ?> checked="" <?php endif; ?>>Hợp đồng hợp tác</label>
                                    </div>
                                    <div class="checkbox disabled">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::CAM_KET, $arrUserImage)) : ?> checked="" <?php endif; ?>>Bản cam kết QC,Tư vấn</label>
                                    </div>
                                    <div class="checkbox">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::BANG_CAP, $arrUserImage)) : ?> checked="" <?php endif; ?>>Bằng cấp</label>
                                    </div>
                                    <div class="checkbox disabled">
                                      <label><input disabled="" type="checkbox" value="" <?php if(in_array(ImageType::CAM_KET_BAO_MAT_TT, $arrUserImage)) : ?> checked="" <?php endif; ?>>Cam kết bảo mật thông tin</label>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-bold"> <?php echo $value['user_created'] ?></div>
                            <div class="small"> <?php echo date('d-m-Y H:i:s',strtotime($value['created']))?></div>
                        </td>
                        <td align="center">
                            <a href="<?php echo URL::build('user_admin');?>&cmd=edit&id=<?php echo $value['account_id'] ?>&flag=secur" class="btn btn-primary btn-sm" target="blank">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <td class="text-center text-danger" colspan="9">Không có dữ liệu!</td>
            <?php endif; ?>
            <?php endif; ?>
        </tbody>
    </table>
     </div>
     <?php if($message == '') : ?>
    <?php if(sizeof($items) > 0): ?>
        <div class="row">
            <div class="col-md-2">
                <div class="input-group">
                    <input onchange="ReloadList(1)" name="item_per_page" id="item_per_page" class="form-control" min="15" max="500" style="width: 80px;" value="15" placeholder="Dòng hiển thị" type="number"/>
                    <!-- <span class="input-group-addon" style="border: none;" id="total">Tổng : <?php echo $total; ?></span> -->
                </div>
            </div>
            <div class="col-md-6 text-center">
                [[|paging|]]
            </div>
        </div>
        <input  name="page_no" type="hidden" id="page_no" value="[[|page_no|]]"/>
    <?php endif; ?>
    <?php endif; ?>
</form>
<script type="text/javascript">
    var item_per_page = '<?php echo $item_per_page; ?>';
    $('#item_per_page').val(item_per_page)
    $(document).ready(function(){
        $('html body .view').on('click',function(){
            let phone = $(this).data('phone');
            let result = phone.slice(0,phone.length - 4);
            // $('#phone').val(result)
            $('#phone_hidden').val(phone)
            if ($('#start_date').val()) {
                start_date = $('#start_date').val();
            } else {
                start_date = '';
            }
            if ($('#end_date').val()) {
                end_date = $('#end_date').val();
            } else {
                end_date = '';
            }
            if($('#item_per_page').val() > 500 || $('#item_per_page').val() < 5 || $('#item_per_page').val() == ""){
                alert('Số dòng hiển thị trong khoảng 5-500');
                return false;
            }
            var myData = {
                'load_ajax':'1',
                'user_name':$('#user_name').val(),
                'account_name':$('#account_name').val(),
                'phone':$('#phone').val(),
                'phone_hidden':$('#phone_hidden').val(),
                'start_date':start_date,
                'end_date':end_date,
                'cmnd':$('#cmnd').val(),
                'option_status':$('#option_status').val(),
                'hkd':$('#hkd').val(),
                'item_per_page':$('#item_per_page').val(),
                'option_shk':$('#option_shk').val(),
                'option_hosoxinviec':$('#option_hosoxinviec').val(),
                'option_hopdonghoptac':$('#option_hopdonghoptac').val(),
                'option_bancamket':$('#option_bancamket').val(),
                'option_cmnd':$('#option_cmnd').val(),
                'option_hosoxinviec':$('#option_hosoxinviec').val(),
                'option_hopdonghoptac':$('#option_hopdonghoptac').val(),
                'option_bancamket':$('#option_bancamket').val(),
                'option_trung_nhan_su':$('#option_trung_nhan_su').val(),
                'option_cmnd':$('#option_cmnd').val(),

                'option_bangcap':$('#option_bangcap').val(),
                'option_giaykhaisinh':$('#option_giaykhaisinh').val(),
                'option_camketbaomat':$('#option_camketbaomat').val(),
                'option_giaykhamsuckhoe':$('#option_giaykhamsuckhoe').val(),
                
                'status':$('#status').val(),
                'option_system_group':$('#option_system_group').val(),
                'do':'ajax_list',
                'check_submit':1,
                'all_system':all_system
            }
            $( "#module_"+blockId ).html('<div id="item-list" style="height:450px;padding:20px;"><div class="overlay text-info">\n' +
            '        <div class="spin-loader"></div> \n' +
            '      </div></div>');
            t = setTimeout(function (){
                $.ajax({
                    method: "POST",
                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                    data : myData,
                    beforeSend: function(){

                    },
                    success: function(content){
                        content = $.trim(content);
                        $( "#module_"+blockId ).html(content);
                        // search.attr("disabled", false);
                    },
                    error: function(){
                        alert('Lỗi tải danh sách. Bạn vui lòng kiểm tra lại kết nối!');
                        location.reload();
                    }
                });
            },2000);  
        })
    })
</script>