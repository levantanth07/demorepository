<?php
class ViewReportMonthForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('ViewReportMonthForm');
    }
    function draw(){
        $imageDefault = DashboardDB::getDefaultAvatar();
        $arrayAvatar = DashboardDB::getArrayAvatar();
        $current_user_id = Dashboard::$user_id;
        $startDay = date('Y-m-01');
        $today = date('Y-m-d');
        $users = DashboardDB::get_user_report('GANDON');
        $userMarketing = DashboardDB::get_user_report('MARKETING');
        
        $strStatus = "('" . implode("','", array_column(DashboardDB::get_revenue_status_update(), 'id')) . "')";
        
        $arrUserSale = [];
        $arrUserMarketing = [];
        foreach ($users as $key => $value) {
            if(!empty($value['is_active'])){
                $arrUserSale[] = $value['user_id'];
            }
            
        }
        foreach ($userMarketing as $key => $value) {
            if(!empty($value['is_active'])){
                $arrUserMarketing[] = $value['user_id'];
            }
        }
        $strUserSale = "('" . implode("','", $arrUserSale) . "')";
        $strUserMarketing = "('" . implode("','", $arrUserMarketing) . "')";
        $sqlSale = "SELECT 
                            sum(total_price) as total,
                            count(id) as qty,
                            orders.user_confirmed as user_confirmed
                        FROM 
                            orders
                        WHERE 
                            orders.user_confirmed IN $strUserSale
                        AND 
                            orders.status_id IN $strStatus
                        AND
                            date(orders.confirmed) <= '".$today."' AND date(orders.confirmed) >= '".$startDay."'
                        GROUP BY orders.user_confirmed
                    ";
        $orderSale = DB::fetch_all_array($sqlSale);
        $sales = [];
        foreach ($users as $kUser => $valUser) {
            $qtySale = 0;
            $priceSale = 0;
            foreach ($orderSale as $kSale => $valSale) {
                if (!empty($valUser['is_active'])) {
                    if ($valSale['user_confirmed'] == $valUser['user_id'] ) {
                        $qtySale = $valSale['qty'];
                        $priceSale = round($valSale['total']/1000000,2);
                        $sales[$valUser['user_id']]['qty'] = $qtySale;
                        $sales[$valUser['user_id']]['total'] = $priceSale;
                        $sales[$valUser['user_id']]['name'] = $valUser['full_name'];
                        $sales[$valUser['user_id']]['avatar'] = $valUser['avatar']?$valUser['avatar']:$imageDefault;
                    } else {
                        $sales[$valUser['user_id']]['qty'] = $qtySale;
                        $sales[$valUser['user_id']]['total'] = $priceSale;
                        $sales[$valUser['user_id']]['name'] = $valUser['full_name'];
                        $sales[$valUser['user_id']]['avatar'] = $valUser['avatar']?$valUser['avatar']:$imageDefault;
                    }
                } 
            }
        }
        $sqlMarketing = "SELECT 
                            sum(total_price) as total,
                            count(id) as qty,
                            orders.user_created as user_created
                        FROM 
                            orders
                        WHERE 
                            orders.user_created IN $strUserMarketing
                        AND 
                            orders.status_id IN $strStatus
                        AND
                            date(orders.confirmed) <= '".$today."' AND date(orders.confirmed) >= '".$startDay."'
                        GROUP BY orders.user_created";
        $orderMarketing = DB::fetch_all_array($sqlMarketing);
        $marketing = [];
        foreach ($userMarketing as $kUserMkt => $valUserMkt) {
            $qtyMarketing = 0;
            $priceMrketing = 0;
            foreach ($orderMarketing as $kMarketing => $valMarketing) {
                if(!empty($valUserMkt['is_active'])){
                    if ($valMarketing['user_created'] == $valUserMkt['user_id'] ) {
                        $qtyMarketing = $valMarketing['qty'];
                        $priceMrketing = round($valMarketing['total']/1000000,2);
                        $marketing[$valUserMkt['user_id']]['qty'] = $qtyMarketing;
                        $marketing[$valUserMkt['user_id']]['total'] = $priceMrketing;
                        $marketing[$valUserMkt['user_id']]['name'] = $valUserMkt['full_name'];
                        $marketing[$valUserMkt['user_id']]['avatar'] = $valUserMkt['avatar']?$valUserMkt['avatar']:$imageDefault;
                    } else {
                        $marketing[$valUserMkt['user_id']]['qty'] = $qtyMarketing;
                        $marketing[$valUserMkt['user_id']]['total'] = $priceMrketing;
                        $marketing[$valUserMkt['user_id']]['name'] = $valUserMkt['full_name'];
                        $marketing[$valUserMkt['user_id']]['avatar'] = $valUserMkt['avatar']?$valUserMkt['avatar']:$imageDefault;
                    }
                }
            }
        }
        if(sizeof($sales)>=2){
            System::sksort($sales, 'total','DESC');
        }
        if(sizeof($marketing)>=2){
            System::sksort($marketing, 'total','DESC');
        }
        $this->map['sales'] = $sales;
        $this->map['marketing'] = $marketing;
        $this->map['arrayAvatar'] = $arrayAvatar;
        $this->parse_layout('view_report_month',$this->map);
    }
}
?>