<?php

class StandardizeSourceData extends Form
{
    function __construct()
    {
        require_once ROOT_PATH . '/packages/vissale/modules/AdminSystemSources/SystemSourceData.php';
        $standardSources = SystemSourceData::SYSTEM_SOURCES;

        foreach ($standardSources as $souceName => $includeIds) {
           self::handleStandardizeData($souceName, $includeIds);
        } //end foreach
        die;
    }

    private static function handleStandardizeData(string $souceName, array $includeIds) {
        echo "Đang xử lý '$souceName' ... <br>";
        $standardId = self::findStandardSource($souceName);
        if (!$standardId) {
            $standardId = self::createStandardSource($souceName);
            echo "Standard_Id: Tạo mới $standardId <br>";
        } //end if

        echo "Standard_Id: $standardId <br>";
        self::updateIncludeId($standardId, $includeIds);
        self::standardizeOldData($souceName, $includeIds, $standardId);
        echo "✔ Đã xử lý<hr/>";
    }

    /**
     * Tìm kiếm sản phẩm chuẩn hóa
     *
     * @param string $souceName
     * @return integer
     */
    private static function findStandardSource(string $souceName): int
    {
        $sql = "SELECT `id` FROM `order_source` WHERE `group_id` = 0 AND `ref_id` = 0 AND `name` = '$souceName' LIMIT 1";
        return intval(DB::fetch($sql, 'id'));
    }

    /**
     * Tạo mới nguồn marketing chuẩn hóa
     *
     * @param string $souceName
     * @return integer
     */
    private static function createStandardSource(string $souceName): int
    {
        $userId = Session::get('user_data')['user_id'] ?? 0;
        $data = [
            'name' => $souceName,
            'group_id' => 0,
            'ref_id' => 0,
            'created_time' => now(),
            'created_acc_id' => $userId,
            'is_active' => 1,
        ];

        return intval(DB::insert('order_source', $data));
    }

    /**
     * Bổ sung danh sách id nguồn marketing cũ 
     * vào nguồn chuẩn hóa mới
     *
     * @param integer $sourceId
     * @param array $includeIds
     * @return boolean
     */
    private static function updateIncludeId(int $sourceId, array $includeIds): bool
    {
        if (!$includeIds) {
            return false;
        } //end if

        $_includeIds = implode(',', $includeIds);
        $data = ['include_ids' => $_includeIds];
        $cond = "`id` = $sourceId";
        DB::update('order_source', $data, $cond);
        return true;
    }

    /**
     * Chuẩn hóa dữ liệu cũ
     * Đổi tên + gán ref_id (ID nguòn chuẩn hóa mới)
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
        DB::update('order_source', $data, $cond);
        return true;
    }
}
