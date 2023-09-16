<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;

class CostHkdForm extends Form
{

   
    const FLASH_MESSAGE_KEY = 'CostHkdForm';
    const MAX_SYSTEM_SELECTED = 20;

    public $systemsSelectbox = '';
    private $userID = 0;
    private $systemGroup = [];
    private $latestUpdateTime = 0;

    public function __construct()
    {
        Form::Form('CostHkdForm');

        require_once ROOT_PATH . 'packages/user/modules/Dashboard/forms/Cost/FilterConditionHkd.php';
        FilterConditionHkd::init();

        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">';
        $this->group_id = Session::get('group_id');
        $this->link_js('assets/standard/js/multiple.select.js');
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

                RequestHandler::sendJson($this->getGroups($systemIDs->toArray()));

            case 'load_report_hkd':
                $statistics = [];
                if (FilterConditionHkd::validateTimeRange()) {
                    $statistics = $this->getDaysStatistics(
                        [Session::get('group_id')]
                    );
                }

                return $this->parse_layout(
                    'cost_hkd_ajax',
                    $statistics
                );

            case 'export_report_hkd':
                return $this->exportReport();

            case 're_cache_hkd':
                return $this->reCache();

            case 'json':
                return $this->json();
        }

        $this->map['default_system_id'] = $this->systemGroup['id'] ?? 0;
        $this->map['date_from'] = date('m/d/Y', FilterConditionHkd::getTimeFrom());
        $this->map['date_to'] = date('m/d/Y', FilterConditionHkd::getTimeTo());
        
        $this->parse_layout('cost_hkd', $this->map);
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
     * Thực thi việc cập nhật data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function reCache()
    {
        $days = $this->getDays()[0] ?? [];
        if (!$days || count($days) > 31) {
            RequestHandler::sendJsonError('THOI_GIAN_KHONG_HOP_LE');
        }

        if (!$days = $this->getValidDaysForReCache($days)) {
            RequestHandler::sendJsonError(date('d/m/Y H:i', $this->latestUpdateTime + AGGREGATE_RE_CACHE));
        }

        try {
            $client = new Client([
                'headers' => [
                    'Authorization' => 'hello guy, i\'m hacker',
                    'Content-Type' => 'application/json',
                ]
            ]);
    
            $response = $client->request('POST', HOST_API_QUEUE, [
                'json' => ['days' => $days]
            ]);

            return RequestHandler::sendJsonSuccess('OK');
        } catch (ClientException $e) {
            return Message::toString($e->getResponse());
        }
    }

    /**
     * Gets the valid days for re cache.
     *
     * @param      array  $days   The days
     */
    private function getValidDaysForReCache(array $days)
    {
        $sql = '
            SELECT `time`,`updated_at`
            FROM `cost_day`
            WHERE 
                `time` IN ("' . implode('", "', $days) . '")
            GROUP BY `time`
            ORDER BY updated_at DESC
        ';
        $times = DB::fetch_all_columns($sql, ['time', 'updated_at']);
        
        $times = from($times)->reduce(function ($res, $time) {
            $day = str_replace(' 00:00:00', '', $time['time']);
            $res[$day] = $time['updated_at'];

            return $res;
        }, [])->toArray();

        $timeLimit = time() - AGGREGATE_RE_CACHE;

        return from($days)->filter(function ($day) use ($times, $timeLimit) {
            if (!isset($times[$day])) {
                return true;
            }

            $updated = intval(strtotime($times[$day]));
            $this->latestUpdateTime = max($updated, $this->latestUpdateTime);

            return $updated < $timeLimit;
        }, [])->toArray();
    }

    /**
     * { function_description }
     */
    private function exportReport()
    {
        ['days' => $days, 'statistics' => $statistics] = $this->getDaysStatistics([Session::get('group_id')]);
        
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();

        $days = array_keys($days);
        $cells = [array_merge(['STT', 'Chỉ tiêu', 'Tổng', 'Tỷ lệ'], $days)];

        $key = 1;
        foreach ($statistics as $value) {
            $cells[] = array_reduce($days, function ($res, $day) use ($value) {
                $res[] = number_format($value[$day], 2);

                return $res;
            }, [
                $key++,
                strip_tags($value['name']),
                number_format($value['total'], 2),
                number_format($value['rate'], 2)
            ]);
        }

        $sheet = $spreadsheet->getActiveSheet()->fromArray($cells);
        $sheet->getStyleByColumnAndRow(1, 1, 1024, 1)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('ff037db4');
 
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
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function json()
    {
        $statistics = FilterConditionHkd::validateTimeRange() ? $this->getGroupsStatistics([Session::get('group_id')]) : [];

        return RequestHandler::sendJsonSuccess($statistics);
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
    private function getDaysStatistics(array $groupIDs)
    {
        $statistics = [
            'doanh_thu_chuyen' => ['name' => 'DT Chuyển', 'total' => 0, 'rate' => 0],

            'doanh_thu_so_moi' => ['name' => 'DT Chuyển (Số mới)', 'total' => 0, 'rate' => 0],
            'doanh_thu_cskh' => ['name' => 'DT Chuyển (Tối ưu + CSKH) <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="DT Chuyển (Tối ưu + CSKH) : Gồm đơn cskh, đặt lại lần 1,2...">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],

            'gia_von' => ['name' => 'Giá vốn <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Giá vốn = Giá vốn khai báo * SL Sản phẩm có trong đơn hàng

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_quang_cao' => ['name' => 'CPQC', 'total' => 0, 'rate' => 0],
            'chi_phi_luong' => ['name' => 'Chi phí lương <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Chi phí lương = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_cod' => ['name' => 'Cước COD <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Cước COD = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_cuoc_dt' => ['name' => 'Cước ĐT <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Cước ĐT = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_hoan' => ['name' => 'CP Hoàn <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP Hoàn = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_khac' => ['name' => 'CP khác (CP via BM,...CP truyền thông,..) <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP khác = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_tien_nha' => ['name' => 'CP Tiền nhà <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP Tiền nhà = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'loi_nhuan' => ['name' => 'Lợi nhuận <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Lợi nhuận = Doanh thu chuyển - Giá vốn - CPQC - Các loại chi phi

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],

            'total_order' => ['name' => 'Số đơn hàng', 'total' => 0, 'rate' => 0],
            'total_user_mkt' => ['name' => 'Nhân sự MKT', 'total' => 0, 'rate' => 0],
            'total_user_sale' => ['name' => 'Nhân sự Sale', 'total' => 0, 'rate' => 0],
            'hieu_suat_sale' => ['name' => 'Hiệu suất nhân sự Sale <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Hiệu suất nhân sự Sale = (Tổng DT Chuyển/Tổng NV Sale)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'hieu_suat_mkt' => ['name' => 'Hiệu suất nhân sự MKT <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Hiệu suất nhân sự MKT = (DT Chuyển (Số mới)/Tổng NV MKT)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'doanh_thu_don_hang' => ['name' => 'DT Chuyển/đơn hàng <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="DT Chuyển/đơn hàng = (Tổng DT Chuyển/Tổng đơn hàng)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
        ];

        [$days, $startDay, $endDay, $realStart, $realEnd] = $this->getDays();

        // Lấy khai báo chi phí, hiện tại là ngày đầu tiên trong thang
        $costs = $this->getCost($startDay, $groupIDs);

       
        // Lấy doanh thu các thứ theo tháng của shop, nhóm lại theo ngày
        $revenue = $this->getDayStatistics($realStart, $realEnd, $groupIDs);

        // chưa có dữ liệu để tính toán
        // if (!$costs && !$revenue) {
        //     return [];
        // }

        $total = [];
        $shortDayCurrent = date('d/m',strtotime($endDay));
        foreach ($days as $shortDay => $day) {
            $this->cal($statistics, $revenue, $costs, $shortDay);
            $statistics['doanh_thu_chuyen']['total']   += $statistics['doanh_thu_chuyen'][$shortDay];
            $statistics['gia_von']['total']            += $statistics['gia_von'][$shortDay];
            $statistics['chi_phi_quang_cao']['total']  += $statistics['chi_phi_quang_cao'][$shortDay];
            $statistics['chi_phi_luong']['total']      += $statistics['chi_phi_luong'][$shortDay];
            $statistics['chi_phi_cod']['total']        += $statistics['chi_phi_cod'][$shortDay];
            $statistics['chi_phi_cuoc_dt']['total']    += $statistics['chi_phi_cuoc_dt'][$shortDay];
            $statistics['chi_phi_hoan']['total']       += $statistics['chi_phi_hoan'][$shortDay];
            $statistics['chi_phi_khac']['total']       += $statistics['chi_phi_khac'][$shortDay];
            $statistics['chi_phi_tien_nha']['total']   += $statistics['chi_phi_tien_nha'][$shortDay];
            $statistics['loi_nhuan']['total']          += $statistics['loi_nhuan'][$shortDay];

            $statistics['doanh_thu_so_moi']['total']   += $statistics['doanh_thu_so_moi'][$shortDay];
            $statistics['doanh_thu_cskh']['total']     += $statistics['doanh_thu_cskh'][$shortDay];

            $statistics['total_order']['total']        += $statistics['total_order'][$shortDay];
            $statistics['total_user_mkt']['total']     = $statistics['total_user_mkt'][$shortDayCurrent];
            $statistics['total_user_sale']['total']    = $statistics['total_user_sale'][$shortDayCurrent];

            $statistics['hieu_suat_sale']['total']     = $statistics['total_user_sale']['total'] > 0 ? round($statistics['doanh_thu_chuyen']['total']/$statistics['total_user_sale']['total'], 2) : 0;
            $statistics['hieu_suat_mkt']['total']      = $statistics['total_user_mkt']['total'] > 0 ? round($statistics['doanh_thu_so_moi']['total']/$statistics['total_user_mkt']['total'], 2) : 0;
            $statistics['doanh_thu_don_hang']['total'] = $statistics['total_order']['total'] > 0 ? round($statistics['doanh_thu_chuyen']['total']/$statistics['total_order']['total'], 2) : 0;

            // $statistics['hieu_suat_sale']['total']     = $statistics['hieu_suat_sale'][$shortDay];
            // $statistics['hieu_suat_mkt']['total']      = $statistics['hieu_suat_mkt'][$shortDay];
            // $statistics['doanh_thu_don_hang']['total'] = $statistics['doanh_thu_don_hang'][$shortDay];

            // $shop['hieu_suat_sale'] = $shop['total_user_sale'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_sale'], 2) : 0;
            // $shop['hieu_suat_mkt'] = $shop['total_user_mkt'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_mkt'], 2) : 0;
            // $shop['doanh_thu_don_hang'] = $shop['total_order'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_order'], 2) : 0;
        }

        $statistics['doanh_thu_chuyen']['rate']    = $this->calRate($statistics, 'doanh_thu_chuyen');
        $statistics['gia_von']['rate']             = $this->calRate($statistics, 'gia_von');
        $statistics['chi_phi_quang_cao']['rate']   = $this->calRate($statistics, 'chi_phi_quang_cao');
        $statistics['chi_phi_luong']['rate']       = $this->calRate($statistics, 'chi_phi_luong');
        $statistics['chi_phi_cod']['rate']         = $this->calRate($statistics, 'chi_phi_cod');
        $statistics['chi_phi_cuoc_dt']['rate']     = $this->calRate($statistics, 'chi_phi_cuoc_dt');
        $statistics['chi_phi_hoan']['rate']        = $this->calRate($statistics, 'chi_phi_hoan');
        $statistics['chi_phi_khac']['rate']        = $this->calRate($statistics, 'chi_phi_khac');
        $statistics['chi_phi_tien_nha']['rate']    = $this->calRate($statistics, 'chi_phi_tien_nha');
        $statistics['loi_nhuan']['rate']           = $this->calRate($statistics, 'loi_nhuan');

        $statistics['doanh_thu_so_moi']['rate']   = $this->calRate($statistics, 'doanh_thu_so_moi');
        $statistics['doanh_thu_cskh']['rate']     = $this->calRate($statistics, 'doanh_thu_cskh');
        $statistics['total_order']['rate']        = 0;
        $statistics['total_user_mkt']['rate']     = 0;
        $statistics['total_user_sale']['rate']    = 0;
        $statistics['hieu_suat_sale']['rate']     = 0;
        $statistics['hieu_suat_mkt']['rate']      = 0;
        $statistics['doanh_thu_don_hang']['rate'] = 0;

        return [
            'days' => $days,
            'statistics' => $statistics
        ];
    }

    /**
     * Gets the groups statistics.
     *
     * @param      array  $groupIDs  The group i ds
     *
     * @return     array  The groups statistics.
     */
    private function getGroupsStatistics(array $groupIDs)
    {
        $statistics = [
            'doanh_thu_chuyen' => ['name' => 'DT Chuyển', 'total' => 0, 'rate' => 0],

            'doanh_thu_so_moi' => ['name' => 'DT Chuyển (Số mới)', 'total' => 0, 'rate' => 0],
            'doanh_thu_cskh' => ['name' => 'DT Chuyển (Tối ưu + CSKH) <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="DT Chuyển (Tối ưu + CSKH) : Gồm đơn cskh, đặt lại lần 1,2...">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],

            'gia_von' => ['name' => 'Giá vốn <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Giá vốn = Giá vốn khai báo * SL Sản phẩm có trong đơn hàng

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_quang_cao' => ['name' => 'CPQC', 'total' => 0, 'rate' => 0],
            'chi_phi_luong' => ['name' => 'Chi phí lương <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Chi phí lương = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_cod' => ['name' => 'Cước COD <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Cước COD = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_cuoc_dt' => ['name' => 'Cước ĐT <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Cước ĐT = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_hoan' => ['name' => 'CP Hoàn <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP Hoàn = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_khac' => ['name' => 'CP khác (CP via BM,...CP truyền thông,..) <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP khác = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'chi_phi_tien_nha' => ['name' => 'CP Tiền nhà <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="CP Tiền nhà = Tỷ lệ ước chừng * Doanh thu chuyển theo ngày

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'loi_nhuan' => ['name' => 'Lợi nhuận <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Lợi nhuận = Doanh thu chuyển - Giá vốn - CPQC - Các loại chi phi

">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],

            'total_order' => ['name' => 'Số đơn hàng', 'total' => 0, 'rate' => 0],
            'total_user_mkt' => ['name' => 'Nhân sự MKT', 'total' => 0, 'rate' => 0],
            'total_user_sale' => ['name' => 'Nhân sự Sale', 'total' => 0, 'rate' => 0],
            'hieu_suat_sale' => ['name' => 'Hiệu suất nhân sự Sale <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Hiệu suất nhân sự Sale = (Tổng DT Chuyển/Tổng NV Sale)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'hieu_suat_mkt' => ['name' => 'Hiệu suất nhân sự MKT <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Hiệu suất nhân sự MKT = (DT Chuyển (Số mới)/Tổng NV MKT)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
            'doanh_thu_don_hang' => ['name' => 'DT Chuyển/đơn hàng <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="DT Chuyển/đơn hàng = (Tổng DT Chuyển/Tổng đơn hàng)">
                                                <i class="fa fa-question-circle"></i>
                                            </a>', 'total' => 0, 'rate' => 0],
        ];

        [$days, $startDay, $endDay] = $this->getDays();

        // Lấy khai báo chi phí, hiện tại là ngày đầu tiên trong thang
        $costs = $this->getCost($startDay, $groupIDs);

        // Lấy doanh thu các thứ theo tháng của shop, nhóm lại theo ngày
        $revenue = $this->getDayStatistics(end($days), array_shift($days), $groupIDs, false);
        $revenue = from($revenue)->keyBy('group_id', false)->toArray();
 
        // chưa có dữ liệu để tính toán
        // if (!$costs && !$revenue) {
        //     return [];
        // }

        $total = [];
        foreach ($groupIDs as $groupID) {
            $this->calGroup($statistics, $revenue, $costs, $groupID);
            $statistics['doanh_thu_chuyen']['total']    += $statistics['doanh_thu_chuyen'][$groupID];
            $statistics['gia_von']['total']             += $statistics['gia_von'][$groupID];
            $statistics['chi_phi_quang_cao']['total']   += $statistics['chi_phi_quang_cao'][$groupID];
            $statistics['chi_phi_luong']['total']       += $statistics['chi_phi_luong'][$groupID];
            $statistics['chi_phi_cod']['total']         += $statistics['chi_phi_cod'][$groupID];
            $statistics['chi_phi_cuoc_dt']['total']     += $statistics['chi_phi_cuoc_dt'][$groupID];
            $statistics['chi_phi_hoan']['total']        += $statistics['chi_phi_hoan'][$groupID];
            $statistics['chi_phi_khac']['total']        += $statistics['chi_phi_khac'][$groupID];
            $statistics['chi_phi_tien_nha']['total']    += $statistics['chi_phi_tien_nha'][$groupID];
            $statistics['loi_nhuan']['total']           += $statistics['loi_nhuan'][$groupID];

            $statistics['doanh_thu_so_moi']['total']   += $statistics['doanh_thu_so_moi'][$groupID];
            $statistics['doanh_thu_cskh']['total']     += $statistics['doanh_thu_cskh'][$groupID];
            $statistics['total_order']['total']        += $statistics['total_order'][$groupID];
            $statistics['total_user_mkt']['total']     += $statistics['total_user_mkt'][$groupID];
            $statistics['total_user_sale']['total']    += $statistics['total_user_sale'][$groupID];

            $statistics['hieu_suat_sale']['total']     += $statistics['hieu_suat_sale'][$groupID];
            $statistics['hieu_suat_mkt']['total']      += $statistics['hieu_suat_mkt'][$groupID];
            $statistics['doanh_thu_don_hang']['total'] += $statistics['doanh_thu_don_hang'][$groupID];
        }

        $statistics['doanh_thu_chuyen']['rate']    = $this->calRate($statistics, 'doanh_thu_chuyen');
        $statistics['gia_von']['rate']             = $this->calRate($statistics, 'gia_von');
        $statistics['chi_phi_quang_cao']['rate']   = $this->calRate($statistics, 'chi_phi_quang_cao');
        $statistics['chi_phi_luong']['rate']       = $this->calRate($statistics, 'chi_phi_luong');
        $statistics['chi_phi_cod']['rate']         = $this->calRate($statistics, 'chi_phi_cod');
        $statistics['chi_phi_cuoc_dt']['rate']     = $this->calRate($statistics, 'chi_phi_cuoc_dt');
        $statistics['chi_phi_hoan']['rate']        = $this->calRate($statistics, 'chi_phi_hoan');
        $statistics['chi_phi_khac']['rate']        = $this->calRate($statistics, 'chi_phi_khac');
        $statistics['chi_phi_tien_nha']['rate']    = $this->calRate($statistics, 'chi_phi_tien_nha');
        $statistics['loi_nhuan']['rate']           = $this->calRate($statistics, 'loi_nhuan');

        $statistics['doanh_thu_so_moi']['rate']   = $this->calRate($statistics, 'doanh_thu_so_moi');
        $statistics['doanh_thu_cskh']['rate']     = $this->calRate($statistics, 'doanh_thu_cskh');
        $statistics['total_order']['rate']        = '';
        $statistics['total_user_mkt']['rate']     = '';
        $statistics['total_user_sale']['rate']    = '';
        $statistics['hieu_suat_sale']['rate']     = '';
        $statistics['hieu_suat_mkt']['rate']      = '';
        $statistics['doanh_thu_don_hang']['rate'] = '';

        $statistics['loi_nhuan']['total'] = $this->numFormat($statistics['loi_nhuan']['total'], 2);
        
        return $statistics;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $revenue  The revenue
     * @param      <type>  $costs    The costs
     * @param      <type>  $day      The day
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function cal(&$statistics, $revenue, $costs, $day)
    {
        $statistics['doanh_thu_chuyen'][$day]   = 0;
        $statistics['gia_von'][$day]            = 0;
        $statistics['chi_phi_quang_cao'][$day]  = 0;
        $statistics['chi_phi_luong'][$day]      = 0;
        $statistics['chi_phi_cod'][$day]        = 0;
        $statistics['chi_phi_cuoc_dt'][$day]    = 0;
        $statistics['chi_phi_hoan'][$day]       = 0;
        $statistics['chi_phi_khac'][$day]       = 0;
        $statistics['chi_phi_tien_nha'][$day]   = 0;
        $statistics['loi_nhuan'][$day]          = 0;

        $statistics['total_order'][$day]        = 0;
        $statistics['total_user_mkt'][$day]     = 0;
        $statistics['total_user_sale'][$day]    = 0;
        $statistics['hieu_suat_sale'][$day]     = 0;
        $statistics['hieu_suat_mkt'][$day]      = 0;
        $statistics['doanh_thu_don_hang'][$day] = 0;
        $statistics['doanh_thu_so_moi'][$day]   = 0;
        $statistics['doanh_thu_cskh'][$day]     = 0;

        foreach ($revenue as $shopRevenue) {
            if ($shopRevenue['time'] !== $day || !isset($costs[$shopRevenue['group_id']])) {
                continue;
            }

            // Tính chi phí của ngày hiện trại của shop
            // cách tính: chi phí shop khai báo * doanh thu shop / 100
            $shopCost = $costs[$shopRevenue['group_id']] ?? [];

            // Tính doanh thu, giá vốn, quảng cáo của shop ở ngày hiện tại đang xét
            $shop['doanh_thu_chuyen']  = round($shopRevenue['doanh_thu'] / 10e5, 2);
            $shop['chi_phi_quang_cao'] = round($shopRevenue['chi_phi_quang_cao'] / 10e5, 2);

            $shop['total_order']       = $shopRevenue['total_order'];
            $shop['total_user_mkt']    = $shopRevenue['total_user_mkt'];
            $shop['total_user_sale']   = $shopRevenue['total_user_sale'];
            $shop['doanh_thu_so_moi']  = $shopRevenue['doanh_thu_so_moi']/1000000;
            $shop['doanh_thu_cskh']    = $shopRevenue['doanh_thu_cskh']/1000000;

            // thay doi https://pm.tuha.vn/issues/12347
            // $shop['gia_von']           = round(($shopCost['gia_von'] ?? 0) * $shop['doanh_thu_chuyen'] / 100, 2);

            // issues BIG 14985 
            $shop['gia_von'] = $shopRevenue['von']/1000000;
            $shop['hieu_suat_sale'] = $shop['total_user_sale'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_sale'], 2) : 0;
            $shop['hieu_suat_mkt'] = $shop['total_user_mkt'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_mkt'], 2) : 0;
            $shop['doanh_thu_don_hang'] = $shop['total_order'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_order'], 2) : 0;

            $shop['chi_phi_luong']     = round($shop['doanh_thu_chuyen'] * ($shopCost['chi_phi_luong'] ?? 0) / 100, 2);
            $shop['chi_phi_cod']       = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_cod'] ?? 0) / 100, 2);
            $shop['chi_phi_cuoc_dt']   = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_dt'] ?? 0) / 100, 2);
            $shop['chi_phi_hoan']      = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_hoan'] ?? 0) / 100, 2);
            $shop['chi_phi_khac']      = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_khac'] ?? 0) / 100, 2);
            $shop['chi_phi_tien_nha']  = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_tien_nha'] ?? 0) / 100, 2);
            $shop['loi_nhuan']         = round($shop['doanh_thu_chuyen']
                                    - $shop['gia_von']
                                    - $shop['chi_phi_quang_cao']
                                    - $shop['chi_phi_luong']
                                    - $shop['chi_phi_cod']
                                    - $shop['chi_phi_cuoc_dt']
                                    - $shop['chi_phi_hoan']
                                    - $shop['chi_phi_khac']
                                    - $shop['chi_phi_tien_nha'], 2);

            // Cộng dồn vào thống kê ngày
            $statistics['doanh_thu_chuyen'][$day]   += round($shop['doanh_thu_chuyen'], 2);
            $statistics['gia_von'][$day]            += round($shop['gia_von'], 2);
            $statistics['chi_phi_quang_cao'][$day]  += round($shop['chi_phi_quang_cao'], 2);
            $statistics['chi_phi_luong'][$day]      += round($shop['chi_phi_luong'], 2);
            $statistics['chi_phi_cod'][$day]        += round($shop['chi_phi_cod'], 2);
            $statistics['chi_phi_cuoc_dt'][$day]    += round($shop['chi_phi_cuoc_dt'], 2);
            $statistics['chi_phi_hoan'][$day]       += round($shop['chi_phi_hoan'], 2);
            $statistics['chi_phi_khac'][$day]       += round($shop['chi_phi_khac'], 2);
            $statistics['chi_phi_tien_nha'][$day]   += round($shop['chi_phi_tien_nha'], 2);
            $statistics['loi_nhuan'][$day]          += round($shop['loi_nhuan'], 2);

            // $statistics['hieu_suat_sale'][$day]     += round($shop['hieu_suat_sale'], 2);
            // $statistics['hieu_suat_mkt'][$day]      += round($shop['hieu_suat_mkt'], 2);
            // $statistics['doanh_thu_don_hang'][$day] += round($shop['doanh_thu_don_hang'], 2);
            $statistics['total_order'][$day]        += round($shop['total_order'], 2);
            $statistics['total_user_mkt'][$day]     += round($shop['total_user_mkt'], 2);
            $statistics['total_user_sale'][$day]    += round($shop['total_user_sale'], 2);
            $statistics['doanh_thu_so_moi'][$day]   += round($shop['doanh_thu_so_moi'], 2);
            $statistics['doanh_thu_cskh'][$day]     += round($shop['doanh_thu_cskh'], 2);

            $statistics['hieu_suat_sale'][$day]     = $statistics['total_user_sale'][$day] > 0 ? round($statistics['doanh_thu_chuyen'][$day]/$statistics['total_user_sale'][$day], 2) : 0;
            $statistics['hieu_suat_mkt'][$day]      = $statistics['total_user_mkt'][$day] > 0 ? round($statistics['doanh_thu_so_moi'][$day]/$statistics['total_user_mkt'][$day], 2) : 0;
            $statistics['doanh_thu_don_hang'][$day] = $statistics['total_order'][$day] > 0 ? round($statistics['doanh_thu_chuyen'][$day]/$statistics['total_order'][$day], 2) : 0;
        }
    }

    /**
     * { function_description }
     *
     * @param      <type>  $revenue  The revenue
     * @param      <type>  $costs    The costs
     * @param      <type>  $day      The day
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function calGroup(&$statistics, $revenue, $costs, $groupID)
    {
        $statistics['doanh_thu_chuyen'][$groupID]   = 0;
        $statistics['gia_von'][$groupID]            = 0;
        $statistics['chi_phi_quang_cao'][$groupID]  = 0;
        $statistics['chi_phi_luong'][$groupID]      = 0;
        $statistics['chi_phi_cod'][$groupID]        = 0;
        $statistics['chi_phi_cuoc_dt'][$groupID]    = 0;
        $statistics['chi_phi_hoan'][$groupID]       = 0;
        $statistics['chi_phi_khac'][$groupID]       = 0;
        $statistics['chi_phi_tien_nha'][$groupID]   = 0;
        $statistics['loi_nhuan'][$groupID]          = 0;

        $statistics['total_order'][$groupID]        = 0;
        $statistics['total_user_mkt'][$groupID]     = 0;
        $statistics['total_user_sale'][$groupID]    = 0;
        $statistics['hieu_suat_sale'][$groupID]     = 0;
        $statistics['hieu_suat_mkt'][$groupID]      = 0;
        $statistics['doanh_thu_don_hang'][$groupID] = 0;
        $statistics['doanh_thu_so_moi'][$groupID]   = 0;
        $statistics['doanh_thu_cskh'][$groupID]     = 0;

        // Tính chi phí của shop
        // cách tính: chi phí shop khai báo * doanh thu shop / 100
        $shopCost = $costs[$groupID] ?? [];

        if (is_null($shopRevenue = $revenue[$groupID] ?? null)) {
            return $statistics;
        }

        // Tính doanh thu, giá vốn, quảng cáo của shop ở ngày hiện tại đang xét
        $shop = [
            'doanh_thu_chuyen'  => round($shopRevenue['doanh_thu'] / 10e5, 2),
            'chi_phi_quang_cao' => round($shopRevenue['chi_phi_quang_cao'] / 10e5, 2),
            'total_order'       => $shopRevenue['total_order'],
            'total_user_mkt'    => $shopRevenue['total_user_mkt'],
            'total_user_sale'   => $shopRevenue['total_user_sale'],
            'doanh_thu_so_moi'  => $shopRevenue['doanh_thu_so_moi']/1000000,
            'doanh_thu_cskh'    => $shopRevenue['doanh_thu_cskh']/1000000,
        ];
        
        // thay doi https://pm.tuha.vn/issues/12347
        // $shop['gia_von'] = round(($shopCost['gia_von'] ?? 0) * $shop['doanh_thu_chuyen'] / 100, 2);
        // issues BIG 14985 
        $shop['gia_von'] = $shopRevenue['von']/1000000;

        $shop['hieu_suat_sale'] = $shop['total_user_sale'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_sale'], 2) : 0;
        $shop['hieu_suat_mkt'] = $shop['total_user_mkt'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_user_mkt'], 2) : 0;
        $shop['doanh_thu_don_hang'] = $shop['total_order'] > 0 ? round($shop['doanh_thu_chuyen']/$shop['total_order'], 2) : 0;

        $shop['chi_phi_luong']     = round($shop['doanh_thu_chuyen'] * ($shopCost['chi_phi_luong'] ?? 0) / 100, 2);
        $shop['chi_phi_cod']       = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_cod'] ?? 0) / 100, 2);
        $shop['chi_phi_cuoc_dt']   = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_dt'] ?? 0) / 100, 2);
        $shop['chi_phi_hoan']      = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_hoan'] ?? 0) / 100, 2);
        $shop['chi_phi_khac']      = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_khac'] ?? 0) / 100, 2);
        $shop['chi_phi_tien_nha']  = round($shop['doanh_thu_chuyen'] * ($shopCost['cuoc_tien_nha'] ?? 0) / 100, 2);
        $shop['loi_nhuan']         = round($shop['doanh_thu_chuyen']
                                - $shop['gia_von']
                                - $shop['chi_phi_quang_cao']
                                - $shop['chi_phi_luong']
                                - $shop['chi_phi_cod']
                                - $shop['chi_phi_cuoc_dt']
                                - $shop['chi_phi_hoan']
                                - $shop['chi_phi_khac']
                                - $shop['chi_phi_tien_nha'], 2);

        // Cộng dồn vào thống kê
        $statistics['doanh_thu_chuyen'][$groupID]   += round($shop['doanh_thu_chuyen'], 2);
        $statistics['gia_von'][$groupID]            += round($shop['gia_von'], 2);
        $statistics['chi_phi_quang_cao'][$groupID]  += round($shop['chi_phi_quang_cao'], 2);
        $statistics['chi_phi_luong'][$groupID]      += round($shop['chi_phi_luong'], 2);
        $statistics['chi_phi_cod'][$groupID]        += round($shop['chi_phi_cod'], 2);
        $statistics['chi_phi_cuoc_dt'][$groupID]    += round($shop['chi_phi_cuoc_dt'], 2);
        $statistics['chi_phi_hoan'][$groupID]       += round($shop['chi_phi_hoan'], 2);
        $statistics['chi_phi_khac'][$groupID]       += round($shop['chi_phi_khac'], 2);
        $statistics['chi_phi_tien_nha'][$groupID]   += round($shop['chi_phi_tien_nha'], 2);
        $statistics['loi_nhuan'][$groupID]          += round($shop['loi_nhuan'], 2);

        $statistics['hieu_suat_sale'][$groupID]     += round($shop['hieu_suat_sale'], 2);
        $statistics['hieu_suat_mkt'][$groupID]      += round($shop['hieu_suat_mkt'], 2);
        $statistics['doanh_thu_don_hang'][$groupID] += round($shop['doanh_thu_don_hang'], 2);
        $statistics['total_order'][$groupID]        += round($shop['total_order'], 2);
        $statistics['total_user_mkt'][$groupID]     += round($shop['total_user_mkt'], 2);
        $statistics['total_user_sale'][$groupID]    += round($shop['total_user_sale'], 2);
        $statistics['doanh_thu_so_moi'][$groupID]   += round($shop['doanh_thu_so_moi'], 2);
        $statistics['doanh_thu_cskh'][$groupID]     += round($shop['doanh_thu_cskh'], 2);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $statistics  The statistics
     * @param      <type>  $index       The index
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function calRate($statistics, $index)
    {
        return $statistics['doanh_thu_chuyen']['total'] > 0
            ? round($statistics[$index]['total'] * 100 / $statistics['doanh_thu_chuyen']['total'], 2)
            : 0;
    }

    /**
     * Gets the revenue statistics.
     *
     * @param      string  $startDay   The start day
     * @param      string  $endDay     The end day
     * @param      <type>  $groupIDs  The group IDs
     */
    private function getDayStatistics(string $startDay, string $endDay, array $groupIDs, ?bool $groupByDay = true)
    {
        $groupBy = array_merge(['group_id'], $groupByDay ? ['time'] : []);

        return !$groupIDs ? [] : DB::fetch_all_array('
            SELECT 
                group_id,
                DATE_FORMAT(time, "%d/%m") as time,
                SUM(doanh_thu) AS doanh_thu,
                SUM(von) AS von,
                SUM(chi_phi_quang_cao) AS chi_phi_quang_cao,
                SUM(total_order) AS   total_order,
                SUM(total_user_sale) AS total_user_sale,
                SUM(total_user_mkt) AS total_user_mkt,
                SUM(doanh_thu_so_moi) AS doanh_thu_so_moi,
                SUM(doanh_thu_cskh) AS doanh_thu_cskh
            FROM  cost_day
            WHERE 
                group_id IN (' . implode(',', $groupIDs) . ') 
                AND time >= "' . $startDay . '"
                AND time <= "' . $endDay . '"
            GROUP BY 
                ' . implode(',', $groupBy));
    }

    /**
     * Gets the cost.
     *
     * @param      string  $startDay  The start day
     * @param      array   $groupIDs  The group i ds
     *
     * @return     <type>  The cost.
     */
    private function getCost(string $startDay, array $groupIDs)
    {
        return DB::fetch_all('
            SELECT
                group_id as id,
                gia_von,
                chi_phi_luong,
                cuoc_cod,
                cuoc_dt,
                cuoc_hoan,
                cuoc_khac,
                cuoc_tien_nha
            FROM cost_declaration 
            WHERE 
                time = "' . $startDay . '" 
                AND group_id IN (' . implode(',', $groupIDs) . ')
            GROUP BY group_id
        ');
    }

    /**
     * Gets the days.
     */
    private function getDays()
    {
        $toDateTime = new DateTime(date('Y-m-d', FilterConditionHkd::getTimeTo()));
        $fromDateTime = new DateTime(date('Y-m-d', FilterConditionHkd::getTimeFrom()));

        $period = new DatePeriod($fromDateTime, new DateInterval('P1D'), $toDateTime);
        
        $days = [];
        $realStart = null;
        $realEnd = null;
        foreach ($period as $key => $value) {
            $days[$value->format('d/m')] = $value->format('Y-m-d');
            is_null($realStart) && ($realStart = $value->format('Y-m-d'));
        }
        $realEnd = $days[$toDateTime->format('d/m')] = $toDateTime->format('Y-m-d');
        is_null($realStart) && ($realStart = $realEnd);

        return [
            array_reverse($days), // danh sách ngày
            $toDateTime->format('Y-m-1'), // ngày bắt đầu
            $toDateTime->format('Y-m-d'), // ngày kết thúc,
            $realStart,
            $realEnd
        ];
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
}
