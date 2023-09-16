<?php

class EditLvNAdminSystemBundlesForm extends Form
{
	const ITEMS_PER_PAGE = 10;
	const VALID_ACTIONS = [
		REQ_ACTION_CREATE,
		REQ_ACTION_EDIT,
		REQ_ACTION_DELETE,
	];

	function __construct()
	{
		Form::Form('EditLvNAdminSystemBundlesForm');
		if (xgetUrl('action')) {
			echo self::onAjax();
			die;
		} //end if
	}

	function on_submit()
	{
	}

	function draw()
	{
		require_once 'packages/core/includes/utils/paging.php';
	
		$lv = xgetUrl('lv', 2, 'i');
		$total =  AdminSystemBundlesDB::countBundles($lv)['total'];

		$this->map = array();
		$this->map['title'] = "Quản lý nhóm sản phẩm cấp $lv";
		$this->map['lv'] = $lv;
		$this->map['keysearch'] = xgetUrl('keysearch', null);
		$this->map['filter_status'] = xgetUrl('filter_status', -1, 'i');
		$this->map['filter_parent_id'] = xgetUrl('filter_parent_id', null);
		$this->map['total'] = $total;
		$this->map['bundles'] = AdminSystemBundlesDB::getBundles($lv, self::ITEMS_PER_PAGE);
		$this->map['parent_bundles'] = AdminSystemBundlesDB::getAllL1Bundles();
		$statuses = [-1 => '--- Tất cả ---'] + AdminSystemBundlesDB::BUNDLE_STATUSES;
		$this->map['filter_status_list'] = $statuses;
		$this->map['paging'] = self::buildPagination($total, $this->mapPagingParams($lv));
		$this->parse_layout('edit_lvN', $this->map);
	}

	/**
	 * mapPagingParams function
	 *
	 * @param integer $lv
	 * @return array
	 */
	private function mapPagingParams(int $lv): array
	{
		return [
			'lv' => $lv,
			'keysearch' => $this->map['keysearch'],
			'filter_status' => $this->map['filter_status'],
			'filter_parent_id' => $this->map['filter_parent_id']
		];
	}

	/**
	 * buildPagination function
	 *
	 * @param integer $total
	 * @param array $pagingParams
	 * @return mixed
	 */
	private static function buildPagination(int $total, array $pagingParams)
	{
		return pagingV2($total, self::ITEMS_PER_PAGE, 10, false, 'page_no', $pagingParams);
	}

	/**
	 * onAjax function
	 *
	 * @return Json [
	 *      'success' => bool,
	 *      'msg' => string,
	 *      'data' => mixed
	 *  ]
	 */
	private static function onAjax()
	{
		$action = xgetUrl('action');
		$data = self::getSubmitData();
		return self::handleAjax($action, $data);
	}

	/**
	 * getSubmitData function
	 *
	 * @return array
	 */
	private static function getSubmitData(): array
	{
		return [
			'id' => xgetUrl('id', 0, 'i'),
			'name' => xgetUrl('name', null, 's'),
			'status' => intval(xgetUrl('status', false, 'b')),
			'parent_id' => xgetUrl('parent_id', null),
		];
	}

	/**
	 * handleAjax function
	 *
	 * @param string $action
	 * @param array $data
	 * @return Json [
	 *      'success' => bool,
	 *      'msg' => string,
	 *      'data' => mixed
	 *  ]
	 */
	private static function handleAjax(string $action, array $data)
	{
		$msg = self::validatorRequest($action, $data);
		if ($msg) {
			return resError($msg);
		} //end if

		$res = false;
		switch ($action) {
			case REQ_ACTION_CREATE:
				$res = AdminSystemBundlesDB::insert($data);
				if (!$res) {
					return resError('Tên nhóm đã tồn tại');
				} //end if
				break;

			case REQ_ACTION_EDIT:
				$res = AdminSystemBundlesDB::update($data);
				if (!$res) {
					return resError('Tên nhóm đã tồn tại');
				} //end if
				break;

			case REQ_ACTION_DELETE:
				$res = AdminSystemBundlesDB::delete($data['id'], AdminSystemBundlesDB::ON_MASTER_PRODUCTS);
				if (!$res) {
					return resError('Nhóm sản phẩm cấp 2 đã được sử dụng');
				} //end if
				break;

			default:
				return resError('Thao tác không hợp lệ');
		} //end switch

		return resSuccess($res);
	}

	/**
	 * validatorRequest function
	 *
	 * @param string $action
	 * @param array $data
	 * @return string
	 */
	private static function validatorRequest(string $action, array $data): string
	{
		if ($action === REQ_ACTION_EDIT || $action === REQ_ACTION_CREATE) {
			if (!$data['name']) {
				return 'Vui lòng điền tên nhóm sản phẩm';
			} //end if

			if ($data['parent_id'] < 1) {
				return 'Vui lòng lựa chọn nhóm sản phẩm cấp 1';
			} //end if

			return '';
		}

		$id = $data['id'] ?? 0;
		if (!$id) {
			return "Thao tác không hợp lệ";
		} //end if

		return '';
	}
}
