<?php
class AdminUserInfoDB{
    static function sys_work_tuha($group_id,$group_name){
        $gid = md5($group_id.CATBE);
        $url = 'https://work.tuha.vn/work-auth/group.php/?cmd=update_group_info&gid='.$gid.'&name='.$group_name;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
    }
    static function getLinkApi(){
        $group_id = Session::get('group_id');
        $row = DB::select('api_keys','group_id='.$group_id.'');
        $links = '';
        if ($row) {
            $links = 'https://big-api.shopal.vn/api/order?api_key='.$row['api_key'].'&bundle_id=ID_LOAI_SAN_PHAM&user_created=ID_NHANVIEN&user_assigned=ID_NGUOINHAN&source_id=ID_NGUON_DONHANG';
        }
        return $links;
    }
    static function get_systems(){
        return $groups = DB::fetch_all('select id,name,structure_id from groups_system where 1=1 order by structure_id');
    }
    static function get_api(){
        $group_id = Session::get('group_id');
        if($row=DB::select('api_keys','group_id='.$group_id.'')){
            return [
                'api_name'=>$row['name'],
                'api_is_active'=>$row['is_active'],
                'api_key'=>$row['api_key']
            ];
        }else{
            return [];
        }
    }
    static function update_api(){
        $group_id = Session::get('group_id');
        if($row=DB::select('api_keys','group_id='.$group_id.'')){
            $arr = [
                'time_to_count'=>0,
                'request_count'=>0,
                'api_key' => DB::escape(Url::get('api_key')),
                'name'=> DB::escape(Url::get('api_name')),
                'is_active'=>Url::get('api_is_active')?'1':'0'
            ];
            DB::update('api_keys',$arr,'id='.$row['id']);
            //echo json_encode(['RESULT'=>2]);
        }else{
            $arr = [
                'group_id'=>$group_id,
                'time_to_count'=>0,
                'request_count'=>0,
                'api_key' => DB::escape(Url::get('api_key')),
                'name'=>DB::escape(Url::get('api_name')),
                'is_active'=>Url::get('api_is_active')?'1':'0'
            ];
            DB::insert('api_keys',$arr);
        }
        $api = Url::get('api_key');
        $links = 'https://big-api.shopal.vn/api/order?api_key='.$api.'&bundle_id=ID_LOAI_SAN_PHAM&user_created=ID_NHANVIEN&user_assigned=ID_NGUOINHAN&source_id=ID_NGUON_DONHANG';
        return $links;
    }
    static function generate_api_key(){
        $group_id = Session::get('group_id');
        return User::encode_password($group_id.time());
    }
    static function get_phone_stores(){
        $group_id = Session::get('master_group_id')?Session::get('master_group_id'):Session::get('group_id');
        $sql = '
			select
				phone_store.*
			from
			 	phone_store
			where
				phone_store.group_id = '.$group_id.'
			order by
				phone_store.name
		';
        return DB::fetch_all($sql);
    }
    static function get_groups_systems(){
        $sql = '
			select
				id,
				structure_id
				,name
			from
			 	`groups_system`
			where
				1=1
			order by
				structure_id
		';
        return DB::fetch_all($sql);
    }
    static function get_group_info($group_id){
        $group = [];
        $group['total_order'] = DB::fetch('select count(orders.id) as total from orders join orders_extra on orders_extra.order_id=orders.id where orders.group_id='.$group_id.' and (orders_extra.update_successed_time or orders.status_id='.THANH_CONG.')','total');
        $group['total_product'] = DB::fetch('select count(p.id) as total from products as p where p.group_id='.$group_id.' and IFNULL(p.del,0)=0','total');
        $group['total_user'] = DB::fetch('select count(u.id) as total from users as u join account as a on a.id=u.username where u.group_id='.$group_id.' and a.is_active=1','total');
        return $group;
    }
    static function process_group_data($group_id,$min_date){
        $min_time = strtotime($min_date);
        //process orders: orders, order_revisions
        //process users: account, party, users, account_log
        $log_str = '- Ngày tạo shop chuyển về '.$min_date.'<br>';
        $log_str .= '- Chuyển đơn hàng thành công và đã thu tiền thành đơn Khai thác lại. <br>
                    - Ngày tạo, ngày xác nhận về ngày '.$min_date.' nếu thời gian tạo nhỏ hơn '.$min_date.'<br>';;
        $log_str .= '- Tổng tiền đơn hàng về 0<br>';
        $log_str .= '- Ngày tạo tài khoản về ngày '.$min_date.' nếu thời gian tạo nhỏ hơn '.$min_date.'<br>';
        $log_str .= '- Thời gian log tài khoản về ngày '.$min_date.' nếu thời gian tạo nhỏ hơn '.$min_date.'<br>';
        DB::update('groups',['created'=>$min_date],'groups.id='.$group_id.'');
        DB::update('orders',[
            'created'=>$min_date,
            'confirmed'=>$min_date,
            'delivered'=>$min_date,
            'confirmed'=>$min_date,
            'status_id'=>KHAI_THAC_LAI,
            'total_price'=>'0',
            'price'=>'0',
            'discount_price'=>'0',
            'shipping_price'=>'0',
            'other_price'=>'0'
        ],'orders.group_id='.$group_id.' and created <="'.$min_date.'" and orders.status_id = '.THANH_CONG.' and created<"'.$min_date.'"');

        DB::update('orders',[
            'created'=>$min_date,
            'confirmed'=>$min_date,
            'delivered'=>$min_date,
            'confirmed'=>$min_date,
            'status_id'=>KHAI_THAC_LAI,
            'total_price'=>'0',
            'price'=>'0',
            'discount_price'=>'0',
            'shipping_price'=>'0',
            'other_price'=>'0'
        ],'orders.group_id='.$group_id.' and created <="'.$min_date.'" and orders.status_id = '.DA_THU_TIEN.' and created<"'.$min_date.'"');
        DB::update('account',['create_date'=>$min_date],'account.group_id='.$group_id.' and created<"'.$min_date.'"');
        DB::update('users',['created'=>$min_date],'users.group_id='.$group_id.' and created<"'.$min_date.'"');
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $groupName = DB::fetch('SELECT id,name FROM groups WHERE id=' . $group_id);
            if(@require_once(ROOT_PATH.'packages/vissale/lib/php/log.php')) {
                $dataLog = array(
                    'condition' => array(
                        'group_id' => $group_id,
                        'group_name' => $groupName['name']
                    ),
                    'fillable' => array(
                        'time' => $min_time
                    )
                );
                updateAccountLog($dataLog);
            }
        } else {
            DB::query('
                UPDATE account_log
                SET account_log.time = ' . $min_time . '
                WHERE account_log.group_id = ' . $group_id . '
            ');
        }
        DB::query('
            UPDATE log
            JOIN account on account.id = log.user_id
            SET log.time = '.$min_time.'
            WHERE account.group_id = '.$group_id.' and log.time < '.$min_time.'
        ');
        System::log('ERASE_DATA','Giải phóng dữ liệu shop',$log_str);
        echo '<div style="padding:20px;margin: auto;width: 80%;">
                <h3>Kết quả:</h3>
              ';
        echo $log_str;
        echo  '<hr>';
        echo '<div style="text-align: center;"><a href="'.Url::build_current(['do']).'" style="border:1px solid #ff3600;border-radius:5px;padding:5px 10px;text-decoration: none;">Quay lại màn hình chính.</a></div>';
        echo '</div>';
        exit();
    }
}
?>
