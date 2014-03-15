<?php
$types = json_decode(gzinflate(file_get_contents('data/types.data')));

if(!empty($_GET['type']) && !empty($_GET['measure']) && !empty($_GET['timestamp']) && !empty($_GET['long']) && !empty($_GET['lat']) && !empty($_GET['api_key'])) {
    $api_keys = file('api.keys', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if(!in_array($_GET['api_key'], $api_keys)) {
        // Send a 403 HTTP response (access forbidden)
        header('HTTP/1.1 403 Forbidden');
        exit();
    }

    $data = array();
    if(file_exists('data/'.$_GET['api_key'].'.data')) {
        $data = json_decode(gzinflate(file_get_contents('data/'.$_GET['api_key'].'.data')));
    }

    $type = (array_key_exists($_GET['type'], $types)) ? $_GET['type'] : false;
    if($type === false) {
        header('HTTP/1.1 400 Bad Request');
        exit();
    }

    $data[] = array(
        'type' => $_GET['type'],
        'measure' => intval($_GET['measure']),
        'timestamp' => intval($_GET['timestamp']),
        'longitude' => floatval($_GET['long']),
        'latitude' => floatval($_GET['lat']),
    );

    file_put_contents(gzdeflate(json_encode($data)), $_GET['api_key'].'.data');
}
