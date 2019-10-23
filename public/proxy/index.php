<?php

define('REMOTE_SERVER', 'http://cockpit.code8.cz/');

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');;
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
} else {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');;
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

$method = $_SERVER["REQUEST_METHOD"];
$request_url = REMOTE_SERVER . $_GET['remote_url_from_rewirte'];

// add GET parameters
$encoded = '';
foreach ($_GET as $name => $value) {
    if ($name !== 'remote_url_from_rewirte') {
        $encoded .= urlencode($name) . '=' . urlencode($value) . '&';
    }
}
if (strlen($encoded) > 0) {
    $request_url .= '?' . substr($encoded, 0, strlen($encoded) - 1);
}

// POST params
$post_params = '';
// include GET as well as POST variables; your needs may vary.
foreach ($_POST as $name => $value) {
    $post_params .= urlencode($name) . '=' . urlencode($value) . '&';
}
if (strlen($post_params) > 0) {
    $post_params = substr($encoded, 0, strlen($post_params) - 1);
}


// create curl resource
$ch = curl_init();
// set url
curl_setopt($ch, CURLOPT_URL, $request_url);
// show header
curl_setopt($ch, CURLOPT_HEADER, true);
//return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


// curl_setopt($ch, CURLOPT_SSLVERSION, 3);



$response = curl_exec($ch);

// parse response
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

curl_close($ch);



echo $body;
