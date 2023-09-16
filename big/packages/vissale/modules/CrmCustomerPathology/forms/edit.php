<?php
require_once('packages/core/includes/utils/currency.php');

class EditCrmCustomerPathologyForm extends Form {

	protected $today;
    protected $team_ids;
    protected $cid;
    protected $customer_kinds;
    protected $map;
    protected $customer_data;

    function __construct(){
		Form::Form('EditCrmCustomerPathologyForm');

//        $this->add('category_id',new IntType(true,'Bạn vui lòng chọn loại Thu / Chi'));
//        $this->add('team_id',new IntType(true,'Bạn vui lòng chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui lòng nhập tên người nhận', 5, 50));
//        $this->add('amount', new FloatType(true,'Bạn vui lòng nhập số tiền'));
//        $this->add('bill_date', new DateType(true,'Bạn vui lòng nhập ngày Thu / Chi'));

        $this->add('name', new TextType(true,'Bạn vui lòng nhập tên bệnh', 1, 1000));
        $this->add('note', new TextType(true,'Bạn vui lòng nhập Tình trạng bệnh', 1, 5000));

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
        $this->cid = URL::get('cid');

        $this->customer_kinds = [
            0 => 'Bình thường',
            1 => 'Vui vẻ',
            -1 => 'Bực tức'
        ];

        $this->customer_data = CrmCustomerPathologyDB::get_customer( $this->cid );
//        var_dump( $this->customer_data );
    }

	function on_submit() {
        $logType = null;

		if ( $this->check() ) {

			$rows = $this->save_item();

			if(!$this->is_error()){

				if (Url::get('cmd')=='edit') {
                    $note_id = URL::post('note_id');
                    $note = CrmCustomerPathologyDB::get_item(md5($note_id . CATBE));
                    if (empty($note)) {
                        Url::js_redirect(true,'Không thể cập nhật bệnh lý', array('nid'));
                    }
                    unset($rows['customer_id']);
                    unset($rows['created_time']);
                    unset($rows['group_id']);
                    unset($rows['created_user_id']);
					DB::update_id('crm_customer_pathology', $rows, $note_id);
                    //message for log
                    $oldData = $note;
                    $newData = $rows;
                    $map_msg = ['name'=>'Tên bệnh', 'note'=>'Tình trạng'];
                    $logMessage = System::generate_log_message($oldData, $newData, $map_msg);
                    if(strlen(trim($logMessage)) > 0){
                        $message = "đã sửa bệnh: <br>" . $logMessage;
                        System::log('EDIT', "pathology_id_{$note_id}",$message, "pathology_id_=$note_id");
                    }
				} else {
                    //
					$id = DB::insert('crm_customer_pathology', $rows);
                    $note_id = $id;
                    System::log('ADD', "pathology_id_{$id}",'thêm mới bệnh lý');
				}

                if($this->is_error()){
                   return;
                }
                Url::js_redirect('customer','Bạn đã lưu thành công', array('cid'=>Url::get('cid'),'do'=>'view','branch_id','idBenhLy'=>$note_id),'benhly');
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
            $nid = URL::iget('nid');
            $note = CrmCustomerPathologyDB::get_item(md5($nid . CATBE));
            if (empty($note)) {
                Url::js_redirect(true,'bệnh lý không tồn tại.');
            }
        }

        //
        if (URL::get('cmd')=='edit' && !empty($note)
            && (CrmCustomerPathologyDB::can_edit($note['created_user_id']) || Session::get('admin_group'))) {
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
            $this->map['logs'] = System::get_logs(false, "pathology_id_={$note['id']}");
        }
        if (URL::get('cmd')=='edit' && empty($note) ) {
            echo "Bạn không có quyền sửa bệnh lý.";
        }
        //
        $layout = 'edit';
		$this->parse_layout($layout, $this->map);
	}


    function save_item() {
        //
        $rows = [];
        $rows['customer_id'] = DB::escape(URL::post('customer_id'));
        $rows['note'] =  DB::escape(URL::post('note'));
        $rows['name'] =  DB::escape(URL::post('name'));
        $rows['created_time'] = time();
        $rows['group_id'] = Session::get('group_id');
        $rows['created_user_id'] = get_user_id();
        return $rows;
    }

}

