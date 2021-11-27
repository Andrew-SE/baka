<?php

class Requests{

    public function SqlInjPrevent($input)
    {
        //return strip_tags(trim($input));
        return trim($input);
    }

    public function CurlPost($url, $headers , $postFields){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers,

        ));
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo $_SESSION['error'] = curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }

    public function CurlGet($url, $headers )
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
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo $_SESSION['error'] = curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }
}
