CitoyensCapteursNO2
=====
Visu pour http://jnum.parisdescartes.fr/atelier-mesure-de-la-pollution-association-citoyens-capteurs/.

README plus détaillé à venir.

## Notes

* Ne pas oublier de modifier le .htaccess pour empêcher les accès non autorisés à la page _settings.php_.

## Envoi des données
GET avec les paramètres, API_KEY pour empêcher des mesures bidons.
API_KEY dans le fichier api.keys.

## Stockage des données
https://gist.github.com/Phyks/9549370 => Légèrement modifié.

## API
Paramètres :
* capteur : un, plusieurs ou tous
* type de mesure : un, plusiuers ou tous
* timestamp : tous ou entre dates
* longitude : tous ou entre longitudes
* latitude : tous ou entre latitudes
* visu : retourner ou non les paramètres de visu

## TODO
http://maps.stamen.com/#terrain/12/37.7706/-122.3782
