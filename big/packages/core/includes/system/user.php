<?php
define('CURRENT_CATEGORY',1);
define('ANY_CATEGORY',2);
class User{
	const PASSWD_WEAK = 1;// 1 - weak 
    const PASSWD_NOT_WEAK = 2;// 2 - not weak 
    const PASSWD_ACCEPTABLE = 3;// 3 - acceptable 
    const PASSWD_STRONG = 4;// 4 - strong 
    const TIME_TO_OFFLINE = 600;
    const RESET_PASS_IMMEDIATE_TIME = '0000-00-00 00:00:00';
	var $groups = array();
	var $privilege = array();
	var $actions = array();
	var $settings = array();
	static $current=false;

	private static $account = null;
	private static $user = null;
	
	public function __construct($id=false)
	{
		if($id){
			return;
		}

		if(!Session::is_set('user_id')){
			return Session::set('user_id','guest');
		}

		if(!$this->data = Query::from('account')->where('id', Session::get('user_id'))->first()) {
			return URL::redirectToLogin();
		}

		if(!self::$user = Query::from('users')->where('username', Session::get('user_id'))->first()) {
			return URL::redirectToLogin();
		}

        self::$account = $this->data;

		if (empty($this->data['cache_privilege'])) {
			require_once 'packages/core/includes/system/make_user_privilege_cache.php';
			eval(make_user_privilege_cache(Session::get('user_id')));
		}else{
			eval($this->data['cache_privilege']);
		}

		if (empty($this->data['cache_setting'])) {
			require_once 'packages/core/includes/system/make_account_setting_cache.php';
			$code = make_account_setting_cache(Session::get('user_id'));
			eval('$this->settings='.$code);
		}else{
			eval('$this->settings='.$this->data['cache_setting']);
		}
	}

    /**
     * Gets the account.
     *
     * @param      <type>  $fields  The fields
     *
     * @return     <type>  The account.
     */
    public static function getAccount()
    {
        return self::$account;
    }

    /**
     * Gets the user.
     *
     * @return     <type>  The user.
     */
    public static function getUser()
    {
    	return self::$user;
    }

	static function id(){
		if(Session::is_set('user_id')){
			return Session::get('user_id');
		}
		return 'guest';
	}
	static function update_account_log($log_type=0,$content,$group_id = null, $group_name = null, $accountID = null)
	{
		if(!$content){
			return;
		}

        $account_id = $accountID ?? User::id();
        $group_id = $group_id ?: Session::get('group_id');
        $groupName = $group_name ?: DB::fetch('SELECT id,name FROM groups WHERE id=' . $group_id, 'name');

        $dataLog = [
            'account_id'=>$account_id,
            'log_type'=>(int)$log_type,
            'content'=>$content,
            'time'=>time(),
            'group_id'=>(int)$group_id,
            'module_id'=>0,
            'ip'=>System::get_client_ip_env(),
        ];

        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            require_once(ROOT_PATH.'packages/vissale/lib/php/log.php');
            
            return storeAccountLog(array_merge($dataLog, [
                'group_name' => $groupName,
                'client_id'=>'WEB',
                'header'=>json_encode(getallheaders()),
                'session_id'=>substr(session_id(), -10)
            ]));
        }

        LogHandler::sendSqlToQueue($dataLog);
	}
	static function is_login(){
		return Session::is_set('user_id') and Session::get('user_id')!='guest' and DB::exists_id('account',Session::get('user_id'));
	}
	static function is_online($id){
		$row=DB::fetch('select count(id) as total from account where id="'.$id.'" and session_id != "" and last_online_time> '.(time()-self::TIME_TO_OFFLINE));
		if ($row['total']>0){
			return true;
		}else{
			return false;
		}
	}
	static function get_password_strength($password, $username)
	{
    	$strength = 0;

        require_once ROOT_PATH . 'packages/core/includes/common/WeakPasswordDetector.php';
        if(strlen($password) < 6 || WeakPasswordDetector::detect($password, true, $username)){
            return $strength;
        }

        $patterns = [
            // có một kí tự thường
            // có một kí tự hoa
            '#^(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z]).*$#',

            // có một số
            '#^(?=\D*\d+).*#',

            // 6 kí tự trở lên
            // Không được phép có 3 kí tự giống nhau liền nhau, ví dụ aaa, bbb, 11111, ...
            // Không được phép có bộ 3 kí lặp lại, ví dụ: abc@abc, 43211432, ...
            // Không được chứa bộ 3 kí tự đảo ngược: ví dụ: abc-*cba
            // Có ít nhất 1 kí tự đặc biệt
            '#^(?=.*[¬!"£$%^&*()`{}\[\]:@~;\'\#<>?,.\/\\-=_+\|]+)(?!.*(.)\1\1)(?!.*(.{3}).*\2)(?!.*(.)(.)(.).*\5\4\3).*$#i',

            // Độ dài mật khẩu tối thiểu 8 kí tự khác nhau
            '#^(?!.*(.).*\1).{8,}$#i'
        ];

        // Các tiêu chí độ mạnh password là tăng dần nên nếu như bất cứ tiêu chí nào bị vi phạm thì coi như
        // độ mạnh mật khẩu là của tiêu chí trước đó
        foreach($patterns as $pattern) {
            if(!preg_match($pattern, $password)){
                return $strength;
            }

            $strength++;
        }

        return $strength;
	}
	static function encode_password($password){
		return md5($password.CATBE);
	}
	static function is_in_group($user_id,$group_id){
		$row=DB::select('user_group',' user_id="'.$user_id.'" and group_id="'.$group_id.'" and is_active=1');
		if ($row or User::is_admin()){
			return true;
		}else{
			return false;
		}
	}
	static function groups(){	
		return $this->groups;
	}
	static function home_page(){
		if(User::$current and User::$current->groups){
			$group = reset(User::$current->groups);
			if($group['home_page']==''){
				$group['home_page'] = URL::build('home');
			}
			return $group['home_page'];
		}
		return URL::build('home');
	}
	
	function is_admin_user(){
		return isset($this->groups[3]);
	}
	static function is_admin(){
		if(isset(User::$current)){
			return User::$current->is_admin_user();
		}
	}
	static function can_do_action($action,$pos,$module_id=false, $structure_id = 0, $portal_id = false){
		if(!$portal_id){
			$portal_id = PORTAL_ID;
		}
		if(User::is_admin()){
			return true;
		}
		if(!$module_id){
			if(isset(Module::$current->data)){
				$module_id = Module::$current->data['module']['id'];
				//$is_service = Module::$current->data['module']['type']=='SERVICE';
			}else{
				$module_id=false;
			}
		}
		if(!$module_id){
			return;
		}
		if($structure_id){
			if($structure_id==CURRENT_CATEGORY){
				$structure_id=0;
				if(URL::sget('category_id')){
					$structure_id=DB::structure_id('static function',URL::sget('category_id'));
				}
				if(!$structure_id){
					$structure_id = ID_ROOT;
				}
			}			
			if(isset(User::$current->actions[$portal_id][$module_id][0])){
				return User::$current->actions[$portal_id][$module_id][0]&(1 << (7-$pos));
			}
			if($structure_id==ANY_CATEGORY){
				if(isset(User::$current->actions[$portal_id]) and isset(User::$current->actions[$portal_id][$module_id])){
					foreach(User::$current->actions[$portal_id][$module_id] as $category_privilege){	
						if($category_privilege&(1 << (7-$pos))){
							return true;
						}
					}
				}
				return false;
			}else{
				while(1){				
					if(isset(User::$current->actions[$portal_id][$module_id][$structure_id])){
						return User::$current->actions[$portal_id][$module_id][$structure_id]&(1 << (7-$pos));
					}
					else
					if($structure_id <= ID_ROOT){
						break;
					}else{
						$structure_id = IDStructure::parent($structure_id);
					}
				}
			}
			return false;
		}else{
			return isset(User::$current->actions[$portal_id][$module_id][0]) and (User::$current->actions[$portal_id][$module_id][0]&(1 << (7-$pos)));
		}
	}
	static function can_view($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('view',0,$module_id, $structure_id);
	}
	static function can_view_detail($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('view_detail',1,$module_id, $structure_id);
	}
	static function can_add($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('add',2,$module_id, $structure_id);
	}
	static function can_edit($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('edit',3,$module_id, $structure_id);
	}
	static function can_delete($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('delete',4,$module_id, $structure_id);
	}	
	static function can_moderator($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('moderator',5,$module_id, $structure_id);
	}
	static function can_reserve($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('reserve',6,$module_id, $structure_id);
	}
	static function can_admin($module_id=false, $structure_id = 0, $portal_id = false){
		return USER::can_do_action('admin',7,$module_id, $structure_id);
	}
	static function check_categories($categories,$module=false){
		foreach($categories as $key=>$value){
			if(isset($value['structure_id']) and !User::can_view($module,$value['structure_id'])){
				unset($categories[$key]);
			}
		}
		return $categories;
	}	
	static function get_setting($name,$default=''){
		return Portal::get_setting($name,$default, User::id());
	}
	static function set_setting($name, $value,$user_id=false){
		if(!$user_id){
			$user_id = Session::get('user_id');
		}
		Portal::set_setting($name, $value,$user_id);
	}
}
User::$current = new User();
require_once ROOT_PATH . 'packages/core/includes/common/GroupOption.php';
require_once ROOT_PATH . 'packages/core/includes/common/UserRole.php';
if(!Session::is_set('user_id') and isset($_COOKIE['user_id'])and $_COOKIE['user_id']){
	setcookie('user_id',"",time()-3600);
}
