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

/* Functions */
require_once('rain.tpl.class.php');
raintpl::$tpl_dir = 'tpl/';
raintpl::$cache_dir = 'tmp/';
$tpl = new raintpl();

function get_key($api_keys, $sensor) {
    $key = substr(md5($sensor.time()), 0, 5);

    if(!array_key_exists($key, $api_keys)) {
        return $key;
    }
    else {
        return get_key($api_keys, $sensor.$key);
    }
}

function br2nl($string)
{
    return str_replace('<br />', "\n", $string);
}


/* Config test and define keys and types */
if((!is_file('api.keys') || !is_file('data/types.data')) && !isset($_GET['settings'])) {
    header('location: ?settings=');
    exit();
}

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

$menu = array(
    'carte'=>'<a href="index.php">Carte</a>',
    'live'=>' | <a href="?live=">Capteur en live</a>',
    'export'=>' | <a href="?export=">Export</a>',
    'about'=>' | <a href="?about=">À propos</a>',
    'support'=>' | <a href="?participez=">Participez&nbsp;!</a>');

/* Settings page */
if(isset($_GET['settings'])) {
    session_start();

    // Disconnect
    if($_GET['settings'] == 'logout') {
        session_destroy();
        header('location: index.php');
    }

    // Connection / Password initialization
    if(!empty($_POST['password'])) {
        if(is_file('password') && file_get_contents('password') == sha1($_POST['password'])) {
            $_SESSION['login'] = true;
            header('location: ?settings=');
        }
        elseif(!is_file('password')) {
            file_put_contents('password', sha1($_POST['password']));
            $_SESSION['login'] = true;
            header('location: ?settings=');
        }
    }

    $tpl->assign('head_title', ' - Préférences');
    $tpl->assign('page_title', ' - <a href="?settings=">Préférences</a>');
    $tpl->assign('menu', $menu);
    $tpl->assign('no_js', true);

    if(empty($_SESSION['login'])) {
        if(is_file('password')) {
            $tpl->assign('configured', 1);
        }
        else {
            $tpl->assign('configured', 0);
        }
        $tpl->assign('login', true);
        $tpl->draw('settings');
    }
    else {
        // Delete a measurement
        if($_GET['settings'] == 'delete_measurement' && !empty($_GET['sensor']) && isset($_GET['id'])) {
            if(is_file('data/'.$_GET['sensor'].'.data')) {
                $measures = json_decode(gzinflate(file_get_contents('data/'.$_GET['sensor'].'.data')), true);
                if(array_key_exists($_GET['id'], $measures)) {
                    unset($measures[$_GET['id']]);
                    if(empty($measures)) {
                        unlink('data/'.$_GET['sensor'].'.data');
                    }
                    else {
                        file_put_contents('data/'.$_GET['sensor'].'.data', gzdeflate(json_encode($measures)));
                    }
                }
            }
            header('location: ?settings=');
            exit();
        }

        // Delete a sensor
        if($_GET['settings'] == 'delete_sensor' && !empty($_GET['key'])) {
            if(array_key_exists($_GET['key'], $api_keys)) {
                unset($api_keys[$_GET['key']]);
                file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));

                if(is_file('data/'.$_GET['key'].'.data')) {
                    unlink('data/'.$_GET['key'].'.data');
                }

                header('location: ?settings=');
                exit();
            }
        }

        // Delete a type
        if($_GET['settings'] == 'delete_type' && !empty($_GET['id'])) {
            if(array_key_exists($_GET['id'], $types)) {
                unset($types[$_GET['id']]);
                file_put_contents('data/types.data', gzdeflate(json_encode($types)));

                foreach($api_keys as $api_key=>$value) {
                    if(is_file('data/'.$api_key.'.data')) {
                        $measures = json_decode(gzinflate(file_get_contents('data/'.$api_key.'.data')), true);
                        foreach($measures as $key=>$measure) {
                            if($measure['type'] == $_GET['id']) {
                                unset($measures[$key]);
                            }
                        }
                        if(empty($measures)) {
                            unlink('data/'.$api_key.'.data');
                        }
                        else {
                            file_put_contents('data/'.$api_key.'.data', gzdeflate(json_encode($measures)));
                        }
                    }
                }

                header('location: ?settings=');
                exit();
            }
        }

        // Add a sensor
        if(!empty($_POST['sensor'])) {
            $new_key = get_key($api_keys, $_POST['sensor']);
            if(!empty($_POST['key'])) {
                rename('data/'.$_POST['key'].'.data', 'data/'.$new_key.'.data');
                unset($api_keys[$_POST['key']]);
            }
            $api_keys[$new_key] = $_POST['sensor'];
            file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));
            header('location: ?settings=');
            exit();
        }

        // Add a type
        if(!empty($_POST['name']) && !empty($_POST['unit']) && !empty($_POST['threshold_1']) && !empty($_POST['threshold_2']) && !empty($_POST['threshold_3']) && !empty($_POST['spatial_validity']) && !empty($_POST['start_decrease']) && !empty($_POST['fully_gone']) && !empty($_POST['description'])) {
            if(floatval($_POST['threshold_1']) > floatval($_POST['threshold_2'])) {
                exit('ERROR : Le seuil 1 doit être en-deça du second seuil.');
            }
            if(floatval($_POST['threshold_2']) > floatval($_POST['threshold_3'])) {
                exit('ERROR : Le seuil 2 doit être en-deça du troisième seuil.');
            }

            if(intval($_POST['start_decrease']) > intval($_POST['fully_gone'])) {
                exit('ERROR : La durée avant le début de la diminution de l\'opacité doit être en-deça de celle correspondant à l\'opacité minimale.');
            }

            if(!empty($_POST['old_id'])) {
                unset($types[$_POST['old_id']]);
                foreach($api_keys as $api_key=>$value) {
                    if(is_file('data/'.$api_key.'.data')) {
                        $measurements = json_decode(gzinflate(file_get_contents('data/'.$api_key.'.data')), true);
                        foreach($measurements as $key=>$measurement) {
                            if($measurement['type'] == $_POST['old_id']) {
                                $measurements[$key]['type'] = $_POST['id'];
                            }
                        }
                        if(empty($measurements)) {
                            unlink('data/'.$api_key.'.data');
                        }
                        else {
                            file_put_contents('data/'.$api_key.'.data', gzdeflate(json_encode($measures)));
                        }
                    }
                }
            }

            $types[$_POST['id']] = array('name' => $_POST['name'], 'unit' => $_POST['unit'], 'threshold_1' => floatval($_POST['threshold_1']), 'threshold_2' => floatval($_POST['threshold_2']), 'threshold_3' => floatval($_POST['threshold_3']), 'spatial_validity' => intval($_POST['spatial_validity']), 'start_decrease' => intval($_POST['start_decrease']), 'fully_gone' => intval($_POST['fully_gone']), 'description' => preg_replace("/(\r\n|\n|\r)/", "<br />", $_POST['description']));
            file_put_contents('data/types.data', gzdeflate(json_encode($types)));
            header('location: ?settings=');
            exit();
        }

        if(!empty($_GET['settings'])) {
            $tpl->assign('settings', $_GET['settings']);
        }
        else {
            $tpl->assign('settings', false);
        }

        // Edit a sensor
        if(!empty($_GET['key']) && array_key_exists($_GET['key'], $api_keys)) {
            $tpl->assign('sensor_key', $_GET['key']);
        }
        // Edit a type
        elseif(!empty($_GET['id']) && array_key_exists($_GET['id'], $types)) {
            $tpl->assign('type_id', $_GET['id']);
        }

        $tpl->assign('api_keys', $api_keys);
        $tpl->assign('types', $types);

        $datafiles = [];
        foreach($api_keys as $key=>$value) {
            if(is_file('data/'.$key.'.data')) {
                $datafiles[$key] = json_decode(gzinflate(file_get_contents('data/'.$key.'.data')), true);
            }
        }
        $tpl->assign('datafiles', $datafiles);

        $tpl->draw('settings');
    }
}
/* Live view */
elseif(isset($_GET['live'])) {
    unset($menu['live']);
    $menu = array('sensor' => '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Choix du capteur</a> | ') + $menu;
    $tpl->assign('menu', $menu);
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens&nbsp;Capteurs</a>');
    $tpl->assign('api_keys', $api_keys);

    if(in_array($_GET['live'], $api_keys)) {
        $live = htmlspecialchars($_GET['live']);
        $tpl->assign('head_title', ' - Suivi du capteur '.$live);
        $tpl->assign('page_title', ' - Suivi du capteur '.$live);
        $tpl->assign('live', $live);
    }
    else {
        $tpl->assign('head_title', 'CitizenAir - Capteur en live');
        $tpl->assign('page_title', ' - Capteur en live');
    }

    $tpl->draw('live');
}
/* Export view */
elseif(isset($_GET['export'])) {
    unset($menu['export']);
    $tpl->assign('head_title', ' - Export');
    $tpl->assign('page_title', ' - Export des données');
    $tpl->assign('menu', $menu);
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens&nbsp;Capteurs</a>');
    $tpl->assign('no_js', true);
    $tpl->assign('capteurs', $api_keys);
    $tpl->assign('types', $types);
    $tpl->draw('export');
}
/* About page */
elseif(isset($_GET['about'])) {
    unset($menu['about']);
    $tpl->assign('head_title', ' - À propos');
    $tpl->assign('page_title', ' - À propos');
    $tpl->assign('menu', $menu);
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens&nbsp;Capteurs</a>');
    $tpl->assign('no_js', true);

    if(!empty($_GET['about']) && array_key_exists($_GET['about'], $types)) {
        $tpl->assign('description', $types[$_GET['about']]['description']);
    }

    $tpl->draw('about');
}
/* Support page */
elseif(isset($_GET['participez'])) {
    unset($menu['participez']);
    $tpl->assign('head_title', 'CitizenAir - Participez&nbsp;!');
    $tpl->assign('page_title', ' - Participez&nbsp;!');
    $tpl->assign('menu', $menu);
    $tpl->assign('credits', '<a href="http://www.citoyenscapteurs.net/">Citoyens&nbsp;Capteurs</a>');
    $tpl->assign('no_js', true);

    $tpl->draw('participez');
}
/* Default map */
else {
    $menu['carte'] = '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Légende</a>';
    $tpl->assign('head_title', 'CitizenAir');
    $tpl->assign('page_title', '');
    $tpl->assign('menu', $menu);
    $tpl->assign('legend_items', $types);
    $tpl->assign('credits', '<a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC&nbsp;BY&nbsp;SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens&nbsp;Capteur</a>');
    $tpl->draw('index');
}
