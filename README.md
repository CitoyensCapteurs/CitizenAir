CitizenAir
=====

(An English version is available in the file `README.en.md`)

Visualisation web pour les données issues de http://jnum.parisdescartes.fr/atelier-mesure-de-la-pollution-association-citoyens-capteurs/.


## Installation

* Cloner le dépôt git où vous voulez.
* S'assurer que le script a les droits nécessaires pour créer les fichiers `api.keys` et `passwd` à la racine et pour écrire dans le répertoire `data/`.
* S'assurer que le script a les droits nécessaires pour écrire dans le répertoire `tmp/`.
* Accéder à la page `index.php?settings=` pour mettre en place un mot de passe pour protéger la configuration.


## Configuration

Toute la configuration se fait par la page `index.php?settings=`. Elle permet notamment :
* D'ajouter de nouveaux types de mesures, en spécifiant leurs paramètres finement.
* De gérer les capteurs autorisés, identifiés par une clé aléatoire unique de 5 caractères. Cette clé sert à envoyer les données à la visualisation.

Un mot de passe vous sera demandé pour accéder à la page de configuration. Si vous oubliez ce mot de passe, vous pouvez le réinitialiser simplement en supprimant le fichier `passwd` à la racine. et en accédant à la page `index.php?settings=` qui vous demandera alors de créer un nouveau mot de passe.


## Stockage des données

Les données sont stockées par capteur, dans un fichier `data/api_key.data`, au format JSON compressé en gzip. Les types de données disponibles sont stockés dans le fichier `data/types.data` (JSON compressé en gzip aussi).
Lors de la récupération des données, celles-ci sont retournées en JSON par la page `api.php`, en retournant les valeurs nécessaires uniquement. La façon la plus simple d'accéder aux données et d'en ajouter de nouvelles et d'utiliser l'API _via_ le fichier `api.php`


## Envoi des données avec l'API

Pour envoyer des données à l'API, il suffit de faire une requête _GET_ sur la page `api.php` en passant les paramètres suivants (tous sont obligatoires) :
* `do` = `add`, pour envoyer des données
* `type` = un identifiant de type disponible
* `value` = valeur de la mesure (_float_)
* `timestamp` = timestamp UNIX de la mesure
* `api_key` = clé secrète liée au capteur (voir configuration)
* `long` = longitude de la mesure (_float_)
* `lat` = latitude de la mesure (_float_)
* `force=1` pour passer outre la vérification de la position, par rapport à la dernière mesure (deux mesures d'un même capteur ne doivent pas être séparées de plus de 100km/h * temps entre les mesures, par défaut)

Par exemple :
```
/api.php?do=add&type=NO2&measure=200&timestamp=10000&api_key=API_KEY&long=48.84874&lat=2.34211
```

## Récupération des données avec l'API

La même page, `api.php`, permet également de récupérer les données stockées. Il faut alors l'appeler avec le paramètre `do=get` pour récupérer des données (par exemple : `/api.php?do=get`). On peut lui passer les paramètres optionnels suivants :
* `sensor` = une liste de noms de capteurs (de noms, et non de clés d'API), séparés par des virgules. Si pas de noms passés, il n'y a pas de filtrage sur le capteur.
* `type` = une liste de types de mesures, séparés par des virgules. Si pas de types passés, il n'y a aucun filtrage sur le type de mesure.
* `time_min` et `time_max` pour filtrer les mesures réalisées dans l'intervalle de temps spécifié (les deux paramètres ne sont pas obligatoires).
* `lat_min` et `lat_max` pour filtrer les mesures entre les latitudes spécifiées (les deux paramètres ne sont pas obligatoires).
* `long_min` et `long_max` pour filtrer les mesures entre les longitudes spécifiées (les deux paramètres ne sont pas obligatoires).
* `format` qui peut valoir `json` ou `csv` pour choisir le format d'export. Par défaut, le format `json` est utilisé si le paramètre n'est pas spécifié.
* `visu=1` qui, si spécifié, retourne en plus les données de visualisation liée à chaque mesure (niveau, type de mesures, …)
* `sort=sensor|latitude|longitude|timestamp|type|value|unit` pour trier les valeurs (optionnel, tri par timestamp par défaut)


## Visualisation web

La visualisation web disponible (page `index.php`) n'est qu'une interface pour représenter les données stockées. Elle utilise l'API décrite précédemment pour récupérer les mesures. Elle permet de visualiser directement sur une carte OpenStreeMaps les mesures effectuées, avec une légende et toutes les informations utiles.

## Licences

Ce projet est un _fully open source project_ :
* Les sources de l'application se trouvent sur [Github](https://github.com/CitoyensCapteurs/CitizenAir) sous licence [GNU GPL v3](https://www.gnu.org/copyleft/gpl.html)
* Les sources et tutoriaux de montage des capteurs disponibles sur [le wiki de l'association](http://wiki.citoyenscapteurs.net/) sont placés sous licence [Open Hardware License du CERN](http://www.ohwr.org/projects/cernohl/wiki)
* Les données des capteurs téléchargeables au format [JSON](https://fr.wikipedia.org/wiki/JSON) et [CSV](https://fr.wikipedia.org/wiki/Comma-separated_values) sont sous licence [Open Data Commons By](http://opendatacommons.org/licenses/by/)


![OpenData](http://assets.okfn.org/images/ok_buttons/od_80x23_orange_grey.png)

![OpenHardware](https://raw.githubusercontent.com/CitoyensCapteurs/CitizenAir/master/tpl/img/ohr.png)

![GPLv3](https://raw.githubusercontent.com/CitoyensCapteurs/CitizenAir/master/tpl/img/gpl.png)

### Licence du code

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
Tous les scripts devraient contenir cette note de licence. Si ce n'est pas le cas, n'hésitez pas à nous demander. Veuillez noter que les fichiers pour lesquels il est difficile de déterminer les informations sur la licence (comme les images) sont aussi distribués sous ces termes, à l'exception des images _tpl/img/ohr.png_, _tpl/img/gpl.png_, _tpl/img/od.png_, _tpl/img/flattr.png_ et _tpl/img/paypal.gif_.

### Licence de Leaflet (fichiers `leaflet.css`, `leaflet.js`)
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

### Licence de RainTPL (fichier `rain.tpl.class.php`)
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

### Licence de timeline.js (fichier `tpl/js/timeline.js`)

````
 * --------------------------------------------------------------------------------
 * "THE NO-ALCOHOL BEER-WARE LICENSE" (Revision 42):
 * Phyks (webmaster@phyks.me) wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff (and you can also do whatever you want
 * with this stuff without retaining it, but that's not cool...). If we meet some
 * day, and you think this stuff is worth it, you can buy me a <del>beer</del> soda
 * in return.
 * Phyks
 * ---------------------------------------------------------------------------------
````


### Pour plus d'infos sur le dev, voir le fichier `humans.txt`

## TODO v2

* timeline : 1 jour / 1 semaine / 1 mois + choix du capteur
* Couleur du capteur dans la timeline
* capteur fixe et superposition des marqueurs ?
* Minifier
* Permalink
* Date absolue quelque part
* Nominatim reverse geolocation pour le choix de l'export
* Dropper les timestamps sur l'UI, améliorer l'export
* Plusieurs fichiers par capteur
* dev.citizenair.io
