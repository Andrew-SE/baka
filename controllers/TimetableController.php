<?php

class TimetableController extends Controller
{

    function process($parameters)
    {
        var_dump(json_decode($_SESSION['timetable_raw']));
        $timetableArray = $_SESSION['timetable'];
// $arr = json_decode($_SESSION['timetable']);
// //var_dump($arr);
        foreach ($timetableArray as $all => $value) {
            echo $all . " - " .$value ."<br>";
        }

// echo "<br>";
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
        }
    }
}