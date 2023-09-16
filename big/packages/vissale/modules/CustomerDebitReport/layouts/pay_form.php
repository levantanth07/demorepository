<div class="container">
    <style>
    .img-print-template{
    }
    .img-print-template img{
    max-width: 100%;
    }
    </style>
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý Thu-Chi
            <span>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit')
            {echo 'Sửa';}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                    <tr>
                        <td align="center">
                            <a class="btn btn-primary" onclick="EditCustomerDebitReport.submit();">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Ghi lại </a>
                        </td>
                        <td align="center"><a class="btn btn-warning" onclick="ClickHereToPrint('ifrmPrint', 'printBox')"> <span title="In"> </span><i class="glyphicon glyphicon-print"></i> IN </a> </td>
                        <td align="center">
                            <a class="btn btn-danger" href="<?php echo Url::build_current(array('cmd'=>'list'));?>#">
                            <i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </fieldset>
        <br clear="all"/>
        <fieldset id="add_receive_form" class="row">
            <div class="col-md-8">
                <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <strong>Phiếu Chi</strong>
                    </div>
                    <div class="panel-body">
                        <form name="EditCustomerDebitReport" id="EditCustomerDebitReport" method="post" enctype="multipart/form-data">
                            <input name="bill_type" type="hidden" id="bill_type" value='0' class="form-control">
                            <div class="form-group">
                                <label for="bill_number">Mã Phiếu</label>
                                <input name="bill_number" type="text" id="bill_number" class="form-control" disabled>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Loại Chi</label>
                            <select name="category_id" id="category_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="team_id">Nhóm người nhận</label>
                        <select name="team_id" id="team_id" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="received_full_name">Tên người nhận</label>
                        <input name="received_full_name" type="text" id="received_full_name" class="form-control"
                        placeholder="Tên người nhận">
                    </div>
                    <div class="form-group">
                        <label for="bill_date">Thời Gian</label>
                        <input name="bill_date" type="text" id="bill_date" class="form-control" placeholder="dd/mm/yyy" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Số tiền:</label>
                        <input name="amount" type="text" id="amount" class="form-control text-right"
                        placeholder="00.00" onChange="this.value=numberFormat(this.value);">
                    </div>
                    <div class="form-group">
                        <label for="bill_date">Nội dung phiếu chi:</label>
                        <textarea name="note" type="text" id="note" class="form-control" rows="5"
                        placeholder="ghi chú"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="exampleInputFile">Ảnh chụp:</label>
                            <input  name='attachment_file' type="file" id="attachment_file" class="form-control-file">
                            <p class="help-block">Ảnh chụp phiếu thu</p>
                        </div>
                        <div class="col-md-8">
                            <!--IF:cond(Url::get('attachment_file'))--><a href="[[|attachment_file|]]" target=_blank><!--/IF:cond-->
                            <!--IF:cond(Url::get('id'))-->
                            <img src="[[|attachment_file|]]" alt="Ảnh đại diện" class="img-reponsive" id="mainImage" width="200">
                            <!--ELSE-->
                            <img src="assets/standard/images/pattern2.png" alt="Ảnh đại diện" class="img-reponsive" id="mainImage" width="200">
                            <!--/IF:cond-->
                            <!--IF:cond(Url::get('attachment_file'))--></a><!--/IF:cond-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
            </div>
            <div class="panel-body" style="max-height: 865px;overflow-y: auto">
                <ul class="list-group">
                    <!--LIST:logs-->
                    <li class="list-group-item">
                        <row>
                        <div class="col-md-6"><i class="fa fa-clock-o"></i> <?php echo date("d/m/Y H:i'", [[=logs.created_time=]]); ?></div>
                        <div class="col-md-6"><strong>[[|logs.created_account_id|]]</strong> đã [[|logs.type|]]</div>
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
</fieldset>
</div>
<script>
jQuery(document).ready(function(){
jQuery('#bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
});
</script>
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
<span style="font-size:x-large;">[[|print_name|]]</span><br />
<span style="font-size:small;">SĐT: </span><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;">[[|print_phone|]]</span><br />
<span style="font-size:small;">Địa chỉ:&nbsp;[[|print_address|]]</span>
</div><div class="mb10" style="text-align:center;"><strong class="fs18"><span style="font-size:large;">PHIẾU CHI</span></strong></div>
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
                            <td style="border-bottom:1px dotted \#000;"><?=Url::get('payment_full_name');?></td>
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
                            <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">Số điện thoại:</span></td>
                            <td style="border-bottom:1px dotted \#000;">................................................................................</td>
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
                            <td style="border-bottom:1px dotted \#000;">................................................................................</td>
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
                            <td style="white-space:nowrap;" width="24%"><span style="font-size:small;">L&yacute; do nộp:</span></td>
                            <td style="border-bottom:1px dotted \#000;"><?=Url::get('note');?></td>
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
                <span style="font-size:small;">Số tiền: </span><strong><span style="font-size:small;"><?=Url::get('amount');?> VNĐ</span></strong>
            </td>
        </tr>
        <tr>
            <td>
                <p><span style="font-size:small;">Bằng chữ: </span><span style="color:#213140;font-size:small;line-height:22px;background-color:#ffffff;">
                ................................................................................................................................................................
            </span><em>&nbsp;</em></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" height="10"></td>
    </tr>
</tbody>
</table>
<div class="mb10" style="text-align:right;"><span style="font-size:small;">Ng&agrave;y .......... Th&aacute;ng .......... Năm ...............</span></div>
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