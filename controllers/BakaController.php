<?php

/**
 * Kontroler pro práci s Bakaláři
 */
class BakaController extends Controller
{

    function process($parameters)
    {
        $bak = new Baka();

        $this->header = array(
            'title' => 'Bakalari login Form',
            'description' => 'Bakalari login form'
        );


        $this->data['warning']="";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST['login'])){

                //$shool = $_POST['schoois_stringl'];
                $_SESSION['time']=gettimeofday(true);
                $shool = "https://bakalari.uzlabina.cz";
                if(isset($_POST['bakaUser']) && isset($_POST['bakaPass']) && isset($shool)){

                    $tokens = $bak->Login($_POST['bakaUser'],$_POST['bakaPass'],$shool);
                    if (!is_object($tokens)) ErrorController::error($tokens);
                    else{
                        $_SESSION['bakalari_token'] = $tokens->access_token;
                        $_SESSION['bakalari_token_refresh'] = $tokens->refresh_token ?? null;

                        $thisWeek = $bak->Timetable($_SESSION['bakalari_token'],$shool);
                        $permanent = $bak->TimetablePermanent($_SESSION['bakalari_token'],$shool);
                        $next = $bak->TimetableNextWeek($_SESSION['bakalari_token'],$shool);
                        $userInfo = $bak->GetUser($_SESSION['bakalari_token'],$shool);
                        if (!is_object($thisWeek)||!is_object($permanent)||!is_object($next) || !is_object($userInfo)) {
                            //Chyby: Unauthorized | Method Not Allowed | Bad Request
                            ErrorController::error("Nastala neočekávaná chyba, zkuste to prosím znovu");
                        }
                        else{
                            $_SESSION['userType'] = $userInfo->UserType;
                            $_SESSION['userName'] = $userInfo->FullName;
                            $_SESSION['userUID'] = substr(explode("/",$userInfo->UserUID)[1],1);

                            $_SESSION['timetable_permanent_obj'] = $permanent;
                            $_SESSION['timetable_next_obj'] = $next;
                            $_SESSION['timetable_obj'] = $thisWeek;

                            $this->redirect('microsoft');
                        }
                    }
                }
                else $this->data['warning'] = "Nekompletní přihlašovací údaje";
            }
        }

        $this->view = 'bakaForm';
    }
}