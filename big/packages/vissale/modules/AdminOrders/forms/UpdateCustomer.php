<?php

class UpdateCustomer
{   
    private $orderID;
    private $userID;
    private $order;
    private $savedCustomer;
    private $customerID;

    public function __construct(int $orderID, int $userID = 0)
    {
        $this->orderID = $orderID;
        $this->userID = $userID;

        $this->exec();
    }

    /**
     * { function_description }
     *
     * @throws     Exception  (description)
     */
    private function exec()
    {
        if(!$this->getOrder()){
            throw new Exception('Không tìm thấy đơn hàng chỉ định.');
        }

        $this->mapRequest();

        // Tồn tại mã KH ở đơn hàng
        if($this->order['customer_id'] = intval($this->order['customer_id'])){

            $this->customerID = $this->order['customer_id'];

            // Nếu tồn tại khách hàng có id này thì cập nhật KH
            if($this->getCustomerByID()){
                return $this->updateCustomer();
            }

            // Ngược lại thì insert khách hàng mới và lấy ID để cập nhật vào đơn hàng
            return $this->updateOrderCustomerID($this->insertCustomer());
        }

        // Không tồn tại mã khách hàng ở đơn hàng 
        // Tồn tại khách hàng theo SDT 
        if($this->getCustomerByPhone()){
            // Cập nhật mã khách hàng ở đơn hàng theo mã khách hàng lấy được theo SDT
            $this->updateOrderCustomerID($this->savedCustomer['id']);
            
            return $this->updateCustomer();
        }

        // Không tồn tại mã khách hàng ở đơn hàng 
        // Không tồn tại khách hàng theo SDT 
        // Tạo khách hàng mới và cập nhật mã KH của nó vào đơn hàng
        return $this->updateOrderCustomerID($this->insertCustomer());
    }

    /**
     * { function_description }
     *
     * @param      <type>  $customerID  The customer id
     */
    private function updateOrderCustomerID(int $customerID)
    {
        return DB::update('orders', ['customer_id' => $customerID], 'id='.$this->orderID);
    }

    /**
     * Gets the order.
     *
     * @return     <type>  The order.
     */
    private function getOrder()
    {   
        $sql = 'SELECT id,group_id,status_id,user_confirmed,customer_id 
                FROM orders 
                WHERE id = ' . $this->orderID;
        
        return $this->order = DB::fetch($sql);
    }

    /**
     * Lấy thông tin submit từ request
     */
    private function mapRequest()
    {
        $this->customer = [
            'name' => DB::escape(trim(Url::get('customer_name'))),
            'mobile' => str_replace(['(',')','-'],'.', trim(Url::get('mobile'))),
            'email' =>  DataFilter::removeXSSinHtml(DB::escape(trim(Url::get('email')))),
            'crm_group_id'=>Url::iget('customer_group'),
            'phone' =>  DB::escape(str_replace(['(',')','-'],'.', trim(Url::get('mobile2')))),
            'address' =>  DB::escape(Url::get('address')),
            'zone_id' => Url::iget('city_id'),  
            'group_id' => $this->order['group_id'],
            'user_id'=> (int) $this->order['user_confirmed'],
            'birth_date'=> Url::get('birth_date') ? Date_Time::to_sql_date(Url::get('birth_date')) : AdminOrders::$date_init_value,
            'source_id'=> (int) Url::get('source_id'),
            'gender'=> (int) Url::get('gender')
        ];
    }

    /**
     * Gets the customer.
     *
     * @param      <type>  $mobile  The mobile
     *
     * @return     <type>  The customer.
     */
    private function getCustomerByPhone()
    {
        return $this->savedCustomer = DB::fetch('SELECT id,name,used_phones,mobile from crm_customer 
                         WHERE mobile="' . DB::escape($this->customer['mobile']).'" 
                         AND group_id=' . $this->order['group_id']);
    }

    /**
     * Gets the customer.
     *
     * @param      <type>  $mobile  The mobile
     *
     * @return     <type>  The customer.
     */
    private function getCustomerByID()
    {
        return $this->savedCustomer = DB::fetch('SELECT id,name,used_phones,mobile from crm_customer 
                         WHERE id="' . $this->customerID.'" 
                         AND group_id=' . $this->order['group_id']);
    }

    /**
     * { function_description }
     */
    private function updateCustomer()
    {
        if($this->userID){
            $this->customer['user_id'] = $this->userID;
        }

        if($this->isMobileChanged() && !$this->isSaveMobileChanged()){
            $this->customer['used_phones'] = ltrim($this->savedCustomer['used_phones'] . ',' . $this->savedCustomer['mobile'], ',');
        }

        DB::update('crm_customer',$this->customer,'id='.$this->customerID);
    }

    /**
     * Determines if mobile changed.
     *
     * @return     bool  True if mobile changed, False otherwise.
     */
    private function isMobileChanged()
    {
        return $this->customer['mobile'] != $this->savedCustomer['mobile'];
    }

    /**
     * Determines if save mobile changed.
     *
     * @return     bool  True if save mobile changed, False otherwise.
     */
    private function isSaveMobileChanged()
    {
        return preg_match('#'.$this->savedCustomer['mobile'].'#', $this->savedCustomer['used_phones']);
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertCustomer()
    {
        $this->customer['time'] = time();
        $this->customer['creator_id'] = get_user_id();
        
        return $this->customerID = DB::insert('crm_customer',$this->customer);
    }
}