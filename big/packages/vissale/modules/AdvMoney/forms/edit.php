<?php
class EditAdvMoneyForm extends Form
{
    protected $map;
    protected $isObd;
    function __construct()
    {
        Form::Form('EditAdvMoneyForm');
        $this->add('vs_adv_money.account_id', new TextType(false, 'invalid_account', 0, 125));
        $this->isObd = isObd();
        if ($this->isObd) {
            $this->sources = AdvMoneyDB::getSystemSources();
            $this->bundles = AdvMoneyDB::getSystemBundles();
        } else {
            $this->sources = AdvMoneyDB::get_source();
            $this->bundles = AdvMoneyDB::get_bundle();
        }
    }
    function on_submit()
    {
        if ($this->check() and URL::get('confirm_edit') and !Url::get('search')) {
            $format = AdvMoney::$data_time['format'];
            if (isset($_REQUEST['mi_account'])) {
                $IDs = $this->getRequestIDs();
                $savedItems = $this->getSavedItems($IDs);

                $allow_change = (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2 && (int)date("H") < CPQC_HOUR) ? 1 : 0;
                if (!defined('CPQC_FOLLOW') || CPQC_FOLLOW != 2) {
                    $allow_change = 1;
                }
                if (!$allow_change) {
                    die('Thời gian hiện tại: ' . date('d/m/Y H:i') . '. Đã hết thời gian thao tác!');
                }
                if ($savedItems && array_values($savedItems)[0]['date'] != date('Y-m-d', strtotime('-1 days'))) {
                    die('Thời gian hiện tại: ' . date('d/m/Y H:i') . '. Đã hết thời gian thao tác!');
                }

                foreach ($_REQUEST['mi_account'] as $key => $record) {
                    DataFilter::removeHtmlTags($record);
                    if ($record['id'] == '(auto)') {
                        $record['id'] = false;
                    }
                    $ID = $record['id'];
                    if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) {
                        $record['date'] = date('Y-m-d', strtotime('-1 days'));
                    } else {
                        $record['date'] = $record['date'] ? Date_Time::to_sql_date($record['date']) : '0000-00-00';
                    }
                    if (!empty($format['time_slot_1'])) {
                        $record['time_slot_1'] = $record['time_slot_1'] ? System::calculate_number($record['time_slot_1']) : 0;
                    }
                    if (!empty($format['time_slot_2'])) {
                        $record['time_slot_2'] = $record['time_slot_2'] ? System::calculate_number($record['time_slot_2']) : 0;
                    }
                    if (!empty($format['time_slot_3'])) {
                        $record['time_slot_3'] = $record['time_slot_3'] ? System::calculate_number($record['time_slot_3']) : 0;
                    }
                    if (!empty($format['time_slot_4'])) {
                        $record['time_slot_4'] = $record['time_slot_4'] ? System::calculate_number($record['time_slot_4']) : 0;
                    }
                    if (!empty($format['time_slot_5'])) {
                        $record['time_slot_5'] = $record['time_slot_5'] ? System::calculate_number($record['time_slot_5']) : 0;
                    }
                    if (!empty($format['time_slot_6'])) {
                        $record['time_slot_6'] = $record['time_slot_6'] ? System::calculate_number($record['time_slot_6']) : 0;
                    }
                    if (!empty($format['time_slot_7'])) {
                        $record['time_slot_7'] = $record['time_slot_7'] ? System::calculate_number($record['time_slot_7']) : 0;
                    }

                    $record['clicks'] = $record['clicks'] ? System::calculate_number($record['clicks']) : 0;
                    if ($record['id'] and DB::exists_id('vs_adv_money', $record['id'])) {
                        unset($record['date']);
                        unset($savedItems[$ID]['date']);
                        DB::update('vs_adv_money', $record, 'id=' . $record['id']);
                        //store log
                        $this->storeLogUpdated($ID, $this->prepareLogUpdated($savedItems[$ID], $record));
                    } else {
                        unset($record['id']);
                        $record['group_id'] = Session::get('group_id');
                        $record['account_id'] = Session::get('user_id');
                        $record['full_name'] = $_SESSION['user_data']['full_name'];
                        $record['created_date'] = date('Y-m-d H:i:s');
                        $record['id'] = DB::insert('vs_adv_money', $record);

                        // log
                        $record['id'] && $this->storeLogAdded($record['id'], $this->prepareLogInserted($record));
                    }
                }

                if (isset($ids) and sizeof($ids)) {
                    $_REQUEST['selected_ids'] .= ',' . join(',', $ids);
                }
            }
            //die;
            Url::js_redirect(true);
        }
    }
    function draw()
    {
        $this->map = array();
        $items = array();
        ///
        $check = true;
        $readonly = 0;
        if (defined('CPQC_FOLLOW') &&  CPQC_FOLLOW === 2 && (int)date("H") > (CPQC_HOUR - 1)) {
            $readonly = 1;
        }
        if (defined('CPQC_FOLLOW') &&  CPQC_FOLLOW === 2 && (int)date("H") > (CPQC_HOUR - 1) && Url::get('cmd') == 'add') {
            die('Thời gian hiện tại: ' . date('d/m/Y H:i') . '. Đã hết thời gian thao tác!');
        }
        $dateDataEdit = '';
        if (Url::get('cmd') == 'edit') {
            $ids = Url::get('ids');
            $idsArr = explode(',', $ids);
            $escape_ids = array_map(function ($id) {
                return DB::escape($id);
            }, $idsArr);

            $ids = implode(',', $escape_ids);
            $cond = '
                vs_adv_money.group_id = ' . Session::get('group_id') . ' 
                ' . (!Session::get('admin_group') ? ' and account_id="' . Session::get('user_id') . '"' : '') . '
                ' . (Url::iget('id') ? ' AND vs_adv_money.id=' . Url::iget('id') : '') . '
                ' . (Url::get('ids') ? ' AND vs_adv_money.id  IN (' . $ids . ')' : '') . '
            ';
            $item_per_page = 200;
            $_sql = "SELECT count(distinct vs_adv_money.id) as acount
				FROM vs_adv_money
				WHERE $cond";

            $count = DB::fetch($_sql);
            $this->map['total'] = $count['acount'];
            require_once 'packages/core/includes/utils/paging.php';
            $paging = paging($count['acount'], $item_per_page);
            if ($this->isObd) {
                $sql = "SELECT vs_adv_money.*, bundles.ref_id
                    FROM vs_adv_money
                        LEFT JOIN bundles ON bundles.id = vs_adv_money.bundle_id
                    WHERE $cond
                    ORDER BY vs_adv_money.date  DESC
                    LIMIT " . ((page_no() - 1) * $item_per_page) . ',' . $item_per_page . '
                ';
            } else {
                $sql = '
				select
					vs_adv_money.*
				from
					vs_adv_money
				WHERE
					'.$cond.'
				order by
					vs_adv_money.date  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'';
            }//end if

            $items = DB::fetch_all($sql);
            foreach ($items as $key => $val) {
                $items[$key]['time_slot_1'] = System::display_number($val['time_slot_1']);
                $items[$key]['time_slot_2'] = System::display_number($val['time_slot_2']);
                $items[$key]['time_slot_3'] = System::display_number($val['time_slot_3']);
                $items[$key]['time_slot_4'] = System::display_number($val['time_slot_4']);
                $items[$key]['time_slot_5'] = System::display_number($val['time_slot_5']);
                $items[$key]['time_slot_6'] = System::display_number($val['time_slot_6']);
                $items[$key]['time_slot_7'] = $val['time_slot_7'];
                $items[$key]['clicks'] = $val['clicks'];
                $items[$key]['date'] = Date_Time::to_common_date($val['date']);
                if ($items[$key]['ref_id']) {
                    $items[$key]['bundle_id'] = $items[$key]['ref_id'];
                }//end if

                $dateDataEdit = $val['date'];
            }

            // log xem 
            $this->storeLogViewed(URL::getUInt('id'));
        }
        $data = AdvMoney::$data_time;
        $_REQUEST['mi_account'] = $items;
        $this->map['time_slot'] = $data['timeSlot'];
        $this->map['format'] = $data['format'];
        $this->map['dateDataEdit'] = $dateDataEdit ? date('d/m/Y', strtotime($dateDataEdit)) : '';
        $this->map['yesterday'] = date('d/m/Y', strtotime('-1 days'));
        if ($readonly || ($this->map['dateDataEdit'] && $this->map['dateDataEdit'] != $this->map['yesterday'])) {
            $setReadonly = 1;
        } else {
            $setReadonly = 0;
        }
        $this->map['setReadonly'] = $setReadonly;

        ///
        $paging = '';
        $this->map['paging'] = $paging;
        $cond = 'account.group_id=' . Session::get('group_id') . ' and vs_adv_money.group_id=' . Session::get('group_id');
        require_once 'packages/core/includes/utils/paging.php';
        $users = AdvMoneyDB::get_items($cond, 1000);
        $this->map['account_id_options'] = '<option value="">Chọn trạng tài khoản</option>';
        foreach ($users as $key => $val) {
            $this->map['account_id_options'] .= '<option value="' . $key . '">' . $val['name'] . '</option>';
        }
        $this->map['privilege_code_list'] = array('' => '-/-Tùy chọn-/-', 'CANCEL' => 'Không', 'XUATKHO' => 'Có');
        $this->map['assign_order_list'] = array('' => '-/-Tùy chọn-/-', 'CANCEL' => 'Không', 'GANDON' => 'Có');

        $this->map['source_options'] = '<option value="0">Nguồn</option>';
        $this->map['bundle_options'] = '<option value="0">Phân loại SP</option>';

        foreach ($this->sources as $key => $value) {
            $this->map['source_options'] .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        foreach ($this->bundles as $key => $value) {
            $this->map['bundle_options'] .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }

        $this->map['logs'] = (Url::get('cmd') == 'edit' && $_REQUEST['mi_account']) ? $this->getLogs() : [];
        $this->map['users'] = AdvMoneyDB::getUserByIDs($this->getLogUserIDs());

        $this->parse_layout('edit', $this->map);
    }



    /**
     * Gets the log user i ds.
     *
     * @return     <type>  The log user i ds.
     */
    private function getLogUserIDs()
    {
        return array_reduce($this->map['logs'], function ($userIDs, $log) {
            $userIDs[] = $log['data']['by'] ?? 0;

            return $userIDs;
        }, []);
    }

    /**
     * Gets the request i ds.
     *
     * @return     <type>  The request i ds.
     */
    private function getRequestIDs()
    {
        return Arr::of($_REQUEST['mi_account'])
            ->filter(function ($row) {
                return is_numeric($row['id']);
            })
            ->column('id')
            ->toArray();
    }

    /**
     * Gets the saved items.
     *
     * @param      array  $IDs    I ds
     */
    private function getSavedItems(array $IDs)
    {
        $sql = 'SELECT * FROM `vs_adv_money` WHERE `id` IN (' . implode(',', $IDs) . ') AND `group_id` = ' . Session::get('group_id');

        return $IDs ? DB::fetch_all($sql) : [];
    }

    /**
     * { function_description }
     *
     * @param      array   $old    The old
     * @param      <type>  $new    The new
     */
    private function prepareLogUpdated(array $old, $new)
    {
        $fields = [
            'date'        => 'Ngày tạo',
            'time_slot_1' => 'Chi phí 10h',
            'time_slot_2' => 'Chi phí 11h30',
            'time_slot_3' => 'Chi phí 14h',
            'time_slot_4' => 'Chi phí 15h30h',
            'time_slot_5' => 'Chi phí 17h30',
            'time_slot_6' => 'Chi phí 23h',
            'time_slot_7' => 'Chi phí 24h',
            'clicks'      => 'Lượt click',
            'source_id'   => 'Nguồn',
            'bundle_id'   => 'Phân loại'
        ];
        if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) {
            $fields = [
                'date'        => 'Ngày tạo',
                'time_slot_7' => 'Chi phí QC',
                'clicks'      => 'Lượt click',
                'source_id'   => 'Nguồn',
                'bundle_id'   => 'Phân loại'
            ];
        }

        return Arr::of($fields)
            ->reduce(function ($res, $txt, $slug) use ($old, $new) {
                if ($old[$slug] == $new[$slug]) {
                    return $res;
                }

                switch ($slug) {
                    case 'bundle_id':
                        $new = $this->getBundleNameByID($new[$slug]);
                        $old = $this->getBundleNameByID($old[$slug]);
                        break;

                    case 'source_id':
                        $new = $this->getSourceNameByID($new[$slug]);
                        $old = $this->getSourceNameByID($old[$slug]);
                        break;

                    default:
                        $new = $new[$slug];
                        $old = $old[$slug];
                }

                $res[] = sprintf('Thay đổi <txt>%s</txt> từ <old>%s</old> => <new>%s</new>', $txt, $old, $new);

                return $res;
            }, [])
            ->toArray();
    }

    /**
     * { function_description }
     *
     * @param      array   $data   The data
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function prepareLogInserted(array $data)
    {
        return Arr::of($this->logFields())
            ->reduce(function ($res, $txt, $slug) use ($data) {
                switch ($slug) {
                    case 'bundle_id':
                        $val = $this->getBundleNameByID($data[$slug]);
                        break;

                    case 'source_id':
                        $val = $this->getSourceNameByID($data[$slug]);
                        break;

                    default:
                        $val = $data[$slug];
                }

                $res[] = sprintf('Thêm <txt>%s</txt>: <new>%s</new>', $txt, $val);

                return $res;
            }, [])
            ->toArray();
    }

    /**
     * Logs fields.
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function logFields()
    {
        if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) {
            return [
                'date'        => 'Ngày tạo',
                'time_slot_7' => 'Chi phí QC',
                'clicks'      => 'Lượt click',
                'source_id'   => 'Nguồn',
                'bundle_id'   => 'Phân loại'
            ];
        } else {
            return [
                'date'        => 'Ngày tạo',
                'time_slot_1' => 'Chi phí 10h',
                'time_slot_2' => 'Chi phí 11h30',
                'time_slot_3' => 'Chi phí 14h',
                'time_slot_4' => 'Chi phí 15h30h',
                'time_slot_5' => 'Chi phí 17h30',
                'time_slot_6' => 'Chi phí 23h',
                'time_slot_7' => 'Chi phí 24h',
                'clicks'      => 'Lượt click',
                'source_id'   => 'Nguồn',
                'bundle_id'   => 'Phân loại'
            ];
        }
    }


    /**
     * Gets the bundle name by id.
     *
     * @param      <type>  $ID     { parameter_description }
     *
     * @return     <type>  The bundle name by id.
     */
    private function getBundleNameByID($ID)
    {
        return $this->bundles[$ID]['name'] ?? '';
    }

    /**
     * Gets the source name by id.
     *
     * @param      <type>  $ID     { parameter_description }
     *
     * @return     <type>  The source name by id.
     */
    private function getSourceNameByID($ID)
    {
        return $this->sources[$ID]['name'] ?? '';
    }

    /**
     * Logs an updated.
     *
     * @param      array  $updateds  The updateds
     */
    private function storeLogUpdated(int $ID, array $updated)
    {
        if (!$updated) {
            return;
        }

        $this->latestLog = [
            'type'       => AdvMoney::LOG_UPDATE_TYPE,
            'at' => date('Y-m-d H:i:s'),
            'by' => Session::get('user_id'),
            'data'    => $updated
        ];

        storeLog($ID, AdvMoney::LOG_TABLE, $this->latestLog);
    }



    /**
     * Stores a log viewed.
     *
     * @param      int   $ID     { parameter_description }
     */
    private function storeLogViewed(int $ID)
    {
        $this->latestLog = [
            'type'       => AdvMoney::LOG_VIEW_TYPE,
            'at'         => date('Y-m-d H:i:s'),
            'by'         => Session::get('user_id')
        ];

        storeLog($ID, AdvMoney::LOG_TABLE, [
            'type'       => AdvMoney::LOG_VIEW_TYPE,
            'at'         => date('Y-m-d H:i:s'),
            'by'         => Session::get('user_id')
        ]);
    }

    /**
     * Stores a log added.
     *
     * @param      int   $ID     { parameter_description }
     */
    private function storeLogAdded(int $ID, array $inserted)
    {
        if (!$inserted) {
            return;
        }

        $this->latestLog = [
            'type'       => AdvMoney::LOG_ADD_TYPE,
            'at'         => date('Y-m-d H:i:s'),
            'by'         => Session::get('user_id'),
            'data'       => $inserted
        ];

        storeLog($ID, AdvMoney::LOG_TABLE, $this->latestLog);
    }

    /**
     * Gets the logs.
     *
     * @return     <type>  The logs.
     */
    private function getLogs()
    {
        return getLog(URL::getUInt('id'), AdvMoney::LOG_TABLE, 20) ?: [];
    }
}
