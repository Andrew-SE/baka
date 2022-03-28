<?php

/**
 * Kořenová Class pro posílání dotazů na API endpointy
 */
class Requests{

    /**
     * Curl request metodou POST
     * @param $url
     * @param $headers
     * @param $postFields
     * @return bool|string
     */
    public function CurlPost($url, $headers , $postFields = null){

        return $this->CurlExec($url, $headers, "POST", $postFields);
    }

    /**
     * Curl request metodou GET
     * @param $url
     * @param $headers
     * @return bool|string
     */
    public function CurlGet($url, $headers )
    {

        return $this->CurlExec($url, $headers, "GET");
    }

    /** univerzální curl dotaz na nějakou url/endpoint
     * @param $url
     * @param $headers
     * @param string $request
     * @param $postFields
     * @return bool|string
     */
    public function CurlExec($url, $headers, string $request, $postFields = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request,
            CURLOPT_HTTPHEADER => $headers,
        ));

        if(!is_null(CERT_PATH)){
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, CERT_PATH);
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, CERT_PATH);

        }

        if ($request == "POST" )
            curl_setopt($curl,CURLOPT_POSTFIELDS, $postFields);


        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $_SESSION['CurlError'] = curl_error($curl);

        }
        curl_close($curl);
        return $response;
    }
}
