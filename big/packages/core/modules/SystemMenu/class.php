<?php
class SystemMenu extends Module{
	function __construct($row){
		if(User::is_admin()){
			Module::Module($row);
			require_once 'db.php';
			$_SESSION['language_id'] = 1;
			require_once 'forms/admin_menu.php';
			$this->add_form(new SystemMenuForm());
		}else{
			Url::redirect('dang-nhap',array('href'=>'?page='. DataFilter::removeXSSinHtml(Url::get('page')).''.( DataFilter::removeXSSinHtml(Url::get('id'))?'&id='. DataFilter::removeXSSinHtml(Url::get('id')):'').''));
		}
	}
}
?>
