<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php 
    $title = 'Báo cáo doanh thu marketing '.(Url::iget('type')?((Url::iget('type')==1)?'SALE':((Url::iget('type')==2)?'CSKH':'Đặt lại')):'');
    if(Url::get('act') == 'upsale'){
        $title = 'Báo cáo doanh thu upsale '.(Url::iget('type')?((Url::iget('type')==1)?'SALE':((Url::iget('type')==2)?'CSKH':'Đặt lại')):'');
    }
?>
<style>
.hide-native-select select{display: none !important;}   
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
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
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" style="width: 100px;" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" style="width: 100px;" placeholder="đến ngày">
                        </div>
                        <div class="form-group">
                            <select name="date_type" id="date_type" class="form-control" style="width: 125px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="type" id="type" class="form-control"></select>
                        </div>

                        <div class="form-group">
                            <!-- Custom tag làm điểm gắn kết SELECT -->
                            <JSHELPER id="is_active"></JSHELPER>
                        </div>

                        <div class="form-group">
                            <!-- Custom tag làm điểm gắn kết SELECT -->
                            <JSHELPER id="team"></JSHELPER>
                        </div>
                        
                        <div class="form-group">
                            <!-- Custom tag làm điểm gắn kết SELECT -->
                            <JSHELPER id="user"></JSHELPER>
                        </div>

                        <!--IF:cond(Session::get('account_type')==3)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control"></select>
                        </div>
                        <!--/IF:cond-->
                    </div>
                    <div class="col-md-2 pull-right text-right">
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12" style="padding: 20px 40px 0; font-size: 13px;">
                        <strong style="color: red">Lưu ý:</strong><br>
                        * Số cấp: Số do marketing tạo ra trong khoảng thời gian xem báo cáo<br>
                        * Số chia: Số do marketing tạo ra đã được chia cho sale<br>
                        * Chốt / cấp: Tỷ lệ số đơn đã chốt / số đơn cấp<br>
                        * Chốt / chia: Tỷ lệ số đơn đã chốt / số đơn đã chia <br>
                        * Tiếp cận: Số do marketing tạo ra đang ở các trạng thái được tính là tiếp cận<br>
                        * Kết nối: Tỷ lệ số tiếp cận / số chia
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <!--    Start For Marketing -->
            <div class="row">
                <div class="col-md-12">
                    <div id="MktReportForm" style="background:#FFF;margin: 10px;padding:10px;">
                        <!--IF:cond(sizeof([[=reports=]])>1)-->
                        <table id="MktReportTable" width="100%" bordercolor="#999" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                            <tbody>
                            <!--LIST:reports-->
                            <?php
                            $total_confirmed = 0;
                            $total_confirmed_new = 0;
                            $total_confirmed_old = 0;
                            $amount_confirmed = 0;
                            $total_amount_confirmed = 0;
                            $ly_le_chot = 0;
                            $so_cap = [[=reports.so_cap=]];
                            $so_chia = [[=reports.so_chia=]];
                            ?>
                            <!--IF:cond([[=reports.id=]]=='label')-->
                            <tr style="font-weight:bold;background:#417ae8;color:#fff;">
                                <td rowspan=2 style="width: 150px;">[[|reports.name|]]</td>
                                <!--LIST:mkt_status-->
                                <td colspan=2 align="center"><?php echo $this->map['reports']['current'][[[=mkt_status.id=]]][1]['name'];?></td>
                                <!--/LIST:mkt_status-->
                                <td rowspan=2 class="text-center">[[|reports.so_cap|]]</td>
                                <td rowspan=2 class="text-center">Chốt / cấp</td>
                                <td rowspan=2 class="text-center">[[|reports.so_chia|]]</td>
                                <td rowspan=2 class="text-center">Chốt / chia</td>
                                <td rowspan=2 class="text-center">Tiếp cận</td>
                                <td rowspan=2 class="text-center" title="Số tiếp cận / số chia">Kết nối</td>
                            </tr>
                            <tr>
                                <!--LIST:mkt_status-->
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=mkt_status.id=]]][1]['qty'];?></td>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=mkt_status.id=]]][1]['total_price'];?></td>
                                <!--/LIST:mkt_status-->
                            </tr>
                            <!--ELSE-->
                            <tr>
                                <td>[[|reports.name|]]</td>
                                <!--LIST:mkt_status-->
                                <?php
                                    $qty = $this->map['reports']['current'][[[=mkt_status.id=]]][1]['qty'];
                                    $total_price = $this->map['reports']['current'][[[=mkt_status.id=]]][1]['total_price'];
                                    if([[=mkt_status.id=]]==XAC_NHAN){
                                        $total_confirmed +=System::calculate_number($qty);
                                        $amount_confirmed += System::calculate_number($total_price);
                                        $total_amount_confirmed += System::calculate_number($total_price);
                                    }
                                ?>
                                <td align="center" class="col" style="min-width: 30px;"><?=$qty?></td>
                                <td align="center" class="col"><?=$total_price?></td>
                                <!--/LIST:mkt_status-->
                                <td align="center" class="col">
                                    <?php
                                        $so_cap_color = $so_cap?'#7fff00':'#EFEFEF';
                                    ?>
                                    <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: <?=$so_cap_color?>;width:<?=(([[=so_cap_max=]]>0)?($so_cap/[[=so_cap_max=]])*100:'0')?>%;"><div style="float:left;width: 100%;z-index: 100;"><?=$so_cap?></div></div></div>
                                </td>
                                <td align="center" class="col">
                                    <?php
                                    $ly_le_chot_ = (($so_cap>0)?round($total_confirmed/$so_cap,3)*100:0);
                                    $ly_le_chot_ = (floatval($ly_le_chot_)>100)?100:floatval($ly_le_chot_);
                                    $ty_le_chot_color = '#7fff00';
                                    if($ly_le_chot_<20 and $ly_le_chot_>0){
                                        $ty_le_chot_color = '#f24e34';
                                    }elseif($ly_le_chot_>=20 and $ly_le_chot_<50){
                                        $ty_le_chot_color = '#f6c12b';
                                    }elseif($ly_le_chot_>=50){
                                        $ty_le_chot_color = '#41f62b';
                                    }
                                    ?>
                                    <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: <?=$ty_le_chot_color?>;width:<?=$ly_le_chot_?>%;"><div style="float:left;width: 100%;z-index: 100;"><?=$ly_le_chot_?>%</div></div></div>
                                </td>
                                <td align="center" class="col"><?php echo System::display_number([[=reports.so_chia=]]);?></td>
                                <td align="center" class="col">
                                    <?php
                                    $ly_le_chot__ = (($so_chia>0)?round($total_confirmed/$so_chia,3)*100:0);
                                    $ly_le_chot__ = (floatval($ly_le_chot__)>100)?100:floatval($ly_le_chot__);
                                    $ty_le_chot_color = '#7fff00';
                                    if($ly_le_chot__<20 and $ly_le_chot__>0){
                                    $ty_le_chot_color = '#f24e34';
                                    }elseif($ly_le_chot__>=20 and $ly_le_chot__<50){
                                    $ty_le_chot_color = '#f6c12b';
                                    }elseif($ly_le_chot__>=50){
                                    $ty_le_chot_color = '#41f62b';
                                    }
                                    ?>
                                    <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: <?=$ty_le_chot_color?>;width:<?=$ly_le_chot__?>%;"><div style="float:left;width: 100%;z-index: 100;"><?=$ly_le_chot__?>%</div></div></div>
                                </td>
                                <td class="text-center">
                                    [[|reports.so_tiep_can|]]
                                </td>
                                <td class="text-center" style="overflow: hidden">
                                    <?php if([[=reports.id=]] <> 'label'){?>
                                    <div class="bar-wrapper" style="background: #efefef;"><div class="bar" style="background: [[|reports.ty_le_ket_noi_color|]];width: <?=[[=reports.ty_le_ket_noi=]]?>%"><div style="float:left;width: 100%;z-index: 100;"><?=[[=reports.ty_le_ket_noi=]]?[[=reports.ty_le_ket_noi=]].'%':'';?></div></div></div>
                                    <?php }else{?>
                                    [[|reports.ty_le_ket_noi|]]
                                    <?php }?>
                                </td>
                            </tr>
                            <!--/IF:cond-->
                            <!--/LIST:reports-->
                            <tr>
                                <td><strong>Tổng</strong></td>
                                <!--LIST:mkt_status-->
                                <td align="center" class="col"><strong><?php echo System::display_number([[=mkt_status.qty=]]);?></strong></td>
                                <td align="center" class="col"><strong><?php echo System::display_number([[=mkt_status.total=]]);?></strong></td>
                                <!--/LIST:mkt_status-->
                                <td align="center" class="col"><strong>[[|tong_so_cap|]]</strong></td>
                                <td align="center" class="col"><strong>[[|tong_so_chot_per_cap|]]%</td>
                                <td align="center" class="col"><strong>[[|tong_so_chia|]]</strong></td>
                                <td align="center" class="col"><strong>[[|tong_so_chot_per_chia|]]%</strong></td>
                                <td align="center" class="col"><strong>[[|so_tiep_can_total|]]</strong></td>
                                <td align="center" class="col"><strong>[[|ket_noi|]]%</strong></td>
                            </tr>
                            </tbody>
                        </table>
                        <!--ELSE-->
                        <!--IF:view_cond(!Url::post('view_report'))-->
                        <div class="alert alert-warning-custom text-center">Bạn vui lòng nhấn nút Xem báo cáo</div>
                        <!--ELSE-->
                        <div class="alert alert-warning text-center">Không có dữ liệu báo cáo phù hợp</div>
                        <!--/IF:view_cond-->
                        <!--/IF:cond-->
                    </div>
                </div>
            </div>
            <!--    end For Marketing -->
        </div>
    </div>
</div>
<script type="text/javascript">

    let usersTeams = <?=json_encode($this->map['users_teams'])?> || [];
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

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
                    if(checked && val == 0){
                        $('#select-team').multiselect('selectAll', false)
                        $('#select-user').multiselect('selectAll', false)
                        return;
                    }

                    // nếu uncheck vào chọn tất cả team thì unselect tất cả team và tất cả user
                    else if(!checked && val == 0){
                        $('#select-team').multiselect('deselectAll', false)
                        $('#select-user').multiselect('deselectAll', false)
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
                buttonWidth: '200px',
                maxHeight: 200,
                enableFiltering: true,
                filterBehavior: 'text',
                enableCaseInsensitiveFiltering: true,
                buttonWidth: '200px',
                maxHeight: 200,
                onChange: function(option, checked) {
                    const val = parseInt(option.val());
                    // nếu check vào chọn tất cả user thì select tất cả user
                    if(checked && val == 0){
                        $('#select-user').multiselect('selectAll', false)
                    }

                    // nếu uncheck vào chọn tất cả user thì unselect tất cả user
                    else if(!checked && val == 0){
                        $('#select-user').multiselect('deselectAll', false)
                    }
                }
            })
            // Mặc định
            .multiselect('select', <?=json_encode(URL::getArray('users', [0]))?>);
        }

        JSHELPER.render.select({
            data: {0: 'Tất cả nhóm', ...<?=json_encode($this->map['teams'])?>},
            HTML_COLUMN: 'name',
            selectAttrs: {class: 'form-control', name: "teams[]", multiple: true, id: 'select-team', style: 'display: none'},
        }).mount('#team', null, onMountedTeam); 

        JSHELPER.render.select({
            data: {0: '<?=$this->map['is_upsale'] ? 'Tất cả Upsale' : 'Tất cả MKT'?>', ...<?=json_encode($this->map['users'])?>},
            HTML_COLUMN: 'username',
            selectAttrs: {class: 'form-control', name: "users[]", multiple: true, id: 'select-user' , style: 'display: none'},
        }).mount('#user', null, onMountedUser);  

        JSHELPER.render.select({
            data: {0: 'Tất cả user', 1: 'Đã kích hoạt', 2: 'Hủy kích hoạt'},
            selected: <?=URL::getUInt('is_active', 1)?>,
            selectAttrs: {class: 'form-control', name: "is_active", id: 'is_active'},
        }).mount('#is_active');  
    });
</script>