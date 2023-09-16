<?php
class PreViewQlbhStockInvoiceForm extends Form
{   
    protected $map;
    function __construct()
    {   
        ob_start();
        Form::Form('PreViewQlbhStockInvoiceForm');

        if(!QlbhStockInvoice::$quyen_quan_ly_kho){
            Url::access_denied();
        }
    }

    function draw()
    {
        if(!($rawOrderIDs = Url::get('order_ids'))){
            ob_get_clean();
            header('location: /index062019.php?page=qlbh_xuat_kho&cmd=add&type=EXPORT');
            exit;
        }

        require_once ROOT_PATH . 'packages/vissale/modules/QlbhStockInvoice/previewdb.php';
        $db = new PreviewQlbhStockInvoiceDB($rawOrderIDs, Url::get('warehouse_id'));
        $this->map = $db->getInvoice();

        $this->parse_layout('preview',$this->map);
    }
}
