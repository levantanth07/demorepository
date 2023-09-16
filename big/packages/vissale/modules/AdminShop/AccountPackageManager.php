<?php

class AccountPackageManager
{
    private $group = [];

    public function __construct()
    {
        try {
            require_once ROOT_PATH . 'packages/user/modules/AdminUserInfo/forms/information.php';
            // mysqli_begin_transaction(DB::$db_connect_id);

            if(!$this->group = $this->getGroup(URL::getUInt('group_id'))){
                return RequestHandler::sendJsonError();
            }

            // Insert thông tin gói
            if(!$packageGroup = $this->insertAccPackagesGroups()){
                throw new PalException('Thêm thông tin gói bị lỗi.');
            }

            // Cập nhật thông tin cho group
            if(!$shop = $this->updateShop($packageGroup['package_id'])){
                throw new PalException('Cập nhật thông tin nhóm bị lỗi.');
            }

            // Hủy kich hoạt user khi hạ cấp gói
            if(!$this->disableUserIfDowngradePackage($packageGroup['group_id'], $shop['user_counter'])){
                throw new PalException('Huỷ kích hoạt gói bị lỗi.');
            }

            // Bật tắt đồng bộ crm
            if($shop['palion_payment_status'] && $this->canSync($shop)) {
                $this->syncCrm($shop, !$this->isExpired($shop));
            }

            // mysqli_commit(DB::$db_connect_id);
        } catch (PalException $e) {
            // mysqli_rollback(DB::$db_connect_id);

            return RequestHandler::sendJsonError($e->getMessage());
        } catch (Throwable $e) {
            // mysqli_rollback(DB::$db_connect_id);

            return RequestHandler::sendException($e);
        } finally{
            mysqli_close(DB::$db_connect_id);
        }

        return RequestHandler::sendJsonSuccess('Done');
    }

    /**
     * Determines ability to synchronize.
     *
     * @param      array  $shop   The shop
     *
     * @return     bool   True if able to synchronize, False otherwise.
     */
    private function canSync(array $shop)
    {  
        switch($this->group['is_crm']) {
            // Đã tắt đồng bộ
            case AdminUserInfoInformationForm::CRM_OFF:
                return !$this->isExpired($shop);

            // Đã bật đồng bộ
            case AdminUserInfoInformationForm::CRM_ON:
                return $this->isExpired($shop);

            // Đang chờ bật
            case AdminUserInfoInformationForm::CRM_WAIT_ON:
                return $this->isExpired($shop);

            // Đang chờ tắt
            case AdminUserInfoInformationForm::CRM_WAIT_OFF:
                return !$this->isExpired($shop);
            
            default:
                return false;
        }
    }

    /**
     * { function_description }
     *
     * @param      array   $shop   The shop
     * @param      bool    $on
     *
     * @return
     */
    private function syncCrm(array $shop, bool $on = true)
    {
        return $this->markSyncCrm($on) && AdminUserInfoInformationForm::syncCrm($this->group['id'], $this->group, $on ? 3 : 2);
    }

    /**
     * { function_description }
     *
     * @param      bool    $on
     *
     * @return
     */
    private function markSyncCrm(bool $on = true)
    {
        return DB::update_id(
            'groups', 
            ['is_crm' => $on ? AdminUserInfoInformationForm::CRM_WAIT_ON : AdminUserInfoInformationForm::CRM_WAIT_OFF ], 
            $this->group['id']
        );
    }

    /**
     * Determines whether the specified shop is expired.
     *
     * @param      array  $shop   The shop
     *
     * @return     bool   True if the specified shop is expired, False otherwise.
     */
    private function isExpired(array $shop)
    {
        return Carbon\Carbon::parse($shop['palion_expired_at'])->lt(Carbon\Carbon::now());
    }


    /**
     * { function_description }
     *
     * @return
     */
    private function insertAccPackagesGroups()
    {
        $columns['group_id'] = $this->group['id'];// 15726
        $columns['billing_at'] = URL::getDateTimeFmt('billing_at', 'd/m/Y', 'Y-m-d');
        $columns['package_id'] = URL::getUInt('package_id');// 36
        $columns['months'] = URL::getUInt('months');// 12
        $columns['discount'] = URL::getUIntFormated('discount');// 1,026,000
        $columns['total_price'] = URL::getUIntFormated('total_price');// 2,394,000
        $columns['palion_payment_status'] =  URL::getUInt('palion_status');
        $columns['palion_paid_at'] = URL::getDateTimeFmt('palion_at', 'd/m/Y', 'Y-m-d', NULL_TIME);
        $columns['palion_expired_at'] = URL::getDateTimeFmt('palion_expired_at', 'd/m/Y', 'Y-m-d', NULL_TIME);
        $columns['palion_price'] =  URL::getUIntFormated('palion_price');
        
        return DB::insert('acc_packages_groups', $columns) ? $columns : false;
    }

    /**
     * Cập nhật thông tin gói cho shop
     *
     * @param      array   $packageGroup  The package group
     *
     * @return
     */
    private function updateShop(int $packageID)
    {
        // Lấy thông tin gói dịch vụ sử dụng
        $package = $this->getPackage($packageID);

        // Cập nhật thông tin gói cho shop
        $row_update = [
            'page_counter' =>  URL::getUInt('page_counter', $package['max_page']),
            'user_counter' => URL::getUInt('user_counter', $package['max_user']),

            'palion_payment_status' =>  URL::getUInt('palion_status'),
            'palion_paid_at' => URL::getDateTimeFmt('palion_at', 'd/m/Y', 'Y-m-d', NULL_TIME),
            'palion_expired_at' => URL::getDateTimeFmt('palion_expired_at', 'd/m/Y', 'Y-m-d', NULL_TIME),
            'palion_price' =>  URL::getUIntFormated('palion_price'),// 1,026,000

            'package_id' => $package['id'],
            'modified' => date('Y-m-d H:i:s'),
            'expired_date' => $this->getGroupExpiresDate($this->group['id'])
        ];

        if(!$row_update['user_counter']){
            throw new PalException('Số lượng user tối thiểu là 1.');
        }

        if($row_update['palion_payment_status'] && $row_update['palion_expired_at'] === NULL_TIME){
            throw new PalException('Vui lòng nhập ngày hết hạn palion !');
        }

        return DB::update_id('groups', $row_update, $this->group['id']) ? $row_update : false;
    }

    /**
     * Disables the user if downgrade package.
     *
     * @param      array  $shop   The shop
     */
    private function disableUserIfDowngradePackage(int $groupID, int $maxNumUser)
    {
        $numUserWillBeDisabled = self::getNumUserActiveOfGroup($groupID) - $maxNumUser;
        if($numUserWillBeDisabled > 0){
            $fmt = 'UPDATE `account` SET is_active = 0 WHERE account.group_id = %d AND id != "%s" AND is_active = 1 ORDER BY RAND() LIMIT %d';
            $sql = sprintf($fmt, $this->group['id'], $this->group['code'], $numUserWillBeDisabled);

            return DB::query($sql);
        }

        return true;
    }

    /**
     * Gets the number user of group.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The number user of group.
     */
    private static function getNumUserActiveOfGroup(int $groupID)
    {
        return DB::fetch('SELECT count(*) AS total FROM account WHERE is_active=1 AND group_id= ' . $groupID, 'total');
    }

    /**
     * Gets the package.
     *
     * @param      int     $packageID  The package id
     *
     * @return     <type>  The package.
     */
    private function getPackage(int $packageID)
    {
        return DB::fetch("SELECT id, max_user, max_page FROM acc_packages WHERE id = " . $packageID);
    }

    /**
     * Gets the group.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  The group.
     */
    private function getGroup(int $groupID)
    {
        return DB::fetch("SELECT id,expired_date, code, name, is_crm from `groups` WHERE id = " . $groupID);
    }

    /**
     * Gets the group expires date.
     *
     * @return     <type>  The group expires date.
     */
    private function getGroupExpiresDate()
    {
        if (!is_empty_date($this->group['expired_date'])) {
            return URL::getDateTimeFmt('update_expired_date', 'd/m/Y', 'Y-m-d 00:00:00');
        }

        return date('Y-m-d', strtotime('+' . Url::post('months') . ' months'));
    }
}
