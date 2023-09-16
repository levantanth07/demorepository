<?php

class ShopShippingAddressForm extends Form
{

    function __construct()
    {
        Form::Form('ShopShippingAddressForm');

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'get_zones' && URL::get('zone_id') && URL::get('type')) {
            $zone_id = URL::get('zone_id');
            $type = URL::get('type');
            $zones = [];
            if ($type == 'district') {
                $data_string = array('p_id' => $zone_id);
                $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/district', $data_string);
                if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                    $zones = $dataRes['data'];
                }
//                $zones = ShopShippingAddressDB::getDistrictsByProvinceId($zone_id);
            } else if ($type == 'ward') {
                $data_string = array('d_id' => $zone_id);
                $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/ward', $data_string);
                if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                    $zones = $dataRes['data'];
                }
//                $zones = ShopShippingAddressDB::getWardsByDistrictId($zone_id);
            }

            echo json_encode($zones, JSON_UNESCAPED_UNICODE); die();
        }

        if (URL::get('cmd') == 'save_address' && !empty($_POST)) {
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            $row = [];
            $data['success'] = false;
            $except = ['form_block_id', 'shipping_options_id'];
            foreach ($_POST as $key => $value) {
                if (!in_array($key, $except)) {
                    $row[$key] = $value;
                }
            }

            $group_id = Session::get('group_id');
            $row['shop_id'] = $group_id;
            $row['is_default'] = !empty($_POST['is_default']) ? 1 : 0;

            // create address from api
            DataFilter::removeHtmlTags($row);
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/create', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }
            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'set_default_address' && Url::post('id')) {
            $id = Url::post('id');
            $data['success'] = false;
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            // get address from api
            $item = null;
            $data_string = array('id' => $id);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/set-default', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }
            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'delete_address' && Url::post('id')) {
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            $id = Url::post('id');
            $data['success'] = false;

            // get address from api
            $item = null;
            $data_string = array('id' => $id);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/delete', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'edit_address' && Url::get('id')) {
            $id = Url::get('id');
            $data = [];
            $data['html'] = '';

            if (ShopShippingAddressDB::checkPermission() == false) {
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data); die();
            }
            try {
//                $item = ShopShippingAddressDB::getShippingAddressById($id);

                // get address from api
                $item = null;
                $data_string = array('id' => $id);
                $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/get', $data_string);
                if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                    $item = $dataRes['data'];
                }

                $data['html'] = $this->build_modal_edit_address($item);
            } catch (Exception $e) {

            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'sync_ghn' && Url::get('id')) {
            $data['success'] = false;
            $row['id'] = Url::get('id');
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            // create address from api
            DataFilter::removeHtmlTags($row);
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/sync-ghn', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'logistic_get_otp_ghn' && Url::get('phone')) {
            $data['success'] = false;
            $row['phone'] = Url::get('phone');
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            // create address from api
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/get-otp-ghn', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }
            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'logistic_sync_otp_ghn' && Url::get('otp')) {
            $data['success'] = false;
            $row['otp'] = Url::get('otp');
            $row['id'] = Url::get('id');
            $row['phone'] = Url::get('phone');
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            // create address from api
            DataFilter::removeHtmlTags($row);
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/sync-otp-ghn', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }
            echo json_encode($data); die();
        }

        if (URL::get('action') == 'ajax' && URL::get('cmd') == 'sync_ems' && Url::get('id')) {
            $data['success'] = false;
            $row['id'] = Url::get('id');
            if (ShopShippingAddressDB::checkPermission() == false) {
                $data = [];
                $data['success'] = false;
                $data['messages'] = 'Bạn không có quyền thực hiện thao tác này';
                echo json_encode($data);
                die();
            }
            // create address from api
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/sync-ems', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }

            echo json_encode($data); die();
        }

        if (URL::get('cmd') == 'save_edit_address' && Url::post('id')) {
            $id = Url::post('id');
            $group_id = Session::get('group_id');
            $row['shop_id'] = $group_id;
            $data['success'] = false;

            $except = ['form_block_id', 'shipping_options_id'];
            foreach ($_POST as $key => $value) {
                if (!in_array($key, $except)) {
                    $row[$key] = $value;
                }
            }

            $row['is_default'] = !empty($_POST['is_default']) ? 1 : 0;

            // create address from api
            DataFilter::removeHtmlTags($row);
            $data_string = $row;
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/update', $data_string);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $data['success'] = true;
            }

            echo json_encode($data); die();
        }
    }

    function draw()
    {
        // System::debug(Session::get('group_id'));
        if (ShopShippingAddressDB::checkPermission() == false) {
            Url::js_redirect(false,'Bạn không có quyền truy cập!');
        }
        $group_id = Session::get('group_id');
        $this->map = [];
        $this->map['title'] = 'Địa chỉ lấy hàng';
        $zone_id_list = ['' => 'Tỉnh/Thành phố (*)'];
        // get transport setting from api
        $provinces = array();
        $data_string = array();
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/province', $data_string);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $provinces = $dataRes['data'];
            if (count($provinces) > 0) {
                foreach ($provinces as $item) {
                    $zone_id_list[$item['id']] = $item['name'];
                }
            }
        }

        // get transport setting from api
        $items_address = array();
        $data_string = array('shop_id' => $group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/get-address', $data_string);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $items_address = $dataRes['data'];
        }

        $this->map['zone_id_list'] = $zone_id_list;
        $group_info = ShopShippingAddressDB::getGroupInfo($group_id);
        $this->map['shop_name'] = $group_info['name'];
        $this->map['shop_phone'] = $group_info['phone'];
//        $items_address = ShopShippingAddressDB::getShippingAddress($group_id);
        $shippingOptions = ShopShippingAddressDB::getShippingOptionsEms();
        $this->map['shippingOptions'] = $shippingOptions;
        $this->map['items_address'] = $items_address;

        $this->parse_layout('list', $this->map);
    }

    function build_modal_edit_address($item)
    {
//        $zones_originals = ShopShippingAddressDB::getProvinces();
        if (ShopShippingAddressDB::checkPermission() == false) {
            Url::js_redirect(false,'Bạn không có quyền truy cập!');
        }
        $zones_districts = array();
        $zones_originals = array();
        $zones_wards = array();
        $data_string = array();
        // get provinces from api
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/province', $data_string);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $zones_originals = $dataRes['data'];
        }
        // get provinces from api
        $data_string = array('p_id' => $item['province_v2_id']);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/district', $data_string);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $zones_districts = $dataRes['data'];
        }
//        $zones_districts = ShopShippingAddressDB::getDistrictsByProvinceId($item['zone_id']);


        $data_string = array('d_id' => $item['district_v2_id']);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/address/ward', $data_string);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $zones_wards = $dataRes['data'];
        }
//        $zones_wards = ShopShippingAddressDB::getWardsByDistrictId($item['district_id']);
        $checked_default = ($item['is_default'] == 1) ? 'checked' : "";
        $wareHouseId = '';

        $zoneWardsHtml = '';
        if (!empty($zones_wards)) {
            foreach ($zones_wards as $zone) {
                $selected = ($zone['id'] == $item['ward_v2_id']) ? 'selected' : '';
                $zoneWardsHtml .= '<option value="'. $zone['id'] .'" '. $selected .'>'. $zone['name'] .'</option>';
            }
        }

        $zoneDistrictsHtml = '';
        if (!empty($zones_districts)) {
            foreach ($zones_districts as $zone) {
                $selected = ($zone['id'] == $item['district_v2_id']) ? 'selected' : '';
                $zoneDistrictsHtml .= '<option value="'. $zone['id'] .'" '. $selected .'>'. $zone['name'] .'</option>';
            }
        }

        $zoneProvincesHtml = '';
        if (!empty($zones_originals)) {
            foreach ($zones_originals as $zone) {
                $selected = ($zone['id'] == $item['province_v2_id']) ? 'selected' : '';
                $zoneProvincesHtml .= '<option value="'. $zone['id'] .'" '. $selected .'>'. $zone['name'] .'</option>';
            }
        }

        $shippingOptions = ShopShippingAddressDB::getShippingOptionsEmsAll();
        $syncWarehouseEms = "";
//        if (!empty($shippingOptions)) {
//            $syncWarehouseEms = '
//                <div class="panel panel-default">
//                    <div class="panel-heading">
//                        ĐỒNG BỘ VỚI KHO HÀNG BÊN EMS
//                    </div>
//                    <div class="panel-body">
//            ';
//            foreach ($shippingOptions as $optionItem) {
//                $disabled = ""; $lblSync = "";
//                if ($optionItem['shipping_address_id'] == $item['id']) {
//                    $disabled = 'disabled';
//                    $lblSync = '<span class="label label-warning">Đã đồng bộ</span>';
//                }
//                $syncWarehouseEms .= '
//                    <div class="checkbox">
//                        <label>
//                            <input type="checkbox" name="shipping_options_id[]" value="'.$optionItem['id'].'"
//                             '. $disabled .'>
//                                '. $optionItem['name'] . '('. $optionItem['token'] .') '. $lblSync .'
//                        </label>
//                    </div>
//                ';
//            }
//            $syncWarehouseEms .= '
//                    </div>
//                </div>
//            ';
//        }

        $html = '
            <div class="modal fade" id="modal-edit-address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <form action="" id="form-edit-address" name="form_add_address">
                        <input type="hidden" id="address_id" name="id" value="'. $item["id"] .'">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Sửa Địa Chỉ</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Tên shop <span class="text-red">(*)</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Tên (*)" value="'. $item['name'] .'" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Số điện thoại <span class="text-red">(*)</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại (*)" value="'. $item['phone'] .'" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Tỉnh/Thành phố <span class="text-red">(*)</span></label>
                                    <select name="zone_id" id="zone_id" class="form-control zone_id zone_ajax" data-dependent=".district_id" data-option="Quận/Huyện (*)" required>
                                        <option value="">Tỉnh/Thành phố (*)</option>
                                        '. $zoneProvincesHtml .'
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Quận/Huyện <span class="text-red">(*)</span></label>
                                    <select name="district_id" id="district_id" class="form-control district_id zone_ajax" data-dependent=".ward_id" data-option="Phường/Xã (*)" required>
                                        <option value="">Quận/Huyện (*)</option>
                                        '. $zoneDistrictsHtml .'
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Phường/Xã <span class="text-red">(*)</span></label>
                                    <select name="ward_id" id="ward_id" class="form-control ward_id" required>
                                        <option value="">Phường/Xã (*)</option>
                                        '. $zoneWardsHtml .'
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">Địa chỉ chi tiết <span class="text-red">(*)</span></label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ chi tiết (*)" value="'. $item['address'] .'" required>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label for="is_default">
                                            <input type="checkbox" class="" id="is_default" name="is_default" '. $checked_default .' value="1"> Đặt làm mặc định
                                        </label>
                                    </div>
                                </div>
                                '. $syncWarehouseEms .'
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                                <button type="submit" class="btn btn-primary">Hoàn thành</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        ';

        return $html;
    }
}
