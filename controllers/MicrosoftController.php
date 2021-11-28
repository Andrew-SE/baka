<?php

class MicrosoftController extends Controller
{

    function process($parameters)
    {
        $this->timetableCheck();

        $MC_AUTH_URL = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize";

        $mc = new Microsoft();


        if (!isset($_GET['code']) && !isset($_SESSION['access_token'])) {
            $urlAuth = $MC_AUTH_URL . "?" . "client_id=" . CLIENT_ID . "&response_type=code" . "&redirect_uri=" . REDIRECT_URL . "&response_mode=query" . "&scope=" . SCOPE;
            //echo $urlAuth;
            header("Location:  " . $urlAuth);
            exit();
        }
        else{
            $mc->Token($_GET['code']);
        }
        if(isset($_SESSION['access_token'])) $this->redirect('menu');
        else $this->redirect('microsoft');


    }

    private function timetableCheck(){
        if (!isset( $_SESSION['timetable'])) $this->redirect('baka');
    }
}