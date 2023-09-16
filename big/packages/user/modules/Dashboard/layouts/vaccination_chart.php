<style type="text/css">
#title-report {display: none;}
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
loadding {background: url('http://tuha.big/assets/standard/svgs/loading-black.svg'); background-repeat: no-repeat; background-position: center; background-size: 30px; display: block; width: 100%; height: 100px; }
.error{border-color: red;}
b#system-name {display: block; }
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
                        </div>
                    </div>

                </div>

                <div class="panel-body form-inline" id="report">
                    <h2 id="title-report" class="text-center">Biểu đồ tiêm chủng Covid 19 <b id="system-name"></b></h2>
                    <div class="row">
                        <div class="col-md-6">
                            <figure class="highcharts-figure">
                                <div id="chart-wrapper-count"></div>
                            </figure>
                        </div>
                        <div class="col-md-6">
                            <figure class="highcharts-figure">
                                <div id="chart-wrapper-status"></div>
                            </figure>
                        </div>
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
     * Loads a chart.
     */
    const loadChart = async function(){
        showLoadding();
        try{
            const table = await $.post('/form.php', {
                do: 'vaccination_chart',
                groups: $('#ms-select-groups').val(),
                act: 'load_chart',
                block_id: MODULE_ID
            });

            // Build the chart
            Highcharts.chart('chart-wrapper-count', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Biểu đồ tình trạng tiêm vắc xin covid 19'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.2f} % / {point.num} NV',
                            connectorColor: 'silver'
                        }
                    }
                },
                series: [{
                    name: 'Tiêm chủng COVID',
                    data: table.count.map(e => {
                        e.y = parseFloat(e.y)
                        return e
                    })
                }]
            });

            Highcharts.setOptions({
                colors: ['#24CBE5', '#64E572', '#ff0000', '#ff7700', '#FFF263', '#a163ec', '#cccccc']
            });

            Highcharts.chart('chart-wrapper-status', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Biểu đồ tình trạng sức khỏe'
                },
                tooltip: {
                    pointFormat: '{series.name}:sss <b>{point.percentage:.2f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.2f} % / {point.num} NV',
                            connectorColor: 'silver'
                        }
                    }
                },
                series: [{
                    name: 'Tiêm chủng COVID',
                    data: table.status.map(e => {
                        e.y = parseFloat(e.y)
                        return e
                    })
                }]
            });
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
                $('#chart-wrapper').html('<p class="text-center" style="padding: 40px">Vui lòng chọn shop và bấm vào nút <b>xem báo cáo</b></p>')

                loadGroups(systemIDs)
            }
        })
        .multiselect('select', <?=$this->map['default_system_id']?>, true)
        .multiselect('updateButtonText')

        $('[name="view_report"]').click(function(){
            loadChart();
        })
    });
</script>
