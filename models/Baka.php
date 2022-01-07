<?php

class Baka extends Requests
{
    private $header = "Content-Type: application/x-www-form-urlencoded";

    public function GetCities(){
        $url = 'https://sluzby.bakalari.cz/api/v1/municipality';
        $head = "Accept: application/json";
        $response = $this->CurlGet($url, array($head));
        return $response;
    }
    public function GetSchools($city){
        $url = 'https://sluzby.bakalari.cz/api/v1/municipality/'. $city;
        $head = "Accept: application/json";
        $response = $this->CurlGet($url, array($head));
        return $response;
    }

    function Login($bakaUser, $bakaPass, $school)
    {
        $postFields = "client_id=ANDR&grant_type=password&username=" . $bakaUser . "&password=" . $bakaPass;
        $urlLogin =  $school . "/api/login";

        $response = $this->CurlPost($urlLogin, array($this->header), $postFields);

        $response = json_decode($response, true);

        unset($_SESSION['bakalari_token']);
        $_SESSION['bakalari_token'] = $response['access_token'];

    }

    function Timetable($token, $school)
    {
        $urlTimetable = $school . "/api/3/timetable/actual";
        $headers = array($this->header, "Authorization: Bearer " . $token);

        $response = $this->CurlGet($urlTimetable, $headers);
        $_SESSION['timetable_raw']=$response;
        $_SESSION['timetable'] = json_decode($response, true);
    }

    function TimetablePermanent($token, $school)
    {
        $urlTimetable = $school . "/api/3/timetable/permanent";

        $headers = array($this->header, "Authorization: Bearer " . $token);
        $response = $this->CurlGet($urlTimetable, $headers);

        $_SESSION['timetable_permanent'] = json_decode($response, true);
    }

    function TimetableNextWeek($token, $school)
    {
        $date = date("Y-m-d", time());
        $date = date("Y-m-d", strtotime($date . "+ 7 days"));

        $urlTimetable =  $school . "/api/3/timetable/actual?date=$date";//yyyy-mm-dd;
        $headers = array($this->header, "Authorization: Bearer " . $token);
        $response = $this->CurlGet($urlTimetable, $headers);

        $_SESSION['timetable_next_raw'] = $response;
        $_SESSION['timetable_next'] = json_decode($response, true);
    }
}