
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
    .table tr th,.table tr td{border:1px solid #0b97c4 !important;}
</style>
<br>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">Import file excel bưu điện - </li>
            <li class="pull-right">
                <div class="pull-right">

                </div>
            </li>
        </ol>
    </nav>
    <form name="ListImportMaBuuDienForm" method="post" id="ListImportMaBuuDienForm" enctype="multipart/form-data">
        <div class="box box-info">
            <div class="box-header">
                <div class="box-title">Import file excel bưu điện</div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-addon">Chọn file excel hoặc csv</span>
                                    <input name="excel_file" type="file" id="excel_file" class="form-control">
                                    <span class="input-group-btn"></span>
                                    <input  name="upload" type="submit" id="upload" class="btn btn-warning" value="Bước 1: Tải lên để xử lý">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        Bước 2: <input  name="import_ma_buu_dien" type="submit" value="Import Mã bưu điện" class="btn btn-primary">
                                        Bước 3: Chuyển trạng thái
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12 input-group" style="padding-bottom: 20px;">
                                                <input name="import_thanh_cong" type="submit" value="Thành công" class="btn btn-success" style="background: #996699;border-color: #996699;width:140px">
                                                Hoặc: <input  name="import_thu_tien" type="submit" value="Đã thu tiền" class="btn btn-success" style="width: 140px;">
                                            </div>
                                            <div class="col-md-12 input-group">
                                                <input name="import_chuyen_hoan" type="submit" value="Chuyển hoàn" class="btn btn-danger" style="width: 140px;">
                                                Hoặc: <input  name="import_tra_hang_ve_kho" type="submit" value="Đã trả hàng về kho" class="btn btn-default" style="width: 140px;background: #CCC;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="text-warning text-bold">Nhấn vào ảnh để tải File mẫu (Mẫu mới cập nhật, Quý khách vui lòng tải lại):</div>
                                        <a href="assets/vissale/images/mau_buu_dien.xlsx" title="Tải về file mẫu"><img src="assets/vissale/images/mau_import_buu_dien.png" alt="" class="img-responsive"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="box">
                                    <div class="alert alert-warning-custom" style="height:auto">
                                        Bước 1: Tải file excel đã được chỉnh thứ tự cột theo file mẫu (rất quan trọng)
                                        <br>
                                        Bước 2: Import Mã bưu điện để khớp Mã đơn hàng và Mã bưu điện
                                        <br>
                                        Bước 3: Sau khi Bưu điện trả về kết quả, quý khách tải file excle lên và import Thành công hoặc Chuyển hoàn.
                                        <br>
                                        Bước 4: Sau khi đơn đã thành công, bạn có thể import đã thu tiền hay chưa.
                                        <br>
                                        Lưu ý:
                                        <br>
                                        - Cột Trị giá phải điền thông tin đúng bằng tổng tiền của đơn hàng
                                        <br>
                                        - Để import đơn hàng tại trạng thái Đã thu tiền, đơn hàng cần được import mã bưu điện trước và phải trải qua trạng thái Thành công.
                                        <br>
                                        - Khi import chuyển trạng thái đơn hàng:
                                        <ul>
                                            <li style="list-style: none;">+   Nếu trong file không điền cột trị giá và phí vận chuyển, đơn hàng vẫn giữ nguyên giá trị. </li>
                                            <li style="list-style: none;">+   Nếu trong file điền cột trị giá và phí vận chuyển, đơn hàng sẽ được update theo giá trị đã điền trong file. </li>
                                            
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <!--IF:cond___(isset($_SESSION['exel_items']))-->
                        <div class="alert alert-info">
                            Có <strong>[[|total|]]</strong> đơn hàng trong file excel bạn vừa tải lên.
                            <?= [[=total=]]?'<strong>Hãy thực hiện bước tiếp theo <i class="fa fa-hand-o-up"></i>...!</strong>':'';?>
                        </div>
                        <!--/IF:cond___-->
                        <!--IF:cond___(isset($_SESSION['mbd_import_excel_fail_rows']))-->
                        <div class="alert alert-danger">
                            Có <strong><?= count($_SESSION['mbd_import_excel_fail_rows']) ?></strong> dòng bị lỗi. <a href="<?=URL::build_current(['cmd' => 'download_excel_fail']);?>"style="color: orange">Bấm vào đây</a> để tải file chứa các dòng lỗi.
                        </div>
                        <!--/IF:cond___-->
                        <!--IF:cond___(isset($_SESSION['mbd_import_excel_fail_mdb_data']) && count($_SESSION['mbd_import_excel_fail_mdb_data']) > 0)-->
                        <div class="alert alert-danger">
                            Có <strong><?= count($_SESSION['mbd_import_excel_fail_mdb_data']) ?></strong> dòng bị lỗi chuyển trạng thái. <a href="<?=URL::build_current(['cmd' => 'download_excel_null_mdb']);?>"style="color: orange">Bấm vào đây</a> để tải file chứa các dòng lỗi.
                        </div>
                        <!--/IF:cond___-->
                        <!--IF:cond___(isset($_SESSION['mbd_import_excel_success_rows']) && count($_SESSION['mbd_import_excel_success_rows']) > 0)-->
                        <div class="alert alert-success">
                            Có <strong><?= count($_SESSION['mbd_import_excel_success_rows']) ?></strong> dòng thành công <a href="<?php echo Session::get('mbd_import_excel_success_link') ?>" style="color: orange">Bấm vào đây</a> để xem các đơn hàng thành công.
                        </div>
                        <!--/IF:cond___-->
                        <table class="table table-bordered">
                            <?php $i=1;$total = 0;$total_ship = 0;$total_price=0;?>
                            <!--LIST:items-->
                            <tr <?php echo ($i%2==0)?'style="background: #efefef"':'';?>>
                                <td>[[|items.1|]]</td>
                                <td>[[|items.2|]]</td>
                                <td>[[|items.3|]]</td>
                                <td>[[|items.4|]]</td>
                                <td>[[|items.5|]]</td>
                                <td>[[|items.6|]]</td>
                                <td align="right" <?php echo ($i>1 and [[=items.total_price=]]!=[[=items.7=]])?'style="color:#F00;"':'';?>><?php $total_price += intval([[=items.total_price=]]);echo ($i==1)?[[=items.total_price=]]:System::display_number([[=items.total_price=]]);?></td>
                                <td align="right" <?php echo ($i>1 and [[=items.total_price=]]!=[[=items.7=]])?'style="color:#F00;"':'';?>><?php $total += intval([[=items.7=]]);echo ($i==1)?[[=items.7=]]:System::display_number([[=items.7=]]);?></td>
                                <td align="right"><?php $total_ship += intval([[=items.8=]]); echo ($i==1)?[[=items.8=]]:System::display_number([[=items.8=]]);?></td>
                            </tr>
                            <?php $i++?>
                            <!--/LIST:items-->
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Tổng</strong></td>
                                <td align="right"><strong><?php echo System::display_number($total_price);?></strong></td>
                                <td align="right"><strong><?php echo System::display_number($total);?></strong></td>
                                <td align="right"><strong><?php echo System::display_number($total_ship);?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
