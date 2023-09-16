<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use mikehaertl\wkhtmlto\Pdf;
class SendFileEmailToWarehouse extends Form{
    protected $map;
    protected $basePath;
    function __construct(){
        Form::Form('SendFileEmailToWarehouse');
        $this->basePath = dirname(__DIR__,5);
    }
    function draw(){
        $arrReturn = array();
        $userInfo = Session::get('user_data');
        $groupId = Session::get('group_id');
        $checkSize = Url::get('checkSize');
        $groupInfo = AdminOrders::$group;
        $shopName = $groupInfo['name'];
        $data = Url::get('dataSendMail');
        $invoice = (Url::get('invoice'));
        $warehouseName = DB::escape(Url::get('warehouseName'));
        $censoredPhoneNumber = 1; // mac dinh la an sdt
        if (is_group_owner() || AdminOrders::$quyen_indon) {
            $censoredPhoneNumber =  Url::iget('checkPhone') === 0? 1 : 0;
        }
        $carrierName = DB::escape(Url::get('nameCarrier'));
        $carrierId = DB::escape(Url::get('idCarrier'));
        $warehouseEmail = Url::get('email');
        $warehouseId = DB::escape(Url::get('warehouseId')); 
        $orderIds = DB::escape(Url::get('ids'));
        $keySize = Url::get('paperSize');
        $printPaperSize = $this->paperSizes();
        $inforPaperSize = $printPaperSize[$keySize]['name'];
        $sqlWarehouse = "SELECT id,email,group_id FROM qlbh_warehouse WHERE id = $warehouseId LIMIT 0,1";
        $query = DB::fetch($sqlWarehouse);
        if ($query) {
            if ($query['group_id'] == 0) {
                $queryWarehouseShop = DB::fetch("SELECT * FROM qlbh_warehouse WHERE kho_tong_shop = 1 AND  group_id = $groupId");
                if (!$queryWarehouseShop || $queryWarehouseShop['email'] !== $warehouseEmail) {
                    $arrReturn = array(
                        'status' => 0,
                        'message' => 'Kho chưa khai báo Email hoặc Email không chính xác!',
                    );
                    echo json_encode($arrReturn);
                    return;
                }
            } else if ($query['group_id'] != 0){
                if ($query['group_id'] != $groupId) {
                    $arrReturn = array(
                        'status' => 0,
                        'message' => 'Kho không thuộc shop của bạn!',
                    );
                    echo json_encode($arrReturn);
                    return;
                }
            } else {
                if ($query['email'] !== $warehouseEmail || !$query['email'] ) {
                    $arrReturn = array(
                        'status' => 0,
                        'message' => 'Kho chưa khai báo Email hoặc Email không chính xác!',
                    );
                    echo json_encode($arrReturn);
                    return;
                }
            }
            
        } else {
            $arrReturn = array(
                'status' => 0,
                'message' => 'Kho không tồn tại',
            );
            echo json_encode($arrReturn);
            return;
        }
        
        if (!$groupInfo['email']) {
            $arrReturn = array(
                'status' => 0,
                'message' => 'Shop chưa khai báo Email!',
            );
            echo json_encode($arrReturn);
            return;
        }
        require_once 'packages/core/includes/utils/mailer/ConfigMailer.php';
        require_once ROOT_PATH . 'packages/core/includes/common/LogTypeExtra.php';
        $temPath = $this->basePath . '/cache/tempdf/';
        $count = $this->countSendEmail($groupId, $warehouseId, $carrierId);

        $fileName = $warehouseName.' - '.$carrierName.' - '.date('d-m-Y').' - lần ' . $count . '.pdf';
        
        // d/s shop fix thong tin
        $groupIds = array(14649,14032,6842,3347,20263,14681,14133,14018,18149,14161,15254,14136,20247,15257,14676,15261,20231,18164,15121,18746,19487,1168,20535,20875,14143,14172,20103,13587,20119,20135,16335,18532,13765,2543,20541,6838,21102,20550,11673,15919,20215,13720,18160,15280,13711,2547,14043,13769,13853,6773,16296,13805,16312,14063,18650,14693,16306,20183,20167,16592,15130,18034,6833,11666,16265,19639,20055,6847,19959,20071,15905,18125,18494,18162,13976,19703,15192,13349,19655,13977,20519,19306,15201,20023,19991,19671,13754,19176,13607,19719,19687,1227,21151,19975,15215,20087,20007,1270,18135,19470,18194,19458,16914,16939,6104,13761,18209,18182,19473,15899,18234,13780,6121,19447,16928,13900,20446,19895,16941,15885,18187,13069,15897,19927,13749,19911);
        
        $infoSize = "Khổ dọc";
        if ($checkSize == 0) {
            $infoSize = "Khổ ngang";
        }
        $temPath = $this->basePath . '/cache/tempdf/';
        $filePath = $temPath . $fileName;
        $filePath = $this->createPDF($data, $filePath, $checkSize);

        if($invoice){
            $invoiceName = 'Phiếu kho - ' . $warehouseName.' - '.$carrierName.' - '.date('d-m-Y').'.pdf';
            $invoicePath = $temPath . $invoiceName;
            $invoicePath = $this->createPDF($invoice, $invoicePath, 1);
        }

        $configMailer = new ConfigMailer();
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $configMailer::$host;
        $mail->SMTPAuth = $configMailer::$smtpAuth;
        $mail->Username = $configMailer::$username;
        $mail->Password = $configMailer::$password;
        $mail->SMTPSecure = $configMailer::$smtpsSecure;
        $mail->Port = $configMailer::$port;
        $mail->CharSet = $configMailer::$charset;
        $mail->isHTML(true);
        $mail->setfrom('noreply@delivery.tuha.vn',$shopName);
        $mail->addaddress($warehouseEmail, $warehouseName);
        $subject = $shopName.' - '.$carrierName.' - Thông tin đơn hàng ngày ' . date('d-m-Y');
        if (in_array($groupId, $groupIds)) {
            $subject = $shopName.' - '.$carrierName.' - ' . date('d-m-Y') . ' - lần ' . $count . ' - ' . count(explode(',', $orderIds)) . ' đơn';
        }
        $mail->Subject = $subject;
        $mailBody = 'Kính gửi đơn vị '.$warehouseName;
        $mailBody .= '<br>';
        $mailBody .= 'Tôi là ' . $userInfo['full_name'] . ' – Nhân viên Shop/Công ty ' . $shopName . '.';
        $mailBody .= '<br>';
        $mailBody .= 'Thay mặt Công ty/Shop '.$shopName.', tôi gửi tệp tin đính kèm (' . $fileName . '), trong đó đã bao gồm các thông tin về file In đơn hàng phát sinh trong ngày ' . date('d-m-Y') . ' để chuyển tới đơn vị vận chuyển ('. $carrierName .')<br>';
        $mailBody .= $invoice ? 'Ngoài ra tôi còn gửi kèm <b>File phiếu Xuất kho('.$invoiceName.').</b>' : '';
        $mailBody .= '<br>';
        $mailBody .= 'Nếu anh/chị có thắc mắc hay cần chỉnh sửa nội dung, vui lòng phản hồi lại cho tôi qua email: (' . $groupInfo['email'] . ')';
        $mailBody .= '<br>';
        $mailBody .= 'Cảm ơn sự đồng hành và hỗ trợ của của anh/chị. Chúc anh/chị có một ngày làm việc hiệu quả.';
        $mailBody .= '<br><br>';
        $mailBody .= 'Trân trọng!';
        if (in_array($groupId, $groupIds)) {
            $mailBody = 'Nếu anh/chị có thắc mắc hay cần chỉnh sửa nội dung, vui lòng phản hồi lại cho tôi qua email: (' . $groupInfo['email'] . ')';
        }
        $mail->Body = $mailBody;
        $mail->addAttachment($filePath);
        $invoice && $mail->addAttachment($invoicePath);
        if($mail->send())
        {
            $arrReturn = array(
                'status' => 1,
                'message' => 'Đã gửi mail file In đơn cho Bộ phận kho!',
            );
            $desc = 'Gửi email File in '.count(explode(',', $orderIds)).' đơn '.$inforPaperSize .' - '. $infoSize .' cho kho';
            $arrPatchData = array(
                                'list_export_order_id' => $orderIds,
                                'carrier' => $warehouseName,
                                'carrier_email' => $warehouseEmail,
                                'censored_phone_number' => $censoredPhoneNumber? 1 : 0,
                                'log_type_extra' => $invoice ? LogTypeExtra::SEND_EMAIL_TO_WAREHOUSE_ATTACH_INVOICE : 0,
                            );
            System::log('SEND_EMAIL_TO_WAREHOUSE', 'Gửi email File In đơn cho Kho', $desc, '', '', false, $arrPatchData);
            unlink($filePath);
            unlink($invoicePath);
            $arrReturn = array(
                'status' => 1,
                'message' => 'SUCCESS',
            );
        } else {
             $arrReturn = array(
                'status' => 0,
                'message' => 'Gửi email thất bại',
            );
        }
        
    }


    /**
     * Creates a pdf.
     *
     * @param      <type>                          $data      The data
     * @param      <type>                          $filePath  The file path
     *
     * @throws     \PHPMailer\PHPMailer\Exception  (description)
     *
     * @return     <type>                          ( description_of_the_return_value )
     */
    private function createPDF($data, $filePath, $orientation = 0)
    {
        $pdf = new Pdf([
            'binary' => '/usr/local/bin/wkhtmltopdf',
            'ignoreWarnings' => true,
            'orientation' => $orientation ? 'Portrait' : 'Landscape',
            'encoding' => 'UTF-8',
            'commandOptions' => [
                'useExec' => false,
                'escapeArgs' => false,
                'procOptions' => array(
                'bypass_shell' => true,
                'suppress_errors' => true,
                ),
            ],
        ]);
        $pdf->addPage($data);
        if (!$pdf->saveAs($filePath)) {
            throw new Exception(json_encode($pdf->getError()));
        }

        return $filePath;
    }
    function paperSizes() {
        return [
            'A4-A5' => [
                'types' => ['DON_HANG'],
                'name' => 'Khổ A4|A5'
            ],
            'A4-A5-NGANG' => [
                'types' => ['DON_HANG'],
                'name' => 'Khổ A4|A5 (Khổ ngang)'
            ],
            'A4IN4DOC' => [
                'types' => ['DON_HANG'],
                'name' => 'Mẫu 4 đơn hàng trên 1 trang A4 (Viettel - Khổ dọc)'
            ],
            'A4IN8' => [
                'types' => ['DON_HANG'],
                'name' => 'Mẫu 8 đơn hàng trên 1 trang (Khổ dọc)'
            ],
            'A4IN6' => [
                'types' => ['DON_HANG'],
                'name' => 'Mẫu 6 đơn hàng trên 1 trang (Khổ ngang)'
            ],
            'A4IN8_BARCODE' => [
                'types' => ['DON_HANG'],
                'name' => 'Mẫu 6 đơn hàng trên 1 trang có mã vạch (Khổ ngang)'
            ],
            'A4IN4' => [
                'types' => ['DON_HANG'],
                'name' => 'Mẫu 4 đơn hàng trên 1 trang (Khổ ngang)'
            ],
            'K80' => [
                'types' => ['DON_HANG'],
                'name' => 'Khổ K80'
            ]/*
            '4IN1A4' => [
                'types' => ['DON_HANG'],
                'name' => '4 phiếu gửi 1 trang A4'
            ]*/
        ];
    }

    function countSendEmail($groupId, $warehouseId, $carrierId)
    {
        if ($result = $this->getCount($groupId, $warehouseId, $carrierId)) {
            $this->increaseCount(++$result['count'], $result['id']);

            return $result['count'];
        }

        return $this->createCount($groupId, $warehouseId, $carrierId);
    }

    /**
     * Gets the count.
     *
     * @param      <type>  $groupId      The group identifier
     * @param      <type>  $warehouseId  The warehouse identifier
     * @param      <type>  $carrierId    The carrier identifier
     *
     * @return     <type>  The count.
     */
    private function getCount($groupId, $warehouseId, $carrierId)
    {
        $today = strtotime(date('Y-m-d'));
        $sqlFind = "SELECT id,count FROM send_email_print_order WHERE send_email_print_order.sending_time = $today
                        AND warehouse_id = ".DB::escape($warehouseId) ."
                        AND carrier_id = ".DB::escape($carrierId)."
                        AND group_id = ".DB::escape($groupId);
        return DB::fetch($sqlFind);
    }

    /**
     * Creates a count.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function createCount($groupId, $warehouseId, $carrierId)
    {
        $arrayInsert = [
            'group_id'=>DB::escape($groupId),
            'warehouse_id'=>DB::escape($warehouseId),
            'carrier_id'=>DB::escape($carrierId),
            'count'=> 1,
            'created_at' => date('Y-m-d H:i:s'),
            'sending_time' => strtotime(date('Y-m-d')),
        ];

        DB::insert('send_email_print_order',$arrayInsert);

        return 1;
    }

    /**
     * Increases the count.
     *
     * @param      <type>  $count  The count
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function increaseCount($count, $id)
    {
        $count = DB::escape($count);
        $id = DB::escape($id);
        DB::update('send_email_print_order',['count' => $count],'id = ' . $id);

        return $count;
    }
}
?>
