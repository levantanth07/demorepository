<?php
class ReportForm extends Form
{   
    const SECONDS_IN_A_MONTH = 2678400; //31 ngay

    const TITLE = 'BÁO CÁO TỔNG HỢP SALE';

    private $noRevenueStatus = '';
    private $notReachStatus = '';

    private $sqlTimeStart = '';
    private $sqlTimeEnd = '';

    private $groupID = 0;
    
    /**
     * Constructs a new instance.
     */
    function __construct()
    {
        Form::Form('ReportForm');

        $this->groupID = Session::get('group_id');

        $this->mapRequest();
    }

    public function on_submit()
    {
        $this->validateDateTime();

        $statusList = DashboardDB::get_statuses(false);
        $users = DashboardDB::getUserNew('GANDON', URL::getUInt('is_active'));
        $userIDs = array_column($users, 'user_id');
        
        if(!$users){
            $this->map['users'] = [];
            $this->map['statusList'] = $statusList;
            return;
        }

        $this->noRevenueStatus = DashboardDB::get_no_revenue_status(); // các trạng thái không doanh thu
        $this->notReachStatus = DashboardDB::get_no_reached_status(); // các trạng thái ko tiếp cận

        // Thống kê doanh thu, số đơn hàng được chia
        $ordersAssigned = $this->getRevenueAndNumOrdersAssignedOfUsers($userIDs);

        // Thống kê doanh thu, số đơn hàng tiếp cận
        $ordersReach = $this->getRevenueAndNumOrdersReachOfUsers($userIDs);

        // Thống kê doanh thu, số đơn hàng chốt mới
        $ordersConfirmed = $this->getRevenueAndNumOrdersByTypeOfUsers(1, $userIDs);

        // Thống kê doanh thu, số đơn hàng chăm sóc
        $ordersCare = $this->getRevenueAndNumOrdersByTypeOfUsers(2, $userIDs);

        // Thống kê doanh thu, số đơn hàng đặt lại
        $ordersRelay = $this->getRevenueAndNumOrdersByTypeOfUsers('3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25', $userIDs);

        // Thống kê doanh thu, số đơn hàng crosssale, affliocate
        $ordersCross = $this->getRevenueAndNumOrdersByTypeOfUsers('26, 27', $userIDs);

        // Thống kê doanh thu, số đơn hàng tối ưu
        $ordersOptimize = $this->getRevenueAndNumOrdersByTypeOfUsers('9', $userIDs);

        // Thống kê doanh thu, số đơn hàng huy
        $ordersCancel = $this->getRevenueAndNumOrdersByStatusOfUsers(HUY, $userIDs);

        // Thống kê doanh thu, số đơn hàng chuyển hàng
        $ordersDelivered = $this->getRevenueAndNumOrdersByStatusOfUsers(CHUYEN_HANG, $userIDs);

        // Thống kê doanh thu, số đơn hàng chuyển hoàn
        $ordersReturn = $this->getRevenueAndNumOrdersByStatusOfUsers(CHUYEN_HOAN, $userIDs);

        $statusesStatistics  = $this->getStatusesStatistics($statusList, $userIDs);

        $reports = [];
        $total = ['status' => []];
        $default = ['total_price' => 0, 'total_order' => 0];
        foreach($users as $username=>$value){
            $user = [];
            $userID = $value['user_id'];
            $user['full_name'] = $value['full_name'];
            $user['id'] = $value['id'];
            $user['user_name'] = $value['username'];

            $user['cross']        = $this->calTotal($total['cross'], $ordersCross[$userID] ?? $default);
            $user['chia']        = $this->calTotal($total['chia'], $ordersAssigned[$userID] ?? $default);
            $user['tiep_can']    = $this->calTotal($total['tiep_can'], $ordersReach[$userID] ?? $default);
            $user['chot_moi']    = $this->calTotal($total['chot_moi'], $ordersConfirmed[$userID] ?? $default);
            $user['cham_soc']    = $this->calTotal($total['cham_soc'], $ordersCare[$userID] ?? $default);
            $user['dat_lai']     = $this->calTotal($total['dat_lai'], $ordersRelay[$userID] ?? $default);
            $user['toi_uu']      = $this->calTotal($total['toi_uu'], $ordersOptimize[$userID] ?? $default);
            $user['huy']         = $this->calTotal($total['huy'], $ordersCancel[$userID] ?? $default);
            $user['chuyen_hang'] = $this->calTotal($total['chuyen_hang'], $ordersDelivered[$userID] ?? $default);
            $user['chuyen_hoan'] = $this->calTotal($total['chuyen_hoan'], $ordersReturn[$userID] ?? $default);

            // TÌNH TRẠNG SỐ - TỔNG = SUM(CHỐT MỚI,Chăm sóc,Đặt lại,Tối ưu)
            $user['total_order'] = $user['chot_moi']['total_order'] + 
                                   $user['cham_soc']['total_order'] +
                                   $user['dat_lai']['total_order'] +
                                   $user['cross']['total_order'] +
                                   $user['toi_uu']['total_order'];
            $total['total_order'] = ($total['total_order'] ?? 0) + $user['total_order'];

            // DOANH THU - TỔNG = SUM(CHỐT MỚI,Chăm sóc,Đặt lại,Tối ưu)
            $user['total_price'] = $user['chot_moi']['total_price'] + 
                                   $user['cham_soc']['total_price'] +
                                   $user['dat_lai']['total_price'] +
                                   $user['cross']['total_price'] +
                                   $user['toi_uu']['total_price'];
            $total['total_price'] = ($total['total_price'] ?? 0) + $user['total_price'];

            // TỶ LỆ VỀ SỐ
            $user['rate'] = [
                'tiep_can'          => $this->calNumRate($user['tiep_can'], $user['chia']),
                'chot_moi_tiep_can' => $this->calNumRate($user['chot_moi'], $user['tiep_can']),
                'chot_moi_chia'     => $this->calNumRate($user['chot_moi'], $user['chia']),
                'huy'               => $this->calNumRate($user['huy'], $user['chot_moi']),
                'hoan'              => $this->calNumRate($user['chuyen_hoan'], $user['chuyen_hang']),
            ];



            // TỶ LỆ DOANH THU
            $user['revenue_rate'] = [
                'so_don'          => $this->calRate($user['chot_moi']['total_price'], $user['chot_moi']['total_order']),
                'sdt'             => $this->calRate($user['total_price'], $user['chia']['total_order']),
                'ngay'            => $this->calRate($user['chot_moi']['total_price'], $user['chia']['total_order']),
            ];


            $user['status'] = [];
            foreach ($statusesStatistics as $statusID => $statusStatistics) {
                $user['status'][$statusID] = $statusStatistics[$userID] ?? ['total_price' => 0, 'total_order' => 0];
                $this->calTotal($total['status'][$statusID], $user['status'][$statusID]);
            }

            $reports[$userID] = $user;
        }

        // TỶ LỆ VỀ SỐ
        $total['rate'] = [
            'tiep_can'          => $this->calNumRate($total['tiep_can'], $total['chia']),
            'chot_moi_tiep_can' => $this->calNumRate($total['chot_moi'], $total['tiep_can']),
            'chot_moi_chia'     => $this->calNumRate($total['chot_moi'], $total['chia']),
            'huy'               => $this->calNumRate($total['huy'], $total['chot_moi']),
            'hoan'              => $this->calNumRate($total['chuyen_hoan'], $total['chuyen_hang']),
        ];

        

        // TỶ LỆ DOANH THU
        $total['revenue_rate'] = [
            'so_don'          => $this->calRate($total['chot_moi']['total_price'], $total['chot_moi']['total_order']),
            'sdt'             => $this->calRate($total['total_price'], $total['chia']['total_order']),
            'ngay'            => $this->calRate($total['chot_moi']['total_price'], $total['chia']['total_order']),
        ];

        uasort($reports, function($a, $b){
            return $b['total_order'] - $a['total_order'];
        });

        $this->map['total'] = $total;
        $this->map['users'] = $reports;
        $this->map['statusList'] = $statusList;
    }

    /**
     * { function_description }
     *
     * @param      array  $total  The total
     * @param      array  $col    The col
     */
    private function calTotal(&$total, array $col)
    {
        $total['total_order'] = ($total['total_order'] ?? 0) + $col['total_order'];
        $total['total_price'] = ($total['total_price'] ?? 0) + $col['total_price'];

        return $col;
    }

    /**
     * Tỉ lệ số đơn
     *
     * @param      <type>  $num1   The number 1
     * @param      <type>  $num2   The number 2
     */
    private function calNumRate($num1, $num2)
    {   
        return $this->calRatePercent($num1['total_order'], $num2['total_order']);
    }

    /**
     * Tính tỉ lệ phần trăm
     *
     * @param      <type>  $num1   The number 1
     * @param      <type>  $num2   The number 2
     */
    private function calRatePercent($num1, $num2)
    {   
        return $this->calRate($num1, $num2) * 100;
    }

    /**
     * Tính tỉ lệ
     *
     * @param      <type>  $num1   The number 1
     * @param      <type>  $num2   The number 2
     */
    private function calRate($num1, $num2)
    {   
        return $num2 != 0 ? round($num1 / $num2, 2) : 0;
    }

    /**
     * { function_description }
     */
    public function draw()
    {   
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH']) && !Dashboard::$xem_khoi_bc_sale){
            Url::access_denied();
        }
        // date
        $this->map['date_from'] = $this->mapDate('date_from');
        $this->map['date_to'] = $this->mapDate('date_to');

        // team
        $this->map['account_group_id_list'] = [''=>'Tất cả các nhóm tài khoản'] + MiString::get_list(DashboardDB::getAccountGroup());

        $this->map += DashboardDB::get_report_info();

        $this->parse_layout('tong_hop_sale',$this->map);
    }

    /**
     * { function_description }
     */
    private function mapRequest()
    {
        // date
        $this->map['date_from'] = $this->mapDate('date_from');
        $this->map['date_to'] = $this->mapDate('date_to');

        $this->sqlTimeStart = $this->mapSQLDate('date_from') . ' 00:00:00';
        $this->sqlTimeEnd = $this->mapSQLDate('date_to') . ' 23:59:59';

    }

    /**
     * { function_description }
     */
    private function validateDateTime()
    {
        if(URL::getTimeFmt('date_to', 'd/m/Y') - URL::getTimeFmt('date_from', 'd/m/Y') > self::SECONDS_IN_A_MONTH){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }
    }

    /**
     * { function_description }
     *
     * @param      string  $reqParamName  The request parameter name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function mapDate(string $reqParamName)
    {
        return URL::getDateTimeFmt($reqParamName, 'd/m/Y', 'd/m/Y', date('d/m/Y'));
    }

    /**
     * { function_description }
     *
     * @param      string  $reqParamName  The request parameter name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function mapSQLDate(string $reqParamName)
    {
        return URL::getDateTimeFmt($reqParamName, 'd/m/Y', 'Y-m-d');
    }

    /**
     * Lấy thống kê số đơn hàng và doanh thu được chia của từng user
     *
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The revenue and number orders assigned of users.
     */
    private function getRevenueAndNumOrdersAssignedOfUsers(array $userIDs)
    {
        $wheres = [];
        $wheres[] = 'orders.group_id = ' . $this->groupID;
        $wheres[] = 'AND orders.user_assigned IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.assigned >= "' . $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.assigned <= "' . $this->sqlTimeEnd . '"';

        return $this->getRevenueAndCountOrderByCond('orders.user_assigned', $wheres);
    }

    /**
     * Lấy thống kê số đơn hàng và doanh thu tiếp cận của từng user
     *
     * @param      array   $userIDs  The user ids
     *
     * @return     <type>  The revenue and number orders reach of users.
     */
    private function getRevenueAndNumOrdersReachOfUsers(array $userIDs)
    {
        $wheres = [];
        $wheres[] = 'orders.group_id = ' . $this->groupID;
        $wheres[] = 'AND orders.user_assigned IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.assigned >= "' . $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.assigned <= "' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.status_id != '. CHUA_XAC_NHAN;

        if ($this->notReachStatus) {
            $wheres[] = 'AND orders.status_id NOT IN (' . $this->notReachStatus . ')';
        }

        return $this->getRevenueAndCountOrderByCond('orders.user_assigned', $wheres);
    }

    /**
     * Lấy thống kê số đơn hàng và doanh thu của từng user theo loai don
     *
     * @param      string  $type     The type
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The revenue and number orders by type of users.
     */
    private function getRevenueAndNumOrdersByTypeOfUsers(string $type = '', array $userIDs)
    {
        $wheres = [];
        $wheres[] = 'orders.group_id = ' . $this->groupID;
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.confirmed >= "' . $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed <= "' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.type IN (' . $type . ')';
        $wheres[] = 'AND orders.status_id NOT IN (' . $this->noRevenueStatus . ',' . CHUYEN_HOAN .', '. HUY . ')';

        return $this->getRevenueAndCountOrderByCond('orders.user_confirmed', $wheres);
    }

    /**
     * Lấy thống kê số đơn hàng và doanh thu của từng user theo trạng thái
     *
     * @param      array   $userIDs  The user ids
     *
     * @return     <type>  The revenue and number orders reach of users.
     */
    private function getRevenueAndNumOrdersByStatusOfUsers(string $status = '', array $userIDs)
    {
        $wheres = [];
        $wheres[] = 'orders.group_id = ' . $this->groupID;
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.confirmed >= "' . $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed <= "' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.status_id = '. $status;

        return $this->getRevenueAndCountOrderByCond('orders.user_confirmed', $wheres);
    }

    /**
     * Gets the revenue and count order by condition.
     *
     * @param      array   $cond   The condition
     *
     * @return     <type>  The revenue and count order by condition.
     */
    private function getRevenueAndCountOrderByCond(string $userField, $cond = [])
    {
        return DB::fetch_all("
            SELECT 
                COUNT(orders.id) AS total_order, SUM(orders.total_price) AS total_price, $userField as id 
            FROM orders
            LEFT JOIN orders_extra ON orders.id = orders_extra.order_id
            WHERE 
                " . implode(" ", $cond) . "
            GROUP BY $userField
            "
        );
    }

    /**
     * Gets the statuses statistics.
     *
     * @param      array  $statusList  The status list
     * @param      array  $userIDs     The user i ds
     *
     * @return     array  The statuses statistics.
     */
    private function getStatusesStatistics(array $statusList, array $userIDs)
    {
        $statistics = [];
        foreach ($statusList as $statusID => $status) {
            switch($statusID){
                case HUY:
                case CHUYEN_HOAN:
                case CHUYEN_HANG:
                case XAC_NHAN:
                    $statistics[$statusID] = $this->getCommonStatistics($userIDs, $statusID);
                    break;

                case KE_TOAN:
                    $statistics[$statusID] = $this->getAccountingStatistics($userIDs);
                    break;

                case THANH_CONG:
                    $statistics[$statusID] = $this->getSuccessedStatistics($userIDs);
                    break;

                case DA_THU_TIEN:
                    $statistics[$statusID] = $this->getPaidedStatistics($userIDs);
                    break;

                default:
                    $statistics[$statusID] = $this->getOtherStatistics($userIDs, $statusID);
            }
        }

        return $statistics;
    }

    /**
     * Gets the common statistics.
     *
     * @param      array   $userIDs   The user i ds
     * @param      int     $statusID  The status id
     *
     * @return     <type>  The common statistics.
     */
    private function getCommonStatistics(array $userIDs, int $statusID = null)
    {   
        $wheres = $this->getWhereCommon($statusID); 
        $wheres[] = 'AND orders.confirmed>="' . $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed<="' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.status_id = ' . $statusID;
        
        return $this->getRevenueAndCountOrderByCond('user_confirmed', $wheres);
    }

    /**
     * Gets the accounting statistics.
     *
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The accounting statistics.
     */
    private function getAccountingStatistics(array $userIDs)
    {   
        $wheres = $this->getWhereCommon(); 
        $wheres[] = 'AND orders.confirmed>="'. $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed<="' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.status_id = ' . KE_TOAN;

        // Thời gian ke toan phải lớn hơn thời gian xác nhận
        $wheres[] = 'AND orders_extra.accounting_confirmed  >= orders.confirmed';
        
        return $this->getRevenueAndCountOrderByCond('user_confirmed', $wheres);
    }

    /**
     * Gets the successed statistics.
     *
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The successed statistics.
     */
    private function getSuccessedStatistics(array $userIDs)
    {   
        $wheres = $this->getWhereCommon(); 

        $wheres[] = 'AND orders.confirmed >="'. $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed <="' . $this->sqlTimeEnd . '"';

        // Thời gian thanh cong phải lớn hơn thời gian xác nhận
        $wheres[] = 'AND orders_extra.update_successed_time >= orders.confirmed';
        $wheres[] = 'AND orders.status_id = ' . THANH_CONG;
        $wheres[] = 'and orders.user_confirmed IN (' . implode(',', $userIDs) . ')';

        return $this->getRevenueAndCountOrderByCond('user_confirmed', $wheres);
    }

    /**
     * Gets the paided statistics.
     *
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The paided statistics.
     */
    private function getPaidedStatistics(array $userIDs)
    {   
        $wheres = $this->getWhereCommon(); 
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.confirmed>="'. $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.confirmed<="' . $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders_extra.update_paid_time>="'. $this->sqlTimeStart . '"';

        // Đơn đã qua trạng thái đã thu tiền
        $wheres[] = 'AND orders_extra.update_paid_time IS NOT NULL';
        $wheres[] = 'AND orders_extra.update_paid_user IS NOT NULL';

        // Thời gian thu tiền phải lớn hơn thời gian xác nhận
        $wheres[] = 'AND orders_extra.update_paid_time  >= orders.confirmed';

        // Chỉ lấy đơn ở trạng thái đã thu tiền 
        $wheres[] = 'AND orders.status_id = ' . DA_THU_TIEN;

        return $this->getRevenueAndCountOrderByCond('user_confirmed', $wheres);
    }

    /**
     * Gets the other statistics.
     *
     * @param      array   $userIDs  The user i ds
     *
     * @return     <type>  The other statistics.
     */
    private function getOtherStatistics(array $userIDs, int $statusID)
    {   
        $wheres = $this->getWhereCommon(); 
        $wheres[] = 'AND orders.created>="'. $this->sqlTimeStart . '"';
        $wheres[] = 'AND orders.created<="'. $this->sqlTimeEnd . '"';
        $wheres[] = 'AND orders.user_confirmed IN (' . implode(',', $userIDs) . ')';
        $wheres[] = 'AND orders.status_id=' . $statusID;

        return $this->getRevenueAndCountOrderByCond('user_confirmed', $wheres);
    }


    /**
     * Gets the where common.
     *
     * @param      int    $statusID  The status id
     *
     * @return     array  The where common.
     */
    private function getWhereCommon(int $statusID = null)
    {
        $wheres = ['orders.group_id = ' . $this->groupID];

        if($statusID != HUY AND $this->noRevenueStatus){
            $wheres[] = 'AND orders.status_id NOT IN ('.$this->noRevenueStatus.')';
        }
        
        return $wheres;
    }

}
