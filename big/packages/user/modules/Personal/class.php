<?php
class Personal extends Module{
	function __construct($row){
		Module::Module($row);
		if(User::is_login()){
			switch( URL::get('cmd')){
				case 'get_password_length':
					echo User::get_password_strength(Url::get('password'), Url::get('username'));
					exit();
					break;
				case 'check_email':
					$this->check_email();
					break;
				case 'change_pass':
					require_once 'forms/change_pass.php';
					$this->add_form(new ChangePassForm);
					break;
				default:
					require_once 'forms/information.php';
					$this->add_form(new PersonalInformationForm);
					break;
			}
		}else{
			Url::redirect('sign_in');
		}
	}
	function check_email(){
		if($value = DB::escape(Url::get('email')) and DB::fetch('select account.id from account inner join party on party.user_id = account.id where (party.email = "'.$value.'" or account.id="'.$value.'") and account.id <> "'.Session::get('user_id').'" and account.type="USER"')){
			echo 'false';
		}else{
			echo 'true';
		}
		exit();
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
}
?>
