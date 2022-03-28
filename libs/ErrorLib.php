<?php

class ErrorLib
{
    public static function error(string $message){
        require("views/errorPupUp.php");
    }
    public static function upload(){
        require("views/uploadPopUp.php");
    }
    public static function delete(){
        require("views/deletedPopUp.php");
    }

    static public function popUpCheck($toCheck){
        $checkIt = json_decode($toCheck);
        if (isset($checkIt->error)){
            ErrorController::error("Nastala neočekávaná chyba, zkuste to prosím znovu. ( ". $checkIt->error->code.")");
            return null;
        }
        else
            return $checkIt;


    }
}