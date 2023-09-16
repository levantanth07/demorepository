<?php
class ListAccountLogForm extends Form
{
	function __construct()
	{
		Form::Form('ListAccountLogForm');
		//$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		/*if(Url::get('cmd') == 'delete' and Url::get('selected_ids') and count(Url::get('selected_ids'))>0)
		{
			foreach(Url::get('selected_ids') as $key=>$value)
			{
				DB::delete_id('AccountLog',$value);
			}
		}*/
		Url::redirect_current(['keyword']);
	}
    function draw()
    {
        require_once ROOT_PATH.'packages/vissale/lib/php/log.php';
        $conditions = array();
        $group_id = Url::get('group_id')?Url::get('group_id'):Session::get('group_id');
        $cond = 'account_log.`log_type` IN (0, 1)';
        $conditions['log_type'] = array(0, 1);
        if(!User::is_admin()){
            $cond .= ' and groups.id='.DB::escape($group_id);
            $conditions['group_id'] = DB::escape($group_id);
        }else{
            if (Url::get('group_id')) {
                $cond .= ' and groups.id='.DB::escape(Url::get('group_id'));
                $conditions['group_id'] = DB::escape($group_id);
            }
        }
        if($keyword = Url::get('keyword')){
            $cond .= ' AND (account_log.account_id LIKE "%'.DB::escape($keyword).'%" or account_log.content LIKE "%'.DB::escape($keyword).'%" or account_log.ip LIKE "%'.DB::escape($keyword).'%")';
            $conditions['keyword'] = DB::escape($keyword);
        }

        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $payload = array(
                'conditions' => $conditions,
                'options' => array(
                    'total' => true
                )
            );
            $total = getAccountLog($payload);
        } else {
            $total = AccountLogDB::get_total_item($cond);
        }

        require_once 'packages/core/includes/utils/paging.php';
        $item_per_page = 50;
        $paging = paging($total,$item_per_page,10,false,'page_no',['keyword']);
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
        } else {
            $items = AccountLogDB::get_items($cond,$item_per_page);
        }

        $this->parse_layout('list',array(
            'paging'=>$paging
        ,'items'=>$items
        ,'total'=>$total
        ));
    }
}
?>