<?php
if (isset($_POST['session_id'])) {
    $session_id = $_POST['session_id'];
    $url = "https://api.themely.com/v1/sessions/retrieve/cwp/";
    $ch = curl_init($url);
    $body = json_encode(['session_id' => $session_id]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Accept: application/json'
    ]);
    $return = curl_exec($ch);
    curl_close($ch);
    echo $return;
};