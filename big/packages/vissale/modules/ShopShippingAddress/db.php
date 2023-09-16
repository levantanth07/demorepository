<?php

class ShopShippingAddressDB
{

    static function checkPermission(){
        if (Session::get('admin_group') || is_group_owner() || Menu::$quyen_ke_toan || Menu::$quyen_admin_ke_toan) {
            return true;
        }
        return false;
    }
    static function getProvinces()
    {
        return DB::fetch_all("SELECT province_id AS id, province_name AS name FROM zone_provinces_v2 AS provinces ORDER BY province_id ASC");
    }

    static function getShippingOptionsEms()
    {
        $groupId = Session::get('group_id');
        $results = DB::fetch_all("
            SELECT so.id, so.name, so.token
            FROM shipping_options AS so
            WHERE group_id = $groupId AND carrier_id = 'api_ems'  AND del = 0
            ORDER BY is_default DESC
        ");

        return $results;
    }

    // Lấy ra danh sách tài khoản vận chuyển liên quan đến địa chỉ
    static function getShippingOptionsEmsAll()
    {
        $groupId = Session::get('group_id');
        $results = DB::fetch_all("
            SELECT so.id, so.name, so.token, sae.shipping_address_id
            FROM shipping_options AS so
            LEFT JOIN shipping_address_ems AS sae ON sae.shipping_option_id =  so.id
            WHERE group_id = $groupId AND carrier_id = 'api_ems' AND del = 0
            ORDER BY is_default DESC
        ");

        return $results;
    }

    static function getDistrictsByProvinceId($province_id)
    {
        return DB::fetch_all("SELECT district_id AS id, district_name AS name FROM zone_districts_v2 AS districts WHERE province_id = '$province_id' ORDER BY id ASC");
    }

    static function getWardsByDistrictId($district_id)
    {
        return DB::fetch_all("SELECT ward_id as id, ward_name AS name FROM zone_wards_v2 AS wards WHERE district_id = '$district_id' ORDER BY id ASC");
    }

    static function getZonesOriginal()
    {
        return static::getZones(ID_ROOT);
    }

    static function getZones($structure_id)
    {
        $cond = IDStructure::direct_child_cond($structure_id).' AND structure_id <> ' . ID_ROOT;
        $zones = DB::fetch_all('SELECT id, name_id, structure_id, zone.name AS name FROM zone WHERE '.$cond.' ORDER BY structure_id');

        return $zones;
    }

    static function getZonesById($id)
    {
        try {
            $id = DB::escape($id);
            $structure_id = DB::fetch("SELECT structure_id FROM zone WHERE id='$id'", 'structure_id');
            $zones = static::getZones($structure_id);

            return $zones;
        } catch (Exception $e) {
            return false;
        }
    }

    static function getGroupInfo($group_id)
    {
        return DB::fetch("SELECT name, phone from `groups` WHERE id = '$group_id'");
    }

    static function getShippingAddress($group_id)
    {
        $sql = "
            SELECT
                ssa.id, ssa.name, ssa.phone, ssa.address, ssa.is_default,
                provinces.province_name, districts.district_name, wards.ward_name, ems_warehouse_id
            FROM shop_shipping_address AS ssa
            LEFT JOIN zone_provinces_v2 AS provinces ON provinces.province_id = ssa.zone_id
            LEFT JOIN zone_districts_v2 AS districts ON districts.district_id = ssa.district_id
            LEFT JOIN zone_wards_v2 AS wards ON wards.ward_id = ssa.ward_id
            WHERE ssa.group_id = $group_id
            ORDER BY ssa.is_default DESC, ssa.id DESC
        ";
        $results = DB::fetch_all($sql);

        return $results;
    }

    static function getShippingAddressById($id)
    {
        $id = DB::escape($id);
        return DB::fetch("SELECT * FROM shop_shipping_address WHERE id = '$id'");
    }

    static function getTotalAddressByGroupId($group_id)
    {
        return DB::fetch("SELECT COUNT(id) AS total FROM shop_shipping_address WHERE group_id = '$group_id'", 'total');
    }
}
