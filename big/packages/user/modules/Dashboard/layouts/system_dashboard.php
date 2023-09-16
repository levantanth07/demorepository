
<style>
    #vinhDanhBoard .box-warning{
        opacity: 0.8;
        min-height: 400px;
        border: 1px solid #ffd452;
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
    .top2{margin-top:30px;height: 195px;color: #664a51;padding:90px 5px 5px 5px;font-weight: bold;font-size:13px;}
    .top1{height: 195px;color: #ef0019;padding:10px 5px 5px 5px;font-weight: bold;}
    .top3{margin-top:30px;height: 195px;color:#914c1b;padding:90px 5px 5px 5px;font-weight: bold;font-size:13px;}
    .top-wrapper{
        width: 100%;
        height: 300px;
        background: url(assets/vissale/images/new_cup1.jpg) no-repeat;
        background-size: contain;
        background-position: center;
        overflow: hidden;
        background-color: #f7f7f7;
    }
    #date_from,#date_to{width: 100px;}
    @media screen and (max-width: 720px){
        #date_from,#date_to{width: 100%;}
        .top1{padding-top: 10px;font-size: 16px;line-height: 16px;height: 155px;}
        .top2{padding-top:50px;font-size:12px;height: 155px;}
        .top3{padding-top:50px;font-size:12px;height: 155px;}
        .top-wrapper{
            height: 213px;
            width: 100%;
        }
    }
    .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
        border: 1px solid #eaeaea;
    }
    p.group-expired{
        font-size: 12px; font-weight: normal; font-style: italic; color: red; margin: 0
    }

div#comTop1,div#comTop2,div#comTop3 {
    width: fit-content;
    position: absolute;
    display: block;
    text-align: center;
}

.top-wrapper {
    position: relative;
    display: flex;
}

div#comTop1 {
    left: 50%;
    transform: translateX(-50%);
    color: red;
}

div#comTop3, div#comTop2 {
    top: 40%;
    left: 50%;

}

div#comTop2 {
    left: -100%;
}

div#comTop3 {
    left: 100%;
}
.aaa {
    width: 100px;
    height: 100%;
    margin: auto;
    position: relative;
}

@media screen and (min-width: 500px){
    .aaa {
        width: 150px;
    }
}

@media screen and (min-width: 700px){
    .aaa {
        width: 200px;
    }
}

loading {width: 120px; height: 30px; display: none; background: url('/assets/standard/svgs/loading-black.svg'); background-size: 30px; background-repeat: no-repeat; background-position: center; margin-bottom: 15px; position: relative;}
loading::after {content: 'Xin chờ giây lát';font-size: 13px;position: absolute;bottom: -15px;transform: translateX(-50%);}
.hidden{display: none;}
.small{
    font-size:  14px;
}
tr[class^="row-"] td:nth-child(2) {position: -webkit-sticky;
position: -moz-sticky;
position: -o-sticky;
position: -ms-sticky;
position: sticky; left: 0; background: #fff;}
div#company_rank, #saleRankReport, #mktRankReport {overflow: auto; padding:  0}
tr[class^="row-"]:nth-child(1) td:nth-child(2) {background: #c8fae3;}
tr[class^="row-"]:nth-child(2) td:nth-child(2) {background: #e5ffda;}
tr[class^="row-"]:nth-child(3) td:nth-child(2) {background: #f0ffef;}


#saleRankReport tr>th,
#saleRankReport tr>td,
#mktRankReport tr>th,
#mktRankReport tr>td,
#company_rank tr>th,
#company_rank tr>td {
    border-left:  0;
    border-top:  0;
    border-right: 1px solid #eaeaea;
}
div#company_rank, #saleRankReport, #mktRankReport{max-height: 500px;}

div#company_rank tfoot, div#company_rank thead,
div#saleRankReport tfoot, div#saleRankReport thead,
div#mktRankReport tfoot, div#mktRankReport thead
{position: -webkit-sticky;
position: -moz-sticky;
position: -o-sticky;
position: -ms-sticky;
position: sticky;
 bottom: 0; z-index: 9999; background: #fff;}

div#company_rank thead, div#mktRankReport thead, div#saleRankReport thead{top:  0}

div#saleRankReport .table>thead>tr>th, div#mktRankReport .table>thead>tr>th, div#company_rank .table>thead>tr>th {
    border-bottom: 1px solid #f4f4f4;
}

</style>
<div class="container full" id="vinhDanhBoard">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">
                Dashboard hệ thống - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-xep-hang-hkd/"
                                        target="_blank" class="btn btn-default"
                                        style="padding: 0px 2px;">
                                        <i class="fa fa-question-circle"></i>
                                        Hướng dẫn
                                     </a>

            </li>
            <li class="pull-right hidden-xs">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid box-default">
                        <form name="ReportForm" method="post">
                            <div class="row box-body">
                                <div class="col-md-12 form-inline">
                                    <div class="form-group">
                                        <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                                    </div>
                                    <div class="form-group">
                                        <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                                    </div>
                                    <div class="form-group">
                                        <select name="status_id" id="status_id" class="form-control"></select>
                                    </div>
                                    <!--IF:cond(Dashboard::$bdh)-->
                                    <div class="form-group">
                                        <select name="filter_by" id="filter_by" class="form-control"></select>
                                    </div>
                                    <!--/IF:cond-->
                                    <div class="form-group">
                                        <?=$this->map['system_group']?>
                                    </div>
                                    <div class="form-group">
                                        <select name="view_type" id="view_type" class="form-control"></select>
                                    </div>
                                    <div class="form-group pull-right">
                                        HỘ
                                        <select  name="group_ids[]" id="group_ids" multiple style="display: none;width: 200px;">
                                            [[|group_ids_option|]]
                                        </select>
                                        <button type="button" class="btn btn-warning" id="toggle-hide-btn" onclick="togglePoint.bind(this)()">Ẩn/Hiện điểm</button>
                                    </div>
                                    <div style="clear: both"></div>
                                    <div style="padding-top: 10px;font-size:11px;color:#ff851b">
                                        Chú thích:<br>
                                        - Cột SĐT: Tất cả các đơn đã được tạo trong thời gian xem <br>
                                        - Cột Đơn: Các đơn đã chốt trong thời gian xem (trừ các đơn không tính doanh thu)<br>
                                        - Cột Tổng: Bao gồm các Đơn mới, Đơn tối ưu, Đơn CSKH (cskh, đặt lại,...) và các đơn chưa phân loại đơn
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box box-default">
                <div class="box-header system-box-header">
                    <?php
                    $img = [[=group_system_icon_url=]];
                    if($img){
                        ?>
                        <img src="[[|group_system_icon_url|]]" width="60" onerror="this.src='assets/standard/images/no_avatar.webp'" alt="[[|group_system_name|]]">
                    <?php }?>
                    [[|title|]]
                </div>
                <div class="box-body" style="padding:0px;">
                    <ul class="nav nav-tabs">
                        <li role="presentation" data-chart="company_chart" class="active">
                            <a href="javascript:void(0)">
                                <strong class="title text-center"><i class="fa fa-certificate"></i> BXH công ty</strong>
                            </a>
                        </li>
                        <li role="presentation" data-chart="sale_chart" class="">
                            <a href="javascript:void(0)"><strong class="title text-center">BXH Sale</strong></a>
                        </li>
                        <li role="presentation" data-chart="marketing_chart" class="">
                            <a href="javascript:void(0)"><strong class="title text-center">BXH Marketing</strong></a>
                        </li>
                    </ul>

                    <div class="box box-warning box-solid" id="company_chart">
                        <div class="box-header">
                            <h4 class="title text-center"><i class="fa fa-certificate"></i> Bảng xếp hạng công ty</h4>
                        </div>
                        <div class="box-body" style="padding:0px;">
                            <div class="top-wrapper">
                                <div class="aaa">
                                    <div id="comTop3"></div>
                                    <div id="comTop2"></div>
                                    <div id="comTop1"></div>
                                </div>
                            </div>
                            <div id="company_rank">
                                <table class="table">
                                    <thead style="position: sticky; top: 0; z-index: 1000; background: #fff;">
                                        <tr>
                                        <th rowspan="2" class="small text-center">#</th>
                                        <th rowspan="2" class="small text-center">Tên công ty</th>
                                        <th rowspan="2" class="text-center" class="small text-center">SL tài khoản</th>
                                        <!-- <th rowspan="2" class="text-center" class="small text-center">SL NV</th> -->
                                        <th colspan="3" class="small text-center">Tổng</th>
                                        <th colspan="3" class="small text-center">Đơn mới</th>
                                        <th colspan="3" class="small text-center">Đơn tối ưu</th>
                                        <th colspan="3" class="small text-center">Đơn CSKH</th>
                                    </tr>
                                    <tr>
                                        <!-- Tổng -->
                                        <th class="small text-center">Điểm</th>
                                        <th class="small text-center">Đơn</th>
                                        <th class="small text-center">SĐT</th>

                                        <!-- Đơn mới -->
                                        <th class="small text-center">Điểm</th>
                                        <th class="small text-center">Đơn</th>
                                        <th class="small text-center">SĐT</th>

                                        <!-- Tối ưu -->
                                        <th class="small text-center">Điểm</th>
                                        <th class="small text-center">Đơn</th>
                                        <th class="small text-center">SĐT</th>

                                        <!-- Đơn CSKH -->
                                        <th class="small text-center">Điểm</th>
                                        <th class="small text-center">Đơn</th>
                                        <th class="small text-center">SĐT</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div style="display:  flex; width:  100%; justify-content: center; padding-bottom: 10px">
                                <button onclick="loadCompanyChart.bind(this)()" class="btn btn-default btn-lg" type="button">
                                    <span><i class="fa fa-hand-o-right"></i> Xem báo cáo</span>
                                    <loading></loading>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="box box-warning box-solid" style="display: none;" id="sale_chart">
                        <div class="box-header">
                            <h4 class="title text-center">Bảng xếp hạng Sale</h4>
                        </div>
                        <div class="box-body" id="saleRankReport">
                            <table class="table table-striped">
                                <tr>
                                    <th width="1%">#</th>
                                    <th width="39%">Họ và tên</th>
                                    <th width="30%" class="text-right">Điểm</th>
                                    <th width="30%">Công ty</th>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="text-center">
                            <button class="btn btn-default btn-lg" onclick="getUsersByRank.bind(this)('GANDON');">
                                <span><i class="fa fa-hand-o-right"></i> Xem báo cáo</span>
                                <loading></loading>
                            </button>
                        </div>
                    </div>

                    <div class="box box-warning box-solid" style="display: none;" id="marketing_chart">
                        <div class="box-header">
                            <h4 class="title text-center">Bảng xếp hạng Marketing</h4>
                        </div>
                        <div class="box-body" id="mktRankReport">
                            <table class="table table-striped">
                                <tr>
                                    <th width="1%">#</th>
                                    <th width="39%">Họ và tên</th>
                                    <th width="30%" class="text-right">Điểm</th>
                                    <th width="30%">Công ty</th>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="text-center">
                            <button class="btn btn-default btn-lg" onclick="getUsersByRank.bind(this)('MARKETING');">
                                <span><i class="fa fa-hand-o-right"></i> Xem báo cáo</span>
                                <loading></loading>
                            </button>
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

<style type="text/css">
    @keyframes placeHolderShimmer{
        0%{
            background-position: -2000px 0
        }
        100%{
            background-position: 2000px 0
        }
    }

    .row-loading {
        animation-duration: 3s;
        animation-fill-mode: forwards;
        animation-iteration-count: infinite;
        animation-name: placeHolderShimmer;
        animation-timing-function: linear;
        background: #f6f7f800;
        background: linear-gradient(to right, #eeeeee5e 8%, #dddddd 18%, #eeeeee5e 33%);
        background-size: 2000px 104px;
        position: relative;
    }
</style>
<script type="text/javascript">
    const togglePoint = function(){
        if($(this).data('hide')){
            $('.money').removeClass('hidden');
            $(this).data('hide', false);
        }else{
            $(this).data('hide', true);
            $('.money').addClass('hidden');
        }
    }
    /**
     * { function_description }
     *
     * @param      {<type>}  name    The name
     * @return     {<type>}  { description_of_the_return_value }
     */
    const cookies = function(name)
    {
        let cookies = document.cookie.split('; ').reduce((cookies, rawCookie) => {
            return segments = rawCookie.split('='), cookies[segments[0]] = segments[1], cookies
        }, {});

        return cookies[name];
    }

    var blockId = <?php echo Module::block_id(); ?>;
    $(document).ready(function() {
        $('#group_ids').multiselect(
            {
                enableCaseInsensitiveFiltering: true,
                filterPlaceholder: 'Search ...',
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

        ////////////////////////////////////////////
        $('li[role="presentation"]').click(function(event){
            // Hủy kích hoạt tất cả tab khác
            $('li[role="presentation"]').removeClass('active');
            // Kích hoạt tab hiện tại
            $(this).addClass('active');

            // Ẩn tất cả báo cáo
            $('[id$="_chart"]').hide();
            // Hiện báo cáo hiện tại
            $('#' + $(this).data('chart')).show();
        })
    });
    function getUsersByRank($code){
        const BTN = $(this);

        if(BTN.data('loading')){
            return;
        }

        BTN.data('loading', true);
        BTN.find('span').hide();
        BTN.find('loading').css('display', 'block');

        let groupIDs = $('#group_ids option:selected').map((i,e) => e.value).toArray();
        let myData = {
            do:'get_users_by_rank',
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            status_id:$('#status_id').val(),
            code:$code,
            system_group_id:$('#system_group_id').val(),
            group_ids: groupIDs.includes('') ? [] : groupIDs,
            block_id:blockId
        };

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

                $('#toggle-hide-btn').data('hide') ? $('.money').addClass('hidden') : $('.money').removeClass('hidden');
            },
            error: function(){
                alert('Lỗi tải danh sách đơn hàng. Bạn vui lòng kiểm tra lại kết nối!');
                location.reload();
            }
        })
        .always(function(){
            BTN.data('loading', false);
            BTN.find('span').show();
            BTN.find('loading').css('display', '');
        })
    }

    let BTN;
    /**
     * Loads a company chart.
     */
    function loadCompanyChart()
    {
        BTN = $(this);

        if(BTN.data('loading')){
            return;
        }

        openBtnLoading()

        let data = {
            do: 'load_company_chart',
            page: 'dashboard',
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            status_id: $('#status_id').val(),
            system_group_id: $('#system_group_id').val(),
            group_ids: $('#group_ids').val(),
            view_type: $('#view_type').val(),
            block_id:blockId
        };
        $.post('/form.php', data)
        .done(function(data){
            $('#company_rank').html(data);
            $('#toggle-hide-btn').data('hide') ? $('.money').addClass('hidden') : $('.money').removeClass('hidden');

            if(typeof data == 'object' && data.status == 'error'){
                closeBtnLoading();
                return alert(data.message);
            }

            loadCompanyChartData();
        })
        .fail(function(error){
            alert('Tải dữ liệu thất bại !');
        })
        .always(function(){
            // closeBtnLoading();
        })
    }

    // bắt sự kiện lọc hết. bỏ lọc hết shop
    $('body').on('change', '.multiselect-container li:nth-child(2)',function () {
        var getclass = $(this).attr('class');
        if(getclass == 'active'){
            $('#group_ids option').prop('selected', true);
            $("#group_ids").multiselect("refresh");
            // $('.form-inline').submit();
        }
        else {
            $('#group_ids option').prop('selected', false);
            $("#group_ids").multiselect("refresh");
        }

    });

    /**
     * Opens a button loading.
     */
    function openBtnLoading()
    {
        BTN.data('loading', true);
        BTN.find('span').hide();
        BTN.find('loading').css('display', 'block');
    }

    /**
     * Closes a button loading.
     */
    function closeBtnLoading()
    {
        BTN.data('loading', false);
        BTN.find('span').show();
        BTN.find('loading').css('display', '');
    }

    const stacks = [];
    const NUM_WORKERS = 3;
    const NUM_GROUP_IDS = 200;
    let TOTAL;
    let COUNT_WORKER = 0;
    let NUM_TRIES = 0;
    let MAX_TRIES = 10;
    let HAS_ERROR = 0;
    /**
     * Loads a company chart data.
     */
    function loadCompanyChartData()
    {
        HAS_ERROR = 0;
        NUM_TRIES = 0;
        stacks.push.apply(stacks, $('#company-chart-table tr[data-group-id]').map(function(i, e) { return e.dataset.groupId - 0}).get());

        for(COUNT_WORKER = NUM_WORKERS; COUNT_WORKER > 0; COUNT_WORKER--){
            fetchCompanyChartData();
        }

        openBtnLoading();

        [1, 2, 3].map(function(position){
            $('#comTop' + position).text('')
        });

        TOTAL  = {
            ALL: {point: 0, order: 0, phone: 0},
            SOMOI: {point: 0, order: 0, phone: 0},
            TOIUU: {point: 0, order: 0, phone: 0},
            CSKH: {point: 0, order: 0, phone: 0},
        }
    }

    /**
     * Fetches a company chart data.
     *
     * @return     {<type>}  The company chart data.
     */
    function fetchCompanyChartData()
    {
        let ID = stacks.splice(0, NUM_GROUP_IDS);

        if(!ID.length){
            return setTimeout(function(){workerStop()}, 0);
        }

        let data = {
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            status_id: $('#status_id').val(),
            system_group_id: $('#system_group_id').val(),
            group_ids: $('#group_ids').val(),
            view_type: $('#view_type').val(),
            ID: ID
        };
        $.post('/index062019.php?page=dashboard&do=system_dashboard&act=get-group-statistic', data)
        .done(function(data){
            if(typeof data != 'object'){
                return;
            }
            if(data.status == 'error'){
                HAS_ERROR = 1;
                alert(data.message);
            }
            mapTotal(data);
            mapOrder(data);
            mapPhone(data);
        })
        .fail(function(err){
            retry(ID)
        })
        .always(function(){
            fetchCompanyChartData();
        })
    }

    /**
     * Xử lí việc tải lại hay không.
     *
     * @param      {<type>}  ID
     */
    function retry(ID)
    {
        if(++NUM_TRIES > MAX_TRIES){
            NUM_TRIES == (MAX_TRIES+1) && alert(`Đã có lỗi xảy ra trong quá trình nạp dữ liệu báo cáo. Chúng tôi đã thử tải lại ${MAX_TRIES} lần nhưng vẫn thất bại. Vui lòng kiểm tra lại kết nối của bạn hoặc báo cáo cho chúng tôi. Cám ơn bạn.`);
            return;
        }

        stacks.push(ID);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  data    The data
     */
    function mapTotal(data)
    {
        data.total.map(_total => {
            if(!_total['id']){
                return;
            }
            _total.total = _total.total == 'null' ? 0 : parseInt(_total.total) || 0;
            _total.somoi = _total.somoi == 'null' ? 0 : parseInt(_total.somoi) || 0;
            _total.toiuu = _total.toiuu == 'null' ? 0 : parseInt(_total.toiuu) || 0;
            _total.cskh = _total.cskh == 'null' ? 0 : parseInt(_total.cskh) || 0;

            let el = $('#company-chart-table tr[data-group-id=' + _total['id'] + ']');
            el.find('.money_total').text(Number(_total.total/10e5).toLocaleString('en'))
            el.find('.money_total')[0].dataset['originValue'] =  _total.total;
            TOTAL.ALL.point += _total.total;

            el.find('.money_somoi').text(Number(_total.somoi/10e5).toLocaleString('en'))
            el.find('.money_somoi')[0].dataset['originValue'] =  _total.somoi;
            TOTAL.SOMOI.point += _total.somoi;

            el.find('.money_toiuu').text(Number(_total.toiuu/10e5).toLocaleString('en'))
            el.find('.money_toiuu')[0].dataset['originValue'] =  _total.toiuu;
            TOTAL.TOIUU.point += _total.toiuu;

            el.find('.money_cskh').text(Number(_total.cskh/10e5).toLocaleString('en'))
            el.find('.money_cskh')[0].dataset['originValue'] =  _total.cskh;
            TOTAL.CSKH.point += _total.cskh;
        })
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  data    The data
     */
    function mapOrder(data)
    {
        data.order.map(_order => {
            if(!_order['id']){
                return;
            }
            _order.total = _order.total == 'null' ? 0 : parseInt(_order.total) || 0;
            _order.somoi = _order.somoi == 'null' ? 0 : parseInt(_order.somoi) || 0;
            _order.toiuu = _order.toiuu == 'null' ? 0 : parseInt(_order.toiuu) || 0;
            _order.cskh = _order.cskh == 'null' ? 0 : parseInt(_order.cskh) || 0;

            let el = $('#company-chart-table tr[data-group-id=' + _order['id'] + ']');
            el.find('.order').text(Number(_order.total).toLocaleString('en'))
            el.find('.order')[0].dataset['originValue'] =  _order.total;
            TOTAL.ALL.order += _order.total;

            el.find('.order_somoi').text(Number(_order.somoi).toLocaleString('en'))
            el.find('.order_somoi')[0].dataset['originValue'] =  _order.somoi;
            TOTAL.SOMOI.order += _order.somoi;

            el.find('.order_toiuu').text(Number(_order.toiuu).toLocaleString('en'))
            el.find('.order_toiuu')[0].dataset['originValue'] =  _order.toiuu;
            TOTAL.TOIUU.order += _order.toiuu;

            el.find('.order_cskh').text(Number(_order.cskh).toLocaleString('en'))
            el.find('.order_cskh')[0].dataset['originValue'] =  _order.cskh;
            TOTAL.CSKH.order += _order.cskh;
        })
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  data    The data
     */
    function mapPhone(data)
    {
        data.phone.map(_phone => {
            if(!_phone['id']){
                return;
            }
            _phone.total = _phone.total == 'null' ? 0 : parseInt(_phone.total) || 0;
            _phone.somoi = _phone.somoi == 'null' ? 0 : parseInt(_phone.somoi) || 0;
            _phone.cskh = _phone.cskh == 'null' ? 0 : parseInt(_phone.cskh) || 0;

            let el = $('#company-chart-table tr[data-group-id=' + _phone['id'] + ']');
            el.find('.phone').text(Number(_phone.total).toLocaleString('en'));
            el.find('.phone')[0].dataset['originValue'] =  _phone.total;
            TOTAL.ALL.phone += _phone.total;

            el.find('.phone_somoi').text(Number(_phone.somoi).toLocaleString('en'));
            el.find('.phone_somoi')[0].dataset['originValue'] =  _phone.somoi;
            TOTAL.SOMOI.phone += _phone.somoi;

            el.find('.phone_toiuu').text(Number(_phone.toiuu).toLocaleString('en'));
            el.find('.phone_toiuu')[0].dataset['originValue'] =  _phone.toiuu;
            TOTAL.TOIUU.phone += parseInt(_phone.toiuu);

            el.find('.phone_cskh').text(Number(_phone.cskh).toLocaleString('en'));
            el.find('.phone_cskh')[0].dataset['originValue'] =  _phone.cskh;
            TOTAL.CSKH.phone += _phone.cskh;

        })
    }

    /**
     * { function_description }
     */
    function workerStop()
    {
        if(++COUNT_WORKER != NUM_WORKERS || HAS_ERROR){
            closeBtnLoading();
            return;
        }

        tbody = document.querySelector('#company-chart-table tbody');
        const tRows = [].slice.call(tbody.children);
        tbody.innerHTML = '';

        tRows.sort(function(a, b){
            b = b.querySelector('.money_total').dataset['originValue'];
            a = a.querySelector('.money_total').dataset['originValue'];

            a = parseInt(a == 'null' ? 0 : (a ? a : 0)) || 0;
            b = parseInt(b == 'null' ? 0 : (b ? b : 0)) || 0;

            return b - a
        }).map(function(tr, i){
            tr.querySelector('td').innerText = i+1;
            tr.attributes['class'].value = 'row-' + (i+1);
            tbody.appendChild(tr);
        });

        [1, 2, 3].map(function(position){
            if(tRows[position - 1]){
                $('#comTop' + position).text(tRows[position - 1].querySelector('td.group-name').innerText);
            }
        });

        let tFoot = document.querySelector('#company-chart-table tfoot tr');
        setTotal(tFoot, '.money_total', TOTAL.ALL.point, true);
        setTotal(tFoot, '.money_somoi', TOTAL.SOMOI.point, true);
        setTotal(tFoot, '.money_toiuu', TOTAL.TOIUU.point, true);
        setTotal(tFoot, '.money_cskh', TOTAL.CSKH.point, true);

        setTotal(tFoot, '.order', TOTAL.ALL.order, false);
        setTotal(tFoot, '.order_somoi', TOTAL.SOMOI.order, false);
        setTotal(tFoot, '.order_toiuu', TOTAL.TOIUU.order, false);
        setTotal(tFoot, '.order_cskh', TOTAL.CSKH.order, false);

        setTotal(tFoot, '.phone', TOTAL.ALL.phone, false);
        setTotal(tFoot, '.phone_somoi', TOTAL.SOMOI.phone, false);
        setTotal(tFoot, '.phone_toiuu', TOTAL.TOIUU.phone, false);
        setTotal(tFoot, '.phone_cskh', TOTAL.CSKH.phone, false);

        closeBtnLoading();
    }

    /**
     * Sets the total.
     *
     * @param      {<type>}   tfoot      The tfoot
     * @param      {<type>}   className  The class name
     * @param      {number}   num        The new value
     * @param      {boolean}  divide     The divide
     */
    function setTotal(tfoot, className, num, divide)
    {
        tfoot.querySelector(className).innerText = Number(divide ? num/10e5 : num).toLocaleString('en')
    }

    function setRowLoading(IDs)
    {
        // IDs.map(ID => {
        //     document.querySelector('#company-chart-table tr[data-group-id="' + ID + '"]').classList.add('row-loading')
        // })
    }

    function removeRowLoading(IDs)
    {
        // IDs.map(ID => {
        //     document.querySelector('#company-chart-table tr[data-group-id="' + ID + '"]').classList.remove('row-loading')
        // })
    }
</script>
