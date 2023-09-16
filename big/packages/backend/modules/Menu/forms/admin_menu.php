<?php
class MenuForm extends Form
{
    protected $map;
    function __construct(){
        Form::Form('MenuForm');
        $this->link_js('assets/standard/js/bootstrap.min.js');
        $this->link_css('assets/standard/css/bootstrap.min.css');
        //$this->link_js('vendor/twbs/bootstrap/dist/js/bootstrap.min.js');
        //$this->link_css('vendor/twbs/bootstrap/dist/css/bootstrap.min.css');
        $this->link_js('assets/standard/js/prettyPhoto/jquery.prettyPhoto.js');
        $this->link_js('assets/standard/js/jquery.isotope.min.js');
        $this->link_js('assets/standard/js/main.js');
        $this->link_js('assets/standard/js/wow.min.js');
        $this->link_css('assets/lib/sweetalert2/sweetalert2.min.css');
        $this->link_js('assets/lib/sweetalert2/sweetalert2.all.min.js');
        $this->link_js('packages/core/includes/js/common.js?v=240920211');
        $this->link_css('assets/standard/css/font-awesome.min.css');
        $this->link_css('assets/standard/css/animate.min.css');
        $this->link_css('assets/standard/js/prettyPhoto/css/prettyPhoto.css');
        $this->link_css('assets/admin/css/style.css?v=18072019');
        $this->link_css('assets/default/css/cms.css');
        $this->link_js('assets/lib/bootstrap-notify-master/bootstrap-notify.min.js');
        $this->link_js('assets/admin/scripts/bootstrap_datepicker/js/bootstrap-datepicker.min.js');
        $this->link_js('assets/vissale/js/lodash.min.js');
        require_once 'packages/vissale/modules/AdminProductsBuild/ProductHelper.php';
        if (URL::post('action') == 'update_notification') {
            $data = ['success' => false];
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                MenuDB::updateNotifications();
                $data = ['success' => true];

                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'get_notifications' && Url::get('page')) {
            $data = ['success' => false];
            try {
                $html = '';
                $notifications = MenuDB::getNotifications(Url::get('page'));
                if (!empty($notifications)) {
                    foreach ($notifications as $notification) {
                        $link = 'javascript:void(0)';
                        $class_active = $notification['is_read'] != 1 ? 'noti-active' : "";
                        if ($notification['notificationable_type'] == 1) {
                            $link = 'index062019.php?page=admin_orders&cmd=shipping_history&id=' . $notification['notificationable_id'];
                            $html .= '
                                <li>
                                    <a href="'. $link .'" class="'. $class_active .'">
                                      '. $notification['content'] .'
                                      <div><b>'. date('d-m-Y H:i:s', strtotime($notification['created_at'])) .'</b></div>
                                    </a>
                                </li>
                            ';
                        } else {
                            $html .= '
                                <li>
                                    <div class="'. $class_active .'">
                                        <div><b>'. $notification['title'] .'</b></div>
                                      '. $notification['content'] .'
                                      <div><b>'. date('d-m-Y H:i:s', strtotime($notification['created_at'])) .'</b></div>
                                    </div>
                                </li>
                            ';
                        }
                    }
                }

                $data = [
                    'success' => true,
                    'html' => $html
                ];
            } catch (Exception $e) {
                
            }

            echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
        }

        if (URL::get('action') == 'not_display_popup' && Url::post('notification_id')) {
            $data = ['success' => false];
            try {
                $user_id = get_user_id();
                $notification_id = Url::post('notification_id');
                $notification_recieved = DB::fetch("SELECT id, notification_id FROM notifications_recieved WHERE user_id = $user_id AND notification_id = $notification_id");
                if (!empty($notification_recieved)) {
                    DB::update_id("notifications_recieved", [
                        'is_read' => 1,
                        'read_at' => date('Y-m-d H:i:s')
                    ], $notification_recieved['id']);
                } else {
                    DB::insert('notifications_recieved', [
                        'notification_id' => $notification_id,
                        'user_id' => $user_id,
                        'is_read' => 1,
                        'read_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $data = ['success' => true];
            } catch (Exception $e) {
                
            }

            echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
        }
    }

    function draw(){
        $this->map = array();
        $user['rated_point'] = 0;
        $user['rated_quantity'] = 0;
        $user = Session::get('user_data');
        $this->map['rated_point'] = round($user['rated_point'],2);
        $this->map['rated_quantity'] = $user['rated_quantity'];
        $this->map['full_name'] = $user['full_name'];
        $this->map['account_id'] = Session::get('user_id');
        $this->map['avatar_url'] = (isset($user['avatar_url']) and $user['avatar_url'])?$user['avatar_url']:'assets/standard/images/no_avatar.webp';
        $group_id = Session::get('group_id');
        $group = DB::fetch('select id,expired_date,active from `groups` WHERE id = '.$group_id);
        if(!$group['active']){
            die('<div style="font-size:20px;color:#f00;text-align:center;padding:20px;">SHOP CỦA QUÝ KHÁCH ĐÃ DỪNG HOẠT ĐỘNG.<br>QUÝ KHÁCH VUI LÒNG LIÊN HỆ BAN QUẢN TRỊ ĐỂ MỞ LẠI. <br>HOTLINE: 03.9557.9557</div>');
        }
        $expired_date = $group['expired_date'];

        $expired = false;
        if(strtotime($expired_date)<=strtotime(date('Y-m-d')) and $expired_date!='0000-00-00 00:00:00' and $expired_date){
            $expired = true;
        }
        $this->map['expired_date'] = ($expired_date!='0000-00-00 00:00:00')?date('d/m/Y',strtotime($expired_date)):'Không có thời hạn';
        $this->map['expired'] = $expired;
        $prepairing_expired = false;
        if($expired==false and strtotime($expired_date)<strtotime(date('Y-m-d')) + 30*24*3600 and $expired_date!='0000-00-00 00:00:00' and $expired_date){
            $prepairing_expired = true;
        }
        
        $this->map['prepairing_expired'] = $prepairing_expired;
        require 'packages/core/includes/utils/category.php';
        $layout = 'admin_menu';
        $this->map['user_id'] = get_user_id();
        
        $this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
        $master_group_id = get_master_group_id();
        if($master_group_id and is_master_group()){
            $groups = get_groups($master_group_id);
        }else{
            $groups = array();
        }
        $group = MenuDB::get_group_info();
        
        $this->map['group_name'] = $group['name'];
        $this->map['group_address'] = $group['address'];
        $this->map['system_group_id'] = $group['system_group_id'];
        $this->map['notify'] = false;
        
        if(time() < strtotime('2018-12-19 23:00:00')){
            echo 'hello';
            $this->map['notify'] = true;
        }
        $this->map += array(
                'is_master_group'=>is_master_group(),
                'group_id_list'=>array(''=>'Xem theo tài khoản') + MiString::get_list($groups));
        //$password = DB::fetch('select password from account where id="'.Session::get('user_id').'"','password');
        $jwt = $this->get_jwt();
        $this->map['likeworking_login_url'] = $jwt;
        $this->map['target_height'] = 100;
        $this->map['target_percent'] = 50;
        $this->map['current_target_amount'] = 0;
        $this->map['phone_store_name'] = '';
        if($phone_store_id = Session::get('phone_store_id')){
            $this->map['phone_store_name'] = DB::fetch('select id,name from phone_store where id='.$phone_store_id,'name');
        }
        //DB::check_query('171.241.178.178');
        $notifications = MenuDB::getNotifications();
        $total_notification = MenuDB::getTotalNotifications();

        $this->map['notifications'] = $notifications;
        $this->map['total_notification'] = $total_notification;

        $schedules = get_schedules();
        $this->map['schedules'] = $schedules;
        $this->map['total_schedule'] = sizeof($schedules);

        $notification_popup = MenuDB::getNotificationPopup();
        //DB::check_query('171.241.178.178');
        $this->map['notification_popup'] = $notification_popup;
        ///
        $this->map['tuha_administrator'] = can_tuha_administrator();
        $this->map['tuha_content_admin'] = can_tuha_content_admin();
        ///
        
        $this->map['can_access_master_product_page'] = ProductHelper::hasPrivilegeOnMasterProduct();

        $this->map['product_total'] = DB::fetch('select count(id) as total from products where group_id = '.$group_id.' and IFNULL(products.del,0)=0','total');

        // $this->map['user_total'] = DB::fetch('select count(users.id) as total from users join account on account.id=users.username where users.group_id = '.$group_id.' and IFNULL(account.is_active,0)<>0','total');
        $cond = 'account.id<>"admin"';
        $cond .= !User::is_admin() ? ' AND account.group_id='.Session::get('group_id') : '';
        $cond .= ' AND account.is_active=1';
        $this->map['user_total'] = DB::fetch('select 
                count(`account`.id) as total
            from
                `account`
                join `party` on `party`.user_id=`account`.id
                join `users` on `users`.username=`account`.id
                left join `groups` on `groups`.id=`account`.`group_id`
            where
                '.$cond.'
            ','total');
        $assigned_orders = [];
        if(Menu::$quyen_sale){
            $assigned_orders = MenuDB::get_un_assign_orders();
        }
        $this->map['assigned_orders'] = $assigned_orders;
        $this->parse_layout($layout,$this->map);
    }
    function get_jwt(){
        $sql = '
            select 
                account.id, account.password,
                users.id as user_id,
                party.phone,
                account.type,
                account.group_id,
                account.admin_group,
                account.create_date,
                account.last_online_time,
                account.is_active,
                party.kind,
                party.email,
                party.full_name,
                groups.name as group_name,
                groups.prefix_account,
                party.birth_date,
                party.identity_card,
                party.gender,
                party.address,
                party.zone_id
            from 
                account 
                inner join party on party.user_id=account.id 
                inner join users on users.username = account.id
                inner join `groups` on groups.id=account.group_id
            where
                    account.id = "'.Session::get('user_id').'"
            limit 0,1
        ';
        $row = DB::fetch($sql);
        require_once ('work-auth/JWT/JWT.php');
        $jwt = JWT::encode(
            json_encode($row),
            TUHA_TOKEN,
            'HS512'
        );
        return $jwt;
    }
}
?>