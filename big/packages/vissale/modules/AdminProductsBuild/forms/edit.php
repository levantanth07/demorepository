<?php

class EditAdminProductsBuildForm extends Form
{
    private $mp; // master product helper

    private $masterProduct = [];

    private $productSystems = [];

    private static $system = [];

    private $product = [];

    public static $isOBD = false;

    function __construct()
    {
        $this->mp = new ProductHelper();
        self::$isOBD = get_group_system_by_group_id(Session::get('group_id'), ['structure_id'])['structure_id'] == Systems::getOBDStructureID();

        if(!self::$system = Systems::getF0OfGroup(Systems::getOBDStructureID(), Session::get('group_id'))){
            self::$system = [];
        }

        Form::Form('EditAdminProductsBuildForm');
        $this->add('code',new TextType(true,'Bạn vui lòng nhập mã sản phẩm', 2, 255));
        $this->add('name',new TextType(true,'Bạn vui lòng nhập tên sản phẩm', 2, 255));
        $messagePrice = "Bạn vui lòng nhập giá bán";
        $messageImportPrice = "Bạn vui lòng nhập giá vốn";

        if(isset($_REQUEST['price'])){
            $price = str_replace(',','',$_REQUEST['price']);
            $lengPrice = strlen(str_replace(',','',$_REQUEST['price']));
            if($lengPrice > 16){
                $messagePrice = "Bạn vui lòng nhập giá bán tối đa 16 ký tự";
            }
            if($price < 0){
                $messagePrice = "Giá bán phải là giá trị dương";
            }
        }
        if(isset($_REQUEST['import_price'])){
            $_REQUEST['import_price'] = str_replace(',','',$_REQUEST['import_price']);
            $lengImportPrice = strlen($_REQUEST['import_price']);
            if($lengImportPrice > 16){
                $messageImportPrice = "Bạn vui lòng nhập giá vốn tối đa 16 ký tự";
            }
            if($_REQUEST['import_price'] < 0){
                $messageImportPrice = "Giá vốn phải là giá trị dương";
            }
        }

        $weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : null;
        $message_weight = AdminProductsBuild::validate_product_weight($weight);
        $this->add('weight', new IntType(true, $message_weight, 1, 99000000));
        $this->add('price', new IntType(true,$messagePrice));
        //$this->add('import_price', new FloatType(true,$messageImportPrice));
        if(Url::get('do')=='add'){
            $this->add('on_hand', new FloatType(true,'Bạn vui lòng nhập số tồn kho',0.1));
        }

        // Ajax upload image
        if (URL::get('action') == 'upload_image' && URL::get('type') == 'ajax') {
            $data = [];
            $upload_image = [];
            $max_upload_file_size = 2*1024*1024;
            for ($i=0; $i < count($_FILES['image_files']['name']); $i++) {
                if ($_FILES['image_files']['size'][$i] <= $max_upload_file_size) {
                    $target_path = "upload/product/" . date('Ymd') . '/';
                    if (!file_exists($target_path)) {
                        mkdir($target_path, 0777, true);
                    }

                    $ext = pathinfo($_FILES['image_files']['name'][$i], PATHINFO_EXTENSION);
                    $file_name = pathinfo($_FILES['image_files']['name'][$i], PATHINFO_FILENAME);
                    $target_path = $target_path . $file_name. '_' . time() . "." . $ext;

                    if(move_uploaded_file($_FILES['image_files']['tmp_name'][$i], $target_path)) {
                        $data['success'][] = $target_path;
                    } else {
                        $data['error'][] = $_FILES['image_files']['name'][$i];
                    }
                } else {
                    $data['error'][] = $_FILES['image_files']['name'][$i];
                }
            }

            echo json_encode($data);
            die();
        }
    }

    function on_submit()
    {
        $group_id = Session::get('group_id');
        $id = Url::iget('id');
        $name = Url::get('name');
        $code = Url::get('code');
        $standardized = Url::get('standardized');
        if(!$this->checkProductMaster($name, $code) && $standardized == 1){
            $this->error('master_product', 'Đây không phải sản phẩm hệ thống !');
            return;
            // return URL::js_redirect(true, 'Đây không phải sản phẩm hệ thống');
            // return $this->render_error('Đây không phải sản phẩm hệ thống !');
        }
        if(!$this->checkProductMaster($name, $code) && Url::get('do') == 'add'){
            $this->error('master_product', 'Đây không phải sản phẩm hệ thống !');
            return;
        }
        if($id && (!$this->product = $this->mp->getProduct($group_id, $id))){
            return $this->sendProductNotFound();
        }

        if(URL::get('do') == 'update-from-master'){
            return $this->updateProductFromMaster($id);
        }

        if(URL::get('do') == 'update-all'){
            return $this->updateAllProductFromMaster();
        }

        if(!$this->isUpdateRequest() && !$this->isMasterProduct() && self::$isOBD){
            return URL::js_redirect(true, 'Không được phép thêm sản phẩm chưa chuẩn hóa !', ['do']);
        }

        if ($this->check() && $this->validateUploadImage('image_url'))
        {
            $this->validateSystemProduct();

            $rows = [];
            if (!$this->is_error()) {
                try{
                    $ID = URL::iget('id');
                    $rows['modified'] = date('Y-m-d H:i:s');
                    $rows['code'] = $this->getCode();
                    $rows['name'] = $this->getName();
                    $rows['weight'] = $this->getWeight();
                    $rows['color'] = $_POST['color'];
                    $rows['price'] = (int)str_replace(',', '', $_POST['price']);
                    // $rows['import_price'] = $this->getImportPrice();
                    $rows['size'] = $_POST['size'];
                    $rows['unit_id'] = $this->getUnitID();
                    $rows['bundle_id'] = $this->getBundleID();
                    $rows['label_id'] = $this->getLabel();
                    $rows['del'] = $this->getStatus();

                    $rows['registration_certificate_id'] = URL::getString('registration_certificate_id');
                    $rows['registration_certificate_by'] = URL::getString('registration_certificate_by');
                    $rows['registration_certificate_at'] = preg_replace('#^(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', URL::getString('registration_certificate_at', 'NULL'));
                    if(strtotime($rows['registration_certificate_at']) > time()){
                        $this->error('registration_certificate_at', 'Ngày cấp không hợp lệ !');
                        return;
                    }

                    // Kiểm tra xem mã sản phẩm hệ thống có trùng với mã sản phẩm cũ (bảng products)
                    if($this->isCodeExistsInProducts($group_id, $rows['code'], $ID)){
                        return;
                    }

                    // Kiểm tra xem mã sản phẩm có bị trùng với mã sản phẩm hệ thống (bảng master_product)
                    // Việc kiếm tra chỉ được thực hiện khi nó là sản phẩm thường.
                    if(!$this->isMasterProduct() && $this->isCodeExistsInMasterProducts($rows['code'])){
                        return;
                    }

                    $rows['standardized'] = 0;
                    $rows['master_updated_at'] = 'NULL';
                    if($this->isMasterProduct()){
                        $rows['standardized'] = 1;
                        $rows['master_updated_at'] = $this->masterProduct['updated_at'] ? $this->masterProduct['updated_at'] : 'NULL';
                    }

                    if ($this->isUpdateRequest() and $item = DB::exists_id('products', $ID)) {
                        $rows['modified_user_id'] = get_user_id();
                        if($standardized == 0){
                             unset($rows['name']);
                             unset($rows['code']);
                        }
                        DB::update('products', $rows, 'id='.$ID);
                    } else {
                        $rows['group_id'] = $group_id;
                        $rows['user_id'] = get_user_id();
                        $rows['created'] = date('Y-m-d H:i:s');
                        $ID = DB::insert('products', $rows);
                    }
                    if(!System::is_local()){
                        AdminProductsBuildDB::save_item_image('image_url',$ID);
                    }
                    if ($this->is_error()) {
                        return;
                    }

                    Url::js_redirect(true,'Bạn đã lưu thành công',array('cid'));
                }catch (Exception $e){
                    die("Lỗi hệ thống!");

                }
            }
        }
    }

    function draw()
    {
        $this->map = [];
        $this->map['title'] = 'Thêm mới sản phẩm';
        $gallery_images = [];

        if (URL::get('do')=='edit' and $id = Url::iget('id')) {
            if(!$data = $this->generatePrintData()){
                return $this->sendProductNotFound();
            }//end if

            $this->map['on_hand'] = $data['on_hand'];
            $this->map['standardized'] = $data['standardized'];
            foreach ($data as $key => $value) {
                $_REQUEST[$key] = $value;
                if ($key == 'gallery_images') {
                    $gallery_images = json_decode($value);
                }//end if

                if ($key == 'price'){
                    $_REQUEST[$key] = number_format($value);
                }//end if

                if ($key == 'import_price'){
                    $_REQUEST[$key] = number_format($value);
                }//end if

                if ($key == 'registration_certificate_at' && is_datetime($value)){
                    $_REQUEST[$key] = date('d/m/Y', strtotime($value));
                }//end if
            }//end if

            $this->map['title'] = 'Sửa sản phẩm';
        }//end if

        if (isset($_POST['gallery_images'])) {
            $gallery_images = $_POST['gallery_images'];
        }

        $this->map['gallery_images'] = $gallery_images;
        $this->map['bundle_id_list'] = AdminProductsBuildDB::getShopBundles();

        $groupId = Session::get('group_id');
        $sql = "
            SELECT units.id,units.name, units.standardized
            FROM units
            WHERE units.group_id = $groupId
            GROUP BY units.id
            ORDER BY units.name";

        $this->map['unit_id_list'] = DB::fetch_all($sql);
        $this->map['is_edit'] = $this->isUpdateRequest();
        $this->map['system_f0_id'] = isset(self::$system['f0']) ? self::$system['f0']  : 0;
        $this->map['is_obd'] = self::$isOBD;

        $this->map['del_list'] = [0 => 'Kinh doanh', 1 => "Không kinh doanh"];
        $this->parse_layout('edit', $this->map);
    }

    function generatePrintData()
    {
        $group_id = Session::get('group_id');
        $id = Url::iget('id');
        $data = DB::fetch('SELECT products.id,`code`, products.`name`, price,import_price, 
                          image_url,color, `size`,`weight`, unit_id, 
                          products.bundle_id, del, products.on_hand, standardized,
                          products.label_id, labels.name AS label_name,
                          registration_certificate_id,
                          registration_certificate_by,
                          registration_certificate_at 
                          FROM products 
                          LEFT JOIN labels ON products.label_id = labels.id
                          WHERE products.id = '. $id .' and products.group_id='.$group_id);

        if(!$data){
            return false;
        }

        if($data['standardized'] && $masterProduct = $this->mp->getMasterProductByCode($data['code'], ['master_product.id', 'master_product.name'])){
            $data['master_product_id'] = $masterProduct['id'];
            if(!$data['name']){
                $data['name'] = $masterProduct['name'];
            }
        }

        //gallery_images
        require_once('packages/vissale/lib/php/vissale.php');
        $warehouse_id = get_default_warehouse($group_id);
        $product_id = $data['id'];
        $data['on_hand'] = get_product_remain($product_id,$warehouse_id);

        if($dataSubmited = Form::get_flash_message('update_product_submited')){
            return array_merge($data, $dataSubmited);
        }

        return $data;
    }
    function nhap_kho($id,$quantity){
        $group_id = AdminProductsBuild::$group_id;
        $warehouse_id = get_default_warehouse($group_id);
        $invoice_id = false;
        if($warehouse_id){
            $lastest_item = DB::fetch('SELECT id,bill_number FROM qlbh_stock_invoice where type="IMPORT"  and qlbh_stock_invoice.group_id='.$group_id.' ORDER BY bill_number DESC');
            $bill_number = $lastest_item['bill_number'] + 1;
            $total_amount = 0;
            $array = array(
                'bill_number'=>$bill_number,
                'type'=>'IMPORT',
                'deliver_name'=>'',
                'note'=>Url::get('note'),
                'receiver_name'=>'',
                'total_amount'=>$total_amount,
                'create_date'=>date('Y-m-d'),
                'order_id'=>'',
                'note'=>'Nhập kho khi thêm sản phẩm mới'
            );
            $sql = '
                    SELECT
                        products.id,
                        products.unit_id,
                        products.name as product_name,
                        products.code as product_code,
                        products.import_price as price,
                        products.weight
                    FROM
                        products
                        LEFT JOIN units ON units.id = products.unit_id
                    WHERE
                        products.id = '.$id.'
                    ';
            $products = DB::fetch_all($sql);
            if(sizeof($products)>0){
                $invoice_id = DB::insert('qlbh_stock_invoice',$array+array('group_id'=>$group_id,'user_id'=>Session::get('user_id'),'time'=>time()));
                foreach($products as $key=>$record){
                    $record['product_id'] = $key;
                    $record['unit_id'] = $record['unit_id']?$record['unit_id']:'0';
                    $record['price']=$record['price']?str_replace(',','',$record['price']):'0';
                    $record['warehouse_id'] = $warehouse_id;
                    $payment_price = str_replace(',','',$record['price']);
                    $total_amount += $payment_price;
                    $record['quantity']=str_replace(',','',$quantity);
                    unset($record['id']);
                    unset($record['weight']);
                    $empty = true;
                    foreach($record as $record_value){
                        if($record_value){
                            $empty = false;
                        }
                    }
                    if(!$empty){
                        $record['invoice_id'] = $invoice_id;
                        if(DB::exists('SELECT id FROM products WHERE code=\''.$record['product_code'].'\' AND '.(Session::get('master_group_id')?' (products.group_id = '.Session::get('master_group_id').' or products.group_id = '.$group_id.') ':'products.group_id = '.$group_id.'').'')){
                            if(isset($record['id'])){
                                unset($record['id']);
                            }
                            DB::insert('qlbh_stock_invoice_detail',$record);
                        }
                    }
                }
                DB::update('qlbh_stock_invoice',array('total_amount'=>$total_amount),'id='.$invoice_id);
            }
            //} end IF
            return $invoice_id;
        }else{
            return false;
        }
    }

    /**
     * Determines if fill system product if exists.
     */
    private function validateSystemProduct()
    {
        if(!$this->isMasterProduct() || empty($_POST['code'])){
            return;
        }

        $_REQUEST['standardized'] = $this->isMasterProduct();

        // Không tìm thấy hệ thống quản lý group hiện tại trong các F0 của obd
        if(!self::$system && !self::$isOBD){
            $this->error('system_group', 'Sản phẩm này không nằm trong danh mục sản phẩm hệ thống của bạn !');
            return;
        }

        $masterProductID = URL::iget('master_product_id');
        $masterProductCode = URL::get('code');
        $groupID = Session::get('group_id');

        // Lấy ra sản phẩm hệ thống và liên kết của nó với hệ thống F0
        $this->masterProduct = $this->mp->getMasterProduct($masterProductID);

        // Trường hợp mã sản phẩm trong request không trùng khớp mã sản phẩm của ID trong request
        if($masterProductCode !== $this->masterProduct['code']){
            $this->error('master_product_code', 'Mã sản phẩm hệ thông không hợp lệ !');
            return;
        }
    }

    /**
     * Determines if master product code exists in products.
     *
     * @param      int     $groupID  The group id
     * @param      string  $code     The code
     *
     * @return     bool    True if master product code exists in products, False otherwise.
     */
    public function isCodeExistsInProducts(int $groupID, string $code, int $masterProductID)
    {
        if($this->isUpdateRequest()){
            $product = $this->mp->findProductExceptID($groupID, $code, $masterProductID);
        }else{
            $product = $this->mp->findProduct($groupID, $code);
        }

        if($product){
            $message = 'Đã tồn tại mã sản phẩm này !';

            if($this->isMasterProduct() && !$product['standardized']){
                $message .= sprintf('Bạn sẽ cần <button type="button" onclick="changeCode(event)" data-code="%s" class="btn-change-code">đổi</button> mã sản phẩm cũ nếu muốn tiếp tục thêm sản phẩm.', $this->masterProduct['code']);
            }

            $this->set_flash_message('update_product_submited', $_REQUEST);
            $this->error('product_code', $message);
        }

        return $product;
    }

    /**
     * Determines whether the specified code is code exists in master products.
     *
     * @param      string  $code   The code
     *
     * @return     bool    True if the specified code is code exists in master products, False otherwise.
     */
    public function isCodeExistsInMasterProducts(string $code)
    {
        if(!$product = $this->mp->getMasterProductWithoutRealtionByCode($code)){
            return;
        }

        $message = 'Mã sản phẩm này đang trùng với mã sản phẩm hệ thống !';
        $this->error('product_code', $message);

        return $product;
    }

    /**
     * Determines if update request.
     *
     * @return     bool  True if update request, False otherwise.
     */
    private function isUpdateRequest()
    {
        return Url::get('do')=='edit';
    }

    public function checkProductMaster($name, $code){
        $code = DB::escape($code);
        $name = DB::escape($name);
        $sql = 'SELECT id,name,code FROM master_product WHERE name = "'.$name.'" AND code = "'.$code.'"';
        return DB::fetch($sql);
    }

    /**
     * Determines if master product.
     *
     * @return     bool  True if master product, False otherwise.
     */
    private function isMasterProduct()
    {
        return URL::getUInt('master_product_id');
    }

    public function getBundleIdByname(string $name)
    {
        $name = DB::escape($name);
        $sql = "SELECT `id`, `name`, `group_id`
            FROM bundles
            WHERE `group_id` = 0 AND `name` = '$name'";
        return DB::fetch($sql, 'id');
    }

    /**
     * Gets the bundle id.
     *
     * @return     <type>  The bundle id.
     */
    private function getBundleID()
    {
        $groupID = Session::get('group_id');

        if(!$this->isMasterProduct()){
            return Url::getUInt('bundle_id');
        }//end if

        $bundleId = self::getBundleIdByname($this->masterProduct['master_bundle_name']);
        if ($bundleId) {
            return $bundleId;
        }//end if

        return $this->insertBundleName($this->masterProduct['master_bundle_name'], $groupID);
    }

    /**
     * { function_description }
     *
     * @param      string  $bundleName  The bundle name
     * @param      int     $groupID     The group id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertBundleName(string $bundleName, int $groupID)
    {
        return $this->mp->insertBundle([
            'name' => $bundleName,
            'standardized' => 1,
            'group_id' => $groupID
        ]);
    }

    /**
     * Gets the bundle id.
     *
     * @return     <type>  The bundle id.
     */
    private function getUnitID()
    {
        $groupID = Session::get('group_id');

        if(!$this->isMasterProduct()){
            return Url::iget('unit_id');
        }

        return $this->insertUnitName($this->masterProduct['master_unit_name'], $groupID);
    }

    /**
     * { function_description }
     *
     * @param      string  $unitName  The unit name
     * @param      int     $groupID   The gorup id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertUnitName(string $unitName, int $groupID)
    {
        return $this->mp->insertUnit([
            'name' => $unitName,
            'standardized' => 1,
            'group_id' => $groupID
        ]);
    }

    /**
     * Gets the status.
     *
     * @return     <type>  The status.
     */
    private function getStatus()
    {
        return Url::getInt('del');//!$this->isMasterProduct() ? Url::getInt('del') : ($this->masterProduct['status'] ? 0 : 1);
    }

    /**
     * Gets the code.
     *
     * @return     <type>  The code.
     */
    private function getCode()
    {
        return trimSpace(!$this->isMasterProduct() ? trimSpace(Url::getString('code')) : $this->masterProduct['code']);
    }

    /**
     * Gets the code.
     *
     * @return     <type>  The code.
     */
    private function getImportPrice()
    {
        if(!$this->isMasterProduct()){
            return Url::getUIntFormated('import_price');
        }

        return !empty($this->masterProduct['cost_price']) ? $this->masterProduct['cost_price'] : 0;
    }

    private function getWeight()
    {
        if(!$this->isMasterProduct()){
            return Url::getUIntFormated('weight');
        }

        return !empty($this->masterProduct['weight']) ? $this->masterProduct['weight'] : 0;
    }

    /**
     * Gets the code.
     *
     * @return     <type>  The code.
     */
    private function getLabel()
    {
        return intval($this->masterProduct['label_id'] ?? 0);
    }

    /**
     * Gets the name.
     *
     * @return     <type>  The name.
     */
    private function getName()
    {
        return trimSpace(!$this->isMasterProduct() ? Url::getString('name') : $this->masterProduct['name']);
    }

    /**
     * Gets the bundle id.
     *
     * @return     <type>  The bundle id.
     */
    private function setImageUrl(&$rows)
    {
        if(!$this->isMasterProduct()){
            return;
        }

        return $rows['image_url'] = $this->masterProduct['image'];
    }

    /**
     * Sends a product not found.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function sendProductNotFound()
    {
        return $this->render_error('Không tìm thấy sản phẩm !');
    }

    /**
     * { function_description }
     */
    private function updateProductFromMaster()
    {
        $groupID = Session::get('group_id');
        $update = $this->mp->getMasterProductByCode($this->product['code'], [
            'master_product.name',
            'master_unit.name as master_unit_name',
            'master_bundle.name as master_bundle_name',
            'master_product.label_id as label_id',
            'labels.name as label_name',
            'master_product.weight as weight',
            'master_product.cost_price as import_price',
            'IF(master_product.status = 1, 0, 1) as del',
            'master_product.updated_at AS master_updated_at'
        ]);

        if($update && $this->updateProduct($this->product['id'], $groupID, $update)){
            RequestHandler::sendJsonSuccess($update);
        }

        RequestHandler::sendJsonError([]);
    }

    /**
     * { function_description }
     */
    private function updateAllProductFromMaster()
    {
        $groupID = Session::get('group_id');

        $products = $this->mp->getProductsForUpdate($groupID);
        if(!$products){
            RequestHandler::sendJsonError([]);
        }

        $results = [];
        foreach($products as $productID => $product){
            unset($product['id']);
            if($this->updateProduct($productID, $groupID, $product)){
                $results[] = $product;
            }
        }

        if(count($products) == count($results)){
            RequestHandler::sendJsonSuccess($products);
        }

        RequestHandler::sendJsonError([]);

    }

    /**
     * { function_description }
     *
     * @param      array   $columns  The columns
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function updateProduct(int $productID, int $groupID, array $columns)
    {
        $columns['unit_id'] = $this->insertUnitName($columns['master_unit_name'], $groupID);
        $columns['bundle_id'] = $this->insertBundleName($columns['master_bundle_name'], $groupID);

        unset($columns['master_unit_name']);
        unset($columns['master_bundle_name']);
        unset($columns['label_name']);

        return $this->mp->updateProduct($productID, $columns);
    }
}
