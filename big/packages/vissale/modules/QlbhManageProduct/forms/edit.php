<?php
class EditQlbhManageProductForm extends Form{
	function __construct(){
		Form::Form('EditQlbhManageProductForm');
		$this->add('qlbh_product.name',new TextType(true,'invalid_name',0,255)); 
		$this->add('qlbh_product.code',new UniqueType('invalid_code','qlbh_product','code')); 
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_product'])){
				foreach($_REQUEST['mi_product'] as $key=>$record){
					$record['price'] = System::calculate_number($record['price']);
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('qlbh_product',$record['id'])){
						DB::update('qlbh_product',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('qlbh_product',$record);
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
			Url::redirect_current(array());
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		//if(!isset($_REQUEST['mi_product']))
		{
			$cond = '1>0
				'.(Url::iget('id')?' AND qlbh_product.id='.Url::iget('id').'':'').'
				'.(Url::get('keyword')?' AND qlbh_product.ten LIKE "%'.Url::get('keyword').'%"':'').'
			';
			$item_per_page = 100;
			DB::query('
				select 
					count(*) as acount
				from 
					qlbh_product
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			DB::query('
				select 
					qlbh_product.*
				from 
					qlbh_product
				where 
					'.$cond.'
				order by 
					qlbh_product.name asc
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			');
			$mi_product = DB::fetch_all();
			foreach($mi_product as $key=>$value){
				$mi_product[$key]['price'] = System::display_number($value['price']);
			}
			$_REQUEST['mi_product'] = $mi_product;
		}
		////////////////////////////////
		$sql = '
			select 
				qlbh_unit.*				
			from 
				qlbh_unit
			where 
				1=1
			order by 
				qlbh_unit.name
		';
		$clbs = DB::fetch_all($sql);
		//$this->map['ssnh_mua_giais'] = $clbs;
		$this->map['unit_id_options'] = '<option value="">Chọn</option>';
		foreach($clbs as $key=>$value){
				$this->map['unit_id_options'] .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
		////////////////////////////////
		$items = DB::fetch_all('
			select 
				`qlbh_product_category`.id
				,`qlbh_product_category`.structure_id
				,`qlbh_product_category`.`status` 
				,`qlbh_product_category`.`icon_url` 
				,`qlbh_product_category`.name
				,`qlbh_product_category`.description
			from 
			 	`qlbh_product_category`
			where
				 qlbh_product_category.status <> "HIDE"
			order by 
				`qlbh_product_category`.structure_id
		');
		$this->map['category_id_options'] = '<option value="">Chọn</option>';
		foreach($items as $key=>$value){
				$this->map['category_id_options'] .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
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
				DB::update('qlbh_product',array('logo'=>Url::get($file)),'id='.$id);
			}
		}
	}
	function delete($id){
		DB::delete_id('qlbh_product',$id);
	}
}
?>