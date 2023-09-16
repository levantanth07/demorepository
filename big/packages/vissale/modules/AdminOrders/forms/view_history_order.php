<?php

class ViewHistoryOrder extends Form{
    protected $map;
    function __construct(){
        Form::Form('ViewHistoryOrder');
    }
    function draw(){
        $groupId = Session::get('group_id');
        $order_id = Url::iget('order_id');
        $page = 1;
        $layout = 'view_history_order';
        if($order_id){
            $this->map['order_revisions'] = AdminOrdersDB::get_order_revisions($page,$order_id);
        }else{
            $this->map['order_revisions'] = array();
        }
        $this->parse_layout($layout,$this->map);
    }
}
?>
