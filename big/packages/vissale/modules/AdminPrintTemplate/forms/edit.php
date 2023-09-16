<?php

class EditAdminPrintTemplateForm extends Form{
	function __construct(){
		Form::Form('EditAdminPrintTemplateForm');
		$this->add('print_name',new TextType(true,'loi nhap ten',0,2000));
	}
	function save_item(){
		$rows = array();
		$rows += array(
			'print_name' => DB::escape(Url::get('print_name'))
			,'set_default'=> Url::get('set_default')?DB::escape(Url::get('set_default')):'0'
			,'template'=> Url::get('template')?DB::escape(Url::get('template')):'0'
			,'group_id'=> Session::get('group_id')
			,'print_address'
			,'print_phone'
		);
		//System::debug($rows);die;
		return $rows;
	}
	function on_submit(){
		if($this->check()){
			$rows = $this->save_item();
			if(!$this->is_error()){
				if(Url::get('cmd')=='edit' and $item = DB::exists_id('order_print_template',DB::escape(Url::get('id')))){
					if($rows['set_default']==1){
						DB::query('UPDATE order_print_template SET set_default = 0 WHERE id <> 0');
					}
					$id = intval(Url::get('id'));
					DB::update_id('order_print_template',$rows,$id);
				}else{
					if($rows['set_default']==1){
						DB::query('UPDATE order_print_template SET set_default = 0 WHERE id <> 0');
					}
					$id = DB::insert('order_print_template',$rows);
				}
				//die;
				Url::js_redirect(true);
			}
		}
	}
	function draw(){
		$this->map = array();
		if(Url::get('cmd')=='edit' and Url::get('id') and $items = DB::exists_id('order_print_template',intval(Url::get('id')))){
			foreach($items as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
		}else{
			$_REQUEST['time'] = date('d/m/Y');
		}
		$this->map['template_list'] = array(''=>'Chọn loại in','1'=>'Mẫu 8 đơn hàng trên một trang','2'=>'Mẫu có mã vạch - 2 đơn hàng trên một trang');
		$this->parse_layout('edit',$this->map);
	}
}

?>

