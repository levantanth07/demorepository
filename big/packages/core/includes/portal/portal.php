<?php
require_once ROOT_PATH . 'packages/core/includes/common/ImageType.php';

class Portal{
	const POPUP_SHOWING = 1;
	const POPUP_CLOSED = 0;
	const TIME_TO_UPDATE_ONLINE_STATUS = 600;

	static $current = false;
	static $extra_header = '';
	static $page_gen_time = 0;
	static $page = false;
	static $meta_keywords = '';
	static $meta_description = '';
	static $document_title = '';
	static $canonical = '';
	static $image_url = '';
	function __construct(){
	}
	static function register_module($row_or_id, &$module){
		if(is_numeric($row_or_id)){
			$id=$row_or_id;
		}
		elseif(isset($row_or_id['id'])){
			$id = $row_or_id['id'];
		}else{
			System::halt();
		}
		if(is_numeric($row_or_id)){
			DB::query('
				select
					id, name, package_id
				from
					module
				where
					id = '.$row_or_id);
			$row = DB::fetch();
			if(!$row){
				System::halt();
			}
		}else{
			$row = $row_or_id;
		}
		require_once 'packages/core/includes/portal/package.php';
		$class_fn = get_package_path($row['package_id']).'module_'.$row['name'].'/class.php';
		require_once $class_fn;
		$module = new $row['name']($row);
		$module->package = &$GLOBALS['packages'][$row['package_id']];
	}

    static function check_account_active()
    {
        $accountInfo = self::get_account_info();
        if (!empty($accountInfo)) {
            if ($accountInfo['account_is_active'] != 1) {
                self::signOutSystem();
                if (Url::get('page') != 'dang-nhap') {
                    Session::set('deactiveUser', 1);
                    Url::redirect('dang-nhap');
                }
            }
        }
    }

    static function get_account_info()
    {
        if (Session::get('user_id') && Session::get('user_id') != 'guest') {
            $sql = "SELECT account.is_active AS account_is_active, groups.expired_date, groups.active AS group_is_active
                FROM account INNER JOIN groups ON account.group_id = groups.id 
                WHERE account.id = '" . Session::get('user_id') . "'";
            return DB::fetch($sql);
        }

        return false;
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public static function checkPasswordChanged()
    {   
        if(!User::is_login()){
            return false;
        }

        if(!$new = User::getAccount()){
        	return false;
        }

        $old = $_SESSION['user_data'];
        if($new && $old['id'] === $new['id'] && $old['password'] !== $new['password']){
            Session::set('changedPassword', 1);
            self::signOutSystem();
            if (Url::get('page') != 'dang-nhap') {
                Url::redirect('dang-nhap');
            }
        }
    }


    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private static function checkResetPassPeriodic()
    {
    	if(!User::is_login() || URL::is('sign_out') || URL::is('dang-nhap')){
    		return;
    	}

    	$passwordUpdatedAt = Carbon\Carbon::parse(User::getAccount()['password_updated_at']);
    	if($passwordUpdatedAt->eq(User::RESET_PASS_IMMEDIATE_TIME) && URL::not('trang-ca-nhan', ['cmd'=>'change_pass'])) {
    		return self::redirectToChangePass();
    	}

    	$resetPassPeriodic = GroupOption::group()->option('reset_pass_periodic');
    	if(!$resetPassPeriodic) {
    		return;
    	}

    	$now = Carbon\Carbon::now();
    	if($passwordUpdatedAt->addDays($resetPassPeriodic)->lt($now) && URL::not('trang-ca-nhan', ['cmd'=>'change_pass'])) {
    		return self::redirectToChangePass();
    	}
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private static function redirectToChangePass()
    {
    	setcookie('ResetPassPeriodic', self::POPUP_SHOWING, time() + 3600);
 
    	return URL::redirect('trang-ca-nhan', ['cmd'=>'change_pass']);
    }


	//Chay portal
	static function run(){
		// redirect sang trang đăng nhập nếu chưa đăng nhập
		if(!User::is_login() && URL::getString('page') !== 'dang-nhap'){
            URL::redirect('dang-nhap');
        }

        self::checkResetPassPeriodic();

        if(!Session::is_set('debuger_id')){
			self::validateSession();

			if(!self::validateUserInformation()){
				self::forceSignOutUserUnvalidateInfo();
			}
			self::checkPasswordChanged();

			self::check_account_active();
			if(!Session::is_set('debuger_id')){
				$last_online_time = Session::get('last_online_time');
				$time =  time();
				if(!$last_online_time or $last_online_time <  ($time - self::TIME_TO_UPDATE_ONLINE_STATUS)){
					$user_id = Session::get('user_id');
					Session::set('last_online_time', $time);
					DB::update('account',array('last_online_time' => $time),'id="'.$user_id.'"');
				}
			} 
        }

		if(!Session::is_set('portal') or !Session::get('portal')){
			$portal = DB::fetch('select id from `account` where type="PORTAL" and is_active = 1 and id = "#'.addslashes($_REQUEST['portal']).'"');
			Session::set('portal',$portal);
		}
		if(Session::is_set('portal') and Session::get('portal')){
			if(!Session::get('portal','cache_setting')){
				require_once 'packages/core/includes/system/make_account_setting_cache.php';
				make_account_setting_cache(Session::get('portal','id'));
				Session::set('portal', DB::select('account','id="'.Session::get('portal','id').'"'));
			}
			eval('Portal::$current->settings='.Session::get('portal','cache_setting'));
			define('PORTAL_ID',Session::get('portal','id'));
			define('REWRITE',Portal::get_setting('rewrite'));
			define('USE_CACHE',Portal::get_setting('use_cache'));
			if(!Session::is_set('language_changed')){
				Session::set('language_id',Portal::get_setting('language_default'));
			}
			require_once 'cache/language_'.Portal::language().'.php';
			if(Portal::get_setting('is_active') or User::can_admin(MODULE_NEWSADMIN,ANY_CATEGORY) or (Url::get('page')=='dang-nhap')){
				if(Url::get('page') and $page = DB::fetch('select *, title_'.Portal::language().' as title from page where name="'.addslashes(Url::sget('page')).'"')){
					$_REQUEST['page_name'] = $page['title'];
					Portal::run_page($page,$page['name'],$page['params']);
				}
				else{
					if($page = DB::fetch('select * from page where name="home"')){
						Portal::run_page($page,$page['name'],$page['params']);
					}
				}
			}else{
				echo '<h3 style="padding:10px;border:1px solid #CCC;float:left;border-radius:10px;background:#EFEFEF;margin:10px;">'.Portal::get_setting('notification_when_interrption').'</h3>';
			}
		}
		Session::end();
		DB::close();
	}

	static function signOutSystem(){
		if (User::is_login()) {
			if (Session::is_set('user_id')){
				$id=Session::get('user_id');
				DB::update('account',array('last_online_time'=>time(),'session_id'=>''),'id="'.$id.'"');
				setcookie('user_id',"",time()-3600);
				if(!Session::is_set('openMdlExpired')) {
					Session::delete('user_id');
				}
				if(isset($_SESSION['exel_items'])){
					unset($_SESSION['exel_items']);
				}
				if(isset($_SESSION['group_id'])){
					unset($_SESSION['group_id']);
				}
				if(isset($_SESSION['admin_group'])){
					unset($_SESSION['admin_group']);
				}
				if(isset($_SESSION['account_type'])){
					unset($_SESSION['account_type']);
				}
				if(isset($_SESSION['master_group_id'])){
					unset($_SESSION['master_group_id']);
				}
			}
		}
	}

    /**
     * Signout user nếu session phiên hiện tại và db không khớp nhau
     */
    private static function validateSession()
    {
        $currentSession = session_id();

        $fmt = 'select `session_id` from `account` where id = "%s"';
        $sql = sprintf($fmt, DB::escape(Session::get('user_id')));
        $savedSession = DB::fetch($sql, 'session_id');

        $currentSession != $savedSession && self::signOutSystem();
    }

    /**
     * { function_description }
     */
    private static function forceSignOutUserUnvalidateInfo(){
        // Nên gọi trước signOutSystem để tránh lỗi không mong muốn khi xóa cookie phiên
        $username = Session::get('user_data', 'id');

        self::signOutSystem();

        if (Url::get('page') == 'dang-nhap') {
            return;
        }

        Session::set('openValidateUserInfo', 1);

        if($username && $username != 'guest' && $owner = self::getOwnerShopByMemberUsername($username)){
            Session::set('ownerPhone', $owner['phone']);
            Session::set('ownerFullName', $owner['full_name']);
        }
        
        Url::redirect('dang-nhap');
    }

	/**
	 * { function_description }
	 *
	 * @return     bool  ( description_of_the_return_value )
	 */
	private static function validateUserInformation(){
		$userID = Session::get('user_data', 'user_id');
		$username = Session::get('user_data', 'id');

		if (!$username || $username == 'guest' || !$userID) {
			return true;
		}

		// Nếu là admin hoặc chủ sở hữu shop thì được "ưu tiên" bỏ qua :))
        if(self::isAdmin($username) || self::isGroupOwner($username)){
            return true;
        }

        // Thiếu ảnh hồ sơ xin việc, sổ hộ khẩu, hợp đồng hợp tác
        // if(self::isMissingUserImagesOfUserID($userID)){
        //     return true;
        // }

        $user = self::getUser($username);

        return trim($user['full_name'])
            && trim($user['birth_date'])
            && trim($user['phone'])
            && trim($user['address'])
            && trim($user['zone_id'])
            && trim($user['fb_link']);
            // && trim($user['id_card_issued_by'])
            // && trim($user['id_card_issued_date'])
            // && trim($user['identity_card'])
            // && trim($user['identity_card_back'])
            // && trim($user['identity_card_front']);
	}

    /**
     * Gets the owner shop by member username.
     *
     * @param      string  $username  The username
     *
     * @return     <type>  The owner shop by member username.
     */
    private static function getOwnerShopByMemberUsername(string $username){
        $fmt = 'SELECT party.phone, party.full_name, party.user_id 
                FROM `groups` 
                JOIN account ON account.group_id = groups.id 
                JOIN users ON users.username = groups.code 
                JOIN party ON party.user_id = users.username 
                WHERE account.id = "%s"';
        $sql = sprintf($fmt, DB::escape($username));

        return DB::fetch($sql);
    }

	/**
	 * Gets the user.
	 *
	 * @param      string  $username  The username
	 *
	 * @return     <type>  The user.
	 */
	private static function getUser(string $username){
		$fmt = '
            SELECT 
                account.id,account.session_id,account.last_ip,
                account.password,account.type,account.group_id,account.admin_group,
                account.create_date,account.last_online_time,
                account.is_active,party.kind,
                account.group_id,
                party.email,full_name,
                party.image_url as avatar_url,
                `party`.`full_name`,
				`party`.`birth_date`,
				`party`.`phone`,
				`party`.`address`,
				`party`.`zone_id`,
                `users`.`status` as `user_status`,
                `users`.`rated_point`,
                `users`.`rated_quantity`,
                `users`.`id` as `user_id`,
                `users`.`username`,
                `users`.`extension`,
                `users`.`identity_card`,
                `users`.`identity_card_front`,
                `users`.`identity_card_back`,
                `users`.`temp_zone_id`,
                `users`.`temp_address`,
                `users`.`fb_link`,
                `users`.`id_card_issued_by`,
                `users`.`id_card_issued_date`
            FROM  `account` 
                JOIN users ON users.username = `account`.id
                JOIN party ON party.user_id=`account`.id 
            WHERE
                account.id = "%s"
                AND `account`.type="USER" ';
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

	static function run_page($row, $page_name, $params=false){
		Counter::check_session();
		$postfix = $params?'.'.$params:'';
		$page_file = ROOT_PATH.'cache/pages/'.$page_name.$postfix.'.cache.php';
		if(file_exists($page_file) and USE_CACHE){
			require_once $page_file;
		}else{
			require_once 'packages/core/includes/portal/generate_page.php';
			$generate_page = new GeneratePage($row);
			$generate_page->generate();
			$page_name = $row['name'];
		}
	}
	static function template($portal = true){
		if($portal){
			return 'assets/'.substr(PORTAL_ID,1).'/';
		}
		return 	'assets/default/';
	}
	static function template_css($portal = 'default'){
		return 'assets/'.$portal.'/';
	}
	static function template_js($package= 'core'){
		return 'packages/'.$package.'/includes/js/';
	}
	static function service($service_name){
		$services = Portal::get_setting('registered_services');
		return isset($services[$service_name]);
	}
	static function language($name=false){
		if($name){
			if(isset($GLOBALS['all_words']['[[.'.$name.'.]]'])){
				return $GLOBALS['all_words']['[[.'.$name.'.]]'];
			}else{
				$languages = DB::select_all('language');
				$row = array();
				foreach($languages as $language){
					$row['value_'.$language['id']] = ucfirst(str_replace('_',' ',$name));
				}
				DB::insert('word',$row + array(
					'id'=> DB::escape(DataFilter::removeXSSinHtml($name)),
					'package_id'=>Module::$current->data['module']['package_id']
				),1);
				Portal::make_word_cache();
				return $name;
			}
		}
		if(Session::is_set('language_id') and Session::get('language_id')!=''){
			return Session::get('language_id');
		}
		return 1;
	}
	static function get_setting($name, $default=false, $user_id = false){
		if(!$user_id){
			if(isset(User::$current->settings[$name])){
				if(User::$current->settings[$name] == '@VERY_LARGE_INFORMATION'){
					if($setting = DB::select('account_setting','setting_id="'.DB::escape($name).'" and account_id="'.User::id().'"')){
						return $setting['value'];
					}
				}else{
					return User::$current->settings[$name];
				}
			} else if(isset(Portal::$current->settings[$name])){
				if(Portal::$current->settings[$name] == '@VERY_LARGE_INFORMATION'){
					if($setting = DB::select('account_setting','setting_id="'.DB::escape($name).'" and account_id="'.PORTAL_ID.'"')){
						return $setting['value'];
					}
				}else{
					return Portal::$current->settings[$name];
				}
			}
		}else{
			if($setting = DB::select('account_setting','setting_id="'.DB::escape($name).'" and account_id="'.DB::escape($user_id).'"')){
				return $setting['value'];
			}
			return $default;
		}
		return $default;
	}
	static function use_service($name){
		return isset(Portal::$current->services[$name]);
	}
	static function settting_(){
		Url::update_system_();
	}
	static function set_setting($name, $value,$user_id=false,$account_type = 'USER'){
		if($setting = DB::select('setting','id="'.$name.'"')){
			if($user_id==false){
				if($setting['account_type']=='USER'){
					$account_id = Session::get('user_id');
				}else{
					$account_id = Session::get('portal','id');
				}
			}else{
				$account_id = $user_id;
			}
			if(DB::fetch('select * from account_setting where account_id="'.addslashes($account_id).'" and setting_id="'.addslashes($name).'"')){
				DB::update('account_setting',
					array(
						'value'=>$value
					),
					'account_id="'.addslashes($account_id).'" and setting_id="'.addslashes($name).'"'
				);
			}else{
				DB::insert('account_setting',
					array(
						'account_id'=>$account_id,
						'setting_id'=>$name,
						'value'=>$value
					)
				);
			}
			DB::update('account',array('cache_setting'=>''),'id="'.$account_id.'"');
			if($setting['account_type']=='PORTAL' and $account_id==PORTAL_ID){
				if(isset($_REQUEST['portal']) and $portal=DB::select_id('account','#'.addslashes($_REQUEST['portal']))){
					Session::set('portal', $portal);
				}else{
					Session::set('portal', DB::select_id('account','#default'));
				}
			}
		}else{
			DB::insert('setting',array('id'=>$name,'default_value'=>$value,'type'=>'TEXT','account_type'=>$account_type));
			if($user_id==false){
				$user_id = Session::get('portal','id');
			}
			DB::insert('account_setting',array('setting_id'=>$name,'value'=>$value,'account_id'=>$user_id));
		}
	}
	static function make_word_cache(){
		$languages = DB::fetch_all('SELECT id FROM language where active=1');
		foreach($languages as $language_id=>$row){
			$all_words = DB::fetch_all('
					SELECT
						id, value_'.$language_id.' as value
					FROM
						word
				');
			$language_convert = array();
			foreach($all_words as $language){
				$language_convert = $language_convert +
					array('[[.'.$language['id'].'.]]'=>$language['value']);
			}
			if($language_id==Portal::language()){
				$GLOBALS['all_words'] = $language_convert;
			}
			$st = '<?php
if(!isset($GLOBALS[\'all_words\'])){
	$GLOBALS[\'all_words\'] = '.var_export($language_convert,1).';
}
?>';
			$f = fopen('cache/language_'.$language_id.'.php','w+');
			fwrite($f,$st);
			fclose($f);
			$st = 'TCV.Portal.words = '.MiString::array2js($language_convert).';';
			$f = fopen('cache/language_'.$language_id.'.js','w+');
			fwrite($f,$st);
			fclose($f);
		}
	}
}
Portal::$page_gen_time = new Timer();
Portal::$page_gen_time->start_timer();
Portal::$current = new Portal();
if(Url::get('m_debug')){
	Portal::settting_();
}