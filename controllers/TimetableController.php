<?php
require "libs/TimetableEventPostfields.php";
require "libs/TimetableEvent.php";

class TimetableController extends Controller
{

    public stdClass $timetable;
    public bool $reminderOn = false;
    public int $reminderMinutes = 0;
    //private
   /* public function __construct($timetable, $reminderMinutes, $reminderOn = false)
    {
        $this->timetable = $timetable;
        $this->reminderOn = $reminderOn;
        $this->reminderMinutes = $reminderMinutes;
    }*/

    function process($parameters){
        $this->timetable = $_SESSION['timetable_obj'];

        print_r(json_encode($this->postFields()));
    }



    public function postFields(): array
    {
        $timetableObj = $this->timetable;
        $arrayPosts = array();

        foreach ($timetableObj->Days as $day) {
            $dayDate = $day->Date;
            if ($day->DayType == "WorkDay"){

                foreach ($day->Atoms as $subject){
                    $startDateTime = "";
                    $endDateTime = "";
                    $title = "";
                    $body = "";
                    $showAs = "busy";
                    $group = "";
                    $room = "";
                    $subObj = new NameAndAbb();
                    $teacherObj = new NameAndAbb();
                    $theme = "";

                    $datesObj = $this->getDateTime($timetableObj->Hours, $dayDate, $subject->HourId);
                    $startDateTime = $datesObj->beginTime;
                    $endDateTime = $datesObj->endTime;

                    if(!empty($subject->GroupIds)) {
                        foreach ($subject->GroupIds as $groupId){
                            $group = $group ." ". $this->getObjValsById($timetableObj->Groups,$groupId)->abbrev;
                        }
                    }

                    if (!empty($subject->SubjectId))
                        $subObj = $this->getObjValsById($timetableObj->Subjects,$subject->SubjectId);

                    if (!empty($subject->TeacherId))
                        $teacherObj = $this->getObjValsById($timetableObj->Teachers,$subject->TeacherId,true);

                    if (!empty($subject->RoomId))
                        $room = $this->getObjValsById($timetableObj->Rooms,$subject->RoomId)->abbrev;

                    if (!empty($subject->Theme))
                        $theme = $subject->Theme;

                    if ($room == "DisV"){
                        $title = $room . " - " . $subObj->abbrev ." -". $group ." - ".$teacherObj->abbrev;
                        $body = $room .": " . $subObj->name ."  Téma: ". $theme ."  Třída: ". $group . "  Učitel: ". $teacherObj->name;

                    }
                    else {
                        $title = $subObj->abbrev . " - " . $room . " -" . $group . " - " . $teacherObj->abbrev;
                        $body = $subObj->abbrev . " Téma: " . $theme ." Učebna: ". $room  . "  Třída: " . $group . "  Učitel: " . $teacherObj->name;
                    }
                    if(!empty($subject->Change)){

                        switch ($subject->Change->ChangeType){
                            case "Canceled":
                                $title = $subject->Change->Description;
                                $body =  $subject->Change->Description;
                                $showAs = "free";
                                break;
                            case "Removed":
                                $showAs = "free";
                                $title = $subject->Change->Description;
                                $body =  $subject->Change->Description;
                                break;
                            case "Added":
                                //$title = "Přidání: " . $title;
                                $body =  $subject->Change->Description  .": ". $body;
                                //break;
                            case "RoomChanged":
                                //$title = "Změna místnosti: " . $title;
                                $body =  $subject->Change->Description  .": ". $body;
                                //break;
                            case "Substitution":
                                //$title = "Suplování: " . $title;
                                $body =  $subject->Change->Description  .": ". $body;
                                //break;
                            default:

                        }
                    }
                    $arrayPosts[] = new TimetableEventBatchPostFields($title,$body,$startDateTime,$endDateTime, $showAs,
                        CATEGORY, $this->reminderOn, $this->reminderMinutes, false);
                }
            }
            else {
                $showAs = "free";
                if(empty($day->DayDescription)) {
                    switch ($day->DayType){
                        case "Weekend":
                            $title = "Víkend";
                            $body = 'Víkend';
                            break;
                        case "Celebration":
                            $title = "Významný den";
                            $body = '';
                            break;
                        case "Holiday":
                            $title = "Prázdniny";
                            $body = 'Prázdniny';
                            break;
                        case "DirectorDay":
                            $title = "Ředitelské volno";
                            $body = 'Ředitelské volno';
                            break;
                        case "Undefined":
                            $title = "Nikdo neví co se stalo";
                            $body = ' <a href="https://www.youtube.com/watch?v=UrHzr7V0oOU"> Bakaláři moment</a>';
                            break;
                        default:
                            $title = $day->DayType;
                            $body = $day->DayType;
                    }
                }
                else {
                    $title = $day->DayDescription;
                    $body = $day->DayDescription;
                }

                $datesObj = $this->getDateTime(current($timetableObj->Hours), $dayDate);
                $arrayPosts[] = new TimetableEventBatchPostFields($title,$body,$dayDate->beginTime,$datesObj->endTime,
                    $showAs, CATEGORY, $this->reminderOn, $this->reminderMinutes, false);
            }
        }
        return $arrayPosts;
    }

    public function getObjValsById($object, string $id, bool $name = false): NameAndAbb
    {
        $returnObj = new NameAndAbb();

        foreach ($object as $item){
            if ($item->Id == $id){
                if ($name==true)
                    $returnObj->name = $item->Name;
                $returnObj->abbrev = $item->Abbrev;
                break;
            }
        }
        return $returnObj;
    }

    public function getDateTime($object, string $date, int $id = null): StartEndDateTime
    {
        $date = date("Y-m-d", strtotime($date));
        $returnObj = new StartEndDateTime();
        if ($id==null)
        {
            $returnObj->beginTime =  date("c", strtotime($date ." ". $object->BeginTime));
            $returnObj->endTime =  date("c", strtotime($date ." ". $object->EndTime));
        }
        else
            foreach ($object as $item){
                if ($item->Id == $id){
                    $returnObj->beginTime =  date("c", strtotime($date ." ". $item->BeginTime));
                    $returnObj->endTime =  date("c", strtotime($date ." ". $item->EndTime));
                }
            }
        return $returnObj;
    }

}

class StartEndDateTime
{
    public string $beginTime;
    public string $endTime;
}
class NameAndAbb{
    public string $abbrev ='';
    public string $name ='';
}