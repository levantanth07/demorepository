
<?php
$title = Url::get('has_revenue')?'BÁO CÁO DOANH THU SẢN PHẨM/HÀNG HÓA':'BẢNG KÊ SẢN PHẨM/HÀNG HÓA';
?>
<style>
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
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
         border: 1px solid #f4f4f4 !important; 
    }
    .table>tbody+tbody {
         border: 1px solid #f4f4f4 !important; 
    }
</style>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">
                <?=$title?> - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-doanh-thu-san-pham-hang-hoa/"
                                  target="_blank" class="btn btn-default"
                                  style="padding: 0px 2px;">
                                  <i class="fa fa-question-circle"></i>
                                  Hướng dẫn
                               </a>

            </li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <form name="OrderProductForm" method="post" class="form-inline">
            <div class="box-header">
                <div class="box-title">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input name="product_code" type="text" id="product_code" style="width: 160px;" placeholder="Mã hàng" class="form-control">
                            </div>
                            <div class="form-group">
                                <select name="bundle_id" id="bundle_id" style="width:150px;" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <select name="label_id" id="label_id" style="width:150px;" class="form-control"></select>
                            </div>
                            
                            <div class="form-group">
                                <input name="date_from" type="text" id="date_from" style="width: 100px;" placeholder="Từ ngày" class="form-control">
                            </div>
                            <div class="form-group">
                                <input name="date_to" type="text" id="date_to" style="width: 100px;" placeholder="đến ngày" class="form-control">
                            </div>
                            <div class="form-group">
                                <select name="status_id" id="status_id" style="width:160px;" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <select name="has_revenue" id="has_revenue" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <select name="user_id" id="user_id" style="width:150px;" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="warehouse_id" id="warehouse_id" style="width:150px;" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-tools">
                    <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                    <button type="button" class="btn btn-success" id="btnExport" onclick="fnExcelReport('reportTable');"> Xuất Excel </button>
                    <button class="btn btn-default" type="button" onclick="printWebPart('reportForm')"> <i class="fa fa-print"></i> IN</button>
                </div>
            </div>
        </form>
        <div class="box-body">
            <div class="panel">
                <div class="panel-body" id="reportForm">
                    <!--IF:report_cond(!empty([[=products=]]))-->
                    <div style="text-align:left;">
                        <div style="width:99%;padding:2px 2px 2px 2px;text-align:center;font-size:14px;">
                            <div style="padding:2px 2px 2px 2px;">
                                <h3>
                                    <?=$title?>
                                </h3>
                                <div>
                                </div>
                                <div style="padding:2px 2px 2px 2px;text-align:center;">
                                    Trạng thái: [[|status_name|]]<br>
                                    <div>[[|product_name|]]</div>
                                    <div>[[|bundle_name|]]</div>
                                    Từ ngày <?php echo Url::get('date_from');?> đến <?php echo Url::get('date_to');?>
                                    <div>[[|warehouse_name|]]</div>
                                </div>
                                <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                                    <table id="reportTable" class="table table-bordered tableFixHead" width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#999">
                                        <thead style="position: sticky; top: 0; z-index: 1">
                                            <tr>
                                                <th width="4%" scope="col">STT</th>
                                                <th width="10%" align="center" scope="col">Mã sản phẩm, hàng hóa </th>
                                                <?php if([[=admin_tong=]]){?>
                                                <th width="10%" scope="col" align="center">SHOP</th>
                                                <?php }?>
                                                <th width="25%" align="center" scope="col">Tên sản phẩm, hàng hóa </th>
                                                <th width="8%" scope="col" align="center">Kho</th>
                                                <th width="8%" scope="col" align="center">Phân loại</th>
                                                <th width="8%" scope="col" align="center">Nhãn sản phẩm</th>
                                                <th width="2%" scope="col" align="center">Đơn vị</th>
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <th width="10%" scope="col" align="center">Giá khai báo</th>
                                                <!--/IF:cond-->
                                                <th width="5%" scope="col" align="center">Số lượng</th>
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <th width="10%" scope="col" align="center">Giảm giá sp</th>
                                                <th width="10%" scope="col" align="center">Giá sau giảm</th>
                                                <th width="10%" scope="col" align="center">Tổng tiền</th>
                                                <!--/IF:cond-->
                                            </tr>
                                        </thead>
                                        <!--LIST:products-->
                                        <tbody>
                                            <tr>
                                                <td align="center">[[|products.i|]]</td>
                                                <td align="center">
                                                     &#8203 [[|products.code|]]
                                                </td>
                                                <?php if([[=admin_tong=]]){?>
                                                <td>[[|products.group_name|]]</td>
                                                <?php }?>
                                                <td align="left">[[|products.name|]]</td>
                                                <td align="left">[[|products.warehouse_name|]]</td>
                                                <td align="center">[[|products.bundle_name|]]</td>
                                                <td align="center">[[|products.lable_name|]]</td>
                                                <td align="center">[[|products.unit_name|]]</td>
                                                
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right">[[|products.price|]]</td>
                                                <!--/IF:cond-->
                                                <td align="center" class="text-right">[[|products.number|]]</td>
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right">[[|products.discount_amount|]]</td>
                                                <!--/IF:cond-->
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right">[[|products.after_discount|]]</td>
                                                <!--/IF:cond-->
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right">[[|products.total_price|]]</td>
                                                <!--/IF:cond-->
                                            </tr>
                                        </tbody>
                                        <!--/LIST:products-->
                                        <tbody>
                                            <?php for($i=0;$i<=5;$i++){ ?>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <?php if([[=admin_tong=]]){?>
                                                    <td align="center">&nbsp;</td>
                                                    <?php }?>
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <!--IF:cond(Url::get('has_revenue'))-->
                                                    <td align="center">&nbsp;</td>
                                                    <!--/IF:cond-->
                                                    <td align="center">&nbsp;</td>
                                                    <!--IF:cond(Url::get('has_revenue'))-->
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                    <td align="center">&nbsp;</td>
                                                <!--/IF:cond-->
                                                </tr>
                                            <?php
                                                if($i==1)
                                                {
                                                    echo '<div style="display:none;page-break-after:always;">';
                                                }
                                            }?>
                                        </tbody>
                                        <tfoot style="position: sticky; bottom: 0; background: #fff; font-weight: bold;">
                                            <tr style="position: sticky; left: 0; background: #DDD;">
                                                <td  colspan="2"><strong>Tổng</strong></td>
                                                <?php if([[=admin_tong=]]){?>
                                                <td align="center">x</td>
                                                <?php }?>
                                                <td align="center">x</td>
                                                <td align="center">x</td>
                                                <td align="center">x</td>
                                                <td align="center">x</td>
                                                <td align="center">x</td>
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right"><strong>[[|total_all_price|]]</strong></td>
                                                <!--/IF:cond-->
                                                <td align="right" class="text-right"><strong>[[|total_qty|]]</strong></td>
                                                <!--IF:cond(Url::get('has_revenue'))-->
                                                <td align="right" class="text-right"><strong>[[|total_all_discount|]]</strong></td>
                                                <td align="right" class="text-right"><strong>[[|total_all_after_discount|]]</strong></td>
                                                <td align="right" class="text-right"><strong>[[|total_amount|]]</strong></td>
                                                <!--/IF:cond-->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--ELSE-->
                    <!--IF:report_cond_(Url::get('view_report'))-->
                    <div class="alert text-center text-danger">Không có kết quả quản phù hợp!</div>
                    <!--ELSE-->
                    <div class="alert text-center text-info">Vui lòng nhấn nút <strong>Xem báo cáo</strong></div>
                    <!--/IF:report_cond_-->
                    <!--/IF:report_cond-->
                </div>
                <script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>
        </div>
    </div>
</div>