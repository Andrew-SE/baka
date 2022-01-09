<?php


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
        if (!isset( $_SESSION['timetable_obj'])) $this->redirect('baka');
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

        $calendar = $this->microsoftMod->CategoryExists();
        if(!empty($calendar->value)) {
            foreach ($calendar->value as $category) {
                if ($category->displayName == $this->CATEGORY) {
                    $_SESSION['calendarID'] = $category->id;
                }
            }
        }
        if (empty($_SESSION['calendarID'])) $this->microsoftMod->CategoryCreate($this->CATEGORY);
    }

    /**
     * Reakce na dané akce od uživatele, nahrání|smazání
     * @param bool $deleteTimetableOnly true pouze smazat, false smazání i nahrání
     * @param string|null $type jaký rozvrh nahrávat|mazat, next|permanent|actual/null
     * @param bool $reminder true zapnout, false pro vypnutí
     * @param int $time číslo kolik min před eventem upozornit
     */
    private function CalendarTimetableActions(bool $deleteTimetableOnly, string $type = null, bool $reminder = false, int $time = 0){
        switch ($type){
            case "next":
                $timetable = $_SESSION['timetable_next_obj'];
                break;
            case "permanent":
                $timetable = $_SESSION['timetable_permanent_obj'];

                break;
            default:
                $timetable = $_SESSION['timetable_obj'];
        }

        if ($type == "permanent") $events = $this->CalendarGetEvents($timetable, $type);
        else $events = $this->CalendarGetEvents($timetable);

        if(!empty($events)) $this->CalendarTimetableRemove($events);

        if (!$deleteTimetableOnly) {
            if ($type == "permanent") $postFields = $this->timetableProcess->postFields($timetable, $reminder, $time, true);
            else $postFields = $this->timetableProcess->postFields($timetable, $reminder, $time);

            $this->microsoftMod->EventsArrayCreate($postFields);
        }

    }

    /**
     * Vymazat Eventy
     * @param array $eventsIds
     * @return void
     */
    private function CalendarTimetableRemove(array $eventsIds)
    {
        if(!empty($eventsIds))$this->microsoftMod->DeleteEvents($eventsIds);
    }


    /**
     * Následně se všechny eventy vyfiltrují a do pole se uloží id eventů které jsou v definované kategorii
     * @param stdClass $timetable objekt rozvrhu poodle ze kterého se berou data a časy
     * @param string|null $type "permanent" pro stálý rozvrh, jinak default
     * @param bool $fromToday true, chceme-li eventy od dneška
     * @return array pole s id všech nelezených eventů vzpadajících do definované kategorie $CATEGORY
     */
    private function CalendarGetEvents(stdClass $timetable, string $type = null, bool $fromToday = false): array
    {
        if ($fromToday)
            $firstDay = strtotime("now");
        else
            $firstDay = strtotime(current($timetable->Days)->Date);
        //Casy prvni a posledni mozne hodiny, microsoft nevidel 1. hodiny, tak jsem prebral a přidal +15 min
        $beginTime = date("H:i:s.u", strtotime("-15 min", strtotime(current($timetable->Hours)->BeginTime)));
        $endTime = date("H:i:s.u", strtotime("+15 min",  strtotime(end($timetable->Hours)->EndTime)));
        if ($type === "permanent") $weeks = 5;
        else $weeks = 1;


        //Projde všechny dny, až na víkendy, a vytáhne si všechny request pro daný den, a náslené získání eventů
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
        //echo "<br>".is_string($responses)."<br>";
        //Projde všechny eventy dne a porovná kategorie, pokud je kategorie $CATEGORY, přidá id do pole
        $events = array();
        if (!empty($responses) && is_array($responses)) {
            foreach ($responses as $response) {
                foreach ($response->body->value as $event => $val) {
                    foreach ($val->categories as $category) {
                        if ($category == $this->CATEGORY) $events[] = $val->id;
                    }
                }
            }
        }

        return $events;
    }



}
