<?php
class LoginHistoryDB
{
	static function get_total_item($cond)
	{
		return DB::fetch('
			select
				count(account_log.id) as account
			from
				account_log
				left outer join  `groups` on groups.id=account_log.group_id
			where
				'.$cond.'
		','account');
	}
	static function get_items($cond = '',$item_per_page=20)
	{
		return DB::fetch_all('
			select
				account_log.*,groups.name as group_name,account_log.id as _id
			from
				account_log
				left outer join  `groups` on groups.id=account_log.group_id
			where
				'.$cond.'
			ORDER BY
				account_log.id desc
			limit
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
    static function getGroupIdByName($cond)
    {
        $groups =  DB::fetch_all('
			select
				groups.id,groups.name
			from
				groups
			where
				'.$cond.'
		');
        $array = [];
        foreach ($groups as $key => $value) {
            $array[] = $key;
        }
        return $array;
    }
}
?>