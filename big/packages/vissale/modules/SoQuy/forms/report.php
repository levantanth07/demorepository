<?php

class ReportModuleForm extends Form
{

    function __construct()
    {
        Form::Form('ReportModuleForm');
    }

    function draw()
    {
        $this->map = [];
        $this->map['title'] = 'Sổ quỹ tiền mặt';
        $group_id = Url::iget('group_id')?Url::iget('group_id'):Session::get('group_id');
        $cond = " and cf.group_id=".$group_id.' AND IFNULL(cf.del,0) = 0';

        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/Y');
        }

        $start_time = "";
        if (!empty($_REQUEST['date_from'])) {
            $start_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_from']));
            $cond .= " AND cf.bill_date >= '$start_time'";
        }

        $end_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_to']));
        $loai_quy = "";
        $payment_type = Url::get('phuong_thuc_thanh_toan');

        switch ($payment_type) {
            case 1:
                $cond .= " AND cfd.payment_method = $payment_type";
                $loai_quy = "TIỀN MẶT";
                break;
            case 2:
                $cond .= " AND cfd.payment_method = $payment_type";
                $loai_quy = "CHUYỂN KHOẢN";
                break;
            case 3:
                $cond .= " AND cfd.payment_method = $payment_type";
                $loai_quy = "QUẸT THẺ";
                break;
            case 4:
                $loai_quy = "TẤT CẢ";
                break;
            default:
                //chỉ hiển thị quỹ tiền mặt
                $_REQUEST['phuong_thuc_thanh_toan'] = 1;
                $payment_type = $_REQUEST['phuong_thuc_thanh_toan'];
                $loai_quy = "TIỀN MẶT";
                $cond .= " AND cfd.payment_method = $payment_type";
                break;
        }

        $cond .= " AND cf.bill_date <= '$end_time'";
        $ton_quy = SoQuyDB::getTonQuy($start_time, $end_time, $payment_type);
        $title_no_result = 'Vui lòng nhấn nút Xem báo cáo';
        $this->map['ton_quy'] = $ton_quy;
        if(Url::get('view_report')){
            $items = SoQuyDB::getSoQuy($cond);
            if (empty($items)) {
                $title_no_result = 'Không có dữ liệu !';
            } else {
                foreach ($items as $k => $item) {
                    $items[$k]['bill_code'] = SoQuy::generatePrefixType($item['bill_type']) .'_'.SoQuy::generateCode($item['bill_number']);
                }
            }
        } else {
            $items = [];
        }

        $this->map['items'] = $items;
        $this->map['title_no_result'] = $title_no_result;
        $this->map['loai_quy'] = $loai_quy;
        $this->map['phuong_thuc_thanh_toan_list'] = array('4' => 'Tất cả', 1 => 'Tiền mặt', 2 => 'Chuyển khoản', 3 => 'Thẻ');
        $this->map['group_id_list'] = array(''=>'Chọn chi nhánh') + MiString::get_list(SoQuyDB::get_groups(Session::get('group_id')));
        $this->parse_layout('report', $this->map);
    }
}