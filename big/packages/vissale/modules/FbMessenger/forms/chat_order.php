<?php
class FbMessengerChatOrder extends Form{
    function __construct(){
        Form::Form('FbMessengerChatOrder');
        // $this->link_css('assets/default/css/cms.css');
        $this->link_css('assets/default/css/jquery/tabs.css');
        // $this->link_js('assets/vissale/chat/css/chat.css');
    }

    function on_submit(){
        if(Url::get('cmd') == 'save'){
            foreach($_REQUEST as $key=>$value){
                if(preg_match('/config_(.*)/',$key,$matches)){
                    if($key == 'config_product_module_enable'){
                        if($value){
                            $status = 'SHOW';
                        }else{
                            $status = 'HIDE';
                        }
                        $sql = '
							update
								function
							SET
								status="'.$status.'"
							WHERE
								'.IDStructure::child_cond(DB::structure_id('function',118)).'
							';
                        DB::query($sql);
                        header('location:?page=function&cmd=export_cache');
                        header('location:?page=setting'.''.(Url::get('a')?'&a='.Url::get('a'):''));
                    }
                    FbMessengerDB::update_setting($matches[1],$value);
                }
            }
            if($_FILES){
                foreach($_FILES as $key=>$value){
                    if(preg_match('/config_(.*)/',$key,$matches)){
                        FbMessengerDB::save_image($matches[1]);
                    }
                }
            }
            Url::js_redirect(true,'Dữ liệu đã cập nhật...!');
        }
    }
    function draw(){
        $this->map = array();
        $this->map['pages'] = FbMessengerDB::get_friendpages();
        $this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
        $this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
        $this->map['group_id'] = Session::get('group_id');
        $this->map['conversation_id'] = intval($_GET['conversation_id']);
        $this->parse_layout('chat_order',$this->map);
    }
}
?>