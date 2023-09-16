<?php
class ListShopForm extends Form
{
	function __construct()
	{
		Form::Form('ListShopForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			foreach(URL::get('selected_ids') as $id)
			{
				$this->delete_group($id);
			}
			require_once 'packages/core/includes/system/update_privilege.php';
			//make_privilege_cache();
			//die;
			Url::js_redirect(true,'Đã xóa thành công',array('expired_month','order_by','order_dir','cmd'=>'shop'));
		}
	}
	function delete_orders($group_id){
		//$orders = DB::select_all('orders','group_id='.$group_id);

		//DB::query('delete order_revisions.* from order_revisions INNER JOIN orders ON orders.id=order_revisions.order_id WHERE orders.group_id = '.$group_id.'');
		//DB::query('delete orders_products.* from orders_products INNER JOIN orders ON orders.id=orders_products.order_id WHERE orders.group_id = '.$group_id.'');
		//DB::query('delete order_changes.* from order_changes INNER JOIN orders ON orders.id=order_changes.order_id WHERE orders.group_id = '.$group_id.'');
		//DB::query('delete order_report.* from order_report INNER JOIN orders ON orders.id=order_report.order_id WHERE orders.group_id = '.$group_id.'');
		
		//DB::delete('orders','group_id = '.$group_id.'');
	}
	function delete_users($group_id){
		$accounts = DB::select_all('account','group_id='.$group_id);
		DB::query('delete party.* from party INNER JOIN account ON account.id=party.user_id WHERE account.group_id = '.$group_id.'');

		DB::delete('account','group_id = '.$group_id.'');
		foreach($accounts as $id=>$val)
			{
				DB::delete('account_privilege','account_id = "'.$id.'"');
				///
				$user_id = DB::fetch('select id from users where username="'.$id.'"','id');
				DB::delete('users','username = "'.$id.'"');
				DB::delete('users_roles','user_id = '.$user_id.'');
				///
				DB::delete('account_privilege','account_id = "'.$id.'"');
				///
				///
				DB::delete_id('account',$id);
			}
	}
	function delete_group($group_id){
		$group = DB::fetch('select id,name from `groups` where id = "'.$group_id.'"');
		$group_name = $group['name'];
		if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY) and $group_id){
			$this->delete_users($group_id);
			$this->delete_orders($group_id);
			DB::delete('fb_pages','group_id = '.$group_id.'');
			DB::delete('fb_cron_config','group_id = '.$group_id.'');
			
			DB::delete('fb_post_comments','group_id = '.$group_id.'');
			
			DB::delete('fb_customers','group_id = '.$group_id.'');
			
			DB::delete('fb_conversation','group_id = '.$group_id.'');
			DB::delete('orders_column_custom','group_id = '.$group_id.'');
			DB::delete('fb_conversation_messages','group_id = '.$group_id.'');
			
			DB::delete('roles','group_id = '.$group_id.'');
			//roles_perms
			
			DB::delete('statuses','group_id = '.$group_id.'');
			DB::delete('order_print_template','group_id = '.$group_id.'');
			DB::delete('order_source','group_id = '.$group_id.'');
			DB::delete('products','group_id = '.$group_id.'');

			DB::delete('groups','id = '.$group_id.'');
			System::log('DELETE','Xóa group','Xóa group '.$group_name);
		}
	}
	function draw()
	{
		$this->map = array();
		if(URL::get('selected_ids'))
		{
			$selected_ids=URL::get('selected_ids');
			foreach($selected_ids as $key=>$selected_id)
			{
				$selected_ids[$key]='"'.$selected_id.'"';
			}
		}
		$cond = '1=1'
				.((URL::get('cmd')=='delete_shop' and is_array(URL::get('selected_ids')))?' and `groups`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
				.(Url::iget('account_type')?' and groups.account_type='.Url::iget('account_type'):'')
				.(Url::get('user_id')?' and (groups.name like "%'.addslashes(Url::get('user_id')).'%" or groups.username like "%'.addslashes(Url::get('user_id')).'%")':'')
				.(Url::get('expired_month')?' and (groups.expired_date >="'.date('Y').'-'.Url::get('expired_month').'-01 00:00:00" AND groups.expired_date <="'.date('Y').'-'.Url::get('expired_month').'-'.(date('t',strtotime(''.date('Y').'-'.Url::get('expired_month').'-01'))).' 00:00:00")':'')
            .(Url::get('system_group_id')?' and '.IDStructure::child_cond(DB::structure_id('groups_system',Url::iget('system_group_id'))).'':'
		');
		if(User::is_admin()){
			
		}else{
            if(Session::get('account_type')==3){
				$cond .= ' and groups.master_group_id='.Session::get('group_id');
			}
		}		
		if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)){
			//echo $cond;
		}
		$item_per_page = 20;
		DB::query('
			select count(groups.id) as acount
			from
				`groups`
				left join groups_system on groups_system.id=groups.system_group_id
				left join groups as parent on parent.id=groups.master_group_id
				left join phone_store on phone_store.id=groups.phone_store_id
			where
				'.$cond.'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page,10,false,'page_no',array('user_id','cmd','account_type','system_group_id','expired_month'));
		DB::query('
			select
				groups.id,
				groups.code,
				groups.name,
				groups.email,
				groups.created,
				groups.account_type,
				groups.active,
				groups.expired_date,
				groups.prefix_post_code,
				groups.image_url,
				groups.master_group_id,
				groups.user_counter,
				groups.description,
				0 as total_order,
				(select count(*) as total from account WHERE group_id=groups.id) AS total_user,
				groups_system.name as system_group_name,
				parent.name as master_group_name,
				phone_store.name as phone_store_name
			from
			 	`groups`
			 	left join groups_system on groups_system.id=groups.system_group_id
			 	left join groups as parent on parent.id=groups.master_group_id
			 	left join phone_store on phone_store.id=groups.phone_store_id
			where
				'.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):(Url::get('expired_month')?'order by groups.expired_date desc':'order by groups.id desc')).'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		$i=1;
		$type_label = array(''=>'Chưa xác định','0'=>'Thường','1'=>'Dùng thử','2'=>'Cũ','3'=>'Hệ thống');
		foreach ($items as $key=>$value)
		{
			$items[$key]['i']=$i++;			
			$items[$key]['admins'] = UserAdminDB::get_admins_of_shop($value['id']);
			$items[$key]['account_type'] = $type_label[$value['account_type']];
		}
		
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
    	$systems = UserAdminDB::get_systems();
		//require_once ('packages/core/includes/utils/category.php');
		//combobox_indent($systems);
		$this->map['system_group_id_list'] = array(''=>'Thuộc hệ thống') + MiString::get_list($systems);
		$expired_month = array();
		for($i=1;$i<=12;$i++){
			$expired_month[$i] = 'Tháng '.$i;
		}
		$this->map['expired_month_list'] = array('Tài khoản hết hạn tháng') + $expired_month;
		///////
		$account_types =  array(''=>'Loại tài khoản',0=>'Tài khoản thường',1=>'Dùng thử',2=>'Tài khoản cũ');
		$this->map['account_type_list'] = $account_types;
		///////
		$this->map['actived_shop'] = UserAdminDB::get_total_actived_shop($cond);
        $this->map['expired_shop'] = UserAdminDB::get_total_expired_shop($cond);
        $this->map['good_shop'] = UserAdminDB::get_total_good_shop($cond);
		$this->parse_layout('shop',$just_edited_id+$this->map +
			array(
				'total'=>$count['acount'],
				'items'=>$items,
				'paging'=>$paging,
					'total_group'=>UserAdminDB::get_total_group()
			)
		);
	}
}
?>
