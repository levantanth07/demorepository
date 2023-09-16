<?php
class WaitingListForm extends Form{
	function __construct(){
		Form::Form('WaitingListForm');
	}
	function delete(){
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0){
			foreach($_REQUEST['selected_ids'] as $key){
				if($item = DB::exists('contact_form','id='.$key.' and shop_id='.AdminOrders::$group_id)){
					save_recycle_bin('contact_form',$item);
					save_log($key);
				}
			}
		}
		Url::redirect_current(array('cmd'));
	}
	function update_product($order_id,$product_codes){
		$arr = explode(',',$product_codes);
		$price = 0;
		$total_price = 0;
		foreach($arr as $key=>$val){
			$product = DB::fetch('select id,name,price from products where code = "'.DB::escape($val).'" and group_id='.AdminOrders::$group_id);
			$record['order_id'] = $order_id;
			$record['group_id'] = AdminOrders::$group_id;
			$record['product_id'] = $product['id'];
			$record['product_name'] = $product['name'];
			$record['product_price'] = $product['price']?$product['price']:0;
			$price += $product['price']?$product['price']:0;
			$total_price += $product['price']?$product['price']:0;
			$record['qty'] = 1;
			$record['created'] = date('Y-m-d H:i:s');
			DB::insert('orders_products',$record);
		}
		DB::update('orders',array('price'=>$price,'total_price'=>$total_price),'id='.DB::escape($order_id));
	}
	function on_submit(){
		if(Url::get('make_order')){
			if(isset($_REQUEST['mi_order'])){
				foreach($_REQUEST['mi_order'] as $key=>$record){
					
					$newRecord = array();
					foreach($record as $rkey => $rvalue){
						$newRecord[DB::escape($rkey)] = DB::escape($rvalue);
					}
					$record = $newRecord;

					if(isset($record['checked']) and $record['checked']){
						$account_id= $record['account_id']?DB::escape($record['account_id']):Session::get('user_id');
						$user_created = DB::fetch('select id,username from users where username="'.$account_id.'"','id');
						$source_id = ($record['contact_type']==3)?860:8;//860: tuha.vn, 8: slandingpages.com
						$arr = array(
							'group_id'=>AdminOrders::$group_id,
							'mobile'=>DB::escape($record['phone']),
							'customer_name'=>$record['contact'],
							'note1'=>DB::escape($record['message']),
							'user_created'=>$user_created?$user_created:'0',
							'created'=>date('Y-m-d H:i:s'),
							'status_id'=>CHUA_XAC_NHAN,
							'source_id'=>$source_id,
							'source_name'=>'Landing page',
							'type'=>1
						);
						if(DB::exists('select id from orders where mobile="'.DB::escape($record['phone']).'" and group_id='.AdminOrders::$group_id)){
							DB::update('contact_form',array('checked'=>time()),'id='.DB::escape($record['id']));
						}else{
							if($order_id = DB::insert('orders',$arr)){
								$product_codes= $record['product_codes']?$record['product_codes']:'';
								if($product_codes){
									$this->update_product($order_id,$product_codes);
								}
								DB::update('contact_form',array('checked'=>time()),'id='.DB::escape($record['id']));
							}
						}
					}
					/*
					else{
						unset($record['id']);
						$record['id'] = DB::insert('contact_form',$record);
					}
					*/
				}
			}
			Url::js_redirect(true,'Cập nhật thành công',array('cmd'=>'waiting_list'));
		}
	}
	function draw(){
		$this->map = array();
		$item_per_page_list = array(''=>'Dòng hiển thị',50=>50,100=>100,200=>200);
		$group_name = '';
		if(Url::iget('group_id')){
			$group_name = ' của "'.DB::fetch('select name from `groups` where id='.Url::iget('group_id'),'name').'"';
		}
		//echo AdminOrders::$group_id;
		/*$orders = DB::select_all('orders');
		$i=0;
		foreach($orders as $key=>$val){
			$desc = preg_replace("/data-sheets-value=\"([^\"]+)\"/",'',preg_replace("/data-sheets-userformat=\"([^\"]+)\"/",'',html_entity_decode($val['description_1'])));
			DB::update('orders',array('description_1'=>$desc),'id='.$key);
		}
		die;
		/*$str = '<p><span data-sheets-userformat="[null,null,513,[null,0],null,null,null,null,null,null,null,null,0]" data-sheets-value="[null,2,&quot;\u00c1o kho\u00e1c d\u1ea1 x\u00f9 8822-1&quot;]" style="font-size:13px;font-family:Arial;">Áo khoác dạ xù 8822-2</span></p> <p><span style="line-height: 20.8px;">- Màu sắc: </span><span data-sheets-userformat="[null,null,513,[null,0],null,null,null,null,null,null,null,null,0]" data-sheets-value="[null,2,&quot;v\u00e0ng&quot;]" style="font-size:13px;font-family:Arial;">vàng</span></p> <p>- Chất liệu:&nbsp;<span data-sheets-userformat="[null,null,513,[null,0],null,null,null,null,null,null,null,null,0]" data-sheets-value="[null,2,&quot;len d\u00e0y d\u1eb7n&quot;]" style="font-size:13px;font-family:Arial;">len dày dặn</span></p> <p><span style="line-height: 20.8px;">- Size:&nbsp;</span><span data-sheets-userformat="[null,null,513,[null,0],null,null,null,null,null,null,null,null,0]" data-sheets-value="[null,2,&quot;Free size&quot;]" style="font-size:13px;font-family:Arial;">Free size</span></p> <p>- Kiểu dáng: <span data-sheets-userformat="[null,null,513,[null,0],null,null,null,null,null,null,null,null,0]" data-sheets-value="[null,2,&quot;d\u00e1ng d\u00e0i, c\u1ed5 l\u00f4ng, m\u1eb7c c\u1ef1c sang&quot;]" style="font-size:13px;font-family:Arial;">dáng dài, mặc cực sang</span></p> <p><img alt="" src="http://hangjeans.com/image/data/AO_NU/AoKhoacNu/25-12-2015/Apkhoacdanu 8822-L (6).jpg" style="width: 870px; height: 1100px;" /><img alt="" src="http://hangjeans.com/image/data/AO_NU/AoKhoacNu/25-12-2015/Apkhoacdanu 8822-L (5).jpg" style="width: 870px; height: 1100px;" /><img alt="" src="http://hangjeans.com/image/data/AO_NU/AoKhoacNu/25-12-2015/Apkhoacdanu 8822-L (7).jpg" style="width: 870px; height: 1100px;" /><img alt="" src="http://hangjeans.com/image/data/AO_NU/AoKhoacNu/25-12-2015/Apkhoacdanu 8822-L (8).jpg" style="width: 870px; height: 1100px;" /><img alt="" src="http://hangjeans.com/image/data/AO_NU/AoKhoacNu/25-12-2015/Apkhoacdanu 8822-L (9).jpg" style="width: 870px; height: 1100px;" /></p>';
		echo preg_replace("/data-sheets-value=\"([^\"]+)\"/",'',preg_replace("/data-sheets-userformat=\"([^\"]+)\"/",'',$str));
		die;*/
		$cond = $this->get_condition();
		require_once 'packages/core/includes/utils/paging.php';
		require_once 'cache/config/product_status.php';
		$item_per_page = Url::get('item_per_page')?Url::get('item_per_page'):50;
		$total = AdminOrdersDB::get_total_orders_from_landingpage($cond);
		//$paging = paging($total,$item_per_page,5,false,'page_no',array('item_per_page','group_id','cmd','search_account_id','type','category_id','status','keyword','ngay_tao_from','ngay_tao_to','ngay_xn_from','ngay_xn_to','ngay_chuyen_from','ngay_chuyen_to','is_inner_city'));
		$paging_array = array('item_per_page','group_id','cmd','search_account_id','type','category_id','status','keyword','ngay_tao_from','ngay_tao_to','ngay_xn_from','ngay_xn_to','ngay_chuyen_from','ngay_chuyen_to','is_inner_city');
		//$paging = paging($total,$item_per_page,5,false,'page_no',$paging_array);
		$paging = page_ajax($total,$item_per_page,$paging_array,5,'page_no');
		///////////////
		$items = AdminOrdersDB::get_orders_from_landingpage($cond);
		System::sksort($items, "id",false);
		$_REQUEST['mi_order'] = ($items);
		///////////////
		$status_arr = AdminOrdersDB::get_status();
		$status = MiString::get_list($status_arr);
		$status_options = '<option value="">Chọn</option>';
		foreach($status_arr as $key=>$val){
			$status_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
		}
		$this->map['status_options'] = $status_options;
		//////////////

		$this->map['device'] = Session::get('device');
		$layout = 'waiting_list';
		$party = AdminOrdersDB::get_order_info();
		$master_group_id = get_master_group_id();
		if($master_group_id and is_master_group()){
			$groups = get_groups($master_group_id);
		}else{
			$groups = array();
		}	
		
		$this->map += array(
				'is_master_group'=>is_master_group(),
				'group_name'=>$group_name,
				'shipping_services'=>AdminOrdersDB::shipping_services(),
				'status'=>AdminOrdersDB::get_status(),
				'items'=>$items,
				'paging'=>$paging,
				'group_id_list'=>array(''=>'Chọn GROUP') + MiString::get_list($groups),
				'total'=>$total,
				'category_id_list'=>array(""=>'Chọn phân loại')+MiString::get_list(AdminOrdersDB::get_category()),
				'status_id_list'=>array(""=>'Chọn trạng thái')+$status,
				'search_account_id_list'=>array(""=>'Chọn nhân viên')+MiString::get_list(AdminOrdersDB::get_users()),
				'item_per_page_list'=>$item_per_page_list
		);
		$this->parse_layout($layout,$party + $this->map);
	}
	function get_condition(){
		$cond = 'contact_form.shop_id = '.AdminOrders::$group_id.' and (checked = 0 or checked is null)';
		$cond .= URL::get('keyword')? ' AND (contact_form.message like "%'.addslashes(URL::sget('keyword')).'%" or contact_form.email like "%'.addslashes(URL::sget('keyword')).'%" or contact_form.contact like "%'.addslashes(URL::sget('keyword')).'%" or contact_form.phone = "'.addslashes(URL::sget('keyword')).'")':'';
		return $cond;
	}
}
?>
