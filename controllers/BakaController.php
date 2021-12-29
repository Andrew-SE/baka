<?php

class BakaController extends Controller
{

    function process($parameters)
    {
        $bak = new Baka();


        $this->header = array(
            'title' => 'Bakalari login Form',
            'description' => 'Bakalari login form'
        );
    /*
       //Priparava na vyber skol
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
    */


        $this->data['warning']="";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if(isset($_POST['login'])){

                //$shool = $_POST['school'];
                $_SESSION['time']=gettimeofday(true);
                $shool = "https://bakalari.uzlabina.cz";
                if(isset($_POST['bakaUser']) && isset($_POST['bakaPass']) && isset($shool)){

                    $bak->Login($bak->SqlInjPrevent($_POST['bakaUser']),$_POST['bakaPass'],$shool);
                    if(!isset($_SESSION['bakalari_token'])) $this->data['warning'] = "Incorrect credenctials";
                    else{
                        $bak->Timetable($_SESSION['bakalari_token'],$shool);
                        $bak->TimetablePermanent($_SESSION['bakalari_token'],$shool);
                        $bak->TimetableNextWeek($_SESSION['bakalari_token'],$shool);
                        $this->redirect('microsoft');

                    }
                }
                else $this->data['warning'] = "Incomplete credenctials";
            }
        }

        $this->view = 'bakaForm';
    }
}