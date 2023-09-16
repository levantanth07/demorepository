<?php
class ThuChiModule extends Module
{
    public static $group_id;
    public static $quyen_xem;
    public static $quyen_sua;
    public static $quyen_xoa;
    public static $quyen_admin;
	function __construct($row)
	{
		Module::Module($row);
		self::$group_id = Session::get('group_id');
        self::$quyen_xem = true;
        self::$quyen_sua = true;
        self::$quyen_xoa = true;
        self::$quyen_admin = true;
        require_once('packages/core/includes/utils/currency.php');
		require_once 'db.php';
        if(
            check_user_privilege('ADMIN_KETOAN')
            or check_user_privilege('THUNGAN')
        )
		{
			switch(Url::get('cmd'))
			{
				case 'add':
                    if(self::$quyen_sua) {
                        $this->add_cmd();
                        break;
                    }else{
                        Url::js_redirect(true,'Bạn không có quyền truy cập');
                    }
				case 'edit':
				case 'print':
                if(self::$quyen_sua) {
                    $this->edit_cmd();
                    break;
                }else{
                    Url::js_redirect(true,'Bạn không có quyền truy cập');
                }
				case 'delete_id':
                    if(self::$quyen_xoa) {
                        require_once 'forms/delete.php';
                        $this->add_form(new DeleteThuChiModuleForm());
                        break;
                    }
				default:
				    if(self::$quyen_xem){
                        $this->list_cmd();
                        break;
                    }else{
                        Url::js_redirect('admin_orders','Bạn không có quyền truy cập');
                    }
			}
		}
		else
		{
			Url::access_denied();
		}

	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')))
		{
			@unlink(Url::get('link'));
		}

		echo '<script>window.close();</script>';
	}

	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListThuChiModuleForm());
	}

	function add_cmd()
	{
		if(check_user_privilege('ADMIN_KETOAN')
            or check_user_privilege('THUNGAN'))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditThuChiModuleForm());
		}
		else
		{
			Url::access_denied();
		}
	}
	function edit_cmd()
	{
		if(check_user_privilege('ADMIN_KETOAN')
            or check_user_privilege('THUNGAN')){
			require_once 'forms/edit.php';
			$this->add_form(new EditThuChiModuleForm());
		}else{
			echo '<script>alert("Bạn không có quyền!");</script>';
			Url::redirect_current();
		}
	}
	static public function generateCode($bill_number){
        return strlen($bill_number)< 6 ? str_pad(($bill_number),6,"0",STR_PAD_LEFT): ($bill_number);
	}

    static public function generatePrefixType($bill_type)
    {
        if ($bill_type == 'receive') {
            return 'PT';
        }

        return 'PC';
    }

}

