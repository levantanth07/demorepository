<?php
/**
 * Created by khoand.
 * User: apple
 * Date: 1/22/19
 * Time: 3:33 PM
 */

class AdminStatusDB{
    static function dev_ip(){
        if($_SERVER['REMOTE_ADDR']=='14.248.155.25'){
            return true;
        }else{
            return false;
        }
    }
    static function update_status_custom(){
        $ids = (array) Url::post('id');
        $positions = Url::post('position');
        $levels = Url::post('level');
        $colors = Url::post('custom_color');
        $group_id = Session::get('group_id');
        foreach ($ids as $key => $val) {
            $val = DB::escape($val);
            if($val and $status = DB::select('statuses','id='.$val)){
                $color = isset($colors[$key]) ? $colors[$key] : '';
                $position = (isset($positions[$key]) and $positions[$key]) ? $positions[$key] : '0';
                $level = (isset($levels[$key])) ? $levels[$key] : '1';
                if ($row = DB::select('statuses_custom', 'status_id=' . $val . ' and group_id=' . $group_id)) {
                    $arr = ['color' => $color, 'position' => $position,'level'=>$level];
                    // if(!$status['is_default']){
                    //     $arr['level'] = DB::escape($level);
                    // }
                    DB::update('statuses_custom', $arr, 'id=' . $row['id']);
                } else {
                    $arr = ['level'=>DB::escape($level),'status_id' => $val, 'group_id' => $group_id, 'color' => DB::escape($color), 'position' => DB::escape($position)];

                    // if($status['is_default']){
                    //     $arr['level'] = $level;
                    // }
                    DB::insert('statuses_custom', $arr);
                }
            }
        }
    }
}