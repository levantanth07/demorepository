<?php
class EditAdminProductsForm extends Form{
    private $mp;
    public static $isOBD;
    public static $system;
    function __construct(){
        Form::Form('EditAdminProductsForm');
        $this->add('products.name',new TextType(true,'Chưa nhập tên sản phẩm',0,255));
        $this->mp = new ProductHelper();
        self::$isOBD = get_group_system_by_group_id(Session::get('group_id'), ['structure_id'])['structure_id'] == Systems::getOBDStructureID();
        if(!self::$system = Systems::getF0OfGroup(Systems::getOBDStructureID(), Session::get('group_id'))){
            self::$system = [];
        }
    }

    function on_submit(){
        if(Url::get('importExcelBtn') and $excel_file = $_FILES['excel_file'] and $temp_file = $excel_file['tmp_name']){
            $arr = $this->read_excel($temp_file);
            foreach($arr as $key=>$val){
                if($key>1){
                    $record['code'] = trimSpace($val[1]);
                    $record['name'] = trimSpace($val[2]);
                    $record['price'] = $val[3];
                    $record['color'] = $val[4];
                    $record['size'] = $val[5];
                    $bundle = (isset($val[6]) and $val[6])?$val[6]:0;
                    $bundle_id = $bundle?DB::fetch('select id from bundles where `name` LIKE "'.DB::escape($bundle).'" and group_id="'.AdminProducts::$group_id.'" LIMIT 1','id'):0;
                    $record['bundle_id'] = $bundle_id;
                    $unit_id = DB::fetch('select id from units where name LIKE "'.DB::escape($val[7]).'" and group_id="'.AdminProducts::$group_id.'" LIMIT 1','id');
                    $record['unit_id'] = $unit_id?$unit_id:0;
                    $record['group_id'] = AdminProducts::$group_id;
                    if(!DB::exists('select id from products where code="'.DB::escape($record['code']).'" and group_id="'.AdminProducts::$group_id.'" LIMIT 1')){
                        DB::insert('products',$record);
                    }
                }
            }
            Url::js_redirect(true,'Import Sản phẩm từ File Excel thành công!');
        }

        if(Url::get('confirm_edit') && !Url::get('search')){
            if(URL::get('deleted_ids')){
                $ids = implode(',', parse_id(URL::get('deleted_ids')));
                $deleted = DB::delete('products',' `id` IN ('.$ids.') AND `group_id` = '.AdminProducts::$group_id.' AND `del` = 1');
                if(!$deleted){
                    $this->render_error('Xoá sản phẩm thất bại !');
                }

                if(!empty($ids)){
                    $products = DB::fetch_all('SELECT `name` FROM `products` WHERE `id` IN (' . $ids . ')');
                    System::log('DELETE_PRODUCT','Xóa sản phẩm','Xóa sản phẩm ' . implode(', ', array_column($products, 'name')));
                }

                return Url::js_redirect(true);
            }



            if(empty($_REQUEST['mi_product']) || !is_array($_REQUEST['mi_product'])){
                $this->sendBadRequest();
            }

            require_once 'packages/core/includes/utils/vn_code.php';

            $this->update();

            // Url::js_redirect(true);
        }

    }
    function draw(){
        $this->map = array();
        $group_id = AdminProducts::$group_id;
        $keyword = Datafilter::removeDuplicatedSpaces(DB::escape(Url::get('keyword')));
        $cond = ''.(Url::get('del')?'IFNULL(products.del,0) = 1':'IFNULL(products.del,0) = 0').'
            AND products.group_id='.$group_id.'';
        $cond .= $keyword != "" ?' AND (products.name LIKE "%'.$keyword.'%" or products.code LIKE "%'.$keyword.'%")':'';

        //if(!isset($_REQUEST['mi_product']))
        {
            $item_per_page = 20;
            DB::query('
                select 
                    count(*) as acount
                from 
                    products
                where 
                    '.$cond.'
            ');
            $count = DB::fetch();
            $this->map['total'] = $count['acount'];
            require_once 'packages/core/includes/utils/paging.php';
            $paging = paging($count['acount'],$item_per_page,10,false,'page_no',array('keyword'));
            $sql = '
                select 
                    products.id,
                    products.name,
                    products.code,
                    products.price,
                    products.import_price,
                    products.color,
                    products.size,
                    products.weight,
                    products.unit_id,
                    products.bundle_id,
                    products.created,
                    products.modified,
                    products.del,
                    products.total_order,
                    products.image_url,
                    products.standardized
                from 
                    products
                WHERE
                    '.$cond.'
                order by 
                    products.del,products.id DESC
                LIMIT
                    '.((page_no()-1)*$item_per_page).','.$item_per_page.'
            ';
            $mi_product = DB::fetch_all($sql);
            foreach($mi_product as $key=>$val){
                $mi_product[$key]['price'] = System::display_number($val['price']);
                $mi_product[$key]['import_price'] = System::display_number($val['import_price']);
            }
            $_REQUEST['mi_product'] = $mi_product;
        }
        $this->map['paging'] = $paging;
        //////////
        $sql = '
            select 
                bundles.id,bundles.name
            from 
                bundles
            WHERE
                bundles.group_id='.AdminProducts::$group_id.'
            GROUP BY
                bundles.id
            order by 
                bundles.name
        ';
        $groups = DB::fetch_all($sql);
        $bundle_options = '<option value="">Chọn</option>';
        foreach($groups as $key=>$val){
            $bundle_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['bundle_options'] = $bundle_options;
        //////
        $sql = '
            select
                units.id,units.name
            from
                units
            WHERE
                units.group_id='.AdminProducts::$group_id.'
            GROUP BY
                units.id
            order by
                units.name
        ';
        $groups = DB::fetch_all($sql);
        $unit_options = '<option value="">Chọn</option>';
        foreach($groups as $key=>$val){
            $unit_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['unit_options'] = $unit_options;
        //////
        $this->map['status_options'] = '<option value="">Trạng thái</option><option value="SHOW">Kích hoạt</option><option value="HIDE">Ẩn</option>';
        $this->parse_layout('edit',$this->map);
    }
    function read_excel($excel_file){
        require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = PHPExcel_IOFactory::load($excel_file);
        $dataArr = array();
        $available_total = 0;
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            for ($row = 1; $row <= $highestRow; ++ $row) {
                $dataArr[$row]['id'] = $row;
                $empty = true;
                for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $dataArr[$row][$col+1] = $val;
                    if($val and $col==2 and $row!=1){
                        $dataArr[$row][$col+1] = $val.'';
                    }
                    if($val){
                        $empty = false;
                    }
                }
                if($empty==true and $row!=1){
                    unset($dataArr[$row]);
                }
            }
        }
        return $dataArr;
    }
    function save_item_image($file,$id){
        if(isset($_FILES[$file]) and $_FILES[$file]){
            require_once 'packages/core/includes/utils/ftp.php';
            $image_url = FTP::upload_file($file,'upload/default', true,'content', 'IMAGE', false);
            if($image_url){
                DB::update('products',array('image_url'=>$image_url),'id='.$id);
            }
        }
    }

    /**
     * Sends a bad request.
     */
    private function sendBadRequest()
    {
        $this->render_error('Bad Request !');
    }

    /**
     * Updates the object.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function update()
    {
        if(!$productIDs = $this->filterProductIDs()){
            return $this->sendBadRequest();
        }

        $sql = 'SELECT * FROM `products` WHERE id IN (' . implode(',', $productIDs) . ') AND `group_id` = ' . AdminProducts::$group_id;
        $this->products = DB::fetch_all($sql);

        if(!is_array($this->products) || count($productIDs) !== count($this->products)){
            return $this->sendBadRequest();
        }

        $currentUserID = get_user_id();
        $currentTime = date('Y-m-d H:i:s');
        $idsFail = '';
        $idsTrue = '';
        $imported_total = 0;
        $imported_total_fail = 0;
        foreach($_REQUEST['mi_product'] as $key => $reqProduct){
            $productID = $reqProduct['id'];
            if(!$product = $this->products[$productID]){
                continue;
            }

            $update = [];

            $update['price'] = $reqProduct['price'] || $reqProduct['price'] == 0?System::calculate_number($reqProduct['price']):'';

            // if(!empty($reqProduct['import_price']) && !self::$isOBD){
            //     $update['import_price'] = parse_int_formated($reqProduct['import_price']);
            // }
            if(!empty($reqProduct['weight']) && !self::$isOBD){
                $update['weight'] = parse_int_formated($reqProduct['weight']);
            } else {
                $update['weight'] = $product['weight'];
            }
            $update['color'] = $reqProduct['color'];
            $update['size'] = $reqProduct['size'];

            $update['del'] = 0;
            if(!empty($reqProduct['del'])){
                $update['del'] = $reqProduct['del'] == 'on' || $reqProduct['del'] == 1 ? 1 : 0;
            }

            $update['modified'] = $currentTime;
            $update['modified_user_id'] = $currentUserID;

            if(!preg_match('/^[0-9]{1,16}$/', $update['price'])){
                $idsFail .= ($idsFail ? ', ' : '') . $reqProduct['code'];
                $imported_total_fail++;
            } elseif ($update['weight'] && (int)$update['weight'] < 0) {
                $idsFail .= ($idsFail ? ', ' : '') . $reqProduct['code'];
                $imported_total_fail++;

            } else {
                $checkChange = false;
                if (isset($update['price']) && $product['price'] != $update['price']) {
                    $checkChange = true;
                }
                // if (isset($update['import_price']) && $product['import_price'] != $update['import_price']) {
                //     $checkChange = true;
                // }
                if (isset($update['color']) && $product['color'] != $update['color']) {
                    $checkChange = true;
                }
                if (isset($update['size']) && $product['size'] != $update['size']) {
                    $checkChange = true;
                }
                if (isset($update['size']) && $product['size'] != $update['size']) {
                    $checkChange = true;
                }
                if (isset($update['weight']) && $product['weight'] != $update['weight']) {
                    $checkChange = true;
                }

                if ($checkChange && empty($update['weight'])) {
                    $idsFail .= ($idsFail ? ', ' : '') . $reqProduct['code'];
                    $imported_total_fail++;
                } else {
                    DB::update('products',$update,'id=' . $productID);
                    $idsTrue .= ($idsTrue ? ', ' : '') . $reqProduct['code'];
                    $imported_total++;
                }
            }
            if(!System::is_local()){
                if(!$this->validateUploadImage('image_url_'.$key)){
                    Url::js_redirect(true);
                }

                $this->save_item_image('image_url_'.$key,$productID);
            }
        }
        $line = '\r\n';
        if($imported_total_fail > 0){
            Url::js_redirect(true,'Cập nhật '.$imported_total_fail.' sản phẩm thất bại!'.$line.'Bạn vui lòng nhập giá bán, trọng lượng là số nguyên dương và không quá 16 ký tự!'.$line.'Các mã thất bại ( '. $idsFail .' )');
        }
        if($imported_total_fail == 0 && $imported_total > 0){
            Url::js_redirect(true,'Bạn đã cập nhật '.$imported_total.' sản phẩm thành công');
        }
    }

    /**
     * Determines if valid products.
     *
     * @return     bool  True if valid products, False otherwise.
     */
    private function filterProductIDs()
    {
        return array_map(function($product){
            return intval($product['id']);
        }, $_REQUEST['mi_product']);
    }
}
?>
