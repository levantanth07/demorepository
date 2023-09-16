<?php

class RegisteredForm extends Form
{
    function __construct()
    {
        Form::Form('RegisteredForm');
    }

    function draw()
    {
        $data = array();
        $accounts = DB::fetch_all("SELECT * FROM vtp_account WHERE group_id = " . Session::get('group_id'));
        $data['accounts'] = $accounts;

        $this->parse_layout('registered', $data);
    }
}
