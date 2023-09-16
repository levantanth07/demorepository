<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">Báo Cáo Đơn Hàng Theo Post</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">Ngày</span>
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" aria-describedby="basic-addon1">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1"> Post/page</span>
                            <input name="post_id" type=text id="post_id" class="form-control" placeholder="Post ID">
                            <select name="search_page_id" id="search_page_id" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group pull-right">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                            <input type="button" value="In báo cáo" class="btn btn-default" onclick="printWebPart('reportForm')">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="reportForm" class="box-body">
            <table width="100%" border="0">
                <tr>
                    <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                        <div>Điện thoại: [[|phone|]]</div>
                        <div>Địa chỉ: [[|address|]]</div></th>
                    <th width="40%" style="text-align: center;">
                        <h2>Báo Cáo Đơn Hàng Theo Post</h2>
                        <div style="padding:5px;">
                            <!--IF:cond(Url::get('date_from'))-->
                            Từ ngày <?php echo Url::get('date_from');?>
                            <!--/IF:cond-->
                            <!--IF:cond(Url::get('date_to'))-->
                            Đến ngày <?php echo Url::get('date_to');?>
                            <!--/IF:cond-->
                        </div>
                    </th>
                    <th width="30%" style="text-align: right;">
                        <div>Ngày: <?php echo date('d/m/Y')?></div>
                        <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                    </th>
                </tr>
            </table>
            <table width="100%" class="table table-bordered" bordercolor="#000">
                <tbody>
                <?php $total=0;?>
                <!--IF:cond1(sizeof([[=reports=]]) > 1)-->
                <!--LIST:reports-->
                <tr <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#DDD;"':'';?>>
                    <td><a href="https://facebook.com/[[|reports.post_id|]]" target="_blank">[[|reports.post_id|]]</a></td>
                    <td>[[|reports.page_name|]]</td>
                    <!--LIST:status-->
                    <td align="right"><?php echo $this->map['reports']['current'][[[=status.id=]]];?></td>
                    <!--/LIST:status-->
                    <td align="right"><?php $total += intval([[=reports.total=]]);echo ([[=reports.total=]]);?></td>
                </tr>
                <!--/LIST:reports-->
                <!--ELSE-->
                <div class="alert alert-warning-custom text-center">Vui lòng nhấn nút Xem báo cáo</div>
                <!--/IF:cond1-->
                </tbody>
            </table>
            <div style="padding:5px;text-align:right;">Tổng: <strong><?php echo $total;?></strong></div>
            <br>
        </div>
    </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>
</div>