<?php

class SignInOtherAccountForm extends Form{
    protected $map;

	function __construct(){
        Form::Form('SignInOtherAccountForm');
        $user = Session::get('user_id');
        if(!$this->canDebugOtherAccount($user)){
            URL::redirect_current();
        }

    }

    private function canDebugOtherAccount($username)
    {
        return in_array($username, SYSTEM_DEBUG_ACCOUNTS);
    }

    function on_submit(){
        $username = $_GET['username'];
        $this->sign_in($username);
    }

    function sign_in($username){ 
        $user = DB::escape(trim($username)); 
        $is_login = false;
        if($user and !DB::exists('
            select 
                account.id
            from 
                account
                inner join party on party.user_id=account.id 
            where 
                (account.id = "'.$user.'") 
        ')){
            echo '<script>alert("Tài khoản không tồn tại trong hệ thống!");</script>';
			Url::redirect_current(['cmd'=>'debug']);
        }else if(DB::exists('select id from account_privilege where  (account_id = "'.$user.'")  and (privilege_id = 34 or privilege_id  = 35)')){
            echo '<script>alert("Bạn không có quyền đăng nhập vào tài khoản này!");</script>';
			Url::redirect_current(['cmd'=>'debug']);
        }else{
            $row=DB::fetch('
            SELECT 
                account.id,account.session_id,account.last_ip,
                account.password,account.type,account.group_id,account.admin_group,
                account.create_date,account.last_online_time,
                account.is_active,party.kind,
                account.group_id,
                party.email,full_name,
                party.image_url as avatar_url,
                groups.account_type,
                groups.master_group_id,
                groups.phone_store_id,
                `users`.`status` as `user_status`,
                `users`.`rated_point`,
                `users`.`rated_quantity`,
                `users`.`id` as `user_id`,
                `users`.`username`,
                `users`.`extension`,
                `users`.`identity_card`,
                `users`.`identity_card_front`,
                `users`.`identity_card_back`,
                `users`.`phone`,
                `users`.`temp_zone_id`,
                `users`.`temp_address`,
                `users`.`address`,
                `users`.`fb_link`,
                `users`.`id_card_issued_by`,
                `users`.`id_card_issued_date`
            FROM  `account` 
                JOIN users ON users.username = `account`.id
                JOIN party ON party.user_id=`account`.id 
                JOIN  `groups` ON groups.id=`account`.group_id
            WHERE
                account.id = "'.$user.'"
                AND `account`.type="USER" 
			');

            if(!$row['is_active']){
                $this->error('username','Tài khoản chưa được kích hoạt.',false);
            }else{
                if(Session::is_set('debuger_id') && $user === Session::get('debuger_id')){
                    Session::delete('debuger_id');
                }else{
                    $debuger_id = Session::get('user_id');
                    Session::set('debuger_id',$debuger_id);
                }

                Session::set('user_id', $row['id']);
                if ($row['phone_store_id']) {
                    Session::set('phone_store_id', $row['phone_store_id']);
                }

                Session::set('admin_group',$row['admin_group']);
                Session::set('account_type',$row['account_type']);
                Session::set('master_group_id',$row['master_group_id']);
                Session::set('group_id',$row['group_id']);
                Session::set('user_data',$row);
                $TONG_CONG_TY = 3;
                require_once 'packages/vissale/lib/php/vissale.php';
                if (Session::get('admin_group')) {
                    if ($row['user_status'] <= 1) {
                        echo '<script>window.location="/' . Url::build('trang-gioi-thieu') . '";</script>';
                        exit();
                    } else {
                        if ($row['account_type'] == $TONG_CONG_TY) {
                            echo '<script>window.location="/' . Url::build('report') . '";</script>';
                            exit();
                        } else {
                            echo '<script>window.location="/' . Url::build('admin_orders') . '";</script>';
                            exit();
                        }
                    }
                } else {
                    echo '<script>window.location="/' . Url::build('admin_orders') . '";</script>';
                    exit();
                }
                exit();
            }
        }
    }
}