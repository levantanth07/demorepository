<?php

class ExportExcelSystemForm extends Form
{
    public static $system_group_id;
    public static $user_id;
    public static $header;
    public static $header_product;
    public static $total;

    function __construct(){
        Form::Form('ExportExcelSystemForm');

        self::$user_id = get_user_id();
        self::$system_group_id = DB::fetch('select id,system_group_id from groups_system_account where user_id='.self::$user_id.' limit 0,1','system_group_id');
        self::$header = array(
            'group_id' => 'ID shop',
            'prefix_post_code' => 'Mã công ty (mã bưu cục)',
            'group_name' => 'Tên shop',
            'id' => 'Mã đơn hàng',
            'postal_code' => 'Mã vận chuyển',
            'type' => 'Loại đơn',
            'customer_id' => 'Mã khách hàng',
            'city' => 'Tỉnh/Thành',
            'bundle_name' => 'Phân loại sản phẩm',
            'status_name' => 'Trạng thái',
            'shipping_price' => 'Phí vận chuyển',
            'other_price' => 'Phụ thu',
            'discount_price' => 'Giảm giá',
            'total_price' => 'Thành tiền',
            'user_created_name' => 'Người tạo',
            'created' => 'Ngày tạo',
            'user_assigned_name' => 'Nhân viên được chia',
            'user_confirmed_name' => 'Nhân viên xác nhận',
            'confirmed' => 'Ngày xác nhận',
            'accounting_confirmed' => 'Ngày đóng hàng',
            'delivered' => 'Ngày chuyển',
            'update_successed_time' => 'Ngày thành công',
            'update_paid_time' => 'Ngày đã thu tiền',
            'update_returned_time' => 'Ngày hoàn',
            'update_returned_to_warehouse_time' => 'Ngày đã trả hàng về kho');
        self::$header_product = array(
            'group_id' => 'ID shop',
            'prefix_post_code' => 'Mã công ty (mã bưu cục)',
            'group_name' => 'Tên shop',
            'id' => 'Mã đơn hàng',
            'postal_code' => 'Mã vận chuyển',
            'product_code' => 'Mã sản phẩm',
            'product_name' => 'Tên sản phẩm',
            'product_qty' => 'Số lượng',
            'product_price' => 'Đơn giá',
            'product_discount_price' => 'Giảm giá SP',
            'product_total_price' => 'Thành tiền SP',
            'type' => 'Loại đơn',
//            'customer_id' => 'Mã khách hàng',
//            'city' => 'Tỉnh/Thành',
            'bundle_name' => 'Phân loại sản phẩm',
            'status_name' => 'Trạng thái',
            'user_created_name' => 'Người tạo',
            'created' => 'Ngày tạo',
            'user_assigned_name' => 'Nhân viên được chia',
            'user_confirmed_name' => 'Nhân viên xác nhận',
            'confirmed' => 'Ngày xác nhận',
            'accounting_confirmed' => 'Ngày đóng hàng',
            'delivered' => 'Ngày chuyển',
            'update_successed_time' => 'Ngày thành công',
            'update_paid_time' => 'Ngày đã thu tiền',
            'update_returned_time' => 'Ngày hoàn',
            'update_returned_to_warehouse_time' => 'Ngày đã trả hàng về kho');
        self::$total = -1;
    }

    function on_submit(){
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        
        $cond = self::getConditions();
        $user_id = Session::get('user_id');
        $export_type = Url::get('export_type', 'donhang');

        if(!strpos($cond, 'AND')){
            Url::js_redirect('admin_orders','Tiêu chí chọn ' . $cond . ' không chính xác. Vui lòng kiểm tra lại', ['cmd'=>'export_excel_system']);
        }
        if(count(explode('AND', strtoupper($cond))) < 2){
            Url::js_redirect('admin_orders','Sếp cần chọn tiêu chí xuất excel', ['cmd'=>'export_excel_system']);
        }

        $message = '';
        if ($export_type === 'donhang') {
            $total = (int) self::get_total($cond);
            self::$total = $total;
            if (Url::get('num') && $total > 0) {
                $num = Url::iget('num');
                $filename = "data_orders_export_" . date("Y-m-d") . "_" . $num . ".csv";
                self::download_send_headers($filename);
                echo self::array2csv($total, $num, $cond);
                // Log
                $logTitle = 'Xuất excel đơn hàng theo hệ thống';
                $desc = self::getDesc($total, $num);
                $arrPatchData = array(
                    'censored_phone_number' => 0,
                    'list_export_order_id' => ''
                );
                System::logHT('EXPORT_EXCEL', $logTitle, $desc, '', '', false, $arrPatchData);
                //
                die;
            } else {
                $message = 'Không có đơn hàng được xuất';
            }
        }
        if ($export_type === 'sanpham') {
            $total = (int) self::get_total_product($cond);
            self::$total = $total;
            if (Url::get('num') && $total > 0) {
                $num = Url::iget('num');
                self::download_send_headers("data_order_products_export_" . date("Y-m-d") . "_" . $num . ".csv");
                echo self::array2csv_product($total, $num, $cond);
                // Log
                $logTitle = 'Xuất excel sản phẩm đơn hàng theo hệ thống';
                $desc = self::getDesc($total, $num);
                $arrPatchData = array(
                    'censored_phone_number' => 0,
                    'list_export_order_id' => ''
                );
                System::log('EXPORT_EXCEL', $logTitle, $desc, '', '', false, $arrPatchData);
                //
                die;
            } else {
                $message = 'Không có sản phẩm đơn hàng được xuất';
            }
        }

//        Url::js_redirect('admin_orders',$message, ['cmd'=>'export_excel_system']);
    }

    function draw()
    {
        $loop = 0;
        if (self::$total > 0) {
            $loop = ceil(self::$total / 20000);
        }
        
        $this->mapFilterDates();
        $this->map['loop'] = $loop;
        $this->map['total'] = self::$total;
        $this->map['export_type_list'] = array('donhang' => 'Xuất theo đơn hàng', 'sanpham' => 'Xuất theo sản phẩm');

        $this->parse_layout('export_excel_system', $this->map);
    }

    private function get_total($cond)
    {
        $sql = '
            SELECT
                count(*) as total
            FROM
                orders
                LEFT JOIN orders_extra ON orders_extra.order_id=orders.id
            WHERE
                '.$cond.'
            ';
        $items = DB::fetch($sql, 'total');
        return $items;
    }

    private function get_total_product($cond)
    {
        $sql = '
            SELECT
                count(*) as total
            FROM
                orders
                LEFT JOIN orders_extra ON orders_extra.order_id=orders.id 
                INNER JOIN orders_products as op ON orders.id = op.order_id
            WHERE
                '.$cond.'
            ';
        $items = DB::fetch($sql, 'total');
        return $items;
    }

    private function get_items($cond, $skip, $per)
    {
        $sql = '
            SELECT
                orders.group_id,
                groups.prefix_post_code,
                groups.name as group_name,
                orders.id,
                orders.postal_code,
                orders.type,
                orders.customer_id,
                orders.city,
                (select name from bundles where id = orders.bundle_id) as bundle_name,
                statuses.name as status_name,
                orders.shipping_price,
                orders.other_price,
                orders.discount_price,
                orders.total_price,
                (SELECT username from users where id = orders.user_created) as user_created_name,
                orders.created,
                (SELECT username from users where id = orders.user_assigned) as user_assigned_name,
                (SELECT username from users where id = orders.user_confirmed) as user_confirmed_name,
                orders.confirmed,
                orders_extra.accounting_confirmed,
                orders.delivered,
                orders_extra.update_successed_time,
                orders_extra.update_paid_time,
                orders_extra.update_returned_time,
                orders_extra.update_returned_to_warehouse_time
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                LEFT JOIN orders_extra ON orders_extra.order_id=orders.id 
                LEFT JOIN statuses ON orders.status_id=statuses.id 
            WHERE
                '.$cond.'
            LIMIT '.$skip.', '.$per.'
            ';
        $items = DB::fetch_all($sql);
        return ($items);
    }
    
    private function get_items_product($cond, $skip, $per)
    {
        $group_id = AdminOrders::$group_id;
        $statuses = AdminOrdersDB::get_status();

        $sql = '
            SELECT
                orders.group_id,
                groups.prefix_post_code,
                groups.name as group_name,
                orders.id,
                orders.postal_code,
                products.code as product_code,
                op.id as op_id,
                IF(op.product_name IS NULL OR op.product_name = "", products.name, op.product_name) as product_name,
                op.qty as product_qty,
                op.product_price,
                op.discount_amount,
                op.discount_rate,
                orders.type,
                (select name from bundles where id = orders.bundle_id) as bundle_name,
                statuses.name as status_name,
                (SELECT username from users where id = orders.user_created) as user_created_name,
                orders.created,
                (SELECT username from users where id = orders.user_assigned) as user_assigned_name,
                (SELECT username from users where id = orders.user_confirmed) as user_confirmed_name,
                orders.confirmed,
                orders_extra.accounting_confirmed,
                orders.delivered,
                orders_extra.update_successed_time,
                orders_extra.update_paid_time,
                orders_extra.update_returned_time,
                orders_extra.update_returned_to_warehouse_time
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                LEFT JOIN orders_extra ON orders_extra.order_id=orders.id
                LEFT JOIN statuses ON orders.status_id=statuses.id  
                INNER JOIN orders_products as op ON orders.id = op.order_id 
                LEFT JOIN products ON products.id = op.product_id
            WHERE
                '.$cond.'
            LIMIT '.$skip.', '.$per.'
            ';
        $items = DB::fetch_all_key($sql, 'op_id');
        return ($items);
    }

    private function get_groups(){
        $sql = '
            select groups.id
            from `groups`
            join groups_system on groups_system.id = groups.system_group_id
            where '.Systems::getIDStructureChildCondition(DB::structure_id('groups_system',self::$system_group_id)).'
            order by groups_system.structure_id
        ';
        return DB::fetch_all($sql, 'id');
    }
    
    private function getDesc($total, $num)
    {
        $ngay_tao_checkbox          = isset($_REQUEST['ngay_tao_checkbox']) ? 1 : 0;
        $ngay_chia_checkbox         = isset($_REQUEST['ngay_chia_checkbox']) ? 1 : 0;
        $ngay_xn_checkbox           = isset($_REQUEST['ngay_xn_checkbox']) ? 1 : 0;
        $ngay_chuyen_kt_checkbox    = isset($_REQUEST['ngay_chuyen_kt_checkbox']) ? 1 : 0;
        $ngay_chuyen_checkbox       = isset($_REQUEST['ngay_chuyen_checkbox']) ? 1 : 0;
        $ngay_chuyen_hoan_checkbox  = isset($_REQUEST['ngay_chuyen_hoan_checkbox']) ? 1 : 0;
        $ngay_thanh_cong_checkbox   = isset($_REQUEST['ngay_thanh_cong_checkbox']) ? 1 : 0;
        $ngay_thu_tien_checkbox     = isset($_REQUEST['ngay_thu_tien_checkbox']) ? 1 : 0;
        $ngay_tra_hang_checkbox     = isset($_REQUEST['ngay_tra_hang_checkbox']) ? 1 : 0;
        $ngay_tao          = self::checkDate($ngay_tao_checkbox, Url::get('ngay_tao_from'), Url::get('ngay_tao_to'));
        $ngay_chia         = self::checkDate($ngay_chia_checkbox, Url::get('ngay_chia_from'), Url::get('ngay_chia_to'));
        $ngay_xn           = self::checkDate($ngay_xn_checkbox, Url::get('ngay_xn_from'), Url::get('ngay_xn_to'));
        $ngay_chuyen_kt    = self::checkDate($ngay_chuyen_kt_checkbox, Url::get('ngay_chuyen_kt_from'), Url::get('ngay_chuyen_kt_to'));
        $ngay_thanh_cong   = self::checkDate($ngay_thanh_cong_checkbox, Url::get('ngay_thanh_cong_from'), Url::get('ngay_thanh_cong_to'));
        $ngay_thu_tien     = self::checkDate($ngay_thu_tien_checkbox, Url::get('ngay_thu_tien_from'), Url::get('ngay_thu_tien_to'));
        $ngay_chuyen       = self::checkDate($ngay_chuyen_checkbox, Url::get('ngay_chuyen_from'), Url::get('ngay_chuyen_to'));
        $ngay_chuyen_hoan  = self::checkDate($ngay_chuyen_hoan_checkbox, Url::get('ngay_chuyen_hoan_from'), Url::get('ngay_chuyen_hoan_to'));
        $ngay_tra_hang     = self::checkDate($ngay_tra_hang_checkbox, Url::get('ngay_tra_hang_from'), Url::get('ngay_tra_hang_to'));

        $system_group = DB::fetch('select id,name from groups_system where id='.self::$system_group_id);
        $desc = 'Xuất excel file số '.$num.' thông tin '.$total.' đơn hàng từ hệ thống '.$system_group['name'].'. ';
        
        return $desc.(($ngay_tao_checkbox && $ngay_tao)?' Ngày tạo từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_chia_checkbox && $ngay_chia)?' Ngày chia từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_xn_checkbox && $ngay_xn)?' Ngày xác chốt từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_chuyen_kt_checkbox && $ngay_chuyen_kt)?' Ngày đóng từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_chuyen_checkbox && $ngay_thanh_cong)?' Ngày chuyển hàng từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_chuyen_hoan_checkbox && $ngay_thu_tien)?' Ngày chuyển hoàn từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_thanh_cong_checkbox && $ngay_chuyen)?' Ngày thành công từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_thu_tien_checkbox && $ngay_chuyen_hoan)?' Ngày thu tiền từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
            '.(($ngay_tra_hang_checkbox && $ngay_tra_hang)?' Ngày trả hàng từ: '.$ngay_tao[0].' đến '.$ngay_tao[1].'. ':'').'
        ';
    }

    private function getConditions()
    {
        $group_ids = implode(',', array_keys(self::get_groups()));
        $ngay_tao_checkbox          = isset($_REQUEST['ngay_tao_checkbox']) ? 1 : 0;
        $ngay_chia_checkbox         = isset($_REQUEST['ngay_chia_checkbox']) ? 1 : 0;
        $ngay_xn_checkbox           = isset($_REQUEST['ngay_xn_checkbox']) ? 1 : 0;
        $ngay_chuyen_kt_checkbox    = isset($_REQUEST['ngay_chuyen_kt_checkbox']) ? 1 : 0;
        $ngay_chuyen_checkbox       = isset($_REQUEST['ngay_chuyen_checkbox']) ? 1 : 0;
        $ngay_chuyen_hoan_checkbox  = isset($_REQUEST['ngay_chuyen_hoan_checkbox']) ? 1 : 0;
        $ngay_thanh_cong_checkbox   = isset($_REQUEST['ngay_thanh_cong_checkbox']) ? 1 : 0;
        $ngay_thu_tien_checkbox     = isset($_REQUEST['ngay_thu_tien_checkbox']) ? 1 : 0;
        $ngay_tra_hang_checkbox     = isset($_REQUEST['ngay_tra_hang_checkbox']) ? 1 : 0;
        
        $ngay_tao          = self::checkDate($ngay_tao_checkbox, Url::get('ngay_tao_from'), Url::get('ngay_tao_to'));
        $ngay_chia         = self::checkDate($ngay_chia_checkbox, Url::get('ngay_chia_from'), Url::get('ngay_chia_to'));
        $ngay_xn           = self::checkDate($ngay_xn_checkbox, Url::get('ngay_xn_from'), Url::get('ngay_xn_to'));
        $ngay_chuyen_kt    = self::checkDate($ngay_chuyen_kt_checkbox, Url::get('ngay_chuyen_kt_from'), Url::get('ngay_chuyen_kt_to'));
        $ngay_thanh_cong   = self::checkDate($ngay_thanh_cong_checkbox, Url::get('ngay_thanh_cong_from'), Url::get('ngay_thanh_cong_to'));
        $ngay_thu_tien     = self::checkDate($ngay_thu_tien_checkbox, Url::get('ngay_thu_tien_from'), Url::get('ngay_thu_tien_to'));
        $ngay_chuyen       = self::checkDate($ngay_chuyen_checkbox, Url::get('ngay_chuyen_from'), Url::get('ngay_chuyen_to'));
        $ngay_chuyen_hoan  = self::checkDate($ngay_chuyen_hoan_checkbox, Url::get('ngay_chuyen_hoan_from'), Url::get('ngay_chuyen_hoan_to'));
        $ngay_tra_hang     = self::checkDate($ngay_tra_hang_checkbox, Url::get('ngay_tra_hang_from'), Url::get('ngay_tra_hang_to'));

        if ($ngay_tao_checkbox && !$ngay_tao) { 
            $mess = 'ngày tạo từ ' . $_REQUEST['ngay_tao_from'] . ' đến ' . $_REQUEST['ngay_tao_to'];
            unset($_REQUEST['ngay_tao_checkbox']); 
            unset($_REQUEST['ngay_tao_from']); 
            unset($_REQUEST['ngay_tao_to']); 
            return $mess;
        }
        if ($ngay_chia_checkbox && !$ngay_chia) {
            $mess = 'ngày chia ' . $_REQUEST['ngay_chia_from'] . ' đến ' . $_REQUEST['ngay_chia_to'];
            unset($_REQUEST['ngay_chia_checkbox']); 
            unset($_REQUEST['ngay_chia_from']); 
            unset($_REQUEST['ngay_chia_to']); 
            return $mess;
        }
        if ($ngay_xn_checkbox && !$ngay_xn) {
            $mess = 'ngày chốt ' . $_REQUEST['ngay_xn_from'] . ' đến ' . $_REQUEST['ngay_xn_to'];
            unset($_REQUEST['ngay_xn_checkbox']); 
            unset($_REQUEST['ngay_xn_from']); 
            unset($_REQUEST['ngay_xn_to']); 
            return $mess; 
        }
        if ($ngay_chuyen_kt_checkbox && !$ngay_chuyen_kt) { 
            $mess = 'ngày đóng hàng ' . $_REQUEST['ngay_chuyen_kt_from'] . ' đến ' . $_REQUEST['ngay_chuyen_kt_from'];
            unset($_REQUEST['ngay_chuyen_kt_checkbox']); 
            unset($_REQUEST['ngay_chuyen_kt_from']); 
            unset($_REQUEST['ngay_chuyen_kt_from']); 
            return $mess; 
        }
        if ($ngay_thanh_cong_checkbox && !$ngay_thanh_cong) { 
            $mess = 'ngày thành công ' . $_REQUEST['ngay_thanh_cong_from'] . ' đến ' . $_REQUEST['ngay_thanh_cong_to'];
            unset($_REQUEST['ngay_thanh_cong_checkbox']); 
            unset($_REQUEST['ngay_thanh_cong_from']); 
            unset($_REQUEST['ngay_thanh_cong_to']); 
            return $mess; 
        }
        if ($ngay_thu_tien_checkbox && !$ngay_thu_tien) { 
            $mess = 'ngày thu tiền ' . $_REQUEST['ngay_thu_tien_from'] . ' đến ' . $_REQUEST['ngay_thu_tien_to'];
            unset($_REQUEST['ngay_thu_tien_checkbox']); 
            unset($_REQUEST['ngay_thu_tien_from']); 
            unset($_REQUEST['ngay_thu_tien_to']); 
            return $mess; 
        }
        if ($ngay_chuyen_checkbox && !$ngay_chuyen) { 
            $mess = 'ngày chuyển ' . $_REQUEST['ngay_chuyen_from'] . ' đến ' . $_REQUEST['ngay_chuyen_to'];
            unset($_REQUEST['ngay_chuyen_checkbox']); 
            unset($_REQUEST['ngay_chuyen_from']); 
            unset($_REQUEST['ngay_chuyen_to']); 
            return $mess; 
        }
        if ($ngay_chuyen_hoan_checkbox && !$ngay_chuyen_hoan) { 
            $mess = 'ngày chuyển hoàn ' . $_REQUEST['ngay_chuyen_hoan_from'] . ' đến ' . $_REQUEST['ngay_chuyen_hoan_to'];
            unset($_REQUEST['ngay_chuyen_hoan_checkbox']); 
            unset($_REQUEST['ngay_chuyen_hoan_from']); 
            unset($_REQUEST['ngay_chuyen_hoan_to']); 
            return $mess; 
        }
        if ($ngay_tra_hang_checkbox && !$ngay_tra_hang) { 
            $mess = 'ngày trả hàng ' . $_REQUEST['ngay_tra_hang_from'] . ' đến ' . $_REQUEST['ngay_tra_hang_to'];
            unset($_REQUEST['ngay_tra_hang_checkbox']); 
            unset($_REQUEST['ngay_tra_hang_from']);
            unset($_REQUEST['ngay_tra_hang_to']); 
            return $mess;
        }
        
        $cond = '
            '.'orders.group_id IN ('.AdminOrders::$group_id.','.$group_ids.') 
            '.(($ngay_tao_checkbox && $ngay_tao)?' AND orders.created>="'.Date_Time::to_sql_date($ngay_tao[0]).' 00:00:00" AND orders.created<="'.Date_Time::to_sql_date($ngay_tao[1]).' 23:59:59"':'').'
            '.(($ngay_chia_checkbox && $ngay_chia)?' AND orders.assigned>="'.Date_Time::to_sql_date($ngay_chia[0]).' 00:00:00" AND orders.assigned<="'.Date_Time::to_sql_date($ngay_chia[1]).' 23:59:59"':'').'
            '.(($ngay_xn_checkbox && $ngay_xn)?' AND orders.confirmed>="'.Date_Time::to_sql_date($ngay_xn[0]).' 00:00:00" AND orders.confirmed<="'.Date_Time::to_sql_date($ngay_xn[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen_kt_checkbox && $ngay_chuyen_kt)?' AND orders_extra.accounting_confirmed>="'.Date_Time::to_sql_date($ngay_chuyen_kt[0]).' 00:00:00" AND orders_extra.accounting_confirmed<="'.Date_Time::to_sql_date($ngay_chuyen_kt[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen_checkbox && $ngay_chuyen)?' AND orders.delivered>="'.Date_Time::to_sql_date($ngay_chuyen[0]).' 00:00:00" AND orders.delivered<="'.Date_Time::to_sql_date($ngay_chuyen[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen_hoan_checkbox && $ngay_thu_tien)?' AND orders_extra.update_paid_time>="'.Date_Time::to_sql_date($ngay_thu_tien[0]).' 00:00:00" AND orders_extra.update_paid_time<="'.Date_Time::to_sql_date($ngay_thu_tien[1]).' 23:59:59"':'').'
            '.(($ngay_thanh_cong_checkbox && $ngay_thanh_cong)?' AND orders_extra.update_successed_time>="'.Date_Time::to_sql_date($ngay_thanh_cong[0]).' 00:00:00" AND orders_extra.update_successed_time<="'.Date_Time::to_sql_date($ngay_thanh_cong[1]).' 23:59:59"':'').'
            '.(($ngay_thu_tien_checkbox && $ngay_chuyen_hoan)?' AND orders_extra.update_returned_time>="'.Date_Time::to_sql_date($ngay_chuyen_hoan[0]).' 00:00:00" AND orders_extra.update_returned_time<="'.Date_Time::to_sql_date($ngay_chuyen_hoan[1]).' 23:59:59"':'').'
            '.(($ngay_tra_hang_checkbox && $ngay_tra_hang)?' AND orders_extra.update_returned_to_warehouse_time>="'.Date_Time::to_sql_date($ngay_tra_hang[0]).' 00:00:00" AND orders_extra.update_returned_to_warehouse_time<="'.Date_Time::to_sql_date($ngay_tra_hang[1]).' 23:59:59"':'').'
        ';

        if (!$ngay_tao && !$ngay_chia && !$ngay_xn && !$ngay_chuyen_kt && !$ngay_thanh_cong && !$ngay_thu_tien && !$ngay_chuyen && !$ngay_chuyen_hoan && !$ngay_tra_hang) {
            $cond = '
                '.'orders.group_id IN ('.AdminOrders::$group_id.','.$group_ids.')
                '.' AND orders.created>="'.date('Y/m/01', strtotime('-1 month')).' 00:00:00"'.'
                '.' AND orders.created<="'.date('Y/m/d').' 23:59:59"'.'
            ';
        }

        return $cond;
    }

    private function checkDate($checkbox, $from_date, $to_date) {
        if ($checkbox && Date_Time::to_sql_date($from_date) && Date_Time::to_sql_date($to_date)) {

            $from_date = strtotime(str_replace('/','-', $from_date));
            $to_date = strtotime(str_replace('/','-', $to_date));

            if ($from_date > $to_date) {
                return false;
            }
            if ($to_date - $from_date > 5356800) {
                return false;
            }
            $from_date = date('d/m/Y', $from_date);
            $to_date = date('d/m/Y', $to_date);
            return array($from_date, $to_date);
        }
        return false;
    }

    private function mapFilterDates()
    {
        $fromFieldsName = [
            'ngay_tao_from',
            'ngay_chia_from',
            'ngay_xn_from',
            'ngay_chuyen_from',
            'ngay_chuyen_kt_from',
            'ngay_thanh_cong_from',
            'ngay_thu_tien_from',
            'ngay_chuyen_hoan_from',
            'ngay_tra_hang_from',
        ];

        array_map(function($fieldName){
            $_REQUEST[$fieldName] = URL::getDateTimeFmt($fieldName, 'd/m/Y', 'd/m/Y', date('d/m/Y', strtotime('-3 day')));
        }, $fromFieldsName);

        $toFieldsName = [
            'ngay_tao_to',
            'ngay_chia_to',
            'ngay_xn_to',
            'ngay_chuyen_to',
            'ngay_chuyen_kt_to',
            'ngay_thanh_cong_to',
            'ngay_thu_tien_to',
            'ngay_chuyen_hoan_to',
            'ngay_tra_hang_to',
        ];

        $currentDay = date('d/m/Y');
        array_map(function($fieldName) use($currentDay){
            $_REQUEST[$fieldName] = URL::getDateTimeFmt($fieldName, 'd/m/Y', 'd/m/Y', $currentDay);
        }, $toFieldsName);
    }

    private function array2csv($total, $num, $cond)
    {
        ob_clean();
        ob_start();
        $df = fopen("php://output", 'w');
        fputs( $df, "\xEF\xBB\xBF" ); // UTF-8 BOM !!!!!
        fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($df, array_values(self::$header));

        $per = 20000;
        $skip = ($num - 1) * $per;

        $items = self::get_items($cond, $skip, $per);
        foreach ($items as $row) {
            $row['type'] = $row['type'] ? AdminOrders::$type[$row['type']] : '';
            $row['created'] = ($row['created'] && $row['created'] != '0000-00-00 00:00:00') ? $row['created'] : '';
            $row['confirmed'] = ($row['confirmed'] && $row['confirmed'] != '0000-00-00 00:00:00') ? $row['confirmed'] : '';
            $row['delivered'] = ($row['delivered'] && $row['delivered'] != '0000-00-00 00:00:00') ? $row['delivered'] : '';
            $row['accounting_confirmed'] = ($row['accounting_confirmed'] && $row['accounting_confirmed'] != '0000-00-00 00:00:00') ? $row['accounting_confirmed'] : '';
            $row['update_successed_time'] = ($row['update_successed_time'] && $row['update_successed_time'] != '0000-00-00 00:00:00') ? $row['update_successed_time'] : '';
            $row['update_paid_time'] = ($row['update_paid_time'] && $row['update_paid_time'] != '0000-00-00 00:00:00') ? $row['update_paid_time'] : '';
            $row['update_returned_time'] = ($row['update_returned_time'] && $row['update_returned_time'] != '0000-00-00 00:00:00') ? $row['update_returned_time'] : '';
            $row['update_returned_to_warehouse_time'] = ($row['update_returned_to_warehouse_time'] && $row['update_returned_to_warehouse_time'] != '0000-00-00 00:00:00') ? $row['update_returned_to_warehouse_time'] : '';
            //
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    private function array2csv_product($total, $num, $cond)
    {
        ob_clean();
        ob_start();
        $df = fopen("php://output", 'w');
//        fputs( $df, "\xEF\xBB\xBF" ); // UTF-8 BOM !!!!!
        fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($df, array_values(self::$header_product));

        $per = 20000;
        $skip = ($num - 1) * $per;

        $items = self::get_items_product($cond, $skip, $per);
        foreach ($items as $row) {
            unset($row['op_id']);

            $row['product_code'] = "'".$row['product_code'];
            $row['type'] = $row['type'] ? AdminOrders::$type[$row['type']] : '';
            $row['created'] = ($row['created'] && $row['created'] != '0000-00-00 00:00:00') ? $row['created'] : '';
            $row['confirmed'] = ($row['confirmed'] && $row['confirmed'] != '0000-00-00 00:00:00') ? $row['confirmed'] : '';
            $row['delivered'] = ($row['delivered'] && $row['delivered'] != '0000-00-00 00:00:00') ? $row['delivered'] : '';
            $row['accounting_confirmed'] = ($row['accounting_confirmed'] && $row['accounting_confirmed'] != '0000-00-00 00:00:00') ? $row['accounting_confirmed'] : '';
            $row['update_successed_time'] = ($row['update_successed_time'] && $row['update_successed_time'] != '0000-00-00 00:00:00') ? $row['update_successed_time'] : '';
            $row['update_paid_time'] = ($row['update_paid_time'] && $row['update_paid_time'] != '0000-00-00 00:00:00') ? $row['update_paid_time'] : '';
            $row['update_returned_time'] = ($row['update_returned_time'] && $row['update_returned_time'] != '0000-00-00 00:00:00') ? $row['update_returned_time'] : '';
            $row['update_returned_to_warehouse_time'] = ($row['update_returned_to_warehouse_time'] && $row['update_returned_to_warehouse_time'] != '0000-00-00 00:00:00') ? $row['update_returned_to_warehouse_time'] : '';
            //
            if ($row['discount_amount']) {
                $row['discount_rate'] = $row['product_price'] * $row['product_qty'] - $row['discount_amount'];
            } elseif ($row['discount_rate']) {
                $row['discount_amount'] = round($row['product_price'] * $row['product_qty'] * ($row['discount_rate'] / 100));
                $row['discount_rate'] = $row['product_price'] * $row['product_qty'] - $row['discount_amount'];
            } else {
                $row['discount_rate'] = $row['product_price'] * $row['product_qty'];
            }
            //
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    private function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=UTF-8');
    }
}
