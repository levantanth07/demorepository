<?php
class Menu extends Module{
    public static $new_user;
    public static $quyen_sale;
    public static $admin_group;
    public static $quyen_van_don;
    public static $quyen_ke_toan;
    public static $quyen_quan_ly_khach_hang;

	function __construct($row){
        Module::Module($row);
        require_once 'db.php';
        require_once 'packages/vissale/lib/php/vissale.php';
        require_once('assets/lib/dectect_mobile/Mobile_Detect.php');

        self::$new_user = (isset($_SESSION['user_data']['user_status']) and $_SESSION['user_data']['user_status']<=1)?1:0;
        self::$admin_group = Session::get('admin_group');
        self::$quyen_van_don = check_user_privilege('VAN_DON');
        self::$quyen_ke_toan = check_user_privilege('KE_TOAN');
        self::$quyen_quan_ly_khach_hang = check_user_privilege('CUSTOMER');
		$detect = new Mobile_Detect;
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'TAB' : 'MOBILE') : 'DESKTOP');
		Session::set('device',$deviceType);
        self::$quyen_sale = check_user_privilege('GANDON');//quyen sale
        if(Url::get('do')=='get_unassign_orders'){
            $str = '';
            if(User::is_login() and (Menu::$quyen_sale || Menu::$quyen_van_don)){
                $assigned_orders = MenuDB::get_un_assign_orders();
                $getOrderStatusKhachPhanVanSale = MenuDB::getOrderStatusKhachPhanVanSale();
                $getOrderStatusKhachKhongNgheMaySale = MenuDB::getOrderStatusKhachKhongNgheMaySale();
                $getOrderStatusKhachPhanVanVanDon = MenuDB::getOrderStatusKhachPhanVanVanDon();
                $getOrderStatusKhachKhongNgheMayVanDon = MenuDB::getOrderStatusKhachKhongNgheMayVanDon();
                $phanVan = [];
                $khongNgheMay = [];
                $phanVan = $getOrderStatusKhachPhanVanSale + $getOrderStatusKhachPhanVanVanDon;
                $khongNgheMay = $getOrderStatusKhachKhongNgheMaySale + $getOrderStatusKhachKhongNgheMayVanDon;
                $length = get_group_options('hide_phone_number') ? get_group_options('hide_phone_number') : 0;
                if($assigned_orders ||  $phanVan || $khongNgheMay){
                    $str .= '<ul class="nav nav-tabs nav-justified">';
                        if($assigned_orders){
                            $str .= '<li class="active"><a data-toggle="tab" href="#home">Số được chia</a></li>';
                            $str .= '<li><a data-toggle="tab" href="#menu1">Khách phân vân</a></li>';
                            $str .= '<li><a data-toggle="tab" href="#menu2">Khách không nghe máy</a></li>';
                        } else if($phanVan){
                            $str .= '<li><a data-toggle="tab" href="#home">Số được chia</a></li>';
                            $str .= '<li class="active"><a data-toggle="tab" href="#menu1">Khách phân vân</a></li>';
                            $str .= '<li><a data-toggle="tab" href="#menu2">Khách không nghe máy</a></li>';
                        } else if($khongNgheMay){
                            $str .= '<li><a data-toggle="tab" href="#home">Số được chia</a></li>';
                            $str .= '<li><a data-toggle="tab" href="#menu1">Khách phân vân</a></li>';
                            $str .= '<li class="active"><a data-toggle="tab" href="#menu2">Khách không nghe máy</a></li>';
                        }
                            
                            
                    $str .= '</ul>';
                    $str .= '<div class="tab-content">';
                    if($assigned_orders){
                        $str .= '<div id="home" class="tab-pane fade in active">';
                    }
                    else {
                        $str .= '<div id="home" class="tab-pane fade">';
                    }
                                $str .= '<div style="overflow: hidden;padding-top:0px; max-height:90px; overflow: auto;">';
                                            if($assigned_orders && Menu::$quyen_sale)
                                            {
                                                foreach($assigned_orders as $key => $item) {
                                                    $mobile = ModifyPhoneNumber::hidePhoneNumber($item['mobile'],$length);
                                                    $str .='<div style="background: #ffa153;width:100px;float: left;padding:2px;margin-bottom: 2px; border:1px solid #FFF;margin:1px;border-radius: 3px;">
                                                        Số'.$mobile.'
                                                    <a class="btn btn-warning pull-right btn-sm" style="background:#fff;color:#999;height: 20px;padding:2px;text-decoration: none;text-transform: uppercase;border:1px solid #333" href="'.Url::build('admin_orders',['cmd'=>'edit','id'=>$key]).'"> <i class="fa fa-sign-in"></i> Xử lý</a></div>';
                                                }
                                            } else {
                                                $str .= '';
                                            }
                                $str .= '</div>';   
                        $str .= '</div>';
                        if(!$assigned_orders && $phanVan){
                            $str .= '<div id="menu1" class="tab-pane fade in active">';
                        }
                        else {
                            $str .= '<div id="menu1" class="tab-pane fade">';
                        }
                                    $str .= '<div style="overflow: hidden;padding-top:0px; max-height:90px; overflow: auto;">';
                                                if($phanVan)
                                                {
                                                    foreach($phanVan as $key => $item) {
                                                        $mobile = ModifyPhoneNumber::hidePhoneNumber($item['mobile'],$length);
                                                        $str .='<div style="background: #ffa153;width:100px;float: left;padding:2px;margin-bottom: 2px; border:1px solid #FFF;margin:1px;border-radius: 3px;">
                                                            Số'.$mobile.'
                                                        <a class="btn btn-warning pull-right btn-sm" style="background:#fff;color:#999;height: 20px;padding:2px;text-decoration: none;text-transform: uppercase;border:1px solid #333" href="'.Url::build('admin_orders',['cmd'=>'edit','id'=>$key]).'"> <i class="fa fa-sign-in"></i> Xử lý</a></div>';
                                                    }
                                                } else {
                                                    $str .= '';
                                                }
                                    $str .= '</div>';   
                            $str .= '</div>';
                            if(!$assigned_orders && !$phanVan && $khongNgheMay){
                                $str .= '<div id="menu2" class="tab-pane fade in active">';
                            }
                            else {
                                $str .= '<div id="menu2" class="tab-pane fade">';
                            }
                                        $str .= '<div style="overflow: hidden;padding-top:0px; max-height:90px; overflow: auto;">';
                                                    if($khongNgheMay)
                                                    {
                                                        foreach($khongNgheMay as $key => $item) {
                                                            $mobile = ModifyPhoneNumber::hidePhoneNumber($item['mobile'],$length);
                                                            $str .='<div style="background: #ffa153;width:100px;float: left;padding:2px;margin-bottom: 2px; border:1px solid #FFF;margin:1px;border-radius: 3px;">
                                                                Số'.$mobile.'
                                                            <a class="btn btn-warning pull-right btn-sm" style="background:#fff;color:#999;height: 20px;padding:2px;text-decoration: none;text-transform: uppercase;border:1px solid #333" href="'.Url::build('admin_orders',['cmd'=>'edit','id'=>$key]).'"> <i class="fa fa-sign-in"></i> Xử lý</a></div>';
                                                        }
                                                    } else {
                                                        $str .= '';
                                                    }
                                        $str .= '</div>';   
                                $str .= '</div>';
                    $str .= '</div>';
                }
            }else{
                $str = '';
            }
            echo $str;
            exit();
        }
		if(User::is_login()){
		    // doi trang thai nguoi dung
            if(Url::get('do')=='update_user_status'){
                $user_status = Url::get('status');
                DB::update('users',['status'=>$user_status],'username="'.$row['id'].'"');
                if($user_status==2){
                    setcookie('new_user', "",time() - 3600);
                    $_SESSION['user_data']['user_status'] = 2;
                }else{
                    setcookie('new_user',true,(time()+86400*7));
                    $_SESSION['user_data']['user_status'] = 1;
                }
                echo json_encode(['result'=>$user_status]);
                exit();
            }
			// yêu cầu đổi mật khẩu
			if((Url::get('page')!='trang-ca-nhan') and !DB::exists('select id from account_log where log_type = 1 and account_id="'.User::id().'"')){
				//Url::js_redirect('trang-ca-nhan','Bạn vui lòng đổi mật khẩu để đảm bảo an toàn cho chính tài khoản của bạn!',array('cmd'=>'change_pass'));
			}
			//
			//Session::set('language_id',1);
			if(is_master_group() and Url::iget('group_id')){
				Session::set('group_id',Url::iget('group_id'));
			}
      		$this->check_allow_ips();
			if(User::can_edit(MODULE_PRODUCTADMIN,ANY_CATEGORY) or User::can_edit(MODULE_NEWSADMIN,ANY_CATEGORY) or Session::get('group_id')){
				require_once 'forms/admin_menu.php';
				$this->add_form(new MenuForm());
			}
			/*else{
				require_once 'forms/user_menu.php';
				$this->add_form(new UserMenuForm());
			}*/
		}
		else
		{
			if(Url::get('page')){
				$arr = array('href'=>'index062019.php?page='. DataFilter::removeXSSinHtml(Url::get('page')).''.(Url::get('id')?'&id='. DataFilter::removeXSSinHtml(Url::get('id')):'').'');
			}else{
				$arr = array();
			}
			//Url::redirect('dang-nhap',$arr);
			if(System::is_local()){
                Url::redirect('dang-nhap',$arr);
            }else{
                header('location:https://'.$_SERVER['HTTP_HOST']);
            }
            exit();
		}
	}
	function check_allow_ips(){
		$group_id = Session::get('group_id');
		if(!Session::get('admin_group') and $ips = DB::fetch('select id,allow_ips from `groups` where id='.$group_id,'allow_ips')){
			$key = 'add_user_ip';
        	$users = DB::fetch('select id,value,group_id from group_options where `key`= "' . $key . '" and group_id = '. $group_id);
			$arr = explode(',',$ips);
			$allowUser = [];
			if($users && !empty($users['value'])){
				$allowUser = json_decode($users['value'],true);
			}
			$user_id = Session::get('user_id');
			$sql = "SELECT id,username FROM users WHERE username = '".$user_id."'";
			$query = DB::fetch($sql);
			if(!empty($allowUser)){
				if(!in_array($query['id'], $allowUser) and System::get_client_ip_env() != '127.0.0.1' and $ips and !in_array(System::get_client_ip_env(),$arr)){
					die('<h4 style="color:#f00;">Bạn không có quyền truy cập từ IP '.System::get_client_ip_env().'...!</h4>');
				}
			} else {
				if(System::get_client_ip_env() != '127.0.0.1' and $ips and !in_array(System::get_client_ip_env(),$arr)){
					die('<h4 style="color:#f00;">Bạn không có quyền truy cập từ IP '.System::get_client_ip_env().'...!</h4>');
				}
			}
			
		}
	}
}
?>
