<?php

class MultiShippingForm extends Form{

    function __construct(){
        Form::Form('MultiShippingForm');
        if (empty(get_group_options('integrate_shipping'))) {
            echo '<script>alert("Bạn chưa kích hoạt kết nối vận chuyển.");window.location="/?page=admin_group_info";</script>';
            die;
        }

        /*if (!Url::get('new_ids') && !Url::get('ids')) {
            echo '<script>alert("Bạn chưa chọn đơn hàng cần chuyển.");window.location="/?page=admin_orders";</script>';
            die;
        }*/
    }

    function validateForm()
    {
        $groupId = Session::get('group_id');
        $flag = true; $errors = [];
        if (!Url::get("ids")) {
            $errors[] = 'Chưa có đơn hàng nào được lựa chọn.';
        }
        if (!Url::get("shipping_carrier_id")) {
            $errors[] = "Bạn chưa chọn hãng vận chuyển.";
        }
        if (!Url::get("radio_shipping_address")) {
            $errors[] = "Bạn chưa chọn địa chỉ lấy hàng.";
        }
        if (!Url::get("shipping_option_id")) {
            if (Url::get("shipping_option_id") != 0)
                $errors[] = "Bạn chưa chọn tài khoản vận chuyển.";
        }
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
        if (Url::get("ids")) {
            $ids = Url::get("ids");
            $arrayIds = explode(',', $ids);
            $arrayFormat = [];
            foreach ($arrayIds as $key => $value) {
                $arrayFormat[] = DB::escape($value);
            }
            $strIds = implode(',', $arrayFormat);
            $orders = AdminOrdersDB::getOrdersInfo($strIds);
            $orders_products = AdminOrdersDB::getProductsFromOrderIds($strIds);
            if ($orders_products) {
                foreach ($orders_products as $op_id => $product) {
                    $error = [];
                    if (empty($product['weight'])) {
                        $error[] = "Chưa nhập trọng lượng";
                    }
                    if (!empty($error)) {
                        $errors[] = "Đơn hàng <b>{$product['order_id']}: </b>Sản phẩm <b>{$product['product_name']}</b>: " . implode('; ', $error);
                    }
                }
            } else {
                $errors[] = 'Đơn hàng chưa có sản phẩm';
            }
            foreach ($orders as $order_id => $order) {
                $error = [];
                $order['price'] = $order['price'] ?: 1;
                if ( (empty($order['insurance_value']) || $order['insurance_value']  <= 0) || empty($order['customer_name']) || empty($order['mobile']) || empty($order['city']) || empty($order['address']) || empty($order['price']) || empty($order['district_id'])  || empty($order['ward_id']) || empty($order['user_confirmed'])) {
                    if (empty($order['customer_name'])) {
                        $error[] = "Chưa nhập tên khách hàng";
                    }
                    if (empty($order['mobile'])) {
                        $error[] = "Chưa nhập điện thoại khách hàng";
                    }
                    if (empty($order['city'])) {
                        $error[] = "Chưa nhập tỉnh/thành phố";
                    }
                    if (empty($order['address'])) {
                        $error[] = "Chưa nhập địa chỉ khách hàng";
                    }
                    if (empty($order['price'])) {
                        $error[] = "Chưa nhập giá sản phẩm";
                    }
                    if (empty($order['district_id'])) {
                        $error[] = "Chưa nhập quận/huyện khách hàng";
                    }
                    if (empty($order['ward_id'])) {
                        $error[] = "Chưa nhập phường/xã khách hàng";
                    }
                    if (empty($order['user_confirmed'])) {
                        $error[] = "Chưa trải qua trạng thái Xác nhận chốt đơn";
                    }
                    if ((empty($order['insurance_value']) || $order['insurance_value']  <= 0)) {
                        $error[] = "Khai giá phải lớn hơn 0";
                    }
                    $errors[] = "Đơn hàng <b>{$order_id}</b>: " . implode('; ', $error);
                } elseif ($order['insurance_value'] > 0) {
                    $checkError = false;
                    $carrier_id = Url::get("shipping_carrier_id");
                    switch ($carrier_id) {
                        case 'api_best':
                            if ((int)$order['insurance_value'] > 20000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển BEST] Giá trị khai giá tối đa: 20,000,000 vnđ";
                            }
                            break;
                        case 'api_ems':
                            if ((int)$order['insurance_value'] > 20000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển EMS] Giá trị khai giá tối đa: 20,000,000 vnđ";
                            }
                            break;
                        case 'api_ghn':
                            if ((int)$order['insurance_value'] > 10000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển GHN] Giá trị khai giá tối đa: 10,000,000 vnđ";
                            }
                            break;
                        case 'api_ghtk':
                            if ((int)$order['insurance_value'] > 20000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển GHTK] Giá trị khai giá tối đa: 20,000,000 vnđ";
                            }
                            break;
                        case 'api_viettel_post':
                            if ((int)$order['insurance_value'] > 30000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển Viettel Post] Giá trị khai giá tối đa: 30,000,000 vnđ";
                            }
                            break;
                        case 'api_jt':
                            if ((int)$order['insurance_value'] > 30000000) {
                                $checkError = true;
                                $error[] = "[Hãng vận chuyển J&T] Giá trị khai giá tối đa: 30,000,000 vnđ";
                            }
                            break;
                    }
                    if ($checkError) $errors[] = "Đơn hàng <b>{$order_id}</b>: " . implode('; ', $error);
                }
            }

            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors
                ];
            } else {
                return [
                    'success' => true,
                    'orders' => $orders
                ];
            }
        }
    }

    function deliver_backup_func() {
        if (Url::get("ids") && Url::get("shipping_carrier_id") && Url::get("radio_shipping_address") && Url::get("shipping_option_id")) {
            $addDeliverOrder = get_group_options('add_deliver_order');
            if (empty($addDeliverOrder)) {
                $addDeliverOrder = 1;
            }
            $ids = Url::get("ids");
            $carrier_id = Url::get("shipping_carrier_id");
            $radio_shipping_address = Url::get("radio_shipping_address");
            $shipping_option_id = Url::get("shipping_option_id");
            $shipping_address = AdminOrdersDB::getShippingAddressById(DB::escape($radio_shipping_address));
            //$shipping_option = AdminOrdersDB::getShippingOptionById($shipping_option_id);
            $arrayIds = explode(',', $ids);
            $arrayFormat = [];
            foreach ($arrayIds as $key => $value) {
                $arrayFormat[] = DB::escape($value);
            }
            $strIds = implode(',', $arrayFormat);
            $orders = AdminOrdersDB::getOrdersInfo($strIds);
            $group_id = AdminOrders::$group_id;
            $is_cod = Url::get('is_cod');
            $pick_option = Url::get('pick_option');
            $is_freeship = Url::get('is_freeship');
            $costs_config = AdminOrdersConfig::get_list_shipping_costs();
            $status_config = AdminOrdersConfig::config_shipping_status();
            // System::debug($orders);
            $rows_carrier = [
                'shipping_address_text' => '<div><b>'. $shipping_address['name'] .'</b></div> <div>'. $shipping_address['phone'] .'</div> <div>'. $shipping_address['address'] .'</div>',
                'carrier_id' => $carrier_id,
                'shipping_status' => 1,
                'shipping_address_id' => $radio_shipping_address,
                'is_cod' => $is_cod,
                'pick_option' => $pick_option,
                'is_freeship' => $is_freeship,
                'group_id' => $group_id,
                'shipping_option_id' => $shipping_option_id
            ];
            $errors = [];
            $success = [];
            $ex_ids = '';
            $im_ids = '';
            $config = $costs_config[$carrier_id];
            $shipping_option = AdminOrdersDB::getShippingOptionById($shipping_option_id);
            $token = $shipping_option['token'];
            $response_deliver = [];
            if ($carrier_id == 'api_ghn') {
                $paymentTypeID = $config['PaymentTypeID'];
                if ($is_freeship == 1) {
                    $paymentTypeID = 1;
                }

                $from_district_id = $shipping_address['district_id'];
                $clientAddress = $shipping_address['address'] . ', ' .$shipping_address['ward_name'] . ', '. $shipping_address['district_name'] . ', ' . $shipping_address['province_name'];
                $service_url = $config['api_find_service_url'];
                // System::debug($orders); die();
                foreach ($orders as $order_id => $order) {
                    $data = [];
                    $to_district_id = $order['district_id'];
                    if (empty($order['customer_name']) || empty($order['mobile']) || empty($order['city']) || empty($order['address']) || empty($order['price']) || empty($order['district_id'])  || empty($order['ward_id'])) {
                        $errors[$order_id][] = 'Vui lòng kiểm tra thông tin: Tên khách hàng, Điện thoại, Địa chỉ, Tỉnh thành, Quận huyện, Phường xã, Giá sản phẩm đã nhập đầy đủ.';
                        continue;
                    }

                    $total_price = $order['total_price'];
                    $shipping_price = $order['shipping_price'];
                    // $cod_amount = $order['price'];
                    $cod_amount = $total_price - $shipping_price;
                    $cod_amount_origin = $total_price - $shipping_price;
                    $products = AdminOrdersDB::getProductsFromOrderId($order_id);
                    // System::debug($products);
                    $total_weight = 0;
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            $total_weight += (int)($product['qty'] * $product['weight']);
                        }
                    }

                    if ($is_cod == 2) {
                        $cod_amount = 0;
                    }

                    if ($total_weight == 0) {
                        $errors[$order_id][] = 'Chưa nhập trọng lượng sản phẩm.';
                        continue;
                    }

                    // Lấy ra serviceId
                    $arr_post_fileds = [
                        "token" => $token,
                        "Weight" => (int)$total_weight,
                        "FromDistrictID" => (int)$from_district_id,
                        "ToDistrictID" => (int)$to_district_id
                    ];
                    $services = json_decode(AdminOrdersConfig::execute_curl($service_url, json_encode($arr_post_fileds)), true);
                    // Tính tạm phí vận chuyển
                    if (!$services['code']) {
                        $errors[$order_id][] = "GHN không lựa chọn được dịch vụ phù hợp cho đơn hàng này.";
                    }

                    $services['data'] = (array) $services['data'];
                    $services = end($services['data']);
                    $ServiceID = $services['ServiceID'];
                    $ghn_shipping_address = trim($order['address']) . ', ' . trim($order['city']);
                    if ($pick_option == "cod") {
                        $service_id = $config['ServiceIdCod'];
                    } else {
                        $service_id = $config['ServiceIdPost'];
                    }

                    $data = [
                        'token' => $token,
                        'PaymentTypeID' => $paymentTypeID,
                        'FromDistrictID' => (int)$from_district_id,
                        'ToDistrictID' => (int)$to_district_id,
                        'ClientContactName' => $shipping_address['name'],
                        'ClientContactPhone' => $shipping_address['phone'],
                        'ClientAddress' => $clientAddress,
                        'CustomerName' => trim($order['customer_name']),
                        'CustomerPhone' => $order['mobile'],
                        'ShippingAddress' => $ghn_shipping_address,
                        'ServiceID' => (int)$ServiceID,
                        'Weight' => (float)$total_weight,
                        'Length' => 10,
                        'Width' => 10,
                        'Height' => 5,
                        'NoteCode' => Url::get('note_code'),
                        'Note' => $order['shipping_note'],
                        'ReturnContactName' => trim($order['customer_name']),
                        'ReturnContactPhone' => $order['mobile'],
                        'ReturnAddress' => $ghn_shipping_address,
                        'ReturnDistrictID' => (int)$to_district_id,
                        'ExternalReturnCode' => '',
                        'AffiliateID' => $config['AffiliateID'],
                        'is_cod' => $is_cod,
                        'CoDAmount' => (float)$cod_amount,
                        'ShippingOrderCosts' => [
                            [
                                'ServiceID' => $service_id
                            ]
                        ],
                        'pick_option' => $pick_option,
                        'is_freeship' => $is_freeship
                    ];
                    $rows_carrier['order_id'] = $order_id;
                    $rows_carrier['note_code'] = Url::get('note_code');
                    $rows_carrier['total_weight'] = (float)$total_weight;
                    $rows_carrier['total_width'] = 10;
                    $rows_carrier['total_length'] = 10;
                    $rows_carrier['total_height'] = 5;
                    $rows_carrier['cod_amount'] = $cod_amount;
                    $rows_carrier['address_text'] = $order['customer_name'] . ',' . $order['mobile']  . ',' .$order['address'];
                    if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress($order_id)) {
                        $response_api = AdminOrdersConfig::execute_curl($config['create_shipping_url'], json_encode($data));
                        $response_api = json_decode($response_api, true);
                        if ($response_api['code']) {
                            // mysqli_begin_transaction(DB::$db_connect_id);
                            try {
                                $response_api = $response_api['data'];
                                $rows_carrier['shipping_order_code'] = $response_api['OrderCode'];
                                $rows_carrier['total_service_fee'] = $response_api['TotalServiceFee'];
                                $rows_carrier['shipping_order_id'] = $response_api['OrderID'];
                                DB::insert('orders_shipping', $rows_carrier);

                                DB::insert('order_shipping_status_history', [
                                    'order_id' => $order_id,
                                    'status' => $status_config['CHO_LAY_HANG'],
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);

                                // $total_price_update = $cod_amount_origin +
                                $shipping_fee = $response_api['TotalServiceFee'];
                                if ($is_freeship == 1) {
                                    $shipping_fee = 0;
                                }

                                if ($addDeliverOrder == 2) {
                                    $shipping_fee = 0;
                                }

                                $total_fee = $cod_amount_origin + $shipping_fee;
                                $status_id = CHUYEN_HANG; // chuyen hang
                                $ex_ids .= ($ex_ids?',':'').$order_id;
                                DB::update_id('orders', [
                                    'shipping_price' => $shipping_fee,
                                    'postal_code' => $response_api['OrderCode'],
                                    'status_id' => $status_id,
                                    'delivered'=>date('Y-m-d H:i:s'),
                                    'user_delivered'=>AdminOrders::$user_id,
                                    'total_price' => $total_fee
                                ], $order_id);
                                /// khoand added at 18:02 19/06/2019
                                if(DB::exists('select id from orders_extra where order_id='.$order_id)){
                                    DB::update('orders_extra', [
                                        'sort_code' => $response_api['SortCode']
                                    ], 'order_id='.$order_id.'');
                                }
                                ///
                                AdminOrdersDB::update_revision($order_id,$order['status_id'],$status_id);
                                $success[] = $order_id;
                                // mysqli_commit(DB::$db_connect_id);
                                // mysqli_close(DB::$db_connect_id);
                            } catch (Exception $e) {
                                // mysqli_rollback(DB::$db_connect_id);
                                // mysqli_close(DB::$db_connect_id);
                                $errors[$order_id][] = "Tạo đơn thành công trên GHN. Tuy nhiên, đã có lỗi xảy ra khi cập nhật đơn hàng trên tuha.";
                                continue;
                                // echo $e->getMessage(); die();
                            }
                        } else {
                            $errors[$order_id][] = $response_api['msg'];
                            continue;
                        }
                    } else {
                        $errors[$order_id][] = 'Mã đơn hàng đã tồn tại trên hệ thống vận chuyển.';
                        continue;
                    }
                }
            } elseif ($carrier_id == 'api_ghtk') {
                $refer_token = $config['token'];
                foreach ($orders as $order_id => $order) {
                    $data = [];
                    if (empty($order['customer_name']) || empty($order['mobile']) || empty($order['city']) || empty($order['address']) || empty($order['price']) || empty($order['district_id'])  || empty($order['ward_id'])) {
                        $errors[$order_id][] = 'Vui lòng kiểm tra thông tin: Tên khách hàng, Điện thoại, Địa chỉ, Tỉnh thành, Quận huyện, Phường xã, Giá sản phẩm đã nhập đầy đủ.';
                        continue;
                    }

                    $total_price = $order['total_price'];
                    $shipping_price = $order['shipping_price'];
                    // $cod_amount = $order['price'];
                    $cod_amount = $total_price - $shipping_price;
                    $cod_amount_origin = $total_price - $shipping_price;
                    $order_price = $order['price'];
                    if ($is_cod == 2) {
                        $cod_amount = 0;
                    }

                    $data['order'] = [
                        'id' => $order_id,
                        'pick_name' => $shipping_address['name'],
                        'pick_address' => $shipping_address['address'],
                        'pick_province' => $shipping_address['province_name'],
                        'pick_district' => $shipping_address['district_name'],
                        'pick_tel' => $shipping_address['phone'],
                        'tel' => $order['mobile'],
                        'name' => trim($order['customer_name']),
                        'address' => $order['address'],
                        'province' => $order['city'],
                        'district' => $order['district_reciever'],
                        'pick_money' => $cod_amount,
                        'value' => $order_price,
                        'pick_option' => Url::get('pick_option'),
                        'is_freeship' => Url::get('is_freeship'),
                        'note' => $order['shipping_note']
                    ];
                    $products = AdminOrdersDB::getProductsFromOrderId(DB::escape($order_id));
                    if (empty($products)) {
                        $errors[$order_id][] = 'Chưa chọn sản phẩm.';
                        continue;
                    }

                    $total_weight = 0;
                    foreach ($products as $product) {
                        $total_weight += (int)($product['qty'] * $product['weight']);
                        $data['products'][] = [
                            'name' => $product['product_name'],
                            'weight' => (int)$product['qty'] * (int)($product['weight']) / 1000,
                            'quantity' => $product['qty']
                        ];
                    }

                    if ($total_weight == 0) {
                        $errors[$order_id][] = 'Chưa nhập trọng lượng sản phẩm.';
                        continue;
                    }

                    $rows_carrier['order_id'] = $order_id;
                    $rows_carrier['note_code'] = Url::get('note_code');
                    $rows_carrier['total_weight'] = (float)$total_weight;
                    $rows_carrier['total_width'] = 10;
                    $rows_carrier['total_length'] = 10;
                    $rows_carrier['total_height'] = 5;
                    $rows_carrier['cod_amount'] = $cod_amount;
                    $rows_carrier['address_text'] = $order['customer_name'] . ',' . $order['mobile']  . ',' .$order['address'];
                    if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress(DB::escape($order_id))) {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $config['create_shipping_url'],
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => array(
                                "Content-Type: application/json",
                                "Token: " . $token,
                                "X-Refer-Token: " . $refer_token
                            )
                        ));

                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response_api = json_decode($response, true);
                        if ($response_api['success']) {
                            $rows_carrier['shipping_order_code'] = $response_api['order']['label'];
                            $rows_carrier['total_service_fee'] = $response_api['order']['fee'];
                            try {
                                DB::insert('orders_shipping', $rows_carrier);

                                DB::insert('order_shipping_status_history', [
                                    'order_id' => $order_id,
                                    'status' => $status_config['CHO_LAY_HANG'],
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);

                                $shipping_fee = $response_api['order']['fee'];
                                if ($is_freeship == 1) {
                                    $shipping_fee = 0;
                                }

                                if ($addDeliverOrder == 2) {
                                    $shipping_fee = 0;
                                }

                                $total_fee = $cod_amount_origin + $shipping_fee;
                                $status_id = CHUYEN_HANG;
                                DB::update_id('orders', [
                                    'shipping_price' => $shipping_fee,
                                    'postal_code' => $response_api['order']['label'],
                                    'status_id' => $status_id,
                                    'delivered'=>date('Y-m-d H:i:s'),
                                    'user_delivered'=>AdminOrders::$user_id,
                                    'total_price' => $total_fee
                                ], $order_id);
                                AdminOrdersDB::update_revision($order_id,$order['status_id'],$status_id);
                                $success[] = $order_id;
                                $ex_ids .= ($ex_ids?',':'').$order_id;
                            } catch (Exception $e) {
                                $errors[$order_id][] = "Tạo đơn thành công trên GHTK. Tuy nhiên, đã có lỗi xảy ra khi cập nhật đơn hàng trên tuha.";
                                continue;
                            }
                        } else {
                            $errors[$order_id][] = 'Có lỗi xảy ra khi tạo đơn trên GHTK. - ' . $response_api['message'];
                            continue;
                        }
                    } else {
                        $errors[$order_id][] = 'Mã đơn hàng đã tồn tại trên hệ thống vận chuyển.';
                        continue;
                    }
                }
            } elseif ($carrier_id == 'api_bdhn') {
                $userIdCurrent = Session::get('user_id');
                foreach ($orders as $order_id => $order) {
                    $data = [];
                    if (empty($order['customer_name']) || empty($order['mobile']) || empty($order['city']) || empty($order['address']) || empty($order['price']) || empty($order['district_id'])  || empty($order['ward_id'])) {
                        $errors[$order_id][] = 'Vui lòng kiểm tra thông tin: Tên khách hàng, Điện thoại, Địa chỉ, Tỉnh thành, Quận huyện, Phường xã, Giá sản phẩm đã nhập đầy đủ.';
                        continue;
                    }

                    $products = AdminOrdersDB::getProductsFromOrderId(DB::escape($order_id));
                    $total_weight = 0;
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            $total_weight += (int)($product['qty'] * $product['weight']);
                        }
                    }

                    $dia_chi_kho_hang = $shipping_address['address'] . ', ' . $shipping_address['district_name'] . ', ' . $shipping_address['province_name'];
                    $data = [
                        'SoDonHang' => $order_id,
                        'HoTenNguoiGui' => $shipping_address['name'],
                        'DiaChiNguoiGui' => $dia_chi_kho_hang,
                        'DienThoaiNguoiGui' => $shipping_address['phone'],
                        'TenKhoHang' => $shipping_address['name'],
                        'DiaChiKhoHang' => $dia_chi_kho_hang,
                        'DienThoaiLienHeKhoHang' => $shipping_address['phone'],
                        'HoTenNguoiNhan' => $order['customer_name'],
                        'DiaChiNguoiNhan' => $order['address'] . ', ' . $order['district_reciever'] . ', ' . $order['city'],
                        'DienThoaiNguoiNhan' => $order['mobile'],
                        'TongTrongLuong' => (float)$total_weight,
                        'TongCuoc' => 0,
                        'TongTienPhaiThu' => $order['price'],
                        'NgayGiao' => date('m/d/Y'),
                        'TinhThanh' => $order['city'],
                        'QuanHuyen' => $order['district_reciever'],
                        'PhuongThuc' => $is_cod,
                        'NoiDungHang' => $order['shipping_note'],
                        'DonHangDoiTra' => false,
                        'MaHuyenPhat' => $config['MaHuyenPhat'],
                        'iddichvu' => 2
                    ];
                    if ($userIdCurrent == 'pal.hoantv') {
                        $soapclient = new SoapClient($config['url_ghi_du_lieu']);
                        $response_phien = $soapclient->KetNoi(['Ma' => $token]);
                        System::debug($response_phien); die();
                    }

                    $soapclient = new SoapClient($config['url_ghi_du_lieu']);
                    $response_phien = $soapclient->KetNoi(['Ma' => $token]);
                    if (empty($response_phien->KetNoiResult)) {
                        $errors[$order_id][] = "Mã xác thực BĐHN không chính xác.";
                        continue;
                    }

                    if (AdminOrdersDB::checkExistShippingAddress(DB::escape($order_id))) {
                        $errors[$order_id][] = "Mã đơn hàng đã tồn tại trên hệ thống vận chuyển.";
                        continue;
                    }

                    $data['MaPhien'] = $response_phien->KetNoiResult;
                    $response_create_order = $soapclient->TaoYeuCauThuGom2017($data);
                    $result_order = explode('|', $response_create_order->TaoYeuCauThuGom2017Result);
                    $status_order = $result_order[0];
                    $rows_carrier['order_id'] = $order_id;
                    $rows_carrier['note_code'] = Url::get('note_code');
                    $rows_carrier['total_weight'] = (float)$total_weight;
                    $rows_carrier['cod_amount'] = 0;
                    $rows_carrier['address_text'] = $order['customer_name'] . ',' . $order['mobile']  . ',' .$order['address'];
                    if ($status_order == 99) {
                        $rows_carrier['shipping_order_code'] = $result_order[1];
                        $rows_carrier['updated_at'] = date('Y-m-d H:i:s');
                        try {
                            DB::insert('orders_shipping', $rows_carrier);

                            DB::insert('order_shipping_status_history', [
                                'order_id' => $order_id,
                                'status' => $status_config['CHO_LAY_HANG'],
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            $status_id = CHUYEN_HANG;
                            DB::update_id('orders', [
                                'postal_code' => $result_order[1],
                                'status_id' => $status_id,
                                'delivered'=>date('Y-m-d H:i:s'),
                                'user_delivered'=>AdminOrders::$user_id
                            ], $order_id);
                            AdminOrdersDB::update_revision($order_id,$order['status_id'],$status_id);
                            $success[] = $order_id;
                            $ex_ids .= ($ex_ids?',':'').$order_id;
                        } catch (Exception $e) {
                            $errors[$order_id][] = "Tạo đơn thành công trên BDHN. Tuy nhiên, đã có lỗi xảy ra khi cập nhật đơn hàng trên tuha.";
                            continue;
                        }
                    } else {
                        $errors[$order_id][] = "Có lỗi xảy ra trong quá trình tạo đơn hàng.";
                        continue;
                    }
                }
            } elseif ($carrier_id == 'api_viettel_post') {
                foreach ($orders as $order_id => $order) {
                    $data = [];
                    if (empty($order['customer_name']) || empty($order['mobile']) || empty($order['city']) || empty($order['address']) || empty($order['price']) || empty($order['district_id'])  || empty($order['ward_id'])) {
                        $errors[$order_id][] = 'Vui lòng kiểm tra thông tin: Tên khách hàng, Điện thoại, Địa chỉ, Tỉnh thành, Quận huyện, Phường xã, Giá sản phẩm đã nhập đầy đủ.';
                        continue;
                    }

                    $total_price = $order['total_price'];
                    $shipping_price = $order['shipping_price'];
                    // $cod_amount = $order['price'];
                    $cod_amount = $total_price - $shipping_price;
                    $cod_amount_origin = $total_price - $shipping_price;
                    $order_price = $order['price'];
                    if ($is_cod == 2) {
                        $cod_amount = 0;
                    }

                    $params = [
                        'from_province_name' => $shipping_address['viettel_province_name'],
                        'from_district_name' => $shipping_address['viettel_district_name'],
                        'to_province_name' => $order['viettel_city'],
                        'to_district_name' => $order['viettel_district_reciever'],
                    ];
                    $response_params = AdminOrdersConfig::get_zones_viettel_id($params, $config);
                    foreach ($response_params as $param) {
                        if (empty($param)) {
                            break;
                        }
                    }

                    $from_province_id = $response_params['from_province_id'];
                    $to_province_id = $response_params['to_province_id'];
                    $from_district_id = $response_params['from_district_id'];
                    $to_district_id = $response_params['to_district_id'];
                    $order_payment = 2; // Thu hộ tiền cước - tiền hàng
                    if (Url::get('is_freeship') == 1) {
                        $order_payment = 3;
                    }

                    if (Url::get('is_cod') == 2) {
                        // Không cho phép thu tiền hộ
                        $order_payment = 1; // Không thu tiền
                    }

                    $order_service = "VCN";
                    if ($order_viettel = DB::fetch("SELECT id, viettel_service FROM orders_viettel WHERE order_id = " . $order_id)) {
                        if (!empty($order_viettel['viettel_service'])) {
                            $order_service = $order_viettel['viettel_service'];
                        }
                    }

                    $data = [
                        "ORDER_NUMBER" => $order_id,
                        "GROUPADDRESS_ID" => (Url::get('kho_viettel') ? Url::get('kho_viettel') : 0),
                        "SENDER_PROVINCE" => $from_province_id,
                        "SENDER_DISTRICT" => $from_district_id,
                        "SENDER_FULLNAME" => $shipping_address['name'],
                        "SENDER_ADDRESS" => $shipping_address['address'],
                        "SENDER_PHONE" => $shipping_address['phone'],
                        "SENDER_WARD" => 0,
                        "RECEIVER_FULLNAME" => trim($order['customer_name']),
                        "RECEIVER_ADDRESS" => $order['address'],
                        "RECEIVER_PHONE" => $order['mobile'],
                        "RECEIVER_WARD" => 0,
                        "RECEIVER_DISTRICT" => $to_district_id,
                        "RECEIVER_PROVINCE" => $to_province_id,
                        "PRODUCT_TYPE" => "HH",
                        "ORDER_PAYMENT" => $order_payment,
                        "ORDER_SERVICE" => $order_service,
                        "MONEY_COLLECTION" => $cod_amount, // Số tiền thu hộ không bao gồm tiền cước
                        "MONEY_TOTAL" => 0,
                    ];
                    /*
                         * "PRODUCT_LENGTH" => 10,
                        "PRODUCT_WIDTH" => 10,
                        "PRODUCT_HEIGHT" => 5,*/
                    $list_item = [];
                    $product_name = [];
                    $total_quantity = 0; $product_price = 0;
                    $total_weight = 0;
                    $products = AdminOrdersDB::getProductsFromOrderId(DB::escape($order_id));
                    if (empty($products)) {
                        $errors[$order_id][] = 'Chưa chọn sản phẩm.';
                        continue;
                    }

                    $productsInfo = [];
                    foreach ($products as $product) {
                        $product_name_item = $product['product_name'];
                        $product_price_item = $product['product_price'] * $product['qty'];
                        $list_item[] = [
                            'PRODUCT_NAME' => $product_name_item,
                            'PRODUCT_WEIGHT' => (int)($product['weight']),
                            'PRODUCT_QUANTITY' => $product['qty'],
                            "PRODUCT_PRICE" => $product['product_price']
                        ];
                        $product_name[] = $product_name_item;
                        $total_quantity += $product['qty'];
                        $product_price += $product_price_item;
                        $total_weight += (int)($product['qty'] * $product['weight']);
                        $productsInfo[] = $product['qty'] . 'x' . $product_name_item;
                    }

                    if ($total_weight == 0) {
                        $errors[$order_id][] = 'Chưa nhập trọng lượng sản phẩm.';
                        continue;
                    }

                    $data['PRODUCT_NAME'] = 'combo sản phẩm bao gồm: ' . implode('; ', $productsInfo);
                    $data['PRODUCT_QUANTITY'] = $total_quantity;
                    $data['PRODUCT_PRICE'] = $product_price;
                    $data['PRODUCT_WEIGHT'] = $total_weight;
                    $data['ORDER_SERVICE_ADD'] = "";
                    if (!empty($list_item)) {
                        $data['LIST_ITEM'] = $list_item;
                    }

                    if (Url::get('pick_option') == "post") {
                        $data['ORDER_SERVICE_ADD'] = "GNG";
                    }

                    $note_code = Url::get('note_code');
                    $text_note = [];
                    $text_note[] = $order["shipping_note"];
                    if ($note_code == 'CHOXEMHANGKHONGTHU') {
                        $text_note[] = "Cho xem hàng và không cho thử";
                    } elseif ($note_code == "CHOTHUHANG") {
                        $text_note[] = "Cho thử hàng";
                    } elseif ($note_code == "KHONGCHOXEMHANG") {
                        $text_note[] = "Không cho xem hàng";
                    }

                    $text_note = implode("|", $text_note);
                    $data['ORDER_NOTE'] = implode('-', $productsInfo) . $text_note;
                    $rows_carrier['order_id'] = $order_id;
                    $rows_carrier['note_code'] = Url::get('note_code');
                    $rows_carrier['total_weight'] = (float)$total_weight;
                    $rows_carrier['total_width'] = 10;
                    $rows_carrier['total_length'] = 10;
                    $rows_carrier['total_height'] = 5;
                    $rows_carrier['cod_amount'] = $cod_amount;
                    $rows_carrier['address_text'] = $order['customer_name'] . ',' . $order['mobile']  . ',' .$order['address'];
                    if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress(DB::escape($order_id))) {
                        // System::debug($data);die();
                        $response_api = AdminOrdersConfig::execute_post_curl($config['api_create_order'], json_encode($data), $token);
                        $response_api = json_decode($response_api, true);
                        if ($response_api['status'] == 200) {
                            try {
                                $response_api = $response_api['data'];
                                $rows_carrier['shipping_order_code'] = $response_api['ORDER_NUMBER'];
                                $rows_carrier['total_service_fee'] = $response_api['MONEY_TOTAL'];
                                DB::insert('orders_shipping', $rows_carrier);

                                DB::insert('order_shipping_status_history', [
                                    'order_id' => $order_id,
                                    'status' => $status_config['CHO_LAY_HANG'],
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);

                                $shipping_fee = $response_api['MONEY_TOTAL'];
                                if ($is_freeship == 1) {
                                    $shipping_fee = 0;
                                }

                                if ($addDeliverOrder == 2) {
                                    $shipping_fee = 0;
                                }

                                $total_fee = $cod_amount_origin + $shipping_fee;
                                $status_id = CHUYEN_HANG;
                                DB::update_id('orders', [
                                    'shipping_price' => $shipping_fee,
                                    'postal_code' => $response_api['ORDER_NUMBER'],
                                    'total_price' => $total_fee,
                                    'status_id' => $status_id,
                                    'delivered'=>date('Y-m-d H:i:s'),
                                    'user_delivered'=>AdminOrders::$user_id
                                ], $order_id);
                                AdminOrdersDB::update_revision($order_id,$order['status_id'],$status_id);
                                $success[] = $order_id;
                                $ex_ids .= ($ex_ids?',':'').$order_id;
                            } catch (Exception $e) {
                                $errors[$order_id][] = "Tạo đơn thành công trên Viettel Post. Tuy nhiên, đã có lỗi xảy ra khi cập nhật đơn hàng trên tuha.";
                                continue;
                            }
                        } else {
                            $errorsMessage = [
                                201 => 'Token không hợp lệ',
                                202 => 'Lỗi token (để trống, đã hết hạn...)',
                                203 => 'Lỗi trường không được bỏ trống (mã đơn hàng,....)',
                                204 => 'Lỗi dữ liệu không hợp lệ',
                                205 => 'Lỗi hệ thống',
                                206 => 'Mã đơn hàng đã tồn tại trên hệ thống',
                            ];
                            $errors[$order_id][] = 'Có lỗi xảy ra khi tạo đơn trên Viettel Post - ' . ($response_api['status']?$errorsMessage[$response_api['status']]:'');
                            continue;
                        }
                    } else {
                        $errors[$order_id][] = 'Mã đơn hàng đã tồn tại trên hệ thống vận chuyển.';
                        continue;
                    }
                }
            }
            if($ex_ids){
                if(AdminOrders::$create_export_invoice_when_delivered){
                    require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
                    //QlbhStockInvoiceDB::xuatKho(DB::escape($ex_ids));
                }
            }
            $response_deliver['success'] = $success;
            $response_deliver['errors'] = $errors;
            $_SESSION['response_deliver'] = $response_deliver;
            //Url::js_redirect(true, 'Dữ liệu đã được cập nhật...', ['cmd' => 'multi-shipping']);

            // System::debug($response_deliver);
        }
    }

    function on_submit(){

        if (!Url::get('new_ids')) {
            $validates = $this->validateForm();
            if (!$validates['success']) {
                $response_deliver['success'] = [];
                $response_deliver['errors'] = $validates['errors'];
                $_SESSION['response_deliver'] = $response_deliver;
                return;
            }

            // mysqli_begin_transaction(DB::$db_connect_id);
            require_once "vendor/autoload.php";
            $orders = $validates['orders'];
            $ids = Url::get('ids');
            $carrier_id = Url::get("shipping_carrier_id");
            $radio_shipping_address = Url::get("radio_shipping_address");
            $shipping_option_id = Url::get("shipping_option_id");
            $best_ServiceId = Url::get("best_ServiceId", 12491);
            $bdvn_ServiceId = Url::get("bdvn_ServiceId", 'BK');
            $jnt_partsign = Url::get("jnt_partsign", '0');
            $shipping_address_id = 0;
//            $shipping_address = null;
//            $shipping_address = AdminOrdersDB::getShippingAddressById($radio_shipping_address);
            // get from api
            $data_request = array('id' => $radio_shipping_address);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/get', $data_request);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $shipping_address = $dataRes['data'];
                $shipping_address_id = $shipping_address['mysql_id'];
            }
            //

            $group_id = AdminOrders::$group_id;
            $is_cod = Url::get('is_cod');
            $pick_option = Url::get('pick_option');
            $is_freeship = Url::get('is_freeship');
            if ($carrier_id == 'api_ems') {
                $is_freeship = 1;
            }
            $shippingNote = [];
            if (Url::get('is_fragile')) {
                $shippingNote[] = 'Hàng dễ vỡ';
            }

            $shipping_costs = array();
            $shipping_options = array();
            // get from api
            $data_request = array('shop_id' => $group_id);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $shipping_costs = $dataRes['data']['brand'];
                $shipping_options = $dataRes['data']['settings']['settings'];
            }
            //

            $shippingInfo = null;
            $transportInfo = null;
            if (isset($shipping_options[$shipping_option_id])) {
                $shippingInfo = $shipping_options[$shipping_option_id];
            }
            foreach ($shipping_costs as $transport) {
                if ($transport['alias'] === $carrier_id) {
                    $transportInfo = $transport;
                }
            }

            if ($transportInfo['alias'] === 'api_jt') {
                $data_string = false;
                $ordertype = array(1 => "0", 2 => "1")[$is_cod];
                $servicetype = array('cod' => "1", 'post' => "0")[$pick_option];
                $arrFreeShip = array('CC_CASH', 'PP_CASH', 'PP_PM');
                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = AdminOrders::$user_id;
                    $data_string->transport_config = new \stdClass();
                    $data_string->transport_config->logisticproviderid = $transportInfo['alias'];
                    $data_string->transport_config->customerid = $shippingInfo['token'];
                    $data_string->transport_config->customerid_name = $shippingInfo['name'];
                    $data_string->transport_config->setting_id = $shippingInfo['_id'];
                    $data_string->transport_config->ordertype = $ordertype;
                    $data_string->transport_config->partsign = $jnt_partsign;
                    $data_string->transport_config->servicetype = $servicetype;
                    $data_string->transport_config->createordertime = date('Y-m-d H:i:s');
                    $data_string->transport_config->sendstarttime = '';
                    $data_string->transport_config->sendendtime = '';
                    $data_string->transport_config->paytype = $arrFreeShip[$is_freeship];
                    $data_string->transport_config->transport = '';
                    $data_string->transport_config->remark = implode(", ", $shippingNote);
                    $data_string->transport_config->note_code = Url::get('note_code');

                    $data_string->sender = new \stdClass();
                    $data_string->sender->name = $shipping_address['name'];
                    $data_string->sender->mobile = $shipping_address['phone'];
                    $data_string->sender->phone = $shipping_address['phone'];
                    $data_string->sender->prov = $shipping_address['province_name'];
                    $data_string->sender->city = $shipping_address['district_name'];
                    $data_string->sender->area = $shipping_address['ward_name'];
                    $data_string->sender->address = $shipping_address['address'];

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->order_code = (string) $order_id;
                        $orderInfo->txlogisticid = 'TH_JNT_' . $order_id;
                        $orderInfo->goodsvalue = $order['price'];
                        $orderInfo->itemsvalue = $order['total_price'];
                        $orderInfo->insurance_value = $order['insurance_value'];
                        $orderInfo->isInsured = '';
                        $orderInfo->weight = 0;
                        $orderInfo->volume = 0;
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->to_name = $order['customer_name'];
                        $orderInfo->receiver->to_phone = $order['mobile'];
                        $orderInfo->receiver->to_address = $order['address'];
                        $orderInfo->receiver->to_area = '';
                        $orderInfo->receiver->to_ward_code = '';
                        $orderInfo->receiver->to_district_id = '';
                        $orderInfo->receiver->to_district_name = '';
                        $orderInfo->receiver->to_province_name = '';
                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        WHERE orders.id = ' . DB::escape($order_id);
//                        WHERE orders.id IN (' . $arrOrder . ')';

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $strProperties = '';
                                $orderInfo->receiver->to_area = ($orderInfo->receiver->to_area) ?: $row['ward_name'];
                                $orderInfo->receiver->to_district_name = ($orderInfo->receiver->to_district_name) ?: $row['district_name'];
                                $orderInfo->receiver->to_province_name = ($orderInfo->receiver->to_province_name) ?: $row['province_name'];

                                $item = new \stdClass;
                                $item->itemname = $row['quantity'].'x'.$row['itemname']; // required
                                $item->englishName = $row['quantity'].'x'.$row['itemname']; // required
                                $item->number = $row['quantity']; // required
                                $item->itemvalue = $row['itemvalue']; // required
                                $item->desc = 'abc'; // required
                                array_push($orderInfo->items, $item);

                                $orderInfo->weight += (int)($row['weight'] * $row['quantity']);
                                if (is_numeric($row['width']) && is_numeric($row['height']) && is_numeric($row['length']))
                                    $orderInfo->volume += ($row['width'] * $row['height'] * $row['length']);
                            }
                        }
                        $orderInfo->weight = (string) $orderInfo->weight;
                        $orderInfo->volume = (string) $orderInfo->volume;
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
//                    print_r(json_encode($data_string));die;
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/jt/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_best') {
                $data_string = new \stdClass();
                $ordertype = array(1 => "0", 2 => "1")[$is_cod];
                $servicetype = array('cod' => "1", 'post' => "0")[$pick_option];

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = AdminOrders::$user_id;
                    $data_string->JourneyType = 1;
                    $data_string->ServiceId = (int) $best_ServiceId;
                    $data_string->SourceCity = $shipping_address['province_name'];
                    $data_string->SourceDistrict = $shipping_address['district_name'];
                    $data_string->SourceWard = $shipping_address['ward_name'];
                    $data_string->SourceAddress = $shipping_address['address'];
                    $data_string->SourceName = $shipping_address['name'];
                    $data_string->SourcePhoneNumber = $shipping_address['phone'];

                    $data_string->transport_config = new \stdClass();
                    $data_string->transport_config->customerid = $shippingInfo['client_id'];
                    $data_string->transport_config->customer_token = $shippingInfo['token'];
                    $data_string->transport_config->customerid_name = $shippingInfo['name'];
                    $data_string->transport_config->setting_id = $shippingInfo['_id'];
                    $data_string->transport_config->ordertype = $ordertype;
                    $data_string->transport_config->servicetype = $servicetype;
                    $data_string->transport_config->paytype = ($is_freeship) ?  'PP_CASH' : 'CC_CASH';
                    $data_string->transport_config->remark = implode(", ", $shippingNote);
                    $data_string->transport_config->note_code = Url::get('note_code');

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->order_code = (string) $order_id;
                        $orderInfo->Code = 'TH_BEST_' . $order_id;
                        $orderInfo->ProductPrice = $order['price'];
                        $orderInfo->CollectAmount = $order['total_price'];
                        $orderInfo->insurance_value = $order['insurance_value'];
                        $orderInfo->ProductName = '';
                        $orderInfo->Weight = 0;
                        $orderInfo->Width = 0;
                        $orderInfo->Height = 0;
                        $orderInfo->Length = 0;
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->DestCity = '';//$order['city'];
                        $orderInfo->receiver->DestDistrict = '';//$order['district_reciever'];
                        $orderInfo->receiver->DestWard = '';
                        $orderInfo->receiver->DestAddress = $order['address'];
                        $orderInfo->receiver->DestName = $order['customer_name'];
                        $orderInfo->receiver->DestPhoneNumber = $order['mobile'];

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        WHERE orders.id = ' . DB::escape($order_id);
//                        WHERE orders.id IN (' . $arrOrder . ')';

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
//                                $item = new \stdClass;
                                $strProperties = '';
                                $orderInfo->receiver->DestWard = ($orderInfo->receiver->DestWard) ?: $row['ward_name'];
                                $orderInfo->receiver->DestDistrict = ($orderInfo->receiver->DestDistrict) ?: $row['district_name'];
                                $orderInfo->receiver->DestCity = ($orderInfo->receiver->DestCity) ?: $row['province_name'];
                                $orderInfo->ProductName .= $row['quantity'] . 'x' . $row['itemname'] . ', ';
                                $orderInfo->Weight += (int)($row['weight'] * $row['quantity']);
//                                if (is_numeric($row['width']) && is_numeric($row['height']) && is_numeric($row['length']))
//                                    $orderInfo->volume += ($row['width'] * $row['height'] * $row['length']);
//                                array_push($orderInfo->items, $item);
                            }
                        }

//                        $orderInfo->weight = (string) $orderInfo->weight;
//                        $orderInfo->volume = (string) $orderInfo->volume;
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/best/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_ghtk') {
                $data_string = new \stdClass();
                $note_code = Url::get('note_code');
                $ghtk_ServiceId = Url::get('ghtk_ServiceId', '');
                $ghtkAddOns = array();
                if (!empty(Url::get('ghtk_addon'))) {
                    $ghtkAddOns = Url::get('ghtk_addon');
                }

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = (int)AdminOrders::$user_id;

                    $data_string->sender = new \stdClass();
                    $data_string->sender->pick_province = $shipping_address['province_name'];
                    $data_string->sender->pick_district = $shipping_address['district_name'];
                    $data_string->sender->pick_ward = $shipping_address['ward_name'];
                    $data_string->sender->pick_address = $shipping_address['address'];
                    $data_string->sender->pick_name = $shipping_address['name'];
                    $data_string->sender->pick_tel = $shipping_address['phone'];

                    $data_string->config = new \stdClass();
                    $data_string->config->setting_token = $shippingInfo['token'];
                    $data_string->config->setting_name = $shippingInfo['name'];
                    $data_string->config->setting_id = $shippingInfo['_id'];
                    $data_string->config->is_cod = $is_cod;
                    $data_string->config->pick_option = $pick_option;
                    $data_string->config->is_freeship = $is_freeship;
                    $data_string->config->note_code = $note_code;
                    $data_string->config->transport = $ghtk_ServiceId;
                    $data_string->config->addons = $ghtkAddOns;

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->price = $order['price'];
                        $orderInfo->total_price = $order['total_price'];
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->viettel_service = '';
                        $orderInfo->ghtk_service = '';
                        $orderInfo->ems_service = '';

                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->province = '';//$order['city'];
                        $orderInfo->receiver->district = '';//$order['district_reciever'];
                        $orderInfo->receiver->ward = '';
                        $orderInfo->receiver->address = $order['address'];
                        $orderInfo->receiver->name = $order['customer_name'];
                        $orderInfo->receiver->tel = $order['mobile'];

                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name,
                            orders_viettel.viettel_service, orders_ghtk.transport_ghtk as ghtk_service, orders_ems.service_id as ems_service
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id 
                        LEFT JOIN orders_viettel ON orders.id = orders_viettel.order_id 
                        LEFT JOIN orders_ghtk ON orders.id = orders_ghtk.order_id 
                        LEFT JOIN orders_ems ON orders.id = orders_ems.order_id 
                        WHERE orders.id = ' . DB::escape($order_id);

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $orderInfo->receiver->ward = ($orderInfo->receiver->ward) ?: $row['ward_name'];
                                $orderInfo->receiver->district = ($orderInfo->receiver->district) ?: $row['district_name'];
                                $orderInfo->receiver->province = ($orderInfo->receiver->province) ?: $row['province_name'];

                                $orderInfo->viettel_service = ($orderInfo->viettel_service) ?: $row['viettel_service'];
                                $orderInfo->ghtk_service = ($orderInfo->ghtk_service) ?: $row['ghtk_service'];
                                $orderInfo->ems_service = ($orderInfo->ems_service) ?: $row['ems_service'];

                                $item = new \stdClass;
                                $item->name = $row['itemname']; // required
                                $item->weight = (int) $row['weight']; // required
                                $item->quantity = (int) $row['quantity']; // required
                                array_push($orderInfo->items, $item);
                            }
                        }
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/ghtk/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_ems') {
                $data_string = new \stdClass();
                $note_code = Url::get('note_code');
                $ems_service = Url::get('ems_service', 0);
                $emsAddOns = array();
                if (!empty(Url::get('ems_addon'))) {
                    $emsAddOns = Url::get('ems_addon');
                }

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = (int)AdminOrders::$user_id;

                    $data_string->sender = new \stdClass();
                    $data_string->sender->province = $shipping_address['province_name'];
                    $data_string->sender->district = $shipping_address['district_name'];
                    $data_string->sender->ward     = $shipping_address['ward_name'];
                    $data_string->sender->address  = $shipping_address['address'];
                    $data_string->sender->name     = $shipping_address['name'];
                    $data_string->sender->phone    = $shipping_address['phone'];

                    $data_string->config = new \stdClass();
                    $data_string->config->setting_token = $shippingInfo['token'];
                    $data_string->config->setting_name = $shippingInfo['name'];
                    $data_string->config->setting_id = $shippingInfo['_id'];
                    $data_string->config->is_cod = $is_cod;
                    $data_string->config->pick_option = $pick_option;
                    $data_string->config->is_freeship = $is_freeship;
                    $data_string->config->is_fragile = Url::get('is_fragile');
                    $data_string->config->note_code = $note_code;
                    $data_string->config->transport = $ems_service;
                    $data_string->config->addons = $emsAddOns;

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->price = $order['price'];
                        $orderInfo->total_price = $order['total_price'];
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->viettel_service = '';
                        $orderInfo->ghtk_service = '';
                        $orderInfo->ems_service = '';

                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->province   = '';
                        $orderInfo->receiver->district   = '';
                        $orderInfo->receiver->ward       = '';
                        $orderInfo->receiver->address    = $order['address'];
                        $orderInfo->receiver->name       = $order['customer_name'];
                        $orderInfo->receiver->phone      = $order['mobile'];

                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name,
                            orders_viettel.viettel_service, orders_ghtk.transport_ghtk as ghtk_service, orders_ems.service_id as ems_service
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id 
                        LEFT JOIN orders_viettel ON orders.id = orders_viettel.order_id 
                        LEFT JOIN orders_ghtk ON orders.id = orders_ghtk.order_id 
                        LEFT JOIN orders_ems ON orders.id = orders_ems.order_id 
                        WHERE orders.id = ' . DB::escape($order_id);

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $orderInfo->receiver->ward = ($orderInfo->receiver->ward) ?: $row['ward_name'];
                                $orderInfo->receiver->district = ($orderInfo->receiver->district) ?: $row['district_name'];
                                $orderInfo->receiver->province = ($orderInfo->receiver->province) ?: $row['province_name'];

                                $orderInfo->viettel_service = ($orderInfo->viettel_service) ?: $row['viettel_service'];
                                $orderInfo->ghtk_service = ($orderInfo->ghtk_service) ?: $row['ghtk_service'];
                                $orderInfo->ems_service = ($orderInfo->ems_service) ?: $row['ems_service'];

                                $item = new \stdClass;
                                $item->name = $row['itemname']; // required
                                $item->weight = (int) $row['weight']; // required
                                $item->quantity = (int) $row['quantity']; // required
                                array_push($orderInfo->items, $item);
                            }
                        }
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/ems/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_viettel_post') {
                $data_string = new \stdClass();
                $note_code = Url::get('note_code');
                $viettel_service = Url::get('viettel_service', '');
                $vtpAddOns = array();
                if (!empty(Url::get('vtp_addon'))) {
                    $vtpAddOns = Url::get('vtp_addon');
                }

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = (int)AdminOrders::$user_id;

                    $data_string->sender = new \stdClass();
                    $data_string->sender->province = $shipping_address['province_name'];
                    $data_string->sender->district = $shipping_address['district_name'];
                    $data_string->sender->ward     = $shipping_address['ward_name'];
                    $data_string->sender->address  = $shipping_address['address'];
                    $data_string->sender->name     = $shipping_address['name'];
                    $data_string->sender->phone    = $shipping_address['phone'];

                    $data_string->config = new \stdClass();
                    $data_string->config->setting_token = $shippingInfo['token'];
                    $data_string->config->setting_name = $shippingInfo['name'];
                    $data_string->config->setting_id = $shippingInfo['_id'];
                    $data_string->config->is_cod = $is_cod;
                    $data_string->config->pick_option = $pick_option;
                    $data_string->config->is_freeship = $is_freeship;
                    $data_string->config->note_code = $note_code;
                    $data_string->config->transport = $viettel_service;
                    $data_string->config->addons = $vtpAddOns;

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->price = $order['price'];
                        $orderInfo->total_price = $order['total_price'];
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->viettel_service = '';
                        $orderInfo->ghtk_service = '';
                        $orderInfo->ems_service = '';

                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->province   = '';
                        $orderInfo->receiver->district   = '';
                        $orderInfo->receiver->ward       = '';
                        $orderInfo->receiver->address    = $order['address'];
                        $orderInfo->receiver->name       = $order['customer_name'];
                        $orderInfo->receiver->phone      = $order['mobile'];

                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name, 
                            orders_viettel.viettel_service, orders_ghtk.transport_ghtk as ghtk_service, orders_ems.service_id as ems_service
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        LEFT JOIN orders_viettel ON orders.id = orders_viettel.order_id 
                        LEFT JOIN orders_ghtk ON orders.id = orders_ghtk.order_id 
                        LEFT JOIN orders_ems ON orders.id = orders_ems.order_id 
                        WHERE orders.id = ' . DB::escape($order_id);

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $orderInfo->receiver->ward = ($orderInfo->receiver->ward) ?: $row['ward_name'];
                                $orderInfo->receiver->district = ($orderInfo->receiver->district) ?: $row['district_name'];
                                $orderInfo->receiver->province = ($orderInfo->receiver->province) ?: $row['province_name'];

                                $orderInfo->viettel_service = ($orderInfo->viettel_service) ?: $row['viettel_service'];
                                $orderInfo->ghtk_service = ($orderInfo->ghtk_service) ?: $row['ghtk_service'];
                                $orderInfo->ems_service = ($orderInfo->ems_service) ?: $row['ems_service'];

                                $item = new \stdClass;
                                $item->name = $row['itemname']; // required
                                $item->weight = (int) $row['weight']; // required
                                $item->quantity = (int) $row['quantity']; // required
                                array_push($orderInfo->items, $item);
                            }
                        }
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/viettelpost/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_bdvn') {
                $data_string = new \stdClass();
                $ordertype = array(1 => "0", 2 => "1")[$is_cod];
                $servicetype = array('cod' => "1", 'post' => "2")[$pick_option];
                $countOrder = count($orders);
                $bdvnAddOns = array();
                if (!empty(Url::get('bdvn_addon'))) {
                    $bdvnAddOns = Url::get('bdvn_addon');
                }
                $hidden_info = Url::get("hidden_info");

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = AdminOrders::$user_id;
                    $data_string->ServiceName = $bdvn_ServiceId;
                    $data_string->AddOns = $bdvnAddOns;
                    $data_string->SenderProvinceId = $shipping_address['province_name'];
                    $data_string->SenderDistrictId = $shipping_address['district_name'];
                    $data_string->SenderWardId = $shipping_address['ward_name'];
                    $data_string->SenderAddress = $shipping_address['address'];
                    $data_string->SenderFullname = $shipping_address['name'];
                    $data_string->SenderTel = $shipping_address['phone'];

                    $data_string->transport_config = new \stdClass();
                    $data_string->transport_config->client_id = $shippingInfo['client_id'];
                    $data_string->transport_config->customerCode = $shippingInfo['customerCode'];
                    $data_string->transport_config->name = $shippingInfo['name'];
                    $data_string->transport_config->setting_id = $shippingInfo['_id'];
                    $data_string->transport_config->ordertype = $ordertype;
                    $data_string->transport_config->servicetype = $servicetype;
                    $data_string->transport_config->paytype = ($is_freeship) ?  'BAN_CASH' : 'MUA_CASH';
                    $data_string->transport_config->remark = implode(", ", $shippingNote);
                    $data_string->transport_config->note_code = Url::get('note_code');
                    $data_string->transport_config->hidden_info = $hidden_info;

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->order_code = (string) $order_id;
                        $orderInfo->Code = (string) $order_id;
                        $orderInfo->ProductPrice = $order['price'];
                        $orderInfo->CollectAmount = $order['total_price'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->ProductName = '';
                        $orderInfo->Weight = 0;
                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->ReceiverProvinceId = '';//$order['city'];
                        $orderInfo->receiver->ReceiverDistrictId = '';//$order['district_reciever'];
                        $orderInfo->receiver->ReceiverWardId = '';
                        $orderInfo->receiver->ReceiverAddress = $order['address'];
                        $orderInfo->receiver->ReceiverFullname = $order['customer_name'];
                        $orderInfo->receiver->ReceiverTel = $order['mobile'];

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname, 
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        WHERE orders.id = ' . DB::escape($order_id);
//                        WHERE orders.id IN (' . $arrOrder . ')';

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        $countTotal = mysqli_num_rows($result);
                        if ($countTotal > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
//                                $item = new \stdClass;
                                $orderInfo->receiver->ReceiverWardId = ($orderInfo->receiver->ReceiverWardId) ?: $row['ward_name'];
                                $orderInfo->receiver->ReceiverDistrictId = ($orderInfo->receiver->ReceiverDistrictId) ?: $row['district_name'];
                                $orderInfo->receiver->ReceiverProvinceId = ($orderInfo->receiver->ReceiverProvinceId) ?: $row['province_name'];

                                $productNameFirst = ($countTotal > 1 && $orderInfo->ProductName != '') ? '; ' : ($countTotal > 1 ? 'combo sản phẩm bao gồm: ' : '');
                                $orderInfo->ProductName .= $productNameFirst . $row['quantity'] . 'x' . $row['itemname'];
                                $orderInfo->Weight += (int)($row['weight'] * $row['quantity']);
//                                if (is_numeric($row['width']) && is_numeric($row['height']) && is_numeric($row['length']))
//                                    $orderInfo->volume += ($row['width'] * $row['height'] * $row['length']);
//                                array_push($orderInfo->items, $item);
                            }
                        }

//                        $orderInfo->weight = (string) $orderInfo->weight;
//                        $orderInfo->volume = (string) $orderInfo->volume;
                        array_push($data_string->orders, $orderInfo);
//                        for ($x = 0; $x < 1000; $x++) {
//                            $orderInfo = (object) json_decode(json_encode($orderInfo));
//                            $orderInfo->order_id = 100000000000 + $x + $order_id;
//                            $orderInfo->order_code = (string) $orderInfo->order_id;
//                            $orderInfo->Code = 'THBDVN' . $orderInfo->order_code;
//                            array_push($data_string->orders, $orderInfo);
//                            $countOrder++;
//                        }
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $timestart = time();
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/bdvn/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
//                        $arrParams = array(
//                            'act' => 'print',
//                            'bdvn_paper_size' => 'A4-A5-NGANG-NEW',
//                            'timestamp' => $timestart,
//                            'brand' => 'api_bdvn',
//                            'limit' => $countOrder
//                        );
//                        $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
//                        $url = $protocol.'://'. $_SERVER['HTTP_HOST'].'/'.'index062019.php?page=admin_orders&'. http_build_query($arrParams);
//                        echo '<script>window.open("'.$url.'", "_blank")</script>';
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_bigfastv2') {
                $data_string = new \stdClass();
                $note_code = Url::get('note_code');
                $bigfast_service = Url::get('bigfast_service', 0);
                $bigfast_addons = array();

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = (int)AdminOrders::$user_id;

                    $data_string->sender = new \stdClass();
                    $data_string->sender->province = $shipping_address['province_name'];
                    $data_string->sender->district = $shipping_address['district_name'];
                    $data_string->sender->ward     = $shipping_address['ward_name'];
                    $data_string->sender->address  = $shipping_address['address'];
                    $data_string->sender->name     = $shipping_address['name'];
                    $data_string->sender->phone    = $shipping_address['phone'];

                    $data_string->config = new \stdClass();
                    $data_string->config->setting_token = $shippingInfo['token'];
                    $data_string->config->setting_name = $shippingInfo['name'];
                    $data_string->config->setting_id = $shippingInfo['_id'];
                    $data_string->config->is_cod = $is_cod;
                    $data_string->config->pick_option = $pick_option;
                    $data_string->config->is_freeship = $is_freeship;
                    $data_string->config->note_code = $note_code;
                    $data_string->config->service = $bigfast_service;
                    $data_string->config->addons = $bigfast_addons;

                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->price = $order['price'];
                        $orderInfo->total_price = $order['total_price'];
                        $orderInfo->shipping_note = $order['shipping_note'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->province   = '';//$order['city'];
                        $orderInfo->receiver->district   = '';//$order['district_reciever'];
                        $orderInfo->receiver->ward       = '';
                        $orderInfo->receiver->address    = $order['address'];
                        $orderInfo->receiver->name       = $order['customer_name'];
                        $orderInfo->receiver->phone      = $order['mobile'];

                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname,
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        WHERE orders.id = ' . DB::escape($order_id);

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $orderInfo->receiver->ward = ($orderInfo->receiver->ward) ?: $row['ward_name'];
                                $orderInfo->receiver->district = ($orderInfo->receiver->district) ?: $row['district_name'];
                                $orderInfo->receiver->province = ($orderInfo->receiver->province) ?: $row['province_name'];

                                $item = new \stdClass;
                                $item->name = $row['itemname']; // required
                                $item->price = (int) $row['itemvalue']; // required
                                $item->weight = (int) $row['weight']; // required
                                $item->quantity = (int) $row['quantity']; // required
                                $item->code = '';
                                array_push($orderInfo->items, $item);
                            }
                        }
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/bigfastv2/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } elseif ($transportInfo['alias'] === 'api_ghn') {
                $data_string = new \stdClass();
                $ordertype = array(1 => "0", 2 => "1")[$is_cod];
                $servicetype = array('cod' => "1", 'post' => "0")[$pick_option];

                // push order shipping to api transport
                if ($shippingInfo && $transportInfo && isset($shipping_address['info']['ghn_warehouse_id'])) {
                    $data_string = new \stdClass();
                    $data_string->shop_id = $group_id;
                    $data_string->user_id = AdminOrders::$user_id;

                    $data_string->transport_config = new \stdClass();
                    $data_string->transport_config->logisticproviderid = $transportInfo['alias'];
                    $data_string->transport_config->customerid = $shippingInfo['client_id'];
                    $data_string->transport_config->customer_token = $shippingInfo['token'];
                    $data_string->transport_config->customerid_name = $shippingInfo['name'];
                    $data_string->transport_config->setting_id = $shippingInfo['_id'];
                    $data_string->transport_config->ordertype = $ordertype;
                    $data_string->transport_config->servicetype = $servicetype;
                    $data_string->transport_config->paytype = ($is_freeship) ?  'PP_CASH' : 'CC_CASH';
                    $data_string->transport_config->shop_id = $shipping_address['info']['ghn_warehouse_id'];
                    $data_string->transport_config->remark = implode(", ", $shippingNote);
                    $data_string->transport_config->note_code = Url::get('note_code');

                    $data_string->sender = new \stdClass();
                    $data_string->sender->name = $shipping_address['name'];
                    $data_string->sender->mobile = $shipping_address['phone'];
                    $data_string->sender->phone = $shipping_address['phone'];
                    $data_string->sender->prov = $shipping_address['province_name'];
                    $data_string->sender->city = $shipping_address['district_name'];
                    $data_string->sender->area = $shipping_address['ward_name'];
                    $data_string->sender->address = $shipping_address['address'];

                    $data_string->return = new \stdClass();
                    $data_string->return->return_phone = $shipping_address['phone'];
                    $data_string->return->return_address = $shipping_address['address'];
                    $data_string->return->return_district_id = $shipping_address['district_info']['ghn']['id'];
                    $data_string->return->return_ward_code = $shipping_address['ward_info']['ghn']['code'];

                    $data_string->orders = array();
                    $checkInfoReceiver = false;
                    $data_string->orders = array();
                    foreach ($orders as $order_id => $order) {
                        $total_pay_amount = DB::fetch('SELECT SUM(pay_amount) AS total FROM order_prepaid WHERE order_id = ' . $order_id,'total');
                        if ($total_pay_amount) {
                            if ($order['total_price'] < $total_pay_amount) {
                                $order['total_price'] = 0;
                            } else {
                                $order['total_price'] = $order['total_price'] - $total_pay_amount;
                            }
                        }

                        $orderInfo = new \stdClass();
                        $orderInfo->order_id = $order_id;
                        $orderInfo->order_code = (string) $order_id;
                        $orderInfo->txlogisticid = 'TH_GHN_' . $order_id;
                        $orderInfo->goodsvalue = $order['price'];
                        $orderInfo->itemsvalue = $order['total_price'];
                        $orderInfo->insurance_value = $order['insurance_value'];

                        $orderInfo->isInsured = '';
                        $orderInfo->productName = '';
                        $orderInfo->weight = 0;
                        $orderInfo->length = 0;
                        $orderInfo->width = 0;
                        $orderInfo->height = 0;
                        $orderInfo->receiver = new \stdClass();
                        $orderInfo->receiver->to_name = $order['customer_name'];
                        $orderInfo->receiver->to_phone = $order['mobile'];
                        $orderInfo->receiver->to_address = $order['address'];
                        $orderInfo->receiver->area = '';
                        $orderInfo->receiver->to_ward_code = '';
                        $orderInfo->receiver->to_district_id = '';
                        $orderInfo->receiver->to_district_name = '';
                        $orderInfo->receiver->to_province_name = '';
                        $orderInfo->items = array();

                        // get order + product
                        $arrOrder = array_keys($orders);
                        $arrOrder = implode(',', $arrOrder);
                        $query = 'SELECT orders.id, orders.price as goodsvalue, IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as itemname, 
                            op.qty as quantity, op.product_price as itemvalue, op.width, op.height,
                            op.length, op.weight, zone_wards_v2.ward_name, zone_districts_v2.district_name, zone_provinces_v2.province_name
                        FROM orders
                        INNER JOIN orders_products as op ON orders.id = op.order_id
                        LEFT JOIN products ON products.id = op.product_id
                        LEFT JOIN zone_provinces_v2 ON orders.city_id = zone_provinces_v2.province_id
                        LEFT JOIN zone_districts_v2 ON orders.district_id = zone_districts_v2.district_id
                        LEFT JOIN zone_wards_v2 ON orders.ward_id = zone_wards_v2.ward_id
                        WHERE orders.id = ' . DB::escape($order_id);
//                        WHERE orders.id IN (' . $arrOrder . ')';

                        $result = mysqli_query(DB::$db_connect_id, $query);
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                $data_string->receiver->area = ($data_string->receiver->area) ?: $row['ward_name'];
                                $orderInfo->receiver->area = ($orderInfo->receiver->area) ?: $row['ward_name'];
                                $orderInfo->receiver->to_district_name = ($orderInfo->receiver->to_district_name) ?: $row['district_name'];
                                $orderInfo->receiver->to_province_name = ($orderInfo->receiver->to_province_name) ?: $row['province_name'];
                                $orderInfo->productName .= $row['itemname'] . ',';
                                $orderInfo->weight += (int)($row['weight'] * $row['quantity']);

                                $item = new \stdClass;
                                $item->name = $row['itemname']; // required
                                $item->code = $row['itemname']; // required
                                $item->quantity = $row['quantity']; // required
                                $item->price = $row['itemvalue']; // required
                                $item->length = $row['length']; // required
                                $item->width = $row['width']; // required
                                $item->height = $row['height']; // required
                                array_push($orderInfo->items, $item);
                            }
                        }
                        $orderInfo->weight = (string) $orderInfo->weight;
                        array_push($data_string->orders, $orderInfo);
                    }
                }

                // get from api
                if ($data_string) {
                    $data_request = $data_string;
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/ghn/create-order', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing-v3');
                    } elseif ($dataRes['status_code'] === 400) {
                        $txtLog = '';
                        if (count($dataRes['data']) > 0) {
                            $txtLog = 'Đơn hàng\n';
                            foreach ($dataRes['data'] as $log) {
                                $txtLog .= $log['order_id'] . '' . $log['reason'] . '\n';
                            }
                        }
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    } elseif (!$dataRes['success']) {
                        $txtLog = 'Lỗi không xác định!';
                        echo "<script type='text/javascript'>alert('$txtLog');</script>";
                    }
                }
            } else {
                $costs_config = AdminOrdersConfig::get_list_shipping_costs();
                $status_config = AdminOrdersConfig::config_shipping_status();
                $status_jobs_config = AdminOrdersConfig::config_jobs_status();
                $user_delivered = get_user_id();
                $viettel_post_config = AdminOrdersConfig::viettel_post_config();
                $viettel_post_addon = AdminOrdersConfig::viettel_post_addon();
                $ems_addons = AdminOrdersConfig::ems_services_addon();
                $worker_account_type = Session::get('account_type');
                $worker_account_id = Session::get('user_id');
                $worker_group_id = Session::get('group_id');
                $viettel_post_arr = ['' => 'Chọn dịch vụ'];
                foreach ($viettel_post_config as $value) {
                    $viettel_post_arr[$value['SERVICE_CODE']] = $value['SERVICE_NAME'];
                }

                $ghtk_ServiceId = Url::get('ghtk_ServiceId', '');
                // System::debug($orders);
                if (Url::get('viettel_service')) {
                    $shippingNote[] = "Dịch vụ Viettel Post: " . ($viettel_post_arr[Url::get('viettel_service')] ?? "");
                }
                $emsServiceConfig = AdminOrdersConfig::ems_services();
                if (Url::get('ems_service')) {
                    $shippingNote[] = "Dịch vụ EMS: " . ($emsServiceConfig[Url::get('ems_service')] ?? "");
                }
                $addOns = [];
                if (!empty(Url::get('vtp_addon'))) {
                    $addOns = Url::get('vtp_addon');
                    $addOnsTxt = [];
                    foreach ($addOns as $addOn) {
                        $addOnsTxt[] = $viettel_post_addon[$addOn];
                    }
                    $shippingNote[] = implode(', ', $addOnsTxt);
                }

                $ghtkAddOns = array();
                if (!empty(Url::get('ghtk_addon'))) {
                    $ghtkAddOns = Url::get('ghtk_addon');
                }

                $emsAddOns = [];
                if (!empty(Url::get('ems_addon'))) {
                    $emsAddOns = Url::get('ems_addon');
                    $emsAddOnsTxt = [];
                    foreach ($emsAddOns as $addOn) {
                        $emsAddOnsTxt[] = $ems_addons[$addOn];
                    }
                    $shippingNote[] = implode(', ', $emsAddOnsTxt);
                }

                $config = $costs_config[$carrier_id];
//                $shipping_option = AdminOrdersDB::getShippingOptionById($shipping_option_id);
                $shipping_option = $shippingInfo;
                $token = $shipping_option['token'];
                $shipping_option_id_mongo = $shipping_option['_id'];
                $addDeliverOrder = get_group_options('add_deliver_order');
                $show_product_detail = get_group_options('show_product_detail');
                if (empty($addDeliverOrder)) {
                    $addDeliverOrder = 1;
                }
                if (empty($show_product_detail)) {
                    $show_product_detail = 0;
                }
                $shippingNote = implode(", ", $shippingNote);
                $rows_carrier = [
                    'shipping_address_text' => '<div><b>'. $shipping_address['name'] .'</b></div> <div>'. $shipping_address['phone'] .'</div> <div>'. $shipping_address['address'] .'</div>',
                    'carrier_id' => $carrier_id,
                    'shipping_status' => 1,
                    'shipping_address_id' => $shipping_address_id,
                    'is_cod' => $is_cod,
                    'pick_option' => $pick_option,
                    'is_freeship' => $is_freeship,
                    'group_id' => $group_id,
                    'shipping_option_id' => $shipping_option_id,
                    'shipping_note' => $shippingNote
                ];

                $rows_carrier['token'] = $token;
                $payLoads = [
                    'carrier_id' => $carrier_id,
                    'radio_shipping_address' => $radio_shipping_address,
                    'shipping_option_id' => $shipping_option_id,
                    'group_id' => $group_id,
                    'is_cod' => $is_cod,
                    'pick_option' => $pick_option,
                    'is_freeship' => $is_freeship,
                    'costs_config' => $costs_config,
                    'status_config' => $status_config,
                    'status_jobs_config' => $status_jobs_config,
                    'rows_carrier' => $rows_carrier,
                    'config' => $config,
                    'shipping_option' => $shipping_option,
                    'token' => $token,
                    'shipping_address' => $shipping_address,
                    'user_delivered' => $user_delivered,
                    'addDeliverOrder' => $addDeliverOrder,
                    'show_product_detail' => $show_product_detail,
                    'note_code' => Url::get('note_code'),
                    'viettel_service' => Url::get('viettel_service'),
                    'ems_service' => Url::get('ems_service'),
                    'ghtk_ServiceId' => $ghtk_ServiceId,
                    'vtpAddOns' => $addOns,
                    'emsAddOns' => $emsAddOns,
                    'ghtkAddOns' => $ghtkAddOns,
                    'is_fragile' => Url::get('is_fragile'),
                    'worker_account_type' => $worker_account_type,
                    'worker_account_id' => $worker_account_id,
                    'worker_group_id' => $worker_group_id
                ];

                try {
                    $ordersId = [];
                    foreach ($orders as $order_id => $order) {
                        $ordersId[] = $order_id;
                        $payLoads['order'] = $order;
                        $payLoads['order_id'] = $order_id;
                        // if (!AdminOrdersDB::checkExistJobs($order_id)) {
                        $jobId = DB::insert('jobs', [
                            'order_id' => $order_id,
                            'status_id' => $status_jobs_config['CHO_XU_LY'],
                            'attempts' => 0,
                            'created_at' => time(),
                            'payload' => json_encode($payLoads),
                            'group_id' => $group_id,
                            'carrier_id' => $carrier_id,
                            'shipping_option_id' => $shipping_option_id_mongo,
                        ]);
                        $payLoads['jobId'] = $jobId;
                        Amp\Loop::run(function () use ($payLoads) {
                            $beanstalk = new Amp\Beanstalk\BeanstalkClient("tcp://127.0.0.1:11300");
                            yield $beanstalk->use('deliver');

                            $payload = json_encode($payLoads);

                            $jobId = yield $beanstalk->put($payload);

                            // echo "Inserted job id: $jobId\n";

                            $beanstalk->quit();
                        });
                        // }
                    }
                    // mysqli_commit(DB::$db_connect_id);
                    $_SESSION['ids_shipping'] = $ordersId;
                    Url::redirect_url('index062019.php?page=admin_orders&cmd=shipping-processing');
                } catch (Exception $e) {
                    mysqli_rollback(DB::$db_connect_id);
                    echo "Lỗi! Vui lòng thử lại sau.";
                }
            }
        }
    }

    function draw()
    {
        $data = [];
        $group_id = AdminOrders::$group_id;
        $data['shop_id'] = $group_id;
        $data['HOST_API'] = HOST_API;
//        $group_id = 10051;
//        $shipping_costs = AdminOrdersConfig::get_list_shipping_costs();

        $shipping_costs = array();
        $shipping_options = array();
        // get from api
        $data_request = array("shop_id" => $group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $shipping_costs = $dataRes['data']['brand'];
            $shipping_options = $dataRes['data']['settings']['settings'];
        }
        //

        $data['shipping_costs'] = $shipping_costs;
        $default_shipping = '';
        if (count($shipping_options) > 0) {
            foreach($shipping_options as $key => $shipping) {
                if ($shipping['is_default']) {
                    $default_shipping = $shipping['carrier_id'];
                    break;
                }
            }
        }
        $data['default_shipping'] = $default_shipping;

        $shipping_address = array();
        // get from api
        $data_request = array('shop_id' => $group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/get-address', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $shipping_address = $dataRes['data'];
        }
        //

//        $shipping_address = AdminOrdersDB::getShippingAddress($group_id);
        $data['shipping_address'] = $shipping_address;
        $syncWareHouseEms = [];
        if (!empty($shipping_address)) {
            $shippingAddressIds = array_keys($shipping_address);
            $shippingAddressIds = implode(',', $shippingAddressIds);
            $syncWareHouseEms = DB::fetch_all("SELECT id, shipping_option_id, shipping_address_id FROM shipping_address_ems WHERE shipping_address_id IN ($shippingAddressIds)");
        }
        $data['syncWareHouseEms'] = $syncWareHouseEms;

//        $shipping_options = AdminOrdersDB::getShippingOptionsActive();
        $shipping_option_viettel = [];
        $shippingOptionActive = [];
        if (!empty($shipping_options)) {
            foreach ($shipping_options as $item) {
                if (!in_array($item['carrier_id'], $shippingOptionActive)) {
                    $shippingOptionActive[] = $item['carrier_id'];
                }
            }
        }

        $data['shippingOptionActive'] = $shippingOptionActive;
        $data['shipping_options'] = json_encode($shipping_options, JSON_UNESCAPED_UNICODE);
        $data['shipping_option_viettel'] = json_encode($shipping_option_viettel, JSON_UNESCAPED_UNICODE);
        // $data['shipping_option_viettel'] = '[]';
        $new_ids = "";
        if (Url::get("new_ids")) {
            $new_ids = Url::get("new_ids");
        }

        if (Url::get("ids")) {
            $new_ids = Url::get("ids");
        }

        $data['new_ids'] = $new_ids;
        $data['note_code_list'] = [
            'CHOXEMHANGKHONGTHU' => 'Cho xem hàng và không cho thử',
            'CHOTHUHANG' => 'Cho xem hàng và thử hàng',
            'KHONGCHOXEMHANG' => 'Không cho xem hàng'
        ];
        $is_cod_list = [
            1 => 'Cho phép thu tiền hộ',
            2 => 'Không thu tiền hộ'
        ];
        $data['is_cod_list'] = $is_cod_list;
        $is_freeship_list = [
            0 => 'Không miễn phí vận chuyển',
            1 => 'Miễn phí vận chuyển'
        ];
        $addDeliverOrder = get_group_options('add_deliver_order');
        if (empty($addDeliverOrder)) {
            $addDeliverOrder = 1;
        }
        if ((int) $addDeliverOrder === 2) {
            // mien phi van chuyen => default
            $is_freeship_list = [
                1 => 'Miễn phí vận chuyển',
                0 => 'Không miễn phí vận chuyển'
            ];
        }
        $data['is_freeship_list'] = $is_freeship_list;

        $pick_option_list = [
            'cod' => 'COD đến lấy hàng',
            'post' => 'Gửi hàng tại bưu cục'
        ];
        $data['pick_option_list'] = $pick_option_list;
        $system_group_id = DB::fetch('select system_group_id from `groups` where id='.$group_id,'system_group_id');
        $system_id = 1;
        if ($system_group_id and (IDStructure::is_child(DB::structure_id('groups_system',$system_group_id),DB::structure_id('groups_system',2)) or $system_group_id==2)){
            $system_id = 2;
        }
        $data['system_id'] = $system_id;

        $viettel_post_config = AdminOrdersConfig::viettel_post_config();
        $viettel_post_arr = ['' => 'Chọn dịch vụ'];
        foreach ($viettel_post_config as $value) {
            $viettel_post_arr[$value['SERVICE_CODE']] = $value['SERVICE_NAME'];
        }

        $data['viettel_service_list'] = $viettel_post_arr;

        $viettel_post_addon = AdminOrdersConfig::viettel_post_addon();
        $data['viettel_post_addon'] = $viettel_post_addon;

        $ems_addon = AdminOrdersConfig::ems_services_addon();
        $data['ems_addon'] = $ems_addon;

        $emsServiceList = ['' => 'Chọn dịch vụ'];
        $emsServiceConfig = AdminOrdersConfig::ems_services();
        foreach ($emsServiceConfig as $k => $value) {
            $emsServiceList[$k] = $value;
        }

        $data['ems_service_list'] = $emsServiceList;

        $this->parse_layout('multi_shipping', $data);
    }
}
