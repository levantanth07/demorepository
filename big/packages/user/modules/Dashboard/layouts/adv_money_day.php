<?php
$title = 'Báo cáo chi phí quảng cáo theo ngày';
?>
<style type="text/css">
    .normal-text{font-style: normal; color: #333; font-weight: normal}
    .thumb-wrapper {display: flex; }
    .thumb {    width: 80px; height: 45px; overflow: hidden; border-radius: 3px; margin: 0 20px 0 0; justify-content: center; align-items: center; display: flex; box-shadow: 0 0 3px 0 #c1c1c1; background: #f3f3f3;} 
    #product-system-wrapper img {display: block; max-width: 100%; }
    .nav-tabs>li:first-child{ margin-left: 15px; }
    .pagenav {display: flex; align-items: center; }

    .pagenav b {margin: 0 10px; }
    .pagenav .pagination {    flex-grow: 1; justify-content: flex-end; display: flex;}
    td.history-edited i{color: #888}
    td.history-edited p{margin: 0; font-size: 14px}
    th, td{ vertical-align: middle !important; }
    table.table.table-bordered td, table.table.table-bordered th {
        border: 1px solid #f4f4f4;
        border-left: none;
        border-top: none;
        border-right: 1px solid #e2e3e3 !important;
        border-bottom: 1px solid #e2e3e3 !important;
        font-size:  13px;
        padding:  3px;
    }
    thead{
        position: sticky;
        top: 0;
        background: #d1d1d1;
        z-index: 1;
    }
    thead th{
        text-align: center;
    }
    tbody tr td:not([group="day"]):not([group="hour"]):not([group="params"]) {
        text-align: right;
        color:  #000;
    }
    tbody tr:nth-child(odd) td:not([group="day"]):not([group="hour"]):not([group="params"]) {
        background: #f5f5f5;
    }
    tbody tr:hover td:not([group="day"]):not([group="hour"]):not([group="params"]) {
        text-align: right;
    }
    table {
        border-top: 1px solid #e2e3e3 !important;
        border-collapse: separate;
    }
    [group] {position: sticky; background: #d1d1d1;left: 0px;}
    td[group] {background: #fff !important; font-weight: bold;}
    td[group="params"],th[group="params"]{}
    [group="day"] {}
    [group="hour"] {left: 87px;}
    [group="params"] {left: 87px;}
    .table-wraper{max-height: 500px; overflow: auto;}
    .deactive{font-size:  10px; color #000; font-style: italic; font-weight: normal;}
    .username{vertical-align: top !important;}
    [column="sum_group_col"] {    color: #9c0d03 !important; font-weight: bold; text-align: center; }
    [column="sum_team_col"] {color: #000 !important;font-weight: bold; }
    [column="team_col"] {color: #000 !important; }
    [column="user_col"] {color: #000 !important; }
    th.username {background: #d9d9d9; }
</style>
<script src="/packages/core/includes/js/helper.js?v=101020201"></script>
<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">
                <?=$title?> - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-chi-phi-quang-cao-theo-ngay/"
                                 target="_blank" class="btn btn-default"
                                 style="padding: 0px 2px;">
                                <i class="fa fa-question-circle"></i>
                                 Hướng dẫn
                              </a>

            </li>
            <li class="pull-right">
                <div class="pull-right">

                </div>
            </li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xs-12">
            <form method="get" enctype="multipart/form-data" action="/index062019.php">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">Báo cáo chi phí quảng cáo theo <?= (defined('CPQC_FOLLOW') &&  CPQC_FOLLOW === 2) ? 'ngày' : 'khung giờ' ?></strong>
                        <div class="pull-right">
                            <button type="button" class="btn btn-success" id="btnExport" onclick="TableToExcel('ReportTable');">Xuất Excel </button>
                            <!-- <button type="button" class="btn btn-success" id="btnExport" onclick="fnExcelReport('ReportTable');">Xuất Excel </button> -->
                            <button type="button" class="btn btn-default" onclick="printWebPart('reportForm')"><i class="fa fa-print"></i> In báo cáo</button>
                            <a href="<?=Url::build('adv_money',['cmd'=>'add'])?>" class="btn btn-warning"> + Khai báo chi phí QC</a>
                            <!--IF:cond(Dashboard::$admin_group)-->
                            <a href="<?=Url::build('admin_group_info')?>#marketing" class="btn btn-default"> <i class="fa fa-exclamation-triangle"></i> Cài đặt cảnh báo <?= (defined('CPQC_FOLLOW') &&  CPQC_FOLLOW === 2) ? '' : 'và khung giờ' ?></a>
                            <!--/IF:cond-->
                        </div>
                    </div>

                    <div class="panel-body form-inline">
                        <input name="page" type="hidden" value="dashboard">
                        <input name="do" type="hidden" value="adv_money_day">
                        <div class="nav">
                            <div class="form-group">
                                <input type="text" style="width: 120px" name="date_from" id="date_from" placeholder="Từ ngày" class="form-control" value="<?=date('d/m/Y', strtotime(FilterCondition::getTimeFrom()));?>">
                            </div>
                            <div class="form-group">
                                <input type="text" style="width: 120px" name="date_to" id="date_to" placeholder="đến ngày" class="form-control" value="<?=date('d/m/Y', strtotime(FilterCondition::getTimeTo()));?>">
                            </div>

                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="team"></JSHELPER>
                            </div>
                            
                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="user"></JSHELPER>
                            </div>

                            <!--IF:cond(!defined('CPQC_FOLLOW') || CPQC_FOLLOW === 1)-->
                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="select_times"></JSHELPER>
                            </div>
                            <!--/IF:cond-->

                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="is_active"></JSHELPER>
                            </div>

                            <div class="form-group">
                                <select name="bundle_ids[]" id="bundle_ids" multiple="multiple" class="multiple-select-bundle" style="width:120px; display: none;">
                                    [[|bundle_id_list|]]
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="source_ids[]" id="source_ids" multiple="multiple" class="multiple-select-source" style="width:120px; display: none;">
                                    [[|source_id_list|]]
                                </select>
                            </div>

                            <button class="btn btn-primary" type="submit" name="view_report">Xem báo cáo</button>
                        </div>
                    </div>
                    <div id="reportForm">
                    <h2 class="text-center">Báo cáo chi phí quảng cáo theo <?= (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) ? 'ngày' : 'khung giờ' ?></h2>
                    <?php if(!URL::getString('form_block_id')): ?>
                        <div style="display: flex; width: 100%; justify-content: center; padding: 40px; ">Vui lòng nhấn nút <b style="padding: 0 5px;">Xem báo cáo</b></div>
                    <?php elseif(Form::has_flash_message(AdvDayDashboardForm::FLASH_MESSAGE_KEY)):?>
                        <?=Form::draw_flash_message_error(AdvDayDashboardForm::FLASH_MESSAGE_KEY, ['margin' => '15px'])?>
                    <?php else:?>
                    <div class="table-wraper">
                        <!-- Table -->
                        <table class="table table-bordered " id="ReportTable" bordercolor="#999" border="0" cellspacing="0" cellpadding="0">
                            <thead style="background: #d1d1d1">
                                <tr style="background: #d1d1d1">
                                    <th style="border:1px solid #999;" rowspan="2" group="day">
                                        <div style="width: 80px">Ngày</div>
                                    </th>
                                    <!--IF:cond(!defined('CPQC_FOLLOW') || CPQC_FOLLOW === 1)-->
                                    <th style="border:1px solid #999;" rowspan="2" group="hour">
                                        <div style="width: 80px">Giờ</div>
                                    </th>
                                    <!--/IF:cond-->
                                    <th style="border:1px solid #999;" rowspan="2" group="params">
                                        <div style="width: 200px">Thông số</div>
                                    </th>
                                    <th style="border:1px solid #999;" rowspan="2" column="sum_group_col">
                                        <div style="width: 120px">Tổng HKD</div>
                                    </th>

                                    <!-- Loop theo teams -->
                                    <?php foreach ($this->map['display_users_teams'] as $teamID => $userIDs):?>
                                    <?php if(!isset($this->map['teams'][$teamID])): ?>
                                    <th style="border:1px solid #999;" rowspan="2" column="sum_team_col">Tổng không có nhóm</th>
                                    <th style="border:1px solid #999;" colspan="<?=count($userIDs)?>" column="team_col">Không có nhóm</th>
                                    <?php else: ?>
                                    <th style="border:1px solid #999;" rowspan="2" column="sum_team_col">Tổng <?=$this->map['teams'][$teamID]['name']?></th>
                                    <th style="border:1px solid #999;" colspan="<?=count($userIDs)?>" column="team_col">Team <?=$this->map['teams'][$teamID]['name']?></th>
                                    <?php endif; ?>
                                    <!-- /Loop theo teams -->
                                    <?php endforeach; ?>
                                </tr>        
                                <tr style="background: #e2e3e3">
                                    <!-- Loop theo teams -->
                                    <?php foreach ($this->map['display_users_teams'] as $teamID => $userIDs):?>

                                    <!-- Loop theo danh sách user trong teams -->
                                    <?php foreach ($userIDs as $userID):?>

                                    <!-- Render username -->
                                    <th style="border:1px solid #999;" class="username" column="user_col">
                                        <?=$this->map['users'][$userID]['username']?>
                                        <?=$this->map['users'][$userID]['is_active'] ? '' : '<p class="deactive">(Hủy kích hoạt)</p>'?>        
                                    </th>

                                    <!-- /Loop theo danh sách user trong teams -->
                                    <?php endforeach; ?>

                                    <!-- /Loop theo teams -->
                                    <?php endforeach; ?>
                                </tr>                      
                            </thead>
                            <tbody>                       
                            <!-- Loop ngày -->
                            <?php foreach ($this->map['rows'] as $key => $day): ?>

                                <!-- Loop khung giờ -->
                                <?php $i = 0; ?>
                                <?php foreach ($this->map['times'] as $key1 => $time): ?>

                                    <!-- Bỏ qua không hiện các khung giờ được select -->
                                    <?php if(!in_array($time, $this->map['display_select_times'])) continue; ?>
                                    
                                    <!-- Loop thông số -->
                                    <?php $j = 0; ?>
                                    <?php foreach ($this->map['rowname'] as $rownameIndex => $rname): ?>
                                         <tr class="time-block-<?=$time?>">
                                            <!-- Render ngày -->
                                            <?php if($i++ === 0): ?>
                                            <td style="border:1px solid #999;" group="day" rowspan="<?=$this->map['rowspan_date']?>"><?=date('d/m/Y', strtotime($key))?></td>
                                            <?php endif;?>

                                            <!-- Render khung giờ -->
                                            <?php if($j++ === 0 && (!defined('CPQC_FOLLOW') || CPQC_FOLLOW === 1)): ?>
                                            <td style="border:1px solid #999;" group="hour" rowspan="<?=$this->map['rowspan_time']?>"><?=$time?></td>
                                            <?php endif;?>

                                            <!-- Render tên tham số -->
                                            <td style="border:1px solid #999;" group="params"><?=$rname?></td>

                                            <!-- Render cột tổng shop -->
                                            <td style="border:1px solid #999;" column="sum_group_col">
                                                <?=System::display_number($this->map['sum_groups'][$key][$time][$rownameIndex] ?? 0)?>
                                                <?php if($rownameIndex == 'budget_per_revenue'):?>%<?php endif;?>
                                            </td>

                                            <!-- Loop theo teams -->
                                            <?php foreach ($this->map['display_users_teams'] as $teamID => $userIDs):?>

                                            <!-- Render cột tổng team -->
                                            <td style="border:1px solid #999;" column="sum_team_col">
                                                <?=System::display_number($this->map['sum_teams'][$key][$teamID][$time][$rownameIndex] ?? 0)?>
                                                <?php if($rownameIndex == 'budget_per_revenue'):?>%<?php endif;?>
                                            </td>

                                            <!-- Loop theo danh sách user trong teams -->
                                            <?php foreach ($userIDs as $userID):?>

                                            <!-- Render cột thông số của user -->
                                            <td style="border:1px solid #999;">
                                                <?=System::display_number($day[$userID][$time][$rownameIndex] ?? 0)?>
                                                <?php if($rownameIndex == 'budget_per_revenue'):?>%<?php endif;?>
                                            </td>
                                            
                                            <!-- /Loop theo danh sách user trong teams -->
                                            <?php endforeach; ?>

                                            <!-- /Loop theo teams -->
                                            <?php endforeach; ?>
                                        </tr>

                                    <!-- /Loop thông số -->
                                    <?php endforeach;?>

                                <!-- /Loop khung giờ -->
                                <?php endforeach;?>
                            
                            <!-- /Loop ngày -->
                            <?php endforeach; ?>
                            </tbody>

                        <!-- /Table -->
                        </table>
                    </div>
                    <?php endif;?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 
<script type="text/javascript">
    const MODULE_ID = <?=Module::block_id()?>;
    let usersTeams = <?=json_encode($this->map['users_teams'])?> || [];
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.multiple-select-source').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '120px',
            maxHeight: 200,
            nonSelectedText: 'Chọn nguồn',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
        $('.multiple-select-bundle').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '120px',
            maxHeight: 200,
            nonSelectedText: 'Chọn phân loại',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
          
    });

    var TableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table cellspacing="0" rules="rows" border="1" style="color:Black;background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;width:100%;border-collapse:collapse;font-size:9pt;text-align:center;">{table}</table></body></html>'
        , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
            if (navigator.msSaveBlob) {
                var blob = new Blob([format(template, ctx)], { type: 'application/vnd.ms-excel', endings: 'native' });
                navigator.msSaveBlob(blob, 'export.xls')
            } else {
                window.location.href = uri + base64(format(template, ctx))
            }
        }
    })()
    /**
     * Called on mounted team.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    const onMountedTeam = function(el){
        $('#select-team').multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
            onChange: function(option, checked) {
                const val = parseInt(option.val());

                // nếu check vào chọn tất cả team thì select tất cả team và tất cả user
                if(val == 0){
                    $('#select-team').multiselect(checked ? 'selectAll' : 'deselectAll', false)
                    $('#select-team').multiselect(checked ? 'selectAll' : 'deselectAll', false)
                    return;
                }

                // Danh sách các team được chọn
                const teamIDs = $('#select-team').val() || [];
                
                // nếu danh sách team dc chọn có '0' (tất cả team) thì thực hiện chọn tất cả user
                if(teamIDs.includes('0')){
                    return $('#select-user').multiselect('selectAll', false)
                }

                // hủy chọn tất cả user, sau đó tiến hành chọn vào các user thuộc team đã chọn 
                $('#select-user').multiselect('deselectAll', false)
                $('#select-user').multiselect('select', teamIDs.reduce(function(res, teamID){
                    return res.push(...usersTeams[teamID]), res
                }, []));
                
            }
        })
        // Mặc định
        .multiselect('select', <?=json_encode(URL::getArray('teams', [0]))?>);
    }

    /**
     * Called on mounted user.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    const onMountedUser = function(el){
        $('#select-user').multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            onChange: function(option, checked) {
                const val = parseInt(option.val());
                // nếu check vào chọn tất cả user thì select tất cả user
                if(val == 0){
                    $('#select-user').multiselect(checked ? 'selectAll' : 'deselectAll', false)
                }
            }
        })
        // Mặc định
        .multiselect('select', <?=json_encode(URL::getArray('users', [0]))?>);
    }

    /**
     * Called on mounted time.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    const onMountedTime = function(el){
        $('#select-times').multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
            onChange: function(option, checked) {
                const val = parseInt(option.val());
                // nếu check vào chọn tất cả khoảng thì select tất cả khoảng
                if(val == 0){
                    $('#select-times').multiselect(checked ? 'selectAll' : 'deselectAll', false)
                }
            }
        })
        // Mặc định
        .multiselect('select', <?=json_encode(URL::getArray('select_times', [0]))?>)
    }

    JSHELPER.render.select({
        data: {0: 'Tất cả nhóm', ...<?=json_encode($this->map['teams'])?>},
        HTML_COLUMN: 'name',
        selectAttrs: {class: 'form-control', name: "teams[]", multiple: true, id: 'select-team', style: 'display: none'},
    }).mount('#team', null, onMountedTeam); 

    JSHELPER.render.select({
        data: {0: 'Tất cả MKT', ...<?=json_encode($this->map['users'])?>},
        HTML_COLUMN: 'username',
        selectAttrs: {class: 'form-control', name: "users[]", multiple: true, id: 'select-user' , style: 'display: none'},
    }).mount('#user', null, onMountedUser);  

    JSHELPER.render.select({
        data: {0: 'Tất cả khung giờ', ...<?=json_encode($this->map['select_times'])?>},
        selectAttrs: {class: 'form-control', name: "select_times[]", multiple: true, id: 'select-times', style: 'display: none'},
    }).mount('#select_times', null, onMountedTime); 

    JSHELPER.render.select({
        data: {0: 'Tất cả user', 1: 'Đã kích hoạt', 2: 'Hủy kích hoạt'},
        selected: <?=URL::getUInt('is_active', 1)?>,
        selectAttrs: {class: 'form-control', name: "is_active", id: 'is_active'},
    }).mount('#is_active');  
</script>
