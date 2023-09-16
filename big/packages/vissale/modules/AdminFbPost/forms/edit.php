<?php
class EditAdminFbPostForm extends Form{
	function __construct(){
		Form::Form('EditAdminFbPostForm');
		$this->link_css('assets/default/css/cms.css');
		$this->add('fb_posts.name',new TextType(true,'Chưa nhập tên sản phẩm',0,255));
	}
	function on_submit(){
		$groupId = Session::get('group_id');
		if(Url::get('confirm_edit') and !Url::get('search')){
			if(isset($_REQUEST['mi_product'])){
				$user_id = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
				foreach($_REQUEST['mi_product'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					$record['page_id'] = (isset($record['page_id']) and $record['page_id'])?  DB::escape($record['page_id']):''; 
					unset($record['total_order']);
                    $record['bundle_id'] = $record['bundle_id']?  DB::escape($record['bundle_id']):'0';
					if($record['id'] and DB::exists_id('fb_posts', DB::escape($record['id']))){
						$record['modified'] = date('Y-m-d H:i:s');
						$record['user_modified'] = $user_id;
						DB::update('fb_posts',$record,'id='. DB::escape($record['id']).' AND group_id ='.$groupId);
					}else{
						unset($record['id']);
						////
						$record['fb_page_id'] = 0;
						$record['fb_post_id'] = 0;
						$record['product_id'] = 0;
						$record['nodata_number_day'] = 0;
						$record['last_time_fetch_comment'] = 0;
						$record['next_time_fetch_comment'] = 0;
						$record['level_fetch_comment'] = 0;
						$record['status'] = 0;
						$record['hide_phone_comment'] = 0;
						////
						$record['group_id'] = Session::get('group_id');
						$record['created'] = date('Y-m-d H:i:s');
						$record['modified'] = '0000-00-00 00:00:00';
						$record['user_created'] = $user_id;
						$record['user_modified'] = 0;
						$record['id'] = DB::insert('fb_posts',$record);
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				$formatIds = [];
				foreach ($ids as $value) {
				    $formatIds[] = DB::escape($value);
				}
				$strIds =  implode(',', $formatIds);
				$sql = "DELETE FROM fb_posts WHERE id IN $strIds AND group_id = $groupId";
				$query = DB::query($sql);
			}
			if(Session::get('user_id')=='dinhkkk'){
				//die;
			}
			Url::js_redirect(true);
		}
	}
	function draw(){
		$this->map = array();
		$paging = '';
		$group_id = Session::get('group_id');
		$cond = '
			fb_posts.group_id='.$group_id.'
				'.(Url::get('keyword')?' AND (fb_posts.post_id LIKE "%'.DB::escape(Url::get('keyword')).'%" or fb_posts.page_id LIKE "%'.DB::escape(Url::get('keyword')).'%")':'').'
			';		
		//if(!isset($_REQUEST['mi_product']))
		{
			$item_per_page = 30;
			DB::query('
				select 
					count(*) as acount
				from 
					fb_posts
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page,20,false,'page_no',array('keyword'));
			$sql = '
				select 
					fb_posts.*
				from 
					fb_posts
				WHERE
					'.$cond.'
				GROUP BY
					fb_posts.id
				order by 
					fb_posts.id DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';//(select count(orders_fb_posts.id) from orders_fb_posts where orders_fb_posts.product_id=fb_posts.id limit 0,1)
			$mi_product = DB::fetch_all($sql);
			foreach($mi_product as $key=>$val){
				//$mi_product[$key]['price'] = System::display_number($val['price']);
			}
			$_REQUEST['mi_product'] = $mi_product;
		}
		$this->map['paging'] = $paging;
		$paging = paging($count['acount'],$item_per_page);
		/////
		require_once('packages/vissale/modules/AdminOrders/db.php');
		$mkts = AdminOrdersDB::get_users('MARKETING',false,true);
		$mkt_options = '<option value="">Chọn Marketing</option>';
		foreach($mkts as $key=>$val){
			$mkt_options .= '<option value="'.$key.'">'.$val['full_name'].'</option>';
		}
		$this->map['mkt_options'] = $mkt_options;
		//////
        if(Session::get('master_group_id')){
            $group_id = Session::get('master_group_id');
        }
        $bundles = AdminOrdersDB::get_bundles($group_id);
        $bundles_options = '<option value="">--Chọn--</option>';
        $bundles_options .= '<option value="0">Khác</option>';
        foreach($bundles as $key=>$val){
            $bundles_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['bundles_options'] = $bundles_options;
        //////
		$this->parse_layout('edit',$this->map);
	}
}
?>