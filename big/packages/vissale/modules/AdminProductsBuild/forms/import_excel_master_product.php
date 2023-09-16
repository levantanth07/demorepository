<?php

class ImportExcelMasterProduct
{
    private $mp;
    private $tmpFilePath;
    private $systems;

    function __construct()
    {   
        require_once ROOT_PATH . 'packages/core/includes/common/ExcelHelper.php';
        $this->mp = new ProductHelper();
        // return;
        $this->handle();
    }

    /**
     * { function_description }
     */
    private function handle()
    {   
        $this->validate();

        $this->initSystems();

        // $this->success($this->import());

        $this->success($this->importUpdate());
    }

    /**
     * { function_description }
     */
    private function validate()
    {
        if(empty($_FILES['excel_file'])){
            $this->error('File import rỗng !');
        }

        $this->tmpFilePath = $_FILES['excel_file']['tmp_name'];
    }

    private function mb_ucfirst($string, $encoding)
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    /**
     * { function_description }
     */
    private function importUpdate()
    {
        $rows = ExcelHelper::parse($this->tmpFilePath);
        $rows = $this->mapUpdate($rows);

        // insert 
        $errors = [];
        $IDs = [];
        foreach($rows as $key => $row)
        {   
            if(!$key){
                continue;
            }
            try{
                $product = $this->mp->getMasterProductByCode($row['code'], ['master_product.id', 'master_product.bundle_id', 'master_product.id']);
                if(!$product){
                    throw new Exception("khong ton tai product", 1);
                    unset($row['code']);
                }
                // Insert bundle_name and fill
                $insert = ['name' => $row['label'], 'bundle_id' => $product['bundle_id']];
                if(!$row['label_id'] = $this->mp->insertLabel($insert)){
                    throw new Exception("Insert label error", 1);
                }else{
                    unset($row['label']);
                }
                
                $row['updated_at'] = $row['all_fields_updated_at'] = date('Y-m-d H:i:s');
                $row['updated_by'] = 226020;

                // insert product and relation system
                $productID = $this->mp->updateMasterProduct($product['id'], $row);

                $IDs[] = $row['code'];
            }catch(Throwable $e){
                $errors[$row['code']] = "Lỗi. Vui lòng thử lại sau.";
            }
        }        

        return [
            'errors' => $errors,
            'success' => $IDs,
            'missings' => array_filter($rows, function($row) use($IDs){
            return !in_array($row['code'], $IDs);
        })
        ];
    }

    /**
     * { function_description }
     */
    private function import()
    {
        $rows = ExcelHelper::parse($this->tmpFilePath);
        $rows = $this->map($rows);
        // insert 
        $errors = [];
        $IDs = [];
        foreach($rows as $key => $row)
        {   
            if(!$key){
                continue;
            }
            try{
                // Insert bundle_name and fill
                $insert = ['name' => $row['bundle_name']];
                if($row['bundle_id'] = $this->mp->insertMasterBundle($insert)){
                    unset($row['bundle_name']);
                }

                // insert unit and fill 
                $insert = ['name' => $row['unit_name']];
                if($row['unit_id'] = $this->mp->insertMasterUnit($insert)){
                    unset($row['unit_name']);
                }

                // convert systems name to ID
                $systemIDs = $this->systemNamesToIDs(explode(',', $row['systems']));
                unset($row['systems']);

                $row['status'] = 1;
                // insert product and relation system
                $productID = $this->mp->insertMasterProduct($row);

                // update code
                // $productCode = $this->mp->generateCode($productID);
                // $this->mp->updateMasterProduct($productID, ['code' => $productCode]) ? $productID : false;

                $IDs[] = $row['code'];
            }catch(Throwable $e){
                $errors[$productID] = "Lỗi. Vui lòng thử lại sau";
                
            }
        }        




        return [
            'errors' => $errors,
            'success' => $IDs,
            'missings' => array_filter($rows, function($row) use($IDs){
            return !in_array($row['code'], $IDs);
        })
        ];
    }

    /**
     * Sets the systems.
     */
    private function initSystems()
    {
        // $_systems = $this->mp->getChildOfOBD(['id', 'name']);
        // foreach($_systems as $_id => $_name){
        //     $this->systems[$_id] = trim($_name['name']);
        // }

        $this->systems = [
            44  => 'D&H',
            7   => 'ERK',
            13  => 'PAH',
            6   => 'UNICORN',
            58  => 'VICGROUP',
            53  => 'UNC',
            112 => 'AVG',
        ];
    }

    /**
     * { function_description }
     *
     * @param      <type>  $rows   The rows
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function mapUpdate($rows)
    {   
        return array_map(function($row){
            return [
                'code' => $row[1],
                'cost_price' => $row[2],
                'label' => mb_ucfirst(mb_strtolower($row[3]), 'utf-8'),
            ];
        }, $rows);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $rows   The rows
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function map($rows)
    {   
        // array_shift($rows);

        return array_map(function($row){
            return [
                'systems' => $row[1],
                'bundle_name' => mb_ucfirst(mb_strtolower($row[2]), 'utf-8'),
                'full_name' => $row[3],
                'unit_name' => mb_ucfirst(mb_strtolower($row[4]), 'utf-8'),
                'factory_product_code' => $row[5],
                'code' => $row[6],
                'name' => $row[7],
                'status' => 0 // mặc định import thì trạng thái là đang kinh doanh 
            ];
        }, $rows);
    }

    /**
     * { function_description }
     *
     * @param      array  $names  The names
     *
     * @return     array  ( description_of_the_return_value )
     */
    private function systemNamesToIDs(array $names)
    {
        $IDs = [];
        foreach ($names as $key => $name) {
            $id = array_search(trim($name), $this->systems);
            if($id !== false){
                $IDs[] = $id;
            }
        }

        return $IDs;
    }

    /**
     * // ham nay muc dich toi uu hieu nang 
     *
     * @param      array  $rows   The rows
     */
    private function fillBundelID(array $rows)
    {   
        // ex: [aa, bb, cc, Aa, bB, CC, AA, bb, cc];
        $bundleNames = array_column($rows, 'bundle_name');

        // => [aa, bb, cc, Aa, bB, CC, AA];
        $bundleNamesUniq = array_unique($bundleNames);
        
        // ex: [10 => Aa, 15 => Ăa, 20 => aa, 25 => BB, 30 => cc, 35 => cC];
        $_bundles = $this->mp->findMasterBundleNameIn($bundleNamesUniq, ['id']);

        foreach($_bundles as $_bundleID => $_bundle){
            // với mỗi tên sản phẩm có được từ db ta cần check xem tên sp này có khớp với danh sách
            // tên ban đầu, nếu có thì gán id bundle tương ứng tại vị trí đó vào xóa bundle_name của 
            // rows ở vị trí đó
            if($_key = array_search($_bundle, $bundleNames)){
                $rows[$_key]['bundle_id'] = $_bundleID; // gán
                unset($rows[$_key]['bundle_name']); // xóa
                continue;
            }

            if($__key = array_search($_bundle, $bundleNamesUniq)){
                unset($bundleNamesUniq[$__key]); // xóa
                continue;
            }
        }
        // $bundleNamesUniq => [bb, bB, CC, AA]

    }

    /**
     * // ham nay muc dich toi uu hieu nan
     *
     * @param      array  $rows   The rows
     */
    private function fillUnitID(array $rows)
    {
        $unitNameUniq = array_unique(array_column($rows, 'unit_name'));
        $this->mp->findMasterBundleNameIn($unitNameUniq, ['id']);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $message  The message
     */
    private function error($message)
    {
        send_json(['status' => 'error', 'message' => $message]);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $message  The message
     */
    private function success($message)
    {
        send_json(['status' => 'success', 'message' => $message]);
    }
}