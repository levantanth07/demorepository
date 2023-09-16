<?php $title = 'BÁO CÁO CHI PHÍ VẬN CHUYỂN'?>
<style>
    .hide-native-select select{display: none;}
    button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
    i.glyphicon.glyphicon-remove-circle {top: 0px; }
    .tableFixHead tr th { 
        position: sticky; top: 0; z-index: 1; 
    }
    .tableFixHead tr th { 
        position: sticky; top: 0; z-index: 1; 
    }
    table  { 
        border-collapse: collapse; width: 100%; 
    }
    .tableFixHead tr th { 
        background:#DDD; 
    }
    .th-fixed {
        background: rgb(221, 221, 221);
        position: sticky;
        left: -11px;
        top: auto;
        white-space: normal;
        min-width: 150px;
    }
</style>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                        </div>
                        <div class="form-group">
                            <select name="users_ids[]" id="users_ids" multiple="multiple" class="multiple-select-sale" style="width:200px; display: none;">
                                [[|users_ids_option|]]
                            </select>
                        </div>
                        <div class="form-group" style="padding:2px;margin-left: 20px;">
                            <label>Hình thức vận chuyển: </label>
                            <select name="shipping_service_id" id="shipping_service_id" class="form-control"></select>
                        </div>
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        <input type="button" value="In báo cáo" class="btn btn-default" onclick="printWebPart('TransportForm')">
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="col-md-12">
                <div id="TransportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                    <!--IF:report_cond(!empty([[=reports=]]))-->
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;">
                                <h2><?=$title?></h2>
                                <div>Theo hình thức chuyển hàng: [[|shipping_services_name|]] </div>
                                <div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div>
                            </th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                    <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                        
                        <!--LIST:reports-->
                        <!--IF:cond([[=reports.id=]]=='label')-->
                        <thead style="position: sticky; top: 0; z-index: 1;font-weight: bold;">
                            <tr style="font-weight:bold;background:#DDD;">
                                <td rowspan=2>[[|reports.name|]]</td>
                                <!--LIST:status-->
                                <td colspan=3 align="center"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['name'];?></td>
                                <!--/LIST:status-->
                            </tr>
                            <tr style="background:#DDD;">
                                <!--LIST:status-->
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_shipping'];?></td>
                                <!--/LIST:status-->
                            </tr>
                            <tr style="background:#DDD">
                                <td><strong>Tổng</strong></td>
                                <!--LIST:status-->
                                <td align="center" class="col"><strong><?php echo System::display_number([[=status.qty=]]);?></strong></td>
                                <td align="center" class="col"><strong><?php echo System::display_number([[=status.total=]]);?></strong></td>
                                <td align="center" class="col"><strong><?php echo System::display_number([[=status.shipping=]]);?></strong></td>
                                <!--/LIST:status-->
                            </tr>
                        </thead>
                        
                        <!--ELSE-->
                        <tr>
                            <td>[[|reports.name|]]</td>
                            <!--LIST:status-->
                            <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                            <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                            <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_shipping'];?></td>
                            <!--/LIST:status-->
                        </tr>
                        <!--/IF:cond-->
                        <!--/LIST:reports-->
                        
                        
                    </table>
                    </div>
                    <br>
                    <!--ELSE-->
                    <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <!--/IF:report_cond-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
        $('.multiple-select-sale').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Nhân viên MKT',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    });
</script>