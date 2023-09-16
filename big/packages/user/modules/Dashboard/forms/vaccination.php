<?php
class VaccinationForm extends Form
{   
    const FLASH_MESSAGE_KEY = 'VaccinationForm';
    const MAX_SYSTEM_SELECTED = 20;

    public $systemsSelectbox = '';
    private $userID = 0;
    private $systemGroup = [];

    public function __construct()
    {
        Form::Form('VaccinationForm');

        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">';
        $this->group_id = Session::get('group_id');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_js('packages/core/includes/js/helper.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        if(URL::getString('do') === 'vaccination_chart'){
            $this->link_js('https://code.highcharts.com/highcharts.js');
            $this->link_js('https://code.highcharts.com/modules/exporting.js');
            $this->link_js('https://code.highcharts.com/modules/data.js');
            $this->link_js('https://code.highcharts.com/modules/accessibility.js');
        }

        $this->userID = get_user_id();
        $this->systemGroup = Systems::getSystemByUserID(
            $this->userID, [
                'groups_system.id',
                'groups_system.name',
                'groups_system.icon_url',
                'groups_system.structure_id'
            ]
        );
        $this->systemsSelectbox = $this->selectBoxSystems();
    }  

    public function on_draw()
    {   
        switch(URL::getString('act')){
            case 'load_groups_by_system_id':

                // Nếu không tồn tại hệ thống mình quản lý thì dừng lại
                if(!isset($this->systemGroup['structure_id'])){
                    RequestHandler::sendJson([]);
                }

                // Số lượng hệ thống lớn hơn số tối đa được phép xem cũng stop 
                $systemIDs = $this->getRequestSystemIDs();
                if($systemIDs->count() > self::MAX_SYSTEM_SELECTED){
                    RequestHandler::sendJson([]);
                }

                RequestHandler::sendJson($this->getGroups($systemIDs->toArray()));

            case 'load_report':
                return $this->parse_layout(
                    'vaccination_report', 
                    $this->getGroupsStatistics(URL::getArray('groups'))
                );

            case 'load_report_full':
                return $this->parse_layout(
                    'vaccination_report_full', 
                    $this->getGroupsStatistics(URL::getArray('groups'))
                );

            case 'load_chart':
                RequestHandler::sendJson(
                    $this->createChartData(
                        $this->getGroupsStatistics(URL::getArray('groups'))
                    )
                );

            case 'export_report':
                return $this->exportReport();
        }

        $this->map['default_system_id'] = $this->systemGroup['id'] ?? 0;
        
        $this->parse_layout(
            URL::getString('do'), 
            $this->map
        );
    }

    /**
     * Gets the request system i ds.
     *
     * @return     Arr  The request system i ds.
     */
    private function getRequestSystemIDs()
    {
        return Arr::of(URL::getArray('systemID'))
            ->map(function($ID){
                return (int) $ID;
            })
            ->filter(function($ID){
                return $ID > 0;
            });
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function selectBoxSystems()
    {
        return !isset($this->systemGroup['structure_id']) ? '' : SystemsTree::selectBox(
            $this->systemGroup['structure_id'], 
            [   
                'selected' => URL::getUInt('system_group_id'), 
                'selectedType' => SystemsTree::SELECTED_CURRENT,
                'props' => [
                    'name' =>"system_group_id",
                    'id' =>"system_group_id",
                    'multiple' =>"multiple",
                    'class' =>"form-control",
                    'style' => 'display: none'
                ]
            ]
        );
    }

    /**
     * Gets the groups.
     *
     * @param      array   $systemIDs  The system i ds
     *
     * @return     <type>  The groups.
     */
    private function getGroups(array $systemIDs)
    {   
        $sql = '
            SELECT
                `groups`.`id`, `groups`.`name`
            FROM
                `groups`
            JOIN `groups_system` ON `groups_system`.`id` = `system_group_id`
            WHERE
                groups.active = 1 AND groups.expired_date >= "' . date('Y-m-d H:i:s') . '"
                AND ' . Systems::getIDStructureChildCondition($this->systemGroup['structure_id'])
                . $this->getChildSystemCondition($systemIDs);

        return DB::fetch_all($sql);
    }

    /**
     * Gets the child system condition.
     *
     * @param      array  $systemIDs  The system i ds
     */
    private function getChildSystemCondition(array $systemIDs)
    {   
        $sql = 'SELECT `structure_id` FROM `groups_system` WHERE `id` IN (' . implode(',', $systemIDs) . ')';
        
        $condition = Arr::of(DB::fetch_all_column($sql, 'structure_id'))
            ->map(function($structureID){
                return Systems::getIDStructureChildCondition($structureID);
            })
            ->join(' OR ');

        return $condition ? ' AND (' . $condition . ')' : '';
    }

    /**
     * { function_description }
     *
     * @param      array  $groupIDs  The group i ds
     */
    private function getGroupsStatistics(array $groupIDs)
    {   
        $calFields = [
            'num_users',
            'chua_xac_dinh', 'chua_tiem', 'mui_1', 'mui_2', 
            'mui_3', 'sk_chua_xac_dinh', 'sk_binh_thuong', 
            'sk_f0', 'sk_f1', 'sk_f2', 'sk_f3', 'sk_khac', 
        ];

        // Lấy số liệu từ DB
        $statistics = $this->getDatabaseStatistics($groupIDs);

        // Tính toán theo tỉ lệ phần trăm của các shop
        $statistics = Arr::of($statistics)
            ->map(function($group, $key, $ctx) use($calFields) {
                
                foreach ($calFields as $slug) {
                    $this->calPercent($group, $slug);
                }
                
                return $group;
            })
            ->toArray();

        // Tính tổng theo giá trị
        $sum = Arr::of($statistics)
            ->reduce(function($res, $group) use($calFields) {
                foreach ($calFields as $slug) {
                    $this->sum($res, $group, $slug);
                }

                return $res;
            }, [])
            ->toArray();
        $user = $sum;
        // Tính tổng theo phần trăm
        foreach ($calFields as $slug) {
            $this->calPercent($sum, $slug);
        }
        return ['statistics' => $statistics, 'sum' => $sum, 'num'=>$user];
    }

    /**
     * { function_description }
     *
     * @param      array   $group      The group
     * @param      string  $fieldName  The field name
     */
    private function calPercent(array &$group, $fieldName)
    {
        $num = number_format($group[$fieldName] * 100 / $group['num_users'], 2);
        $group[$fieldName . '_pc'] = preg_replace('#\.0+$#', '', $num);
    }

    /**
     * { function_description }
     *
     * @param      array   $res  The res
     * @param      array   $group       The group
     * @param      string  $fieldName   The field name
     * @param      bool    $sumPercent  The sum percent
     */
    private function sum(array &$res, array $group, string $fieldName)
    {   
        $res[$fieldName] = ($res[$fieldName] ?? 0) + $group[$fieldName];
    }

    /**
     * Gets the database statistics.
     *
     * @param      array   $groupIDs  The group i ds
     *
     * @return     <type>  The database statistics.
     */
    private function getDatabaseStatistics(array $groupIDs)
    {
        return DB::fetch_all(' 
            SELECT 
                groups.id,
                groups.name,
                COUNT(*) as num_users,
                SUM(IF(vaccination.count = 0 OR vaccination.count IS NULL, 1, 0)) as chua_xac_dinh, 
                SUM(IF(vaccination.count = 1, 1, 0)) as chua_tiem, 
                SUM(IF(vaccination.count = 2, 1, 0)) as mui_1, 
                SUM(IF(vaccination.count = 3, 1, 0)) as mui_2, 
                SUM(IF(vaccination.count = 4, 1, 0)) as mui_3,
                
                SUM(IF(vaccination.status = 0 OR vaccination.status IS NULL, 1, 0)) as sk_chua_xac_dinh, 
                SUM(IF(vaccination.status = 1, 1, 0)) as sk_binh_thuong, 
                SUM(IF(vaccination.status = 2, 1, 0)) as sk_f0, 
                SUM(IF(vaccination.status = 3, 1, 0)) as sk_f1, 
                SUM(IF(vaccination.status = 4, 1, 0)) as sk_f2, 
                SUM(IF(vaccination.status = 5, 1, 0)) as sk_f3, 
                SUM(IF(vaccination.status = 6, 1, 0)) as sk_khac
            FROM groups
            JOIN users ON users.group_id = groups.id
            JOIN account ON account.id = users.username
            LEFT JOIN vaccination ON users.id = vaccination.user_id
            WHERE 
                account.is_active = 1
                AND groups.id IN (' . implode(',', $groupIDs) . ')
            GROUP BY groups.id
        ');
    }

    /**
     * Creates a chart data.
     *
     * @param      array  $allStatistics  All statistics
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function createChartData(array $allStatistics)
    {
        return [
            'count' => [
                ['name' => 'Chưa xác định',  'y' => $allStatistics['sum']['chua_xac_dinh_pc'], 'num' => $allStatistics['sum']['chua_xac_dinh']],
                ['name' => 'Chưa tiêm',      'y' => $allStatistics['sum']['chua_tiem_pc'],     'num' => $allStatistics['sum']['chua_tiem']],
                ['name' => 'Mũi 1',          'y' => $allStatistics['sum']['mui_1_pc'],         'num' => $allStatistics['sum']['mui_1']],
                ['name' => 'Mũi 2',          'y' => $allStatistics['sum']['mui_2_pc'],         'num' => $allStatistics['sum']['mui_2']],
                ['name' => 'Mũi 3',          'y' => $allStatistics['sum']['mui_3_pc'],         'num' => $allStatistics['sum']['mui_3']],
            ],
            'status' => [
                ['name' => 'Chưa xác định',  'y' => $allStatistics['sum']['sk_chua_xac_dinh_pc'],'num' => $allStatistics['sum']['sk_chua_xac_dinh']],
                ['name' => 'Bình thường',    'y' => $allStatistics['sum']['sk_binh_thuong_pc'],  'num' => $allStatistics['sum']['sk_binh_thuong']],
                ['name' => 'F0',             'y' => $allStatistics['sum']['sk_f0_pc'],           'num' => $allStatistics['sum']['sk_f0']],
                ['name' => 'F1',             'y' => $allStatistics['sum']['sk_f1_pc'],           'num' => $allStatistics['sum']['sk_f1']],
                ['name' => 'F2',             'y' => $allStatistics['sum']['sk_f2_pc'],           'num' => $allStatistics['sum']['sk_f2']],
                ['name' => 'F3',             'y' => $allStatistics['sum']['sk_f3_pc'],           'num' => $allStatistics['sum']['sk_f3']],
                ['name' => 'Khác',           'y' => $allStatistics['sum']['sk_khac_pc'],         'num' => $allStatistics['sum']['sk_khac']],    
            ],              
        ];
    }
}
