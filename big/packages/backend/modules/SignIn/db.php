<?php
require_once 'packages/core/includes/common/ImageType.php';

class SignInDB{
    static function get_group_info($group_id)
    {
        return DB::fetch('
            SELECT
                id,name,code,address,phone,system_group_id
            FROM
                `groups`
            WHERE
                id='.$group_id.'
        ');
    }
    static function createOrder($user_created,$data){

    }
    static function web_register($row){
        $account_id = $row['account_id'];
        //group
        $group_arr = array(
            'system_group_id'=>8, // khach ngoai
            'code' => $account_id,
            'email'=>$row['email'],
            'phone'=>$row['phone'],
            'name' => $row['group_name'],
            'account_type'=>1,// dung thu
            'status'=>1,//shop dang ky từ web
            'active'=>1,'created'=>date('Y-m-d H:i:s'),
            'expired_date'=>date('Y-m-d',(time() + 7*24*3600)),// 7 ngày
            'master_group_id'=>0,
            'page_counter'=>5,
            'user_counter'=>5
        );
        ///
        if(DB::exists('select id from account where id="'.$account_id.'"')) {
            Url::js_redirect(true,'⚠️ Tài khoản '.$account_id.' đã tồn tại, Quý Khách vui lòng chọn tài khoản khác...!',['do','user_id','full_name','email','shop_name','phone']);
        }
        if(DB::exists('select id,email from party where email="'.$row['email'].'"')) {
            Url::js_redirect(true,'⚠️ Email '.$row['email'].' đã tồn tại, Quý Khách vui lòng chọn email khác...!',['do','user_id','full_name','email','shop_name','phone']);
        }
        if(DB::exists('select id,phone from users where phone="'.$row['phone'].'"')) {
            Url::js_redirect(true,'⚠️ Số điện thoại '.$row['phone'].' đã tồn tại, Quý Khách vui lòng chọn số điện thoại khác...!',['do','user_id','full_name','email','shop_name','phone']);
        }
        ///
        if($group_id = DB::insert('groups', $group_arr)){
            /// // party
            $party_new_row =
                array(
                    'email'=>$row['email'],
                    'phone'=>$row['phone'],
                    'type'=>'USER',
                    'status'=>'SHOW',
                    'zone_id'=>0,
                    'full_name'=>$row['full_name']
                );
            DB::insert('party', $party_new_row + array('time'=>time(),'user_id' => $account_id));
            //// account
            $account_new_row = array(
                'id' => $account_id,
                'account_group_id'=>0,
                'is_active'=>1,
                'type'=>'USER',
                'cache_privilege'=>'',
                'password'=>User::encode_password($row['password']),
                'group_id'=>$group_id,
                'admin_group'=>1,
                'last_online_time'=>0,'cache_setting'=>'',
                'is_block'=>0,
                'create_date'=>date('Y-m-d')
            );
            DB::insert('account', $account_new_row);
            /// //// users
            $user_arr = array(
                'group_id'=>$group_id,
                'username'=>$account_id,
                'name'=>$row['name'],
                'phone'=>$row['phone'],
                'address'=>'',
                'status'=>1,
                'created'=>date('Y-m-d H:i:s')
            );
            DB::insert('users',$user_arr);
            SignInDB::update_privilege($account_id,$group_id);

            // Tạo quyền mặc định
            SignInDB::create_default_roles($group_id,'GANDON','SALE');
            SignInDB::create_default_roles($group_id,'MARKETING','MARKETING');
            //////////////////////////////////////////////////////////////////////////////
            $messenger=file_get_contents('packages/backend/modules/SignIn/layouts/notification_email.html');
            $message=str_replace('[[|username|]]',$account_id,$messenger);
            $message=str_replace('[[|mobile|]]',$row['phone'],$message);
            $message=str_replace('[[|shop_id|]]',$group_id,$message);
            $message=str_replace('[[|shop_name|]]',$row['group_name'],$message);
            $subject='Đăng ký tài khoản mới';
            {
                $email = 'huyen.nt125@gmail.com';//
                $cc = 'ceo@palvietnam.vn';
                System::send_mail('noreply.tuha@gmail.com',($email),$subject,$message,$cc);
            }
            //////////////////////////////////////////////////////////////////////////////
        }
    }
    static function create_default_roles($group_id,$code,$name){
        $role_id = DB::insert('roles',['name'=>$name,'created'=>date('Y-m-d H:i:s'),'group_id'=>$group_id]);
        if (!DB::exists('select id from roles_to_privilege where role_id=' . $role_id . ' AND privilege_code="' . $code . '"')) {
            DB::insert('roles_to_privilege', array('role_id' => $role_id, 'privilege_code' => $code));
            // trang thai
            $status_arr = [
                10, // CHUA XAC NHAN
                4, // KHONG NGHE DIEN
                7, // XAC NHAN
                96, // GOI MAY BAN
            ];
            foreach ($status_arr as $k=>$v){
                $status_arr = array(
                    'role_id' => $role_id,
                    'status_id' => $v,
                    'created' => date('Y-m-d H:i:s')

                );
                DB::insert('roles_statuses',$status_arr);
            }
        }
    }
    static function update_privilege($account_id,$group_id){
        ///////update quyen///////
        if(!DB::exists('SELECT id FROM account_privilege WHERE account_id="'.$account_id.'" and privilege_id=18 and group_id='.$group_id)){
            DB::update('account',array('cache_privilege'=>''),'id="'.$account_id.'"');
            DB::insert('account_privilege',array(
                'account_id'=>$account_id,
                'privilege_id'=>18,
                'portal_id'=>PORTAL_ID,
                'category_id'=>1,
                'group_id'=>$group_id
            ));
        }
        /////
    }
    static function register($user_id,$email){
        if (!DB::exists('select * from `party` where `user_id`="'.$user_id.'" '.($email?' or `email`="'.$email.'"':'').'') and !DB::exists('select * from `account` where `id`="'.$user_id.'"')){
            echo '<div class="alert alert-warning">Tài khoản này chưa tồn tại, vui lòng nhấn đăng ký ở dưới <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></div>.';
        }
        else{
            SignInDB::login($user_id,$email);
        }
    }
    static function login($user_id,$email){
        $user = $user_id;
//        if(strpos($user,'0',0) === 0){
//            $user = (84).substr($user,-(strlen($user) - 1),strlen($user) - 1);
//        }
        $is_login = false;
        if($row=DB::fetch('select account.id,account.password,account.admin_group,account.group_id,account.type,account.create_date,account.last_online_time,account.is_active,party.kind,party.email,full_name from account inner join party on party.user_id=account.id where (account.id = "'.$user.'"'.($email?' OR party.email = "'.$email.'"':'').') and account.type="USER"')){
            $is_login = true;
        }
        if($is_login){
            $today=getdate();
            $check_date = strtotime($today['year'].'/'.$today['mon'].'/'.$today['mday']);
            Session::set('user_id',$row['id']);
            Session::set('group_id',$row['group_id']);
            if($row['admin_group']){
                Session::set('admin_group',$row['admin_group']);
            }
            Session::set('user_data',$row);
            $time = time();
            DB::update('account',['last_online_time'=>$time],'id="'.Session::get('user_id').'"');
            Session::set('last_online_time', $time);

            if(isset($_SERVER['HTTP_REFERER']) and !preg_match('/dang-nhap/',$_SERVER['HTTP_REFERER'])){
                echo '<script>window.location="'.$_SERVER['HTTP_REFERER'].'";</script>';//trang-ca-nhan.html
            }else {
                if (Url::iget('doing_review') and $film = DB::select('hay_film', 'id=' . Url::iget('doing_review'))) {
                    echo '<script>window.location="phim/' . $film['name_id'] . '-id' . $film['id'] . '.html#danhgiaphim";</script>';//trang-ca-nhan.html
                } elseif (Url::iget('film_id') and $film = DB::select('hay_film', 'id=' . Url::iget('film_id'))) {
                    echo '<script>window.location="phim/' . $film['name_id'] . '-id' . $film['id'] . '.html";</script>';//trang-ca-nhan.html
                } else {
                    echo '<script>window.location="/";</script>';//trang-ca-nhan.html
                }
            }
        }
    }

    /**
     * Determines whether the specified username is username exists.
     *
     * @param      string  $username  The username
     *
     * @return     bool    True if the specified username is username exists, False otherwise.
     */
    public static function isUsernameExists(string $username){
        $fmt = 'SELECT account.id 
                FROM account 
                INNER JOIN party ON party.user_id=account.id 
                WHERE account.id = "%s"';
        $sql = sprintf($fmt, DB::escape($username));

        return DB::exists($sql);
    }

    /**
     * Gets the user.
     *
     * @param      string  $username  The username
     * @param      string  $password  The password
     *
     * @return     <type>  The user.
     */
    public static function getUser(string $username, string $password){
        $fmt = '
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
                `users`.`id_card_issued_date`,
                groups.integrate_callio,
				groups.integrate_voip24h,
				users.integrate_callio as user_integrate_callio,
				users.integrate_voip24h as user_integrate_voip24h,
				users.id as users_user_id
            FROM  `account` 
                JOIN users ON users.username = `account`.id
                JOIN party ON party.user_id=`account`.id 
                JOIN  `groups` ON groups.id=`account`.group_id
            WHERE
                account.id = "%s"
                AND `account`.type="USER" 
                AND `account`.`password`="%s"';
        $sql = sprintf($fmt, DB::escape($username), User::encode_password($password));
        return DB::fetch($sql);
    }

    /**
     * Gets the party by username.
     *
     * @param      string  $username  The username
     *
     * @return     array  The party by username.
     */
    public static function getPartyByUsername(string $username){
        $fmt = 'SELECT * FROM `party` WHERE `user_id`="%s" and type="USER"';
        $sql = sprintf($fmt, DB::escape($username));

        return DB::fetch($sql);
    }

    /**
     * Determines whether the specified user id is missing user images of user id.
     *
     * @param      int   $userID  The user id
     *
     * @return     bool  True if the specified user id is missing user images of user id, False otherwise.
     */
    public static function isMissingUserImagesOfUserID(int $userID){
        $types = sprintf('%d,%d,%d',
                         ImageType::HO_SO_XIN_VIEC,
                         ImageType::SO_HO_KHAU,
                         ImageType::HOP_DONG_HOP_TAC);
        $sql = sprintf('SELECT count(*) as count
                       FROM  `user_images` 
                       WHERE user_id="%d" 
                       AND type IN (%s)
                       GROUP BY `type`', $userID, $types);

        $res = DB::fetch_all_array($sql);

        return count($res) != 3;
    }

    /**
     * Determines whether the specified username is admin.
     *
     * @param      string  $username  The username
     *
     * @return     bool    True if the specified username is admin, False otherwise.
     */
    public static function isAdmin(string $username){
        $fmt = 'SELECT count(id) AS total 
                FROM account_privilege 
                WHERE account_id="%s" 
                AND privilege_id IN (34,35)';
        $sql = sprintf($fmt, $username);
        $account_privilege = DB::fetch($sql);

        return isset($account_privilege['total']) && $account_privilege['total'] > 0;
    }

    /**
     * Determines whether the specified username is group owner.
     *
     * @param      string  $username  The username
     *
     * @return     bool    True if the specified username is group owner, False otherwise.
     */
    public static function isGroupOwner(string $username){
        $fmt = 'select count(id) as total from `groups` where code="%s"';
        $sql = sprintf($fmt, DB::escape($username));
        $res = DB::fetch($sql);

        return $res['total'] > 0;
    }

    /**
     * Gets the owner shop by member username.
     *
     * @param      string  $username  The username
     *
     * @return     <type>  The owner shop by member username.
     */
    public static function getOwnerShopByMemberUsername(string $username){
        $fmt = 'SELECT party.phone, party.full_name, party.user_id 
                FROM `groups` 
                JOIN account ON account.group_id = groups.id 
                JOIN users ON users.username = groups.code 
                JOIN party ON party.user_id = users.username 
                WHERE account.id = "%s"';
        $sql = sprintf($fmt, DB::escape($username));

        return DB::fetch($sql);
    }

}
?>
