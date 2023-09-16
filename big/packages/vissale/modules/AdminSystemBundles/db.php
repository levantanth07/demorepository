<?php

class AdminSystemBundlesDB
{
    const BUNDLE_STATUS_ENABLE = 1;
    const BUNDLE_STATUS_DISABLE = 0;
    const BUNDLE_STATUSES = [
        self::BUNDLE_STATUS_ENABLE => 'Hiệu lực',
        self::BUNDLE_STATUS_DISABLE => 'Hết hiệu lực',
    ];

    const ON_MASTER_PRODUCTS = 1;
    const ON_MASTER_BUNDLES = 2;

    /**
     * condGetBundles function
     *
     * Set điều kiện lấy danh sách nhóm sản phẩm
     * 
     * @param integer $lv
     * @return string
     */
    static function condGetBundles(int $lv = 1): string
    {
        $conditions = " bd.is_active = 1";

        if ($lv > 1) {
            $parentId =  xgetUrl('filter_parent_id', null);
            if ($parentId > 0) {
                $conditions .= " AND bd.parent_id = $parentId";
            } else {
                $conditions .= " AND bd.parent_id != 0";
            } //end if

        } else {
            $conditions .= " AND bd.parent_id = 0";
        } //end if

        $status = xgetUrl('filter_status', null);
        if (!is_null($status) && $status >= 0) {
            $conditions .= " AND bd.status = $status";
        } //end if

        $keysearch = xgetUrl('keysearch', null, 's');
        if (strlen($keysearch) > 0) {
            $conditions .= " AND bd.name LIKE '$keysearch%'";
        } //end if

        return $conditions;
    }

    /**
     * getBundles function
     *
     * Lấy danh sách nhóm sản phẩm kèm phân trang
     * 
     * @param integer $lv
     * @param integer $item_per_page
     * @return array
     */
    static function getBundles(
        int $lv = 1,
        int $item_per_page = 15,
        array $cols = [
            'bd.id',
            'bd.name',
            'bd.parent_id',
            'bd2.name as parent_name',
            'bd.status',
        ]
    ): array {
        $limit = $item_per_page ? generatePagination($item_per_page) : '';
        $conditions = self::condGetBundles($lv);
        $selectCols = generateColumns($cols);
        $query = "SELECT $selectCols
            FROM master_bundle as bd 
                LEFT JOIN master_bundle as bd2 
                ON bd.parent_id = bd2.id
            WHERE $conditions
            ORDER By bd.id DESC
            $limit";

            return DB::fetch_all($query);
    }

    /**
     * getAllBundles function
     *
     * Lấy tất cả nhóm sản phẩm cấp 1
     * 
     * @param integer $lv
     * @param integer $item_per_page
     * @return array
     */
    static function getAllL1Bundles(): array
    {
        $query = "SELECT id, name
            FROM master_bundle
            WHERE parent_id = 0 AND is_active = 1 AND ref_id = 0
            ORDER By id DESC
        ";
        return DB::fetch_all($query);
    }

    /**
     * countBundles function
     *
     * Tổng số nhóm sản phẩm
     * 
     * @param integer $lv
     * @return array
     */
    static function countBundles(int $lv = 1): array
    {
        $conditions = self::condGetBundles($lv);
        $query = "SELECT count(id) as total
            FROM master_bundle as bd
            WHERE $conditions";

        return DB::fetch($query);
    }

    /**
     * Xóa nhóm sản phẩm
     * 
     * @param integer $id
     * @return mixed
     */
    static function delete(int $id, int $type)
    {
        $check = self::preDelete($id, $type);
        if ($check) {
            return false;
        } //end if

        $conditions = "id = $id";
        return DB::delete('master_bundle', $conditions);
    }


    /**
     * Tạo nhóm sản phẩm
     *
     * @param array $data
     * @return mixed
     */
    static function insert(array $data)
    {
        $table = 'master_bundle';
        $isLv1Bundle = $data['parent_id'] ? false : true;
        if (self::checkNameExist($data['name'], $isLv1Bundle)) {
            return false;
        } //end if
        $value = [
            'name' => $data['name'],
            'status' => $data['status'],
        ];

        if (!is_null($data['parent_id'] ?? null)) {
            $value['parent_id'] = intval($data['parent_id']);
        } //end if

        if(!$isLv1Bundle) {
            $bundle = [
                'name' => $data['name'],
                'group_id' => 0,
                'standardized' => 1,
                'ref_id' => 0,
            ];
            DB::insert('bundles', $bundle);
        }

        return DB::insert('master_bundle', $value);
    }

    /**
     * Update nhóm sản phẩm
     * 
     * @param array $data
     * @return mixed
     */
    static function update(array $data)
    {
        $isLv1Bundle = $data['parent_id'] ? false : true;
        if (self::checkNameExist($data['name'], $isLv1Bundle, $data['id'])) {
            return false;
        } //end if

        if(!$isLv1Bundle) {
           self::handleBundlesOfHKD($data['id'], $data['name']);
        }

        $value = [
            'name' => $data['name'],
            'status' => $data['status'],
        ];

        if (!is_null($data['parent_id'] ?? null)) {
            $value['parent_id'] = intval($data['parent_id']);
        } //end if

        $id = $data['id'];
        $conditions = "id = $id";
        return DB::update('master_bundle', $value, $conditions);
    }

    /**
     * Cập nhật bundles của hộ kinh doanh
     *
     * @param integer $masterBundleId
     * @param string $newName
     * @return boolean|void
     */
    static function handleBundlesOfHKD(int $masterBundleId, string $newName) {
        $masterBundleName = self::findMasterBundle($masterBundleId);
        $standardBundle = self::findStandardBundleByName($masterBundleName);
        if (!$standardBundle) {
            return;
        }//end if

        $bundleIds = $standardBundle['id'];
        $conditions = "ref_id = $bundleIds OR id = $bundleIds";
        return DB::update('bundles', ['name' => $newName], $conditions);
    }

    static function findMasterBundle(int $id) { 
        $sql = "SELECT name FROM master_bundle WHERE id = $id LIMIT 1";
        return DB::fetch($sql, 'name');
    }

    static function findStandardBundleByName(string $name) { 
        $sql = "SELECT * 
            FROM `bundles` 
            WHERE `group_id` = 0
                AND `standardized` = 1
                AND `ref_id` = 0
                AND `name` = '$name'
            LIMIT 1";

        return DB::fetch($sql);
    }

    /**
     * Kiểm tra tên đã tồn tại trong nhóm sản phẩm hệ thống hay chưa
     *
     * @param string $name
     * @param boolean $isLv1Bundle
     * @param integer $id
     * @return mixed
     */
    static function checkNameExist(string $name, bool $isLv1Bundle = false, int $id = 0)
    {
        $query = "SELECT `id`, `name` FROM `master_bundle` WHERE 1 = 1";

        if ($id) {
            $query .= " AND `id` != $id";
        }//end if

        if ($isLv1Bundle) {
            $query .= " AND `parent_id` = 0";
        } else {
            $query .= " AND `parent_id` != 0";
        }//end if


        $query .= " AND  `name` = '$name' LIMIT 1";
        return DB::fetch($query);
    }

    /**
     * checkUsedOnMasterProducts function
     *
     * Kiểm tra bundle đã được sử dụng trong sản phẩm hệ thống hay chưa
     * 
     * @param integer $id
     * @return mixed
     */
    static function checkUsedOnMasterProducts(int $id)
    {
        $query = "SELECT `id` FROM `master_product` WHERE `bundle_id` = $id LIMIT 1";
        return DB::fetch($query);
    }

    /**
     * checkUsedOnMasterBundle function
     *
     * Kiểm tra bundle đã được sử dụng làm parent_id hay chưa
     * 
     * @param integer $id
     * @return mixed
     */
    static function checkUsedOnMasterBundle(int $id)
    {
        $query = "SELECT `id` FROM `master_bundle` WHERE `parent_id` = $id LIMIT 1";
        return DB::fetch($query);
    }

    /**
     * preDelete function
     * 
     * Kiểm tra nhóm sản phẩm đã được sử dụng hay chưa trước khi xóa
     *
     * @param integer $id
     * @param integer $type
     * @return mixed
     */
    static function preDelete(int $id, int $type)
    {
        switch ($type) {
            case self::ON_MASTER_PRODUCTS:
                $mProduct =  self::checkUsedOnMasterProducts($id);
                if ($mProduct) {
                    return true;
                }

                $masterBundleName = self::findMasterBundle($id);
                $standardBundle = self::findStandardBundleByName($masterBundleName);
                if (!$standardBundle) {
                    return false;
                }//end if

                $standardBundleId = intval($standardBundle['id']);

                $sql = "SELECT id FROM orders WHERE bundle_id = $standardBundleId LIMIT 1";
                $existOrder = DB::fetch($sql);
                if ($existOrder) {
                    return true;
                }

                $sql = "SELECT id FROM bundles WHERE ref_id = $standardBundleId LIMIT 1";
                $existBundle = DB::fetch($sql);
                if ($existBundle) {
                    return true;
                }

                $conditions = "id = $standardBundleId";
                DB::delete('bundles', $conditions);
                return false;

                break;
            case self::ON_MASTER_BUNDLES:
                return self::checkUsedOnMasterBundle($id);
                break;
            default:
                return false;
                break;
        } //end switch 
    }
}
