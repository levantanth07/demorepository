<?php

class ListAdminProductsBuildForm extends Form
{
    private $mp; // masterproduct helper
    public static $isOBD = false;
    public static $system = [];

    function __construct()
    {
        Form::Form('ListAdminProductsBuildForm');

        $this->mp = new ProductHelper();
        self::$isOBD = get_group_system_by_group_id(Session::get('group_id'), ['structure_id'])['structure_id'] == Systems::getOBDStructureID();

        if (!self::$system = Systems::getF0OfGroup(Systems::getOBDStructureID(), Session::get('group_id'))) {
            self::$system = [];
        }
    }

    function delete()
    {
        // Xóa 1 sp
        if ($id = Url::iget('id')) {
            $destination = Url::get('destination');
            if (!$this->mp->getProduct(AdminProductsBuild::$group_id, $id)) {
                $this->sendProductNotFound();
            }
            if (!product_is_in_order($id)) {
                if (!DB::delete('products', 'id = ' . $id . ' AND del = 1 AND group_id = ' . AdminProductsBuild::$group_id)) {
                    $this->render_error('Xoá sản phẩm thất bại !');
                }
            } else {
                $this->render_error('Xoá sản phẩm thất bại !');
            }
            die('<script>window.location="' . $destination . '";</script>');
        }

        // Xóa nhiều sp
        if (!empty($_REQUEST['selected_ids'])) {
            $sql = sprintf(
                'DELETE FROM `products` WHERE `id` IN (%s) AND group_id = %d AND del = 1',
                implode(',', $_REQUEST['selected_ids']),
                AdminProductsBuild::$group_id
            );

            if (!DB::delete('products', 'id IN (' . implode(',', $_REQUEST['selected_ids']) . ') AND del = 1 AND group_id = ' . AdminProductsBuild::$group_id)) {
                $this->render_error('Xoá sản phẩm thất bại !');
            }

            Url::redirect_current();
        }

        $this->render_error('Bad Request !');
    }

    function on_submit()
    {

        switch (Url::get('do')) {
            case 'delete':
                $this->delete();
                break;
            default:
                if (Url::get('importExcelBtn') and $excel_file = $_FILES['excel_file'] and $temp_file = $excel_file['tmp_name']) {
                    $arr = $this->read_excel($temp_file);
                    $idsFail = '';
                    $idsTrue = '';
                    $imported_total = 0;
                    $imported_total_fail = 0;
                    foreach ($arr as $key => $val) {
                        if ($key > 1) {
                            $record['code'] = trimSpace($val[1]);
                            $record['name'] = trimSpace($val[2]);
                            $record['weight'] = $val[3];
                            $record['price'] = $val[4] ?? '';
                            $record['color'] = $val[5];
                            $record['size'] = $val[6];
                            $bundle_id = $this->getBundleIDByName($val[7] ?? 0);
                            $record['bundle_id'] = $bundle_id;
                            $unit_id = DB::fetch('select id from units where name LIKE "' . $val[8] . '" and group_id="' . AdminProductsBuild::$group_id . '"', 'id');
                            $record['unit_id'] = $unit_id ? $unit_id : 0;
                            $record['group_id'] = AdminProductsBuild::$group_id;
                            $pattern = '/^[0-9]+$/';
                            $subject = $record['price'];
                            $price = false;
                            if (preg_match($pattern, $subject, $matches)) {
                                $price = true;
                            }
                            $validate_weight = AdminProductsBuild::validate_product_weight($record['weight']);
                            if (strlen($record['price']) > 16 || $record['price'] < 0 || $price == false || !is_numeric($record['weight']) || $record['weight'] <= 0 || $validate_weight !== '') {
                                $idsFail .= ($idsFail ? ', ' : '') . $record['code'];
                                $imported_total_fail++;
                            } else {
                                if (!DB::exists('select id from products where code="' . $record['code'] . '" and group_id="' . AdminProductsBuild::$group_id . '"')) {
                                    DB::insert('products', $record);
                                    $idsTrue .= ($idsTrue ? ', ' : '') . $record['code'];
                                    $imported_total++;
                                } else {
                                    $idsFail .= ($idsFail ? ', ' : '') . $record['code'];
                                    $imported_total_fail++;
                                }
                            }
                        }
                    }
                    $line = '\r\n';
                    if ($imported_total_fail > 0 && $imported_total == 0) {
                        Url::js_redirect(true, 'Import ' . $imported_total_fail . ' sản phẩm thất bại! Các mã thất bại ( ' . $idsFail . ' )');
                    }
                    if ($imported_total_fail > 0 && $imported_total > 0) {
                        Url::js_redirect(true, 'Bạn đã Import ' . $imported_total . ' sản phẩm thành công' . $line . 'Import ' . $imported_total_fail . ' sản phẩm thất bại' . $line . 'Các mã thất bại ( ' . $idsFail . ' )');
                    }
                    if ($imported_total_fail == 0 && $imported_total > 0) {
                        Url::js_redirect(true, 'Bạn đã Import ' . $imported_total . ' sản phẩm thành công');
                    }
                }

                break;
        }
    }

    /**
     * Gets the bundle id by name.
     *
     * @param      string    $bundleName  The bundle name
     *
     * @return     bool|int  The bundle id by name.
     */
    private function getBundleIDByName(string $bundleName)
    {
        if (!$bundleName) {
            return 0;
        }

        $bundleID = DB::fetch('select id from bundles where `name` = "' . DB::escape($bundleName) . '" and group_id="' . AdminProductsBuild::$group_id . '" LIMIT 1', 'id');

        return $bundleID ?: 0;
    }

    function draw()
    {
        if (Url::get('do') == 'delete') {
            return $this->delete();
        }

        require_once 'packages/core/includes/utils/paging.php';
        $this->map = [];
        $this->map['title'] = 'Danh sách sản phẩm';
        $cond = '
            products.group_id = ' . Session::get('group_id') . '
            AND ' . (Url::get('del') ? 'IFNULL(products.del,0) = 1' : 'IFNULL(products.del,0) = 0') . '
        ';
        $item_per_page = 20;

        $total = AdminProductsBuildDB::get_total_product($cond);
        $this->map['total'] = $total;
        $products = AdminProductsBuildDB::getProductsList($cond, $item_per_page);
        $this->map['items'] = $products;
        $this->map['num_product_need_update'] = AdminProductsBuildDB::countProductForUpdated((int) Session::get('group_id'));
        $paging = paging(
            $total,
            $item_per_page,
            10,
            false,
            'page_no',
            array('search_text', 'do', 'item_per_page', 'bundle_id', 'unit_id', 'del')
        );

        $this->map['paging'] = $paging;

        $bundles = AdminProductsBuildDB::getSystemBundles();
        $this->map['bundle_id_list'] = self::buildOptions($bundles, 'Chọn nhóm sản phẩm');

        $units = AdminProductsBuildDB::getShopUnits();
        $this->map['unit_id_list'] = self::buildOptions($units, 'Chọn đơn vị');

        $labels = $this->mp->getAllLabels(Session::get('group_id'), URL::getUInt('bundle_id'), ['labels.id', 'labels.name']);
        $this->map['label_id_list'] = array_reduce($labels, function ($result, $label) {
            $result[$label['id']] = $label['name'];

            return $result;
        }, [0 => 'Nhãn']);


        $this->map['del_list'] = ['' => 'Tình trạng', 0 => 'Kinh doanh', 1 => "Không kinh doanh"];

        $this->parse_layout('list', $this->map);
    }
    function read_excel($excel_file)
    {
        require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = PHPExcel_IOFactory::load($excel_file);
        $dataArr = array();
        $available_total = 0;
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            for ($row = 1; $row <= $highestRow; ++$row) {
                $dataArr[$row]['id'] = $row;
                $empty = true;
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $dataArr[$row][$col + 1] = $val;
                    if ($val and $col == 2 and $row != 1) {
                        $dataArr[$row][$col + 1] = $val . '';
                    }
                    if ($val) {
                        $empty = false;
                    }
                }
                if ($empty == true and $row != 1) {
                    unset($dataArr[$row]);
                }
            }
        }
        return $dataArr;
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
     * buildOptions function
     *
     * @param array $data
     * @param string $defaultName
     * @param string $defaultVal
     * @return array
     */
    private static function buildOptions(
        array $data,
        string $defaultName = "Lựa chọn",
        $defaultVal = ''
    ): array {
        $list = [$defaultVal => $defaultName];
        foreach ($data as $key => $val) {
            $list[$key] = $val['name'];
        }//end if

        return $list;
    }
}
