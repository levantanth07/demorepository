<?php
class EditAdminGroupsMasterForm extends Form{
	function EditAdminGroupsMasterForm(){
		Form::Form('EditAdminGroupsMasterForm');
		$this->add('groups_master.name',new TextType(true,'Chưa nhập nhãn',0,125));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			require_once 'packages/core/includes/utils/search.php';
			if(isset($_REQUEST['mi_label'])){
				foreach($_REQUEST['mi_label'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					//$record['name_id'] = convert_utf8_to_url_rewrite($record['name']);
					if($record['id'] and DB::exists_id('groups_master',$record['id'])){
						DB::update('groups_master',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('groups_master',$record);
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
					DB::delete_id('groups_master',$id);
				}
			}
			//update_mi_upload_file();
			Url::js_redirect(true,'Dữ liệu đã được cập nhật');
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			1=1
				'.(Url::get('keyword')?' AND groups_master.name_1 LIKE "%'.Url::get('keyword').'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_label']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct groups_master.id) as acount
				from 
					groups_master
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					groups_master.*
				from 
					groups_master
				WHERE
					'.$cond.'
				GROUP BY
					groups_master.id
				order by 
					groups_master.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_label = DB::fetch_all($sql);
			$_REQUEST['mi_label'] = $mi_label;
		}
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>