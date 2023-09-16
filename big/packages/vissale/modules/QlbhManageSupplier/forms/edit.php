<?php
class EditQlbhManageSupplierForm extends Form{
	function __construct(){
		Form::Form('EditQlbhManageSupplierForm');
		$this->add('qlbh_supplier.name',new TextType(true,'invalid_name',0,255)); 
		//$this->add('qlbh_supplier.code',new UniqueType('invalid_code','qlbh_supplier','code'));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_supplier'])){
				foreach($_REQUEST['mi_supplier'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					$record['zone_id'] = $record['zone_id']? DB::escape($record['zone_id']) : 0; 
					if($record['id'] and DB::exists_id('qlbh_supplier', DB::escape($record['id']))){
						DB::update('qlbh_supplier',$record,'id='. DB::escape($record['id']));
					}else{
						unset($record['id']);
						$record['group_id']=Session::get('group_id');
						$record['id'] = DB::insert('qlbh_supplier',$record);
					}
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				foreach($ids as $id){
					$this->delete($id);
				}
			}
			//update_mi_upload_file();
            if(Session::get('user_id') == 'dinhkkk'){
			    die;
            }
			//exit();
			Url::js_redirect(true);
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		//if(!isset($_REQUEST['mi_supplier']))
		{
			$cond = '
				qlbh_supplier.group_id = '.Session::get('group_id').'
				'.(Url::iget('id')?' AND qlbh_supplier.id='.Url::iget('id').'':'').'
				'.(Url::get('keyword')?' AND qlbh_supplier.ten LIKE "%'.DB::escape(Url::get('keyword')).'%"':'').'
			';
			$item_per_page = 100;
			DB::query('
				select 
					count(*) as acount
				from 
					qlbh_supplier
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			DB::query('
				select 
					qlbh_supplier.*
				from 
					qlbh_supplier
				where 
					'.$cond.'
				order by 
					qlbh_supplier.name asc
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			');
			$mi_supplier = DB::fetch_all();
			$_REQUEST['mi_supplier'] = $mi_supplier;
		}
		////////////////////////////////
		$sql = '
			select 
				zone.*				
			from 
				zone
			where 
				zone.status <> "HIDE"
				AND '.IDStructure::direct_child_cond(DB::structure_id('zone',1)).'
			order by 
				zone.structure_id
		';
		$clbs = DB::fetch_all($sql);
		//$this->map['ssnh_mua_giais'] = $clbs;
		$this->map['zone_id_options'] = '<option value="">Chọn</option>';
		foreach($clbs as $key=>$value){
				$this->map['zone_id_options'] .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
		////////////////////////////////
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
	function save_item_image($file,$id){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/item/';
		if(isset($_FILES[$file]) and $_FILES[$file]){
			update_upload_file($file,$dir);
			$row = array();
			if(Url::get($file)){
				DB::update('qlbh_supplier',array('logo'=>Url::get($file)),'id='.$id);
			}
		}
	}
	function delete($id){
		DB::delete('qlbh_supplier','id='.DB::escape($id).' and group_id='.Session::get('group_id'));
	}
}
?>