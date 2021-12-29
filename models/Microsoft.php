<?php
class Microsoft extends Requests{
    use Timetable;

    //Získání microsoft access tokenu pro přístup
    public function Token($code)
    {
        $postFields = "client_id=".CLIENT_ID
            ."&scope=".SCOPE
            ."&code=".$code
            ."&redirect_uri=".REDIRECT_URL
            ."&grant_type=authorization_code
        &client_secret=".CLIENT_SECRET;
        $headers = array("Content-Type: application/x-www-form-urlencoded");

        $response = $this->CurlPost(ACCESS_TOKEN_URL,$headers,$postFields);

        $response = json_decode($response,true);
        $_SESSION['access_token'] =  $response['access_token']; // access token is valid for 3600 seconds
        $_SESSION['refresh_token'] =  $response['access_token']; // refresh token to refresh access token
        //$_SESSION['ttl'] =  $response['expires_in'];

    }

    /* Vytvoření kategorie "Rozvrh Bakaláře",
    podle které se rezeznává rozvrh v Outlook kalendaří */
    public function CategoryCreate()
    {
        $postFields= array(
            'displayName' => 'Rozvrh Bakaláře',
            'color' => 'preset11',
        );

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );
        $this->CurlPost(CATEGORY_CREATE_URL,$headers,$postFields);
    }

    //Zjištění zdali kategorie existuje
    public function CategoryExists()
    {
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );

        $response=$this->CurlGet(CATEGORY_LIST_URL,$headers);
        $response = json_decode($response,true);
        $rozvrh = "Rozvrh Bakalaře";
        foreach($response as $all => $v){
            if(is_array($v))
                foreach($v as $calendarID => $calendar){
                    foreach($calendar as $calendarInfo =>$info){
                        if($calendarInfo == "name" && $info == $rozvrh){
                            $_SESSION['calendarID'] = $response[$all][$calendarID]['id'];
                        }
                    }
                }
        }
    }


    // Přidání rozvrhu do kalendáře (aktuální/příští týden)
    public function CalendarAddTimetable($actual,$reminder,$timer){
        if($actual == 1)
            $timetable = $_SESSION['timetable'];//json_decode($_SESSION['timetable'],true);
        else {
            $timetable = $_SESSION['timetable_next']; //json_decode($_SESSION['timetable_next'],true);
        }

        $requests = array();
        $this->DeleteExistingTimetable($timetable);

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
        $this->DeleteExistingPemanentTimetable();

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

    //Už nevím proč to tu je :d
    //potom předělat
    //predelat, ze casy se berou z rozvrhu
    public function DeleteExistingTimetable($timetable)
    {
        for($day=0;$day <= 4; $day++){
            $beginTime = $this->TimetableData($day,"Days",$timetable)."T06:00:00.0000000";
            $endTime = $this->TimetableData($day,"Days",$timetable)."T18:00:00.0000000";
            $this->GetEventsToDelete($beginTime, $endTime);
        }
    }

    public function DeleteExistingPemanentTimetable()
    {
        $timetable = $_SESSION['timetable_permanent'];
        for ($i=7; $i <= 28 ; $i+=7) {
            for($day=0;$day <= 4; $day++){
                $date = $this->TimetableData($day,"Days",$timetable);
                $date=strtotime($date);
                $date=strtotime("+$i day",$date);
                $date= date('Y-m-d',$date);
                $beginTime = $date."T06:00:00.0000000";
                $endTime = $date."T18:00:00.0000000";
                $this->GetEventsToDelete($beginTime, $endTime);
            }
        }
    }


    //Vymazání rozvrhu
    public function GetEventsToDelete($beginTime, $endTime)
    {
        $url = "https://graph.microsoft.com/v1.0/me/calendar/calendarView?startDateTime=$beginTime&endDateTime=$endTime&\$select=id,categories&\$top=100";
        $headers = array(
            "Content-Type: application/json",
            "Prefer: outlook.timezone=\"Central Europe Standard Time\"",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );
        $response=$this->CurlGet($url, $headers);
        $responseArray = json_decode($response,true);

        $categoryName = "Rozvrh Bakaláře";
        $requests = array();
        $i = 0;

        foreach($responseArray as $all => $values){
            if(is_array($values))
                foreach($values as $value => $events){
                    foreach($events as $event =>$info){
                        if(is_array($info)){
                            foreach($info as $categories => $category){
                                if($category == $categoryName){
                                    $id = $responseArray[$all][$value]['id'];
                                    if(isset($id)){
                                        $i++;
                                        array_push($requests,array(
                                            "id"=>"$i",
                                            "method"=>"DELETE",
                                            "url"=>"/me/events/".$id,
                                            "headers"=>array("Authorization"=>"Bearer ".$_SESSION['access_token']),
                                        ));

                                    }
                                    if($i>3){
                                        $request=array("requests"=>$requests);
                                        $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token']), json_encode($request));
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
        if($i != 0){
            $request=array("requests"=>$requests);
            $this->CurlPost("https://graph.microsoft.com/v1.0/\$batch", array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token']), json_encode($request));
        }
    }
}
