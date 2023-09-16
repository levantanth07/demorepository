<?php
class CrmCustomerSchedule extends Module
{
    protected $md5_key;
    public static $admin_group;
	public function __construct($row)
	{
	    self::$admin_group = Session::get('admin_group');
	    $this->md5_key = CATBE;
		Module::Module($row);
		require_once 'db.php';
		require_once 'packages/vissale/modules/CrmCustomer/db.php';
        require_once 'packages/core/includes/utils/paging.php';
        if(User::is_login()){
			switch(Url::get('cmd'))
			{
				case 'add':
				    if($cid = Url::iget('cid') and DB::exists('select id from crm_customer where id='.$cid)){
                        $this->add_cmd();
                    }else{
				        die('Chưa có khách hàng được chọn để tạo lịch!');
                    }
					break;

				case 'edit':
                    $this->edit_cmd();
                    break;
                case 'delete':
                    if($this->delete_cmd()){
                        Url::js_redirect(true,'Bạn xóa thành công!');
                    }else{
                        die('Lỗi: dữ liệu không tồn tại!');
                    }
                    break;
                case 'today_schedule':
                    $this->today_schedule();
                    break;

				default:
					$this->list_cmd();
					break;
			}
		} else {
			Url::access_denied();
		}
	}

    protected function today_schedule()
    {
        require_once 'forms/today_schedule.php';
        $this->add_form( new CrmCustomerTodayScheduleForm() );
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
		$this->add_form(new ListCrmCustomerScheduleForm());
	}

	function add_cmd()
	{
		if(User::is_login())
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditCrmCustomerScheduleForm());
		} else {
			Url::access_denied();
		}
	}

	function edit_cmd()
	{
        $sid = DB::escape(Url::get('sid'));
        $sql = "SELECT id FROM crm_customer_schedule WHERE MD5(CONCAT(id,'$this->md5_key'))='{$sid}'";
        if( ($row = DB::fetch($sql)) && CrmCustomerScheduleDB::can_edit($row['id']) ) {
			require_once 'forms/edit.php';
			$this->add_form(new EditCrmCustomerScheduleForm());
		} else {
			echo '<script>alert("Bạn không có quyền sửa lịch hẹn này!");</script>';
			Url::redirect_current();
		}
	}
    function delete_cmd()
    {
        if(!self::$admin_group){
            Url::js_redirect(true,'Chỉ admin shop mới có quyền xóa lịch hẹn!');
        }
        $sid = Url::get('sid');
        $sql = "SELECT id FROM crm_customer_schedule WHERE MD5(CONCAT(id,'$this->md5_key'))='{$sid}'";
        if( ($row = DB::fetch($sql)) && CrmCustomerScheduleDB::can_edit($row['id']) ) {
            DB::delete('crm_customer_schedule','id='.$row['id']);
            return true;
        }else{
            return false;
        }
    }

	public static function generateCode($bill_number){
        return strlen($bill_number)< 6 ? str_pad(($bill_number),6,"0",STR_PAD_LEFT): ($bill_number);
	}

    public static function generatePrefixType($bill_type)
    {
        if ($bill_type == 'receive') {
            return 'PT';
        }

        return 'PC';
    }

}

