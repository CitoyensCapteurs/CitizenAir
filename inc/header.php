<?php
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
                        <title>CitizenAir".(!empty($live) ? ' - Live du capteur '.$live : '')."</title>
                        <link rel=\"stylesheet\" href=\"leaflet.css\">
                        <link rel=\"stylesheet\" href=\"style.css\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <header id=\"title\">
                            <p class='left'>".((!isset($_GET['live'])) ? '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Légende</a> | <a href="?live=">Capteur en live</a>' : '<a href="#legend" onclick="event.preventDefault(); toggleLegend(false);">Choix du capteur</a> | <a href="index.php">Retour à la carte</a>')." | <a href=\"\">Export</a> | <a href=\"about.php\">À propos</a></p>
                            <h1 class=\"white-links\"><a href=\"\">CitizenAir</a>".((!empty($live)) ? ' - Live du capteur '.$live : '')."</h1>
                        </header>
                        <div id=\"main\">";
