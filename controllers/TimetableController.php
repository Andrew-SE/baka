<?php

class TimetableController extends Controller
{

    function process($parameters)
    {
       // var_dump($_SESSION['timetable_next_raw']);
       $timetableArray = $_SESSION['timetable_next'];

       //var_dump($timetableArray['Hours']);
        echo $_SESSION['access_token'];

// $arr = json_decode($_SESSION['timetable']);
// //var_dump($arr);
/*
        foreach ($timetableArray as $all => $value) {
            echo $all . " - " .$value ."<br>";
        }*/



        //echo $timetableArray['Classes'][0]['Abbrev'];
        $what = 'Classes';
        $id=0;
        $timetable = $timetableArray;
        $timetable_specific= $timetableArray[$what];


        $timetable_specific= $timetableArray[$what];
       /*

        foreach($timetable_specific as $first => $value){
            foreach($value as $second =>$val ){
                echo $second .'  --  ';
                if($second == "DayType" && $val != "WokDay"){
                    if(!empty($timetable['Days'][$id]['DayDescription']))
                        echo $timetable['Days'][$id]['DayDescription'];
                    else echo $timetable['Days'][$id]['DayType'];
                }
                if($second == "Date" ){
                    $date = explode("T", $timetable['Days'][$id]['Date']);//$id== number of day 0-4
                    echo $date[0];
                }
                else if($second == "Id" && $id == $val && $what == "Hours" ){
                    print_r( array(date("H:i:s",strtotime($timetable['Hours'][$first]['BeginTime'])),date("H:i:s",strtotime($timetable['Hours'][$first]['EndTime']))));
                }
                else if($second == "Id" && $id == $val && $what == "Subjects" ){
                    echo $timetable['Subjects'][$first]['Abbrev'];
                }
                else if($second == "Id" && $id == $val && $what == "Classes"){
                    echo $timetable['Classes'][$first]['Abbrev'];
                }
                else if($second == "Id" && $id == $val && $what == "Teachers" ){
                    echo array($timetable['Teachers'][$first]['Name'],$timetable['Teachers'][$first]['Abbrev']);
                }
                else if($second == "Id" && $id == $val && $what == "Classes"){
                    echo $timetable['Classes'][$first]['Abbrev'];
                }
                else if($what == "Groups"&& $second == "Id" && $id == $val){
                    $grp = explode(" ",$timetable['Groups'][$first]['Abbrev']);
                    if(!empty($grp[1]))
                        echo $grp;
                    else echo $grp[0];
                }
                else if($what == "Rooms" && $second == "Id" && $id == $val){
                    echo $timetable['Rooms'][$first]['Abbrev'];
                }
            }
        }
        /*
        foreach ($timetableArray as $main => $values) {
            if($main == "Days")
                foreach($values as $first => $value){
                    foreach($value as $second => $val){
                        if(is_array($val)){ //if($second == "Atoms"){
                            foreach($val as $third => $vals){
                                foreach($vals as $fourth => $v){
                                    if(is_array($v)){
                                        foreach($v as $fifth => $vv){
                                            echo $main . " - " .$first ." - " .$second." - " .$third." - ".$fourth." - ".$fifth." - ".$vv ."<br>";
                                        }
                                    }
                                    else{
                                        echo $main . " - " .$first ." - " .$second." - " .$third." - ".$fourth." - ".$v ."<br>";
                                    }
                                }
                                echo "<br>";
                            }
                        }
                        else{
                            echo $main . " - " .$first ." - " .$second." - " .$val ."<br>";
                        }
                    }
                    echo "<br>";
                }
        }*/
    }
}