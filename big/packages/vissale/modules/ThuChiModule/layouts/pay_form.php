<style>
    .img-print-template{
    }
    .img-print-template img{
        max-width: 100%;
    }
</style>
<div style="display:none">
    <div id="mi_payment_sample">
        <div id="input_group_#xxxx#" class="multi-item-group text-center">
            <input  name="mi_payment[#xxxx#][order_type]" type="hidden" id="order_type_#xxxx#" class="multi-edit-text-input" tabindex="-1">
            <input  name="mi_payment[#xxxx#][order_id]" type="hidden" id="order_id_#xxxx#" class="multi-edit-text-input" tabindex="-1">
            <span class="multi-edit-input" style="width:50px;"><input  name="mi_payment[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input" style="width:250px;"><select  name="mi_payment[#xxxx#][category_id]" class="multi-edit-text-input" id="category_id_#xxxx#" tabindex="#xxxx#">[[|category_id_options|]]</select></span>
            <span class="multi-edit-input" style="width:100px;"><input  name="mi_payment[#xxxx#][amount]" class="multi-edit-text-input align-right" type="text" id="amount_#xxxx#" onChange="updateTotalPayment();this.value=numberFormat(this.value);" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:100px;"><select  name="mi_payment[#xxxx#][payment_method]" class="multi-edit-text-input" id="payment_method_#xxxx#">[[|payment_method_options|]]</select></span>
            <span class="multi-edit-input" style="width:200px;"><input  name="mi_payment[#xxxx#][description]" class="multi-edit-text-input" type="text" id="description_#xxxx#" tabindex="#xxxx#" onchange="updateTotalPayment();"></span>
            <span class="multi-edit-input no-border btn btn-default" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_payment','#xxxx#','');updateTotalPayment();updateTotalPayment();event.returnValue=false;" style="cursor:pointer;"  tabindex="-1" title="Nhấn chuột vào đây sẽ xóa lượt thanh toán. Thao tác hoàn thành khi nhấn lưu lại"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></span>
        </div>
    </div>
</div>
<?php $title = 'Phiếu chi '.((Url::get('cmd')=='edit')?'(Sửa)':'(Thêm mới)')?>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build_current(['type'])?>">Thu chi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Phiếu chi</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <fieldset id="toolbar">
        <div id="toolbar-title">
            <?=$title;?>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                <tr>
                    <td align="center">
                        <a class="btn btn-primary" onclick="EditThuChiModule.submit();this.setAttribute('disabled', true);">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Ghi lại </a>
                    </td>
                    <!--IF:cond(Url::get('id'))-->
                    <td align="center"><a class="btn btn-warning" onclick="ClickHereToPrint('ifrmPrint', 'printBox')">
                            <span title="In"> </span><i class="glyphicon glyphicon-print"></i> IN </a></td>
                    <td>
                        <select class="form-control" id="chon_lien_in">
                            <option value="Liên 1">Liên 1</option>
                            <option value="Liên 2">Liên 2</option>
                            <option value="Liên 3">Liên 3</option>
                        </select>
                    </td>
                    <!--/IF:cond-->
                    <td align="center">
                        <a class="btn btn-danger" href="<?php echo Url::build_current(array('cmd' => 'list','type')); ?>#">
                            <i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset><br clear="all"/>
    <fieldset id="add_receive_form" class="row">
        <form  name="EditThuChiModule" id="EditThuChiModule" method="post" enctype="multipart/form-data">
            <input name="deleted_ids" type="hidden" id="deleted_ids">
            <input name="bill_type" type="hidden" id="bill_type" value='0' class="form-control">
            <input name="turn_card_id" type="hidden" id="turn_card_id">
            <input name="print" type="hidden" id="print" value="">
            <div class="col-md-8">
                <?php if (Form::$current->is_error()) {echo Form::$current->error_messages();} ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <strong>Phiếu Chi</strong>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bill_number">Mã Phiếu</label>
                                    <input name="bill_number" type="text" id="bill_number" class="form-control"
                                           value="(auto)" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="team_id">Nhóm người nhận</label>
                                    <select name="team_id" id="team_id" class="form-control"></select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="received_full_name">Tên người nhận</label>
                                    <input name="received_full_name" type="text" id="received_full_name"
                                           class="form-control"
                                           placeholder="Tên người nhận">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile">Số ĐT người nhận</label>
                                    <input name="mobile" type="text" id="mobile"
                                           class="form-control"
                                           placeholder="Số ĐT người nhận">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bill_date">Thời Gian</label>
                                    <input name="bill_date" type="text" id="bill_date" class="form-control"
                                           placeholder="dd/mm/yyy">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tổng số tiền:</label>
                                    <input name="amount" type="text" id="amount" class="form-control text-right"
                                           placeholder="00.00" onChange="this.value=numberFormat(this.value);" readonly>
                                    <!--IF:total_payment_cond(Url::get('total_payment'))-->
                                    <div class=" text-right text-danger">Đã thanh toán [[|total_payment|]] /
                                        [[|total_price|]]
                                    </div>
                                    <!--/IF:total_payment_cond-->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group hidden">
                                    <div class="col-md-4">
                                        <label for="exampleInputFile">Ảnh chụp:</label>
                                        <input name='attachment_file' type="file" id="attachment_file"
                                               class="form-control-file">
                                        <p class="help-block">Ảnh chụp phiếu thu</p>
                                    </div>
                                    <div class="col-md-8">
                                        <!--IF:cond(Url::get('attachment_file'))--><a href="[[|attachment_file|]]"
                                                                                      target=_blank><!--/IF:cond-->
                                            <!--IF:cond(Url::get('id'))-->
                                            <img src="[[|attachment_file|]]" alt="Ảnh đại diện"
                                                 class="img-reponsive" id="mainImage" width="200">
                                            <!--ELSE-->
                                            <img src="assets/standard/images/pattern2.png" alt="Ảnh đại diện"
                                                 class="img-reponsive" id="mainImage" width="200">
                                            <!--/IF:cond-->
                                            <!--IF:cond(Url::get('attachment_file'))--></a><!--/IF:cond-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Chi tiết</h4>
                                <div class="multi-item-wrapper">
                                    <div id="mi_payment_all_elems">
                                        <div style="white-space:nowrap;">
                                            <span class="multi-edit-input header" style="width:50px;">ID</span>
                                            <span class="multi-edit-input header"
                                                  style="width:250px;">Khoản chi</span>
                                            <span class="multi-edit-input header"
                                                  style="width:100px;">Thanh toán</span>
                                            <span class="multi-edit-input header"
                                                  style="width:100px;">Phương thức</span>
                                            <span class="multi-edit-input header"
                                                  style="width:200px;">Diễn giải</span>
                                            <span class="multi-edit-input header"
                                                  style="width:40px;text-align: center;">Xóa</span>
                                            <br clear="all">
                                        </div>
                                    </div>
                                </div>
                                <br clear="all">
                                <div style="padding:5px 0px 5px 0px;">
                                    <button type="button" class="btn btn-default" title="Thêm sản phẩm dịch vụ"
                                            onclick="mi_add_new_row('mi_payment');"><i
                                                class="glyphicon glyphicon-plus-sign"></i> Thêm khoản chi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <label for="bill_date">Nội dung phiếu chi:</label>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <textarea name="note" type="text" id="note" class="form-control" rows="5"
                                      placeholder="ghi chú"></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
                    </div>
                    <div class="panel-body" style="max-height: 865px;overflow-y: auto">
                        <ul class="list-group">
                            <!--LIST:logs-->
                            <li class="list-group-item">
                                <row>
                                    <div class="col-md-6"><i
                                                class="fa fa-clock-o"></i> <?php echo date("d/m/Y H:i'", [[=logs.created_time=]]); ?>
                                    </div>
                                    <div class="col-md-6"><strong>[[|logs.created_account_id|]]</strong> [[|logs.type|]]</div>
                                </row>
                                <row>
                                    <div class="col-md-12 small">[[|logs.content|]]</div>
                                </row>
                            </li>
                            <!--/LIST:logs-->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </fieldset>
</div>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<style>
.printBox * {
color: \#000;
}
.printBox{display: none;}
table {
page-break-inside: auto
}
tr {
page-break-inside: avoid;
page-break-after: auto
}
.fs18 {
font-size: 18px;
}
.mb25 {
margin-bottom: 25px;
}
.mb10 {
margin-bottom: 10px;
}
.mb5 {
margin-bottom: 5px;
}
</style>
<div id="printBox" class="printBox">
<div class="mb25">
<span style="font-size:14px;">[[|print_name|]]</span><br />
<span style="font-size:small;">SĐT: </span><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;">[[|print_phone|]]</span><br />
<span style="font-size:small;">Địa chỉ:&nbsp;[[|print_address|]]</span>
</div>
    <div class="mb10" style="text-align:center; margin: 20px 0;">
        <strong class="fs18"><span style="font-size:x-large;">PHIẾU CHI</span></strong><br/>
        <span style="font-size:small;">(<span id="lien_in_value">Liên 1</span>)<span>
    </div>
<div class="mb5" style="text-align:center;"><strong><span style="font-size:small;">M&atilde; phiếu thu: <?=Url::get('bill_number');?></span></strong></div>
<div class="mb25" style="text-align:center;"><em><span style="font-size:small;">Ng&agrave;y:&nbsp;</span></em><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;"><em><?=Url::get('bill_date');?></em></span></div>
<table width="100%">
    <tbody>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="24%"><span style="font-size:small;">Họ tên người nộp tiền:</span></td>
                            <td style="border-bottom:1px dotted \#000;font-size:small;"><?=Url::get('payment_full_name');?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td style="white-space:nowrap;" width="24%">
                                <span style="font-size:small;">Số điện thoại:</span>
                            </td>
                            <td style="border-bottom:1px dotted \#000; font-size:small;">................................................................................</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">Địa chỉ:</span></td>
                            <td style="border-bottom:1px dotted \#000; font-size:small;">................................................................................</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td style="white-space:nowrap;" width="24%">
                                <span style="font-size:small;">Lý do nộp:</span>
                            </td>
                            <td style="border-bottom:1px dotted \#000; font-size:small;"><?=Url::get('note');?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td>
                <span style="font-size:small;">Số tiền: </span><strong>
                    <span style="font-size:small;"><?=Url::get('amount');?> VNĐ</span></strong>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    <span style="font-size:small;">Bằng chữ:</span>
                    <span style="color:#213140;font-size:small;"></span>
                    <em style="font-size:small;">&nbsp <?=Url::get('amount_words');?></em>
                </p>
        </td>
    </tr>
    <tr>
        <td colspan="2" height="10"></td>
    </tr>
</tbody>
</table>
<div class="mb10" style="text-align:right;">
    <span style="font-size:small;">Ngày .......... Tháng .......... Năm ...............</span>
</div>
<table width="100%">
<tbody>
    <tr>
        <td align="center" width="33%"><strong><span style="font-size:small;">Người lập phiếu</span></strong></td>
        <td align="center" width="33%"><strong><span style="font-size:small;">Người nộp</span></strong></td>
        <td align="center" width="33%"><strong><span style="font-size:small;">Thủ quỹ</span></strong></td>
    </tr>
</tbody>
</table>
</div>
<script>
    mi_init_rows('mi_payment',<?php if(isset($_REQUEST['mi_payment'])){echo MiString::array2js($_REQUEST['mi_payment']);}else{echo '[]';}?>);
    <!--IF:cond(Url::get('print_now')==1)-->
    ClickHereToPrint('ifrmPrint', 'printBox');
    <!--/IF:cond-->
    updateTotalPayment();
    $(document).ready(function(){
        $('#bill_date').datepicker({
            StartDate: new Date(2019, 1 - 1, 1),
            format:'dd/mm/yyyy',language:'vi'
        });
        $('#chon_lien_in').change(function(event) {
            $('#lien_in_value').text( $(this).val() );
        });
    });
    function updateTotalPayment(){
        let total = 0;
        let amount;
        let note = '';
        for(let i=101;i<=input_count;i++){
            if(getId('id_'+i) && getId('amount_'+i)){
                amount = to_numeric($('#amount_'+i).val());
                total += amount;
                note += (note?', ':'') + $('#description_'+i).val();
            }
        }
        $('#note').val(note);
        $('#amount').val(numberFormat(total));
    }
</script>