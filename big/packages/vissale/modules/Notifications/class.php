<?php

class Notifications extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
        require_once 'db.php';

        switch (Url::get('do')) {
            case 'list':
                require_once "forms/list.php";
                $this->add_form(new NotificationForm());
                break;

            case 'ajax_load_data':
                $this->ajax_load_data();
            break;
            
            default:
                require_once "forms/list.php";
                $this->add_form(new NotificationForm());
                break;
        }
    }

    function ajax_load_data()
    {
        $data = ['success' => false];
        try {
            if (Url::get('page')) {
                $html = '';
                $notifications = NotificationDB::getNotifications(Url::get('page'));
                if (!empty($notifications)) {
                    foreach ($notifications as $notification) {
                        $link = 'javascript:void(0)';
                        if ($notification['notificationable_type'] == 1) {
                            $link = 'index062019.php?page=admin_orders&cmd=shipping_history&id=' . $notification['notificationable_id'];
                        }

                        $html .= '
                            <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header"><span class="time">'. date('d-m-Y H:i:s', strtotime($notification['created_at'])) .'</span></h3>
                                    <div class="timeline-body"><div><b>'. $notification['title'] .'</b></div>'. $notification['content'] .'</div>
                                </div>
                                <a href="'. $link .'" class="btn-abs" target="_blank"></a>
                            </li>
                        ';
                    }
                }

                $data = [
                    'success' => true,
                    'html' => $html
                ];
            }
        } catch (Exception $e) {
            
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
    }
}