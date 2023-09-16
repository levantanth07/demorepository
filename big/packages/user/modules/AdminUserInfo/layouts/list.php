<link href="assets/vissale/css/custom.css" rel="stylesheet" type="text/css" />
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<style type="text/css">
    .select2 {
        width: 100% !important;
    }
</style>
<?php
    $option_status          = [[=option_status=]];
    $option_cmnd            = [[=option_cmnd=]];
    $option_shk             = [[=option_shk=]];
    $option_hosoxinviec     = [[=option_hosoxinviec=]];
    $option_hopdonghoptac   = [[=option_hopdonghoptac=]];
    $option_bancamket       = [[=option_bancamket=]];
    $option_system_group    = [[=option_system_group=]];
    $start_date             = [[=start_date=]];
    $end_date               = [[=end_date=]];

    $option_bangcap         = [[=option_bangcap=]];
    $option_giaykhaisinh    = [[=option_giaykhaisinh=]];
    $option_camketbaomat    = [[=option_camketbaomat=]];
    $option_giaykhamsuckhoe = [[=option_giaykhamsuckhoe=]];
    $option_trung_nhan_su = [[=option_trung_nhan_su=]];
    $current_date = [[=current_date=]];
?>
<script>
    var current_date = '<?php echo $current_date ?>';
</script>
<div class="container full">
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">
                Danh sách thông tin tài khoản
            </h3>
        </div>
        <div class="box-body">
            <form name="ListUserAdminInforForm" method="post">
                <div class="row">
                    
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3"><h5 style="padding-left: 0px;">Ngày tạo tài khoản</h5></div>
                            <div class="col-md-3"><h5 style="padding-left: 0px;">Hệ thống</h5></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <div class="row">
                                    <div class="col-md-6 col-xs-12 col-sm-6" style="padding-right: 0px;border-color:red;">
                                        <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" placeholder="Từ ngày" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-xs-12 col-sm-6" style="padding-left: 0px;">
                                        <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" placeholder="Đến ngày" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_system_group" id="option_system_group" class="form-control select2">
                                    <?php foreach ($option_system_group as $key => $value) : ?>
                                        <?php echo $value ?>
                                    <?php endforeach;?>  
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <input type="text" name="hkd" id="hkd" placeholder="Tìm tên HKD" class="form-control">
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_status" id="option_status" class="form-control">
                                    <?php foreach ($option_status as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>  
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <input type="text" name="cmnd" id="cmnd" placeholder="Tìm CMTND/Căn cước" class="form-control">
                            </div>
                            
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <input type="text" name="user_name" id="user_name" placeholder="Tìm Họ và Tên" class="form-control">
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <input type="text" name="account_name" id="account_name" placeholder="Tìm tên tài khoản" class="form-control">
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <input type="text" name="phone" id="phone" placeholder="Tìm số điện thoại" class="form-control">
                                <input type="hidden" name="phone_hidden" id="phone_hidden" class="form-control">
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_cmnd" id="option_cmnd" class="form-control">
                                    <?php foreach ($option_cmnd as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>  
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_hopdonghoptac" id="option_hopdonghoptac" class="form-control">
                                    <?php foreach ($option_hopdonghoptac as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_bancamket" id="option_bancamket" class="form-control">
                                    <?php foreach ($option_bancamket as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_shk" id="option_shk" class="form-control">
                                    <?php foreach ($option_shk as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>    
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_hosoxinviec" id="option_hosoxinviec" class="form-control">
                                    <?php foreach ($option_hosoxinviec as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_giaykhaisinh" id="option_giaykhaisinh" class="form-control">
                                    <?php foreach ($option_giaykhaisinh as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_camketbaomat" id="option_camketbaomat" class="form-control">
                                    <?php foreach ($option_camketbaomat as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_giaykhamsuckhoe" id="option_giaykhamsuckhoe" class="form-control">
                                    <?php foreach ($option_giaykhamsuckhoe as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_bangcap" id="option_bangcap" class="form-control">
                                    <?php foreach ($option_bangcap as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-5">
                                <select name="option_trung_nhan_su" id="option_trung_nhan_su" class="form-control">
                                    <?php foreach ($option_trung_nhan_su as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-md-6 col-xs-12 col-sm-6">
                                <button class="btn btn-default btn-sm float-right reset"  type="reset">Reset</button>
                                <button class="btn btn-primary btn-sm float-right mr-5" id="search">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <br>

            <div class="" id="action_content_event">
                <div class="" id="module_<?php echo Module::block_id(); ?>">
                </div>
            </div>
            <input type="hidden" name="page_no" value="1"/>
            <input type="hidden" name="total" id="total" value="">
        </div>
    </div>
</div>
<script type="text/javascript">
    var options = $('#option_system_group option');
    var all_option_system_group = $.map(options ,function(option) {
        return option.value;
    });
    var blockId = <?php echo Module::block_id(); ?>;
    var all_system = all_option_system_group.join();
    $('.reset').click(function(e){
        $( "#module_"+blockId ).html('');
        location.reload();
    })
    function ReloadList(pageNo) {
        
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
            'hkd':$('#hkd').val(),
            'page_no':pageNo,
            'option_status':$('#option_status').val(),
            'item_per_page':$('#item_per_page').val(),
            'option_shk':$('#option_shk').val(),
            'option_hosoxinviec':$('#option_hosoxinviec').val(),
            'option_hopdonghoptac':$('#option_hopdonghoptac').val(),
            'option_bancamket':$('#option_bancamket').val(),
            'option_trung_nhan_su':$('#option_trung_nhan_su').val(),
            'option_cmnd':$('#option_cmnd').val(),
            'status':$('#status').val(),
            'option_system_group':$('#option_system_group').val(),
            'do':'ajax_list',
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
                    },
                    error: function(){
                        alert('Lỗi tải danh sách. Bạn vui lòng kiểm tra lại kết nối!');
                        location.reload();
                    }
                });
            },1200);
    }
    $(document).ready(function(){
        $('.js-example-basic-single').select2({
            placeholder: 'TÊN HỆ THỐNG',
            allowClear: true
        });
        $('.select2').select2({
            dropdownAutoWidth : true,
        });
        $(".js-example-placeholder-single").select2({
            placeholder: "Chọn hệ thống",
            allowClear: true
        });
        $('#phone').on('change',function(){
            let phone = $(this).val();
            $('#phone_hidden').val(phone)
        })
        $('#search').on('click',function(e){
            e.preventDefault();
            var search = $(this);
            search.attr("disabled", true);
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
            if (start_date != '' &&  end_date != '' ) {
                var start = Date.parse(start_date) / 1000;
                var end = Date.parse(end_date) / 1000;
                var current = Date.parse(current_date) / 1000;
                if (start > current){
                    alert('Bạn vui lòng chọn ngày bắt đầu phải nhở hoặc bằng ngày kết thúc hoặc ngày hiện tại');
                    search.attr("disabled", false);
                    $('#start_date').focus();
                    $('#start_date').css('border-color','red');
                    return false;
                } else if (start > end){
                    alert('Bạn vui lòng chọn ngày bắt đầu phải nhở hoặc bằng ngày kết thúc hoặc ngày hiện tại');
                    search.attr("disabled", false);
                    $('#start_date').focus();
                    return false;
                } else if (end > current){
                     alert('Bạn vui lòng chọn ngày kết thúc phải nhở hoặc bằng ngày hiện tại');
                    search.attr("disabled", false);
                    $('#end_date').focus();
                     $('#end_date').css('border-color','red');
                    return false;
                }
            } else if(start_date == ''){
                alert('Bạn vui lòng chọn ngày bắt đầu đúng định dạng');
                search.attr("disabled", false);
                $('#start_date').focus();
                $('#start_date').css('border-color','red');
                return false;
            } else if (end_date == ''){
                alert('Bạn vui lòng chọn ngày kết thúc đúng định dạng');
                search.attr("disabled", false);
                $('#end_date').focus();
                $('#end_date').css('border-color','red');
                return false;
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
                        search.attr("disabled", false);
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