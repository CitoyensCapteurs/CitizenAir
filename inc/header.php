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

    if(!is_file('api.keys') || !is_file('data/types.data')) {
        header('location: settings.php');
        exit();
    }
    $types = json_decode(gzinflate(file_get_contents('data/types.data')), true);
    $api_keys = json_decode(gzinflate(file_get_contents('api.keys')), true);

    $tpl = "<!doctype html>
                <html lang=\"fr\">
                    <head>
                        <meta charset=\"utf-8\">
                        <title>CitizenAir".(!empty($live) ? ' - Live du capteur '.$live : '')." ".(isset($_GET['export']) ? ' - Export des données' : '')."</title>
                        <link rel=\"stylesheet\" href=\"leaflet.css\">
                        <link rel=\"stylesheet\" href=\"style.css\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <header id=\"title\">
                            <p class='left'>".(isset($_GET['export']) ? '<a href="index.php">Retour</a>' : ((!isset($_GET['live'])) ? '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Légende</a> | <a href="?live=">Capteur en live</a> | <a href="?export=">Export</a>' : '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Choix du capteur</a> | <a href="index.php">Retour à la carte</a> | <a href="?export=">Export</a>'))." | <a href=\"about.php\">À propos</a></p>
                            <h1 class=\"white-links\"><a href=\"\">CitizenAir</a>".((!empty($live)) ? ' - Live du capteur '.$live : '')." ".(isset($_GET['export']) ? ' - Export des données' : '')."</h1>
                        </header>
                        <div id=\"main\">";
