<style>
    #vinhDanhBoard .box-warning{
        opacity: 0.8;
        min-height: 400px;
        border: 7px solid #ffd452;
        border-radius: 5px;
    }
    #vinhDanhBoard .box-header{
        background-image: linear-gradient(to right, #fcb045, #fd1d1d);
    }
    #vinhDanhBoard .row-1{
        font-weight: bold;
        background-image: linear-gradient(to right, #B7F8DB , #FFF) !important;
        font-size:14px;
        height: 50px;
        color:#ff8c52;
    }
    #vinhDanhBoard .row-1 td:first-child{

    }
    #vinhDanhBoard .row-2{
        font-weight: bold;
        background-color: #dfffd0 !important;
        font-size:14px;
    }
    #vinhDanhBoard .row-3{
        font-weight: bold;
        background-color: #ecffeb !important;
        font-size:14px;
    }
    #vinhDanhBoard .system-box-header{
        background: #fff;
        text-align: center;
        font-size:26px;
        font-family: "Times New Roman";
        color: #999;
        text-transform: uppercase;
    }
    #vinhDanhBoard .row-4,#vinhDanhBoard .row-5,#vinhDanhBoard .row-6,#vinhDanhBoard .row-7,#vinhDanhBoard .row-8,#vinhDanhBoard .row-9,#vinhDanhBoard .row-10{
        font-weight: bold;
        color:#666;
    }
    .top2{margin-top:30px;height: 195px;color: #ff7096;padding:90px 5px 5px 5px;font-weight: bold;font-size:13px;}
    .top1{height: 195px;color: #ef0019;padding:0px 5px 5px 5px;font-weight: bold;}
    .top3{margin-top:30px;height: 195px;color:#ff7096;padding:90px 5px 5px 5px;font-weight: bold;font-size:13px;}
    .top-wrapper{
        width: 506px;
        height: 264px;
        background: url('assets/vissale/images/prize-podium.png') no-repeat;
        margin:auto;
    }
    @media screen and (max-width: 720px){
        .top1{padding-top:0px;font-size:12px;height: 155px;}
        .top2{padding-top:50px;font-size:12px;height: 155px;}
        .top3{padding-top:50px;font-size:12px;height: 155px;}
        .top-wrapper{
            width: 100%;height: 155px;
        }
    }
</style>
<div class="container full" id="vinhDanhBoard">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">Bảng xếp hạng</li>
            <li class="pull-right hidden-xs">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="box box-default" style="background: url('assets/vissale/images/victory_bg.png');background-position: center;">
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="top-wrapper">
                                                <div id="saleTop2" class="col-xs-4 text-center top2"> </div>
                                                <div id="saleTop1" class="col-xs-4 text-center top1"> </div>
                                                <div id="saleTop3" class="col-xs-4 text-center top3"> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box box-warning box-solid">
                                        <div class="box-header">
                                            <h4 class="title text-center">Bảng xếp hạng Sale</h4>
                                        </div>
                                        <div class="box-body" id="saleRankReport">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="1%">#</th>
                                                    <th width="39%">Họ và tên</th>
                                                    <th width="30%" class="text-right">Điểm</th>
                                                    <th width="30%">Đội nhóm</th>
                                                </tr>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <button class="btn btn-default btn-lg" onclick="getUsersByRank('GANDON');"><i class="fa fa-hand-o-right"></i> Xem</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="top-wrapper">
                                        <div id="saleTop2" class="col-xs-4 text-center top2"> </div>
                                        <div id="saleTop1" class="col-xs-4 text-center top1"> </div>
                                        <div id="saleTop3" class="col-xs-4 text-center top3"> </div>
                                    </div>
                                    <div class="box box-warning box-solid">
                                        <div class="box-header">
                                            <h4 class="title text-center">Bảng xếp hạng Marketing</h4>
                                        </div>
                                        <div class="box-body" id="mktRankReport">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th width="1%">#</th>
                                                    <th width="39%">Họ và tên</th>
                                                    <th width="30%" class="text-right">Điểm</th>
                                                    <th width="30%">Đội nhóm</th>
                                                </tr>
                                            </table>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <button class="btn btn-default btn-lg" onclick="getUsersByRank('MARKETING');"><i class="fa fa-hand-o-right"></i> Xem</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ghi chú</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var blockId = <?php echo Module::block_id(); ?>;
    $(document).ready(function() {
        $('#group_ids').multiselect(
            {
                enableFiltering:true,
                buttonWidth: '120px',
                maxHeight: 200
            }
        );
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.box-google-keep').click(function() {
            $('#noteModal .modal-body').empty().html($(this).find('.text-ellipsis').html())

            $('#noteModal').modal('show')
        })
    });
    function getUsersByRank($code){
        let myData = {
            'do':'get_user_rank',
            'date_from':"<?=Url::get('date_from')?>",
            'date_to':"<?=Url::get('date_to')?>",
            'status_id':$('#status_id').val(),
            'code':$code,
            block_id:blockId
        };
        console.log(myData);
        $.ajax({
            method: "POST",
            url: 'form.php',
            data : myData,
            beforeSend: function(){
                if($code=='GANDON'){
                    $('#saleRankReport').html('<div class="text-warning">Đang tải dữ liệu, bạn vui lòng đợi... </div>');
                }else{
                    $('#mktRankReport').html('<div class="text-warning">Đang tải dữ liệu, bạn vui lòng đợi... </div>');
                }
            },
            success: function(content){
                if($code=='GANDON'){
                    $('#saleRankReport').html(content);
                }else{
                    $('#mktRankReport').html(content);
                }
            },
            error: function(){
                alert('Lỗi tải danh sách đơn hàng. Bạn vui lòng kiểm tra lại kết nối!');
                location.reload();
            }
        });
    }
</script>