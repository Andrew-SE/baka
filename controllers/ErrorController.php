<?php

class ErrorController extends Controller
{
    public function process($parameters)
    {
        header("HTTP/1.0 404 Not Found");
        $this->header = array(
            'title'=> 'Error 404',
            'description' => 'Error' ,
            'key_words' => 'error'
        );
        $this->view='error';

    }
}