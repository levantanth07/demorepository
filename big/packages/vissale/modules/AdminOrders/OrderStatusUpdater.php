<?php
use GuzzleHttp\Client;
class OrderStatusUpdater
{   
    private $errors = [];

    private $orderID;
    private $toStatusID;
    private $userID;
    private $groupID;
    private $canCreateInvoice;
    private $statuses = [];

    private $order;
    private $orderExtra;

    private $update = [];

    const UNCONFIRMED_ORDER = 1;
    const UNKNOW_STATUS = 2;
    const ORDER_ID_OR_GROUP_ID_INVALID = 3;
    const USER_INVALID = 4;
    const ORDER_NOT_EXISTS = 5;
    const EMPTY_STATUSES = 6;
    const NOT_ALLOWED_TO_UPDATE = 7;
    const NO_STATUS_CHANGE = 8;
    const NO_CITY_ID = 9;

    /**
     * Constructs a new instance.
     * Không cho phép khởi tạo trực tiếp 
     */
    private function __construct() {}

    /**
     * Khởi tạo đối tượng, thiết lập thông tin đơn hàng
     *
     * @param      int     $orderID  The order id
     * @param      int     $groupID  The group id
     *
     * @return     Static  ( description_of_the_return_value )
     */
    public static function setOrder(int $orderID, int $groupID)
    {
        $instance = new Static();
        $instance->orderID = $orderID;
        $instance->groupID = $groupID;

        return $instance;
    }

    /**
     * Thiết lập ID trạng thái muốn chuyển đến
     *
     * @param      int   $toStatusID  To status id
     *
     * @return     self  ( description_of_the_return_value )
     */
    public function setStatusID(int $toStatusID)
    {
        $this->toStatusID = $toStatusID;

        return $this;
    }

    /**
     * Thiết lập ID người dùng muốn gắn vào trạng thái
     *
     * @param      int   $userID  The user id
     *
     * @return     self  ( description_of_the_return_value )
     */
    public function setUserID(int $userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Sets the statuses.
     *
     * @param      array  $statuses  The statuses
     *
     * @return     self   ( description_of_the_return_value )
     */
    public function setStatuses(array $statuses)
    {   
        $this->statuses = $statuses; 

        return $this;
    }

    /**
     * Thiết lập flag sẽ được dùng ở một số trạng thái nhất định xem có được phép tạo phiếu xuất/nhập
     *
     * @param      bool  $canCreateInvoice  Indicates if create invoice
     *
     * @return     self  True if able to create invoice, False otherwise.
     */
    public function canCreateInvoice(bool $canCreateInvoice = true)
    {
        $this->canCreateInvoice = $canCreateInvoice;

        return $this;
    }

    /**
     * Thực thi việc cập nhật trạng thái
     *
     * @return     self|static
     */
    public function exec()
    {   
        if(!$this->validate()){
            return $this;
        }

        $this->orderExtra = $this->getOrderExtra();
        if($this->toStatusID == XAC_NHAN && !$this->order['user_confirmed']){
            return $this->toConfirmed();
        }

        if($this->toStatusID == CHUYEN_HANG && !$this->order['user_delivered']){
            return $this->toDelivered();
        }

        if($this->toStatusID == KE_TOAN && (!$this->orderExtra || !$this->orderExtra['accounting_user_confirmed'])){
            return $this->toAccounting();
        }

        if($this->toStatusID == THANH_CONG && (!$this->orderExtra || !$this->orderExtra['update_successed_user'])){
            return $this->toSuccess();
        }

        if($this->toStatusID == TRA_VE_KHO && (!$this->orderExtra || empty($this->orderExtra['update_returned_to_warehouse_user']))){
            return $this->toReturnedToWarehouse();
        }

        if($this->toStatusID == HUY && (!$this->orderExtra || !$this->orderExtra['update_cancel_user'])){
            return $this->toCancel();
        }

        if($this->toStatusID == CHUYEN_HOAN && (!$this->orderExtra || !$this->orderExtra['update_returned_user'])){
            return $this->toReturned();
        }
        // Khách phân vân
        if($this->toStatusID == KHACH_PHAN_VAN ){
            $flag = false;
            if(!$this->orderExtra || !$this->orderExtra['confused_user_confirmed']){
                $flag = false;
                return $this->toConfusedUser($flag);
            } else{
                $flag = true;
                return $this->toConfusedUser($flag);
            }
            
        }
        // Khách không nghe máy
        if($this->toStatusID == KHACH_KHONG_NGHE_MAY ){
            $flag = false;
            if(!$this->orderExtra || !$this->orderExtra['not_answer_phone_user_confirmed']){
                $flag = false;
                return $this->toNotAnswerPhoneUser($flag);
            } else{
                $flag = true;
                return $this->toNotAnswerPhoneUser($flag);
            }
            
        }

        if($this->toStatusID == DA_THU_TIEN && (!$this->orderExtra || !$this->orderExtra['update_paid_user'])){

            return $this->toPaid();
        }

        return $this->updateOrderAndLog([]);
    }

    /**
     * Validate thông tin đầu vào 
     *
     * @return     static|self
     */
    private function validate()
    {   
        if(!$this->getOrder()){
            $this->errors[] = self::ORDER_NOT_EXISTS;
        }
        
        $toLevel = (int) $this->statuses[$this->toStatusID]['level'];
        $confirmLevel = $this->statuses[XAC_NHAN] ? (int) $this->statuses[XAC_NHAN]['level'] : 0;
        if($this->statuses[XAC_NHAN] && $toLevel > $confirmLevel && !$this->order['user_confirmed']){
            $this->errors[] = self::UNCONFIRMED_ORDER;
        }

        if($this->toStatusID == KE_TOAN && !$this->order['user_confirmed']){
            $this->errors[] = self::UNCONFIRMED_ORDER;
        }
        if($this->toStatusID == KHACH_PHAN_VAN && !$this->order['user_confirmed']){
            $this->errors[] = self::UNCONFIRMED_ORDER;
        }
        if($this->toStatusID == KHACH_KHONG_NGHE_MAY && !$this->order['user_confirmed']){
            $this->errors[] = self::UNCONFIRMED_ORDER;
        }

        if(!$this->toStatusID){
            $this->errors[] = self::UNKNOW_STATUS;
        }
        
        if(!$this->orderID || !$this->groupID){
            $this->errors[] = self::ORDER_ID_OR_GROUP_ID_INVALID;
        }

        if(!$this->userID || !$this->isGroupOwnUser()){
            $this->errors[] = self::USER_INVALID;
        }

        // if(!$this->statuses){
        //     $this->errors[] = self::EMPTY_STATUSES;
        // }

        if($this->order['status_id'] == $this->toStatusID){
            $this->errors[] = self::NO_STATUS_CHANGE;
        }
        
        if(!AdminOrders::$quyen_admin_ke_toan && !$this->canUpdateOrderStatus()){
            $this->errors[] = self::NOT_ALLOWED_TO_UPDATE;
        }

        return !$this->errors;
    }

    /**
     * Determines if group own user.
     *
     * @return     bool  True if group own user, False otherwise.
     */
    private function isGroupOwnUser()
    {
        return $this->getUser();
    }

    /**
     * Gets the user.
     *
     * @return     array|int  The user.
     */
    private function getUser()
    {   
        $fmt = 'SELECT `id` FROM `users` WHERE `id` = %d AND `group_id` = %d';
        $sql = sprintf($fmt, $this->userID, $this->groupID);

        return $this->user = DB::query($sql);
    }


    /**
     * Chuyển đơn sang trạng thái xác nhận
     */
    private function toConfirmed()
    {   
        $update = [];
        $update['confirmed'] = date('Y-m-d H:i:s');
        $update['user_confirmed'] = $this->userID;

        $this->updateOrderAndLog($update);
        // if (defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1) {
        //     AdminOrdersDB::sentDataToApiTuha($this->orderID);
        // }
        if($this->canCreateInvoice && AdminOrders::$create_export_invoice_when_confirmed){
            $this->createExportInvoice();
        }

        return $this;
    }

    /**
     * Chuyển đơn sang trạng thái chuyển hàng
     */
    private function toDelivered()
    {   
        if(!$this->hasProductsInOrder()){
            $this->errors[] = 'Đơn hàng không có sản phẩm.';
            
            return $this;
        }

        $update = [];
        $update['delivered'] = date('Y-m-d H:i:s');
        $update['user_delivered'] = $this->userID;
        
        $this->updateOrderAndLog($update);

        if($this->canCreateInvoice && AdminOrders::$create_export_invoice_when_delivered){
            $this->createExportInvoice();
        }

        return $this;
    }

    /**
     * Chuyển đơn sang trạng thái đóng hàng 
     */
    private function toAccounting()
    {   
        $update = [];
        $update['accounting_confirmed'] = date('Y-m-d H:i:s');
        $update['accounting_user_confirmed'] = $this->userID;
        $this->updateOrderExtraStatus($update);
        // if (defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1) {
        //     AdminOrdersDB::sentDataToApiTuha($this->orderID);
        // }
        return $this;
    }

    private function toConfusedUser($flag)
    {   
        $update = [];
        if($flag == false){
            $update['confused_confirmed'] = date('Y-m-d H:i:s');
            $update['confused_user_confirmed'] = $this->userID;

            $update['confused_lastest_confirmed'] = date('Y-m-d H:i:s');
            $update['confused_user_lastest_confirmed'] = $this->userID;
        } else {
            $update['confused_lastest_confirmed'] = date('Y-m-d H:i:s');
            $update['confused_user_lastest_confirmed'] = $this->userID;
        }
        $this->updateOrderExtraStatus($update);
        return $this;
    }
    private function toNotAnswerPhoneUser($flag)
    {   
        $update = [];
        if($flag == false){
            $update['not_answer_phone_confirmed'] = date('Y-m-d H:i:s');
            $update['not_answer_phone_user_confirmed'] = $this->userID;
            $update['not_answer_phone_lastest_confirmed'] = date('Y-m-d H:i:s');
            $update['not_answer_phone_user_lastest_confirmed'] = $this->userID;
        } else {
            $update['not_answer_phone_lastest_confirmed'] = date('Y-m-d H:i:s');
            $update['not_answer_phone_user_lastest_confirmed'] = $this->userID;
        }

        $this->updateOrderExtraStatus($update);
        return $this;
    }

    /**
     * Chuyển đơn sang thành công
     */
    private function toSuccess()
    {   
        $update = [];
        $update['update_successed_time'] = date('Y-m-d H:i:s');
        $update['update_successed_user'] = $this->userID;

        return $this->updateOrderExtraStatus($update);
    }

    /**
     * Chuyển đơn sang chuyển hoàn
     */
    private function toReturned()
    {   
        $update = [];
        $update['update_returned_time'] = date('Y-m-d H:i:s');
        $update['update_returned_user'] = $this->userID;

        $this->updateOrderExtraStatus($update);

        // if($this->canCreateInvoice && AdminOrders::$create_import_invoice_when_return){
        //     $this->createImportInvoice();
        // }

        return $this;
    }

    private function toCancel()
    {   
        $update = [];
        $update['update_cancel_time'] = date('Y-m-d H:i:s');
        $update['update_cancel_user'] = $this->userID;

        $this->updateOrderExtraStatus($update);

        return $this;
    }

    private function toReturnedToWarehouse()
    {   
        $update = [];
        $update['update_returned_to_warehouse_time'] = date('Y-m-d H:i:s');
        $update['update_returned_to_warehouse_user_id'] = $this->userID;

        $this->updateOrderExtraStatus($update);

        if($this->canCreateInvoice && AdminOrders::$create_import_invoice_when_return){
            $this->createImportInvoice();
        }

        return $this;
    }

    /**
     * Chuyển đơn sang thu tiền
     */
    private function toPaid()
    {   
        $update = [];
        $update['update_paid_time'] = date('Y-m-d H:i:s');
        $update['update_paid_user'] = $this->userID;

        return $this->updateOrderExtraStatus($update);
    }

    /**
     * Thực hiện việc cập nhật order extra
     */
    private function updateOrderExtraStatus(array $update)
    {
        if($this->orderExtra){
            $this->updateOrderExtra($update);
        }else{
            $this->insertOrderExtra($update);
        }

        return $this->updateOrderAndLog([]);
    }

    /**
     * Insert Order Extra
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertOrderExtra(array $update)
    {
        $update['order_id'] = $this->orderID;
        $update['group_id'] = $this->groupID;

        return DB::insert('orders_extra',$update);
    }

    /**
     * Update Order Extra
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function updateOrderExtra(array $update)
    {
        return DB::update('orders_extra',$update, 'id = ' . $this->orderExtra['id']);
    }

    /**
     * Update thông tin đơn hàng và log 
     */
    private function updateOrderAndLog(array $update)
    {   
        $update['status_id'] = $this->toStatusID;
        $update['modified'] = date("Y-m-d H:i:s");
        $itemStatus = $this->get_status($this->toStatusID);
        if($itemStatus['id'] == DA_THU_TIEN || $itemStatus['id'] == THANH_CONG){
            $order_not_success = 0;
        } else if((int)$itemStatus['level'] < 3){
            $order_not_success = 0;
        }
        $rows_extra = array(
            'order_not_success'=>$order_not_success,
        );
        DB::update_id('orders',$update,$this->orderID);
        $this->updateOrderExtra($rows_extra);
        if (defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1 && (Url::get('cmd') == 'add' || Url::get('cmd') == 'edit')) {
            $oldStatus = $this->order['status_id'];
            $newStatus = $this->toStatusID;
            AdminOrdersDB::sentDataToApiTuha($this->orderID,$oldStatus,$newStatus);
        }
        AdminOrdersDB::update_revision($this->orderID,$this->order['status_id'],$this->toStatusID);

        return $this;
    }
    private function get_status($statusId){
        $cond = ' statuses.id = '.$statusId.'';
        $sql = '
            SELECT 
                statuses.id, statuses.no_revenue,
                IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
                IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
                IF(statuses.is_system=1,statuses.color,statuses_custom.color) as color
            FROM 
                `statuses`
                LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
            WHERE 
                '.$cond.'
            GROUP BY
                statuses.id
            ORDER BY 
                IF(statuses.is_system=1,statuses.level,statuses_custom.level),
                statuses_custom.position,
                statuses.id
        ';

        $items = DB::fetch($sql);
        return $items;
    }
    /**
     * Creates an export invoice.
     */
    private function createExportInvoice()
    {
        require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
        QlbhStockInvoiceDB::xuat_kho($this->orderID);
    }

    /**
     * Creates an import invoice.
     */
    private function createImportInvoice()
    {
        require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
        QlbhStockInvoiceDB::nhap_kho($this->orderID);
    }

    /**
     * Determines ability to update order status.
     *
     * @return     bool  True if able to update order status, False otherwise.
     */
    private function canUpdateOrderStatus()
    {   
        return !$this->order['status_id']                  // chua co trang thai
               || $this->order['status_id'] != THANH_CONG; // trang thai khong phai thanh cong
    }

    /**
     * Gets the order.
     *
     * @return     <type>  The order.
     */
    private function getOrder()
    {   
        $selects = 'id,total_price,user_confirmed,confirmed,status_id,user_delivered,delivered';
        $fmt = 'SELECT %s FROM orders WHERE id= %d AND group_id = %d';
        $sql = sprintf($fmt, $selects, $this->orderID, $this->groupID);

        return $this->order = DB::fetch($sql);
    }

    /**
     * Gets the order extra.
     *
     * @return     <type>  The order extra.
     */
    private function getOrderExtra()
    {
        return DB::fetch('SELECT * FROM orders_extra WHERE order_id = ' . $this->orderID . ' AND group_id = ' . $this->groupID);
    }

    /**
     * Determines if products in order.
     *
     * @return     bool  True if products in order, False otherwise.
     */
    private function hasProductsInOrder()
    {
        return $this->getProducts();
    }

    /**
     * Gets the products.
     *
     * @return     <type>  The products.
     */
    private function getProducts()
    {
        return $this->products = DB::fetch('SELECT `id` FROM `orders_products` WHERE `order_id` = ' . $this->orderID);
    }

    /**
     * Thực thi closure nếu có lỗi xảy ra
     *
     * @param      Closure  $handler  The handler
     *
     * @return     self     ( description_of_the_return_value )
     */
    public function error(Closure $handler)
    {
        if(!$this->errors){
            return $this;
        }

        call_user_func_array($handler, [$this->errors]);

        return $this;
    }

    /**
     * Gets the errors.
     *
     * @return     <type>  The errors.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Nhóm các status có cùng level lại với nhau
     *
     * @param      array   $statuses  The statuses
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function groupStatusesByLevel(array $statuses)
    {
        return array_reduce($statuses, function($results, $status){
            $level = $status['level'] = intval($status['level']);

            if(!isset($results[$level])){
                $results[$level] = [];
            }

            $results[$level][] = $status;

            return $results;
        },[]);
    }
}
