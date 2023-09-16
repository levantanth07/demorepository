<?php
require_once ROOT_PATH.'packages/user/modules/UserAdmin/db.php';
require_once ROOT_PATH.'packages/user/modules/UserAdmin/class.php';
require_once 'packages/core/includes/utils/paging.php';
require_once ROOT_PATH.'packages/vissale/lib/php/log.php';
class ListAjaxHistoryForm extends Form
{
    protected $map;

    function __construct()
    {
        Form::Form('ListAjaxHistoryForm');
        $this->link_css('assets/default/css/cms.css');
    }
    function on_submit()
    {
        
    }
    function draw()
    {
       
        $date = new DateTime(date("Y-m-d"));
        $date->modify('-7 day');
        $tomorrowDATE = $date->format('Y-m-d');
        $conditions = array();
        if ((isset($_REQUEST['start_date']) &&  $_REQUEST['start_date'] != '') && (isset($_REQUEST['end_date']) && $_REQUEST['end_date'] != '')) {
            $start_date = $_REQUEST['start_date'] . ' 00:00:00';
            $end_date = $_REQUEST['end_date'] . ' 23:59:59';
            $log_start_date = (int)date("Ymd", strtotime($_REQUEST['start_date']));
            $log_end_date = (int)date("Ymd", strtotime($_REQUEST['end_date']));
        } else {
            $start_date = $tomorrowDATE.' 00:00:00 ';
            $end_date = date('Y-m-d').' 23:59:59';
            $log_start_date = (int)date("Ymd");
            $log_end_date = (int)date("Ymd");
        }

        $conditions['date_range'] = [$log_start_date,$log_end_date];

        $message = '';
        
        $actionType  = intval($_REQUEST['option_action']);
        $actionUser  = $_REQUEST['option_user'];
        $condMysql = ' created_at>="'.$start_date.'" 
                        AND created_at<="'.$end_date.'" ';
        $where_search = array(
            '$and' => array(
                array(
                    "created_at" => array('$gte' =>$start_date, '$lte' => $end_date),
                ),
            )
        );
        if ($_REQUEST['option_action'] != 0) {
            $where_search['$and'][0]['action_type'] = $actionType;
            $conditions['action_type'] = $actionType;
            $condMysql.= ' AND action_type ='.$actionType.'';
        }
        if ($_REQUEST['option_user'] != 0) {
            $where_search['$and'][0]['user_id'] = $actionUser;
            $conditions['user_id'] = $actionUser;
            $condMysql.= ' AND user_id ='.$actionUser.'';
        }
        $item_per_page = Url::get('item_per_page') ? Url::get('item_per_page') : 15;
        $item_per_page = intval($item_per_page);
        $skip = (page_no() - 1) * $item_per_page;
        $limit = $item_per_page;

        $options = [
            'limit' => $limit,
            'skip' => $skip,
            'sort' => ['created_at' => -1]
        ];
        // product
        $page_no = (Url::get('page_no') and Url::get('page_no')>0) ? intval(Url::get('page_no')) : 1;
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $payloadTotal = array(
                'conditions' => $conditions,
                'options' => array(
                    'total' => true
                )
            );
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'sorts' => array(
                        '_id' => 'DESC'
                    ),
                    'limit' => $limit,
                    'page' => $page_no
                )
            );
            $i=0;
            $total = getSecurityLog($payloadTotal);
            $items = getSecurityLog($payload);
            foreach ($items as $key => $value) {
                $items[$key]['i']=$i++;
                if($item_per_page){
                    $index = $i + $item_per_page*(page_no()-1);
                }else{
                    $index = $i;
                }
                $items[$key]['index'] = $index;
            }
            $this->map['items'] =  $items;
        } else {
            // $mongoDB = MONGO_DB_NAME;
            // $collection = (new MongoDB\Client())->{$mongoDB}->security_log;
            // $items = $collection->find($where_search,$options);
            // $total = $collection->countDocuments($where_search);
            $items = $this->getSecurityLogMysql($condMysql,$options);
            $total = $this->getTotalSecurityLogMysql($condMysql);
            $total = $total['total'];
            $i=0;
            $newItems = [];
            foreach ($items as $k=>$v)
            {
               $newItems[$k] = [
                  'created_at' => strtotime($v['created_at']),
                  'username' => $v['username'],
                  'action' => $v['action'],
                  'content' => $v['content'],
                  'ip_address'  =>$v['ip_address'],
                  'user_agent'  =>$v['user_agent'],
                  'device_type' =>$v['device_type'],
               ];
            }

            $itemFomart = array_column($newItems, 'created_at');
            array_multisort($itemFomart, SORT_DESC, $newItems);
            $itemAll = [];
            foreach ($itemFomart as $key=>$value)
            {
                foreach ($newItems as $keyNew => $valNew) {
                   if($value == $valNew['created_at']){
                        $itemAll[$key] = $valNew;
                   }
                }
            }
            foreach ($itemAll as $keyAll => $valueAll) {
                $itemAll[$keyAll]['i']=$i++;
                if($item_per_page){
                    $index = $i + $item_per_page*(page_no()-1);
                }else{
                    $index = $i;
                }
                $itemAll[$keyAll]['index'] = $index;
                $itemAll[$keyAll]['created_at'] = date('Y-m-d H:i:s',$valueAll['created_at']);
            }
            $this->map['items'] =  $itemAll;
        }
        $paginate = $this->getPaginate($total);
        $this->map['paging'] =  $paginate;
        $this->map['total'] =  (int)$total;
        $this->map['page_no'] = page_no();
        $this->map['message'] =  $message;
        $this->map['item_per_page'] = $item_per_page;
        $this->parse_layout('ajax_list_history',$this->map);
    }
    function getPaginate($total){
        $item_per_page = Url::get('item_per_page') ?  Url::get('item_per_page') : 15;
        $paging_array = array(
            'item_per_page'=>Url::get('item_per_page') ? Url::get('item_per_page') : 15,
            'start_date'=>Url::get('start_date'),
            'end_date'=>Url::get('end_date'),
            'option_user'=>Url::get('option_user'),
            'option_action'=>Url::get('option_action'),
        );
        $paging = order_page_ajax($total,$item_per_page,$paging_array,7,'page_no','');
        return $paging;
    }

    function getSecurityLogMysql($condMysql,$options)
    {
        $sql = 'SELECT * FROM security_log WHERE '.$condMysql.' ORDER BY id DESC LIMIT '.$options['skip'].','.$options['limit'].'';
        return DB::fetch_all($sql);
    }
    function getTotalSecurityLogMysql($condMysql)
    {
        $sql = 'SELECT count(*) as total FROM security_log WHERE '.$condMysql.'';
        return DB::fetch($sql);
    }
}
?>
