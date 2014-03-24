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

class filterMeasures {
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
        $test = $test[0];
        return array_key_exists($test['type'], $this->types)
            && ($this->longitude_min === false || $this->latitude_min <= $test['latitude']) && ($this->longitude_max === false || $this->latitude_max >= $test['latitude'])
            && ($this->latitude_min === false || $this->latitude_min <= $test['latitude']) && ($this->latitude_max === false || $this->latitude_max >= $test['latitude'])
            && ($this->timestamp_min === false || $this->timestamp_min <= $test['timestamp']) && ($this->timestamp_max === false || $this->timestamp_max >= $test['timestamp']);
    }
}

function get_level($measure, $seuil_1, $seuil_2, $seuil_3) {
    if($measure < $seuil_1) {
        return 'low';
    }
    elseif($measure < $seuil_2) {
        return 'medium';
    }
    elseif($measure < $seuil_3) {
        return 'high';
    }
    else {
        return 'highest';
    }
}

function sort_array($array, $key) {
    $sort_keys = array();

    foreach ($array as $key2 => $entry) {
        $sort_keys[$key2] = $entry[$key];
    }

    return array_multisort($sort_keys, SORT_DESC, $array);
}

if(empty($_GET['do']) || ($_GET['do'] != 'add' && $_GET['do'] != 'get')) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

// Si on veut envoyer une donnée
if($_GET['do'] == 'add' && !empty($_GET['type']) && isset($_GET['measure']) && isset($_GET['timestamp']) && isset($_GET['long']) && isset($_GET['lat']) && !empty($_GET['api_key'])) {

    if(!array_key_exists($_GET['api_key'], $api_keys)) {
        // Send a 403 HTTP response (access forbidden)
        header('HTTP/1.1 403 Forbidden');
        exit();
    }

    $data = array();
    if(file_exists('data/'.$_GET['api_key'].'.data')) {
        $data = json_decode(gzinflate(file_get_contents('data/'.$_GET['api_key'].'.data')), true);
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

    file_put_contents('data/'.$_GET['api_key'].'.data', gzdeflate(json_encode($data)));
    exit();
}

// Si on veut récupérer les données
if($_GET['do'] == 'get') {
// ==========
// Paramètres
// ==========
    // Capteurs
    $keys = array();
    if(!empty($_GET['capteur'])) {
        foreach(explode(',', $_GET['capteur']) as $capteur) {
            $keys[array_search($capteur, $api_keys)] = $capteur;
        }
    }
    else {
        $keys = $api_keys;
    }

    // Types de mesures
    $measures_types = $types;
    if(!empty($_GET['type'])) {
        $filter_types = explode(',', $_GET['type']);
        foreach($measures_types as $type=>$type_full) {
            if(!in_array($type, $filter_types)) {
                unset($measures_types[$type]);
            }
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

// Récupération des données
    $data = array();
    foreach($keys as $key=>$capteur) {
        if(file_exists('data/'.$key.'.data')) {
            $data[$capteur] = json_decode(gzinflate(file_get_contents('data/'.$key.'.data')), true);
        }
    }

// Filtrage
    $data = array_filter($data, array(new filterMeasures($measures_types, $latitude_min, $latitude_max, $longitude_min, $longitude_max, $timestamp_min, $timestamp_max), 'filter'));
// Completion des données
    $dataset = array();
    foreach($data as $capteur=>$measures) {
        foreach($measures as $measure) {
            $dataset[] = array(
                'capteur' => $capteur,
                'latitude' => $measure['latitude'],
                'longitude' => $measure['longitude'],
                'timestamp' => $measure['timestamp'],
                'type' => $measure['type'],
                'measure' => $measure['measure'],
                'unit' => $types[$measure['type']]['unit']
            );

            if(!empty($_GET['visu'])) {
                $index = count($dataset) - 1;
                $dataset[$index]['type_name'] = $types[$measure['type']]['name'];
                $dataset[$index]['level'] = get_level($measure['measure'], $types[$measure['type']]['seuil_1'], $types[$measure['type']]['seuil_2'], $types[$measure['type']]['seuil_3']);
                $dataset[$index]['start_decrease'] = $types[$measure['type']]['start_decrease'];
                $dataset[$index]['fully_gone'] = $types[$measure['type']]['fully_gone'];
                $dataset[$index]['spatial_validity'] = $types[$measure['type']]['spatial_validity'];
            }
        }
    }
// Tri par timestamp
    sort_array($dataset, 'timestamp');

// Envoi des données
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
