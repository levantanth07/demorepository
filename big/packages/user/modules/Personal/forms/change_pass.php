<?php
class ChangePassForm extends Form
{
	function __construct()
	{
		Form::Form('ChangePass');
		$this->add('old_password',new PasswordType(true,'Nhập mật khẩu đang sử dụng'));
		$this->add('new_password',new PasswordType(true,'Nhập mật khẩu mới'));
		$this->add('retype_new_password',new PasswordType(true,'Lỗi nhập lại mật khẩu'));
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if ($this->check())
		{
			$row = DB::select('account','id="'.Session::get('user_id').'" AND group_id='.Session::get('group_id'));
			if ($_REQUEST['old_password'] and User::encode_password($_REQUEST['old_password'])==($row['password']))
			{
				$this->user_changepass();
			}
			else
			{
				$this->error('old_password','Mật khẩu đang sử dụng không chính xác');
			}
		}
	}
	function draw()
	{
		$this->parse_layout('change_pass');
	}

	function user_changepass()
	{
		$password = DataFilter::filterIn($_REQUEST['new_password']);
		$retypepassword = DataFilter::filterIn($_REQUEST['retype_new_password']);

		if ($password!=$retypepassword){
			return $this->error('retype_new_password','Lỗi nhập lại mật khẩu');
		}

		if(User::get_password_strength($password, Session::get('user_id')) <= User::PASSWD_NOT_WEAK){
            return $this->error('new_password','Mật khẩu chưa đủ mạnh');
        }

		$values = [
			'password' => User::encode_password($password),
			'password_updated_at' => now()
		];

		require_once ROOT_PATH . 'packages/core/includes/common/ResetPassword.php';
        $rp = ResetPassword::newQuery();
        if(!$rp->validateUpdateUserID(User::getUser()['id'], $values['password'])){
            return $this->error(
                'id',
                'Bạn không được phép nhập trùng mật khẩu ' 
                . ResetPassword::NUMBER_CHANGES_PASSWORD_MUST_UNIQUE 
                . ' lần gần nhất',
                false
            );
        }
        
		DB::update('account', $values,'id="'.Session::get('user_id').'"');
		$rp->editOrNew(User::getUser()['id'], $values['password']);

		if(!Session::is_set('debuger_id')) {
			User::update_account_log(1, 'Đổi mật khẩu');
		}

		Url::js_redirect(true,'Bạn đã đổi mật khẩu thành công!');
	}
}
?>
