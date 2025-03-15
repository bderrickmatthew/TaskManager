<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://127.0.0.1/api/v1/users',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('name' => 'User', 'login' => 'user', 'password' => '123'),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;