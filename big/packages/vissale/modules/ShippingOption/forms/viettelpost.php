<?php

class ViettelPostForm extends Form
{
    function __construct()
    {
        Form::Form('ViettelPostForm');
    }

    function draw()
    {
        $data = array();
        $this->parse_layout('viettelpost', $data);
    }
}
