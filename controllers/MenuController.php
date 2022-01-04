<?php

class MenuController extends Controller
{
    use Timetable;

    private Microsoft $microsoftMod;
    private string $CATEGORY = "Rozvrh Bakaláře";

    function __construct()
    {
        $this->preCheck();
        $this->microsoftMod = new Microsoft();
    }

    function process($parameters)
    {
        $this->preCheck();
        $this->calendarCategory();
        //echo $_SESSION['access_token'];
        $this->CalendarTimetableAdd(false, 1);
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
                        $this->microsoftMod->CalendarAddPermanentTimetable($reminder,$time);
                        break;
                    case 'calendarActual':
                        $this->microsoftMod->CalendarAddTimetable(1,$reminder,$time);

                        break;
                    case 'calendarNextWeek':
                        $this->microsoftMod->CalendarAddTimetable(0,$reminder,$time);
                        break;

                    case 'delete':
                        $this->microsoftMod->DeleteExistingTimetable($_SESSION['timetable']);
                        break;
                    case 'delete_next':
                        $this->microsoftMod->DeleteExistingTimetable($_SESSION['timetable_next']);
                        break;
                    case 'deletePermanent':
                        $this->microsoftMod->DeleteExistingPemanentTimetable();
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


    /* Kategorie v outlook kalendáři */
    private function calendarCategory(){

        //Zjištění zdali kategorie existuje
        $calendar = $this->microsoftMod->CategoryExists();
        foreach ($calendar['value'] as $category){
            if ($category['displayName'] == $this->CATEGORY){
                $_SESSION['calendarID'] = $category['id'];
            }
        }

        if (empty($_SESSION['calendarID'])) $this->microsoftMod->CategoryCreate($this->CATEGORY);
    }

    private function CalendarTimetableAdd(bool $reminder, int $timer, string $type = null){
        switch ($type){
            case "next":
                $timetable = $_SESSION['timetable_next'];

                break;
            case "permanent":
                $timetable = $_SESSION['timetable_permanent'];
                break;
            default:
                $timetable = $_SESSION['timetable'];
        }

        $this->CalendarTimetableRemove($timetable);

    }

    private function CalendarTimetableRemove($timetable){

        $beginTime = date("H:i:s.u",strtotime($timetable['Hours'][0]['BeginTime']));
        $endTime = date("H:i:s.u",strtotime($timetable['Hours'][count($timetable['Hours'])-1]['EndTime']));

        $firstDay = date( "d.m.Y" ,strtotime($timetable['Days'][0]['Date']));
        $lastDay = date( "d.m.Y" ,strtotime($timetable['Days'][count($timetable['Days'])-1]['Date']));

        //print_r( date_diff( date_create_from_format($firstDay), date_create_from_format($lastDay)) );

        //tomorrow
        //date("d.m.Y",  strtotime("+1 day", strtotime($timetable['Days'][0]['Date'])));
        $today = date("d.m.Y");
    }

}
