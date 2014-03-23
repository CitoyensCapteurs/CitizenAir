<?php
require_once('inc/header.php');

if(isset($_GET['live']) && in_array($_GET['live'], $api_keys)) {
    $live = htmlspecialchars($_GET['live']);
}

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
$tpl .= "</div>
            <div id=\"map\" ".((isset($_GET['live'])) ? 'class="live"' : '')."><div id=\"need-js\"><p>Vous devez activer JavaScript pour utiliser ce site web.</p></div>";

if(isset($_GET['live']) && empty($live)) {
    $tpl .= "<p>Choisissez le capteur à suivre :</p>
            <ul>";
    foreach($api_keys as $capteur) {
        $tpl .= '<li><a href="?live='.htmlspecialchars($capteur).'">'.htmlspecialchars($capteur).'</a></li>';
    }
    $tpl .= "</ul>";
}
$tpl .= "</div>
    </div>";

require_once('inc/footer.php');

echo $tpl;
?>
