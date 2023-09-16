<?php

class StockInvoice
{   
    private $groupID;
    private $warehouseID;
    private $shopUsers;

    const CONFIRMED_STATUS_IDS = [
        OrderStatus::XAC_NHAN,
        OrderStatus::KE_TOAN,
        OrderStatus::CHUYEN_HOAN,
        OrderStatus::CHUYEN_HANG,
        OrderStatus::THANH_CONG,
        OrderStatus::DA_THU_TIEN,
    ];

    const DELIVERED_STATUS_IDS = [
        OrderStatus::CHUYEN_HANG,
        OrderStatus::CHUYEN_HOAN,
        OrderStatus::THANH_CONG,
        OrderStatus::DA_THU_TIEN,
    ];

    const RETURNED_STATUS_IDS = [
        OrderStatus::TRA_VE_KHO
    ];

    /**
     * Constructs a new instance.
     *
     * @param      bool|int  $groupID      The group id
     * @param      bool|int  $warehouseID  The warehouse id
     */
    public function __construct(int $groupID = null, int $warehouseID = null)
    {
        $this->groupID = $groupID ? $groupID : Session::get('group_id');
        $this->warehouseID = $warehouseID ? $warehouseID : get_default_warehouse($this->groupID);
        $this->shopUsers = AdminOrdersDB::getShopUsers($this->groupID, ['users.id', 'users.username', 'users.name']);
    }

    /**
     * Tạo phiếu kho
     *
     * @param      array  $order         The order
     * @param      array  $orderExtra    The order extra
     * @param      array  $products      The products
     * @param      array  $shopSettings  The shop settings
     */
    public function create(array $order, array $orderExtra, array $products, array $shopSettings)
    {   
        // Trường hợp phiếu xuất khi đơn qua xác nhận
        if(
            isset($shopSettings['when_confirmed']) 
            && $shopSettings['when_confirmed']
            && in_array($order['status_id'], self::CONFIRMED_STATUS_IDS)
        ){
            $this->createExport($order, $orderExtra, $products);
        }

        // Trường hợp phiếu xuất khi đơn qua chuyển hàng
        if(
            isset($shopSettings['when_delivered']) 
            && $shopSettings['when_delivered'] 
            && in_array($order['status_id'], self::DELIVERED_STATUS_IDS)
        ){
            $this->createExport($order, $orderExtra, $products);
        }

        // Trường hợp phiếu nhập khi đơn trả về kho
        if(
            isset($shopSettings['when_returned']) 
            && $shopSettings['when_returned']
            && in_array($order['status_id'], self::RETURNED_STATUS_IDS)
        ){
            $this->createImport($order, $orderExtra, $products);
        }
    } 

    /**
     * Creates an export.
     *
     * @param      array  $order       The order
     * @param      array  $orderExtra  The order extra
     * @param      array  $products    The products
     */
    private function createExport(array $order, array $orderExtra, array $products)
    {   
        $invoiceFields = $this->makeExportInvoiceFields($order, ...$this->getUserDateField($order, $orderExtra));
        $type = "EXPORT";
        $this->execute($order, $products, $invoiceFields, $type);
    }

    /**
     * Creates an import.
     *
     * @param      array  $order       The order
     * @param      array  $orderExtra  The order extra
     * @param      array  $products    The products
     */
    private function createImport(array $order, array $orderExtra, array $products)
    {   
        $invoiceFields = $this->makeImportInvoiceFields($order, ...$this->getUserDateField($order, $orderExtra));
        $type = "IMPORT";
        $this->execute($order, $products, $invoiceFields, $type);
    }

    /**
     * Makes export invoice fields.
     *
     * @param      array   $order        The order
     * @param      string  $userID       The user id
     * @param      string  $deliverName  The deliver name
     * @param      string  $createDate   The create date
     *
     * @return     array   
     */
    private function makeExportInvoiceFields(array $order, string $userID, string $deliverName, string $createDate)
    {      
        return [
            'bill_number'=> $this->createBillNumber('EXPORT'),
            'type'=>'EXPORT',
            'deliver_name'=> $deliverName,
            'note'=> '',
            'receiver_name'=> '',
            'total_amount'=> 0,
            'create_date'=> $createDate,
            'order_id'=> $order['id'],
            'note'=> 'Phiếu import excel từ 1 đơn hàng ' . $order['id'],
            'group_id'=>$this->groupID,
            'user_id'=> $userID,
            'time'=>time()
        ];
    }

    /**
     * Makes import invoice fields.
     *
     * @param      array   $order        The order
     * @param      string  $userID       The user id
     * @param      string  $deliverName  The deliver name
     * @param      string  $createDate   The create date
     *
     * @return     array   
     */
    private function makeImportInvoiceFields(array $order, string $userID, string $deliverName, string $createDate)
    {   
        return [
            'bill_number'=> $this->createBillNumber('IMPORT'),
            'type'=>'IMPORT',
            'deliver_name'=> $deliverName,
            'note'=> '',
            'receiver_name'=> '',
            'total_amount'=> 0,
            'create_date'=> $createDate,
            'order_id'=> $order['id'],
            'note'=>'Phiếu import excel từ đơn hàng đã trả hàng về kho (Mã: ' . $order['id'] .')',
            'group_id'=>$this->groupID,
            'user_id'=> $userID,
            'time'=>time()
        ];
    }

    /**
     * Creates an invoice.
     *
     * @param      array   $invoiceFields  The invoice fields
     *
     * @return     int 
     */
    private function createInvoice(array $invoiceFields)
    {
        return DB::insert('qlbh_stock_invoice', $invoiceFields);
    }

    /**
     * Creates a bill number.
     *
     * @return     <type>  
     */
    private function createBillNumber(string $type)
    {
        return DB::fetch('SELECT id,bill_number FROM qlbh_stock_invoice where type="'.$type.'"  and qlbh_stock_invoice.group_id='.$this->groupID.' ORDER BY bill_number DESC', 'bill_number') + 1;
    }

    /**
     * Thực thi tạo phiếu kho 
     *
     * @param      array      $order          The order
     * @param      array      $products       The products
     * @param      array      $invoiceFields  The invoice fields
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     
     */
    public function execute(array $order, array $products, array $invoiceFields, string $type)
    {
        if(!$order || !$this->warehouseID || !$products){
            return;
        }

        $orderID = $order['id'];
        
        //Tạo phiếu xuất kho
        $invoiceID = $this->createInvoice($invoiceFields);

        // Gắn các sản phẩm của đơn hàng vào phiếu
        $invoiceProducts = $this->prepareProducts($products, $invoiceID, $this->warehouseID);
        if(!$this->insertProductList($invoiceProducts)){
            throw new Exception('Insert Product Invoice Failed!');
        }

        // Tính tổng tền sản phẩm và cập nhật vào tổng tiền phiếu
        $totalAmount = array_reduce($invoiceProducts, function($totalAmount, $product){
            return $totalAmount += $product['price'] * $product['quantity'];
        }, 0);
        DB::update('qlbh_stock_invoice', ['total_amount'=>$totalAmount],'id='.$invoiceID);
        $data = '';
        if($type == "EXPORT"){
            $data = "Import excel: Tạo phiếu xuất kho tự động. Mã phiếu ".'PX'.$this->createBillNumber("EXPORT");
        } else {
            $data = "Import excel: Tạo phiếu nhập kho tự động. Mã phiếu ".'PN'.$this->createBillNumber("IMPORT");
        }
        if($data){
            AdminOrdersDB::update_revision($orderID,false,false,$data);
        }
        return $invoiceID;
    }

    /**
     * Chuẩn hóa danh sách sản phẩm
     *
     * @param      array   $products     The products
     * @param      int     $invoiceID    The invoice id
     * @param      int     $warehouseID  The warehouse id
     *
     * @return     array[][]  
     */
    private function prepareProducts(array $products, int $invoiceID, int $warehouseID)
    {   
        return array_map(function($product) use($invoiceID, $warehouseID) {
            return [
                'unit_id' => 0,
                'price' => $product['price'],
                'warehouse_id' => $warehouseID,
                'quantity' => $product['qty'],
                'product_code' => $product['code'],
                'product_name' => $product['name'],
                'product_id' => $product['id'],
                'invoice_id' => $invoiceID
            ];
        }, $products);
    }

    /**
     * { function_description }
     *
     * @param      array   $products  The products
     *
     * @return     bool  
     */
    private function insertProductList(array $products)
    {
        $fmt = 'INSERT INTO qlbh_stock_invoice_detail %s VALUES %s';
        $fieldNames = "(`" . implode("`,`", array_keys($products[0])) . "`)";
        $fieldValues = [];
        foreach ($products as $key => $product) {
            $values = [];
            foreach ($product as $fieldName => $fieldValue) {
                $values[] = DB::escape($fieldValue);
            }

            $fieldValues[$key] = "('" . implode("','", $values) . "')";
        }

        $sql = sprintf($fmt, $fieldNames, implode(',', $fieldValues));

        return DB::query($sql);

    }

    /**
     * Gets the user date field.
     *
     * @param      array  $order       The order
     * @param      array  $orderExtra  The order extra
     */
    private function getUserDateField(array $order, array $orderExtra)
    {   
        $userID = $deliverName = $createDate = '';
        switch ($order['status_id']) {
            case OrderStatus::XAC_NHAN:
                $createDate = $order['confirmed'];
                if($order['user_confirmed']){
                    $userID = $this->shopUsers[$order['user_confirmed']]['username'];
                    $deliverName = $this->shopUsers[$order['user_confirmed']]['name'];
                }
                break;

            case OrderStatus::KE_TOAN:
                $createDate = $orderExtra['accounting_confirmed'];
                if($orderExtra['accounting_user_confirmed']){
                    $userID = $this->shopUsers[$orderExtra['accounting_user_confirmed']]['username'];
                    $deliverName = $this->shopUsers[$orderExtra['accounting_user_confirmed']]['name'];
                }
                break;

            case OrderStatus::CHUYEN_HANG:
                $createDate = $order['delivered'];
                if($order['user_delivered']){
                    $userID = $this->shopUsers[$order['user_delivered']]['username'];
                    $deliverName = $this->shopUsers[$order['user_delivered']]['name'];
                }
                break;

            case OrderStatus::CHUYEN_HOAN:
                $createDate = $orderExtra['update_returned_time'];
                if($orderExtra['update_returned_user']){
                    $userID = $this->shopUsers[$orderExtra['update_returned_user']]['username'];
                    $deliverName = $this->shopUsers[$orderExtra['update_returned_user']]['name'];
                }
                break;

            case OrderStatus::THANH_CONG:
                $createDate = $orderExtra['update_successed_time'];
                if($orderExtra['update_successed_user']){
                    $userID = $this->shopUsers[$orderExtra['update_successed_user']]['username'];
                    $deliverName = $this->shopUsers[$orderExtra['update_successed_user']]['name'];
                }
                break;

            case OrderStatus::DA_THU_TIEN:
                $createDate = $orderExtra['update_paid_time'];
                if($orderExtra['update_paid_user']){
                    $userID = $this->shopUsers[$orderExtra['update_paid_user']]['username'];
                    $deliverName = $this->shopUsers[$orderExtra['update_paid_user']]['name'];
                }
                break;

            case OrderStatus::TRA_VE_KHO:
                $createDate = $orderExtra['update_returned_to_warehouse_time'];
                if($orderExtra['update_returned_to_warehouse_user_id']){
                    $userID = $this->shopUsers[$orderExtra['update_returned_to_warehouse_user_id']]['username'];
                    $deliverName = $this->shopUsers[$orderExtra['update_returned_to_warehouse_user_id']]['name'];
                }
                break;
        }

        return [$userID, $deliverName, $createDate];
    }
}