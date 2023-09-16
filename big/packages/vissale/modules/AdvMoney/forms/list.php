<?php

use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

class ListAdvMoneyForm extends Form
{
	protected $isObd;
	function __construct()
	{
		Form::Form('ListUserAdminForm');
		$this->isObd = isObd();
	}

	function on_submit()
	{
		if (URL::get('confirm')) {
			foreach (URL::get('selected_ids') as $id) {
				$id = DB::escape($id);
				DB::delete('party', 'user_id = "' . $id . '"');
				DB::delete('account_privilege', 'account_id = "' . $id . '"');
				DB::delete('users', 'username = "' . $id . '"');
				DB::delete('account_privilege', 'account_id = "' . $id . '"');
				DB::delete('groups', 'code = "' . $id . '"');
				DB::delete_id('account', $id);
			}
			require_once 'packages/core/includes/system/update_privilege.php';
			make_privilege_cache();
			Url::redirect_current();
		}
	}

	function draw()
	{
		//error_reporting(0);
		$this->map = array();
		///////////////////////

		$month = array();
		for ($i = 1; $i < 12; $i++) {
			$month[$i] = 'Tháng ' . $i;
		}
		$this->map['month_list'] = array('' => 'Chọn tháng') + $month;
		$_REQUEST['month'] = Url::get('month') ? Url::get('month') : date('m-Y');
		$dateft = date_create_from_format('m-Y', $_REQUEST['month']);
		$date_from =  date_format($dateft, 'Y-m-01');
		$date_to = date("Y-m-t", strtotime($date_from));

		// gioi han thai gian them-sua tu 0-10h
		$this->map['allow_change'] = (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2 && (int)date("H") < CPQC_HOUR) ? 1 : 0;
		$this->map['date_allow_change'] = date('Y-m-d', strtotime('-1 days'));
		if (!defined('CPQC_FOLLOW') || CPQC_FOLLOW != 2) {
			$this->map['allow_change'] = 1;
		}
		//

		$selected_ids = "";
		if (URL::get('selected_ids')) {
			$selected_ids = URL::get('selected_ids');
			foreach ($selected_ids as $key => $selected_id) {
				$selected_ids[$key] = '"' . $selected_id . '"';
			}
		}
		if ($this->isObd) {
			$cond = 'account.group_id=' . Session::get('group_id') . ' and vs_adv_money.group_id=' . Session::get('group_id') . '
					 ' . (!Session::get('admin_group') ? ' and account_id="' . Session::get('user_id') . '"' : '') . '
					and vs_adv_money.date>="' . $date_from . '" and vs_adv_money.date<="' . $date_to . '"'
				. (Url::get('user_id') ? ' and account.id like CONVERT( _utf8 "%' . addslashes(Url::get('user_id')) . '%" USING latin1)' : '')
				. (Url::get('master_group_id') ? ' and groups_master.id = ' . Url::iget('master_group_id') . '' : '
	
			');
	
			$cond .= AdvMoneyDB::buildSourceCond();
			$cond .= AdvMoneyDB::buildBundleCond();
		} else {
			$cond = 'account.group_id='.Session::get('group_id').' and vs_adv_money.group_id='.Session::get('group_id').'
				'.(!Session::get('admin_group')?' and account_id="'.Session::get('user_id').'"':'').'
			and vs_adv_money.date>="'.$date_from.'" and vs_adv_money.date<="'.$date_to.'"'
				.(Url::get('user_id')?' and account.id like CONVERT( _utf8 "%'.addslashes(Url::get('user_id')).'%" USING latin1)':'')
				.(Url::get('source_id')? ' and vs_adv_money.source_id = '.DB::escape(Url::get('source_id')):'')
				.(Url::get('bundle_id')? ' and vs_adv_money.bundle_id = '.DB::escape(Url::get('bundle_id')):'')
				.(Url::get('master_group_id')?' and groups_master.id = '.Url::iget('master_group_id').'':'
			');
		}

		$item_per_page = 100;
		$_sql = 'SELECT count(*) as acount
			FROM `account`
				INNER JOIN  `party` ON `party`.user_id=`account`.id
				INNER JOIN  `vs_adv_money` ON `vs_adv_money`.account_id=`account`.`id`
				INNER JOIN  `groups` ON `groups`.id=`account`.`group_id`
			WHERE ' . $cond . '
			' . (URL::get('order_by') ? 'order by ' . DB::escape(URL::get('order_by')) . (URL::get('order_dir') ? ' ' . DB::escape(URL::get('order_dir')) : '') : '') . '
		';

		$count = DB::fetch($_sql);
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'], $item_per_page);
		$items = AdvMoneyDB::get_items($cond, $item_per_page, $date_from, $date_to);
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')) {
			if (is_string(UrL::get('selected_ids'))) {
				if (strstr(UrL::get('selected_ids'), ',')) {
					$just_edited_id['just_edited_ids'] = explode(',', UrL::get('selected_ids'));
				} else {
					$just_edited_id['just_edited_ids'] = array('0' => UrL::get('selected_ids'));
				}
			}
		}
		$data = AdvMoney::$data_time;
		$this->map['time_slot'] = $data['timeSlot'];
		$this->map['source_options'] = '<option value="">Nguồn</option>';
		$this->map['bundle_options'] = '<option value="">Phân loại SP</option>';
		if ($this->isObd) {
			$sources = AdvMoneyDB::getSystemSources();
			$bundles = AdvMoneyDB::getSystemBundles();
		} else {
			$sources = AdvMoneyDB::get_source();
			$bundles = AdvMoneyDB::get_bundle();
		}//end if

		foreach ($sources as $key => $value) {
			if (Url::get('source_id') && Url::get('source_id') == $value['id']) {
				$this->map['source_options'] .= '<option value="' . $value['id'] . '" selected>' . $value['name'] . '</option>';
			} else {
				$this->map['source_options'] .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
			}
		}
		foreach ($bundles as $key => $value) {
			if (Url::get('bundle_id') && Url::get('bundle_id') == $value['id']) {
				$this->map['bundle_options'] .= '<option value="' . $value['id'] . '" selected>' . $value['name'] . '</option>';
			} else {
				$this->map['bundle_options'] .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
			}
		}

		$this->map['logs'] = $this->getLogs(array_keys($items));
		$this->map['users'] = AdvMoneyDB::getUserByIDs($this->getLogUserIDs());

		$this->parse_layout(
			'list',
			$just_edited_id + $this->map +
				array(
					'total' => $count['acount'],
					'items' => $items,
					'paging' => $paging,
					'format' => $data['format']
				)
		);
	}



	/**
	 * Gets the logs.
	 *
	 * @param      array   $IDs    I ds
	 *
	 * @return     <type>  The logs.
	 */
	private function getLogs(array $IDs)
	{
		return getManyLog(implode(',', $IDs), AdvMoney::LOG_TABLE, 1);
	}

	/**
	 * Gets the log user i ds.
	 *
	 * @return     <type>  The log user i ds.
	 */
	private function getLogUserIDs()
	{
		if ($this->map['logs']) {
			return array_reduce($this->map['logs'], function ($userIDs, $logs) {
				array_push(
					$userIDs,
					...array_map(function ($log) {
						return $log['data']['by'] ?? 0;
					}, $logs)
				);

				return $userIDs;
			}, []) ?: [];
		} else {
			return [];
		}
	}
}
