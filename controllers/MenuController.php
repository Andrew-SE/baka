<?php

/**
 * Kontroler pro menu, které nahrává/maže rozvrh
 */
class MenuController extends Controller
{

    private TimetableProcess $timetableProcess;
    private Microsoft $microsoftMod;
    private string $CATEGORY = CATEGORY;



    function process($parameters)
    {
        $this->preCheck();
        $this->microsoftMod = new Microsoft();
        $this->timetableProcess = new TimetableProcess();
        $this->calendarCategory();



        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if(isset($_POST['reminder']) and $_POST['reminder']==false){
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
                    case 'logoutMicrosoft':
                        $this->logoutMicrosoft();
                        break;
                }
            }
        }
 
        $this->data['microsoftMail'] =  $this->getMail();
        $this->data['bakaUserType'] = $_SESSION['userType'];
        $this->data['bakaName'] = $_SESSION['userName'];
        $this->header = array(
            'title' => 'Timetable menu',
            'description' => 'timetable menu'
        );

        $this->view = 'menu';

    }

    /**
     * Zjištění zda-li uživatel má vůbec mít přístum na tuto stránku
     * @return void
     */
    private function preCheck(){
        if (!isset( $_SESSION['timetable_obj']) && !isset( $_SESSION['timetable_next_obj']) && !isset( $_SESSION['timetable_permanent_obj'])) $this->redirect('baka');
        if (!isset( $_SESSION['access_token'])) $this->redirect('baka');
    }

    /**
     * Status check
     * @param string $toCheck Co se má zkontrolovat
     * @return mixed|null Vrátí decoded json pokud se nenašlo nic | false pokud se vyskytla chyba | true pokud se spustila jiná hláška
     */
    private function statusCheck(string $toCheck){
        $checkIt = json_decode($toCheck);
        if (isset($checkIt->error)){
            ErrorLib::error("Nastala neočekávaná chyba, zkuste to prosím znovu. ( ". $checkIt->error->code.")");
            return false;
        }
        elseif (isset($checkIt->uploaded)){
            ErrorLib::upload();
            return true;
        }
        elseif (isset($checkIt->deleted)){
            ErrorLib::delete();
            return true;
        }
        else
            return $checkIt;

    }

    /**
     * Odhlášení z microsoft účtu
     * @return void
     */
    private function logoutMicrosoft(){
        unset($_SESSION);
        header("Location:  ". LOGOUT_URL);
        exit();
    }


    /**
     * Zjistí zda-li existuje kategorie 'CATEGORY' (kategorie) v outlook kalendáři, případně ji vytvoří
     * @return void
     */
    private function calendarCategory(){

        $calendar = $this->statusCheck($this->microsoftMod->CategoryExists());
        if(is_object($calendar)) {
            if (!empty($calendar->value)) {
                foreach ($calendar->value as $category) {
                    if ($category->displayName == $this->CATEGORY) {
                        $_SESSION['calendarID'] = $category->id;
                    }
                }
            }
            if (empty($_SESSION['calendarID'])) $this->statusCheck($this->microsoftMod->CategoryCreate($this->CATEGORY));
        }
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

        if ($type == "permanent") $events =  $this->CalendarGetEvents($timetable, $type);
        else $events = $this->CalendarGetEvents($timetable);

        if(!empty($events)) $this->CalendarTimetableRemove($events);

        $teacherId = null;
        if ($_SESSION['userType'] == "teacher")
            $teacherId = $_SESSION['userUID'];

        if (!$deleteTimetableOnly) {
            if ($type == "permanent") $postFields = $this->timetableProcess->postFields($timetable, $reminder, $time, true, $teacherId);
            else $postFields = $this->timetableProcess->postFields($timetable, $reminder, $time, false, $teacherId);
            $this->statusCheck($this->microsoftMod->EventsArrayCreate($postFields));
        }

    }

    /**
     * Vymazat Eventy
     * @param array $eventsIds
     * @return void
     */
    private function CalendarTimetableRemove(array $eventsIds)
    {
        if(!empty($eventsIds))$this->statusCheck($this->microsoftMod->DeleteEvents($eventsIds));
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
            $eventsRequests[] = $this->GetEventsRequest($dateTimeBegin,$dateTimeEnd, $day+1);
            //Víkendy nechci
            if($day % 7 == 4) {
                $day+=2;
            }
        }
        //var_dump($this->microsoftMod->GetEvents($eventsRequests));
        $responses =  $this->statusCheck($this->microsoftMod->GetEvents($eventsRequests));

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

    /**
     * Vrátí pole s requestem pro získání evenů podle datumu a času přípraveného pro Batch
     * @param string $beginTime Začátek ve formátu Y-m-dTH:i:s.u
     * @param string $endTime Konec ve formátu Y-m-dTH:i:s.u
     * @param int $id Id pro batch
     * @return array
     */
    public function GetEventsRequest(string $beginTime, string $endTime, int $id): array
    {
        return array(
            "id"=> $id,
            "method"=> "GET",
            "url"=> "me/calendar/calendarView?startDateTime=$beginTime&endDateTime=$endTime&\$select=id,categories&\$top=100",
            "headers"=> [
                "Content-Type"=> "application/json",
                "Prefer: outlook.timezone=\"Central Europe Standard Time\"",
            ],
        );
    }

    /**
     * Získání emailové adresy aktuálně přuhlášeného microsoft účtu
     */
    private function getMail(): string
    {
        $response = $this->statusCheck($this->microsoftMod->getUser());
        if (is_object($response)){
            if (isset( $response->mail))
                return $response->mail;
            elseif (isset($response->userPrincipalName))
                return $response->userPrincipalName;
        }
        return "";
    }

}
