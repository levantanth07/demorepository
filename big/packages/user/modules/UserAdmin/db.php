<?php
class UserAdminDB{
    function check_dupplicate_identity_card($card_number,$group_id=false){
        $card_number = DB::escape($card_number);
        $group_id = DB::escape($group_id);
        if(!$group_id){
            $group_id=UserAdmin::$group_id;
        }
        if(DB::exists('select id from users where group_id='.$group_id.' and identity_card="'.$card_number.'"')){
            return true;
        }else{
            return false;
        }
    }
    static function get_account_log($group_id){
        $group_id = DB::escape($group_id);
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $conditions = array(
                'group_id' => $group_id,
                'log_type' => 0,
                'module_id' => MODULE_USERADMIN
            );
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'sorts' => array(
                        '_id' => 'DESC'
                    ),
                    'limit' => 100,
                    'page' => 1
                )
            );
            $items = getAccountLog($payload);
            return $items;
        } else {
            $sql = '
                SELECT
                  account_log.id,
                  account_log.time,
                  account_log.ip,
                  account_log.content
                FROM
                  account_log
                WHERE 
                  account_log.group_id = '.$group_id.'
                  AND account_log.log_type = 0
                  AND module_id = '.MODULE_USERADMIN.'
                ORDER BY
                  account_log.id DESC
                limit 0,100
            ';
            return DB::fetch_all($sql);
        }
    }
    static function update_log($old_vals,$new_vals){
        unset($old_vals['cache_privilege']);
        unset($new_vals['cache_privilege']);
        unset($old_vals['cache_setting']);
        unset($new_vals['cache_setting']);
        unset($old_vals['status']);
        unset($new_vals['status']);
        unset($new_vals['name']);

        $data = '';
        $label_arr = [
            'id ' => 'Tên tài khoản',
            'password' => 'Mật khẩu',
            'is_active' => 'Kích hoạt',
            'is_block' => 'Khóa tài khoản',
            'group_id' => 'group_id',
            'admin_group' => 'Quyền quản lý shop',
            'phone'=>'Điện thoại',
            'gender'=>'Giới tính',
            'email'=>'Email',
            'birth_date'=>'Ngày sinh',
            'full_name'=>'Họ và tên',
            'account_group_id'=>'Nhóm tài khoản',
            'full_name'=>'Họ và tên',
            'status'=>'Trạng thái tài khoản',
            'address'=>'Địa chỉ'
        ];
        foreach($new_vals as $key=>$value){
            if(!is_array($value) and isset($old_vals[$key]) and $old_vals[$key] !== $value){
                $old_value = ($key=='password')?'***':$old_vals[$key];
                $new_value = ($key=='password')?'***':$value;
                $data .= '<div><strong>'.$label_arr[$key]. '</strong> từ "<span style="color:#1966ff">' .($old_value). '</span>" => "<span style="color:#ff641c">' .$new_value.'</span>"</div>';
            }
        }
        if($data){
            $data = 'Chỉnh sửa tài khoản "<strong>'.$old_vals['id'].'</strong>":<br>'.$data;
            System::account_log(0,$data,MODULE_USERADMIN);
        }
    }
    static function get_total_expired_shop($cond){
        $sql = '
            SELECT
              count(groups.id) as total
            FROM
              groups
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and groups.expired_date < "'.date('Y-m-d').'"
        ';
        return DB::fetch($sql,'total');
    }
    static function get_total_actived_shop($cond){
        $sql = '
            SELECT
              count(groups.id) as total
            FROM
              groups
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and groups.expired_date > "'.date('Y-m-d').'"
        ';
        return DB::fetch($sql,'total');
    }
    static function get_total_good_shop($cond){
        $sql = '
            SELECT
              count(groups.id) as total
            FROM
              groups
              left join groups_system on groups_system.id=groups.system_group_id
            WHERE 
              '.$cond.' and groups.expired_date > "'.date('Y-m-d').'"
        ';
        return DB::fetch($sql,'total');
    }
    static function check_user_counter($group_id){
        $group_id = DB::escape($group_id);
        $user_counter = DB::fetch('select user_counter from `groups` where id='.$group_id.'','user_counter');
        $sql = '
          select 
            count(users.id) as total 
          from 
            users
            inner join account on account.id = users.username
          where 
            users.group_id='.$group_id.' and account.is_active';
        $total_user = DB::fetch($sql,'total');
        if($total_user >= $user_counter){
            return $user_counter;
        }else{
            return false;
        }
    }
    static function check_page_counter($group_id){
        $group_id = DB::escape($group_id);
        $page_counter = DB::fetch('select page_counter from `groups` where id='.$group_id.'','page_counter');
        $total_page = DB::fetch('select count(id) as total from fb_pages where group_id='.$group_id.'','total');
        if($total_page < $page_counter){
            return $page_counter;
        }else{
            return false;
        }
    }
    static function get_admins_of_shop($group_id){
        $group_id = DB::escape($group_id);
        $sql = '
            SELECT
              users.id,
              users.name
            FROM
              users
              INNER JOIN account ON account.id = users.username
            WHERE
              users.group_id = '.$group_id.' and account.admin_group=1
            ORDER BY
              users.name
        ';
        $items = DB::fetch_all($sql);
        $str = '';
        foreach($items as $val){
            $str .= ($str?', ':'').$val['name'];
        }
        return $str;
    }
    static function get_systems(){
        return $groups = DB::fetch_all('select id,name,structure_id from groups_system where 1=1 order by structure_id');
    }
    static function get_account_groups($group_id){
        $group_id = DB::escape($group_id);
        return $groups = DB::fetch_all('select id,name from account_group where group_id='.$group_id.' order by name');
    }
    static function get_account_groups_of_user($user_id){
        $user_id = DB::escape($user_id);

        $items = DB::fetch_all('select account_group.id,account_group.name from account_group join account_group_admin on account_group_admin.account_group_id=account_group.id where account_group_admin.user_id='.$user_id.' order by account_group.name');
        return $items;
    }
    static function init_fb_cron_config($group_id){
        $group_id = DB::escape($group_id);
        if(!DB::exists('select group_id from fb_cron_config where group_id = '.$group_id)){
            $items = DB::fetch_all('select fb_cron_config._key as id,fb_cron_config.* from fb_cron_config WHERE group_id=1');
            foreach($items as $key=>$val){
                $arr = array(
                    'group_id'=>$group_id,
                    '_key'=>$val['_key'],
                    'type'=>$val['type'],
                    'description'=>$val['description'],
                    'value'=>$val['value'],
                    'level'=>$val['level'],
                    'created'=>date('Y-m-d H:i:s'),
                    'updated'=>date('Y-m-d H:i:s'),
                    'parent_id'=>$val['parent_id']?$val['parent_id']:0,
                );
                DB::insert('fb_cron_config',$arr);
            }
        }
    }
    static function get_total_group(){
        return DB::fetch('select count(*) as total from `groups`','total');
    }
    static function get_roles_of_the_user($account_id,$group_id,$to_array = false){
        $account_id = DB::escape($account_id);
        $group_id = DB::escape($group_id);

        if($user_id = DB::fetch('select id from users where username="'.$account_id.'" and group_id='.$group_id,'id')){
            $sql = '
            select
                users_roles.id
                ,users_roles.user_id
                ,users_roles.role_id
                ,roles.name
            from
                users_roles
                INNER JOIN roles ON users_roles.role_id = roles.id
            WHERE
                users_roles.user_id='.$user_id.'
            GROUP BY
                users_roles.id
            order by
                users_roles.id  DESC';
            $items = DB::fetch_all($sql);
            if($to_array){
                return $items;
            }else{
                $str = '';
                foreach ($items as $key => $val) {
                    $str.= '<span class="label label-default">'.$val['name'].'</span>, ';
                }
                return rtrim($str,', ');
            }
        }
        else{
            if($to_array){
                return [];
            }else{
                return '';
            }
        }

    }
    static function get_roles($group_id){
        $sql = '
            SELECT
              roles.id,roles.name,roles.group_id
            FROM
              roles
            WHERE
              roles.group_id = '.$group_id.'
            ORDER BY
              roles.name
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }

    static function save_item_image($file, $variant = false){
        if((isset($_FILES[$file]) and $_FILES[$file]) || $variant){
            require_once 'packages/core/includes/utils/ftp.php';
            $image_url = FTP::upload_file($file,'upload/ho_so_nhan_vien',true,'content', 'IMAGE', $variant);
            return $image_url;
        }
    }

    /**
     * Insert images
     *
     * @param      array  $objects  The objects
     * @param      int    $user_id  The user identifier
     * @param      int    $type     The type
     */
    public static function insert_user_images(array $objects, int $user_id, int $type){
        // Lấy tất cả user_image của người dùng theo user_id và loại, và chuyển nó về dạng [ID => image_url, ...]
        $image_urls = array_map(function($row){
            return $row['image_url'];
        }, self::get_user_images_by_type($user_id, $type, ['id', 'image_url']));

        // Duyệt qua danh sách link gửi lên
        $inserts = [];
        foreach ($objects as $object) {
            // nếu link trong danh sách gửi lên tồn tại trong DB thì xóa nó khỏi danh sách 
            // link từ DB và bỏ qua nó.
            if(($id = array_search($object->url, $image_urls)) !== false){
                unset($image_urls[$id]);
                continue;
            }

            // Thực hiện di chuyển file sang thư mục lưu trữ và tạo 1 dòng thông tin cho việc insert 
            $storage_file = UserAdmin::move_uploaded_temp_file($object->url);
            $inserts[] = sprintf('(%d, "%d", "%s", %d, %d)', $user_id, $type, DB::escape($storage_file), $user_id, $user_id );
        }

        // Duyệt qua danh sách user_image và thực hiện xóa file không còn được sử dụng trên server lưu trữ
        // Cập nhật lại danh sách ID không được sử dụng trong DB để xóa nó sau đó
        $user_images_id_unuse = [];
        foreach ($image_urls as $id => $image_url) {
            $user_images_id_unuse[] = $id;
            UserAdmin::delete_storage_file($image_url);
        }

        // Delete các file không còn được dùng trong DB
        if(count($user_images_id_unuse)){
            $sqlFormat = 'DELETE FROM `user_images` WHERE `user_id` = %d AND `type` = %d AND `id` IN (%s)';
            $sql = sprintf($sqlFormat, $user_id, $type, implode(',', $user_images_id_unuse));

            DB::query($sql);
        }

        // Insert các file được thêm mới nếu có
        if(count($inserts)){
            $sqlFormat = 'INSERT INTO `user_images`(`user_id`, `type`, `image_url`, `created_by`, `updated_by`) VALUES %s';
            $sql = sprintf($sqlFormat, implode(',', $inserts));
            DB::query($sql);
        }
    }

    /**
     * Gets the user images by type.
     *
     * @param      int     $user_id  The user identifier
     * @param      int     $type     The type
     * @param      array   $select   The select
     *
     * @return     array  The user images by type.
     */
    public static function get_user_images_by_type(int $user_id, int $type, array $select = ['*']){
        $select = sprintf('`%s`', implode('`, `', $select));
        $sqlFormat = 'SELECT %s FROM `user_images` WHERE `user_id` = %d AND `type` = "%s"';

        return DB::fetch_all(sprintf($sqlFormat, $select, $user_id, DB::escape($type)));
    }

    /**
     * Gets the user by group identifier.
     *
     * @param      int     $group_id  The group identifier
     *
     * @return     array  The user by group identifier.
     */
    public static function get_user_by_group_id(int $group_id){
        return DB::fetch(sprintf('SELECT * FROM users WHERE group_id = %d LIMIT 1', $group_id));
    }

    /**
     * Gets the user by username.
     *
     * @param      string  $username  The username
     *
     * @return     array  The user by username.
     */
    public static function get_user_by_username(string $username){
        return DB::fetch('
            SELECT users.*, account.is_active FROM users 
            LEFT JOIN account ON account.id = users.username
            WHERE username="'.DB::escape($username).'"
            '
        );
    }


    /**
     * Gets the users by user i ds.
     *
     * @param      array   $userIDs  The user i ds
     * @param      int     $groupID  The group id
     * @param      array   $selects  The selects
     *
     * @return     <type>  The users by username.
     */
    public static function getUsersByUserUsernames(array $userIDs, int $groupID = null, array $selects = ['*'])
    {
        $list_id = array_map( function($id){
            return DB::escape($id);
        }, $userIDs);
        $list_id  = implode('", "', $list_id);
        $sql = 'SELECT ' . implode(',', $selects) .
            ' FROM `users` '.
            'LEFT JOIN account on users.username = account.id WHERE users.`username` IN ("' . $list_id . '")';
        $sql .= $groupID > 0 ? ' AND users.`group_id` = ' . $groupID : '';
        return DB::fetch_all_array($sql);
    }


    /**
     * Gets the teams by group id.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The teams by group id.
     */
    public static function getTeamsByGroupID(int $groupID)
    {
        $sql = '
            SELECT `account_group`.`id`, `account_group`.`name`
            FROM `account_group`
            WHERE 
                `group_id` = ' . $groupID;

        return DB::fetch_all_column($sql, 'name', 'id');
    }

    /**
     * Gets user by phone.
     *
     * @param  int $phone  The number phone
     */
    public static function update_is_count($phone) {

        if (empty($phone)) {
            return;
        }
        $getUsers = DB::fetch_all_array('
            SELECT 
                account.id, 
                account.is_active, 
                account.is_count, 
                users.phone, 
                users.created
            FROM account
            LEFT JOIN 
                users ON users.username = account.id
            WHERE users.phone = "'.$phone.'"
            ORDER BY
                account.is_active DESC,
                users.created ASC 
                ');
        if (!empty($getUsers)) {
            if ($getUsers[0]['is_active'] == 1) {
                if ($getUsers[0]['is_count'] == 0) {
                    $is_count = [
                        "is_count" => 1
                    ];
                    DB::update('account',$is_count,' id="'.$getUsers[0]['id'].'"');
                }
                unset($getUsers[0]);
            }
            $getItems = array_column($getUsers, 'id');
            $is_count = [
                "is_count" => 0
            ];
            DB::update('account',$is_count,' id IN ("'.implode('","',$getItems).' ") AND is_count = 1');
        }
    }
}
?>