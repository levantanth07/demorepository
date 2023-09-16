<?php

class AdminSystemSourcesDB
{
    const SOURCE_STATUS_ENABLE = 1;
    const SOURCE_STATUS_DISABLE = 0;
    const SOURCE_STATUSES = [
        self::SOURCE_STATUS_ENABLE => 'Hiệu lực',
        self::SOURCE_STATUS_DISABLE => 'Hết hiệu lực',
    ];

    /**
     * condGetSources function
     *
     * Set điều kiện lọc nguồn đơn
     * 
     * @return string
     */
    static function condGetSources(): string
    {
        $conditions = " group_id = 0 AND ref_id = 0";

        $isActive = xgetUrl('filter_active', null);
        if (!is_null($isActive) && $isActive >= 0) {
            $conditions .= " AND `is_active` = $isActive";
        } //end if

        $keysearch = xgetUrl('keysearch', null, 's');
        if (strlen($keysearch) > 0) {
            $conditions .= " AND `name` LIKE '$keysearch%'";
        } //end if

        return $conditions;
    }

    /**
     * getSources function
     *
     * Lấy danh sách nguồn marketing + phân trang
     * 
     * @param integer $lv
     * @param integer $item_per_page
     * @return array
     */
    static function getSources(
        int $item_per_page = 15
    ): array {
        $limit = $item_per_page ? generatePagination($item_per_page) : '';
        $conditions = self::condGetSources();
        $query = "SELECT *
            FROM order_source
            WHERE $conditions
            ORDER By id DESC
            $limit";
    
        $query = formatQuery($query);
        return DB::fetch_all($query);
    }

    /**
     * countSources function
     *
     * Tổng số nguồn đơn hàng
     * 
     * @return integer
     */
    static function countSources(): int
    {
        $conditions = self::condGetSources();
        $query = "SELECT count(id) as total
            FROM order_source
            WHERE $conditions
        ";

        $query = formatQuery($query);
        $result = DB::fetch($query);
        return $result['total'] ?? 0;
    }

    /**
     * delete function
     *
     * Delete source
     * 
     * @param integer $id
     * @return mixed
     */
    static function delete(int $id)
    {
        $check = self::preDelete($id);
        if ($check) {
            return false;
        } //end if

        $table = 'order_source';
        $conditions = "id = $id";
        return DB::delete($table, $conditions);
    }


    /**
     * insert function
     * 
     * Create source
     *
     * @param array $data
     * @return mixed
     */
    static function insert(array $data)
    {
        $table = 'order_source';
        if (self::checkNameExist($data['name'])) {
            return false;
        } //end if

        $userId = Session::get('user_data')['user_id'] ?? 0;
        $value = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
            'created_time' => now(),
            'group_id' => 0,
            'created_acc_id' => $userId,
        ];

        return DB::insert($table, $value);
    }

    /**
     * update function
     *
     * Update source
     * 
     * @param array $data
     * @return mixed
     */
    static function update(array $data)
    {
        $table = 'order_source';
        if (self::checkNameExist($data['name'], $data['id'])) {
            return false;
        } //end if

        $value = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
        ];
    
        $id = $data['id'];
        $conditions = "id = $id";
        return DB::update($table, $value, $conditions);
    }

    /**
     * checkNameExist function
     * 
     * Kiểm tra tên đã tồn tại hay chưa
     *
     * @param string $name
     * @param integer $id
     * @return mixed
     */
    static function checkNameExist(string $name, int $id = 0)
    {
        $query = "SELECT `id`, `name` FROM order_source WHERE name = '$name'";
        if ($id) {
            $query .= " AND id != $id AND ref_id != $id";
        } //end if

        $query .= " LIMIT 1";
        return DB::fetch($query);
    }

    /**
     * checkUsedOnOrders function
     *
     * Kiểm tra nguồn MKT đã được sử dụng hay chưa
     * 
     * @param integer $sourceId
     * @return mixed
     */
    static function checkUsedOnOrders(int $sourceId)
    {
        $query = "SELECT id FROM orders WHERE source_id = $sourceId LIMIT 1";
        return DB::fetch($query);
    }

    /**
     * preDelete function
     * 
     * Kiểm tra điều kiện trước khi xóa
     *
     * @param integer $id
     * @return mixed
     */
    static function preDelete(int $id)
    {
        try {
            return self::checkUsedOnOrders($id);
        } catch (Exception $e) {
            return false;
        }//end try
    }
}
