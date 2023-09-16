<?php
class AdminSystemSources extends Module
{
	// ?page=admin_system_sources&cmd=sources_standardization&key=84b5ffb08eff4b649d3bcad4163aace1
	const CMD_STANDARDIZE = 'sources_standardization';

	function __construct($row)	
	{
		Module::Module($row);
		require_once ROOT_PATH . 'packages/vissale/lib/php/vissale.php';

		if (!Session::get('group_id') || !allowAddAdminSource()) {
			URL::access_denied();
		} //end if

		require_once 'db.php';

		$cmd = Url::get('cmd');
		switch ($cmd) {
			case self::CMD_STANDARDIZE:
				$key = md5('TuhaVoDich');
				// 84b5ffb08eff4b649d3bcad4163aace1
				if (Url::get('key') !== $key) {
					URL::access_denied();
				}//end if

				require_once 'forms/standardize.php';
				$this->add_form(new StandardizeSourceData());
				break;

			default:
				require_once 'forms/edit.php';
				$this->add_form(new EditAdminSystemSourcesForm());
				break;
		}
	}
}
