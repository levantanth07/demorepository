<?php

class EditCustomerDebitReportForm extends Form {

	protected $today;
    protected $team_ids;

    function __construct(){
		Form::Form('EditCustomerDebitReportForm');

        $this->add('category_id',new IntType(true,'Bạn vui long chọn loại Thu / Chi'));
        $this->add('team_id',new IntType(true,'Bạn vui long chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui long nhập tên người nhận', 5, 50));
        $this->add('amount', new FloatType(true,'Bạn vui long nhập số tiền'));
        $this->add('bill_date', new DateType(true,'Bạn vui long nhập ngày Thu / Chi'));
        $this->add('note', new TextType(true,'Bạn vui long nhập nội dung Thu/Chi', 2, 255));

        if (Url::get('type')=='receive') {
            $this->add('payment_full_name', new TextType(true,'Bạn vui long nhập tên người nộp', 5, 50));
        }
        if (Url::get('type')=='pay') {
            $this->add('received_full_name', new TextType(true,'Bạn vui long nhập tên người nhận', 5, 50));
        }

		//
        $this->init();

}
    protected function init(){
        $this->today = date("Y-m-d H:i:s");
        $this->team_ids  = CustomerDebitReportDB::$teams;
    }

	function save_item() {
        if ( Url::get('type')=='receive' ) {
            return $this->generateReceivingData();
        }
        //for pay
        return $this->generatePayingData();
	}

	// Khởi tạo dữ liệu để ghi phiếu thu
    function generateReceivingData()
    {
        $type = 1;
        $group_id = Session::get('group_id');
        $cond = "group_id = {$group_id} AND bill_type={$type} ";
        $rows = $_POST;
        unset($rows['bill_id']);
        unset($rows['form_block_id']);
        $rows['bill_number'] = CustomerDebitReportDB::get_max_bill_number($cond) + 1;
        $rows['created_time'] = time();
        $rows['created_account_id'] = Session::get('user_id');
        $rows['created_full_name'] = Session::get('user_data')['full_name'];
        $rows['payment_type'] = 1;
        $rows['group_id'] = Session::get('group_id');
        $rows['bill_date'] = Date_Time::to_sql_date($rows['bill_date']);
        $rows['amount'] = System::calculate_number($rows['amount']);

//		System::debug($rows);die;

        return $rows;
    }

    // Khởi tạo dữ liệu để ghi phiếu chi
    function generatePayingData()
    {
        $type = 0;
        $group_id = Session::get('group_id');
        $cond = "group_id = {$group_id} AND bill_type={$type} ";
        $rows = $_POST;
        unset($rows['bill_id']);
        unset($rows['form_block_id']);
        $rows['bill_number'] = CustomerDebitReportDB::get_max_bill_number($cond) + 1;
        $rows['created_time'] = time();
        $rows['created_account_id'] = Session::get('user_id');
        $rows['created_full_name'] = Session::get('user_data')['full_name'];
        $rows['payment_full_name'] = Session::get('user_data')['full_name'];
        $rows['payment_account_id'] = Session::get('user_id');
        $rows['payment_type'] = 1;
        $rows['group_id'] = Session::get('group_id');
        $rows['bill_date'] = Date_Time::to_sql_date($rows['bill_date']);
        $rows['amount'] = System::calculate_number($rows['amount']);

//		System::debug($rows);die;

        return $rows;
    }

	function on_submit(){
        $logType = null;

		if ( $this->check() ){

			$rows = $this->save_item();

			if(!$this->is_error()){

				if(Url::get('cmd')=='edit' and $item = DB::exists_id('cash_flow', Url::get('id'))){

					$id = intval(Url::get('id'));
                    CustomerDebitReportDB::update_edited_log($id, $rows, 'update');
					DB::update_id('cash_flow', $rows, $id);

				} else {
					$id = DB::insert('cash_flow', $rows);
                    CustomerDebitReportDB::update_edited_log($id, $rows, 'create');
				}

                $this->update_avatar($this, $id);

                if($this->is_error()){
                   return;
                }

				Url::js_redirect(true);
			}
		}

	}

    function update_avatar(&$form, $cashflow_id){

        require_once 'packages/core/includes/utils/upload_file.php';
        $dir = 'default/groups/'.Session::get('group_id').'/cashflow/';
        if (isset($_FILES['attachment_file']) and $_FILES['attachment_file']['size'] > 2*1024*1024) {
            $form->error('attachment_file','Ảnh không được lớn hơn 2MB', false);
        } else {

            list($width, $height, $type, $attr) = getimagesize($_FILES['attachment_file']['tmp_name']);
            $new_width = false;
            $new_height = false;
            if ($width > 1024) {
                $new_width = 1024;
                $new_height = 1024;
            }

            update_upload_file('attachment_file', $dir, 'IMAGE',false, $new_width, $new_height,true);

            if( URL::get('attachment_file') ) {
                //
                $rows = [];
                $rows['attachment_file'] = URL::get('attachment_file');
                CustomerDebitReportDB::update_edited_log($cashflow_id, $rows, 'update');
                //
                DB::update('cash_flow', array('attachment_file'=> $_REQUEST['attachment_file']),'id='.$cashflow_id);
            }
        }
    }

    function generatePrintData()
    {
        $print = DB::fetch('select print_name,print_phone,print_address,template as print_template from order_print_template where group_id='.Session::get('group_id').' order by set_default DESC,order_print_template.id DESC LIMIT 0,1');
        $party = DB::fetch('select name as print_name from `groups` where id='.Session::get('group_id'));
        $printData = array_merge($print, $party);
        foreach ($printData as $key => $value) {
            $this->map[$key] = $value;
        }
    }


	function draw() {

//        var_dump( EditCustomerDebitReportForm::getPrintData() );die;

		$this->map = array();
        $this->map['category_id_list'] = CustomerDebitReportDB::generateCategories();
        $this->map['team_id_list'] = $this->team_ids;

        if (URL::get('cmd')=='edit') {
        } else {
            if (!URL::get('bill_date')) {
                $_REQUEST['bill_date'] = date('d/m/Y');
            }
        }
        //
        if ((URL::get('cmd')=='edit' or URL::get('cmd')=='print') and Url::iget('id') and $row=CustomerDebitReportDB::get_item()) {
            $row['bill_date'] = Date_Time::to_common_date($row['bill_date']);
            $row['amount'] = System::display_number($row['amount']);
            foreach ($row as $key => $val){
                if(!Url::get($key)){
                    $_REQUEST[$key] = $val;
                }
                $this->map['attachment_file'] = Url::get('attachment_file') ? Url::get('attachment_file') : 'assets/standard/images/pattern2.png';
            }

            $logs = CustomerDebitReportDB::get_logs( URL::get('id') );
            $this->map['logs'] = $logs;
            $this->generatePrintData();
        }
        //
		if (Url::get('type')=='receive') {
            $layout = 'receive_form';
            if (!URL::get('received_full_name')) {
                $_REQUEST['received_full_name'] = Session::get('user_data')['full_name'];
            }
            if(Url::get('cmd')=='print') {
                $layout = 'receive_print';
            }
        } else {
		    $layout = 'pay_form';
            if(Url::get('cmd')=='print') {
                $layout = 'pay_print';
            }
        }

        if (URL::get('cmd')=='edit' && !$row) {
            $layout = 'not_found';
        }

		$this->parse_layout($layout, $this->map);
	}
}

