<?php
class AdvDayDashboardForm extends Form
{   
    const FLASH_MESSAGE_KEY = 'adv_money_day_time';

    protected $map;
    protected $group_id;
    private $unknowTeamID = 1000000000;
    private $allTeams = [];
    private $allUsers = [];
    private $allUsersTeams = [];

    private $isTeamLead = false;
    private $isOwner = false;

    private $displayUserIDs = [];
    private $displayDates = [];
    private $displayUsersTeams = [];

    private $times = [
        '10h' => '10:00:00',
        '11h30' => '11:30:00',
        '14h' => '14:00:00',
        '15h30' => '15:30:00',
        '17h30' => '17:30:00',
        '23h' => '23:00:00',
        '24h' => '23:59:59'
    ];

    private $defaultParams = ['budget' => 0, 'total_order' => 0, 'revenue' => 0, 'new' => 0, 'care' => 0, 'order_new_and_care' => 0, 'budget_per_total_order' => 0, 'budget_per_new_order' => 0, 'budget_per_revenue' => 0, ];

    private $sum = [
        'teams' => [],
        'groups' => []
    ];

    protected $isObd;

    public function __construct()
    {
        Form::Form('AdvDayDashboardForm');

        $this->group_id = Session::get('group_id');
        $this->no_revenue_status = DashboardDB::get_no_revenue_status();
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        if($this->isDenied()){
            return Url::access_denied();
        }

        $this->isObd = isObd();
        require_once ROOT_PATH . 'packages/user/modules/Dashboard/forms/AdvMoney/FilterCondition.php';
        FilterCondition::init();

        $this->map['rows'] = [];
        // Lấy danh sách tất cả team, user của shop
        ['teams' => $this->allTeams, 'users' => $this->allUsers, 'users_teams' => $this->allUsersTeams] = $this->getAllTeamsAndUser();
        ksort($this->allUsersTeams);
        
        if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) {
            $this->times = array_filter($this->times, function ($k) {
                return $k === '24h';
            }, ARRAY_FILTER_USE_KEY);
        }

        $this->map['times'] = array_keys($this->times);
        $this->map['select_times'] = Arr::of($this->times)->map(function($time, $slug){ return $slug;})->toArray();

        $selectedTimes = FilterCondition::getTimes();
        $this->map['display_select_times'] = in_array(0, $selectedTimes) ? $this->map['select_times'] : $selectedTimes;

        $this->map['rowname'] = $this->getRowNames();
        $this->map['rowspan_time'] = count($this->map['rowname']);
        $this->map['rowspan_date'] = count($this->map['display_select_times']) * $this->map['rowspan_time'];
        
    }  

    /**
     * Determines if denied.
     *
     * @return     bool  True if denied, False otherwise.
     */
    private function isDenied()
    {
        return 
        !($this->isTeamLead = is_account_group_manager()) 
        && !($this->isOwner = is_group_owner())
        && !Dashboard::$quyen_marketing
        && !Dashboard::$quyen_admin_marketing 
        && !Dashboard::$admin_group
        && !Dashboard::$xem_khoi_bc_marketing
         ;
    }

    public function on_submit()
    {   
        if(!FilterCondition::validateDayRange()){
            return;
        }

        // Danh sach users cần xem báo cáo
        $this->displayUserIDs = $this->getUsers();
        $this->displayDates = FilterCondition::getDates();
        $this->displayUsersTeams = $this->createDisplayUsersTeams();

        // Lấy danh sách ngân sách mkt đã khai báo của cac user cho các ngày và khoảng thời gian
        $budgets = $this->getBudget();

        // Tính toán số liệu thống kê cho ngân sách của các users, trả về danh sách thông kê mảng 2 chiều có key 1 là date,
        // key 2 là userID
        $this->userStatistics = $this->makeStatistics($budgets);

        $this->map['rows'] = [];
        foreach ($this->displayDates as $date) {
            $cells = [];

            foreach ($this->displayUsersTeams as $teamID => $usersIDs) {

                foreach ($usersIDs as $userID) {
                    $cells[$userID] = null;

                    $cells[$userID] = isset($this->userStatistics[$date][$userID]) ?
                        $this->userStatistics[$date][$userID]
                        : $this->getStatisticByUser(
                            $this->fakeBudget($userID, $this->group_id, $this->allUsers[$userID]['username'], $teamID,$date)
                        );
                }

                // tổng của team theo khung giờ
                if(isset($this->sum['teams'][$date][$teamID])){
                    $this->calculateStatisticOftime($this->sum['teams'][$date][$teamID]);
                }
            }
            
            // tổng của HKD theo khung giờ
            if(isset($this->sum['groups'][$date])){
                $this->calculateStatisticOftime($this->sum['groups'][$date]) ;  
            }
            
            $this->map['rows'][$date] = $cells;
        }

    } 

    /**
     * Calculates the statistic of time.
     *
     * @param      array  $data   The data
     *
     * @return     array  The statistic of time.
     */
    private function getBundles(){
        $group_id = Session::get('group_id');
        $sql = '
                select
                    bundles.id,bundles.name
                from
                    bundles
                WHERE
                    bundles.group_id='.$group_id.'
                order by
                    bundles.name
            ';
        $result = DB::fetch_all($sql);
        return $result;
    }
    private function getSource(){
        $group_id = Session::get('group_id');
        $sql = '
                select
                    order_source.id,order_source.name,order_source.default_select
                from
                    order_source
                where
                    order_source.group_id = '.$group_id.'
                    OR order_source.group_id=0
                order by
                    order_source.id
            ';
        $result = DB::fetch_all($sql);
        return $result;
    }
    private function calculateStatisticOfTime(array &$data)
    {
        foreach ($this->map['display_select_times'] as $timeSlug) {
            
            // Ngân sách chia tổng đơn
            $data[$timeSlug]['budget_per_total_order'] = $data[$timeSlug]['total_order'] > 0 ? round($data[$timeSlug]['budget'] / $data[$timeSlug]['total_order'], 2) : 0;
            
            // Ngân sách chia đơn sale mới
            $data[$timeSlug]['budget_per_new_order'] = $data[$timeSlug]['new'] > 0 ? round($data[$timeSlug]['budget'] / $data[$timeSlug]['new'], 2): 0;
            
            // Ngân sách chia doanh thu
            $data[$timeSlug]['budget_per_revenue'] = $data[$timeSlug]['revenue'] > 0 ? round($data[$timeSlug]['budget'] * 100 / $data[$timeSlug]['revenue'], 2) : 0;
        }

        return $data;
    }

    public function draw()
    {
        $this->map['sum_teams'] = $this->sum['teams'];
        $this->map['sum_groups'] = $this->sum['groups'];

        // all
        $this->map['users'] = $this->allUsers;
        $this->map['teams'] = $this->allTeams;
        $this->map['users_teams'] = $this->allUsersTeams;

        // https://pm.tuha.vn/issues/11202
        // 1. Option lọc nhóm nhân viên
        // - Bổ sung giá trị lọc: Không có nhóm
        // - Quyền owner, admin, xem doanh thu mkt, quản lý mkt
        if($this->isOwner ||  Dashboard::$admin_group || Dashboard::$quyen_admin_marketing || Dashboard::$quyen_bc_doanh_thu_mkt || Dashboard::$xem_khoi_bc_marketing){
            $this->map['teams'] = [$this->unknowTeamID => ['id' => $this->unknowTeamID, 'name' => 'Không có nhóm']] + $this->map['teams'];
        }

        // display
        $this->map['display_users_teams'] = $this->displayUsersTeams;
        $this->map['display_user_ids'] = $this->displayUserIDs;
        $this->map['display_dates'] = $this->displayDates;
        if($this->isObd) {
            $bundles = DashboardDB::getBundles();
            $sources = DashboardDB::getSource();
        } else {
            $bundles = $this->getBundles();
            $sources = $this->getSource();
        }
        // bundle
        $this->map['bundle_id_list'] = '';
        $bundleRequest = [];
        $bundleRequest = Url::get('bundle_ids');
        $strBundleRequets = '';
        if(!empty($bundleRequest)){
            $bundleFormat = [];
            foreach ($bundleRequest as $value) {
                $bundleFormat[] = DB::escape($value);
            }
            foreach($bundles as $key=>$val){
                if (in_array($key,$bundleFormat)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            $strBundleRequets = implode(',', $bundleFormat);
        } else {
            foreach($bundles as $key=>$val){
                $selected = '';
                $this->map['bundle_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            
        }
        // source
        $this->map['source_id_list'] = '';
        $sourceRequest = [];
        $sourceRequest = Url::get('source_ids');
        $strSourceRequets = '';
        if(!empty($sourceRequest)){
            $sourceFormat = [];
            foreach ($sourceRequest as $value) {
                $sourceFormat[] = DB::escape($value);
            }
            foreach($sources as $key=>$val){
                if (in_array($key,$sourceFormat)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['source_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            $strSourceRequets = implode(',', $sourceFormat);
        } else {
            foreach($sources as $key=>$val){
                $selected = '';
                $this->map['source_id_list'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
            
        }

        $this->parse_layout('adv_money_day',$this->map);
    }

    /**
     * Gets the budget.
     *
     * @return     <type>  The budget.
     */
    private function getBudget()
    {
        $all = $this->_getBudget();
        // gắn team id vào từng row
        return Arr::of($all)->map(function($row) {
            if (isset($this->allUsers[$row['id']]) && $user = $this->allUsers[$row['id']]) {
                $row['team_id'] = $user['team_id'] ? $user['team_id'] : $this->unknowTeamID;
            } else {
                $row['team_id'] = $this->unknowTeamID;
            }
            

            return $row;
        })->toArray();
    }

    /**
     * Makes statistics.
     *
     * @param      array  $budgets  The budgets
     */
    private function makeStatistics(array $budgets)
    {
        return Arr::of($budgets)->reduce(function($output, $budget){
            if(!isset($output[$budget['date']])){
                $output[$budget['date']] = [];
            }

            $output[$budget['date']][$budget['id']] = $this->getStatisticByUser($budget);

            return $output;
        }, [])->toArray();
    }

    /**
     * Gets the budget.
     *
     * @return     <type>  The budget.
     */
    private function _getBudget()
    {   
        $sourceRequest = [];
        $sourceRequest = Url::get('source_ids');
        $strSourceRequets = '';
        if(!empty($sourceRequest)){
            $sourceFormat = [];
            foreach ($sourceRequest as $value) {
                $sourceFormat[] = DB::escape($value);
            }
            $strSourceRequets = implode(',', $sourceFormat);
        }
        $bundleRequest = [];
        $bundleRequest = Url::get('bundle_ids');
        $strBundleRequets = '';
        if(!empty($bundleRequest)){
            $bundleFormat = [];
            foreach ($bundleRequest as $value) {
                $bundleFormat[] = DB::escape($value);
            }
            $strBundleRequets = implode(',', $bundleFormat);
        }
        $condSource = '';
        $condBundle = '';
        if($strSourceRequets){
            $_strSourceRequets = $this->isObd 
                ? DashboardDB::getIncludeSourceIds($strSourceRequets, $this->group_id)
                : $strSourceRequets;

            $condSource = ' and vs_adv_money.source_id  IN ('.$_strSourceRequets.') ';
        }
        if($strBundleRequets){
            $_strBundleRequets = $this->isObd 
                ? DashboardDB::getIncludeBundleIds($strBundleRequets, $this->group_id)
                : $strBundleRequets;

            $condBundle = ' and vs_adv_money.bundle_id  IN ('.$_strBundleRequets.') ';
        }
        $sql = '
            SELECT
                users.id
                ,vs_adv_money.group_id
                ,vs_adv_money.account_id
                ,vs_adv_money.total
                ,vs_adv_money.`date`
                ,SUM(vs_adv_money.time_slot_1) AS budget_10h
                ,SUM(vs_adv_money.time_slot_2) AS budget_11h30
                ,SUM(vs_adv_money.time_slot_3) AS budget_14h
                ,SUM(vs_adv_money.time_slot_4) AS budget_15h30
                ,SUM(vs_adv_money.time_slot_5) AS budget_17h30
                ,SUM(vs_adv_money.time_slot_6) AS budget_23h
                ,SUM(vs_adv_money.time_slot_7) AS budget_24h
            FROM
                vs_adv_money
            JOIN users ON users.username = vs_adv_money.account_id
            WHERE
                vs_adv_money.group_id='.$this->group_id.'
                and vs_adv_money.date>="'.FilterCondition::getTimeFrom().'" 
                and vs_adv_money.date<="'.FilterCondition::getTimeTo().'"
                ' . $condSource .  $condBundle . ($this->displayUserIDs ? 'and users.id IN (' . implode(',' , $this->displayUserIDs) . ')' : '') . '
            GROUP BY
                vs_adv_money.`date`, vs_adv_money.`account_id`
            ORDER BY
                vs_adv_money.`date` DESC
        ';

        return DB::fetch_all_array($sql);
    }

    /**
     * { function_description }
     *
     * @param      int     $userID     The user id
     * @param      int     $groupID    The group id
     * @param      string  $accountID  The account id
     * @param      string  $teamID     The team id
     * @param      string  $date       The date
     *
     * @return     array   ( description_of_the_return_value )
     */
    private function fakeBudget(int $userID, int $groupID, string $accountID, int $teamID, string $date)
    {
        return [
            'id' => $userID,
            'group_id' => $groupID,
            'account_id' => $accountID,
            'total' => 0,
            'date' => $date,
            'team_id' => $teamID,
            'budget_10h' => 0,
            'budget_11h30' => 0,
            'budget_14h' => 0,
            'budget_15h30' => 0,
            'budget_17h30' => 0,
            'budget_23h' => 0,
            'budget_24h' => 0,
        ];
    }

    /**
     * Gets the row names.
     *
     * @return     array  The row names.
     */
    private function getRowNames()
    {
        return [
            'budget' => 'Ngân sách MKT',
            'total_order' => 'Tổng data',
            'budget_per_total_order' => '$/Data',
            'new' => 'Số đơn sale mới',
            'care' => 'Số đơn tối ưu',
            'order_new_and_care' => 'Tổng số đơn mới',
            'revenue' => 'Doanh thu đơn sale mới',
            'budget_per_new_order' => '$/Đơn Sale mới',
            'budget_per_revenue' => 'Ngân sách MKT/Doanh thu đơn mới',
        ];
    }

    /**
     * Gets the default time slug parameters.
     *
     * @return     <type>  The default time slug parameters.
     */
    private function getDefaultTimeSlugParams()
    {   
        return Arr::of($this->times)->filter(function($time, $slug){
            return in_array($slug, $this->map['display_select_times']);
        })
        ->map(function($time, $slug){
            return $this->defaultParams;
        })
        ->toArray();
    }

    /**
     * Tính toán thống kê thông số cho user
     *
     * @return     array
     */
    private function getStatisticByUser($row)
    {
        $teamID = $row['team_id'];
        if(!isset($this->sum['teams'][$row['date']][$teamID])){
            $this->sum['teams'][$row['date']][$teamID] = $this->getDefaultTimeSlugParams();
        }

        if(!isset($this->sum['groups'][$row['date']])){
            $this->sum['groups'][$row['date']] = $this->getDefaultTimeSlugParams();
        }

        if(!$result = DB::fetch($this->buildSql($row))){
            return [];
        }

        $row += $result;
        $init = [];

        return Arr::of($this->map['display_select_times'])
            // phan lo ket qua
            // [10h] => Array ([budget] => 143 [total_order] => 1 [revenue] => 2 ... )
            // [11h30] => Array ([budget] => 3454 [total_order] => 3 [revenue] => 4 ... )
            // ...
            ->reduce(function($results, $timeSlug) use($row, $teamID){

                $row['total_order_' . $timeSlug] = $row['total_order_' . $timeSlug] > 0 ? $row['total_order_' . $timeSlug] : 0;
                $row['revenue_' . $timeSlug] = $row['revenue_' . $timeSlug] > 0 ? $row['revenue_' . $timeSlug] : 0;
                $row['new_' . $timeSlug] = $row['new_' . $timeSlug] > 0 ? $row['new_' . $timeSlug] : 0;
                $row['care_' . $timeSlug] = $row['care_' . $timeSlug] > 0 ? $row['care_' . $timeSlug] : 0;

                // tổng của hkd theo khung giờ
                $sumGroup = $this->sum['groups'][$row['date']][$timeSlug];
                // tổng của team theo khung giờ
                $sumTeam = $this->sum['teams'][$row['date']][$teamID][$timeSlug];

                // Ngân sách
                $results[$timeSlug]['budget'] = $row['budget_' . $timeSlug];
                $sumTeam['budget'] += $results[$timeSlug]['budget'];
                $sumGroup['budget'] += $results[$timeSlug]['budget'];

                // tổng đơn
                $results[$timeSlug]['total_order'] = intval($row['total_order_' . $timeSlug]);
                $sumTeam['total_order'] += $results[$timeSlug]['total_order'];
                $sumGroup['total_order'] += $results[$timeSlug]['total_order'];

                // doanh thu chốt đơn
                $results[$timeSlug]['revenue'] = intval($row['revenue_' . $timeSlug]);
                $sumTeam['revenue'] += $results[$timeSlug]['revenue'];
                $sumGroup['revenue'] += $results[$timeSlug]['revenue'];

                // số đơn khách mới
                $results[$timeSlug]['new'] = intval($row['new_' . $timeSlug]);
                $sumTeam['new'] += $results[$timeSlug]['new'];
                $sumGroup['new'] += $results[$timeSlug]['new'];

                // số đơn tối ưu
                $results[$timeSlug]['care'] = intval($row['care_' . $timeSlug]);
                $sumTeam['care'] += $results[$timeSlug]['care'];
                $sumGroup['care'] += $results[$timeSlug]['care'];

                // tổng đơn khách mới + sale
                $results[$timeSlug]['order_new_and_care'] = intval($row['new_' . $timeSlug] + $row['care_' . $timeSlug]);
                $sumTeam['order_new_and_care'] += $results[$timeSlug]['order_new_and_care'];
                $sumGroup['order_new_and_care'] += $results[$timeSlug]['order_new_and_care'];

                // Ngân sách chia tổng đơn
                $results[$timeSlug]['budget_per_total_order'] = $row['total_order_' . $timeSlug] > 0 ? round($row['budget_' . $timeSlug] / $row['total_order_' . $timeSlug]) : 0;

                // Ngân sách chia đơn sale mới
                $results[$timeSlug]['budget_per_new_order'] = $row['new_' . $timeSlug] > 0 ? round($row['budget_' . $timeSlug] / $row['new_' . $timeSlug]) : 0;

                // Ngân sách chia doanh thu
                $results[$timeSlug]['budget_per_revenue'] = $row['revenue_' . $timeSlug] > 0 ? round($row['budget_' . $timeSlug] * 100 / $row['revenue_' . $timeSlug], 2) : 0;


                $this->sum['groups'][$row['date']][$timeSlug] = $sumGroup;
                $this->sum['teams'][$row['date']][$teamID][$timeSlug] = $sumTeam;

                return $results;
            }, $init)

            ->merge([
                'id' => $row['id'],
                'group_id' => $row['group_id'],
                'account_id' => $row['account_id'],
                'total' => $row['total'],
                'date' => $row['date'],
            ])
            ->toArray();
    }

    /**
     *  xây dựng câu truy vấn để tính thống kê cho user
     *
     * @return     string
     */
    private function buildSql($row)
    {
        $sourceRequest = [];
        $sourceRequest = Url::get('source_ids');
        $strSourceRequets = '';
        if(!empty($sourceRequest)){
            $sourceFormat = [];
            foreach ($sourceRequest as $value) {
                $sourceFormat[] = DB::escape($value);
            }
            $strSourceRequets = implode(',', $sourceFormat);
        }
        $bundleRequest = [];
        $bundleRequest = Url::get('bundle_ids');
        $strBundleRequets = '';
        if(!empty($bundleRequest)){
            $bundleFormat = [];
            foreach ($bundleRequest as $value) {
                $bundleFormat[] = DB::escape($value);
            }
            $strBundleRequets = implode(',', $bundleFormat);
        }
        $condSource = '';
        $condBundle = '';
        if($strSourceRequets){
            $_strSourceRequets = $this->isObd 
                ? DashboardDB::getIncludeSourceIds($strSourceRequets, $this->group_id)
                : $strSourceRequets;

            $condSource = ' and o.source_id  IN ('.$_strSourceRequets.') ';
        }
        if($strBundleRequets){
            $_strBundleRequets = $this->isObd 
                ? DashboardDB::getIncludeBundleIds($strBundleRequets, $this->group_id)
                : $strBundleRequets;

            $condBundle = ' and o.bundle_id  IN ('.$_strBundleRequets.') ';
        }
        $revenueStatusCondition = $this->no_revenue_status ? ' AND o.status_id NOT IN ('.$this->no_revenue_status.')' : '';

        $sql_sum1 = '';
        $sql_sum2 = '';
        $sql_sum3 = '';
        $sql_sum4 = '';
        if (!empty($this->times)) {
            foreach ($this->times as $k => $v) {
                $confirmedCondition_{$k} = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times[$k] .  '"';
                $createdCondition_{$k} = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times[$k] .  '"';

                $sql_sum1 .= 'SUM(IF(' . $createdCondition_{$k} . ', 1, 0)) as total_order_'.$k.',';
                $sql_sum2 .= 'SUM(IF(' . $confirmedCondition_{$k}   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_'.$k.',';
                $sql_sum3 .= 'SUM(IF(' . $confirmedCondition_{$k}   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_'.$k.',';
                $sql_sum4 .= 'SUM(IF(' . $confirmedCondition_{$k}   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_'.$k.',';
            }
        }
        $sql_sum4 = substr($sql_sum4, 0, -1);

        return '
            SELECT o.id, '.$sql_sum1.$sql_sum2.$sql_sum3.$sql_sum4.' 
            FROM orders as o
            WHERE 
                o.group_id=' . $row['group_id'] . '
                AND o.user_created=' . $row['id'] . '
                AND (
                    (o.created >= "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' 23:59:59")
                    OR (o.confirmed >= "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' 23:59:59")
                )';
        
//        $confirmedCondition_10h   = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['10h'] .  '"';
//        $confirmedCondition_11h30 = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['11h30'] .  '"';
//        $confirmedCondition_14h   = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['14h'] .  '"';
//        $confirmedCondition_15h30 = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['15h30'] .  '"';
//        $confirmedCondition_17h30 = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['17h30'] .  '"';
//        $confirmedCondition_23h   = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['23h'] .  '"';
//        $confirmedCondition_24h   = 'o.confirmed > "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' ' . $this->times['24h'] .  '"';
//
//        $createdCondition_10h   = 'o.created <= "' . $row['date'] . ' ' . $this->times['10h'] .  '"';
//        $createdCondition_11h30 = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['11h30'] .  '"';
//        $createdCondition_14h   = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['14h'] .  '"';
//        $createdCondition_15h30 = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['15h30'] .  '"';
//        $createdCondition_17h30 = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['17h30'] .  '"';
//        $createdCondition_23h   = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['23h'] .  '"';
//        $createdCondition_24h   = 'o.created > "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' ' . $this->times['24h'] .  '"';
//
//        return '
//            SELECT o.id, 
//                -- tổng đơn
//                SUM(IF(' . $createdCondition_10h . ', 1, 0)) as total_order_10h,
//                SUM(IF(' . $createdCondition_11h30 . ', 1, 0)) as total_order_11h30,
//                SUM(IF(' . $createdCondition_14h . ', 1, 0)) as total_order_14h,
//                SUM(IF(' . $createdCondition_15h30 . ', 1, 0)) as total_order_15h30,
//                SUM(IF(' . $createdCondition_17h30 . ', 1, 0)) as total_order_17h30,
//                SUM(IF(' . $createdCondition_23h . ', 1, 0)) as total_order_23h,
//                SUM(IF(' . $createdCondition_24h . ', 1, 0)) as total_order_24h,
//
//                -- doanh thu chốt đơn sale mới
//                SUM(IF(' . $confirmedCondition_10h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_10h,
//                SUM(IF(' . $confirmedCondition_11h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_11h30,
//                SUM(IF(' . $confirmedCondition_14h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_14h,
//                SUM(IF(' . $confirmedCondition_15h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_15h30,
//                SUM(IF(' . $confirmedCondition_17h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_17h30,
//                SUM(IF(' . $confirmedCondition_23h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_23h,
//                SUM(IF(' . $confirmedCondition_24h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), o.total_price, 0)) as revenue_24h,
//
//                -- số đơn khách mới
//                SUM(IF(' . $confirmedCondition_10h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_10h,
//                SUM(IF(' . $confirmedCondition_11h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_11h30,
//                SUM(IF(' . $confirmedCondition_14h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_14h,
//                SUM(IF(' . $confirmedCondition_15h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_15h30,
//                SUM(IF(' . $confirmedCondition_17h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_17h30,
//                SUM(IF(' . $confirmedCondition_23h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_23h,
//                SUM(IF(' . $confirmedCondition_24h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (0, 1), 1, 0)) as new_24h,
//
//                -- số đơn tối ưu
//                SUM(IF(' . $confirmedCondition_10h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_10h,
//                SUM(IF(' . $confirmedCondition_11h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_11h30,
//                SUM(IF(' . $confirmedCondition_14h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_14h,
//                SUM(IF(' . $confirmedCondition_15h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_15h30,
//                SUM(IF(' . $confirmedCondition_17h30 . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_17h30,
//                SUM(IF(' . $confirmedCondition_23h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_23h,
//                SUM(IF(' . $confirmedCondition_24h   . $revenueStatusCondition . $condSource . $condBundle . ' AND o.type IN (9), 1, 0)) as care_24h
//
//            FROM orders as o
//            WHERE 
//                o.group_id=' . $row['group_id'] . '
//                AND o.user_created=' . $row['id'] . '
//                AND (
//                    (o.created >= "' . $row['date'] . ' 00:00:00" AND o.created <= "' . $row['date'] . ' 23:59:59")
//                    OR (o.confirmed >= "' . $row['date'] . ' 00:00:00" AND o.confirmed <= "' . $row['date'] . ' 23:59:59")
//                )';
    }

    /**
     * Gets all teams and user.
     *
     * @return     <type>  All teams and user.
     */
    private function getAllTeamsAndUser()
    {   

        $reduceInit = [
            'teams' => [], 
            'users' => [],
            'users_teams' => [
                $this->unknowTeamID => [] // đây là team mặc định cho các user đơn lẻ
            ]
        ];

        if($this->isOwner || Dashboard::$quyen_admin_marketing || Dashboard::$admin_group || Dashboard::$xem_khoi_bc_marketing){
            // xem  hết cuar shop 
            $canViewCondition = '';
        }else if(is_account_group_department() && $account_group_ids = get_account_group_ids() && !Dashboard::$quyen_xem_bc_doi_nhom) {
            $canViewCondition = ' AND account.account_group_id IN ('.$account_group_ids.')';
        } else if(Dashboard::$quyen_xem_bc_doi_nhom && get_account_id() && !is_account_group_manager()){
            $canViewCondition = ' AND account.account_group_id = '.get_account_id().' ';
        }else if(Dashboard::$quyen_xem_bc_doi_nhom && get_account_id() && is_account_group_manager() && ( !is_account_group_department() || is_account_group_department() )){
            $canViewCondition = ' AND account.account_group_id IN ('.DashboardDB::merAccountGroup().')';
        }else if($this->isTeamLead){
            // xem cua team
            $canViewCondition = ' AND account_group.admin_user_id = ' . get_user_id();
        }else if(Dashboard::$quyen_marketing){
            // xem của nó
            $canViewCondition = ' AND account.id = "' . Session::get('user_id') . '"';
        }else{
            return $reduceInit;
        }

        $isActiveCondition = '';
        switch(FilterCondition::getIsActive()){
            case 1:
                $isActiveCondition = ' AND account.is_active = 1';
                break;

            case 2:
                $isActiveCondition = ' AND (account.is_active = 0 OR account.is_active IS NULL)';
                break;
        }

        $sql = '
            SELECT account.id as username,users.id as user_id, account_group.name as team_name, account_group.id as team_id, IFNULL(account.is_active, 0) AS is_active
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            LEFT JOIN users_roles ON  users_roles.user_id = users.id
            JOIN roles ON roles.id = users_roles.role_id
            JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
            WHERE 
                account.group_id = ' . $this->group_id . ' AND roles_to_privilege.privilege_code IN ("MARKETING") '
                . $isActiveCondition
                . $canViewCondition 
            . '
            GROUP BY account.id, roles_to_privilege.privilege_code';
        $results = DB::fetch_all_array($sql);

        

        return Arr::of($results)->reduce(function($output, $row) {
            //Xây dựng danh sách nhóm
            $teamID = intval($row['team_id']);
            if($teamID && !isset($output['teams'][$teamID])){
                $output['teams'][$teamID] = ['id' => $row['team_id'], 'name' => $row['team_name']];
                $output['users_teams'][$teamID] = [];
            }

            // xây dựng danh sách người dùng
            $userID = $row['user_id'];
            if(!isset($output['users'][$userID])){
                $output['users'][$userID] = ['id' => $userID, 'username' => $row['username'], 'is_active' => $row['is_active'], 'team_id' => $teamID];
            }

            $output['users_teams'][$teamID ? $teamID : $this->unknowTeamID][] = $userID;
            return $output;
        }, $reduceInit)
        ->toArray();
    }

    /**
     * Gets the filter users.
     */
    private function getUsers()
    {   
        $users = Arr::of(FilterCondition::getUsers());

        return $users->exists(0) ? array_keys($this->allUsers) : $users->filter(function($e){
            return !!$this->allUsers[$e];
        })->toArray();
    }

    /**
     * Creates display users teams.
     *
     * @return     Array
     */
    private function createDisplayUsersTeams()
    {   
        return Arr::of($this->allUsersTeams)->map(function($team){
            return Arr::of($team)->filter(function($userID){
                return in_array($userID, $this->displayUserIDs);
            })->toArray();
        })
        ->filter(function($team){
            return $team;
        })
        ->toArray();
    }
}
?>
