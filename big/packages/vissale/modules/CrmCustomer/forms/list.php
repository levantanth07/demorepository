<?php
class ListCrmCustomerForm extends Form {
	protected $map;
	protected $conditions;
    const DEFAULT_IMPORT_STATUS = 27;
    function __construct() {
        require_once('packages/vissale/modules/CrmCustomerSchedule/db.php');
        require_once('packages/vissale/modules/CrmCustomerCallHistory/db.php');
		Form::Form('ListCrmCustomerForm');
		$this->init();
        $this->link_js('assets/vissale/js/lodash.min.js');
    }

	function on_submit() {
        if (!empty($_REQUEST['customer_id']) && !URL::iget('customer_id')) {
            Url::js_redirect(true,'Bạn vui lòng nhập đúng định dạng mã khách hàng');
        }

        //can xu ly file excel o day
		if (URL::get('confirm')) {
			require_once 'detail.php';
			foreach (URL::get('selected_ids') as $id) {
				CrmCustomerDB::delete($id);
				if ($this->is_error()) {
					return;
				}
			}
			Url::redirect_current(array('crm_group_id' => isset($_GET['crm_group_id']) ? $_GET['crm_group_id'] : '', 'zone_id' => isset($_GET['zone_id']) ? $_GET['zone_id'] : '', 'mobile' => isset($_GET['mobile']) ? $_GET['mobile'] : ''));
		}

        //can xu ly file excel o day
        if (!empty( $_FILES['import_crm'] )) {
            return $this->do_import_customer();
        }
	}

	function draw() {
	    $this->map = array();
        $title = (URL::get('do')=='delete')?'Xóa khách hàng':'Danh sách khách hàng';
        $conditions = $this->generate_conditions();
        $title .= $conditions['title'];
        $order_by = $conditions['order_by'];
        $_REQUEST['schedule_filter'] = URL::get('schedule_filter');
        //
		$item_per_page = 20;

        $total_record = CrmCustomerDB::get_total_customer($conditions);
		//var_dump( $total_record );die;

		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($total_record, $item_per_page, 5, false, 'page_no',
            ['branch_id','source_id','user_id','zone_id','contact_name','customer_name','customer_id','mobile','user_id','schedule_filter','from_date','to_date','act','do','crm_group_id','level','status_id']);

		$items = CrmCustomerDB::get_customers($conditions, $item_per_page ,$order_by);

        $this->modifyCustomers($items);

		$groups = CrmCustomerDB::get_crm_groups();
		$group_id_list = array('' => 'Xem theo nhóm') + MiString::get_list($groups);
        foreach ($groups as $key => $value) {
            foreach ($items as $k => $v) {
                if($key == $v['name_customer_extra']){
                    $items[$k]['name_customer_extra'] = $value['name'];
                }
            }
        }
		DB::query('select
			id, `zone`.`name`
			from `zone` where ' . IDStructure::direct_child_cond(ID_ROOT) . '
			order by structure_id asc'
		);
		$zone_id_list = array('' => 'Xem theo tỉnh thành') + MiString::get_list(DB::fetch_all());
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')) {
			if (is_string(UrL::get('selected_ids'))) {
				if (strstr(UrL::get('selected_ids'), ',')) {
					$just_edited_id['just_edited_ids'] = explode(',', UrL::get('selected_ids'));
				} else {
					$just_edited_id['just_edited_ids'] = array('0' => UrL::get('selected_ids'));
				}
			}
		}
		$this->map['contact_id_list'] = array('Người giới thiệu') + MiString::get_list(CrmCustomerDB::get_contacts());
		$this->map['source_id_list'] = array('Nguồn khách hàng') + MiString::get_list(AdminOrdersDB::get_source());
		$users = AdminOrdersDB::get_users(false, false, true);
		$this->map['user_id_list'] = array('' => 'Nhân viên phụ trách') + MiString::get_list($users, 'full_name');
		$this->map['title'] = $title;
		$this->map += array(
			'items' => $items,
			'paging' => $paging,
			'crm_group_id_list' => $group_id_list,
			'crm_group_id' => URL::get('crm_group_id', ''), 'zone_id_list' => $zone_id_list,
			'zone_id' => URL::get('zone_id', ''),
			'total' => $total_record
		);
		//get branches
        $branches = CrmCustomerScheduleDB::getGroupsByMasterGroup();
        $this->map['branch_id_list'] = [''=>'Chọn công ty (shop)'] + CrmCustomerScheduleDB::generate_branches($branches);
        $this->map['schedule_filter_list'] = CrmCustomerDB::get_schedule_filter();
        //customers status
        $this->map['level_list']     = [''=>'Lọc Level', 1=>'Level 1', 2=>'Level 2', 3=>'Level 3', 4=>'Level 4',5=>'Level 5',6=>'Level 6'];
        $this->map['customer_statuses']     = CrmCustomerDB::get_all_statuses();
        $this->map['customer_statuses_json']     = '<script> var all_statuses = '.json_encode( array_values(CrmCustomerDB::get_all_statuses()) ) . ';</script>';
        $this->map['level_count'] = CrmCustomerDB::get_customers_by_levels($conditions);
        //
        $layout = 'list';
        if (Url::get('do')=='sale') {
            $layout = 'list_sale';
        }
		$this->parse_layout($layout, $just_edited_id +
			$this->map
		);
        //
        $this->parse_layout('script', $just_edited_id +
            $this->map
        );
	}

    function init()
    {
        if (Url::get('do')=='export_excel') {
            $item_per_page = 10000;
            require_once 'packages/core/includes/utils/paging.php';
            $conditions = $this->generate_conditions();
            $items = CrmCustomerDB::get_customers($conditions, $item_per_page, NULL);
            $this->modifyCustomers($items);
            $this->export_excel($items);

            Url::js_redirect('customer', "Đã xuất excel thành công !", array());
            exit();
        }
        if (Url::post('cmd')=='callcloud') {
            echo $this->get_customer_by_phone(Url::post('mobile'));
            exit(0);
        }
    }

    protected function modifyCustomers(&$items)
    {
        $item_per_page = 20;
        $i = 1;
        $length = get_group_options('hide_phone_number');
        foreach ($items as $key => &$value) {
            $items[$key]['code'] = '';//format_code($value['id'], $value['group_id']);
            $items[$key]['no'] = $i + ((page_no() - 1) * $item_per_page);
            $items[$key]['created_time'] = ($value['created_time'] ? ' lúc ' . date('H:i\' d/m/y', $value['created_time']) : '');
            $items[$key]['appointed_time'] = ($value['appointed_time'] ? date('H:i\' d/m/y', $value['appointed_time']) : '');
            $items[$key]['noted_time'] = ($value['noted_time'] ? date('H:i\' d/m/y', $value['noted_time']) : '');
            $items[$key]['called_time'] = ($value['called_time'] ? date('H:i\' d/m/y', $value['called_time']) : '');
            $items[$key]['call_status'] = ( !empty( CrmCustomerCallHistoryDB::$status[$value['status_id']] ) ? CrmCustomerCallHistoryDB::$status[$value['status_id']] : '');
            $items[$key]['address'] = $value['address'] . ($value['address'] ? ', ' . $value['zone'] : $value['zone']);
            //$items[$key]['card_info'] = $value['card_name'] ? ($value['card_name'] . ' giảm ' . $value['card_discount_rate'] . '%' . '<br>(' . Date_Time::to_common_date($value['card_start_date']) . ' - ' . Date_Time::to_common_date($value['card_end_date']) . ')') : '';
            $items[$key]['mobile_show'] = $value['mobile'];
            $mbl1 = $value['mobile'];
            if(strlen(trim($mbl1)) > 0) {
                $mobileResult = ModifyPhoneNumber::hidePhoneNumber($mbl1, $length);
            }else{
                $mobileResult = '';
            }
            $items[$key]['mobile'] = $mobileResult;
            $i++;
        }
    }

    public function get_customer_by_phone($mobile)
    {
        $data = CrmCustomerDB::get_customer_by_phone($mobile);
        if(!empty($data['id'])) {$data['url'] = Url::build('customer', ['do'=>'view', 'cid'=>md5($data['id'].CATBE)]);}
        if (empty($data['id'])) {$data['url'] = Url::build('customer', ['do'=>'add', 'mobile'=>$mobile]);}
        $customer_age = Date_Time::get_age( substr($data['birth_date'],-4,4) );
        $data['customer_age'] = !empty($customer_age) ? " ($customer_age tuổi)" : '';
         return json_encode($data);
    }

    public function generate_conditions()
    {
        $title = '';
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        //share customer giua cac chi nhanh
        $shareCond = '';//" OR (crm_customer.id IN (SELECT customer_id FROM crm_customer_share WHERE crm_customer_share.group_id={$group_id}))";

        if ($account_type == TONG_CONG_TY) {
            $cond = '(crm_customer.group_id = ' . $group_id . ' or groups.master_group_id = ' . $group_id .')';
        } else {
            if ($master_group_id) {
                $cond = '(crm_customer.group_id = ' . $group_id . $shareCond . ')';
            } else {
                $cond = '(crm_customer.group_id = ' . $group_id . $shareCond . ')';
            }
        }
        //$cond .= ' AND (orders.status_id IN ('.THANH_CONG.','.CHUYEN_HANG.'))';
        $order_by =  NULL;
        $extra_select = '';
        $having_cond = '';
        $inner_join_sql = '';
        $mobile_cond = '';
        if ($mobile = URL::get('mobile')) {
            $mobile = preg_replace('/\D/', '', $mobile);
            $mobile = (float)$mobile;
            $mobile_cond .= " AND `crm_customer`.`mobile` LIKE '%$mobile'";
        }

        $cond .= ''
            //. (URL::iget('crm_group_id') ? ' and ' . IDStructure::child_cond(DB::structure_id('crm_customer_group', URL::iget('crm_group_id')), false, 'crm_customer_group.') . '' : '')
            . (URL::iget('crm_group_id') ? ' AND crm_group_id='.URL::iget('crm_group_id').' ' : '')
            . (URL::get('zone_id') ? ' and ' . IDStructure::child_cond(DB::fetch('select structure_id from `zone` where id="' . DB::escape(URL::sget('zone_id', 1)) . '"', 'structure_id'), false, 'zone.') . '' : '')
            . (URL::get('customer_name') ? ' and `crm_customer`.`name` LIKE "%' . DB::escape(URL::sget('customer_name')) . '%"' : '')
            . (URL::iget('customer_id') ? ' and `crm_customer`.`id` = ' . URL::iget('customer_id') . '' : '')
            . (URL::get('branch_id') ? ' and `crm_customer`.`group_id` = ' . URL::iget('branch_id') . '' : '')
            . (URL::iget('source_id') ? ' and `crm_customer`.`source_id` = ' . URL::iget('source_id') . '' : '')
            . (URL::iget('contact_id') ? ' and `crm_customer`.`contact_id` = ' . URL::iget('contact_id') . '' : '')
            . (URL::iget('user_id') ? ' and `crm_customer`.`user_id` = ' . URL::iget('user_id') . '' : '')
            . (URL::get('website') ? ' and `crm_customer`.`website` LIKE "%' . DB::escape(URL::sget('website')) . '%"' : '')
            . ($mobile ? $mobile_cond : '')
            . (URL::get('phone') ? ' and `crm_customer`.`mobile` LIKE "%' . DB::escape(URL::sget('phone')) . '%"' : '')
            . (URL::get('no_id') ? ' and `crm_customer`.`id` <> ' . URL::iget('no_id') . '' : '')
            . ((URL::get('do') == 'delete' and is_array(URL::get('selected_ids'))) ? ' and `crm_customer`.id in (' . URL::getSafeRawIDs('selected_ids') . ')' : '')
        ;
        //status_id
        $status_id = DB::escape(URL::get('status_id'));
        $level = DB::escape(URL::get('level'));
        if (!empty(URL::get('status_id'))) {
            $inner_join_sql .= " INNER JOIN `statuses` ON (`statuses`.id=`crm_customer`.`status_id` AND `crm_customer`.`status_id`=$status_id)";
        }
        if ( empty($status_id) && !empty(URL::get('level')) ) {
            $level_list = array_values ( CrmCustomerDB::get_all_statuses() );
            $level_ids = $this->get_status_ids($level_list, $level);
            $level_ids = implode(',', $level_ids);
            $inner_join_sql .= " INNER JOIN `statuses` ON ( `statuses`.id=`crm_customer`.`status_id` AND `crm_customer`.`status_id` IN ($level_ids) )";
        }

        //
        if ( !empty(URL::get('schedule_filter')) ) {
            $inner_join_sql .= ' INNER JOIN `orders` on (`orders`.customer_id=`crm_customer`.`id`)';
            $order_by = ' ORDER BY orders.id DESC'; // ABS(orders.created - NOW())
        }else{
            $inner_join_sql .= ' LEFT JOIN `orders` on (`orders`.customer_id=`crm_customer`.`id`)';
        }
        //
        //find schedule by from_date
        $from_date = strtotime('-3 day');
        if ( Url::get('from_date') ) {
            $from_date = Date_Time::to_time(Url::sget('from_date'));
        }
        //find schedule by to_date
        $to_date = strtotime(date('Y-m-d') . ' 23:59:59');
        if ( Url::get('to_date') ) {
            $to_date = Date_Time::to_time( Url::sget('to_date') . '  23:59:59');
        }
        $_REQUEST['from_date'] = date('d/m/Y', $from_date);
        $from_date = date('Y-m-d H:i:s', $from_date);
        $_REQUEST['to_date'] = date('d/m/Y', $to_date);
        $to_date = date('Y-m-d H:i:s', $to_date);

        //tìm khách mới, chỉ có 1 liệu trình
        if (URL::get('schedule_filter') === 'khach_moi') {
            $title .= ' MỚI';
            $extra_select .= " ,(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = crm_customer.id AND orders.created >= '$from_date' AND orders.created <= '$to_date' ) AS count_after";
            $extra_select .= " ,(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = crm_customer.id AND orders.created < '$from_date') AS count_before";
            $having_cond .=  ' HAVING count_after>0';
            $having_cond .= ' AND count_before=0';
        }
        //tìm khách tái khám,
        if (URL::get('schedule_filter')==='khach_mua_tiep') {
            $extra_select .= " ,(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = crm_customer.id AND orders.created >= '$from_date' AND orders.created <= '$to_date') AS count_after";
            $extra_select .= " ,(SELECT COUNT(*) FROM `orders` WHERE orders.customer_id = crm_customer.id AND orders.created < '$from_date') AS count_before";
            $title .= ' TÁI KHÁM';
            $having_cond .=  'HAVING count_after>0';
            $having_cond .= ' AND count_before>0';
        }

        //khách có hẹn liệu trình
        if ( URL::get('schedule_filter')==='process_schedule' ) {
            $title .= ' Khách đặt lại';
            $cond .= " AND (SELECT COUNT(orders.id) FROM `orders` 
                        WHERE orders.customer_id = crm_customer.id 
                        AND orders.created >= '{$from_date}'
                        AND orders.created <= '{$to_date}'
                        ) > 0";
            $inner_join_sql .= ' LEFT JOIN `orders` as O1 ON (O1.customer_id = crm_customer.id)';
        }

        // SELECT DAY(`birth_date`) FROM `crm_customer` WHERE `birth_date` != '00:00:00' AND DAY(`birth_date`)>DAY(NOW()) LIMIT 0, 1000
        if ( Url::get('do')=='search' && Url::get('birth')=='today' ) {
            $cond .= " AND crm_customer.`birth_date` != '00:00:00' AND  ( MONTH(crm_customer.`birth_date`) = MONTH(NOW()) AND DAY(crm_customer.`birth_date`) = DAY(NOW()) )";
        }
        if ( Url::get('do')=='search' && Url::get('birth')=='week' ) {
            $cond .= " AND DATE(crm_customer.`birth_date` + INTERVAL (YEAR(NOW()) - YEAR(crm_customer.`birth_date`)) YEAR) 
                        BETWEEN DATE(NOW() - INTERVAL WEEKDAY(NOW()) DAY)
                        AND DATE(NOW() + INTERVAL 6 - WEEKDAY(NOW()) DAY)";
        }
        return [
            'title'         => $title,
            'cond'          => $cond,
            'inner_join_sql' => $inner_join_sql,
            'having_cond'   => $having_cond,
            'extra_select'   => $extra_select,
            'order_by'   => $order_by,
        ];
    }

    function export_excel($items){
        //define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        /** Include PHPExcel */
        require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Vissale")
            ->setLastModifiedBy("Vissale")
            ->setTitle("Email List")
            ->setSubject("Email List")
            ->setDescription("Email List")
            ->setKeywords("office PHPExcel php")
            ->setCategory("Test result file");
        // set value for header
        $i=1;
        $letter_arr = array(
            1=>'A',
            2=>'B',
            3=>'C',
            4=>'D',
            5=>'E',
            6=>'F',
            7=>'G',
            8=>'H',
            9=>'I',
            10=>'J',
            11=>'K',
            12=>'L',
            13=>'M',
            14=>'N',
            15=>'O',
            16=>'P',
            17=>'Q',
            18=>'R'
        );
        $objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);
        $colomns = array(
            'STT','CODE','HO_TEN','MOBILE','NGHE_NGHIEP',
            'ADDRESS','TINH','PHU_TRACH','NHOM_KHACHHANG','CHU_Y','GHI_CHU','TG_NOTE', 'TG_CUOC_GOI','TRANG_THAI_CG','TG_LICH_HEN', 'COUNT_TOUR'
        );
        foreach($colomns as $value){
            $objWorkSheet->setCellValue(''.$letter_arr[$i].'1', $value);
            $objWorkSheet->getColumnDimension($letter_arr[$i])->setWidth(10);
            $i++;
        }
        $i=2;
        $c = 1;
        //var_dump( $items );die;
        foreach($items as $key=>$value){
            $ex_items[$key][1] = $c; $c++;
            $ex_items[$key][2] = $value['code'];
            $ex_items[$key][3] = $value['name'];
            $ex_items[$key][4] = (string)$value['mobile'];
            $ex_items[$key][5] = $value['job_title'];
            $ex_items[$key][6] = $value['address'];//
            $ex_items[$key][7] = $value['zone'];
            $ex_items[$key][8] = $value['follow_user_name'];//nguoi phu trach
            $ex_items[$key][9] = $value['crm_group_name'];//nhom khach hang
            $ex_items[$key][10] = $value['warning_note'];//luu y canh bao
            $ex_items[$key][11] = $value['description'];//luu y chung
            $ex_items[$key][12] = $value['noted_time'];//note
            $ex_items[$key][13] = $value['called_time'];
            $ex_items[$key][14] = $value['call_status'];//schedule
            $ex_items[$key][15] = $value['appointed_time'];//schedule
            $ex_items[$key][16] = !empty($value['count_after']) ? $value['count_after'] : 0;//schedule
        }
        foreach($ex_items as $key=>$value){
            // Add some data
            $objWorkSheet = $objPHPExcel->setActiveSheetIndex();
            $j = 1;
            foreach($colomns as $v){
                $objWorkSheet->setCellValue(($letter_arr[$j].$i), $value[$j]);
                $j++;
            }
            $i++;
        }
        // Rename worksheet
        //echo date('H:i:s') , " Rename worksheet" , EOL;
        //$objPHPExcel->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        //	echo date('H:i:s') , " Write to Excel2007 format" , EOL;
        $callStartTime = microtime(true);
        //echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
        $subfix = 'khachhang_'.Session::get('group_id').'_'.Session::get('user_id').'_'.date('d_m_Y_H:i:s');
        $file = ''.$subfix.'.xls';
        header('Content-Encoding: UTF-8');
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment;filename="'.$file.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        echo "\xEF\xBB\xBF";  // BOM header UTF-8
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_clean();
        $objWriter->save('php://output');
        exit;
    }

    /**
     * @param
     * @return bool
     */
    function do_import_customer()
    {
        $count_success = 0;
        $count_fail = 0;
        $excel_file = $_FILES['import_crm'];
        $temp_file = $excel_file['tmp_name'];
        $customers = read_excel($temp_file);
        $listFailedMobiles = '';
        foreach ($customers as $key => $customer) {
            if ($key < 2) {
                continue;
            }
            $customer[6] = preg_replace('/\D/', '', $customer[6]);
            $data = CrmCustomerDB::get_customer_by_phone($customer[6]);

            if ( $data['customer_name']!='') {
                $count_fail++;
                $listFailedMobiles .= '\n'.$customer[6];
                continue;
            }

            $codes = $this->getCustomerCode( $customer[1] );
            $groupCode = $codes[0];
            $crmGroupId = $this->getCategoryId( $customer[2] );
            $maxCode = $this->getMaxCustomerCodeByGroupCode($groupCode);
            $group_id = $this->getGroupIdByGroupCode($groupCode);

            $insertData = [
                'time' => time(),
                'creator_id' => 2,
                'code' => intval($maxCode)+1,
                'group_id' => $group_id ? $group_id : Session::get('group_id'),
                'name' => $customer[3],
                'gender' => $this->getGender( $customer[4] ),
                'mobile' => CrmCustomerDB::convertVietnamMobileNo($customer[6]),
                'address' => $customer[8],
                'type' => 2,
                'crm_group_id' => $crmGroupId,
                'master_group_id' => 1943,
                'imported_account_id' => Session::get('user_id'),
                'imported_time' => date('Y-m-d'),
                'shared' => 0,
                'status_id' => self::DEFAULT_IMPORT_STATUS,
            ];

            DB::insert('crm_customer', $insertData);
            $count_success++;
        }
        Url::js_redirect('customer',"Dữ liệu đã cập nhật, thành công:$count_success, thất bại:$count_fail" . $listFailedMobiles, array('do'));
        return true;
    }

    function getCustomerCode($importedCode)
    {
        return explode('.', $importedCode);
    }

    function getCategoryId($categoryName)
    {
        $categoryName = DB::escape($categoryName);
        $id = DB::fetch("SELECT id as id FROM crm_customer_group WHERE name LIKE '$categoryName'", 'id');
        if ($id) {
            return $id;
        }
        return DB::insert('crm_customer_group', ['name' => $categoryName,'description' => NULL , 'group_id' => 1943]);
    }

    function getMaxCustomerCodeByGroupCode($groupCode)
    {
        $groupCode = DB::escape($groupCode);
        $sql = "
            SELECT MAX(crm_customer.code) as code 
            from `groups`
            INNER JOIN crm_customer_group ON (crm_customer_group.group_id = groups.id)
            INNER JOIN crm_customer ON (crm_customer.group_id = groups.id)
            WHERE groups.code LIKE '$groupCode'
    ";

        return DB::fetch($sql, 'code');
    }

    function getGroupIdByGroupCode($groupCode)
    {
        $groupCode = DB::escape($groupCode);
        $sql = "SELECT id
            from `groups`
            WHERE code LIKE '$groupCode'";
        return DB::fetch($sql, 'id');
    }

    function getGender($gender)
    {
        $gender = mb_strtolower($gender);
        switch ($gender) {
            case 'nam':
                return 1;
            case 'nu' :
                return 2;
            default :
                return  0;
        }
    }

    private function get_status_ids($levels, $level_no){
        $items = array_map( function ($element) use ($level_no) {
            if ($element['level']===$level_no) {
                return $element['id'];
            }
        } , $levels);
        $ids = [];

        foreach ($items as $key => $value) {
            if ( empty($value) ) {
                continue;
            }
            $ids[] = $value;
        }
        return $ids;
    }
}
