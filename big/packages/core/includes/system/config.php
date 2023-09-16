<?php
define('DEBUG',1);
define('START_YEAR',2018);
define('PREFIX','');

header("Content-Type: text/html; charset=utf-8");
ini_set ('zend.ze1_compatibility_mode','off');

$_REQUEST['portal'] = $_REQUEST['portal'] ?? 'default';
$page = $_REQUEST['page'] ?? 'home';

define('DEVELOPER',false);
define('NULL_TIME', '0000-00-00 00:00:00');
define('NULL_DATE', '0000-00-00');

// include kernel
require_once ROOT_PATH . 'packages/core/includes/common/Logger.php';
require_once ROOT_PATH . 'packages/core/includes/common/PalException.php';
require_once ROOT_PATH . 'packages/core/includes/utils/functions.php';
require_once ROOT_PATH . 'packages/core/includes/utils/DataFilter.php';
require_once ROOT_PATH . 'cache/modules.php';
require_once ROOT_PATH . 'packages/core/includes/system/default_session.php';
require_once ROOT_PATH . 'packages/core/includes/system/database.php';
include_once ROOT_PATH . 'cache/config/temp_off.php';
require_once ROOT_PATH . 'packages/core/includes/system/system.php';

require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
require_once ROOT_PATH . 'packages/core/includes/common/NumberToText.php';
require_once ROOT_PATH . 'packages/core/includes/common/RequestHandler.php';
require_once ROOT_PATH . 'packages/core/includes/common/Groups.php';
require_once ROOT_PATH . 'packages/core/includes/common/Systems.php';
require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';
require_once ROOT_PATH . 'packages/core/includes/common/OrderStatus.php';
require_once ROOT_PATH . 'packages/core/includes/common/Privilege.php';
require_once ROOT_PATH . 'packages/core/includes/common/LogHandler.php';

require_once ROOT_PATH . 'packages/core/includes/system/url.php';
require_once ROOT_PATH . 'packages/core/includes/system/id_structure.php';
require_once ROOT_PATH . 'packages/core/includes/portal/types.php';
require_once ROOT_PATH . 'packages/core/includes/portal/form.php';
require_once ROOT_PATH . 'packages/core/includes/system/user.php';
require_once ROOT_PATH . 'packages/core/includes/portal/module.php';
require_once ROOT_PATH . 'packages/core/includes/portal/portal.php';
require_once ROOT_PATH . 'packages/core/includes/system/visitor.php';
require_once ROOT_PATH . 'packages/core/includes/system/log.php';
require_once ROOT_PATH . 'packages/core/includes/utils/modify_string.php';

ini_set('magic_quotes_runtime', 0);
