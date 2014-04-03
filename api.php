<?php
/* 
 * This file is part of CitizenAir.
 * CitizenAir is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * CitizenAir is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CitizenAir.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!is_file('data/types.data') || !is_file('api.keys')) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}
$types = json_decode(gzinflate(file_get_contents('data/types.data')), true);
$api_keys = json_decode(gzinflate(file_get_contents('api.keys')), true);

class filterMeasurements {
    private $types, $visu;

    function __construct($types, $latitude_min, $latitude_max, $longitude_min, $longitude_max, $timestamp_min, $timestamp_max) {
        $this->types = $types;
        $this->latitude_min = $latitude_min;
        $this->latitude_max = $latitude_max;
        $this->longitude_min = $longitude_min;
        $this->longitude_max = $longitude_max;
        $this->timestamp_min = $timestamp_min;
        $this->timestamp_max = $timestamp_max;
    }

    function filter($test) {
        return array_key_exists($test['type'], $this->types)
            && ($this->longitude_min === false || $this->latitude_min <= $test['latitude']) && ($this->longitude_max === false || $this->latitude_max >= $test['latitude'])
            && ($this->latitude_min === false || $this->latitude_min <= $test['latitude']) && ($this->latitude_max === false || $this->latitude_max >= $test['latitude'])
            && ($this->timestamp_min === false || $this->timestamp_min <= $test['timestamp']) && ($this->timestamp_max === false || $this->timestamp_max >= $test['timestamp']);
    }
}

function get_level($value, $threshold_1, $threshold_2, $threshold_3) {
    if($value < $threshold_1) {
        return 'low';
    }
    elseif($value < $threshold_2) {
        return 'medium';
    }
    elseif($value < $threshold_3) {
        return 'high';
    }
    else {
        return 'highest';
    }
}

function sort_array(&$array, $key) {
    $sort_keys = array();

    foreach ($array as $key2 => $entry) {
        $sort_keys[$key2] = $entry[$key];
    }


    return array_multisort($sort_keys, SORT_DESC, $array);
}

function compute_distance($lat1, $long1, $lat2, $long2) {
    $latitude_diff = deg2rad($lat1 - $lat2);
    $longitude_diff = deg2rad($long1 - $long2);

    $a = pow(sin($latitude_diff)/2, 2) + cos(deg2rad($lat1))*cos(deg2rad($lat2))*pow(sin($longitude_diff)/2, 2);
    $c = 2*atan2(sqrt($a),sqrt(1-$a));
    $R = 6371000;
    return $R*$c;
}

if(empty($_GET['do']) || ($_GET['do'] != 'add' && $_GET['do'] != 'get')) {
    exit('ERROR : Invalid request.');
}

// Sending API
if($_GET['do'] == 'add' && !empty($_GET['type']) && isset($_GET['value']) && isset($_GET['timestamp']) && isset($_GET['long']) && isset($_GET['lat']) && !empty($_GET['api_key'])) {

    if(!array_key_exists($_GET['api_key'], $api_keys)) {
        // Send a 403 HTTP response (access forbidden) if api_key not valid
        header('HTTP/1.1 403 Forbidden');
        exit('ERROR : Wrong api key.');
    }

    // Check measurement validity : type and value
    $type = (array_key_exists($_GET['type'], $types)) ? $_GET['type'] : false;
    if($type === false || floatval($_GET['value']) < 0) {
        exit('ERROR : Invalid measurement.');
    }

    $data = array();
    if(file_exists('data/'.$_GET['api_key'].'.data')) {
        $data = json_decode(gzinflate(file_get_contents('data/'.$_GET['api_key'].'.data')), true);
    }

    // Check position difference with last measurement
    if(empty($_GET['force'])) {
        for($i = 0; $i < count($data); $i++) {
            if($data[$i]['timestamp'] > $_GET['timestamp']) {
                break;
            }
        }
        if(isset($data[$i - 1])) {
            $last = $data[$i - 1];

            if(compute_distance($_GET['latitude'], $_GET['longitude'], $last['latitude'], $last['longitude']) > 5.5 * ($_GET['timestamp'] - $last['timestamp'])) { // Sensor should move at less than 20 km/h
                exit('ERROR : Invalid measurement.');
            }
        }
    }

    $data[] = array(
        'type' => $_GET['type'],
        'value' => floatval($_GET['value']),
        'timestamp' => intval($_GET['timestamp']),
        'longitude' => floatval($_GET['long']),
        'latitude' => floatval($_GET['lat']),
    );

    sort_array($data, 'timestamp');

    file_put_contents('data/'.$_GET['api_key'].'.data', gzdeflate(json_encode($data)));
    exit('Success');
}

// Getting API
if($_GET['do'] == 'get') {
    // Sensors parameter
    $keys = array();
    if(!empty($_GET['sensor'])) {
        foreach(explode(',', $_GET['sensor']) as $sensor) {
            $keys[array_search($sensor, $api_keys)] = $sensor;
        }
    }
    else {
        $keys = $api_keys;
    }

    // Types
    $query_types = $types;
    if(!empty($_GET['type'])) {
        $filter_types = explode(',', $_GET['type']);
        foreach(array_diff(array_keys($query_types), array_keys($filter_types)) as $key) {
            unset($query_types[$key]);
        }
    }

    // Timestamp
    $timestamp_min = false;
    if(!empty($_GET['time_min'])) {
        $timestamp_min = floatval($_GET['time_min']);
    }
    $timestamp_max = false;
    if(!empty($_GET['time_max'])) {
        $timestamp_max = floatval($_GET['time_max']);
    }

    // Longitude
    $longitude_min = false;
    if(!empty($_GET['long_min'])) {
        $longitude_min = floatval($_GET['long_min']);
    }
    $longitude_max = false;
    if(!empty($_GET['long_max'])) {
        $longitude_max = floatval($_GET['long_max']);
    }

    // Latitude
    $latitude_min = false;
    if(!empty($_GET['lat_min'])) {
        $latitude_min = floatval($_GET['lat_min']);
    }
    $latitude_max = false;
    if(!empty($_GET['lat_max'])) {
        $latitude_max = floatval($_GET['lat_max']);
    }

    // Fetch data
    $data = array();
    foreach($keys as $key=>$sensor) {
        if(file_exists('data/'.$key.'.data')) {
            $data[$sensor] = json_decode(gzinflate(file_get_contents('data/'.$key.'.data')), true);
        }
    }

    // Filtering
    $data_filtered = array();
    foreach($data as $key => $array) {
        $data_filtered[$key] = array_filter($array, array(new filterMeasurements($query_types, $latitude_min, $latitude_max, $longitude_min, $longitude_max, $timestamp_min, $timestamp_max), 'filter'));
    }

    // Completion with extra info
    $dataset = array();
    foreach($data_filtered as $sensor=>$measurements) {
        foreach($measurements as $measurement) {
            $dataset[] = array(
                'sensor' => $sensor,
                'latitude' => $measurement['latitude'],
                'longitude' => $measurement['longitude'],
                'timestamp' => $measurement['timestamp'],
                'type' => $measurement['type'],
                'value' => $measurement['value'],
                'unit' => $types[$measurement['type']]['unit']
            );

            if(!empty($_GET['visu'])) {
                $index = count($dataset) - 1;
                $dataset[$index]['type_name'] = $types[$measurement['type']]['name'];
                $dataset[$index]['level'] = get_level($measurement['value'], $types[$measure['type']]['threshold_1'], $types[$measure['type']]['threshold_2'], $types[$measure['type']]['threshold_3']);
                $dataset[$index]['start_decrease'] = $types[$measurement['type']]['start_decrease'];
                $dataset[$index]['fully_gone'] = $types[$measurement['type']]['fully_gone'];
                $dataset[$index]['spatial_validity'] = $types[$measurement['type']]['spatial_validity'];
            }
        }
    }

    // Sorting
    if(!empty($_GET['sort']) && count($dataset) > 1 && in_array($_GET['sort'], array_keys($dataset[0]))) {
        sort_array($dataset, $_GET['sort']);
    }

    // Sorting
    if(!empty($_GET['format']) && $_GET['format'] == 'csv') {
        $out = fopen('php://output', 'w');
        header("Content-Type:application/csv"); 
        header("Content-Disposition:attachment;filename=citizenair.csv"); 

        if(empty($_GET['visu'])) {
            fputcsv($out, array('capteur','latitude','longitude','timestamp','type','measure','unit'));
        }
        else {
            fputcsv($out, array('capteur','latitude','longitude','timestamp','type','measure','unit', 'type_name', 'level', 'start_decrease', 'fully_gone', 'spatial_validity'));
        }
        foreach($dataset as $data) {
            fputcsv($out, $data);
        }
        fclose($out);
    }
    else {
        header("Content-Type:application/json"); 
        header("Content-Disposition:attachment;filename=citizenair.json"); 

        echo(json_encode($dataset));
    }
    exit();
}
