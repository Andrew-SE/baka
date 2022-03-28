<?php

/**
 * Error kontroler pro přesměrování případných erorů a zobrazení informačních popups
 */
class ErrorController extends Controller
{
    public function process($parameters)
    {
        if (!empty($_SESSION['CurlError'])){
            $this->header = array(
                'title'=> 'Curl Error',
                'description' => 'Curl Error' ,
                'key_words' => 'Curl Error'
            );
            $this->data['error'] = "Curl error occurred try again later. <br> Error: " . $_SESSION['CurlError'];
            unset($_SESSION['CurlError']);
        }
        else {
            header("HTTP/1.0 404 Not Found");
            $this->header = array(
                'title' => 'Error 404',
                'description' => 'Error',
                'key_words' => 'error'
            );
            $this->data['error']="Error 404 occurred:  Page could not be found! ";
        }
        $this->view='error';

    }

    //Statické funkce pro zobrazení popupů, v jakékoliv šabloně

    public static function error(string $message){
        require("views/errorPupUp.php");
    }
    public static function upload(){
        require("views/uploadPopUp.php");
    }
    public static function delete(){
        require("views/deletedPopUp.php");
    }

}