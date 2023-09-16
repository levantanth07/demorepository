<?php
class EditAdminBundlesForm extends Form
{
	function __construct()
	{
		Form::Form('EditAdminBundlesForm');
		$this->add('bundles.name', new TextType(true, 'Bạn vui lòng nhập tên phân loại sản phẩm (Tối đa 50 ký tự)', 0, 50));
		$this->link_js('packages/core/includes/js/multi_items.js');
	}
	function on_submit()
	{
		$this->submitRefIds();
		return Url::js_redirect(true, 'Thành công');
		// if ($this->check() and Url::post('save') and !Url::get('keyword')) {
		// 	if (isset($_REQUEST['mi_bundle'])) {
		// 		foreach ($_REQUEST['mi_bundle'] as $key => $record) {
		// 			if (isset($record) and !$record['name']) {
		// 				$this->error('bundles.name', 'Bạn vui lòng nhập tên');
		// 				return;
		// 			}
		// 			$record['group_id'] = Session::get('group_id');
		// 			if ($record['id'] == '(auto)') {
		// 				$record['id'] = false;
		// 			}
		// 			if ($record['id'] and DB::exists_id('bundles', $record['id'])) {
		// 				DB::update('bundles', $record, 'id=' . $record['id']);
		// 			} else {
		// 				unset($record['id']);
		// 				$record['id'] = DB::insert('bundles', $record);
		// 			}
		// 			/////
		// 		}
		// 		if (isset($ids) and sizeof($ids)) {
		// 			$_REQUEST['selected_ids'] .= ',' . join(',', $ids);
		// 		}
		// 	}
		// 	if ($deleted_ids = URL::get('deleted_ids')) {
		// 		$ids = explode(',', $deleted_ids);
		// 		foreach ($ids as $id) {
		// 			DB::delete_id('bundles', $id);
		// 		}
		// 	}

		// 	Url::js_redirect(true);
		// }
	}

	private function submitRefIds()
	{
		if (!$this->check() || !isset($_REQUEST['mi_bundle'])) {
			Url::js_redirect(true);
		}//end if

		$isObd = isObd();
		$groupId = Session::get('group_id');
		foreach ($_REQUEST['mi_bundle'] as $key => $record) {
			$recordId = intval($record['id'] ?? 0);
			$newRefId = intval($record['ref_id'] ?? 0);
			if (!$recordId) {
				continue;
			}//end if

			$bundle = DB::exists_id('bundles', $recordId);
			if (!$bundle || $bundle['group_id'] == 0 || $bundle['standardized'] == 1) {
				continue;
			}//end if
			$sql = "SELECT id, name, include_ids FROM bundles WHERE id = $newRefId AND group_id = 0 AND ref_id = 0 and standardized = 1";
			$systemBundle = DB::fetch($sql);
			if (!$systemBundle) {
				continue;
			}//end if
				
			$currentRefId = $bundle['ref_id'];
			if ($newRefId == $currentRefId) {
				continue;
			}//end if

			if ($isObd) {
				$this->handleGroupInOBD($newRefId, $currentRefId, $bundle, $systemBundle);
			}//end if

			$data = [
				'ref_id' => $newRefId,
				'name' => $systemBundle['name'],
			];

			DB::update('bundles', $data, "id = $recordId AND group_id = $groupId");
		}//end foreach

		Url::js_redirect(true);
	}

	private function handleGroupInOBD(
		int $newRefId, 
		int $currentRefId, 
		array $bundle,
		array $systemBundle
	) {
		if ($newRefId == $currentRefId) {
			return;
		}//end if

		$bundleId = $bundle['id'];
		if ($newRefId) {
			$systemBundle = self::findSystemBundle($newRefId);
			if (!$systemBundle) {
				return;
			} //end if

			$includeIds = $systemBundle['include_ids'];
			$_includeIds = DataFilter::removeEmptyValueListIds($includeIds);
			if (!in_array($bundleId, $_includeIds)) {
				$_includeIds[] = $bundleId;
			} //end if

			$_includeIds = DB::escapeArray($_includeIds);
			$includeIds = implode(',', $_includeIds);
			DB::update('bundles', ['include_ids' => $includeIds], "id = $newRefId");
		} //end if

		if ($currentRefId) {
			$systemSource = self::findSystemBundle($currentRefId);
			if (!$systemSource) {
				return;       
			} //end if

			$includeIds = $systemSource['include_ids'];
			$_includeIds = DataFilter::removeEmptyValueListIds($includeIds);
			if (in_array($bundleId, $_includeIds)) {
				foreach (array_keys($_includeIds, $bundleId, true) as $key) {
					unset($_includeIds[$key]);
				} //end foreach
			} //end if

			$_includeIds = DB::escapeArray($_includeIds);
			$includeIds = implode(',', $_includeIds);
			DB::update('bundles', ['include_ids' => $includeIds], "id = $currentRefId");
		}//end if
	}

	/**
	 * buildBundleCond function
	 *
	 * @return string
	 */
	private static function buildBundleCond(): string
	{
		$groupId = Session::get('group_id');
		if (isObd()) {
			$cond = " WHERE (group_id = $groupId OR (group_id = 0 AND standardized = 1 AND ref_id = 0))";
			if ($keyword = DB::escape(Url::get('keyword'))) {
				$cond .= " AND `name` LIKE '%$keyword%'";
			} //end if
		} else {
			$cond = ' WHERE bundles.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND bundles.name LIKE "%'.DB::escape(Url::get('keyword')).'%"':'').'
			';
		}

		return $cond;
	}

	/**
	 * countBundles function
	 *
	 * @param string $cond
	 * @return integer
	 */
	private static function countBundles(String $cond = ''): int
	{
		$sql = "SELECT count(id) as acount
			FROM bundles
			$cond";

		$sql = formatQuery($sql);
		return intval(DB::fetch($sql)['acount'] ?? 0);
	}

	/**
	 * getBundles function
	 *
	 * @param string $cond
	 * @param integer $limit
	 * @return array
	 */
	private static function getBundles(String $cond = '', int $limit = 200): array
	{
		$querylimit = ((page_no() - 1) * $limit) . ', ' . $limit;
		$sql = "SELECT *
			FROM bundles
			$cond
			ORDER BY id  DESC
			LIMIT $querylimit";
		$sql = formatQuery($sql);
		return DB::fetch_all($sql);
	}

	function draw()
	{
		require_once 'packages/core/includes/utils/paging.php';
		$this->map = array();
		$paging = '';
		$limit = 200;
		$cond = self::buildBundleCond();
		$count = self::countBundles($cond);
		$paging = paging($count, $limit);
		$_REQUEST['mi_bundle'] = self::getBundles($cond, $limit);

		$bundle_options = '<option value="">Chọn</option>';
		foreach (self::getSystemBundles() as $key => $val) {
			$bundle_options .= '<option value="' . $key . '">' . $val['name'] . '</option>';
		} //end if

		$this->map['bundle_options'] = $bundle_options;
		$this->map['ref_id_list'] = self::getSystemBundles();
		$this->map['total'] = $count;
		$this->map['paging'] = $paging;
		$this->parse_layout('edit', $this->map);
	}

	/**
	 * Lấy danh sách nhóm sản phẩm chuẩn hóa
	 *
	 * @return array
	 */
	public static function getSystemBundles(): array
	{
		$sql = "SELECT `id`, `name`
		   FROM `bundles`
		   WHERE `group_id` = 0
			   AND `standardized` = 1 
			   AND `ref_id` = 0 
		   ORDER BY `name`";

		$result = DB::fetch_all($sql);
		return $result;
	}

	/**
	 * Tìm nhóm hệ thống
	 *
	 * @param integer $id
	 * @return mixed
	 */
	private static function findSystemBundle(int $id)
	{
		$query = "SELECT `id`, `name`, `include_ids`
			FROM bundles
			WHERE id = $id
			AND `ref_id` = 0
			AND `group_id` = 0";

		return DB::fetch($query);
	}
}
