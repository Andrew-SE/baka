<?php

/**
 * Práce s microsoft Graph API
 * všechny funkce vrací json
 */
class Microsoft extends Requests{

    /**
     * Získání informací o uživateli pro následné zobrazení emailu
     * @return string
     */
    public function getUser(): string
    {
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );
        $response = $this->CurlGet("https://graph.microsoft.com/v1.0/me", $headers);
        //var_dump($response);
        return $this->errorCheck($response, false);
    }

    /**
     * Rozdělení pole na části po 4, pro Batch
     * @param array $requestsArray
     * @return array
     */
    public function batchArraysPrep(array $requestsArray): array
    {
        return array_chunk($requestsArray,4);
    }

    /**
     * Batch array request
     * @param array $requestsArray
     * @return string
     */
    public function batchArraysRequest(array $requestsArray): string
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
        return json_encode($responses);
    }


    /**
     * Získání přístupových tokenů
     * @param string|null $code Code pro získání access a refresh tokenu
     * @param string|null $refreshToken Refresh token pro obnovení 'access tokenu' bez code
     * @return string Response pole s tokeny
     */
    public function Token(string $code = null, string $refreshToken = null ): string
    {
        $postFields = "";
        if (is_null($refreshToken)) {
            $postFields = "client_id=" . CLIENT_ID
                . "&scope=" . SCOPE
                . "&code=" . $code
                . "&redirect_uri=" . REDIRECT_URL
                . "&grant_type=authorization_code
                &client_secret=" . CLIENT_SECRET;
        }
        else {
            $postFields = "client_id=" . CLIENT_ID
                . "&scope=" . SCOPE
                . "&refresh_token=" . $refreshToken
                . "&redirect_uri=" . REDIRECT_URL
                . "&grant_type=refresh_token
                &client_secret=" . CLIENT_SECRET;
        }
        $headers = array("Content-Type: application/x-www-form-urlencoded");

        $response = $this->CurlPost(ACCESS_TOKEN_URL,$headers, $postFields);

        return $this->errorCheck($response, false);
    }

    /**
     * Získání kategorií používaných v kalendářig
     * @return string
     */
    public function CategoryExists(): string
    {
        /**
         * Zjištění zdali kategorie existuje
         */
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$_SESSION['access_token'],
        );

        $response=$this->CurlGet(CATEGORY_LIST_URL,$headers);
        return $this->errorCheck($response, false);
    }


    /**
     * Vytvoření kategorii, podle které se rezeznává rozvrh v Outlook kalendáři
     * @param string $name název Kategorie
     * @return string
     */
    public function CategoryCreate(string $name): string
    {
        $postFields= array(
            'displayName' => $name,
            'color' => 'preset11',
        );
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer ". $_SESSION['access_token'],
        );
        $response = $this->CurlPost(CATEGORY_CREATE_URL,$headers,json_encode($postFields));
        return $this->errorCheck($response, false, 1);
    }


    /**
     * Získání eventů
     * @param array $requestsArray Pole s připravenými requesty pro ms batch
     * @return string
     */
    public function GetEvents(array $requestsArray): string
    {
        $preparedRequests = $this->batchArraysPrep($requestsArray);
        $response = $this->batchArraysRequest($preparedRequests);
        //Pozmenit errorCheck pro getEvents
        return $this->errorCheck($response, true);
    }


    /**
     * Smazání eventů
     * @param array $events pole s id eventů, které chceme smazat
     * @return string
     */
    public function DeleteEvents(array $events): string
    {
        $requests= array();
        for ($pos = 0; $pos < count($events);$pos++){
            $requests[] = array(
                "id" => $pos+1,
                "method" => "DELETE",
                "url" => "/me/events/" . $events[$pos]
            );
        }

        $requestsBatch = $this->batchArraysPrep($requests);
        $response = $this->batchArraysRequest($requestsBatch);
        return $this->errorCheck($response,true, 2);
    }

    /**
     * Vytvoření eventu
     * @param array $postFields
     * @return string
     */
    public function EventsArrayCreate(array $postFields): string
    {

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
        return $this->errorCheck($response,true, 1);
    }

    /**
     * základní kontrola chyb
     * @param $response
     * @param bool $multiResponse
     * @param int|null $type null - do nothing (default) |  1 - upload | 2 - delete
     * @return string
     */
    public function errorCheck($response, bool $multiResponse = false, int $type = null): string
    {
        $response = json_decode($response);
        $errorCallback = new stdClass();
        if ($multiResponse){
            foreach ($response as $resp) {
                if (isset($resp->body->error)) {
                    $errorCallback->error = "Error";
                    $errorCallback->errorCode = $response->error->code??"Neznámý chybový kód";
                    $response = $errorCallback;
                }
            }
        }
        else {
            if (isset($response->error)){
                $errorCallback->error = "Error";
                $errorCallback->errorCode = $response->error->code??"Neznámý chybový kód";
                $response = $errorCallback;
            }
        }

        if (!isset($errorCallback->error) && !is_null($type)){
            switch ($type){
                case 1:
                    $errorCallback->uploaded = "Uspěšně nahráno";
                    break;
                case 2:
                    $errorCallback->deleted = "Uspěšně smazáno";
                    break;
                default:
            }
            $response = $errorCallback;
        }

        return json_encode($response);
    }
}
