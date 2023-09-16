<?php

class StandardizeMasterBundle extends Form
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
        $systemBundlesData = SystemBundlesData::SYSTEM_MASTER_BUNDLES;
        foreach ($systemBundlesData as $lv1Name => $lv2Names) {
            echo "Đang xử lý [ $lv1Name ] ... <br>";
            $lv1Id = self::findMasterBundleId($lv1Name);
            echo "⌊___ Lv 1 - ID: $lv1Id <br>";

            foreach ($lv2Names as $lv2Name => $includeids) {
                $lv2Id = self::findMasterBundleId($lv2Name, $lv1Id);
                echo "⌊__________________ Lv 2 - ID: $lv2Id: $lv2Name <br>";
                if (!$includeids) {
                    echo "⌊____________________________________ Skip<br>";
                    continue;
                }
                echo "⌊____________________________________ Update include ID <br>";
                self::updateIncludeId('master_bundle', $lv2Id, $includeids);
                self::standardizeOldData($lv2Name, $includeids, $lv2Id);
            } //end if

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
    private function findMasterBundleId(string $name, int $parentId = 0): int
    {
        $id = self::findMBundle($name, $parentId);
        if ($id) {
            return $id;
        } //end if

        $id = self::createMBundle($name, $parentId);
        $lv = $parentId ? 'Lv 2' : 'Lv 1';
        if (!$id) {
            echo "<p style='background-color: red'>$lv - Tạo mới không thành công [ ID: $id - $name ] </p><br>";
            return $id;
        } //end if
        echo "$lv - Tạo mới [ ID: $id - $name ] <br>";
        return $id;
    }

    /**
     * Tìm kiếm sản phẩm chuẩn hóa
     *
     * @param string $name
     * @param integer $parentId
     * @return integer
     */
    private static function findMBundle(string $name, int $parentId = 0): int
    {
        $sql = "SELECT `id` 
            FROM `master_bundle`
            WHERE `parent_id` = $parentId 
                AND `name` = '$name' 
                AND `ref_id` = 0
                AND `is_active` = 1
            LIMIT 1";

        return intval(DB::fetch($sql, 'id'));
    }

    /**
     * Tạo mới nhóm sản phẩm chuẩn hóa
     *
     * @param string $name
     * @param integer $parentId
     * @return integer
     */
    private static function createMBundle(string $name, int $parentId = 0): int
    {
        $data = [
            'name' => $name,
            'parent_id' => $parentId,
            'status' => 1,
            'ref_id' => 0,
            'is_active' => 1,
        ];

        return intval(DB::insert('master_bundle', $data));
    }

    /**
     * Bổ sung danh sách id nhóm sản phẩm cũ 
     * vào nhóm sản phẩm chuẩn hóa mới
     *
     * @param string $table
     * @param integer $id
     * @param array $includeIds
     * @return boolean
     */
    private static function updateIncludeId(string $table, int $id, array $includeIds): bool
    {
        if (!$includeIds) {
            return false;
        } //end if

        $_includeIds = implode(',', $includeIds);
        $data = ['include_ids' => $_includeIds];
        $cond = "`id` = $id";
        DB::update($table, $data, $cond);
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
        DB::update('master_bundle', $data, $cond);
        return true;
    }
}
