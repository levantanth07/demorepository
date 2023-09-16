<?php
class EditQlbhCuaHangForm extends Form{
	function __construct(){
		Form::Form('EditQlbhCuaHangForm');
		$this->add('qlbh_shop.name',new TextType(true,'invalid_name',0,255)); 
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_shop'])){
				foreach($_REQUEST['mi_shop'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('qlbh_shop',$record['id'])){
						DB::update('qlbh_shop',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['account_id']=Session::get('user_id');
						$record['id'] = DB::insert('qlbh_shop',$record);
					}
				}
				if (isset($ids) and sizeof($ids)){
					//$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				foreach($ids as $id){
					$this->delete($id);
				}
			}
			//update_mi_upload_file();
			Url::redirect_current(array());
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		//if(!isset($_REQUEST['mi_shop']))
		{
			$cond = '1=1
				'.(Url::iget('search_zone_id')?' AND '.IDStructure::child_cond(DB::structure_id('zone',Url::iget('search_zone_id'))).'':'').'
				'.(Url::iget('id')?' AND qlbh_shop.id='.Url::iget('id').'':'').'
				'.(Session::get('warehouse_id')?' AND qlbh_shop.account_id="'.Session::get('user_id').'"':'').'
				'.(Url::get('keyword')?' AND qlbh_shop.ten LIKE "%'.Url::get('keyword').'%"':'').'
			';
			$item_per_page = 100;
			DB::query('
				select 
					count(*) as acount
				from 
					qlbh_shop
					left outer join zone on zone.id = qlbh_shop.zone_id
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			DB::query('
				select 
					qlbh_shop.*
				from 
					qlbh_shop
					left outer join zone on zone.id = qlbh_shop.zone_id
				where 
					'.$cond.'
				order by 
					qlbh_shop.account_id,qlbh_shop.name asc
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			');
			$mi_shop = DB::fetch_all();
			$_REQUEST['mi_shop'] = $mi_shop;
		}
		////////////////////////////////
		$sql = '
			select 
				zone.*				
			from 
				zone
			where 
				zone.status <> "HIDE"
				AND '.IDStructure::child_cond(DB::structure_id('zone',1)).'
			order by 
				zone.structure_id
		';
		$zones = DB::fetch_all($sql);
		require_once('packages/core/includes/utils/category.php');
		combobox_indent($zones);
		$this->map['search_zone_id_list'] = array('Cửa hàng trên toàn quốc') + MiString::get_list($zones);
		$this->map['zone_id_options'] = '<option value="">Chọn</option>';
		foreach($zones as $key=>$value){
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
				DB::update('qlbh_shop',array('logo'=>Url::get($file)),'id='.$id);
			}
		}
	}
	function delete($id){
		DB::delete_id('qlbh_shop',$id);
	}
}
?>