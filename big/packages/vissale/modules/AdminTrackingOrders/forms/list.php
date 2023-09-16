<?php
class ListOrdersForm extends Form
{
    static $max_phone_number_length = 20;
    public function __construct()
    {
        $this->link_css('packages/vissale/modules/AdminTrackingOrders/css/common.css?v=13');
    }

    function draw()
    {
        if (!check_system_user_permission('tracuudonhang')) {
            Url::js_redirect(false, 'Bạn không có quyền truy cập', ['page' => 'admin_group_info']);
            die;
        } //end if

        $compact = $this->handler();
        $this->parse_layout('list',  $compact);
    }

    /**
     * defineViewData function
     *
     * @return array
     */
    function defineViewData(): array
    {
        return [
            'search_url' => self::generate_search_url(),
            'error' => null,
            'paging' => '',
            'total_orders' => 0,
            'reset_filter_url' => Url::build('tracking-orders'),
            'configs_phone_min' => get_group_options('min_search_phone_number'),
            'configs_phone_max' => self::$max_phone_number_length,
            'configs_phone_val' => null,
            'orders_block' => self::renderEmptyOrders('Vui lòng nhập số điện thoại'),
        ];
    }

    /**
     * handler function
     *
     * @return array
     */
    function handler(): array
    {
        require_once 'packages/core/includes/utils/paging.php';

        $compact = self::defineViewData();
        $compact = self::validateFilterData($compact);
        if ($compact['error']) {
            return $compact;
        } //end if

        $item_per_page = 15;
        $orders = AdminTrackingOrdersDB::getOrders($item_per_page);
        $totalOrders = AdminTrackingOrdersDB::getTotalOrders($item_per_page);
        $users = self::getUsers($orders);
        $offset = AdminTrackingOrdersDB::getPageOffset($item_per_page) + 1;

        $compact['orders_block'] = self::renderlistOrders($users, $orders, $offset);
        $compact['paging'] = paging($totalOrders, $item_per_page, 10, false, 'page_no', ['phone_number', 'search_text']);
        $compact['total_orders'] = $totalOrders;

        return $compact;
    }

    /**
     * validateFilterData function
     *
     * @param array $compact
     * @return array
     */
    function validateFilterData(array $compact): array
    {
        $phone = strVal(AdminTrackingOrdersDB::urlGet('phone_number'));
        $compact['configs_phone_val'] = $phone;

        if (!$phone) {
            $compact = self::generateError($compact, 'Vui lòng nhập số điện thoại');
            return $compact;
        } //end if

        $min = $compact['configs_phone_min'];
        $max = $compact['configs_phone_max'];
        $length = strlen($phone);
        if ($length < $min || $length > $max) {
            $err = "Vui lòng nhập số điện thoại trong khoảng $min => $max";
            $compact = self::generateError($compact, $err);
        } //end if

        return $compact;
    }

    /**
     * generateError function
     *
     * @param array $compact
     * @param string $error
     * @return array
     */
    function generateError(array $compact, string $error = ''): array
    {
        $compact['error'] = $error;
        return $compact;
    }

    function on_submit()
    {
    }

    /**
     * renderlistOrders function
     *
     * @param array $orders
     * @return string
     */
    function renderlistOrders(
        array $users,
        array $orders,
        int $index = 1
    ): string {
        $html = '';
        if (!$orders) {
            return self::renderEmptyOrders();
        } //end if

        $obj_users = new ArrHelper($users);
        foreach ($orders as $key => $order) {
            $obj_order = new ArrHelper($order);
            $order_id = $obj_order->getStr('id');
            $group_id = $obj_order->getStr('group_id');
            $group_name = $obj_order->getStr('group_name');
            $created = $this->formatDateTime($obj_order->getStr('created'));
            $user_created = $obj_order->getStr('user_created');
            $user_created_name = $obj_users->getStr("$user_created.name");
            $user_created_phone = $obj_users->getStr("$user_created.phone");
            $user_confirmed = $obj_order->getStr('user_confirmed');
            $user_confirmed_time = $this->formatDateTime($obj_order->getStr('confirmed'));
            $user_confirmed_name = $obj_users->getStr("$user_confirmed.name");
            $user_confirmed_phone = $obj_users->getStr("$user_confirmed.phone");
            $user_assigned = $obj_order->getStr('user_assigned');
            if (!$user_assigned) {
                $user_assigned_id = $obj_order->getStr('first_user_assigned');
                $assigned_time = $obj_order->getStr("assigned");
            } else {
                $user_assigned_id = $user_assigned;
                $assigned_time = $obj_order->getStr("first_assigned");
            }

            $user_assigned_time = $this->formatDateTime($assigned_time);
            $user_assigned_name = $obj_users->getStr("$user_assigned_id.name");
            $user_assigned_phone = $obj_users->getStr("$user_assigned_id.phone");

            $html .= "
                <tr>
                    <td class='text-center' data-id='$order_id'>$index</td>
                    <td data-id='$group_id'>$group_name</td>
                    <td data-id='$user_created'>
                        <p class='mb-sm'>$user_created_name</p>
                        <p class='mb-sm'>$user_created_phone</p>
                        $created
                    </td>
                    <td data-id='$user_assigned'>
                        <p class='mb-sm'>$user_assigned_name</p>
                        <p class='mb-sm'>$user_assigned_phone</p>
                        $user_assigned_time
                    </td>
                    <td data-id='$user_confirmed'>
                        <p class='mb-sm'>$user_confirmed_name</p>
                        <p class='mb-sm'>$user_confirmed_phone</p>
                        $user_confirmed_time
                    </td>
                </tr>
            ";

            $index++;
        } //end foreach

        return $html;
    }

    function formatDateTime($datetime)
    {
        return ($datetime == "0000-00-00 00:00:00") ? null : self::formatDate($datetime);
    }

    /**
     * getUsers function
     *
     * @param array $orders
     * @return array
     */
    function getUsers(array $orders): array
    {
        $user_assigned = array_column($orders, 'user_assigned');
        $first_user_assigned = array_column($orders, 'first_user_assigned');
        $user_confirmed = array_column($orders, 'user_confirmed');
        $user_created = array_column($orders, 'user_created');
        $userIds = array_unique(array_merge($user_assigned, $user_confirmed, $user_created, $first_user_assigned));
        return AdminTrackingOrdersDB::getUsers($userIds);
    }

    /**
     * renderOrderRevisions function
     *
     * @param array $revisions
     * @param integer $order_id
     * @return string
     */
    function renderOrderRevisions(array $revisions, int $order_id): string
    {
        if (!$revisions) {
            return 'N/A';
        } //end if

        $html = " 
                <a data-toggle='collapse'  class='pb-1r d-block pl-1r' href='#collapse-$order_id'>
                    <i class='glyphicon glyphicon-sort'></i> Xem chi tiết 
                </a>
                <div id='collapse-$order_id' class='panel-collapse collapse scrollY'>
            ";

        foreach ($revisions as $revision) {
            $created_at = $revision['created'];
            $user_name = $revision['user_created_name'];
            $info = $revision['data'];
            $html .= "
                <div class='revision'>
                    <p>
                        $created_at - 
                        <span class='text-red'>
                            $user_name
                        </span>
                    </p>
                    <p>$info</p>
                </div>
            ";
        } //end foreach

        $html .= '</div>';
        return $html;
    }

    /**
     * renderEmptyOrders function
     *
     * @return string
     */
    function renderEmptyOrders(string $msg = 'Không tìm thấy số điện thoại yêu cầu, vui lòng nhập lại'): string
    {
        return "
            <tr class='bg-gray'>
                <td colspan='12'>
                    <div class='text-center pt-4r pb-4r'>
                        <i class='glyphicon glyphicon-inbox pb-2r text-3r'></i>
                        <p>$msg</p>
                    </div>
                </td>
            </tr>
        ";
    }

    /**
     * renderOrderProducts function
     *
     * @param array $order_products
     * @return string
     */
    function renderOrderProducts(array $order_products): string
    {
        $html = '<ol>';
        if (!$order_products) {
            return $html;
        } //end if

        foreach ($order_products as $index => $product) {
            $id = $product['product_id'];
            $name = $product['product_name'];
            $html .= "<li><p data-id='$id'>$name</p></li>";
        } //end foreach
        $html .= '</ol>';
        return $html;
    }

    /**
     * getOrdersRevisions function
     *
     * @param array $order_ids
     * @return array
     */
    function getOrdersRevisions(array $order_ids): array
    {
        $order_revisions = AdminTrackingOrdersDB::getOrderRevisions($order_ids);
        $revisions = [];
        foreach ($order_revisions as $id => $revision) {
            $revisions[$revision['order_id']][] = $revision;
        } //end foreach

        return $revisions;
    }

    /**
     * getOrderProducts function
     *
     * @param array $order_ids
     * @return array
     */
    function getOrderProducts(array $order_ids): array
    {
        $order_products = AdminTrackingOrdersDB::getOrderProducts($order_ids);
        $products = [];
        foreach ($order_products as $id => $order_product) {
            $products[$order_product['order_id']][] = $order_product;
        } //end foreach

        return $products;
    }

    /**
     * generate_search_url function
     *
     * @return string
     */
    function generate_search_url(): string
    {
        return '/' . Url::build_current(array('do' => 'search'));
    }

    function formatDate($date, $format = 'd-m-Y H:i:s'): string
    {
        if (!$date) {
            return $date;
        } //end if

        $date = new DateTime($date);
        return $date->format($format);
    }
}
