<?php
class AdminSystemBundles extends Module
{
	// ?page=admin_system_bundles&cmd=master_bundles_standardization&key=84b5ffb08eff4b649d3bcad4163aace1
	const CMD_STANDARDIZE_MASTER_BUNDLE = 'master_bundles_standardization';
	// ?page=admin_system_bundles&cmd=bundles_standardization&key=84b5ffb08eff4b649d3bcad4163aace1
	const CMD_STANDARDIZE_BUNDLE = 'bundles_standardization';

	const SYSTEM_BUNDLE_LV1 = '1';
	const SYSTEM_BUNDLE_LV2 = '2';
	const VALID_SYSTEM_BUNDLES = [
		self::SYSTEM_BUNDLE_LV1,
		self::SYSTEM_BUNDLE_LV2,
	];

	function __construct($row)
	{
		Module::Module($row);

		if (!Session::get('group_id')) {
			URL::access_denied();
		} //end if

		require_once ROOT_PATH . 'packages/vissale/lib/php/vissale.php';
		if (!checkRoleAddBundle()) {
			URL::access_denied();
		} //end if

		require_once 'db.php';
		$cmd = Url::get('cmd');
		$key = md5('TuhaVoDich');
		// 84b5ffb08eff4b649d3bcad4163aace1
		if ($cmd && Url::get('key') == $key) {
			switch ($cmd) {
				case self::CMD_STANDARDIZE_MASTER_BUNDLE:
					require_once 'forms/StandardizeMasterBundle.php';
					$this->add_form(new StandardizeMasterBundle());
					break;
				case self::CMD_STANDARDIZE_BUNDLE:
					require_once 'forms/StandardizeBundle.php';
					$this->add_form(new StandardizeBundle());
					break;
				default:
					break;
			} //end switch

			die;
		} //end if

		$lv = xgetUrl('lv', self::SYSTEM_BUNDLE_LV1);
		if (!in_array($lv, self::VALID_SYSTEM_BUNDLES)) {
			return URL::js_redirect(true, 'Thao tác không hợp lệ');
		} //end if

		switch ($lv) {
			case self::SYSTEM_BUNDLE_LV1:
				require_once 'forms/edit_lv1.php';
				$this->add_form(new EditLv1AdminSystemBundlesForm());
				break;

			case self::SYSTEM_BUNDLE_LV2:
				require_once 'forms/edit_lvN.php';
				$this->add_form(new EditLvNAdminSystemBundlesForm());
				break;

			default:
				break;
		} //end switch
	}
}
