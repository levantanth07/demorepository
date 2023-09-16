<?php

class EditAdminSystemSourcesForm extends Form
{
	const ITEMS_PER_PAGE = 10;
	const VALID_ACTIONS = [
		REQ_ACTION_CREATE,
		REQ_ACTION_EDIT,
		REQ_ACTION_DELETE,
	];

	function __construct()
	{
		Form::Form('EditAdminSystemSourcesForm');
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

		$total = AdminSystemSourcesDB::countSources();

		$this->map = array();
		$this->map['total'] = $total;
		$this->map['paging'] = self::buildPagination($total);
		$this->map['sources'] = AdminSystemSourcesDB::getSources(self::ITEMS_PER_PAGE);
		$this->map['keysearch'] = xgetUrl('keysearch', null);
		$this->map['filter_active'] = xgetUrl('filter_active', -1, 'i');
		$this->map['filter_active_list'] = self::buildStatusOptions();
		$this->parse_layout('edit', $this->map);
	}

	/**
	 * buildPagination function
	 *
	 * @param integer $total
	 * @return mixed
	 */
	private static function buildPagination(int $total)
	{
		return paging($total, self::ITEMS_PER_PAGE, 10, false, 'page_no', ['keysearch', 'filter_active']);
	}

	/**
	 * buildStatusOptions function
	 *
	 * @return array
	 */
	private static function buildStatusOptions(): array
	{
		return [-1 => '--- Tất cả ---'] + AdminSystemSourcesDB::SOURCE_STATUSES;
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
			'is_active' => intval(xgetUrl('is_active', false, 'b')),
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
				$res = AdminSystemSourcesDB::insert($data);
				if (!$res) {
					return resError('Tên nguồn marketing đã tồn tại');
				} //end if
				break;

			case REQ_ACTION_EDIT:
				$res = AdminSystemSourcesDB::update($data);
				if (!$res) {
					return resError('Tên nguồn marketing đã tồn tại');
				} //end if
				break;

			case REQ_ACTION_DELETE:
				$res = AdminSystemSourcesDB::delete($data['id']);
				if (!$res) {
					return resError('Nguồn marketing đã được sử dụng');
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

			return '';
		}

		$id = $data['id'] ?? 0;
		if (!$id) {
			return "Thao tác không hợp lệ";
		} //end if

		return '';
	}
}
