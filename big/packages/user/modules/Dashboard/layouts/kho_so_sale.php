<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php
    $title = 'Báo cáo kho số Sale';
    $items = [[=reports=]];
?>
<style>
    .hide-native-select select{display: none;}
    button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
    i.glyphicon.glyphicon-remove-circle {top: 0px; }
    .tableFixHead { 
        overflow: auto; height: 100px; 
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
<div class="container full report">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">TUHA</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    <?=Portal::get_setting('ads_text_link');?>
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-xs-10">
                        <div class="form-group">
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <JSHELPER id="team"></JSHELPER>
                        </div>
                        <div class="form-group">
                            <select name="type" id="type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="users_ids[]" id="users_ids" multiple="multiple" class="multiple-select" style="width:150px; display: none;">
                                [[|users_ids_option|]]
                            </select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="assigned_type" id="assigned_type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control" required oninvalid="this.setCustomValidity('Bạn vui lòng chọn công ty.')" oninput="setCustomValidity('')"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>
                    </div>
                    <div class="col-xs-2 text-right">
                        <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default btn-lg"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12" id="reportForm">
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;"><h2><?=$title?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <?php if(!empty($items)): ?>
                    <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                    <table width="100%" class="table table-bordered tableFixHead" border="1" cellpadding="5" cellspacing="0">
                        <thead style="position: sticky; top: 0; z-index: 1">
                            <tr style="font-weight:bold;background:#5cbdf5;color:#fff;text-align: center;">
                                <td>Tên tài khoản</td>
                                <td>Họ và tên</td>
                                <td>% Chốt <span title="% chốt = số chốt/số được chia" class="fa fa-question-circle"></span></td>
                                <td>% Chốt thực <span title="% chốt thực = số chốt / số được chia tiếp cận được" class="fa fa-question-circle"></span></td>
                                <td>% Kết nối <span title="% kết nối = số tiếp cận được / số được chia" class="fa fa-question-circle"></span></td>
                                <td>Số chia</td>
                                <td>Đã gọi</td>
                                <td>Số hủy</td>
                                <td>Số tiếp cận</td>
                                <td>Chốt</td>
                                <td>Tồn</td>
                                <td>Doanh thu</td>
                            </tr>
                        </thead>
                        <tbody> 
                            <?php
                                $total_doanh_thu = 0;
                                $total_so_ton = 0;
                                $total_so_chot = 0; 
                                $total_so_tiep_can = 0;
                                $total_so_chia_tiep_can_duoc = 0;
                                $total_so_huy = 0; 
                                $total_so_da_goi = 0; 
                                $total_so_chia = 0;
                            ?>
                            <?php foreach($items as $key => $value) : ?>
                                <?php
                                    $total_doanh_thu += $value['doanh_thu'];
                                    $total_so_ton += $value['so_ton'];
                                    $total_so_chot += $value['so_chot'];
                                    $total_so_tiep_can += $value['so_tiep_can'];
                                    $total_so_huy += $value['so_huy'];
                                    $total_so_da_goi += $value['so_da_goi'];
                                    $total_so_chia += $value['so_chia'];
                                    $total_so_chia_tiep_can_duoc += $value['so_chia_tiep_can_duoc'];
                                ?>
                                <tr class="text-center">
                                    <td style="position: sticky; left: 0; background: #CCC;font-weight: bold" class="text-center"><?php echo $value['username'] ?></td>
                                    <td><?php echo $value['name'] ?></td>
                                    <td align="center">
                                        <div class="bar-wrapper" style="background: #efefef;width: 100%;">
                                            <div class="bar" style="background: <?php echo $value['ty_le_chot_color'] ?>;width: <?php echo $value['ty_le_chot'] > 100 ? 100 : $value['ty_le_chot'] ?>%">
                                                <div style="z-index: 100;"><?php echo $value['ty_le_chot'] ? $value['ty_le_chot'].'%':'';?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="bar-wrapper" style="background: #efefef;width: 100%;">
                                            <div class="bar" style="background: <?php echo $value['ty_le_chot_thuc_color'] ?>;width: <?php echo $value['ty_le_chot_thuc'] > 100 ? 100 : $value['ty_le_chot_thuc'] ?>%">
                                                <div style="z-index: 100;"><?php echo $value['ty_le_chot_thuc'] ? $value['ty_le_chot_thuc'].'%':'';?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="center">
                                        <div class="bar-wrapper" style="background: #efefef;width: 100%;">
                                            <div class="bar" style="background: <?php echo $value['ty_le_ket_noi_color'] ?>;width: <?php echo $value['ty_le_ket_noi'] > 100 ? 100 : $value['ty_le_ket_noi'] ?>%">
                                                <div style="z-index: 100;"><?php echo $value['ty_le_ket_noi'] ? $value['ty_le_ket_noi'].'%':'';?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $value['so_chia'] ?>
                                    </td>
                                    <td><?php echo $value['so_da_goi'] ?></td>
                                    <td><?php echo $value['so_huy'] ?></td>
                                    <td><?php echo $value['so_tiep_can'] ?></td>
                                    <td><?php echo $value['so_chot'] ?></td>
                                    <td><?php echo $value['so_ton'] ?></td>
                                    <td><?php echo System::display_number($value['doanh_thu']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot style="position: sticky; bottom: 0; background: #fff; font-weight: bold;">
                            <tr style="position: sticky; left: 0; background: #DDD;font-weight: bold" class="text-center">
                                <td colspan="2">Tổng</td>
                                <td><?php echo $total_so_chia > 0 ? (round($total_so_chot/$total_so_chia,2)*100) : 0  ?> %</td>
                                <td><?php echo $total_so_chia_tiep_can_duoc > 0 ? (round($total_so_chot/$total_so_chia_tiep_can_duoc,2)*100) : 0  ?> %</td>
                                <td><?php echo $total_so_chia > 0 ? (round($total_so_tiep_can/$total_so_chia,2)*100) : 0  ?> %</td>
                                <td><?php echo $total_so_chia; ?></td>
                                <td><?php echo $total_so_da_goi; ?></td>
                                <td><?php echo $total_so_huy; ?></td>
                                <td><?php echo $total_so_tiep_can; ?></td>
                                <td><?php echo $total_so_chot; ?></td>
                                <td><?php echo $total_so_ton; ?></td>
                                <td><?php echo System::display_number($total_doanh_thu); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                    <?php else: ?>
                    <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
        
        /**
         * Called on mounted bundle.
         *
         * @param      {<type>}  el      { parameter_description }
         */
        const onMountedBundle = function(el){
            const select = $(el);

            select.multiselect({
                buttonWidth: '150px',
                maxHeight: 200,
                onChange: function(option, checked) {
                    const val = parseInt(option.val());

                    if(val) return;
                    
                    select.multiselect(checked ? 'selectAll' : 'deselectAll', false);
                }
            })
            // Mặc định
            .multiselect('select', <?=json_encode(URL::getArray('bundle_id', [0]))?>);
        } 
        
        JSHELPER.render.select({
            data: {0: 'Tất cả phân loại', ...<?=json_encode($this->map['bundle_id'])?>},
            HTML_COLUMN: 'name',
            selectAttrs: {class: 'form-control', name: "bundle_id[]", multiple: true, id: 'select-bundle', style: 'display: none'},
        }).mount('#team', null, onMountedBundle);
    });
    $(document).ready(function(){
        // $('.multiple-select').multipleSelect({
        //   filter: true,
        //   placeholder: 'Chọn nhân viên',
        //   filterPlaceholder: 'Nhân viên'
        // });
        // $('.multiple-select ul li.ms-select-all span').html('Chọn tất cả');
        $('.multiple-select').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Nhân viên Sale',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
        $('.multiple-select ul li.ms-select-all span').html('Chọn tất cả');
    })
</script>