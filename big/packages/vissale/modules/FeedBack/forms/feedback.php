<?php

class FeedBackForm extends Form
{

    function __construct()
    {
        Form::Form('FeedBackForm');
        if (Url::get("do") == "add_feedback" && Url::post("content")) {
            $data['success'] = false;
            try {
                $user_id = get_user_id();
                $group_id = Session::get('group_id');
                $rows = [
                    "content" => Url::post('content'),
                    "user_id" => $user_id,
                    "group_id" => $group_id,
                    "updated_at" => date("Y-m-d H:i:s")
                ];
                if (Url::post('screenshot')) {
                    $rows['screenshot'] = Url::post('screenshot');
                }
                
                DB::insert("feedbacks", $rows);
                $data['success'] = true;
            } catch (Exception $e) {
                $data['success'] = false;
            }

            echo json_encode($data); die();
        } elseif (Url::get("do") == "upload_image") {
            $data = ['success' => false];
            $upload_image = [];
            $max_upload_file_size = 2*1024*1024;
            $user_id = get_user_id();
            if ($_FILES['image_files']['size'] <= $max_upload_file_size) {
                $target_path = "upload/$user_id/" . date('Ymd') . '/';
                if (!file_exists($target_path)) {
                    mkdir($target_path, 0777, true);
                }
                
                $ext = pathinfo($_FILES['image_files']['name'], PATHINFO_EXTENSION);
                $file_name = pathinfo($_FILES['image_files']['name'], PATHINFO_FILENAME);
                $target_path = $target_path . $file_name. '_' . time() . "." . $ext; 

                if(move_uploaded_file($_FILES['image_files']['tmp_name'], $target_path)) {
                    $data['path'] = $target_path;
                    $data['success'] = true;
                } else {
                    $data['error'] = "Có lỗi xảy ra. Bạn vui lòng thử lại sau.";
                }
            } else {
                $data['error'] = "Chỉ cho phép upload file không quá 2MB";
            }
            
            echo json_encode($data);
            die();
        } else {
            if (!User::is_admin()) {
                URL::access_denied();
            }
        }
    }

    function draw()
    {
        $data = [];
        $data['title'] = "Góp ý";
        $data['groups'] = FeedBackDB::getGroups();
        require_once 'packages/core/includes/utils/paging.php';
        $feedbacks = [];
        $cond = [];
        $item_per_page = 15;
        if (Url::post('group_id')) {
            $group_id = Url::post('group_id');
            $cond[] = "AND f.group_id = $group_id";
        }

        if (Url::post('is_read')) {
            $is_read = Url::post('is_read');
            $cond[] = "AND f.is_read = $is_read";
        }

        if (Url::get('start_date')) {
            $start_date = date('Y-m-d', Date_Time::to_time(Url::get('start_date')));
            $cond[] = "AND DATE_FORMAT(f.created_at, '%Y-%m-%d') >= '$start_date'";
        }

        if (Url::get('end_date')) {
            $end_date = date('Y-m-d', Date_Time::to_time(Url::get('end_date')));
            $cond[] = "AND DATE_FORMAT(f.created_at, '%Y-%m-%d') <= '$end_date'";
        }

        $total = FeedBackDB::getAllFeedBacks($cond);
        $feedbacks = FeedBackDB::getFeedBacks($cond, $item_per_page);
        $paging = paging($total, $item_per_page,10,false,'page_no',
            array('cmd','item_per_page','group_id' ,'is_read','id', 'start_date', 'end_date')
        );
        $data['feedbacks'] = $feedbacks;
        $data['is_read_list'] = ['' => 'Trạng thái góp ý' ,1 => 'Đã xem', 2 => 'Chưa xem'];

        $this->parse_layout("feedback", $data);
    }
}