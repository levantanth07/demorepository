<?php
class ProductHelper
{
    const TEMP_DIR_PREFIX = 'upload/master_product';

    public function __construct()
    {
        require_once ROOT_PATH . 'packages/core/includes/common/Systems.php';
    }


    private static function getMasterProductAccounts() {
        return defined('MASTER_PRODUCT_ACCOUNTS') ? MASTER_PRODUCT_ACCOUNTS : [];
    }

    public static function hasPrivilegeOnMasterProduct() {
        $masterProductAccounts = self::getMasterProductAccounts();
        return in_array(Session::get('user_id'), $masterProductAccounts);
    }

    /**
     * { function_description }
     *
     * @return     array
     */
    public function mapRequest()
    {
        $product = [];

        if(!empty($_FILES['image']['name'])){
            $product['image'] = $this->uploadImage();
        }

        $product['name'] = DataFilter::removeDuplicatedSpaces(URL::getString('name'));
        $product['full_name'] = mb_ucfirst(DataFilter::removeDuplicatedSpaces( URL::getString('full_name')));
        $product['code'] = URL::getString('code', '');
        $product['status'] = URL::getInt('status');
        $product['factory_product_code'] = URL::getString('factory_product_code');
        // $product['bundle_name'] = mb_ucfirst($this->trim(URL::getString('bundle_name')), 'utf-8');
        $product['unit_name'] = mb_ucfirst($this->trim(URL::getString('unit_name')), 'utf-8');
        $product['label_name'] = mb_ucfirst($this->trim(URL::getString('label_name')), 'utf-8');
        $product['cost_price'] = URL::getUIntFormated('cost_price');
        $product['weight'] = URL::getUIntFormated('weight');
        $product['note'] = URL::getString('note');
        $product['bundle_id'] = xgetUrl('bundle_id', 0, 'i');
        
        if(!$product['bundle_id']){
            throw new Exception('Nhóm sản phẩm trống !');
        }
        // if(!$product['bundle_name'] || !$product['unit_name']){
        //     throw new Exception('Lỗi thông tin sản phẩm !');
        // }

        $system = isset($_POST['system']) && is_array($_POST['system']) ? $_POST['system'] : [];

        return [$product, $system];
    }

    /**
     * { function_description }
     *
     * @param      string  $text   The text
     *
     * @return     <type>
     */
    public function trim(string $text)
    {
        return preg_replace('#\s+#', ' ', $text);
    }

    /**
     * Uploads images.
     */
    public function uploadImage(){
        $this->validateImageUpload();
        require_once('packages/core/includes/utils/ftp.php');
        if($image_url = FTP::upload_file('image', self::TEMP_DIR_PREFIX, true,'content', 'IMAGE', false)) {
            return $image_url;
        }
    }

    /**
     * { function_description }
     */
    public function validateImageUpload()
    {
        if(!$_FILES['image']['name'] || $_FILES['image']['size'] <= 0){
            throw new Exception('Ảnh tải lên không hợp lệ');
        }

        if($_FILES['image']['size'] > 1024 * 1024){
            throw new Exception('Vui lòng upload ảnh có dung lượng nhỏ hơn 1MB');
        }
    }

    /**
     * Gets the product for update.
     *
     * @param      int     $groupID  The group id
     * @param      array   $selects  The selects
     *
     * @return     <type>  The product for update.
     */
    public function getProductsForUpdate(int $groupID)
    {
        $fmt = '
            SELECT 
                p.id,
                mp.name,
                master_unit.name as master_unit_name, 
                master_bundle.name as master_bundle_name,
                IF(mp.status = 1, 0, 1) as del, 
                labels.name as label_name,
                mp.label_id,
                mp.weight as weight,
                mp.cost_price as import_price,
                mp.updated_at AS master_updated_at
            FROM `products` p 
            JOIN master_product mp ON mp.code = p.code
            LEFT JOIN master_unit ON master_unit.id = mp.unit_id
            LEFT JOIN master_bundle ON master_bundle.id = mp.bundle_id
            LEFT JOIN labels ON labels.id = mp.label_id
            WHERE 
                p.group_id = %d
                AND (
                    (mp.updated_at IS NOT NULL AND p.master_updated_at IS NULL)
                    OR (mp.updated_at IS NOT NULL AND p.master_updated_at IS NOT NULL AND mp.updated_at != p.master_updated_at)
                )';
        $sql = sprintf($fmt, $groupID);

        return DB::fetch_all($sql);
    }

    /**
     * { function_description }
     *
     * @param      int     $id     The identifier
     *
     * @return     <type>
     */
    public function _getMasterProduct(string $column, $value, array $selects = [])
    {
        if(!$selects){
            $selects = [
                'master_product.*',
                'labels.name as label_name',
                'labels.id as label_id',
                'master_unit.name as master_unit_name',
                'master_unit.id as master_unit_id',
                'master_bundle.name as master_bundle_name',
                'master_bundle.id as master_bundle_id'
            ];
        }

        $fmt = '
            SELECT 
                %s
            FROM `master_product` 
            LEFT JOIN master_unit ON master_unit.id = master_product.unit_id
            LEFT JOIN master_bundle ON master_product.bundle_id = master_bundle.id
            LEFT JOIN labels ON labels.id = master_product.label_id
            WHERE master_product.%s = %s LIMIT 1';
        $sql = sprintf($fmt, implode(',', $selects), $column, $value);

        return DB::fetch($sql);
    }

    /**
     * { function_description }
     *
     * @param      int     $id     The identifier
     *
     * @return     <type>
     */
    public function getMasterProduct(int $product_id, array $selects = [])
    {
        return $this->_getMasterProduct('id', $product_id, $selects);
    }

    /**
     * { function_description }
     *
     * @param      int     $id     The identifier
     *
     * @return     <type>
     */
    public function getMasterProductByCode(string $productCode, array $selects = [])
    {
        return $this->_getMasterProduct('code', $productCode, $selects);
    }

    /**
     * Gets the product system.
     *
     * @param      int     $product_id  The product identifier
     *
     * @return     <type>  The product system.
     */
    public function getProductSystem(int $product_id)
    {
        $fmt = '
            SELECT 
                product_system.system_id as id, 
                groups_system.name, 
                groups_system.structure_id 
            FROM `product_system` 
            LEFT JOIN master_product ON master_product.id = product_system.master_product_id
            LEFT JOIN groups_system ON groups_system.id = product_system.system_id
            WHERE master_product.id = %d';
        $sql = sprintf($fmt, $product_id);

        return DB::fetch_all($sql);
    }

    /**
     * Finds a product.
     *
     * @param      int     $groupID  The group id
     * @param      string  $code     The code
     *
     * @return     <type>
     */
    public function findProduct(int $groupID, string $code)
    {
        $fmt = 'SELECT * FROM `products` WHERE `group_id` = %d AND `code` = %s LIMIT 1';
        $sql = sprintf($fmt, $groupID, self::safe($code));

        return DB::fetch($sql);
    }

    /**
     * Gets the product.
     *
     * @param      int     $ID     { parameter_description }
     *
     * @return     <type>  The product.
     */
    public function getProduct(int $groupID, int $ID)
    {
        $fmt = 'SELECT * FROM `products` WHERE `group_id` = %d AND `id` = %d LIMIT 1';
        $sql = sprintf($fmt, $groupID, $ID);

        return DB::fetch($sql);
    }

    /**
     * Finds a product except id.
     *
     * @param      int     $groupID    The group id
     * @param      string  $code       The code
     * @param      int     $productID  The product id
     *
     * @return     <type>
     */
    public function findProductExceptID(int $groupID, string $code, int $productID)
    {
        $fmt = 'SELECT * FROM `products` WHERE `group_id` = %d AND `code` = %s AND id != %d LIMIT 1';
        $sql = sprintf($fmt, $groupID, self::safe($code), $productID);

        return DB::fetch($sql);
    }

    /**
     * { function_description }
     *
     * @param      int        $productID  The master id
     * @param      array      $columns   The columns
     *
     * @throws     Exception  (description)
     *
     * @return     bool
     */
    public function updateProduct(int $productID, array $columns)
    {
        if(!DB::update('products', $columns, 'id = ' . $productID)){
            throw new Exception('Cập nhật hệ thống thất bại !');
        }

        return true;
    }

    /**
     * Finds a master product.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findMasterProduct(string $name, array $select = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_product` WHERE `name` = %s';
        $sql = sprintf($fmt, implode(',', $select), self::safe($name));

        return DB::fetch_all_array($sql);
    }

    /**
     * Finds a master product.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findMasterProductByFullName(string $fullName, array $select = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_product` WHERE `full_name` = %s';
        $sql = sprintf($fmt, implode(',', $select), self::safe($fullName));

        return DB::fetch_all_array($sql);
    }

    /**
     * { function_description }
     *
     * @param      array   $columns  The columns
     * @param      array   $select   The select
     *
     * @return     <type>
     */
    public function searchMasterProduct(array $columns, array $select = ['*'])
    {
        $columns = array_map(function($column){
            return sprintf('`%s` %s %s', $column[0], $column[1], self::safe($column[2]));
        }, $columns);

        $fmt = 'SELECT %s FROM `master_product` WHERE %s';
        $sql = sprintf($fmt, implode(',', $select), implode(' AND ', $columns));

        return DB::fetch_all_array($sql);
    }

    /**
     * Gets the master product without realtion by code.
     *
     * @param      string  $code    The code
     * @param      array   $select  The select
     *
     * @return     <type>  The master product without realtion by code.
     */
    public function getMasterProductWithoutRealtionByCode(string $code, array $select = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_product` WHERE `code` = %s';
        $sql = sprintf($fmt, implode(',', $select), self::safe($code));

        return DB::fetch($sql);
    }

    /**
     * Lấy tất cả sản phẩm hệ thống
     *
     * @param      array   $select  The select
     *
     * @return     <type>
     */
    public function all(array $select = ['*'])
    {
        $sql = 'SELECT ' . implode(',', $select) . ' FROM `master_product`';

        return DB::fetch_all_array($sql);
    }

    /**
     * { function_description }
     *
     * @param      int     $id     The identifier
     *
     * @return     <type>
     */
    public function allWithRelation(array $selects = [])
    {
        $selects = $selects ? $selects : [
            'master_product.*',
            'master_unit.name as master_unit_name',
            'master_bundle.name as master_bundle_name',
            'master_product.cost_price',
            'labels.name as label_name',
            'IF(master_product.status = 1, "Đang kinh doanh", "Ngừng kinh doanh") AS status_name',
            '(SELECT users.username FROM users WHERE users.id = master_product.created_by) as created_username',
            '(SELECT users.username FROM users WHERE users.id = master_product.updated_by) as updated_username',
        ];

        $fmt = '
            SELECT 
                %s
            FROM `master_product` 
            LEFT JOIN master_unit ON master_unit.id = master_product.unit_id
            LEFT JOIN master_bundle ON master_product.bundle_id = master_bundle.id
            LEFT JOIN labels ON labels.id = master_product.label_id';
        $sql = sprintf($fmt, implode(',', $selects));

        return DB::fetch_all_array($sql);
    }

    /**
     * Finds a master product.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findMasterProductExceptID(string $name, int $ID, array $selects = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_product` WHERE `name` = %s AND `id` != %d';
        $sql = sprintf($fmt, implode(',', $selects), self::safe($name), $ID);

        return DB::fetch_all_array($sql);
    }

    /**
     * Finds a master product.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findMasterProductByFullNameExceptID(string $name, int $ID)
    {
        $fmt = 'SELECT * FROM `master_product` WHERE `full_name` = %s AND `id` != %d';
        $sql = sprintf($fmt, self::safe($name), $ID);

        return DB::fetch_all_array($sql);
    }

    /**
     * { function_description }
     *
     * @param      array  $master_products  The master products
     *
     * @return     int
     */
    public function insertMasterProduct(array $columns)
    {
        if(!$ID = DB::insert('master_product', $columns)){
            throw new Exception('Thêm sản phẩm hệ thống thất bại !');
        }

        return $ID;
    }

    /**
     * { function_description }
     *
     * @param      array  $master_products  The master products
     *
     * @return     int
     */
    public function updateMasterProduct(int $masterProductID, array $columns)
    {
        if(!DB::update('master_product', $columns, 'id = ' . $masterProductID)){
            throw new Exception('Cập nhật hệ thống thất bại !');
        }

        return true;
    }

    /**
     * Làm cho giá trị được dùng truy vấn an toàn hơn
     *
     * @param      int     $value  The value
     *
     * @return     <type>
     */
    public static function safe($value)
    {
        return is_string($value) ? sprintf('"%s"', DB::escape($value)) : $value - 0;
    }

    /**
     * { function_description }
     */
    public function generateCode()
    {
        $total = DB::fetch('SELECT `code` FROM `master_product` ORDER BY `code` DESC LIMIT 1', 'code');
        $total = $total ? ++$total : 1;

        return str_pad($total, 6, '0', STR_PAD_LEFT);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $ID     { parameter_description }
     *
     * @return     <type>
     */
    public function deleteMasterProduct($ID)
    {
        if(DB::delete('master_product', 'id = ' . $ID)){
            return $this->deleteProductSystem($ID);
        }
    }

    /**
     * Gets the systems child of obd.
     *
     * @param      array   $select  The select
     *
     * @return     <type>  The systems child of obd.
     */
    public function getChildOfOBD(array $select = ['*'])
    {
        return Systems::getDirectSystemsChild(Systems::getOBDStructureID(), $select);
    }

    /**
     * Lấy ra nhãn sản phẩm theo tên nhãn và ID phân loại
     *
     * @param      string  $name      The name
     * @param      int     $bundelID  The bundel id
     * @param      string  $distribute  The product distribute
     *
     * @return     <type>
     */
    public function findLabel(string $name, int $bundleID)
    {
        $fmt = 'SELECT * FROM `labels` WHERE `name` = %s AND `bundle_id` = %d LIMIT 1';
        $sql = sprintf($fmt, self::safe($name), $bundleID);

        return DB::fetch($sql);
    }

    /**
     *  Lấy ra phân loại sản phẩm theo tên phân loại
     *
     * @param      string  $distribute  The product distribute
     *
     * @return     <type>
     */
    public function findMasterBundle(string $name)
    {
        $fmt = 'SELECT * FROM `master_bundle` WHERE `name` = %s LIMIT 1';
        $sql = sprintf($fmt, self::safe($name));

        return DB::fetch($sql);
    }

    /**
     * Finds a master bundle name in.
     *
     * @param      array   $names  The names
     *
     * @return     <type>
     */
    public function findMasterBundleNameIn(array $names, array $select = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_bundle` WHERE `name` IN (%s)';
        $nameString = array_reduce($names, function($str, $name){
            return $str .= self::safe($name) . ',';
        }, '');
        $sql = sprintf($fmt, implode($select), rtrim($nameString, ','));

        return DB::fetch_all($sql);
    }

    /**
     * { function_description }
     */
    public function insertLabel(array $column)
    {
        if($label = $this->findLabel($column['name'], $column['bundle_id'])){
            return $label['id'];
        }

        return DB::insert('labels', $column);
    }

    /**
     * { function_description }
     */
    public function insertMasterBundle(array $column)
    {
        if($master_bundle = $this->findMasterBundle($column['name'])){
            return $master_bundle['id'];
        }

        return DB::insert('master_bundle', $column);
    }

    /**
     * Finds a bundle.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findBundle(int $groupID, string $name)
    {
        $fmt = 'SELECT * FROM `bundles` WHERE `name` = %s AND `group_id` = %d';
        $sql = sprintf($fmt, self::safe($name), $groupID);

        return DB::fetch_all_array($sql);
    }

    /**
     * Gets the exists bundle.
     *
     * @param      int     $groupID  The group id
     * @param      string  $name     The name
     *
     * @return     <type>  The exists bundle.
     */
    public function getExistsBundle(int $groupID, string $name)
    {
        return $this->getExistsName($this->findBundle($groupID, $name), $name);
    }

    /**
     * { function_description }
     */
    public function insertBundle(array $columns)
    {
        if($bundle = $this->getExistsBundle($columns['group_id'],  $columns['name'])){
            return $bundle['id'] ? $bundle['id'] : $this->standardizedBundle($bundle['id']);
        }

        return DB::insert('bundles', $columns);
    }

    /**
     * Finds a unit.
     *
     * @param      string  $name   The name
     *
     * @return     <type>
     */
    public function findUnit(int $groupID, string $name)
    {
        $fmt = 'SELECT * FROM `units` WHERE `name` = %s AND `group_id` = %d';
        $sql = sprintf($fmt, self::safe($name), $groupID);

        return DB::fetch_all_array($sql);
    }

    /**
     * Gets the exists unit.
     *
     * @param      int     $groupID  The group id
     * @param      string  $name     The name
     *
     * @return     <type>  The exists unit.
     */
    public function getExistsUnit(int $groupID, string $name)
    {
        return $this->getExistsName($this->findUnit($groupID, $name), $name);
    }

    /**
     * Gets the exists name.
     *
     * @param      array   $rows   The rows
     * @param      string  $name   The name
     *
     * @return     <type>  The exists name.
     */
    public function getExistsName(array $rows, string $name)
    {
        $name = mb_strtolower($name);
        $exists = array_filter($rows, function($row) use($name){
            return $name === mb_strtolower($row['name']);
        });

        return array_shift($exists);
    }

    /**
     * { function_description }
     */
    public function insertUnit(array $columns)
    {
        if($unit = $this->getExistsUnit($columns['group_id'],  $columns['name'])){
            return $unit['id'] ? $unit['id'] : $this->standardizedUnit($unit['id']);
        }

        return DB::insert('units', $columns);
    }

    /**
     *  Lấy ra đơn vị theo tên tên đơn vị
     *
     * @param      string  $distribute  The product distribute
     *
     * @return     <type>
     */
    public function findMasterUnit(string $name)
    {
        $fmt = 'SELECT * FROM `master_unit` WHERE `name` = %s LIMIT 1';
        $sql = sprintf($fmt, self::safe($name));

        return DB::fetch($sql);
    }

    /**
     * Finds a master unit name in.
     *
     * @param      array   $names   The names
     * @param      array   $select  The select
     *
     * @return     <type>
     */
    public function findMasterUnitNameIn(array $names, array $select = ['*'])
    {
        $fmt = 'SELECT %s FROM `master_unit` WHERE `name` IN (%s)';
        $nameString = array_reduce($names, function($str, $name){
            return $str .= self::safe($name) . ',';
        }, '');
        $sql = sprintf($fmt, implode($select), rtrim($nameString, ','));

        return DB::fetch_all($sql);
    }

    /**
     * { function_description }
     */
    public function insertMasterUnit(array $column)
    {
        if($master_unit = $this->findMasterUnit($column['name'])){
            return $master_unit['id'];
        }

        return DB::insert('master_unit', $column);
    }

    /**
     * Tìm kiếm sản phẩm hệ thống theo điều kiện. Chức năng thực hiện tìm kiếm các sản phẩm phù hợp
     * điều kiện filters. Đồng thời tỉnh tổng số lượng kết quả phù hợp
     * Note: Không được sửa hàm này, nếu cần lấy ra thông tin hay format khác hãy viết hàm mới
     *
     * @param      <type>  $page   The page
     *
     * @return     <type>  The master products.
     */
    public function findMasterProducts(array $filters)
    {
        $selects = [
            'master_product.*',
            'master_unit.name as master_unit_name',
            'master_unit.id as master_unit_id',
            'labels.name as label_name',
            'master_bundle.name as master_bundle_name',
            'master_bundle.id as master_bundle_id',

            // Người tạo
            'users_created.id as created_id',
            'users_created.username as created_username',
            'users_created.name as created_name',

            // Người cập nhật
            'users_updated.id as updated_id',
            'users_updated.username as updated_username',
            'users_updated.name as updated_name',
        ];
        $limit = [$filters['limit'] * ($filters['p'] - 1), $filters['limit']];
        $resultSql = $this->_findMasterProductsQueryBuilder($filters, $selects, $limit);
        $countSql = $this->_findMasterProductsQueryBuilder($filters, ['count(*) as count'],[0, 10e9]);

        return [
            DB::fetch_all($resultSql), // select product
            DB::fetch($countSql, 'count', 0) // count result
        ];
    }

    /**
     * Xây dựng câu truy vấn thỏa mãn điều kiện.
     * - Câu truy vấn này sẽ luôn luôn join unit và bundle để lấy thông tin đầy đủ cho sản phẩm
     *  + Nếu lọc theo bundle thì sẽ thêm điều kiện khi join trên table này
     *  + Nếu lọc theo unit hệ thống thì cũng sẽ thêm điều kiện join trên table này
     * - Khi cần lọc thông tin theo từ khóa ngưới dùng nhập vào thì ta sẽ bổ sung thêm điều kiện
     *  tìm kiếm theo mã hoặc tên sản phẩm
     *
     * ** Note: Nếu có sửa hàm này và thêm điều kiện vui lòng viết hàm build điều kiện và inject
     * nó vào danh sách sprint mà không thêm trực tiếp biểu thức truy vấn vào đây. Nhớ comment lý do để
     * đồng nghiệp còn biết :D
     * (Để tránh những hàm 500- cả ngàn dòng code ^^)
     *
     * @param      array   $filters  The filters
     *
     * @return     <type>
     */
    private function _findMasterProductsQueryBuilder(array $filters, array $selects = ['*'], array $limit = [0, 14])
    {
        // Bỏ loc theo he thong - 21/7/2021 - https://pm.tuha.vn/issues/6451
        $fmt = '
            SELECT 
                %s -- select
            FROM `master_product` 
                %s -- unit condition
                %s -- bundle condition
                %s -- label condition
                %s -- user_created condition
                %s -- user_updated condition
            WHERE
                %s -- status condition
                %s -- product code condition
            ORDER BY master_product.id DESC
            LIMIT %s
            ';
        $sql = sprintf(
            $fmt,
            implode(',', $selects),
            $this->joinUnit($filters['unit']),
            $this->joinBundle($filters['bundle']),
            $this->joinLabel($filters['label']),
            $this->joinUserCreated($filters['created_by']),
            $this->joinUserUpdated($filters['updated_by']),
            $this->buildStatusCondition($filters['status']),
            $this->buildCodeAndNameCondition($filters),
            implode(',', $limit)
        );

        return $sql;
    }

    /**
     * Builds an unit condition.
     *
     * @param      int     $unitID  The unit id
     *
     * @return     string  The unit condition.
     */
    private function joinUnit(int $unitID)
    {
        return 'JOIN master_unit ON master_unit.id = master_product.unit_id'
            . ($unitID ? ' AND `master_unit`.`id` = ' . $unitID : '');
    }

    /**
     * Builds a bundle condition.
     *
     * @param      int     $bundleID  The bundle id
     *
     * @return     string  The bundle condition.
     */
    private function joinBundle(int $bundleID)
    {
        $sql = ' JOIN master_bundle ON master_bundle.id = master_product.bundle_id';
        if (!$bundleID) {
            return $sql;
        }//end if
    
        $sqlGetIncludeIds = "SELECT id, include_ids FROM master_bundle WHERE id = $bundleID";
        $includeIds = DB::fetch($sqlGetIncludeIds, 'include_ids');

        if (!$includeIds) {
            return $sql . " AND `master_bundle`.`id` = $bundleID";
        }//end if

        $_includeIds = explode(',', $includeIds);
        $_includeIds[] = $bundleID;
        $_includeIds = DB::escapeArray($_includeIds);
        $includeIds = implode(',', $_includeIds);
        return $sql . " AND `master_bundle`.`id` in ($includeIds)";
    }

    /**
     * Builds a label condition.
     *
     * @param      int     $bundleID  The bundle id
     *
     * @return     string  The bundle condition.
     */
    private function joinLabel(int $labelID)
    {
        return (!$labelID ? 'LEFT ' : '') . 'JOIN labels ON labels.id = master_product.label_id'
            . ($labelID ? ' AND `labels`.`id` = ' . $labelID : '');
    }

    /**
     * { function_description }
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>
     */
    private function joinUserCreated(int $userID)
    {
        $join[] = 'LEFT JOIN `users` AS `users_created` ON `users_created`.`id` = `master_product`.`created_by`';

        if($userID){
            $join[]= 'AND `users_created`.`id` = ' . $userID;
        }

        return implode(' ', $join);
    }

    /**
     * { function_description }
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>
     */
    private function joinUserUpdated(int $userID)
    {
        $join[] = 'LEFT JOIN `users` AS `users_updated` ON `users_updated`.`id` = `master_product`.`updated_by`';

        if($userID){
            $join[]= 'AND `users_updated`.`id` = ' . $userID;
        }

        return implode(' ', $join);
    }

    /**
     * - Trạng thái sản phẩm:
     *  + status = 1 đang kinh doanh
     *  + status = 0 ngừng kinh doanh
     *  + status < 0 không quan tâm trạng thái, lúc này để câu truy vấn chính xác thì sẽ điều kiện
     *    của nó là 1
     *
     * @param      int     $status  The status
     *
     * @return     <type>  The status condition.
     */
    private function buildStatusCondition(int $status)
    {
        return $status < 0 ? 1 : sprintf('`master_product`.`status` = %d', $status);
    }

    /**
     * Builds a code and name condition.
     *
     * @param      array   $filters  The filters
     *
     * @return     string  The code and name condition.
     */
    private function buildCodeAndNameCondition(array $filters)
    {
        $operator = $filters['operator'] ? $filters['operator'] : 'OR';

        if(empty($filters['full_name']) && empty($filters['name']) && empty($filters['code'])){
            return '';
        }

        $conditions = [];

        if(isset($filters['code']) &&$filters['code'] != ""){
            $conditions[] = '`master_product`.`code` = ' . self::safe(DataFilter::removeDuplicatedSpaces($filters['code']));
        }

        if(isset($filters['name']) && $filters['name'] != ""){
            $conditions[] = '`master_product`.`name` LIKE "%' . DB::escape(DataFilter::removeDuplicatedSpaces($filters['name'])) . '%"';
        }

        if(isset($filters['full_name']) && $filters['full_name'] != ""){
            $conditions[] = '`master_product`.`full_name` LIKE "%' . DB::escape(DataFilter::removeDuplicatedSpaces($filters['full_name'])) . '%"';
        }

        return sprintf('AND (%s)', implode(' ' . $operator . ' ', $conditions));
    }

    /**
     * Gets all master bundles.
     *
     * @return     <type>  All master bundles.
     */
    public function getAllMasterBundles()
    {
        return DB::fetch_all('SELECT * FROM `master_bundle`');
    }

    /**
     * Gets all labels.
     *
     * @return     <type>  All labels.
     */
    public function getAllLabels(?int $groupID = null, ?int $bundleID = 0, $selects = ['*'])
    {
        return DB::fetch_all(
            'SELECT ' . implode(',', $selects)  . ' FROM `labels`'
            . ($groupID ? ' JOIN products ON products.label_id = labels.id' : '')
            . ' WHERE 1'
            . ($groupID ? ' AND `products`.`group_id` = ' . $groupID : '')
            . ($bundleID ? ' AND `bundle_id` = ' . $bundleID : '')
        );
    }

    /**
     * Gets all bundles by group id.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  All bundles by group id.
     */
    public function getAllBundlesByGroupID(int $groupID)
    {
        return DB::fetch_all('SELECT `id`,`name` FROM `bundles` WHERE `group_id` = ' . $groupID);
    }

    /**
     * Gets all master units.
     *
     * @return     <type>  All master units.
     */
    public function getAllMasterUnits()
    {
        return DB::fetch_all('SELECT * FROM `master_unit`');
    }

    /**
     * Gets all units by group id.
     *
     * @param      int     $groupID  The group id
     *
     * @return     <type>  All units by group id.
     */
    public function getAllUnitsByGroupID(int $groupID)
    {
        return DB::fetch_all('SELECT `id`,`name` FROM `units` WHERE `group_id` = ' . $groupID);
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    public function curentTime()
    {
        return date('Y-m-d H:i:s');
    }
}
