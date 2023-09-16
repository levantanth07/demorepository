<?php

class ShippingOption extends Module
{
    public static $system_id;
    public static $group_id;
    function __construct($row)
    {
        self::$group_id = Session::get('group_id');
        self::$system_id = false;
        $system_group_id = DB::fetch('select system_group_id from `groups` where id='.self::$group_id,'system_group_id');
        require_once 'packages/vissale/modules/ShopShippingAddress/db.php';
        require_once 'packages/vissale/modules/AdminOrders/config.php';

        if($system_group_id and (IDStructure::is_child(DB::structure_id('groups_system',$system_group_id),DB::structure_id('groups_system',2)) or $system_group_id==2)){
            self::$system_id = 2;
        }
        Module::Module($row);
        if (User::is_login() and Session::get('group_id') and Session::get('admin_group')) {
            switch (Url::get('cmd')) {
                case "viettel_post":
                    require_once 'forms/viettelpost.php';
                    $this->add_form(new ViettelPostForm());
                break;
                case "register_viettel_post":
                    require_once 'forms/register_viettel_post.php';
                    $this->add_form(new RegisterViettelPostForm());
                break;
                case "registered":
                    require_once 'forms/registered.php';
                    $this->add_form(new RegisteredForm());
                break;
                case "get_zones":
                    if (Url::get('zone_id')) {
                        $this->getZones(Url::get('type'), Url::get('zone_id'));
                    }
                break;
                case "get_token":
                    $this->get_token();
                break;
                case "register_vtp_ajax":
                    $this->register_vtp_ajax();
                break;
                default:
                    require_once 'forms/edit.php';
                    $this->add_form(new ShippingOptionsForm());
                break;
            }
        } else {
            URL::access_denied();
        }
    }

    function register_vtp_ajax()
    {
        $data['success'] = false;
        $requiredField = [
            'email', 'phone', 'user_password', 'name', 'address', 'zone_id', 'district_id', 'ward_id'
        ];
        $flag = true;
        foreach ($requiredField as $field) {
            if (!Url::get($field)) {
                $flag = false;
                break;
            }
        }
        if (!$flag) {
            $data['error'] = 'Kiểm tra đã điền đẩy đủ thông tin nhập vào.';
            echo json_encode($data); die();
            die();
        }

        $configs = AdminOrdersConfig::get_list_shipping_costs();
        $config = $configs['api_viettel_post'];
        try {
            $from_province_name = DB::fetch("SELECT viettel_province_name FROM zone_provinces WHERE province_id = " .DB::escape(Url::get('zone_id')), "viettel_province_name");
            $from_district_name = DB::fetch("SELECT district_name FROM zone_districts_v2 WHERE district_id = " . DB::escape(Url::get('district_id')), "district_name");
            $from_ward_name = DB::fetch("SELECT ward_name FROM zone_wards_v2 WHERE ward_id = " . DB::escape(Url::get('ward_id')), "ward_name");
            $paramsArea = $this->get_zones_viettel_id([
                'from_province_name' => $from_province_name,
                'from_district_name' => $from_district_name,
                'from_ward_name' => $from_ward_name,
            ], $config);
            $errorsProvince = [];
            $configProvince = [
                'from_province_id' => 'Tỉnh/Thành phố',
                'from_district_id' => 'Quận/Huyện',
                'from_ward_id' => 'Phường/Xã'
            ];
            foreach ($paramsArea as $k => $val) {
                if (empty($val)) {
                    $flag = false;
                    $errorsProvince[] = 'Lỗi '. $configProvince[$k];
                }
            }
            if (!$flag) {
                $data['error'] = implode(', ', $errorsProvince);
                echo json_encode($data); die();
                die();
            }

            $params_partner = [
                'USERNAME' => $config['username_partner'],
                'PASSWORD' => $config['password_partner']
            ];
            // echo json_encode($paramsArea); die();
            $response = $this->execute_post_curl($config['api_login_partner_url'], json_encode($params_partner));
            $response_api = json_decode($response, true);
            if ($response_api['status'] == 200) {
                $token_partner = $response_api['data']['token'];
                $params_customer = [
                    'EMAIL' => Url::get('email'),
                    'PHONE' => Url::get('phone'),
                    'PASSWORD' => Url::get('user_password'),
                    'NAME' => Url::get('name'),
                    'ADDRESS' => Url::get('address'),
                    'PROVINCE_ID' => (int)$paramsArea['from_province_id'],
                    'DISTRICT_ID' => (int)$paramsArea['from_district_id'],
                    'WARDS_ID' => (int)$paramsArea['from_ward_id'],
                ];
                $response_customer = $this->execute_post_curl($config['api_register_url'], json_encode($params_customer), $token_partner);
                $response_api_customer = json_decode($response_customer, true);
                if ($response_api_customer['status'] == 200) {
                    $data['success'] = true;
                    $data['token'] = $response_api_customer['data']['token'];
                    if(ShippingOption::$system_id == 2){
                        $obd_link = 'https://partner.viettelpost.vn/v2/user/add-group?gid=2';
                        $obd_response = $this->execute_get_curl($obd_link, $data['token']);
                        $obd_response_api = json_decode($obd_response, true);
                        if ($obd_response_api['status'] == 200) {
                            $data['success'] = true;
                            $data['token'] = $response_api_customer['data']['token'];
                        } else {
                            $data['success'] = false;
                            $data['error'] = $obd_response_api['message'];
                        }
                    }

                    $params_customer['from_province_name'] = $from_province_name;
                    $params_customer['from_district_name'] = $from_district_name;
                    $params_customer['from_ward_name'] = $from_ward_name;
                    // Insert tài khoản vào DB
                    DB::insert('vtp_account', [
                        'group_id' => Session::get('group_id'),
                        'user_id' => Session::get('user_id'),
                        'content' => json_encode($params_customer, JSON_UNESCAPED_UNICODE),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    // Tạo tài khoản tự động
                    if (Url::get('button_action') == 'auto_register') {
                        $userId = DB::fetch('select id from users where username="' . Session::get('user_id') . '"', 'id');
                        $totalShippingOption = DB::fetch("SELECT COUNT(*) AS total FROM shipping_options WHERE group_id = " . Session::get('group_id'), "total");
                        DB::insert('shipping_options', [
                            'group_id' => Session::get('group_id'),
                            'user_id' => $userId,
                            'carrier_id' => 'api_viettel_post',
                            'token' => $data['token'],
                            'name' => 'Viettel Post ' . $totalShippingOption,
                            'created_at' => date('Y-m-d H:i:s'),
                            'is_default' => 0
                        ]);
                    }
                    // $data['cusId'] = $response_api_customer['data']['userId'];
                } elseif ($response_api_customer['status'] == 201) {
                    $data['error'] = "Token không hợp lệ";
                } elseif ($response_api_customer['status'] == 202) {
                    $data['error'] = "Lỗi token (để trống, không đúng, đã hết hạn...)";
                } elseif ($response_api_customer['status'] == 203) {
                    $data['error'] = "Lỗi trường không được bỏ trống (email,phone,address,name....)";
                } elseif ($response_api_customer['status'] == 204) {
                    $data['error'] = "Số điện thoại không hợp lệ.";
                } elseif ($response_api_customer['status'] == 205) {
                    $data['error'] = "Lỗi hệ thống";
                } elseif ($response_api_customer['status'] == 206) {
                    $data['error'] = "Tài khoản (email, phone) đã tồn tại.";
                } else {
                    $data['error'] = "Lỗi hệ thống";
                }
            }
        } catch (Exception $e) {
            $data['error'] = "Lỗi! Vui lòng thử lại sau.";
        }

        echo json_encode($data); die();
    }

    function getZones($type = 'district', $zoneId)
    {
        $zones = [];
        if ($type == 'district') {
            $zones = ShopShippingAddressDB::getDistrictsByProvinceId(DB::escape($zoneId));
        } else if ($type == 'ward') {
            $zones = ShopShippingAddressDB::getWardsByDistrictId(DB::escape($zoneId));
        }

        echo json_encode($zones, JSON_UNESCAPED_UNICODE); die();
    }

    static function get_zones_viettel_id($params = [], $config)
    {
        $from_province_name = AdminOrdersConfig::ltrim_zones($params['from_province_name']);
        $from_district_name = AdminOrdersConfig::ltrim_zones($params['from_district_name']);
        $from_ward_name = AdminOrdersConfig::ltrim_zones($params['from_ward_name']);
        $from_province_id = ""; $from_district_id = ""; $from_ward_id = "";
        $response_province = json_decode(AdminOrdersConfig::execute_get_curl($config['api_list_province_url']), true);
        $listProvinces = [];
        if ($response_province['status'] == 200) {
            $provinces = $response_province['data'];
            foreach ($provinces as $province) {
                if (AdminOrdersConfig::ltrim_zones($province['PROVINCE_NAME']) == $from_province_name) {
                    $from_province_id = $province['PROVINCE_ID'];
                }

                if (!empty($from_province_id)) {
                    break;
                }
            }

            if (!empty($from_province_id) && !empty($provinces)) {
                $url_district = $config['api_list_district_url'];
                $response_from_district = json_decode(AdminOrdersConfig::execute_get_curl($url_district . '?provinceId=' . $from_province_id), true);
                if ($response_from_district['status'] == 200) {
                    $districts = $response_from_district['data'];
                    foreach ($districts as $district) {
                        if (AdminOrdersConfig::ltrim_zones($district['DISTRICT_NAME']) == $from_district_name) {
                            $from_district_id = $district['DISTRICT_ID'];
                            break;
                        }
                    }
                }
            }
            //api_list_ward_url
            if (!empty($from_district_id)) {
                $url_wards = $config['api_list_ward_url'];
                $response_from_wards = json_decode(AdminOrdersConfig::execute_get_curl($url_wards . '?districtId=' . $from_district_id), true);
                if ($response_from_wards['status'] == 200) {
                    $wards = $response_from_wards['data'];
                    foreach ($wards as $ward) {
                        if (AdminOrdersConfig::ltrim_zones($ward['WARDS_NAME']) == $from_ward_name) {
                            $from_ward_id = $ward['WARDS_ID'];
                            break;
                        }
                    }
                }
            }
        }

        return [
            'from_province_id' => $from_province_id,
            'from_district_id' => $from_district_id,
            'from_ward_id' => $from_ward_id
        ];
    }

    function get_token()
    {
        $data['success'] = false;
        if (Url::get('name') && Url::get('password')) {
            try {
                $config = AdminOrdersConfig::get_list_shipping_costs();
                $config = $config['api_viettel_post'];
                $name = Url::get('name');
                $password = Url::get('password');
                // Login Partner
                $params_partner = [
                    'USERNAME' => $config['username_partner'],
                    'PASSWORD' => $config['password_partner']
                ];
                $response = $this->execute_post_curl($config['api_login_partner_url'], json_encode($params_partner));
                $response_api = json_decode($response, true);
                if ($response_api['status'] == 200) {
                    $token_partner = $response_api['data']['token'];
                    $url_connect_customer = $config['api_connect_customer_url'];
                    $params_customer = [
                        'USERNAME' => $name,
                        'PASSWORD' => $password
                    ];
                    $response_customer = $this->execute_post_curl($config['api_connect_customer_url'], json_encode($params_customer), $token_partner);
                    $response_api_customer = json_decode($response_customer, true);
                    if ($response_api_customer['status'] == 200) {
                        $data['success'] = true;
                        $data['token'] = $response_api_customer['data']['token'];
                        if(ShippingOption::$system_id == 2){
                            $obd_link = 'https://partner.viettelpost.vn/v2/user/add-group?gid=2';
                            $obd_response = $this->execute_get_curl($obd_link, $data['token']);
                            $obd_response_api = json_decode($obd_response, true);
                            if ($obd_response_api['status'] == 200) {
                                $data['success'] = true;
                                $data['token'] = $response_api_customer['data']['token'];
                            } else {
                                $data['success'] = false;
                                $data['error'] = $obd_response_api['message'];
                            }
                        }

                        // Tạo tài khoản tự động
                        if (Url::get('button_action') == 'auto_register') {
                            $userId = DB::fetch('select id from users where username="' . Session::get('user_id') . '"', 'id');
                            $totalShippingOption = DB::fetch("SELECT COUNT(*) AS total FROM shipping_options WHERE group_id = " . Session::get('group_id'), "total");
                            DB::insert('shipping_options', [
                                'group_id' => Session::get('group_id'),
                                'user_id' => $userId,
                                'carrier_id' => 'api_viettel_post',
                                'token' => $data['token'],
                                'name' => 'Viettel Post ' . $totalShippingOption,
                                'created_at' => date('Y-m-d H:i:s'),
                                'is_default' => 0
                            ]);
                        }
                        // $data['cusId'] = $response_api_customer['data']['userId'];
                    } elseif ($response_api_customer['status'] == 201) {
                        $data['error'] = "Token không hợp lệ";
                    } elseif ($response_api_customer['status'] == 202) {
                        $data['error'] = "Lỗi token (để trống, không đúng, đã hết hạn...)";
                    } elseif ($response_api_customer['status'] == 204) {
                        $data['error'] = "Tài khoản hoặc mật khẩu chủ sở hữu không hợp lệ!  ";
                    } else {
                        $data['error'] = "Lỗi hệ thống";
                    }
                } elseif ($response_api['status'] == 204) {
                    $data['error'] = "Tài khoản hoặc mật khẩu không đúng, or tài khoản đã bị khóa.";
                } else {
                    $data['error'] = "Lỗi hệ thống";
                }

            } catch (Exception $e) {
                $data['error'] = "Lỗi! Vui lòng thử lại sau.";

            }
        }

        echo json_encode($data); die();
    }

    function execute_post_curl($url, $json_fields = [], $token = "")
    {
        $ch = curl_init();
        $headers = [
            'Content-Type: application/json'
        ];
        if (!empty($token)) {
            $headers[] = 'Token: ' . $token;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if (!empty($json_fields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_fields);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }
    function execute_get_curl($url, $token)
    {
        $ch = curl_init();

        if (!empty($token)) {
            $headers[] = 'Token: ' . $token;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }

}
