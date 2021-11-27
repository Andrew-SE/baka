<?php

trait Timetable{

    //Tohle buůde chatít změnu
    public function TimetableData($id, $what,$timetable){
        //$timetable = json_decode($timetable,true);

        if($what == "DayDescription"){
            if(!empty($timetable['Days'][$id]['DayDescription']))
                return $timetable['Days'][$id]['DayDescription'];
            else return $timetable['Days'][$id]['DayType'];
        }

        foreach ($timetable as $all => $specific) {
            if($all == $what)
                foreach($specific as $first => $value){
                    foreach($value as $second =>$val ){
                        if($second == "DayType" && $val != "WokDay"){
                            if(!empty($timetable['Days'][$id]['DayDescription']))
                                return $timetable['Days'][$id]['DayDescription'];
                            else return $timetable['Days'][$id]['DayType'];
                        }
                        if($second == "Date" ){
                            $date = explode("T", $timetable['Days'][$id]['Date']);//$id== number of day 0-4
                            return $date[0];
                        }
                        else if($second == "Id" && $id == $val && $what == "Hours" ){
                            return array(date("H:i:s",strtotime($timetable['Hours'][$first]['BeginTime'])),date("H:i:s",strtotime($timetable['Hours'][$first]['EndTime'])));
                        }
                        else if($second == "Id" && $id == $val && $what == "Subjects" ){
                            return $timetable['Subjects'][$first]['Abbrev'];
                        }
                        else if($second == "Id" && $id == $val && $what == "Teachers" ){
                            return array($timetable['Teachers'][$first]['Name'],$timetable['Teachers'][$first]['Abbrev']);
                        }
                        else if($what == "Groups"&& $second == "Id" && $id == $val){
                            $grp = explode(" ",$timetable['Groups'][$first]['Abbrev']);
                            if(!empty($grp[1]))
                                return $grp;
                            else return $grp[0];
                        }
                        else if($what == "Rooms" && $second == "Id" && $id == $val){
                            return $timetable['Rooms'][$first]['Abbrev'];
                        }
                    }
                }
        }
    }

}
