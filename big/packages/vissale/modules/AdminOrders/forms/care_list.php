<?php
class CareListForm extends Form{
	function __construct(){
		Form::Form('CareListForm');
	}
	function save_position(){
		foreach($_REQUEST as $key=>$value){
			if(preg_match('/position_([0-9]+)/',$key,$match) and isset($match[1])){
				DB::update_id('orders',array('position'=>Url::get('position_'.$match[1])),$match[1]);
			}
		}
		Url::redirect_current();
	}
	function on_submit(){
		switch(Url::get('cmd')){
			default:
				if(Url::get('autoAssignOrder')){// chia nhanh
					if($all_ass_account_id = Url::get('all_ass_account_id')){// added by khoand in 05/10/2018
						$new_account_ids = array();
						foreach($all_ass_account_id as $value){
							$user = DB::fetch('select id,name from users where id='.DB::escape($value).'');
							$new_account_ids[$user['id']]['user_id'] = $user['id'];
							$new_account_ids[$user['id']]['id'] = $value;
							$new_account_ids[$user['id']]['name'] = $value;
						}
						$options = [
							'acc_group_id'=>false,
							'items'=>[],
							'account_ids'=>$new_account_ids,
							'source_id'=> DB::escape(Url::get('ass_source_id')),
							'bundle_id'=> DB::escape(Url::get('ass_bundle_id')),
							'assign_option'=>DB::escape(Url::get('assign_option')),
							'limit'=>Url::iget('assigned_total')
						];
						if($total = $this->autoAssignOrder($options)){
							Url::js_redirect(true,'Gán đơn tự động '.$total.' đơn thành công',['cmd'=>'care_list','act']);
						}else{
							Url::js_redirect(true,'Không có tài khoản hoặc đơn hàng nào được chọn để chia đơn',['cmd'=>'care_list','act']);
						}
					}else{
						$account_group_id=Url::iget('assigned_account_group_id')?Url::iget('assigned_account_group_id'):false;
						$options = [
							'acc_group_id'=>$account_group_id,
							'source_id'=>DB::escape(Url::get('ass_source_id')),
							'bundle_id'=>DB::escape(Url::get('ass_bundle_id')),
							'assign_option'=>DB::escape(Url::get('assign_option')),
							'limit'=>Url::iget('assigned_total')
						];
						if($total = $this->autoAssignOrder($options)){
							Url::js_redirect(true,'Gán đơn tự động '.$total.' đơn thành công',['cmd'=>'care_list','act']);
						}else{
							Url::js_redirect(true,'Không có tài khoản hoặc đơn hàng nào được chọn để chia đơn',['cmd'=>'care_list','act']);
						}
					}
				}
				break;
		}
	}
	function draw(){
		$this->map = array();
		if(!Url::get('ngay_from')){
			$_REQUEST['ngay_from'] = date('d/m/Y',time() - 24*3*3600);
		}
		if(!Url::get('ngay_to')){
			$_REQUEST['ngay_to'] = date('d/m/Y');
		}
		$account_type = $this->map['account_type'] = AdminOrders::$account_type;
		$this->map['total_not_assigned_order'] = 0;
		$item_per_page_list = array(''=>'Dòng hiển thị',50=>50,100=>100,200=>200);
		$group_name = '';
		if(Url::iget('group_id')){
			$group_name = ' của "'.DB::fetch('select name from `groups` where id='.Url::iget('group_id'),'name').'"';
		}
		$cond = '1=1';
		$cond .= $this->get_condition();
		$this->get_just_edited_id();
		require_once 'packages/core/includes/utils/paging.php';
		require_once 'cache/config/product_status.php';
		$item_per_page = Url::iget('item_per_page')?Url::iget('item_per_page'):50;
		$total = $this->get_total_item($cond);
		$total_amount = 0;
		//$paging = paging($total,$item_per_page,5,false,'page_no',array('item_per_page','group_id','cmd','search_account_id','type','category_id','status','keyword','ngay_from','ngay_to','ngay_xn_from','ngay_xn_to','ngay_chuyen_from','ngay_chuyen_to','is_inner_city'));
		$paging_array = array('item_per_page','group_id','cmd','search_user_assigned_id','type','status_id','keyword','ngay_from','ngay_to','ngay_xn_from','ngay_xn_to','ngay_chuyen_from','ngay_chuyen_to','order_status_id','cs_status_id');
		//$paging = paging($total,$item_per_page,5,false,'page_no',$paging_array);
        $paging = paging($total,$item_per_page,20,false,'page_no',$paging_array);
		//////////////
		$this->map['cs_status_id_list'] = AdminOrders::$cs_status;
		$this->map['order_status_id_list'] = [''=>'Trạng thái đơn',THANH_CONG=>'Thành công',CHUYEN_HANG=>'Chuyển hàng'];

		$items = $this->get_items($cond,'orders.id DESC',$item_per_page);
		System::sksort($items, "id",false);

		$this->map['items'] = $items;
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
		$layout = 'care_list';
		$party = AdminOrdersDB::get_order_info();
		$master_group_id = get_master_group_id();
		if($master_group_id and is_master_group()){
			$groups = get_groups($master_group_id);
		}else{
			$groups = array();
		}
        $cs_users = AdminOrdersDB::get_users('CS',false,true);
		$this->map['min_search_phone_number'] = 6;//get_group_options('min_search_phone_number');
		if(AdminOrders::$quyen_cs){
			$restricted_users = MiString::get_list($cs_users,'full_name');
		}else{
			$restricted_users = MiString::get_list(AdminOrdersDB::get_users('CS',false,false,true),($account_type==TONG_CONG_TY)?'full_name':'');
		}
		//user_assigned_id
		$users = AdminOrdersDB::get_users('CS',false,true);
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
				'search_account_id_list'=>array(""=>'Chọn nhân viên')+MiString::get_list($users),
				'account_id_list'=>array(""=>'Chọn nhân viên để gán đơn hàng')+MiString::get_list($users),
				'bundle_id_list'=>array(""=>'Phân loại')+MiString::get_list(AdminOrdersDB::get_bundles()),
				'all_ass_account_id[]_list'=>array(""=>'Chọn nhân viên để gán đơn hàng')+$restricted_users,
				'item_per_page_list'=>$item_per_page_list,
				'total_amount'=>System::display_number($total_amount),
				'search_user_assigned_id_list'=>array(""=>'Chọn nhân viên')+MiString::get_list($users,'full_name')
		);
		$this->parse_layout($layout,$party + $this->just_edited_id+$this->map);
	}
	function get_just_edited_id(){
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')){
			if(is_string(UrL::get('selected_ids'))){
				if (strstr(UrL::get('selected_ids'),',')){
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function get_condition(){
		$cond = '';
		if(Url::get('category_id') and DB::exists_id('category',intval(Url::sget('category_id')))){
			$cond.= ' and '.IDStructure::child_cond(DB::structure_id('category',intval(Url::sget('category_id'))));
		}
		if(Url::get('group_id')){
			$cond.= ' and orders.group_id='.DB::escape(Url::get('group_id')).'';
		}else{
			$cond.= ' and orders.group_id='.Session::get('group_id').'';
		}
		if(Url::iget('bundle_id')){
			$cond.= ' and orders.bundle_id='.Url::iget('bundle_id').'';
		}
		if($cs_status_id = Url::iget('cs_status_id')){
			$cond.= ' and order_rating.cs_status_id='.$cs_status_id.'';
		}
		if(AdminOrders::$quyen_admin_cs){
			if($user_assigned_id = Url::iget('search_user_assigned_id') and DB::exists('select id from users where id='.$user_assigned_id.' and group_id='.AdminOrders::$group_id)){
				$cond .= ' and order_rating.assigned_user_id='.$user_assigned_id;
			}
		}
		if(Url::check('is_inner_city')){
			$cond.= ' and orders.is_inner_city=1';
		}
		if($ss = Url::get('shipping_services')){
			$idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
			$cond.= ' and orders.shipping_service_id IN ('.($ss).')';
		}
		if($ss = Url::get('dau_so')){
			$cond.= ' and orders.telco_code IN ("'.DB::escape($ss).'")';
		}

		if($ids = Url::get('ids')){
			$idsArray = explode(',', $ids);
            $escapeIds = array_map(function($id){
                return DB::escape($id);
            }, $idsArray);
            $ids = implode(',', $escapeIds);
			$cond.= ' and orders.id IN ('.($ids).')';
		}
		$min_search_phone_number = get_group_options('min_search_phone_number');
		$min_search_phone_number = 6;//$min_search_phone_number?$min_search_phone_number:3;
		if($keyword = DB::escape(DataFilter::removeDuplicatedSpaces(URL::get('keyword'))) and strlen($keyword) >= 2){
			$keyword = preg_replace('/\"/', '', $keyword);
			if(strlen($keyword)>=$min_search_phone_number){
				if(Session::get('account_type')==TONG_CONG_TY or AdminOrders::$master_group_id){
					$cond .= ' AND (
										(orders.postal_code) = "'.$keyword.'" 
										or orders.id = '.intval($keyword).'
										or (orders.code) = "'.$keyword.'"

										or (
											(orders.mobile) LIKE ("'.$keyword.'%")
											OR (orders.mobile2) LIKE ("'.$keyword.'%")	
										)
									)';
				}else{
					$cond .= ' AND (
						(orders.postal_code) = "'.$keyword.'"
						or orders.id = '.intval($keyword).'
						or (orders.code) = "'.$keyword.'"

						or (
							(orders.mobile) LIKE ("0'.$keyword.'%")
							OR (orders.mobile) LIKE ("'.$keyword.'%")
							OR (orders.mobile2) LIKE ("'.$keyword.'%")	
						)
					)';
				}
				//	or orders.note2 like "%'.$keyword.'%" or orders.note1 like "%'.$keyword.'%"
				//OR (orders.mobile2) LIKE ("'.$keyword.'%")
				//OR (orders.customer_name) LIKE ("'.$keyword.'")
			}else{
				$cond .= ' AND order_rating.assigned_user_id='.AdminOrders::$user_id;
				$cond .= ' AND (
					orders.postal_code = "'.$keyword.'" 
					or orders.id = '.intval($keyword).'
					or orders.code = "'.$keyword.'"
				)';
			}
			$cond .= ' and (orders.status_id='.THANH_CONG.' or orders.status_id='.CHUYEN_HANG.')';
		}else{
			if(!AdminOrders::$quyen_admin_cs){
				$cond .= ' AND order_rating.assigned_user_id='.AdminOrders::$user_id;
			}
			if(Url::get('act')=='need_rate'){
				$cond .= '
					AND ((select count(id) from order_rating where order_id=orders.id) = 0 or order_rating.cs_status_id=0)
				';
			}else if(Url::get('act')=='overdue'){
				$cond .= ' 
					AND orders_extra.update_successed_time < date_sub(NOW(), INTERVAL 2 day)
					AND ((select count(id) from order_rating where order_id=orders.id) = 0 or order_rating.cs_status_id=0)
				';
			}else if(Url::get('act')=='rated'){
				$cond .= ' 
					AND (select count(id) from order_rating where order_id=orders.id and rating_time>0) > 0
				';
			}else{
				if($order_status_id = Url::iget('order_status_id')){
					$cond.= ' and orders.status_id='.$order_status_id;
				}
			}
		}
		if($c_n = DB::escape(DataFilter::removeDuplicatedSpaces(URL::get('customer_name')))){
			$c_n = preg_replace('/\"/', '', $c_n);
			if(strlen($c_n)>=2){
				$cond .= ' AND orders.customer_name LIKE "%'.$c_n.'%"';
				//$cond .= ' AND MATCH(orders.customer_name) AGAINST ("'.$c_n.'" IN NATURAL LANGUAGE MODE)';
			}
		}
		if(Url::iget('order_status_id') == CHUYEN_HANG) {
			$cond .= '
						'.(Url::get('ngay_from')?' AND orders.delivered>="'.Date_Time::to_sql_date(Url::get('ngay_from')).' 00:00:00"':'').'
						'.(Url::get('ngay_to')?' AND orders.delivered<="'.Date_Time::to_sql_date(Url::get('ngay_to')).' 23:59:59"':'').'
					';
			$cond .= '
						and (orders_extra.delivered >= "'.date('Y-m-d',time()-30*24*3600).'")
					';
		}else{
			$cond .= '
						'.(Url::get('ngay_from')?' AND orders_extra.update_successed_time>="'.Date_Time::to_sql_date(Url::get('ngay_from')).' 00:00:00"':'').'
						'.(Url::get('ngay_to')?' AND orders_extra.update_successed_time<="'.Date_Time::to_sql_date(Url::get('ngay_to')).' 23:59:59"':'').'
					';
			$cond .= '
						and (orders_extra.update_successed_time >= "'.date('Y-m-d',time()-30*24*3600).'")
					';
		}
//		if(System::is_local() or Session::get('user_id') == 'kkk.khoand'){
//			System::debug($cond);
//		}
		return $cond;
	}
	function get_total_item($cond){
		$master_group_id = AdminOrders::$master_group_id;
		$join = ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
		$sql = '
			select
				orders.id
			FROM
				orders
				JOIN `groups` ON groups.id = orders.group_id
				LEFT JOIN order_rating ON order_rating.order_id = orders.id
				'.$join.'
				'.(Url::sget('product_code')?' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id':'').'
			WHERE
				'.$cond.'
				';
		$m_key= md5('total_item_'.$cond);
		if ($m_key and !System::is_local() and $master_group_id) {
			$total = MC::get_items($m_key);
			if (!$total) {
				DB::query($sql);
				$qr = DB::$db_result;
				$total = 0;
				if(isset($qr->num_rows)){
					$total = $qr->num_rows;
				}
				MC::set_items($m_key, $total, time() + 60);
			}
		} else {
			DB::query($sql);
			$qr = DB::$db_result;
			$total = 0;
			if(isset($qr->num_rows)){
				$total = $qr->num_rows;
			}
		}
		return $total;
	}
    function get_items($cond,$order_by,$item_per_page=false){
        $statuses = AdminOrdersDB::get_status();
        if($ids = Url::get('checked_order') or $ids=Url::get('ids')){
            $item_per_page = false;
        }
        $districts = AdminOrdersDB::get_districts();
        $wards = AdminOrdersDB::get_wards();
        require_once 'packages/core/includes/utils/paging.php';
        $master_group_id = AdminOrders::$master_group_id;
        $group_id = Session::get('group_id');
        $group = DB::fetch('select id,account_type,prefix_post_code from `groups` where id='.$group_id);
        $pre = $group['prefix_post_code'];
        $show_full_name = get_group_options('show_full_name');
		$join = ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        $sql = '
			SELECT
				orders.id,
				orders.group_id,
				orders.master_group_id,
				orders.fb_customer_id,
				orders.fb_page_id,
				orders.fb_post_id,
				orders.fb_comment_id,
				orders.fb_conversation_id,
				orders.total_qty,
				orders.code,
				orders.postal_code,
				orders.customer_name,
				orders.customer_id,
				orders.mobile,
				orders.mobile2,
				orders.telco_code,
				orders.city,
				orders.address,
				orders.note1,
				orders.note2,
				orders.note1 as note,
				orders.cancel_note,
				orders.shipping_note,
				orders.status_id,
				orders.price,
				orders.discount_price,
				orders.shipping_price,
				orders.other_price,
				orders.total_price,
				orders.user_modified,
				orders.user_confirmed,
				orders.confirmed,
				orders.delivered,
				orders.user_delivered,
				orders.created,
				orders.modified,
				orders.last_online_time,
				orders.last_edited_account_id,
				orders.source_id,
				orders.source_name,
				orders.type,
				orders.bundle_id,
				groups.name as group_name,
				orders.district_id,
                "" AS district_reciever,
                orders.ward_id,
                "" AS ward,
                orders_extra.update_successed_time,
                order_rating.cs_note,
                IF(orders_extra.update_successed_time>=date_sub(NOW(), INTERVAL 2 day),1,0) as can_rate,
				(SELECT shipping_services.name FROM shipping_services WHERE shipping_services.id = orders.shipping_service_id) shipping_service,
				(SELECT party.label FROM party JOIN users ON users.username=party.user_id WHERE users.id=orders.user_assigned limit 0,1) AS label,
				(SELECT concat(users.username,"<br>",users.name) FROM users WHERE users.id=order_rating.assigned_user_id limit 0,1) AS user_assigned,
				(SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_confirmed limit 0,1) AS user_confirmed_name,
				(SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_created limit 0,1) AS user_created,
				(SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_delivered limit 0,1) AS user_delivered_name,
				(SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders_extra.update_successed_user limit 0,1) AS user_successed_name,
				(SELECT '.(($show_full_name==1)?'upsale_user.name':'upsale_user.username').' FROM orders_extra JOIN users as upsale_user ON upsale_user.id = orders_extra.upsale_from_user_id WHERE upsale_user.id=orders_extra.upsale_from_user_id AND orders_extra.order_id = orders.id limit 0,1) AS upsale_from_user_id
			FROM
				orders
				JOIN `groups` ON groups.id = orders.group_id
				LEFT JOIN order_rating ON order_rating.order_id = orders.id
				'.$join.'
				'.(Url::sget('product_code')?' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id':'').'
			WHERE
				'.$cond.'
			ORDER BY
				'.$order_by.'
			'.($item_per_page?' LIMIT '.((page_no()-1)*$item_per_page).', '.DB::escape($item_per_page).'':'').'
		';
//        if(Session::get('user_id')=='kkk.khoand'){
//        	//System::debug($sql);
//		}
		$items = DB::fetch_all($sql);
        $i=1;

		$length = get_group_options('hide_phone_number');
        foreach($items as $key =>$value){
        	$sql = 'SELECT 
						order_rating.id,
						order_rating.rating_point,
						users.`name` as rating_user,
						order_rating.cs_status_id
					FROM 
						order_rating
						left join users on users.id=order_rating.rating_user_id
					WHERE 
						order_rating.order_id='.$key;
        	if($rating = DB::fetch($sql)){
        		if(intval($rating['rating_point'])>0){
					$items[$key]['rated'] = $rating['rating_point'];
					$items[$key]['rating_user'] = $rating['rating_user'];
					$items[$key]['rating_time'] = date('H:i d/m/Y');
					$items[$key]['can_rate'] = false;
				}else{
					$items[$key]['rated'] = false;
					$items[$key]['rating_user'] = '';
				}
				$items[$key]['cs_status_name'] = $this->map['cs_status_id_list'][$rating['cs_status_id']];
			}else{
				$items[$key]['rated'] = false;
				$items[$key]['rating_user'] = '';
				$items[$key]['cs_status_name'] = 'Chưa xử lý';
			}
        	$items[$key]['customer_name'] = MiString::string2js($value['customer_name']);

			$items[$key]['address'] = MiString::string2js($value['address']);
			$items[$key]['note1'] = MiString::string2js($value['note1']);
			$items[$key]['note2'] = MiString::string2js($value['note2']);
			$items[$key]['note'] = MiString::string2js($value['note']);
            $no_style = true;
            if((Url::get('act')!='print' and Url::get('cmd')!='export_excel') and Url::get('cmd') != 'export_ship_excel' and Url::get('cmd') != 'care_list' ){
                $no_style = false;
            }
            $items[$key]['district_reciever'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
            $items[$key]['ward'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';
            //----------------- start xu ly hien thi trang thai -----------------
            $status = '...';
            $status_color = '#EFEFEF';
            if(isset($statuses[$value['status_id']])){
                $status = $statuses[$value['status_id']]['name'];
                $status_color = $statuses[$value['status_id']]['color'];
            }
            if($no_style==false){
                $status = '<span class="order-list-status" style="border: 1px solid '.$status_color.';border-left: 3px solid '.$status_color.';">'.$status.'</span>';
            }
            $items[$key]['status_name'] = $status;
            //----------------- end xu ly hien thi trang thai -----------------
            //----------------- start xu ly hien thi mobile -------------------
			$mbl1 = ModifyPhoneNumber::hidePhoneNumber($value['mobile'], $length);
			$mbl2 = ModifyPhoneNumber::hidePhoneNumber($value['mobile2'], $length);
            $mobile = $mbl1.($mbl1?' ':'').$mbl2;

            $items[$key]['mobile'] = $mobile;
            //----------------- end xu ly hien thi mobile ---------------------
            $create_time = strtotime($value['created']);
            $items[$key]['created'] = date('Y-m-d H:i:s\'',$create_time);
            if($item_per_page){
                $index = $i + $item_per_page*(page_no()-1);
            }else{
                $index = $i;
            }
            $items[$key]['index'] = $index;
            $source_name = $value['source_name'].' ('.(($value['type']==2)?'CSKH':'SALE').')';
            $items[$key]['source'] = $source_name;

            $items[$key]['code'] = str_pad($key,6,'0',STR_PAD_LEFT);
            $items[$key]['total_price'] = System::display_number($value['total_price']);
            $items[$key]['editting'] = AdminOrdersDB::is_edited($key);
            $i++;
        }
        return ($items);
    }
	function autoAssignOrder(Array $options){
		$acc_group_id=isset($options['acc_group_id'])?$options['acc_group_id']:false;
		$items=isset($options['items'])?$options['items']:[];
		$account_ids=isset($options['account_ids'])?$options['account_ids']:false;
		$source_id=isset($options['source_id'])?$options['source_id']:false;
		$bundle_id=isset($options['bundle_id'])?$options['bundle_id']:false;
		$assign_option=isset($options['assign_option'])?$options['assign_option']:false;
		$limit=isset($options['limit'])?$options['limit']:false;
		$quyen_chia_don = AdminOrders::$quyen_chia_don;
		$is_account_group_manager = is_account_group_manager();
		if(!$quyen_chia_don and !$is_account_group_manager){
			return false;
		}
		$auto = '';

		if($account_ids){
			$auto = ' nhanh';
			$users = $account_ids;
		}else{
			if($acc_group_id){
				$users = AdminOrdersDB::get_users('CS',$acc_group_id);
			}else{
				$users = AdminOrdersDB::get_users('CS');
			}
		}
		$group_id = AdminOrders::$group_id;
		$master_group_id = AdminOrders::$master_group_id;

		if(!$items){
			$cond = '(IFNULL(assigned_user_id,0)=0)';
			if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
				$cond.= ' and (groups.id='.$group_id.' or groups.master_group_id = '.$group_id.')';
			}elseif($master_group_id){
				$cond.= ' and (groups.id='.$group_id.' or (groups.master_group_id = '.$master_group_id.' and G1.id = '.$group_id.'))';
			}else{
				$cond.= ' and groups.id='.$group_id.'';
			}
			if($source_id){
				$cond.= ' and orders.source_id='.$source_id.'';
			}
			if($bundle_id){
				$cond.= ' and orders.bundle_id='.$bundle_id.'';
			}
			$cond.= ' and orders.status_id = '.THANH_CONG;
			require_once 'packages/core/includes/utils/paging.php';
			if($assign_option==1){
				$order_by = ' orders.id ASC';
			}else{
				$order_by = 'orders.id DESC';
			}
			$items = $this->get_assign_items($cond,$limit,$order_by);
		}
		$total = 0;
		$shuffled_array = array();
		$keys = array_keys($items);
		shuffle($keys); // trộn ngẫu nhiên data số
		foreach ($keys as $key)
		{
			$shuffled_array[$key] = $items[$key];
		}
		$items = $shuffled_array;
		if(sizeof($items)>0 and sizeof($users)>0){
			$average = floor(sizeof($items)/(sizeof($users)));
			$user_id = get_user_id();
			foreach($users as $k=>$v){
				$c=1;
				foreach($items as $key=>$val){
					if($c<=$average or $average==0){
						if(!$val['assigned_user_id'] or $val['assigned_user_id']==0){
							$arr=array('assigned_user_id'=>$v['user_id'],'assigned_time'=>time());
							if($row=DB::fetch('select id from order_rating where order_id='.$key)){
								DB::update('order_rating',$arr,'order_id='.$key);
							}else{
								DB::insert('order_rating',$arr+['order_id'=>$key,'rating_point'=>0,'rating_time'=>'0','update_rating_time'=>'0','rating_user_id'=>'0','cs_status_id'=>'0']);
							}
							AdminOrdersDB::update_revision($key,false,false,'Số được gán'.$auto.' cho tài khoản CS '.$v['id']);
							unset($items[$key]);
							$total++;
							$c++;
						}
					}
				}
			}
			$average = floor(sizeof($items)/(sizeof($users)));// reset lai gia tri trung binh
			foreach($users as $k=>$v){
				$c=1;
				foreach($items as $key=>$val){
					if($c<=$average or $average==0){
						if(!$val['user_assigned'] or $val['user_assigned']==0){
							$arr=array('user_assigned'=>$v['user_id'],'assigned'=>date('Y-m-d H:i:s'));
							if(!$val['first_user_assigned']){
								$arr['first_user_assigned'] = $user_id;
								$arr['first_assigned'] = date('Y-m-d H:i:s');
							}
							DB::update('orders',$arr,'id='.$key);
							AdminOrdersDB::update_revision($key,false,false,'Đơn hàng được gán'.$auto.' cho tài khoản '.$v['id']);
							AdminOrdersDB::update_assign_log($key,$v['user_id']);
							unset($items[$key]);
							$total++;
							$c++;
						}
					}
				}
			}
			return $total;
		}else{
			return false;
		}
	}
	function get_assign_items($cond,$limit=false,$order_by=false){
		$join = ' LEFT JOIN order_rating ON order_rating.order_id=orders.id';
		$sql = '
			SELECT
				orders.id,
				order_rating.assigned_user_id
			FROM
				orders
				JOIN `groups` ON groups.id = orders.group_id
				'.$join.'
			WHERE
				'.$cond.'
            '.($order_by?' ORDER BY '.DB::escape($order_by):'').'
			'.($limit?' LIMIT 0,'.DB::escape($limit):'').'
		';
		$items = DB::fetch_all($sql);
		return ($items);
	}
}
?>
