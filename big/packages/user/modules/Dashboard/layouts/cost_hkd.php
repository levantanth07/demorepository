<style type="text/css">
#table-wraper {overflow: auto; }
div#table-wraper td:nth-child(-n + 3), div#table-wraper th:nth-child(-n + 3) {position: sticky; left: 1px; }
div#table-wraper td:nth-child(2), div#table-wraper th:nth-child(2) {left: 78px; }
div#table-wraper td:nth-child(3), div#table-wraper th:nth-child(3) {left: 215px; }
div#table-wraper td:nth-child(3) {left: 215px; }
div#table-wraper td:nth-child(2) {font-weight: bold;}
.hilight-cell{background: #67c69e !important; font-weight: bold;}
div#table-wraper th {background: #037db4; color: #fff }
div#table-wraper tr:nth-child(odd) td {background: #f6f6f6; }
div#table-wraper tr:nth-child(even) td {background: #fff; }
div#table-wraper table {border-collapse: separate; }
div#table-wraper td, div#table-wraper th {border-color: #d1d1d1; border-left: 0; border-top: 0;}
#title-report {display: none;}
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
loadding {background: url('http://tuha.big/assets/standard/svgs/loading-black.svg'); background-repeat: no-repeat; background-position: center; background-size: 30px; display: block; width: 100%; height: 100px; }
.error{border-color: red;}
@-webkit-keyframes rotating {from {-webkit-transform: rotate(0deg); -o-transform: rotate(0deg); transform: rotate(0deg); } to {-webkit-transform: rotate(360deg); -o-transform: rotate(360deg); transform: rotate(360deg); }
}
@keyframes rotating {from {-ms-transform: rotate(0deg); -moz-transform: rotate(0deg); -webkit-transform: rotate(0deg); -o-transform: rotate(0deg); transform: rotate(0deg); } to {-ms-transform: rotate(360deg); -moz-transform: rotate(360deg); -webkit-transform: rotate(360deg); -o-transform: rotate(360deg); transform: rotate(360deg); } }
.rotating {-webkit-animation: rotating 1s linear infinite; -moz-animation: rotating 1s linear infinite; -ms-animation: rotating 1s linear infinite; -o-animation: rotating 1s linear infinite; animation: rotating 1s linear infinite; }
</style>

<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading form-inline">
                    <div class="nav">
                        <div class="form-group">
                            <input type="text" id="date_from" name="date_from" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <input type="text" id="date_to" name="date_to" class="form-control" />
                        </div>
                        <!-- <div class="form-group">
                        <?=$this->systemsSelectbox?>
                        </div>
                        <div class="form-group">
                        <JSHELPER id="select-groups"></JSHELPER>
                        </div> -->
                        <div class="form-group">
                        <button class="btn btn-primary" type="button" name="view_report">Xem báo cáo</button>
                        </div>
                        <div class="form-group pull-right">
                            <button type="button" class="btn btn-success" id="export-report">Xuất excel</button>
                            <button type="button" class="btn btn-default" id="print-report"><i class="fa fa-print"></i> In Báo cáo</button>
                            <!-- <button type="button" class="btn btn-warning" id="update-data">
                                <svg style="height: 12px; vertical-align: middle;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sync" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-sync fa-w-16 fa-2x"><path fill="#fff" d="M440.65 12.57l4 82.77A247.16 247.16 0 0 0 255.83 8C134.73 8 33.91 94.92 12.29 209.82A12 12 0 0 0 24.09 224h49.05a12 12 0 0 0 11.67-9.26 175.91 175.91 0 0 1 317-56.94l-101.46-4.86a12 12 0 0 0-12.57 12v47.41a12 12 0 0 0 12 12H500a12 12 0 0 0 12-12V12a12 12 0 0 0-12-12h-47.37a12 12 0 0 0-11.98 12.57zM255.83 432a175.61 175.61 0 0 1-146-77.8l101.8 4.87a12 12 0 0 0 12.57-12v-47.4a12 12 0 0 0-12-12H12a12 12 0 0 0-12 12V500a12 12 0 0 0 12 12h47.35a12 12 0 0 0 12-12.6l-4.15-82.57A247.17 247.17 0 0 0 255.83 504c121.11 0 221.93-86.92 243.55-201.82a12 12 0 0 0-11.8-14.18h-49.05a12 12 0 0 0-11.67 9.26A175.86 175.86 0 0 1 255.83 432z" class=""></path></svg>
                                 <text>Cập nhật số liệu</text> 
                                <a href="javascript:void(0);" style="color: black" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Cập nhật lại số liệu mới nhất">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </button> -->
                            <!-- <a href="<?=Url::build('admin_group_info', ['do' => 'cost_declaration', 'act' => 'add'])?>" class="btn btn-warning"> + Khai báo chi phí</a> -->
                        </div>
                        <div style="clear: both"></div>
                        <div style="padding-top: 10px;font-size:11px;color:#ff851b">
                            Chú thích:<br>
                            - Số liệu được cập nhật theo ngày vào 12h và 24h
                        </div>
                    </div>

                </div>

                <div class="panel-body form-inline" id="report">
                    <style type="text/css">
                        @media print {
                            #title-report{text-align: center !important;}
                            .text-center {text-align: center !important; }
                        }
                    </style>
                    <h2 class="text-center">Báo cáo Tỷ lệ doanh thu ước chừng HKD</h2>
                    <center id="d" style="display: none; margin-bottom: 15px;"><b>Từ ngày <span id="d_from"></span> đến ngày <span id="d_to"></span></b></center>
                    <div id="table-wraper">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const MAX_SYSTEM_SELECTED = <?=CostHkdForm::MAX_SYSTEM_SELECTED?>;
    const MODULE_ID = <?=Module::block_id()?>;
    let ONLOAD = -1;

    const MONTH_EL = $('select[name="month"]')
    const YEAR_EL = $('select[name="year"]')

    MONTH_EL.val(new Date().getMonth() + 1)
    YEAR_EL.val(new Date().getFullYear())

    /**
     * Loads groups.
     *
     * @param      {<type>}  systemID  The system id
     */
    const loadGroups = async function(systemID){
        try{
            $("#ms-select-groups").multiselect('destroy');
            const response = await fetchGroupsBySystemID(systemID);
            const rawSelectGroup = JSHELPER.render.select({
                data: {0: 'Tất cả nhóm', ...response},
                HTML_COLUMN: 'name',
                selectAttrs: {
                    class: 'form-control d-none',
                    name: "select-groups[]",
                    multiple: true,
                    id: 'ms-select-groups',
                    style: 'display: none'
                },
            })._mountHTML

            $('JSHELPER#select-groups').html(rawSelectGroup);

            onMountedSelectGroups()

        }catch(e){
            console.log(e)
        }
    }

    /**
     * Shows the loadding.
     */
    const showLoadding = function()
    {
        $('#table-wraper').html('<loadding></loading>')
    }

    /**
     * Loads a report.
     *
     * @return     {<type>}  { description_of_the_return_value }
     */
    const loadReport = async function(){
        showLoadding();
        try{
            const table = await $.post('/form.php', {
                do: 'cost_hkd',
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                groups: $('#ms-select-groups').val(),
                act: 'load_report_hkd',
                block_id: MODULE_ID
            });

            $('#table-wraper').html(table);

            let date = new Date();
            let currentDate = date.getDate() + '/' + (date.getMonth() + 1);
            let dayIndex = [].slice.call(document.querySelectorAll('th')).reduce(function(o, e, i){
                return e.innerText == currentDate && (o = i), o
            }, null)
            document.querySelectorAll('td:nth-child(' + (dayIndex + 1) + ')').forEach(e => e.classList.add('hilight-cell'))
        }catch(e){
            alert('Server Error!')
            console.log(e)
        }
    }

    /**
     * Fetches a groups by system id.
     *
     * @param      {<type>}  systemID  The system id
     * @return     {<type>}  The groups by system id.
     */
    const fetchGroupsBySystemID = async function(systemID){
        return await $.post('/form.php', {
            do: 'cost_hkd',
            systemID: systemID,
            act: 'load_groups_by_system_id',
            block_id: MODULE_ID
        })
    }

    /**
     * Called on mounted team.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    const onMountedSelectGroups = function(el){
        $('#ms-select-groups').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            onChange: function(option, checked) {
                const val = parseInt(option.val());

                if(val == 0){
                    return $('#ms-select-groups').multiselect(checked ? 'selectAll' : 'deselectAll', false).multiselect('updateButtonText')
                }
            }
        })

        // Tự động select hết
        $('#ms-select-groups')
            .multiselect('selectAll', false)
            .multiselect('updateButtonText')
    }

    /**
     * { function_description }
     */
    const exportEport = async function()
    {   
        try{
            const data = {
                do: 'cost_hkd',
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                groups: '',
                act: 'export_report_hkd',
                block_id: MODULE_ID
            };
            
            const body = Object.entries(data).reduce(function(res, e){
                const [key, val] = e;
                
                if(!Array.isArray(val)){
                    res.push(encodeURIComponent(key) + '=' + encodeURIComponent(val));
                }else{
                    res.push(...(val.map(e => encodeURIComponent(key) + '[]=' + encodeURIComponent(e))))
                }

                return res;
            }, []).join('&')

            const response = await fetch('/form.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                },
                body: body
            })
            const blob = await response.blob();

            var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Báo cáo doanh thu tỉ lệ ' + $('#system-name').text() + '.xlsx';
                link.click();
        }catch(e){
            alert('Server Error!')
            console.log(e)
        }
    }

    /**
     * Prints a report.
     */
    const printReport = function()
    {   
        const TABLE = $("#report");
        if(!TABLE){
            return;
        }

        const newWindown = window.open("");
        newWindown.document.write(TABLE[0].outerHTML);
        newWindown.print();
        newWindown.close();
    }

    let DATE_FROM = moment('<?=$this->map['date_from']?>');
    let DATE_TO = moment('<?=$this->map['date_to']?>');
    /**
     * { function_description }
     *
     * @param      {<type>}  e       { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    const dateFromChange = function(e)
    {   
        DATE_FROM = e.date;

        return validateDateTime();
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  e       { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    const dateToChange = function(e)
    {
        DATE_TO = e.date;

        return validateDateTime();
    }

    /**
     * { function_description }
     *
     * @param      {boolean}  bool    The bool
     */
    const dateUI = function(bool)
    {
        $('#date_from')[bool ? 'removeClass' : 'addClass']('error')
        $('#date_to')[bool ? 'removeClass' : 'addClass']('error')

        $('[name="view_report"]').prop('disabled', !bool);
        $('#update-data').prop('disabled', !bool);
    }

    /**
     * { function_description }
     */
    const validateDateTime = function()
    { 
        if(DATE_TO.format('MM') - DATE_FROM.format('MM')){
            alert('Vui lòng chọn khoảng thời gian trong 1 tháng !');
            return dateUI(false);
        }

        if(DATE_TO.isBefore(DATE_FROM)){
            alert('Thời gian bắt đầu và kết thúc không hợp lệ !');
            return dateUI(false);
        }

        return dateUI(true);
    }

    $(document).ready(function() {
        $('#date_from').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: DATE_FROM
        }).on('dp.change', dateFromChange);

        $('#date_to').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: DATE_TO
        }).on('dp.change', dateToChange);

        $('#system_group_id').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            enableClickableOptGroups : true, 
            onChange: function(option, checked) {
                const systemIDs = $('#system_group_id').val().map(e => parseInt(e) || 0)

                if(systemIDs.length > MAX_SYSTEM_SELECTED){
                    alert('Vui lòng chọn tối đa ' + MAX_SYSTEM_SELECTED + ' hệ thống !');
                    return;
                }

                // Hiện title
                $('#title-report').show()
                let systemNames = $('#system_group_id option:selected')
                    .get()
                    .map(e => e.innerText.replace(/\s*--\s*/g, ''))
                    .join(',')
                $('#system-name').text(systemNames)

                // Xóa table
                $('#table-wraper').html('<p class="text-center" style="padding: 40px">Vui lòng chọn shop và bấm vào nút <b>xem báo cáo</b></p>')

                loadGroups(systemIDs)
            }
        })
        .multiselect('select', <?=$this->map['default_system_id']?>, true)
        .multiselect('updateButtonText')

        $('[name="view_report"]').click(function(){
            $('#d_from').text(DATE_FROM.format('DD/MM/20YY'));
            $('#d_to').text(DATE_TO.format('DD/MM/20YY'));
            $('#d').show();

            loadReport();
        })

        $('#export-report').click(exportEport)

        $('#print-report').click(printReport)

        // Cap nhat du lieu
        $('#update-data').click(async function(e){
            const old = $(this).find('text').text();
            $(this).find('text').text('Xin chờ giây lát');
            this.querySelector('svg').classList.add('rotating');

            const response = await $.post('/form.php', {
                    do: 'cost_hkd',
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    act: 're_cache_hkd',
                    block_id: MODULE_ID
                });

            $(this).find('text').text(old);
            this.querySelector('svg').classList.remove('rotating');

            if(typeof response != 'object'){
                return alert('Server Error !');
            }

            switch(response.status){
                case 'error':
                    if(response.message === 'THOI_GIAN_KHONG_HOP_LE'){
                        return alert('Thời gian không hợp lệ')
                    }

                    return alert('Dữ liệu mới được cập nhật. Vui lòng thao tác lại lúc ' + response.message);

                case 'success':
                    loadReport();
                    return alert('Cập nhật dữ liệu hoàn tất. Vui lòng bấm nút xem báo cáo để xem dữ liệu mới nhất');

                default:
                    return alert('Unknow Error !');
            }
        })
    });


</script>
