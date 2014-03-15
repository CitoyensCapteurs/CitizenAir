<?php
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

if(!empty($_GET['do']) && $_GET['do'] == 'delete_device' && !empty($_GET['key'])) {
    if(array_key_exists($_GET['key'], $api_keys)) {
        unset($api_keys[$_GET['key']]);
        file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));
        header('location: settings.php');
        exit();
    }
}

if(!empty($_GET['do']) && $_GET['do'] == 'delete_type' && !empty($_GET['id'])) {
    if(array_key_exists($_GET['id'], $types)) {
        unset($types[$_GET['id']]);
        file_put_contents('data/types.data', gzdeflate(json_encode($types)));
        header('location: settings.php');
        exit();
    }
}

if(!empty($_POST['device'])) {
    if(!empty($_POST['key'])) {
        unset($api_keys[$_POST['key']]);
    }
    $api_keys[substr(md5($_POST['device']), 0, 5)] = $_POST['device'];
    file_put_contents('api.keys', gzdeflate(json_encode($api_keys)));
    header('location: settings.php');
    exit();
}

if(!empty($_POST['name']) && !empty($_POST['unit']) && !empty($_POST['seuil_1']) && !empty($_POST['seuil_2']) && !empty($_POST['spatial_validity']) && !empty($_POST['start_decrease']) && !empty($_POST['fully_gone'])) {
    if(intval($_POST['seuil_1']) > intval($_POST['seuil_2'])) {
        exit('Le seuil 1 doit être en-deça du second seuil.');
    }

    if(intval($_POST['start_decrease']) > intval($_POST['fully_gone'])) {
        exit('La durée avant le début de la diminution de l\'opacité doit être en-deça de celle correspondant à l\'opacité minimale.');
    }

    $types[$_POST['id']] = array('name' => $_POST['name'], 'unit' => $_POST['unit'], 'seuil_1' => intval($_POST['seuil_1']), 'seuil_2' => intval($_POST['seuil_2']), 'spatial_validity' => intval($_POST['spatial_validity']), 'start_decrease' => intval($_POST['start_decrease']), 'fully_gone' => intval($_POST['fully_gone']));
    file_put_contents('data/types.data', gzdeflate(json_encode($types)));
    header('location: settings.php');
    exit();
}
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Citoyens Capteurs - Pollution [Configuration]</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="settings">
        <header id="title"><a href="index.php"><h1>Citoyens Capteurs - Mesure de pollution</a> - Configuration</h1></header>
        <?php
            if(!empty($_GET['do'])) {
                if($_GET['do'] == 'add_device' || ($_GET['do'] == 'edit_device' && !empty($api_keys) && !empty($_GET['key']) && array_key_exists($_GET['key'], $api_keys))) {
        ?>
                    <form method="post" action="settings.php">
                        <p><label for="device">Nom : </label><input type="text" name="device" id="device" <?php if(!empty($_GET['key'])) { echo 'value="'.htmlspecialchars($api_keys[$_GET['key']]).'"'; }?>/></p>
                        <p>
                            <input type="submit" value="Sauver"/>
                            <?php
                                if(!empty($_GET['key'])) {
                                    echo '<input type="hidden" name="key" value="'.htmlspecialchars($_GET['key']).'"/>';
                                }
                            ?>
                        </p>
                    </form>
        <?php
                }
                elseif($_GET['do'] == 'add_type' || ($_GET['do'] == 'edit_type' && !empty($types) && !empty($_GET['id']) && array_key_exists($_GET['id'], $types))) {
        ?>
                    <form method="post" action="settings.php">
                        <p><label for="id">ID : </label><input types="text" name="id" id="id" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($_GET['id']).'"'; }?>/></p>
                        <p><label for="name">Nom (HTML possible) : </label><input types="text" name="name" id="name" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['name']).'"'; }?>/></p>
                        <p><label for="unit">Unité (HTML possible) : </label><input types="text" name="unit" id="unit" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['unit']).'"'; }?>/></p>
                        <p><label for="seuil_1">Seuil 1 : </label><input types="number" name="seuil_1" id="seuil_1" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['seuil_1']).'"'; }?>/></p>
                        <p><label for="seuil_2">Seuil 2 : </label><input types="number" name="seuil_2" id="seuil_2" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['seuil_2']).'"'; }?>/></p>
                        <p><label for="spatial_validity">Validité spatiale (diamètre, en mètres) : </label><input types="number" name="spatial_validity" id="spatial_validity" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['spatial_validity']).'"'; }?>/></p>
                        <p><label for="start_decrease">Durée avant baisse de l'opacité (en secondes) : </label><input types="number" name="start_decrease" id="start_decrease" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['start_decrease']).'"'; }?>/></p>
                        <p><label for="fully_gone">Durée avant opacité min. (en secondes) : </label><input types="number" name="fully_gone" id="fully_gone" <?php if(!empty($_GET['id'])) { echo 'value="'.htmlspecialchars($types[$_GET['id']]['fully_gone']).'"'; }?>/></p>
                        <p>
                            <input type="submit" value="Sauver"/>
                        </p>
                    </form>
        <?php
                }
            }
            else {
        ?>
                <h2>Liste des capteurs actuellement autorisés</h2>
                <p><a href="?do=add_device">Ajouter un capteur</a></p>
                <?php
                    if(!empty($api_keys)) {
                ?>
                        <table>
                            <tr>
                                <th>Nom</th>
                                <th>Clé</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                            <?php
                                foreach($api_keys as $key=>$name) {
                                    $key = htmlspecialchars($key);
                                    echo '<tr><td>'.htmlspecialchars($name).'</td><td>'.$key.'</td><td><a href="?do=edit_device&amp;key='.$key.'">Modifier</a></td><td><a href="?do=delete_device&amp;key='.$key.'">Supprimer</a></td></tr>';
                                }
                            ?>
                        </table>
                <?php
                    } else {
                        echo "<p>Aucun capteur pour le moment.</p>";
                    }
                ?>

                <h2>Liste des types de mesure disponibles</h2>
                <p><a href="?do=add_type">Ajouter un type</a></p>
                <?php
                    if(!empty($types)) {
                ?>
                        <table>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Unité</th>
                                <th>Seuil 1</th>
                                <th>Seuil 2</th>
                                <th>Validité spatiale</th>
                                <th>Durée avant baisse de l'opacité</th>
                                <th>Durée avant opacité min.</th>
                                <th>Modifier</th>
                                <th>Supprimer</th>
                            </tr>
                            <?php
                                foreach($types as $id=>$type) {
                                    $id = htmlspecialchars($id);
                                    echo '<tr><td>'.$id.'</td><td>'.$type['name'].'</td><td>'.$type['unit'].'</td><td>'.$type['seuil_1'].'</td><td>'.$type['seuil_2'].'</td><td>'.$type['spatial_validity'].'</td><td>'.$type['start_decrease'].'</td><td>'.$type['fully_gone'].'</td><td><a href="?do=edit_type&amp;id='.$id.'">Modifier</a></td><td><a href="?do=delete_type&amp;id='.$id.'">Supprimer</a></td></tr>';
                                }
                            ?>
                        </table>
                <?php
                    } else {
                        echo "<p>Aucun capteur pour le moment.</p>";
                    }
		    }
        ?>
    </body>
</html>

