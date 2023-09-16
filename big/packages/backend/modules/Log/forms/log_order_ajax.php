<?php
class LogOrderAjaxForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('LogOrderAjaxForm');
    }

    function on_submit()
    {

    }

    function draw()
    {
        $orderId = trim(Url::iget('order_id'));
        $logs = [];
        $arr = [];
        $dataLogs = LogDB::get_order_revisions();
        if ($dataLogs) {
            foreach ($dataLogs as $key => $value) {
                foreach ($value as $k => $v) {
                    if (is_array($v)) {
                        $arr[] = $v;
                    }
                }
            }
            $logs = call_user_func_array('array_merge', $arr);
            $this->map['logs'] = $logs;
            $this->parse_layout('log_order_ajax',$this->map);
        } else {
            $message = 'FALSE';
            echo ($message);
        }
    }
}
?>
