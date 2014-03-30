CitizenAir
=====
Web visualization for data coming from http://jnum.parisdescartes.fr/atelier-mesure-de-la-pollution-association-citoyens-capteurs/ (webpage in French).


## Installation

* Clone git repo where you want.
* Check that the script has enough rights to create files `api.keys` and `passwd` at the root and to write in the `data/` directory.
* Check that the script has enough rights to write in `tmp/` dir.
* Go to `index.php?settings=` to set a password to protect configuration options.


## Configuration

The configuration page is `index.php?settings=`. You can:
* Add new measurement types, specifying very specific per type parameters.
* Manage authorized sensors, identified by a unique random key. This key is necessary to send data to the visualization.

You will need a password to access this page. If you forget it, you can reinitialize it by simply deleting the `passwd` file at the root and by going to `index.php?settings=`. You will be prompted for a new password, that will be stored.


## Data storage

Data are stored in a per sensor file, `data/api_key.data`, in gzipped JSON. Data types are stored in `data/types.data` (gzipped JSON also).

When you get the data back, they are returned as JSON string by the `api.php` script. The easiest way to access data and to add new data is to use the API, through the `api.php` script.


## Sending data through the API

To send data to the visualization, you should send a _GET_ request to the `api.php` page, passing the following parameters (all mandatory):
* `do` = `add`, to send data
* `type` = a type id
* `measure` = measurement value (_float_)
* `timestamp` = UNIX timestamp of the measurement
* `api_key` = secret api key linked to the sensor (see configuration)
* `long` = measurement longitude (_float_)
* `lat` = measurement latitude (_float_)

For example:
```
/api.php?do=add&type=NO2&measure=200&timestamp=10000&api_key=API_KEY&long=48.84874&lat=2.34211
```

## Getting back data with the API

The same script `api.php` allows you to get back stored data. You should then call it with the `do=get` parameter to get back data (for example: `/api.php?do=get`). You can also pass optional parameters to filter the results:
* `capteur` = a comma-separated list of sensor names. If no names are given, there won't be any filtering on sensor name.
* `type` = a comma-separated list of type names. If no names are given, there won't be any filtering on type name.
* `time_min` and `time_max` to filter measurements done between `time_min` and `time_max` (you can provide only one if you want)
* `lat_min` and `lat_max` to filter measurements done between `lat_min` and `lat_max` (you can provide only one if you want)
* `long_min` and `long_max` to filter measurements done between `long_min` and `long_max` (you can provide only one if you want)
* `format` which can be `csv` or `json` to choose export format. By default, `json` is used if this parameter is not specified.
* `visu=1` which, if specified, will return visualization data such as levels, type name associated to each measurement, …


## Web visualization

The provided web visualization (file `index.php`) is nothing more than a web interface to display stored data. It uses the previously described API to get the measurements. You can see the meaurements directly on an OpenStreetMaps, with legend and useful information.

## License

This project is a fully open source project:
* Application sources are on [Github](https://github.com/CitoyensCapteurs/CitizenAir) under [GNU GPL v3](https://www.gnu.org/copyleft/gpl.html) license 
* The how-tos to mount the sensors are available on [the association wiki](http://wiki.citoyenscapteurs.net/) and placed under [Open Hardware License by CERN](http://www.ohwr.org/projects/cernohl/wiki)
* Data from the sensors are downloadable in [JSON](https://fr.wikipedia.org/wiki/JSON) and [CSV](https://fr.wikipedia.org/wiki/Comma-separated_values) and are provided under [Open Data Commons By](http://opendatacommons.org/licenses/by/) license


![OpenData](http://assets.okfn.org/images/ok_buttons/od_80x23_orange_grey.png)

![OpenHardware](https://raw.githubusercontent.com/CitoyensCapteurs/CitizenAir/master/tpl/img/ohr.png)

![GPLv3](https://raw.githubusercontent.com/CitoyensCapteurs/CitizenAir/master/tpl/img/gpl.png)

### Code license

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

### Leaflet license (files `leaflet.css`, `leaflet.js`)
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

### RainTPL license (file `rain.tpl.class.php`)
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

### For more info on the dev, please see `humans.txt` file
