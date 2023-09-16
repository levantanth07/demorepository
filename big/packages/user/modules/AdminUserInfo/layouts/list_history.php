<link href="assets/vissale/css/custom.css" rel="stylesheet" type="text/css" />
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<?php
    $option_user   = [[=option_user=]];
    $option_action = [[=option_action=]];
    $start_date    = [[=start_date=]];
    $end_date      = [[=end_date=]];
?>
<div class="container full">
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">
                Lịch sử thao tác
            </h3>
        </div>
        <div class="box-body">
            <form name="ListUserAdminInforForm" method="post">
                <div class="row">
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label>Từ ngày</label>
                                <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" placeholder="Từ ngày" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Đến ngày</label>
                                <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" placeholder="Đến ngày" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label>Hành động</label>
                                <select  name="option_action" id="option_action" class="form-control">
                                    <?php foreach ($option_action as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>  
                                </select>
                            </div>
                            <div class="col-md-2 mb-2" style="margin-top: 25px">
                                <select  name="option_user" id="option_user" class="form-control">
                                    <?php foreach ($option_user as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value ?></option>
                                    <?php endforeach;?>  
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button style="margin-top: 25px;" class="btn btn-primary" id="search" onclick="ReloadList(1)">Tìm kiếm</button>
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
            <input type="hidden" name="select_item_perpage" id="select_item_perpage" value="">
        </div>
    </div>
</div>
<script type="text/javascript">
    var blockId = <?php echo Module::block_id(); ?>;
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
            'start_date':start_date,
            'end_date':end_date,
            'page_no':pageNo,
            'option_user':$('#option_user').val(),
            'item_per_page':$('#item_per_page').val(),
            'option_action':$('#option_action').val(),
            'do':'ajax_list_history',
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
            placeholder: 'CMTND/Căn cước',
            allowClear: true
        });
        $(".js-example-placeholder-single").select2({
            placeholder: "Chọn hệ thống",
            allowClear: true
        });
        $('#search').on('click',function(e){
            e.preventDefault();
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
            var a = $(this).parents('body').find('#item_per_page').val();
            console.log(a)
            return;
            if($('#item_per_page').val() > 500 || $('#item_per_page').val() < 5 || $('#item_per_page').val() == ""){
                alert('Số dòng hiển thị trong khoảng 5-500');
                return false;
            }
            var myData = {
                'load_ajax':'1',
                'start_date':start_date,
                'end_date':end_date,
                'option_user':$('#option_user').val(),
                'item_per_page':$('#item_per_page').val(),
                'option_action':$('#option_action').val(),
                'do':'ajax_list_history',
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
        })
    })
</script>