<?php
class Password extends Form
{	
	public $map = [];
	private $errorResponse = [
		'error' => 'Bạn không có quyền truy cập tính năng đổi mật khẩu'
	];

	function __construct()
	{
		Form::Form('Password');
		$this->link_css('assets/default/css/cms.css');
	}
	
	/**
	 * Called on submit.
	 */
	function on_submit()
	{
	}

	    /**
     * { function_description }
     *
     * @param      <type>  $data   The data
     */
    private function responseJSON($data)
    {
        header('content-type: application/json');
        die(json_encode($data));
    }

    /**
     * Determines ability to access password page.
     *
     * @return     bool  True if able to access password page, False otherwise.
     */
    private function canAccessPasswordPage()
    {
        return User::is_admin();
    }

    /**
     * Gets the user by username.
     *
     * @param      string  $username  The username
     */
    private function getUserByUsername(string $username)
    {
        $fmt = 'SELECT 
                    `users`.`id` as user_id,
                    `account`.`id`, 
                    `account`.`is_active`, 
                    `account`.`last_online_time` ,
                    `groups_system`.`name` as `master_group`,
                    IF(`account`.`id` = `groups`.`code`, 1, 0) as is_owner,
                    `account`.`admin_group` as is_shop_admin,
                    IF(`account_group`.`admin_user_id` IS NULL, 0, 1) as is_leader
                FROM `account`
                JOIN `users` ON `account`.`id`=`users`.`username`
                JOIN `groups` ON `groups`.id=`account`.`group_id`
                JOIN `groups_system` ON `groups_system`.id=`groups`.`system_group_id`
                LEFT JOIN `account_group` ON `account_group`.`admin_user_id` = `users`.`id`
                WHERE 
                    `account`.`id` = "%s" 
                    AND `account`.`type` = "USER" 
                LIMIT 1';
        $sql = sprintf($fmt, $username);

        return DB::fetch($sql);
    }

    /**
     * { function_description }
     *
     * @param      string  $username       The username
     * @param      string  $plainPassword  The plain password
     *
     * @return     array   ( description_of_the_return_value )
     */
    private function changePasswordOfUsername(string $username, string $plainPassword)
    {   
        $user = $this->getUserByUsername($username);
        if(!$user){
            return ['status' => 'ERROR'];
        }

        if(User::get_password_strength($plainPassword, $username) <= User::PASSWD_NOT_WEAK){
            return ['status' => 'FAIL'];
        }

        $password = User::encode_password($plainPassword);
        
        require_once ROOT_PATH . 'packages/core/includes/common/ResetPassword.php';
        $rp = ResetPassword::newQuery();
        if(!$rp->validateUpdateUserID($user['user_id'], $password)){
            return ['status' => 'FAIL'];
        }
        
        $status = DB::update(
            'account', 
            ['password' => $password, 'password_updated_at' => now()],
            'id = "' . $username . '"'
        );
        $rp->editOrNew($user['user_id'], $password);

        if($status === false){
            return ['status' => 'FAIL'];
        }

        UserAdminDB::update_log(
            ['id'=> $username, 'password'=>'*'],
            ['password' => '**']
        );

        return ['status' => 'OK'];

    }

	
	/**
	 * { function_description }
	 */
	function draw()
	{	
		$action = Url::get('action');

		if(!$this->canAccessPasswordPage()){
             return !$action ? die($this->errorResponse['error']) : $this->responseJSON($this->errorResponse);
        }

        switch($action){
        	case 'change':
            	$response = $this->changePasswordOfUsername(Url::get('username'), Url::get('password'));
            	break;

        	case 'get_user':
        		$response = $this->getUserByUsername(Url::get('username'));
        		break;

        	default:
        		return $this->parse_layout('password', $this->map);
        }

		return $this->responseJSON($response);
	}
}
?>
