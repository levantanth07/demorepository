<?php
use GuzzleHttp\Client;
@require_once(ROOT_PATH.'packages/vissale/lib/php/log.php');
class AdminUserInfoInformationForm extends Form
{
    const CRM_OFF = 0;
    const CRM_ON = 1;
    const CRM_WAIT_OFF = 2;
    const CRM_WAIT_ON = 3;

    protected $map;
    protected $admin_tuha;
    protected $admin_group;
    protected $admin_mkt;
    function __construct()
    {
        $this->admin_tuha = (User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))?true:false;
        $this->admin_group = (Session::get('admin_group'))?true:false;
        $this->admin_mkt = check_user_privilege('ADMIN_marketing')?true:false;
        Form::Form('AdminUserInfo');
        if($this->admin_group or $this->admin_tuha) {
            $this->add('name', new TextType(true, 'invalid_full_name', 0, 50));
            $this->add('address', new TextType(false, 'invalid_address', 0, 200));
            $this->add('phone', new PhoneType(false, 'invalid_phone_number'));
            $this->add('email', new EmailType(false, 'email_invalid'));
        }
        $this->link_css('assets/default/css/cms.css');
        $this->link_js('packages/core/includes/js/jquery/datepicker.js');
        $this->link_css('assets/default/css/jquery/datepicker.css');
        $this->link_js('assets/standard/js/multi.select.js');
        $this->link_css('assets/standard/css/multi-select.css?v=18102021');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->link_js('packages/core/includes/js/helper.js?v=20042022');

        $this->group_id = Session::get('group_id');
        if($this->admin_tuha){
            $group_id = URL::getUInt('group_id') ? URL::getUInt('group_id') : $this->group_id;
            $this->handleMedidoc($group_id);
        }

    }
    function on_submit()
    {
        $account_type = Session::get('account_type');
        if($this->admin_tuha){
            $group_id = Url::iget('group_id')?Url::iget('group_id'):Session::get('group_id');
        }else{
            if($account_type==3){
                $group_id = Url::iget('group_id')?DB::escape(Url::iget('group_id')):Session::get('group_id');
            }else{
                $group_id = Session::get('group_id');
            }
        }
        
        $systemGroupId = Url::iget('system_group_id');
        
        $isGroupIdInOBD = isObd($group_id);
        $isNewSystemGroupInOBD = isSystemGroupInOBD($systemGroupId);
        if (!$isGroupIdInOBD && $isNewSystemGroupInOBD) {
            $_sql = "SELECT *
                FROM bundles 
                WHERE group_id = $group_id
                    AND ref_id = 0
                LIMIT 1";
    
            $invalidBundle = DB::fetch($_sql);
            if ($invalidBundle) {
                return Url::js_redirect(true, '⚠️ Vui lòng liên hệ shop để cập nhật nguồn nhóm sản phẩm hệ thống: [Sản phẩm] > [Phân loại]',
                ['group_id']);
                 // To do something;
            }//end if

            $_sql = "SELECT *
                FROM order_source
                WHERE group_id = $group_id
                    AND ref_id = 0
                LIMIT 1";
    
            $invalidSource = DB::fetch($_sql);
            if ($invalidSource) {
                return Url::js_redirect(true, '⚠️ Vui lòng liên hệ shop để cập nhật nguồn marketing hệ thống: [Đơn hàng] > [Quản lý nguồn đơn hàng]',
                 ['group_id']);
                // To do something;
            }//end if
        }//end if

        $this->handleChangeGroupSystem(
            $isGroupIdInOBD, 
            $isNewSystemGroupInOBD, 
            $group_id
        );

        $logDescription = '';
        $arrPercent = array();
        for($percent=1;$percent<=100;$percent++){
            $arrPercent[$percent] = $percent;
        }
        $arrGroupOptionData = array(
            'mkt_cost_per_revenue_danger' => array(
                'type' => 'select',
                'name' => 'Chi phí/doanh thu nguy hiểm',
                'request' => Url::get('mkt_cost_per_revenue_danger'),
                'arrData' => $arrPercent,
                'suffix' => '%',
            ),
            'mkt_cost_per_revenue_warning' => array(
                'type' => 'select',
                'name' => 'Chi phí/doanh thu cảnh báo',
                'request' => Url::get('mkt_cost_per_revenue_warning'),
                'arrData' => $arrPercent,
                'suffix' => '%',
            ),
        );

        $publishEventOnInsert = false;
        $publishEventOnUpdate = false;
        $optionsChangedIds = [];
        $groupOptionInsertedId = null;

        if($this->check() && $this->validateUploadImage('image_url'))
        {

            //$group = DB::select('groups','id='.$group_id);
            $old_row = DB::select('groups','id='.$group_id);

            $clone_data_vichat['group_id'] = $group_id;
            $clone_data_vichat['page_counter'] = $old_row['page_counter'];
            $clone_data_vichat['user_counter'] = $old_row['user_counter'];
            $clone_data_vichat['expired_date'] = $old_row['expired_date'];
            $this->updateGroupToVichat($clone_data_vichat);

            $row = array(
                'name' => DB::escape(Url::get('name')),
                'address' => DB::escape(Url::get('address')),
                'phone' => DB::escape(Url::get('phone')),
                'email' => DB::escape(Url::get('email')),
                'prefix_post_code' => DB::escape(Url::get('prefix_post_code')),
                'prefix_account' => DB::escape(Url::get('prefix_account')),
                'allow_ips' => DB::escape(Url::get('allow_ips'))
            );

            // Bật đồng bộ
            if($this->admin_tuha && $this->isSyncCrmEnable($old_row)) {
                self::syncCrm($group_id, $old_row, $row['is_crm'] = self::CRM_WAIT_ON);
            }

            // Tắt đồng bộ
            if($this->admin_tuha && $this->isSyncCrmDisable($old_row)){
                self::syncCrm($group_id, $old_row, $row['is_crm'] = self::CRM_WAIT_OFF);
            }
            
            // Callio + Voip24h
            $integrate_callio = Url::check('integrate_callio');
            $integrate_voip24h = Url::check('integrate_voip24h');

            if ($this->admin_group) {
                if ($integrate_callio && !$old_row['callio_info']) {
                    // integrateBtn
                    if (isset($_POST['integrateBtn'])) {
                        $callio_name = $_POST['callio_name'];
                        $callio_email = $_POST['callio_email'];
                        $callio_phone = $_POST['callio_phone'];
                        // dang ky khach hang callio
                        $url = CALLIO_AGENCY_HOST . '/client';
                        $payload = array(
                            "domain"    => "big-".$group_id.".phonenet.io",
                            "country"   => "5e743fd08f8a4f7c65f33b0a",
                            "plan"      => "5ec38abc92a9e41b3be3fa95",
                            "timezone"  => "Asia/Ho_Chi_Minh",
                            "email"     => $callio_email,
                            "phone"     => $callio_phone,
                            "companyName" => $callio_name
                        );
                        $dataRes = EleFunc::callioPost($url, $payload);
                        if (isset($dataRes['id'])) {
                            $row2 = array();
                            $row2['integrate_voip24h'] = 0;
                            $row2['integrate_callio'] = 1;
                            $row2['callio_info'] = json_encode($dataRes, JSON_UNESCAPED_UNICODE);
                            DB::update('groups',$row2,' id = '.$group_id.'');

                            $logDescription = 'Đã tích hợp Callio (tổng đài ảo)';
                            System::log('UPDATE_SHOP_SETTING', 'Cập nhật thông tin cài đặt cửa hàng', $logDescription, '', '', false, array());

                            $location = Url::build_current(['group_id']);
                            echo '<script>
                            alert("Dữ liệu cập nhật thành công!");
                            location="/'.$location.'";
                        </script>';
                            exit();
                        } else {
                            $location = Url::build_current(['group_id']);
                            echo '<script>
                            alert("Tích hợp thất bại! '.$dataRes[0]['msg'].'");
                            location="/'.$location.'";
                        </script>';
                            exit();
                        }
                    }
                } elseif (!$integrate_callio && $old_row['callio_info'] && $old_row['integrate_callio']) {
                    $row['integrate_callio'] = 0;
                    $logDescription = 'Đã huỷ tích hợp Callio (tổng đài ảo)';
                } elseif ($integrate_callio && $old_row['callio_info'] && !$old_row['integrate_callio']) {
                    $row['integrate_voip24h'] = 0;
                    $row['integrate_callio'] = 1;
                    $logDescription = 'Đã tích hợp Callio (tổng đài ảo)';
                }

                if ($integrate_voip24h && !$old_row['voip24h_info']) {
                    // integrateBtn
                    if (isset($_POST['integrateBtn'])) {
                        $voip24h_domain = $_POST['voip24h_domain'];
                        // dang ky khach hang callio
                        $row1 = array();
                        $row1['integrate_callio'] = 0;
                        $row1['integrate_voip24h'] = 1;
                        $row1['voip24h_info'] = json_encode(array('domain' => $voip24h_domain));
                        DB::update('groups',$row1,' id = '.$group_id.'');

                        $logDescription = 'Đã tích hợp Voip24h';
                        System::log('UPDATE_SHOP_SETTING', 'Cập nhật thông tin cài đặt cửa hàng', $logDescription, '', '', false, array());

                        $location = Url::build_current(['group_id']);
                        echo '<script>
                            alert("Dữ liệu cập nhật thành công!");
                            location="/'.$location.'";
                        </script>';
                        exit();
                    }
                } elseif (!$integrate_voip24h && $old_row['voip24h_info'] && $old_row['integrate_voip24h']) {
                    $row['integrate_voip24h'] = 0;
                    $logDescription = 'Đã huỷ tích hợp Voip24h';
                } elseif ($integrate_voip24h && $old_row['voip24h_info'] && !$old_row['integrate_voip24h']) {
                    $row['integrate_callio'] = 0;
                    $row['integrate_voip24h'] = 1;
                    $logDescription = 'Đã tích hợp Voip24h';
                }
            }
            $checkCallioInfo = false;
            if (isset($old_row['callio_info']) || isset($row['callio_info'])) {
                $checkCallioInfo = true;
            }
            $checkVoip24hInfo = false;
            if (isset($old_row['voip24h_info']) || isset($row['voip24h_info'])) {
                $checkVoip24hInfo = true;
            }
            //
            
            if($this->admin_tuha){
                $row['expired_date'] = DB::escape(Url::get('expired_date'));//Date_Time::to_sql_date();
                $row['system_group_id'] = Url::iget('system_group_id');
                $row['master_group_id'] = Url::iget('master_group_id');
                $row['page_counter'] =  DB::escape(Url::get('page_counter'));
                $row['user_counter'] =  DB::escape(Url::get('user_counter'));
                $real_total_user = DB::fetch('select count(*) as total from account where is_active=1 and  group_id='.$group_id,'total');
                $active = Url::check('active')?1:'0';

                if (!$this->isTrialAccount($old_row)) {
                    if ($active) {
                        $need_to_disable_users = $real_total_user - $row['user_counter'];
                    } else {
                        $need_to_disable_users = $real_total_user;
                    }

                    if ($need_to_disable_users > 0) {
                        $users_need_to_be_disable = DB::fetch_all('select id,username from users where group_id=' . $group_id . ' order by id desc limit 0,' . $need_to_disable_users);
                        foreach ($users_need_to_be_disable as $k_ => $v_) {
                            DB::update('account', ['is_active' => 0], 'id="' . $v_['username'] . '"');
                        }
                    }
                }

                $row['description'] = Url::get('description');
                $row['account_type'] = Url::get('account_type');
                $row['code'] = trim(Url::get('code'));
                $row['active'] = $active;
            }
            if($this->admin_group){
                $row['date_established'] = Url::get('date_established')?DB::escape(Url::get('date_established')):AdminUserInfo::$date_init_value;//Date_Time::to_sql_date();
                if( Session::get('account_type')==3) {
                    if ($old_row['phone_store_id'] and $old_row['phone_store_id'] != Url::get('phone_store_id')) {
                        DB::query('update phone_store set total_group = total_group - 1 where id=' . $old_row['phone_store_id']);
                    }
                    if ($phone_store_id = DB::escape(Url::get('phone_store_id'))) {
                        $row['phone_store_id'] = $phone_store_id;
                        DB::query('update phone_store set total_group = total_group + 1 where id=' . $phone_store_id);
                    } else {
                        $row['phone_store_id'] = 0;
                    }
                }
                //update api, khoand added in 17/12/2018
                AdminUserInfoDB::update_api();
                if(Url::check('syn_group_name')){
                    AdminUserInfoDB::sys_work_tuha($group_id,Url::get('name'));
                }
            }
            if($this->admin_group or $this->admin_tuha){
                $this->updateGroupAvatar($group_id);
                $row['modified'] = date('Y-m-d H:i:s');

                // $this->handleChangeGroupSystem(
                //     $isGroupIdInOBD, 
                //     $isNewSystemGroupInOBD, 
                //     $group_id
                // );
                DB::update('groups',$row,' id = '.$group_id.'');


                $arrGroupVi = array(
                    'name' => 'Tên Shop',
                    'code' => 'Tài khoản sở hữu (owner)',
                    'description' => 'Ghi chú của admin',
                    'email' => 'Email',
                    'address' => 'Địa chỉ',
                    'phone' => 'Điện thoại',
                    'account_type' => array(
                        'name' => 'Loại tài khoản',
                    ), // select
                    'active' => 'Kích hoạt SHOP',
                    'master_group_id' => array(
                        'name' => 'Thuộc hệ thống',
                    ), //select
                    'prefix_post_code' => 'Mã công ty (Mã bưu cục)',
                    'system_group_id' => array(
                        'name' => 'Thuộc hệ thống'
                    ), //select
                    'prefix_account' => 'Tiền tố tạo tài khoản',
                    'allow_ips' => 'Chỉ cho phép vào từ IP',
                    'phone_store_id' => 'Kho số',
                    'syn_group_name' => 'Đồng bộ tên Work.tuha.vn',
                    'expired_date'=>'Ngày hết hạn',
                    'is_crm'=>'Đồng bộ CRM',
                );
                foreach($row as $keyRow => $rowRow){
                    if(isset($old_row[$keyRow]) && $old_row[$keyRow] != $row[$keyRow] && $keyRow != 'modified'){
                        $infoFieldGroupData = $arrGroupVi[$keyRow];
                        if(is_array($infoFieldGroupData)){
                            $fieldName = $infoFieldGroupData['name'];
                            $arrDataParent = array();
                            if($keyRow == 'account_type'){
                                $arrDataParent =  array(
                                    '0'=>'Tài khoản thường',
                                    1=>'Dùng thử',
                                    2=>'Tài khoản cũ',
                                    TONG_CONG_TY=>'TỔNG CÔNG TY');
                            } else if($keyRow == 'master_group_id'){
                                $arrDataParent = array('0'=>'Chọn nhóm cha') + MiString::get_list(DB::fetch_all('select id,name from `groups` where account_type=3'));
                            } else if($keyRow == 'system_group_id'){
                                $arrDataParent = array('0'=>'Chọn nhóm cha') + MiString::get_list(AdminUserInfoDB::get_groups_systems());
                            }
                            $oldValue = (isset($arrDataParent[$old_row[$keyRow]])) ? $arrDataParent[$old_row[$keyRow]] : '';
                            $newValue = (isset($arrDataParent[$row[$keyRow]])) ? $arrDataParent[$row[$keyRow]] : '';
                        }else{
                            $fieldName = $infoFieldGroupData = $arrGroupVi[$keyRow];
                            $oldValue = $old_row[$keyRow];
                            $newValue = $row[$keyRow];
                        }
                        if($keyRow == 'is_crm'){
                            // $dataSync = ['Tắt đồng bộ', 'Bật đồng  bộ', 'Chờ tắt đồng bộ', 'Chờ bật đồng bộ'];
                            // $logDescription .= 'Thay đổi ' . $fieldName . ' từ "' . $dataSync[$oldValue] . '" => "' . $dataSync[$newValue] . '" <br>';
                        }else{
                            $logDescription .= 'Thay đổi ' . $fieldName . ' từ "' . $oldValue . '" => "' . $newValue . '" <br>';
                        }
                        
                    }
                }
                $arrGroupOptionData += array(
                    'business_model' => array(
                        'type' => 'select',
                        'name' => 'Mô hình kinh doanh',
                        'request' => Url::get('business_model'),
                        'arrData' => array(
                            0 => 'Mặc định',
                            1 => 'Bán theo SPA/TMV',
                            2 => 'Mô hình bán lẻ - POS'
                        ),
                    ),
                    'show_full_name' => array(
                        'type' => 'check',
                        'name' => 'Hiển thị Họ Và Tên thay cho Tên tài khoản',
                        'request' => Url::check('show_full_name')?1:0
                    ),
                    'integrate_shipping' => array(
                        'type' => 'check',
                        'name' => 'Kích hoạt kết nối vận chuyển',
                        'request' => Url::check('integrate_shipping')?1:0
                    ),
                    // 'require_address' => array(
                    //     'type' => 'check',
                    //     'name' => 'Xác nhận yêu cầu nhập tỉnh thành, quận huyện, phường xã',
                    //     'request' => Url::check('require_address')?1:0
                    // ),
                    'enable_product_rating' => array(
                        'type' => 'check',
                        'name' => 'Đánh giá sản phẩm',
                        'request' => Url::check('enable_product_rating')?1:0
                    ),
                    'show_product_detail' => array(
                        'type' => 'check',
                        'name' => 'Hiển thị đầy đủ thông tin sản phẩm (Mã SP, Mầu, Size) khi hiển thị ở danh sách và excel',
                        'request' => Url::check('show_product_detail')?1:0
                    ),
                    'min_search_phone_number' => array(
                        'type' => 'select',
                        'name' => 'Tìm số điện thoại tối thiểu',
                        'request' => Url::get('min_search_phone_number'),
                        'arrData' => array(
                            3 =>'3 số',
                            4 =>'4 số',
                            5 =>'5 số',
                            6 =>'6 số'
                        ),
                    ),
                    'default_sort_of_order_list' => array(
                        'type' => 'select',
                        'name' => 'Sắp xếp đơn hàng mặc định ưu tiên theo',
                        'request' => Url::get('default_sort_of_order_list'),
                        'arrData' => array(
                            'orders.id DESC'=>'Đơn mới nhất',
                            'orders.user_assigned ASC,orders.id DESC'=>'Đơn chưa xử lý'
                        ),
                    ),
//                    'duplicate_type' => array(
//                        'type' => 'select',
//                        'name' => 'Trùng đơn theo phân loại sản phẩm',
//                        'request' => Url::get('duplicate_type')?Url::get('duplicate_type'):'0',
//                        'arrData' => array(
//                            0 =>'Chỉ theo SĐT',
//                            1 =>'Theo SĐT + Phân loại'
//                        ),
//                    ),
                    'show_history_order' => [
                        'type' => 'check_reverse',
                        'name' => 'Hiện lịch sử đơn hàng tại popup sửa nhanh đơn hàng',
                        'request' => Url::check('show_history_order')? 0 : 1
                    ],
                    'show_price_in_export_invoice' => array(
                        'type' => 'check',
                        'name' => 'Hiển thị giá bán trên phiếu xuất kho',
                        'request' => Url::check('show_price_in_export_invoice')?1:0
                    ),
                    'create_export_invoice_when_confirmed' => array(
                        'type' => 'check',
                        'name' => 'Tạo phiếu xuất kho khi đơn xác nhận',
                        'request' =>  Url::check('create_export_invoice_when_confirmed')?1:0
                    ),
                    'create_export_invoice_when_delivered' => array(
                        'type' => 'check',
                        'name' => 'Tạo phiếu xuất kho khi đơn chuyển hàng',
                        'request' => Url::check('create_export_invoice_when_delivered')?1:0,
                    ),
                    'create_import_invoice_when_return' => array(
                        'type' => 'check',
                        'name' => 'Tạo phiếu nhập kho đơn Đã trả hàng về kho',
                        'request' => Url::check('create_import_invoice_when_return')?1:0,
                    ),
                    'hide_total_amount' =>array(
                        'type' => 'check',
                        'name' => 'Ẩn Tổng tiền đơn hàng với MKT',
                        'request' => Url::check('hide_total_amount')?1:0
                    ),

                    'users_can_edit_order' => array(
                        'type' => 'select',
                        'name' => 'Quyền sửa đơn hàng',
                        'request' => Url::get('users_can_edit_order'),
                        'arrData' => array(
                            0 =>'Tất cả',
                            1 =>'Trừ marketing (Với đơn đã chia)'
                        ),
                    ),
                    // 'choose_time_declare_advertising_money' => array(
                    //     'type' => 'select',
                    //     'name' => 'Chọn khung giờ Khai báo tiền QC',
                    //     'request' => Url::get('choose_time_declare_advertising_money'),
                    //     'arrData' => array(
                    //         0 =>'Tất cả',
                    //         1 => AdvMoney::TIME_SLOT_1,
                    //         2 => AdvMoney::TIME_SLOT_2,
                    //         3 => AdvMoney::TIME_SLOT_3,
                    //         4 => AdvMoney::TIME_SLOT_4,
                    //         5 => AdvMoney::TIME_SLOT_5,
                    //         6 => AdvMoney::TIME_SLOT_6,
                    //         7 => AdvMoney::TIME_SLOT_7,
                    //     ),
                    // ),
                    'time_to_refesh_order' => array(
                        'type' => 'select',
                        'name' => 'Tự động làm mới danh sách đơn hàng',
                        'request' => Url::get('time_to_refesh_order'),
                        'arrData' => array(
                            0 => 'Tắt',
                            5 => '5 phút',
                            10 => '10 phút',
                            15 => '15 phút',
                            20 => '20 phút',
                            30 => '30 phút',
                            60 => '60 phút',
                            75 => '75 phút',
                            90 => '90 phút'
                        ),
                    ),
                    'hide_phone_number' => array(
                        'type' => 'select',
                        'name' => 'Ẩn số điện thoại',
                        'request' => Url::get('hide_phone_number'),
                        'arrData' => array(
                            3 => 'Ẩn 3 số',
                            4 => 'Ẩn 4 số',
                            5 => 'Ẩn 5 số',
                            6 => 'Ẩn 6 số',
                            7 => 'Ẩn 7 số',
                            8 => 'Ẩn 8 số',
                            9 => 'Ẩn 9 số',
                            10 => 'Ẩn 10 số'
                        ),
                    ),
                    'add_deliver_order' => array(
                        'type' => 'select',
                        'name' => 'Cộng phí vận chuyển vào tổng tiền đơn hàng',
                        'request' => Url::get('add_deliver_order'),
                        'arrData' => array(
                            1 => 'Cho phép',
                            2 => 'Không cho phép'
                        ),
                    ),
                    'sale_can_self_assigned' => array(
                        'type' => 'select',
                        'name' => 'SALE có thể tự chia đơn cho chính mình',
                        'request' => Url::get('sale_can_self_assigned'),
                        'arrData' => array(
                            0 => 'Không kích hoạt',
                            1 => 'Kích hoạt'
                        ),
                    ),
                    'sale_can_assigned_created_user' => array(
                        'type' => 'select',
                        'name' => 'SALE được gán người tạo đơn',
                        'request' => Url::get('sale_can_assigned_created_user'),
                        'arrData' => array(
                            0 => 'Không kích hoạt',
                            1 => 'Kích hoạt'
                        ),
                    ),
                        'chup_anh_nhan_vien' => array(
                        'type' => 'check',
                        'name' => 'Chụp ảnh nhân viên đang sử dụng',
                        'request' => Url::check('chup_anh_nhan_vien')?1:0,
                    ),
                    'disable_negative_export' => array(
                        'type' => 'check',
                        'name' => 'Không cho xuất âm',
                        'request' => Url::check('disable_negative_export')?1:0
                    ),
                    'reset_pass_periodic' => [
                        'type' => 'select',
                        'name' => 'Số ngày cập nhật mật khẩu / 1 lần',
                        'request' => URL::getUInt('reset_pass'),
                        'arrData' => [
                            0 =>'không kích hoạt',
                            7 =>'7 ngày',
                            14 =>'14 ngày',
                            20 =>'20 ngày',
                            30 =>'30 ngày',
                            45 =>'45 ngày',
                            60 =>'60 ngày',
                            90 =>'90 ngày'
                        ]
                    ]
                );
                if (is_group_owner()) {
                    $arrGroupOptionData += array(
                        'show_phone_number_excel_order' => array(
                            'type' => 'check',
                            'name' => 'Hiện Số điện thoại xuất excel đơn hàng',
                            'request' => Url::check('show_phone_number_excel_order') ? 1 : 0,
                        ),
                        'show_phone_number_print_order' => array(
                            'type' => 'check',
                            'name' => 'Hiện Số điện thoại in đơn hàng',
                            'request' => Url::check('show_phone_number_print_order') ? 1 : 0,
                        ),
                        'hien_sdt_bung_don' => array(
                            'type' => 'check',
                            'name' => 'Hiện Số điện thoại bung đơn',
                            'request' => Url::check('hien_sdt_bung_don')?1:0,
                        ),
                        'show_full_name_export_excel_order' => array(
                            'type' => 'check',
                            'name' => 'Hiện tên sản phẩm đầy đủ khi xuất excel đơn hàng',
                            'request' => Url::check('show_full_name_export_excel_order') ? 1 : 0,
                        ),
                    );
                }
            }

            if(is_group_owner() || $this->canChangeShopInfomation()) {
                $arrGroupOptionData['reset_pass_periodic'] = [
                    'type' => 'select',
                    'name' => 'Số ngày cập nhật mật khẩu / 1 lần',
                    'request' => URL::getUInt('reset_pass'),
                    'arrData' => [
                        0 =>'không kích hoạt',
                        7 =>'7 ngày',
                        14 =>'14 ngày',
                        20 =>'20 ngày',
                        30 =>'30 ngày',
                        45 =>'45 ngày',
                        60 =>'60 ngày',
                        90 =>'90 ngày'
                    ]
                ];

                if($this->handleResetPassPeriodic(URL::getUInt('reset_pass'), $group_id)) {
                    $logDescription .= 'Yêu cầu đổi pass ngay lập tức';
                }
            }
        }

        $listOldGroupOptionData = DB::fetch_all('select group_options.* from group_options where group_id='.$group_id);
        $arrOldGroupOptionData = array();
        foreach($listOldGroupOptionData as $rowOldGroupOptionData){
            $arrOldGroupOptionData[$rowOldGroupOptionData['key']] = $rowOldGroupOptionData;
        }
        $strValueInsertGroupOption = '';
        $strUpdateGroupOption = '';
        foreach($arrGroupOptionData as $keyGroupOptionData => $rowGroupOptionData){
            $m_key = $keyGroupOptionData.'_'.$group_id;
            if(!System::is_local()){
                $m_key_value = MC::get_items($m_key);
            }
            $suffix = (isset($arrGroupOptionData[$keyGroupOptionData]['suffix'])) ? $arrGroupOptionData[$keyGroupOptionData]['suffix'] : '';
            if(isset($arrOldGroupOptionData[$keyGroupOptionData])){
                if($arrOldGroupOptionData[$keyGroupOptionData]['value'] != $arrGroupOptionData[$keyGroupOptionData]['request']){
                    $newValue = $arrGroupOptionData[$keyGroupOptionData]['request'];
                    $oldValue = $arrOldGroupOptionData[$keyGroupOptionData]['value'];
                    $strUpdateGroupOption .= 'update group_options set value = "'.$newValue
                        .'" where id = '.$arrOldGroupOptionData[$keyGroupOptionData]['id'].'; ';

                    $optionsChangedIds[] = $arrOldGroupOptionData[$keyGroupOptionData]['id'];

                    if(!System::is_local() and $m_key_value and $m_key_value != $newValue){
                        MC::set_items($m_key, $newValue, time() + 60*30);//30 phut
                    }

                    //log thay doi
                    $newValueLog = $newValue;
                    $oldValueLog = $oldValue;
                    switch ($arrGroupOptionData[$keyGroupOptionData]['type']){
                        case 'select':
                            $newValueKey = (empty($newValue)) ? 0 : $newValue;
                            $newValueLog = $arrGroupOptionData[$keyGroupOptionData]['arrData'][$newValueKey];
                            $oldValueKey = (empty($oldValue)) ? 0 : $oldValue;
                            $oldValueLog = $arrGroupOptionData[$keyGroupOptionData]['arrData'][$oldValueKey];
                            break;

                        case 'check':
                            $newValueLog = (empty($newValueLog)) ? 'không' : 'có';
                            $oldValueLog = (empty($oldValueLog)) ? 'không' : 'có';
                            break;

                        case 'check_reverse':
                            $newValueLog = $newValueLog == 1 ? 'không' : 'có';
                            $oldValueLog = $oldValueLog == 1 ? 'không' : 'có';
                            break;
                    }

                    $logDescription .= 'Thay đổi <b>' . $arrGroupOptionData[$keyGroupOptionData]['name'] . '</b> từ "' . $oldValueLog.$suffix . '" => "' . $newValueLog.$suffix . '" <br>';
                }
            } else {
                $newValue = $arrGroupOptionData[$keyGroupOptionData]['request'];
                $strValueInsertGroupOption .= '(';
                //group_id
                $strValueInsertGroupOption .= '"' . $group_id . '", ';
                //key
                $strValueInsertGroupOption .= '"' . $keyGroupOptionData . '", ';
                //value
                $strValueInsertGroupOption .= '"' . $newValue . '", ';
                //created_at
                $strValueInsertGroupOption .= '"' . date('Y-m-d H:i:s') . '" ';
                $strValueInsertGroupOption .= '), ';
                if (!System::is_local()) {
                    MC::set_items($m_key, $arrGroupOptionData[$keyGroupOptionData]['request'], time() + 60 * 30);//30 phut
                }

                //log them moi
                $newValueLog = $newValue;
                if ($arrGroupOptionData[$keyGroupOptionData]['type'] == 'select') {
                    $newValueKey = (empty($newValue)) ? 0 : $newValue;
                    $newValueLog = $arrGroupOptionData[$keyGroupOptionData]['arrData'][$newValueKey];
                } else if ($arrGroupOptionData[$keyGroupOptionData]['type'] == 'check') {
                    $newValueLog = (empty($newValueLog)) ? 'không' : 'có';
                }
                $logDescription .= 'Thêm mới <b>' . $arrGroupOptionData[$keyGroupOptionData]['name'] . ':</b> "' . $newValueLog . $suffix . '" <br>';
            }
        }

        if($strUpdateGroupOption != ''){
            $strUpdateGroupOption = rtrim($strUpdateGroupOption, '; ');
            $updatedGroupOptions = DB::multi_query($strUpdateGroupOption);

            if ($updatedGroupOptions) {
                $publishEventOnUpdate = true;
            }
        }

        $logDescription .= $this->advertisingMoney($group_id);
        if(Url::get('allow_ips') == ''){
            $this->deleteUserIp();
        } else {
            $logDescription .= $this->addUserIp($group_id);
        }
        if($strValueInsertGroupOption != ''){
            $strValueInsertGroupOption = rtrim($strValueInsertGroupOption,', ');
            $strInsertGroupOption = 'insert into group_options (group_id, `key`, `value`, created_at) values '.$strValueInsertGroupOption;
            $insertGroupOptions = DB::query($strInsertGroupOption);

            if ($insertGroupOptions) {
                $publishEventOnInsert = true;
            }
        }

        //<=== Publish data to CRM ===/
        try {
            if (! empty($publishEventOnInsert)) {
                CrmSync::publishEventOnInsert(
                    'group_options',
                    $group_id,
                    "1=1"
                );
            } else {
                if (! empty($publishEventOnUpdate) && ! empty($optionsChangedIds)) {
                    CrmSync::publishEventOnUpdate(
                        'group_options',
                        $group_id,
                        "`id` in (".implode(',', $optionsChangedIds).")"
                    );
                }
            }
        } catch (Exception $exception) {
            // Log exception
        }
        //>==========================/

        //insert log
        if($logDescription != ''){
            System::log('UPDATE_SHOP_SETTING', 'Cập nhật thông tin cài đặt cửa hàng', $logDescription, '', '', false, array());
        }
        $location = Url::build_current(['group_id']);
        echo '<script>
                    if(window.opener && window.opener.ListShopForm){
                        window.close();
                    }else{
                        alert("Dữ liệu cập nhật thành công!");
                        location="/'.$location.'";
                    }
                </script>';
        exit();
    }

    /**
     * Khi có sự thay đổi thời gian cập nhật mật khẩu thì chúng ta sẽ cập nhật vết thay đổi mật khẩu của tất cả user
     * trong shop đến thời điểm hiện tại nếu như trạng thái trước đó là không kích hoạt. Việc này sẽ reset thời gian
     * cập nhật mật khẩu
     *
     * @param      int   $day      The day
     * @param      int   $groupID  The group id
     */
    private function handleResetPassPeriodic(int $day, int $groupID)
    {
        require_once ROOT_PATH . 'packages/core/includes/common/ResetPassword.php';

        $reset_pass_immediate = URL::getUInt('reset_pass_immediate');
        $oldValue = get_group_options('reset_pass_periodic', $groupID);

        if(!$reset_pass_immediate) {
            $password_updated_at = now();
        } else if($day) {
            $password_updated_at = Carbon\Carbon::now()->subDays($day)->format('Y-m-d H:i:s');
        } else{
            $password_updated_at = User::RESET_PASS_IMMEDIATE_TIME;
        }

        if($reset_pass_immediate || (!$oldValue && $day)) {
            Query::from('account')
                ->where('group_id', $groupID)
                ->where(function($q) {
                    $q->where('password_updated_at', '!=', User::RESET_PASS_IMMEDIATE_TIME);
                    $q->orWhereNull('password_updated_at');
                })
                ->update(['password_updated_at' => $password_updated_at]);
        }

        if($reset_pass_immediate) {
            $rows = array_map(function($user) {
                return [
                    'user_id' => $user['id'],
                    'password' => ResetPassword::RESET_PASS_IMMEDIATE_VALUE,
                    'created_by' => User::getUser()['id'],
                    'created_at' => now()
                ];
            }, $this->getUsersNeedResetPasswordImmediate($groupID));

            ResetPassword::newQuery()->insert($rows);
        }

        return !!$reset_pass_immediate;
    }

    /**
     * Lấy danh sách user cần thay đổi pass ngay lập tức
     *
     * @param      int     $groupID  The group id
     *
     * @return     array
     */
    private function getUsersNeedResetPasswordImmediate(int $groupID, array $select = ['users.id'])
    {
        return Query::from('users')
            ->where('group_id', $groupID)
            ->whereNotExists(function($q) {
                $q->from('reset_password')
                    ->whereColumn('users.id', '=', 'reset_password.user_id')
                    ->where(function($q) {
                        $q->where('reset_password.password', ResetPassword::RESET_PASS_IMMEDIATE_VALUE);
                        $q->orWhereNull('reset_password.password');
                    })
                    ->where(function($q) {
                        $q->whereNull('reset_password.updated_at');
                        $q->orWhereNull('reset_password.updated_by');
                    });
            })
            ->get($select);
    }


    /**
     * { function_description }
     *
     * @param      int   $groupID  The group id
     */
    private function handleMedidoc(int $groupID)
    {
        $key = 'form_medidoc';

        $sql = 'SELECT group_options.id,group_options.value,groups.name 
                FROM group_options 
                JOIN groups ON groups.id = group_options.group_id
                WHERE `key`= "' . $key . '" and group_id = '. $groupID;
        $savedMedidoc = DB::fetch($sql);

        $this->map['form_medidoc'] = !empty($savedMedidoc['value']) ? json_decode($savedMedidoc['value']) : '';
        $this->map['form_medidoc'] ? $this->map['form_medidoc'] : [];
        if(!isset($_POST['form_block_id'])){
            return $_REQUEST['form_medidoc'] = $this->map['form_medidoc'];
        }

        // Lấy mảng và lọc ra các giá trị được phép
        $valids = ['TRI_MAT_NGU' => 'SP Trị mất ngủ', 'TANG_CHIEU_CAO' => 'SP Tăng chiều cao - KT', 'TANG_CHIEU_CAO_NEW' => 'SP Tăng chiều cao', 'GIAM_CAN' => 'SP Giảm cân','MO_HOI'=> 'SP Mồ hôi', 'TIEU_DUONG_MO_MAU'=> 'Tiểu đường, mỡ máu', 'SP_TOC' => 'SP về tóc'];
        $postData = Arr::of(URL::getArray('form_medidoc', []))
                    ->reduce(function($res, $val) use($valids) {
                        isset($valids[$val]) && ($res[] = $val);

                        return $res;
                    }, [])
                    ->toArray();

        if($savedMedidoc){// Cập nhật
            DB::update('group_options', ['value' => json_encode($postData)], sprintf('`key` = "%s" AND `group_id` = %d', $key, $groupID));
        }else{// insert
            DB::insert('group_options', ['value' => json_encode($postData), 'key' => $key, 'group_id' => $groupID, 'created_at' => date('Y-m-d H:i:s')]);
        }

        if(!System::is_local()){
            MC::set_items('form_medidoc_' . $groupID, json_encode($postData), time() + 60*30);//30 phut
        }

        // Fill tên form
        $_saved = []; $_changed = [];
        Arr::of($valids)->map(function($name, $key) use(&$_changed, &$_saved, $postData) {
            in_array($key, $this->map['form_medidoc']) && ($_saved[] = $name);
            in_array($key, $postData) && ($_changed[] = $name);
        });

        $logDescription = '';
        // Thêm
        if(!$_saved && $_changed){
            $logDescription = sprintf('Thêm Form Medidoc "%s".', implode(',', $_changed));
        }

        // Xóa
        elseif($_saved && !$_changed){
            $logDescription = sprintf('Xóa Form Medidoc "%s".', implode(',', $_saved));
        }

        // Sửa
        elseif(array_diff($_saved, $_changed) || count($_saved) != count($_changed)){
            $logDescription = sprintf('Thay đổi Form Medidoc từ "%s" => "%s".', implode(',', $_saved), implode(',', $_changed));
        }

        $logDescription && System::log(
            'UPDATE_SHOP_SETTING',
            'Cập nhật thông tin cài đặt cửa hàng ' . $savedMedidoc['name'],
            $logDescription,
            '', '', false, array()
        );

        return $_REQUEST['form_medidoc'] = $this->map['form_medidoc'] = $postData;
    }

    function updateGroupToVichat($infor) {
        $baseUri = API_GROUP_PALBOX;
        $apiKey = API_KEY_PALBOX;
        $client = new Client();
        try {
            $response = $client->put(
                $baseUri,
                array(
                    'json' => $infor,
                    'headers' => ['api-key' => $apiKey],
                    'allow_redirects' => false,
                    'timeout' => 5
                )
            );
        } catch (Exception $e) {
        }
    }

    private function isTrialAccount(&$group)
    {
        $packageId = $group['package_id'];
        $expiredDate = $group['expired_date'];

        return (!$packageId && $expiredDate >= date('Y-m-d 00:00:00'));
    }

    private function advertisingMoney($group_id){
        $key = 'choose_time_declare_advertising_money';
        $logDescription = '';
        $advMoney = DB::fetch('select id,value,group_id from group_options where `key`= "' . $key . '" and group_id = '. $group_id);
        if(isset($_REQUEST['choose_time_declare_advertising_money'])){
            $value = json_encode($_REQUEST['choose_time_declare_advertising_money']);
            $arrInsert = [
                'group_id' => Session::get('group_id'),
                'key' => 'choose_time_declare_advertising_money',
                'value' => $value,
                'created_at' => date('Y-m-d H:i:s')
            ];
            if(!$advMoney){
                $logDescription .= 'Thêm mới <b> Chọn khung giờ Khai báo tiền QC :</b> "' . $value . '" <br>';
                DB::insert('group_options',$arrInsert);
            } else {
                $advTime = json_decode($advMoney['value'],true);
                $diff = array_diff($_REQUEST['choose_time_declare_advertising_money'],$advTime);
                if(!empty( $advMoney['value'] )){
                    $logs = $advMoney['value'];
                } else {
                    $logs = 'Chọn tất cả';
                }
                if(!empty($diff)){
                    $jsonDiff = json_encode($_REQUEST['choose_time_declare_advertising_money']);
                    $arrUpdate = [
                        'value' => $jsonDiff,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $logDescription .= 'Thay đổi <b> Chọn khung giờ Khai báo tiền QC :</b> từ ' . $logs . ' => '. $jsonDiff .' <br>';
                    DB::update('group_options',$arrUpdate,'id='.$advMoney['id']);
                }
                else if(empty($diff) && !empty($_REQUEST['choose_time_declare_advertising_money']) && sizeof($advTime) != sizeof($_REQUEST['choose_time_declare_advertising_money'])){
                    $jsonDiff = json_encode($_REQUEST['choose_time_declare_advertising_money']);
                    $arrUpdate = [
                        'value' => $jsonDiff,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $logDescription .= 'Thay đổi <b> Chọn khung giờ Khai báo tiền QC :</b> từ ' . $logs . ' => '. $jsonDiff .' <br>';
                    DB::update('group_options',$arrUpdate,'id='.$advMoney['id']);
                }

            }
        } else {
            if($advMoney){
                $arrUpdate = [
                        'value' => '',
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                if(!empty($advMoney['value'])){
                    $logDescription .= 'Thay đổi <b> Chọn khung giờ Khai báo tiền QC :</b> từ ' . $advMoney['value'] . ' => Chọn tất cả <br>';
                }
                DB::update('group_options',$arrUpdate,'id='.$advMoney['id']);
            }
        }
        return $logDescription;
        // if($logDescription != ''){
        //     System::log('UPDATE_SHOP_SETTING', 'Cập nhật thông tin cài đặt cửa hàng', $logDescription, '', '', false, array());
        // }
    }
    //
    private function addUserIp($group_id){
        $key = 'add_user_ip';
        $users = DB::fetch('select id,value,group_id from group_options where `key`= "' . $key . '" and group_id = '. $group_id);
        $logRequest = '';
        $arrLogRequest = [];
        $logs = '';
        $logDescription = '';
        if(isset($_REQUEST['users_ids'])){
            $jsonDiff = json_encode($_REQUEST['users_ids']);
            $userRequest = implode(',', $_REQUEST['users_ids']);
            $logUsersRequest  = DB::fetch_all("SELECT id,username FROM users WHERE id IN ($userRequest)");
            foreach ($logUsersRequest as $k => $val) {
                $arrLogRequest[] = $val['username'];
            }
            $logRequest = implode(', ',$arrLogRequest);
            $arrInsert = [
                'group_id' => Session::get('group_id'),
                'key' => 'add_user_ip',
                'value' => $jsonDiff,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if(!$users){
                $logDescription .= 'Thêm mới <b> Chọn tài khoản :</b> "' . $logRequest . '" <br>';
                DB::insert('group_options',$arrInsert);
            } else {
                $usersIds = [];
                $diff = [];
                if(!empty($users['value'] )){
                    $usersIds = json_decode($users['value'],true);
                    $strUsers = implode(',', $usersIds);
                    $diff = array_diff($_REQUEST['users_ids'],$usersIds);
                    $logUsers  = DB::fetch_all("SELECT id,username FROM users WHERE id IN ($strUsers)");
                    $arrLogCurrent = [];
                    foreach ($logUsers as $k => $val) {
                        $arrLogCurrent[] = $val['username'];
                    }
                    $logs = implode(', ',$arrLogCurrent);
                } else {
                    $logs = 'Bỏ chọn';
                }
                if(!empty($diff)){
                    $jsonDiff = json_encode($_REQUEST['users_ids']);
                    $arrUpdate = [
                        'value' => $jsonDiff,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $logDescription .= 'Thay đổi <b> Chọn tài khoản :</b> từ ' . $logs . ' => '. $logRequest .' <br>';
                    DB::update('group_options',$arrUpdate,'id='.$users['id']);
                }
                else if(empty($diff) && !empty($_REQUEST['users_ids']) && sizeof($usersIds) != sizeof($_REQUEST['users_ids']))
                {
                    $jsonDiff = json_encode($_REQUEST['users_ids']);
                    $arrUpdate = [
                        'value' => $jsonDiff,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $logDescription .= 'Thay đổi <b> Chọn tài khoản :</b> từ ' . $logs . ' => '. $logRequest .' <br>';
                    DB::update('group_options',$arrUpdate,'id='.$users['id']);
                }
            }
        } else {
            if($users){
                $arrUpdate = [
                    'value' => '',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                if(!empty($users['value'])){
                    $usersId = json_decode($users['value'],true);
                    $strUsers = implode(',', $usersId);
                    $logUsers  = DB::fetch_all("SELECT id,username FROM users WHERE id IN ($strUsers)");
                    $arrLog = [];
                    foreach ($logUsers as $k => $val) {
                        $arrLog[] = $val['username'];
                    }
                    $logs = implode(', ',$arrLog);
                    $logDescription .= 'Thay đổi <b> Chọn tài khoản :</b> từ ' . $logs . ' => Bỏ chọn <br>';
                }
                DB::update('group_options',$arrUpdate,'id='.$users['id']);
            }
        }
        return $logDescription;
    }

	function draw()
	{
		$this->map =  array();
		$account_type = Session::get('account_type');
        $this->map['link_api'] = AdminUserInfoDB::getLinkApi();

        $group_id = $this->canChangeShopInfomation()
                        ? URL::getUInt('group_id', Session::get('group_id'), true)
                        : Session::get('group_id');

		$this->map['show_full_name'] = get_group_options('show_full_name', $group_id);
		$_REQUEST['show_full_name'] = $this->map['show_full_name'];

        $this->map['integrate_shipping'] = get_group_options('integrate_shipping', $group_id);
        $_REQUEST['integrate_shipping'] = $this->map['integrate_shipping'];

        // $this->map['require_address'] = get_group_options('require_address');
        // $_REQUEST['require_address'] = $this->map['require_address'];

        $this->map['enable_product_rating'] = get_group_options('enable_product_rating', $group_id);
        $_REQUEST['enable_product_rating'] = $this->map['enable_product_rating'];

        $this->map['show_product_detail'] = get_group_options('show_product_detail', $group_id);
        $_REQUEST['show_product_detail'] = $this->map['show_product_detail'];

        $this->map['show_phone_number_excel_order'] = get_group_options('show_phone_number_excel_order', $group_id);
        $_REQUEST['show_phone_number_excel_order'] = $this->map['show_phone_number_excel_order'];

        $this->map['show_full_name_export_excel_order'] = get_group_options('show_full_name_export_excel_order', $group_id);
        $_REQUEST['show_full_name_export_excel_order'] = $this->map['show_full_name_export_excel_order'];

        $this->map['show_history_order'] = get_group_options('show_history_order', $group_id);
        $_REQUEST['show_history_order'] = $this->map['show_history_order'];

        //show_phone_number_print_order
        $this->map['show_phone_number_print_order'] = get_group_options('show_phone_number_print_order', $group_id);
        $_REQUEST['show_phone_number_print_order'] = $this->map['show_phone_number_print_order'];

        $this->map['min_search_phone_number'] = get_group_options('min_search_phone_number', $group_id);
        $_REQUEST['min_search_phone_number'] = $this->map['min_search_phone_number'];

        $this->map['default_sort_of_order_list'] = get_group_options('default_sort_of_order_list', $group_id);
        $_REQUEST['default_sort_of_order_list'] = $this->map['default_sort_of_order_list'];

        $this->map['hien_sdt_bung_don'] = get_group_options('hien_sdt_bung_don', $group_id);
        $_REQUEST['hien_sdt_bung_don'] = $this->map['hien_sdt_bung_don'];

        $this->map['chup_anh_nhan_vien'] = get_group_options('chup_anh_nhan_vien', $group_id);
        $_REQUEST['chup_anh_nhan_vien'] = $this->map['chup_anh_nhan_vien'];

        $_REQUEST['reset_pass_periodic'] = get_group_options('reset_pass_periodic', $group_id);
        $this->map['reset_pass_periodic'] = get_group_options('reset_pass_periodic', $group_id);


        $sql = '
            SELECT
                *
            FROM
                `groups`
            WHERE
                id="'.$group_id.'"
                '.((!$this->admin_tuha and $account_type==3 and Url::iget('group_id'))?' and master_group_id='.Url::iget('group_id'):'').'
                ';
        $row = array();
        if($row = DB::fetch($sql)){
            //$row['expired_date'] = date('d/mY',strtotime($row['expired_date']));
            //$row['date_established'] = $row['date_established']?date('d/mY',strtotime($row['date_established'])):'';
            $row += AdminUserInfoDB::get_api();
            foreach($row as $key=>$value){
                if(!isset($_REQUEST[$key])){
                    $_REQUEST[$key] = $value;
                }
            }
        }
        
        //$this->map['choose_time_declare_advertising_money'] = get_group_options('choose_time_declare_advertising_money');

//        $group_id = Session::get('group_id');
        $timeSlot = [
            AdminUserInfo::TIME_SLOT_1,
            AdminUserInfo::TIME_SLOT_2,
            AdminUserInfo::TIME_SLOT_3,
            AdminUserInfo::TIME_SLOT_4,
            AdminUserInfo::TIME_SLOT_5,
            AdminUserInfo::TIME_SLOT_6,
            AdminUserInfo::TIME_SLOT_7,
        ];
        if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) {
            $this->map['value_adv'] = ['24h'];
        } else {
            $key = 'choose_time_declare_advertising_money';
            $advMoney = DB::fetch('select * from group_options where `key`= "' . $key . '" and group_id = '. $group_id);
            $this->map['value_adv'] = [];
            if($advMoney){
                if($advMoney['value']){
                    $this->map['value_adv'] = json_decode($advMoney['value'],true);
                } else {
                    $this->map['value_adv'] = [];
                }
            }
        }
        $this->map['timeSlot'] = $timeSlot;

        $props = [
            'name' => 'system_group_id',
            'id' => 'system_group_id',
            'class' => 'form-control select2',
            'style' => 'font-size:16px;font-weight:bold;color:#56FF08',
        ];
        !User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY) && ($props['disabled'] = true);
        $this->map['system_group_id'] =  SystemsTree::selectBox(
            null,
            [
                'selected' => URL::iget('system_group_id'),
                'selectedType' => SystemsTree::SELECTED_CURRENT,
                'props' => $props,
                'default' => '<option value="0">------Thuộc hệ thống------</option>'
            ]
        );
        $this->map['min_search_phone_number_list'] = array('8'=>'8 số','9'=>'9 số');
        $this->map['default_sort_of_order_list_list'] = array('orders.id DESC'=>'Đơn mới nhất','orders.user_assigned ASC,orders.id DESC'=>'Đơn chưa xử lý');
        ///////
        $master_groups = DB::fetch_all('select id,name from `groups` where account_type=3');
        $this->map['master_group_id_list'] = array('0'=>'Chọn nhóm cha') + MiString::get_list($master_groups);
        $account_types =  array('0'=>'Tài khoản thường',1=>'Dùng thử',2=>'Tài khoản cũ',TONG_CONG_TY=>'TỔNG CÔNG TY');
        $this->map['account_type_list'] = $account_types;
        ///////
        $_REQUEST['business_model'] = get_group_options('business_model', $group_id);
        $this->map['business_model_list'] =  array('0'=>'Mặc định','1'=>'Bán theo SPA/TMV','Mô hình bán lẻ - POS');
        ///////
        $_REQUEST['vnpost_hn_id'] = get_group_options('vnpost_hn_id', $group_id);
        $_REQUEST['vnpost_hn_verify_code'] = get_group_options('vnpost_hn_verify_code', $group_id);

        $_REQUEST['ghn_client_id'] = get_group_options('ghn_client_id', $group_id);
        $_REQUEST['ghn_token'] = get_group_options('ghn_token', $group_id);

        ///
        $this->map['phone_store_id_list'] = ['0'=>'Chọn kho số'] + MiString::get_list(AdminUserInfoDB::get_phone_stores());

        $this->map['duplicate_type_list'] = ['0'=>'Chỉ theo SĐT','1'=>'Theo SĐT + Phân loại'];
        $_REQUEST['duplicate_type'] = get_group_options('duplicate_type', $group_id);

        $_REQUEST['show_price_in_export_invoice'] = get_group_options('show_price_in_export_invoice', $group_id);
        $_REQUEST['create_export_invoice_when_confirmed'] = get_group_options('create_export_invoice_when_confirmed', $group_id);
        $_REQUEST['create_export_invoice_when_delivered'] = get_group_options('create_export_invoice_when_delivered', $group_id);

        $_REQUEST['create_import_invoice_when_return'] = get_group_options('create_import_invoice_when_return', $group_id);

        $_REQUEST['hide_total_amount'] = get_group_options('hide_total_amount', $group_id);

        $this->map['callio_info'] = '';
        if ($_REQUEST['callio_info']) {
            $this->map['callio_info'] = json_decode($_REQUEST['callio_info']);
        }
        $this->map['voip24h_info'] = '';
        if ($_REQUEST['voip24h_info']) {
            $this->map['voip24h_info'] = json_decode($_REQUEST['voip24h_info']);
        }

        $_REQUEST['users_can_edit_order'] = get_group_options('users_can_edit_order', $group_id);
        $this->map['users_can_edit_order_list'] = ['Tất cả','Trừ marketing (Với đơn đã chia)'];//tai khoan nao co quyen sua don hang

        $_REQUEST['time_to_refesh_order'] = get_group_options('time_to_refesh_order', $group_id);
        $this->map['time_to_refesh_order_list'] = ['0'=>'Tắt',5=>'5 phút',10=>'10 phút',15=>'15 phút',20=>'20 phút',30=>'30 phút', 60 => '60 phút', 75 => '75 phút', 90 => '90 phút'];

        $_REQUEST['hide_phone_number'] = get_group_options('hide_phone_number', $group_id);
        $this->map['hide_phone_number_list'] =  [3=>'Ẩn 3 số',4=>'Ẩn 4 số',5=>'Ẩn 5 số',6=>'Ẩn 6 số','7'=>'Ẩn 7 số','8'=>'Ẩn 8 số','9'=>'Ẩn 9 số','10'=>'Ẩn 10 số'];

        $_REQUEST['add_deliver_order'] = get_group_options('add_deliver_order', $group_id);
        $this->map['add_deliver_order_list'] =  [1 => 'Cho phép', 2 => 'Không cho phép'];

        $_REQUEST['sale_can_self_assigned'] = get_group_options('sale_can_self_assigned', $group_id);
        $this->map['sale_can_self_assigned_list'] =  [''=>'Không kích hoạt',1=>'Kích hoạt'];

        $_REQUEST['sale_can_assigned_created_user'] = get_group_options('sale_can_assigned_created_user', $group_id);
        $this->map['sale_can_assigned_created_user_list'] =  [''=>'Không kích hoạt',1=>'Kích hoạt'];

        $_REQUEST['disable_negative_export'] = get_group_options('disable_negative_export', $group_id);

        $_REQUEST['no_create_order_when_duplicated'] = get_group_options('no_create_order_when_duplicated', $group_id);

        $_REQUEST['mkt_cost_per_revenue_danger'] = get_group_options('mkt_cost_per_revenue_danger', $group_id);
        $_REQUEST['mkt_cost_per_revenue_warning'] = get_group_options('mkt_cost_per_revenue_warning', $group_id);
        $users = $this->getUserGroup();
        $users_ids = $this->getUserIp();
        $this->map['users_ids_option'] = '';
        if($users_ids){
            $users_ids = array_filter($users_ids,'strlen');
            if (count($users_ids) == count($users)) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            foreach($users as $key=>$val){
                if (in_array($key,$users_ids)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        } else {
            foreach($users as $key=>$val){
                $selected = '';
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        }
        
        $this->parse_layout('information',$this->map);

    }

    /**
     * Determines ability to change shop infomation.
     *
     * @return     bool  True if able to change shop infomation, False otherwise.
     */
    private function canChangeShopInfomation()
    {
        return $this->admin_tuha || Session::get('account_type') == 3;
    }


    private function deleteUserIp(){
        $groupId = Session::get('group_id');
        $key = 'add_user_ip';
        DB::query('delete from group_options where `key`= "' . $key . '" and group_id = '. $groupId);
    }
    private function getUserIp(){
        $groupId = Session::get('group_id');
        $key = 'add_user_ip';
        $users = DB::fetch('select id,value,group_id from group_options where `key`= "' . $key . '" and group_id = '. $groupId);
        $arrUsers = [];
        if($users){
            $arrUsers = json_decode($users['value'],true);
        }
        return $arrUsers;
    }

    private function getUserGroup()
    {
        $groupId = Session::get('group_id');
        $sql = "SELECT 
                    users.id,
                    users.username
                FROM 
                    users
                JOIN account ON account.id = users.username AND account.is_active = 1
                WHERE
                    users.group_id = $groupId
                ";
        $query = DB::fetch_all($sql);
        return $query;
    }

    /**
     * Cập nhật ảnh đại diện cho shop
     *
     * @param      int|string  $groupID  The group id
     *
     * @return     bool      ( description_of_the_return_value )
     */
    private function updateGroupAvatar(int $groupID){
        require_once 'packages/core/includes/utils/ftp.php';
        require_once ROOT_PATH . 'packages/core/includes/utils/upload_file.php';

        $dir = 'upload/default/groups/'.$groupID;
        $imageUrl = FTP::upload_file('image_url', $dir, true,'content', 'IMAGE', false, true);

        return $imageUrl && DB::update('groups',['image_url' => $imageUrl],' id = "'.$groupID.'"');
    }

    /**
     * Publish event on insert account
     * @param $group_id
     * @param array $data
     */
    protected function publishEventOnInsert($group_id, array $data) {
        try {
            if (! $this->_checkForPublishEvent($group_id)) {
                return;
            }

            if (! empty($data)) {
                foreach ($data as $item) {
                    message_queue::getInstance()->publishPushedEventInsert('group_options', null, $item);
                }
            }
        } catch (Exception $exception) {
            // logs exception
        }
    }

    /**
     * Publish event on update account
     * @param $group_id
     * @param array $data
     */
    protected function publishEventOnUpdate($group_id, array $data) {
        try {
            if (! $this->_checkForPublishEvent($group_id)) {
                return;
            }

            if (! empty($data)) {
                foreach ($data as $item) {
                    message_queue::getInstance()->publishPushedEventUpdate('group_options', null, $item);
                }
            }
        } catch (Exception $exception) {
            // logs exception
        }
    }

    /**
     * @param int $group_id
     * @return bool
     */
    private function _checkForPublishEvent($group_id) {
        // 1: đã đăng ký crm 0: chưa đăng ký
        $isCrm = DB::fetch('select `is_crm` from `groups` where `id`='.DB::escape($group_id), 'is_crm');

        if ($isCrm == 1) {
            $message_queue_path = join(DIRECTORY_SEPARATOR, [
                rtrim(ROOT_PATH, '\\/'),
                'packages',
                'core',
                'includes',
                'system',
                'message_queue.php'
            ]);

            require_once $message_queue_path;

            return true;
        }

        return false;
    }

    /**
     * Determines whether the specified old is synchronize crm enable.
     *
     * @param      array  $old    The old
     *
     * @return     bool   True if the specified old is synchronize crm enable, False otherwise.
     */
    public static function isSyncCrmEnable(array $old)
    {
        return (
                    // yêu cầu bật thủ công hoặc chưa hết hạn
                    URL::getUInt('is_crm') === self::CRM_ON || self::isNotExpired($row['palion_expired_at'] ?? '')
                )

                // và đang ở trạng thái chờ tắt hoặc tắt thì cho phép bật lại
                && in_array(intval($old['is_crm']), [self::CRM_WAIT_OFF, self::CRM_OFF]);
    }

    /**
     * Determines whether the specified old is synchronize crm disable.
     *
     * @param      array  $old    The old
     *
     * @return     bool   True if the specified old is synchronize crm disable, False otherwise.
     */
    public static function isSyncCrmDisable(array $old)
    {
        return (
            // yêu cầu tắt thủ công và hoặc hết hạn
            URL::getUInt('is_crm') === self::CRM_OFF || self::isExpired($row['palion_expired_at'] ?? '')
        )

         // và đang ở trạng thái chờ bật hoặc bật thì cho phép tắt
        && in_array(intval($old['is_crm']), [self::CRM_WAIT_ON, self::CRM_ON]);
    }

    /**
     * Determines whether the specified value is expired.
     *
     * @param      string  $value  The value
     *
     * @return     bool    True if the specified value is expired, False otherwise.
     */
    private static function isExpired(string $value)
    {
        return !empty($value) && preg_match('#^(?!0000-00-00).*$#', $value) && strtotime($value) > time();
    }

    /**
     * Determines whether the specified value is not expired.
     *
     * @param      string  $value  The value
     *
     * @return     bool    True if the specified value is not expired, False otherwise.
     */
    private static function isNotExpired(string $value) 
    {
        return !self::isExpired($value);
    }

    /**
     * { function_description }
     *
     * @param      int    $groupID  The group id
     * @param      array  $old      The old
     * @param      array  $options  The options
     */
    public static function syncCrm(int $groupID, array &$old, int $type = 0)
    {
        switch($type){
            case AdminUserInfoInformationForm::CRM_WAIT_ON:
                $description = 'Thay đổi <strong>Đồng bộ CRM</strong> từ "Tắt đồng bộ" => "Chờ bật đồng bộ" <br>';
                $_type = 'ON';
                break;

            case AdminUserInfoInformationForm::CRM_WAIT_OFF:
                $description = 'Thay đổi <strong>Đồng bộ CRM</strong> từ "Bật đồng bộ" => "Chờ tắt đồng bộ" <br>';
                $_type = 'OFF';
                break;

            default:
                return;
        }

        $title = 'Cập nhật thông tin cài đặt cửa hàng "'. $old['name'] .' ('.$old['id'].')"';
        System::log('UPDATE_SHOP_SETTING', $title, $description, '', '', false, []);

        return self::onOffCrm($groupID, $_type);
    }

    
    private function onOffCrm($group_id, $type = "ON"){
        $client = new Client();
        if ($type == 'ON') {
            $table = 'log_outgoing_palion_sync_on';
        } else {
            $table = 'log_outgoing_palion_sync_off';
        }
        $uniqid = uniqid('palion_',true);
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $header = getallheaders();
        $exception = new stdClass();
        $exception->code = 0;
        $exception->message = '';
        $response = new stdClass();
        $response->header = new stdClass();
        $response->body = new stdClass();
        $req = new stdClass();
        $req->header = $header;
        $req->body = [
            'uniqid' => $uniqid,
            'provider' => API_PROVIDER,
            'group_id' => $group_id,
            'user_id' => Session::get('user_id'),
        ];
        self::storeRequestToPalion([
            'uniqid' => $uniqid,
            'query_params' => new stdClass(),
            'request' => $req,
            'response' => $response,
            'exception' => $exception,
            'created_at' => date('c'),
            'updated_at' => date('c'),
            'table' => $table,
            'isUpdated' => 0,
        ]);
        try {
            $params = [
                'provider' => API_PROVIDER,
                'group_id' => $group_id,
                'user_id' => Session::get('user_id'),
                'token' => TOKEN_CRM
            ];
            $url = $type === 'ON' ? API_HTTP_START_CRM : API_HTTP_STOP_CRM;
            $res = $client->request('POST', $url, ['form_params' =>$params, 'timeout' => 5]);
            $body = $res->getBody();
            $stringBody = (string) $body;
            $response->body = json_decode($stringBody);
            self::storeRequestToPalion([
                'uniqid' => $uniqid,
                'query_params' => new stdClass(),
                'request' => $req,
                'response' => $response,
                'exception' => $exception,
                'created_at' => date('c'),
                'updated_at' => date('c'),
                'table' => $table,
                'isUpdated' => 1,
            ]);
            return json_decode($stringBody, true);
        } catch (Exception $e) {
            $exception->code = $e->getCode();
            $exception->message = $e->getMessage();
            self::storeRequestToPalion([
                'uniqid' => $uniqid,
                'query_params' => new stdClass(),
                'request' => $req,
                'response' => $response,
                'exception' => $exception,
                'created_at' => date('c'),
                'updated_at' => date('c'),
                'table' => $table,
                'isUpdated' => 1,
            ]);
            error_log($e->getMessage());
            return array('success' => 0, 'message' => "Lỗi gọi sang Palion crm");
        }
    }

    private static function storeRequestToPalion($data = array()) {
        $client = new Client([
            'base_uri' => BIG_QUEUE_HOST_API . '/api/',
            'timeout'  => 5.0,
        ]);
        try {
            $response = $client->request('POST', "store-request-to-palion",
                [
                    'json' => $data,
                    'headers' => [
                        'Authorization' => $_SERVER['HTTP_HOST'],
                        'Accept'     => 'application/json',
                    ]
                ]
            );
            return true;
        } catch (Exception $e) {

        }
    }

    private function handleChangeGroupSystem(
        $isGroupIdInOBD, 
        $isNewSystemGroupInOBD, 
        $groupId
    ) {
        $bundlesOfGroup = self::getBundlesOfGroup($groupId);
        self::updateIncludeIdsOfBundles(
            $bundlesOfGroup,
            $isGroupIdInOBD, 
            $isNewSystemGroupInOBD 
        );
        $sourcesOfGroup = self::getSourceOfGroup($groupId);
        self::updateIncludeIdsOfSources(
            $sourcesOfGroup,
            $isGroupIdInOBD, 
            $isNewSystemGroupInOBD 
        );
    }

    private static function updateIncludeIdsOfBundles(
        $bundlesOfGroup,
        $isGroupIdInOBD, 
        $isNewSystemGroupInOBD
    ){
        $isHKDToOBD = !$isGroupIdInOBD && $isNewSystemGroupInOBD;
        $isOBDToHKD = $isGroupIdInOBD && !$isNewSystemGroupInOBD;
        if ($isHKDToOBD) {
            foreach ($bundlesOfGroup as $id => $bundle) {
                $id = $bundle['id'];
                $refId = $bundle['ref_id'];
                $systemBundle = self::findSystemBundle($refId);
                if (!$systemBundle) {
                    continue;
                }//end if

                $includeIds = self::addIncludeIds($id, $systemBundle['include_ids']);
                DB::update('bundles', ['include_ids' => $includeIds], "id = $refId");
            }//end foreach
        } else if ($isOBDToHKD) {
            foreach ($bundlesOfGroup as $id => $bundle) {
                $id = $bundle['id'];
                $refId = $bundle['ref_id'];
                $systemBundle = self::findSystemBundle($refId);
                if (!$systemBundle) {
                    continue;
                }//end if

                $includeIds = self::removeIncludeIds($id, $systemBundle['include_ids']);
                DB::update('bundles', ['include_ids' => $includeIds], "id = $refId"); 
            }//end foreach
        }//end if
    }

    private static function updateIncludeIdsOfSources(
        $sources,
        $isGroupIdInOBD, 
        $isNewSystemGroupInOBD
    ) {
        $isHKDToOBD = !$isGroupIdInOBD && $isNewSystemGroupInOBD;
        $isOBDToHKD = $isGroupIdInOBD && !$isNewSystemGroupInOBD;
        if ($isHKDToOBD) {
            foreach ($sources as $id => $source) {
                $id = $source['id'];
                $refId = $source['ref_id'];
                $systemSource = self::findSystemSource($refId);
                if (!$systemSource) {
                    continue;
                }//end if

                $includeIds = self::addIncludeIds($id, $systemSource['include_ids']);
                DB::update('order_source', ['include_ids' => $includeIds], "id = $refId");
            }//end foreach
        } else if ($isOBDToHKD) {
            foreach ($sources as $id => $source) {
                $id = $source['id'];
                $refId = $source['ref_id'];
                $systemSource = self::findSystemSource($refId);
                if (!$systemSource) {
                    continue;
                }//end if

                $includeIds = self::removeIncludeIds($id, $systemSource['include_ids']);
                DB::update('order_source', ['include_ids' => $includeIds], "id = $refId"); 
            }//end foreach
        }//end if
    }
    

    private static function getBundlesOfGroup($groupId): array
    {
        $_sql = "SELECT `id`, `name`, `ref_id`, `include_ids`
            FROM bundles 
            WHERE group_id = $groupId
                AND ref_id != 0";

        return DB::fetch_all_array($_sql);
    }

    private static function getSourceOfGroup($groupId): array
    {
        $_sql = "SELECT `id`, `name`, `ref_id`, `include_ids`
            FROM order_source 
            WHERE group_id = $groupId
                AND ref_id != 0";

        return DB::fetch_all_array($_sql);
    }

    /**
     * addIncludeIds function
     *
     * @param integer $id
     * @param string|null $includeIds
     * @return string
     */
    private static function addIncludeIds(int $id, $includeIds):string
    {
        $includeIds = DataFilter::removeEmptyValueListIds($includeIds);
        if (!in_array($id, $includeIds)) {
            $includeIds[] = $id;
        } //end if
        $_includeIds = DB::escapeArray($includeIds);
        return implode(',', $_includeIds);
    }

    /**
     * removeIncludeIds function
     *
     * @param integer $id
     * @param string|null $includeIds
     * @return string
     */
    private static function removeIncludeIds(int $id, $includeIds):string
    {
        $includeIds = DataFilter::removeEmptyValueListIds($includeIds);
        if (in_array($id, $includeIds)) {
            foreach (array_keys($includeIds, $id) as $key) {
                unset($includeIds[$key]);
            } //end foreach
        } //end if
        $_includeIds = DB::escapeArray($includeIds);
        return implode(',', $_includeIds);
    }

    private static function findSystemBundle(int $id)
    {
        $_sql = "SELECT `id`, `include_ids`
        FROM bundles 
        WHERE id = $id
            AND group_id = 0
            AND ref_id = 0
            AND standardized = 1";
        return DB::fetch($_sql);
    }

    private static function findSystemSource(int $id)
    {
        $_sql = "SELECT `id`, `include_ids`
        FROM order_source 
        WHERE id = $id
            AND group_id = 0
            AND ref_id = 0";
        return DB::fetch($_sql);
    }
}
