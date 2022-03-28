<?php

/**
 * Kontroler pro přihlášení do Microsoft účtu
 */
class MicrosoftController extends Controller
{

    function process($parameters)
    {
        $this->timetableCheck();

        $mc = new Microsoft();
        if (!isset($_GET['code']) && !isset($_SESSION['access_token'])) {
            $urlAuth = MC_AUTH_URL . "?" . "client_id=" . CLIENT_ID . "&response_type=code" . "&redirect_uri="
                . REDIRECT_URL . "&response_mode=query" . "&scope=" . SCOPE;
            header("Location:  " . $urlAuth);
            exit();
        }
/*        elseif(isset($_SESSION['refresh_token'])){
            $tokenResponse = $mc->Token(1,null, $_SESSION['refresh_token']);
            $_SESSION['access_token'] =  $tokenResponse['access_token']; // access token is valid for 3600 seconds
            $_SESSION['refresh_token'] =  $tokenResponse['refresh_token']; // refresh token to refresh access token
        }*/
        else{
            $tokenResponse = $mc->Token($_GET['code']);
            $tokenResponse = $this->errorCheck($tokenResponse);
            $_SESSION['access_token'] =  $tokenResponse->access_token; // access token is valid for 3600 seconds
            $_SESSION['refresh_token'] =  $tokenResponse->refresh_toke0n??null; // refresh token to refresh access token
            $_SESSION['ttl_mc_access_token'] =  $tokenResponse->expires_in??null;
        }

        if(isset($_SESSION['access_token'])) $this->redirect('menu');
        else $this->redirect('microsoft');


    }

    /**
     * Zkontrolování zdali existuje rozvrh (jestli je vůbec smysl se přihlašovat do microsoftu)
     * @return void
     */
    private function timetableCheck(){
        if (!isset( $_SESSION['timetable_obj']))
        {
            $this->redirect('baka');
        }
    }

    /**
     * Check for errors
     * @param $toCheck
     * @return mixed|null
     */
    private function errorCheck($toCheck){
        $checkIt = json_decode($toCheck);
        if (isset($checkIt->error)){
            ErrorLib::error("Nastala neočekávaná chyba, zkuste to prosím znovu. ( ". $checkIt->error->code.")");
            return null;
        }
        else
            return $checkIt;

    }
}