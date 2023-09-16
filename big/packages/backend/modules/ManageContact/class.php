<?php
class ManageContact extends Module{
	function ManageContact($row){
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY)){
			if(Url::get('page') == 'manage_contact'){
				switch(Url::get('cmd')){
					case 'delete':
						$this->delete_contact();
						break;
					case 'check':
						$this->check_contact();
						break;
					default:
						require_once 'forms/list.php';
						$this->add_form(new ManageContactForm());
						break;
				}
			}else{
				require_once 'forms/newsletter.php';
				$this->add_form(new ManageNewsletterForm());
			}
		}else{
			Url::access_denied();
		}
	}
	function delete_contact(){
		if(User::can_delete(false,ANY_CATEGORY) and Url::get('id') and $item = DB::exists_id('contact',intval(Url::get('id')))){
			//save_recycle_bin('contact',$item);
			DB::delete('contact','id='.intval(Url::get('id')));
			save_log(Url::get('id'));
		}
		Url::redirect_current();
	}
	function check_contact(){
		if(User::can_edit(false,ANY_CATEGORY) and  Url::get('id') and $contact = DB::fetch('select id,is_check from contact where id='.intval(Url::sget('id')))){
			DB::update_id('contact',array('is_check'=>$contact['is_check']==0?'1':'0'),$contact['id']);
			Url::redirect_current(array('cmd'=>'success'));
		}
	}
}
?>