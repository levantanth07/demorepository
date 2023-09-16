<?php
require_once('packages/core/includes/utils/currency.php');
require_once('packages/vissale/modules/CrmCustomer/db.php');

class EditCrmCustomerCallHistoryForm extends Form {

	protected $today;
    protected $team_ids;
    protected $cid;
    protected $customer_kinds;
    protected $map;
    protected $customer_data;

    function __construct(){
		Form::Form('EditCrmCustomerCallHistoryForm');

//        $this->add('category_id',new IntType(true,'Bạn vui lòng chọn loại Thu / Chi'));
//        $this->add('team_id',new IntType(true,'Bạn vui lòng chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui lòng nhập tên người nhận', 5, 50));
//        $this->add('amount', new FloatType(true,'Bạn vui lòng nhập số tiền'));
//        $this->add('bill_date', new DateType(true,'Bạn vui lòng nhập ngày Thu / Chi'));
        $this->add('status',new IntType(true,'Bạn vui lòng chọn trạng thái cuộc gọi', 1, 100));
        $this->add('content', new TextType(true,'Bạn vui lòng nhập nội dung', 1, 10000));

//        if (Url::get('type')=='receive') {
//            $this->add('payment_full_name', new TextType(true,'Bạn vui lòng xem lại tên người nộp', 3, 50));
//        }
//        if (Url::get('type')=='pay') {
//            $this->add('received_full_name', new TextType(true,'Bạn vui lòng xem lại tên người nhận', 3, 50));
//        }

		//
        $this->init();

    }

    protected function init() {
        $this->map = array();
        $this->today = date("Y-m-d H:i:s");
        $this->cid = URL::get('cid');
        $this->customer_kinds = [
            0 => 'Bình thường',
            1 => 'Vui vẻ',
            -1 => 'Bực tức'
        ];
        $this->customer_data = CrmCustomerCallHistoryDB::get_customer( $this->cid );

        $this->map['customer_kinds_list'] = $this->customer_kinds;
        $this->map['cid'] = $this->cid;
        $this->map['status_list'] = CrmCustomerCallHistoryDB::$status;
        $_REQUEST['customer_name'] = $this->customer_data['name'];
        $_REQUEST['customer_id'] = $this->customer_data['id'];
        $_REQUEST['created_time'] = date('d/m/Y H:i:s');
        //
        require_once 'packages/vissale/modules/CrmCustomer/db.php';
        $this->map['customer_statuses'] = CrmCustomerDB::get_all_statuses();
        $this->map['customer_status_id_list'] = [''=>'Chọn Level'] + MiString::get_list( $this->map['customer_statuses'], 'name' );
        $_REQUEST['customer_status_id'] = !empty($this->customer_data['status_id']) ? $this->customer_data['status_id'] : '';
        $_REQUEST['old_customer_status_id'] = $_REQUEST['customer_status_id'];
        $this->map['old_customer_status_id'] = $_REQUEST['old_customer_status_id'];
        //var_dump( $this->customer_data );
    }

	function on_submit() {
        $logType = null;
        //var_dump( $_POST );die;
		if ( $this->check() ) {

			$rows = $this->save_item();

			if(!$this->is_error()){

				if (Url::get('cmd')=='edit') {
                    $call_item_id = DB::escape(URL::post('note_id'));
                    $call_item = CrmCustomerCallHistoryDB::get_item(md5($call_item_id . CATBE));
                    if (empty($call_item)) {
                        Url::js_redirect(true,'Không thể cập nhật cuộc gọi', array('nid'));
                    }
                    unset($rows['customer_id']);
                    unset($rows['created_time']);
                    unset($rows['group_id']);
                    unset($rows['created_user_id']);
					DB::update_id('crm_customer_callhistory', $rows, $call_item_id);
                    //message for log
                    $oldData = $call_item;
                    $newData = $rows;
                    $oldData['kind'] = $this->customer_kinds[intval($call_item['kind'])];
                    $newData['kind'] = $this->customer_kinds[intval($newData['kind'])];
                    $oldData['status'] = CrmCustomerCallHistoryDB::$status[intval($oldData['status'])];
                    $oldData['customer_status_id'] = $this->map['customer_status_id_list'][$_POST['old_customer_status_id']];

                    $newData['status'] = CrmCustomerCallHistoryDB::$status[intval($newData['status'])];
                    $newData['customer_status_id'] = $this->map['customer_status_id_list'][$_POST['customer_status_id']];

                    $map_msg = ['status' => 'Trạng thái', 'content' => 'Nội dung', 'kind' => 'Cảm xúc', 'customer_status_id' => 'Level KH',];
                    $logMessage = System::generate_log_message($oldData, $newData, $map_msg);
                    if (strlen(trim($logMessage)) > 0) {
                        $message = "đã sửa cuộc gọi: <br>" . $logMessage;
                        System::log('EDIT', "callhistory_id_{$call_item_id}", $message, "callhistory_id_=$call_item_id");
                    }
                    if (!empty(Url::post('customer_status_id'))) {
                        CrmCustomerDB::update_customer_status($oldData, $newData, $_POST['customer_status_id']);
                    }
				} else {
                    //
                    $newData = $rows;
                    $oldData = $rows;
					$id = DB::insert('crm_customer_callhistory', $rows);
                    $call_item_id = $id;
                    System::log('ADD', "callhistory_id_{$id}",'thêm mới cuộc gọi', "callhistory_id_=$id");
                    $oldData['customer_status_id'] = $this->map['customer_status_id_list'][$_POST['old_customer_status_id']];
                    $newData['customer_status_id'] = $this->map['customer_status_id_list'][$_POST['customer_status_id']];
                    //var_dump( $oldData, $newData, $_POST['customer_status_id'] );die;
                    if ( !empty($_POST['customer_status_id']) ) {
                        CrmCustomerDB::update_customer_status($oldData, $newData, $_POST['customer_status_id']);
                    }
				}
                if($this->is_error()){
                   return;
                }
                //close window
                if (Url::get('window')==1) {
                    echo '<script>window.opener.location.reload(); window.close();</script>';
                    exit();
                }
                Url::js_redirect('customer','Bạn đã lưu thành công', array('cid'=>Url::iget('cid'), 'do'=>'view','branch_id','idCuocGoi'=>$call_item_id),'cuocgoi');
			}
		}
	}

	function draw() {
        $call_item = [];
        if (URL::get('cmd')=='edit') {
            $nid = URL::get('nid');
            $call_item = CrmCustomerCallHistoryDB::get_item($nid);
            if (empty($call_item)) {
                Url::js_redirect(true,'cuộc gọi không tồn tại.');
            }
        }
        //
        if ( URL::get('cmd')=='edit' && !empty($call_item)
            && (CrmCustomerCallHistoryDB::can_edit($call_item['id']) || Session::get('admin_group')) ) {
            foreach ($call_item as $key => $value) {

                if ($key == 'id') {
                    $_REQUEST['note_id'] = $value;
                    continue;
                }

                if ($key == 'kind') {
                    $_REQUEST['customer_kinds'] = $value;
                    continue;
                }

                $_REQUEST[$key] = $value;
            }
            $this->map['logs'] = System::get_logs(false, "callhistory_id_={$call_item['id']}");
        }
        if ( (URL::get('cmd')=='edit' && empty($call_item)) || (isset($call_item['id']) and !CrmCustomerCallHistoryDB::can_edit($call_item['id']))) {
            echo "Bạn không có quyền sửa cuộc gọi.";
        }
        //
        $layout = 'edit';
		$this->parse_layout($layout, $this->map);
	}

    function save_item() {
        //
        $rows = [];
        $rows['customer_id'] = DB::escape(URL::post('customer_id'));
        $rows['status'] = DB::escape(URL::post('status'));
        $rows['content'] = DB::escape(URL::post('content'));
        $rows['created_time'] = time();
        $rows['group_id'] = Session::get('group_id');
        $rows['created_user_id'] = get_user_id();
        $rows['kind'] = intval( URL::post('customer_kinds') );

        return $rows;
    }

}

