CitizenAir
=====
Visualisation web pour les données issues de http://jnum.parisdescartes.fr/atelier-mesure-de-la-pollution-association-citoyens-capteurs/.


## Installation

* S'assurer que le script a les droits nécessaires pour créer un fichier _api.keys_ à la racine et pour écrire dans le répertoire _data/_.
* Décommenter la partie nécessaire dans le .htaccess pour empêcher l'accès libre à la page de configuration _settings.php_ et mettre un fichier _.htpasswd_ correspondant à côté.


## Configuration

Toute la configuration se fait par la page _settings.php_.Elle permet notamment :
* D'ajouter de nouveaux types de mesures, en spécifiant leurs paramètres finement.
* De gérer les capteurs autorisés, identifiés par une clé unique de 5 caractères. Cette clé sert à envoyer les données à la visualisation.


## Stockage des données

Les données sont stockées par capteur, dans un fichier _data/api_key.data_, au format JSON compressé en gzip. Les types de données disponibles sont stockés dans le fichier _data/types.data_.
Lors de la récupération des données, celles-ci sont retournées en JSON par la page _api.php_, en retournant les valeurs nécessaires uniquement.


## Envoi des données avec l'API

Pour envoyer des données à l'API, il suffit de faire une requête GET sur la page api.php en passant les paramètres suivants :
* do = add, pour envoyer des données
* type = un identifiant de type disponible
* measure = valeur de la mesure
* timestamp = timestamp UNIX de la mesure
* api_key = clé secrète liée au capteur
* long = longitude de la mesure
* lat = latitude de la mesure

Par exemple :
```
/api.php?do=add&type=NO2&measure=200&timestamp=10000&api_key=API_KEY&long=48.84874&lat=2.34211
```

## Récupération des données avec l'API

La même page, _api.php_ permet également de récupérer les données stockées. Il faut alors l'appeler avec le paramètre `do=get` pour récupérer des données (par exemple : `/api.php?do=get`). On peut lui passer les paramètres optionnels suivants :
* capteur = une liste de noms de capteurs (de noms, et non de clés d'API), séparés par des virgules. Si pas de noms passés, il n'y a pas de filtrage sur le capteur.
* type = une liste de types de mesures, séparés par des virgules. Si pas de types passés, il n'y a aucun filtrage sur le type de mesure.
* time\_min et time\_max pour filtrer les mesures réalisées dans l'intervalle de temps spécifié (les deux paramètres ne sont pas obligatoires).
* lat\_min et lat\_max pour filtrer les mesures entre les latitudes spécifiées (les deux paramètres ne sont pas obligatoires).
* long\_min et long\_max pour filtrer les mesures entre les longitudes spécifiées (les deux paramètres ne sont pas obligatoires).


## Visualisation web

La visualisation web disponible (page _index.php_) n'est qu'une interface pour représenter les données stockées. Elle utilise l'API décrite précédemment. Elle permet de visualiser directement sur une carte OpenStreeMaps les mesures effectuées, avec une légende et toutes les informations utiles.

## TODO

[ ] http://maps.stamen.com/#terrain/12/37.7706/-122.3782
[ ] timeline
[ ] onglets (cf IRC)
[ ] update régulier
[ ] protection pour ne pas avoir deux fois le même api_key
