<style type="text/css">
    .sidebar-mini.sidebar-collapse .content-wrapper, .sidebar-mini.sidebar-collapse .right-side, .sidebar-mini.sidebar-collapse .main-footer {
        margin-left: 0px !important;
        z-index: 840;
    }
    .wrapper{
        height: auto !important; 
    }
</style>
<section class="wrapper">
    <h1 class="charts-title">BẢNG XẾP HẠNG VINH DANH</h1>
    <h3 class="charts-note">(<?php echo date('d/m/Y'); ?>)</h3>
    <div class="content" id="content">

    </div>
</section>
<script type="text/javascript">
    var loadReport = function loadData(){
                    $.ajax({
                        url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                        type: "POST",
                        cache: false,
                        data : {
                            'do':'get_data_report_day',
                        },
                        success: function(data){
                            $('#content').html(data); 
                        }
                    });
                }
    loadReport();
    setInterval(loadReport, 300000);
</script>