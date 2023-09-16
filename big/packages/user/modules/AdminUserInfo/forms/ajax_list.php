<?php
require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';
require_once ROOT_PATH . 'packages/core/includes/common/ImageType.php';
require_once ROOT_PATH.'packages/user/modules/UserAdmin/db.php';
require_once ROOT_PATH.'packages/user/modules/UserAdmin/class.php';
require_once 'packages/core/includes/utils/paging.php';
class ListAjaxUserAdminInforForm extends Form
{
    protected $map;
    protected $ID_BASE      = 100.0;
    protected $ID_MAX_LEVEL = 9;
    protected $ID_OBD = 2;
    protected $ID_ROOT = 0;
    function __construct()
    {
        Form::Form('ListAjaxUserAdminInforForm');
        $this->link_css('assets/default/css/cms.css');
        

    }
    function on_submit()
    {
        
    }
    function draw()
    {
        $date = new DateTime(date("Y-m-d"));
        $date->modify('-93 day');
        $tomorrowDATE = $date->format('Y-m-d');
        if ((isset($_REQUEST['start_date']) &&  $_REQUEST['start_date'] != '') && (isset($_REQUEST['end_date']) && $_REQUEST['end_date'] != '')) {
            $start_date = $_REQUEST['start_date'] . ' 00:00:00';
            $end_date = $_REQUEST['end_date'] . ' 23:59:59';
        } else {
            $start_date = $tomorrowDATE.' 23:59:59';
            $end_date = date('Y-m-d').' 00:00:00';
        }
        $period = new DatePeriod(
             new DateTime($start_date),
             new DateInterval('P1D'),
             new DateTime($end_date)
        );
        $count = [];
        $i = 1;
        foreach ($period as $key => $value) {
            $count[] = $i;
            $i++;  
        }
        $data = $this->getItems();
        $message = '';
        $userId = $data['userId'];
        $valuePerpage = Url::get('item_per_page') ? Url::get('item_per_page') : 15;
        
        
        $paginate = $this->getPaginate($_SESSION['total']);
        $this->map['items'] =  $data['items'];
        $this->map['message'] =  $message;
        $this->map['total'] = $_SESSION['total'];
        $this->map['paging'] =  $paginate;
        $this->map['item_per_page'] = $valuePerpage;
        $this->map['page_no'] = page_no();
        $this->parse_layout('ajax_list',$this->map);
    }
    function getItems(){
        $data = [];
        $cond = $this->getCond();
        $total = 0;
        if (Url::get('check_submit') == 1) {
            if (isset($_SESSION['total'])) {
                unset($_SESSION['total']);
                $_SESSION['total'] = $this->getTotalItem($cond);
                $total = $_SESSION['total'];
            } else {
                $_SESSION['total'] = $this->getTotalItem($cond);
                $total = $_SESSION['total'];
            }
        }
        $item_per_page = 15;
        if (Url::get('item_per_page')) {
            $item_per_page =Url::get('item_per_page');
        }
        $skip = (page_no() - 1) * intval($item_per_page);
        $take = intval($item_per_page);
        $items = $this->getData($cond, $skip, $take);
        $i=0;
        $data = [];
        $userId = [];
        $phone = [];
        foreach ($items as $key=>$value)
        {
            $items[$key]['i']=$i++;
            if($item_per_page){
                $index = $i + ($item_per_page*(page_no()-1));
            }else{
                $index = $i;
            }
            $items[$key]['index'] = $index;
            $items[$key]['ids'] = '';
            $userId[] = $value['users_id'];
            $phone[] = $value['phone_number'];
        }
        $strPhone = implode(',', $phone);
        $phoneAllArray = $this->getDuplicatePhone($strPhone);
        foreach ($items as $k => $value) {
           if(isset($phoneAllArray[$value['phone_number']]) && $value['is_count'] == 0 && sizeof($phoneAllArray[$value['phone_number']]) > 1){
                $items[$k]['ids'] = implode(',',$phoneAllArray[$value['phone_number']]);
           } 
        }
        $data['items'] = $items;
        $data['total'] = $total;
        $data['userId'] = $userId;
        return $data;
    }
    function getDuplicatePhone($strPhone){
        $phoneAllArray = [];
        if($strPhone){
            $sql = "SELECT 
                    users.id,
                    users.phone
                FROM 
                    users LEFT JOIN account ON users.username = account.id 
                WHERE 
                    phone IN ($strPhone)";
            $results = DB::fetch_all($sql);
            foreach ($results as $res => $val) {
                $phoneAllArray[$val['phone']][$val['id']] = (int)$val['id'];
            }
        }
        
        return $phoneAllArray;
    }
    function getData($cond, $skip, $take){
        $sql = '
            SELECT
                 account.id as account_id
                ,account.group_id
                ,account.admin_group
                ,groups.name as group_name
                ,groups.code
                ,party.full_name
                ,users.phone as phone_number
                ,account.is_active AS active
                ,party.label
                ,(SELECT name FROM zone WHERE zone.id=zone_id) AS zone_name
                ,groups_system.name as master_group
                ,users.id as users_id
                ,users.id
                ,(select name from users as created_user where created_user.id = users.user_created) as user_created
                ,users.created
                ,users.address
                ,account.account_group_id
                ,account.is_count
                ,users.identity_card
                ,users.identity_card_front
                ,users.identity_card_back
                ,user_images.image_url
                ,user_images.type
                ,GROUP_CONCAT(DISTINCT user_images.type) user_image
            FROM
                users
                INNER JOIN account on users.username = account.id
                INNER JOIN party on party.user_id = account.id
                LEFT JOIN groups on groups.id = users.group_id
                LEFT JOIN groups_system on groups_system.id = groups.system_group_id
                LEFT JOIN user_images on user_images.user_id = users.id
            WHERE
                '.$cond. ' GROUP BY users.id ORDER BY users.id DESC LIMIT '.$skip.','.$take.' ';
            return  DB::fetch_all($sql);
    }
    function getCond(){
        $cond = (User::is_admin()?'account.id<>"admin"':' account.id<>"admin"');

        $date = new DateTime(date("Y-m-d"));
        $date->modify('-93 day');
        $tomorrowDATE = $date->format('Y-m-d');
        $start_date = '';
        $end_date = '';
        if(Url::get('start_date')){
            $start_date = Url::get('start_date') . ' 00:00:00';
        } else {
            $start_date = date('Y-m-d') . ' 00:00:00';
        }
        if(Url::get('end_date')){
            $end_date = Url::get('end_date') . ' 23:59:59';
        } else {
            $end_date = date('Y-m-d') . ' 23:59:59';
        }
        $cond .= ' AND users.created BETWEEN "'. $start_date  .'" AND "'. $end_date . '" ';
        if (Url::get('option_system_group')) {
            $system_group_id = DB::escape(Url::get('option_system_group'));
            $groupIds = $this->getGroupIdSystem($system_group_id);
            $strGroupId = implode(',',$groupIds);
            $cond .= ' AND groups.id IN (' . $strGroupId . ') ';
        } else {
            $groupIds = $this->getGroupIdSystem($this->ID_OBD);
            $strGroupId = implode(',',$groupIds);
            $cond .= ' AND groups.id IN (' . $strGroupId . ') ';
        }

        if (Url::get('phone_hidden')) {
            $phone = DB::escape(trim(Url::get('phone_hidden')));
            $cond .= ' AND users.phone LIKE "' . $phone . '%" ';
        }

        if (Url::get('account_name')) {
            $account_name = DB::escape(Url::get('account_name'));
            $cond .= ' AND (users.username ="' . $account_name . '" OR  users.name LIKE "' . $account_name . '%" OR users.id = "'. $account_name .'")';
        }

        if (Url::get('user_name')) {
            $name = DB::escape(Url::get('user_name'));
            $cond .= ' AND users.name LIKE "' . $name . '%"';
        }

        if (Url::get('hkd')) {
            $hkd = DB::escape(Url::get('hkd'));
            $cond .= ' AND (groups.name LIKE "' . $hkd . '%" OR groups.id = "'. $hkd .'" OR groups.code = "'. $hkd .'")';
        }

        if (Url::get('cmnd')) {
            $cmnd = DB::escape(Url::get('cmnd'));
            $cond .= ' AND users.identity_card LIKE "' . $cmnd . '%" ';
        }
        $null = '""';
        

        if(Url::get('option_status') == 1){
            $cond .= ' AND account.is_active = 1';
        }

        if(Url::iget('option_trung_nhan_su')){
            if(Url::iget('option_trung_nhan_su') == 1){
                $cond .= ' AND account.is_count = 1';
                $cond .= ' AND account.is_active = 1';
            } else {
                $cond .= ' AND account.is_count = 0';
            }
            
        }
        if(Url::get('option_status') == 2){
            $cond .= ' AND (account.is_active = 0 OR account.is_active IS NULL)';
        }
        $optionCmnd = Url::get('option_cmnd');
        $optionCmndCond = '';
        if ($optionCmnd == 1) {
            $cond.= ' AND ( users.identity_card_front IS NOT NULL AND users.identity_card_front <> "" ) ';
        } elseif ($optionCmnd == 2) {
            $cond.= ' AND ( users.identity_card_back IS NOT NULL AND users.identity_card_back <> "" ) ';
        } else if($optionCmnd == 3){
            $cond.= ' AND ( (users.identity_card_back IS NULL OR users.identity_card_back = "") AND (users.identity_card_front IS NULL OR users.identity_card_front = "") ) ';
        }

        $hoSoXinViec = Url::get('option_hosoxinviec');
        $hoSoXinViecCond = '';
        if ($hoSoXinViec == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::HO_SO_XIN_VIEC.' LIMIT 1) ';
        } elseif ($hoSoXinViec == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::HO_SO_XIN_VIEC.' LIMIT 1) ';
        }

        $soHoKhau = Url::get('option_shk');
        $soHoKhauCond = '';
        if ($soHoKhau == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::SO_HO_KHAU.' LIMIT 1) ';
        } elseif ($soHoKhau == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::SO_HO_KHAU.' LIMIT 1) ';
        }

        $hopDongHopTac = Url::get('option_hopdonghoptac');
        $hopDongHopTacCond = '';
        if ($hopDongHopTac == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::HOP_DONG_HOP_TAC.' LIMIT 1) ';
        } elseif ($hopDongHopTac == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::HOP_DONG_HOP_TAC.' LIMIT 1) ';
        }

        $camKetTuVan = Url::get('option_bancamket');
        $camKetTuVanCond = '';
        if ($camKetTuVan == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::CAM_KET.' LIMIT 1) ';
        } elseif ($camKetTuVan == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::CAM_KET.' LIMIT 1) ';
        }

        $bangCap = Url::get('option_bangcap');
        if ($bangCap == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::BANG_CAP.' LIMIT 1) ';
        } elseif ($bangCap == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::BANG_CAP.' LIMIT 1) ';
        }

        $giayKhaiSinh = Url::get('option_giaykhaisinh');
        if ($giayKhaiSinh == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::KHAI_SINH.' LIMIT 1) ';
        } elseif ($giayKhaiSinh == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::KHAI_SINH.' LIMIT 1) ';
        }

        $camKetBaoMat = Url::get('option_camketbaomat');
        if ($camKetBaoMat == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::CAM_KET_BAO_MAT_TT.' LIMIT 1) ';
        } elseif ($camKetBaoMat == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::CAM_KET_BAO_MAT_TT.' LIMIT 1) ';
        }

        $giayKhamSk = Url::get('option_giaykhamsuckhoe');
        if ($giayKhamSk == 1) {
            $cond .=' AND EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::GIAY_KHAM_SUC_KHOE.' LIMIT 1) ';
        } elseif ($giayKhamSk == 2) {
            $cond .=' AND NOT EXISTS (SELECT 1 FROM user_images WHERE user_images.user_id = users.id AND user_images.type = '.ImageType::GIAY_KHAM_SUC_KHOE.' LIMIT 1) ';
        }
        return $cond;
    }
    function getTotalItem($cond){
        $sql = '
            SELECT
                count(*) as total
            FROM
                users
                INNER JOIN account on users.username = account.id
                INNER JOIN groups on groups.id = users.group_id
            WHERE
                '.$cond. ' ';

        $items = DB::fetch($sql,'total');
        return $items;
    }
    function getPaginate($total){
        $total = $total;
        $item_per_page = Url::get('item_per_page') ?  Url::get('item_per_page') : 15;
        $paging_array = array(
            'item_per_page'=>Url::get('item_per_page') ? Url::get('item_per_page') : 15,
            'phone_hidden' => Url::get('phone_hidden'),
            'user_name'=>Url::get('user_name'),
            'account_name'=>Url::get('account_name'),
            'start_date'=>Url::get('start_date'),
            'end_date'=>Url::get('end_date'),
            'option_shk'=>Url::get('option_shk'),
            'option_hosoxinviec'=>Url::get('option_hosoxinviec'),
            'option_hopdonghoptac'=>Url::get('option_hopdonghoptac'),
            'option_bancamket'=>Url::get('option_bancamket'),
            'option_cmnd'=>Url::get('option_cmnd'),
            'option_status'=>Url::get('option_status'),
            'cmnd'=>Url::get('cmnd'),
            'hkd'=>Url::get('hkd'),
            'option_system_group'=>Url::get('option_system_group'),
        );
        $paging = order_page_ajax($total,$item_per_page,$paging_array,7,'page_no','');
        return $paging;
    }
    function getGroupIdSystem($id){
        $sql = "SELECT id,name,structure_id FROM groups_system WHERE id = $id";
        $query = DB::fetch($sql);
        $groupIds = $this->getShopByStructureId($query['structure_id']);
        return $groupIds;
    }
    function getShopByStructureId($structureID)
    {
        $fmt = '
                SELECT `groups`.`id`, `groups_system`.`structure_id`,`groups`.`name` 
                FROM `groups_system` 
                JOIN `groups` 
                ON `groups`.`system_group_id` = `groups_system`.`id` 
                WHERE %s ';
        $sql = sprintf($fmt, $this->child_cond($structureID));

        $system = DB::fetch_all($sql);
        $groupIds = [];
        foreach ($system as $key => $sys) {
             $groupIds[] = $key;
        }
        return $groupIds;
    }
    function child_cond($structure_id, $except_me = false,$extra = '')
    {
        if($except_me)
        {
            return '('.$extra.'`structure_id` > '.$structure_id.' and '.$extra.'`structure_id` < '.$this->next($structure_id).')';
        }
        else
        {
            return '('.$extra.'`structure_id` >= '.$structure_id.' and '.$extra.'`structure_id` < '.$this->next($structure_id).')';
        }
    }
    function next($structure_id)
    {
        $r = $structure_id+intval(pow($this->ID_BASE,$this->ID_MAX_LEVEL -$this->level($structure_id)));
        return $r;
    }
    function level($structure_id)
    {
        $level = 0;
        if($structure_id>=$this->ID_ROOT)
        {
            $i = 0;
            $st = '_'.$structure_id;
            while(substr($st,$level*2,2)!='00')
            {
                $level++;
                if($level>100){
                    break;
                }
            }
            $level--;
        }
        return $level;
    }
}
?>
