<?php
class EditAdminSourceForm extends Form
{
	protected $isObd;
	function __construct()
	{
		Form::Form('EditAdminSourceForm');
		$this->isObd = isObd();
	}
	function on_submit()
	{
		if (URL::get('confirm_edit') and !Url::get('search')) {
			require_once 'packages/core/includes/utils/vn_code.php';

			$miOrderSource = $_REQUEST['mi_order_source'] ?? [];
			if ($miOrderSource) {
				$this->updateOrCreateSource($miOrderSource);
			} //end if


			if (allowAddAdminSource()) {
				self::deleteByIds();
			}

			Url::js_redirect(true);
		} //end if
	}

	/**
	 * updateOrCreateSource function
	 *
	 * @return void
	 */
	private function updateOrCreateSource(array $miOrderSource = [])
	{
		$allowAddAdminSource = allowAddAdminSource();
		foreach ($miOrderSource as $data) {
			$record = self::mapRecord($data);
			unset($record['name2']);
			if (!$record['name'] && $allowAddAdminSource) {
				return Url::js_redirect(true, 'Vui lòng nhập tên nguồn marketing');
			} //end if

			$record['default_select'] = !isset($record['default_select']) ? 0 : 1;
			$record['created_time'] = date('Y-m-d H:i:s');
			$record['created_acc_id'] = 1;

			$record['ref_id'] = intval($record['ref_id'] ?? 0);
			if ($record['id'] == '(auto)') {
				$record['id'] = false;
			} //end if

			if ($record['id']) {
				$this->updateRecord($record);
				continue;
			} //end if

			if ($allowAddAdminSource) {
				$this->insertRecord($record);
			} //end if
		} //end foreach
	}

	/**
	 * deleteByIds function
	 *
	 * @return void
	 */
	private static function deleteByIds()
	{
		return;
		if (URL::get('deleted_ids')) {
			$ids = parse_id(URL::get('deleted_ids'));
			foreach ($ids as $id) {
				$condition = "id = " . DB::escape($id) . " AND group_id IN (0," . Session::get('group_id') . ')';
				DB::delete('order_source', $condition);
			} //end foreach
		} //end if
	}

	/**
	 * updateRecord function
	 *
	 * @param array $record
	 * @return mixed
	 */
	private function updateRecord(array $record)
	{
		$groupId = Session::get('group_id');
		$recordId = $record['id'];
		$query = "SELECT `id`, `name`, `name2`, `ref_id` FROM order_source WHERE id = $recordId AND group_id = $groupId";
		$source = DB::fetch($query);
		if (!$source) {
			return null;
		} //end if
		$newRefId = $record['ref_id'];
		$currentRefId = $source['ref_id'];
		if ($newRefId != $currentRefId) {
			if ($newRefId) {
				$systemSource = self::findSystemSource($newRefId);
				if (!$systemSource) {
					unset($record['ref_id']);
				} else {
					if ($this->isObd) {
						$includeIds = $systemSource['include_ids'];
						$includeIds = DataFilter::removeEmptyValueListIds($includeIds);
						if (!in_array($recordId, $includeIds)) {
							$includeIds[] = $recordId;
						} //end if

						$_includeIds = DB::escapeArray($includeIds);
						$includeIds = implode(',', $_includeIds);
						DB::update('order_source', ['include_ids' => $includeIds], "id = $newRefId");
					}

					$record['name'] = $systemSource['name'];
					if (!$source['name2']) {
						$record['name2'] = $source['name'];
					}
				} //end if
			} //end if

			if ($this->isObd) {
				if ($currentRefId) {
					$systemSource = self::findSystemSource($currentRefId);
					if ($systemSource) {
						$includeIds = $systemSource['include_ids'];
						$includeIds = DataFilter::removeEmptyValueListIds($includeIds);
						if (in_array($recordId, $includeIds)) {
							foreach (array_keys($includeIds, $recordId) as $key) {
								unset($includeIds[$key]);
							} //end foreach
						} //end if

						$_includeIds = DB::escapeArray($includeIds);
						$includeIds = implode(',', $_includeIds);
						DB::update('order_source', ['include_ids' => $includeIds], "id = $currentRefId");
					} //end if
				}
			}
		} //end if

		unset($record['id']);
		if (!allowAddAdminSource()) {
			unset($record['default_select']);
			unset($record['created_time']);
			unset($record['created_acc_id']);
			unset($record['group_id']);
			unset($record['include_ids']);
			unset($record['is_active']);
		}

		DB::update('order_source', $record, "id = $recordId");
	}

	/**
	 * Tìm nguồn hệ thống
	 *
	 * @param integer $id
	 * @return mixed
	 */
	private static function findSystemSource(int $id)
	{
		$query = "SELECT `id`, `name`, `include_ids`
			FROM order_source
			WHERE id = $id
			AND `ref_id` = 0
			AND `group_id` = 0";

		return DB::fetch($query);
	}

	/**
	 * insertRecord function
	 *
	 * @param array $record
	 * @return void
	 */
	private function insertRecord(array $record)
	{
		unset($record['id']);
		$record['group_id'] = Session::get('group_id');
		$record['id'] = DB::insert('order_source', $record);
	}

	/**
	 * mapRecord function
	 *
	 * @param array $data
	 * @return array
	 */
	private static function mapRecord(array $data): array
	{
		$record = [];
		foreach ($data as $key => $val) {
			$record[DB::escape($key)] = trim(DB::escape(DataFilter::removeXSSinHtml($val)));
		} //end foreach

		return $record;
	}

	function draw()
	{
		$this->map = array();
		$cond = 'group_id=' . Session::get('group_id') . ' or group_id=0';
		$item_per_page = 200;
		$count = self::countMiOrderSource($cond);
		$total = $count['acount'] ?? 0;
		$this->map['total'] = $total;

		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($total, $item_per_page);
		$_REQUEST['mi_order_source'] = self::getMiOrderSource($item_per_page, $cond);
		$this->map['allowAddAdminSource'] = allowAddAdminSource();
		$systemSourceOptions = '<option value="">Chọn</option>';
		foreach (self::getSystemSources() as $key => $val) {
			$systemSourceOptions .= '<option value="' . $key . '">' . $val['name'] . '</option>';
		} //end if
		$this->map['systemSources'] = $systemSourceOptions;
		$this->map['paging'] = $paging;
		$this->parse_layout('edit', $this->map);
	}

	/**
	 * getMiOrderSource function
	 *
	 * @param integer $item_per_page
	 * @param string $cond
	 * @return array
	 */
	private static function getMiOrderSource(int $item_per_page, string $cond): array
	{
		$limit = (page_no() - 1) * $item_per_page;
		$sql = "
			SELECT *
			FROM order_source
			WHERE $cond
			ORDER BY id DESC
			LIMIT $limit, $item_per_page
		";

		return DB::fetch_all($sql);
	}

	/**
	 * countMiOrderSource function
	 *
	 * @param integer $item_per_page
	 * @param string $cond
	 * @return array
	 */
	private static function countMiOrderSource(string $cond): array
	{
		$sql = "
			SELECT count(distinct order_source.id) as acount
			FROM order_source
			WHERE $cond
		";

		DB::query($sql);
		return DB::fetch($sql);
	}

	/**
	 * Lấy danh sách nguồn marketing chuẩn hóa
	 *
	 * @return array
	 */
	private static function getSystemSources(): array
	{
		$sql = "SELECT `id`, `name`, `default_select`
            FROM `order_source`
            WHERE `ref_id` = 0
                AND `group_id`=0
            ORDER BY id";

		$result = DB::fetch_all($sql);
		return $result;
	}
}
