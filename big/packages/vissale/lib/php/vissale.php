<?php
define('TONG_CONG_TY', 3);
////// Trang thai
define('CHUA_XAC_NHAN', 10);
define('XAC_NHAN', 7);
define('KE_TOAN', 3);
define('CHUYEN_HANG', 8);
define('CHUYEN_HOAN', 6);
define('THANH_CONG', 5);
define('DA_THU_TIEN', 2);
define('HUY', 9);
define('DON_MOI', 1);
define('DON_CSKH', 2);
define('DON_TOI_UU', 9);
define('KHAI_THAC_LAI', 18);
define('TRA_VE_KHO', 1);
define('KHACH_PHAN_VAN', 13);
define('KHACH_KHONG_NGHE_MAY', 12);
define('REQ_ACTION_CREATE', "create");
define('REQ_ACTION_EDIT', "edit");
define('REQ_ACTION_DELETE', "delete");
if (System::is_local()) {
    $GLOBALS['date_init_value'] = 'NULL';
} else {
    $GLOBALS['date_init_value'] = '0000-00-00 00:00:00';
}
if (!System::is_local()) {
    require_once ROOT_PATH . 'packages/core/includes/system/memcached.php';
}

require_once ROOT_PATH . 'packages/vissale/lib/php/log.php';


const MOBILE_TYPE_DOMESTIC = 1;
const MOBILE_TYPE_FOREIGN = 2;
const MOBILE_TYPES = [
    MOBILE_TYPE_DOMESTIC => 'VN',
    MOBILE_TYPE_FOREIGN => 'QT',
];

const MOBILE_REGEX_PATTERNS = [
    MOBILE_TYPE_DOMESTIC => '/^0[1-9][0-9]{8,9}$/',
    MOBILE_TYPE_FOREIGN => '/^00[1-9][0-9]{1,17}$/',
];

function logApiBigGame($data)
{
    $log  = json_encode($data) . "-------------------------" . PHP_EOL;
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/cache/logApiBigGame/')) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . '/cache/logApiBigGame/', 0777, true);
    }
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/cache/logApiBigGame/log_' . date("Y-m-d H:i:s") . '.log', $log, FILE_APPEND);
}

function addLogMongoSecurity($action)
{
    $data = [];
    $user_id = get_user_id();
    $user_name = Session::get('user_id');
    $datetime = time();
    $content = '';
    $ip = getIpAddress();
    $getBrowser = getBrowser();
    $yourbrowser = "Trình duyệt: " . $getBrowser['name'] . " " . $getBrowser['version'] . " on " . $getBrowser['platform'];
    $actual_link = System::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $isMob = is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"));
    $isTab = is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet"));
    $isDesktop = !$isMob && !$isTab;
    $device = '';
    if ($isMob) {
        $device = "Điện thoại";
    }
    if (!$isTab && !$isMob) {
        $device = "Máy tính";
    }
    if ($isTab) {
        $device = "Máy tính bảng";
    }
    $ip_address = 'Địa chỉ IP: ' . $ip;
    $user_agent =  $yourbrowser;
    $device_type =  'Thiết bị: ' . $device;
    $content =  'URL: ' . $actual_link;
    $data['user_id']     = $user_id;
    $data['username']    = $user_name;
    $data['created_at']  = $datetime;
    $data['action']      = $action['content'];
    $data['action_type'] = $action['type'];
    $data['content']     = $content;
    $data['ip_address']  = $ip_address;
    $data['user_agent']  = $user_agent;
    $data['device_type'] = $device_type;
    $data['created_date'] = date('Y-m-d');
    storeSecurityLog($data);
}
function addLogSecurityMysql($action)
{
    // $mongoDB = MONGO_DB_NAME;
    // $mongoHost = MONGO_HOST;
    // $mongoUser = MONGO_USERNAME;
    // $mongoPass = MONGO_PASSWORD;
    // $arrayAccess = [
    //     'username' => $mongoUser,
    //     'password' => $mongoPass
    // ];
    // if ($mongoUser && $mongoPass) {
    //      $collection = (new MongoDB\Client($mongoHost,$arrayAccess))->{$mongoDB}->security_log;
    // } else {
    //      $collection = (new MongoDB\Client())->{$mongoDB}->security_log;
    // }
    $user_id = get_user_id();
    $user_name = Session::get('user_id');
    $datetime = date('Y-m-d H:i:s');
    $content = '';
    $ip = getIpAddress();
    $getBrowser = getBrowser();
    $yourbrowser = "Trình duyệt: " . $getBrowser['name'] . " " . $getBrowser['version'] . " on " . $getBrowser['platform'];
    $actual_link = System::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $isMob = is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"));
    $isTab = is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet"));
    $isDesktop = !$isMob && !$isTab;
    $device = '';
    if ($isMob) {
        $device = "Điện thoại";
    }
    if (!$isTab && !$isMob) {
        $device = "Máy tính";
    }
    if ($isTab) {
        $device = "Máy tính bảng";
    }
    $ip_address = 'Địa chỉ IP: ' . $ip;
    $user_agent =  $yourbrowser;
    $device_type =  'Thiết bị: ' . $device;
    $content =  'URL: ' . $actual_link;
    $array = [
        'user_id'     => $user_id,
        'username'    => $user_name,
        'created_at'  => $datetime,
        'action'      => $action['content'],
        'action_type' => $action['type'],
        'content'     => $content,
        'ip_address'  => $ip_address,
        'user_agent'  => $user_agent,
        'device_type' => $device_type,
        'created_date' => date('Y-m-d')
    ];
    DB::insert('security_log', $array);
    // $insertOneResult = $collection->insertOne();
}
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/OPR/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Edge/i', $u_agent)) {
        $bname = 'Edge';
        $ub = "Edge";
    } elseif (preg_match('/Trident/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
    }
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
    if ($version == null || $version == "") {
        $version = "?";
    }
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}
function getIpAddress()
{
    $ipAddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($ipAddressList as $ip) {
            if (!empty($ip)) {
                $ipAddress = $ip;
                break;
            }
        }
    } else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (!empty($_SERVER['HTTP_FORWARDED'])) {
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    } else if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    }
    return $ipAddress;
}
function product_is_in_order($id)
{
    $id = DB::escape($id);
    $sql = "SELECT id FROM orders_products WHERE product_id = $id LIMIT 0,1";
    $query = DB::fetch($sql);
    if ($query) {
        return true;
    } else {
        return false;
    }
}
function is_system_user()
{
    $user_id = get_user_id();
    $row = DB::fetch('select id,system_group_id from groups_system_account where user_id=' . $user_id . ' limit 0,1');
    if (!empty($row)) {
        $system_group_id = $row['system_group_id'];
        if (DB::exists('select groups.id from `groups` join groups_system on groups_system.id=groups.system_group_id where ' . Systems::getIDStructureChildCondition(DB::structure_id('groups_system', $system_group_id)))) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function check_system_user_permission($permission)
{
    $user_id = get_user_id();
    $row = DB::fetch('select id,system_group_id,permissions from groups_system_account where user_id=' . $user_id . ' limit 0,1');
    if (!empty($row)) {
        $system_group_id = $row['system_group_id'];
        if (DB::exists('select groups.id from `groups` join groups_system on groups_system.id=groups.system_group_id where ' . Systems::getIDStructureChildCondition(DB::structure_id('groups_system', $system_group_id)))) {
            $permissions = $row['permissions'];
            if ($permissions) {
                $permissionArr = explode(',', $permissions);
                if (in_array($permission, $permissionArr)) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function array_shuffle(array &$array, $keepKey = false)
{
    if (!$keepKey) {
        shuffle($array);
        return $array;
    }

    $output = [];
    $keys = array_keys($array);
    shuffle($keys);

    array_map(function ($key) use (&$array, &$output) {
        $output[$key] = $array[$key];
    }, $keys);

    return $array = $output;
}
function get_schedules()
{
    $group_id = Session::get('group_id');
    $user_id = get_user_id();
    $andConditions = [];
    $andConditions[] = 'crm_customer_schedule.group_id = ' . $group_id;
    if (Menu::$admin_group || check_user_privilege('CUSTOMER')) {
        //$andConditions[] = '( crm_customer_schedule.created_user_id = ' . $user_id . ' OR orders.user_assigned = ' . $user_id . ')';
    } else {
        $andConditions[] = '( crm_customer_schedule.created_user_id = ' . $user_id . ' OR orders.user_assigned = ' . $user_id . ')';
    }
    $limit = 5;
    $start_time = (new DateTime(date('Y-m-d')))->getTimestamp();
    $end_time = (new DateTime(date('Y-m-d 23:59:59')))->getTimestamp();
    if ($from_date = URL::get('from_date')) {
        $fromDate =  Date_Time::to_time($from_date);
        $andConditions[] = "`crm_customer_schedule`.appointed_time >= '$fromDate'";
    } else {
        $andConditions[] = "`crm_customer_schedule`.appointed_time >= '$start_time'";
    }
    if ($to_date = URL::get('to_date')) {
        $toDate =  Date_Time::to_time($to_date) + (24 * 3600);
        $andConditions[] = "`crm_customer_schedule`.appointed_time <= '$toDate'";
    } else {
        $andConditions[] = "`crm_customer_schedule`.appointed_time <= '$end_time'";
    }
    $cond = '';
    $andQuery = implode(' AND ', $andConditions);
    if (!empty($andQuery)) {
        $cond .= " ($andQuery)";
    }
    $sql = "
            SELECT 
                    `crm_customer_schedule`.id,
                    `crm_customer_schedule`.note,
                    `crm_customer_schedule`.note_services,
                    `crm_customer_schedule`.created_time,
                    `crm_customer_schedule`.customer_id,
                    `crm_customer_schedule`.group_id,
                    `crm_customer_schedule`.created_user_id,
                    `crm_customer_schedule`.branch_id,
                    `crm_customer_schedule`.staff_id,
                    `crm_customer_schedule`.status_id,
                    `crm_customer_schedule`.staff_name,
                    `crm_customer_schedule`.schedule_type,
                    `crm_customer_schedule`.appointed_time AS appointed_time,
                    `crm_customer_schedule`.arrival_time AS arrival_time,
                    crm_customer.name AS customer_name,
                    crm_customer.mobile AS customer_mobile,
                    crm_customer.status_id AS customer_status_id,
                    users.name as created_user_name
            FROM `crm_customer_schedule`
            LEFT JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            JOIN orders ON (orders.id = `crm_customer_schedule`.order_id)
            WHERE $cond
            ORDER BY `crm_customer_schedule`.arrival_time ASC, `crm_customer_schedule`.appointed_time
            LIMIT 0,$limit
        ";
    return DB::fetch_all($sql);
}
function format_code($value, $group_id = false)
{
    $group_id = $group_id ? $group_id : Session::get('group_id');
    $code = get_prefix($group_id);
    $code .= str_pad(($value), 6, "0", STR_PAD_LEFT);
    return $code;
}
function get_prefix($group_id = false)
{
    $group_id = $group_id ? $group_id : Session::get('group_id');
    if ($prefix_account = DB::fetch('select prefix_account from `groups` where id=' . $group_id, 'prefix_account')) {
        return $prefix_account;
    } else {
        return '';
    }
}
function get_account_groups()
{
    $groups = [];
    $cond = 'account_group.group_id=' . Session::get('group_id');
    if (!Session::get('admin_group')) {
        $account_group_ids = get_account_group_ids();
        if ($account_group_ids) { //quy ly nhom
            $cond .= ' AND account_group.id IN (' . $account_group_ids . ')';
        } else {
            if ($account_group_id = DB::fetch('select account_group_id from account where account.id="' . Session::get('user_id') . '"', 'account_group_id')) {
                $cond .= ' AND account_group.id = ' . $account_group_id . '';
            } else {
                if (!check_user_privilege('CHIADON') && !check_user_privilege('MARKETING')) {
                    return [];
                }
            }
        }
    }
    $sql = '
      select 
        account_group.id,account_group.name 
      from 
        account_group 
        join `account` on `account`.account_group_id=account_group.id
      where 
        ' . $cond . ' 
      order by 
        account_group.name
    ';
    $groups = DB::fetch_all($sql);
    return $groups;
}
function get_user_ids_in_group($account_group_ids)
{
    $items = '';
    if ($account_group_ids) {
        $items = DB::fetch_all('select users.id from users join account on account.id = users.username where account.account_group_id IN  (' . $account_group_ids . ')');
        $items = MiString::get_list($items, 'id');
        $items = implode(',', $items);
    }
    return $items;
}
function get_account_group_ids()
{
    $user_id = get_user_id();
    $items = DB::fetch_all('select id from account_group where admin_user_id=' . $user_id);
    $account_group_admin = DB::fetch_all('select account_group_id as id from account_group_admin where user_id=' . $user_id);
    $items += $account_group_admin;
    $items = MiString::get_list($items, 'id');
    $items = implode(',', $items);
    return $items;
}
function is_account_group_manager()
{
    $user_id = get_user_id();
    if ($user_id) {
        $account_group = DB::fetch('select count(id) as total from account_group where admin_user_id=' . $user_id);
        if ($account_group['total'] > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function is_account_group_department()
{
    $user_id = get_user_id();
    if ($user_id) {
        $account_group = DB::fetch('select count(id) as total from account_group_admin where user_id=' . $user_id);
        if ($account_group['total'] > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_account_id()
{
    $user_id = Session::get('user_id');
    if ($user_id) {
        $account_group = DB::fetch('select id,account_group_id from account where id="' . $user_id . '"');
        if (!empty($account_group['account_group_id'])) {
            return $account_group['account_group_id'];
        } else {
            return [];
        }
    } else {
        return [];
    }
}

function can_tuha_administrator()
{
    return has_account_privilege(Session::get('user_id'), 34);
}

function can_tuha_content_admin()
{
    return has_account_privilege(Session::get('user_id'), 35);
}

/**
 * Determines if account privilege.
 *
 * @param      string  $user_name     The user name
 * @param      int     $privilege_id  The privilege identifier
 *
 * @return     bool    True if account privilege, False otherwise.
 */
function has_account_privilege(string $user_name, int $privilege_id)
{
    if (User::is_admin()) {
        return true;
    }

    $fmt = 'select count(id) as total from account_privilege where account_id="%s" and privilege_id=%d';
    $sql = sprintf($fmt, $user_name, $privilege_id);
    $account_privilege = DB::fetch($sql);

    return isset($account_privilege['total']) && $account_privilege['total'] > 0;
}

function notify_to_group($group_id, $type, $title, $body)
{
    //$type: 'ORDER','COMMENT', 'INBOX'
    /*
    POST https://api.tuha.vn/api/push-notification
    HEADERS:
      Content-Typeapplication/json
    BODY
      {
        "type": "COMMENT",
        "title": "Tin nhắn mới",
        "body": "Quy Nguyen đã gửi một tin nhắn",
        "topic": "group_1139" hoặc 'user_dinhkkk'
      }
    */
    $url = 'https://api.tuha.vn/api/push-notification';
    $fields = array(
        "type" => $type,
        "title" => $title,
        "body" => $body,
        "topic" => "group_" . $group_id
    );
    $fields = json_encode($fields);
    /*$headers = array (
        'Content-Type: application/json'
    );
    System::debug($fields);
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
    $result = curl_exec ( $ch );
    //echo $result;
    curl_close ( $ch );
    */
    ///
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

function get_group_options($key, $group_id = 0)
{
    return GroupOption::group($group_id)->option($key);
}

function group_option_defaults($key)
{
    $m_key_value = '';
    $arrDefault = array(
        'no_create_order_when_duplicated' => 1,
        'duplicate_type' => 0
    );
    if (in_array($key, array_keys($arrDefault))) {
        $m_key_value = $arrDefault[$key];
    }
    return $m_key_value;
}

function update_group_options($key, $value)
{
    $group_id = Session::get('group_id');
    $m_key = $key . '_' . $group_id;
    if (!System::is_local()) {
        $m_key_value = MC::get_items($m_key);
    }
    if ($row = DB::fetch('select id from group_options where `key`="' . $key . '" and group_id=' . $group_id)) {
        DB::update('group_options', array('value' => $value, 'updated_at' => date('Y-m-d H:i:s')), 'id=' . $row['id']);
        if (!System::is_local() and $m_key_value and $m_key_value != $value) {
            MC::set_items($m_key, $value, time() + 60 * 30); //30 phut
        }
    } else {
        DB::insert('group_options', array('group_id' => $group_id, 'key' => $key, 'value' => $value, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => $GLOBALS['date_init_value']));
        if (!System::is_local()) {
            MC::set_items($m_key, $value, time() + 60 * 30); //30 phut
        }
    }
}

function get_user_id()
{
    return User::is_login() ? (DB::fetch('select id from users where username="' . Session::get('user_id') . '"', 'id')) : false;
}


function is_admin_group_system()
{
    $user_id = get_user_id();
    $count = DB::fetch('select count(id) as total from groups_system_account where user_id=' . $user_id);
    return $count['total'] > 0;
}

function get_admin_group_system($system_group_id)
{
    $sql = '
        select
            groups_system_account.id,groups_system_account.user_id,concat(users.name, IF(groups_system_account.role=1," (CT)"," (PCT)")) as name
        FROM
                groups_system_account
            inner join users on users.id = groups_system_account.user_id
        where
            groups_system_account.system_group_id=' . $system_group_id . '
        ';
    $items = DB::fetch_all($sql);
    return $items;
}
function get_product_columns()
{
    $columns = array();
    $order_columns = 'STT:index:1,Mã:code:2,Tên sản phẩm dịch vụ:name:3,Giá:price:4,Mầu sắc:color:5,Size:size:6,Rộng:width:7,Cao:height:8,Kg:weight:9,Đơn vị:unit:10,Phân loại:bundle:11,Nhãn:label:12';

    $arr = explode(',', $order_columns);
    if (is_array($arr) and !empty($arr)) {
        foreach ($arr as $value) {
            $arr1 = explode(':', $value);
            $columns[$arr1[1]]['id'] = $arr1[1];
            $columns[$arr1[1]]['name'] = $arr1[0];
            $columns[$arr1[1]]['order'] = $arr1[2];
            $columns[$arr1[1]]['selected'] = true;
        }
    }
    System::sksort($columns, 'order', true);
    return $columns;
}
function get_order_by_product_columns()
{
    $columns = array();
    $order_columns_ = 'STT:index:1,
    Tên KH:customer_name:2,
    Mã công ty:pre:3,
    Mã đơn hàng:order_id:4,
    Mã vận chuyển:postal_code:5,
    Số điện thoại:mobile:6,
    Note chung:note1:7,
    Trạng thái:status_name:8,
    Tổng tiền:total_price:9,
    Trả trước:prepaid:10,
    Còn nợ:prepaid_remain:11,
    Mã hàng:product_code:12,
    Tên hàng đầy đủ:full_name:13,
    Tên hàng:product_name:13,
    ĐVT:product_unit:14,
    Số lượng:product_quantity:15,
    Đơn giá:product_price:16,
    Giảm giá SP:discount_amount:17,
    Thành tiền SP:product_amount:18,
    Giảm giá ĐH:discount_price:19,
    Địa chỉ:address:20,
    Phường / xã:ward:21,
    Quận / huyện:district_reciever:22,
    Tỉnh / thành:city:23,
    Note giao hàng:shipping_note:24,
    Xác nhận bởi:user_confirmed:25,
    Ngày xác nhận:confirmed:26,
    Giao hàng:user_delivered:27,
    Phân loại:bundle:28,
    Nguồn:source_name:29,
    Đơn:type:30,
    Người tạo đơn:user_created:31,
    Nguồn Upsale:upsale_from_user_id:32,
    Đơn đã chia cho:user_assigned:33,
    Ngày tạo:created:34,
    Ngày chia:assigned:35,
    Ngày chốt:confirmed:36,
    Ngày chuyển đóng hàng:accounting_confirmed:37,
    Ngày chuyển hàng:delivered:38,
    Ngày thành công:update_successed_time:39,
    Ngày thu tiền:update_paid_time:40,
    Ngày chuyển hoàn:update_returned_time:41,
    Ngày Đã trả hàng về kho:update_returned_to_warehouse_time:42,
    Nguồn Shop:source_shop_id:43,
    Nguồn Người tạo:source_created_user:44,
    Nguồn Người upsale:source_upsale:45';

    $order_columns = $order_columns_;
    $arr = explode(',', $order_columns);

    if (is_array($arr) and !empty($arr)) {
        foreach ($arr as $value) {
            $arr1 = explode(':', $value);
            $columns[$arr1[1]]['id'] = $arr1[1];
            $columns[$arr1[1]]['name'] = $arr1[0];
            $columns[$arr1[1]]['order'] = $arr1[2];
            $columns[$arr1[1]]['selected'] = true;
        }
    }
    // System::sksort($columns, 'order', true);
    return $columns;
}
function get_ship_order_columns()
{
    $columns = array();
    //$order_columns_ = 'STT:index:1,NV được chia:user_assigned:2,Mã:id:3,Mã VĐ:postal_code:4,Nguồn:source:5,Tên KH:customer_name:6,Số điện thoại:mobile:7,Địa chỉ:address:8,Tỉnh/thành:city:9,Sản phẩm:products:10,Bài Post:fb_post_id:11,Page:page:11,Note chung:note1:12,Note 2:note2:12,Trạng thái:status_name:13,Tổng tiền:total_price:14,Trùng đơn:duplicate_note:15,NV Xác nhận:user_confirmed:16,Ngày XN:confirmed:17,Người chuyển:user_delivered:18,Ngày chuyển:delivered:19,Người tạo:user_created:20,Ngày tạo:created:21,Mã bưu điện:telco_code:22,Note giao hàng:shipping_note:23,Hình thức giao:shipping_service:24,Công ty:group_name:25,Nguồn Up Sale:upsale_from_user_id:26';
    $order_columns_ = 'STT:index:1,Tên KH:customer_name:2,Mã đơn hàng:id:3,Số điện thoại:mobile:4,Tổng tiền:prepaid_remain:5,Sản phẩm:products:6,Địa chỉ:address:7,Phường / xã:ward:8,Quận / huyện:district_reciever:9,Tỉnh / thành:city:10,Note giao hàng:shipping_note:11';
    //Portal::get_setting('order_columns',true,'#default');//,Nguồn UTM:utm_source:24,Chiến dịch UTM:utm_campaign:25,Nội dung UTM:utm_content:26
    //$order_columns = Portal::get_setting('order_columns',true,'#default');
    $order_columns = $order_columns_; // $order_columns?$order_columns:$order_columns_;
    $arr = explode(',', $order_columns);
    if (is_array($arr) and !empty($arr)) {
        foreach ($arr as $value) {
            $arr1 = explode(':', $value);
            $columns[$arr1[1]]['id'] = $arr1[1];
            $columns[$arr1[1]]['name'] = $arr1[0];
            $columns[$arr1[1]]['order'] = $arr1[2];
            $columns[$arr1[1]]['selected'] = true;
        }
    }
    System::sksort($columns, 'order', true);
    return $columns;
}
function get_order_columns($all = false)
{
    $group_id = Session::get('group_id');
    $columns = array();
    $order_columns_ = 'STT:index:1,NV được chia:user_assigned:3,Mã:id:2,Email KH:email:4,Mã VĐ:postal_code:5,Nguồn:source:6,Tên KH:customer_name:7,Số điện thoại:mobile:8,Địa chỉ:address:9,Tỉnh/thành:city:10,Sản phẩm:products:11,FB KH:fb_customer_id:12,Bài Post:fb_post_id:13,Note chung:note1:14,Page:page:15,Note 2:note2:16,Trạng thái:status_name:17,Tổng tiền:total_price:18,Trùng đơn:duplicate_note:19,NV Xác nhận:user_confirmed:20,Ngày XN:confirmed:21,Người chuyển:user_delivered:22,Ngày chuyển:delivered:23,Người tạo:user_created:24,Ngày tạo:created:25,Mã bưu điện:telco_code:26,Note giao hàng:shipping_note:27,Hình thức giao:shipping_service:28,Công ty:group_name:29,Nguồn Up Sale:upsale_from_user_id:30,Mã KH:customer_id:31';
    //Portal::get_setting('order_columns',true,'#default');//,Nguồn UTM:utm_source:24,Chiến dịch UTM:utm_campaign:25,Nội dung UTM:utm_content:26
    $order_columns = Portal::get_setting('order_columns', true, '#default');
    $order_columns = $order_columns ? $order_columns : $order_columns_;
    if (Url::get('cmd') == 'list_pos') {
        $order_columns = 'STT:index:1,Mã:id:2,Email KH:email:4,Nguồn:source:6,Tên KH:customer_name:7,Số điện thoại:mobile:8,Sản phẩm:products:9,Tổng tiền:total_price:10,FB KH:fb_customer_id:12,Note chung:note1:14,Note 2:note2:16,Trạng thái:status_name:17,NV thu tiề:user_confirmed:20,Ngày hoàn thành:confirmed:21,Người tạo:user_created:24,Ngày tạo:created:25';
        $arr = explode(',', $order_columns);
        if (is_array($arr) and !empty($arr)) {
            foreach ($arr as $value) {
                $arr1 = explode(':', $value);
                if (isset($arr1[1])) {
                    $columns[$arr1[1]]['id'] = $arr1[1];
                    $columns[$arr1[1]]['name'] = $arr1[0];
                    $columns[$arr1[1]]['order'] = $arr1[2];
                    $columns[$arr1[1]]['selected'] = true;
                }
            }
        }
    } else {
        $arr = explode(',', $order_columns);
        if ($row = DB::fetch('select id,show_columns from orders_column_custom WHERE group_id=' . $group_id)) {
            $user_order_column = $row['show_columns'];
            $user_arr = explode(',', $user_order_column);
            if ($all) {
                if (is_array($arr) and !empty($arr)) {
                    foreach ($arr as $value) {
                        $arr1 = explode(':', $value);
                        $columns[$arr1[1]]['id'] = $arr1[1];
                        $columns[$arr1[1]]['name'] = $arr1[0];
                        $columns[$arr1[1]]['order'] = $arr1[2];
                        $columns[$arr1[1]]['selected'] = false;
                    }
                }
                if (is_array($user_arr) and !empty($user_arr)) {
                    foreach ($user_arr as $value) {
                        $arr1 = explode(':', $value);
                        if (isset($columns[$arr1[1]])) {
                            $columns[$arr1[1]]['selected'] = true;
                            $columns[$arr1[1]]['order'] = $arr1[2];
                        }
                    }
                }
            } else {
                if (is_array($user_arr) and !empty($user_arr)) {
                    foreach ($user_arr as $value) {
                        $arr2 = explode(':', $value);
                        if (isset($arr2[1])) {
                            $columns[$arr2[1]]['id'] = $arr2[1];
                            $columns[$arr2[1]]['name'] = $arr2[0];
                            $columns[$arr2[1]]['order'] = $arr2[2];
                            $columns[$arr2[1]]['selected'] = true;
                        }
                    }
                }
            }
        } else {
            if (is_array($arr) and !empty($arr)) {
                foreach ($arr as $value) {
                    $arr1 = explode(':', $value);
                    if (isset($arr1[1])) {
                        $columns[$arr1[1]]['id'] = $arr1[1];
                        $columns[$arr1[1]]['name'] = $arr1[0];
                        $columns[$arr1[1]]['order'] = $arr1[2];
                        $columns[$arr1[1]]['selected'] = true;
                    }
                }
            }
        }
    }
    if (!isset($columns['customer_id'])) {
        $columns['customer_id'] = [
            'id' => 'customer_id',
            'name' => 'Mã KH',
            'order' => 28,
            'selected' => 1
        ];
    }
    System::sksort($columns, 'order', true);
    return $columns;
}

function is_master_group()
{
    if (DB::exists('select id from groups_master where account_id="' . Session::get('user_id') . '"')) {
        return true;
    } else {
        return false;
    }
}

function get_groups($master_group_id)
{
    $sql = '
                select
                    groups.id,groups.name
                FROM
                `groups`
                WHERE
                    groups.master_group_id = ' . $master_group_id . '
                order by
                    groups.id
            ';
    $items = DB::fetch_all($sql);
    return $items;
}

function get_master_group_id()
{
    return DB::fetch('select id from groups_master where account_id="' . Session::get('user_id') . '"', 'id');
}

function get_fb_user_page($fb_customer_id)
{
    return DB::fetch('select fb_id,fb_name from fb_customers where id=' . $fb_customer_id);
}

function get_fb_post_comment($fb_comment_id)
{
    return DB::fetch('select post_id,comment_id from fb_post_comments where id="' . $fb_comment_id . '" and group_id=' . Session::get('group_id') . '');
}

function get_fb_post($fb_post_id)
{
    return DB::fetch('select post_id,page_id from fb_posts where id=' . $fb_post_id . ' and group_id=' . Session::get('group_id') . '');
}

function get_basic_fb_user_info($id)
{
    $url = 'https://graph.facebook.com/' . $id;
    $info = json_decode(file_get_contents($url), true);
    return $info;
}

function check_user_privilege($privilege_code, $user_id = false, $status = false, $forceCheck = false)
{
    if (Session::get('admin_group') && !$forceCheck) {
        return true;
    }

    return UserRole::user($user_id)->has($privilege_code);
}

function read_excel($inputFileName)
{
    $max_allow_row = 5000;
    require 'vendor/autoload.php';
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    //    $reader->setReadDataOnly(TRUE);
    $spreadsheet = $reader->load($inputFileName);
    //    $spreadsheet = $reader->load("test.xlsx");
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    if ($highestRow > $max_allow_row) {
        die('<h4 style="margin:auto;float:left;color:#f00;padding:5px;border:1px solid #F00;border-radius: 5px;">File excel của bạn có ' . System::display_number($highestRow) . ' dòng. Bạn chỉ được tải lên file excel có tối đa 10,000 dòng, vui lòng kiểm tra lại.</h4>');
    }
    $countRow = 1;
    $data = [];
    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $rowData = [];
        $c_r = 1;
        $empty = true;
        foreach ($cellIterator as $cell) {
            $rowData['id'] = $countRow;
            $row_value = $cell->getValue();
            if ($row_value) {
                $empty = false;
            }
            $rowData[$c_r] = $row_value;
            $data[$cell->getRow()] = $rowData;
            $c_r++;
        }
        if ($empty == true) {
            unset($data[$countRow]);
        }
        $countRow++;
    }
    return (array) $data;
}
function read_excel_old($excel_file)
{
    require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
    $objPHPExcel = PHPExcel_IOFactory::load($excel_file);
    $dataArr = array();
    $available_total = 0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
        $worksheetTitle = $worksheet->getTitle();
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        for ($row = 1; $row <= $highestRow; ++$row) {
            $dataArr[$row]['id'] = $row;
            $empty = true;
            for ($col = 0; $col < $highestColumnIndex; ++$col) {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                $dataArr[$row][$col + 1] = $val;
                if ($val and $col == 2 and $row != 1) {
                    $dataArr[$row][$col + 1] = $val . '';
                }
                if ($val) {
                    $empty = false;
                }
            }
            if ($empty == true and $row != 1) {
                unset($dataArr[$row]);
            }
        }
    }
    return $dataArr;
}
function is_group_owner()
{
    $row = DB::fetch('select count(id) as total from `groups` where code="' . Session::get('user_id') . '"');
    return $row['total'] > 0;
}
function object_to_array($data)
{
    if (is_array($data) || is_object($data)) {
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}
function get_product_remain($product_id, $warehouse_id = false)
{
    $product_code = DB::fetch('select code from products where id =' . $product_id, 'code');
    if ($warehouse_id == false) {
        $warehouse_id = get_default_warehouse();
    }
    $date = date('Y-m-d');
    $group_id = Session::get('group_id');
    $sql = '
        SELECT 
            sum(
                qlbh_stock_invoice_detail.quantity
            ) as total 
        FROM 
            qlbh_stock_invoice_detail
            JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id 
        WHERE 
            qlbh_stock_invoice.group_id = ' . $group_id . '
            AND qlbh_stock_invoice_detail.product_code = "' . $product_code . '" 
            AND (
                (
                    qlbh_stock_invoice.type = "IMPORT"
                    AND qlbh_stock_invoice_detail.warehouse_id = ' . $warehouse_id . '
                )
            ) 
            AND qlbh_stock_invoice.create_date <= "' . $date . '"
    ';
    $total_imported1 = DB::fetch($sql, 'total');
    $sql = '
        SELECT 
            sum(
                qlbh_stock_invoice_detail.quantity
            ) as total 
        FROM 
            qlbh_stock_invoice_detail
            JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id 
        WHERE 
            qlbh_stock_invoice.group_id = ' . $group_id . '
            AND qlbh_stock_invoice_detail.product_code = "' . $product_code . '" 
            AND (
                (
                    qlbh_stock_invoice.type = "EXPORT"
                    AND qlbh_stock_invoice_detail.to_warehouse_id = ' . $warehouse_id . '
                )
            ) 
            AND qlbh_stock_invoice.create_date <= "' . $date . '"
    ';
    $total_imported2 = DB::fetch($sql, 'total');

    $sql = '
        SELECT 
            sum(
                qlbh_stock_invoice_detail.quantity
            ) as quantity 
        FROM 
            qlbh_stock_invoice_detail 
            JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id 
        WHERE 
            qlbh_stock_invoice.group_id = ' . $group_id . ' 
            AND qlbh_stock_invoice_detail.product_code = "' . $product_code . '" 
            AND (
                qlbh_stock_invoice.type=\'EXPORT\' AND qlbh_stock_invoice_detail.warehouse_id=' . $warehouse_id . '
            ) 
            AND qlbh_stock_invoice.create_date <= "' . $date . '"
    ';

    $total_exported = DB::fetch($sql, 'quantity');

    return ($total_imported1 + $total_imported2) - $total_exported;
}
function get_default_warehouse($group_id = false)
{
    $group_id = $group_id ? $group_id : Session::get('group_id');
    $default_warehouse_id = DB::fetch('SELECT id FROM `qlbh_warehouse` WHERE `qlbh_warehouse`.`is_default` = 1 and group_id=' . $group_id, 'id');
    $default_warehouse_id = $default_warehouse_id ? $default_warehouse_id : 1;
    return $default_warehouse_id;
}
function allow_negative_export()
{
    return get_group_options('disable_negative_export') ? false : true;
}
function get_no_revenue_status($group_id)
{
    $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where no_revenue=1 and (group_id = ' . $group_id . ' or statuses.is_system=1)'));
    $no_revenue_status = implode(',', $no_revenue_status);
    return $no_revenue_status;
}

/**
 * Gets the group.
 *
 * @param      bool|int  $groupID  The group id
 * @param      array     $select   The select
 *
 * @return     bool      The group.
 */
function get_group_system_by_group_id(int $groupID, array $select = ['*'])
{
    $fmt = 'SELECT %s FROM groups 
            JOIN groups_system ON groups_system.id = groups.system_group_id AND groups.id = %d';

    $sql = sprintf($fmt, implode(',', $select), $groupID);

    return $groupID ? DB::fetch($sql) : 0;
}
/**
 * Sends a json.
 *
 * @param      <type>  $data   The data
 */
function send_json($data)
{
    header('Content-Type: application/json');
    die(json_encode($data));
}

/**
 * { function_description }
 *
 * @param      <type>  $string    The string
 * @param      <type>  $encoding  The encoding
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function mb_ucfirst($string, $encoding = 'utf8')
{
    $string = mb_strtolower($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, null, $encoding);

    return mb_strtoupper($firstChar, $encoding) . $then;
}

/**
 * Determines whether the specified datetime is datetime.
 *
 * @param      <type>  $datetime  The datetime
 *
 * @return     bool    True if the specified datetime is datetime, False otherwise.
 */
function is_datetime($datetime)
{
    return !empty($datetime)  && !preg_match('#^00(:|-)00(:|-)00#', $datetime);
}

/**
 * Determines whether the specified datetime is empty date.
 *
 * @param      string  $datetime  The datetime
 *
 * @return     bool    True if the specified datetime is empty date, False otherwise.
 */
function is_empty_date($datetime)
{
    return empty($datetime) || $datetime === '0000-00-00 00:00:00';
}

/**
 * Thực hiện explode một chuỗi ID thành mảng ID
 *
 * @param      string  $plainIDs  The plain i ds
 *
 * @return     array
 */
function parse_id(string $plainIDs)
{
    $IDs = array_map('intval', explode(',', $plainIDs));

    return array_filter($IDs, function ($ID) {
        return $ID > 0;
    });
}

/**
 * { function_description }
 *
 * @param      string  $intFormated  The integer formated
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function parse_int_formated(string $intFormated)
{
    return preg_replace('#[^\d\.]+#', '', $intFormated);
}

/**
 * Loại bỏ khoảng trắng thừa 2 đầu chuỗi và gộp các khoảng trắng liên tiếp
 *
 * @param      string  $value  The value
 */
function trimSpace(string $value)
{
    return preg_replace('#\s+#', ' ', trim($value));
}

/**
 * Lấy tất cả roles của user
 * Chỉ gọi hàm này nếu cần data là "tươi, mới nhất"(Ví dụ ngay sau khi cập nhật hoặc insert role của user và cần
 * lấy role của user đó)
 * Nếu không thì nên dùng getRolesCode() vì có caching trong cùng request
 *
 * @param      array   $selects  The selects
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function getUserRoles()
{
    return array_column(
        DB::fetch_all_array('
        SELECT DISTINCT(UPPER(roles_to_privilege.privilege_code)) AS code
        FROM users_roles
        JOIN roles ON roles.id = users_roles.role_id
        JOIN roles_to_privilege ON roles_to_privilege.role_id = roles.id
        WHERE users_roles.user_id = ' . get_user_id()),
        'code'
    );
}


function xss_clean(string $data, int $limit = 0): string
{
    $data = DB::escape($data);
    if ($limit) {
        $data = substr($data, 0, $limit);
    } //end if

    // Fix &entity\n;
    $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
        // Remove really unwanted tags
        $old_data = $data;
        $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    // we are done...
    return $data;
}


function validateTelephoneNumber(string $telephone_number, int $type = AdminOrders::MOBILE_TYPE_DOMESTIC): bool
{
    $patterns = AdminOrders::$mobile_regex_patterns;
    $pattern = $patterns[$type] ?? null;
    if (!$pattern) {
        return false;
    } //end if

    if (preg_match($pattern, $telephone_number)) {
        return true;
    } //end if

    return false;
}

function isAccountTestValidatePhone(): bool
{
    if (SYSTEM_PHONE_VALIDATOR) {
        return true;
    } //end if

    $group_id = Session::get('group_id');
    if (in_array($group_id, ACCOUNT_TEST_VALIDATE_PHONE)) {
        return true;
    } //end if

    return false;
}

function checkPhoneNumberType($telephone_number): int
{
    return AdminOrders::MOBILE_TYPE_DOMESTIC;
}


function checkUserOnlyRole($privilege_code)
{
    $user_id = get_user_id();
    $sql = "SELECT * FROM 
                            users_roles
                        LEFT JOIN 
                            roles_to_privilege ON users_roles.role_id = roles_to_privilege.role_id
                        WHERE 
                            users_roles.user_id = $user_id
                        ";
    $results = DB::fetch_all($sql);
    if ($results) {
        $array = [];
        foreach ($results as $key => $value) {
            $array[] = $value['privilege_code'];
        }
        if (!empty($array) && sizeof($array) == 1 && in_array($privilege_code, $array)) {
            return true;
        }
        return false;
    }
    return false;
}

function checkPermissionAccess($privilege_code = NULL)
{
    $user_id = get_user_id();
    $str_privilege_code = '';
    $results = [];
    if ($privilege_code) {
        $str_privilege_code = "('" . implode("','", $privilege_code) . "')";
        $sql = "SELECT 
                    roles_to_privilege.id
                FROM 
                    roles_to_privilege 
                LEFT JOIN 
                    users_roles ON users_roles.role_id = roles_to_privilege.role_id
                WHERE 
                    roles_to_privilege.privilege_code 
                IN  
                    $str_privilege_code 
                AND 
                    users_roles.user_id = $user_id";
        $results = DB::fetch_all($sql);
    }
    if (Url::get('page') == 'rating-question-template') {
        if (sizeof($results) > 0) {
            return true;
        }
        return false;
    } else if (Session::get('admin_group') || is_group_owner() || sizeof($results) > 0) {
        return true;
    }
    return false;
}


if (!function_exists('limitAndPage')) {
    /**
     * limitAndPage function
     *
     * @param integer $item_per_page
     * @param string $page_name
     * @return string
     */
    function generatePagination(
        int $limit = 10,
        string $page_name = 'page_no'
    ): string {
        $page_no = intval(Url::get($page_name) ?: 1);
        $offset = ($page_no - 1) * $limit;
        return " LIMIT $offset, $limit";
    }
}

if (!function_exists('xgetUrl')) {
    /**
     * xgetUrl function
     *
     * @param string $name
     * @param mixed $default
     * @param string|null $type
     * @return mixed
     */
    function xgetUrl(string $name, $default = null, string $type = '')
    {
        $val = URL::get($name, $default);

        if (!is_null($val)) {
            $val = trim(xss_clean($val));
        } //end if

        if (!$type) {
            return $val;
        } //end if

        return castDataType($val, $type);
    }
} //end if

if (!function_exists('resSuccess')) {
    /**
     * resSuccess function
     * Description: return success with data
     * @param mixed $data
     * @param string $msg
     * @return Json [
     *      'success' => true,
     *      'msg' => string,
     *      'data' => mixed
     *  ]
     */
    function resSuccess($data = null, string $msg = 'Thành công')
    {
        return json_encode([
            'success' => true,
            'msg' => $msg,
            'data' => $data
        ]);
    }
}

if (!function_exists('resError')) {
    /**
     * resError function
     * Summary: return error with data
     * @param string $msg
     * @param mixed $data
     * @return Json [
     *      'success' => false,
     *      'msg' => string,
     *      'data' => mixed
     *  ]
     */
    function resError(string $msg = 'Không thành công', $data = null)
    {
        return json_encode([
            'success' => false,
            'msg' => $msg,
            'data' => $data
        ]);
    }
}

if (!function_exists('now')) {
    /**
     * now function
     *
     * @param string $format
     * @return string
     */
    function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}

if (!function_exists('generateColumns')) {
    /**
     * generateColumns function
     *
     * @param array $cols
     * @return string
     */
    function generateColumns(array $cols = []): string
    {
        return implode(', ', $cols);
    }
}

if (!function_exists('checkRoleAddBundle')) {
    /**
     * checkRoleAddBundle
     * 
     * Kiểm tra quyền thêm bundle
     * 
     * @return boolean
     */
    function checkRoleAddBundle(): bool
    {
        if (!defined('SYSTEM_ADD_BUNDLE') || !defined('ACCOUNTS_ADD_BUNDLE')) {
            return false;
        } //end if
        $groupId = Session::get('group_id');
        return SYSTEM_ADD_BUNDLE || in_array($groupId, ACCOUNTS_ADD_BUNDLE);
    }
} //end if

if (!function_exists('allowAddAdminSource')) {
    /**
     * allowAddAdminSource function
     *
     * Kiểm tra quyền thêm nguồn đơn
     * 
     * @return boolean
     */
    function allowAddAdminSource(): bool
    {
        if (!defined('SYSTEM_ADD_ADMIN_SOURCE') || !defined('ACCOUNTS_ADD_ADMIN_SOURCE')) {
            return false;
        } //end if

        $groupId = Session::get('group_id');
        return SYSTEM_ADD_ADMIN_SOURCE || in_array($groupId, ACCOUNTS_ADD_ADMIN_SOURCE);
    } //end if
} //end if

if (!function_exists('allowAddOrderStatus')) {
    /**
     * allowAddOrderStatus function
     * 
     * Kiểm tra quyền thêm trạng thái đơn hàng
     *
     * @return boolean
     */
    function allowAddOrderStatus(): bool
    {
        if (!defined('SYSTEM_ADD_ORDER_STATUS') || !defined('ACCOUNTS_ADD_ORDER_STATUS')) {
            return false;
        } //end if

        if (SYSTEM_ADD_ORDER_STATUS) {
            return true;
        } //end if

        $group_id = Session::get('group_id');
        if (in_array($group_id, ACCOUNTS_ADD_ORDER_STATUS)) {
            return true;
        } //end if

        return false;
    } //end if
} //end if

if (!function_exists('formatQuery')) {
    /**
     * formatQuery function
     *
     * @param string $query
     * @return string
     */
    function formatQuery(string $query): string
    {
        return trim(preg_replace('!\s+!', ' ', $query));
    }
} //end if

if (!function_exists('castDataType')) {
    /**
     * castDataType function
     * 
     * Ép kiểu dữ liệu
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    function castDataType($value, string $type = 's')
    {
        switch ($type) {
            case 's':
            case 'str':
            case 'string':
                return strval($value);
                break;

            case 'i':
            case 'int':
            case 'integer':
                return intval($value);
                break;

            case 'd':
            case 'double':
                return doubleval($value);
                break;

            case 'f':
            case 'float':
                return floatval($value);
                break;

            case 'b':
            case 'bool':
            case 'boolean':
                return boolval($value);
                break;

            case 'a':
            case 'arr':
            case 'array':
                return arrayVal($value);
                break;

            default:
                return $value;
                break;
        } //end switch
    }
} //end if

if (!function_exists('isJson')) {
    /**
     * isJson function
     * 
     * Kiểm tra dữ liệu có phải json hay không
     * 
     * @param mixed $value
     * @return boolean
     */
    function isJson($value): bool
    {
        try {
            json_decode($value);
            return json_last_error() === JSON_ERROR_NONE;
        } catch (Exception $e) {
            return false;
        } //end try
    }
} //end if

if (!function_exists('arrayVal')) {
    /**
     * arrayVal function
     * 
     * Convert dữ liệu sang kiểu Array
     * 
     * @param mixed $value
     * @return array
     */
    function arrayVal($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        //end if

        if (isJson($value)) {
            return json_decode($value, true);
        } //end if

        return [$value];
    }
} //end if

if (!function_exists('validatePhoneNumber')) {
    function validatePhoneNumber(string $telephone_number): bool
    {
        foreach (MOBILE_REGEX_PATTERNS as $regex) {
            if (preg_match($regex, $telephone_number)) {
                return true;
            } //end if
        } //end foreach

        return false;
    }
} //end if


if (!function_exists('isObd')) {
    function isObd(int $groupId = 0)
    {
        if (!$groupId) {
            $groupId = Session::get('group_id');
        }//end if

        return Systems::isGroupInOBD($groupId);
    }
}//end if

if (!function_exists('isSystemGroupInOBD')) {
    function isSystemGroupInOBD(int $systemGroupID = 0)
    {
        if (!$systemGroupID) {
            return false;
        }//end if

        return Systems::isSystemGroupInOBD($systemGroupID);
    }
}//end if