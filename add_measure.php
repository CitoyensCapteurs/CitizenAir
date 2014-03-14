<?php
$types = file('types.data', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

if(!empty($_GET['measure']) && !empty($_GET['timestamp']) && !empty($_GET['time_validity']) && !empty($_GET['spatial_validity']) && !empty($_GET['long']) && !empty($_GET['lat']) && !empty($_GET['type']) && !empty($_GET['api_key'])) {
    $api_key = file_get_contents('api.key');

    if($api_key != $_GET['api_key']) {
        // Send a 403 HTTP response (access forbidden)
        header('HTTP/1.1 403 Forbidden');
        exit();
    }

    $data = array();
    if(file_exists('data.json')) {
        $data = json_decode(file_get_contents('data.json'));
    }

    $type = (in_array($_GET['type'], $types)) ? $_GET['type'] : false;
    if($type === false) {
        header('HTTP/1.1 400 Bad Request');
        exit();
    }

    $data[] = array('measure' => intval($_GET['measure']), 'type' => '', 'timestamp' => intval($_GET['timestamp']), 'time_validity' => intval($_GET['time_validity']), 'spatial_validity' => intval($_GET['time_validity']), 'longitude' => floatval($_GET['long']), 'latitude' => floatval($_GET['lat']));

    file_put_contents(json_encode('data.json'));
}
?>
