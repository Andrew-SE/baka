<?php

class MenuController extends Controller
{

    function process($parameters)
    {

        $this->preCheck();

        $microsoft = new Microsoft();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if(isset($_POST['reminder']) and $_POST['reminder']=="true"){
                $reminder=false;
                $time = 0;
            }
            else {
                $reminder=true;
                $time = $_POST['timer'];
            }

            foreach($_POST as $key => $val){
                switch($key){

                    case 'calendarPermanent':
                        $microsoft->CategoryExists();
                        if(!isset($_SESSION['calendarID'])){
                            $microsoft->CategoryCreate();
                        }

                        $microsoft->CalendarAddPermanentTimetable($reminder,$time);
                        break;
                    case 'calendarActual':
                        $microsoft->CategoryExists();
                        if(!isset($_SESSION['calendarID'])){
                            $microsoft->CategoryCreate();
                        }
                        $microsoft->CalendarAddTimetable(1,$reminder,$time);
                        break;
                    case 'calendarNextWeek':
                        $microsoft->CategoryExists();
                        if(!isset($_SESSION['calendarID'])){
                            $microsoft->CategoryCreate();
                        }
                        $microsoft->CalendarAddTimetable(0,$reminder,$time);
                        break;

                    case 'delete':
                        $microsoft->DeleteExistingTimetable($_SESSION['timetable']);
                        break;
                    case 'delete_next':
                        $microsoft->DeleteExistingTimetable($_SESSION['timetable_next']);
                        break;
                    case 'deletePermanent':
                        $microsoft->DeleteExistingPemanentTimetable();
                        break;
                }
            }
        }

        $this->header = array(
            'title' => 'Timetable menu',
            'description' => 'timetable menu'
        );

        $this->view = 'menu';
    }

    private function preCheck(){
        if (!isset( $_SESSION['timetable'])) $this->redirect('baka');
        if (!isset( $_SESSION['access_token'])) $this->redirect('baka');
    }

    private function logout(){
        $urlLogOut = LOGOUT_URL."?"."client_id=". CLIENT_ID."&response_type=code"."&redirect_uri=". REDIRECT_URL."&response_mode=query"."&scope=". SCOPE;
        header("Location:  ". $urlLogOut);
    }
}