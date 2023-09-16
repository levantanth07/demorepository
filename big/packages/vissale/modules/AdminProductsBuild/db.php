<?php

class AdminProductsBuildDB
{
    static function save_item_image($file, $id)
    {
        require_once('packages/core/includes/utils/ftp.php');
        if (isset($_FILES[$file]) and $_FILES[$file]) {
            $image_url = FTP::upload_file($file, 'upload/default', true);
            if ($image_url) {
                DB::update('products', array('image_url' => $image_url), 'id=' . $id);
            }
        }
    }
    static function getProductsList($cond, $item_per_page)
    {
        $searchQuery = static::generateSearchQuery();
        $limit = (page_no() - 1) * $item_per_page;
        $sql = "
            SELECT 
                products.id, products.code, 
                products.name, products.import_price, products.price,
                products.image_url,
                products.total_order,
                -- count(orders_products.product_id) as total_order,
                color, `size`, products.weight,
                products.unit_id, 
                products.bundle_id, 
                products.label_id, 
                del, 
                bundles.name AS bundles_name, 
                labels.name AS label_name, 
                units.name AS units_name,
                products.standardized,
                master_product.updated_at AS updated_at,
                products.master_updated_at
            FROM products
            -- LEFT JOIN orders_products ON orders_products.product_id = products.id
            LEFT JOIN bundles ON bundles.id = products.bundle_id
            LEFT JOIN units ON units.id = products.unit_id
            LEFT JOIN labels ON labels.id = products.label_id
            LEFT JOIN master_product ON master_product.code = products.code
            WHERE $cond $searchQuery
            ORDER BY products.id  DESC
            LIMIT $limit, $item_per_page
        ";

        return DB::fetch_all($sql);
    }

    static function get_total_product($cond)
    {
        $searchQuery = static::generateSearchQuery();
        return DB::fetch("
            SELECT COUNT(products.id) AS total
            FROM products
            LEFT JOIN bundles ON bundles.id = products.bundle_id
            LEFT JOIN units ON units.id = products.unit_id
            WHERE $cond $searchQuery
            ORDER BY products.id DESC
        ", 'total');
    }

    static function generateSearchQuery()
    {
        $query = "";
        if (URL::get('search_text')) {
            $keyword = DB::escape(DataFilter::removeDuplicatedSpaces(URL::get('search_text')));
            $query .= " AND (products.code LIKE '%" . $keyword . "%' OR products.name LIKE '%" . $keyword . "%')";
        }

        if ($bundle_id = DB::escape(URL::get('bundle_id'))) {
            $sqlGetIncludeIds = "SELECT id, include_ids FROM bundles WHERE id = $bundle_id";
            $includeIds = DB::fetch($sqlGetIncludeIds, 'include_ids');
            if (!$includeIds) {
                $query .= " AND products.bundle_id = $bundle_id";
            } else {
                $_includeIds = explode(',', $includeIds);
                $_includeIds[] = $bundle_id;
                $_includeIds = DB::escapeArray($_includeIds);
                $includeIds = implode(',', $_includeIds);
                $query .=  " AND products.bundle_id in ($includeIds)";
            }//end if
        }//end if

        if ($unit_id = URL::get('unit_id')) {
            $query .= " AND products.unit_id = $unit_id";
        }

        if ($label_id = URL::get('label_id')) {
            $query .= " AND products.label_id = $label_id";
        }

        if ($del = URL::get('del')) {
            $query .= " AND del = $del";
        }

        self::setStandardizedCondition($query);

        return $query;
    }

    /**
     * Sets the standardized condition.
     *
     * @param      string  $query  The query
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private static function setStandardizedCondition(string &$query)
    {
        if (!isset($_REQUEST['standardized']) || $_REQUEST['standardized'] == -1) {
            return;
        }

        if ($_REQUEST['standardized'] == 0) {
            return $query .= " AND (products.standardized = 0 OR products.standardized IS NULL)";
        }

        if ($_REQUEST['standardized'] == 1) {
            return $query .= " AND products.standardized = 1";
        }
    }

    /**
     * Counts the number of product for updated.
     *
     * @param      int   $groupID  The group id
     *
     * @return     int   Number of product for updated.
     */
    public static function countProductForUpdated(int $groupID)
    {
        // lấy ra các sản phầm có master_updated_at khác với master_product.updated_at
        $sql = "
            SELECT 
                count(*) AS count 
            FROM products p
            JOIN master_product mp ON mp.code = p.code
            WHERE 
                p.group_id = $groupID
                AND (
                    (mp.updated_at IS NOT NULL AND p.master_updated_at IS NULL)
                    OR (mp.updated_at IS NOT NULL AND p.master_updated_at IS NOT NULL AND mp.updated_at != p.master_updated_at)
                )
        ";

        return (int) DB::fetch($sql, 'count');
    }

    /**
     * getBundles function
     *
     * @return array
     */
    static function getBundles(): array
    {
        $query = "SELECT id, name
            FROM master_bundle
            WHERE parent_id != 0 AND status = 1
        ";

        return DB::fetch_all($query);
    }

    /**
     * getSystemBundles function
     *
     * @return array
     */
    static function getSystemBundles(): array
    {
        $sql = "SELECT `id`, `name`, `standardized`
            FROM  bundles
            WHERE group_id = 0 AND standardized = 1
            ORDER BY `name`";

        return DB::fetch_all($sql);
    }

    /**
     * getShopBundles function
     *
     * @return array
     */
    static function getShopBundles(): array
    {
        $groupId = Session::get('group_id');
        $sql = "SELECT `id`, `name`, `standardized`
            FROM  bundles
            WHERE  `group_id` = $groupId OR (`group_id` = 0 AND `standardized` = 1)
            ORDER BY `name`";

        return DB::fetch_all($sql);
    }

    /**
     * getShopUnits function
     *
     * @return array
     */
    static function getShopUnits(): array
    {
        $groupId = Session::get('group_id');
        $sql = "SELECT *
            FROM units
            WHERE group_id= $groupId
            ORDER BY `name`";

        return DB::fetch_all($sql);
    }
}
