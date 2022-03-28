<?php

/**
 * Dotazy pro Bakaláři API
 *
 */
class Baka extends Requests
{
    private $header = "Content-Type: application/x-www-form-urlencoded";

    /**
     * Získání všech měst
     * @return bool|string
     */
    public function GetCities(){
        $url = 'https://sluzby.bakalari.cz/api/v1/municipality';
        $head = "Accept: application/json";
        $response = $this->CurlGet($url, array($head));
        return $response;
    }

    /**
     * Získání všech škol v 1 daném městě
     * @param string $city město pro výběr škol
     * @return bool|string
     */
    public function GetSchools(string $city){
        $url = 'https://sluzby.bakalari.cz/api/v1/municipality/'. $city;
        $head = "Accept: application/json";
        $response = $this->CurlGet($url, array($head));
        return $response;
    }

    /**
     * Přihlášení do Bakalářů a získání json s tokenem
     * @param string $bakaUser
     * @param string $bakaPass
     * @param string $school
     * @return string
     */
    function Login(string $bakaUser,string $bakaPass, string $school)
    {
        $postFields = "client_id=ANDR&grant_type=password&username=" . $bakaUser . "&password=" . $bakaPass;
        $urlLogin =  $school . "/api/login";

        $response = $this->CurlPost($urlLogin, array($this->header), $postFields);
        return $this->errorCheck(json_decode($response));
    }

    /**
     * Informace o uživateli
     * @param string $token
     * @param string $school
     * @return string
     */
    function GetUser(string $token, string $school)
    {
        $urlTimetable = $school . "/api/3/user";

        $headers = array($this->header, "Authorization: Bearer " . $token);
        $response = $this->CurlGet($urlTimetable, $headers);
        return $this->errorCheck(json_decode($response));
    }

    /**
     * Aktuální rozvrh
     * @param string $token
     * @param string $school
     * @return string
     */
    function Timetable(string $token, string $school)
    {
        $urlTimetable = $school . "/api/3/timetable/actual";
        $headers = array($this->header, "Authorization: Bearer " . $token);

        $response = $this->CurlGet($urlTimetable, $headers);
        return $this->errorCheck(json_decode($response));
    }

    /**
     * Stálý rozvrh
     * @param string $token
     * @param string $school
     * @return string
     */
    function TimetablePermanent(string $token, string $school)
    {
        $urlTimetable = $school . "/api/3/timetable/permanent";

        $headers = array($this->header, "Authorization: Bearer " . $token);
        $response = $this->CurlGet($urlTimetable, $headers);

        return $this->errorCheck(json_decode($response));
    }

    /**
     * Rozvrh na příší týden
     * @param string $token
     * @param string $school
     * @return string
     */
    function TimetableNextWeek(string $token, string $school)
    {
        $date = date("Y-m-d", time());
        $date = date("Y-m-d", strtotime($date . "+ 7 days"));

        $urlTimetable =  $school . "/api/3/timetable/actual?date=$date";
        $headers = array($this->header, "Authorization: Bearer " . $token);
        $response = $this->CurlGet($urlTimetable, $headers);
        return $this->errorCheck(json_decode($response));
    }

    /**
     * Zkontrolování zda-li odpověd z api neskončila chybou
     * @param $response
     * @return string
     */
    public function errorCheck($response){
        if ($response == null) return "Bakaláři neodpovídají, zkuste to později";
        if (isset($response->error)) return $response->error_description;
        else return $response;
    }

}