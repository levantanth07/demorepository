<?php

class AdminNotifications extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'db.php';
        if(User::can_admin(true,ANY_CATEGORY) or User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)) {
            // System::debug(NotificationsDB::getUsersSystemOfGroup(1135));
            require_once 'forms/notifications.php';
            $this->add_form(new NotificationsForm());
        } else {
            URL::access_denied();
        }
    }
}