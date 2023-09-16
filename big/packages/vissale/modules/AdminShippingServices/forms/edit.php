<?php
class EditAdminShippingServicesForm extends Form{
	function __construct(){
		Form::Form('EditAdminShippingServicesForm');
		$this->add('shipping_services.name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			
			if(isset($_REQUEST['mi_ss'])){
				foreach($_REQUEST['mi_ss'] as $key=>$record){
					$newRecord = array();
					foreach($record as $rkey => $rvalue){
						$newRecord[DB::escape($rkey)] = DB::escape($rvalue);
					}
					$record = $newRecord;
					if($record['id']=='(auto)'){
						$record['id']=false;
					}

					if($record['id']){
						$saved = DB::fetch('SELECT `id` FROM shipping_services WHERE id = ' . DB::escape($record['id']) . ' AND group_id = ' . Session::get('group_id'));
						if(!$saved){
							Url::js_redirect(true, 'Dữ liệu không hợp lệ !');
						}
						DB::update('shipping_services',$record,'id='.DB::escape($record['id']));
					}else{
						unset($record['id']);
						$record['group_id'] = Session::get('group_id');
						$record['id'] = DB::insert('shipping_services',$record);
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}

			if(URL::get('deleted_ids')){
				$ids = parse_id(URL::get('deleted_ids'));
				foreach($ids as $id){
					$id = DB::escape($id);
					DB::delete('shipping_services',"id = $id AND group_id = " . Session::get('group_id'));
				}
			}
			Url::js_redirect();
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			(shipping_services.group_id = '.Session::get('group_id').')
				'.(Url::get('keyword')?' AND shipping_services.name LIKE "%'.Url::get('keyword').'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_ss']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct shipping_services.id) as acount
				from 
					shipping_services
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					shipping_services.*
				from 
					shipping_services
				WHERE
					'.$cond.'
				GROUP BY
					shipping_services.id
				order by 
					shipping_services.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_ss = DB::fetch_all($sql);
			$_REQUEST['mi_ss'] = $mi_ss;
		}
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>