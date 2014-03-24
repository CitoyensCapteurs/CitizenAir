/* 
 * This file is part of CitizenAir.
 * CitizenAir is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * CitizenAir is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with CitizenAir.  If not, see <http://www.gnu.org/licenses/>.
 */

// ========
// Settings
// ========
var tiles_provider = 'http://c.tile.stamen.com/toner-lite/{z}/{x}/{y}.jpg' // Stamen Toner

var colors = Array();
colors['highest'] = 'black';
colors['high'] = 'red';
colors['medium'] = 'orange';
colors['low'] = 'green';

var fillColors = Array();
fillColors['highest'] = '#111';
fillColors['high'] = '#f03';
fillColors['medium'] = '#F88017';
fillColors['low'] = '#3f3';

// =========
// Functions
// =========
if (!Date.now) {
    Date.now = function() { return new Date().getTime(); };
}

function toggleLegend(immediate) {
    var tmp = '';
    var tmp2 = '';
    if(e.style.marginLeft == '0px') {
        if(immediate) {
            tmp = e.style.transition;
            window.e.style.transition = 'none';
            tmp2 = m.style.transition;
            window.m.style.transition = 'none';
        }
        window.e.style.marginLeft = -e.offsetWidth + 'px';
        if(live === false) {
            window.m.style.marginLeft = '0';
        }
        window.location = '#';
        if(immediate) {
            window.e.style.transition = tmp;
            window.m.style.transition = tmp2;
        }
    }
    else {
        if(immediate) {
            tmp = e.style.transition;
            window.e.style.transition = 'none';
            tmp2 = m.style.transition;
            window.m.style.transition = 'none';
        }
        window.e.style.marginLeft = '0';
        if(live === false) {
            window.m.style.marginLeft = e.offsetWidth + 'px';
        }
        window.location = '#legend';
        if(immediate) {
            window.e.style.transition = tmp;
            window.m.style.transition = tmp2;
        }
    }
}

function geolocErrorFunction(error) { //Handle errors
    switch(error.code) {
        case error.TIMEOUT:
            //Restart with a greater timeout
            navigator.geolocation.getCurrentPosition(successFunction, errorFunction, {enableHighAccuracy:true,  maximumAge:0, timeout:20000});
            break;

        case error.PERMISSION_DENIED:
            alert("Erreur : L'application n'a pas l'autorisation d'utiliser les ressources de geolocalisation.");
            break;

        case error.POSITION_UNAVAILABLE:
            alert("Erreur : La position n'a pu être déterminée.");
            break;

        default:
            alert("Erreur "+error.code+" : "+error.message);
            break;
    }
}


function geolocSuccessFunction(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    window.map.setView([latitude, longitude], 18);
}

function relativeDate(time) {
    // Takes an ISO time and returns a string representing how
    // long ago the date represents.
    /*
     * JavaScript Pretty Date
     * Copyright (c) 2011 John Resig (ejohn.org)
     * Licensed under the MIT and GPL licenses.
     * http://ejohn.org/files/pretty.js
     */
    // Translated in French
    var date = new Date(time || "");
    var diff = (new Date().getTime() - date.getTime()) / 1000;
    var day_diff = Math.floor(diff / 86400);

    if ( isNaN(day_diff) || day_diff < 0)
        return;

    return day_diff == 0 && (
            diff < 60 && "à l'instant" ||
            diff < 120 && "il y a 1&nbsp;minute" ||
            diff < 3600 && "il y a " + Math.floor( diff / 60 ) + "&nbsp;minutes" ||
            diff < 7200 && "il y a 1&nbsp;heure" ||
            diff < 86400 && "il y a " + Math.floor( diff / 3600 ) + "&nbsp;heures") ||
        day_diff == 1 && "hier" ||
        day_diff < 7 && " il y a " + day_diff + " jours" ||
        day_diff < 31 && "il y a " + Math.ceil( day_diff / 7 ) + "&nbsp;semaines" ||
        day_diff < 365 && "il y a " + Math.ceil( day_diff / 30 ) + "&nbsp;mois" ||
        day_diff < 700 && "il y a 1&nbsp;an" ||
        "il y a " + Math.ceil(day_diff / 365) + "&nbsp;ans";
}

function getOpacity(time, start_decrease, fully_gone) {
    if(Date.now() - time < start_decrease) {
        return 1;
    }
    else if(Date.now() - time < fully_gone) {
        return (fully_gone - (Date.now() - time)) / (fully_gone - start_decrease) * 0.85 + 0.15;
    }
    else {
        return 0.15;
    }
}

function fitBounds(measures) {
    bounds = [];
    for(measure in measures) {
        bounds.push([measures[measure]['latitude'], measures[measure]['longitude']]);
    }
    window.map.fitBounds(bounds); //0.00015 = margin because of markers size
}


// ==========
// AJAX query
// ==========

var markers = new Array();

function ajaxQuery() {
    var xhr;
    var measures = false;
    try {  
        xhr = new XMLHttpRequest();
    }
    catch (e) {
        try {   
            xhr = new ActiveXObject('Msxml2.XMLHTTP');
        }
        catch (e2) {
            try {  
                xhr = new ActiveXObject('Microsoft.XMLHTTP');
            }
            catch (e3) {  
                xhr = false;
            }
        }
    }

    if(xhr == false) {
        alert("Une erreur a été rencontrée pendant la récupération des mesures. Veuillez réessayer.");
    }
    else {
        xhr.onreadystatechange  = function() {
            if(xhr.readyState == 4) {
                if(xhr.status == 200) {
                    measures = JSON.parse(xhr.responseText); // Parse the response

                    if(measures.length == 0) { //If there was an error
                        alert("Une erreur a été rencontrée pendant la récupération des mesures. Veuillez réessayer.");
                    }
                    else {
                        if(live === false) {
                            // Plot data
                            for(var measure in measures) {
                                var marker = L.circle([measures[measure].latitude, measures[measure].longitude], measures[measure].spatial_validity / 2, {
                                    color: colors[measures[measure].level],
                                    fillColor: fillColors[measures[measure].level],
                                    fillOpacity: getOpacity(measures[measure].timestamp, measures[measure].start_decrease, measures[measure].fully_gone)
                                });
                                if(!window.map.hasLayer(marker)) {
                                    marker.addTo(window.map);
                                    marker.bindPopup("Mesure effectuée " + relativeDate(measures[measure].timestamp) + ".<br/>" + measures[measure].type_name + " : " + measures[measure].measure + measures[measure].unit + "<br/>Capteur : " + measures[measure].capteur);
                                    window.markers.push(marker);
                                }
                            }
                            fitBounds(measures);
                        }
                        else if(live !== '') {
                            document.getElementsByClassName('live')[0].innerHTML = '<p>' + measures[0].measure + " " + measures[0].unit + ',<br/>' + relativeDate(measures[0].timestamp) + '</p>';
                        }
                    }
                }
            }
        };

        if(live === false) {
            xhr.open("GET", "api.php?do=get&visu=1",  true);
            xhr.send();
        }
        else if(live !== '') {
            xhr.open("GET", "api.php?do=get&visu=1&capteur="+live,  true);
            xhr.send();
        }
    }
}

// ==============
// Initialisation
// ==============

window.onresize = function() {
    m.style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - header_footer_size + 'px';

    if(window.isExport !== false) {
        return;
    }

    var tmp = '';
    if(e.style.marginLeft == '0px') {
        tmp = m.style.transition;
        m.style.transition = 'none';
        m.style.marginLeft = e.offsetWidth + 'px';
        m.style.transition = tmp;
    }

    e.style.height = m.style.height;
} // Same thing on window resizing

window.onload = function() {
    window.live = false;
    window.isExport = false;
    window.m = document.getElementById('map');
    window.e = document.getElementById("legend");
    window.header_footer_size = Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + Math.max(document.getElementById('footer').offsetHeight, document.getElementById('footer').clientHeight || 0);

    document.getElementById('need-js').innerHTML = '';

    if(window.location.hash == '#legend') {
        toggleLegend(true);
    }

    if(document.getElementsByClassName('live').length > 0) {
        window.live = true;
    }

    window.m.style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) - Math.max(document.getElementById('footer').offsetHeight, document.getElementById('footer').clientHeight || 0) +  'px'; // Set dynamically the height of the map

    if(document.getElementsByClassName('export').length > 0) {
        window.isExport = true;
        return;
    }

    window.e.style.height = m.style.height;


    if(live === false) {
        // Set the map
        window.map = L.map('map').setView([48.86222, 2.35083], 13);

        // Get location
        navigator.geolocation.getCurrentPosition(geolocSuccessFunction, geolocErrorFunction, {enableHighAccuracy:true,  maximumAge:60000, timeout:500});

        L.tileLayer(window.tiles_provider, {
            maxZoom: 19
        }).addTo(window.map);
    }

    ajaxQuery();
};

if(window.isExport === false) {
    window.setInterval(function() { ajaxQuery(); }, 300000);
}
