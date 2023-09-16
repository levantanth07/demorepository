<?php

class CostDeclaration extends Form
{   
    const KEY = 'CostDeclaration';
    
    public $map = [];

    private static $groupID;

    private $requestData = [];
    private $currentTime;
    private $costFields = [
        'gia_von' => 'Giá vốn',
        'chi_phi_luong' => 'Chi phí Lương',
        'cuoc_cod' => 'Cước COD',
        'cuoc_dt' => 'Cước ĐT',
        'cuoc_hoan' => 'Cước hoàn',
        'cuoc_khac' => 'Cước khác',
        'cuoc_tien_nha' => 'Cước tiền nhà',
    ];

    public function __construct()
    {   
        Form::Form('CostDeclaration');
        self::$groupID = Session::get('group_id');

        $this->currentTime = date('Y-m-d H:i:s');

        // Kế toán, Quản lý kế toán, admin, owner shop
        if(!$this->canAccess()){
            URL::access_denied();
        }
    }

    /**
     * Determines ability to access.
     *
     * @return     bool  True if able to access, False otherwise.
     */
    private function canAccess()
    {
        return Session::get('admin_group') 
            || is_group_owner() 
            || check_user_privilege('KE_TOAN') 
            || check_user_privilege('admin_ketoan');
    }

    /**
     * Called on draw.
     */
    public function on_draw()
    {   
        if($this->isAddRequest()){
            return $this->renderAddPage();
        }

        if($this->isEditRequest()){
            return $this->renderEditPage();
        }

        if($this->isDeleteRequest()){
            return $this->delete();
        }

        return $this->renderListPage();
    }

    /**
     * { function_description }
     */
    private function renderAddPage()
    {   
        $this->map['month'] = URL::getUInt('month', date('m'));
        $this->map['year'] = URL::getUInt('year', date('Y'));

        $this->parse_layout('cost_declaration_add', $this->map);
    }

    /**
     * { function_description }
     */
    private function renderEditPage()
    {   
        if(!$row = $this->getByID(URL::getUInt('id'))){
            return $this->render_error('Không tìm thấy khai báo này');
        }

        if(!URL::get('form_block_id')){
            foreach ($row as $key => $value) {
                $_REQUEST[$key] = $value;
            }

        }
        
        $time = strtotime($row['time']);
        $this->map['month'] = date('m', $time);
        $this->map['year'] = date('Y', $time);

        $this->parse_layout('cost_declaration_edit', $this->map);
    }


    /**
     * { function_description }
     */
    private function delete()
    {   
        if(!$row = $this->getByID(URL::getUInt('id'))){
            return $this->render_error('Không tìm thấy khai báo này');
        }

        if(!DB::delete('cost_declaration', 'id=' . $row['id'])){
            return $this->set_flash_message($this->key('delete_fail'), 'Xóa thất bại !');
        }
        
        $this->set_flash_message($this->key('delete_success'), 'Xóa thành công !');
        
        return URL::redirect_current(['do'=>'cost_declaration', 'act' => 'list']);
    }

    /**
     * { function_description }
     */
    private function renderListPage()
    {   
        $this->map['rows'] = $this->prepareList();

        $this->parse_layout('cost_declaration_list', $this->map);
    }

    /**
     * Called on submit.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function on_submit()
    {   
        $this->isAddRequest() && $this->handleAddRequest();
        $this->isEditRequest() && $this->handleEditRequest();
    }

    /**
     * Determines if add request.
     *
     * @return     bool  True if add request, False otherwise.
     */
    private function isAddRequest()
    {
        return URL::get('act') === 'add';
    }

    /**
     * Determines if edit request.
     *
     * @return     bool  True if edit request, False otherwise.
     */
    private function isEditRequest()
    {
        return URL::get('act') === 'edit';
    }

    /**
     * Determines if delete request.
     *
     * @return     bool  True if delete request, False otherwise.
     */
    private function isDeleteRequest()
    {
        return URL::get('act') === 'delete';
    }

    /**
     * { function_description }
     */
    private function handleAddRequest()
    {   
        if(!$this->mapTime()){
            return $this->set_flash_message($this->key('time'), 'Thời gian không hợp lệ');
        }

        if(!$this->mapCost()){
            return $this->addFlashMessageCostError();
        }

        if($this->existsTime()){
            return $this->set_flash_message($this->key('time'), 'Đã tồn tại khai báo cho thời gian này');
        }

        if(!$this->insert()){ 
            return $this->set_flash_message($this->key('insert_fail'), 'Thêm thất bại !');
        }

        $this->set_flash_message($this->key('insert_success'), 'Thêm thành công !');

        return URL::redirect_current(['do'=>'cost_declaration', 'act' => 'list']);
    }

    /**
     * { function_description }
     */
    private function handleEditRequest()
    {   
        $this->fillID();

        if(!$this->mapTime()){
            return $this->set_flash_message($this->key('time'), 'Thời gian không hợp lệ');
        }

        if(!$this->mapCost()){
            return $this->addFlashMessageCostError();
        }

        if($this->existsTime($this->requestData['id'])){
            return $this->set_flash_message($this->key('time'), 'Đã tồn tại khai báo cho thời gian này');
        }

        if(!$this->update()){ 
            return $this->set_flash_message($this->key('update_fail'), 'Cập nhật thất bại !');
        }

        $this->set_flash_message($this->key('update_success'), 'Cập nhật thành công !');

        return URL::redirect_current(['do'=>'cost_declaration', 'act' => 'list']);
    }

    /**
     * { function_description }
     *
     * @param      string  $name   The name
     */
    private function key(string $name)
    {
        return self::KEY . '_' . $name;
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function mapTime()
    {
        $month = URL::getUInt('month');
        $year = URL::getUInt('year');

        if(strtotime($year . '-' . $month . '-1') < strtotime(date('Y') . '-' . date('m') . '-1')){
            return false;
        }

        return $this->requestData['time'] = sprintf('%s-%s-1', $year, $month);
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function mapCost()
    {   
        return array_reduce(array_keys($this->costFields), function($valid, $fieldName){
            
            $fieldVal = URL::getFloat($fieldName);
            if($fieldVal < 0){
                return $valid = 0;
            }
            
            $this->requestData[$fieldName] = $fieldVal;
            
            return $valid;
        }, 1);
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function fillGroup()
    {
        return $this->requestData['group_id'] = self::$groupID;
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function fillID()
    {
        return $this->requestData['id'] = URL::getUInt('id');
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function fillCreated()
    {
        $this->requestData['created_at'] = $this->currentTime;
        $this->requestData['created_by'] = get_user_id();

        return true;
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function fillUpdated()
    {
        $this->requestData['updated_at'] = $this->currentTime;
        $this->requestData['updated_by'] = get_user_id();

        return true;
    }

    /**
     * { function_description }
     */
    private function existsTime(int $withoutID = null)
    {
        return DB::fetch('
            SELECT `id` 
            FROM `cost_declaration` 
            WHERE 
                `time` = "' . $this->requestData['time'] . '"' 
                . ' AND group_id = ' . self::$groupID
                . ($withoutID ? ' AND id != ' . $withoutID : '') 
                . ' LIMIT 1');
    }

    /**
     * Adds a flash message cost error.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function addFlashMessageCostError()
    {
        return array_walk($this->costFields, function($key, $val){
            $this->set_flash_message($this->key($key), $val . ' không hợp lệ.');
        });
    }

    /**
     * Cập nhật
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function update()
    {
        $this->fillUpdated();

        return DB::update('cost_declaration', $this->requestData, 'id=' . $this->requestData['id']);
    } 

    /**
     * Thêm mới
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insert()
    {   
        $this->fillGroup();
        $this->fillCreated();

        return DB::insert('cost_declaration', $this->requestData);
    }

    /**
     * { function_description }
     */
    private function prepareList()
    {   
        $allUsers = $this->allUsers();

        return array_map(function($row) use($allUsers) {
            $row['created_name'] = $allUsers[$row['created_by']]['username'];
            $row['updated_name'] = $row['updated_by'] ? $allUsers[$row['updated_by']]['username'] : '';

            return $row;
        }, $this->fetchList());
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function allUsers()
    {
        return DB::fetch_all('
            SELECT `id`, `username`
            FROM users
            WHERE 
                group_id = ' . self::$groupID
        );
    }

    /**
     * Fetches a list.
     *
     * @return   array  The list.
     */
    private function fetchList()
    {
        return DB::fetch_all_array('
            SELECT * 
            FROM cost_declaration
            WHERE 
                group_id = ' . self::$groupID . '
            ORDER BY id DESC'
        );
    }

    /**
     * Gets the by id.
     *
     * @param      int     $ID     { parameter_description }
     *
     * @return     <type>  The by id.
     */
    private function getByID(int $ID)
    {
        return DB::fetch('
            SELECT * 
            FROM cost_declaration
            WHERE 
                group_id = ' . self::$groupID . '
                AND id = ' . $ID . '
                AND time >= ' . date('Y-m-1')
        );
    }

    /**
     * Gets the cost fields.
     *
     * @return     <type>  The cost fields.
     */
    public function renderErrors()
    {
        $this->renderErrorByKey('update_fail');
        $this->renderErrorByKey('delete_fail');
        $this->renderErrorByKey('insert_fail');
        $this->renderErrorByKey('time');
        
        array_map(function($key){
            $this->renderErrorByKey($key);
        }, array_keys($this->costFields));
    }

    /**
     * { function_description }
     */
    public function renderSuccess()
    {
        $this->renderSuccessByKey('update_success');
        $this->renderSuccessByKey('delete_success');
        $this->renderSuccessByKey('insert_success');
    }

    /**
     * { function_description }
     *
     * @param      string  $key    The key
     */
    private function renderErrorByKey(string $key)
    {
        FORM::has_flash_message($this->key($key)) && FORM::draw_flash_message_error($this->key($key));
    }

    /**
     * { function_description }
     *
     * @param      string  $key    The key
     */
    private function renderSuccessByKey(string $key)
    {
        FORM::has_flash_message($this->key($key)) && FORM::draw_flash_message_success($this->key($key));
    }
}