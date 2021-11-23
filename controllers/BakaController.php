<?php

class BakaController extends Controller
{

    function process($parameters)
    {
        $bak = new Baka();
        $wrongCred = false;

        $this->header = array(
            'title' => 'Bakalari login Form',
            'key_words' => 'contact, bakalari, form',
            'description' => 'Bakalari login form'
        );
        $schoolList = array();
        $schoolCityList = json_decode($bak->GetCities(),TRUE);
        array_shift($schoolCityList);
        foreach ($schoolCityList as $city => $key){
//            var_dump($city);
            //echo $key['name'];
            //echo '<br/>';
            $school = $bak->GetSchools($key['name']);
            array_push($schoolList,$school);
            //var_dump($key['name']);
            //echo '<br/>';
        }
        var_dump($schoolList);


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST['timetable'])){
                //$shool = $_POST['school'];
                $_SESSION['time']=gettimeofday(true);
                $shool = "https://bakalari.uzlabina.cz";
                if(isset($_POST['bakaUser']) && isset($_POST['bakaPass']) && isset($shool)){

                    $bak->Login($bak->SqlInjPrevent($_POST['bakaUser']),$bak->SqlInjPrevent($_POST['bakaPass']),$shool);
                    if(!isset($_SESSION['bakalari_token'])) $wrongCred = true;
                    else{
                        $bak->Timetable($_SESSION['bakalari_token'],$shool);
                        $bak->TimetablePermanent($_SESSION['bakalari_token'],$shool);
                        $bak->TimetableNextWeek($_SESSION['bakalari_token'],$shool);

                    }
                }
                else $wrongCred = true;
            }
        }

        $this->view = 'contactForm';
    }
}