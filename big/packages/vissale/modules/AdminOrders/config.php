<?php
class AdminOrdersConfig
{
    static $token = '85a7ada570bc8d6cd9fa01f2248f5574';

    static function config_shipping_status()
    {
        return [
            'DA_DAT' => 5,
            'CHO_LAY_HANG' => 1,
            'DA_LAY_HANG' => 6,
            'DANG_GIAO' => 2,
            'DA_GIAO' => 3,
            'HOAN_THANH' => 7,
            'HUY' => 4,
            'TRA_HANG' => 8,
            'DANG_TRA_HANG' => 9
        ];
    }

    static function config_jobs_status()
    {
        return [
            'CHO_XU_LY' => 1,
            'THANH_CONG' => 2,
            'THAT_BAI' => 3
        ];
    }

    static function config_jobs_status_text()
    {
        return [
            1 => [
                'name' => 'Đang xử lý',
                'button' => 'label-danger'
            ],
            2 => [
                'name' => 'Thành công',
                'button' => 'label-success'
            ],
            3 => [
                'name' => 'Thất bại',
                'button' => 'label-default'
            ],
        ];
    }

    static function viettel_post_addon()
    {
        return [
            'GBH' => 'Bảo hiểm',
            'GBP' => 'Báo phát',
            'GTT' => 'Phát tận tay',
            'GCH' => 'GCH chuyển hoàn',
            'GDK' => 'Đồng kiểm',
            'GTK' => 'Thư ký',
            'GGD' => 'Giao bưu phảm tại điểm (Không áp dụng cho dịch vụ Hỏa tốc và dịch vụ Vận tải tiết kiệm)',
            'GTC' => 'Hàng giá trị cao'
        ];
    }

    static function viettel_post_config()
    {
        return [
            [
                "SERVICE_CODE" => "VCN",
                "SERVICE_NAME" => "VCN Chuyển phát nhanh"
            ],
            [
                "SERVICE_CODE" => "VTK",
                "SERVICE_NAME" => "VTK Tiết kiệm"
            ],
            [
                "SERVICE_CODE" => "V60",
                "SERVICE_NAME" => "V60 Dịch vụ Nhanh 60h"
            ],
            [
                "SERVICE_CODE" => "VHT",
                "SERVICE_NAME" => "VHT Phát Hỏa tốc"
            ],
            [
                "SERVICE_CODE" => "SCOD",
                "SERVICE_NAME" => "SCOD Giao hàng thu tiền"
            ],
            [
                "SERVICE_CODE" => "NCOD",
                "SERVICE_NAME" => "NCOD - TMDT Bay"
            ],
            [
                "SERVICE_CODE" => "PTN",
                "SERVICE_NAME" => "PTN - TMDT Phát trong ngày"
            ],
            [
                "SERVICE_CODE" => "PHS",
                "SERVICE_NAME" => "PHS - TMDT Phát hôm sau"
            ],
            [
                "SERVICE_CODE" => "V30",
                "SERVICE_NAME" => "V30"
            ],
            [
                "SERVICE_CODE" => "V35",
                "SERVICE_NAME" => "V35"
            ],
            [
                "SERVICE_CODE" => "V20",
                "SERVICE_NAME" => "V20"
            ],
            [
                "SERVICE_CODE" => "V25",
                "SERVICE_NAME" => "V25"
            ],
            [
                "SERVICE_CODE" => "V02",
                "SERVICE_NAME" => "V02 - TMDT Phát nhanh 2h"
            ],
            [
                "SERVICE_CODE" => "VBS",
                "SERVICE_NAME" => "VBS Nhanh theo hộp"
            ],
            [
                "SERVICE_CODE" => "VBE",
                "SERVICE_NAME" => "VBE Tiết kiệm theo hộp"
            ],
            [
                "SERVICE_CODE" => "LCOD",
                "SERVICE_NAME" => "LCOD - TMDT Bộ"
            ],
            [
                "SERVICE_CODE" => "ECOD",
                "SERVICE_NAME" => "ECOD Giao hành thu tiền tiết kiệm"
            ],
            [
                "SERVICE_CODE" => "VCBA",
                "SERVICE_NAME" => "VCBA - Chuyển phát đường bay"
            ],
            [
                "SERVICE_CODE" => "VCBO",
                "SERVICE_NAME" => "VCBO - Chuyển phát đường bộ"
            ]
        ];
    }

    static function ghtk_config()
    {
        return [
            'road' => 'Đường bộ',
            'fly' => 'Đường bay'
        ];
    }

    // Lấy ra các dịch vụ của EMS
    static function ems_services()
    {
        return [
            1 => 'Dịch vụ chuyển phát nhanh EMS',
            14 => 'Thương Mại Điện Tử EMS',
            15 => 'Thương Mại Điện Tử EMS Tiết Kiệm',
            17 => 'EMS Thương mại điện tử đồng giá'
        ];
    }

    static function ems_services_addon()
    {
        return [
            'COD' => 'Giao hàng thu tiền (COD)',
            'PTT' => 'Phát tận tay',
            'KG' => 'Khai giá',
            'ART' => 'Báo phát'
        ];
    }

    static function ems_status()
    {
        return [
            'DA_TAO' => 1,
            'CHO_LAY_HANG' => 2,
            'DA_LAY_HANG' => 3,
            'DANG_VAN_CHUYEN' => 4,
            'DANG_PHAT_HANG' => 5,
            'PHAT_KHONG_THANH_CONG' => 6,
            'PHAT_THANH_CONG' => 7,
            'CHUYEN_HOAN' => 8,
            'HUY' => 9
        ];
    }

    static function get_status_shipping()
    {
        return [
            5 => [
                'icon' => 'fa fa-inbox',
                'name' => 'Đơn hàng đã đặt',
                'class' => 'btn btn-info',
                'label_class' => 'label-info'
            ],
            1 => [
                'icon' => 'fa fa-rocket',
                'name' => 'Chờ lấy hàng',
                'class' => 'btn btn-danger',
                'label_class' => 'label-danger'
            ],
            6 => [
                'icon' => 'fa fa-archive',
                'name' => 'Đã lấy hàng',
                'class' => 'btn btn-primary',
                'label_class' => 'label-primary'
            ],
            2 => [
                'icon' => 'fa fa-truck',
                'name' => 'Đang giao',
                'class' => 'btn btn-warning',
                'label_class' => 'label-warning'
            ],
            3 => [
                'icon' => 'fa fa-star',
                'name' => 'Đã giao',
                'class' => 'btn btn-warning',
                'label_class' => 'label-warning'
            ],
            7 => [
                'icon' => 'fa fa-star',
                'name' => 'Hoàn thành',
                'class' => 'btn btn-success',
                'label_class' => 'label-success'
            ],
            4 => [
                'icon' => 'fa fa-ban',
                'name' => 'Hủy',
                'class' => 'btn btn-default',
                'label_class' => 'label-default'
            ],
            9 => [
                'icon' => 'fa fa-ban',
                'name' => 'Đang trả hàng',
                'class' => 'btn btn-default',
                'label_class' => 'label-default'
            ],
            8 => [
                'icon' => 'fa fa-ban',
                'name' => 'Trả hàng',
                'class' => 'btn btn-default',
                'label_class' => 'label-default'
            ],
        ];
    }

    static function get_list_shipping_costs(){
        // $ghn_host = 'https://apiv3-test.ghn.vn/';
        $ghn_host = 'https://console.ghn.vn/';
        // $ghtk_host = 'https://dev.ghtk.vn/';
        $ghtk_host = 'https://services.giaohangtietkiem.vn/';
        // $emsHost = "http://staging.ws.ems.com.vn/api/v1/";
        $emsHost = "http://ws.ems.com.vn/api/v1/";
        $emsToken = "89523b465384003748f3f31fef06e2df";
        $arr_shipping_costs = [
            'api_bdhn' => [
                'name' => 'Bưu điện Hà Nội',
                'avatar' => '/assets/vissale/images/logo_transport/vn_post.jpg',
                'MaHuyenPhat' => 0,
                'url_ghi_du_lieu' => 'http://buudienhanoi.com.vn/Nhanh/BDHNNhanh.asmx?WSDL',
                'url_tra_cuu_du_lieu' => 'http://buudienhanoi.com.vn/dinhvi/ws_dinhvi.asmx?WSDL',
                'ma_xac_thuc' => '',
                'ItemID'=> '',
                'token' => ''
            ],
            'api_ghtk' => [
                'name' => 'Giao hàng tiết kiệm',
                'api_calculatefee_url' => $ghtk_host . 'services/shipment/fee',
                // 'token' => '58A64c0768274c1F2F32309d04187255D17f2298',
                'token' => 'E2e909021fc593640849f434Db336f833426C058',
                'avatar' => '/assets/vissale/images/logo_transport/ghtk.png',
                'create_shipping_url' => $ghtk_host . 'services/shipment/order',
                'cancel_shipping_url' => $ghtk_host . 'services/shipment/cancel'
            ],
            'api_ghn' => [
                'name' => 'Giao hàng nhanh',
                'api_calculatefee_url' => $ghn_host  . 'api/v1/apiv3/CalculateFee',
                'api_set_config_client' => $ghn_host . 'api/v1/apiv3/SetConfigClient',
                'avatar' => '/assets/vissale/images/logo_transport/ghn_1.png',
                // 'token' => '1e08811f90944a568c94f21f09586f02',
                'token' => '5c0f3a2594c06b4b983081b5',
                'api_find_service_url' => $ghn_host  . 'api/v1/apiv3/FindAvailableServices',
                'create_shipping_url' => $ghn_host  . 'api/v1/apiv3/CreateOrder',
                'update_shipping_url' => $ghn_host  . 'api/v1/apiv3/UpdateOrder',
                'cancel_shipping_url' => $ghn_host  . 'api/v1/apiv3/CancelOrder',
                'service_list_period' => [
                    53319 => '6 giờ',
                    53320 => '1 Ngày',
                    53321 => '2 Ngày',
                    53322 => '3 Ngày',
                    53323 => '4 Ngày',
                    53324 => '5 Ngày',
                    53327 => '6 Ngày'
                ],
                // 'AffiliateID' => 485253,
                'AffiliateID' => 723527,
                'PaymentTypeID' => 2, // Buyer pay shipping fee,
                'ServiceIdPost' => 53337, // Gửi hàng tại điểm
                'ServiceIdCod' => 53335 // Shipper đến lấy hàng
            ],
            "api_viettel_post" => [
                'name' => 'Viettel Post',
                'api_login_partner_url' => "https://partner.viettelpost.vn/v2/user/Login",
                "username_partner" => "ceo@palvietnam.vn",
                "password_partner" => "tuhavodich",
                "api_connect_customer_url" => "https://partner.viettelpost.vn/v2/user/ownerconnect",
                "api_list_province_url" => "https://partner.viettelpost.vn/v2/categories/listProvince",
                "api_list_district_url" => "https://partner.viettelpost.vn/v2/categories/listDistrict",
                "api_list_ward_url" => "https://partner.viettelpost.vn/v2/categories/listWards",
                "api_calculatefee_url" => "https://partner.viettelpost.vn/v2/order/getPriceAll",
                "api_get_list_inventory" => "https://partner.viettelpost.vn/v2/user/listInventory",
                "api_create_order" => "https://partner.viettelpost.vn/v2/order/createOrder",
                "api_cancel_order" => "https://partner.viettelpost.vn/v2/order/UpdateOrder",
                "api_register_url" => "https://partner.viettelpost.vn/v2/user/ownerRegister",
                'avatar' => '/assets/vissale/images/logo_transport/viettel_post.png'
            ],
            "api_ems" => [
                'name' => 'EMS Việt Nam',
                'avatar' => '/assets/vissale/images/logo_transport/ems_logo.jpg',
                'api_get_provice' => $emsHost . "address/province?merchant_token={$emsToken}",
                'api_get_district' => $emsHost . "address/district?merchant_token={$emsToken}",
                'api_get_ward' => $emsHost . "address/ward?merchant_token={$emsToken}",
                'api_create_warehouse' => $emsHost . "inventory/create",
                'api_create_order' => $emsHost . "orders/create",
                'api_cancel_order' => $emsHost . "orders/manual-cancel-order"
            ],
        ];

        return $arr_shipping_costs;
    }

    static function ltrim_zones($string) {
        // $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = mb_strtolower($string);
        $string = str_replace(['ð', 'ắ'], ['đ', 'ă'], $string);
        $trimArray = ['thành phố', 'huyện', 'thị xã', 'tp', 'quận', 'phường', 'xã', 'thị trấn'];
        $string = str_replace($trimArray, '', $string);
        //$string = str_replace('huyên yên lạc', 'yên lạc', $string);
        //$string = str_replace('nà hang', 'na hang', $string);
        //$string = str_replace('đảo phú quốc', 'phú quốc', $string);
        $string = trim($string);
        return $string;
    }

    static function toSlug($str) {
        $str = trim(mb_strtolower($str));
        // $trimArray = ['thành phố', 'huyện', 'thị xã', 'tp', 'quận', 'phường', 'xã', 'thị trấn'];
        // $str = str_replace($trimArray, '', $str);
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

    static function toSlugWards($str) {
        $str = AdminOrdersConfig::ltrim_zones($str);
        $str = AdminOrdersConfig::toSlug($str);

        return $str;
    }

    static function remove_utf8_bom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    static function get_zones_viettel_id($params = [], $config)
    {
        // $from_province_name = mb_strtolower(trim($params['from_province_name']));
        $from_province_name = AdminOrdersConfig::ltrim_zones($params['from_province_name']);
        // $to_province_name = mb_strtolower(trim($params['to_province_name']));
        $to_province_name = AdminOrdersConfig::ltrim_zones($params['to_province_name']);
        // $from_district_name = mb_strtolower(trim($params['from_district_name']));
        $from_district_name = AdminOrdersConfig::ltrim_zones($params['from_district_name']);
        // $to_district_name = mb_strtolower(trim($params['to_district_name']));
        $to_district_name = AdminOrdersConfig::ltrim_zones($params['to_district_name']);
        $from_province_id = ""; $to_province_id = ""; $from_district_id = ""; $to_district_id = "";
        $response_province = json_decode(static::execute_get_curl($config['api_list_province_url']), true);
        // System::debug($response_province); die();
        $listProvinces = [];
        if ($response_province['status'] == 200) {
            $provinces = $response_province['data'];
            foreach ($provinces as $province) {
                if (AdminOrdersConfig::ltrim_zones($province['PROVINCE_NAME']) == $from_province_name) {
                    $from_province_id = $province['PROVINCE_ID'];
                }

                if (AdminOrdersConfig::ltrim_zones($province['PROVINCE_NAME']) == $to_province_name) {
                    $to_province_id = $province['PROVINCE_ID'];
                }
                $listProvinces[] = AdminOrdersConfig::ltrim_zones($province['PROVINCE_NAME']);

                if (!empty($from_province_id) && !empty($to_province_id)) {
                    break;
                }
            }
            /* System::debug($to_province_name);
            System::debug($listProvinces);
            System::debug($to_province_id); die(); */
            if (!empty($from_province_id) && !empty($provinces)) {
                $url_district = $config['api_list_district_url'];
                $response_from_district = json_decode(static::execute_get_curl($url_district . '?provinceId=' . $from_province_id), true);
                if ($response_from_district['status'] == 200) {
                    $districts = $response_from_district['data'];
                    foreach ($districts as $district) {
                        if (AdminOrdersConfig::ltrim_zones($district['DISTRICT_NAME']) == $from_district_name) {
                            $from_district_id = $district['DISTRICT_ID'];
                            break;
                        }
                    }
                }

                $response_to_district = json_decode(static::execute_get_curl($url_district . '?provinceId=' . $to_province_id), true);

                $listDistricts = [];
                if ($response_to_district['status'] == 200) {
                    $districts = $response_to_district['data'];
                    foreach ($districts as $district) {
                        $listDistricts[] = AdminOrdersConfig::ltrim_zones($district['DISTRICT_NAME']);
                        if (AdminOrdersConfig::ltrim_zones($district['DISTRICT_NAME']) == $to_district_name) {
                            $to_district_id = $district['DISTRICT_ID'];
                            break;
                        }
                    }
                }
            }
        }
        /*if(Session::get('user_id')=='dinhkkk'){
            System::debug($to_province_id);
            System::debug($to_district_name);
            System::debug($listDistricts); die();
        }*/
        return [
            'from_province_id' => $from_province_id,
            'to_province_id' => $to_province_id,
            'from_district_id' => $from_district_id,
            'to_district_id' => $to_district_id
        ];
    }

    static function execute_curl($url, $json_fields = []) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($json_fields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_fields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_fields))
            );
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }

    static function execute_get_curl($url, $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }

    static function execute_post_curl($url, $json_fields = [], $token = "")
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

    //các nhà mạng
    static function homeNetwork()
    {
        return [
            'VIETTEL' => 'Viettel',
            'VINAPHONE' => 'VinaPhone',
            'MOBIFONE' => 'MobiFone',
            'VIETNAMOBILE' => 'Vietnamobile',
            'GMOBILE' => 'Gmobile',
            'ITELECOM' => 'Itelecom',
            'KHAC' => 'Khác',
        ];
    }
}
