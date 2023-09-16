<?php
class EditAdminPhoneStoreForm extends Form{
	function __construct(){
		Form::Form('EditAdminPhoneStoreForm');
		$this->add('phone_store.name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_bundle'])){
				foreach($_REQUEST['mi_bundle'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					$record['total_group'] = $record['total_group']?$record['total_group']:'0';
					if($record['id'] and DB::exists_id('phone_store',$record['id'])){
						DB::update('phone_store',$record,'id='.$record['id']);
					}else{
                        $record['group_id'] = Session::get('group_id');
						unset($record['id']);
						$record['id'] = DB::insert('phone_store',$record);
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				foreach($ids as $id){
					DB::delete_id('phone_store',$id);
				}
			}
			//update_mi_upload_file();
			Url::js_redirect(true);
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			phone_store.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND phone_store.name LIKE "%'.Url::get('keyword').'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_bundle']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct phone_store.id) as acount
				from 
					phone_store
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					phone_store.*
				from 
					phone_store
				WHERE
					'.$cond.'
				GROUP BY
					phone_store.id
				order by 
					phone_store.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_bundle = DB::fetch_all($sql);
			$_REQUEST['mi_bundle'] = $mi_bundle;
		}
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>