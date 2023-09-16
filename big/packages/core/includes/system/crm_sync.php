<?php

require_once ROOT_PATH . "config/message_queue_keys.php";

class CrmSync {
    /**
     * @var ShutdownScheduler $shutdownScheduler
     */
    protected static $shutdownScheduler;

    /**
     * @param $table
     * @param $group_id
     * @param $condition
     * @param array $meta_data
     */
    public static function publishEventOnInsert($table, $group_id, $condition, array $meta_data = []) {
        if (self::isGetMethod()) {
            return;
        }

        if (! self::isTableForPublish($table)) {
            return;
        }

        if (empty($group_id) || ! self::isGroupForPublish($group_id)) {
            return;
        }

        $unique_key_insert = '1_' . $table .'_'. md5($condition);
        $data = [
            'group_id' => $group_id,
            'condition' => $condition,
            'meta_data' => $meta_data
        ];

        self::getShutdownScheduler()->registerShutdownEvent(
            $unique_key_insert,
            function() use ($table, $data) {
                try {
                    message_queue::getInstance()->publishPushedEventInsert($table, null, $data);
                } catch (Exception $exception) {
                    //TODO: add to log;
                }
            }
        );
    }

    /**
     * @param $table
     * @param $group_id
     * @param $condition
     * @param array $meta_data
     * @return null|void
     */
    public static function publishEventOnUpdate($table, $group_id, $condition, array $meta_data = []) {
        if (self::isGetMethod()) {
            return;
        }

        if (! self::isTableForPublish($table)) {
            return;
        }

        if (empty($group_id) || ! self::isGroupForPublish($group_id)) {
            return;
        }

        try {
            preg_match('/^.*?([a-zA-Z0-9_]*id).*?=.*?([\d]+).*?$/', $condition, $matches);

            if (! empty($matches[1]) && ! empty($matches[2])) {
                $unique_key_update = '2_' . $table .'_'. md5($matches[1] . ':' . $matches[2]);
            } else {
                $unique_key_update = '2_' . $table .'_'. md5($condition);
            }

            $data = [
                'group_id' => $group_id,
                'condition' => $condition,
                'meta_data' => $meta_data
            ];

            self::getShutdownScheduler()->registerShutdownEvent(
                $unique_key_update,
                function () use ($table, $data) {
                    try {
                        message_queue::getInstance()->publishPushedEventUpdate($table, null, $data);
                    } catch (Exception $exception) {
                        //TODO: add to log
                    }
                }
            );
        } catch (Exception $exception) {
            //TODO: add to log;
        }
    }

    /**
     * @param $table
     * @param $group_id
     * @param array $deleteRecord
     * @return null|void
     */
    public static function publishEventOnDelete($table, $group_id, array $deleteRecord) {
        if (! in_array($table, ['products'])) {
            if (self::isGetMethod()) {
                return;
            }
        }

        if (! self::isTableForPublish($table)) {
            return;
        }

        if(empty($deleteRecord) || empty($group_id) || ! self::isGroupForPublish($group_id)) {
            return;
        }

        foreach ($deleteRecord as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $unique_key_delete = '3_' . $table . '_' . md5($item['id']);
            $data = [
                'group_id' => $group_id,
                'items' => [$item['id']]
            ];

            self::getShutdownScheduler()->registerShutdownEvent(
                $unique_key_delete,
                function() use ($table, $data) {
                    try {
                        message_queue::getInstance()->publishPushedEventDelete($table, null, $data);
                    } catch (Exception $exception) {
                        //TODO: add to log;
                    }
                }
            );
        }
    }

    public static function getShutdownScheduler() {
        if (is_null(self::$shutdownScheduler)) {
            require_once "shutdown_scheduler.php";

            self::$shutdownScheduler = new ShutdownScheduler();
        }

        return self::$shutdownScheduler;
    }

    /**
     * @param $groupId
     * @return bool
     */
    private static function isGroupForPublish($groupId) {
        if (! empty($groupId)) {
            try {
                if (! class_exists('message_queue')) {
                    $message_queue_path = join(DIRECTORY_SEPARATOR, [
                        rtrim(ROOT_PATH, '\\/'),
                        'packages',
                        'core',
                        'includes',
                        'system',
                        'message_queue.php'
                    ]);

                    require_once $message_queue_path;
                }

                $groups = (array) @json_decode(message_queue::getInstance()->getRedis()->get('crm_big_active_groups'));

                return in_array($groupId, $groups);
            } catch (Exception $exception) {
                //TODO: add to log
            }
        }

        return false;
    }

    /**
     * @param $table
     * @return bool
     */
    private static function isTableForPublish($table) {
        return in_array($table, DATA_UPDATED_TRIGGER_TABLE);
    }

    /**
     * @return bool
     */
    protected static function isGetMethod() {
        if (! isset($_SERVER['REQUEST_METHOD'])) {
            return true;
        }

        return 'GET' === strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @param $user_id
     * @return bool
     */
    protected static function accountIsCSKH($user_id) {
        return DB::fetch(
                "SELECT count(*) as is_cskh FROM `roles_to_privilege`"
                . " WHERE `privilege_code`='CSKH'"
                . " AND `role_id` in (SELECT `role_id` from `users_roles` where `user_id`={$user_id})",
                'is_cskh'
            ) > 0;
    }

    /**
     * @param $account_id
     * @param $group_id
     * @return bool
     */
    protected static function accountIsOwner($account_id, $group_id) {
        $owner_account_id = DB::fetch('select code from `groups` where id=' . (int) $group_id, 'code');
        return $owner_account_id == $account_id;
    }

    /**
     * @param $user_id
     * @return bool
     */
    protected static function accountIsSale($user_id) {
        return DB::fetch(
                "SELECT count(*) as is_sale FROM `roles_to_privilege`"
                . " WHERE `privilege_code`='GANDON'"
                . " AND `role_id` in (SELECT `role_id` from `users_roles` where `user_id`={$user_id})",
                'is_sale'
            ) > 0;
    }

    protected static function updateAccount($user_id, $account_id) {
        $result = DB::query("select * from `account` where `id` = '".DB::escape($account_id)."'");
        while($row = $result->fetch_assoc())
        {
            unset($row['password']);
            $row['is_admin'] = (int) ($row['admin_group'] == 1);
            $row['is_cskh'] = (int) self::accountIsCSKH($user_id);
            $row['is_sale'] = (int) self::accountIsSale($user_id);
            $row['is_owner'] = (int) self::accountIsOwner($account_id, $row['group_id']);

            message_queue::getInstance()->publishPushedEventUpdate('account', null, $row);
        }
    }
}