<?php
class EditQlbhInventoryForm extends Form{
	function __construct(){
		Form::Form('EditQlbhInventoryForm');
		$this->link_js('assets/standard/js/autocomplete.js');
		$this->link_css('assets/standard/css/autocomplete/autocomplete.css');
		$this->add('products.product_code',new TextType(true,'miss_code',0,50));
		$this->add('products.opening_stock',new TextType(true,'miss_opening_stock',0,50));
	}
	function on_submit(){
		if($this->check() and !Url::get('importExcelBtn')){
			if(URl::get('group_deleted_ids')){
				$group_deleted_ids = explode(',',URl::get('group_deleted_ids'));
				foreach($group_deleted_ids as $delete_id){
					DB::delete_id('qlbh_inventory',$delete_id);
				}
			}	
			if(isset($_REQUEST['mi_product_group'])){	
				foreach($_REQUEST['mi_product_group'] as $key=>$record){
					$record['opening_stock']=str_replace(',','',$record['opening_stock']);
					$record['year'] = date('Y');
					unset($record['unit']);
					$empty = true;
					foreach($record as $record_value){
						if($record_value){
							$empty = false;
						}
					}
					if(!$empty){
						$record['warehouse_id'] = Url::iget('warehouse_id');
						if($record['id']){
							DB::update('qlbh_inventory',$record,'id=\''.$record['id'].'\'');
						}else{
							unset($record['id']);
							$record['group_id'] = Session::get('group_id');
							if(DB::exists('SELECT id FROM products WHERE code=\''.$record['product_code'].'\' and group_id='.Session::get('group_id').'')){
								DB::insert('qlbh_inventory',$record);
							}
						}
					}
				}
			}else{
				$this->error('product_code','miss_product_code');
				return;
			}
			//exit();
			Url::js_redirect(true,'Bạn cập nhật dữ liệu thành công!');
		}
	}
	function draw(){
		$this->map = array();
		$item = QlbhInventory::$item;
		if($item){
			foreach($item as $key=>$value){
				if(!isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
			if(!isset($_REQUEST['mi_product_group'])){
				$sql = '
					SELECT
						qlbh_inventory.*,
						products.code,
						products.name,
						units.name as unit
					FROM
						qlbh_inventory
						INNER JOIN products ON products.code = qlbh_inventory.product_code
						INNER JOIN units ON units.id = products.unit_id
					WHERE
						qlbh_inventory.id=\''.$item['id'].'\'
				';
				$mi_product_group = DB::fetch_all($sql);
				foreach($mi_product_group as $k=>$v){
					//$mi_product_group[$k]['opening_stock'] = round($v['opening_stock'],2);
				}
				$_REQUEST['mi_product_group'] = $mi_product_group;
			} 
		}else{
			$mi_product_group = array();
		}
		if(Url::get('importExcelBtn') and $excel_file = $_FILES['excel_file'] and $temp_file = $excel_file['tmp_name']){
			$arr = read_excel($temp_file);
			foreach($arr as $key=>$val){
				if($key>1){
					$record['code'] = $val[1];
					$record['name'] = $val[2];
					$record['price'] = $val[3];
					$record['color'] = $val[4];
					$record['size'] = $val[5];
					$record['bundle_id'] = DB::fetch('select id from bundles where name LIKE "'.$val[6].'" and group_id="'.Session::get('group_id').'"','id');
					$record['unit_id'] = DB::fetch('select id from units where name LIKE "'.$val[7].'" and group_id="'.Session::get('group_id').'"','id');
					$record['group_id'] = Session::get('group_id');
					if(!DB::exists('select id from products where code="'.$record['code'].'" and group_id="'.Session::get('group_id').'"')){
						unset($record['opening_stock']);
						DB::insert('products',$record);
					}
					$record['opening_stock'] = $val[8];
					$mi_product_group[$record['code']] = $record;
				}
			}
			$_REQUEST['mi_product_group'] = $mi_product_group;
		}
		$this->map['warehouse_id_list'] = MiString::get_list(DB::select_all('qlbh_warehouse','group_id = 0 or group_id='.Session::get('group_id'),'structure_id'));
		$this->map['title'] = 'Khai báo tồn đầu kỳ';
		$this->map['products'] = DB::fetch_all('SELECT products.code as id,products.name,units.name as unit FROM products INNER JOIN units ON units.id = products.unit_id where products.group_id='.Session::get('group_id'));
		$this->parse_layout('edit',$this->map);
	}
}
?>