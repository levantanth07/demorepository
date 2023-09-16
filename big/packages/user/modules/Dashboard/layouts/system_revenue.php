
<style>
    @media only screen and (max-width: 500px) {
      .detail {
        max-width: 100px;
      }
    }
    .detail {
        font-weight: normal;
        font-size: 12px;
        color: #f80;
        font-style: italic;
        display: none;
    }
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
        width: 100%;height: 263px;
        background: url('assets/vissale/images/new_cup1.jpg') no-repeat;
        background-size: cover;
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
        border-top: 1px solid #404040;
        padding: 2px;
    }

    #tooltip_ranks {
        display: flex;
        flex-direction: column;
        list-style: none;
        padding: 0;
        margin: 0;
        overflow-y: auto;
        max-height: 55px;
    }

    #tooltip_ranks li{
        display: flex;
        justify-content: left;
        border-bottom: 1px solid #cccccc4d;
    }

    #tooltip_ranks li b{
        margin-right: 15px;
    }

    #tooltip_ranks li i{
        margin-left: auto;
        display: flex;
        text-align: right;
        justify-content: end;
        font-style: normal;
        color: orange;
    }
    .form-group{
        margin-right: 10px;
    }

    tr[system]{
        display: none;
    }

    #loading-box {
        display:  none;
        margin: 10px auto;
        width: 200px;
        padding: 20px;
        text-align: center;
        border-radius: 3px;
        background: #959595 !important;
        color: #fff;
        box-shadow: 0 0 4px 0px #000;
        opacity: 1;
    }
    tbody#systems-report td, thead th {
        border-top: 1px solid #eaeaea;
        border-right: 1px solid #eaeaea;
        padding: 8px !important;
        background: #fff
    }

    thead{
        position: -webkit-sticky;
        position: -moz-sticky;
        position: -o-sticky;
        position: -ms-sticky;
        position: sticky;
        top: 0;
        z-index: 9999;
        background: #fff;
    }

    td:nth-child(2), td:nth-child(1), td:nth-child(3),
    th:nth-child(2), th:nth-child(1), th:nth-child(3){
        position: -webkit-sticky;
        position: -moz-sticky;
        position: -o-sticky;
        position: -ms-sticky;
        position: sticky;
        left: 0;
        z-index: 999;
        background: #fff;
    }

    @media only screen and (max-width: 768px) {
      .form-group, .form-group > input {
          width: 100% !important;
      }
}

</style>

<div class="container full" id="vinhDanhBoard">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">
                Bảng xếp hạng hệ thống - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-xep-hang-he-thong/"
                                            target="_blank"
                                            class="btn btn-default"
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
                        <form name="ReportForm" method="GET" id="filter_system">
                            <input type="hidden" name="page" value="[[|page|]]">
                            <input type="hidden" name="do" value="[[|do|]]">
                            <div class="row box-body">
                                <div class="col-sm-12 form-inline" style="display: flex; flex-wrap: wrap">
                                    <div class="form-group">
                                        <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" value="[[|date_from|]]" />
                                    </div>
                                    <div class="form-group">
                                        <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" value="[[|date_to|]]" />
                                    </div>
                                    <div class="form-group">
                                        <select name="status_id" id="status_id" class="form-control"></select>
                                    </div>
                                    <div class="form-group">
                                        <select name="order_rating" id="order_rating" class="form-control"></select>
                                    </div>
                                    <!-- <div class="form-group">
                                        <select id="order_level" class="form-control">
                                            <option value="0" selected>Sắp xếp theo level</option>
                                            <option value="1">ROOT</option>
                                            <option value="2">CHA</option>
                                            <option value="3">F0</option>
                                            <option value="4">F1</option>
                                            <option value="5">F2</option>
                                            <option value="6">F3</option>
                                            <option value="7">F4</option>
                                            <option value="8">F5</option>
                                            <option value="9">F6</option>
                                        </select>
                                    </div> -->
                                    <div class="form-group">
                                        <?=$this->map['system_group_id']?>
                                        <button type="submit" id="submit_form"  class="btn btn-success"> Xem báo cáo </button>
                                        <img id="loading" style="display: none" src="assets/default/images/lightbox-ico-loading.gif">
                                    </div>
                                    
                                    <div class="form-group ml-auto" style="display: inline-flex;justify-content: flex-end;flex-grow: 1;height: fit-content;">
                                        <button type="button" id="show_note"  class="btn btn-primary" style="margin-right: 10px">Hiện chú thích</button>
                                        <button type="button" id="reset"  class="btn btn-primary" onclick="window.location.href='?page=dashboard&do=system_revenue';">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="box box-default" style="background: url('assets/vissale/images/victory_bg.png');background-position: center;">
                <div class="box-body" style="padding:0px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-warning box-solid">
                                <div class="box-header">
                                    <h4 class="title text-center"><i class="fa fa-certificate"></i> Bảng xếp hạng hệ thống - [[|group_system_name|]]</h4>
                                </div>
                                <div class="box-body" style="padding:0px;max-height: 500px; overflow: auto;">
                                    <table class="table" style="    border-collapse: separate;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%">STT</th>
                                                <th width="5%">CẤP</th>
                                                <th width="70%">Hệ thống</th>
                                                <th width="10%" class="text-right">SL tài khoản</th>
                                                <th width="10%" class="text-right">SL NV</th>
                                                <th width="10%" class="text-right">SĐT</th>
                                                <th width="10%" class="text-right">Điểm</th>
                                                <th width="10%" class="text-right">Điểm/NS</th>
                                            </tr>
                                        </thead>
                                        <tbody id="systems-report">
                                            <?php $i=1;?>
                                            <!--LIST:systems-->
                                            <tr name="system-rows" system="[[|systems.id|]]" groups="[[|systems.groups|]]" filter-rank="1" filter-level="1" class="row-<?=$i;?>" level="[[|systems.level|]]">
                                                <td><?=($i == 1 ? '<i class="fa fa-trophy"></i>' : $i)?></td>
                                                <td>[[|systems.level_name|]]</td>
                                                <td>
                                                    [[|systems.name|]]
                                                    <div class="detail">
                                                        [[|systems.nav|]]
                                                    </div>
                                                </td>
                                                <td class="text-right user">0</td>
                                                <td class="text-right nhanvien">0</td>
                                                <td class="text-right sdt">0</td>
                                                <td class="text-right point">0</td>
                                                <td class="text-right kpi">0</td>
                                            </tr>
                                            <?php $i++;?>
                                            <!--/LIST:systems-->
                                        </tbody>
                                    </table>
                                    <div id="loading-box">
                                        <div class="percent">0%</div>
                                        Đang load dữ liệu
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
    var filter = {
        get_tr: function(){
            return [].slice.call(document.querySelectorAll('tr[name="system-rows"]'));
        },

        by_level: function(level){
            switch (level-0){
                case 0:
                    this.get_tr().map(e => e.setAttribute('filter-level', 1))
                break;

                default:
                    this.get_tr().map(e => e.setAttribute('filter-level', e.getAttribute('level') != level ? 0 : 1))
            }

            return this;
        },

        render: function(){
            this.get_tr().map(function(e) {
                if(e.getAttribute('filter-level') == 1)
                    return e.style.display = '';
                e.style.display = 'none';
            })
        }
    }
    $(document).ready(function() {
        $('#system_group_id').multiselect(
            {
                enableFiltering:true,
                buttonWidth: '120px',
                maxHeight: 200
            }
        );
        $('#filter_system').submit(function() {
            $('#submit_form').prop("disabled",true);
            $('#loading').show();
            return true;
        });
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.box-google-keep').click(function() {
            $('#noteModal .modal-body').empty().html($(this).find('.text-ellipsis').html())

            $('#noteModal').modal('show')
        })

        $('#show_note').click(function(e){
            if(e.target.getAttribute('showing')){
                e.target.removeAttribute('showing');
                $('.detail').hide();
            }else{
                e.target.setAttribute('showing', 1);
                $('.detail').show();
            }
        });

        

        let GROUPS = {};
        let BLOCK_GROUP_IDS_SIZE = 100;
        let length = $('tr[system]').length;
        let GROUP_IDS = [[|groupIDs|]];
        let IS_LOADING = false;

        async function init(){
            try{
                $('#order_level').change(e => {
                    console.log(e);
                    sortByLevel(e)
                })
                $('#order_rating').change(e => {
                    console.log(e);
                    sortSystem();
                    showSystem();
                })
                $('#system_group_id').change(e => showSystem())
                await fetchGroups();
                $('tr[system]').map((i, el) => renderSystem(el))
                sortSystem();
                showSystem();
            }catch(e){
                console.log(e)
                alert('Lỗi: Vui lòng thử lại !');
            }
        }
        init();

        /**
         * { function_description }
         *
         * @param      {<type>}   el      { parameter_description }
         * @return     {Promise}  { description_of_the_return_value }
         */
        function renderSystem(el)
        {   
            if(IS_LOADING) return;

            let statistics = el.getAttribute('groups')
                .split(',')
                .reduce((res, ID) => {
                    if(!GROUPS[ID]){
                        return res;
                    }
                    res.point += ID ? parseInt(GROUPS[ID].revenue) : 0;
                    res.user += ID ? parseInt(GROUPS[ID].user_quantity) : 0;
                    res.nhanvien += ID ? parseInt(GROUPS[ID].user_parent_quantity) : 0;
                    res.sdt += ID ? parseInt(GROUPS[ID].phone_quantity) : 0;

                    return res;
                }, {point: 0, user: 0, sdt: 0, nhanvien: 0});
            el.revenue = statistics.point;
            el.querySelector('.point').innerHTML = Number(statistics.point/10e5).toLocaleString('en')
            el.querySelector('.user').innerHTML = Number(statistics.user).toLocaleString('en')
            el.querySelector('.nhanvien').innerHTML = Number(statistics.nhanvien).toLocaleString('en')
            el.querySelector('.sdt').innerHTML = Number(statistics.sdt).toLocaleString('en')
            el.querySelector('.kpi').innerHTML = statistics.user ? Number(statistics.point/10e5/statistics.user).toLocaleString('en') : 0
        }

        /**
         * { function_description }
         */
        function sortSystem()
        {   
            if(IS_LOADING) return;
            
            let sortType = $('#order_rating').val();
            tbody = document.querySelector('#systems-report');
            const tRows = [].slice.call(tbody.children);
            tbody.innerHTML = '';
            
            tRows.sort(function(a, b){
                return sortType == 'desc' ? b.revenue - a.revenue : a.revenue - b.revenue
            })
            .map(function(tr, i){
                tr.querySelector('td').innerText = i+1;
                tr.attributes['class'].value = 'row-' + (i+1);
                tbody.appendChild(tr);
            });
        }

        function sortByLevel(selectBox)
        {   
            if(IS_LOADING) return;

            let level = $('#order_rating').val();
            tbody = document.querySelector('#systems-report');
            const tRows = [].slice.call(tbody.children);
            tbody.innerHTML = '';
            
            tRows.sort(function(a, b){
                return level = 'desc' ? b.revenue - a.revenue : a.revenue - b.revenue
            })
            .map(function(tr, i){
                tr.querySelector('td').innerText = i+1;
                tr.attributes['class'].value = 'row-' + (i+1);
                tbody.appendChild(tr);
            });
        }

        /**
         * Shows the system.
         */
        function showSystem()
        {   
            let selected = $('#system_group_id').val();
            if(IS_LOADING || !selected) return;

            selected = selected.map(e => parseInt(e));
            [].slice.call(document.querySelectorAll('#systems-report tr'))
            .map(function(el){
                if(selected.indexOf(0) != -1 || selected.indexOf(parseInt(el.getAttribute('system'))) != -1){
                    el.style.display = 'table-row';
                }else{
                    el.style.display = 'none';
                }
            })
        }

        /**
         * Fetches groups.
         *
         * @return     {Promise}  The groups.
         */
        async function fetchGroups()
        {   
            let fetched = 0, total = GROUP_IDS.length;
            $('#loading-box').show();
            $('#loading-box .percent').text('0%');
            IS_LOADING = true;

            while(true){
                try{
                    let groupIDs = GROUP_IDS.splice(0, BLOCK_GROUP_IDS_SIZE);
                    if(!groupIDs.length){
                        break;
                    }

                    fetched += groupIDs.length
                    
                    let url =location.href, 
                        data = {action: "get_group_revenue", group_ids: groupIDs.join(',')};
                        results = await $.post(url, data);

                    GROUPS = Object.assign(GROUPS, results.groups);//{...GROUPS, ...results.groups};

                    $('#loading-box .percent').text(Math.ceil(fetched *100 / total) + "%");
                }catch(e){
                    console.log(e);
                    break;
                }
            }

            IS_LOADING = false;

            $('#loading-box').hide('slow');
        }
    });
</script>