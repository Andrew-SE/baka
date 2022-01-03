<?php

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

}