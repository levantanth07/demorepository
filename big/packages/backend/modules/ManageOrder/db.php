<?php
class ManageOrderDB{
	static function get_services($cond)
	{
		$item_per_page = 50;
		$items = DB::fetch_all('
			SELECT
				order_invoice.*
			FROM
				order_invoice
				inner join order_invoice_category on order_invoice_category.id = order_invoice.category_id
			WHERE
				'.$cond.'
			LIMIT
				0,200
		');
		foreach($items as $key=>$value){
			$items[$key]['brief'] = String::display_sort_title(strip_tags($value['description']),40);
		}
		return $items;
	}
	static function updateCart($id){
		if(Session::is_set('order')){
			$orders = Session::get('order');
		}else{
			$orders = array();
		}
		if($item=DB::select('order_invoice','id='.$id)){
			//if($orders[$id])
			{
				$orders[$id]['id'] = $id;
				$orders[$id]['name'] = $item['name'];
				$orders[$id]['description'] = $item['description'];
				$orders[$id]['price'] = $item['price'];
			}
		}
		Session::set('order',$orders);
	}
	static function deleteOrderedService($id){
		if(Session::is_set('order')){
			if(isset($_SESSION['order'][$id])){
				unset($_SESSION['order'][$id]);
			}
		}
	}
	static function updateData(){
		if(Session::is_set('order')){
			$orders = Session::get('order');
			$total = 0;
			$array = array(
				'full_name',
				'email',
				'phone',
				'organization',
				'org_address',
				'job_titile',
				'time'=>time(),
				'account_id'=>Session::get('user_id'),
				'paid'=>0
			);
			$invoice_id = DB::insert('order_invoice',$array);
			foreach($orders as $key=>$value){
				$total += $value['price'];
				$array = array('invoice_id'=>$invoice_id,'product_id'=>$key,'name'=>$value['name'],'price'=>$value['price']);
				DB::insert('order_invoice_detail',$array);
			}
			DB::update('order_invoice',array('total'=>$total),'id='.$invoice_id);
			inset();
		}
	}
	static function get_order_detail(){
		$cond = ' 
			'.(Url::get('id')?' AND order_invoice.id = "'.Url::get('id').'"':'').'
			'.(Url::get('full_name')?' AND order_invoice.full_name LIKE "%'.Url::get('full_name').'%"':'').'
			'.(Url::get('passport')?' AND order_invoice.email LIKE "%'.Url::get('passport').'%"':'').'
			'.(Url::get('nationality_id')?' AND order_invoice.nationality_id = "'.Url::get('nationality_id').'"':'').'
			'.(Url::get('email')?' AND order_invoice.email LIKE "%'.Url::get('email').'%"':'').'
			'.(Url::get('phone')?' AND order_invoice.phone LIKE "%'.Url::get('phone').'%"':'').'
			'.(Url::get('detail')?' AND isd.name LIKE "%'.Url::get('detail').'%"':'').'			
			'.(Url::get('paid')?' AND order_invoice.paid = '.Url::get('paid').'':'').'
		';
		$sql = '
			SELECT
				order_invoice.*,country.name as nationality
			FROM
				order_invoice
				INNER JOIN order_invoice_detail AS isd ON isd.invoice_id = order_invoice.id
				LEFT OUTER JOIN country ON country.id = order_invoice.nationality_id
			WHERE
				'.(User::can_admin(false,ANY_CATEGORY)?'1=1':'order_invoice.account_id = "'.Session::get('user_id').'"').'
				'.$cond.'
			ORDER BY
				order_invoice.paid,order_invoice.id desc
		';
		$orders = DB::fetch_all($sql);
		foreach($orders as $key=>$value){
			$detail = DB::fetch_all('
				SELECT
					order_invoice_detail.*
				FROM
					order_invoice_detail
				WHERE
					order_invoice_detail.invoice_id = '.$key.'
				ORDER BY
					order_invoice_detail.name
			');
			$d = '';
			foreach($detail as $k=>$v){
				$d .= $v['name'].': '.$v['quantity'].'/'.System::display_number($v['price']).''."\n";	
			}
			$orders[$key]['detail'] = $d;
		}
		return $orders;
	}
}
?>