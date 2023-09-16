<?php
class EditAdminRolesActivitiesForm extends Form{
	function __construct(){
		Form::Form('EditAdminRolesActivitiesForm');
		$this->add('roles_activities.name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_roles_activities'])){
				foreach($_REQUEST['mi_roles_activities'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('roles_activities',$record['id'])){
						DB::update('roles_activities',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('roles_activities',$record);
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
					DB::delete_id('roles_activities',$id);
				}
			}
			//update_mi_upload_file();
			//exit();
			Url::redirect_current(array());
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = ' 1 = 1';		
		//if(!isset($_REQUEST['mi_roles_activities']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct roles_activities.id) as acount
				from 
					roles_activities
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					roles_activities.*
				from 
					roles_activities
				WHERE
					'.$cond.'
				GROUP BY
					roles_activities.id
				order by 
					roles_activities.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_roles_activities = DB::fetch_all($sql);
			$_REQUEST['mi_roles_activities'] = $mi_roles_activities;
		}
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>