<?php

require_once ROOT_PATH . 'packages/vissale/modules/AdminOrders/forms/ImportExcelValidator.php';
require_once ROOT_PATH . 'packages/core/includes/common/ExcelHelper.php';
require_once ROOT_PATH . 'packages/vissale/modules/AdminOrders/forms/StockInvoice.php';
require_once ROOT_PATH . 'packages/core/includes/common/BiggameAPI.php';

class ImportExcelForm extends Form{
    const BLOCK_SIZE = 200;
    private $group_id = 0;
    private $userID = 0;
    private $statuses = [];
    private $shopUsers = [];
    public static $available_total = 0;
    protected $map;
    private $stockInvoiceInstance;
    private $createInvoiceSettings = [];

    function __construct(){
        Form::Form('ImportExcelForm');

        $this->group_id = AdminOrders::$group_id;
        $this->userID = get_user_id();
        $this->statuses = AdminOrders::$admin_group ? AdminOrdersDB::get_status() : AdminOrdersDB::get_status_from_roles($this->userID);

        $this->shopUsers = AdminOrdersDB::getShopUsers(AdminOrders::$group_id, ['users.id', 'users.username']);
        $this->shopUsers = array_column($this->shopUsers, 'id', 'username');

        $this->stockInvoiceInstance = new StockInvoice();

        $this->createInvoiceSettings = [
            'when_confirmed' => AdminOrders::$create_export_invoice_when_confirmed,
            'when_delivered' => AdminOrders::$create_export_invoice_when_delivered,
            'when_returned' => AdminOrders::$create_import_invoice_when_return,
        ];
    }

    function on_submit(){

        if(URL::getString('cancel-import')){
            $this->clearImport();
        }

        if(URL::getString('upload') && isset($_FILES['excel_file']['tmp_name'])){
            return $this->validateData($_FILES['excel_file']['tmp_name']);
        }

        if(URL::getString('import')){
            $this->doImport();
        }
    }

    /**
     * Does an import.
     */
    private function doImport()
    {
        $offset = URL::getUInt('offset');
        if($offset > Session::get('order_import_excel_pass')){
            return;
        }

        $rows = Session::get('order_import_excel_pass_rows');

        for ($i = $offset; $i < $offset + self::BLOCK_SIZE; $i++) {

            if(!isset($rows[$i])){
                break;
            }

            if(isset($rows[$i]['imported'])){
                continue;
            }

            $_SESSION['order_import_excel_pass_rows'][$i]['imported'] = $this->saveOrder($rows[$i], URL::getUInt('createStockInvoice'));

        }

        $i = 0;
        foreach ($_SESSION['order_import_excel_pass_rows'] as $row) {
            if(isset($row['imported'])){
                $i++;
            }
        }

        if($i === Session::get('order_import_excel_pass')){
            // gửi thông tin sang biggame
            BiggameAPI::instance()->sendListCarts($this->filterCarts($_SESSION['order_import_excel_pass_rows']));

            $this->clearImport();
        }

        RequestHandler::sendJsonSuccess([
            'total' => $i
        ]);

    }

    private function filterData($data, $fields){
        foreach ($fields as $field) {
            if(isset($data[$field])){
                $val =  $data[$field];
                $data[$field] = DataFilter::removeXSSinHtml($val);
            }
        }
        return $data;
    }

    /**
     * { function_description }
     */
    private function clearImport()
    {
        Session::delete('order_import_excel');
        Session::delete('order_import_excel_pass');
        Session::delete('order_import_excel_fail');
        Session::delete('order_import_excel_fail_rows');
        Session::delete('order_import_excel_pass_rows');
    }

    /**
     * Saves an order.
     *
     * @param      array      $data   The data
     *
     * @throws     Exception  (description)
     */
    private function saveOrder(array $data, int $createStockInvoice)
    {

        try{
            // var_dump($data);die;
            DB::$db_connect_id->begin_transaction();
            $filter_fields = ['code','customer_name','mobile', 'telco_code', 'note2', 'city','address', 'postal_code', 'shipping_note', 'note1', 'fb_page_id', 'fb_post_id', 'fb_customer_id', 'source_name', 'email'];
            $order = $this->filterData($data['order'], $filter_fields);
            $order['modified'] = date('Y-m-d H:i:s');
            $filter_fields = ['name', 'mobile', 'address', 'email'];
            $customer = $this->filterData($data['customer'], $filter_fields);
            $order['customer_id'] = $this->updateOrInsertCustomer($customer);
            if(!$order['customer_id']){
                throw new Exception('Lỗi cập nhật thông tin khách hàng.');
            }

            if(!$orderID = DB::insert('orders', $order)){
                throw new Exception('Thêm mới đơn hàng bị lỗi.');
            }

            $orderExtra = $data['orderExtra'];
            $orderExtra['order_id'] = $orderID;
            $orderExtra['allow_update_mobile'] = 0;
            if(!$orderExtraID = DB::insert('orders_extra', $orderExtra)){
                throw new Exception('Thêm mới thông tin bổ xung đơn hàng lỗi.');
            }

            if(is_array($data['products'])){
                $this->saveOrderProduct($orderID, $this->group_id, $data['products']);
            }

            $data['order']['id'] = $orderID;
            $this->log($data);

            if($createStockInvoice){
                $this->stockInvoiceInstance->create(
                    $data['order'],
                    $data['orderExtra'],
                    $data['products'],
                    $this->createInvoiceSettings
                );
            }

            DB::$db_connect_id->commit();

            return $orderID;
        } catch (Exception $e) {
            DB::$db_connect_id->rollback();
        }
    }

    /**
     * { function_description }
     *
     * @param      array  $data   The data
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function updateOrInsertCustomer(array $customer)
    {
        $savedCustomer = AdminOrdersDB::getCustomer($customer['mobile'], AdminOrders::$group_id);
        if (!$savedCustomer) {
            return DB::insert('crm_customer', $customer);
        }else{
            $data = array();
            if($savedCustomer['name'] != $customer['name']){
                $data['name'] = $customer['name'];
            }
            if($savedCustomer['zone_id'] != $customer['zone_id']){
                $data['zone_id'] = $customer['zone_id'];
            }
            if($savedCustomer['address'] != $customer['address']){
                $data['address'] = $customer['address'];
            }

            if(count($data)){
                DB::update('crm_customer',$data, 'id=' . $savedCustomer['id']);
            }
        }


        return $savedCustomer['id'];
    }

    /**
     * Saves an order product.
     *
     * @param      int    $orderID   The order id
     * @param      int    $groupID   The group id
     * @param      array  $products  The products
     */
    private function saveOrderProduct(int $orderID, int $groupID, array $products)
    {
        foreach ($products as $product) {
            $values = [
                'order_id' => $orderID,
                'group_id' => $groupID,
                'product_id' => $product['id'],
                'product_name' => DataFilter::removeXSSinHtml($product['name']),
                'product_price' => $product['price'],
                'qty' => $product['qty'],
                'weight' => $product['weight'],
                'created'=>date('Y-m-d H:i:s'),
                'modified'=>'0000-00-00 00:00:00',
                'warehouse_id'=>1
            ];
            if(!DB::insert('orders_products', $values)){
                throw new Exception("Thêm mới sản phẩm lỗi.");
            }
        }
    }

    /**
     * { function_description }
     *
     * @param      array  $data   The data
     */
    private function log(array $data)
    {
        if(Session::is_set('debuger_id')) return;

        $currentUserName = Session::get('user_id');
        $currentTime = date('d-m-Y H:i:s');
        $orderLogRows[] = "Tạo mới đơn hàng import excel.";
        $orderLogRows[] = "Trạng thái đơn hàng: <b>{$this->statuses[$data['order']['status_id']]['name']} </b>";
        $orderLogRows[] = "Người import: <b>{$currentUserName} </b>";
        $orderLogRows[] = "Thời gian import: <b>{$currentTime} </b>";

        if($data['order']['created'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['order']['user_created']){
            $orderLogRows[] = "Người tạo đơn: <b>" . array_search($data['order']['user_created'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian tạo đơn: <b>{$data['order']['created']} </b>";
        }

        if($data['order']['assigned'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['order']['user_assigned']){
            $orderLogRows[] = "Người được chia: <b>" . array_search($data['order']['user_assigned'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian được chia: <b>{$data['order']['assigned']} </b>";
        }

        if($data['order']['confirmed'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['order']['user_confirmed']){
            $orderLogRows[] = "Người xác nhận: <b>" . array_search($data['order']['user_confirmed'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian xác nhận: <b>{$data['order']['confirmed']} </b>";
        }

        if($data['orderExtra']['accounting_user_confirmed'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['orderExtra']['accounting_user_confirmed']){
            $orderLogRows[] = "Người đóng hàng <b>" . array_search($data['orderExtra']['accounting_user_confirmed'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian đóng hàng: <b>{$data['orderExtra']['accounting_confirmed']} </b>";
        }

        if($data['orderExtra']['update_successed_user'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['orderExtra']['update_successed_user']){
            $orderLogRows[] = "Người thành công: <b>" . array_search($data['orderExtra']['update_successed_user'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian thành công: <b>{$data['orderExtra']['update_successed_time']} </b>";
        }

        if($data['order']['user_delivered'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['order']['user_delivered']){
            $orderLogRows[] = "Người chuyển hàng: <b>" . array_search($data['order']['user_delivered'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian chuyển hàng: <b>{$data['order']['delivered']} </b>";
        }

        if($data['orderExtra']['update_returned_user'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['orderExtra']['update_returned_user']){
            $orderLogRows[] = "Người chuyển hoàn: <b>" . array_search($data['orderExtra']['update_returned_user'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian chuyển hoàn: <b>{$data['orderExtra']['update_returned_time']} </b>";
        }

        if($data['orderExtra']['update_returned_to_warehouse_user_id'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['orderExtra']['update_returned_to_warehouse_user_id']){
            $orderLogRows[] = "Người trả hàng: <b>" . array_search($data['orderExtra']['update_returned_to_warehouse_user_id'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian trả hàng: <b>{$data['orderExtra']['update_returned_to_warehouse_time']} </b>";
        }

        if($data['orderExtra']['update_paid_user'] != ImportExcelValidator::DEFAULT_DB_DATE_TIME && $data['orderExtra']['update_paid_user']){
            $orderLogRows[] = "Người thu tiền: <b>" . array_search($data['orderExtra']['update_paid_user'], $this->shopUsers) ." </b>";
            $orderLogRows[] = "Thời gian thu tiền: <b>{$data['orderExtra']['update_paid_time']} </b>";
        }

        $orderLogRows[] = "Tên khách hàng: <b>{$data['order']['customer_name']} </b>";
        $orderLogRows[] = "Sđt khách hàng: <b>{$data['order']['mobile']} </b>";

        if($data['order']['total_price']){
            $orderLogRows[] = "Tổng tiền: <b>{$data['order']['total_price']}</b>";
        }

        if($data['order']['discount_price']){
            $orderLogRows[] = "Giảm giá: <b>{$data['order']['discount_price']}</b>";
        }

        if($data['order']['note2']){
            $orderLogRows[] = "Ghi chú 2: <b>{$data['order']['note2']}</b>";
        }

        $orderLog = [
            'order_id' =>  DataFilter::removeXSSinHtml($data['order']['id']),
            'data' => DataFilter::removeXSSinHtml(implode('<br>', $orderLogRows)),
            'before_order_status_id' => 0,
            'before_order_status' => '',
            'order_status_id' => 0,
            'order_status' => '',
            'user_created_name' => $currentUserName,
            'user_created' => $this->userID,
            'created' => date('Y-m-d H:i:s')
        ];

        $productLogRow = [];
        foreach($data['products'] as $product){
            $productLogRow[] = sprintf(
                'Thêm sản phẩm <b>[Mã: %s] %s</b>, giá: <b>%s</b>, số lượng: <b>%s</b>',
                $product['code'], $product['name'], $product['price'], $product['qty']
            );
        }
        $productLog = [
            'order_id' =>  DataFilter::removeXSSinHtml($data['order']['id']),
            'data' =>  DataFilter::removeXSSinHtml(implode('<br>', $productLogRow)),
            'before_order_status_id' => 0,
            'before_order_status' => '',
            'order_status_id' => 0,
            'order_status' => '',
            'user_created_name' => $currentUserName,
            'user_created' => $this->userID,
            'created' => date('Y-m-d H:i:s')
        ];

        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            include_once ROOT_PATH.'packages/vissale/lib/php/log.php';
            storeLog($data['order']['id'], 'orders', json_encode($orderLog));
            storeLog($data['order']['id'], 'orders', json_encode($productLog));
        }else{
            DB::insert('order_revisions', $orderLog);
            DB::insert('order_revisions', $productLog);
        }
    }

    function getOrderTypes()
    {
        return [
            1 => 'SALE (Số mới)',
            2 => 'CSKH',
            9 => 'Tối ưu',
            3 => 'Đặt lại',
            4 => 'Đặt lại lần 1',
            5 => 'Đặt lại lần 2',
            6 => 'Đặt lại lần 3',
            7 => 'Đặt lại lần 4',
            8 => 'Đặt lại lần 5',
            10 => 'Đặt lại lần 6',
            11 => 'Đặt lại lần 7',
            12 => 'Đặt lại lần 8',
            13 => 'Đặt lại lần 9',
            14 => 'Đặt lại lần 10',
            15 => 'Đặt lại lần 11',
            16 => 'Đặt lại lần 12',
            17 => 'Đặt lại lần 13',
            18 => 'Đặt lại lần 14',
            19 => 'Đặt lại lần 15',
            20 => 'Đặt lại lần 16',
            21 => 'Đặt lại lần 17',
            22 => 'Đặt lại lần 18',
            23 => 'Đặt lại lần 19',
            24 => 'Đặt lại lần 20',
            25 => 'Đặt lại trên lần 20',
            26 => 'Cross sale',
            27 => 'Afiliate'
        ];
    }

    /**
     * Gets all products.
     *
     * @return     <type>  All products.
     */
    private function getAllProducts()
    {
        return DB::fetch_all_key('SELECT id,name,price,weight,code FROM products WHERE  group_id = "' . AdminOrders::$group_id . '" AND del = 0 OR del IS NULL', 'code');
    }

    /**
     * Gets all cities.
     *
     * @return     <type>  All cities.
     */
    private function getAllCities()
    {
        $cities = DB::fetch_all_array('SELECT province_id,province_name FROM zone_provinces_v2');
        $cities = array_map(function($city){
            return [
                'province_id' => $city['province_id'],
                'province_name' => mb_strtolower($city['province_name']),
            ];
        }, $cities);

        return array_column($cities, 'province_id', 'province_name');
    }

    /**
     * Gets all sources.
     *
     * @return     <type>  All sources.
     */
    private function getAllSources()
    {
        if (isObd()) {
            $sources = DB::fetch_all_array('SELECT name, id FROM order_source WHERE  `ref_id` = 0 AND `group_id`=0');
        } else {
            $sources = DB::fetch_all_array('SELECT name, id FROM order_source WHERE group_id = 0 OR group_id=' . AdminOrders::$group_id);
        }//end if

        $sources = array_map(function($source){
            return ['id' => $source['id'], 'name' => mb_strtolower($source['name'])];
        }, $sources);

        return array_column($sources, 'id', 'name');
    }

    /**
     * Gets all bundles.
     *
     * @return     <type>  All bundles.
     */
    private function getAllBundles()
    {
        if(isObd()) {
            $bundles = DB::fetch_all_array('SELECT `name`, `id` FROM `bundles` WHERE (`group_id` = 0 AND `standardized` = 1 AND `ref_id` = 0) OR `group_id` = ' . AdminOrders::$group_id);
        } else {
            $bundles = DB::fetch_all_array('select name, id FROM bundles WHERE group_id=' . AdminOrders::$group_id);
        }//end if

        $bundles = array_map(function($bundle){
            return ['id' => $bundle['id'], 'name' => mb_strtolower($bundle['name'])];
        }, $bundles);

        return array_column($bundles, 'id', 'name');
    }

    /**
     * { function_description }
     *
     * @param      string  $excelFilePath  The excel file path
     */
    private function validateData(string $excelFilePath)
    {
        $this->clearImport();

        if(!$toStatusID = URL::getUInt('order_status_id')){
            return URL::js_redirect(true, 'Trạng thái đơn hàng không hợp lệ',['cmd' => 'import_excel']);
        }

        $rows = $this->readExcelFile($excelFilePath);
        $cities = $this->getAllCities();
        $sources = $this->getAllSources();
        $bundles = $this->getAllBundles();
        $types = $this->getOrderTypes();
        $products = $this->getAllProducts();

        $passCount = 0;
        $errorCount = 0;
        $errorRows = [];
        $errorColumns = [];
        $passRows = [];

        $validator = ImportExcelValidator::new()
            ->setStatusID($toStatusID)
            ->setAssignedUsername(URL::getString('order_account_id'))
            ->setGroupID($this->group_id)
            ->setCurrentUserID($this->userID)
            ->setStatuses($this->statuses)
            ->setUsers($this->shopUsers)
            ->setCities($cities)
            ->setSources($sources)
            ->setBundles($bundles)
            ->setMasterGroupID(intval(Session::get('master_group_id')))
            ->setProducts($products)
            ->setTypes($types);


        foreach($rows as $key=>$row){
            if(!$key){
                $errorRows[] = $row;
                continue;
            }

            if($row[ImportExcelValidator::CONFIRM_CODE] == 'x'){
                continue;
            }

            $validator = $validator->prepareData($row)
                ->execute();

            if($_errorRows = $validator->getErrorColumns()){
                $errorRows[$key] = $row;
                $errorColumns[$key] = $_errorRows;
                $errorCount++;
                continue;
            }

            $passRows[] = [
                'customer' => $validator->getCustomerFields(),
                'order' => $validator->getOrderField(),
                'statuses' => $validator->getStatuses(),
                'orderExtra' => $validator->getOrderExtraField(),
                'products' => $validator->getProductsField(),
            ];

            $passCount++;
        }

        Session::set('status_id_import_excel', $toStatusID);
        Session::set('order_import_excel', count($rows) - 1);
        Session::set('order_import_excel_pass', $passCount);
        Session::set('order_import_excel_fail', $errorCount);
        Session::set('order_import_excel_fail_rows', $errorRows);
        Session::set('order_import_excel_fail_columns', $errorColumns);
        Session::set('order_import_excel_pass_rows', $passRows);

        Url::redirect_current(array('cmd'=>'import_excel','v'));
    }

    /**
     * Reads an excel file.
     *
     * @param      string  $excelFilePath  The excel file path
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function readExcelFile(string $excelFilePath)
    {
        try{
            $rows = [];
            foreach (ExcelHelper::parse($excelFilePath, 1, ImportExcelValidator::NUM_FIELDS) as $_rows) {
                if($rows){
                    array_push($rows, ...$_rows);
                    continue;
                }

                $isValid = array_reduce($_rows[0], function($res, $col){
                    $res += empty($col) ? 0 : 1;
                    return $res;
                }, 0);

                if($isValid != ImportExcelValidator::NUM_FIELDS){
                    return URL::js_redirect(true, 'Cấu trúc File không hợp lệ',['cmd' => 'import_excel']);
                }

                array_push($rows, ...$_rows);
            }
        } catch(PhpOffice\PhpSpreadsheet\Exception $e){
            return URL::js_redirect(true, 'Định dạng File không hợp lệ, vui lòng kiểm tra lại',['cmd' => 'import_excel']);
        }

        return $rows;
    }

    function draw(){
        $this->map = array();
        $this->map['total'] = 0;
        $this->map['items'] = array();
        $status_arr = AdminOrdersDB::get_status();
        $this->map['order_status_id_list'] = [''=>'Chọn trạng thái'] + MiString::get_list($status_arr );
        $this->map['order_account_id_list'] = array(''=>'Chọn nhân viên gán đơn') + MiString::get_list(AdminOrdersDB::get_users('GANDON'));
        $this->map['confirmed_account_id_list'] = array(''=>'Chọn nhân viên xác nhận') + MiString::get_list(AdminOrdersDB::get_users());

        if(isset($_SESSION['exel_items'])){
            $this->map['total'] = !empty($_SESSION['exel_items'])?sizeof($_SESSION['exel_items']):0;
            $this->map['total'] = $this->map['total'] - 1;
        }

        $layout = 'import_excel';
        $this->map['available_total'] = ImportExcelForm::$available_total;
        $this->map['BLOCK_SIZE'] = self::BLOCK_SIZE;
        $this->map['invoiceSettings'] = $this->createInvoiceSettings;
        $this->parse_layout($layout,$this->map);
    }

    /**
     * { function_description }
     *
     * @param      array   $rows   The rows
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function filterCarts(array $rows)
    {
        return from($rows)

            // Lọc các row có orderID(insert thành công) và đã qua xác nhận
            ->filter(function($row){
                return !empty($row['imported']) && !empty($row['order']['confirmed']) && !empty($row['order']['user_confirmed']);
            })

            // Lấy groupName và tên người xác nhận
            ->pipe(function($rows){
                if(!$rows){
                    return $rows;
                }

                $groupID = $rows[0]['order']['group_id'] ?? 0;
                $userIDs = from($rows)->map(function($row){ return $row['order']['user_confirmed']; })->unique();

                $sql = 'SELECT `name` FROM `groups` WHERE `id` = ' . $groupID;
                $groupName = DB::fetch($sql, 'name');

                $sql = 'SELECT `id`, `name` FROM `users` WHERE `id` IN (' . $userIDs->join(',') . ')';
                $users = DB::fetch_all_column($sql, 'name', 'id');

                return [$groupName, $users, $rows];
            })

            // Xây dựng danh sách
            ->pipe(function($results){
                if(!$results){
                    return $results;
                }

                [$groupName, $users, $rows] = $results;

                return array_map(function($row) use($users, $groupName) {
                    $statuses = DB::fetch('select id,no_revenue,level from statuses where id='.(int)$row['order']['status_id']);
                    return [
                        "id"                    => $row['imported'],
                        "confirmed"             => $row['order']['confirmed'],
                        "user_confirmed"        => $row['order']['user_confirmed'],
                        "user_confirmed_name"   => $users[$row['order']['user_confirmed']] ?? '',
                        "accounting_confirmed"  => $row['orderExtra']['accounting_confirmed'],
                        "total_price"           => $row['order']['total_price'],
                        "group_id"              => $row['order']['group_id'],
                        "group_name"            => $groupName,
                        "status_id"             => $row['order']['status_id'],
                        "no_revenue"            => $statuses['no_revenue']??1,
                        "level"                 => $statuses['level']??0
                    ];
                }, $rows);
            })
            ->toArray();
    }
}
?>
