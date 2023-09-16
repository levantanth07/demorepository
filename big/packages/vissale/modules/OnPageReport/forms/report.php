<?php
/**
 * Created by PhpStorm.
 * User: nguyendinhquy
 * Date: 5/3/18
 * Time: 11:49 AM
 */
class ReportForm extends Form
{
    private $_onPageReportDb;
    function RepportForm()
    {
        Form::Form('ReportForm');
    }
    function draw(){
        $quyen_chia_don = check_user_privilege('CHIADON');
        $xem_khoi_bc_truc_page = check_user_privilege('NHOM_BC_TRUC_PAGE_TINH_TRANG_DON');
        if(!$quyen_chia_don && !$xem_khoi_bc_truc_page){
             Url::js_redirect(false, 'Bạn không có quyền truy cập!');
        }
        $this->map = array();
        $this->map['reports_today'] = array();
        $this->map['account_type'] = $account_type = Session::get('account_type');
        if(!isset($_REQUEST['date_from'])) {
            $_REQUEST['date_from'] = date("d/m/Y");
        }
        if(!isset($_REQUEST['date_to'])) {
            $_REQUEST['date_to'] = date("d/m/Y");
        }
        $date_from = Date_Time::to_sql_date($_REQUEST['date_from']);
        $date_to = Date_Time::to_sql_date($_REQUEST['date_to']);
        $cond = 'orders.created >= "'.$date_from.'" and orders.created <= "'.$date_to.' 23:59:59" and (orders.assigned = 0 or orders.assigned is null) and status_id=10';
        $group_id = Session::get('group_id');
        if(Url::get('group_id')){
            $group_id = Url::get('group_id');
        }
        $master_group_id = Session::get('master_group_id');
        if($account_type==3){//khoand edited in 30/09/2018
            $cond .= ' and (orders.group_id='.$group_id.' or orders.master_group_id = '.Session::get('group_id').')';
        }elseif($master_group_id){
            $cond .= ' and (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.'))';
        }else{
            $cond .= ' and orders.group_id='.$group_id.'';
        }

        $this->map['total_not_assigned_yet'] = DB::fetch('select count(id) as total from  orders where '.$cond.'','total');
        $this->_onPageReportDb = new OnPageReportDb();
        if(!empty($_REQUEST['form_block_id'])){
            /*$shift_1_time = Array(
                "from" =>  $date_from.' 08:00:00',
                "to" =>  $date_to.' 11:59:59'
            );
            $shift_2_time = Array(
                "from" =>  $date_from.' 13:30:00',
                "to" =>  $date_to.' 17:29:59'
            );
            $shift_3_time = Array(
                "from" =>  $date_from.' 18:00:00',
                "to" =>  $date_to.' 19:59:59'
            );
            $shift_4_time = Array(
                "from" =>  $date_from.' 22:00:00',
                "to" => $date_to.' 23:59:59'
            );*/
            $shift_1_time = Array(
                "from" =>  $date_from.' 06:00:00',
                "to" =>  $date_to.' 11:59:59'
            );
            $shift_2_time = Array(
                "from" =>  $date_from.' 12:00:00',
                "to" =>  $date_to.' 17:59:59'
            );
            $shift_3_time = Array(
                "from" =>  $date_from.' 18:00:00',
                "to" =>  $date_to.' 21:59:59'
            );
            $shift_4_time = Array(
                "from" =>  $date_from.' 22:00:00',
                "to" => $date_to.' 23:59:59'
            );
            $shift_1 = $this->_onPageReportDb->getPageAssigneeReportByDate($shift_1_time, $group_id);

            $shift_2 = $this->_onPageReportDb->getPageAssigneeReportByDate($shift_2_time, $group_id);
            $shift_3 = $this->_onPageReportDb->getPageAssigneeReportByDate($shift_3_time, $group_id);
            $shift_4 = $this->_onPageReportDb->getPageAssigneeReportByDate($shift_4_time, $group_id);
            $shifs = [ $shift_1, $shift_2, $shift_3 ];
            $all_shifts = [ $shift_1, $shift_2, $shift_3, $shift_4 ];
            $id = 1;
            $result = [0];
            foreach( $shift_1 as $assignee_in_shift ) {
                $result[] = array (
                    "id" => $id,
                    "username" => $assignee_in_shift['username'],
                    "name" => $assignee_in_shift['name'],
                    "shift_1" => $this->__get_assignee_report_by_shift($assignee_in_shift),
                    "shift_2" => $this->__get_assignee_report_by_shift($this->__get_assignee_shift_by_username($assignee_in_shift['username'], $shift_2)),
                    "shift_3" => $this->__get_assignee_report_by_shift($this->__get_assignee_shift_by_username($assignee_in_shift['username'], $shift_3)),
                    "shift_4" => $this->__get_assignee_report_by_shift($this->__get_assignee_shift_by_username($assignee_in_shift['username'], $shift_4)),
                    "total_cancel_count" => $this->sumary_by_shift_and_status($shifs, 'cancel_count', $assignee_in_shift['username']),
                    "total_duplicated" =>  $this->sumary_by_shift_and_status($shifs, 'duplicated', $assignee_in_shift['username']),
                    "total_new_count" =>  $this->sumary_by_shift_and_status($shifs, 'new_count', $assignee_in_shift['username']),
                    "total_all_number" =>  $this->sumary_by_shift_and_status($all_shifts, 'all_number', $assignee_in_shift['username']) + $this->sumary_by_shift_and_status($shifs, 'cancel_count', $assignee_in_shift['username']),
                );
                $id++;
            }
            $result[] = array (
                "id" =>  '',
                "username" => 'Tổng',
                "name" =>  'Tổng',
                "shift_1" =>  [ 1 => Array(
                    "cancel_count" =>  $this->count_all_assignee_by_shift_status($shift_1, 'cancel_count'),
                    "duplicated" => $this->count_all_assignee_by_shift_status($shift_1, 'duplicated'),
                    "new_count" => $this->count_all_assignee_by_shift_status($shift_1, 'new_count'),
                    "all_number" => $this->count_all_assignee_by_shift_status($shift_1, 'all_number')
                )],
                "shift_2" => [ 1 => Array(
                    "cancel_count" =>  $this->count_all_assignee_by_shift_status($shift_2, 'cancel_count'),
                    "duplicated" => $this->count_all_assignee_by_shift_status($shift_2, 'duplicated'),
                    "new_count" => $this->count_all_assignee_by_shift_status($shift_2, 'new_count'),
                    "all_number" => $this->count_all_assignee_by_shift_status($shift_2, 'all_number')
                )],
                "shift_3" => [ 1 => Array(
                    "cancel_count" =>  $this->count_all_assignee_by_shift_status($shift_3, 'cancel_count'),
                    "duplicated" => $this->count_all_assignee_by_shift_status($shift_3, 'duplicated'),
                    "new_count" => $this->count_all_assignee_by_shift_status($shift_3, 'new_count'),
                    "all_number" => $this->count_all_assignee_by_shift_status($shift_3, 'all_number')
                )],
                "shift_4" => [ 1 => Array(
                    "cancel_count" =>  $this->count_all_assignee_by_shift_status($shift_4, 'cancel_count'),
                    "duplicated" => $this->count_all_assignee_by_shift_status($shift_4, 'duplicated'),
                    "new_count" => $this->count_all_assignee_by_shift_status($shift_4, 'new_count'),
                    "all_number" => $this->count_all_assignee_by_shift_status($shift_4, 'all_number')
                )],
                "total_cancel_count" => (
                    $this->count_all_assignee_by_shift_status($shift_1, 'cancel_count')
                    + $this->count_all_assignee_by_shift_status($shift_2, 'cancel_count')
                    + $this->count_all_assignee_by_shift_status($shift_3, 'cancel_count')
                    + $this->count_all_assignee_by_shift_status($shift_4, 'cancel_count'))
            ,
                "total_duplicated" =>  ($this->count_all_assignee_by_shift_status($shift_1, 'duplicated')
                    + $this->count_all_assignee_by_shift_status($shift_2, 'duplicated')
                    + $this->count_all_assignee_by_shift_status($shift_3, 'duplicated')
                    + $this->count_all_assignee_by_shift_status($shift_4, 'duplicated')) ,
                "total_new_count" =>  ($this->count_all_assignee_by_shift_status($shift_1, 'new_count')
                    + $this->count_all_assignee_by_shift_status($shift_2, 'new_count')
                    + $this->count_all_assignee_by_shift_status($shift_3, 'new_count')
                    + $this->count_all_assignee_by_shift_status($shift_4, 'new_count') ),
                "total_all_number" => ($this->count_all_assignee_by_shift_status($shift_1, 'all_number')
                    + $this->count_all_assignee_by_shift_status($shift_2, 'all_number')
                    + $this->count_all_assignee_by_shift_status($shift_3, 'all_number')
                    + $this->count_all_assignee_by_shift_status($shift_4, 'all_number')),
            )
            ;
            $this->map['reports_today'] =  ($result);
        }
        $date_from = new DateTime($date_from);
        $date_to = new DateTime($date_to);
        if($date_from != $date_to){
            $this->map['date_report'] =   $date_from->format('d/m/Y').' - '.$date_to->format('d/m/Y');
        }else{
            $this->map['date_report'] =   $date_from->format('d/m/Y');
        }
        $this->map['group_id_list'] = array('' => 'Chọn công ty') + MiString::get_list($this->_onPageReportDb->get_groups());
        $bundles = isObd() ? $this->_onPageReportDb->getBundles() : $this->_onPageReportDb->get_bundles();
        $this->map['bundle_id_list'] = array('' => 'Chọn phân loại đơn hàng') + MiString::get_list($bundles);
        $this->parse_layout('report',$this->map);
    }
    function __get_assignee_shift_by_username($username, $shift) {
        foreach ($shift as $assignee_by_shift) {
            if($assignee_by_shift['username'] === $username) {
                return $assignee_by_shift;
            }
        }
        return [];
    }
    function __get_assignee_report_by_shift($assignee_in_shift) {

        return [ 1 => Array(
            "cancel_count" =>  $assignee_in_shift['cancel_count'] ? $assignee_in_shift['cancel_count'] : '',
            "duplicated" => $assignee_in_shift['duplicated'] ? $assignee_in_shift['duplicated'] : '',
            "new_count" => ($assignee_in_shift['all_number'] - $assignee_in_shift['duplicated']) !== 0 ? ($assignee_in_shift['all_number'] - $assignee_in_shift['duplicated']) : '',
            "all_number" => $assignee_in_shift['all_number'] ? $assignee_in_shift['all_number'] : ''
        )];
    }
    function sumary_by_shift_and_status($shifts, $status, $username) {
        $sum = 0;
        foreach ($shifts as $shift) {
            foreach ($shift as $assignee) {
                if($assignee['username'] === $username) {
                    if($status === 'new_count'){
                        $sum += ($assignee['all_number'] - $assignee['duplicated']);
                    }else {
                        $sum += $assignee[$status];
                    }
                }
            }
        }
        return $sum;// == 0 ? '' : $sum;
    }
    function count_all_assignee_by_shift_status($shift, $status) {
        $sum = 0;
        foreach ($shift as $assignee) {
            if($status === 'new_count'){
                $sum += ($assignee['all_number'] - $assignee['duplicated']);
            }else {
                $sum += $assignee[$status];
            }
        }
        return $sum;
    }

}
?>
