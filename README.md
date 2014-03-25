CitizenAir
=====
Visualisation web pour les données issues de http://jnum.parisdescartes.fr/atelier-mesure-de-la-pollution-association-citoyens-capteurs/.


## Installation

* Cloner le dépôt git où vous voulez.
* S'assurer que le script a les droits nécessaires pour créer les fichiers _api.keys_ et _passwd_ à la racine et pour écrire dans le répertoire _data/_.
* S'assurer que le script a les droits nécessaires pour écrire dans le répertoire _tmp/_.
* Accéder à la page `index.php?settings=` pour mettre en place un mot de passe pour protéger la configuration.


## Configuration

Toute la configuration se fait par la page _settings.php_.Elle permet notamment :
* D'ajouter de nouveaux types de mesures, en spécifiant leurs paramètres finement.
* De gérer les capteurs autorisés, identifiés par une clé unique de 5 caractères. Cette clé sert à envoyer les données à la visualisation.


## Stockage des données

Les données sont stockées par capteur, dans un fichier _data/api_key.data_, au format JSON compressé en gzip. Les types de données disponibles sont stockés dans le fichier _data/types.data_.
Lors de la récupération des données, celles-ci sont retournées en JSON par la page _api.php_, en retournant les valeurs nécessaires uniquement.


## Envoi des données avec l'API

Pour envoyer des données à l'API, il suffit de faire une requête GET sur la page api.php en passant les paramètres suivants :
* `do` = `add`, pour envoyer des données
* `type` = un identifiant de type disponible
* `measure` = valeur de la mesure
* `timestamp` = timestamp UNIX de la mesure
* `api_key` = clé secrète liée au capteur
* `long` = longitude de la mesure
* `lat` = latitude de la mesure

Par exemple :
```
/api.php?do=add&type=NO2&measure=200&timestamp=10000&api_key=API_KEY&long=48.84874&lat=2.34211
```

## Récupération des données avec l'API

La même page, _api.php_ permet également de récupérer les données stockées. Il faut alors l'appeler avec le paramètre `do=get` pour récupérer des données (par exemple : `/api.php?do=get`). On peut lui passer les paramètres optionnels suivants :
* `capteur` = une liste de noms de capteurs (de noms, et non de clés d'API), séparés par des virgules. Si pas de noms passés, il n'y a pas de filtrage sur le capteur.
* `type` = une liste de types de mesures, séparés par des virgules. Si pas de types passés, il n'y a aucun filtrage sur le type de mesure.
* `time_min` et `time_max` pour filtrer les mesures réalisées dans l'intervalle de temps spécifié (les deux paramètres ne sont pas obligatoires).
* `lat_min` et `lat_max` pour filtrer les mesures entre les latitudes spécifiées (les deux paramètres ne sont pas obligatoires).
* `long_min` et `long_max` pour filtrer les mesures entre les longitudes spécifiées (les deux paramètres ne sont pas obligatoires).
* `format` qui peut valoir `json` ou `csv` pour choisir le format d'export. Par défaut, le format `json` est utilisé si le paramètre n'est pas spécifié.


## Visualisation web

La visualisation web disponible (page _index.php_) n'est qu'une interface pour représenter les données stockées. Elle utilise l'API décrite précédemment. Elle permet de visualiser directement sur une carte OpenStreeMaps les mesures effectuées, avec une légende et toutes les informations utiles.

## Licence

```
CitizenAir is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

CitizenAir is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with CitizenAir.  If not, see <http://www.gnu.org/licenses/>.
```
Tous les scripts devraient contenir cette note de licence. Si ce n'est pas le cas, n'hésitez pas à nous deamnder. Veuillez noter que les fichiers pour lesquels il est difficile de déterminer les informations sur la licence (comme les images) sont aussi distribués sous ces termes.

## Licence de Leaflet (fichiers `leaflet.css`, `leaflet.js`)
```
Copyright (c) 2010-2013, Vladimir Agafonkin
Copyright (c) 2010-2011, CloudMade
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```

## Licence de RainTPL (fichier `rain.tpl.class.php`)
```
/**
 *  RainTPL
 *  -------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under GNU/LGPL 3 License
 *
 *  @version 2.7.2
 */
```

## TODO

* timeline : 1 jour / 1 semaine / 1 mois
* Logos
* Tests et recherches de bugs graphiques
