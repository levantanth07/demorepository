<?php
define('KHOAN_CHI', 0);
define('KHOAN_THU', 1);

class SoQuyDB
{
    static function get_groups($group_id){
        return DB::fetch_all('
			SELECT
				groups.id,groups.name
			FROM
                `groups`
			WHERE
				groups.master_group_id = '.$group_id.'
		');
    }
    static function getSoQuy($cond)
    {
        $sql = "
            SELECT cfd.id, cfd.amount, cfd.description AS note, cf.bill_number, cf.created_time, cf.bill_date, cfd.payment_method AS payment_type, cf.bill_type
            FROM cash_flow_detail AS cfd
            LEFT JOIN cash_flow AS cf ON cf.id = cfd.cash_flow_id
            WHERE cf.del = 0 $cond
            ORDER BY cf.bill_date ASC, cf.bill_number ASC, cfd.id ASC
        ";
        $results = DB::fetch_all($sql);

        return $results;
    }

    /*
     *
     * @param string $payment_type: Phương thức thanh toán 1 => 'Tiền mặt', 2 => 'Chuyển khoản', 3 => 'Thẻ'
     */
    static function getTonQuy($from_date = '', $end_date = '', $payment_type = '')
    {
        $cond = "";
        $end_date = !empty($end_date) ? $end_date : date('Y-m-d');
        $cond .= " AND cf.bill_date < '$end_date'";
        if (!empty($payment_type) && $payment_type!=4) {
            $cond .= " AND cfd.payment_method = $payment_type";
        }

        $total = 0;
        if (!empty($from_date)) {
            $cond .= " AND cf.bill_date < '$from_date'";
            $group_id = Url::iget('group_id') ? Url::iget('group_id') : Session::get('group_id');
            $sql = "
                SELECT cfd.id, cfd.amount, cf.bill_type
                FROM cash_flow_detail AS cfd
                LEFT JOIN cash_flow AS cf ON cf.id = cfd.cash_flow_id
                WHERE cf.del = 0 AND cf.group_id = '$group_id' $cond
                ORDER BY cf.bill_date ASC, cfd.id ASC
            ";

            $results = DB::fetch_all($sql);
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result['bill_type'] == KHOAN_THU) {
                        $total += $result['amount'];
                    } else {
                        $total -= $result['amount'];
                    }
                }
            }
        }

        return $total;
    }
}