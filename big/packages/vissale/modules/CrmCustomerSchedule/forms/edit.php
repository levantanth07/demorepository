<?php
require_once('packages/core/includes/utils/currency.php');

class EditCrmCustomerScheduleForm extends Form
{

    protected $today;
    protected $team_ids;
    protected $cid;
    protected $customer_kinds;
    protected $map;
    protected $customer_data;
    protected $branches;
    protected $categories;
    protected $status;
    protected $schedule_type;
    protected $current_schedule;

    public function __construct()
    {
        Form::Form('EditCrmCustomerScheduleForm');
        $this->init();
        $this->add('note', new TextType(true, 'Bạn vui lòng nhập nội dung lịch hẹn', 1, 5000));
        $max_date = new DateTime();
        $max_date->modify('+365 day');
        $min = time();
        $max = $max_date->getTimestamp(); //unix in 365 days

        if(empty($this->current_schedule)) $this->add('appointed_time', new IntType(true, 'Lỗi nhập thời gian hẹn (thời gian hẹn không được nhỏ hơn hiện tại).',$min, $max));

        if (Url::sget('cmd')=='edit' && !empty( URL::post('arrival_time') )) {
            $min = URL::post('appointed_time');
            $obj = new IntType(true, 'Lỗi nhập thời gian KH đến (thời gian KH đến không được nhỏ hơn thời gian hẹn).', $min, $max);
            $this->add('arrival_time', $obj);
        }
        if ( Url::post('action')=='quick_schedule' ) {
            $this->initQuickAddSchedule();
        }
    }

    function on_submit()
    {
        $logType = null;
        if ($this->check()) {

            $rows = $this->save_item();

            if (!$this->is_error()) {

                if (Url::get('cmd') == 'edit') {
                    $schedule_id = URL::post('schedule_id');
                    $schedule = CrmCustomerScheduleDB::get_item(md5($schedule_id . CATBE));
                    unset(
                        $rows['customer_id'],
                        $rows['created_time'],
                        $rows['group_id'], //ko dc sửa chi nhánh
                        $rows['branch_id'], //ko dc sửa người tạo
                        $rows['appointed_time'] // ko được đổi, nếu khách đổi lịch sẽ phải tạo hẹn mới.
                    );
                    if (!empty($schedule) || check_user_privilege('CUSTOMER') || Session::get('admin_group') || is_group_owner() || $schedule['created_user_id'] == get_user_id()) {
                        DB::update_id('crm_customer_schedule', $rows, $schedule_id);
                    } else {
                        Url::js_redirect(true, 'Không thể cập nhật lịch hẹn', array('nid'));
                    }
                    //message for log
                    $oldData = $schedule;
                    $newData = $rows;
                    $oldData['status_id'] = $this->status[intval($oldData['status_id'])];
                    $oldData['customer_status_id'] = Url::get('old_customer_status_id')?$this->map['customer_status_id_list'][Url::get('old_customer_status_id')]:0;
                    //$oldData['kind'] = $this->customer_kinds[intval($oldData['kind'])];
                    $oldData['schedule_type'] = $this->schedule_type[intval($oldData['schedule_type'])];

                    $newData['status_id'] = $this->status[intval($newData['status_id'])];
                    //$newData['kind'] = $this->customer_kinds[intval($newData['kind'])];
                    $newData['schedule_type'] = $this->schedule_type[intval($newData['schedule_type'])];

                    $newData['arrival_time_display'] = $_POST['arrival_time_display'];
                    $newData['customer_status_id'] = $this->map['customer_status_id_list'][Url::post('customer_status_id')];

                    $map_msg = [
                        'status_id'=>'Trạng thái',
                        'kind'=>'Cảm xúc',
                        'staff_name'=>'Tên NV',
                        'arrival_time_display'=>'Thời gian đến',
                        'schedule_type'=>'Loại lịch hẹn',
                        'note'=>'Nội dung',
                        'customer_status_id'=>'Level KH',
                        'sale_staff_name'=>'Nhân viên sale',
                        'note_services'=>'Dịch vụ quan tâm',
                    ];
                    $logMessage =  System::generate_log_message($oldData, $newData, $map_msg);
                    if(strlen(trim($logMessage)) > 0) {
                        $message = "đã sửa lịch hẹn: <br>".$logMessage;
                        System::log('EDIT', "customer_schedule_id_{$schedule_id}", $message, "customer_schedule_id_=$schedule_id");
                    }
                } else {
                    $cid = Url::get('cid');
                    $group_id = Session::get('group_id');
                    $lastOrderId = CrmCustomerScheduleDB::get_customer_last_order_id($cid,$group_id);
                    $rows['order_id'] = $lastOrderId['last_order_id'];
                    $id = DB::insert('crm_customer_schedule', $rows);
                    System::log('ADD', "customer_schedule_id_{$id}",'thêm mới lịch hẹn');
                    $schedule_id = $id;
                }

                if ($this->is_error()) {
                    return;
                }
                if (!empty(Url::post('customer_status_id'))) {
                    //CrmCustomerDB::update_customer_status($oldData, $newData, $_POST['customer_status_id']);
                }
                if ($cid=Url::get('cid')) {
                    Url::js_redirect('customer', 'Bạn đã lưu thành công', array('cid'=>$cid, 'do'=>'view','branch_id', 'idLichHen' =>$schedule_id),'lichhen');
                }else{
                    Url::js_redirect('lich-hen&cmd=today_schedule', 'Bạn đã lưu thành công', array('cmd'=>'today_schedule'));
                }
            }
        }
    }

    function draw()
    {
        //
        if (URL::get('cmd') == 'edit' && !empty($this->current_schedule)
            && (CrmCustomerScheduleDB::can_edit($this->current_schedule['id']) || Session::get('admin_group'))) {
            foreach ($this->current_schedule as $key => $value) {
                if ($key == 'id') {
                    $_REQUEST['schedule_id'] = $value;
                    continue;
                }
                $_REQUEST[$key] = $value;
            }
            $this->map['logs'] = System::get_logs(false, "customer_schedule_id_={$this->current_schedule['id']}");
        }
        //
        if (URL::get('cmd') == 'edit' && empty($this->current_schedule)) {
            echo "Bạn không có quyền sửa lịch hẹn.";
        }
        //
        $layout = 'edit';
        $this->parse_layout($layout, $this->map);
    }

    protected function init()
    {
        $this->map = array();
        //
        if (URL::get('cmd') == 'edit') {
            $sid = URL::get('sid');
            $this->current_schedule = CrmCustomerScheduleDB::get_item($sid);
            if ( empty( $this->current_schedule ) || !CrmCustomerScheduleDB::can_edit($this->current_schedule['id']) ) {
                Url::js_redirect(true, 'lịch hẹn không tồn tại.');
            }
        }

        $this->today = date('Y-m-d H:i:s');
        if ( !empty(URL::iget('cid') )) {
            $this->cid = URL::iget('cid');
        }
        if ( !empty($this->current_schedule)&&empty(URL::iget('cid')) ) {
            $this->cid = $this->current_schedule['customer_id'];
        }
        //
        $this->branches = CrmCustomerScheduleDB::getGroupsByMasterGroup();
        $this->status = CrmCustomerScheduleDB::$status;
        $this->schedule_type = CrmCustomerScheduleDB::$schedule_type;
        $this->customer_data = CrmCustomerScheduleDB::get_customer($this->cid);
        $this->map['customer_kinds_list'] = $this->customer_kinds;
        $this->map['cid'] = $this->cid;
        if(!empty($this->customer_data)){
            $_REQUEST['customer_name'] = $this->customer_data['name'];
            $_REQUEST['customer_id'] = $this->customer_data['id'];
            $_REQUEST['customer_mobile'] = $this->customer_data['mobile'];
            $_REQUEST['created_time'] = date('d/m/Y H:i:s');
            $_REQUEST['customer_status_id'] = !empty($this->customer_data['status_id']) ? $this->customer_data['status_id'] : '';
            $_REQUEST['old_customer_status_id'] = Url::get('customer_status_id');
            $this->map['old_customer_status_id'] = Url::get('old_customer_status_id');
        }
        $this->map['branch_id_list'] = $this->generate_branches();
        $this->map['status_id_list'] = $this->status;
        $this->map['schedule_type_list'] = $this->schedule_type;
        $this->map['customer_statuses'] = CrmCustomerDB::get_all_statuses();
        $this->map['customer_status_id_list'] = [''=>'Chọn phân loại'] + MiString::get_list( $this->map['customer_statuses'], 'name' );
    }

    protected function save_item()
    {
        //
        $rows = [];
        $rows['customer_id'] = DB::escape(URL::post('customer_id'));
        $rows['note'] = DB::escape(URL::post('note'));
        $rows['created_time'] = time();
        $rows['group_id'] = Session::get('group_id');
        $rows['created_user_id'] = get_user_id();
        $rows['branch_id'] = DB::escape(URL::post('branch_id'));
        $rows['status_id'] = DB::escape(URL::post('status_id'));
        $rows['schedule_type'] = DB::escape(URL::post('schedule_type'));
        $rows['appointed_time'] = DB::escape(URL::post('appointed_time'));
//        $rows['alert_before'] = 30;
        $rows['process_id'] = URL::post('process_id') ? DB::escape(URL::post('process_id')) : 0;
        $rows['staff_id'] = URL::post('staff_id') ? DB::escape(URL::post('staff_id')) : 0;
        $rows['sale_staff_id'] = URL::post('sale_staff_id') ? DB::escape(URL::post('sale_staff_id')) : 0;
        $rows['staff_name'] = URL::post('staff_name') ? DB::escape(URL::post('staff_name')) : '';
        $rows['sale_staff_name'] = URL::post('sale_staff_name') ? DB::escape(URL::post('sale_staff_name')) : '';
        $rows['note_services'] = DB::escape(URL::post('note_services'));
        if ( URL::get('cmd')==='edit' && !empty($this->current_schedule)) {
            $rows['arrival_time'] = URL::post('arrival_time') ? DB::escape(URL::post('arrival_time')) : 0;
        }
        return $rows;
    }

    protected function generate_branches()
    {
        $branches = [];
        foreach ($this->branches as $branch) {
            $branches[$branch['id']] = $branch['name'];
        }
        return $branches;
    }


    protected function initQuickAddSchedule()
    {
        $rows = $this->save_item();
        $id = DB::insert('crm_customer_schedule', $rows);
        System::log('ADD', "customer_schedule_id_{$id}",'thêm mới lịch hẹn');
        //var_dump( $rows );
        Url::js_redirect(true, 'lịch hẹn tạo thành công !', ['cmd'=>'today_schedule']);
        die();
    }
}

