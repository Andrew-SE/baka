<?php
class Microsoft extends Requests{
    use Timetable;

    /**
     * Rozdělení pole na části, pro Batch
     * @param array $requestsArray
     * @return array
     */
    public function batchArraysPrep(array $requestsArray): array
    {
        return array_chunk($requestsArray,4);
    }

    public function batchArraysRequest(array $requestsArray): array
    {
        $responses = array();
        foreach ($requestsArray as $requests){
            $request=array("requests"=>$requests);
            $response = $this->CurlPost(
                "https://graph.microsoft.com/v1.0/\$batch",
                array("Content-Type: application/json","Authorization: Bearer ".$_SESSION['access_token']),
                json_encode($request));
            $responses = array_merge($responses, json_decode($response)->responses);
            //$responses[]= $this->batchRequest($requests)['responses'];
        }
        return $responses;

        //error OF Response !!!!!!!
    }


    /**
     * Získání přístupových tokenů
     * @param int $type 0 pro
     * @param string|null $code Code pro získání tokenů
     * @param string|null $refreshToken Refresh token pro obnovení 'access tokenu' bez code
     * @return mixed Response pole s tokeny
     */
    public function Token(int $type,string $code = null, string $refreshToken = null )
    {
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

    /**
     * Vytvoření kategorie, podle které se rezeznává rozvrh v Outlook kalendáři
     * @param string $name název Kategorie
     */
    public function CategoryCreate(string $name)
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

    /**
     * Získání eventů
     * @param array $requestsArray Pole s připravenými requesty pro ms batch
     * @return array
     */
    public function GetEvents(array $requestsArray): array
    {
        $preparedRequests = $this->batchArraysPrep($requestsArray);
        return $this->batchArraysRequest($preparedRequests);
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
                "url" => "/me/events/" . $events[$pos]
            );
        }

        $requestsBatch = $this->batchArraysPrep($requests);
        $this->batchArraysRequest($requestsBatch);
    }

    /**
     * Vytvoření eventu
     * @param array $postFields
     */
    public function EventCreate(array $postFields){

        $requests = array();
        for ($pos = 0; $pos < count($postFields);$pos++){
            $headers = array("Content-Type"=>"application/json");
            array_push($requests,array(
                "id"=> $pos+1,
                "method"=>"POST",
                "url"=>"/me/calendar/events",
                "body"=>$postFields[$pos],
                "headers"=>$headers,
            ));
        }

        $requestsBatch = $this->batchArraysPrep($requests);
        $response = $this->batchArraysRequest($requestsBatch);
    }
}
