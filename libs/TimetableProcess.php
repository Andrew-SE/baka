<?php

/**
 * Rozluštění json rozvrhu získaného z bakalářské api a vytvoření postfields s jednotlivými hodinami pro
 * následné nahrání hodiny do kalendáře
 *
 * -- parse rozvrhu na jednotlivé hodiny
 */
class TimetableProcess
{

    /**
     * Vytvoření pole postfields z rozvrhu pro nálsedné poslání na Microsoft API
     * @param $timetable
     * @param bool $reminderOn
     * @param int $reminderMinutes
     * @param bool $permanent
     * @param string|null $teacherId
     * @return array
     */
    public function postFields($timetable, bool $reminderOn, int $reminderMinutes, bool $permanent = false, string $teacherId = null): array
    {
        $arrayPosts = array();

        foreach ($timetable->Days as $day) {
            $dayDate = $day->Date;
            if ($day->DayType == "WorkDay") {

                foreach ($day->Atoms as $subject) {

                    /**pokud se zpracovává učitelský rozvrh a máme id uživatele, tak se zjišťuje jestli učitel danou hodinu
                     * učí a pokud danou hodinu neučít, tak se daná hodina do kalendáře nezapíše
                     */
                    if (isset($subject->TeacherId) && !is_null($teacherId)) {
                        if ($teacherId != $subject->TeacherId) continue;
                    }

                    $startDateTime = "";
                    $endDateTime = "";
                    $title = "";
                    $body = "";
                    $showAs = "busy";
                    $group = "";
                    $room = "";
                    $subObj = "";
                    $teacherObj = "";
                    $theme = "";
                    $datesObj = $this->getDateTime($timetable->Hours, $dayDate, $subject->HourId);
                    $startDateTime = $datesObj->beginTime;
                    $endDateTime = $datesObj->endTime;

                    if (!empty($subject->GroupIds)) {
                        foreach ($subject->GroupIds as $groupId) {
                            $group = $group . " " . $this->getObjValsById($timetable->Groups, $groupId)->Abbrev;
                        }
                    }

                    if (!empty($subject->SubjectId))
                        $subObj = $this->getObjValsById($timetable->Subjects, $subject->SubjectId);

                    if (!empty($subject->TeacherId))
                        $teacherObj = $this->getObjValsById($timetable->Teachers, $subject->TeacherId, true);

                    if (!empty($subject->RoomId))
                        $room = $this->getObjValsById($timetable->Rooms, $subject->RoomId)->Abbrev;

                    if (!empty($subject->Theme))
                        $theme = $subject->Theme;

                    // Změna v rozvrhu
                    if (!empty($subject->Change)) {
                        switch ($subject->Change->ChangeType) {
                            case "Canceled":
                                $showAs = "free";
                                $title = $subject->Change->Description;
                                $body = $subject->Change->Description;
                                break;
                            case "Removed":
                                $showAs = "free";
                                $title = $subject->Change->Description;
                                $body = $subject->Change->Description;
                                break;
                            case "Added":
                                $title = $subObj->Abbrev . "_" . $room . "_" . ltrim($group," ") . "_" . $teacherObj->Abbrev;
                                $body = $subject->Change->Description ;
                                break;
                            case "RoomChanged":
                                $title = $subObj->Abbrev . "_" . $room . "_" . ltrim($group," ") . "_" . $teacherObj->Abbrev;
                                $body = $subject->Change->Description;
                                break;
                            case "Substitution":
                                $title = $subObj->Abbrev . "_" . $room . "_" . ltrim($group," ") . "_" . $teacherObj->Abbrev;
                                $body = $subject->Change->Description ;
                                break;
                            default:

                        }
                    }
                    else{
                        if ($room == "DisV") {
                            $title = $room . "_" . $subObj->Abbrev . "_" . ltrim($group," ") . "_" . $teacherObj->Abbrev;
                            $body = $room . ": " . $subObj->Name . "  Téma: " . $theme . "  Třída: " . $group . "  Učitel: " . $teacherObj->Name;

                        } else {
                            $title = $subObj->Abbrev . "_" . $room . "_" . ltrim($group," ") . "_" . $teacherObj->Abbrev;
                            $body = $subObj->Abbrev . " Téma: " . $theme . " Učebna: " . $room . "  Třída: " . $group . "  Učitel: " . $teacherObj->Name;
                        }
                    }


                    $arrayPosts[] = new TimetableEventBatchPostFields($title, $body, $startDateTime, $endDateTime, $showAs,
                        CATEGORY, $reminderOn, $reminderMinutes, $permanent);



                }
            } else {
                $showAs = "free";
                if (empty($day->DayDescription)) {
                    switch ($day->DayType) {
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
                            $body = ' <a href="https://www.youtube.com/watch?v=UrHzr7V0oOU"> Bakaláři moment</a>'; //easter egg :D
                            break;
                        default:
                            $title = $day->DayType;
                            $body = $day->DayType;
                    }
                } else {
                    $title = $day->DayDescription;
                    $body = $day->DayDescription;
                }

                $datesObj = $this->getDateTime($timetable->Hours, $dayDate);

                $arrayPosts[] = new TimetableEventBatchPostFields($title, $body, $datesObj->beginTime, $datesObj->endTime,
                    $showAs, CATEGORY, $reminderOn, $reminderMinutes, $permanent);
            }
        }

        return $arrayPosts;
    }

    /**
     * Univerzální získání jednotlivých informací z objektu rozvrhu
     * @param $object
     * @param string|null $id
     * @param bool $name
     * @return stdClass
     */
    public function getObjValsById($object, ?string $id, bool $name = false): stdClass
    {
        $returnObj = new stdClass();
        foreach ($object as $item){
            if ($item->Id == $id){
                if ($name==true)
                    $returnObj->Name = $item->Name;
                $returnObj->Abbrev = $item->Abbrev;
                break;
            }
        }
        return $returnObj;
    }


    /**
     * Získání počátečního a konečného času Hodiny (eventu)
     * @param $object
     * @param string $date
     * @param int|null $id
     * @return stdClass
     */
    public function getDateTime($object, string $date, int $id = null): stdClass
    {
        $date = date("Y-m-d", strtotime($date));
        $returnObj = new stdClass();
        if ($id==null)
        {
            $returnObj->beginTime =  date("c", strtotime($date ." ". reset($object)->BeginTime));
            $returnObj->endTime =  date("c", strtotime($date ." ". end($object)->EndTime));
        }
        else
            foreach ($object as $item){
                if ($item->Id == $id){
                    $returnObj->beginTime =  date("c", strtotime($date ." ". $item->BeginTime));
                    $returnObj->endTime =  date("c", strtotime($date ." ". $item->EndTime));
                    break;
                }
            }
        return $returnObj;
    }


}

