<?php
class CareDetailForm extends Form{
    protected $map;
    function __construct(){
        Form::Form('CareDetailForm');
        $this->link_js('packages/core/includes/js/jquery/jquery.MultiFile.js');
        $this->link_js('assets/standard/js/autocomplete.js');
        $this->link_css('assets/standard/css/autocomplete/autocomplete.css?v=26072019');
        $this->link_css('packages/vissale/modules/AdminOrders/css/common.css?v=30062019');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }
    function on_submit(){
        if($order_id = DB::escape(Url::post('order_id')) and $order = DB::fetch('select id,user_confirmed from orders where id='.$order_id.' and group_id='.AdminOrders::$group_id)){
            $rating = DB::escape(Url::post('rating'));
            $feedback = DB::escape( Url::get('feedback'));
            $rating_templates = Url::get('rating_template');
            if($rating_templates){
                $rating_template_ids = DB::escape(implode(',',$rating_templates));
            }else{
                $rating_template_ids = '';
            }
            $arr = ['order_id'=>$order_id];
            if($rating){
                $arr += ['cs_status_id'=>1,'cs_note'=>DB::escape(Url::get('cs_note'))];
                $arr += [
                    'order_id'=>$order_id,
                    'feedback'=>$feedback,
                    'rating_template_ids'=>$rating_template_ids
                ];
            }else{
                $arr += ['cs_status_id'=> DB::escape(Url::get('cs_status_id')),'cs_note'=>DB::escape(Url::get('cs_note')),'feedback'=>$feedback];
            }

            if($row=DB::fetch('select id,rating_user_id,cs_status_id from order_rating where order_id='.$order_id)){
                if($row['rating_user_id']){
                    if($rating>0){
                        $arr += [
                            'update_rating_user_id'=>get_user_id(),
                            'update_rating_time'=>time(),
                            'rating_point'=>$rating
                        ];
                    }
                }else{
                    if($rating){
                        $arr += [
                            'rating_user_id'=>get_user_id(),
                            'rating_time'=>time(),
                            'rating_point'=>$rating
                        ];
                    }
                }
                //System::debug($arr);die;
                DB::update('order_rating',$arr,'id='.$row['id']);
                $this->update_avg_user_rating_point($order['user_confirmed']);
                $this->update_rating_log($row['id'],$row['cs_status_id'],Url::get('cs_status_id'));
                $this->close_edit_cs($row['id']);
            }else{
                if($rating){
                    $arr += [
                        'rating_user_id'=>get_user_id(),
                        'rating_time'=>time(),
                        'rating_point'=>$rating
                    ];
                }else{
                    $arr += [
                        'rating_user_id'=>'0',
                        'rating_time'=>'0',
                        'rating_point'=>'0'
                    ];
                }
                DB::insert('order_rating',$arr);
                $this->update_avg_user_rating_point($order['user_confirmed']);
                $this->close_edit_cs($row['id']);
            }
        }
    }
    function draw(){
        $this->map = array();
        $layout = 'care_detail';
        $this->map['title'] = '';
        $this->map += AdminOrders::$item;
        $products = AdminOrdersDB::get_order_product($this->map['id'],false);
        $this->map['products'] = $products;
        $rating = AdminOrdersDB::get_rating_info($this->map['id']);
        $this->map += $rating;
        $_REQUEST['cs_status_id'] = isset($rating['cs_status_id'])?$rating['cs_status_id']:'0';
        $_REQUEST['cs_note'] = isset($rating['cs_note'])?$rating['cs_note']:'';
        $_REQUEST['feedback'] = isset($rating['feedback'])?$rating['feedback']:'';
        if(!$this->map['can_edit']){
            die('<div class="alert alert-danger">Đơn này không được chia cho bạn!</div>');
        }
        $this->map['question_templates'] = $this->get_question_templates();
        $this->map['cs_status_id_list'] = AdminOrders::$cs_status;
        $this->parse_layout($layout,$this->map);
    }
    function get_question_templates(){
        $cond = 'group_id='.AdminOrders::$group_id;
        $sql = '
				select 
					rating_question_template.*
				from 
					rating_question_template
				WHERE
					'.$cond.'
				order by 
					rating_question_template.position,rating_question_template.id  DESC
			';
        return DB::fetch_all($sql);
    }
    function update_avg_user_rating_point($user_id){
        $sql = 'select avg(rating_point) as rated_point, count(order_rating.id) as rated_quantity from order_rating join orders on orders.id=order_id where orders.user_confirmed='.$user_id.' and rating_point>0 group by user_confirmed';
        if($avg_point = DB::fetch($sql)){
            $arr = [
                'rated_point'=>$avg_point['rated_point'],
                'rated_quantity'=>$avg_point['rated_quantity']
            ];
            DB::update('users',$arr,'id='.$user_id);
            $sql = 'select avg(rated_point) as rated_point from users where users.group_id='.AdminOrders::$group_id.' and rated_point>0 group by users.group_id';
            if($group_avg_point = DB::fetch($sql)){
                $arr = [
                    'rated_point'=>$group_avg_point['rated_point'],
                    'rated_quantity'=>0
                ];
                DB::update('groups',$arr,'id='.AdminOrders::$group_id);
            }
        }
    }
    function update_rating_log($order_rating_id,$old_cs_status_id,$new_cs_status_id){
        if($order_rating = DB::select('order_rating','id='.$order_rating_id)){
            if($old_cs_status_id!=$new_cs_status_id){
                DB::insert('order_rating_log_time',
                    [
                        'order_rating_id'=>$order_rating_id,
                        'cs_status_id'=>$new_cs_status_id,
                        'time'=>time(),
                        'user_id'=>get_user_id()
                    ]
                );
            }
        }
    }
    static function close_edit_cs($id=false,$page_no=1){
        $stay = Url::get('stay')?true:false;
        if($stay and $id){
            Url::redirect_current(array('cmd','act','order_id'=>$id));
        }else{
            echo '
				<script>
					if(window.opener){
						'.((Url::get('cmd')=='care_detail')?'window.opener.location.reload();':'').'
						window.close();
					}else{
						window.location = "'.Url::build('admin_orders',['cmd'=>'care_list']).';";
					}
				</script>
			';
            exit();
        }
    }
}

