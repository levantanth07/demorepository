<?php
require_once 'packages/core/includes/utils/functions.php';
require_once 'packages/core/includes/common/ImageType.php';
require_once ROOT_PATH . 'packages/core/includes/system/crm_sync.php';
use GuzzleHttp\Client;
class EditUserAdminForm extends Form
{
    const MAX_NUM_IMAGE = 10;

    private $isAdmin;
    protected $map;
    function __construct()
    {
        $this->isAdmin = can_tuha_administrator() || can_tuha_content_admin();

        Form::Form('EditUserAdminForm');
        if(URL::get('cmd')=='edit')
        {
            //$this->add('id',new IDType(true,'Lỗi nhập tên tài khoản sai quy cách. Kiểm tra dấu cách đầu cuối.','account'));
            $this->add('id',new UniqueType('Duplicate identifier (T&#234;n t&#224;i kho&#7843;n &#273;&#227; &#273;&#432;&#7907;c s&#7917; d&#7909;ng)','account','id'));
        }
        else
        {
            //$this->add('id',new UsernameType(true,'Bạn vui lòng nhập tên tài khoản đúng định dạng'));
        }
        if(Url::get('cmd')=='add'){
            $this->add('password',new TextType(true,'Bạn vui lòng nhập mật khẩu',0,255));
        }

        $this->add('vaccination_count',new IntType(false,'vaccination_count'));
        $this->add('vaccination_status',new IntType(false,'vaccination_status'));
        $this->link_css('assets/default/css/cms.css');

    }
    function updateAccountToVichat($infor) {
        $baseUri = API_UPDATE_ACCOUNT_PALBOX;
        $apiKey = API_KEY_PALBOX;
        $client = new Client();
        try {
            $response = $client->post(
                $baseUri,
                array(
                    'json' => $infor,
                    'headers' => ['api-key' => $apiKey],
                    'allow_redirects' => false,
                    'timeout' => 5
                )
            );

        } catch (Exception $e) {

        }
    }

    function validateTelephoneNumber(string $telephone_number, int $type = MOBILE_TYPE_DOMESTIC): bool
    {
        $pattern = MOBILE_REGEX_PATTERNS[$type] ?? null;
        if (!$pattern) {
            return false;
        }//end if
    
        if (preg_match($pattern, $telephone_number)) {
            return true;
        } //end if
    
        return false;
    }

	function on_submit()
	{

        require_once 'packages/core/includes/utils/ftp.php';
        $isEdit = URL::get('cmd') == 'edit';
        // Xóa các image quá thời gian lưu trữ, thường là 1 ngày
        UserAdmin::remove_expired_temp_files();
        $userID = URL::getUInt('user_id');

        if(!$this->isAdmin && !URL::get('confirm')){
            return;
        }
        // Lấy tất cả thông tin người dùng để validate dữ liệu
        $account_id = DB::escape(Url::get('id'));
        $userInfomation = UserAdminDB::get_user_by_username($account_id);
        $_error = [];
        $identity_card_front = $this->mustUpdateIdCardImage($userInfomation, 'identity_card_front');
        if(!$identity_card_front && !$this->isAdmin){
            $_error['identity_card_front'] = ['Bạn chưa upload ảnh mặt trước CMND/CCCD'];
        }

        $identity_card_back = $this->mustUpdateIdCardImage($userInfomation, 'identity_card_back');
        if(!$identity_card_back && !$this->isAdmin){
            $_error['identity_card_back'] = ['Bạn chưa upload ảnh mặt sau CMND/CCCD'];
        }
        $this->validateUserName();
        $this->add('email',new EmailType(false,'Bạn vui lòng nhập đúng định dạng Email'));
        $this->add('full_name',new TextType(true,'Bạn vui lòng nhập họ và tên',0,255));
        $this->add('phone',new TextType(true,'Bạn vui lòng nhập số điên thoại',0,20));
        //$this->add('birth_date',new DateType(false,'invalid_birth_date'));
        !$this->isAdmin && $this->add('address',new TextType(false,'invalid_address',0,255));
        //$this->add('expired_date',new DateType(false,'expired_date'));
        !$this->isAdmin && $this->add('zone_id',new IDType(false,'invalid_zone_id','zone'));
        !$this->isAdmin && $this->add('job_application',new TextType(true,'File ảnh hồ sơ xin việc không được để trống.',0,1024));
        !$this->isAdmin && $this->add('birth_certificate',new TextType(true,'File ảnh giấy khai sinh không được để trống.',0,1024));
//        !$this->isAdmin && $this->add('health_certification',new TextType(true,'File ảnh giấy khám SK A3 không được để trống.',0,1024));
        !$this->isAdmin && $this->add('diploma',new TextType(true,'File ảnh bằng cấp không được để trống.',0,1024));
//        !$this->isAdmin && $this->add('information_security',new TextType(true,'File ảnh cam kết bảo mật thông tin không được để trống.',0,1024));
        $job_application = DB::escape(Url::post('job_application'));
        $checkLengthImg = explode('{',$job_application);
        if(count($checkLengthImg) < 5 && !$this->isAdmin){
            $this->error('job_application','File ảnh hồ sơ xin việc tối thiểu 4 ảnh.',false);
        }
        $admin_groups_system = User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY);
        $admin_group =  Session::get('admin_group');
        $old_active_value = false;
        if($isEdit) {
            $condition = 'id = "' . DB::escape(Url::get('id')) . '"';
            if($admin_groups_system)
                $condition .= sprintf(' AND group_id=%d', Session::get('group_id'));

            $old_active_value = DB::fetch('select is_active from account where ' . $condition, 'is_active');
            if($old_active_value && URL::get('active') == ''){
                $infor['is_active'] = 0;
                $infor['account_id'] = DB::escape(Url::get('id'));
                $infor['provider'] = 'BIG';
                $this->updateAccountToVichat($infor);
            }else if($old_active_value == '0' && URL::get('active') == '1'){
                if($user_counter=UserAdminDB::check_user_counter(Session::get('group_id'))){
                    $this->error('id','Đã quá số lượng tài khoản được cho phép kích hoạt. Tối đa chỉ được '.$user_counter.' tài khoản.',false);
                    return;
                }
            }
        }else{
            if($user_counter=UserAdminDB::check_user_counter(Session::get('group_id'))){
                $this->error('id','Đã quá số lượng tài khoản được cho phép khởi tạo. Tối đa chỉ được tạo '.$user_counter.' tài khoản.',false);
                return;
            }
        }

        if($this->check() && URL::get('confirm_edit')) {
            $flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : '';
            $this->error_messages = array_merge($this->error_messages ? $this->error_messages : [], $_error);

            $level = 0;
            $edit_mode = false;
            $old_log_arr = [];
            $new_log_arr = [];

            $account_new_row = [
                'type'=>'USER',
                'cache_privilege'=>''
            ];

            $account_new_row['account_group_id'] = DB::escape(URL::get('account_group_id'));
            if(!$account_new_row['account_group_id'])
                $account_new_row['account_group_id'] = 0;

            if(URL::get('password')){
                $account_new_row['password'] = User::encode_password($_REQUEST['password']);
            }

            if(URL::get('password')) {
                if(User::get_password_strength(URL::getString('password'), Url::get('id')) <= User::PASSWD_NOT_WEAK){
                    return $this->error('password','Mật khẩu chưa đủ mạnh');
                }
                
                $password = User::encode_password(URL::getString('password'));

                require_once ROOT_PATH . 'packages/core/includes/common/ResetPassword.php';
                $rp = ResetPassword::newQuery();

                if(!$rp->validateUpdateUserID($userID, $password)){
                    return $this->error(
                        'id',
                        'Không được phép nhập trùng mật khẩu ' 
                        . ResetPassword::NUMBER_CHANGES_PASSWORD_MUST_UNIQUE 
                        . ' lần gần nhất của tài khoản ' . Url::get('id') . '.',
                        false
                    );
                }

                $account_new_row['password'] = $password;
                $account_new_row['password_updated_at'] = now();
            }

			if($admin_groups_system or $admin_group){
				$level = 100;
				$account_new_row['admin_group'] = Url::get('admin_group') ? 1 : '0';
			}

            // Validate ngày sinh
            $birth_date = $this->validateDateTime('birth_date');
            if($birth_date === false){
                $this->error('birth_date','Ngày sinh không hợp lệ.',false);
            }

            // Validate Tỉnh thành phố thường trú
            if(!Url::post('zone_id') && !$this->isAdmin){
                $this->error('zone_id','Tỉnh/TP thường trú không được để trống.',false);
            }

            $full_name = DB::escape(Url::get('full_name'));

            // Validate số điện thoại
            $phone = Url::get('phone');
            $phoneType = Url::get('mobiletype');
            if (!$this->validateTelephoneNumber($phone, $phoneType)) {
               $this->error('phone','Số điện thoại không hợp lệ.',false);
            }//end if
            // if(!preg_match('~^\d{8,11}$~', $phone)){
            //    $this->error('phone','Số điện thoại không hợp lệ.',false);
            // }

            // Validate địa chỉ thường trú
            $address = DB::escape(Url::get('address'));
            if(!$address && !$this->isAdmin){
                $this->error('address','Địa chỉ thường trú không được để trống.',false);
            }
            // Validate hồ sơ xin việc
            $job_application = DB::escape(Url::get('job_application'));
            $checkLengthImg = explode('{',$job_application);
            if(count($checkLengthImg) < 5 && !$this->isAdmin){
                $this->error('job_application','File ảnh hồ sơ xin việc tối thiểu 4 ảnh.',false);
            }
            if(!$job_application && !$this->isAdmin){
                $this->error('job_application','File ảnh hồ sơ xin việc không được để trống.',false);
            }
            // Validate giấy khai sinh
            $birth_certificate = DB::escape(Url::get('birth_certificate'));
            if(!$birth_certificate && !$this->isAdmin){
                $this->error('birth_certificate','File ảnh giấy khai sinh không được để trống.',false);
            }

            // Validate giấy khám SK A3
//            $health_certification = DB::escape(Url::get('health_certification'));
//            if(!$health_certification && !$this->isAdmin){
//                $this->error('health_certification','File ảnh giấy khám SK A3 không được để trống.',false);
//            }

            // Validate bằng cấp
            $diploma = DB::escape(Url::get('diploma'));
            if(!$diploma && !$this->isAdmin){
                $this->error('diploma','File ảnh bằng cấp không được để trống.',false);
            }

            // Validate cam kết bảo mật thông tin
//            $information_security = DB::escape(Url::get('information_security'));
//            if(!$information_security && !$this->isAdmin){
//                $this->error('information_security','File ảnh cam kết bảo mật thông tin không được để trống.',false);
//            }

            // Validate nơi cấp cmnd/cccd
            $id_card_issued_by = Url::get('id_card_issued_by')? DB::escape(Url::get('id_card_issued_by')) : '';
            if(!$id_card_issued_by && !$this->isAdmin){
                $this->error('id_card_issued_by','Nơi cấp CMND/Căn cước CD không được để trống.',false);
            }

            // Validate ngày cấp cmnd/cccd
            $id_card_issued_date = $this->validateDateTime('id_card_issued_date');
            if($id_card_issued_date === false){
                $this->error('birth_date','Ngày cấp CMND/Căn cước CD không hợp lệ.',false);
            }

            // Validate link fb
            $fb_link = Url::get('fb_link')? DB::escape(Url::get('fb_link')) : '';
            if(!$fb_link && !$this->isAdmin){
                $this->error('fb_link','Link FB không được để trống.',false);
            }


            $party_new_row = [
                'zone_id'       =>  Url::post('zone_id') ? DB::escape(Url::post('zone_id')):'0',
                'email'         =>  DB::escape(Url::post('email')),
                'note1'         =>  DB::escape(Url::post('note1')),
                'gender'        =>  Url::post('gender') ? DB::escape(Url::get('gender')) : '0',
                'type'          =>  'USER',
                'label'         =>  DB::escape(Url::post('label')),
                'status'        =>  'SHOW'
            ];

            if($birth_date || $this->isAdmin){
                $party_new_row['birth_date'] = $birth_date;
            }

            $is_active = URL::get('active') ? DB::escape(URL::get('active')) : 0;
            if(!empty($this->error_messages)){
                return;
            }

            if($admin_group) {
                if (!$old_active_value && $is_active
                    && $user_counter = UserAdminDB::check_user_counter(Session::get('group_id'))) {
                    $this->error('id', 'Đã quá số lượng tài khoản được cho phép khởi tạo. Tối đa chỉ được tạo ' . $user_counter . ' tài khoản.', false);
                    return;
                }
                $account_new_row += ['is_active'=>$is_active];
            }
            $group_id = Session::get('group_id');
            if($admin_groups_system) {
                $group_id = Url::get('group_id');
                if(!$group_id)
                    $group_id = 0;

                $system_group_id = Url::get('system_group_id');
                if(!$system_group_id)
                    $system_group_id = 8;

                $user = UserAdminDB::get_user_by_group_id($group_id);
                if($group_id && $user){
                    // $full_name = $user['name'];
                    // $phone = $user['phone'];
                    // $address = $user['address'];
                }else{
                    if($group = DB::fetch('select * from `groups` where code="'.$account_id.'"')){
                        $group_id = $group['id'];
                    }else{
                        $arr_ = array(
                            'system_group_id'=>$system_group_id,
                            'code' => $account_id,
                            'name' => Url::get('group_name') ? DB::escape(Url::get('group_name')) : $account_id,
                            'account_type'=> DB::escape(Url::get('account_type')),
                            'active'=>1,'created'=>date('Y-m-d H:i:s'),
                            'expired_date'=>'0000-00-00',
                            'master_group_id'=> Url::get('master_group_id') ? DB::escape(Url::get('master_group_id')) : 0
                        );
                        $group_id = DB::insert('groups', $arr_);
                    }
                }
            }


            $temp_address = Url::get('temp_address') ? DB::escape(Url::get('temp_address')) : '';
            $temp_zone_id = Url::get('temp_zone_id')?Url::get('temp_zone_id'):0;

            $job_application = $this->parseImagesUrl('job_application', true, true);
            $registration_book = $this->parseImagesUrl('registration_book', true, true);
            $contract = $this->parseImagesUrl('contract', true, true);
            $commitment = $this->parseImagesUrl('commitment', true);
            $birth_certificate = $this->parseImagesUrl('birth_certificate', true, true);
            $health_certification = $this->parseImagesUrl('health_certification', true, true);
            $diploma = $this->parseImagesUrl('diploma', true, true);
            $information_security = $this->parseImagesUrl('information_security', true, true);

            if(isset($this->error_messages['registration_book'])
                || isset($this->error_messages['contract'] )
                || isset($this->error_messages['commitment'])
                || isset($this->error_messages['birth_certificate'])
                || isset($this->error_messages['health_certification'])
                || isset($this->error_messages['diploma'])
                || isset($this->error_messages['information_security'])
            ){
                return;
            }

            $party_new_row += array('full_name'=>$full_name);
            $party_new_row += array('phone'=>$phone);
            $party_new_row += array('address'=>$address);
            require_once 'packages/core/includes/utils/upload_file.php';
            $dir = 'default/groups/'.$group_id.'/';
            if(URL::get('cmd')=='edit' and $account_id and $account = DB::select('account','id="'.$account_id.'" '.(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)?'':' and group_id='.$group_id).'')) {
                $edit_mode = true;
                $old_log_arr += $account;
                if($party=DB::fetch('select id,full_name from party where user_id = "'.$account_id.'" and type ="USER"')){
                    $old_log_arr += ['full_name'=>$party['full_name']];
					DB::update('party', $party_new_row,'user_id="'.$account_id.'" and type="USER"');
				}
			
				DB::update('account', $account_new_row+array('is_block'=>0),'id="'.$account_id.'" '.(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)?'':' and group_id='.$group_id).'');

                if(isset($rp) && $rp instanceof ResetPassword) {
                    $rp->editOrNew($userID, $password);
                }

                $new_log_arr += $account_new_row+$party_new_row;
            } else {
                if(!DB::exists('select id from account where id="'.$account_id.'"')) {
                    require_once 'packages/core/includes/system/si_database.php';
                    DB::insert('party', $party_new_row + array('time'=>time(),'user_id' => $account_id));
                    $account_new_row += array('group_id'=>$group_id);
                    DB::insert('account', $account_new_row + array('last_online_time'=>0,'cache_setting'=>'','is_block'=>0,'id' => $account_id,'create_date'=>date('Y-m-d')));
                }else{
                    $this->error('id','Tên này đã tồn tại trong hệ thống, quý khách vui lòng chọn tên khác.',false);
                    return;
                }
            }

            //////////////////
            if(!($extension = Url::get('extension'))){
                $extension = 'NULL';
            }
            if($user = DB::fetch('select id,username,`name`,phone,address,`status` from users where username="'.$account_id.'"')){

                $arr = array(
                    'name'=>$full_name,
                    'phone'=>$phone,
                    'address'=>$address,

                    'fb_link' => $fb_link,
                    'temp_address' => $temp_address,
                    'temp_zone_id' => $temp_zone_id,

                    'identity_card_front' => $identity_card_front?? 'NULL',
                    'identity_card_back' => $identity_card_back?? 'NULL',
                    'modified'=>date('Y-m-d H:i:s'),
                    'identity_card'
                );

                $newPass = $account_new_row['password'];
                $oldPass = $account['password'];
                $arr['status'] = intval($user['status'] || ($newPass && $newPass != $oldPass));

                if ($id_card_issued_by || $this->isAdmin) {
                    $arr['id_card_issued_by'] = $id_card_issued_by;
                }

                if ($id_card_issued_date || $this->isAdmin) {
                    $arr['id_card_issued_date'] = $id_card_issued_date;
                }

                if(!DB::exists('select id,extension from users where group_id='.$group_id.' and extension="'.$extension.'" and id <> '.$user['id']) || $this->isAdmin){
                    $arr['extension'] = $extension;
                }
                $old_log_arr += $user;
                $new_log_arr += $arr;
                UserAdminDB::update_log($old_log_arr,$new_log_arr);
                DB::update('users',$arr,'username="'.$account_id.'"');
                $user_id = $user['id'];

                if ($phone != $userInfomation['phone']) {
                    UserAdminDB::update_is_count($phone);
                    UserAdminDB::update_is_count($userInfomation['phone']);
                } elseif ($is_active != $userInfomation['is_active']) {
                    UserAdminDB::update_is_count($phone);
                }
            }else{
                $arr = array(
                    'group_id'=>$group_id,
                    'username'=>$account_id,
                    'name'=>$full_name,
                    'phone'=>$phone,
                    'address'=>$address,

                    'fb_link' => $fb_link,
                    'temp_address' => $temp_address,
                    'temp_zone_id' => $temp_zone_id,

                    'identity_card_front' => $identity_card_front?? 'NULL',
                    'identity_card_back' => $identity_card_back?? 'NULL',

                    'status'=>0,
                    'user_created'=>get_user_id(),
                    'created'=>date('Y-m-d H:i:s'),
                    'identity_card'
                );

                if ($id_card_issued_by || $this->isAdmin) {
                    $arr['id_card_issued_by'] = $id_card_issued_by;
                }

                if ($id_card_issued_date || $this->isAdmin) {
                    $arr['id_card_issued_date'] = $id_card_issued_date;
                }

                if(!DB::exists('select id,extension from users where group_id='.$group_id.' and extension="'.$extension.'"')  || $this->isAdmin){
                    $arr['extension'] = $extension;
                }

                $user_id = DB::insert('users',$arr);
                if ($is_active == 1) {
                    UserAdminDB::update_is_count($phone);
                }
            }

            // Xử lý thông tin tiêm chủng
            if($results = $this->upsertVaccination($user_id, $account_id)){
                $this->logUpsertVaccination($results);
            }

            if($user_id){
                UserAdminDB::insert_user_images($job_application, $user_id, ImageType::HO_SO_XIN_VIEC);
                UserAdminDB::insert_user_images($registration_book, $user_id, ImageType::SO_HO_KHAU);
                UserAdminDB::insert_user_images($contract, $user_id, ImageType::HOP_DONG_HOP_TAC);
                UserAdminDB::insert_user_images($commitment, $user_id, ImageType::CAM_KET);
                UserAdminDB::insert_user_images($birth_certificate, $user_id, ImageType::KHAI_SINH);
                UserAdminDB::insert_user_images($health_certification, $user_id, ImageType::GIAY_KHAM_SUC_KHOE);
                UserAdminDB::insert_user_images($diploma, $user_id, ImageType::BANG_CAP);
                UserAdminDB::insert_user_images($information_security, $user_id, ImageType::CAM_KET_BAO_MAT_TT);
            }

            if($admin_groups_system or $admin_group){
                DB::update('users',array('level'=>$level),'username="'.$account_id.'"');
                if(Url::get('fb_page_id')){
                    DB::update('fb_pages',array('account_id'=>$account_id),'id='.Url::get('fb_page_id'));
                }
            }
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
            if($admin_groups_system and $group_id){
                $master_group_id = Url::get('master_group_id')?Url::get('master_group_id'):0;
                $arr_ = ['master_group_id'=>$master_group_id];
                if(Url::get('cmd')=='add'){
                    $arr_['expired_date'] = Url::get('expired_date')?Url::get('expired_date'):'0000-00-00';
                }
                if (Url::get('group_id') != '' && isset($arr_['expired_date'])) {
                    unset($arr_['expired_date']);
                }
                DB::update('groups',$arr_,'id='.$group_id);
            }
            //////////////////////////
            //System::debug(Url::get('roles'));die;
            if($admin_group){
                if($edit_mode==false or ($group_id == $account['group_id'])){
                    if($roles = Url::get('roles')){
                        if($user_id_=DB::fetch('select id from users where username="'.$account_id.'" and group_id='.$group_id,'id')){
                            DB::delete('users_roles','user_id='.$user_id_.'');
                            foreach($roles as $key=>$record){
                                DB::insert('users_roles', array('user_id'=>$user_id_,'role_id'=>$record,'created'=>date('Y-m-d H:i:s')));
                            }
                        }
                    }else{
                        if($user_id_=DB::fetch('select id from users where username="'.$account_id.'" and group_id='.$group_id.'','id')) {
                            if($count = DB::fetch('select count(*) as total from users where username="'.$account_id.'" and group_id='.$group_id,'total')){
                                DB::delete('users_roles','user_id='.$user_id_.'');
                            }
                        }
                    }
                }
            }
            if($admin_group){
                if($edit_mode==false or ($group_id == $account['group_id'])){
                    if($account_groups = Url::get('account_groups')){
                        if($user_id_=DB::fetch('select id from users where username="'.$account_id.'" and group_id='.$group_id,'id')){
                            DB::delete('account_group_admin','user_id='.$user_id_.'');
                            foreach($account_groups as $key=>$record){
                                DB::insert('account_group_admin', array('user_id'=>$user_id_,'account_group_id'=>$record,'created'=>date('Y-m-d H:i:s'),'type'=>1));
                            }
                        }
                    }else{
                        if($user_id_=DB::fetch('select id from users where username="'.$account_id.'" and group_id='.$group_id,'id')) {
                            if($count = DB::fetch('select count(*) as total from users where username="'.$account_id.'" and group_id='.$group_id,'total')){
                                DB::delete('account_group_admin','user_id='.$user_id_.'');
                            }
                        }
                    }
                }
            }

            UserAdminDB::init_fb_cron_config($group_id);
            //////////////////////////
            if(!Url::get('active')){
                $arr = array('not_is_active'=>1);
            }else{
                $arr = array();
            }
            Url::js_redirect(true,'Cập nhật dữ liệu thành công',$arr);

        }
    }
    /**
     * Determines if date format.
     */
    private function isDateFormat(string $date){
        return preg_match('~^\d{4}/\d{1,2}/\d{1,2}$~', $date);
    }

    /**
     *  Validate ngày tháng
     *
     * @param      string  $fieldName  The field name
     *
     * @return     bool    ( description_of_the_return_value )
     */
    private function validateDateTime(string $fieldName){
        if(!($raw = Url::get($fieldName))){
            return $this->isAdmin ? 'NULL' : false;
        }

        $times = explode('/', $raw);
        $datetime = implode('/', array_reverse($times));

        if(count($times) != 3 || !$this->isDateFormat($datetime)){
            return $this->isAdmin ? 'NULL' : false;
        }

        if (strtotime($datetime) >= time() && !$this->isAdmin) {
            return false;
        }

        return $datetime;
    }
    private function validateUserName()
    {
        $username = URL::getString('id');
        if(!preg_match('#^[\w\.\@\-]+$#', $username)){
            $this->add('id',new UsernameType(true,'Bạn vui lòng nhập tên tài khoản đúng định dạng'));
            // URL::js_redirect(true,'Vui lòng nhập username chứa các ký tự: từ 0->9, A-z .. và các ký tự @, gạch dưới, dấu chấm, gạch ngang');
        }
    }
    function draw()
    {
        if (isset($_REQUEST['flag']) && (Session::get('group_id') == GROUP_ID_AN_NINH_SHOP || check_system_user_permission('tracuunhansu'))) {
            $admin_groups_system = true;
            $action['content'] = "Xem chi tiết tài khoản ". Url::sget('id');
            $action['type'] = 2;
            if ( defined('LOG_V2') && !empty(LOG_V2)) {
                addLogMongoSecurity($action);
            } else {
                addLogSecurityMysql($action);
            }
        } else {
            $admin_groups_system = User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY);
        }
		$this->map['atv_privilege_code'] = [];
		$this->map['account_group_admins'] = [];
		$this->map['expired_date'] = '';
        $this->map['mobiletype_list'] = MOBILE_TYPES;
        $this->map['mobile_patterns'] = MOBILE_REGEX_PATTERNS;
        $mobile = null;
		$account_id = Url::sget('id');
		$owner = false;
        $owner_account_id = DB::fetch('select code from `groups` where id='.Session::get('group_id'),'code');
        if($owner_account_id == $account_id){
            $owner = true;
        }
        $account_id = Url::sget('id');
        $account = DB::select('account','id="'.$account_id.'"');
        if( URL::get('cmd')=='edit'
            and $row=DB::select('party','user_id="'.$account_id.'" and type="USER"')
            and $account = DB::select('account','id="'.$account_id.'" '.($admin_groups_system?'':' and group_id='.Session::get('group_id')).'')
            and $user = DB::fetch('SELECT users.id, users.extension,users.identity_card,users.identity_card_front,users.identity_card_back,users.phone,
            temp_zone_id, temp_address, address, fb_link, id_card_issued_by, id_card_issued_date
            FROM users WHERE username="'.$account_id.'"')

        ) {

            $user_images = DB::fetch_all('SELECT * FROM user_images WHERE user_id="'.$user['id'].'"');

            $row['temp_zone_id'] = $user['temp_zone_id'];
            $row['temp_address'] = $user['temp_address'];
            $row['fb_link'] = $user['fb_link'];
            $row['id_card_issued_by'] = $user['id_card_issued_by'];
            $row['id_card_issued_date'] = preg_replace_callback('~(\d{4})-(\d{2})-(\d{2}).*~', function($matches){
                return sprintf('%d/%d/%d', $matches[3],$matches[2], $matches[1]);
            }, $user['id_card_issued_date']);

            foreach ($user_images as $user_image) {
                switch ($user_image['type']) {
                    case ImageType::HO_SO_XIN_VIEC:
                        $row['job_application'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::SO_HO_KHAU:
                        $row['registration_book'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::HOP_DONG_HOP_TAC:
                        $row['contract'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::CAM_KET:
                        $row['commitment'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::KHAI_SINH:
                        $row['birth_certificate'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::GIAY_KHAM_SUC_KHOE:
                        $row['health_certification'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::BANG_CAP:
                        $row['diploma'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                    case ImageType::CAM_KET_BAO_MAT_TT:
                        $row['information_security'][] = [
                            'url' => $user_image['image_url'],
                            'hash' => UserAdmin::hash($user_image['image_url'])
                        ];
                        break;

                }
            }

            $row['job_application'] = !empty($row['job_application']) ? json_encode($row['job_application']) : '';
            $row['registration_book'] = !empty($row['registration_book']) ? json_encode($row['registration_book']) : '';
            $row['contract'] = !empty($row['contract']) ? json_encode($row['contract']) : '';
            $row['commitment'] = !empty($row['commitment']) ? json_encode($row['commitment']) : '';
            $row['birth_certificate'] = !empty($row['birth_certificate']) ? json_encode($row['birth_certificate']) : '';
            $row['health_certification'] = !empty($row['health_certification']) ? json_encode($row['health_certification']) : '';
            $row['diploma'] = !empty($row['diploma']) ? json_encode($row['diploma']) : '';
            $row['information_security'] = !empty($row['information_security']) ? json_encode($row['information_security']) : '';

            $row['address'] = $user['address'];
            $row['phone'] = $user['phone'];
            $row['extension'] = $user['extension'];
            $row['identity_card'] = $user['identity_card'];
            $row['identity_card_front'] = $user['identity_card_front'];
            $row['identity_card_back'] = $user['identity_card_back'];
            if($owner and Session::get('user_id') != $owner_account_id and !$admin_groups_system){
                Url::js_redirect(true,'Bạn không có quyền sửa tài khoản sở hữu');
            }
			$group = DB::select('groups','id='.$account['group_id']);
			$row['id'] = $account['id'];
            $row['user_id'] = $user['id'];
			$row['account_group_id'] = $account['account_group_id'];
			$row['fb_page_id'] = DB::fetch('select id from fb_pages where account_id="'.$row['user_id'].'" limit 0,1','id');
			$row['join_date'] = $account['create_date'];
			$row['expired_date'] = $group['expired_date']?$group['expired_date']:'';
			$row['active'] = $account['is_active'];
			$row['block'] = $account['is_block'];
			$group_id = $row['group_id'] = $account['group_id'];
			$row['group_name'] = $group['code'];
			$row['master_group_id'] = $group['master_group_id'];
			$row['account_type'] = $group['account_type'];
			$row['admin_group'] = $account['admin_group'];
            $mobile = $user['phone'];

            if($row['birth_date']<>'0000-00-00')
            {
                $row['birth_date'] = Date_Time::to_common_date($row['birth_date']);
            }
            else
            {
                $row['birth_date'] = '';
            }
            if($row['join_date']<>'0000-00-00')
            {
                //$row['join_date'] = Date_Time::to_common_date($row['join_date']);
            }
            else
            {
                $row['join_date'] = '';
            }
            unset($row['password']);
            foreach($row as $key=>$value)
            {
                if(!isset($_POST[$key]))
                {
                    $_REQUEST[$key] = $value;
                }
            }
            $edit_mode = true;
            //////
            if($row['group_id']){
                if($user_id = DB::fetch('select id from users where username="'.$row['id'].'" and group_id='.$row['group_id'].'','id')){
                    $sql = '
                    SELECT
                        users_roles.*
                    FROM
                        users_roles
                    LEFT JOIN vaccination on users_roles.user_id=vaccination.user_id
                    WHERE
                        users_roles.user_id='.$user_id.'
                    GROUP BY
                        users_roles.id
                    order by
                        users_roles.id  DESC';
                    $items = DB::fetch_all($sql);
                    $arr=array();
                    foreach ($items as $key => $value) {
                        array_push($arr,$value['role_id']);
                    }

                    // Thông tin tiêm chủng
                    $vaccination = $this->getVaccination($user_id);

                    $this->map['atv_privilege_code'] = $arr;
                    $sql = '
                    SELECT
                        account_group_admin.*
                    FROM
                        account_group_admin
                    WHERE
                        account_group_admin.user_id='.$user_id.'
                    GROUP BY
                        account_group_admin.id
                    order by
                        account_group_admin.id  DESC';
                    $items = DB::fetch_all($sql);
                    $arr=array();
                    foreach ($items as $key => $value) {
                        array_push($arr,$value['account_group_id']);
                    }
                    $this->map['account_group_admins'] = $arr;
                }
            }
        }
        else
        {
            if(!Url::get('expired_date')){
                //$_REQUEST['expired_date'] = date('Y-m-').(intval(date('d'))+7);
                $_REQUEST['expired_date'] = date('Y-m-d',strtotime('+7 days'));
            }
			$edit_mode = false;
			$group_id = Session::get('group_id');
			if(!Url::get('id') and $prefix_account = DB::fetch('select prefix_account from `groups` where id='.Session::get('group_id'),'prefix_account')){
				$_REQUEST['id'] = $prefix_account;
			}
			$this->map['atv_privilege_code'] = [];
			$this->map['account_group_admins'] = [];
		}
        $this->map['mobile'] = $mobile;

        $_REQUEST['vaccination_count'] = $vaccination['count'] ?? 0;
        $_REQUEST['vaccination_status'] = $vaccination['status'] ?? 0;
        $this->map['vaccination_count_list'] = UserAdmin::getVaccinationCountFields();
        $this->map['vaccination_status_list'] = UserAdmin::getVaccinationStatusFields();
        $this->map['vaccination_note'] = $vaccination['note'] ?? '';

        $this->map['roles_activities'] = UserAdminDB::get_roles($group_id);
        $this->map['account_groups'] = $account_groups = UserAdminDB::get_account_groups($group_id);

        $zone = DB::fetch_all('select id,name from zone where '.IDStructure::direct_child_cond(ID_ROOT).' order by structure_id');
        ///////
        $roles = UserAdminDB::get_roles($group_id);
        $role_id_options = '<option value="">Chọn quyền</option>';
        foreach($roles as $key=>$val){
            $role_id_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['role_id_options'] = $role_id_options;
        ///////
        $account_types =  array(0=>'Tài khoản thường',1=>'Dùng thử',2=>'Tài khoản cũ',3=>'Tài khoản hệ thống');
        $this->map['account_type_list'] = $account_types;
        ///////
        //$groups = UserAdminDB::get_master_groups();
        //$this->map['master_group_id_list'] = array(''=>'Chọn nhóm DN') + MiString::get_list($groups);
        $this->map['account_group_id_list'] = array(''=>'Chọn nhóm tk') + MiString::get_list($account_groups);
        require_once('packages/vissale/modules/FbSetting/db.php');
        $pages = FbSettingDB::get_friendpages('fb_pages.status = 0');
        $this->map['fb_page_id_list'] = array(''=>'Gán vào page') + MiString::get_list($pages,'page_name');
        if($admin_groups_system){
            $master_groups = DB::fetch_all('select id,name from `groups` where account_type=3');
            $this->map['master_group_id_list'] = array(''=>'Chọn nhóm cha') + MiString::get_list($master_groups);
        }

        $props = [
            'name' => 'system_group_id',
            'id' => 'system_group_id',
            'class' => 'form-control',
            'style' => 'font-size:16px;font-weight:bold;color:##56FF08',
        ];
        $this->map['system_group_id'] =  SystemsTree::selectBox(
            null,
            [
                'selected' => URL::iget('system_group_id'),
                'selectedType' => SystemsTree::SELECTED_CURRENT,
                'props' => $props,
                'default' => '<option value="0">------Thuộc hệ thống------</option>'
            ]
        );

        $systems = UserAdminDB::get_systems();
        $this->map['system_group_id_list'] = array(''=>'Thuộc hệ thống') + MiString::get_list($systems);

        $this->map['isAdmin'] = $this->isAdmin;
        $cities = MiString::get_list($zone);
        $this->parse_layout('edit',
            ($edit_mode?$row:array())+
            array(
                'zone_id_list' => [0 => 'Chọn Tỉnh/TP thường trú'] + $cities,
                'temp_zone_id_list' => [0 => 'Chọn Tỉnh/TP tạm trú'] + $cities,
                'gender_list'=>array(0=>'Chưa xác định','1'=>'Nam','2'=>'Nữ')
            )+$this->map
        );

    }

    /**
     * { function_description }
     *
     * @param      <type>  $name   The name
     *
     * @return     array   ( description_of_the_return_value )
     */
    private function parseImagesUrl($name, $validateMax = false, $validateEmpty = false){
        $fieldMap = [
            'job_application' => 'hồ sơ xin việc',
            'contract' => 'hợp đồng hợp tác',
            'commitment' => 'cam kết',
            'registration_book' => 'sổ hộ khẩu',
            'birth_certificate' => 'giấy khai sinh',
            'health_certification' => 'giấy khám sk a3',
            'diploma' => 'bằng cấp',
            'information_security' => 'cam kết bảo mật thông tin',
        ];

        $jsonObject = json_decode(Url::get($name));

        // if((!is_array($jsonObject) || empty($jsonObject)) && $validateEmpty && !$this->isAdmin){
        //     $this->error($name, sprintf('Bạn cần upload ảnh %s.', $fieldMap[$name]), false);

        //     return [];
        // }

        $objects = null;
        if($jsonObject){
            $objects = array_filter($jsonObject, function($object){
                return UserAdmin::hash($object->url) === $object->hash;
            });
        }

        if($validateMax && $objects && count($objects) > self::MAX_NUM_IMAGE && !$this->isAdmin)
            $this->error($name, sprintf('Số lượng ảnh %s nhiều hơn 10', $fieldMap[$name]), false);

        // else if($validateEmpty && empty($objects) && !$this->isAdmin)
        //     $this->error($name, sprintf('Bạn cần upload ảnh %s', $fieldMap[$name]), false);

        return !empty($this->error_messages[$name]) ? [] : ($objects ? $objects : []);
    }


    /**
     * { function_description }
     *
     * @param      <type>  $user        The user
     * @param      <type>  $columnName  The column name
     *
     * @return     bool    ( description_of_the_return_value )
     */
    private function mustUpdateIdCardImage($user, $columnName){
        $data = Url::get($columnName);

        if(!$data){
            if(!$user)
                return false;

            return !empty($user[$columnName]) ? UserAdmin::move_uploaded_temp_file($user[$columnName]) : false;
        }

        return UserAdmin::move_uploaded_temp_file($data);
    }

    /**
     * { function_description }
     *
     * @param      int     $userID    The user id
     * @param      string  $userName  The user name
     *
     * @return     bool    ( description_of_the_return_value )
     */
    private function upsertVaccination(int $userID,string $userName)
    {
        if(!$userID){
            return;
        }

        if($saved = $this->getVaccination($userID)){
            return $this->logUpdateVaccination(
                $this->updateVaccination($userID, $saved),
                $saved,
                $userName
            );
        }

        return $this->logInsertVaccination(
            $this->insertVaccination($userID),
            $userName
        );
    }

    /**
     * Gets the vaccination infomation.
     *
     * @param      int     $userID  The user id
     *
     * @return     array  The vaccination infomation.
     */
    private function getVaccination(int $userID)
    {
        return DB::fetch('SELECT `user_id`, `count`, `status`, `note` FROM vaccination WHERE `user_id` = ' . $userID);
    }

    /**
     * Cập nhật thông tin tiêm chủng
     *
     * @param      int     $userID  The user id
     * @param      array   $saved   The saved
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function updateVaccination(int $userID, array $saved)
    {
        $updateFields = [
            'count'      => URL::getUInt('vaccination_count'),
            'status'     => URL::getUInt('vaccination_status'),
            'note'       => URL::getString('vaccination_note'),
            'updated_by' => $userID,
            'updated_at' => date('Y-m-d h:i:s')
        ];

        return DB::update('vaccination', $updateFields, 'user_id=' . $userID)
            ? $updateFields
            : false;
    }

    /**
     * Thêm thông tin tiêm chủng
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertVaccination(int $userID)
    {
        $insertFields = [
            'user_id'    => $userID,
            'count'      => URL::getUInt('vaccination_count'),
            'status'     => URL::getUInt('vaccination_status'),
            'note'       => URL::getString('vaccination_note'),
            'created_by' => $userID,
            'created_at' => date('Y-m-d h:i:s')
        ];

        return DB::insert('vaccination', $insertFields)
            ? $insertFields
            : false;
    }

    /**
     * Gets the vaccination count.
     *
     * @param      int   $ID     { parameter_description }
     */
    private function getVaccinationCount(int $ID)
    {
        return UserAdmin::getVaccinationCountFields()[$ID] ?? 'unknow';
    }

    /**
     * Gets the vaccination status.
     *
     * @param      int    $ID     { parameter_description }
     *
     * @return     array  The vaccination status.
     */
    private function getVaccinationStatus(int $ID)
    {
        return UserAdmin::getVaccinationStatusFields()[$ID] ?? 'unknow';
    }

    /**
     * Logs an insert vaccination.
     *
     * @param      array   $insertResults  The insert results
     * @param      string  $userName       The user name
     */
    private function logInsertVaccination($insertResults, string $userName)
    {
        if(Session::is_set('debuger_id')) return;

        if(!is_array($insertResults)){
            return;
        }

        $logs = ['Thêm thông tin tiêm chủng tài khoản "<strong>'.$userName.'</strong>":<br>'];

        $logs[] = sprintf(
            '<div><strong>Số mũi</strong> "<span style="color:#1966ff">%s</span>"</div>',
            $this->getVaccinationCount($insertResults['count'])
        );

        $logs[] = sprintf(
            '<div><strong>Trạng thái sức khỏe</strong> "<span style="color:#1966ff">%s</span>"</div>',
            $this->getVaccinationStatus($insertResults['status'])
        );

        $logs[] = sprintf(
            '<div><strong>Ghi chú</strong> "<span style="color:#1966ff">%s</span>"</div>',
            $insertResults['note']
        );

        System::account_log(0, implode('', $logs),MODULE_USERADMIN);
    }

    /**
     * Logs an update vaccination.
     *
     * @param      array   $updateFields  The update fields
     * @param      array   $saved         The saved
     * @param      string  $userName      The user name
     */
    private function logUpdateVaccination($updateFields, array $saved, string $userName)
    {
        if(Session::is_set('debuger_id')) return;

        if(!is_array($updateFields)){
            return;
        }

        $logs = ['Cập nhật thông tin tiêm chủng tài khoản "<strong>'.$userName.'</strong>":<br>'];
        $fields = ['count' => 'Số mũi', 'status' => 'Trạng thái sức khỏe', 'note' => 'Ghi chú'];

        foreach($fields as $fieldName => $fieldTxt){

            if($saved[$fieldName] == $updateFields[$fieldName]){
                continue;
            }

            switch($fieldName){
                case 'count':
                    $old = $this->getVaccinationCount($saved['count']);
                    $new = $this->getVaccinationCount($updateFields['count']);
                    break;

                case 'status':
                    $old = $this->getVaccinationStatus($saved['status']);
                    $new = $this->getVaccinationStatus($updateFields['status']);
                    break;

                default:
                    $old = $saved[$fieldName];
                    $new = $updateFields[$fieldName];
            }

            $logs[] = sprintf(
                '<div><strong>%s</strong> từ "<span style="color:#1966ff">%s</span>" => "<span style="color:#ff641c">%s</span>"</div>',
                $fieldTxt,
                $old,
                $new
            );
        }

        if(count($logs) > 1){
            System::account_log(0, implode('', $logs),MODULE_USERADMIN);
        }
    }
}
?>
