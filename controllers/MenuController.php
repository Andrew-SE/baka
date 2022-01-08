<?php
require 'libs/TimetableProcess.php';
require "libs/TimetableEventPostfields.php";
require "libs/TimetableEvent.php";

class MenuController extends Controller
{

    private TimetableProcess $timetableProcess;
    private Microsoft $microsoftMod;
    private string $CATEGORY = CATEGORY;

    function __construct()
    {
        $this->preCheck();
        $this->microsoftMod = new Microsoft();
        $this->timetableProcess = new TimetableProcess();
    }

    function process($parameters)
    {
        $this->preCheck();
        $this->calendarCategory();


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

                    case 'calendarActual':
                        $this->CalendarTimetableActions(false,"actual", $reminder, $time);
                        break;
                    case 'calendarPermanent':
                        $this->CalendarTimetableActions(false,"permanent", $reminder, $time);
                        break;
                    case 'calendarNextWeek':
                        $this->CalendarTimetableActions(false,"next", $reminder, $time);
                        break;

                    case 'delete':
                        $this->CalendarTimetableActions(true);

                        break;
                    case 'delete_next':
                        $this->CalendarTimetableActions(true,"next");
                        break;
                    case 'deletePermanent':
                        $this->CalendarTimetableActions(true,"permanent");
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


    /**
     * Zjistí zda-li existuje kategorie $CATEGORY v outlook kalendáři, případně ji vytvoří
     * @return void
     */
    private function calendarCategory(){
        /**
         *
         */
        $calendar = $this->microsoftMod->CategoryExists();
        if(!empty($calendar['value'])) {
            foreach ($calendar['value'] as $category) {
                if ($category['displayName'] == $this->CATEGORY) {
                    $_SESSION['calendarID'] = $category['id'];
                }
            }
        }
        if (empty($_SESSION['calendarID'])) $this->microsoftMod->CategoryCreate($this->CATEGORY);
    }

    private function CalendarTimetableActions(bool $deleteTimetableOnly, string $type = null, bool $reminder = false, int $time = 0){
        switch ($type){
            case "next":
                $timetable_obj = $_SESSION['timetable_next_obj'];
                $timetable = $_SESSION['timetable_next'];
                break;
            case "permanent":
                $timetable_obj = $_SESSION['timetable_permanent_obj'];
                $timetable = $_SESSION['timetable_permanent'];
                break;
            default:
                $timetable_obj = $_SESSION['timetable_obj'];
                $timetable = $_SESSION['timetable'];
        }

        $events = $this->CalendarGetEvents($timetable);
        $this->CalendarTimetableRemove($events);

        if (!$deleteTimetableOnly) {
            $postFields = $this->timetableProcess->postFields($timetable_obj, $reminder, $time);
            $this->microsoftMod->EventCreate($postFields);
        }

    }

    /**
     * @param array $eventsIds
     * @return void
     */
    private function CalendarTimetableRemove(array $eventsIds)
    {
        if(!empty($eventsIds))$this->microsoftMod->DeleteEvents($eventsIds);
    }


    /**
     * Následně se všechny eventy vyfiltrují a do pole se uloží id eventů které jsou v definované kategorii
     * @param array $timetable Rozvrh podle ze kterého se berou data a časy
     * @param string|null $type "permanent" pro stálý rozvrh, jinak default
     * @param bool $fromToday true, chceme-li eventy od dneška
     * @return array pole s id všech nelezených eventů vzpadajících do definované kategorie $CATEGORY
     */
    private function CalendarGetEvents(array $timetable, string $type = null, bool $fromToday = false): array
    {
        /**

         */
        if ($fromToday)
            $firstDay = strtotime("now");
        else
            $firstDay = strtotime($timetable['Days'][0]['Date']);

        //Casy 1. a posledni mozne hodiny, microsoft nevidel 1. hodiny, tak jsem prebral a přidal +15 min
        $beginTime = date("H:i:s.u", strtotime("-15 min", strtotime($timetable['Hours'][0]['BeginTime'])));
        $endTime = date("H:i:s.u", strtotime("+15 min",  strtotime($timetable['Hours'][count($timetable['Hours']) - 1]['EndTime'])));
        if ($type === "permanent") $weeks = 4;
        else $weeks = 1;


        //Projde všechny dny až na víkendy a vytáhne si všechny request pro den
        //a náslené získání eventů
        $eventsRequests = array();
        for ($day = 0; $day < $weeks * 7; $day++) {
            $date = date("Y-m-d",  strtotime("+$day day", $firstDay));

            $dateTimeBegin = $date . "T". $beginTime;
            $dateTimeEnd = $date . "T". $endTime;
            $eventsRequests[] = $this->microsoftMod->GetEventsRequest($dateTimeBegin,$dateTimeEnd, $day+1);
            //Víkendy nechci
            if($day % 7 == 4) {
                $day+=2;
            }
        }
        $responses = $this->microsoftMod->GetEvents($eventsRequests);
        //Projde všechny eventy dne a porovná kategorie, pokud je kategorie $CATEGORY, přidá id do pole
        $events = array();
        if (!empty($responses)) {
            foreach ($responses as $response) {
                foreach ($response['body']['value'] as $event => $val) {
                    foreach ($val['categories'] as $category) {
                        if ($category == $this->CATEGORY) $events[] = $val['id'];
                    }
                }
            }
        }
        return $events;
    }



}
