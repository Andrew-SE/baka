<?php

/**
 * php info
 */
class InfoController extends Controller
{

    function process($parameters)
    {
        phpinfo();
        //echo   $beginTime = date("H:i:s.u", strtotime("-15 min", strtotime(current($_SESSION['timetable_obj']->Hours)->BeginTime)));
        
        //$this->view = 'bakaForm';
    }
}