<?php
class AccountConfigAutoBotForm extends Form{
	function __construct(){
		Form::Form('AccountConfigAutoBotForm');
	}
	function on_submit(){

	}
	function draw(){
		$this->map = array();
		$this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
		$this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
		$this->parse_layout('fb_setting',$this->map);
	}
}
?>