<?php
class EditAdminUnitsForm extends Form{
	function __construct(){
		Form::Form('EditAdminUnitsForm');
		$this->add('units.name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
	    return;
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_bundle'])){
				foreach($_REQUEST['mi_bundle'] as $key=>$record){
					$record['group_id'] = Session::get('group_id');
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('units',$record['id'])){
						DB::update('units',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('units',$record);
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
					DB::delete_id('units',$id);
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
		$cond = '
			units.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND units.name LIKE "%'.DB::escape(Url::get('keyword')).'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_bundle']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct units.id) as acount
				from 
					units
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					units.*
				from 
					units
				WHERE
					'.$cond.'
				GROUP BY
					units.id
				order by 
					units.id  DESC
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