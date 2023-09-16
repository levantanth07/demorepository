<?php
class MasterProductForm extends Form
{
    public $map = [];

    private $ID;

    private $product = [];

    private $oldProduct = [];

    private $systemIDs = [];

    private $obdChilds = [];

    public static $filters = [];

    /**
     * Constructs a new instance.
     */
    function __construct() {
        require_once 'packages/vissale/modules/AdminProductsBuild/ProductHelper.php';
        $this->helper = new ProductHelper();

        $this->checkPrivilege();

        // $this->obdChilds =  $this->helper->getChildOfOBD(['id', 'name']);
    }

    /**
     * Xử lý dữ liệu được submit
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    function on_submit()
    {
        // try
        // {
            // Trường hợp là delete request thì không validate các input mà bản thân
            // chức năng delete đã có validate riêng nó. Hàm này cần gọi trước tất cả các bước
            // validate request khác
            if($this->isDeleteRequest()){
                return;// $this->deleteProduct(Url::iget('id'));
            }

            if($this->validate() && (!$this->check() || $this->is_error())) {
                return;
            }
            // Validate tên sản phẩm không được phép trùng nhau với update và tồn tại với create
            $this->ID = Url::iget('id');
            $name = DataFilter::removeDuplicatedSpaces(URL::get('name'));
            $fullName = DataFilter::removeDuplicatedSpaces(URL::get('full_name'));
            if(!$this->validateExistsProductName($name)){
                return $this->error('product_name', 'Tên sản phẩm đã tồn tại !', false);
            }

            if(!$this->validateExistsProductFullName($fullName)){
                return $this->error('product_full_name', 'Tên sản phẩm chi tiết đã tồn tại !', false);
            }
            // Map các tham số cần thiết của product từ request
            [$this->product, $this->systemIDs] = $this->helper->mapRequest();
            // Trường hợp là create
            //  - Thêm sản phẩm
            //  - Redirect trở lại trang thêm với flash mesage
            if($this->isCreateRequest()){
                if($this->isCreateFail()){
                    return $this->error('create_error', 'Thêm sản phẩm thất bại !', false);
                }

                $this->set_flash_message('create_success', 'Thêm sản phẩm thành công !');
                Url::redirect_current(['do' => 'master_product']);
            }

            // Trường hợp là update
            if($this->isUpdateRequest()){
                if($this->isUpdateFail()){
                    return $this->error('create_error', 'Cập nhật thông tin sản phẩm thất bại !', false);
                }

                $url = '/index062019.php?page=product_admin&do=master_product';
                if(!empty($_SESSION['redirect_after_update'])){
                    $url = $_SESSION['redirect_after_update'];
                    unset($_SESSION['redirect_after_update']);
                }

                $this->set_flash_message('update_success', 'Sửa sản phẩm thành công !');
                header('location: ' . $url);
                exit;
            }
        // }
        // catch(Throwable $e){
        //     // return $this->error('interact', $this->getErrorMessage($e));
        //     return $this->error('interact', "Lỗi. Vui lòng thử lại sau.");
        // }
    }

    /**
     * Validate thong tin submit
     */
    private function validate() {
        Form::Form('EditMasterProductForm');
        $this->add('name', new TextType(true ,'Bạn vui lòng nhập tên sản phẩm từ 2-255 kí tự !', 2, 255));
        $this->add('full_name', new TextType(true ,'Bạn vui lòng nhập tên sản phẩm chi tiết từ 2-255 kí tự !', 2, 255));
        $this->add('label_name', new TextType(true ,'Bạn vui lòng nhập nhãn sản phẩm chi tiết từ 2-255 kí tự !', 2, 255));
        $this->add('unit_name', new TextType(true ,'Bạn vui lòng nhập tên đơn vị từ 1-20 kí tự !', 1, 20));
        $this->add('factory_product_code', new TextType(false ,'Bạn vui lòng nhập mã nhà máy không quá 100 kí tự !', 0, 100));
        $this->add('bundle_id', new IntType(true ,'Bạn vui lòng lựa chọn phân loại', 1));
        $this->add('cost_price', new FloatType(true ,'Bạn vui lòng nhập giá vốn >= 0 !'));
        $weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : null;
        $message_weight = AdminProductsBuild::validate_product_weight($weight);
        $this->add('weight', new IntType(true, $message_weight, 1, 99000000));

        return true;
    }

    /**
     * Determines if create fail.
     *
     * @return     bool  True if create fail, False otherwise.
     */
    private function isCreateFail()
    {
        return !$this->doInsert();
    }

    /**
     * Determines if update success.
     *
     * @return     bool  True if update success, False otherwise.
     */
    private function isUpdateSuccess()
    {
        return !$this->isUpdateFail();
    }

    /**
     * Determines if update fail.
     *
     * @return     bool  True if update fail, False otherwise.
     */
    private function isUpdateFail()
    {
        return !$this->validateUpdateRequest() || !$this->doUpdate();
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    function draw()
    {
        switch(URL::get('view')){
            case 'create':
                return $this->renderCreatePage();

            case 'update':
                return $this->renderUpdatePage(Url::iget('id'));

            default:
                return $this->renderIndexPage();
        }
    }

    /**
     * Hiển thị form sửa sản phẩm
     */
    private function renderIndexPage()
    {
        require_once 'packages/core/includes/utils/paging.php';

        $this->map['title'] = 'Danh sách sản phẩm';
        // $this->map['system_childs'] = $this->obdChilds;
        $this->map['filters'] = $this->parseFilter();

        [$_master_products, $_count] = $this->prepareMasterProducts();
        $this->map['master_products'] = $_master_products;
        $this->map['count'] = $_count;
        $paging_params = [
            $this->map['count'],
            self::$filters['limit'],
            10,
            false,
            'page_no',
            array_merge(['do', 'pname', 'p', 'item_per_page'],array_keys(self::$filters)),
            'Trang'
        ];
        $this->map['filters'] = self::$filters;
        $this->map['paging'] = paging(...$paging_params);
        $this->map['labels'] = $this->helper->getAllLabels(null, self::$filters['bundle']);
        $this->map['bundle_options'] = json_encode(self::mapBundleOptions());
        $this->map['master_bundles'] = $this->helper->getAllMasterBundles();
        $this->map['master_units'] = $this->helper->getAllMasterUnits();


        $this->parse_layout('master_index', $this->map);
    }

    private function mapBundleOptions(): array
    {
        $bundles = $this->helper->getAllMasterBundles();
        $_bundleOptions = [];
        $valids = [];
        foreach ($bundles as $id => $bundle) {
            $parenId = $bundle['parent_id'];
            $name = $bundle['name'];
            if (!$parenId) {
                $_bundleOptions[$id]['text'] = $name;
                continue;
            }//end if

            $valids[$parenId] = true;
            $_bundleOptions[$parenId]['children'][] = [
                'id' => $id,
                'text' => $name,
            ];
        }//end foreach

        $bundleOptions[] = [
            'id' => '',
            'text' => '--- Tất cả ---',
        ];
        $validBundleIds = array_keys($valids);
        foreach ($_bundleOptions as $id => $bundleOption)  {
            if (in_array($id, $validBundleIds)) {
                $bundleOptions[] = $bundleOption;
            }//end if
        }//end if
        return $bundleOptions;
    }

    /**
     * Hiển thị form thêm sản phẩm
     */
    private function renderCreatePage()
    {
        $this->map['title'] = 'Thêm mới sản phẩm';

        // lưu lại referer
        if(empty($_SESSION['referer']) && !empty($_SERVER['HTTP_REFERER'])){
            $_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
        }

        $this->map['master_products'] = $this->helper->all(['name', 'full_name', 'code', 'image']);
        $this->map['referer'] = $_SESSION['referer'];
        $this->map['bundles'] = AdminProductsBuildDB::getBundles();
        $this->parse_layout('master_create', $this->map);
    }

    /**
     * Hiển thị danh sách sản phẩm
     *
     * @param      <type>  $id     The identifier
     */
    private function renderUpdatePage(int $id = 0)
    {
        $this->map['title'] = 'Sửa sản phẩm';
        // $this->map['system_childs'] = $this->obdChilds;

        if(!$id || !$this->map['master_product'] = $this->helper->getMasterProduct($id)){
            Url::js_redirect(true, 'Bad Request !', ['do' => 'master_product']);
        }

        // lưu lại referer để sau khi update xong sẽ redirect
        if(empty($_SESSION['redirect_after_update']) && !empty($_SERVER['HTTP_REFERER'])){
            $_SESSION['redirect_after_update'] = $_SERVER['HTTP_REFERER'];
        }

        $this->map['bundles'] = AdminProductsBuildDB::getBundles();
        $this->map['master_products'] = $this->helper->all(['name', 'full_name', 'code', 'image']);
        $this->map['redirect_after_update'] = $_SESSION['redirect_after_update'];

        $this->parse_layout('master_update', $this->map);
    }

    /**
     * { function_description }
     *
     * @param      int   $productID  The product id
     */
    private function deleteProduct(int $productID)
    {
        try{
            if(!$master_product = $this->helper->getMasterProduct($productID)){
                throw Exception('Bad Request !');
            }

            $this->helper->deleteMasterProduct($productID);

            $this->set_flash_message('delete_message', 'Xóa sản phẩm thành công !');
        }catch(Throwable $e){
            $this->set_flash_message('delete_message', "Lỗi! Vui lòng thử lại sau.");
        }

        $url = '/index062019.php?page=product_admin&do=master_product';
        if(!empty($_SERVER['HTTP_REFERER'])){
            $url = $_SERVER['HTTP_REFERER'];
        }
        header('location: ' . $url);
        die();
    }

    private function checkPrivilege() {
        if (!$this->helper::hasPrivilegeOnMasterProduct()) {
            Url::js_redirect(true, 'Bạn không có quyền truy cập tính năng này.');
        }
    }

    /**
     * Determines if update request.
     *
     * @return     bool  True if update request, False otherwise.
     */
    private function isUpdateRequest()
    {
        return isset($_POST['method']) && $_POST['method']  === 'put';
    }

    /**
     * Determines if delete request.
     *
     * @return     bool  True if delete request, False otherwise.
     */
    private function isDeleteRequest()
    {
        return isset($_REQUEST['method']) && $_REQUEST['method'] ==='delete';
    }

    /**
     * Determines if create request.
     *
     * @return     bool  True if create request, False otherwise.
     */
    private function isCreateRequest()
    {
        return $_POST['method'] === 'post';
    }

    /**
     * Does an update.
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function doUpdate()
    {
        $this->product['bundle_id'] = $this->product['bundle_id'];
        // $this->product['bundle_id'] = $this->insertBundle();
        $this->product['label_id'] = $this->insertLabel();
        $this->product['unit_id'] = $this->insertUnit();
        $this->product['updated_by'] = Session::get('user_data', 'user_id');

        // Thay doi thong tin khong quan trong
        if($this->hasChangedNormalInfomation()){
            $this->product['all_fields_updated_at'] = $this->helper->curentTime();
        }

        // Thay doi thong tin quan trong
        if($this->hasChangedImportantInfomation()){
            $this->product['updated_at'] = $this->helper->curentTime();
            $this->product['all_fields_updated_at'] = $this->helper->curentTime();
        }

        $this->helper->updateMasterProduct($this->ID, $this->product);

        return true;
    }

    /**
     * Determines if changed normal infomation.
     *
     * @return     bool  True if changed normal infomation, False otherwise.
     */
    private function hasChangedNormalInfomation()
    {
        return $this->hasChangedProps(['full_name', 'factory_product_code', 'note', 'image']);
    }

    /**
     * Determines if changed important infomation.
     *
     * @return     bool  True if changed important infomation, False otherwise.
     */
    private function hasChangedImportantInfomation()
    {
        return $this->hasChangedProps(['name', 'code', 'status', 'bundle_id', 'unit_id', 'label_id', 'cost_price', 'weight']);
    }

    /**
     * Determines if changed properties.
     *
     * @param      <type>  $props  The properties
     *
     * @return     bool    True if changed properties, False otherwise.
     */
    private function hasChangedProps($props)
    {
        return array_filter($props, function($prop){
            return $this->oldProduct[$prop] != $this->product[$prop];
        });
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function validateUpdateRequest()
    {
        if(!$this->oldProduct = $this->helper->getMasterProduct($this->ID)){
            return false;
        }

        if($this->oldProduct['id'] != $this->ID){
            return false;
        }

        if($this->oldProduct['code'] != $this->product['code']){
            return false;
        }

        return true;
    }

    /**
     * Does an insert.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function doInsert()
    {
        $this->product['bundle_id'] = $this->product['bundle_id'];
        // $this->product['bundle_id'] = $this->insertBundle();
        $this->product['label_id'] = $this->insertLabel();
        $this->product['unit_id'] = $this->insertUnit();
        $this->product['code'] = $this->helper->generateCode();
        $this->product['created_at'] = $this->helper->curentTime();
        $this->product['updated_at'] = 'NULL';
        $this->product['created_by'] = Session::get('user_data', 'user_id');
        $this->product['updated_by'] = 'NULL';

        $productID = $this->helper->insertMasterProduct($this->product);
        return $productID;
    }

    /**
     * Insert nhãn sản phẩm
     * NOTE: phải gọi sau insert bundle
     *
     * @throws     Exception  (description)
     *
     * @return     <type>
     */
    private function insertLabel()
    {
        $insert = [
            'name' => $this->product['label_name'],
            'bundle_id' => $this->product['bundle_id'],
            'created_by' => get_user_id(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if(!$labelID = $this->helper->insertLabel($insert)){
            throw new Exception('Thêm nhãn sản phẩm thất bại');
        }

        unset($this->product['label_name']);

        return $labelID;
    }

    /**
     * { function_description }
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     ( description_of_the_return_value )
     */
    private function insertBundle()
    {
        $insert = ['name' => $this->product['bundle_name']];
        if($bundelID = $this->helper->insertMasterBundle($insert)){
            unset($this->product['bundle_name']);

            return $bundelID;
        }

        throw new Exception('Thêm loại sản phẩm thất bại');
    }

    /**
     * { function_description }
     *
     * @throws     Exception  (description)
     */
    private function insertUnit()
    {
        $insert = ['name' => $this->product['unit_name']];
        if($bundelID = $this->helper->insertMasterUnit($insert)){
            unset($this->product['unit_name']);

            return $bundelID;
        }

        throw new Exception('Thêm đơn vị sản phẩm thất bại');
    }

    /**
     * Gets the error message.
     *
     * @param      Throwable  $e      { parameter_description }
     *
     * @return     string     The error message.
     */
    private function getErrorMessage(Throwable $e)
    {
        switch($e->getMessage()){
            default:
                return System::is_local() ? th_debug($e)  : $e->getMessage();
        }
    }

    /**
     * { function_description }
     *
     * @throws     Exception  (description)
     */
    private function validateExistsProductName(string $productName)
    {
        if($this->isCreateRequest() && !$this->canCreateCurrentProductName($productName)){
            return false;
        }

        if($this->isUpdateRequest() && !$this->canUpdateCurrentProductName($productName)){
            return false;
        }

        return true;
    }

    /**
     * { function_description }
     *
     * @throws     Exception  (description)
     */
    private function validateExistsProductFullName(string $fullName)
    {
        if($this->isCreateRequest() && !$this->canCreateCurrentProductFullName($fullName)){
            return false;
        }

        if($this->isUpdateRequest() && !$this->canUpdateCurrentProductFullName($fullName)){
            return false;
        }

        return true;
    }

    /**
     * Determines ability to create product name.
     *
     * @return     bool  True if able to create product name, False otherwise.
     */
    private function canCreateCurrentProductFullName(string $productName)
    {
        $fields = [
            ['full_name', 'LIKE', $productName]
        ];

        $results = $this->helper->searchMasterProduct($fields, ['full_name']);

        return !$this->hasFieldName($results, 'full_name', $productName);
    }

    /**
     * Determines ability to update current product name.
     *
     * @return     bool  True if able to update current product name, False otherwise.
     */
    private function canUpdateCurrentProductFullName(string $productName)
    {
        $fields = [
            ['full_name', 'LIKE', $productName],
            ['id', '!=', $this->ID]
        ];

        $results = $this->helper->searchMasterProduct($fields, ['full_name']);

        return !$this->hasFieldName($results, 'full_name', $productName);
    }

    /**
     * Determines ability to create product name.
     *
     * @return     bool  True if able to create product name, False otherwise.
     */
    private function canCreateCurrentProductName(string $productName)
    {
        $results = $this->helper->findMasterProduct($productName, ['name']);

        return !$this->hasFieldName($results, 'name', $productName);
    }

    /**
     * Determines ability to update current product name.
     *
     * @return     bool  True if able to update current product name, False otherwise.
     */
    private function canUpdateCurrentProductName(string $productName)
    {
        $results = $this->helper->findMasterProductExceptID($productName, $this->ID, ['name']);

        return !$this->hasFieldName($results, 'name', $productName);
    }

    /**
     * Determines if field name.
     *
     * @param      array|bool  $rows   The rows
     * @param      string      $field  The field
     * @param      string      $name   The name
     *
     * @return     bool        True if field name, False otherwise.
     */
    private function hasFieldName(array $rows =[], string $field, string $name)
    {
        return $rows ? array_filter($rows, function($row) use($field, $name) {
            return $row[$field] === $name;
        }) : false;
    }

    /**
     * Tìm kiếm sản phẩm theo điều kiện lọc
     *  - Lấy danh sách sản phẩm mà chưa có thông tin hệ thống
     *  - Lấy danh sách hệ thống được gán cho các sản phẩm
     *  - Gán các hệ thống tương ứng từng sản phẩm
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function prepareMasterProducts()
    {
        [$masterProducts, $count] = $this->helper->findMasterProducts(self::$filters);

        // Bỏ loc theo he thong - 21/7/2021 - https://pm.tuha.vn/issues/6451
        // $masterProductIDs = array_column($masterProducts, 'id');

        // $productSystems = $this->helper->getProductSystems($masterProductIDs);

        // // map product system to product
        // array_walk($masterProducts, function(&$product, $ID, $systems){
        //     $product['systems'] = $systems[$ID];
        //     $product['systems_name'] = implode(', ', array_column($systems[$ID], 'name'));
        // }, $productSystems);

        return [$masterProducts, $count];
    }

    /**
     * { function_description }
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function parseFilter()
    {
        if(!isset($_GET['page_no']) || !$pageNo = intval($_GET['page_no'])){
            $pageNo = 1;
        }


        $limit = URL::getInt('item_per_page', 20);
        if($limit < 5) $limit = 5;
        if($limit > 200) $limit = 200;
        $this->map['item_per_page'] = $limit;

        return self::$filters = [
            'code' => DataFilter::removeDuplicatedSpaces(DB::escape(URL::get('code'))),
            'name' => DataFilter::removeDuplicatedSpaces(DB::escape(URL::get('pname'))),
            'full_name' => DataFilter::removeDuplicatedSpaces(DB::escape(URL::get('full_name'))),
            'bundle' => URL::getUInt('bundle'),
            'unit' => URL::getUInt('unit'),
            'label' => URL::getUInt('label'),
            'created_by' => URL::getUInt('created_by'),
            'updated_by' => URL::getUInt('updated_by'),
            'created_at' => URL::get('created_by'),
            'updated_at' => URL::get('created_by'),
            'status' => isset($_GET['status']) ? intval($_GET['status']) : 1,
            'limit' => $limit,
            'p' => $pageNo,
            'operator' => ''
        ];
    }
}
