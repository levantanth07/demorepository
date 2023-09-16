<?php

class ImportExcelValidator
{
    const DEFAULT_DB_DATE_TIME = '0000-00-00 00:00:00';
    const NUM_FIELDS = 37;

    const STT = 1;               //STT 1
    const ASSIGNED_USER = 2;     //Sale được chia  2
    const ASSIGNED_DATE = 3;     //Ngày chia   3
    const ZIP_CODE = 4;          //Mã Bưu Điện 4
    const FB_PAGE_ID = 5;        //Fb page id  5
    const FB_POST_ID = 6;        //Fb post id  6
    const CONFIRMED_USER = 7;    //Người Xác Nhận  7
    const CONFIRMED_DATE = 8;    //Ngày Xác Nhận   8
    const CREATED_USER = 9;      //Người Tạo   9
    const CREATED_DATE = 10;     //Ngày Tạo    10
    const FB_CUSTOMER = 11;      //Fb khách hàng   11
    const CUSTOMER_NAME = 12;    //Tên Khách Hàng  12
    const ADDRESS = 13;          //Địa Chỉ   13
    const CONFIRM_CODE = 14;     //Mã Xác Nhận Import  14
    const NOTE = 15;             //Ghi Chú 15
    const PHONE_NUMBER = 16;     //Số Điện Thoại   16
    const PRODUCT_CODE = 17;     //Mã Sản Phẩm    17
    const PRODUCT_NAME = 18;     //Tên Sản Phẩm    18
    const WEIGHT = 19;           //Trọng lượng    19
    const CITY = 20;             //Tỉnh Thành  20
    const PRICE = 21;            //Giá 21
    const INTO_MONEY = 22;       //Thành Tiền 22
    const DISCOUNT = 23;         //Thành Tiền 23
    const BUNDLE_NAME = 24;      //Phân Loại Sản Phẩm 24
    const ORDER_TYPE = 25;       //Loại đơn 25
    const SOURCE = 26;           //Nguồn   26
    const SHIP_TYPE = 27;        //Loại Ship   27
    const TOTAL_AMOUNT = 28;     //Tổng Tiền   28
    const DELIVERED_USER = 29;   //Người Chuyển    29
    const DELIVERED_NOTE = 30;   //Ghi Chú Giao Hàng   30
    const DELIVERED_DATE = 31;   //Ngày Chuyển 31
    const SUCCESSED_USER = 32;   //Người Thành Công 32
    const SUCCESSED_DATE = 33;   //Ngày Thành Công 33
    const EMAIL = 34;            //EMAIL 34
    const ACCOUNTING_USER = 35;  //Người đóng hàng 35
    const ACCOUNTING_DATE = 36;  //Ngày đóng hàng 36
    const NOTE2 = 37;            //Note2 37

    const REFUND_DATE = 40;      //Ngày chuyển hoàn 40
    const REFUND_USER = 41;      //Người chuyển hoàn 41
    const RETURNED_DATE = 42;    //Ngày trả hàng về kho 42
    const RETURNED_USER = 43;    //Người trả hàng về kho 43
    const PAIDED_DATE = 44;      //Ngày thu tiền 44
    const PAIDED_USER = 45;      //Người thu tiền 45

    const CITY_ID = 50;
    const SOURCE_ID = 51;
    const BUNDLE_ID = 52;
    const ORDER_TYPE_ID = 53;
    const PRODUCTS = 54;

    const NGAY_TAO_LON_HON_NGAY_CHIA  = 1001;
    const NGAY_TAO_LON_HON_NGAY_KE_TOAN   = 1002;
    const NGAY_TAO_LON_HON_NGAY_XAC_NHAN  = 1003;
    const NGAY_TAO_LON_HON_NGAY_CHUYEN_HANG   = 1004;
    const NGAY_TAO_LON_HON_NGAY_THANH_CONG    = 1005;
    const NGAY_CHIA_LON_HON_NGAY_KE_TOAN  = 1006;
    const NGAY_CHIA_LON_HON_NGAY_XAC_NHAN = 1007;
    const NGAY_CHIA_LON_HON_NGAY_CHUYEN_HANG  = 1008;
    const NGAY_CHIA_LON_HON_NGAY_THANH_CONG   = 1009;
    const NGAY_CHUYEN_HANG_LON_HON_NGAY_THANH_CONG    = 1012;
    const NGAY_THANH_CONG_LON_HON_NGAY_HIEN_TAI   = 1013;
    const NGAY_CHUYEN_HANG_LON_HON_NGAY_HIEN_TAI  = 1014;
    const NGAY_XAC_NHAN_LON_HON_NGAY_HIEN_TAI = 1015;
    const NGAY_KE_TOAN_LON_HON_NGAY_HIEN_TAI  = 1016;
    const NGAY_CHIA_LON_HON_NGAY_HIEN_TAI = 1017;
    const NGAY_TAO_LON_HON_NGAY_HIEN_TAI  = 1018;
    const NGAY_XAC_NHAN_LON_HON_NGAY_KE_TOAN = 1010;
    const NGAY_KE_TOAN_LON_HON_NGAY_CHUYEN_HANG = 1011;
    const NGAY_XAC_NHAN_LON_HON_NGAY_CHUYEN_HANG = 1019;
    const NGAY_KE_TOAN_LON_HON_NGAY_THANH_CONG = 1020;
    const NGAY_XAC_NHAN_LON_HON_NGAY_THANH_CONG = 1021;

    const UNCONFIRMED_ORDER = 2001;
    const UNSUCCESSED_ORDER = 2002;

    const ASSIGNED_INFORMATION_ERROR = 3001;
    const CONFIRMED_INFORMATION_ERROR = 3002;
    const CREATED_INFORMATION_ERROR = 3003;
    const DELIVERED_INFORMATION_ERROR = 3004;
    const SUCCESSED_INFORMATION_ERROR = 3005;
    const ACCOUNTING_INFORMATION_ERROR = 3006;

    private $users = [];
    private $cities = [];
    private $sources = [];
    private $statuses = [];
    private $products = [];
    private $types = [];

    private $row = [];
    private $statusID = 0;
    private $assignedUsername = '';
    private $groupID = 0;
    private $currentUserID = 0;
    private $masterGroupID = 0;

    private $insert = [];

    private $errors = [];
    private $errorColumns = [];
    private $isObd = false;


    /**
     * Constructs a new instance.
     *
     * @param      array  $row    The row
     */
    public function __construct()
    {
        $this->isObd = isObd();
    }

    /**
     * New instance.
     *
     * @param      array  $row    The row
     */
    public static function new()
    {
        return new Static();
    }

    /**
     * { function_description }
     *
     * @param      array   $row    The row
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function prepareData(array $row)
    {
        $this->insert = [];
        $this->errors = [];
        $this->errorColumns = [];
        $this->row = $row;

        return $this;

        $fields = [
            self::ZIP_CODE,
            self::SHIP_TYPE,
            self::PRODUCT_NAME,
            self::PRICE,
            self::SOURCE,
            self::DELIVERED_NOTE,
            self::NOTE,
            self::PRODUCT_CODE,
            self::PRODUCT_CODE,
            self::WEIGHT,
            self::ORDER_TYPE,
            self::BUNDLE_NAME,
            self::EMAIL,
            self::CUSTOMER_NAME,
            self::CONFIRM_CODE,
            self::FB_PAGE_ID,
            self::ADDRESS,
            self::CITY,
        ];

        array_map(function($field) use(&$row){
            $row[$field] = DataFilter::removeXSSinHtml($row[$field]);
        }, $fields);

        return $row;
    }

    /**
     * Sets the status id.
     *
     * @param      int   $statusID  The status id
     */
    public function setStatusID(int $statusID)
    {
        $this->statusID = $statusID;

        return $this;
    }

    /**
     * Gets the group id.
     *
     * @return     <type>  The group id.
     */
    public function getGroupID()
    {
        return $this->groupID;
    }

    /**
     * Sets the assigned username.
     *
     * @param      string  $username  The username
     *
     * @return     self
     */
    public function setAssignedUsername(string $username)
    {
        $this->assignedUsername = $username;

        return $this;
    }

    /**
     * Sets the group id.
     *
     * @param      int   $groupID  The group id
     *
     * @return     self
     */
    public function setGroupID(int $groupID)
    {
        $this->groupID = $groupID;

        return $this;
    }

    /**
     * Sets the current user id.
     *
     * @param      int   $currentUserID  The current user id
     *
     * @return     self
     */
    public function setCurrentUserID(int $currentUserID)
    {
        $this->currentUserID = $currentUserID;

        return $this;
    }

    /**
     * Gets the current user id.
     *
     * @return     <type>  The current user id.
     */
    public function getCurrentUserID()
    {
        return $this->currentUserID;
    }

    /**
     * Set users shop
     *
     * @param      int   $currentUserID  The current user id
     *
     * @return     self
     */
    public function setUsers(array $users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Sets the cities.
     *
     * @param      array  $cities  The cities
     *
     * @return     self
     */
    public function setCities(array $cities)
    {
        $this->cities = $cities;

        return $this;
    }

    /**
     * Sets the sources.
     *
     * @param      array  $sources  The sources
     *
     * @return     self
     */
    public function setSources(array $sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      array  $row    The row
     */
    public static function validate(array $row)
    {
        return self::new()->prepareData($row);
    }

    /**
     * Sets the statuses.
     *
     * @param      array  $statuses  The statuses
     *
     * @return     self
     */
    public function setStatuses(array $statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * Sets the bundles.
     *
     * @param      array  $bundles  The bundles
     *
     * @return     self
     */
    public function setBundles(array $bundles)
    {
        $this->bundles = $bundles;

        return $this;
    }

    /**
     * Sets the products.
     *
     * @param      array  $products  The products
     *
     * @return     self
     */
    public function setProducts(array $products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Sets the types.
     *
     * @param      array  $types  The types
     *
     * @return     self
     */
    public function setTypes(array $types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Sets the master group id.
     *
     * @param      int   $masterGroupID  The master group id
     *
     * @return     self
     */
    public function setMasterGroupID(int $masterGroupID)
    {
        $this->masterGroupID = $masterGroupID;

        return $this;
    }

    /**
     * { function_description }
     */
    public function execute()
    {
        $this->fillCreatedUser()
            ->fillCreatedDate()
            ->fillAssignedUser()
            ->fillAssignedDate()
            ->fillAccountingDate()
            ->fillAccountingUser()
            ->fillConfirmedUser()
            ->fillConfirmedDate()
            ->fillSuccessedUser()
            ->fillSuccessedDate()
            ->fillDeliveredUser()
            ->fillDeliveredDate()
            ->fillCustomerName()
            ->fillConfirmCode()
            ->fillFBPageID()
            ->fillFBPostID()
            ->fillFBCustomer()
            ->fillAddress()
            ->fillCity()
            ->fillMobile()
            ->fillZipCode()
            ->fillShipType()
            ->fillProductName()
            ->fillPrice()
            ->fillSourceName()
            ->fillDeliveredNote()
            ->fillNote()
            ->fillProductCode()
            ->fillWeight()
            ->fillTotalAmount()
            ->fillDiscount()
            ->fillIntoMoney()
            ->fillOrderType()
            ->fillBundle()
            ->fillEmail()
            ->fillNote2()

            // extra column
            ->fillCityID()
            ->fillSourceID()
            ->fillBundleID()
            ->fillOrderTypeID()
            ->fillProducts()

            ->fillRefundUser()
            ->fillRefundDate()
            ->fillReturnedUser()
            ->fillReturnedDate()
            ->fillPaidedUser()
            ->fillPaidedDate()
            ->validateCustomer()
            ->validateUserAndDate()
            ->validateByStatus()
            ->validateDateTime()
            ->validatePhoneNumber();

        if ($this->isObd) {
            $this->validateSource();
        }//end if

        return $this;
    }

    /**
     * Điền thông tin người tạo, mặc định lấy người import, nếu điền thì lấy thông tin trong excerl
     *
     * @return     self
     */
    private function fillCreatedUser()
    {
        $this->insert[self::CREATED_USER] = $this->currentUserID;

        if(!$username = $this->row[self::CREATED_USER]){
            return $this;
        }

        if($userID = $this->getUserIDByUsername($username)){
            $this->insert[self::CREATED_USER] = $userID;
        }

        return $this;
    }

    /**
     * Điền thông tin ngày tạo, mặc định lấy thời gian import, nếu có điền excel thì lấy theo excel
     *
     * @return     self
     */
    private function fillCreatedDate()
    {
        $this->insert[self::CREATED_DATE] = $this->currentTime();

        if($this->row[self::CREATED_DATE]){
            $dateString = $this->parseDateString($this->row[self::CREATED_DATE]);

            if(!$time = strtotime($dateString)){
                return $this;
            }

            if($time > time()){
                return $this;
            }

            $this->insert[self::CREATED_DATE] = date('Y-m-d H:i:s', $time);

            return $this;
        }

        return $this;
    }

    /**
     * Điền thông tin người chia
     * Nếu không có người chia thì mặc đinh 0
     *
     * @return     self
     */
    private function fillAssignedUser()
    {
        $this->insert[self::ASSIGNED_USER] = 0;

        $username = $this->row[self::ASSIGNED_USER] ? $this->row[self::ASSIGNED_USER] : $this->assignedUsername;
        if(!$username){
            return $this;
        }

        if($userID = $this->getUserIDByUsername($username)){
            $this->insert[self::ASSIGNED_USER] = $userID;
        }else{
            $this->errorColumn(self::ASSIGNED_USER);
        }

        return $this;
    }

    /**
     * Điền thông tin ngày chia, nếu có điền excel thì lấy theo excel
     *
     * @return     self
     */
    private function fillAssignedDate()
    {
        // khong dien
        if(!$rawDate = $this->row[self::ASSIGNED_DATE]){
            // dien sai user
            if(in_array(self::ASSIGNED_USER, $this->errorColumns)){
                return $this->errorColumn(self::ASSIGNED_USER, self::ASSIGNED_DATE);
            }

            // nếu khong có user thì lấy 000-00 ... nếu có thì lấy thời gian hiện tại
            $this->insert[self::ASSIGNED_DATE] = $this->insert[self::ASSIGNED_USER] ? $this->currentTime() : self::DEFAULT_DB_DATE_TIME;

            return $this;
        }

        $dateString = $this->parseDateString($rawDate);
        $date = strtotime($dateString);

        // Điền sai
        if(!$date || $date > time()){
            return $this->errorColumn(self::ASSIGNED_DATE);
        }

        $this->insert[self::ASSIGNED_DATE] = $this->insert[self::ASSIGNED_USER] ? date('Y-m-d H:i:s', $date) : self::DEFAULT_DB_DATE_TIME;

        return $this;
    }

    /**
     * import tại tt đóng hàng, nếu trong file k điền sẽ lấy thông tin người - ngày import
     *
     * @return     self
     */
    private function fillAccountingUser()
    {
        $this->insert[self::ACCOUNTING_USER] = 0;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[KE_TOAN]['level']){
            return $this;
        }

        if($this->statuses[$this->statusID]['level'] == $this->statuses[KE_TOAN]['level'] && $this->statusID != KE_TOAN){
            return $this;
        }

        return $this->fillUser(self::ACCOUNTING_USER, $this->statusID === KE_TOAN ? $this->currentUserID : 0);
    }

    /**
     * import tại tt  đóng hàng, nếu trong file k điền sẽ lấy thông tin người - ngày import
     *
     * @return     self
     */
    private function fillAccountingDate()
    {
        $this->insert[self::ACCOUNTING_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[KE_TOAN]['level']){
            return $this;
        }

        if($this->statuses[$this->statusID]['level'] == $this->statuses[KE_TOAN]['level'] && $this->statusID != KE_TOAN){
            return $this;
        }

        return $this->fillDate(self::ACCOUNTING_DATE, $this->statusID === KE_TOAN ? $this->currentTime() : self::DEFAULT_DB_DATE_TIME);
    }

    /**
     * Điền thông tin người xác nhận
     * Nếu không có người xác nhận thì mặc đinh 0
     *
     * @return     self
     */
    private function fillConfirmedUser()
    {
        $this->insert[self::CONFIRMED_USER] = 0;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[XAC_NHAN]['level']){
            return $this;
        }

        return $this->fillUser(self::CONFIRMED_USER, 0);
    }

    /**
     * Điền thông tin ngày xác nhận
     *
     * @return     self
     */
    private function fillConfirmedDate()
    {
        $this->insert[self::CONFIRMED_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[XAC_NHAN]['level']){
            return $this;
        }

        return $this->fillDate(self::CONFIRMED_DATE, self::DEFAULT_DB_DATE_TIME);
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillSuccessedUser()
    {
        $this->insert[self::SUCCESSED_USER] = 0;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[THANH_CONG]['level']){
            return $this;
        }
        return $this->fillUser(self::SUCCESSED_USER, 0);
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillSuccessedDate()
    {
        $this->insert[self::SUCCESSED_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statuses[$this->statusID]['level'] < $this->statuses[THANH_CONG]['level']){
            return $this;
        }
        return $this->fillDate(self::SUCCESSED_DATE, self::DEFAULT_DB_DATE_TIME);
    }

    /**
     * Điền thông tin người chuyển
     * import tại tt chuyển hàng, nếu trong file k điền sẽ lấy thông tin người - ngày import
     *
     * @return     self
     */
    private function fillDeliveredUser()
    {
        $this->insert[self::DELIVERED_USER] = $this->row[self::DELIVERED_USER];
        if($this->statuses[$this->statusID]['level'] < $this->statuses[CHUYEN_HANG]['level']){
            $this->insert[self::DELIVERED_USER] = 0;
            return $this;
        } else if ($this->statuses[$this->statusID]['level'] >= $this->statuses[CHUYEN_HANG]['level']){
            if($this->insert[self::DELIVERED_USER]){
                return $this->fillUser(self::DELIVERED_USER, 0);
            } else {
                return $this->errorColumn(self::DELIVERED_USER);
            }
        }
    }

    /**
     * Điền thông tin ngày chuyển
     * import tại tt chuyển hàng nếu trong file k điền sẽ lấy thông tin người - ngày import
     *
     * @return     self
     */
    private function fillDeliveredDate()
    {
        $this->insert[self::DELIVERED_DATE] = $this->row[self::DELIVERED_DATE];
        if($this->statuses[$this->statusID]['level'] < $this->statuses[CHUYEN_HANG]['level']){
            $this->insert[self::DELIVERED_DATE] = self::DEFAULT_DB_DATE_TIME;
            return $this;
        } else if ($this->statuses[$this->statusID]['level'] >= $this->statuses[CHUYEN_HANG]['level']){
            if($this->insert[self::DELIVERED_DATE]){
                return $this->fillDate(self::DELIVERED_DATE, self::DEFAULT_DB_DATE_TIME);
            } else {
                return $this->errorColumn(self::DELIVERED_DATE);
            }
        }
    }

    /**
     * Điền thông tin người chuyển hoàn
     *
     * @return     self
     */
    private function fillRefundUser()
    {
        $this->insert[self::REFUND_USER] = 0;

        if($this->statusID == CHUYEN_HOAN ){
            $this->insert[self::REFUND_USER] = $this->currentUserID;
        }

        return $this;
    }

    /**
     * Điền thông tin ngày chuyển hoàn
     *
     * @return     self
     */
    private function fillRefundDate()
    {
        $this->insert[self::REFUND_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statusID == CHUYEN_HOAN){
            $this->insert[self::REFUND_DATE] = $this->currentTime();
        }

        return $this;
    }

    /**
     * Điền thông tin người thu tiền
     *
     * @return     self
     */
    private function fillPaidedUser()
    {
        $this->insert[self::PAIDED_USER] = 0;

        if($this->statusID == DA_THU_TIEN){
            $this->insert[self::PAIDED_USER] = $this->currentUserID;
        }

        return $this;
    }

    /**
     * Điền thông tin ngày thu tiền
     *
     * @return     self
     */
    private function fillPaidedDate()
    {
        $this->insert[self::PAIDED_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statusID == DA_THU_TIEN){
            $this->insert[self::PAIDED_DATE] = $this->currentTime();
        }

        return $this;
    }

    /**
     * Điền thông tin người trả hàng
     *
     * @return     self
     */
    private function fillReturnedUser()
    {
        $this->insert[self::RETURNED_USER] = 0;

        if($this->statusID == TRA_VE_KHO){
            $this->insert[self::RETURNED_USER] = $this->currentUserID;
        }

        return $this;
    }

    /**
     * Điền thông tin ngày trả hàng
     *
     * @return     self
     */
    private function fillReturnedDate()
    {
        $this->insert[self::RETURNED_DATE] = self::DEFAULT_DB_DATE_TIME;

        if($this->statusID == TRA_VE_KHO){
            $this->insert[self::RETURNED_DATE] = $this->currentTime();
        }

        return $this;
    }

    /**
     * { function_description }
     */
    private function fillCustomerName()
    {
        $this->insert[self::CUSTOMER_NAME] = $this->row[self::CUSTOMER_NAME];
        if($this->insert[self::CUSTOMER_NAME] == ''){
            $this->errorColumn(self::CUSTOMER_NAME);
        }
        return $this;
    }

    /**
     * { function_description }
     */
    private function fillConfirmCode()
    {
        $this->insert[self::CONFIRM_CODE] = $this->row[self::CONFIRM_CODE];

        return $this;
    }

    /**
     * { function_description }
     */
    private function fillFBPageID()
    {
        $this->insert[self::FB_PAGE_ID] = $this->row[self::FB_PAGE_ID];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillFBPostID()
    {
        $this->insert[self::FB_POST_ID] = $this->row[self::FB_POST_ID];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillFBCustomer()
    {
        $this->insert[self::FB_CUSTOMER] = $this->row[self::FB_CUSTOMER];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillAddress()
    {
        $this->insert[self::ADDRESS] = $this->row[self::ADDRESS];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillCity()
    {
        $this->insert[self::CITY] = $this->row[self::CITY];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillCityID()
    {
        $this->insert[self::CITY_ID] = 0;

        if(!$this->insert[self::CITY]){
            return $this;
        }

        if($cityID = $this->getCityIDByName($this->insert[self::CITY])){
            $this->insert[self::CITY_ID] = $cityID;
        }

        return $this;
    }


    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillMobile()
    {
        $this->insert[self::PHONE_NUMBER] = preg_replace('/\D/', '', $this->row[self::PHONE_NUMBER]);

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillTotalAmount()
    {
        $this->insert[self::TOTAL_AMOUNT] = $this->getUIntOrError(self::TOTAL_AMOUNT);

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillZipCode()
    {
        $this->insert[self::ZIP_CODE] = $this->row[self::ZIP_CODE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillShipType()
    {
        $this->insert[self::SHIP_TYPE] = $this->row[self::SHIP_TYPE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillProductName()
    {
        $this->insert[self::PRODUCT_NAME] = $this->row[self::PRODUCT_NAME];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillPrice()
    {
        $this->insert[self::PRICE] = $this->row[self::PRICE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillSourceName()
    {
        $this->insert[self::SOURCE] = $this->row[self::SOURCE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillSourceID()
    {
        if ($this->isObd) {
            $this->insert[self::SOURCE_ID] = $this->getSourceIDByName($this->insert[self::SOURCE]);
        } else {
            $sourceName = trim($this->insert[self::SOURCE]);
            $this->insert[self::SOURCE_ID] = $this->getSourceIDByName($sourceName);
        }//end if

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillBundleID()
    {
        $this->insert[self::BUNDLE_ID] = '';

        if($this->insert[self::BUNDLE_NAME]){
            $this->insert[self::BUNDLE_ID] = $this->getBundleIDByName($this->insert[self::BUNDLE_NAME]);
            if($this->getBundleIDByName($this->insert[self::BUNDLE_NAME]) != ''){
                $this->insert[self::BUNDLE_ID] = $this->getBundleIDByName($this->insert[self::BUNDLE_NAME]);
            } else {
                return $this->errorColumn(self::BUNDLE_NAME);
            }
        } else {
            return $this->errorColumn(self::BUNDLE_NAME);
        }
        // https://pm.tuha.vn/issues/14428
        return $this;
    }


    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillOrderTypeID()
    {
        $this->insert[self::ORDER_TYPE_ID] = 1;

        foreach ($this->types as $id => $type) {
            if(preg_match('#' . $this->insert[self::ORDER_TYPE] . '#i', $type)){
                $this->insert[self::ORDER_TYPE_ID] = $id;
                return $this;
            }
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillDeliveredNote()
    {
        $this->insert[self::DELIVERED_NOTE] = $this->row[self::DELIVERED_NOTE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillNote()
    {
        $this->insert[self::NOTE] = $this->row[self::NOTE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillProductCode()
    {
        $this->insert[self::PRODUCT_CODE] = $this->row[self::PRODUCT_CODE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillProducts()
    {
        if(empty($this->insert[self::PRODUCT_CODE])){
            return $this;
        }

        // Parse trọng lượng và giá sản phẩm thành mảng
        $productWeights = $this->parseProductWeights($this->insert[self::WEIGHT]);
        $productPrices = $this->parseProductPrices($this->insert[self::PRICE]);

        // Parse phức hợp product qty-code thành mảng và lọc các sản phẩm thuộc shop
        $productCodes = $this->parseProductCodes($this->insert[self::PRODUCT_CODE]);
        $productCodes = array_filter($productCodes, function($productCode){
            return isset($this->products[$productCode['code']]);
        });

        // Để đảm bảo khớp dữ liệu nên ta sẽ dừng việc xử lí thông tin sản phẩm nếu số lượng phần tử 3 mảng code, weight,
        // price là khác nhau
        $numCode = count($productCodes);
        if($numCode !== count($productWeights) || $numCode !== count($productPrices)){
            return $this->errorColumn(self::WEIGHT, self::PRICE, self::PRODUCT_CODE);
        }

        $products = [];
        $orderPrice = 0;
        foreach ($productCodes as $index => $product) {
            // Dừng việc xử lí thông tin sản phẩm nếu có lỗi giá trị giá và trọng lượng
            if($productWeights[$index] < 0 || $productPrices[$index] < 0){
                return $this->errorColumn(self::WEIGHT, self::PRICE);
            }

            $productCode = $product['code'];
            $products[] = [
                'id' => $this->products[$productCode]['id'],
                'code' => $productCode,
                'name' => $this->products[$productCode]['name'],
                'price' => $productPrices[$index],
                'qty' => $product['qty'],
                'weight' => $productWeights[$index]
            ];

            // Tính thành tiền cho đơn hàng
            $orderPrice += $productPrices[$index] * $product['qty'];
        }

        // Báo lỗi nếu giảm giá âm
        if ($this->insert[self::DISCOUNT] < 0) {
            return $this->errorColumn(self::DISCOUNT);
        }

        // Tự động tính thành tiền cho đơn hàng nếu không điền
        if($this->insert[self::INTO_MONEY] === 0){
            $this->insert[self::INTO_MONEY] = $orderPrice;
        }
        // Điền thành tiền nhưng sai
        elseif($this->insert[self::INTO_MONEY] !== $orderPrice){
            return $this->errorColumn(self::INTO_MONEY);
        }

        // Báo lỗi nếu thành tiền đơn hàng nhỏ hơn giảm giá
        if($orderPrice < $this->insert[self::DISCOUNT]){
            return $this->errorColumn(self::DISCOUNT);
        }

        // Tự động tính tổng tiền cho đơn hàng nếu không điền
        if($this->insert[self::TOTAL_AMOUNT] === 0){
            $this->insert[self::TOTAL_AMOUNT] = $orderPrice - $this->insert[self::DISCOUNT];
        }

        $this->insert[self::PRODUCTS] = $products;

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      string  $rawCodes  The raw codes
     *
     * @return     <type>
     */
    private function parseProductCodes(string $rawCodes)
    {
        $segments = explode(',', $rawCodes);

        return array_reduce($segments, function($results, $rawCode){
            if(preg_match('~^(\d+)\-(.+)~', trim($rawCode), $matches)){
                $results[] = ['qty' => (int) $matches[1], 'code' => trim($matches[2])];
            }

            return $results;
        }, []);
    }

    /**
     * { function_description }
     *
     * @param      string  $rawWeights  The raw codes
     *
     * @return     <type>
     */
    private function parseProductWeights(?string $rawWeights = '')
    {
      return $this->parseIntWithSimpleString($rawWeights);
    }

    /**
     * { function_description }
     *
     * @param      string  $rawPrices  The raw codes
     *
     * @return     <type>
     */
    private function parseProductPrices(?string $rawPrices = '')
    {
        return $this->parseIntWithSimpleString($rawPrices);
    }

    /**
     * { function_description }
     *
     * @param      string  $rawString  The raw string
     *
     * @return     <type>
     */
    private function parseIntWithSimpleString(?string $rawString = '')
    {
        $segments = explode(',', $rawString);

        return array_reduce($segments, function($results, $segment){
            $segment = trim($segment);

            if($segment != ''){
                $results[] = is_numeric($segment) ? $segment : 0;
            }

            return $results;
        }, []);
    }

    /**
     * Gets the unsigned integer or error.
     *
     * @param      int   $field  The field
     */
    private function getUIntOrError(int $field)
    {
        if(!isset($this->row[$field])){
            return 0;
        }

        if(!is_numeric($this->row[$field])){
            return $this->errorColumn($field);
        }

        $value = intval($this->row[$field]);
        if($value < 0 || $value != $this->row[$field]){
            return $this->errorColumn($field);
        }

        return $value;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillWeight()
    {
        $this->insert[self::WEIGHT] = $this->row[self::WEIGHT];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillIntoMoney()
    {
        $this->insert[self::INTO_MONEY] = $this->getUIntOrError(self::INTO_MONEY);

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillDiscount()
    {
        $this->insert[self::DISCOUNT] = $this->getUIntOrError(self::DISCOUNT);

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillOrderType()
    {
        $this->insert[self::ORDER_TYPE] = $this->row[self::ORDER_TYPE];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillBundle()
    {
        $this->insert[self::BUNDLE_NAME] = $this->row[self::BUNDLE_NAME];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillEmail()
    {
        $this->insert[self::EMAIL] = $this->row[self::EMAIL];

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function fillNote2()
    {
        $this->insert[self::NOTE2] = $this->row[self::NOTE2];

        return $this;
    }

    /**
     * Gets the user id by username.
     *
     * @param      string  $userName  The user name
     *
     * @return     <type>  The user id by username.
     */
    private function getUserIDByUsername(string $userName)
    {
        return isset($this->users[$userName]) ? $this->users[$userName] : 0;
    }

    /**
     * Gets the city id by name.
     *
     * @param      string  $cityName  The city name
     *
     * @return     <type>  The city id by name.
     */
    private function getCityIDByName(string $cityName)
    {
        foreach($this->cities as $name => $id){
            if(preg_match('#^' . $cityName . '$#i', $name)){
                return $id;
            }
        }

        return 0;
    }

    /**
     * Điền ngày vào cột
     *
     * @param      int     $field     The field
     * @param      string  $default   The default
     *
     * @return     self    The date.
     */
    private function fillDate(int $field, string $default)
    {
        $this->insert[$field] = $default;

        if(!$rawDate = $this->row[$field]){
            return $this;
        }

        $dateString = $this->parseDateString($rawDate);
        $date = strtotime($dateString);

        if(!$date || $date > time()){
            return $this->errorColumn($field);
        }
        $this->insert[$field] = date('Y-m-d H:i:s', $date);
        return $this;
    }

    /**
     * Điền id người dùng vào cột
     *
     * @param      int   $field     The field
     * @param      int   $default   The default
     *
     * @return     self
     */
    private function fillUser(int $field, int $default)
    {
        $this->insert[$field] = $default;

        if(!$username = $this->row[$field]){
            return $this;
        }
        if($userID = $this->getUserIDByUsername($username)){
            $this->insert[$field] = $userID;
        }else{
            return $this->errorColumn($field);
        }

        return $this;
    }

    /**
     * Gets the bundle id by name.
     *
     * @param      string  $bundleName  The bundle name
     *
     * @return     <type>  The bundle id by name.
     */
    private function getBundleIDByName(string $bundleName)
    {
        $bundleName = mb_strtolower($bundleName);

        return isset($this->bundles[$bundleName]) ? $this->bundles[$bundleName] : '';
    }

    /**
     * Gets the source id byname.
     *
     * @param      string  $sourceName  The source name
     */
    private function getSourceIDByName(?string $sourceName = '')
    {
        $sourceName = mb_strtolower($sourceName);
        return isset($this->sources[$sourceName]) ? $this->sources[$sourceName] : 0;
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    private function currentTime()
    {
        static $time = null;

        return is_null($time) ? ($time = date('Y-m-d H:i:s')) : $time;
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    private function current()
    {
        static $time = null;

        return is_null($time) ? ($time = date(' H:i:s')) : $time;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $string  The string
     *
     * @return     <type>
     */
    private function parseDateString($string)
    {
        $patterns = [
            '~[^\d\/\-\s:]+~', // tim cac ki tu khong phai so, dau /, -
            '~/+|-+~',       // tim cac ki tu / hoac -
            '~(\d)\s+(\d)~'
        ];
        $replaces = ['', '-', '$1 $2'];
        $rawDate = trim(preg_replace($patterns, $replaces, $string));

        switch (true) {
            // YY-mm-dd
            case preg_match('#^\d{4}-\d{1,2}-\d{1,2}$#', $rawDate):
                return $rawDate . $this->current();

            // dd-mm-YY
            case preg_match('#^\d{1,2}-\d{1,2}-\d{4}$#', $rawDate):
                return $rawDate . $this->current();

            // YY-mm-dd H:i:s
            case preg_match('#^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$#', $rawDate):
                return $rawDate;;

            // dd-mm-YY H:i:s
            case preg_match('#^\d{1,2}-\d{1,2}-\d{4} \d{1,2}:\d{1,2}:\d{1,2}$#', $rawDate):
                return $rawDate;

            default:
                return null;
        }
    }

    /**
     * { function_description }
     */
    private function validateByStatus()
    {
        $toLevel = $this->statuses[$this->statusID]['level'];

        $confirmedLevel = $this->statuses[XAC_NHAN]['level'];
        if($toLevel >= $confirmedLevel){
            $this->validateImportLevelGtConfirmed();
        }

        $successedLevel = $this->statuses[THANH_CONG]['level'];
        if($toLevel > $successedLevel){
            $this->validateImportLevelGtSuccessed();
        }

        return $this;
    }

    /**
     * Xác thực người và ngày ở các trạng thái 2 trường ngày và người ở trạng thái cụ thể phải điền đủ hoặc không điền
     *
     * @return     self
     */
    private function validateUserAndDate()
    {
        $this->_validateUserAndDate(self::ASSIGNED_USER, self::ASSIGNED_DATE, self::ASSIGNED_INFORMATION_ERROR);
        $this->_validateUserAndDate(self::CREATED_USER, self::CREATED_DATE, self::CREATED_INFORMATION_ERROR);
        $this->_validateUserAndDate(self::CONFIRMED_USER, self::CONFIRMED_DATE, self::CONFIRMED_INFORMATION_ERROR, XAC_NHAN);
        $this->_validateUserAndDate(self::DELIVERED_USER, self::DELIVERED_DATE, self::DELIVERED_INFORMATION_ERROR, CHUYEN_HANG);
        $this->_validateUserAndDate(self::SUCCESSED_USER, self::SUCCESSED_DATE, self::SUCCESSED_INFORMATION_ERROR, THANH_CONG);
        $this->_validateUserAndDate(self::ACCOUNTING_USER, self::ACCOUNTING_DATE, self::ACCOUNTING_INFORMATION_ERROR, KE_TOAN);

        return $this;
    }

    /**
     * Xác thực người và ngày ở trạng thái cụ thể 2 trường ngày và người phải điền đủ hoặc không điền
     * Nếu tham số statusID khác null thì sẽ kiểm tra thêm điều kiện người và ngày tại trạng thái đó có rỗng hay không
     *
     * @param      int     $userCol    The user id
     * @param      string  $datetimeCol  The datetime
     * @param      int     $error     The error
     * @param      int     $statusID  The status id
     *
     * @return     self
     */
    private function _validateUserAndDate(int $userCol, int $datetimeCol, int $error, int $statusID = null)
    {
        // Tồn tại user nhưng ngày không có
        if($this->insert[$userCol] && (!$this->insert[$datetimeCol] || $this->insert[$datetimeCol] === self::DEFAULT_DB_DATE_TIME)){
            return $this->errorColumnWithErrorCode($error, $userCol, $datetimeCol);
        }

        // Tồn tại ngày nhưng không có user
        if(!$this->insert[$userCol] && $this->isNotEmptyDate($this->insert[$datetimeCol])){
            return $this->errorColumnWithErrorCode($error, $userCol, $datetimeCol);
        }

        // Kiểm tra ngày và người tại trạng thái chỉ định xem nó có được điền đầy đủ hay không
        if(!is_null($statusID) && $this->statusID === $statusID && (!$this->insert[$userCol] || !$this->insert[$datetimeCol] || $this->insert[$datetimeCol] === self::DEFAULT_DB_DATE_TIME)){
            return $this->errorColumnWithErrorCode($error, $userCol, $datetimeCol);
        }

        return $this;
    }

    /**
     * Determines whether the specified datetime is not empty date.
     *
     * @param      string  $datetime  The datetime
     *
     * @return     bool    True if the specified datetime is not empty date, False otherwise.
     */
    private function isNotEmptyDate(string $datetime = null)
    {
        return $datetime && $datetime !== self::DEFAULT_DB_DATE_TIME;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function validateDateTime()
    {
        $createdTime = strtotime($this->insert[self::CREATED_DATE]);
        $assignedTime = strtotime($this->insert[self::ASSIGNED_DATE]);
        $accountingTime = strtotime($this->insert[self::ACCOUNTING_DATE]);
        $confirmedTime = strtotime($this->insert[self::CONFIRMED_DATE]);
        $deliveredTime = strtotime($this->insert[self::DELIVERED_DATE]);
        $successedTime = strtotime($this->insert[self::SUCCESSED_DATE]);

        // Ta sẽ cần kiểm tra ngày phía vế phải là không rỗng vì có thể xảy ra trường hợp ngày bên vế phải không điền

        ///////////////////////////// Ngay tao
        // ngày tạo lớn hơn ngày gán
        if($createdTime > $assignedTime && $this->isNotEmptyDate($this->insert[self::ASSIGNED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_TAO_LON_HON_NGAY_CHIA, self::CREATED_DATE, self::ASSIGNED_DATE);
        }

        // Ngay tao lon hon ngay ke toan mac dinh
        if($createdTime > $accountingTime && $this->isNotEmptyDate($this->insert[self::ACCOUNTING_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_TAO_LON_HON_NGAY_KE_TOAN, self::CREATED_DATE, self::ACCOUNTING_DATE);
        }

        // Ngay tao lon hon ngay xac nhan
        if($createdTime > $confirmedTime && $this->isNotEmptyDate($this->insert[self::CONFIRMED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_TAO_LON_HON_NGAY_XAC_NHAN, self::CREATED_DATE, self::CONFIRMED_DATE);
        }

        // Ngay tao lon hon ngay chuyen hàng
        if($createdTime > $deliveredTime && $this->isNotEmptyDate($this->insert[self::DELIVERED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_TAO_LON_HON_NGAY_CHUYEN_HANG, self::CREATED_DATE, self::DELIVERED_DATE);
        }

        // Ngay tao lon hon ngay thành công
        if($createdTime > $successedTime && $this->isNotEmptyDate($this->insert[self::SUCCESSED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_TAO_LON_HON_NGAY_THANH_CONG, self::CREATED_DATE, self::SUCCESSED_DATE);
        }

        ////////////////////////////// Ke toan, xac nhan
        // Ngay xac nhan lon hon ngay ke toan
        if($confirmedTime > $accountingTime && $this->isNotEmptyDate($this->insert[self::ACCOUNTING_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_XAC_NHAN_LON_HON_NGAY_KE_TOAN, self::ACCOUNTING_DATE, self::CONFIRMED_DATE);
        }

        // Ngay ke toan lon hon ngay chuyen hàng
        if($accountingTime > $deliveredTime && $this->isNotEmptyDate($this->insert[self::DELIVERED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_KE_TOAN_LON_HON_NGAY_CHUYEN_HANG, self::ACCOUNTING_DATE, self::DELIVERED_DATE);
        }

        // Ngay xac nhan lon hon ngay chuyen hàng
        if($confirmedTime > $deliveredTime && $this->isNotEmptyDate($this->insert[self::DELIVERED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_XAC_NHAN_LON_HON_NGAY_CHUYEN_HANG, self::CONFIRMED_DATE, self::DELIVERED_DATE);
        }

        // Ngay ke toan lon hon ngay thành công
        if($accountingTime > $successedTime && $this->isNotEmptyDate($this->insert[self::SUCCESSED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_KE_TOAN_LON_HON_NGAY_THANH_CONG, self::ACCOUNTING_DATE, self::SUCCESSED_DATE);
        }

        // Ngay xac nhan lon hon ngay thành công
        if($confirmedTime > $successedTime && $this->isNotEmptyDate($this->insert[self::SUCCESSED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_XAC_NHAN_LON_HON_NGAY_THANH_CONG, self::CONFIRMED_DATE, self::SUCCESSED_DATE);
        }

        ////////////////////////////// chuyen hang
        // Ngay chuyen hang lon hon ngay thành công
        if($deliveredTime > $successedTime && $this->isNotEmptyDate($this->insert[self::SUCCESSED_DATE])){
            $this->errorColumnWithErrorCode(self::NGAY_CHUYEN_HANG_LON_HON_NGAY_THANH_CONG, self::DELIVERED_DATE, self::SUCCESSED_DATE);
        }

        return $this;
    }

    /**
     * Validate khi import với trạng thái từ xác nhận trở lên
     * Rq: Đơn hàng import từ trạng thái xác nhận chốt đơn trở lên (đóng hàng, chuyển hàng,
     * chuyển hoàn,…) phải điền thông tin cột người xác nhận và ngày xác nhận.
     */
    private function validateImportLevelGtConfirmed()
    {
        if(!$this->insert[self::CONFIRMED_USER] || !$this->insert[self::CONFIRMED_DATE] || $this->insert[self::CONFIRMED_DATE] == self::DEFAULT_DB_DATE_TIME){
            $this->errorColumnWithErrorCode(self::UNCONFIRMED_ORDER, self::CONFIRMED_USER, self::CONFIRMED_DATE);
        }

        return $this;
    }

    /**
     * Validate thông tin khi import với trạng thái lớn hơn thành công
     * Rq: Đơn hàng import từ trạng thái thành công trở lên (thành công, đã thu tiền,…) phải điền
     * thông tin cột người xác nhận - ngày xác nhận, người thành công và ngày thành công.
     */
    private function validateImportLevelGtSuccessed()
    {
        if(!$this->insert[self::SUCCESSED_USER] || !$this->insert[self::SUCCESSED_DATE] || $this->insert[self::SUCCESSED_DATE] == self::DEFAULT_DB_DATE_TIME){
            $this->errorColumnWithErrorCode(self::UNSUCCESSED_ORDER, self::SUCCESSED_USER, self::SUCCESSED_DATE);
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    private function validateCustomer()
    {
        if(strlen($this->insert[self::CUSTOMER_NAME] > 125)){
            $this->errorColumn(self::CUSTOMER_NAME);
        }

        if($this->insert[self::PHONE_NUMBER] === ''){
            $this->errorColumn(self::PHONE_NUMBER);
        }
        // https://pm.tuha.vn/issues/14428
        return $this;
    }

    private function validatePhoneNumber()
    {
        $phone_number = $this->insert[self::PHONE_NUMBER];
        if ($this->isAccountTestValidatePhone() && !$this->validateTelephoneNumber($phone_number)) {
            $this->errorColumn(self::PHONE_NUMBER);
        }//end if

        return $this;
    }

    private function validateSource()
    {
        if (!$this->isObd) {
            return $this;
        }//end if

        $sourceId = $this->insert[self::SOURCE_ID];
        if (!$sourceId) {
            $this->errorColumn(self::SOURCE);
        }//end if

        return $this;
    }

    /**
     * validateTelephoneNumber function
     *
     * @param string $telephone_number
     * @return boolean
     */
    function validateTelephoneNumber(string $telephone_number): bool
    {
        $patterns = AdminOrders::$mobile_regex_patterns;
        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $telephone_number)) {
                return true;
            }//end if
        }//end foreach

        return false;
    }

    function isAccountTestValidatePhone(): bool
    {
        $group_id = Session::get('group_id');
        if (SYSTEM_PHONE_VALIDATOR || in_array($group_id, ACCOUNT_TEST_VALIDATE_PHONE)) {
            return true;
        }//end if

        return false;
    }

    /**
     * { function_description }
     *
     * @param      int...  $columnIndex  The column index
     *
     * @return     self
     */
    private function errorColumn(int ...$columnIndex)
    {
        array_push($this->errorColumns, ...$columnIndex);

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      int     $errorCode    The error code
     * @param      int...  $columnIndex  The column index
     *
     * @return     self
     */
    private function errorColumnWithErrorCode(int $errorCode, int ...$columnIndex)
    {
        $this->errors[] = $errorCode;
        array_push($this->errorColumns, ...$columnIndex);

        return $this;
    }

    /**
     * Gets the order field.
     *
     * @return     array  The order field.
     */
    public function getOrderField()
    {
        return [
            'code'                    => $this->insert[self::CONFIRM_CODE],
            'customer_name'           => $this->insert[self::CUSTOMER_NAME],
            'mobile'                  => $this->insert[self::PHONE_NUMBER],
            'status_id'               => $this->statusID,
            'user_assigned'           => $this->insert[self::ASSIGNED_USER],
            'assigned'                => $this->insert[self::ASSIGNED_DATE],
            'group_id'                => $this->groupID,
            'master_group_id'         => $this->masterGroupID,
            'import_acc_id'           => $this->currentUserID,
            'import_time'             => time(),
            'telco_code'              => $this->insert[self::ZIP_CODE],
            'note2'                   => $this->insert[self::NOTE2],
            'type'                    => $this->insert[self::ORDER_TYPE_ID],
            'total_price'             => $this->insert[self::TOTAL_AMOUNT],
            'price'                   => $this->insert[self::INTO_MONEY],
            'last_edited_account_id'  => '',
            'user_confirmed'          => $this->insert[self::CONFIRMED_USER],
            'confirmed'               => $this->insert[self::CONFIRMED_DATE],
            'user_created'            => $this->insert[self::CREATED_USER],
            'created'                 => $this->insert[self::CREATED_DATE],
            'city'                    => $this->insert[self::CITY],
            'city_id'                 => $this->insert[self::CITY_ID],
            'address'                 => $this->insert[self::ADDRESS],
            'postal_code'             => $this->insert[self::ZIP_CODE],
            'deliver'                 => '',
            'shipping_note'           => $this->insert[self::DELIVERED_NOTE],
            'delivered'               => $this->insert[self::DELIVERED_DATE],
            'user_delivered'          => $this->insert[self::DELIVERED_USER],
            'note1'                   => $this->insert[self::NOTE],
            'fb_page_id'              => $this->insert[self::FB_PAGE_ID],
            'fb_post_id'              => $this->insert[self::FB_POST_ID],
            'fb_customer_id'          => $this->insert[self::FB_CUSTOMER],
            'source_id'               => $this->insert[self::SOURCE_ID],
            'bundle_id'               => $this->insert[self::BUNDLE_ID],
            'source_name'             => $this->insert[self::SOURCE],
            'discount_price'          => $this->insert[self::DISCOUNT]
        ];
    }

    public function getStatuses()
    {
        return [
            'status_id'     => $this->statusID,
            'level'         => $this->statuses[$this->statusID]['level'],
            'no_revenue'    => $this->statuses[$this->statusID]['no_revenue']
        ];
    }

    /**
     * Gets the customer fields.
     */
    public function getCustomerFields()
    {
        return [
            'name'          => $this->insert[self::CUSTOMER_NAME],
            'mobile'        => $this->insert[self::PHONE_NUMBER],
            'zone_id'       => $this->insert[self::CITY_ID],
            'address'       => $this->insert[self::ADDRESS],
            'group_id'      => $this->groupID,
            'time'          => time(),
            'email'         => $this->insert[self::EMAIL],
            'creator_id'    => $this->currentUserID,
            'user_id'       => 0,
        ];
    }

    /**
     * Gets the order extra field.
     * Chú ý, thiếu field order_id
     *
     * @return     array  The order extra field.
     */
    public function getOrderExtraField()
    {
        $confused_user_confirmed = $this->statusID == KHACH_PHAN_VAN ? Session::get('user_data')['user_id'] : 0;
        $confused_confirmed  = $this->statusID == KHACH_PHAN_VAN ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00';
        $not_answer_phone_user_confirmed = $this->statusID == KHACH_KHONG_NGHE_MAY ? Session::get('user_data')['user_id'] : 0;
        $not_answer_phone_confirmed  = $this->statusID == KHACH_KHONG_NGHE_MAY ? date('Y-m-d H:i:s') : '0000-00-00 00:00:00';
        return [
            'group_id'=> $this->groupID,
            'accounting_confirmed'                  => $this->insert[self::ACCOUNTING_DATE],
            'accounting_user_confirmed'             => $this->insert[self::ACCOUNTING_USER],
            'update_successed_user'                 => $this->insert[self::SUCCESSED_USER],
            'update_successed_time'                 => $this->insert[self::SUCCESSED_DATE],

            'update_paid_user'                      => $this->insert[self::PAIDED_USER],
            'update_paid_time'                      => $this->insert[self::PAIDED_DATE],
            'update_returned_user'                  => $this->insert[self::REFUND_USER],
            'update_returned_time'                  => $this->insert[self::REFUND_DATE],
            'update_returned_to_warehouse_user_id'  => $this->insert[self::RETURNED_USER],
            'update_returned_to_warehouse_time'     => $this->insert[self::RETURNED_DATE],
            
            'confused_user_confirmed'  => $confused_user_confirmed,
            'confused_confirmed'     => $confused_confirmed,
            'not_answer_phone_user_confirmed'  => $not_answer_phone_user_confirmed,
            'not_answer_phone_confirmed'     => $not_answer_phone_confirmed
        ];
    }

    /**
     * Gets the result.
     *
     * @return     <type>  The result.
     */
    public function getResult()
    {
        return [
            'data' => $this->insert,
            'errors' => $this->errors,
            'errorColumns' => $this->errorColumns
        ];
    }

    /**
     * Gets all fields.
     *
     * @return     <type>  All fields.
     */
    public function getAllFields()
    {
        return $this->insert;
    }

    /**
     * Gets the errors.
     *
     * @return     <type>  The errors.
     */
    public function getErrors()
    {
        return array_unique($this->errors);
    }

    /**
     * Gets the error columns.
     *
     * @return     <type>  The error columns.
     */
    public function getErrorColumns()
    {
        return array_unique($this->errorColumns);
    }

    /**
     * Gets the products.
     *
     * @return     <type>  The products.
     */
    public function getProductsField()
    {
        return $this->insert[ImportExcelValidator::PRODUCTS];
    }
}
