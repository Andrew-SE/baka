<?php

class Requests{


    public function CurlPost($url, $headers , $postFields){

        return $this->CurlExec($url, $headers, "POST", $postFields);
    }

    public function CurlGet($url, $headers )
    {

        return $this->CurlExec($url, $headers, "GET");
    }

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
            CURLOPT_SSL_VERIFYHOST => CERT_PATH,
            CURLOPT_SSL_VERIFYPEER => CERT_PATH,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request,
            CURLOPT_HTTPHEADER => $headers,
        ));

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
