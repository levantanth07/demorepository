<?php
class GroupOption
{
    private static $groups = [];

    private $groupID;

    /**
     * Khởi tạo một instance mới, nếu tham số groupID khác null thì instance được trả lại sẽ làm việc với groupID này 
     * Ngược lại sẽ làm việc với group hiện tại
     * Ví dụ: 
     *  GroupOption::group(1234)
     *
     * @param      int     $groupID  The group id
     *
     * @return     static  ( description_of_the_return_value )
     */
    public static function group(int $groupID = null)
    {
        $self = new static();
        $self->groupID = $groupID;

        return $self;
    }

    /**
     * Gets the group id.
     *
     * @return     <type>  The group id.
     */
    public function getGroupID()
    {
        return $this->groupID > 0 ? $this->groupID : User::getAccount()['group_id'];
    }

    /**
     * Get all group options
     *
     * @param      int     $groupID  The group id
     * @param      array   $selects  The selects
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function all(array $selects = ['*'])
    {   
        return Query::from('group_options')
                    ->where('group_id', $this->getGroupID())
                    ->get($selects);
    }

    /**
     * Gets the options by group id.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The options by group id.
     */
    public function getOptions()
    {   
        $groupID = $this->getGroupID();

        if(empty(GroupOption::$groups[$groupID])){
            GroupOption::$groups[$groupID] = array_column($this->all(), 'value', 'key');
        }

        return GroupOption::$groups[$groupID];
    }

    /**
     * TRUE nếu tồn tại option, ngược lại FALSE. 
     *
     * @param      string  $option   The option
     * @param      bool    $only   The only
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public function option(string $option, $default = false)
    {   
        $options = self::getOptions();

        return isset($options[$option]) ? $options[$option] : $default; 
    }
}