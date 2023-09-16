<?php
class ListLoginHistoryForm extends Form
{
	function __construct()
	{
		Form::Form('ListLoginHistoryForm');
		//$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		/*if(Url::get('cmd') == 'delete' and Url::get('selected_ids') and count(Url::get('selected_ids'))>0)
		{
			foreach(Url::get('selected_ids') as $key=>$value)
			{
				DB::delete_id('LoginHistory',$value);
			}
		}*/
        Url::redirect_current(['ip','group_id','account_id','client_id']);
	}
    function draw()
    {
        require_once ROOT_PATH.'packages/vissale/lib/php/log.php';
        $conditions = array();
        $group_id = Session::get('group_id');
        $cond = 'account_log.`log_type`=2';
        $conditions['log_type'] = 2;
        if(!User::is_admin()){
            $cond .= ' and groups.id='.$group_id;
            $conditions['group_id'] = (int)DB::escape($group_id);
        }
        if($account_id = trim(Url::get('account_id'))  ){
            $cond .= ' and account_log.account_id = "'.DB::escape($account_id).'"';
            $conditions['account_id'] = DB::escape($account_id);
        }
        if($ip = trim(Url::get('ip')) ){
            $cond .= ' and account_log.ip = "'.DB::escape($ip).'"';
            $conditions['ip'] = DB::escape($ip);
        }
        if($client_id = trim(Url::get('client_id')) ){
            $cond .= ' and account_log.client_id = "'.DB::escape($client_id).'"';
            $conditions['client_id'] = DB::escape($client_id);
        }
        if($group_id = trim(Url::get('group_id')) ){
            $cond .= ' and groups.name LIKE "%'.DB::escape($group_id).'%"';
            $condGroup = ' groups.name LIKE "%'.DB::escape($group_id).'%"';
            $groups = LoginHistoryDB::getGroupIdByName($condGroup);
            $conditions['group_id'] = $groups;
        }
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'total' => true
                )
            );
            $total = (count($conditions)>1) ? getAccountLog($payload) : 20000000;
        } else {
            $total = LoginHistoryDB::get_total_item($cond);
        }

        require_once 'packages/core/includes/utils/paging.php';
        $item_per_page = 50;
        $paging = paging($total,$item_per_page,10,false,'page_no',['ip','group_id','account_id','client_id']);
        $itemsFormat = [];
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $page_no = (Url::get('page_no') and Url::get('page_no')>0) ? intval(Url::get('page_no')) : 1;
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'sorts' => array(
                        '_id' => 'DESC'
                    ),
                    'limit' => $item_per_page,
                    'page' => $page_no
                )
            );
            $items = getAccountLog($payload);
            $ids = [];
            $image = [];
            foreach ($items as $key => $value) {
                $ids[] = $value['_id'];
                $itemsFormat[$value['_id']] = $value;
                if(!empty($value['image_url'])){
                    $image[$value['_id']][] = $value['image_url'];
                }
                
            }
            $photos['account_log_id'] = $ids;
            $payloadPhoto = array(
                'conditions' => $photos,
                'options' => array(
                    'sorts' => array(
                        '_id' => 'DESC'
                    ),
                    'limit' => $item_per_page,
                    'page' => $page_no
                )
            );
            $result = getAccountPhotoLog($payloadPhoto);
            if($result){
                foreach ($result as $val) {
                    $image[$val['account_log_id']][] = $val['image_url'];
                    // array_push($image[$val['account_log_id']], $val['image_url']);
                } 
            }
            
            foreach ($itemsFormat as $k => $v) {
                foreach ($image as $key => $value) {
                    if($key == $k){
                        $imageUrl = implode(',', $image[$k]);
                        $itemsFormat[$k]['image_url'] = $imageUrl;
                    }
                }
            }
            $items = $itemsFormat;
        } else {
            $items = LoginHistoryDB::get_items($cond, $item_per_page);
            $ids = [];
            foreach ($items as $key => $value) {
                $ids[] = $value['_id'];
            }
            $strIds = implode(',', $ids);
            $sql = "SELECT * FROM login_user_photos WHERE account_log_id IN ($strIds)";
            $result = DB::fetch_all($sql);
            
            $image = [];
            foreach ($items as $key => $value) {
                foreach ($result as $val) {
                    if($val['account_log_id'] == $value['_id']){
                        $image[$value['_id']][] = $val['image_url'];
                    }
                } 
            }
            foreach ($items as $k => $v) {
                $itemsFormat[$k] = $v;
                if($image[$k]){
                    $imageUrl = implode(',', $image[$k]);
                }
                $itemsFormat[$k]['image_url'] = $imageUrl;
            }
            $items = $itemsFormat;
        }
        $this->parse_layout('list',array(
            'paging'=>$paging
            ,'items'=>$items
            ,'total'=>$total
        ));
    }
}
?>
