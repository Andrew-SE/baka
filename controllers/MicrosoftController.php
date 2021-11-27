<?php

class MicrosoftController extends Controller
{

    function process($parameters)
    {

        //$urlAuth = AUTH_URL . "?" . "client_id=" . CLIENT_ID . "&response_type=code" . "&redirect_uri=" . REDIRECT_URL . "&response_mode=query" . "&scope=" . SCOPE;
        //header("Location:  " . $urlAuth);

        $this->view = 'timetableMenu';
    }
}