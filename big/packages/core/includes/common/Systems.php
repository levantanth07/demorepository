<?php

class Systems
{   
    private static $STRUCTURE_ID_OBD = null;

    const ROOT_ID = 1;

    const IS_PARENT = 'IS_PARENT';
    const GROUPS_ARE_STILL_ACTIVE = 'GROUPS_ARE_STILL_ACTIVE';
    const NOT_EXISTS = 'NOT_EXISTS';
    /**
    *  Lấy các hệ thống con của hệ thống hiện tại. Lưu ý không lấy các hệ thống là cháu chắt ...
    *
    * @param      array   $select  The select
    *
    * @return     <type>  The systems child.
    */
    public static function getDirectSystemsChild($structureID, array $select = ['*'])
    {   
        $where = self::getIDStructureDirectChildCondition($structureID);
        if($where === false){
            return [];
        }
        
        $sql = sprintf('SELECT %s FROM `groups_system` WHERE %s', implode(',', $select), $where);
        
        return DB::fetch_all($sql);
    }

    /**
     * Lấy các hệ thống con của hệ thống hiện tại.
     *
     * @param      <type>  $structureID  The structure id
     * @param      array   $select       The select
     * @param      bool    $isExcept     Indicates if except
     *
     * @return     <type>  The systems child.
     */
    public static function getSystemsChild($structureID, array $select = ['*'], bool $isExcept = true)
    {   
        $where = self::getIDStructureChildCondition($structureID, $isExcept);

        $sql = sprintf('SELECT %s FROM `groups_system` WHERE %s', implode(',', $select), $where);
        
        return DB::fetch_all($sql);
    }

    /**
    *  Trả về true nếu là hệ thuống cha, ngược lại thì false
    *
    * @param      int|string   ID cấu trúc cần kiểm tra
    *
    * @return     bool
    */
    public static function isParent($structureID)
    {   
        $where = self::getIDStructureDirectChildCondition($structureID);
        if($where === false){
            return false;
        }

        $sql = sprintf('SELECT count(*) as `count` FROM `groups_system` WHERE %s', $where);

        return !!DB::fetch($sql, 'count');
    }

    /**
     * Determines if child.
     *
     * @param      <type>  $structureID        The structure id
     * @param      <type>  $parentStructureID  The parent structure id
     */
    public static function isChild($structureID, $parentStructureID)
    {
        return $structureID > $parentStructureID && preg_match('#^' . self::transformIDStructure($parentStructureID, '', '') . '#', $structureID);
    }

    /**
     * Lấy câu điều kiện truy vấn các hệ thống con của structureID hiện tại
     * 
     * Tất cả các hệ thống được xem là cha của một cấp thì luôn chia hết cho 100 mũ 9 - level của nó 
     * Ví dụ hệ thống cấp 6 có structureID 1010101010101000000 => nó phải luôn chia hết cho 100^(9-6) = 100^3 =10^6
     * Do đó tất cả con của nó sẽ phải chia hết cho 100^2 = 10^4 = 10000 bởi vì con của nó có thể là cha của một hệ 
     * thống cấp thấp hơn
     * Như vậy với strucid đầu vào 1010101010101000000 ta chyển nó về 1010101010101_10000 sau đó loại bỏ phần đầu
     * chỉ còn 10000 để đưa nó vào câu điều kiện đủ 
     *
     * @param      <type>  $structureID  The structure id
     *
     * @return     string  The id structure direct child conditon.
     */
    public static function getIDStructureDirectChildCondition($structureID)
    {   
        // Điều kiện đủ
        $unit = preg_replace('#\d+\_(\d+)#', '$1', self::transformIDStructure($structureID, '_1', '00'));
        $directChildCondition = '(`structure_id` % ' . $unit . '=0)';

        // Điều kiện cần là con của structureID
        $childCondition = self::getIDStructureChildCondition($structureID, true);

        return self::getIDStructureLevel($structureID) >= 9 ? false : sprintf('(%s AND %s)', $childCondition, $directChildCondition);
    }

    /**
     * Gets the child condition.
     *
     * @param      <type>  $structureID  The structure id
     * @param      bool    $exceptMe     The except me
     *
     * @return     <type>  The child condition.
     */
    public static function getIDStructureChildCondition($structureID, $exceptMe = false)
    {
        $childCondition = sprintf(
            '(`structure_id` >= %d AND `structure_id` <= %d)', 
            self::transformIDStructure($structureID, '01', '00'), 
            self::transformIDStructure($structureID, ID_MAX_CHILD, ID_MAX_CHILD)
        );

        if($exceptMe){
            return $childCondition;
        }

        return sprintf('(%s OR `structure_id` = %s)', $childCondition, $structureID);
    }

    /**
     * Gets the id structure level.
     *
     * @param      <type>     $strcutureID  The strcuture id
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     The id structure level.
     */
    public static function getIDStructureLevel($structureID)
    {
        if($structureID < ID_ROOT || $structureID > ID_MAX){
            throw new Exception(sprintf('Structure ID `%s` không hợp lệ !', $structureID));
        }

        $position = array_search('00', str_split("0$structureID", 2));

        return $position === false ? 9 : $position - 1;
    }

    /**
     * Gets the id structure level.
     *
     * @param      <type>     $strcutureID  The strcuture id
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     The id structure level.
     */
    public static function getIDStructureParent($structureID)
    {
        return $structureID <= ID_ROOT ? false : self::transformIDStructureLevel($structureID, self::getIDStructureLevel($structureID));
    }

    /**
     * Di chuyển hệ thống có fromID đến hệ thống toID
     *
     * @param      int        $fromID  The from id
     * @param      int        $toID    To id
     *
     * @throws     Exception  (description)
     */
    public static function moveSystem(int $fromID, int $toID)
    {
        if(!$fromID || !$toID){
            throw new Exception(
                sprintf('ID của hệ thống chuyển đến `%d` hoặc chuyển đi `%d` không hợp lệ !', $fromID, $toID)
            );
        }

        [$fromID => $from, $toID => $to] = self::get([$fromID, $toID]);
        if(!$from || !$to){
            throw new Exception('Không tìm thấy hệ thống chuyển đến hoặc chuyển đi !');
        }

        // Lấy ID con mới của hệ thống chuyển đến
        $newIDStructure = self::getAvailbleNextIDStructure($to['structure_id']);
        if($newIDStructure === false){
            throw new Exception('Hệ thống chuyển đến đã đầy !');
        }

        // Lấy tất cả hệ thống con của hệ thống chuyển đi 
        $childsOfFrom = self::getSystemsChild($from['structure_id'], ['id', 'structure_id']);

        if(!empty($childsOfFrom[$toID])){
            throw new Exception('Không được chuyển đến hệ thống con !');
        }

        $fromLevel = self::getIDStructureLevel($from['structure_id']);
        $toLevel = self::getIDStructureLevel($to['structure_id']);

        // chuyển kết quả sang dạng => [id => structure_id, ...];
        foreach ($childsOfFrom as $ID => $child) {
            if(self::getIDStructureLevel($child['structure_id']) + 1 + $toLevel - $fromLevel > ID_MAX_LEVEL){
                throw new Exception('Level hệ thống chuyển đến và chuyển đi không hợp lệ !');
            }

            $childsOfFrom[$ID] = $child['structure_id'];
        }

        // Giả sử ta có hệ thống như sau   => die chuyển sang >> Hệ thống khác có structureID 1010101090000000000
        // Cha: 1010101010101000000                                1010101090101000000                   
        // Con: 1010101010101010000                                1010101090101010000      
        // Con: 1010101010101020000                                1010101090101020000                   
        // Cháu:1010101010101031700                                1010101090101031700                 
        $prefix = self::transformIDStructure($from['structure_id'], '', ''); // 1010101010101   
        $newPrefix = self::transformIDStructure($newIDStructure, '', '');    // 101010109

        // Thay thế prefix ID cũ sang mới
        foreach ($childsOfFrom as $ID => $structure_id_) {
            $structure_id_ = str_replace($prefix, $newPrefix, $structure_id_);
            $childsOfFrom[$ID] = strlen($structure_id_) > 19 
                                    ? substr($structure_id_, 0, 19) 
                                    : str_pad($structure_id_, 19, '0');
        }

        // Add cả cha vào mảng sẽ dùng để cập nhật DB
        $childsOfFrom[$from['id']] = $newIDStructure;

        // Cập nhật database
        DB::$db_connect_id->begin_transaction();
        try {
            foreach ($childsOfFrom as $ID => $column) {
                $column = str_replace($prefix, $newPrefix, $column);
                $column = str_pad($childsOfFrom[$ID], 19, '0');

                if(self::updateIDStructure($ID, $column) === false){
                    throw new Exception(sprintf('Cập nhật ID = `%d`, structure_id = `%d` thất bại !', $ID, $column));
                }
            }

            DB::$db_connect_id->commit();
        } catch (Exception $e) {
            DB::$db_connect_id->rollback();
        }
    }

    /**
     * Removes a system.
     *
     * @param      int        $ID     { parameter_description }
     *
     * @throws     Exception  (description)
     *
     * @return     bool       ( description_of_the_return_value )
     */
    public static function removeSystem(int $ID)
    {   
        DB::$db_connect_id->begin_transaction();
        try {            
            // Di chuyển các shop ra hệ thống root
            if(!Groups::moveToSystem($ID, self::ROOT_ID)){
                throw new Exception('Di chuyển hệ thống thất bại !');
            }

            // Thực hiện xóa hệ thống và admin hệ thống
            if(!DB::delete_id('groups_system', $ID) || !DB::delete('groups_system_account', '`system_group_id` = ' . $ID)){
                throw new Exception('Xóa hệ thống thất bại !');
            }

            DB::$db_connect_id->commit();
            
            return true;
        } catch (Exception $e) {
            DB::$db_connect_id->rollback();
            return false;
        }
    }

    /**
     * Determines ability to remove system.
     *
     * @param      int   $structureID  The structure id
     *
     * @return     bool  True if able to remove system, False otherwise.
     */
    public static function canRemoveSystem(int $structureID)
    {
        if(self::isParent($structureID)){
            return self::IS_PARENT;
        }

        if(self::getNumGroupActiveByIDStructure($structureID)){
            return self::GROUPS_ARE_STILL_ACTIVE;
        }

        return true;
    }

    /**
     * Lấy số lượng group đang active theo id structure.
     *
     * @param      int     $structureID  The structure id
     *
     * @return     int  The number group active by id structure.
     */
    public static function getNumGroupActiveByIDStructure(int $structureID)
    {
        $sql = 'SELECT count(`groups`.`id`) as `count` FROM `groups`
                JOIN `groups_system` ON `groups`.`system_group_id` = `groups_system`.`id`
                WHERE 
                    `groups`.`active` = 1
                    AND `groups_system`.`structure_id` = ' . $structureID;

        return DB::fetch($sql, 'count');
    }

    /**
     * Determines ability to remove system id.
     *
     * @param      int   $ID     { parameter_description }
     *
     * @return     bool  True if able to remove system id, False otherwise.
     */
    public static function canRemoveSystemID(int $ID)
    {
        return self::canRemoveSystem(DB::structure_id('groups_system', $ID));
    }

    /**
     * Cập nhật ID structure
     *
     * @param      int     $ID           { parameter_description }
     * @param      <type>  $structureID  The structure id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function updateIDStructure(int $ID, $structureID)
    {
        return DB::update('groups_system', ['structure_id' => $structureID], "id = $ID");
    }

    /**
     * Tìm điểm kết thúc của ID (00) và thay thế điểm kết thúc trở về sau bằng cặp giá trị mới.
     * Ví dụ: transformIDStructure(101010000000000, 'ff', 'nn') => 10101ffnnnnnnnn 
     * 
     * @param      <type>  $ID     { parameter_description }
     * @param      string  $first  The first
     * @param      string  $next   The next
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function transformIDStructure($ID, string $first = '00', string $next = '00')
    {   
        $segments = str_split("0$ID", 2);
        $matched = false;
        foreach($segments as $key => $segment){
            if($matched){
                $segments[$key] = $next;
            }
            else if($segment === '00'){
                $matched = true;
                $segments[$key] = $first;
            }
        }

        
        return ltrim(implode('', $segments), 0);
    }

    /**
     * Tìm điểm kết thúc của ID (00) và thay thế điểm kết thúc trở về sau bằng cặp giá trị mới.
     * Ví dụ: transformIDStructure(101010000000000, 'ff', 'nn') => 10101ffnnnnnnnn 
     * 
     * @param      <type>  $ID     { parameter_description }
     * @param      string  $first  The first
     * @param      string  $next   The next
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function transformIDStructureLevel($ID, int $level, string $first = '00', string $next = '00')
    {   
        $segments = str_split("0$ID", 2);
        $matched = false;
        foreach($segments as $key => $segment){
            if($matched){
                $segments[$key] = $next;
            }
            else if($key === $level){
                $matched = true;
                $segments[$key] = $first;
            }
        }

        
        return ltrim(implode('', $segments), 0);
    }
    /**
     * Gets the availble next id structure.
     *
     * @param      <type>     $parentStructureID  The structure id
     *
     * @throws     Exception  (description)
     *
     * @return     bool|int   The availble next id structure.
     */
    public static function getAvailbleNextIDStructure($parentStructureID)
    {   
        $parentLevel = self::getIDStructureLevel($parentStructureID);
        if($parentLevel === ID_MAX_LEVEL){
            return false;
        }
         
        $directChilds = self::getDirectSystemsChild($parentStructureID);
        if(count($directChilds) >= ID_MAX_CHILD){
            return false;
        }

        if(!$directChilds){
            return self::transformIDStructure($parentStructureID, '01', '00');
        }

        $savedChilds = array_map(function($child) use($parentLevel){
            if(self::getIDStructureLevel($child['structure_id']) != $parentLevel + 1){
                throw new Exception(sprintf('Hệ thống con `%s`(id = %d) không hợp lệ !', $child['structure_id'], $child['id']));
            }

            // 1080101000000000000 => 1080101
            $prefix = self::transformIDStructure($child['structure_id'], '', '');

            return intval($prefix);
        },$directChilds);

        $prefix = self::transformIDStructure($parentStructureID, '00', '');
        for ($i=1; $i < 100; $i++) { 
            if(!in_array(++$prefix, $savedChilds)){
                break;
            }
        }

        return $prefix * pow(100, ID_MAX_LEVEL - $parentLevel - 1);
    }

    /**
     * Lấy hệ thống F0 của hệ thống cha sao cho F0 chứa shop có id là groupID 
     * ví dụ: root > cha > f0 >f1 ....> fx - shopID 
     * 
     * Lưu ý: Tuyệt đối ko gọi hàm này trong vòng lặp, nên sử dụng 1 lần và cache kết quả lại
     * 
     * @param      int     $parentStructureID  The parent structure id
     * @param      int     $groupID            The group id
     *
     * @return     <type>  The f 0 of group.
     */
    public static function getF0OfGroup(int $parentStructureID, int $groupID)
    {
        // Lấy structure_id của hệ thống quản lý shop hiện tại
        $structureID = get_group_system_by_group_id($groupID, ['structure_id'])['structure_id'];
        
        // Lấy tất cả F0 của hệ thống cha
        $f0Systems = self::getDirectSystemsChild($parentStructureID, ['id', 'structure_id']);
        
        // Build các câu truy vấn lấy hệ thống con của F0 sao cho hệ thống con đó có struture_id 
        // trùng khớp $strutureID
        $findSystemChildSqls = array_map(function($child) use($structureID) {
            $fmt = 'SELECT %d as f0, `id`, `structure_id` FROM `groups_system` WHERE %s AND structure_id = %d';
            return sprintf($fmt, $child['id'], IDStructure::child_cond($child['structure_id']), $structureID);
        }, $f0Systems);

        // Gộp các câu truy vấn lại để build 1 tập kết quả duy nhất, việc này tránh dùng đến vòng 
        // lặp php để chạy lần lượt câu truy vấn 
        $sql = implode(" UNION \n", $findSystemChildSqls);

        return DB::fetch($sql);
    }

    /**
     * Lấy ra một hoặc nhiều hệ thống theo ID hệ thống
     *
     * @param      int|array       $ID 
     * @param      array|string  $selects  The selects
     *
     * @return     <type>        ( description_of_the_return_value )
     */
    public static function get($ID, array $selects = [])
    {
        $fmt = 'SELECT %s FROM `groups_system` WHERE %s';
        $selects = $selects ? '`' . implode('`,`', $selects) . '`' : '*';

        if(is_array($ID)){
            return DB::fetch_all(sprintf($fmt, $selects, 'id IN (' . implode(',', $ID) . ')'));
        }

        return DB::fetch(sprintf($fmt, $selects, 'id = ' . $ID));
    }

    /**
     * Lấy ra một hoặc nhiều hệ thống theo IDStructure
     *
     * @param      int|array       $ID 
     * @param      array|string  $selects  The selects
     *
     * @return     <type>        ( description_of_the_return_value )
     */
    public static function getByIDStructure($IDStructure, array $selects = [])
    {
        $fmt = 'SELECT %s FROM `groups_system` WHERE %s';
        $selects = $selects ? '`' . implode('`,`', $selects) . '`' : '*';

        if(is_array($IDStructure)){
            return DB::fetch_all(sprintf($fmt, $selects, 'structure_id IN (' . implode(',', $IDStructure) . ')'));
        }

        return DB::fetch(sprintf($fmt, $selects, 'structure_id = ' . $IDStructure));
    }

    /**
     * Lấy ra hệ thống mà user_id được gán quyền quản lí
     *
     * @param      int     $userID   The user id
     * @param      array   $selects  The selects
     *
     * @return     <type>  The system by user id.
     */
    public static function getSystemByUserID(int $userID, array $selects = ['*'])
    {   
        $fmt = 'SELECT %s 
                FROM groups_system 
                JOIN groups_system_account ON groups_system_account.system_group_id = groups_system.id 
                WHERE groups_system_account.user_id= %d';

        return DB::fetch(sprintf($fmt, implode(',', $selects), $userID));
    }

    /**
     * Lấy ra toàn bộ các hệ thống được gán quyền quản lí và hệ thống con cháu của nó
     *
     * @param      int    $userID    The user id
     * @param      array  $selects   The selects
     * @param      bool   $isExcept  Có bỏ qua hệ thống được gán quyền quản lí ?
     *
     * @return     array  The systems by user id.
     */
    public static function getSystemsByUserID(int $userID, array $selects = ['*'], bool $isExcept = false)
    {   
        // Lấy ra hệ thống mà user_id được gán quyền quản lí
        if(!$system = self::getSystemByUserID($userID, ['structure_id'])){
            return [];
        }

        return self::getSystemsChild($system['structure_id'], $selects, $isExcept);
    }

    /**
     * Gets the obd structure id.
     *
     * @return     <type>  The obd structure id.
     */
    public static function getOBDStructureID()
    {
        if(is_null(self::$STRUCTURE_ID_OBD)){
            self::$STRUCTURE_ID_OBD = DB::fetch('SELECT structure_id FROM groups_system WHERE id = 2', 'structure_id');
        }

        return self::$STRUCTURE_ID_OBD;
    }

    public static function getOBDStructureIDNew($id)
    {
        return DB::fetch('SELECT structure_id FROM groups_system WHERE id = '.$id, 'structure_id');
    }

    /**
     * Determines whether the specified group id is group in obd.
     *
     * @param      int   $groupID  The group id
     *
     * @return     bool  True if the specified group id is group in obd, False otherwise.
     */
    public static function isGroupInOBD(int $groupID)
    {
        return DB::fetch('
                select groups.id, groups.name, groups_system.structure_id, system_group_id
                from groups
                join groups_system on groups_system.id = groups.system_group_id
                where 
                    groups.id = ' . $groupID . ' 
                    and ' . self::getIDStructureChildCondition(self::getOBDStructureID())
        );
    }

    /**
     * Determines whether the specified system group id is group in obd.
     *
     * @param      int   $groupID  The group id
     *
     * @return     bool  True if the specified group id is group in obd, False otherwise.
     */
    public static function isSystemGroupInOBD(int $systemGroupID)
    {
        return DB::fetch('
                select id, name, structure_id
                from groups_system
                where 
                    id = ' . $systemGroupID . ' 
                    and ' . self::getIDStructureChildCondition(self::getOBDStructureID())
        );
    }
}