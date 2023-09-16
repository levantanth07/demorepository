<script>
    function make_cmd(cmd) {
        jQuery('#cmd').val(cmd);
        document.AccountFbSettingForm.submit();
    }
    function registerPage(obj,page_id){
        window.location='index062019.php?page=fb_setting&cmd=register_page&page_id='+page_id;
    }
    function unRegisterPage(obj,page_id){
        window.location='index062019.php?page=fb_setting&cmd=unregister_page&page_id='+page_id;
    }
</script>
<style>
    .table tr th,.table tr td{border:1px solid #00A8FF !important;}
    .alert-import{
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    .alert-import a{ text-decoration: none; }
    #select-file-area{
        height: 80px;
        background: #f3f3f3;
        border: 2px dashed #adadad !important;
        border-radius: 5px;
        margin: 15px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-direction: column;
        font-size: 25px;
    }
    div#select-file-area div {
        font-size: 12px;
    }

    div#select-file-area div p {
        margin: 0;
        font-weight: bold;
        color: red;
        display: inline-block;
    }
    #file-information-wrapper{width: 100%; flex-grow: 1; display: none; }
    div#file-information {display: flex; font-weight: bold; flex-direction: row; justify-content: center;}
    div#file-information .filename { font-weight: bold}
    div#file-information .filename:before {content: 'Filename: '; font-weight: normal}
    div#file-information .filesize:before {content: 'Filesize: '; font-weight: normal; margin-left: 15px}
</style>
<?php
    $title = 'Import đơn hàng từ excel'.((Url::get('v')==1)?'V1':((Url::get('v')==2)?'V2 mới (19 cột)':' (Marketing)'));
?>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="<?=Url::build_current()?>">Đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-info">
        <form name="ImportExcelForm" method="post" id="ImportExcelForm" enctype="multipart/form-data" class="form-inline">
            <div class="box-header">
                <h3 class="box-title"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> <?=$title?></h3>
            </div>
            <div class="box-body">
                <div class="col-md-12" style="margin-bottom: 15px;">
                    <a target="_blank" href="upload/attachments/mau_import_excel.xlsx?v=09052022" class="btn btn-default">
                        <i class="fa fa-angle-double-down"></i> Tải Excel mẫu <span class="label label-default">(Cập nhật: Ngày 09/05/2022)</span>
                    </a>
                    <div class="pull-right">
                        <?php if(!Session::get('order_import_excel')): ?>
                        <select name="order_status_id" id="order_status_id" class="form-control"></select>
                        <select name="order_account_id" id="order_account_id" class="form-control"></select>
                        <input name="upload" type="submit" id="upload" class="btn btn-warning" value="Tải excel lên QLBH">
                        <?php elseif(Session::get('order_import_excel_pass')): ?>
                        <input name="import" type="button" id="import" class="btn btn-primary" value="Tiếp tục import">
                        <input name="cancel-import" type="submit" id="cancel-import" class="btn btn-default" value="Hủy bỏ">
                        <?php else: ?>
                            <input name="cancel-import" type="submit" id="cancel-import" class="btn btn-default" value="Upload lại">
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!Session::get('order_import_excel')): ?>
                <div class="col-md-12">
                    <div id="select-file-area">
                        Click chọn file excel
                        <div id="file-information-wrapper">
                        <div id="file-information">
                            <div class="filename"></div>
                            <div class="filesize"></div>
                        </div>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <p>Tối đa <?=ExcelHelper::MAX_ALLOW_ROW?> dòng/file.</p>
                            <p>File excel phải có trình tự cột giống file mẫu.</p>
                        </div>
                    </div>
                    <input name="excel_file" type="file" id="excel_file" class="form-control" style="display: none">
                </div>
                <?php else: ?>
                <div class="col-md-12" >
                    <!--IF:cond(Session::get('order_import_excel'))-->
                    <div class="alert alert-import">
                        Bạn đã tải lên <strong><?=Session::get('order_import_excel_pass')?></strong> dòng thành công và đang có <strong color="red"><?=Session::get('order_import_excel_fail')?></strong> dòng lỗi
                        <?php if(Session::get('order_import_excel_fail')): ?>
                        <br>
                        Bạn vui lòng <a class="btn btn-primary btn-xs" href="<?=URL::build_current(['cmd' => 'download_excel_fail']);?>">tải xuống file excel</a> chứa các dòng lỗi và sửa lại rồi thực hiện import lại.
                        <?php endif; ?>
                    </div>
                    <!--/IF:cond-->
                </div>
                <?php endif;?>
                <div class="col-md-12">
                    <div class="alert alert-warning-custom">
                    Bước 1: Chọn trạng thái đơn, tải file excel đã được <strong>chỉnh thứ tự cột theo file mẫu</strong> (rất quan trọng) và nhấn Tải excel lên QLBH<br>
                    Bước 2: Nhấn <strong>Thực hiện import</strong> để hoàn thành.<br>
                    <div class="text-danger">
                    * Chú ý:<br>
                    - Nếu bản ghi (data) nào bạn đã import thành công lên QLBH, bạn vui lòng đánh số
                    <span style="font-size:20px;">x</span> vào cột Mã <strong>Xác Nhận Import</strong>
                    để phần mềm bỏ qua bản ghi đó.<br>
                    - Sau khi thực hiện <strong>Bước 1</strong>, hệ thống sẽ thông báo số lượng bản ghi đã được tải lên QLBH và thông tin các bản ghi lỗi.<br>
                    Vui lòng kiểm tra và update lại các bản ghi lỗi trước khi thực hiện <strong>Bước 2</strong> để được import đầy đủ các bản ghi
                    </div>
                    </div>


                    <hr>
                    <div class="label label-default">* Mới cập nhật: cột Facebook page, Facebook post và Facebook của khách hàng, Nguồn đơn hàng</div>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/packages/core/includes/js/helper.js"></script>
<script type="text/javascript">
    const TO_STATUS_ID = <?=intval(Session::get('status_id_import_excel'))?>; 
    const BLOCK_ID = <?=Module::block_id()?>; 
    const createStockInvoice = <?=json_encode($this->map['invoiceSettings'])?>;
    const CONFIRMED_STATUS_IDS = <?=json_encode(StockInvoice::CONFIRMED_STATUS_IDS)?>;
    const DELIVERED_STATUS_IDS = <?=json_encode(StockInvoice::DELIVERED_STATUS_IDS)?>;
    const RETURNED_STATUS_IDS = <?=json_encode(StockInvoice::RETURNED_STATUS_IDS)?>;
    jQuery(document).ready(function() {
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#confirmed').datepicker();
        jQuery('#created').datepicker();

        $('#select-file-area').click(function(){
            $('#excel_file').trigger('click');
        })

        $('#excel_file').change(function(){
            $('#file-information-wrapper')[this.files.length ? 'show' : 'hide']()
            $('#file-information .filename').text(this.files[0].name);
            $('#file-information .filesize').text(JSHELPER.helpers.fileSizeFormat(this.files[0].size));
        })

        let IMPORTING = 0;
        const IMPORTING_MESSAGE = 'IMPORTING ... ';
        const NUM_ROWS = <?=intval(Session::get('order_import_excel_pass'))?>;
        const BLOCK_SIZE = <?=$this->map['BLOCK_SIZE']?>;
        const OFFSET_ERRORS = [];
        const OFFSETS = [];
        for (let i = 0; i < NUM_ROWS/BLOCK_SIZE; i++) {
            OFFSETS.push(i * BLOCK_SIZE);
        }

        let _createStockInvoice = false;
        const isShowConfirmCreateStockInvoice = function(){
            if(_createStockInvoice){
                return false;
            }

            if(createStockInvoice.when_confirmed === '1' && CONFIRMED_STATUS_IDS.includes(TO_STATUS_ID)){
                return true;
            }

            if(createStockInvoice.when_delivered === '1' && DELIVERED_STATUS_IDS.includes(TO_STATUS_ID)){
                return true;
            }

            if(createStockInvoice.when_returned === '1' && RETURNED_STATUS_IDS.includes(TO_STATUS_ID)){
                return true;
            }

            return false;
        }
        $('#import').click(async function(){
            const importBtn = this;

            // trường hợp đang convert hoặc đã thành công thì ngừng tiếp nhận yêu cầu import
            if(IMPORTING || importBtn.classList.contains('btn-success')){
                return;
            }


            if(isShowConfirmCreateStockInvoice() && confirm('Bạn có muốn tự động sinh phiếu kho sau khi import ?')){
                _createStockInvoice = true;
            }

            importBtn.value = IMPORTING_MESSAGE;
            for (let i = 0; i < OFFSETS.length; i++) {
                await doImport(importBtn, OFFSETS[i]);
            }

            // đặt lại flag đang import
            IMPORTING = 0;

            // Xử lí các request import lỗi
            if(OFFSET_ERRORS.length){
                alert('Một số đơn hàng import không thành công. Vui lòng chọn TIẾP TỤC IMPORT hoặc HỦY BỎ')
                importBtn.value = 'TIẾP TỤC IMPORT';
            }
        })

        $('#cancel-import').click(function(e){
            if(IMPORTING && !confirm('Việc hủy bỏ khi file đang được upload có thể gây lỗi không mong muốn. Bạn chắc chắn muốn hủy bỏ ?')){
                e.preventDefault();
            }
        })

        /**
         * Does an import.
         *
         * @param      {<type>}  importBtn  The import button
         * @param      {<type>}  _offset    The offset
         * @return     {<type>}  { description_of_the_return_value }
         */
        const doImport = async function(importBtn, _offset)
        {
            try{
                let response = await requestImport(importBtn, _offset);

                if(response.status != 'success'){
                    return;
                }

                if(response.total == NUM_ROWS){
                    importBtn.classList.remove('btn-primary');
                    importBtn.classList.add('btn-success');
                    $('#cancel-import').val('Upload lại');
                    location.href = '/index062019.php?page=admin_orders';

                    return importBtn.value = 'SUCCESS';
                }

                importBtn.value = IMPORTING_MESSAGE + Math.ceil((parseInt(response.total) || 0) * 100/NUM_ROWS) + '%' + '(' + response.total + '/' + NUM_ROWS + ')';
            }catch(e){
                OFFSET_ERRORS.push(_offset);
            }
        }

        /**
         * Does an import.
         *
         * @param      {<type>}  importBtn  The import button
         * @param      {<type>}  _offset    The offset
         */
        const requestImport = function(importBtn, _offset)
        {   
            IMPORTING = true;

            return $.post('/index062019.php?page=admin_orders&cmd=import_excel', { 
                form_block_id: BLOCK_ID,
                offset: _offset,
                import: true,
                createStockInvoice: _createStockInvoice ? 1: 0
            })
        }

        $('#upload').click(function(e){
            if(!$('#order_status_id').val()){
                alert('Vui lòng chọn trạng thái !');
                e.preventDefault();
            }

            if(!$('#excel_file').prop('files').length){
                alert('Vui lòng chọn file !');
                e.preventDefault();
            }
        })
    });
    function checkInput(){
        if(!jQuery('#order_status_id').val()){
            alert('Bạn vui lòng chọn trạng thái đơn hàng');
            return false;
        }else{
            return true;
        }
    }


</script>
