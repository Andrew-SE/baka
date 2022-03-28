<?php
/**
 * Error kontroler pro přesměrování případných erorů a zobrazení informačních popups
 */
class HelpController extends Controller
{
    public function process($parameters)
    {
        $this->header = array(
            'title'=> 'Nápověda',
            'description' => 'Nápověda' ,
            'key_words' => 'Nápověda'
        );


        $this->view='help';

    }


}