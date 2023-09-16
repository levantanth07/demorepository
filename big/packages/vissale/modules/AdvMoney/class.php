<?php 
require_once ROOT_PATH . 'packages/vissale/lib/php/log.php';
require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
class AdvMoney extends Module
	{
	const TIME_SLOT_1 = '10h';
	const TIME_SLOT_2 = '11h30';
	const TIME_SLOT_3 = '14h';
	const TIME_SLOT_4 = '15h30';
	const TIME_SLOT_5 = '17h30';
	const TIME_SLOT_6 = '23h';
	const TIME_SLOT_7 = '24h';

	const LOG_TABLE = 'vs_adv_money';
    const LOG_UPDATE_TYPE = 'UPDATE';
    const LOG_VIEW_TYPE = 'VIEW';
    const LOG_ADD_TYPE = 'ADD';

	public static $choose_time_declare_advertising_money;
	public static $data_time;
	function __construct($row){
		self::$choose_time_declare_advertising_money = $this->get_option_time_slot();
		self::$data_time = $this->get_time_slot();
		Module::Module($row);
		require_once('db.php');
		if(User::is_login() and Session::get('group_id') and (check_user_privilege('ADMIN_MARKETING') or check_user_privilege('MARKETING'))){
			switch(Url::get('cmd'))
			{
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditAdvMoneyForm());
					break;
				case 'edit':
					require_once 'forms/edit.php';
					$this->add_form(new EditAdvMoneyForm());
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListAdvMoneyForm());
					break;
			}
		}else{
			URL::access_denied();
		}
	}
	function get_time_slot(){
    	$timeSlot = [
			'time_slot_1' => AdvMoney::TIME_SLOT_1,
			'time_slot_2' => AdvMoney::TIME_SLOT_2,
			'time_slot_3' => AdvMoney::TIME_SLOT_3,
			'time_slot_4' => AdvMoney::TIME_SLOT_4,
			'time_slot_5' => AdvMoney::TIME_SLOT_5,
			'time_slot_6' => AdvMoney::TIME_SLOT_6,
			'time_slot_7' => AdvMoney::TIME_SLOT_7,
		];

		$format = [];

		if(!AdvMoney::$choose_time_declare_advertising_money || AdvMoney::$choose_time_declare_advertising_money['value'] == ''){
			$time = $timeSlot;
			$format = $timeSlot;
		} else {
			$time = json_decode(AdvMoney::$choose_time_declare_advertising_money['value'],true);
			foreach ($timeSlot as $key => $value) {
				foreach ($time as $k => $v) {
					if($value == $v){
						$format[$key] = $value;
					}
				}
			}
		}
		$data['timeSlot'] = $time;
		$data['format'] = $format;
		return $data;
    }
    function get_option_time_slot(){
		if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) return array('value' => '["24h"]');
    	$key = 'choose_time_declare_advertising_money';
    	return DB::fetch('select * from group_options where `key`= "' . $key . '" and group_id = '. Session::get('group_id'));
    } 

    public static function getIconByType(string $type)
    {
        switch($type)
        {
            case AdvMoney::LOG_ADD_TYPE:
                return 'fa fa-plus bg-success';

            case AdvMoney::LOG_VIEW_TYPE:
                return 'fa fa-eye bg-primary';

            case AdvMoney::LOG_UPDATE_TYPE:
                return 'fa fa-pencil bg-red';
            
            default:
        }       return 'fa fa-envelope bg-blue';
    }


    public static function getTitleByType(string $type)
    {
        switch($type)
        {
            case AdvMoney::LOG_ADD_TYPE:
                return 'Thêm mới thông tin';

            case AdvMoney::LOG_VIEW_TYPE:
                return 'Xem thông tin';

            case AdvMoney::LOG_UPDATE_TYPE:
                return 'Sửa thông tin';
            
            default:
        }       return 'Chưa biết';
    }
}
?>
