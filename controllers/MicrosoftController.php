<?php

class MicrosoftController extends Controller
{


    function process($parameters)
    {
        $this->timetableCheck();

        $mc = new Microsoft();


        if (!isset($_GET['code']) && !isset($_SESSION['access_token'])) {
            $urlAuth = MC_AUTH_URL . "?" . "client_id=" . CLIENT_ID . "&response_type=code" . "&redirect_uri=" . REDIRECT_URL . "&response_mode=query" . "&scope=" . SCOPE;
            //echo $urlAuth;
            header("Location:  " . $urlAuth);
            exit();
        }
/*        elseif(isset($_SESSION['refresh_token'])){
            $tokenResponse = $mc->Token(1,null, $_SESSION['refresh_token']);
            $_SESSION['access_token'] =  $tokenResponse['access_token']; // access token is valid for 3600 seconds
            $_SESSION['refresh_token'] =  $tokenResponse['refresh_token']; // refresh token to refresh access token
        }*/
        else{
            $tokenResponse = $mc->Token(0,$_GET['code']);
            $_SESSION['access_token'] =  $tokenResponse->access_token; // access token is valid for 3600 seconds
            $_SESSION['refresh_token'] =  $tokenResponse->refresh_toke0n??null; // refresh token to refresh access token
            //$_SESSION['ttl'] =  $tokenResponse['expires_in'];
        }
        if(isset($_SESSION['access_token'])) $this->redirect('menu');
        else $this->redirect('microsoft');


    }

    private function timetableCheck(){
        if (!isset( $_SESSION['timetable_obj'])) $this->redirect('baka');
    }
}