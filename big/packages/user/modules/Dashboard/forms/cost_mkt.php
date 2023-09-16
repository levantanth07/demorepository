<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;

require_once ROOT_PATH . '/packages/core/includes/common/OrderTypes.php';
class CostMktForm extends Form
{
    const FLASH_MESSAGE_KEY = 'CostForm';
    const COOKIE_KEY = 'custom_columns_cpqc_mkt';
    const MAX_SYSTEM_SELECTED = 20;
    const DEFAULT_COLUMNS = [
        'name' => 'DS',
        'childs' => [
            'cpqc' => [
                'name' => 'CPQC',
            ],
            'sdt' => [
                'name' => 'Tổng SĐT',
            ],
            'cpqc/sdt' => [
                'name' => 'CPQC / SĐT',
            ],
            'block_so_moi' => [
                'name' => 'Đơn mới',
                'childs' => [
                    'so_don_sale_moi' => [
                        'name' => 'Đơn',
                    ],
                    'doanh_thu_sale_moi' => [
                      'name' => 'Điểm',
                    ],
                    'cpqc/so_don_sale_moi' => [
                        'name' => 'CPQC / Đơn',
                    ],
                    'cpqc/doanh_thu_sale_moi' => [
                        'name' => 'CPQC / Điểm',
                    ],
                ],
            ],
            'block_toi_uu' => [
                'name' => 'Đơn tối ưu',
                'childs' => [
                    'so_don_toi_uu' => [
                        'name' => 'Số đơn',
                    ],
                    'doanh_thu_toi_uu' => [
                        'name' => 'Điểm',
                    ],
                ],
            ],
            'block_cskh' => [
                'name' => 'Đơn CSKH',
                'childs' => [
                    'so_don_cskh' => [
                        'name' => 'Số đơn',
                    ],
                    'doanh_thu_cskh' => [
                        'name' => 'Điểm',
                    ],
                ],
            ],
            'block_total' => [
                'name' => 'Tổng',
                'childs' => [
                    'tong_so_don' => [
                        'name' => 'Đơn',
                    ],
                    'tong_doanh_thu' => [
                        'name' => 'Điểm',
                    ],
                    'cpqc/tong_so_don' => [
                        'name' => 'CPQC / Đơn',
                    ],
                    'cpqc/tong_doanh_thu' => [
                        'name' => 'CPQC / Điểm',
                    ],
                ],
            ],
            'so_nv_mkt' => [
                'name' => 'NV MKT',
            ],
            'so_nv_sale' => [
                'name' => 'NV SALE',
            ],
            'block_mkt' => [
                'name' => 'MKT',
                'childs' => [
                    'sdt/so_nv_mkt' => [
                        'name' => 'SĐT/MKT ',
                    ],
                    'cpqc/sdt' => [
                        'name' => 'CPQC/Data ',
                    ],
                    'doanh_thu_sale_moi/so_nv_mkt' => [
                        'name' => 'DT mới/MKT',
                    ],
                ],
            ],
            'block_sale' => [
                'name' => 'SALE',
                'childs' => [
                    'sdt/so_nv_sale' => [
                        'name' => 'SĐT/Sale',
                    ],
                    'tong_doanh_thu/so_nv_sale' => [
                        'name' => 'Điểm /Sale',
                    ],
                    'doanh_thu_cskh/tong_doanh_thu' => [
                        'name' => '%CSKH',
                    ],
                ],
            ],
            'id' => [
                'name' => 'Lợi nhuận',
            ],
        ],
    ];

    public $systemsSelectbox = '';
    private $userID = 0;
    private $systemGroup = [];

    public function __construct()
    {
        Form::Form('CostMktForm');

        require_once ROOT_PATH . 'packages/user/modules/Dashboard/forms/CostMkt/FilterCondition.php';
        FilterCondition::init();

        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">';
        $this->group_id = Session::get('group_id');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_js('assets/vissale/js/jquery-sortable-min.js');
        $this->link_js('packages/core/includes/js/helper.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        $this->userID = get_user_id();
        $this->systemGroup = Systems::getSystemByUserID(
            $this->userID,
            [
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
        switch (URL::getString('act')) {
            // Lấy danh sách shop thuộc hệ thống
            case 'load_groups_by_system_id':
                // Nếu không tồn tại hệ thống mình quản lý thì dừng lại
                if (!isset($this->systemGroup['structure_id'])) {
                    RequestHandler::sendJson([]);
                }

                // Số lượng hệ thống lớn hơn số tối đa được phép xem cũng stop
                $systemIDs = $this->getRequestSystemIDs();
                if ($systemIDs->count() > self::MAX_SYSTEM_SELECTED) {
                    RequestHandler::sendJson([]);
                }

                RequestHandler::sendJson($this->getGroupsBySystemsIDs($systemIDs->toArray()));

            // Xuất bảng thống kê
            case 'load_report':
                $statistics = FilterCondition::validateTimeRange()
                    ? $this->getGroupsStatistics(URL::getArray('groups'))
                    : [];

                return $this->parse_layout(
                    'cost_mkt_ajax',
                    [
                        'columns' => $this->prepareColumns(),
                        'statistics' => $statistics
                    ]
                );

            // Xuất excel
            case 'export_report':
                return $this->exportReport();

            default:
                $this->map['default_system_id'] = $this->systemGroup['id'] ?? 0;
                $this->map['date_from'] = FilterCondition::getDisplayTimeFrom();
                $this->map['date_to'] = FilterCondition::getDisplayTimeTo();
                $this->map['columns_config'] = self::DEFAULT_COLUMNS;
                
                $this->parse_layout('cost_mkt', $this->map);
        }
    }

    /**
     * Gets the request system i ds.
     *
     * @return     Arr  The request system i ds.
     */
    private function getRequestSystemIDs()
    {
        return Arr::of(URL::getArray('systemID'))
            ->map(function ($ID) {
                return (int) $ID;
            })
            ->filter(function ($ID) {
                return $ID > 0;
            });
    }

    public function on_submit()
    {
    }

    /**
     * { function_description }
     */
    private function exportReport()
    {
        $NUM_COLUMNS = 26;
        $row = 1;

        $__REPORT_EXPIRED = URL::get('report_expired');
        $__REPORT_HASH = URL::get('report_hash');
        $__REPORT_DATA = URL::get('report_data');
        $__REPORT_PROFIT = URL::get('report_profit');

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Xử lý dữ liệu cookie, bổ sung 2 trường stt và name vào header
        // (stt và name không được custom)
        $headers = $this->prepareColumns();
        $headers['childs'] = [
            'stt' => ['name' => 'STT'],
            'name' => ['name' => 'Tên HKD'],
        ] + $headers['childs'];

        // Tính toán số cột và row của các thuộc tính
        $headers = $this->calNumRowsAndCols($headers);
        $this->renderHeaders($sheet, $headers, 1);

        // Lấy danh sách cột đã custom
        $columnNames = $this->getColumnNames($headers);

        // Xử lý dữ liệu chính của bảng
        $__REPORT_PROFIT = json_decode($__REPORT_PROFIT, true) ?? [];
        $__REPORT_DATA = json_decode($__REPORT_DATA, true);
        
        $stt = 1;
        $__REPORT_DATA = array_reduce($__REPORT_DATA, function ($res, $cells) use (&$stt, $__REPORT_PROFIT) {
            $profit = $__REPORT_PROFIT[$cells['id'] ?? 'total'] ?? 0;

            $res[] = [
                'stt'                           =>  $stt++,
                'name'                          =>  $cells['name'],
                'cpqc'                          =>  $this->numFormat($cells['cpqc']),
                'sdt'                           =>  $this->numFormat($cells['sdt']),
                'cpqc/sdt'                      =>  $this->numFormat($cells['cpqc/sdt']),
                'so_don_sale_moi'               =>  $this->numFormat($cells['so_don_sale_moi']),
                'doanh_thu_sale_moi'            =>  $this->numFormat($cells['doanh_thu_sale_moi']),
                'cpqc/so_don_sale_moi'          =>  $this->numFormat($cells['cpqc/so_don_sale_moi']),
                'cpqc/doanh_thu_sale_moi'       =>  $this->numFormat($cells['cpqc/doanh_thu_sale_moi']),
                'so_don_toi_uu'                 =>  $this->numFormat($cells['so_don_toi_uu']),
                'doanh_thu_toi_uu'              =>  $this->numFormat($cells['doanh_thu_toi_uu']),
                'so_don_cskh'                   =>  $this->numFormat($cells['so_don_cskh']),
                'doanh_thu_cskh'                =>  $this->numFormat($cells['doanh_thu_cskh']),
                'tong_so_don'                   =>  $this->numFormat($cells['tong_so_don']),
                'tong_doanh_thu'                =>  $this->numFormat($cells['tong_doanh_thu']),
                'cpqc/tong_so_don'              =>  $this->numFormat($cells['cpqc/tong_so_don']),
                'cpqc/tong_doanh_thu'           =>  $this->numFormat($cells['cpqc/tong_doanh_thu']),
                'so_nv_mkt'                     =>  $this->numFormat($cells['so_nv_mkt']),
                'so_nv_sale'                    =>  $this->numFormat($cells['so_nv_sale']),
                'sdt/so_nv_mkt'                 =>  $this->numFormat($cells['sdt/so_nv_mkt']),
                'cpqc/sdt'                      =>  $this->numFormat($cells['cpqc/sdt']),
                'doanh_thu_sale_moi/so_nv_mkt'  =>  $this->numFormat($cells['doanh_thu_sale_moi/so_nv_mkt']),
                'sdt/so_nv_sale'                =>  $this->numFormat($cells['sdt/so_nv_sale']),
                'tong_doanh_thu/so_nv_sale'     =>  $this->numFormat($cells['tong_doanh_thu/so_nv_sale']),
                'doanh_thu_cskh/tong_doanh_thu' =>  $this->numFormat($cells['doanh_thu_cskh/tong_doanh_thu']),
                'id'                            =>  $profit ? $profit : '0',
            ];
            
            return $res;
        }, []);

        // Map dữ liệu được chọn, dữ liệu này sẽ được dùng để ghi vào bảng
        $colsSelected = [];
        foreach ($__REPORT_DATA as $key => $row) {
            $colsSelected[$key] = [];
            foreach ($columnNames as $columnName) {
                $colsSelected[$key][$columnName] = $row[$columnName];
            }
        }

        // Xóa dữ liệu đã nhận từ request - (CÓ THỂ) giúp giảm mức tiêu thụ ram
        unset($__REPORT_DATA);

        // Fill data vào lib sheet
        $sheet->fromArray($colsSelected, null, 'A3');

        // style cho header
        $sheetHeader = $sheet->getStyleByColumnAndRow(1, 1, $headers['cols'], 2);
        $sheetHeader->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('ff037db4');
        $sheetHeader->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
        // GHi dữ liệu ra cleint để client tải về
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="bao-cao-ty-le-doanh-thu.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }

    /**
     * Chuẩn bị danh sách cột để hiển thị
     *
     * @return     array
     */
    private function prepareColumns()
    {
        if (!$cookieValue = json_decode($_COOKIE[self::COOKIE_KEY], true)) {
            return self::DEFAULT_COLUMNS;
        }
        
        return $this->initNode(
            $this->initColumns($cookieValue, self::DEFAULT_COLUMNS)
        );
    }
    
    /**
     * Chuẩn bị danh sách custom để hiển thị
     * CÓ thể giá trị đã cookie không giống giá trị thực tế.
     * Vì vậy cần có bước điền hoặc xóa các field không đúng hoặc thiếu
     *
     * @param      {<type>}  current     The current
     * @param      {<type>}  defaultCfg  The default configuration
     * @return     {<type>}  { description_of_the_return_value }
     */
    private function initColumns(array $current, array $defaultCfg)
    {
        $current = $this->clearCurrent($current, $defaultCfg);
        $current = $this->fillCurrent($current, $defaultCfg);

        return $current;
    }

    /**
     * Xóa các thuộc tính có trong current mà không có trong default
     *
     * @param      array  $current     The current
     * @param      array  $defaultCfg  The default configuration
     */
    private function clearCurrent(array $current, array $defaultCfg)
    {
        foreach ($current as $key => $object) {
            if ($key !== 'checked' && !isset($defaultCfg[$key])) {
                unset($current[$key]);
            }

            if (is_array($current[$key])) {
                $current[$key] = $this->clearCurrent($current[$key], $defaultCfg[$key]);
            }
        }

        return $current;
    }

    /**
     * Thêm các thuộc tính có trong default mà không có trong current
     *
     * @param      array  $current     The current
     * @param      array  $defaultCfg  The default configuration
     */
    private function fillCurrent(array $current, array $defaultCfg)
    {
        foreach ($defaultCfg as $key => $object) {
            if (!isset($current[$key])) {
                $current[$key] = $object;
            }

            if (is_array($current[$key])) {
                $current[$key] = $this->fillCurrent($current[$key], $defaultCfg[$key]);
            }
        }

        return $current;
    }


    /**
     * Determines whether the specified node is node checked.
     *
     * @param      array  $node   The node
     */
    private function isNodeChecked(array $node)
    {
        return !isset($node['checked']) || $node['checked'] === true;
    }

    /**
     * { function_description }
     *
     * @param      array   $node   The node
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function initNode(array $node)
    {
        if (!$this->isNodeChecked($node)) {
            return;
        }
        
        unset($node['checked']);

        $hasChilds = !empty($node['childs']) && is_array($node['childs']);
        if (!$hasChilds) {
            return $node;
        }
        
        $node['childs'] = from($node['childs'])
            ->filter(function ($child) {
                return $this->isNodeChecked($child);
            })
            ->map(function ($child) {
                return $this->initNode($child);
            })
            ->toArray();
        
        return empty($node['childs']) ?: $node;
    }


    /**
     * Tính toán số cột và hàng của node
     *
     * @param      array   $node   The node
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function calNumRowsAndCols(array $node)
    {
        $childs = $node['childs'] ?? null;
        
        if (!is_array($childs)) {
            return $node;
        }
        
        $node['level'] = $node['level'] ?? 0;
        $node['cols'] = 0;
        foreach ($childs as $key => $child) {
            $child['level'] = $node['level'] + 1;
            $node['childs'][$key] = $this->calNumRowsAndCols($child);
            $node['cols'] += $node['childs'][$key]['cols'] ?? 1;
        }

        return $node;
    }

    /**
     * { function_description }
     *
     * @param      PhpOffice\PhpSpreadsheet\Worksheet\Worksheet  $sheet     The sheet
     * @param      array                                         $node      The node
     * @param      int                                           $startCol  The start col
     */
    private function renderHeaders(PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $node, int $startCol = 1)
    {
        $i = 1;
        foreach ($node['childs'] as $key => $child) {
            $childCols = $child['cols'] ?? 1;
            $sheet->setCellValueByColumnAndRow($startCol, $child['level'], $child['name']);
            $sheet->mergeCellsByColumnAndRow($startCol, $child['level'], $startCol + $childCols - 1, $child['level']);

            $this->renderHeaders($sheet, $child, $startCol);
            $startCol += $childCols;
        }
    }

    /**
     * Gets the column names.
     *
     * @param      array  $headers  The headers
     * @param      array  $results  The results
     *
     * @return     array  The column names.
     */
    private function getColumnNames(array $headers, array $results = [])
    {
        foreach ($headers['childs'] ?? [] as $key => $value) {
            if (!$value['childs']) {
                $results[] = $key;
            }
            
            $results = $this->getColumnNames($value, $results);
        }

        return $results;
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
    private function getGroupsBySystemsIDs(array $systemIDs)
    {
        $sql = '
            SELECT
                `groups`.`id`, `groups`.`name`
            FROM
                `groups`
            JOIN `groups_system` ON `groups_system`.`id` = `system_group_id`
            WHERE
                ' . Systems::getIDStructureChildCondition($this->systemGroup['structure_id'])
                . $this->getChildSystemCondition($systemIDs) . '
                AND groups.expired_date >= "' . date('Y-m-d H:i:s') . '"
                AND groups.active = 1
                ';

        return DB::fetch_all($sql);
    }

    /**
     * Gets the groups.
     *
     * @param      array   $systemIDs  The system i ds
     *
     * @return     <type>  The groups.
     */
    private function getGroups(array $groupIDs)
    {
        $sql = '
            SELECT
                `groups`.`id`, `groups`.`name`
            FROM
                `groups`
            JOIN `groups_system` ON `groups_system`.`id` = `system_group_id`
            WHERE
                ' . Systems::getIDStructureChildCondition($this->systemGroup['structure_id']) . '
                AND groups.id IN (' . implode(',', $groupIDs) . ')
                AND groups.expired_date >= "' . date('Y-m-d H:i:s') . '"
                AND groups.active = 1
                ';

        return DB::fetch_all_columns($sql, ['name'], 'id');
    }

    /**
     * Counts the number of users exists role by group i ds.
     *
     * @param      array   $groupIDs  The group i ds
     *
     * @return     <type>  Number of users exists role by group i ds.
     */
    private function countUsersExistsRoleByGroupIDs(array $groupIDs, string $code)
    {
        $sql = '
            SELECT users.group_id, count(*) as num
            FROM
                users
                JOIN account ON users.username = account.id
            WHERE 
                account.is_active = 1 
                AND users.group_id IN (' . from($groupIDs) ->join(',') . ')
                AND EXISTS(
                    SELECT NULL
                    FROM users_roles
                    JOIN roles ON roles.id = users_roles.role_id
                    JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
                    WHERE
                        users.id = users_roles.user_id AND roles_to_privilege.privilege_code = "' . $code . '"
                )
            GROUP BY `group_id`';

        return DB::fetch_all_column($sql, 'num', 'group_id');
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
        ->map(function ($structureID) {
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
        if (!$groups = $this->getGroups($groupIDs)) {
            return [];
        }
        
        $from = FilterCondition::getTimeFrom() . ' 00:00:00';
        $to = FilterCondition::getTimeTo() . ' 23:59:59';

        // Tính toán số lượng mkt, sale của các shop id
        $numMkt = $this->countUsersExistsRoleByGroupIDs($groupIDs, 'MARKETING');
        $numSale = $this->countUsersExistsRoleByGroupIDs($groupIDs, 'GANDON');
      
        // Chi phí quảng cáo
        $cost = $this->getCostMkt($groupIDs, $from, $to);
        
        // Số đơn và doanh thu được tạo
        $phones = $this->getOrdersCreated($groupIDs, $from, $to);

        // Số đơn và doanh thu sale mới
        $ordersSaleMoi = $this->getOrdersTypeSaleMoi($groupIDs, $from, $to);

        // Số đơn và doanh thu tối ưu
        $ordersToiUu = $this->getOrdersTypeToiUu($groupIDs, $from, $to);

        // Số đơn và doanh thu cskh
        $ordersCskh = $this->getOrdersTypeCSKH($groupIDs, $from, $to);

        $statistics = Arr::of($groups)
            ->map(function ($group, $groupID) use ($cost, $phones, $ordersSaleMoi, $ordersToiUu, $ordersCskh, $numMkt, $numSale) {
                $row = new stdClass;
                $row->name = $group['name'];
                $row->id = $groupID;

                // cpqc
                $row->cpqc = $this->calPoint($cost[$groupID] ?? 0);
                
                // tổng số dt
                $row->sdt = $phones[$groupID] ?? 0;
                
                // cpqc/sqdt
                $row->{'cpqc/sdt'} = $this->calRate($row->cpqc, $row->sdt);
                
                // SALE moi
                $row->so_don_sale_moi = $ordersSaleMoi[$groupID]['num_orders'] ?? 0;
                $row->doanh_thu_sale_moi = $this->calPoint($ordersSaleMoi[$groupID]['revenue_orders'] ?? 0);

                $row->{'cpqc/so_don_sale_moi'} = $this->calRate($row->cpqc, $row->so_don_sale_moi);
                $row->{'cpqc/doanh_thu_sale_moi'} = $this->calRate($row->cpqc, $row->doanh_thu_sale_moi);

                // CSKH
                $row->so_don_cskh = $ordersCskh[$groupID]['num_orders'] ?? 0;
                $row->doanh_thu_cskh = $this->calPoint($ordersCskh[$groupID]['revenue_orders'] ?? 0);


                $row->{'cpqc/so_don_cskh'} = $this->calRate($row->cpqc, $row->so_don_cskh);
                $row->{'cpqc/doanh_thu_cskh'} = $this->calRate($row->cpqc, $row->doanh_thu_cskh);

                // Toi uu
                $row->so_don_toi_uu = $ordersToiUu[$groupID]['num_orders'] ?? 0;
                $row->doanh_thu_toi_uu = $this->calPoint($ordersToiUu[$groupID]['revenue_orders'] ?? 0);

                // đơn sale mới + tối ưu + cskh
                $row->tong_so_don = $row->so_don_toi_uu + $row->so_don_sale_moi + $row->so_don_cskh;

                // doanh thu sale mới + tối ưu + cskh
                $row->tong_doanh_thu = $row->doanh_thu_sale_moi + $row->doanh_thu_toi_uu + $row->doanh_thu_cskh;

                // MKT
                $row->so_nv_mkt = $numMkt[$groupID] ?? 0;
                $row->{'sdt/so_nv_mkt'} = $this->calRate($row->sdt, $row->so_nv_mkt);
                // $row->{'cpqc/sdt'} = $this->calRate($row->cpqc, $row->sdt);
                $row->{'doanh_thu_sale_moi/so_nv_mkt'} = $this->calRate($row->doanh_thu_sale_moi, $row->so_nv_mkt);
                
                // SALE
                $row->so_nv_sale = $numSale[$groupID] ?? 0;
                $row->{'sdt/so_nv_sale'} = $this->calRate($row->sdt, $row->so_nv_sale);
                $row->{'tong_doanh_thu/so_nv_sale'} = $this->calRate($row->tong_doanh_thu, $row->so_nv_sale);
                $row->{'doanh_thu_cskh/tong_doanh_thu'} = $this->calRate($row->doanh_thu_cskh * 100, $row->tong_doanh_thu);
                
                $row->{'cpqc/tong_so_don'} = $this->calRate($row->cpqc, $row->tong_so_don);
                $row->{'cpqc/tong_doanh_thu'} = $this->calRate($row->cpqc, $row->tong_doanh_thu);

                return (array) $row;
            }, $groups)
            ->values()
            ->usort(function ($a, $b) {
                return $b['cpqc'] - $a['cpqc'] >= 0 ? 1 : -1;
            });

        // Tính số liệu dòng tổng
        $statistics->push([
            $statistics->reduce(function ($sumRow, $row) {
                Arr::of([
                    'cpqc',
                    'sdt',
                    'so_don_sale_moi',
                    'doanh_thu_sale_moi',
                    'so_don_toi_uu',
                    'doanh_thu_toi_uu',
                    'so_don_cskh',
                    'doanh_thu_cskh',
                    'tong_so_don',
                    'tong_doanh_thu',
                    'so_nv_mkt',
                    'so_nv_sale',
                ])
                ->map(function ($slug) use (&$sumRow, $row) {
                    $sumRow[$slug] = ($sumRow[$slug] ?? 0) + $row[$slug];
                });

                $sumRow['cpqc/sdt']                       = $this->calRate($sumRow['cpqc'], $sumRow['sdt']);
                $sumRow['cpqc/so_don_sale_moi']           = $this->calRate($sumRow['cpqc'], $sumRow['so_don_sale_moi']);
                $sumRow['cpqc/doanh_thu_sale_moi']        = $this->calRate($sumRow['cpqc'], $sumRow['doanh_thu_sale_moi']);
                $sumRow['cpqc/tong_so_don']               = $this->calRate($sumRow['cpqc'], $sumRow['tong_so_don']);
                $sumRow['cpqc/tong_doanh_thu']            = $this->calRate($sumRow['cpqc'], $sumRow['tong_doanh_thu']);

                $sumRow['cpqc/so_don_cskh']               = $this->calRate($sumRow['cpqc'], $sumRow['so_don_cskh']);
                $sumRow['cpqc/doanh_thu_cskh']            = $this->calRate($sumRow['cpqc'], $sumRow['doanh_thu_cskh']);
                $sumRow['sdt/so_nv_mkt']                  = $this->calRate($sumRow['sdt'], $sumRow['so_nv_mkt']);
                $sumRow['doanh_thu_sale_moi/so_nv_mkt']   = $this->calRate($sumRow['doanh_thu_sale_moi'], $sumRow['so_nv_mkt']);
                $sumRow['sdt/so_nv_sale']                 = $this->calRate($sumRow['sdt'], $sumRow['so_nv_sale']);
                $sumRow['tong_doanh_thu/so_nv_sale']      = $this->calRate($sumRow['tong_doanh_thu'], $sumRow['so_nv_sale']);
                $sumRow['doanh_thu_cskh/tong_doanh_thu']  = $this->calRate($sumRow['doanh_thu_cskh'] * 100, $sumRow['tong_doanh_thu']);
                return $sumRow;
            }, ['name' => 'Tổng'])
            ->toArray()
        ]);

        return $statistics->toArray();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $statistics  The statistics
     * @param      <type>  $index       The index
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function calRate($a, $b)
    {
        return $b > 0 ? round($a / $b, 2) : 0;
    }

    /**
     * { function_description }
     *
     * @param      int   $revenue  The revenue
     *
     * @return     int   ( description_of_the_return_value )
     */
    private function calPoint($revenue = 0)
    {
        return round($revenue / 10e5, 2);
    }


    /**
     * Gets the cost mkt.
     *
     * @param      array   $groupIDs  The group i ds
     * @param      string  $dateFrom  The date from
     * @param      string  $dateTo    The date to
     *
     * @return     <type>  The cost mkt.
     */
    private function getCostMkt(array $groupIDs, string $dateFrom, string $dateTo)
    {
        return DB::fetch_all_column('
            SELECT 
                `group_id` as id, 
                GREATEST(t1,t2,t3,t4,t5,t6,t7) AS `cost`
            FROM (
                SELECT `vs_adv_money`.`group_id`, 
                    SUM(time_slot_1) as t1, 
                    SUM(time_slot_2) as t2,
                    SUM(time_slot_3) as t3,
                    SUM(time_slot_4) as t4,
                    SUM(time_slot_5) as t5,
                    SUM(time_slot_6) as t6,
                    SUM(time_slot_7) as t7
                FROM `vs_adv_money`
                WHERE 
                    `vs_adv_money`.`group_id` IN (' . implode(',', $groupIDs) . ')
                    AND `vs_adv_money`.`date` BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"
                GROUP BY `vs_adv_money`.`group_id`
            ) tmp
            ', 'cost', 'id');
    }

    /**
     * Thống kê số đơn được tạo của các shop trong khoảng thời gian
     * Note: Nếu thay đổi vui lòng cập nhật lại mô tả
     *
     * @param      array   $groupIDs  The group i ds
     * @param      string  $dateFrom  The date from
     * @param      string  $dateTo    The date to
     */
    private function getOrdersCreated(array $groupIDs, string $dateFrom, string $dateTo)
    {
        return DB::fetch_all_column('
            SELECT 
                DISTINCT(orders.id),
                `orders`.`group_id` as id,
                COUNT(*) as num_orders
            FROM `orders`
            WHERE 
                `orders`.`group_id` IN (' . implode(',', $groupIDs) . ')

                -- Chỉ lấy đơn được tạo trong khoảng thời gian
                AND `orders`.`created` BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"
                
                AND EXISTS (
                    SELECT NULL 
                    FROM
                        users_roles
                    JOIN roles ON roles.id = users_roles.role_id
                    JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
                    WHERE 
                        orders.user_created = users_roles.user_id

                        -- chỉ lấy đơn có người tạo là mkt
                        AND roles_to_privilege.privilege_code = "MARKETING"
                )
            GROUP BY `orders`.`group_id`
        ', 'num_orders', 'id');
    }

    /**
     * Thống kê đơn sale mới
     *
     * @param      array   $groupIDs  The group i ds
     * @param      string  $dateFrom  The date from
     * @param      string  $dateTo    The date to
     */
    private function getOrdersTypeSaleMoi(array $groupIDs, string $dateFrom, string $dateTo)
    {
        // chỉ lấy đơn là loại sale số mới hoặc không có loại đơn
        $orderTypeCondition = '`orders`.`type` = ' . OrderTypes::SALE_SO_MOI . ' OR `orders`.`type` IS NULL';
        
        return  $this->getOrdersByType($groupIDs, $dateFrom, $dateTo, $orderTypeCondition);
    }

    /**
     * Thống kê đơn tối ưu
     *
     * @param      array   $groupIDs  The group i ds
     * @param      string  $dateFrom  The date from
     * @param      string  $dateTo    The date to
     */
    private function getOrdersTypeToiUu(array $groupIDs, string $dateFrom, string $dateTo)
    {
        // chỉ lấy đơn là loại toi uu
        $orderTypeCondition = '`orders`.`type` = ' . OrderTypes::TOI_UU;
        
        return  $this->getOrdersByType($groupIDs, $dateFrom, $dateTo, $orderTypeCondition);
    }

    /**
     * Thống kê đơn sale CSKH
     *
     * @param      array   $groupIDs  The group i ds
     * @param      string  $dateFrom  The date from
     * @param      string  $dateTo    The date to
     */
    private function getOrdersTypeCSKH(array $groupIDs, string $dateFrom, string $dateTo)
    {
        // chỉ lấy đơn là loại CSKH, Đặt lại, Đặt lại lần 1,2,..
        $types = [
            OrderTypes::CSKH,
            OrderTypes::DAT_LAI,
            OrderTypes::DAT_LAI_1,
            OrderTypes::DAT_LAI_2,
            OrderTypes::DAT_LAI_3,
            OrderTypes::DAT_LAI_4,
            OrderTypes::DAT_LAI_5,
            OrderTypes::DAT_LAI_6,
            OrderTypes::DAT_LAI_7,
            OrderTypes::DAT_LAI_8,
            OrderTypes::DAT_LAI_9,
            OrderTypes::DAT_LAI_10,
            OrderTypes::DAT_LAI_11,
            OrderTypes::DAT_LAI_12,
            OrderTypes::DAT_LAI_13,
            OrderTypes::DAT_LAI_14,
            OrderTypes::DAT_LAI_15,
            OrderTypes::DAT_LAI_16,
            OrderTypes::DAT_LAI_17,
            OrderTypes::DAT_LAI_18,
            OrderTypes::DAT_LAI_19,
            OrderTypes::DAT_LAI_20,
            OrderTypes::DAT_LAI_TREN_20,
        ];
        $orderTypeCondition = '`orders`.`type` IN (' . from($types)->join(',') . ')';
        
        return  $this->getOrdersByType($groupIDs, $dateFrom, $dateTo, $orderTypeCondition);
    }

    /**
     * Thống kê đơn được xác nhận, có tính doanh thu của các shop trong khoảng thời gian theo loại đơn
     * Tính theo nhân viên tạo đơn là MKT
     * Note: Nếu thay đổi vui lòng cập nhật lại mô tả
     *
     * @param      array   $groupIDs            The group i ds
     * @param      string  $dateFrom            The date from
     * @param      string  $dateTo              The date to
     * @param      string  $orderTypeCondition  The order type
     *
     * @return     <type>  The orders by type.
     */
    private function getOrdersByType(array $groupIDs, string $dateFrom, string $dateTo, string $orderTypeCondition)
    {
        return DB::fetch_all_columns('
            SELECT 
                DISTINCT(orders.id),
                orders.group_id AS id,
                COUNT(*) as num_orders,
                SUM(total_price) as revenue_orders
            
            FROM `orders`

            JOIN `statuses` ON `orders`.`status_id` = `statuses`.`id` 

            WHERE 
                `orders`.`group_id` IN (' . implode(',', $groupIDs) . ')

                -- Chỉ lấy đơn xác nhận trong khoảng thời gian
                AND `orders`.`confirmed` BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"
                
                -- chỉ lấy đơn ở trạng thái có tính doanh thu 
                AND (statuses.no_revenue = 0 OR statuses.no_revenue IS NULL) 

                -- chỉ lấy đơn là loại sale tối ưu
                AND (' . $orderTypeCondition . ')

                AND EXISTS (
                    SELECT NULL 
                    FROM
                        users_roles
                    JOIN roles ON roles.id = users_roles.role_id
                    JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
                    WHERE 
                        orders.user_created = users_roles.user_id

                        -- chỉ lấy đơn có người tạo là mkt
                        AND roles_to_privilege.privilege_code = "MARKETING"
                )
            GROUP BY `orders`.`group_id`
        ', ['num_orders', 'revenue_orders'], 'id');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $num    The number
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function numFormat($num)
    {
        return preg_replace('#\.?0+$#', '', number_format($num, 2));
    }

    /**
     * { function_description }
     *
     * @param      <type>   $val    The value
     * @param      Closure  $fn     The function
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public function passOrFail($val, Closure $fn)
    {
        return $fn($val) ? 'pass' : 'fail';
    }
}
