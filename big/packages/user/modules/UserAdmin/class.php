<?php
class UserAdmin extends Module
{   
    const PRIVATE_KEY = 'iwequtrbiqwerwfecqweqibwefoqeuwfoegufoweru';
    const FILE_TEMP_EXPIRED_TIME = 86400; // 1 ngay
    const TEMP_DIR_PREFIX = 'upload/temp';
    const STORAGE_DIR_PREFIX = 'upload/ho_so_nguoi_dung';
    public static $group_id;
    function __construct($row)
    {
        self::$group_id = Session::get('group_id');
        if(User::can_admin(false,ANY_CATEGORY) or Session::get('admin_group'))
        {
            Module::Module($row);
            require_once('db.php');
            switch(URL::post('cmd')){
                case 'integrate_callio':
                    if ($this->integrate_callio()) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'success', 'message'=>'Kích hoạt thành công!'));
                    } else {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'error', 'message'=>'Kích hoạt thất bại!'));
                    }
                    exit();
                case 'deactivate_callio':
                    if ($this->deactivate_callio()) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'success', 'message'=>'Tắt kích hoạt thành công!'));
                    } else {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'error', 'message'=>'Tắt kích hoạt thất bại!'));
                    }
                    exit();
                case 'activate_callio':
                    if ($this->activate_callio()) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'success', 'message'=>'Kích hoạt lại thành công!'));
                    } else {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(array('status'=>'error', 'message'=>'Kích hoạt lại thất bại!'));
                    }
                    exit();
                default:
                    break;
            }

            switch(URL::get('cmd'))
            {
                case 'get_user_info':
                    $this->get_user_info();
                    break;
                case 'get_roles':
                    $this->get_roles();
                    break;
                case 'get_password_length':
                    echo User::get_password_strength(Url::get('password'), Url::get('username'));
                    exit();
                    break;
                case 'edit':
                case 'add':
                    require_once 'forms/edit.php';
                    $this->add_form(new EditUserAdminForm());
                    break;
                case 'upload_images':
                    $this->upload_images();
                    break;
                case 'delete_shop':
                    if(User::can_admin(false,ANY_CATEGORY)){
                        require_once 'forms/shop.php';
                        $this->add_form(new ListShopForm());
                    }else{
                        die('Bạn không có quyền truy cập tính năng quản lý shop');
                    }
                    break;      
                case 'shop':
                    if(User::can_admin(false,ANY_CATEGORY)){
                        require_once 'forms/shop.php';
                        $this->add_form(new ListShopForm());
                    }else{
                        die('Bạn không có quyền truy cập tính năng quản lý shop');
                    }
                    break;  
                case 'password':
                    require_once 'forms/password.php';
                    return $this->add_form(new Password());
                case 'integrate_voip24h':
                    $data = array();
                    $data['integrate_voip24h'] = Url::iget('enable') ?: 0;
                    $data['voip24h_info'] = json_encode(array(
                        'line' => Url::get('line'),
                        'password' => Url::get('password')
                    ), JSON_UNESCAPED_UNICODE);
                    $user_id = Url::iget('userid');
                    DB::update_id('users', $data, $user_id);
                    exit();

                case 'api_active_accounts':
                    if(User::can_admin(false,ANY_CATEGORY)){
                        $this->activeUser();
                    }else{
                        die("Bạn không có quyền truy cập tính năng này");
                    }
                    break;
            
                case 'api_set_manager':
                    if(User::can_admin(false,ANY_CATEGORY)){
                            $this->updateGroupManager();
                    }else{
                        die("Bạn không có quyền truy cập tính năng này");
                    }
                    break;
                case 'api_set_leader':
                        if(User::can_admin(false,ANY_CATEGORY)){
                            $this->updateGroupLeader();
                        }else{
                            die("Bạn không có quyền truy cập tính năng này");
                        }
                        break;
                case'api_set_group':
                    if(User::can_admin(false,ANY_CATEGORY)){
                        $this->groupUser();
                    }else{
                        die("Bạn không có quyền truy cập tính năng này");
                    }
                    break;

                default:
                    require_once 'forms/list.php';
                    $this->add_form(new ListUserAdminForm());
                    break;
            }
        }
        else
        {
            URL::access_denied();
        }
    }
    function get_roles()
    {
        $rolesId = Url::iget('role_id');
        $account_id = Url::sget('account_id');
        $account = DB::select('account','id="'.$account_id.'"');
        $groupId = Session::get('group_id');
        if($account['group_id'] != $groupId){
            $groupId = $account['group_id'];
        }
        $cond = ' roles.group_id='.$groupId.' and roles_to_privilege.role_id="'.$rolesId.'"';
        $sql = '
            SELECT
              roles_to_privilege.id
              ,roles_activities.name as role_name
            FROM
              roles_to_privilege
              LEFT JOIN roles ON roles_to_privilege.role_id = roles.id
              INNER JOIN roles_activities ON roles_activities.code = roles_to_privilege.privilege_code
            WHERE
              '.$cond.'
            ORDER BY
              roles_to_privilege.id
        ';
        $items = DB::fetch_all($sql);
        echo json_encode($items);
    }
    function integrate_callio()
    {
        $group_id = Session::get('group_id');
        $user_id = DB::escape(Url::post('users_id'));
        $total_user = DB::escape(Url::post('total_user'));

        $user = DB::select('users', 'id='.$user_id);
        $group = DB::select('groups', 'id='.$group_id);
        if ($group['integrate_callio'] && $group['callio_info']) {
            if ((int)$user['group_id'] === (int)$group_id) {
                $callio_info = json_decode($group['callio_info']);

                // dang ky nguoi dung callio
                $url = CALLIO_AGENCY_HOST . '/client-user';
                $payload = array(
                    "client"    => $callio_info->id,
                    "name"      => $user['name'],
                    "email"     => "big_".$group_id."_".$user_id."@gmail.com",
                    "password"  => "abc@12345",
                    "role"      => "agent",
                    "ext"       => (string) (10000 + (int)$total_user),
                    "extPassword" => "123456789"
                );
                $dataRes = EleFunc::callioPost($url, $payload);
                if (isset($dataRes['id'])) {
                    $row = array();
                    $row['integrate_callio'] = 1;
                    $row['callio_info'] = json_encode($dataRes, JSON_UNESCAPED_UNICODE);
                    DB::update_id('users', $row, $user_id);
                    return true;
                }
            }
        }
        return false;
    }

    function deactivate_callio()
    {
        $group_id = Session::get('group_id');
        $user_id = DB::escape(Url::post('users_id'));

        $user = DB::select('users', 'id='.$user_id);
        $group = DB::select('groups', 'id='.$group_id);
        if ($group['integrate_callio'] && $group['callio_info']) {
            if ((int)$user['group_id'] === (int)$group_id) {
                $callio_info = json_decode($user['callio_info']);

                // dang ky nguoi dung callio
                $url = CALLIO_AGENCY_HOST . '/client-user/deactivate/' . $callio_info->id;
                $dataRes = EleFunc::callioPost($url);
                if (isset($dataRes['id'])) {
                    $row = array();
                    $row['integrate_callio'] = 0;
                    $row['callio_info'] = json_encode($dataRes, JSON_UNESCAPED_UNICODE);
                    DB::update_id('users', $row, $user_id);
                    return true;
                }
            }
        }
        return false;
    }

    function activate_callio()
    {
        $group_id = Session::get('group_id');
        $user_id = DB::escape(Url::post('users_id'));

        $user = DB::select('users', 'id='.$user_id);
        $group = DB::select('groups', 'id='.$group_id);
        if ($group['integrate_callio'] && $group['callio_info']) {
            if ((int)$user['group_id'] === (int)$group_id) {
                $callio_info = json_decode($user['callio_info']);

                // dang ky nguoi dung callio
                $url = CALLIO_AGENCY_HOST . '/client-user/activate/' . $callio_info->id;
                $dataRes = EleFunc::callioPost($url);
                if (isset($dataRes['id'])) {
                    $row = array();
                    $row['integrate_callio'] = 1;
                    $row['callio_info'] = json_encode($dataRes, JSON_UNESCAPED_UNICODE);
                    DB::update_id('users', $row, $user_id);
                    return true;
                }
            }
        }
        return false;
    }
    
    function get_user_info(){
        $cond = 'users.group_id<>'.self::$group_id.'';
        if($keyword = Url::get('p') and strlen($keyword) > 1){
            if(strpos($keyword,'0')===0){
                $keyword = substr($keyword,1,strlen($keyword)-1);
            }
            $account_id = Url::get('account_id');
            if(DB::exists('select id,phone,identity_card from users where group_id ='.UserAdmin::$group_id.' '.($account_id?' and username <> "'.$account_id.'"':'').' and (phone LIKE "'.$keyword.'" OR phone LIKE "0'.$keyword.'")')){
                echo 1;// lỗi trùng lặp sdt trong cùng shop
                exit();
            }
            if(DB::exists('select id,identity_card from users where group_id ='.UserAdmin::$group_id.' '.($account_id?' and username <> "'.$account_id.'"':'').' and (identity_card LIKE "'.$keyword.'" OR identity_card LIKE "0'.$keyword.'")')){
                echo 2;// lỗi trùng lặp cmtnd trong cùng shop
                exit();
            }
            if(strlen($keyword)>=8){
                $cond .= ' AND (
                    users.phone like "'.$keyword.'%"
                    OR users.phone like "0'.$keyword.'%"
                    OR users.identity_card like "'.$keyword.'%"
                    OR users.identity_card like "0'.$keyword.'%"
                )';
            }else{
                $cond .= ' and 1>1';
            }
            if($account_id){
                $cond .= ' AND users.username <> "'.$account_id.'"';
            }
            $sql = '
            SELECT 
                users.id,
                users.username,
                users.name,
                users.phone,
                users.address,
                users.identity_card,
                users.identity_card_front,
                users.identity_card_back,
                users.created,
                __users.username as user_created,
                party.note1,
                party.zone_id,
                zone.name as zone_name,
                groups.name as group_name,
                account.is_active,
                groups_system.name AS group_system_name,
                CONCAT(users.phone," - ĐT: ",users.name," - ĐC: ",users.address ) AS label
            FROM 
                users
            JOIN (SELECT username, id FROM users) AS __users ON __users.id = users.user_created
            JOIN `account` ON account.id=users.username
            JOIN `party` ON party.user_id=users.username
            JOIN `zone` ON party.zone_id=zone.id
            JOIN `groups` ON groups.id = users.group_id
            LEFT JOIN `groups_system` ON groups_system.id = groups.system_group_id
            WHERE '.$cond.'
            ORDER BY
                users.id
            ';

            if($items = DB::fetch_all($sql)){
                $html='';
                $i=1;
                foreach ($items as $key => $value) {
                    $html.='<tr>';
                    $html.='
                        <td>'.$value['username'].' '.($value['is_active']?'<br><span class="label label-success">Đang hoạt động':'<span class="label label-default">Dừng hoạt động').'</span></td>
                        <td>'.$value['name'].'</td>
                        <td>'.$value['phone'].'</td>
                        <td>
                            '.$value['identity_card'].'
                            '.($value['identity_card_front']?'<br><img src="'.$value['identity_card_front'].'" width="200">':'').
                            ($value['identity_card_back']?'<br><img src="'.$value['identity_card_back'].'" width="200">':'').'
                        </td>'.
                        '<td>'.$value['address']. ' - ' . $value['zone_name'].'</td>'.
                        '<td>'.$value['group_name'].'-' . $value['group_system_name'] . '</td>'.
                        '<td>'.$value['user_created'].'<br><span class="small"><i class="fa fa-clock-o"></i> '.date('H:i\' d/m',strtotime($value['created'])).'</span></td>'.
                        '<td>'.$value['note1'].'</td>'.
                        '<td><button type="button" class="btn btn-success" onclick="onClick(event)" data-user=\''.json_encode($value).'\'>Chọn</button></td>'
                        ;
                    $html.='</tr>';
                    $i++;
                }
                echo $html;
                exit();
            }
            else{
                echo 0;
                exit();
            }
        }
        else{
            echo 0;
            exit();
        }

    }

    /**
     * { function_description }
     *
     * @param      <type>  $string  The string
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function hash($string){
        return md5(self::PRIVATE_KEY . $string . self::PRIVATE_KEY);
    }

    /**
     * Uploads images.
     */
    private function upload_images(){
        require_once ROOT_PATH . 'packages/core/includes/common/ImageType.php';
        
        if(!$_FILES['image']['name'] || $_FILES['image']['size'] <= 0){
            RequestHandler::sendJson('{"error": "Ảnh tải lên không hợp lệ"}');
        }

        if($_FILES['image']['size'] > 1024 * 1024){
            RequestHandler::sendJson('{"error": "Vui lòng upload ảnh có dung lượng nhỏ hơn 1MB"}');
        }

        if(!ImageType::canUploadImageWithMimeType($_FILES['image']['type'], $_FILES['image']['tmp_name'])){
            RequestHandler::sendJson('{"error": "Vui lòng upload ảnh có định dạng jpg,jpeg,png,gif."}');
        }

        require_once 'packages/core/includes/utils/ftp.php';
        if($image_url = FTP::upload_file('image', self::TEMP_DIR_PREFIX, true,'content', 'IMAGE', false)) {
            exit(json_encode([
                'url' => $image_url,
                'hash' => self::hash($image_url)
            ]));
        }

        exit('{"error": "Server Error: Upload ảnh không thành công."}');
    }

    /** 
     * Removes temporary files expires.
     *
     * @return     array  ( description_of_the_return_value )
     */
    public static function remove_expired_temp_files(){
        FTP::home();
        ftp_chdir(FTP::$ftp_connect_id, self::TEMP_DIR_PREFIX . FTP::SUB_DIR_STORAGE);

        $temp_files = ftp_nlist(FTP::$ftp_connect_id, '.');
        if(!is_array($temp_files) || !$temp_files){
            return false;
        }

        $in_date_files = [];
        foreach ($temp_files as $temp_file) {
            if(ftp_mdtm(FTP::$ftp_connect_id, $temp_file) <= time() - self::FILE_TEMP_EXPIRED_TIME){
                ftp_delete(FTP::$ftp_connect_id, $temp_file);
                continue;
            }

            $in_date_files[] = $temp_file;
        }

        return $in_date_files;
    }

    /**
     * { function_description }
     *
     * @param      string  $temp_url  The temporary url
     *
     * @return     string  ( description_of_the_return_value )
     */
    public static function move_uploaded_temp_file(string $temp_url){
        if(!preg_match('~'.self::TEMP_DIR_PREFIX.'~', $temp_url)){
            return $temp_url;
        }

        FTP::home();

        $url_params = parse_url($temp_url);
        $url_params['path'] = preg_replace('~^[/.]+~', '', $url_params['path']);

        // create destination file path
        $destination = preg_replace('~'.self::TEMP_DIR_PREFIX.'~', self::STORAGE_DIR_PREFIX, $url_params['path']);

        try{
            // move tmp file to storage file
            ftp_rename(FTP::$ftp_connect_id, $url_params['path'], $destination);

            // ghi đè path của url thành file đích nếu dung lượng file đích lớn hơn 1kB
            if(ftp_mdtm(FTP::$ftp_connect_id, $destination) > 1024){
                ftp_delete(FTP::$ftp_connect_id, $url_params['path']);
                $url_params['path'] = $destination;
            }
        }catch(Throwable $e){

        }

        return sprintf('%s://%s/%s', $url_params['scheme'], $url_params['host'], $url_params['path']);
    }

    /**
     * { function_description }
     *
     * @param      string  $file_url  The file url
     *
     * @return     string  ( description_of_the_return_value )
     */
    public static function delete_storage_file(string $file_url){
        if(!preg_match('~'.self::STORAGE_DIR_PREFIX.'~', $file_url)){
            return $file_url;
        }

        FTP::home();

        $url_params = parse_url($file_url);
        $url_params['path'] = preg_replace('~^[/.]+~', '', $url_params['path']);

        ftp_delete(FTP::$ftp_connect_id, $url_params['path']);
    }

    /**
     * Gets the vaccination count fields.
     *
     * @return     array  The vaccination count fields.
     */
    public static function getVaccinationCountFields()
    {
        return [
            0 => 'Chưa xác định',
            1 => 'Chưa tiêm',
            2 => '1 mũi',
            3 => '2 mũi',
            4 => '3 mũi',
        ];
    }
    /**
     * Gets the vaccination status fields.
     *
     * @return     array  The vaccination status fields.
     */
    public static function getVaccinationStatusFields()
    {
        return [
            0 => 'Chưa xác định',
            1 => 'Bình thường',
            2 => 'F0',
            3 => 'F1',
            4 => 'F2',
            5 => 'F3',
            6 => 'Khác',
        ];
    }

    
    function activeUser(){
        $status_type = Url::get("status_type");
        $status = $status_type == 'active'? '1' : '0';
        $ids = URL::get('user_ids');
        if(!$ids || !is_array($ids) ||empty($ids)){
            return RequestHandler::sendJsonError('false');
        }else{
            $list_id = array_map( function($id){
                return DB::escape($id);
            }, $ids);
            $group_id = Session::get('group_id');

            $list_id  = implode('", "', $list_id);
            $where = 'account.id IN ("' . $list_id . '")' . (User::is_admin() ? '' : ' AND account.group_id = ' . $group_id);
            $users = DB::fetch_all('select account.id,account.is_active,users.phone from account join users on account.id=users.username where '.$where );
            
            $mesage = '';
            $count_success = 0;
            $count_fail = 0;
            foreach ($users as $user) {
                $old = ['id' => $user['id'], 'is_active' => $user['is_active']];
                $new = ['id' => $user['id'], 'is_active' => $status];
                if($status == '1'){
                    if(!UserAdminDB::check_user_counter($group_id)){
                        DB::update('account', ['is_active' =>  $status], 'id = "'.$user['id'].'"');
                        UserAdminDB::update_is_count($user['phone']);
                        UserAdminDB::update_log($old,$new);
                        $count_success++;
                    }else{
                        $count_fail++;
                    }
                }else{
                    DB::update('account', ['is_active' =>  $status], 'id = "'.$user['id'].'"');
                    UserAdminDB::update_is_count($user['phone']);
                    UserAdminDB::update_log($old,$new);
                    $count_success++;
                }
                
            }

            if($count_success > 0){
                $mesage .= 'Cập nhật thành công '.$count_success.'  tài khoản.';
            }
            if($count_fail > 0){
                $mesage .= ' Có '.$count_fail.' tài khoản không được cập nhật do quá số lượng tài khoản được phép kích hoạt.';
            }

            if($count_success){
                return RequestHandler::sendJsonSuccess($mesage);
            }else{
                return RequestHandler::sendJsonError($mesage);
            }
        }
    }

    function updateGroupLeader(){
        $userID = URL::get('user_id');
        $teamIDs = URL::get('group_ids');
        $shop_id = DB::escape(URL::get('shop_id'));
        $groupID = User::is_admin() ? null : Session::get('group_id');

        if(!$userID || !$teamIDs || !is_array($teamIDs) || empty($teamIDs)|| (!User::is_admin() && $shop_id != $groupID)){
            return RequestHandler::sendJsonError('false');
        }

        $sql = 'select id from users where username="'.DB::escape($userID).'"';
        $sql .= $groupID ? " AND group_id=". $groupID : "";
        $user_id = DB::fetch($sql, 'id');

        if(!$user_id){
            return RequestHandler::sendJsonError('false');
        }else{
            // trường hợp huỷ gán
            if(count($teamIDs) === 1 && $teamIDs[0] == 0){
                $cond = ' admin_user_id = '.$user_id;
                DB::update('account_group', ['admin_user_id' => 0], $cond);
            }else{
                $cond = " id IN(".implode(', ', $teamIDs).")";
                $cond .= $groupID ? " AND group_id=". $groupID : "";
                DB::update('account_group', ['admin_user_id' => $user_id], $cond);
            }

            
        }

        return RequestHandler::sendJsonSuccess('true');
        
    }

    function updateGroupManager(){
        $group_ids = URL::get('group_ids');
        $user_ids = URL::get('user_ids');
        $shop_id= DB::escape(URL::get('shop_id'));
        $groupID = User::is_admin() ? null : Session::get('group_id');
        if(!is_array($user_ids) || empty($user_ids) ||!is_array($group_ids) || empty($group_ids) || !$shop_id || (!User::is_admin() && $shop_id != $groupID)){
            return RequestHandler::sendJsonError('false');
        }

        $list_group = array_map( function($g){
            return DB::escape($g);
        }, $group_ids);
        
        $list_users = array_map( function($u){
            return DB::escape($u);
        }, $user_ids);
        
        $sql = "SELECT * FROM account_group WHERE id IN (".implode(", ", $list_group).") AND group_id = ".$shop_id;
        $groups = DB::fetch_all($sql);

        $usersql= "SELECT id FROM users WHERE username IN ('".implode("', '", $list_users)."') AND group_id = ".$shop_id;

        $users = DB::fetch_all($usersql);

        if(count($group_ids) == 1 && $group_ids[0] == 0){
            if($users){
                $uids = array_map(function($user){
                    return $user['id'];
                }, $users);
                DB::delete('account_group_admin', " user_id IN(".implode(', ',$uids) .")");
            }   
        }else{
            foreach($groups as $group){
                $gid = $group['id'];
                foreach ($users as $key => $user) {
                    $user_id = $user['id'];
                    if(isset($groups[$gid])){
                        if(!DB::exists("select id from account_group_admin where account_group_id=".$gid ." AND user_id=".$user_id)){
                            DB::insert('account_group_admin', 
                            ['user_id' => $user_id, "account_group_id" =>$gid, 'created'=>date('Y-m-d H:i:s'),'type'=>1 ]);
                        }
                    }
                }
            }
        }

        return RequestHandler::sendJsonSuccess('true');

    }


    function groupUser(){
        $userIDs = URL::get('user_ids');
        $teamID = URL::getUInt('group_id');
        $groupID = User::is_admin() ? 0 : Session::get('group_id');

        if(!$userIDs || !is_array($userIDs) || empty($userIDs)){
            return RequestHandler::sendJsonError('false');
        }
        // Lấy danh sách users 
        $users = UserAdminDB::getUsersByUserUsernames($userIDs, $groupID, ['users.id','users.username','users.group_id', 'account.account_group_id']);
        // Nhóm theo group_id
        // Nếu số nhóm khác 1 => các users không cùng shop
        $group_ids = array_map( function ( $ar ) {
            return $ar['group_id'];
        }, $users);

        if(!$users || count(array_unique($group_ids)) !== 1){
            return RequestHandler::sendJsonError('false');
        }

        // Validate team
        if($teamID > 0){
            $teams = UserAdminDB::getTeamsByGroupID($users[0]['group_id']);
            if(!$teams || !isset($teams[$teamID])){
                return RequestHandler::sendJsonError('false');
            }
        }

        $list_id = array_map( function($id){
            return DB::escape($id);
        }, $userIDs);
        $list_id  = implode('", "', $list_id);
        $where = 'id IN ("' . $list_id . '")'; 
        $where .= ' AND `group_id` = ' . $users[0]['group_id'];
        if(!DB::update('account', ['account_group_id' => $teamID], $where)){
            return RequestHandler::sendJsonError('false');
        }

        foreach ($users as $user) {
            $old = ['id' => $user['username'], 'account_group_id' => $user['account_group_id']];
            $new = ['id' => $user['username'], 'account_group_id' => $teamID];
            UserAdminDB::update_log($old,$new);
        }
        
        return RequestHandler::sendJsonSuccess('true');
        
    }
}
?>
