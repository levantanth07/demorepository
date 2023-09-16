<?php

use function Amp\Dns\query;

class AdminTrackingOrdersDB
{
    /**
     * getOrders function
     *
     * @param integer $item_per_page
     * @return array
     */
    static function getOrders(int $item_per_page): array
    {
        $cols = self::generateGetOrdersColumns();
        $conditions = self::generateSearchQuery();
        $limit = self::limitAndPage($item_per_page);
        $query = "
            SELECT $cols
            FROM orders
                INNER JOIN groups ON groups.id = orders.group_id
                INNER JOIN statuses ON statuses.id = orders.status_id
                LEFT JOIN bundles ON bundles.id = orders.bundle_id
            WHERE $conditions
            ORDER BY orders.created DESC, orders.id DESC, orders.status_id ASC
            LIMIT $limit
        ";

        $query = self::formatQuery($query);
        return DB::fetch_all_array($query);
    }

    static function getTotalOrders()
    {
        $conditions = self::generateSearchQuery();
        $query = "
            SELECT COUNT(orders.id) AS total
            FROM orders
                INNER JOIN groups ON groups.id = orders.group_id
                INNER JOIN statuses ON statuses.id = orders.status_id
                LEFT JOIN bundles ON bundles.id = orders.bundle_id
            WHERE $conditions
            ORDER BY orders.created DESC, orders.id DESC
        ";

        $query = self::formatQuery($query);
        return DB::fetch($query, 'total');
    }

    /**
     * getOrders function
     *
     * @param integer $item_per_page
     * @return array
     */
    static function getUsers(array $userIds = []): array
    {
        $cols = self::generateColumns(['id', 'name', 'phone']);
        $conditions = self::whereIn('id', $userIds);
        $query = "
            SELECT $cols
            FROM users
            WHERE $conditions
        ";

        $query = self::formatQuery($query);
        return DB::fetch_all($query);
    }

    /**
     * getOrderRevisions function
     *
     * @param array $orderIds
     * @return array
     */
    static function getOrderRevisions(array $orderIds = []): array
    {
        $cols = self::generateColumns(['id', 'created', 'user_created_name', 'data', 'order_id']);
        $conditions = self::whereIn('order_id', $orderIds);
        $query = "
            SELECT $cols
            FROM order_revisions
            WHERE $conditions
        ";

        $query = self::formatQuery($query);
        return DB::fetch_all($query);
    }

    /**
     * getOrderProducts function
     *
     * @param array $orderIds
     * @return array
     */
    static function getOrderProducts(array $orderIds = []): array
    {
        $cols = self::generateColumns([
            'id',
            'order_id',
            'product_id',
            'product_name',
            'product_price',
        ]);

        $conditions = self::whereIn('order_id', $orderIds);
        $query = "
            SELECT $cols
            FROM orders_products
            WHERE $conditions
        ";

        $query = self::formatQuery($query);
        return DB::fetch_all($query);
    }

    /**
     * generateGetOrdersColumns function
     *
     * @return string
     */
    static function generateGetOrdersColumns(): string
    {
        $cols = [
            'orders.id',
            'customer_id',
            'customer_name',
            'mobile',
            'mobile2',
            'status_id',
            'total_price',
            'orders.group_id',
            'groups.name as group_name',
            'status_id',
            'statuses.name as status_name',
            'orders.created',
            'orders.user_created',
            'confirmed',
            'user_confirmed',
            'assigned',
            'user_assigned',
            'bundle_id',
            'orders.first_user_assigned',
            'orders.first_assigned',
            'bundles.name as bundle_name',
        ];

        return self::generateColumns($cols);
    }

    /**
     * generateColumns function
     *
     * @param array $cols
     * @return string
     */
    static function generateColumns(array $cols = []): string
    {
        return implode(', ', $cols);
    }

    /**
     * generateSearchQuery function
     *
     * @return string
     */
    static function generateSearchQuery(): string
    {
        $conditions = [];
        $phone = xss_clean(URL::sget('phone_number'));
        if ($phone) {
            $conditions[] = "(mobile = '$phone' OR mobile2 = '$phone')";
        } //end if

        // if (Session::get('group_id') != GROUP_ID_AN_NINH_SHOP) {
        $group_ids = self::getGroupIds();
        $conditions[] = self::whereIn('orders.group_id', $group_ids);
        // } //end if

        return self::buildConditions($conditions);
    }

    static function buildConditions(array $conditions = [], string $separator = ' AND '): string
    {
        return (!$conditions) ? '1 = 1' : implode($separator, $conditions);
    }

    /**
     * urlGet function
     *
     * @param string $name
     * @param mixed $default
     * @return mixed;
     */
    static function urlGet(string $name, $default = null)
    {
        if (!URL::get($name)) {
            return $default;
        }

        return DB::escape(
            DataFilter::removeDuplicatedSpaces(URL::get($name))
        );
    }

    /**
     * getSystemGroupId function
     *
     * @param integer $id
     * @return integer|null
     */
    static function getSystemGroupId(int $id)
    {
        $cols = self::generateColumns([
            'id',
            'name',
            'system_group_id'
        ]);

        $query = "
            SELECT $cols
            FROM groups
            WHERE id = $id
        ";

        $query = self::formatQuery($query);
        return DB::fetch($query, 'system_group_id');
    }

    /**
     * getSystemGroupIdOnGSystemAccount function
     *
     * @param integer $userId
     * @return integer|null
     */
    static function getSystemGroupIdOnGSystemAccount(int $userId)
    {
        $cols = self::generateColumns([
            'id',
            'system_group_id'
        ]);

        $query = "
            SELECT $cols
            FROM groups_system_account
            WHERE user_id = $userId
            LIMIT 1
        ";

        $query = self::formatQuery($query);
        return DB::fetch($query, 'system_group_id');
    }

    /**
     * getGIdOfStructureChild function
     *
     * @param integer $system_group_id
     * @return array
     */
    static function getGIdOfStructureChild(int $system_group_id)
    {
        $structure_id = DB::structure_id('groups_system', $system_group_id);
        $conditions = Systems::getIDStructureChildCondition($structure_id);
        $query = "
            SELECT groups.id
            FROM groups
            JOIN groups_system ON groups_system.id = groups.system_group_id
            WHERE $conditions
            ORDER BY groups_system.structure_id
        ";

        $query = self::formatQuery($query);
        return DB::fetch_all($query, 'system_group_id');
    }

    /**
     * getGroupIds function
     *
     * @return array
     */
    static function getGroupIds(): array
    {
        $user_data = Session::get('user_data');
        $group_id = $user_data['group_id'];
        $userId = $user_data['user_id'];
        $system_group_id = self::getSystemGroupIdOnGSystemAccount($userId);
        $list_group = empty($system_group_id) ? null : self::getGIdOfStructureChild($system_group_id);
        $group_ids = empty($list_group) ? [$group_id] : array_keys($list_group);
        return $group_ids;
    }

    static function whereIn(
        string $col,
        array $values = []
    ): string {
        foreach ($values as $key => $value) {
            if ($value === null) {
                unset($values[$key]);
            } //end if
        } //end foreach

        $values = implode(',', $values);
        return " $col IN ($values)";
    }

    /**
     * limitAndPage function
     *
     * @param integer $item_per_page
     * @param string $page_name
     * @return string
     */
    static function limitAndPage(
        int $limit = 5,
        string $page_name = 'page_no'
    ): string {
        $page_no = Url::get($page_name) ?: 1;
        $offset = ($page_no - 1) * $limit;
        return "$offset, $limit";
    }

    /**
     * getPageOffset function
     *
     * @param integer $item_per_page
     * @param string $page_name
     * @return string
     */
    static function getPageOffset(
        int $limit = 5,
        string $page_name = 'page_no'
    ): string {
        $page_no = Url::get($page_name) ?: 1;
        $offset = ($page_no - 1) * $limit;
        return $offset;
    }

    /**
     * string function
     *
     * @param string $query
     * @return string
     */
    static function formatQuery(string $query): string
    {
        return trim(preg_replace('!\s+!', ' ', $query));
    }
}
