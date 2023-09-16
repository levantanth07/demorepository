<?php
// Quản lý tùy chỉnh shop
class AdminUserInfo extends Module
{
	const TIME_SLOT_1 = '10h';
    const TIME_SLOT_2 = '11h30';
    const TIME_SLOT_3 = '14h';
    const TIME_SLOT_4 = '15h30';
    const TIME_SLOT_5 = '17h30';
    const TIME_SLOT_6 = '23h';
    const TIME_SLOT_7 = '24h';
    
	public static $date_init_value;
	public static $allow_earse_time;
	public static $is_admin_shop;
	function __construct($row)
	{
		Module::Module($row);
		Portal::$document_title = Url::get('do') == 'list' ? 'DANH SÁCH THÔNG TIN TÀI KHOẢN' : 'CÀI ĐẶT CỬA HÀNG';
		self::$allow_earse_time = [22,7];
		self::$is_admin_shop = Session::get('admin_group');
		if(System::is_local()){
			self::$date_init_value = 'NULL';//'0000-00-00 00:00:00';
		}else{
			self::$date_init_value = '0000-00-00 00:00:00';
		}

		if(Url::get('do') === 'cost_declaration'){
			require_once 'forms/CostDeclaration.php';
			return $this->add_form(new CostDeclaration());
		}


		require_once 'packages/vissale/lib/php/vissale.php';
		if((Session::get('admin_group') or (Session::get('group_id') == GROUP_ID_AN_NINH_SHOP) or User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)) or check_user_privilege('ADMIN_MARKETING')
		)
		{
			require_once 'db.php';
			if(!Session::get('group_id')){
				echo 'alert("Bạn không có quyền truy cập")';
				header($_SERVER['SERVER_NAME']);
			}
			switch (Url::get('do')){
				case 'update_api':
					echo AdminUserInfoDB::update_api();
					die;
				case 'generate_api_key':
					echo AdminUserInfoDB::generate_api_key();
					die;
				case 'erase_data':
					require_once 'forms/erase_data.php';
					$this->add_form(new EraseDataForm);
					break;
				case 'list':
					require_once 'forms/list.php';
					$this->add_form(new ListUserAdminInforForm);
					break;
				case 'ajax_list':
					require_once 'forms/ajax_list.php';
					$this->add_form(new ListAjaxUserAdminInforForm);
					break;
				case 'list_story':
					require_once 'forms/list_history.php';
					$this->add_form(new ListHistoryForm);
					break;
				case 'ajax_list_history':
					require_once 'forms/ajax_list_history.php';
					$this->add_form(new ListAjaxHistoryForm);
					break;	

				case 'manager_columns_export_excel':
					require_once 'forms/manager_columns_export_excel.php';
					$this->add_form(new ManagerColumnsExportExcel());
					break;

				case 'sync_data_crm':
					require_once 'forms/sync_data_crm.php';
					$this->add_form(new SyncDataCrm);
					break;

				default:
					require_once 'forms/information.php';
					$this->add_form(new AdminUserInfoInformationForm);
					break;
			}
		}
		else
		{
			header('location:/trang-ca-nhan');
			exit();
		}
	}
}
?>