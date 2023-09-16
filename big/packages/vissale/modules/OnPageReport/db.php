<?php
class OnPageReportDb
{
    public static function getBundles() {
        $sql = "SELECT `id`, `name`
            FROM `bundles`
            WHERE `standardized` = 1 
                AND `ref_id` = 0 
                AND `group_id` = 0 
            ORDER BY `name`";

        $result = DB::fetch_all($sql);
        return $result;
    }

    function get_bundles(){
        $sql = '
                select
                    bundles.id,bundles.name
                from
                    bundles
                WHERE
                    bundles.group_id = '.Session::get('group_id').'
                order by
                    bundles.name
            ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    function get_groups(){
        return DB::fetch_all('select id,name from `groups` where master_group_id='.Session::get('group_id').' order by name');
    }

    function getPageAssigneeReportByDate($dattime_range, $group_id)
    {
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        /*if($account_type==3){//khoand edited in 30/09/2018
            $cond = ' (orders.group_id='.$group_id.' or orders.master_group_id = '.$group_id.')';
            $r2_cond = ' (R_2.group_id='.$group_id.' or groups.master_group_id = '.$group_id.')';
        }elseif($master_group_id){
            $cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.'))';
            $r2_cond = ' (R_2.group_id='.$group_id.' or (groups.master_group_id = '.$master_group_id.'))';
        }else{
            $cond = ' orders.group_id='.$group_id.'';
            $r2_cond = ' R_2.group_id='.$group_id.'';
        }*/
        if($account_type==3){
            $cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.Session::get('group_id').'))';
        }else{
            $cond = ' (orders.group_id='.$group_id.')';
        }
        if($bundle_id = Url::iget('bundle_id')){
            if (isObd()) {
                $_bundleIds = self::getIncludeBundleIds($bundle_id, $group_id);
                $cond .= " and orders.bundle_id IN ($_bundleIds)";
            } else {
                $cond .= ' and orders.bundle_id='.$bundle_id.'';
            }
        }
        $r2_cond = ' R_2.group_id='.$group_id.'';
        $sql = '
            SELECT
                * 
            FROM
                users AS R_2
                LEFT JOIN (
                SELECT
                    A_1.user_assigned AS user_assigned,
                    A_1.all_number AS all_number,
                    A_2.duplicated AS duplicated,
                    E_2.cancel_count AS cancel_count 
                FROM
                    (
                        SELECT
                            Count( `user_assigned` ) AS all_number,
                            user_assigned 
                        FROM
                            (
                            SELECT
                                Count( `mobile` ) AS duplicate_number,
                                user_assigned 
                            FROM
                                orders
                            WHERE
                                '.$cond.'
                                AND 
                                assigned >=  "'.$dattime_range['from'].'" 
                                AND 
                                 assigned <=  "'.$dattime_range['to'].'"
                            GROUP BY
                                mobile,
                                user_assigned 
                            HAVING
                                Count( `mobile` ) > 0 
                            ) AS A 
                        GROUP BY
                            A.user_assigned 
                    ) AS A_1
                    LEFT JOIN (
                        SELECT
                            Count( `user_assigned` ) AS duplicated,
                            user_assigned 
                        FROM
                            (
                            SELECT
                                Count( `mobile` ) AS duplicate_number,
                                user_assigned 
                            FROM
                                orders
                                left join order_revisions on order_revisions.order_id = orders.id
                            WHERE
                                '.$cond.'
                                AND 
                                assigned >=  "'.$dattime_range['from'].'" 
                                AND 
                                 assigned <=  "'.$dattime_range['to'].'"
                                 AND 
                                 order_revisions.created<="'.$dattime_range['from'].'" and order_revisions.order_status_id
                            GROUP BY
                                mobile,
                                user_assigned 
                            HAVING
                                Count( `mobile` ) > 0
                            ) AS A 
                        GROUP BY
                            A.user_assigned 
                    ) AS A_2 ON A_1.user_assigned = A_2.user_assigned
                    LEFT JOIN (
                        SELECT
                            Count( `user_assigned` ) AS cancel_count,
                            user_assigned 
                        FROM
                            (
                            SELECT
                                Count( `mobile` ) AS duplicate_number_2,
                                user_assigned 
                            FROM
                                orders 
                            WHERE
                               '.$cond.'
                                AND 
                                status_id = 5 
                                AND 
                                assigned >=  "'.$dattime_range['from'].'" 
                                AND 
                                 assigned <=  "'.$dattime_range['to'].'"
                            GROUP BY
                                mobile,
                                user_assigned 
                            ) AS A 
                        GROUP BY
                            A.user_assigned 
                    ) AS E_2 ON A_1.user_assigned = E_2.user_assigned 
                ) AS R_1 ON R_1.user_assigned = R_2.id 
            WHERE
               '.$r2_cond.'
                
        ';
        if(System::get_client_ip_env()=='171.241.160.60'){
            System::debug($sql);
        }
        $items = DB::fetch_all($sql);
        $users = $this->get_users();
        $result = [];
        if(Session::get('user_id')=='zm.khoatest'){
            // System::debug($items);
        }
        foreach($items as $item ) {
            if(isset($users[$item['id']]) and $users[$item['id']]) {
                $result[] = $item;
            }
        }
        // var_dump($result); exit;
        return ($result);

    }
    function get_users(){
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        if($account_type==3){//khoand edited in 30/09/2018
            $cond = ' (groups.id='.$group_id.' or groups.master_group_id = '.$group_id.')';
        }elseif($master_group_id){
            $cond = ' (groups.id='.$group_id.' or (groups.master_group_id = '.$master_group_id.'))';
        }else{
            $cond = ' groups.id='.$group_id.'';
        }
        $sql = '
            SELECT
              users.id 
            FROM
             users
                INNER JOIN account ON account.id = users.username
                inner join `groups` on groups.id = account.group_id
                INNER JOIN users_roles ON users_roles.user_id = users.id
                INNER JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
        WHERE
                '.$cond.'
                AND account.is_active
                AND roles_to_privilege.privilege_code="GANDON"
        ';
        return DB::fetch_all($sql);
    } 
    /**
     * lấy danh sách id nhóm sản phẩm (Rule chuẩn hóa)
     *
     * @param string $ids
     * @param integer $groupId
     * @return string
     */
    public static function getIncludeBundleIds(string $ids, int $groupId): string {
        $ids = DataFilter::removeXSSinListIds($ids);
        $arrayIds = self::handleGetIncludeBundleIds($ids);
        if (!$arrayIds) {
            return $ids;
        }//end if

        $idsOfGroup = self::getBundleIdsOfGroup($groupId);
        $_ids = self::mapIncludeIds($arrayIds, $idsOfGroup);
        if ($_ids) {
            $ids .= ',' . $_ids;
        }//end if

        return $ids;
    }

    /**
     * Lấy danh sách Id nhóm sản phẩm cũ của HKD
     *
     * @param string $bundleIds
     * @return array
     */
    private static function handleGetIncludeBundleIds(string $bundleIds): array {
        $sql = "SELECT `id`, `include_ids`
            FROM `bundles`
            WHERE `id` IN ($bundleIds)
                AND `ref_id` = 0
                AND `group_id` = 0
                AND `standardized` = 1
        ";

        return DB::fetch_all_column($sql, 'include_ids');
    }

    /**
     * map danh sách id hợp lệ
     *
     * @param array $ids
     * @param array $idsOfGroup
     * @return string
     */
    private static function mapIncludeIds (array $ids, array $idsOfGroup): string {
        $ids = implode(',', $ids);
        $ids = explode(',', $ids);
        $ids = array_intersect($ids, $idsOfGroup);
        return implode(',', $ids);
    }

    /**
     * Lấy danh sách Id nhóm sản phẩm cũ tự tạo của HKD
     *
     * @param integer $groupId
     * @return array
     */
    private static function getBundleIdsOfGroup(int $groupId): array {
        $sql = "SELECT id 
            FROM bundles 
            WHERE group_id = $groupId
        ";
        return DB::fetch_all_column($sql);
    }
}
