<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php
    $title = 'Báo cáo kho số '.([[=sale=]]?'sale':'Marketing');
?>
<style>
.hide-native-select select{display: none;}
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
</style>
<div class="container full report">
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
                            <select name="users_ids[]" id="users_ids" multiple="multiple" class="multiple-select" style="width:200px;display: none">
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
                    <!--IF:cond(!empty([[=reports=]]))-->
                    <table width="100%" class="table table-bordered" bordercolor="#000" border="1" cellpadding="5" cellspacing="0">
                        <tbody>
                        <!--LIST:reports-->
                        <tr align="center" <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#5cbdf5;color:#fff;"':'';?>>
                            <td>[[|reports.username|]]</td>
                            <td>[[|reports.name|]]</td>
                            <!--IF:cond_(![[=sale=]])-->
                            <td>[[|reports.so_cap_moi|]]</td>
                            <td>[[|reports.so_cap_cu|]]</td>
                            <td>
                                <?php if([[=reports.id=]] <> 'label'){?>
                                <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: #7fff00; width:<?=(([[=so_cap_max=]]>0)?(([[=reports.so_cap=]]/[[=so_cap_max=]])*100):'0')?>%;"><div style="z-index: 100;">[[|reports.so_cap|]]</div></div></div>
                                <?php }else{?>
                                [[|reports.so_cap|]]
                                <?php }?>
                            </td>
                            <!--/IF:cond_-->
                            <td class="text-center" style="overflow: hidden">
                                <?php if([[=reports.id=]] <> 'label'){?>
                                <div class="bar-wrapper" style="background: #efefef;width: 100%;"><div class="bar" style="background: [[|reports.ty_le_chot_color|]];width: <?=([[=reports.ty_le_chot=]]>100?100:[[=reports.ty_le_chot=]])?>%"><div style="z-index: 100;"><?=[[=reports.ty_le_chot=]]?[[=reports.ty_le_chot=]].'%':'';?></div></div></div>
                                <?php }else{?>
                                [[|reports.ty_le_chot|]]
                                <?php }?>
                            </td>
                            <!--IF:s_cond([[=sale=]])-->
                            <td class="text-center" style="overflow: hidden">
                                <?php if([[=reports.id=]] <> 'label'){?>
                                <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: [[|reports.ty_le_chot_thuc_color|]];width: <?=([[=reports.ty_le_chot_thuc=]]>100?100:[[=reports.ty_le_chot_thuc=]])?>%"><div style="z-index: 100;"><?=[[=reports.ty_le_chot_thuc=]]?[[=reports.ty_le_chot_thuc=]].'%':'';?></div></div></div>
                                <?php }else{?>
                                [[|reports.ty_le_chot_thuc|]]
                                <?php }?>
                            </td>
                            <!--/IF:s_cond-->
                            <td class="text-center" style="overflow: hidden">
                                <?php if([[=reports.id=]] <> 'label'){?>
                                <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: [[|reports.ty_le_ket_noi_color|]];width: <?=([[=reports.ty_le_ket_noi=]]>100?100:[[=reports.ty_le_ket_noi=]])?>%"><div style="z-index: 100;"><?=[[=reports.ty_le_ket_noi=]]?[[=reports.ty_le_ket_noi=]].'%':'';?></div></div></div>
                                <?php }else{?>
                                [[|reports.ty_le_ket_noi|]]
                                <?php }?>
                            </td>
                            <td>[[|reports.so_chia|]]</td>
                            <td>[[|reports.so_da_goi|]]</td>
                            <td>[[|reports.so_huy|]]</td>
                            <td>[[|reports.so_tiep_can|]]</td>
                            <td>[[|reports.so_chot|]]</td>
                            <td>[[|reports.so_ton|]]</td>
                            <td align="right" class="text-right">[[|reports.doanh_thu|]]</td>
                        </tr>
                        <!--/LIST:reports-->
                        <tr align="center">
                            <td><strong>Tổng</strong></td>
                            <td></td>
                            <!--IF:cond_(![[=sale=]])-->
                            <td><strong>[[|so_cap_total_moi|]]</strong></td>
                            <td><strong>[[|so_cap_total_cu|]]</strong></td>
                            <td><strong>[[|so_cap_total|]]</strong></td>
                            <td><strong>[[|total_ty_le_chot|]] %</strong></td>
                            <!--ELSE-->
                            <td><strong>[[|total_ty_le_chot|]] %</strong></td>
                            <td><strong>[[|total_ty_le_chot_thuc|]] %</strong></td>
                            <!--/IF:cond_-->
                            <td><strong>[[|total_ty_le_chot_ket_noi|]] %</strong></td>
                            <td><strong>[[|so_chia_total|]]</strong></td>
                            <td><strong>[[|so_da_goi_total|]]</strong></td>
                            <td><strong>[[|so_huy_total|]]</strong></td>
                            <td><strong>[[|so_tiep_can_total|]]</strong></td>
                            <td><strong>[[|so_chot_total|]]</strong></td>
                            <td><strong>[[|so_ton_total|]]</strong></td>
                            <td align="right"><strong>[[|doanh_thu_total|]]</strong></td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <!--ELSE-->
                    <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <!--/IF:cond-->
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
        $('.multiple-select').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Chọn nhân viên',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    })
</script>