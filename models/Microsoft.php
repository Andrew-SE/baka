<?php
class Microsoft extends Requests{
    use Timetable;


    //Later
    public function batchRequest($requests){

        $request=array("requests"=>$requests);
        $response = $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token']), json_encode($request));
        return json_decode($response, true);

        //error OF Response
    }

    public function batchArraysPrep(array $requestsArray): array
    {
        return array_chunk($requestsArray,4);
    }

    public function batchArraysRequest(array $requestsArray): array
    {
        $responses = array();
        foreach ($requestsArray as $requests){
            $responses = array_merge($responses, $this->batchRequest($requests)['responses']);
            //$responses[]= $this->batchRequest($requests)['responses'];
        }

        return $responses;
    }


    /**
     * Získání pole obsahující přístupové tokeny
     * @param int $type 0 pro
     * @param string|null $code Code pro získání tokenů
     * @param string|null $refreshToken Refresh token pro obnovení 'access tokenu' bez code
     * @return mixed Response pole s tokeny
     */
    public function Token(int $type,string $code = null, string $refreshToken = null )
    {
        /**
         * Získání pole s access a refresh tokenem

         */
        if ($type === 0) {
            $postFields = "client_id=" . CLIENT_ID
                . "&scope=" . SCOPE
                . "&code=" . $code
                . "&redirect_uri=" . REDIRECT_URL
                . "&grant_type=authorization_code
                &client_secret=" . CLIENT_SECRET;
        }
        elseif ($type === 1) {
            $postFields = "client_id=" . CLIENT_ID
                . "&scope=" . SCOPE
                . "&refresh_token=" . $refreshToken
                . "&redirect_uri=" . REDIRECT_URL
                . "&grant_type=refresh_token
                &client_secret=" . CLIENT_SECRET;
        }
        $headers = array("Content-Type: application/x-www-form-urlencoded");

        $response = $this->CurlPost(ACCESS_TOKEN_URL,$headers,$postFields);

        return json_decode($response,true);

    }

    /**
     * Získání kategorií používaných v kalendářig
     * @return mixed
     */
    public function CategoryExists()
    {
        /**
         * Zjištění zdali kategorie existuje
         */
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );

        $response=$this->CurlGet(CATEGORY_LIST_URL,$headers);
        return json_decode($response,true);
    }

    /* Vytvoření kategorie "Rozvrh Bakaláře",
    podle které se rezeznává rozvrh v Outlook kalendaří */
    public function CategoryCreate($name)
    {
        $postFields= array(
            'displayName' => $name,
            'color' => 'preset11',
        );

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ". $_SESSION['access_token'],
        );
        $this->CurlPost(CATEGORY_CREATE_URL,$headers,$postFields);
    }

    public function GetEvents($requestsArray): array
    {
        $preparedRequests = $this->batchArraysPrep($requestsArray);
        return $this->batchArraysRequest($preparedRequests);
    }

    public function GetEventsRequest($beginTime, $endTime, $id): array
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
     * Smazání eventů
     * @param array $events pole s id eventů, které chceme smazat
     * @return void
     */
    public function DeleteEvents(array $events){
        $requests= array();

        for ($pos = 0; $pos < count($events);$pos++){
            $requests[] = array(
                "id" => $pos+1,
                "method" => "DELETE",
                "url" => "/me/events/" . $events[$pos],
                //"headers"=>array("Authorization"=>"Bearer ".$_SESSION['access_token']),
            );
        }//prepare batch and foreach batch fu

        $requestsBatch = $this->batchArraysPrep($requests);
        $this->batchArraysRequest($requestsBatch);
    }

///
///
///
///
///
///     OLD TO GO

    // Přidání rozvrhu do kalendáře (aktuální/příští týden)
    public function CalendarAddTimetable($actual,$reminder,$timer){
        if($actual == 1)
            $timetable = $_SESSION['timetable'];//json_decode($_SESSION['timetable'],true);
        else {
            $timetable = $_SESSION['timetable_next']; //json_decode($_SESSION['timetable_next'],true);
        }

        $requests = array();

        foreach($timetable as $all => $specific){
            if($all == "Days"){
                foreach($specific as $day => $daysInfo){ // days 0-4
                    $id=0;

                    foreach($daysInfo as $dayInfo => $info){
                        if($dayInfo == "DayType" && $info != "WorkDay"){
                            $description = $this->TimetableData($day,"DayDescription",$timetable);
                            if($description == "Holiday") $title = "Prázdniny";
                            else $title = $description;
                            $subtitle = "Volno";
                            $beginTime = $this->TimetableData($day,"Days",$timetable)."T07:45:00";
                            $endTime = $this->TimetableData($day,"Days", $timetable)."T17:45:00";

                            $postFields = array(
                                "subject"=> $title,
                                "body" => array(
                                    "contentType" => "HTML",
                                    "content" => "$subtitle - $title"
                                ),
                                "start" => array(
                                    "dateTime" => $beginTime,
                                    "timeZone" => "Central Europe Standard Time"
                                ),
                                "end" => array(
                                    "dateTime" => $endTime,
                                    "timeZone" => "Central Europe Standard Time"
                                ),
                                "showAs" => "free",
                                "isReminderOn" => false,
                                "categories" => array("Rozvrh Bakaláře")
                            );

                            $url = EVENT_ADD_DEFAULT_CAL;
                            $headers = array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token']);
                            $this->CurlPost($url, $headers, json_encode($postFields));

                            break;
                        }
                        if($dayInfo == "Atoms"){
                            foreach($info as $lessons => $lessonID){
                                foreach($lessonID as $lesson => $lessonInfo){
                                    $description = "";
                                    switch ($lesson)
                                    {
                                        case "HourId":
                                            $time = $this->TimetableData($lessonInfo,"Hours", $timetable);
                                            $date = $this->TimetableData($day,"Days", $timetable);
                                            $beginTime = $date."T".$time[0];
                                            $endTime = $date."T".$time[1];
                                            break;
                                        case "GroupIds":
                                            foreach($lessonInfo as $groups =>$group){
                                                $grp = $this->TimetableData($group,"Groups", $timetable);
                                                if(is_array($grp)){
                                                    $group = $grp[0]."-".$grp[1];
                                                }
                                                else $group = $grp;
                                            }
                                            break;

                                        case "SubjectId":
                                            $subject = $this->TimetableData($lessonInfo, "Subjects", $timetable);
                                            break;
                                        case "TeacherId":
                                            $teachers = $this->TimetableData($lessonInfo, "Teachers", $timetable);
                                            if(isset($teachers)){
                                                $teacher_abbrev = $teachers[1];
                                                $teacher = $teachers[0];
                                            }
                                            else {
                                                $teacher_abbrev = "";
                                                $teacher = "---";
                                            }
                                            break;
                                        case "RoomId":
                                            $room = $this->TimetableData($lessonInfo, "Rooms", $timetable);

                                            break;
                                        case "Theme":
                                            if(isset($lessonInfo))
                                                $theme = $lessonInfo;
                                            else
                                                $theme = "---";
                                            break;
                                        case "Change":
                                            if(is_array($lessonInfo))
                                                foreach($lessonInfo as $ch => $value){
                                                    switch ($value) {
                                                        case "Added":
                                                            $edit = "Přidáno";
                                                            break;
                                                        case "Removed":
                                                            $description = $timetable['Days'][$day]['Atoms'][$lessons]['Change']['Description'];
                                                            $subjectR = $description;
                                                            break;
                                                        case "Canceled":
                                                            $description = $timetable['Days'][$day]['Atoms'][$lessons]['Change']['Description'];
                                                            $subjectR = $description;
                                                            break;
                                                        case "RoomChanged":
                                                            $edit = "Změna místnosti";
                                                            break;
                                                    }
                                                }
                                            break;
                                    }
                                }
                                if(isset($subjectR)){
                                    $subjectText = $subjectR;
                                    $content = $subjectText;
                                    $reminder = false;
                                    $timer = 0;
                                    $showAs = "free";
                                }
                                else{
                                    if($room=="DisV")
                                        $subjectText = "(".$room.")-".$subject."_".$teacher_abbrev."_".$group;
                                    else
                                        $subjectText = $subject."_".$teacher_abbrev."_".$group;
                                    $content =  $subject . "_" . $group . "  Třída: " . $room . "  Učitel: " . $teacher . "  Téma: " . $theme;
                                    if(isset($edit)){
                                        $content = $edit . " - " . $content;
                                    }
                                    $showAs = "busy";
                                    //$reminder = true;
                                    //$timer = 5;
                                }

                                $postFields = array(
                                    "subject"=> "$subjectText",
                                    "body" => array(
                                        "contentType" => "HTML",
                                        "content" => "$content"
                                    ),
                                    "start" => array(
                                        "dateTime" => $beginTime,
                                        "timeZone" => "Central Europe Standard Time"
                                    ),
                                    "end" => array(
                                        "dateTime" => $endTime,
                                        "timeZone" => "Central Europe Standard Time"
                                    ),
                                    "isReminderOn" => $reminder,
                                    "reminderMinutesBeforeStart" => $timer,
                                    "showAs" => $showAs,
                                    "categories" => array("Rozvrh Bakaláře")
                                );
                                if(isset($showAs))unset($showAs);
                                unset($subjectR, $group);
                                $url = EVENT_ADD_DEFAULT_CAL;
                                $headers = array("Content-Type"=>"application/json");
                                $id++;
                                array_push($requests,array(
                                    "id"=>"$id",
                                    "method"=>"POST",
                                    "url"=>"/me/calendar/events",
                                    "body"=>$postFields,
                                    "headers"=>$headers,
                                ));

                                if($id == 4){
                                    $headers = array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token'] );
                                    $request=array("requests"=>$requests);

                                    $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", $headers, json_encode($request));

                                    unset($request);
                                    unset($requests);
                                    $request = array();
                                    $requests = array();
                                    $id = 0;
                                }
                            }
                            if($id != 0){
                                $headers = array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token'] );
                                $request=array("requests"=>$requests);
                                $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", $headers, json_encode($request));

                                unset($request);
                                unset($requests);
                                $request = array();
                                $requests = array();
                                $id = 0;
                            }

                        }
                    }
                }
            }
        }
    }

    // Přidání stáleho rozvrhu na měsíc do kalendáře
    public function CalendarAddPermanentTimetable($reminder,$timer){
        $timetable_permanent = $_SESSION['timetable_permanent'];//json_decode($_SESSION['timetable_permanent'],true);

        $requests = array();

        for ($i=7; $i <= 28 ; $i+=7) {
            foreach($timetable_permanent as $all => $specific){
                if($all == "Days"){
                    foreach($specific as $day => $daysInfo){
                        foreach($daysInfo as $dayInfo => $info){
                            if($dayInfo == "Atoms"){
                                $id = 0;
                                foreach($info as $lessons => $lessonID){
                                    foreach($lessonID as $lesson => $lessonInfo){
                                        switch ($lesson) {
                                            case "HourId":
                                                $time = $this->TimetableData($lessonInfo,"Hours",$timetable_permanent);
                                                $date = $this->TimetableData($day,"Days",$timetable_permanent);

                                                $date=strtotime($date);
                                                $date=strtotime("+$i day",$date);
                                                $date= date('Y-m-d',$date);

                                                $beginTime = $date."T".$time[0];
                                                $endTime = $date."T".$time[1];
                                                break;
                                            case "GroupIds":
                                                foreach($lessonInfo as $groups =>$group){
                                                    $grp = $this->TimetableData($group,"Groups",$timetable_permanent);
                                                    if(is_array($grp)){
                                                        $group = $grp[0]."-".$grp[1];
                                                    }
                                                    else $group = $grp;
                                                }
                                                break;

                                            case "SubjectId":
                                                $subject = $this->TimetableData($lessonInfo, "Subjects",$timetable_permanent);
                                                break;
                                            case "TeacherId":
                                                $teachers = $this->TimetableData($lessonInfo, "Teachers",$timetable_permanent);
                                                $teacher_abbrev = $teachers[1];
                                                $teacher = $teachers[0];
                                                break;
                                            case "RoomId":
                                                $room = $this->TimetableData($lessonInfo, "Rooms",$timetable_permanent);
                                                break;
                                        }
                                    }
                                    $postFields = array(
                                        "subject"=> "$subject"."_$teacher_abbrev"."_$group",
                                        "body" => array(
                                            "contentType" => "HTML",
                                            "content" => "$subject - $group  Třída: $room  Učitel: $teacher"
                                        ),
                                        "start" => array(
                                            "dateTime" => $beginTime,
                                            "timeZone" => "Central Europe Standard Time"
                                        ),
                                        "end" => array(
                                            "dateTime" => $endTime,
                                            "timeZone" => "Central Europe Standard Time"
                                        ),
                                        "isReminderOn"=>$reminder,
                                        "reminderMinutesBeforeStart" => $timer,
                                        "categories" => array("Rozvrh Bakaláře")
                                    );

                                    $url = EVENT_ADD_DEFAULT_CAL;
                                    $headers = array("Content-Type"=>"application/json");
                                    $id++;
                                    array_push($requests,array(
                                        "id"=>"$id",
                                        "method"=>"POST",
                                        "url"=>"/me/calendar/events",
                                        "body"=>$postFields,
                                        "headers"=>$headers,
                                    ));

                                    if($id == 4){
                                        $headers = array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token'] );
                                        $request=array("requests"=>$requests);

                                        $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", $headers, json_encode($request));

                                        unset($request);
                                        unset($requests);
                                        $request = array();
                                        $requests = array();
                                        $id = 0;
                                    }
                                }
                                if($id != 0){
                                    $headers = array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token'] );
                                    $request=array("requests"=>$requests);
                                    $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", $headers, json_encode($request));

                                    unset($request);
                                    unset($requests);
                                    $request = array();
                                    $requests = array();
                                    $id = 0;
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}
