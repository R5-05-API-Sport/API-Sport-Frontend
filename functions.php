<?php

function request(string $method, string $url, ?array $data = null, ?string $token = null): array {
    $ch = curl_init();
    $headers = ['Accept: application/json'];
    if ($token) $headers[] = 'Authorization: Bearer '.$token;

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
    ];

    if ($data !== null) {
        $headers[] = 'Content-Type: application/json';
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    $options[CURLOPT_HTTPHEADER] = $headers;

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception(curl_error($ch));
    }

    return [
        'status_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        'data' => json_decode($response, true),
    ];
}

function getRequest($url, $token = null)          { return request('GET', $url, null, $token); }
function postRequest($url, $data, $token = null)  { return request('POST', $url, $data, $token); }
function putRequest($url, $data, $token = null)   { return request('PUT', $url, $data, $token); }
function patchRequest($url, $data, $token = null) { return request('PATCH', $url, $data, $token); }
function deleteRequest($url, $token = null)       { return request('DELETE', $url, null, $token); }