<?php
if(!is_file('api.keys')) {
    header('location: settings.php');
    exit();
}
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Citoyens Capteurs - Pollution</title>
        <link rel="stylesheet" href="leaflet.css">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <header id="title">
            <h1><a href="">Citoyens Capteurs - Mesure de pollution</a></h1>
        </header>
        <div id="legend" class="left">
            <h2>NO<sub>2</sub> :</h2>
            <table>
                <tr>
                    <td>Vert</td>
                    <td>&lt; 175 µg/m<sub>3</sub></td>
                </tr>
                <tr>
                    <td>Orange</td>
                    <td>entre 175 et 200 µg/m<sub>3</sub></td>
                </tr>
                <tr>
                    <td>Rouge</td>
                    <td>&gt; 200 µg/m<sub>3</sub></td>
                </tr>
            </table>
        </div>
        <div id="map"></div>
        <div id="attribution">
            <p class='left'><a href="#legend" onclick="toggle('legend');">Légende</a> | <a href="">À propos</a></p>
            <p><a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a></p>
        </div>
        <script type="text/javascript" src="leaflet.js"></script>
        <script type="text/javascript" src="js.js"></script>
    </body>
</html>
