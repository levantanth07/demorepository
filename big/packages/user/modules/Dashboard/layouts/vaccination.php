<style type="text/css">
#table-wraper {max-height: 600px;overflow: auto; }
#table-wraper thead {position: sticky; top: 0; border: #fff; z-index: 300}
#table-wraper thead th {background: #fff; }
div#table-wraper td:nth-child(2) {position: sticky;left: 0px; z-index: 111 }
div#table-wraper th, div#table-wraper td {border-bottom-width: 1px; }
div#table-wraper th{vertical-align: middle; }
div#table-wraper tr:nth-child(odd) td {background: #f6f6f6; }
div#table-wraper tr:nth-child(even) td {background: #fff; }
div#table-wraper table {border-collapse: separate; }
div#table-wraper td, div#table-wraper th {border-color: #d1d1d1; border-left: 0; border-top: 0; text-align:  center}
#title-report {display: none;}
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
loadding {background: url('http://tuha.big/assets/standard/svgs/loading-black.svg'); background-repeat: no-repeat; background-position: center; background-size: 30px; display: block; width: 100%; height: 100px; }
.error{border-color: red;}
b#system-name {display: block; }
div#table-wraper tfoot{position:sticky; bottom: 0px; z-index: 300; }
div#table-wraper tbody td:nth-child(-n+2), div#table-wraper th[rowspan="2"]:nth-child(-n+2), div#table-wraper tfoot td[colspan="2"] {position: sticky; left: 1px; }
div#table-wraper tbody td:nth-child(2), div#table-wraper th[rowspan="2"]:nth-child(2){position: sticky; left: 48px; }
div#table-wraper tfoot td[colspan="2"]{position: sticky; z-index:300;}
div#table-wraper tfoot td{font-weight: bold;}
[col="f0"] {background: red !important; color: #fff; }
[col="f1"] {background: #ff7700 !important; color: #fff; }
</style>

<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading form-inline">
                    <div class="nav">
                        <div class="form-group">
                        <?=$this->systemsSelectbox?>
                        </div>
                        <div class="form-group">
                        <JSHELPER id="select-groups"></JSHELPER>
                        </div>
                        <div class="form-group">
                        <button class="btn btn-primary" type="button" name="view_report">Xem báo cáo</button>
                        <button class="btn btn-success" type="button" name="action_view_report_disable" id="action_view_report_disable">Ẩn %</button>
                        <button class="btn btn-success" type="button" name="action_view_report_full" id="action_view_report_full">Hiển thị %</button>
                        </div>
                    </div>

                </div>

                <div class="panel-body form-inline" id="report">
                    <h2 id="title-report" class="text-center">Báo cáo tiêm chủng Covid 19 <b id="system-name"></b></h2>
                    <div id="table-wraper">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const MAX_SYSTEM_SELECTED = <?=VaccinationForm::MAX_SYSTEM_SELECTED?>;
    const MODULE_ID = <?=Module::block_id()?>;
    let ONLOAD = -1;

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
                do: 'vaccination',
                groups: $('#ms-select-groups').val(),
                act: 'load_report',
                block_id: MODULE_ID
            });

            $('#table-wraper').html(table);
        }catch(e){
            alert('Server Error!')
            console.log(e)
        }
    }
    const loadReportFull = async function(){
        showLoadding();
        try{
            const table_full = await $.post('/form.php', {
                do: 'vaccination',
                groups: $('#ms-select-groups').val(),
                act: 'load_report_full',
                block_id: MODULE_ID
            });

            $('#table-wraper').html(table_full);
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
            do: 'vaccination',
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

    $(document).ready(function() {
        var check = false;
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
             if(<?=URL::getString('chart') === 'true' ? 1 : 0?>){
                loadChart();
             } else {
                // if(check === true){
                //     loadReportFull();
                // } else {
                //     loadReport();
                // }
                loadReport();
             }
        })

        $('#action_view_report_disable').hide()
        $('#action_view_report_disable').on('click',function(e){
            
            $('html body table span').css({"display":"none"});
            $(this).hide();
            check = false;
            $('#action_view_report_full').show();
            // loadReport();
        })
        $('#action_view_report_full').on('click',function(e){
            // loadReportFull();
            check = true;
            $('html body table span').css({"display":"block"});
            //$('html body table span').css({"display":"none"});
            $(this).hide();
            $('#action_view_report_disable').show();
        })
    });
</script>
