<?php

class StandardizeBundle extends Form
{
    function __construct()
    {
        require_once ROOT_PATH . '/packages/vissale/modules/AdminSystemBundles/SystemBundlesData.php';
        self::standardizeBundles();
    }

    /**
     * Chuẩn hóa dữ liệu
     *
     * @return void
     */
    private function standardizeBundles()
    {
        $systemBundlesData = SystemBundlesData::SYSTEM_BUNDLES;
        foreach ($systemBundlesData as $standardName => $includeIds) {
            echo "Đang xử lý [ $standardName ] ... <br>";
            $standardId = self::findBundleId($standardName);
            echo "Standard_Id: $standardId <br>";

            self::updateIncludeId($standardId, $includeIds);
            self::standardizeOldData($standardName, $includeIds, $standardId);
            echo "✔ Đã xử lý <hr/>";
        } //end foreach
        die;
    }

    /**
     * Tìm và lấy id nhóm sản phẩm chuẩn hóa,
     * khởi tạo mới nếu chưa tồn tại
     *
     * @param string $name
     * @return integer
     */
    private function findBundleId(string $name): int
    {
        $id = self::findBundle($name);
        if ($id) {
            return $id;
        } //end if

        $id = self::createBundle($name);
        echo "- Tạo mới [ ID: $id - $name ] <br>";
        return $id;
    }

    /**
     * Tìm kiếm sản phẩm chuẩn hóa
     *
     * @param string $name
     * @param integer $parentId
     * @return integer
     */
    private static function findBundle(string $name, int $parentId = 0): int
    {
        $sql = "SELECT `id` 
            FROM `bundles`
            WHERE `standardized` = 1 
                AND `group_id` = 0
                AND `ref_id` = 0
                AND `name` = '$name'
            LIMIT 1";

        return intval(DB::fetch($sql, 'id'));
    }

    /**
     * Tạo mới nhóm sản phẩm chuẩn hóa
     *
     * @param string $name
     * @return integer
     */
    private static function createBundle(string $name): int
    {
        $data = [
            'name' => $name,
            'ref_id' => 0,
            'standardized' => 1,
            'group_id' => 0,
        ];

        return intval(DB::insert('bundles', $data));
    }

    /**
     * Bổ sung danh sách id nhóm sản phẩm cũ 
     * vào nhóm sản phẩm chuẩn hóa mới
     *
     * @param integer $id
     * @param array $includeIds
     * @return boolean
     */
    private static function updateIncludeId(int $id, array $includeIds): bool
    {
        if (!$includeIds) {
            return false;
        } //end if

        $_includeIds = implode(',', $includeIds);
        $data = ['include_ids' => $_includeIds];
        $cond = "`id` = $id";
        DB::update('bundles', $data, $cond);
        return true;
    }

    /**
     * Chuẩn hóa dữ liệu cũ
     * Đổi tên + gán ref_id (ID nhóm chuẩn hóa)
     *
     * @param string $name
     * @param array $includeIds
     * @param integer $refId
     * @return boolean
     */
    private static function standardizeOldData(string $name, array $includeIds, int $refId = 0): bool
    {
        if (!$includeIds) {
            return false;
        } //end if

        $_includeIds = implode(',', $includeIds);
        $data = [
            'name' => $name,
            'ref_id' => $refId,
        ];

        $cond = "`id` IN ($_includeIds)";
        DB::update('bundles', $data, $cond);
        return true;
    }
}
