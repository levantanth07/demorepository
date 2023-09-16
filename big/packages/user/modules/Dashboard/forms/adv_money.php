<?php
class AdvDashboardForm extends Form
{
    protected $map;
    protected $group_id;
    protected $isObd;
	function __construct()
	{
	    $this->group_id = Session::get('group_id');
		Form::Form('AdvDashboardForm');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->isObd = isObd();
	}	
	function draw()
	{
        if(!checkPermissionAccess(['MARKETING','ADMIN_MARKETING']) && !Dashboard::$xem_khoi_bc_marketing){
            Url::access_denied();
        }
		$this->map = array();
		$this->map['total'] = 0;
		//////////////////////////////////////////////////
		if(!Url::get('date_from')){
			//$_REQUEST['date_from'] = date('01/m/Y');
            $_REQUEST['date_from'] = date('d/m/Y');
		}
		if(!Url::get('date_to')){
			$_REQUEST['date_to'] = date('d/m/Y');
		}

		$total_date = Date_Time::count_day(Date_Time::to_time(Url::get('date_from')),Date_Time::to_time(Url::get('date_to')));
        if($total_date>31){
            die('<div class="alert alert-danger">Bạn vui lòng chọn khoảng thời gian trong tối đa một tháng.</div>');
        }
		$start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
		$end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));

		$status = DashboardDB::get_statuses();
		//$users = DashboardDB::get_users('MARKETING');
        $userFormat = DashboardDB::getUserNew('MARKETING',DB::escape(Url::get('is_active')));
        $users = [];
        foreach ($userFormat as $key => $value) {
          $users[$value['username']]['id'] = $value['username'];
          $users[$value['username']]['user_id'] = $value['user_id'];
          $users[$value['username']]['group_id'] = $value['group_id'];
          $users[$value['username']]['full_name'] = $value['full_name'];
          $users[$value['username']]['username'] = $value['username'];
          $users[$value['username']]['avatar'] = null;
        }
        if ($this->isObd) {
            $bundles = DashboardDB::getBundles();
            $sources = DashboardDB::getSource();
        } else {
            $bundles = $this->getBundles();
            $sources = $this->getSource();
        }
        // bundle
        $this->map['bundle_id_list'] = '';
        $bundleRequest = [];
        $bundleRequest = Url::get('bundle_ids');
        $strBundleRequets = '';
        if(!empty($bundleRequest)){
            $bundleFormat = [];
            foreach ($bundleRequest as $value) {
                $bundleFormat[] = DB::escape($value);
            }
            foreach($bundles as $key=>$val){
                if (in_array($key,$bundleFormat)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            $strBundleRequets = implode(',', $bundleFormat);
        } else {
            foreach($bundles as $key=>$val){
                $selected = '';
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            
        }
        // source
        $this->map['source_id_list'] = '';
        $sourceRequest = [];
        $sourceRequest = Url::get('source_ids');
        $strSourceRequets = '';
        if(!empty($sourceRequest)){
            $sourceFormat = [];
            foreach ($sourceRequest as $value) {
                $sourceFormat[] = DB::escape($value);
            }
            foreach($sources as $key=>$val){
                if (in_array($key,$sourceFormat)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['source_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            $strSourceRequets = implode(',', $sourceFormat);
        } else {
            foreach($sources as $key=>$val){
                $selected = '';
                $this->map['source_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            
        }

        $_strSourceRequets = '';
        if($strSourceRequets){
            $_strSourceRequets = $this->isObd 
                ? DashboardDB::getIncludeSourceIds($strSourceRequets, $this->group_id)
                : $strSourceRequets;
        }

        $_strBundleRequets = '';
        if($strBundleRequets){
            $_strBundleRequets = $this->isObd 
                ? DashboardDB::getIncludeBundleIds($strBundleRequets, $this->group_id)
                : $strBundleRequets;
        }

        $current_account_id =Dashboard::$account_id;
		$cond = '
				vs_adv_money.group_id='.$this->group_id.'
				AND vs_adv_money.date>="'.$start_time.'" AND vs_adv_money.date<="'.$end_time.'"
		';
        $dataUser = [];
        if(!Dashboard::$quyen_xem_bc_doi_nhom && !is_account_group_department() && !is_account_group_manager() and !Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt && !Dashboard::$admin_group && !Dashboard::$xem_khoi_bc_marketing){
            foreach ($users as $key => $value) {
                if($key!=$current_account_id){
                    unset($users[$key]);
                    continue;
                }
                $dataUser[$key] = $value;
            }
        } else {
            $dataUser = $users;
        }
        $this->map['account_id_list'] = array(''=>'Tất cả Marketing') + MiString::get_list($dataUser);
		if(Url::get('account_id')){
		    $cond .= ' AND vs_adv_money.account_id = "'.DB::escape(Url::get('account_id')).'"';
		}

        if($strSourceRequets){
            $cond .= ' and vs_adv_money.source_id  IN ('.$_strSourceRequets.')';
        }

        if($strBundleRequets){
            $cond .= ' and vs_adv_money.bundle_id  IN ('.$_strBundleRequets.')';
        }

        $this->map['marketing_name'] = '';
		///
		$reports = array();
		$reports['label']['id'] = 'label';
		$reports['label']['total'] = 'CPQC';
		$reports['label']['total_comment'] = 'Comment';
		$reports['label']['total_phone'] = 'Tổng sdt';
		$reports['label']['cost_per_phone'] = '$/sdt';
		$reports['label']['sale_order_qty'] = 'Số đơn sale';
		$reports['label']['toi_uu_order_qty'] = 'Số đơn tối ưu';
		$reports['label']['total_order_qty'] = 'Tổng số đơn';
		$reports['label']['cost_per_order'] = '$/đơn';
		$reports['label']['total_price'] = 'Tổng Doanh số';
		$reports['label']['cost_per_total'] = 'CP/DS';
		$reports['label']['date'] = 'Ngày';
        $reports['label']['account_id'] = 'Tài khoản';
        $reports['label']['max_value'] = 0;
		$reports['label']['time'] = 'Khung giờ';
        $reports['label']['cost_per_total_color'] = '';
        $reports['label']['ads_warning_color'] = '';

		if(!empty($_REQUEST['form_block_id'])){
    		$items = DashboardDB::get_adv_moneys($cond,5000);
    		$total = 0;
    		$total_comment = 0;
    		$total_phone = 0;
    		$cost_per_phone = 0;
    		$total_sale_order_qty = 0;
    		$total_toi_uu_order_qty = 0;
            $total_cost_per_total = 0;
    		$total_order_qty = 0;
    		$total_price = 0;
    		if($account_id = DB::escape(Url::get('account_id')) and $user=DB::fetch('select id,name from users where username="'.$account_id.'"')){
    			$this->map['marketing_name'] = '<div style="padding:10px;">Marketing: '.$user['name'].'</div>';
    		}
    		$no_revenue_status = DashboardDB::get_no_revenue_status();
    		foreach($items as $key=>$value) {
    		    $mkt_user_id = isset($dataUser[$value['account_id']])?$dataUser[$value['account_id']]['user_id']:'';
    		    if($mkt_user_id){
                    $full_name = $mkt_user_id? $dataUser[$value['account_id']]['full_name']:'';
                    $time_slot_values = [];
                    for ($i=1;$i<=7;$i++) {
                        $cond = 'orders.group_id='.$this->group_id.' ';
                        $sale_cond = 'orders.group_id='.$this->group_id.' ';
                        if($no_revenue_status){
                            $sale_cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                        }
                        if(Url::get('account_id')){
                            $cond .= ' and orders.user_created='.$user['id'].' ';
                            $sale_cond .= ' and orders.user_created='.$user['id'].' ';
                        }else{
                            if($mkt_user_id){
                                $cond .= ' and orders.user_created='.$mkt_user_id.' ';
                                $sale_cond .= ' and orders.user_created='.$mkt_user_id.' ';
                            }
                        }

                        if($strSourceRequets){
                            $cond .= ' and orders.source_id  IN ('.$_strSourceRequets.')';
                            $sale_cond .= ' and orders.source_id IN ('.$_strSourceRequets.')';
                        }

                        if($strBundleRequets){
                            $cond .= ' and orders.bundle_id  IN ('.$_strBundleRequets.')';
                            $sale_cond .= ' and orders.bundle_id IN ('.$_strBundleRequets.')';
                        }
                        $key_ = $value['date'].'_'.$i.'_'.$value['account_id'];
                        $reports[$key_]['id'] = $key_;
                        $value['total'] = $value['time_slot_'.$i];
                        $time_slot_values[$key_] = $value['time_slot_'.$i];
                        switch($i){
                            case 1://10
                                $reports[$key_]['time'] = '10h';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 10:00:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 10:00:00"';
                                break;
                            case 2://11h30
                                $reports[$key_]['time'] = '11h30';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 11:30:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 11:30:00"';
                                break;
                            case 3://14h
                                $reports[$key_]['time'] = '14h';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 14:00:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 14:00:00"';
                                break;
                            case 4://15h30
                                $reports[$key_]['time'] = '15h30';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 15:30:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 15:30:00"';
                                break;
                            case 5://17h30
                                $reports[$key_]['time'] = '17h30';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 17:30:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 17:30:00"';
                                break;
                            case 6://23h
                                $reports[$key_]['time'] = '23h';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<"'.$value['date'].' 23:00:00"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<"'.$value['date'].' 23:00:00"';
                                break;
                            case 7://24h
                                $reports[$key_]['time'] = '24h';
                                $cond .= 'and orders.created>="'.$value['date'].' 00:00:00" and  orders.created<="'.$value['date'].' 23:59:59"';
                                $sale_cond .= 'and orders.confirmed>="'.$value['date'].' 00:00:00" and  orders.confirmed<="'.$value['date'].' 23:59:59"';
                                break;
                        }
                        $total_price_ = DB::fetch('select sum(total_price) as total from orders where '.$sale_cond,'total');
                        $reports[$key_]['total_price'] = System::display_number($total_price_);
                        // $total_price += $total_price_;

                        $reports[$key_]['total_comment'] = DB::fetch('select count(*) as total from orders WHERE '.$cond,'total');
                        $total_comment += $reports[$key_]['total_comment'];
                        $reports[$key_]['total_phone'] = DB::fetch('select count(*) as total from orders WHERE '.$cond,'total');
                        // $total_phone += $reports[$key_]['total_phone'];
                        $reports[$key_]['total'] = System::display_number($value['total']);
                        // $total += $value['total'];
                        $reports[$key_]['total_phone'];

                        $reports[$key_]['sale_order_qty'] = DB::fetch('select count(*) as total from orders WHERE '.$sale_cond.' and orders.`type`='.DON_MOI,'total') + DB::fetch('select count(*) as total from orders WHERE '.$sale_cond.' and `orders`.`type`=0','total');;
                        // $total_sale_order_qty += $reports[$key_]['sale_order_qty'];
                        $reports[$key_]['toi_uu_order_qty'] = DB::fetch('select count(*) as total from orders WHERE '.$sale_cond.' and type='.DON_TOI_UU,'total');
                        // $total_toi_uu_order_qty += $reports[$key_]['toi_uu_order_qty'];
                        $reports[$key_]['total_order_qty'] = $reports[$key_]['sale_order_qty']+$reports[$key_]['toi_uu_order_qty'];
                        // $total_order_qty += $reports[$key_]['total_order_qty'];
                        // $cost_per_phone+=$cpp;

                        $reports[$key_]['date'] = Date_Time::to_common_date($value['date']);
                        $reports[$key_]['account_id'] = $full_name.'<br><i>('.$value['account_id'].')</i>';
                        $reports[$key_]['max_value'] = $max_value = max(array_values($time_slot_values));

                        $cost_per_total = $total_price_?(round($max_value/$total_price_,2)*100):0;
                        $ads_warning_color = DashboardDB::get_ads_warning_color($cost_per_total);
                        $reports[$key_]['ads_warning_color'] = $ads_warning_color;
                        $reports[$key_]['cost_per_total'] = $cost_per_total;

                        $cpp = $reports[$key_]['total_phone']?($max_value/$reports[$key_]['total_phone']):0;
                        $reports[$key_]['cost_per_phone'] = System::display_number($cpp);
                        $cpp = $reports[$key_]['total_order_qty']?($max_value/$reports[$key_]['total_order_qty']):0;
                        $reports[$key_]['cost_per_order'] = System::display_number($cpp);

                        if ($i == 7) {
                            $total_phone += $reports[$key_]['total_phone'];
                            $total_sale_order_qty += $reports[$key_]['sale_order_qty'];
                            $total_toi_uu_order_qty += $reports[$key_]['toi_uu_order_qty'];
                            $total_order_qty += $reports[$key_]['total_order_qty'];
                            $total_price += $total_price_;
                        }
                    }
                    $key_max = array_keys($time_slot_values, max($time_slot_values))[0];
                    $total += max(array_values($time_slot_values));
                    $cost_per_phone += str_replace(',', '', $reports[$key_max]['cost_per_phone']);
                }
    		}

    		$reports['total_column']['id'] = '';
    		$reports['total_column']['total'] = '<strong>'.System::display_number($total).'</strong>';
    		$reports['total_column']['total_comment'] = '<strong>'.System::display_number($total_comment).'</strong>';
    		$reports['total_column']['total_phone'] = '<strong>'.System::display_number($total_phone).'</strong>';
    		$reports['total_column']['cost_per_phone'] = '<strong>'.(($total_phone>0)?System::display_number(round($total/$total_phone,0)):0).'</strong>';
    		$reports['total_column']['sale_order_qty'] = '<strong>'.System::display_number($total_sale_order_qty).'</strong>';
    		$reports['total_column']['toi_uu_order_qty'] = '<strong>'.System::display_number($total_toi_uu_order_qty).'</strong>';
    		$reports['total_column']['total_order_qty'] = '<strong>'.System::display_number($total_order_qty).'</strong>';
    		
    		$reports['total_column']['date'] = '';
            $reports['total_column']['account_id'] = '';
            $reports['total_column']['max_value'] = 0;
    		$reports['total_column']['time'] = '';
    		$reports['total_column']['cost_per_order'] = '<strong>'.(($total_order_qty>0)?System::display_number(round($total/$total_order_qty,0)):0).'</strong>';
    		$reports['total_column']['total_price'] = '<strong>'.System::display_number($total_price).'</strong>';
    		$total_cost_per_total = (($total_price>0)?round($total/$total_price,2)*100:0);
    		$reports['total_column']['cost_per_total'] = '<strong>'.$total_cost_per_total.'</strong>';
            $reports['total_column']['ads_warning_color'] = DashboardDB::get_ads_warning_color($total_cost_per_total);
    		$reports['total_column']['date'] = '';
    		$reports['total_column']['time'] = '<strong>Tổng</strong>';
        }
		///////////////////////////////////////////
		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = 'Tháng '.str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['month_list'] = array(''=>'Chọn tháng') + $months;
        $account_groups = DashboardDB::getAccountGroup();
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả nhóm tài khoản') + MiString::get_list($account_groups);
		$this->map += DashboardDB::get_report_info();
		$this->parse_layout('adv_money',$this->map);
	}
    private function getBundles(){
        $group_id = Session::get('group_id');
        $sql = '
                select
                    bundles.id,bundles.name
                from
                    bundles
                WHERE
                    bundles.group_id='.$group_id.'
                order by
                    bundles.name
            ';
        $result = DB::fetch_all($sql);
        return $result;
    }
    private function getSource(){
        $group_id = Session::get('group_id');
        $sql = '
                select
                    order_source.id,order_source.name,order_source.default_select
                from
                    order_source
                where
                    order_source.group_id = '.$group_id.'
                    OR order_source.group_id=0
                order by
                    order_source.id
            ';
        $result = DB::fetch_all($sql);
        return $result;
    }
}
