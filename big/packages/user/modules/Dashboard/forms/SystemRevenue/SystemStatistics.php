<?php

class SystemStatistics
{   
    private static $groups = [];

    /**
     * Lấy ra danh sách các trạng thái có tính doanh thu
     *
     * @param      bool    $groupID  The group id
     *
     * @return     <type>  The have revenue statuses.
     */
    public static function getHaveRevenueStatuses(int $groupID)
    {  
        $fmt = 'SELECT `id` FROM statuses WHERE (`is_system` = 1 OR `group_id` = %d) AND (`no_revenue` = 0 OR `no_revenue` IS NULL)';
        
        return DB::fetch_all(sprintf($fmt, $groupID));
    }

    /**
     * Gets the groups.
     *
     * @param      <type>  $structureID  The structure id
     * @param      string  $expiredDate  The expired date
     *
     * @return     <type>  The groups.
     */
    public static function getGroups($structureID, string $expiredDate)
    {
        $sql = '
            SELECT
                `groups`.`id`
            FROM
                `groups`
            JOIN `groups_system` ON `groups_system`.`id` = `system_group_id`
            WHERE
                `groups`.`expired_date` > "' . DB::escape($expiredDate) . '"
                AND ' . Systems::getIDStructureChildCondition($structureID);
        
        return DB::fetch_all($sql);
    }

    /**
     * Gets the group statistic.
     *
     * @param      int   $groupID  The group id
     *
     * @return     int   The group statistic.
     */
    public static function getGroupRevenue(int $groupID)
    {       
        $joins = [];      
        $wheres = [];

        $wheres[] = 'orders.group_id = ' . $groupID;
        if (THANH_CONG == FilterCondition::getStatus()) {
            $wheres[] = 'AND orders_extra.update_successed_time != "0000-00-00 00:00:00"';
            $joins[] = 'JOIN orders_extra ON orders_extra.order_id=orders.id';
        }

        $statusIDs = array_column(self::getHaveRevenueStatuses($groupID), 'id');
        $wheres[] = 'AND orders.status_id IN (' . implode(',', $statusIDs) . ')';
        
        $wheres[] = 'AND orders.confirmed>="' . FilterCondition::getTimeFrom() . ' 00:00:00"';
        $wheres[] = 'AND orders.confirmed<="' . FilterCondition::getTimeTo() . ' 23:59:59"';

        $sql = '
                SELECT
                    IFNULL(SUM(`total_price`), 0) AS revenue
                FROM
                    orders
                ' . implode(' ', $joins) . '
                WHERE
                ' . implode(' ', $wheres) . '';

        return self::getRevenue($sql);
    }

    /**
     * Gets the group user quantity.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The group user quantity.
     */
    public static function getGroupUserQuantity(int $groupID)
    {   
        $users = self::get_user($groupID);
        $and = '';
        if($users){
            $and = ' AND users.id IN ('. $users .')';
        }    
        $sql = 'SELECT count(*) AS user_quantity FROM account JOIN users ON account.id = users.username WHERE account.is_active = 1 AND `account`.`group_id` = ' . $groupID . $and .'';
        return self::getUserQuantity($sql);
    }
    public static function getGroupUserParentQuantity(int $groupID)
    {   
        $users = self::get_user($groupID);
        $and = '';
        if($users){
            $and = ' AND users.id IN ('. $users .')';
        }    
        $sql = 'SELECT count(*) AS user_parent_quantity FROM account JOIN users ON account.id = users.username WHERE account.is_active = 1 AND account.is_count = 1 AND `account`.`group_id` = ' . $groupID . $and .'';
        return self::getUserParentQuantity($sql);
    }
    public static function get_group_owner($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username,
                    groups.name as group_name 
                FROM 
                    users
                JOIN 
                    groups ON groups.code = users.username
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                ";
        $row = self::getOwner($sql);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    public static function get_leader($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username
                FROM 
                    users
                JOIN 
                    account_group ON account_group.admin_user_id = users.id
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                ";
        $row = self::getLeader($sql);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    public static function get_admin($strUser){
        $sql = "SELECT 
                    users.id,
                    users.username
                FROM 
                    users
                JOIN 
                    account ON account.id = users.username AND account.is_active = 1
                WHERE users.id IN ($strUser)
                AND account.admin_group = 1
                ";
        $row = self::getAdmin($sql);
        $data = [];
        if($row){
            foreach ($row as $key => $value) {
                $data[] = $key;
            }
        }
        return $data;
    }
    public static function get_user_privilege($strUser,$arrUser){
        $roleFail = self::get_role_fail(); 
        $codeAll = self::get_role_all();
        $frefix = 'get_user_privilege_';
        $sql = "SELECT 
                        *
                    FROM 
                        roles_to_privilege
                    JOIN 
                        users_roles ON users_roles.role_id = roles_to_privilege.role_id
                    WHERE 
                        users_roles.user_id IN ($strUser)
                    ";
        $row = self::getCacheUser($sql, $frefix);
        $newCode = [];
        foreach ($codeAll as $kCode => $valCode) {
            $newCode[] = $valCode['code'];
        }
        $codeDiff = array_diff($newCode,$roleFail);
        $codeDiffNew = [];
        foreach ($codeDiff as $valDiff) {
            $codeDiffNew[$valDiff] = $valDiff;
        }
        $newArr = [];
        foreach ($row as $k => $v) {
            $newArr[$v['user_id']][$v['privilege_code']] =  $v['privilege_code'];
            
            if(isset($codeDiffNew[$v['privilege_code']])){
                $newArr[$v['user_id']]['CHECK_CODE'] =  1;
            }
        }
        $arr = [];
        foreach ($newArr as $kNew => $valNew) {
            if(!empty($valNew['QUYEN_GIAM_SAT'])){
                $count = count($valNew);
                if($count > 1){
                    foreach ($valNew as $kV => $valV) {
                       if(in_array($kV, $roleFail) && empty($valNew['CHECK_CODE'])){
                            $arr[] = $kNew;
                       }
                    } 
                } elseif ($count == 1) {
                    $arr[] = $kNew;
                } 
            }
        }
        $arrUserFail = array_unique($arr);
        $arrUserTrue = array_diff($arrUser,$arrUserFail);
        return $arrUserTrue;
    }
    public static function get_role_fail()
    {
        $arr = [
            'SUA_DON_NHANH',
            'QUYEN_GIAM_SAT',
            'BC_BXH_VINH_DANH',
            'CUSTOMER',
            'BC_DOANH_THU_NV',
            'BUNGDON2',
            'BC_DOANH_THU_MKT',
            'cs',
            'HCNS',
            'BUNGDON_NHOM',
            'BUNGDON',
            'ADMIN_KHO',
            'ADMIN_CS'
        ];
        return $arr;
    }
    public static function get_role_all()
    {
        $code_fail = self::get_role_fail();
        $str_code_fail = "('" . implode("','", $code_fail) . "')";
        $sql = "SELECT id,code FROM roles_activities WHERE code NOT IN $str_code_fail";
        if(System::is_local()) {
            return DB::fetch_all($sql);
        }

        $cache_key = 'get_role_all_' . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch_all($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
    static function get_user($groupID){
        $sql = 'SELECT users.id FROM account JOIN users ON account.id = users.username WHERE account.is_active = 1 AND `account`.`group_id` = ' . $groupID . '';
        $users = self::getUser($sql);
        $strUser = '';
        $arrUser = [];
        if(!empty($users)){
            foreach ($users as $key => $value) {
                $arrUser[] = $value['id'];
            }
            $strUser = implode(',', $arrUser);
        }
        $userOwner = self::get_group_owner($strUser);
        $userLeader = self::get_leader($strUser);
        $userAdmin = self::get_admin($strUser);
        $userTrueAll = array_unique(array_merge($userOwner,$userLeader,$userAdmin));
        $userFailAll = array_diff($arrUser,$userTrueAll);
        $strUserFailAll = '';
        if(!empty($userFailAll)){
            $strUserFailAll = implode(',', $userFailAll);
        }
        $userPrivilege = array_unique(self::get_user_privilege($strUserFailAll,$userFailAll));
        $users = array_unique(array_merge($userOwner,$userLeader,$userAdmin,$userPrivilege));
        $str = '';
        if(!empty($users)){
            $str = implode(',', $users);
        }
        return $str;
    }
    /**
     * Gets the group phone quantity.
     *
     * @param      int   $groupID  The group id
     */
    public static function getGroupPhoneQuantity(int $groupID)
    {
        $sql = '
                SELECT 
                    COUNT(*) as phone_quantity
                FROM
                    orders
                WHERE
                    orders.created>="' . FilterCondition::getTimeFrom() . ' 00:00:00" 
                    AND orders.created<="' . FilterCondition::getTimeTo() . ' 23:59:59"
                    AND orders.group_id=' . $groupID . '';

        return self::getPhoneQuantity($sql);
    }

    /**
     * Gets the revenue.
     *
     * @param      string  $sql    The sql
     *
     * @return     <type>  The revenue.
     */
    private static function getRevenue(string $sql)
    {   
        return self::getSqlResult('groups_revenue_', $sql);
    }

    /**
     * Gets the revenue.
     *
     * @param      string  $sql    The sql
     *
     * @return     <type>  The revenue.
     */
    private static function getUserQuantity(string $sql)
    {   
        return self::getSqlResult('groups_user_quantity_', $sql);
    }

    private static function getUserParentQuantity(string $sql)
    {   
        return self::getSqlResult('groups_user_parent_quantity_', $sql);
    }

    private static function getUser(string $sql)
    {   
        return self::getSqlResultGetUser('groups_user_', $sql);
    }
    private static function getLeader(string $sql)
    {   
        return self::getSqlResultGetUser('groups_user_leader_', $sql);
    }
    private static function getAdmin(string $sql)
    {   
        return self::getSqlResultGetUser('groups_user_admin_', $sql);
    }
    private static function getCacheUser(string $sql, $frefix)
    {   
        if(System::is_local()) {
            return DB::fetch_all($sql);
        }

        $cache_key = $prefix . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch_all($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
    private static function getOwner(string $sql)
    {   
        return self::getSqlResultGetUser('groups_user_owner_', $sql);
    }

    private static function getSqlResultGetUser(string $prefix, string $sql)
    {
        if(System::is_local()) {
            return DB::fetch_all($sql);
        }

        $cache_key = $prefix . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch_all($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
    /**
     * Gets the phone quantity.
     *
     * @param      string  $sql    The sql
     *
     * @return     <type>  The phone quantity.
     */
    private static function getPhoneQuantity(string $sql)
    {   
        return self::getSqlResult('groups_phone_quantity_', $sql);
    }

    /**
     * Gets the sql result.
     *
     * @param      string  $prefix  The prefix
     * @param      string  $sql     The sql
     *
     * @return     <type>  The sql result.
     */
    private static function getSqlResult(string $prefix, string $sql)
    {
        if(System::is_local()) {
            return DB::fetch($sql);
        }

        $cache_key = $prefix . md5($sql);
        if($cache = MC::get_items($cache_key)){
            return json_decode($cache, true);
        }

        if($cache = DB::fetch($sql)){
            MC::set_items($cache_key, json_encode($cache), time() + 600); // cache 10 phut 
        }

        return $cache;
    }
}