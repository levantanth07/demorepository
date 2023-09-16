<?php 
require_once ROOT_PATH.'vendor/autoload.php';
require_once ROOT_PATH.'cache/db/redis.php';
use RedisClient\RedisClient;
use RedisClient\ClientFactory;

class CrmCustomer extends Module{
    private $_redis;
    public $group_id;
	function __construct($row){
		Module::Module($row);
		$this->group_id = Session::get('group_id');
		require_once 'packages/vissale/modules/AdminOrders/db.php';
        require_once('packages/vissale/lib/php/vissale.php');
		require_once 'db.php';
		$group_id = Session::get('group_id');
		$havePermission = false;
        $quyenQuanLyKhachHang = check_user_privilege('CUSTOMER');
        $tableDB = '';
        $idTable = '';
        if (URL::get('idLichHen')) {
            $tableDB = 'crm_customer_schedule';
            $idTable = URL::get('idLichHen');
        } else if (URL::get('idCuocGoi')) {
            $tableDB = 'crm_customer_callhistory';
            $idTable = URL::get('idLichHen');
        } else if (URL::get('idGhiChu')) {
            $tableDB = 'crm_customer_notes';
            $idTable = URL::get('idGhiChu');
        } else if (URL::get('idBenhLy')) {
            $tableDB = 'crm_customer_pathology';
            $idTable = URL::get('idBenhLy');
        }
        if (Session::get('admin_group') or $quyenQuanLyKhachHang) {
            $havePermission = true;
        } else if ($tableDB != '' and DB::exists('SELECT id FROM ' . $tableDB . '  WHERE id=' . DB::escape($idTable) . ' AND created_user_id = ' . get_user_id())) {
            $havePermission = true;
        }


        if((User::is_login())) {
			switch( URL::get('do') ) {
			case 'delete':
				if(Session::get('admin_group')){
					if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0){
						if(sizeof(URL::get('selected_ids'))>0){
							require_once 'forms/list.php';
							$this->add_form(new ListCrmCustomerForm());
						}else{
							$ids = URL::get('selected_ids');
							$_REQUEST['id'] = $ids[0];
							require_once 'forms/detail.php';
							$this->add_form(new CrmCustomerForm());
						}
					}elseif(Url::check('id') and DB::exists_id('crm_customer',Url::iget('id'))){
						if(Url::get('delete_one_item')){
							CrmCustomerDB::delete(Url::iget('id'));
							Url::redirect_current(array('org'));
						}else{
							require_once 'forms/detail.php';
							$this->add_form(new CrmCustomerForm());
						}
					}else{
						Url::redirect_current(array('org'));
					}
				}
				break;
			case 'edit':
			    $cid = Url::iget('cid');
			    $sql = "SELECT id FROM crm_customer WHERE id=$cid and group_id=$group_id";
				if(Url::check('cid') and $row = DB::fetch($sql) and CrmCustomerDB::can_edit($row['id'])){
					require_once 'forms/edit.php';
					$this->add_form(new EditCrmCustomerForm());
				} else {
					Url::js_redirect(true,'Bạn không có quyền truy cập. Vui lòng liên hệ quản lý cấp trên để trợ giúp sửa đổi!');
				}
				break;
			case 'add':
				die();
				require_once 'forms/edit.php';
				$this->add_form(new EditCrmCustomerForm());
				break;
			case 'view':
				if($cid = Url::iget('cid') and $customer=DB::fetch('select id from crm_customer where id='.$cid.' and group_id='.$this->group_id)){
					require_once 'forms/detail.php';
					$this->add_form(new CrmCustomerForm());
				}else{
					Url::js_redirect(true,'Không có khách hàng phù hợp!');
				}
				break;

            case 'export_excel':
                require_once 'forms/list.php';
                $this->add_form(new ListCrmCustomerForm());
                break;

            case 'call_history':

                if (Session::get('account_type')==3) {
                    require_once 'forms/call_history.php';
                    $this->add_form(new CallHistoryCrmCustomerForm());
                } else {
                    Url::js_redirect(true,'Bạn không có quyền truy cập.');
                }
                break;

			case "redirect":
				$destination = URL::get("destination");
				if($destination){
					$this->redirect($destination);
				}
				break;

            /*case 'today_schedule':
                require_once 'forms/today_schedule.php';
                $this->add_form(new CrmCustomerTodayScheduleForm());
                break;*/

			default: 
				require_once 'forms/list.php';
				$this->add_form(new ListCrmCustomerForm());
				break;
			}

		} else {
			URL::access_denied();
		}
	}


	function redirect($url){
        try {
            $this->_redis = ClientFactory::create(REDIS_LOGIN_TOKEN); 
            $user = Session::get('user_data');
            $token = md5(API_PROVIDER. $user['user_id']. $user['group_id']."".time());
			$destination = $url;
			if(isset($user['is_active']) && $user['is_active']){
				$this->_redis->set($token, json_encode([
					"provider" => API_PROVIDER, 
					"user_id"=> $user['user_id'], 
					"group_id"=>$user['group_id'], 
					"user_name"=> $user['username']
				]), REDIS_LOGIN_TOKEN["time_to_live"]);
				$destination .= strpos($url, "?") ? "&key=".$token : "?key=".$token  ;
			}
            header("Location: ".$destination);
        } catch (\Throwable $th) {
        }
    }
}