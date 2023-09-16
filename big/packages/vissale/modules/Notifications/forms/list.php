<?php

class NotificationForm extends Form
{

    function __construct()
    {
        Form::Form('NotificationForm');
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Thông báo';
        $data['p_notifications'] = NotificationDB::getPublicNotifications();
        $data['u_notifications'] = NotificationDB::getUserNotifications();
        $data['e_notifications'] = NotificationDB::getUserExportExcelNotifications();
        $data['pr_notifications'] = NotificationDB::getUserPrintNotifications();
        $this->parse_layout('list', $data);
    }
}
