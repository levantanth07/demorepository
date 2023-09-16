<?php
require_once('packages/core/includes/utils/currency.php');

class EditCrmCustomerNoteForm extends Form {

	protected $today;
    protected $team_ids;
    protected $cid;
    protected $customer_kinds;
    protected $map;
    protected $customer_data;

    function __construct(){
		Form::Form('EditCrmCustomerNoteForm');

//        $this->add('category_id',new IntType(true,'Bạn vui lòng chọn loại Thu / Chi'));
//        $this->add('team_id',new IntType(true,'Bạn vui lòng chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui lòng nhập tên người nhận', 5, 50));
//        $this->add('amount', new FloatType(true,'Bạn vui lòng nhập số tiền'));
//        $this->add('bill_date', new DateType(true,'Bạn vui lòng nhập ngày Thu / Chi'));

        $this->add('content', new TextType(true,'Bạn vui lòng nhập nội dung', 1, 5000));

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
        $this->today = date("Y-m-d H:i:s");
        $this->team_ids  = CrmCustomerNoteDB::$teams;
        $this->cid = URL::get('cid');

        $this->customer_kinds = [
            '0' => 'Bình thường',
            '1' => 'Vui vẻ',
            '-1' => 'Bực tức'
        ];
        $this->customer_data = CrmCustomerNoteDB::get_customer( $this->cid );
        // var_dump( $this->customer_data );
    }

	function on_submit() {
        $logType = null;
		if ( $this->check() ) {
			$rows = $this->save_item();
			if(!$this->is_error()){
				if (Url::get('cmd')=='edit') {
                    $note_id = URL::post('note_id');
                    $note = CrmCustomerNoteDB::get_item(md5($note_id . CATBE));
                    if (empty($note)) {
                        Url::js_redirect(true,'Không thể cập nhật ghi chú', array('nid'));
                    }
                    unset($rows['customer_id']);
                    unset($rows['created_time']);
                    unset($rows['group_id']);
                    unset($rows['created_user_id']);

                    DB::update_id('crm_customer_notes', $rows, $note_id);
                    //message for log
                    $oldData = $note;
                    $newData = $rows;
                    $oldData['kind'] = $this->customer_kinds[intval($note['kind'])];
                    $newData['kind'] = $this->customer_kinds[intval($newData['kind'])];
                    $map_msg = ['kind'=>'Trạng thái', 'content'=>'Nội dung'];
                    $logMessage = System::generate_log_message($oldData, $newData, $map_msg);
                    if(strlen(trim($logMessage)) > 0){
                        $message = "đã sửa note: <br>" . $logMessage;
                        System::log('EDIT', "customer_note_id_{$note_id}",$message, "customer_note_id_=$note_id");
                    }
				} else {
                    //
					$id = DB::insert('crm_customer_notes', $rows);
                    $note_id = $id;
					System::log('ADD', "customer_note_id_{$id}",'thêm mới note');
				}

                if($this->is_error()){
                   return;
                }
                Url::js_redirect('customer','Bạn đã lưu thành công', array('cid'=>Url::iget('cid'),'branch_id','do'=>'view','idGhiChu'=>$note_id),'ghichu');
			}
		}
	}

	function draw() {
		$this->map = array();
        $this->map['customer_kinds_list'] = $this->customer_kinds;
        $this->map['cid'] = $this->cid;
        $_REQUEST['customer_name'] = $this->customer_data['name'];
        $_REQUEST['customer_id'] = $this->customer_data['id'];
        $_REQUEST['created_time'] = date('d/m/Y H:i:s');

        if (URL::get('cmd')=='edit') {
            $nid = URL::get('nid');
            $note = CrmCustomerNoteDB::get_item($nid);
            if (empty($note)) {
                Url::js_redirect(true,'Ghi chú không tồn tại.');
            }
        }
        //
        if (URL::get('cmd')=='edit' && !empty($note)
            && (CrmCustomerNoteDB::can_edit($note['id']) || Session::get('admin_group'))) {
            foreach ($note as $key => $value) {

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

            $this->map['logs'] = System::get_logs(false, "customer_note_id_={$note['id']}");
        }
        if (URL::get('cmd')=='edit' && empty($note) ) {
            echo "Bạn không có quyền sửa ghi chú.";
        }
        //
        $layout = 'edit';
		$this->parse_layout($layout, $this->map);
	}


    function save_item() {
        //
        $rows = [];
        $rows['customer_id'] = DB::escape(URL::post('customer_id'));
        $rows['content'] = DB::escape(URL::post('content'));
        $rows['created_time'] = time();
        $rows['group_id'] = Session::get('group_id');
        $rows['created_user_id'] = get_user_id();
        $rows['kind'] = intval( URL::post('customer_kinds') );

        return $rows;
    }

}

