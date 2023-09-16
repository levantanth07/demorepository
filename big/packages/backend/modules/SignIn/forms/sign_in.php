<?php
use RedisClient\RedisClient;

class SignInForm extends Form
{
    private static $redis = null;
    const CSRF_TOKEN_EXPIRED_TIME = 60;
    const WRONG_PASSWORD_COUNTER_KEY_PREFIX = '__tuha_login__';
    const CAPTCHA_KEY_PREFIX = '__tuha_login__captcha__';
    const ENABLE_LIMIT_LOGIN = 1; // bật tắt giới hạn lần đăng nhập
    const THROTE_LOGIN_TIME = 60; // giới hạn lần đăng nhập trong bao nhiêu giây
    const LIMIT_LOGIN_COUNT = 2; // số lần đăng nhập tối đa trong khoảng thời gian

    const LOG_ACCOUNT_UPDATE = 0;
    const LOG_ACCOUNT_LOGIN = 2;

    const NEW_USER = 0;
    const SUPER_ADMIN = 3;

    protected $map;
    public function __construct()
    {
        require_once ROOT_PATH . 'packages/core/includes/common/PhpRedisInstance.php';
        self::$redis = PhpRedisInstance::getInstance();

        Form::Form('SignInForm');
        if (Url::get('do') == 'dang-ky') {
            $this->add('shop_name', new TextType(true, 'Quý Khách vui lòng nhập tên cửa hàng (3 ký tự trở lên)', 3, 50));
            $this->add('full_name', new TextType(true, 'Quý Khách vui lòng nhập họ và tên (3 ký tự trở lên)', 3, 50));
            $this->add('user_id', new TextType(true, 'Quý Khách vui lòng nhập tài khoản đăng nhập', 3, 50));
            $this->add('password', new TextType(true, 'Quý Khách vui lòng nhập mật khẩu (6 ký tự trở lên)', 6, 50));
            $this->add('email', new EmailType(true, 'Quý Khách vui lòng nhập email đúng định dạng'));
            $this->add('phone', new TextType(true, 'Quý Khách vui lòng nhập số điện thoại', 3, 50));
        } else {
            $this->add('user_id', new TextType(true, 'Lỗi nhập tên đăng nhập', 2, 255));
        }
    }

    function on_submit(){
        if($this->check()){
            if( !User::is_login()){
                if(Url::get('do') == 'dang-ky') {
                    return $this->dang_ky($this);
                }

                $this->checkCsrfToken();

                if($this->isShowCaptcha() && $this->isWrongCaptcha()){
                    return $this->handleWrongCaptcha();
                }

                $this->signIn();
            }
        }else{
            if(User::is_login()){
                $username = URL::get('username');
                $sign = new SignInOtherAccountForm;
                $sign->sign_in( $username);
            }
        }
    }

    /**
     * Determines if wrong captcha.
     *
     * @return     bool  True if wrong captcha, False otherwise.
     */
    private function isWrongCaptcha()
    {
        return $_SESSION["captcha"]['code'] !== URL::getString('captcha');
    }

    /**
     *
     */
    private function handleWrongCaptcha()
    {
        return $this->error('captcha','Bạn vui lòng nhập chính xác mã bảo vệ như hình bên!');
    }

    /**
     *
     *
     * @param      string  $username  The username
     *
     * @return     bool
     */
    private function blockUser(string $username)
    {
        if(!$user = $this->isUserExist($username)) {
            return false;
        }

        Query::from('account')->where('id', $username)->update(['is_active' => 0]);

        return $user;
    }

    private function isUserExist($username)
    {
        return DB::fetch('
            select 
                account.id,
                account.group_id
            from 
                account
                inner join party on party.user_id=account.id 
            where 
                (account.id = "'.DB::escape($username).'") 
        ');
    }

    function checkCsrfToken(){

        if(!isset($_SESSION['csrf_token_created_time']) || !$_SESSION['csrf_token_created_time'] || $_SESSION['csrf_token_created_time'] < (time() - SignInForm::CSRF_TOKEN_EXPIRED_TIME)){
            unset($_SESSION['token']);
            URL::js_redirect(true,'Phiên đăng nhập đã hết hạn. Vui lòng thực hiện lại.', ['do']);
            exit;
        }


        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        }
    }
    
    public function dangKy(&$form)
    {
        return;
        $comfirm_code = trim(Url::get('captcha'));
        $captcha = $_SESSION["captcha"]['code'];
        if ($comfirm_code != $captcha) {
            $form->error('captcha', 'Bạn vui lòng nhập chính xác mã bảo vệ như hình dưới !');
            return;
        }
        if ($account_id = Url::get('user_id')
            and $password = Url::get('password')
            and $full_name = Url::get('full_name')
            and $email = Url::get('email')
            and $group_name = Url::get('shop_name')
            and $phone = Url::get('phone')
        ) {
            $row['group_name'] = DB::escape($group_name);
            $row['account_id'] = DB::escape($account_id);
            $row['password'] = DB::escape($password);
            $row['full_name'] = DB::escape($full_name);
            $row['email'] = DB::escape($email);
            $row['phone'] = DB::escape($phone);
            $row['name'] = DB::escape($full_name);
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                if (isset($_COOKIE['ref_account_id']) and $_COOKIE['ref_account_id']) {
                    $account_ref = $_COOKIE['ref_account_id'];
                } else {
                    $account_ref = DB::escape(Url::get('ref'));
                }
                if ($account_ref) {
                    // nếu có tài khoản refer
                    $group_id = 1279; // PAL VIỆT NAM
                    $product_id = 17193;
                    $source_id = 860; //tuha.vn
                    $source_name = 'tuha.vn';
                    $user_id = DB::fetch("SELECT id FROM users WHERE username = '$account_ref' AND group_id = $group_id", "id");
                    if (!empty($user_id)) {
                        $row['from_web'] = 4;
                        // Tạo đơn hàng, kiểm tra SĐT đã tạo đơn trên hệ thống chưa
                        $phone_exist = DB::fetch("SELECT id FROM orders WHERE group_id = $group_id AND mobile = '$phone'", "id");
                        if (!$phone_exist && $product = DB::fetch("SELECT id, name, price FROM products WHERE id = $product_id")) {
                            $price = (int) $product['price'];
                            $note1 = 'Tài khoản: ' . $account_id . ', SHOP: ' . $group_name;
                            $order_id = DB::insert('orders', [
                                'total_qty' => 1,
                                'status_id' => 10, //chua xac nhan
                                'group_id' => $group_id,
                                'customer_name' => $full_name,
                                'user_created' => $user_id,
                                'created' => date('Y-m-d H:i:s'),
                                'source_id' => $source_id,
                                'source_name' => $source_name,
                                'mobile' => $phone,
                                'total_price' => $price,
                                'note1' => $note1,
                            ]);
                            if ($order_id) {
                                DB::insert('orders_products', [
                                    'order_id' => $order_id,
                                    'product_id' => $product_id,
                                    'product_name' => $product['name'],
                                    'product_price' => $price,
                                    'qty' => 1,
                                ]);
                            }
                        }
                    }
                }
                SignInDB::web_register($row);
                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
                //Url::js_redirect(true,'Quý khách đã đăng ký thành công tài khoản QLBH. QLBH sẽ liên hệ Quý khách để xác nhận đăng ký tài khoản và kích hoạt. Cảm ơn Quý khách!');
                setcookie('ref_register_url', 'dang-ky', time() + (15 * 60), "/");
                header('location:/dang-ky-thanh-cong.html');
                exit();
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
                Url::js_redirect(true, 'Có lỗi xảy ra. Quý khách vui lòng thử lại.');
                die();
            }
        }
    }

    public function signIn()
    {
        // check last login ip and time, if within 1 minute, user use same ip, return error
        $ip =  System::get_client_ip_env();

        $username = URL::getString('user_id');
        $password = URL::getString('password');

        if($this->isTooManyLoginRequest($username, $ip)){
            return $this->error('user_id','Quý khách đã đăng nhập quá nhiều lần. Vui lòng thử lại sau.',false);
        }

        if(!$user = $this->attemp($username, $password)){
            return $this->handleWrongUsernameOrPassword($username);
        }

        // Clear các thông tin liên quan đến việc đếm số lần nhập sai password hay hiện captcha
        $this->flushCaptchaSession();
        $this->flushWrongPassCounter($username);

        $group = SignInDB::get_group_info($user['group_id']);
        $this->redirectToZomaIfNecessary($group);
        $this->redirectToBigShopalIfNecessary($group, $user);

        if (empty($user['is_active'])) {
            return $this->error('user_id','Tài khoản của quý khách chưa được kích hoạt.',false);
        }

        elseif (!empty($user['is_block'])) {
            return $this->showNotifyMessageBlockedUser($user);
        }

        if ($this->isMissingUserInformation($user)) {
            $owner = SignInDB::getOwnerShopByMemberUsername($username);
            $message = sprintf(
                'Đăng nhập không thành công do chưa nhập đầy đủ thông tin.
                Bạn vui lòng liên lạc lại chủ sở hữu shop (%s-%s)', $owner['full_name'], $owner['phone']
            );
            return $this->error('missing_user_info', $message, false);
        }

        $re_login = false;

        if($user['session_id'] and $user['session_id'] != Session::id()){
            $this->destroyCurrentSession();
            $this->forceSignoutSession($user['session_id']);
            $this->newCurrentSession();
            $re_login = true;
        }

        Session::set('user_id', $user['id']);
        if ($user['phone_store_id']) {
            Session::set('phone_store_id', $user['phone_store_id']);
        }
        Session::set('admin_group', $user['admin_group']);
        Session::set('account_type', $user['account_type']);
        Session::set('master_group_id', $user['master_group_id']);
        Session::set('group_id', $user['group_id']);
        Session::set('user_data', $user);
        Session::set('user_login',$user['id']);
        Session::set('start_time_login',time());

        // kiem tra thong tin callio
        $this->setSessionCallIO($user);

        // kiem tra thong tin voip24h
        $this->setSessionVoip24h($user);

        $this->markNewUser($user);

        if($this->handleTakePictureOfStaff($user['group_id']) === false) {
            $this->log(self::LOG_ACCOUNT_LOGIN, 'Đăng nhập tài khoản');
        }

        $this->rememberUser($user);

        $this->logLoginAccount($user, $re_login);

        $this->markLoginRequestCounter($username, $ip);

         unset($_SESSION['token']);
        unset($_SESSION['csrf_token_created_time']);

        echo
            $re_login
                ? $this->showReloginContent($user)
                : '<script>window.location="' . $this->getUrlRedirectAfterLoginSuccessIfNecessary($user) . '";</script>';
        exit;
    }


    /**
     * Sets the session call i/o.
     *
     * @param      array  $user   The user
     */
    private function setSessionVoip24h(array $user)
    {
        Session::delete('voip24h_payload');
        if ($this->isNotUseVoip24h($user)) {
            return;
        }

        $group_voip24h_info = json_decode($user['voip24h_info']);
        $user_voip24h_info = json_decode($user['user_voip24h_info']);
        Session::set('voip24h_payload', [
            'server' => 'wss://webrtc.voip24h.vn:8089/ws',
            'domain' => 'webrtc.voip24h.vn',
            'line' => $user_voip24h_info->line,
            'password' => $user_voip24h_info->password
        ]);
    }

    /**
     * Sets the session call i/o.
     *
     * @param      array  $user   The user
     */
    private function setSessionCallIO(array $user)
    {
        Session::delete('callio_payload');
        if ($this->isNotUseCallIO($user)) {
            return;
        }

        $group_callio_info = json_decode($user['callio_info']);
        $user_callio_info = json_decode($user['user_callio_info']);
        Session::set('callio_payload', [
            'host' => $group_callio_info->domain,
            'ext' => $user_callio_info->ext,
            'email' => $user_callio_info->email,
            'password' => $user_callio_info->extPassword
        ]);
    }

    /**
     * Determines whether the specified user is use call i/o.
     *
     * @param      array  $user   The user
     *
     * @return     array  True if the specified user is use call i/o, False otherwise.
     */
    private function isUseCallIO(array $user)
    {
        return $user['integrate_callio']
            && $user['user_integrate_callio']
            && $user['callio_info']
            && $user['user_callio_info'];
    }

    /**
     * Determines whether the specified user is not use call i/o.
     *
     * @param      array  $user   The user
     *
     * @return     bool   True if the specified user is not use call i/o, False otherwise.
     */
    private function isNotUseCallIO(array $user)
    {
        return !$this->isUseCallIO($user);
    }

    /**
     * Determines whether the specified user is use voip 24 h.
     *
     * @param      array  $user   The user
     *
     * @return     array  True if the specified user is use voip 24 h, False otherwise.
     */
    private function isUseVoip24h(array $user)
    {
        return $user['integrate_voip24h']
            && $user['user_integrate_voip24h']
            && $user['voip24h_info']
            && $user['user_voip24h_info'];
    }

    /**
     * Determines whether the specified user is not use voip 24 h.
     *
     * @param      array  $user   The user
     *
     * @return     bool   True if the specified user is not use voip 24 h, False otherwise.
     */
    private function isNotUseVoip24h(array $user)
    {
        return !$this->isUseVoip24h($user);
    }


    /**
     * Determines if too many login request.
     *
     * @param      string  $user   The user
     *
     * @return     bool    True if the specified user is too many login request, False otherwise.
     */
    private function isTooManyLoginRequest(string $user, string $ip)
    {
        if(!SignInForm::ENABLE_LIMIT_LOGIN) {
            return false;
        }

        $keys = self::$redis->keys(
                                $this->getLoginCounterKey($user, $ip, true)
                            );

        return count($keys) >= SignInForm::LIMIT_LOGIN_COUNT;
    }

    /**
     * { function_description }
     *
     * @param      string  $user   The user
     * @param      string  $ip     { parameter_description }
     */
    private function markLoginRequestCounter(string $user, string $ip)
    {
        if(SignInForm::ENABLE_LIMIT_LOGIN){
            self::$redis->set(
                $this->getLoginCounterKey($user, $ip), 
                $this->getLoginCounterData(), 
                self::THROTE_LOGIN_TIME
            );
        }
    }

    /**
     * Gets the login counter key.
     *
     * @param      string  $ip        { parameter_description }
     * @param      string  $username  The username
     *
     * @return     <type>  The login counter key.
     */
    private function getLoginCounterKey(string $user, string $ip, bool $isSearching = false)
    {
        $suffix = $isSearching ? '*' : microtime(true);
        
        return strtolower(
            'login-' . $ip . '-' . $user . '-' . $suffix
        );
    }

    /**
     * Gets the login counter data.
     * Trường hợp muốn bổ sung thông tin vào lần đăng nhập thì thay đổi giá trị trả về của hàm này
     * 
     * @return     string  The login counter data.
     */
    private function getLoginCounterData()
    {
        return ''; //date('Y-m-d H:i:s');
    }

    /**
     * clear session hiện tại
     */
    private function destroyCurrentSession()
    {
        Session::destroy();
        Session::commit();
    }

    /**
     * Xóa session cũ (có thể hiểu là đăng xuất thanh niên ở session kia ra)
     *
     * @param      string  $sessionID  The session id
     */
    private function forceSignoutSession(string $sessionID)
    {
        Session::id($sessionID);
        Session::start();
        Session::destroy();
        Session::commit();
    }

    /**
     * // Thiết lập session mới
     */
    private function newCurrentSession()
    {
        Session::start();
        Session::regenerate_id(true);
    }

    /**
     * Thực thi việc redirect sau khi login thành công nếu cần
     */
    private function getUrlRedirectAfterLoginSuccessIfNecessary(array $user)
    {
        if(URL::get('href')){
            return $_REQUEST['href'];
        }

        if(User::can_admin(MODULE_GROUPSSYSTEM,false)){
            return Url::build('admin-shop',['status'=>1]);
        }

        if(!Session::get('admin_group')){
            return Url::build('admin_orders', UserRole::user()->has('CS') ? ['cmd' => 'care_list'] : []);
        }

        if($user['user_status'] <= self::NEW_USER){
            return Url::build('trang-gioi-thieu');
        }

        if($user['account_type'] == self::SUPER_ADMIN){
            return Url::build('report');
        }

        return Url::build('admin_orders');
    }

    /**
     * Log thông tin đăng nhập, dấu thời gian của user
     *
     * @param      array   $user                The user
     * @param      bool    $re_login            The re login
     */
    private function logLoginAccount(array $user,  bool $re_login)
    {
        $attrs = [
            'last_online_time' => time(),
            'session_id' => Session::id(),
            'last_ip' => System::get_client_ip_env()
        ];

        if(!$re_login){
            unset($_SESSION['token']);
            unset($_SESSION['csrf_token_created_time']);
            $attrs['last_login_time'] = date('Y-m-d H:i:s');
        }

        return Query::newQuery()->from('account')->where('id', $user['id'])->update($attrs);
    }

    /**
     *
     *
     * @param      array  $user   The user
     */
    private function rememberUser(array $user)
    {
        if(Url::get('save_password')){
            setcookie('forgot_user',$user['id'].'_'.Url::get('password'));
        }
    }

    /**
     *
     *
     * @param      string  $message  The message
     */
    private function log(int $logType, string $message, string $accountID = null, int $groupID = null)
    {
        if (Session::get('user_id') === 'zm.khoatest' || Session::is_set('debuger_id')){
            return;
        }

        User::update_account_log($logType, $message, $groupID, '', $accountID);
    }

    /**
     *
     */
    private function handleTakePictureOfStaff(int $groupID)
    {
        if(!empty($value = GroupOption::group($groupID)->option('chup_anh_nhan_vien'))) {
            Session::set('chup_anh_nhan_vien', $value);
        }
        
        return !!$value;
    }

    /**
     *
     *
     * @param      array  $user   The user
     */
    private function markNewUser(array $user)
    {
        if($user['user_status'] < 1) {
            $this->forceChangePassImmediate($user['id']);
        }

        if($user['user_status'] <= 1) {
            setcookie('new_user',true,(time()+86400*7));
            DB::update('users',['status'=>$user['user_status'] + 1],'username="'.$user['id'].'"');
        }
    }

    /**
     *
     *
     * @param      string  $username  The username
     */
    private function forceChangePassImmediate(string $username)
    {
        Query::from('account')->where('id', $username)->update(['password_updated_at' => User::RESET_PASS_IMMEDIATE_TIME]);
    }

    /**
     *
     *
     * @param      array  $group  The group
     */
    private function redirectToZomaIfNecessary(array $group)
    {
        if($_SERVER['HTTP_HOST']!='zoma.shopal.vn' and $group['system_group_id']==4){
            echo '<script>alert("Bạn vui lòng vào qua hệ thống ZOMA!");window.location="https://zoma.shopal.vn/dang-nhap";</script>';
            exit();
        }
    }

    /**
     * { function_description }
     *
     * @param      array  $group  The group
     * @param      array  $user   The user
     */
    private function redirectToBigShopalIfNecessary(array $group, array $user)
    {
        if($this->isBigShopal($user)){
            return;
        }

        $structure_id = DB::structure_id('groups_system',$group['system_group_id']);
        $cd_structure_id = DB::structure_id('groups_system', 2);

        if($group['system_group_id'] == 2 || IDStructure::is_child($structure_id,$cd_structure_id)) {
            echo '<script>alert("Bạn vui lòng vào qua hệ thống cộng đồng big.shopal.vn!");window.location="https://big.shopal.vn/dang-nhap";</script>';
            exit();
        }
    }

    /**
     * Determines if big shopal.
     *
     * @param      array  $user   The user
     *
     * @return     bool   True if big shopal, False otherwise.
     */
    private function isBigShopal(array $user)
    {
        return 'big.shopal.vn' === $_SERVER['HTTP_HOST'] 
            || 'big02.shopal.vn' === $_SERVER['HTTP_HOST'] 
            || 'dev-big.shopal.vn' === $_SERVER['HTTP_HOST'] 
            || is_testing_account($user['id']);
    }

    /**
     * Determines if not big shopal.
     *
     * @return     bool  True if not big shopal, False otherwise.
     */
    private function isNotBigShopal(array $user)
    {
        return !$this->isBigShopal($user);
    }

    /**
     * Shows the notify message blocked user.
     *
     * @param      string  $username  The username
     *
     * @return     <type>
     */
    private function showNotifyMessageBlockedUser(string $username)
    {
        $this->flushWrongPassCounter($username);
        $this->flushCaptchaSession();

        return $this->error('password','Tài khoản của bạn đã bị khóa, vui lòng liên hệ quản lí của bạn để kích hoạt lại tài khoản.',false);
    }

    /**
     * Determines whether the specified user is missing user information.
     *
     * @param      array  $user   The user
     *
     * @return     bool   True if the specified user is missing user information, False otherwise.
     */
    private function isMissingUserInformation(array $user)
    {
        // Nếu là admin hoặc chủ sở hữu shop thì được "ưu tiên" bỏ qua :))
        if (SignInDB::isAdmin($user['username']) || SignInDB::isGroupOwner($user['username'])) {
            return false;
        }

        // Thiếu ảnh hồ sơ xin việc, sổ hộ khẩu, hợp đồng hợp tác
        // if (SignInDB::isMissingUserImagesOfUserID($user['user_id'])) {
        //     return true;
        // }

        $party = SignInDB::getPartyByUsername($user['username']);

        return !trim($party['full_name'])
        || !trim($party['birth_date'])
        || !trim($party['phone'])
        || !trim($party['address'])
        || !trim($party['zone_id'])
        || !trim($user['fb_link']);
    }

    /**
     *
     *
     * @param      string  $username  The username
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function flushWrongPassCounter(string $username)
    {
        if($keys = $this->getKeysWpcByUsername($username)) {
            return self::$redis->del($keys);
        }
    }

    /**
     *
     *
     * @return     <type>
     */
    private function handleWrongUsernameOrPassword(string $username)
    {
        $this->increaseCaptchaCount();

        $count = $this->getWrongPassCounter($username);
        if($count < 4) {
            $this->setWrongPassCounter($username);

            return $this->error('password','Tài khoản hoặc mật khẩu không đúng. Vui lòng kiểm tra lại. Bạn còn <b>' . (4-$count) . '</b> lần để thực hiện.',false);
        }

        $this->showNotifyMessageBlockedUser($username);

        if($account = $this->blockUser($username)){
            $this->log(
                self::LOG_ACCOUNT_UPDATE,
                'Chỉnh sửa tài khoản "<b>' . $username . '</b>":<br>' .
                'Kích hoạt từ "<b>Bật kích hoạt</b>" sang "<b>Tắt kích hoạt</b>"<br>' .
                'Nhập sai mật khẩu nhiều lần',
                $account['id'],
                $account['group_id']
            );
        }
    }

    /**
     * Gets the wrong pass counter.
     *
     * @param      string  $username  The username
     *
     * @return     <type>  The wrong pass counter.
     */
    private function getWrongPassCounter(string $username)
    {
        return count(
            $this->getKeysWpcByUsername($username)
        );
    }

    /**
     * Gets the wpc by username.
     *
     * @param      string  $username  The username
     *
     * @return     <type>  The wpc by username.
     */
    private function getWpcByUsername(string $username)
    {
        return self::$redis->get(
            $this->getWpcKey($username)
        );
    }

    private function getKeysWpcByUsername(string $username)
    {
        return self::$redis->keys(
            $this->getWpcKey($username, true)
        );
    }

    /**
     * Sets the wrong pass counter.
     *
     * @param      string  $username  The username
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function setWrongPassCounter(string $username)
    {
        return self::$redis->set(
            $this->getWpcKey($username),
            $this->prepareWpcData($username),
            WRONG_PASSWORD_COUNTER_TIMEOUT
        );
    }

    /**
     * Nếu cần lưu trữ thông tin về lần đăng nhập sai thì lưu vào đây
     *
     * @param      string  $username  The username
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function prepareWpcData(string $username)
    {
        return json_encode([]);
    }

    /**
     * Gets the wpc key.
     *
     * @param      string  $username  The username
     * @param      bool    $allKeys   All keys
     *
     * @return     <type>  The wpc key.
     */
    private function getWpcKey(string $username = '*', bool $allKeys = false)
    {
        return sprintf(
            "%s__%s__%s",
            self::WRONG_PASSWORD_COUNTER_KEY_PREFIX,
            mb_strtolower($username),
            $allKeys ? '*' : microtime(true)
        );
    }


    /**
     * Determines if show captcha.
     *
     * @return     bool  True if show captcha, False otherwise.
     */
    private function isShowCaptcha()
    {
        return $this->getCaptchaCounter() >= 3;
    }

    /**
     *
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function flushCaptchaSession()
    {
        return self::$redis->del(
            $this->getCaptchaCounterKey()
        );
    }

    /**
     * Increases the captcha count.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function increaseCaptchaCount()
    {
        return self::$redis->incr(
            $this->getCaptchaCounterKey()
        );
    }

    /**
     * Gets the captcha counter.
     *
     * @return     <type>  The captcha counter.
     */
    private function getCaptchaCounter()
    {
        return self::$redis->get(
            $this->getCaptchaCounterKey()
        );
    }

    /**
     * Gets the captcha counter key.
     *
     * @return     <type>  The captcha counter key.
     */
    private function getCaptchaCounterKey()
    {
        return sprintf(
            '%s__%s',
            self::CAPTCHA_KEY_PREFIX,
            Session::id()
        );
    }

    /**
     * { function_description }
     *
     * @param      <type>  $username  The username
     * @param      <type>  $password  The password
     */
    private function attemp($username, $password)
    {
        return Query::from('account')
            ->join('users', 'users.username', '=', 'account.id')
            ->join('party', 'party.user_id', '=', 'account.id')
            ->join('groups', 'groups.id', '=', 'account.group_id')
            ->where('account.id', $username)
            ->where('account.type', 'USER') 
            ->where('account.password', User::encode_password($password))
            ->first([
                'account.id',
                'account.session_id',
                'account.last_ip',
                'account.password',
                'account.type',
                'account.group_id',
                'account.admin_group',
                'account.create_date',
                'account.last_online_time',
                'account.is_active',
                'account.is_block',
                'account.group_id',
                'account.password_updated_at',
                'party.kind',
                'party.email',
                'full_name',
                'party.image_url as avatar_url',
                'groups.account_type',
                'groups.master_group_id',
                'groups.phone_store_id',
                'groups.integrate_callio',
                'groups.integrate_voip24h',
                'users.status as user_status',
                'users.rated_point',
                'users.rated_quantity',
                'users.id as user_id',
                'users.username',
                'users.extension',
                'users.identity_card',
                'users.identity_card_front',
                'users.identity_card_back',
                'users.phone',
                'users.temp_zone_id',
                'users.temp_address',
                'groups.callio_info',
                'users.callio_info as user_callio_info',
                'users.address',
                'users.fb_link',
                'users.id_card_issued_by',
                'users.id_card_issued_date',
                'users.integrate_callio as user_integrate_callio',
                'users.integrate_voip24h as user_integrate_voip24h',
                'users.id as users_user_id'
            ]);
    }

    public function draw()
    {
        //echo $_COOKIE['my_last_session_id'];
        if (User::is_login()) {
            if(Url::get('cmd')=='debug' && in_array($_SESSION['user_id'], SYSTEM_DEBUG_ACCOUNTS)){
                $this->map = array();
                $layout = 'sign_in_other_account';
            }else{
                $this->map = $_SESSION['user_data']; //User::$current->data;
                $layout = 'account_info';
            }
            
        } else {
            $this->map = [];
            $layout = 'sign_in';
            if (System::check_user_agent()) {
                $layout = 'm_sign_in';
                $this->map['abc'] = 1;
            }
            if (Url::get('do') == 'dang-ky') {
                $layout = 'register';
            }
        }

        $this->map['is_show_captcha'] = $this->isShowCaptcha();
        $_SESSION['csrf_token_created_time'] = time();
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        $this->parse_layout($layout, $this->map);
    }

    /**
     * Shows the relogin content.
     *
     * @param      array  $user   The user
     */
    private function showReloginContent(array $user)
    {   
        return '
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta http-equiv="content-language" content="vi" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Phần mềm quản lý bán hàng Online Tốt Nhất</title>
            <link href="assets/standard/css/bootstrap.min.css" rel="stylesheet">
            <link href="assets/standard/css/font-awesome.min.css" rel="stylesheet">
            <link href="assets/standard/css/animate.min.css" rel="stylesheet">
            <link href="assets/standard/css/main.css?v=28102019" rel="stylesheet">
            <script src="assets/standard/js/jquery.js"></script>
            <script src="assets/standard/js/bootstrap.min.js"></script>
            </head>
            <body style="background:#efefef">
                <div class="container">
                    <br>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="panel panel-default">
                                <div class="panel-body text-center">
                                    <img src="/assets/standard/images/tuha_logo.png?v=03122021" width="100"><br>
                                    <div class="alert">
                                        Tài khoản của bạn <strong>đã đăng nhập</strong> ở địa chỉ IP: <span style="color:#f00;background: #fbff55;font-size:20pt">' . $user['last_ip'] . '</span>
                                    </div>
                                    <div class="alert alert-danger">Hệ thống đã thoát tài khoản ở địa chỉ trên để đảm bảo an toàn. Vui lòng nhấn nút đăng nhập ngay.</div>
                                    <div style="text-align:center;">
                                        <a class="btn btn-warning btn-lg" href="' . $this->getUrlRedirectAfterLoginSuccessIfNecessary($user) . '">ĐĂNG NHẬP NGAY</a>
                                    </div>
                                    <hr>
                                    <span class="alert alert-warning">Trên iOS quý khách vui lòng đăng nhập lại.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
            </body>
            </html>';
    }
}
