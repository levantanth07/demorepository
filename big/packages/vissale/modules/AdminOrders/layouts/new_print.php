<?php
    $templates = [[=template=]];
    $bar_code_mediadoc = [[=bar_code_mediadoc=]];
    $items = [[=items=]];
    $print_constants = [[=print_constants=]];
    $info_senders = [[=info_senders=]];
    $results = [];
    $paper_size = Url::post('paper_size');
    $j = 1;
    $ordersShippingType = AdminOrdersDB::getOrderShippingsByOrders();
    $ordersType = [];// Loại đơn chuyển hàng: GHTK, Viettel...
    $getFullPhoneNumberPrintOrder = 0;
    if(isset($_REQUEST['getFullPhoneNumberPrintOrder'])){
        $getFullPhoneNumberPrintOrder = 1;
    }
    if (!empty($ordersShippingType)) {
        foreach ($ordersShippingType as $value) {
            $ordersType[$value['order_id']] = $value['carrier_id'];
        }
    }
    $censorePhoneNumber = true;
    $prinType = 'ẩn số điện thoại';
    if(AdminOrders::$show_phone_number_print_order && $getFullPhoneNumberPrintOrder == 1){
        $censorePhoneNumber = false;
        $prinType = 'không ẩn số điện thoại';
    }
    foreach ($items as $k_id => $item) {
        $item['stt_ban_in'] = "No. " . $j;
        $item['ten_ban_in'] = "Sản phẩm 01";
        $item['current_date'] = date('d/m/Y 00:00:00');
        $results[$k_id]['nomalItems']['{__DANH_SACH_SP_1__}'] = "Chưa có sản phẩm";
        if(!$censorePhoneNumber){
            $item['mobile'] =  $item['fullMobile'];
            $items[$k_id]['mobile'] =  $item['fullMobile'];
        }
        foreach ($item as $ki => $val_i) {
            //System::debug($print_constants);die;
            foreach ($print_constants as $kc => $val_c) {
                foreach ($val_c['variables'] as $kv => $variable) {
                    if ($kc != 'SP') {
                        if ($variable['name_db'] == $ki) {
                            if ($ki == 'bar_code' || $ki == 'postal_bar_code' || $ki == 'bar_code_id') {
                                if ($ki == 'postal_bar_code' && $ordersType[$item['id']] == 'api_ghtk') {
                                    $valIArray = explode('.', $val_i);
                                    $val_i = end($valIArray);
                                    // System::debug($val_i);
                                }
                                $results[$k_id]['nomalItems'][$kv] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="assets/lib/php-barcode-master/barcode.php?text='. $val_i .'"><br> <center>'. $val_i .'</center></div>';
                            } elseif ($ki == 'bar_code_qr' || $ki == 'postal_bar_code_qr' || $ki == 'bar_code_id_qr') {
                                $results[$k_id]['nomalItems'][$kv] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="generate-qr.php?text='. $val_i .'"><br> <center>'. $val_i .'</center></div>';
                            } elseif ($ki == 'total_price' || $ki == 'discount_price' || $ki == 'shipping_price' || $ki == 'other_price' || $ki == 'price') {
                                $results[$k_id]['nomalItems'][$kv] = number_format($val_i);
                            } else if($ki == 'bar_code_mediadoc_qr'){
                                $results[$k_id]['nomalItems'][$kv] = '<div style="text-align: right; padding-top: 2px;"><img width="60px" src="'.$bar_code_mediadoc.'"></div>';
                            } else {
                                $results[$k_id]['nomalItems'][$kv] = $val_i ?? '';
                            }
                            // price_not_shipping
                        }
                        if ($variable['name_db'] == 'bar_code_large') {
                            $results[$k_id]['nomalItems']['{__MA_VACH_DH_SIZE_LARGE__}'] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="assets/lib/php-barcode-master/barcode.php?text='. $items[$k_id]['bar_code'] .'&size=60"><br> <center>'. $items[$k_id]['bar_code'] .'</center></div>';
                        }
                        if ($variable['name_db'] == 'bar_code_large_qr') {
                            $results[$k_id]['nomalItems']['{__MA_VACH_DH_SIZE_LARGE_QR__}'] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="generate-qr.php?text='. $items[$k_id]['bar_code'] .'&size=5"><br> <center>'. $items[$k_id]['bar_code'] .'</center></div>';
                        }

                        if ($variable['name_db'] == 'postal_bar_code_large') {
                            if ($variable['name_db'] == 'postal_bar_code_large' && $ordersType[$item['id']] == 'api_ghtk') {
                                // System::debug($ordersType[$item['id']]);
                                $valIArray = explode('.', $items[$k_id]['postal_bar_code']);
                                $items[$k_id]['postal_bar_code'] = end($valIArray);
                            }
                            $results[$k_id]['nomalItems']['{__MA_VACH_VAN_DON_SIZE_LARGE__}'] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="assets/lib/php-barcode-master/barcode.php?text='. $items[$k_id]['postal_bar_code'] .'&size=60"><br> <center>'. $items[$k_id]['postal_bar_code'] .'</center></div>';
                        }
                        if ($variable['name_db'] == 'postal_bar_code_large_qr') {
                            if ($variable['name_db'] == 'postal_bar_code_large_qr' && $ordersType[$item['id']] == 'api_ghtk') {
                                // System::debug($ordersType[$item['id']]);
                                $valIArray = explode('.', $items[$k_id]['postal_bar_code']);
                                $items[$k_id]['postal_bar_code'] = end($valIArray);
                            }
                            $results[$k_id]['nomalItems']['{__MA_VACH_VAN_DON_SIZE_LARGE_QR__}'] = '<div style="text-align: center; padding-top: 2px;font-size:11px;"><img src="generate-qr.php?text='. $items[$k_id]['postal_bar_code'] .'&size=5"><br> <center>'. $items[$k_id]['postal_bar_code'] .'</center></div>';
                        }
                        if ($variable['name_db'] == 'price_not_shipping') {
                            $totalPriceNotShipping = $items[$k_id]['total_price'] - $items[$k_id]['shipping_price'];
                            $results[$k_id]['nomalItems']['{__THANH_TIEN_NOT_SHIPPING__}'] = number_format($totalPriceNotShipping);
                        }
                    } else {
                        if (!empty($item['detail_products'])) {
                            $i = 1; $dssp1 = [];
                            foreach ($item['detail_products'] as $k_detail => $val_detail) {
                                $val_detail['stt'] = $i;
                                foreach ($val_detail as $kd => $val_d) {
                                    if ($variable['name_db'] == $kd) {
                                        $results[$k_id]['intervalItems'][$k_detail][$kv] = $val_d;
                                    }
                                }

                                $dssp1[] = trim($val_detail['qty'] . " " . $val_detail['product_name'] . " " . $val_detail['color'] . " " . $val_detail['size']);

                                $i++;
                            }

                            $results[$k_id]['nomalItems']['{__DANH_SACH_SP_1__}'] = implode(", ", $dssp1);
                        }
                    }
                }
            }
        }

        $j++;
    }
    //System::debug($info_senders);die;
    foreach ($info_senders as $key => $value) {
        foreach ($value as $k => $item) {
            if ($k != 'id') {
                foreach ($print_constants as $kc => $val_c) {
                    if (in_array($kc, ['DN', 'NGNN'])) {
                        foreach ($val_c['variables'] as $kv => $variable) {
                            if ($variable['name_db'] == $k) {
                                if($k=='image_url' and $item){
                                    $item = '<img src="'.$item.'" alt="'.$value['name_sender'].'" height="50">';
                                }
                                if($kv != '{__SDT_NGUOI_NHAN__}') {
                                    $results[$key]['nomalItems'][$kv] = $item;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
if(count($results) > 0) {
    //gui thong bao cho shop owner
    if (!AdminOrders::$is_owner) {
        $userInfo = Session::get('user_data');
        //insert bang notification
        $arrDataNotification = [
            'title' => 'Thông báo in đơn hàng',
            'content' => 'Tài khoản ' . $userInfo['id'] . ' đã thực hiện in ' . count($results) . ' đơn hàng loại ' . $prinType . ', thời gian ' . date('H:i:s d/m/Y'),
            'type' => 1,
            'is_public' => 2,
            'notificationable_type' => 0,
        ];
        $notification_id = DB::insert('notifications', $arrDataNotification);

        $ownerInfo = DB::fetch($sql = 'select users.id, account.group_id from account inner join users on account.id = users.username where account.id = "' . AdminOrders::$group['code'] . '"');
        //insert bang notification_received
        DB::insert('notifications_recieved', [
            'notification_id' => $notification_id,
            'user_id' => $ownerInfo['id'],
            'group_id' => $ownerInfo['group_id'],
            'is_print_notification' => 1,
            'is_read' => 0,
        ]);
    }

    //log lich su in don
    $arrOrderId = array();
    foreach($results as $key => $row){
        if(!in_array($row['nomalItems']['{__MA_DH__}'],$arrOrderId)){
            $arrOrderId[] = $row['nomalItems']['{__MA_DH__}'];
        }
    }
    $requet = $_REQUEST;
    $desc = 'In ' . sizeof($results) . ' đơn hàng';
    $desc .= '<br><div class="small text-gray">Thiết bị: ' . $_SERVER['HTTP_USER_AGENT'] . '</div>';
    $arrPatchData = array(
        'censored_phone_number' => ($censorePhoneNumber)?1:0,
        'list_export_order_id' => implode(', ', $arrOrderId)
    );
    System::log('PRINT', 'In danh sách đơn hàng', $desc, '', '', false, $arrPatchData);
}
    $results = array_values($results);
    // System::debug($results);
?>
<style>
    body {
        height: auto;
    }
    .content {
        min-height: 250px;
        padding: 15px;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: rgb(255, 255, 255);
        border-top: 3px solid rgb(210, 214, 222);
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .box.box-solid {
        border-top: 0;
    }
    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }
    .donhang-search-form {
        margin-bottom: 15px;
    }
    #list-status {
        border-bottom: 1px solid rgb(204, 204, 204);
        margin-bottom: 5px;
    }
    .btn-default .badge {
        color: rgb(255, 255, 255);
        background-color: rgb(51, 51, 51);
    }
    .page-bottom {
        border-top: 1px solid rgb(241, 241, 241);
        padding-top: 10px;
    }
    .float-right {
        float: right
    }
    ul.list-group li:nth-child(2n) {
        background: rgb(245, 245, 245)
    }
    .timeline>li>.timeline-item>.timeline-header {
        font-size: 13px;
    }
    .timeline:before {
        background: rgb(221, 221, 221);
    }
    a.btn-abs {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 99;
        left: 0;
        top: 0;
        right: 0;
    }
    ul.timeline li:hover .timeline-item {
        background: rgb(217, 237, 247)
    }
    h3.panel-heading-title {
        margin: 0px;
    }
    .cke_button__templateconsignment_label {
        display: inline;
    }
    .rowItemKeyword {
        margin-bottom: 5px;
    }
    .panel-heading {
        padding: 10px 15px;
    }
    #list-tabs {
        margin-bottom: 10px;
    }
    .A4-A5:not(:last-child) {
        page-break-after: always;
        margin-top: 20px;
    }
    .A4-A5-NGANG {
        font-size: 13px;
    }
    .A4-A5-NGANG:not(:last-child) {
        page-break-after: always;
    }
    .break-class {
        page-break-after: always;
    }
    .page-break {
        page-break-after: always;
        clear: both
    }
    .A4IN8, .A4IN6, .A4IN4DOC, .A4IN4,.A4IN8_BARCODE {
        width:48%;
        float:left;
        margin:0px 8px 8px 0px;
        background: #FFF;
        overflow: hidden;
    }
    .A4IN8 > table, .A4IN6 > table, .A4IN8_BARCODE > table, .A4IN4 > table {
        background: #fff;
        border: 1px solid black;
    }
    .float-left {
        float: left;
    }
    .A4IN4DOC {
        /* border: 1px solid black; */
    }
    @page {
        margin: 0;
        padding:0;
    }
    .A4IN4DOC > table {
        font-size: 11px !important;
        width: 10cm;
        height: 14.3cm;
        margin-right: 2px;
        margin-bottom: 2px;
    }
    @media print {
        .box-add-note{display:none !important;}
        #btn-print, .hidden-print, .navbar-fixed-bottom {
            display: none;
        }
        .A4IN8, .A4IN6, .A4IN4DOC,.A4IN8_BARCODE,.A4IN4 {
            width:48%;
            float:left;
            margin:0px 8px 8px 0px;
            background: #FFF;
            overflow: hidden;
        }
        .A4IN8 > table, .A4IN6 > table, .A4IN4DOC > table , .A4IN8_BARCODE > table , .A4IN4 > table {
            background: #fff;
            border: 1px solid black;
        }
        .A4-A5:not(:last-child) {
            page-break-after: always;
            margin-top: 20px;
        }
        .break-class {
            page-break-after: always;
        }
        .page-break {
            page-break-after: always;
            clear: both
        }
        .A4-A5 {
            border:1px dotted #333;
            padding: 5px;
        }
        .A4-A5-NGANG:not(:last-child) {
            page-break-after: always;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
            clear: both
        }
        .K80{border:0px;}
    }
</style>
<?php
    if ($paper_size == 'A4IN4DOC') {
?>
<style>
    @page {
        size: A4;
        margin: 0;
    }
    .A4IN4DOC {
        height: 14.3cm;
    }
</style>
<?php
    }
?>
<div class="container">
    <div id="page">
        <section class="content-header clearfix">
            <h1 class="page-title float-left"><i class="fa fa-print"></i> In đơn hàng</h1>
            <div class="float-right text-right">
                <a href="javascript:void(0);" class="btn btn-warning hidden-print" id="btn-print"><i class="fa fa-print"></i> In ngay</a>
                <a href="#" onclick="window.close();" class="btn btn-default hidden-print"> x Đóng lại</a>
            </div>
        </section>
        <section class="content">
            <div id="content">
                <div class="box box-solid">
                    <div class="box-body" id="box-content">
                        <style>
                            @media print {
                                #btn-print, .hidden-print, .navbar-fixed-bottom {
                                    display: none;
                                }
                                .A4IN8, .A4IN6,.A4IN8_BARCODE,.A4IN4 {
                                    width:48%;
                                    float:left;
                                    margin:0px 8px 8px 0px;
                                    background: #FFF;
                                    overflow: hidden;
                                }
                                .A4IN8 > table, .A4IN6 > table, .A4IN8_BARCODE > table , .A4IN4 > table {
                                    background: #fff;
                                    border: 1px solid black;
                                }
                                .A4-A5:not(:last-child) {
                                    page-break-after: always;
                                    margin-top: 20px;
                                }
                                .break-class {
                                    page-break-after: always;
                                }
                                .page-break {
                                    page-break-after: always;
                                    clear: both
                                }
                                .A4-A5 {
                                    border:1px dotted #333;
                                    padding: 5px;
                                }
                                .A4-A5-NGANG:not(:last-child) {
                                    page-break-after: always;
                                }
                                .A4-A5-NGANG {
                                    font-size: 11px !important;
                                }
                                .A4IN4DOC {
                                    float: left;
                                    margin-right: 2px;
                                    margin-bottom: 2px;
                                    width:49%;
                                    margin-top: 5px;
                                }
                                .A4IN4DOC > table {
                                    font-size: 11px !important;
                                    width: 10cm;
                                    height: 14.3cm;
                                    margin: 0px;
                                }
                                #box-content {
                                    margin: 0;
                                    border: initial;
                                    border-radius: initial;
                                    width: initial;
                                    min-height: initial;
                                    box-shadow: initial;
                                    background: initial;
                                }
                                .A4IN4DOC td {
                                    padding: 5px;
                                }
                            }
                        </style>
                        <?php
                        if ($paper_size == 'A4IN4DOC') {
                            ?>
                            <style>
                                @media print {
                                    .A4IN4DOC {
                                        height: 14.3cm
                                    }
                                }
                            </style>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<script>
    var paper_size = '<?= $paper_size ?>';
    var templates = `<?= $templates ?>`;
    var page_variables = <?= json_encode($results, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
    var pageFunctions = {
        replaceTemplate: function(template, variables) {
            if (!template) {
                return;
            }

            var i, j, keyword;
            if (typeof variables != 'undefined' && typeof variables.nomalItems != 'undefined') {
                for (i in variables.nomalItems) {
                    template = this.replaceAll(template, i, variables.nomalItems[i])
                }
            }

            var tempDoc = $('<div/>').html(template);
            var hasIntervalConsignment = false;
            var tableInterval = null;
            var trStart = false;
            if (typeof variables != 'undefined' && typeof variables.intervalItems != 'undefined') {
                for (j in variables.intervalItems) {
                    for (keyword in variables.intervalItems[j]) {
                        if (tempDoc.find(":contains('" + keyword + "')").length) {
                            tempDoc.find(":contains('" + keyword + "')").each(function() {
                                if (!tableInterval) {
                                    tableInterval = $(this).closest('table');
                                }
                            });

                            hasIntervalConsignment = true;
                            break;
                        }
                    }
                }
            }

            if (hasIntervalConsignment) {
                tableInterval = null;
                trStart = false;
                if (typeof variables != 'undefined' && typeof variables.intervalItems != 'undefined') {
                    for (j in variables.intervalItems) {
                        for (keyword in variables.intervalItems[j]) {
                            tempDoc.find(":contains('" + keyword + "')").filter(function() {
                                return ($(this).clone().children().remove().end().filter(":contains('" + keyword + "')").length > 0)
                            }).each(function() {
                                if (!tableInterval) {
                                    tableInterval = $(this).closest('table');
                                }

                                var trIndex = tableInterval.find('tbody tr').index($(this).closest('tr'));
                                if (trIndex != -1) {
                                    if (trStart === false || trStart == -1 || trStart > trIndex) {
                                        trStart = trIndex;
                                    }
                                }
                            });
                        }
                    }
                }

                intervalHtml = '';
                tableInterval.find('tbody tr').each(function() {
                    if (tableInterval.find('tbody tr').index($(this)) == trStart) {
                        intervalHtml += $('<div/>').append($(this)).html();
                    }
                });
                var replaceInterval = '';
                for (i in variables.intervalItems) {
                    var temp = intervalHtml;
                    for (var key in variables.intervalItems[i]) {
                        temp = this.replaceAll(temp, key, variables.intervalItems[i][key]);
                    }

                    replaceInterval += temp;
                }

                tableInterval.find('tbody').append(replaceInterval);
                template = tempDoc.html();
            }

            return template
        },
        replaceAll: function(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }
    }

    $(function() {
        var html = '<div class="wrap-content">';
        if (Object.keys(page_variables).length) {
            var j = 1;
            var item_per_page = 8;
            if (paper_size == 'A4IN6' || paper_size == 'A4IN8_BARCODE') {
                item_per_page = 6
            } else if (paper_size == 'A4IN4DOC' || paper_size == 'A4IN4') {
                item_per_page = 4
            } else if(paper_size == 'K80'){
                item_per_page = 1
            }

            $.each(page_variables, function(i, variables) {
                var item = pageFunctions.replaceTemplate(templates, variables);
                let break_element = (Number.isInteger((j)/item_per_page) && j != page_variables.length) ? '<div class="page-break"></div>' : ''
                // let break_element = ''
                html += $('<div class="' + paper_size + '" />').html(item + '<br />')[0].outerHTML + break_element

                j++;
            })
        }

        html += '</div>'
        $('#box-content').append(html)

        $('#btn-print').click(function() {
            ClickHereToPrint('ifrmPrint','box-content');
        })

        // printWebPart('box-content')
    })
</script>
