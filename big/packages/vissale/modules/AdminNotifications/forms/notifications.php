<?php

class NotificationsForm extends Form
{
    function __construct(){
        Form::Form('NotificationsForm');
        $this->link_js('assets/admin/scripts/tinymce/tinymce.min.js');
        if (URL::get('action') == 'add-new' && Url::post('content')) {
            $data['success'] = false;
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                $is_public = empty(Url::post('group_id')) ? 1 : 2;
                $rows = [
                    'notificationable_type' => 2,
                    'content' => Url::post('content'),
                    'is_public' => $is_public,
                    'title' => Url::post('title')
                ];
                $type = 1;
                if (Url::post('type')) {
                    $type = Url::post('type');
                }

                $rows['type'] = $type;
                if (Url::post('date_from')) {
                    $rows['date_from'] = date('Y-m-d', strtotime(Url::post('date_from')));
                }

                if (Url::post('date_to')) {
                    $rows['date_to'] = date('Y-m-d', strtotime(Url::post('date_to')));
                }

                $notification_id = DB::insert('notifications', $rows);

                if (!empty(Url::post('group_id'))) {
                    foreach (Url::post('group_id') as $group_id) {
                        $user_groups = NotificationsDB::getUsersSystemOfGroup($group_id);
                        foreach ($user_groups as $user) {
                            DB::insert('notifications_recieved', [
                                'notification_id' => $notification_id,
                                'user_id' => $user['id'],
                                'group_id' => $group_id
                            ]);
                        }
                    }
                }

                $data['success'] = true;
                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'show-modal' && Url::get('id')) {
            $id = Url::get('id');
            $result = DB::fetch("SELECT title, type, content, date_from, date_to FROM notifications WHERE id = $id");
            $groups = NotificationsDB::getGroups();
            $groups_id = NotificationsDB::getGroupsByNotification($id);

            echo $html = $this->show_modal_edit($result, $groups_id, $groups); die();
        }

        if (URL::get('action') == 'delete' && Url::post('id')) {
            $data['success'] = false;
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                $notification_id = Url::post('id');
                DB::delete_id('notifications', $notification_id);
                DB::delete('notifications_recieved', "notification_id = $notification_id");

                $data['success'] = true;
                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            }

            echo json_encode($data); die();
        }

        if (URL::get('action') == 'save-edit' && Url::post('id')) {
            $data['success'] = false;
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                $is_public = empty(Url::post('group_id')) ? 1 : 2;
                $notification_id = Url::post('id');
                $date_from = "";
                if (Url::post('date_from')) {
                    $date_from = date('Y-m-d', strtotime(Url::post('date_from')));
                }

                $date_to = "";
                if (Url::post('date_to')) {
                    $date_to = date('Y-m-d', strtotime(Url::post('date_to')));
                }

                $rows = [
                    'content' => Url::post('content'),
                    'is_public' => $is_public,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'type' => Url::post('type'),
                    'title' => Url::post('title')
                ];
                DB::update_id('notifications', $rows, $notification_id);
                if (!empty(Url::post('group_id'))) {
                    $groups_id = NotificationsDB::getGroupsByNotification($notification_id);
                    $groups = Url::post('group_id');
                    $groups_delete = array_diff($groups_id, $groups);
                    $groups_add_new = array_diff($groups, $groups_id);
                    if (!empty($groups_delete)) {
                        $groups_delete = join("','", $groups_delete);
                        DB::delete('notifications_recieved', "notification_id = $notification_id AND group_id IN ('$groups_delete')");
                    }

                    if ($groups_add_new) {
                        foreach ($groups_add_new as $group_id) {
                            $user_groups = NotificationsDB::getUsersSystemOfGroup($group_id);
                            foreach ($user_groups as $user) {
                                DB::insert('notifications_recieved', [
                                    'notification_id' => $notification_id,
                                    'user_id' => $user['id'],
                                    'group_id' => $group_id
                                ]);
                            }
                        }
                    }
                }

                $data['success'] = true;
                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
            }

            echo json_encode($data); die();
        }
    }

    function show_modal_edit($result, $groups_id, $groups)
    {
        $content = $result['content'];
        $opt_group = "";
        // System::debug($groups);
        // System::debug($groups_id); die();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $selected = in_array($group['id'], $groups_id) ? 'selected' : '';
                $opt_group .= '<option value="'. $group['id'] .'" '. $selected .'>'. $group['id'] . '-' . $group['name'] .'</option>';
            }
        }

        $type_arr = [1 => "Thông thường", 2 => "Popup"];
        $opt_type = "";
        foreach ($type_arr as $key => $value) {
            $selected = ($key == $result['type']) ? 'selected' : '';
            $opt_type .= '<option value="'. $key .'" '. $selected .'>'. $value .'</option>';
        }

        $hidden_date_group = ($result['type'] == 1) ? "hidden" : "";
        $date_from = !empty($result['date_from']) ? date('d-m-Y', strtotime($result['date_from'])) : "";
        $date_to = !empty($result['date_to']) ? date('d-m-Y', strtotime($result['date_to'])) : "";
        $html = '
            <div class="modal fade" id="modal-edit-notification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <form action="" id="form-edit-notification" name="form_edit_notification">
                        <input type="hidden" name="notification_id" class="notification_id" value="'. Url::get('id') .'">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Thêm mới thông báo</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="">Tiêu đề thông báo</label>
                                    <input  type="text" id="title" class="title form-control" class="form-control" value="'. $result['title'] .'">
                                </div>
                                <div class="form-group">
                                    <label for="">Nội dung thông báo</label>
                                    <textarea  id="edit-content" name="content" class="form-control content allow-enter" cols="30" rows="5" placeholder="Nội dung thông báo" required>'. $content .'</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Chọn công ty nhận thông báo</label>
                                    <select  name="group_id[]" class="form-control group_id" multiple>'. $opt_group .'</select>
                                </div>
                                <div class="form-group">
                                    <label for="">Loại thông báo</label>
                                    <select  name="type" id="type" class="form-control" required>'. $opt_type .'</select>
                                </div>
                                <div class="form-group form-group-date '. $hidden_date_group .'">
                                    <label>Thời gian hiển thị</label>
                                    <div>
                                        <div class="d-inline">Từ ngày <span class="required">*</span></div>
                                        <div class="d-inline"><input type="text" class="form-control date-popup date_from" name="date_from" id="date_from" autocomplete="disabled" value="'. $date_from .'"></div>
                                        <div class="d-inline">Đến ngày <span class="required">*</span></div>
                                        <div class="d-inline"><input type="text" class="form-control date-popup date_to" name="date_to" id="date_to" autocomplete="disabled" value="'. $date_to .'"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                                <button type="submit" class="btn btn-primary">Hoàn thành</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        ';

        return $html;
    }

    function draw()
    {
        if(!can_tuha_administrator()){
            Url::access_denied();
        }
        $data = [];
        $data['title'] = 'Danh sách thông báo';
        $cond = [];
        if (Url::get('search_text')) {
            $cond[] = "AND (n.content LIKE '%". Url::get('search_text') ."%')";
        }

        if (Url::get('noti_type')) {
            $cond[] = "AND (n.type = ". Url::get('noti_type') .")";
        }

        if (Url::get('start_date')) {
            $start_date = date('Y-m-d', Date_Time::to_time(Url::get('start_date')));
            $cond[] = "AND DATE_FORMAT(n.created_at, '%Y-%m-%d') >= '$start_date'";
        }

        if (Url::get('end_date')) {
            $end_date = date('Y-m-d', Date_Time::to_time(Url::get('end_date')));
            $cond[] = "AND DATE_FORMAT(n.created_at, '%Y-%m-%d') <= '$end_date'";
        }

        $data['noti_type_list'] = ["" => "Chọn loại thông báo", 1 => "Thông Thường", 2 => "Popup"];
        $notifications = NotificationsDB::getNotifications($cond);
        $data['notifications'] = $notifications;

        $groups = NotificationsDB::getGroups();
        $data['groups'] = $groups;

        $this->parse_layout('notifications', $data);
    }
}