<?php
class EditAdminStatusForm extends Form
{
	protected $map;
	function __construct()
	{
		Form::Form('EditAdminStatusForm');
		$this->add('statuses.name', new TextType(true, 'Chưa nhập tên', 0, 255));
	}
	function on_submit()
	{
		$allowAddOrderStatus = allowAddOrderStatus();
		if (!$allowAddOrderStatus) {
			return Url::js_redirect(true, 'Bạn không có quyền thực hiện hành động này');
		};

		if ($this->check() and URL::get('confirm_edit') and !Url::get('search')) {
			require_once 'packages/core/includes/utils/vn_code.php';
			$group_id = Session::get('group_id');
			if (isset($_REQUEST['mi_status'])) {
				foreach ($_REQUEST['mi_status'] as $key => $record) {
					$newRecord = array();
					foreach ($record as $rkey => $rvalue) {
						$newRecord[DB::escape($rkey)] = DB::escape($rvalue);
					}
					$record = $newRecord;
					if ($record['id'] == '(auto)') {
						$record['id'] = false;
					}
					unset($record['is_system']);
					unset($record['orders_total']);
					if (isset($record['no_revenue']) and $record['no_revenue']) {
						$record['no_revenue'] = 1;
					} else {
						$record['no_revenue'] = 0;
					}
					if (isset($record['not_reach']) and $record['not_reach']) {
						$record['not_reach'] = 1;
					} else {
						$record['not_reach'] = 0;
					}
					$m_key = 'statuses_' . $group_id;
					if ($record['id'] and DB::exists_id('statuses', $record['id'])) {
						DB::update('statuses', $record, 'id=' . $record['id']);
						if (!System::is_local()) {
							if ($items = MC::get_items($m_key)) {
								MC::delete_item($m_key);
							}
						}
					} else {
						unset($record['id']);
						$record['group_id'] = Session::get('group_id');
						$record['id'] = DB::insert('statuses', $record);
						if (!System::is_local()) {
							if ($items = MC::get_items($m_key)) {
								MC::delete_item($m_key);
							}
						}
					}
					/////
				}
				if (isset($ids) and sizeof($ids)) {
					$_REQUEST['selected_ids'] .= ',' . join(',', $ids);
				}
			}

			if (URL::get('deleted_ids')) {
				$ids = explode(',', URL::get('deleted_ids'));
				foreach ($ids as $id) {
					$sql = '
						select 
							statuses.id
						from 
							statuses
							INNER JOIN orders ON orders.status_id = statuses.id AND orders.group_id = ' . $group_id . '
						WHERE
						statuses.id = ' . DB::escape($id) . '
						GROUP BY
						statuses.id
						LIMIT 1
					';
					$statusCheck = DB::fetch($sql, 'id');
					if (!$statusCheck) DB::delete_id('statuses', DB::escape($id));
				}
			}
			//update_mi_upload_file();
			AdminStatusDB::update_status_custom();
			Url::js_redirect(true);
		}
	}
	function draw()
	{
		$business_model = get_group_options('business_model');
		$group_id = Session::get('group_id');
		if (Session::get('master_group_id')) {
			$group_id = Session::get('master_group_id');
		}
		$this->map = array();
		//$paging = '';
		$id_arr = '"4","5","6","7","8","9","10","96","3"';
		$cond = '
			statuses.id NOT IN (' . $id_arr . ') and 
			(statuses.group_id = ' . Session::get('group_id') . ' OR (' . (($business_model == 1) ? 'statuses.is_default=2' : 'statuses.is_system=1') . '))
				' . (Url::get('keyword') ? ' AND statuses.name LIKE "%' . Url::get('keyword') . '%"' : '') . '
			';
		$cond_df = '
		
		(statuses.group_id = ' . Session::get('group_id') . ' OR (' . (($business_model == 1) ? 'statuses.is_default=2' : 'statuses.is_system=1') . '))
			' . (Url::get('keyword') ? ' AND statuses.name LIKE "%' . Url::get('keyword') . '%"' : '') . '
		';
		$status_defaults = DB::fetch_all(
			'select 
					statuses.*
					,CONCAT(IF(statuses.is_system=1," ","<strong>"),CONCAT(statuses.name,"</strong>")) AS name
					,statuses_custom.position
					,statuses_custom.color as custom_color
					,IF(statuses.is_system=1,statuses.level,statuses_custom.level) AS level
				from 
					statuses
					left join statuses_custom on statuses_custom.status_id = statuses.id AND statuses_custom.group_id = ' . $group_id . '
				WHERE
					' . $cond_df . '
				GROUP BY
					statuses.id
				order by 
					statuses_custom.position,statuses.is_system DESC,statuses.id  DESC'
		);
		foreach ($status_defaults as $key => $val) {
			$status_defaults[$key]['custom_color'] = $val['custom_color'] ? $val['custom_color'] : $val['color'];
		}
		$this->map['status_defaults'] = $status_defaults;
		//if(!isset($_REQUEST['mi_status']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(statuses.id) as acount
				from 
					statuses
				where 
					' . $cond . '
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'], $item_per_page);
			$sql = '
				select 
					statuses.*,
					if(orders.id, 1, 0) as orders_total,
					statuses_custom.level
				from 
					statuses
					left join statuses_custom on statuses_custom.status_id = statuses.id AND statuses_custom.group_id = ' . $group_id . '
					LEFT JOIN orders ON orders.status_id = statuses.id AND orders.group_id = ' . $group_id . '
				WHERE
					' . $cond . '
				GROUP BY
					statuses.id
				order by 
					statuses.is_system DESC,statuses.id  DESC
				LIMIT
					' . ((page_no() - 1) * $item_per_page) . ',' . $item_per_page . '
			';
			$mi_status = DB::fetch_all($sql);
			$_REQUEST['mi_status'] = $mi_status;
		}
		$this->map['allowAddOrderStatus'] = allowAddOrderStatus();
		$this->map['paging'] = $paging;
		$this->parse_layout('edit', $this->map);
	}
}
