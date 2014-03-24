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

function get_key($api_keys, $device) {
    $key = substr(md5($device.time()), 0, 5);

    if(!array_key_exists($key, $api_keys)) {
        return $key;
    }
    else {
        return get_key($api_keys, $device.$key);
    }
}

if((!is_file('api.keys') || !is_file('data/types.data')) && !isset($_GET['settings'])) {
    header('location: ?settings=');
    exit();
}

require_once('rain.tpl.class.php');
raintpl::$tpl_dir = 'tpl/';
raintpl::$cache_dir = 'tmp/';
$tpl = new raintpl();

if(is_file('api.keys')) {
    $api_keys = json_decode(gzinflate(file_get_contents('api.keys')), true);
}
else {
    $api_keys = array();
}

if(is_file('data/types.data')) {
    $types = json_decode(gzinflate(file_get_contents('data/types.data')), true);
}
else {
    $types = array();
}

if(isset($_GET['settings'])) {
    if($_GET['settings'] == 'delete_device' && !empty($_GET['key'])) {
        if(array_key_exists($_GET['key'], $api_keys)) {
            unset($api_keys[$_GET['key']]);
            file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));
            header('location: ?settings=');
            exit();
        }
    }

    if($_GET['settings'] == 'delete_type' && !empty($_GET['id'])) {
        if(array_key_exists($_GET['id'], $types)) {
            unset($types[$_GET['id']]);
            file_put_contents('data/types.data', gzdeflate(json_encode($types)));
            header('location: ?settings=');
            exit();
        }
    }

    if(!empty($_POST['device'])) {
        $new_key = get_key($api_keys, $_POST['device']);
        if(!empty($_POST['key'])) {
            rename('data/'.$_POST['key'].'.data', 'data/'.$new_key.'.data');
            unset($api_keys[$_POST['key']]);
        }
        $api_keys[$new_key] = $_POST['device'];
        file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));
        header('location: ?settings=');
        exit();
    }

    if(!empty($_POST['name']) && !empty($_POST['unit']) && !empty($_POST['seuil_1']) && !empty($_POST['seuil_2']) && !empty($_POST['seuil_3']) && !empty($_POST['spatial_validity']) && !empty($_POST['start_decrease']) && !empty($_POST['fully_gone'])) {
        if(intval($_POST['seuil_1']) > intval($_POST['seuil_2'])) {
            exit('Le seuil 1 doit être en-deça du second seuil.');
        }
        if(intval($_POST['seuil_2']) > intval($_POST['seuil_3'])) {
            exit('Le seuil 2 doit être en-deça du troisième seuil.');
        }

        if(intval($_POST['start_decrease']) > intval($_POST['fully_gone'])) {
            exit('La durée avant le début de la diminution de l\'opacité doit être en-deça de celle correspondant à l\'opacité minimale.');
        }

        $types[$_POST['id']] = array('name' => $_POST['name'], 'unit' => $_POST['unit'], 'seuil_1' => intval($_POST['seuil_1']), 'seuil_2' => intval($_POST['seuil_2']), 'seuil_3' => intval($_POST['seuil_3']), 'spatial_validity' => intval($_POST['spatial_validity']), 'start_decrease' => intval($_POST['start_decrease']), 'fully_gone' => intval($_POST['fully_gone']));
        file_put_contents('data/types.data', gzdeflate(json_encode($types)));
        header('location: ?settings=');
        exit();
    }


    $tpl->assign('title', 'CitizenAir - Préférences');
    $tpl->assign('title_complement', ' - <a href="?settings=">Préférences</a>');
    $tpl->assign('menu', '<a href="index.php">Carte</a> | <a href="?live=">Capteur en live</a> | <a href="?export=">Export</a> | <a href="?about=">À propos</a>');

    if(!empty($_GET['settings'])) {
        $tpl->assign('settings', $_GET['settings']);
    }
    else {
        $tpl->assign('settings', false);
    }

    if(!empty($_GET['key']) && array_key_exists($_GET['key'], $api_keys)) {
        $tpl->assign('device_key', $_GET['key']);
        $tpl->assign('api_keys', $api_keys);
    }

    if(!empty($_GET['id']) && array_key_exists($_GET['id'], $types)) {
        $tpl->assign('type_id', $_GET['id']);
        $tpl->assign('types', $types);
    }

    $tpl->assign('capteurs', $api_keys);
    $tpl->assign('types', $types);

    $tpl->draw('settings');
}
elseif(isset($_GET['live'])) {
    $tpl->assign('menu', '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Choix du capteur</a> | <a href="index.php">Carte</a> | <a href="?export=">Export</a> | <a href="?about=">À propos</a>');
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>');
    $tpl->assign('capteurs', $api_keys);

    if(in_array($_GET['live'], $api_keys)) {
        $live = htmlspecialchars($_GET['live']);
        $tpl->assign('title_complement', ' - Suivi du capteur '.$live);
        $tpl->assign('title', 'CitizenAir - Suivi du capteur '.$live);
        $tpl->assign('live', $live);
    }
    else {
        $tpl->assign('title', 'CitizenAir - Capteur en live');
        $tpl->assign('title_complement', ' - Capteur en live');
    }

    $tpl->draw('live');
}
elseif(isset($_GET['export'])) {
    $tpl->assign('title', 'CitizenAir - Export');
    $tpl->assign('title_complement', ' - Export des données');
    $tpl->assign('menu', '<a href="index.php">Carte</a> | <a href="?live=">Capteur en live</a> | <a href="?about=">À propos</a>');
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>');
    $tpl->assign('no_js', true);
    $tpl->assign('capteurs', $api_keys);
    $tpl->assign('types', $types);
    $tpl->draw('export');
}
elseif(isset($_GET['about'])) {
    $tpl->assign('title', 'CitizenAir - À propos');
    $tpl->assign('title_complement', ' - À propos');
    $tpl->assign('menu', '<a href="index.php">Carte</a> | <a href="?live=">Capteur en live</a> | <a href="?export=">Export</a>');
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>');
    $tpl->assign('no_js', true);
    $tpl->draw('about');
}
else {
    $tpl->assign('title', 'CitizenAir');
    $tpl->assign('title_complement', '');
    $tpl->assign('menu', '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Légende</a> | <a href="?live=">Capteur en live</a> | <a href="?export=">Export</a> | <a href="?about=">À propos</a>');
    $tpl->assign('legend_items', $types);
    $tpl->assign('credits', '<a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>');
    $tpl->draw('index');
}
