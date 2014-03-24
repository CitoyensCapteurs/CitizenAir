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

require_once('inc/header.php');

if(isset($_GET['live']) && in_array($_GET['live'], $api_keys)) {
    $live = htmlspecialchars($_GET['live']);
}

if(!isset($_GET['export'])) {
    $tpl .= "<div id=\"legend\" class=\"left\">
                    <h2>".((empty($live)) ? 'Légende :' : 'Capteur à afficher :')."</h2>";

    if(!isset($_GET['live'])) {
        foreach($types as $type) {
            $tpl .= '<div class="legend-item"><h2>'.$type['name'].'</h2><table><tr><td>Vert</td><td>&lt;'.$type['seuil_1'].' '.$type['unit'].'</td></tr><tr><td>Orange</td><td>Entre '.$type['seuil_1'].' et '.$type['seuil_2'].' '.$type['unit'].'</td></tr><tr><td>Rouge</td><td>Entre '.$type['seuil_2'].' et '.$type['seuil_3'].' '.$type['unit'].'</td></tr><tr><td>Noir</td><td>&gt;'.$type['seuil_3'].' '.$type['unit'].'</td></tr></table></div>';
        }
    }
    else {
        $tpl .= '<ul>';
        foreach($api_keys as $capteur) {
            $tpl .= '<li><a href="?live='.htmlspecialchars($capteur).'">'.htmlspecialchars($capteur).'</a></li>';
        }
        $tpl .= '</ul>';
    }
    $tpl .= "</div>";
}

$tpl .= "<div id=\"map\" ".((isset($_GET['live'])) ? 'class="live"' : '')." ".(isset($_GET['export']) ? 'class="export"' : '').">
            <div id=\"need-js\"><p>Vous devez activer JavaScript pour utiliser ce site web.</p></div>";

if(isset($_GET['live']) && empty($live)) {
    $tpl .= "<div class=\"table\"><p>Choisissez le capteur à suivre :</p>
                <ul>";
    foreach($api_keys as $capteur) {
        $tpl .= '<li><a href="?live='.htmlspecialchars($capteur).'">'.htmlspecialchars($capteur).'</a></li>';
    }
    $tpl .= "</ul>
            </div>";
}

if(isset($_GET['export'])) {
    $tpl .= "<form method=\"get\" action=\"api.php\" id=\"export_form\">
        <fieldset>
        <p><label for=\"capteur\">Capteur : </label><select name=\"capteur\" id=\"capteur\"><option value=\"\" selected>Tous</option>";
    foreach($api_keys as $capteur) {
        $tpl .= '<option value="'.$capteur.'">'.$capteur.'</option>';
    }
    $tpl .= "</select></p>
        <p><label for=\"type\">Type : </label><select name=\"type\" id=\"type\"><option value=\"\" selected>Tous</option>";
    foreach($types as $id=>$type) {
        $tpl .= '<option value="'.$id.'">'.$type['name'].'</option>';
    }
    $tpl .= "</select></p>
        <p><label for=\"time_min\">Daté entre le </label> <input type=\"text\" name=\"lat_min\" id=\"lat_min\"/><label for=\"lat_max\"> et le </label><input type=\"text\" name=\"lat_max\" id=\"lat_max\"/> (format <a href=\"https://fr.wikipedia.org/wiki/Heure_Unix\">timestamp Unix</a>)</p>
        <p><label for=\"lat_min\">Latitude entre </label> <input type=\"text\" name=\"lat_min\" id=\"lat_min\"/><label for=\"lat_max\"> et </label><input type=\"text\" name=\"lat_max\" id=\"lat_max\"/></p>
        <p><label for=\"long_min\">Longitude entre </label> <input type=\"text\" name=\"lat_min\" id=\"lat_min\"/><label for=\"lat_max\"> et </label><input type=\"text\" name=\"lat_max\" id=\"lat_max\"/></p>
        <p><label for=\"format\">Format : </label><select name=\"format\" id=\"format\"><option value=\"csv\">CSV</option><option value=\"json\" selected>JSON</option></select></p>
        <p><label for=\"visu\">Inclure les données pour la visualisation ? </label><select name=\"visu\" id=\"visu\"><option value=\"0\">Non</option><option value=\"1\" selected>Oui</option></select></p>
        </fieldset>
        <p class=\"center\"><input type=\"submit\" value=\"Exporter\"/><input type=\"hidden\" name=\"do\" value=\"get\"/></p>
        </form>
        <p><em>Note :</em> Tous les champs sont optionnels et ne servent qu'à restreindre les résultats.</p>";
}

$tpl .= "</div>";

require_once('inc/footer.php');

echo $tpl;
?>
