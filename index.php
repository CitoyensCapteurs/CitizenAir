<?php
if(!is_file('api.keys') || !is_file('data/types.data')) {
    header('location: settings.php');
    exit();
}
$types = json_decode(gzinflate(file_get_contents('data/types.data')), true);
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
            <h1 class="white-links"><a href="">Citoyens Capteurs - Mesure de pollution</a></h1>
        </header>
        <div id="legend" class="left">
            <?php
                foreach($types as $type) {
                    echo '<h2>'.$type['name'].'</h2><table><td>Vert</td><td>&lt;'.$type['seuil_1'].' '.$type['unit'].'</td></tr><tr><td>Orange</td><td>Entre '.$type['seuil_1'].' et '.$type['seuil_2'].' '.$type['unit'].'</td></tr><tr><td>Rouge</td><td>&gt;'.$type['seuil_2'].' '.$type['unit'].'</td></tr></table>';
                }
            ?>
        </div>
        <div id="map"></div>
        <footer id="footer" class="white-links">
            <p class='left'><a href="#legend" onclick="event.preventDefault(); toggle('legend', false);">Légende</a> | <a href="">À propos</a></p>
            <p id="attribution"><a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a></p>
        </footer>
        <script type="text/javascript" src="leaflet.js"></script>
        <script type="text/javascript" src="js.js"></script>
    </body>
</html>
