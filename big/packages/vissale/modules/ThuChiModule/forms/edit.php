<?php
class EditThuChiModuleForm extends Form {
    const MAX_COUNT_DATE_EDIT = 60;
	protected $today;
    protected $team_ids;
    protected $map;
    function __construct(){
        $this->link_js('packages/core/includes/js/multi_items.js');
        Form::Form('EditThuChiModuleForm');

        //$this->add('category_id',new IntType(true,'Bạn vui lòng chọn loại Thu / Chi'));
        $this->add('team_id',new IntType(true,'Bạn vui lòng chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui lòng nhập tên người nhận', 5, 50));
        $this->add('amount', new FloatType(true,'Bạn vui lòng nhập số tiền'));
        $this->add('bill_date', new DateType(true,'Bạn vui lòng nhập ngày Thu / Chi'));
        $this->add('note', new TextType(true,'Bạn vui lòng nhập nội dung Thu/Chi', 2, 2000));

        if (Url::get('type')=='receive') {
            $this->add('payment_full_name', new TextType(true,'Bạn vui lòng xem lại tên người nộp', 3, 50));
        }
        if (Url::get('type')=='pay') {
            $this->add('received_full_name', new TextType(true,'Bạn vui lòng xem lại tên người nhận', 3, 50));
        }
        $this->add('cash_flow_detail.amount', new FloatType(true,'Bạn vui lòng nhập lại phần thanh toán'));

		//
        $this->init();

}
    protected function init(){
        $this->today = date("Y-m-d H:i:s");
        $this->team_ids  = ThuChiModuleDB::$teams;
    }
	function on_submit(){
        $logType = null;
		if ( $this->check() ){
            $rows = $this->save_item();
            unset($rows['mi_payment']);
            if(!$rows['turn_card_id']){
                $rows['turn_card_id'] = 0;
            }
			if(!$this->is_error()){
				if(Url::get('cmd')=='edit' and $item = DB::fetch('select id,group_id from cash_flow where md5(CONCAT(id,"'.CATBE.'")) = "'.Url::sget('id').'"')){
					$id = $item['id'];
                    $rows['id'] = $item['id'];
					unset($rows['bill_number']);

                    ThuChiModuleDB::update_edited_log($id, $rows, 'update');
                    DB::update_id('cash_flow', $rows, $id);
                    //update group_id
                    $rows['group_id'] = $item['group_id'];
				} else {
				    //echo 1;die;
                    //
                    $type = ( Url::get('type')=='receive' )?'1':'0';
                    $group_id = ThuChiModule::$group_id;
                    $cond = "group_id = {$group_id} AND bill_type={$type} ";
                    $rows['bill_number'] = ThuChiModuleDB::get_max_bill_number($cond) + 1;
                    $rows['created_time'] = time();
                    $rows['created_account_id'] = Session::get('user_id');
                    $rows['created_full_name'] = Session::get('user_data')['full_name'];
                    $rows['payment_type'] = 1;
                    $rows['payment_time'] = time();
                    $rows['group_id'] = ThuChiModule::$group_id;
                    //
                    $rows['id'] = DB::insert('cash_flow', $rows);
                    ThuChiModuleDB::update_edited_log($rows['id'], $rows, 'create');
				}

                ThuChiModuleDB::update_payment($rows);
                //$this->update_avatar($this, $rows['id']);
                if($this->is_error()){
                   return;
                }
                die;
                if(Url::get('print')==1){
                    Url::js_redirect(true,'Bạn đã lưu và in ngay phiếu',array('cid','id'=>User::encode_password($rows['id']),'type','cmd'=>'edit','print_now'=>1));
                }else{
                    Url::js_redirect(true,'Bạn đã lưu thành công',array('cid','type'));
                }
			}
		}

	}
	function draw() {
        $this->map['category_id_list'] = ThuChiModuleDB::generateCategories();
        $type = 0;
        if (Url::get('type')=='receive') {
            $type = 1;
        }
        $categories = ThuChiModuleDB::getCategories($type);
        $this->map['category_id_options'] = '';
        $cid = Url::get('cid');
        foreach($categories as $key=>$value){
            $selected = '';
            if ($type==0 && $cid && $key==55) {
                $selected = 'selected';
            }
            $this->map['category_id_options'] .= '<option '.$selected.' value="'.$key.'">'.$value['name'].'</option>';
        }

        $this->map['team_id_list'] = $this->team_ids;

        if (Url::get('cmd')==='add' && Url::get('type')==='receive') {
            $_REQUEST['team_id']=1;
        }
        if (Url::get('cmd')==='add' && Url::get('type')==='pay') {
            $_REQUEST['team_id']=3;
        }

        if (URL::get('cmd')==='edit') {

        } else {
            if (!URL::get('bill_date')) {
                $_REQUEST['bill_date'] = date('d/m/Y');
            }
        }
        //
        $this->map['print_name'] = '';
        $this->map['print_phone'] = '';
        $this->map['print_address'] = '';
        $this->map['turn_card_button'] = '';
        if ((URL::get('cmd')=='edit' or URL::get('cmd')=='print') && Url::get('id') && $row=ThuChiModuleDB::get_item()) {
            if($cid = Url::get('cid')){
                $card = DB::select('spa_turn_card','md5(concat(id,"'.CATBE.'"))="'.$cid.'"');
                $this->map['turn_card_button'] = '<a href="?page=turn_card" class="btn btn-default"><i class="fa fa-credit-card"></i> Thẻ lần</a>';
            }
            $row['bill_date'] = Date_Time::to_common_date($row['bill_date']);
            $number = $row['amount'];
            $row['amount'] = System::display_number($number);
            $row['bill_number'] = ThuChiModule::generateCode($row['bill_number'], $row['bill_type']);
            $row['amount_words'] = currency_to_text($number) .' đồng.';
            foreach ($row as $key => $val){
                if(!Url::get($key)){
                    $_REQUEST[$key] = $val;
                }
                $this->map['attachment_file'] = Url::get('attachment_file') ? Url::get('attachment_file') : 'assets/standard/images/pattern2.png';
            }

            $logs = ThuChiModuleDB::get_logs( URL::get('id') );
            //var_dump( $logs );
            $this->map['logs'] = $logs;
            if(!isset($_REQUEST['mi_payment'])){
                $_REQUEST['mi_payment'] = ThuChiModuleDB::get_payment($row['id']);
            }
            $this->generatePrintData();

        } else {
            if($cid = Url::get('cid')){
                $card = DB::select('spa_turn_card','md5(concat(id,"'.CATBE.'"))="'.$cid.'"');
                $_REQUEST['turn_card_id'] = $card['id'];
                $code = get_prefix();
                $code .= str_pad(($card['code']),6,"0",STR_PAD_LEFT);
                //$_REQUEST['turn_card_code'] = $code;
                //$_REQUEST['sold_date'] = $card['sold_date'];
                //$_REQUEST['expired_date'] = $card['expired_date'];
                $customer = DB::select('crm_customer','id='.$card['customer_id']);
                $_REQUEST['payment_full_name'] = $customer['name'];

                if (Url::get('type')=='receive') {
                    $_REQUEST['received_full_name'] = $_SESSION['user_data']['full_name'];
                } else {
                    $_REQUEST['received_full_name'] = $customer['name'];
                }
                
                $_REQUEST['customer_id'] = $customer['id'];
                $_REQUEST['mobile'] = $customer['mobile'];
                $_REQUEST['address'] = $customer['address'];
                require_once('packages/vissale/modules/AdminSpaTurnCard/db.php');
                $total_payment = AdminSpaTurnCardDB::get_total_card_payment($card['id']);
                $total_price = $card['total_price'];
                $this->map['total_price'] = System::display_number($total_price);
                $this->map['total_payment'] = $_REQUEST['total_payment'] = System::display_number($total_payment);
                $_REQUEST['amount'] = System::display_number($total_price - $total_payment);

                if(!Url::get('mi_payment')){
                    if(Url::get('type')=='pay'){
                        $desc = 'TRẢ GÓI thẻ lần mã: '.$code;
                        $_REQUEST['mi_payment']['']['id'] = '(auto)';
                        $_REQUEST['mi_payment']['']['pay_for'] = '';
                        $_REQUEST['mi_payment']['']['amount'] = '';
                        $_REQUEST['mi_payment']['']['description'] = $desc;
                        $_REQUEST['mi_payment']['']['order_id'] = $card['id'];
                        $_REQUEST['mi_payment']['']['order_type'] = 2;
                        $_REQUEST['mi_payment']['']['amount'] = $this->map['total_price'];
                        $_REQUEST['note'] = $desc;
                    }else{
                        $desc = 'Thanh toán mua thẻ: '.$code;
                        $_REQUEST['mi_payment']['']['id'] = '(auto)';
                        $_REQUEST['mi_payment']['']['pay_for'] = '';
                        $_REQUEST['mi_payment']['']['amount'] = '';
                        $_REQUEST['mi_payment']['']['description'] = $desc;
                        $_REQUEST['mi_payment']['']['order_id'] = $card['id'];
                        $_REQUEST['mi_payment']['']['order_type'] = 2;
                        $_REQUEST['mi_payment']['']['amount'] = System::display_number($total_price - $total_payment);
                        $_REQUEST['note'] = $desc;
                    }
                }
            }

            if ( Url::get('type')==='pay' ) {
                if ( !Url::get('received_full_name') ) {
                    $_REQUEST['received_full_name'] = $_SESSION['user_data']['full_name'];
                }
                if ( Url::get('cid') ) {
                    $_REQUEST['team_id'] = 1;
                } else {
                    $_REQUEST['team_id'] = 3;
                }
            }

            if(!Url::get('mi_payment') or sizeof($_REQUEST['mi_payment'])<1){
                for($i=1;$i<=5;$i++){
                    $_REQUEST['mi_payment']['']['id'] = '(auto)';
                    $_REQUEST['mi_payment']['']['pay_for'] = '';
                    $_REQUEST['mi_payment']['']['amount'] = '';
                }
            }
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
            /*if(Url::get('cmd')=='print') {
                $layout = 'pay_print';
            }*/
        }

        if (URL::get('cmd')=='edit' && !$row) {
            $layout = 'not_found';
        }
        $this->map['payment_type_list'] = array('1'=>'Tiền mặt','2'=>'Chuyển khoản','3'=>'Thẻ');
        $this->map['payment_method_options'] = '<option value="1">Tiền mặt</option><option value="3">Thẻ</option><option value="2">Chuyển khoản</option>';
		$this->parse_layout($layout, $this->map);
	}
    function update_avatar(&$form, $cashflow_id){
        require_once 'packages/core/includes/utils/upload_file.php';
        $dir = 'default/groups/'.ThuChiModule::$group_id.'/cashflow/';
        if (isset($_FILES['attachment_file']) and $_FILES['attachment_file']['size'] > 2*1024*1024) {
            $form->error('attachment_file','Ảnh không được lớn hơn 2MB', false);
        } else {
            if(isset($_FILES['attachment_file']) and $_FILES['attachment_file']['size']>0){//edited by khoand at 22:44 21/10/2018
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
                    ThuChiModuleDB::update_edited_log($cashflow_id, $rows, 'update');
                    //
                    DB::update('cash_flow', array('attachment_file'=> $_REQUEST['attachment_file']),'id='.$cashflow_id);
                }
            }
        }
    }
    function generatePrintData()
    {
        $print = DB::fetch('select print_name,print_phone,print_address,template as print_template from order_print_template where group_id='.ThuChiModule::$group_id.' order by set_default DESC,order_print_template.id DESC LIMIT 0,1');
        $party = DB::fetch('select name as print_name from `groups` where id='.ThuChiModule::$group_id);
        $print['print_name'] = '';
        $print['print_phone'] = '';
        $print['print_address'] = '';
        if(empty($print)){
            $group = DB::select('groups','id='.ThuChiModule::$group_id);
            $print['print_name'] = $group['name'];
            $print['print_phone'] = $group['phone'];
            $print['print_address'] = $group['address'];
        }
        $printData = array_merge($print, $party);
        foreach ($printData as $key => $value) {
            $this->map[$key] = $value;
        }
    }
    function save_item() {

        //for pay
        $rows = $this->generatePayingData();
        //for receive
        if ( Url::get('type')=='receive' ) {
            $rows =  $this->generateReceivingData();
        }

        $today = new DateTime();
        $billDate = new DateTime( $rows['bill_date'] );
        $diff = $today->diff($billDate)->format("%a");

        if ( URL::get('cmd')!=='edit' ) {
            //validate max_date in previous time
            if ($diff > self::MAX_COUNT_DATE_EDIT) {
                $this->error('bill_date','Ngày tạo cho phép cách hiện tại '.self::MAX_COUNT_DATE_EDIT.' ngày');
            }
        } else {
            //validate max_date in previous time
            if ($diff > self::MAX_COUNT_DATE_EDIT) {
                unset($rows['bill_date']);
            }
        }

        return $rows;
    }

    // Khởi tạo dữ liệu để ghi phiếu thu
    public function generateReceivingData()
    {
        $type = 1;
        $group_id = ThuChiModule::$group_id;
        $cond = "group_id = {$group_id} AND bill_type={$type} ";
        $rows = $_POST;
        unset($rows['print']);
        unset($rows['bill_id']);
        unset($rows['form_block_id']);
        unset($rows['deleted_ids']);
        $rows['bill_date'] = Date_Time::to_sql_date($rows['bill_date']);
        $rows['amount'] = System::calculate_number($rows['amount']);
        $rows['bill_type'] = $type;
        return $rows;
    }

    // Khởi tạo dữ liệu để ghi phiếu chi
    public function generatePayingData()
    {
        $type = 0;
        $group_id = ThuChiModule::$group_id;
        $cond = "group_id = {$group_id} AND bill_type={$type} ";
        $rows = $_POST;
        unset($rows['bill_id']);
        unset($rows['form_block_id']);
        unset($rows['deleted_ids']);
        unset($rows['print']);
        $rows['payment_full_name'] = Session::get('user_data')['full_name'];
        $rows['payment_account_id'] = Session::get('user_id');
        $rows['bill_date'] = Date_Time::to_sql_date($rows['bill_date']);
        $rows['amount'] = System::calculate_number($rows['amount']);
        $row['bill_type'] = $type;
        $row['mobile'] = Session::get('mobile');
//      System::debug($rows);die;
        return $rows;
    }
}

