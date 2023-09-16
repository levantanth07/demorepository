<?php

class FeedBack extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
        require_once 'db.php';

        switch (Url::get('cmd')) {
            case "feedback":
                require_once "forms/feedback.php";
                $this->add_form(new FeedBackForm());
            break;
            case "change_status":
                $this->change_status();
            break;
            case "delete_feedback":
                $this->delete_feedback();
            break;
            default:
                require_once "forms/feedback.php";
                $this->add_form(new FeedBackForm());
                break;
        }
    }

    function change_status()
    {
        $data = ['success' => false];
        if (User::is_admin()) {
            try {
                if (Url::get('id') && Url::get('read')) {
                    DB::update_id('feedbacks', ['is_read' => Url::get('read')], Url::get('id'));
                    $data['success'] = true;
                }
            } catch (Exception $e) {
                
            }
        }

        echo json_encode($data); die();
    }

    function delete_feedback()
    {
        $data = ['success' => false];
        if (User::is_admin()) {
            try {
                if (Url::get('id')) {
                DB::delete_id('feedbacks', Url::get('id'));
                    $data['success'] = true;
                }
            } catch (Exception $e) {
                
            }
        }

        echo json_encode($data); exit();
    }

    function add_feedback()
    {
        $data['success'] = false;
        if (Url::get('id') && Url::get('is_pin')) {
            DB::update_id('notes', [
                'is_pin' => Url::get('is_pin'),
                'updated_at' => date("Y-m-d H:i:s")
            ], Url::get('id'));
            $data['success'] = true;
        }

        echo json_encode($data); exit();
    }
}