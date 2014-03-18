<?php
if(!is_file('api.keys') || !is_file('data/types.data')) {
    header('location: settings.php');
    exit();
}
$types = json_decode(gzinflate(file_get_contents('data/types.data')), true);
$api_keys = json_decode(gzinflate(file_get_contents('api.keys')), true);

if(isset($_GET['live']) && in_array($_GET['live'], $api_keys)) {
        $live = htmlspecialchars($_GET['live']);
}
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>CitizenAir<?php if(!empty($live)) { echo ' - Live du capteur '.$live; }?></title>
        <link rel="stylesheet" href="leaflet.css">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <header id="title">
            <h1 class="white-links"><a href="">CitizenAir</a><?php if(!empty($live)) { echo ' - Live du capteur '.$live; }?></h1>
        </header>
        <div id="main">
            <div id="legend" class="left">
                <h2><?php if(empty($live)) { echo 'Légende :'; } else { echo 'Capteur à afficher :'; }?></h2>
                <?php
                    if(!isset($live)) {
                        foreach($types as $type) {
                            echo '<div class="legend-item"><h2>'.$type['name'].'</h2><table><td>Vert</td><td>&lt;'.$type['seuil_1'].' '.$type['unit'].'</td></tr><tr><td>Orange</td><td>Entre '.$type['seuil_1'].' et '.$type['seuil_2'].' '.$type['unit'].'</td></tr><tr><td>Rouge</td><td>Entre '.$type['seuil_2'].' et '.$type['seuil_3'].' '.$type['unit'].'</td></tr><tr><td>Noir</td><td>&gt;'.$type['seuil_3'].' '.$type['unit'].'</td></tr></table></div>';
                        }
                    }
                    else {
                        echo '<ul>';
                        foreach($api_keys as $capteur) {
                            echo '<li><a href="?live='.htmlspecialchars($capteur).'">'.htmlspecialchars($capteur).'</a></li>';
                        }
                        echo '</ul>';
                    }
                ?>
            </div>
            <div id="map" <?php if(isset($_GET['live'])) { echo 'class="live"'; }?>>
                <div id="live">
                    <?php
                        if(isset($_GET['live']) && empty($live)) {
                    ?>
                            <p>Choisissez le capteur à suivre :</p>
                            <ul>
                                <?php
                                    foreach($api_keys as $capteur) {
                                        echo '<li><a href="?live='.htmlspecialchars($capteur).'">'.htmlspecialchars($capteur).'</a></li>';
                                    }
                                ?>
                            </ul>
                    <?php
                        }
                    ?>
                </div>
                <p style="display: table-row;"><em>Note :</em> La page se mettra à jour toute seule.</p>
            </div>
        </div>
        <footer id="footer" class="white-links">
            <p class='left'><?php if(!isset($live)) { echo '<a href="#legend" onclick="event.preventDefault(); toggle(\'legend\', false);">Légende</a> | <a href="?live=">Capteur en live</a>'; } else { echo '<a href="#legend" onclick="event.preventDefault(); toggle(\'legend\', false);">Choix du capteur</a> | <a href="index.php">Retour à la carte</a>'; }?> | <a href="">À propos</a></p>
            <p id="attribution"><?php if(!isset($live)) { ?><a title="A JS library for interactive maps" href="http://leafletjs.com">Leaflet</a> | Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <?php } ?><a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a></p>
        </footer>
        <script type="text/javascript" src="leaflet.js"></script>
        <script type="text/javascript" src="js.js"></script>
    </body>
</html>
