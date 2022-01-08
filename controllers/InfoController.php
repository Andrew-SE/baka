<?php

class BakaController extends Controller
{

    function process($parameters)
    {
        phpinfo();
        $this->view = 'bakaForm';
    }
}