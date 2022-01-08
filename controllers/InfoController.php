<?php

class InfoController extends Controller
{

    function process($parameters)
    {
        phpinfo();
        $this->view = 'bakaForm';
    }
}