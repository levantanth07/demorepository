
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <div class="text-bold"><i class="fa fa-flag"></i> Lịch sử đơn hàng</div>
                </div>
                <div class="col-md-4 text-right">
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form name="LogOrderForm" method="post">
                <div class="panel">
                    <div class="row">
                        <div class="col-xs-2">
                            <input name="order_id" type="text" id="order_id" class="form-control" placeholder="Mã đơn hàng">
                        </div>
                        <div class="col-xs-4">
                            <button name="search" type="submit" id="search" class="btn btn-primary"><i class="fa fa-search"></i> Tìm kiếm</button>
                            <button name="reset" type="reset" id="reset" class="btn btn-default"><i class="fa fa-refresh"></i> Làm mới</button>
                        </div>
                    </div>
                </div>
                <div id="data_render">
                    
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#search').on('click',function(e){
            e.preventDefault();
            var search = $(this);
            search.attr("disabled", true);
            var order_id = $('#order_id').val();
            var myData = {
                'do':'log_order_ajax',
                'order_id':order_id
            }
            $("#data_render").html('<div id="item-list" style="height:450px;padding:20px;"><div class="overlay text-info">\n' +
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
                        if (content === 'FALSE') {
                           $("#data_render").html('');
                           search.attr("disabled", false);
                           alert('Vui lòng nhập đúng mã đơn hàng!'); return;
                       }
                        $("#data_render").html(content);
                        search.attr("disabled", false);
                    },
                    error: function(){
                        alert('Lỗi tải danh sách. Bạn vui lòng kiểm tra lại kết nối!');
                        location.reload();
                    }
                });
            },2000);
        })

        $('#reset').on('click',function(e){
            e.preventDefault();
            $('#order_id').val('');
            $('#data_render').html('');
        })
    })
</script>

