<?php

class ManagerColumnsExportExcel extends Form
{   
    const KEY = 'export_excel_order_columns';

    private static $options = [];

    public $map = [];

    private static $groupID;

    private static $booleanMap = ['true' => true, 1 => true, 'false' => false, 0 => false];

    public function __construct()
    {   
        Form::Form('ManagerColumnsExportExcel');
        require_once ROOT_PATH . 'packages/core/includes/common/RequestHandler.php';

        self::$groupID = Session::get('group_id');

        if(!AdminUserInfo::$is_admin_shop){
            self::throwBadRequest();
        }

    }

    /**
     * Called on submit.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function on_submit()
    {  
        // Validate data
        $data = URL::get('data');
        if(empty($data)){
            RequestHandler::sendJsonError('MIN_COLUMN_1');
        }

        $data = [
            'username' => Session::get('user_id'),
            'data' => $data
        ];

        return self::getOption(self::$groupID) 
            ? $this->update(self::$groupID, $data) 
            : $this->insert(self::$groupID, $data);
    }

    /**
     * Cập nhật
     *
     * @param      int    $groupID  The group id
     * @param      array  $value    The value
     */
    public function update(int $groupID, array $value)
    {
        $values = ['value' =>  self::encodeData($value), 'updated_at' => date('Y-m-d H:i:s')];
        $where = 'group_id = ' . $groupID . ' AND `key` = "' . ManagerColumnsExportExcel::KEY . '"';
        
        if(DB::update('group_options', $values, $where)){
            RequestHandler::sendJsonSuccess('Update thành công !');
        }
        
        RequestHandler::sendJsonError('Update thất bại !');   
    } 

    /**
     * Thêm mới
     *
     * @param      int    $groupID  The group id
     * @param      array  $value    The value
     */
    public function insert(int $groupID, array $value)
    {
        $values = [
            'group_id' => $groupID, 
            'key' => ManagerColumnsExportExcel::KEY, 
            'value' => self::encodeData($value), 
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if(DB::insert('group_options', $values)){
            RequestHandler::sendJsonSuccess('Insert thành công !');
        }
        
        RequestHandler::sendJsonError('Insert thất bại  !');
    }

    /**
     * Called on draw.
     */
    public function on_draw()
    {   
        $this->map['columns'] = ManagerColumnsExportExcel::getColumns(self::$groupID);
        $this->map['option'] = self::$options[self::$groupID];
        $this->map['time'] = strtotime($this->map['option']['updated_at'] 
                                       ? $this->map['option']['updated_at'] : $this->map['option']['created_at']);
        $this->map['title'] = 'Quản lý cột xuất excel đơn hàng';

        $this->parse_layout('manager_columns_export_excel', $this->map);
    }

    /**
     * Gets the option.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The option.
     */
    public static function getOption(int $groupID)
    {
        $fmt = 'SELECT * FROM `group_options` WHERE `key` = "%s" AND `group_id` = %d';
        $sql = sprintf($fmt, self::KEY, $groupID);
        
        if($option = DB::fetch($sql)){
            $option['value'] = self::decodeData($option['value']);
            self::$options[self::$groupID] = $option;
        }

        return $option;
    }

    /**
     * Encodes a data.
     *
     * @param      array   $data   The data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function encodeData(array $data)
    {
        return json_encode($data);
    }

    /**
     * Decodes a data.
     *
     * @param      <type>  $data   The data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function decodeData(string $data)
    {   
        $value = json_decode($data, true);
        $value['data'] = explode(',', $value['data']);

        return $value;
    }

    /**
     * Gets the columns.
     *
     * @param      bool    $all    All
     *
     * @return     <type>  The columns.
     */
    public static function getColumns(int $groupID, $all = false)
    {   
        // Lấy tùy chọn cột từ hệ thống 
        $defaultColumns = get_order_columns(true);

        // Lấy tùy chọn của shop
        $groupOption = self::getOption($groupID);

        return !$groupOption ? $defaultColumns : self::mergeColumns($defaultColumns, $groupOption['value']['data']);
    }

    /**
     * Gets the export columns.
     *
     * @param      int   $groupID  The group id
     */
    public static function getExportColumns(int $groupID)
    {
        return array_filter(self::getColumns($groupID), function($column){
            return $column['selected'];
        });
    }

    /**
     * Makes group columns.
     *
     * @param      array  $defaultColumns  The default columns
     * @param      array  $groupOption     The group option
     *
     * @return     bool   ( description_of_the_return_value )
     */
    private static function mergeColumns(array $defaultColumns, array $groupOption)
    {   
        $defaultColumns = array_map(function($column) use($groupOption){
            $index = array_search($column['id'], $groupOption);
            $column['order'] = $index === false ? 1000 : $index;
            $column['selected'] = $index === false ? false : 1;
            
            return $column;
        }, $defaultColumns);

        usort($defaultColumns, function($a, $b){
            return $a['order'] - $b['order'];
        });

        return $defaultColumns;
    }

    /**
     * { function_description }
     */
    private static function throwBadRequest()
    {   
        if(self::isAjax()){
            RequestHandler::sendJsonError('Bad Request !');
        }

        RequestHandler::showError('Bad Request !');
    }
}