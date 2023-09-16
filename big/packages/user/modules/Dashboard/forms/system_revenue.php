<?php
class SystemRevenueForm extends Form
{
    protected $map;
    private $ranks = [];
    public function __construct()
    {
        Form::Form('ReportForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        require_once ROOT_PATH . 'packages/user/modules/Dashboard/forms/SystemRevenue/FilterCondition.php';
        require_once ROOT_PATH . 'packages/user/modules/Dashboard/forms/SystemRevenue/SystemStatistics.php';

        FilterCondition::init();
    }

    public function on_submit()
    {
        if(in_array(URL::getString('action'), ['get_group_revenue'])){
            if(!$groupIDs = URL::getSafeIDs('group_ids')){
                RequestHandler::sendJsonError('--');
            }

            $groupsRevenue = [];
            foreach ($groupIDs as $groupID) {
                $groupsRevenue[$groupID] = array_merge(
                    SystemStatistics::getGroupRevenue($groupID),  
                    SystemStatistics::getGroupUserQuantity($groupID),
                    SystemStatistics::getGroupUserParentQuantity($groupID),
                    SystemStatistics::getGroupPhoneQuantity($groupID)
                ); 
            }
            // var_dump($groupsRevenue);
            RequestHandler::sendJsonSuccess(['groups' => $groupsRevenue]);
        }
    }

    public function draw()
    {
        $this->map['admin_group'] = Dashboard::$admin_group ? true : false;
        $this->map['total']       = 0;

        $this->map['page'] =  DataFilter::removeXSSinHtml(Url::get('page'));
        $this->map['do']   =  DataFilter::removeXSSinHtml(Url::get('do'));

        if (strtotime(FilterCondition::getTimeTo()) - strtotime(FilterCondition::getTimeFrom()) > 31 * 24 * 3600) {
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }

        $userID = Session::get('user_data', 'user_id');
        $selects = ['groups_system.id', 'groups_system.name', 'icon_url', 'structure_id'];
        $system = Systems::getSystemByUserID($userID, $selects);

        // bind du lieu linh tinh len layout
        $this->map['group_system_name']     = $system['name'];
        $this->map['group_system_icon_url'] = $system['icon_url'];

        $mySystems = Systems::getSystemsChild($system['structure_id'], ['*'], false);

        $groupIDs = [];
        if(!empty($_REQUEST['form_block_id'])){
            $selected = FilterCondition::getSelectedSystemID();
            $mySystems = array_filter($mySystems, function($system) use($selected){
                return in_array(0, $selected) || in_array($system['id'], $selected);
            });
            ['results' => $systems, 'groupIDs' => $groupIDs] = $this->get_group_statistic($mySystems);
            $this->map['systems'] = $systems;
        }

        $this->map['groupIDs'] = json_encode(array_values($groupIDs));
        $this->map['status_id_list']    = [XAC_NHAN => 'DT xác nhận', THANH_CONG => 'DT thành công'];
        $this->map['order_rating_list'] = ['desc' => 'Giảm dần', 'asc' => 'Tăng dần'];
        if (Url::get('filter_by')) {
            $this->map['title'] = 'THÀNH LẬP TRONG THÁNG';
        } else {
            $this->map['title'] = 'HỆ THỐNG ' . $this->map['group_system_name'];
        }

        $this->map['date_from'] = date('d/m/Y', strtotime(FilterCondition::getTimeFrom()));
        $this->map['date_to'] = date('d/m/Y', strtotime(FilterCondition::getTimeTo()));
        $this->map['system_group_id'] = $this->selectBoxSystems($system['structure_id']);
        $this->parse_layout('system_revenue', $this->map);
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function selectBoxSystems($parentStructureID)
    {
        $props = [
            'name' => 'system_group_id[]',
            'id' => 'system_group_id',
            'multiple' => '',
            'style' => 'display: none;width: 200px;',
        ];

        return SystemsTree::selectBox(
            $parentStructureID, 
            [   
                'selected' => FilterCondition::getSelectedSystemID(), 
                'selectedType' => SystemsTree::SELECTED_CURRENT,
                'props' => $props,
                'default' => sprintf('<option value="0"%s>Tất cả hệ thống</option>', array_search(0, FilterCondition::getSelectedSystemID()) !== false ? ' selected' : '')
            ]
        );
    }

    /**
     * Gets the group statistic.
     *
     * @param      <type>  $mySystems  My systems
     *
     * @return     array   The group statistic.
     */
    public function get_group_statistic($mySystems)
    {
        $results  = [];
        $groupIDs  = [];
        $level         = [1 => 'root', 'cha', 'F0', 'F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7'];
        
        foreach ($mySystems as $ID => $system) {
            $groups = SystemStatistics::getGroups($system['structure_id'], FilterCondition::getTimeFrom());

            $results[$ID]['level'] = IDStructure::level($mySystems[$ID]['structure_id']);
            $results[$ID]['level_name'] = $level[$results[$ID]['level']];
            $results[$ID]['parent_structure_id'] = IDStructure::parent($mySystems[$ID]['structure_id']);
            $results[$ID]['total'] = 0;

            $groupID = array_column($groups, 'id');
            $results[$ID]['groups'] = implode(',', $groupID);

            $groupIDs = array_merge($groupIDs, $groupID);

            $this->get_nav($mySystems, $mySystems[$ID]['structure_id'], $results[$ID]['nav']);
            $results[$ID]['nav'] = implode('>',$results[$ID]['nav'] ?: []);
            
            $results[$ID]['name']  = $mySystems[$ID]['name'];
            $results[$ID]['id']  = $ID;  
        }

        return ['results' => $results, 'groupIDs' => array_unique($groupIDs)];
    }

    private function sort_prioritize_parrent_revenue(&$systems){
        $length = count($systems);
        $changed = false;

        for($i = 0; $i < $length-1; $i++) {
            if(empty($systems[$i]) || $systems[$i]['level'] <= $systems[$i+1]['level'])
                continue;
            
            if($systems[$i]['total'] == $systems[$i+1]['total']){
                $tmp = $systems[$i]['total'];
                $systems[$i]['total'] = $systems[$i+1]['total'];
                $systems[$i+1]['total'] = $tmp;
                $changed = true;
            }
        }

        $changed && $this->sort_prioritize_parrent_revenue($systems);
    }

    // Lấy ra đường dẫn phân cấp
    function get_nav($system_groups, $parent_id = null, &$nav = [])
    {   
        if(!$parent_id) return [];

        foreach ($system_groups as $key => $item)
        {
            if ($item['structure_id'] == IDStructure::parent($parent_id, false))
            {                 
                unset($system_groups[$key]);
                
                !empty($nav) ? array_unshift($nav, $item['name']) : ($nav[] = $item['name']);

                $this->get_nav($system_groups, $item['structure_id'], $nav);
            }
        }
    }
}