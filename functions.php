<?php

function postRequest($url, $post_data, $token) {
    $ch = curl_init();
    $body = json_encode($post_data);
    if ($token) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($ch);
    if ($response === false) throw new Exception(curl_error($ch));
    return json_decode($response, true);
}

function getRequest($url, $token=null) {
    $ch = curl_init();
    if ($token) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
    ]);
    $response = curl_exec($ch);
    if ($response === false) throw new Exception(curl_error($ch));
    return json_decode($response, true);
}

function deleteRequest($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    if ($token) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
    ]);
    $response = curl_exec($ch);
    if ($response === false) throw new Exception(curl_error($ch));
    return json_decode($response, true);
}

function putRequest($url, $post_data, $token=null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    $body = json_encode($post_data);
    if ($token) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($ch);
    echo "error : ".curl_error($ch);
    if ($response === false) throw new Exception(curl_error($ch));
    return json_decode($response, true);
}

function patchRequest($url, $post_data, $token=null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    $body = json_encode($post_data);
    if ($token) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
    ]);
    $response = curl_exec($ch);
    echo "error : ".curl_error($ch);
    if ($response === false) throw new Exception(curl_error($ch));
    return json_decode($response, true);
}
?>