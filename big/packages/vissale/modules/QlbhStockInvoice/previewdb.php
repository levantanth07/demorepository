<?php

/**
 * This class describes a preview qlbh stock invoice db.
 */
class PreviewQlbhStockInvoiceDB
{   
    private $shopID;
    private $orderIDsString;
    private $orderIDs;
    private $ordersCount;
    private $warehouseID;
    private $warehouseName;
    private $receiverName;

    private $masterGroupID;
    private $totalAmount = 0;
    const DEFAULT_WAREHOUSE_ID = 1; // Kho tong

    /**
     * Constructs a new instance.
     *
     * @param      <type>  $orderIDsString  The order i ds string
     * @param      int     $warehouseID     The warehouse id
     * @param      int     $shopID          The shop id
     * @param      string  $receiverName    The receiver name
     */
    public function __construct($orderIDsString, $warehouseID = 0, $shopID = 0, $receiverName = '')
    {   
        $this->orderIDsString = $orderIDsString;
        $this->orderIDs = explode(',', $this->orderIDsString);
        $this->ordersCount = count($this->orderIDs);

        if(!$this->shopID = $shopID){
            $this->shopID = Session::get('group_id');
        }
        
        $this->setWarehouseID((int) $warehouseID);

        $this->warehouseName = $this->getWarehouseName($this->warehouseID);

        if(!$this->receiverName = $receiverName){
            $this->receiverName = '...';
        }

        $this->masterGroupID = Session::get('master_group_id');
        $this->user = Session::get('user_data');
    }

    /**
     * Sets the warehouse id.
     *
     * @param      <type>  $warehouseID  The warehouse id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function setWarehouseID(int $warehouseID)
    {
        if($warehouseID){
            return $this->warehouseID = $warehouseID;
        }

        if(!$this->warehouseID = $this->getWarehouseIDByGroupID($this->shopID)){
            return $this->warehouseID = self::DEFAULT_WAREHOUSE_ID;
        }
    }

    /**
     * Lấy ID kho
     *
     * @return     <type>  The warehouse id.
     */
    public function getWarehouseIDByGroupID(int $groupID)
    {
        return DB::fetch('SELECT id FROM `qlbh_warehouse` WHERE `qlbh_warehouse`.`is_default` = 1 and group_id=' . $groupID,'id');
    }

    /**
     * Lấy tên kho
     *
     * @return     <type>  The warehouse id.
     */
    public function getWarehouseName(int $ID)
    {
        return DB::fetch('SELECT `name` FROM `qlbh_warehouse` WHERE `qlbh_warehouse`.`id` = '.$ID,'name');
    }

    /**
     * Gets the group.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The group.
     */
    public function getGroup(int $groupID)
    {
        return DB::fetch('select name as group_name,address, phone from `groups` where id = '.$groupID);
    }

    /**
     * Lấy tất cả thông tin hóa đơn
     *
     * @return     array  The invoice.
     */
    public function getInvoice()
    {        
        if(!$this->orderIDsString || !$this->warehouseID){
            return [];
        }

        if(!$orderProducts = $this->getOrdersProducts($this->orderIDsString)){
            return [];
        }

        $products = $this->getProducts($orderProducts);
        $this->totalAmount = $this->countAmount($products);
        $products = $this->uniqueProducts($products);
        $invoice = $this->makeInvoiceHeader();
        
        return array_merge($invoice, ['products' => $products]);
    }

    /**
     * Tạo số bill bằng cách lấy số bill lớn nhất trong db và công thêm 1 
     *
     * @return     <type>  The bill number.
     */
    private function generateBillNumber()
    {   
        $fmt = 'SELECT id,bill_number FROM qlbh_stock_invoice where type="EXPORT"';
        $fmt .= '  and qlbh_stock_invoice.group_id = %d ORDER BY bill_number DESC';
        $sql = sprintf($fmt, $this->shopID);
        $bill = DB::fetch($sql);

        return $bill ? $bill['bill_number'] + 1 : 1;
    }

    /**
     * Tạo thông tin của hóa đơn  
     *
     * @return     array  The invoice header.
     */
    private function makeInvoiceHeader()
    {   
        $note = DB::escape(Url::get('note'));
        if($this->orderIDsString){
            $note = sprintf(
                'Từ %s đơn hàng %s', 
                $this->ordersCount, 
                $this->ordersCount < 2 ? ' (Mã: '.$this->orderIDsString.')' : ''
            );
        }

        $group = $this->getGroup($this->shopID);

        return [
            'bill_number'=>$this->generateBillNumber(),
            'type'=>'EXPORT',
            'deliver_name'=>$this->user['full_name'],
            'receiver_name'=>$this->receiverName,
            'total_amount'=> $this->totalAmount,
            'total_amount_fmt'=> System::display_number($this->totalAmount),
            'has_price' => get_group_options('show_price_in_export_invoice'),
            'create_date'=>date('Y-m-d'),
            'order_id'=>$this->orderIDsString,
            'note'=> $note,
            'warehouse_id' => $this->warehouseID,
            'warehouse_name' => $this->warehouseName,
            'day' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
            'group_name' => $group['group_name'],
            'phone' => $group['phone'],
            'address' => $group['address'],
            'staff_name' => $this->getStaffName()
        ];
    }

    /**
     * Gets the staff name.
     */
    public function getStaffName()
    {
        return DB::fetch('SELECT id,full_name FROM party WHERE user_id=\''.$this->user['id'].'\'','full_name');
    }

    /**
     * Gets the orders products.
     *
     * @param      string  $orderIDsString  The order i ds string
     *
     * @return     <type>  The orders products.
     */
    private function getOrdersProducts(string $ids)
    {
        $idsArr = explode(',',$ids);
        $escape_ids = array_map(function($id){
            return DB::escape($id);
        }, $idsArr);

        $ids = implode(',', $escape_ids);
        $sql = '
            SELECT
                orders_products.id,
                products.code as product_code,
                products.name as product_name,
                orders_products.product_price as price,
                orders_products.qty as quantity,
                orders_products.product_price*orders_products.qty as payment_price,
                units.id as unit_id,
                units.name as unit_name,
                products.id as product_id,
                products.price as price_default,
                orders_products.warehouse_id,
                qlbh_warehouse.name as warehouse_name
            FROM
                orders_products
                JOIN products ON products.id = orders_products.product_id
                LEFT JOIN qlbh_warehouse ON orders_products.warehouse_id = qlbh_warehouse.id
                LEFT JOIN units ON units.id = products.unit_id
            WHERE
                orders_products.order_id IN ('.$ids.')
            ';
        return DB::fetch_all($sql);
    }

    /**
     * Gets the products.
     *
     * @param      array   $orderProducts  The order products
     *
     * @return     <type>  The products.
     */
    private function getProducts(array $orderProducts)
    {   
        return array_map(function($product) {
            $product['unit_id'] = $product['unit_id'] ? $product['unit_id'] : '0';
            $product['price'] = $product['price'] ? str_replace(',','',$product['price']) : '0';
            $product['price_fmt'] = System::display_number($product['price']);
            $product['quantity'] = $product['quantity'] ? str_replace(',','',$product['quantity']) : '0';
            $product['payment_price'] = $product['payment_price'] ? $product['payment_price'] : 0;
            $product['payment_price_fmt'] = System::display_number($product['payment_price']);
            $product['payment_amount'] = $product['payment_price'] ? $product['payment_price'] : $product['price'] * $product['quantity'];
            $product['warehouse_id'] = $product['warehouse_id'] ? $product['warehouse_id'] : $this->warehouseID;
            $product['warehouse_name'] = $this->warehouseName;

            unset($product['payment_price']);
            unset($product['unit']);
            unset($product['id']);

            return $product;
        }, $orderProducts);
    }

    /**
     * Counts the number of amount.
     *
     * @param      array   $products  The products
     *
     * @return     <type>  Number of amount.
     */
    private function countAmount(array $products)
    {
        return array_reduce($products, function($totalAmount, $product) {
            return $totalAmount += System::calculate_number($product['payment_amount']);
        }, 0);
    }

    /**
     * { function_description }
     *
     * @param      array  $products  The products
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function uniqueProducts(array $products)
    {   
        $uniqProducts = [];
        array_walk($products, function($product) use(&$uniqProducts) {
            $code = $product['product_code'];
            $price = $product['price'];
            $key = md5(sprintf('%s-%d', $code, $price));

            if(!isset($uniqProducts[$key])){
                $uniqProducts[$key] = $product;
            }else{
                $uniqProducts[$key]['quantity'] += $product['quantity'];
                $uniqProducts[$key]['payment_amount'] += System::calculate_number(
                    $product['payment_amount']
                );
            }
            $uniqProducts[$key]['payment_amount_fmt'] = System::display_number(
                $uniqProducts[$key]['payment_amount']
            );
        });

        return $uniqProducts;
    }
}