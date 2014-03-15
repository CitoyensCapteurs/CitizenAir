// ========
// Settings
// ========
var tiles_provider = 'http://c.tile.stamen.com/toner-lite/{z}/{x}/{y}.jpg' // Stamen Toner
var attribution = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>';

var colors = Array();
colors['high'] = 'red';
colors['medium'] = 'orange';
colors['low'] = 'green';

var fillColors = Array();
fillColors['high'] = '#f03';
fillColors['medium'] = '#F88017';
fillColors['low'] = '#3f3';

// =========
// Functions
// =========
if (!Date.now) {
    Date.now = function() { return new Date().getTime(); };
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
    var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
        diff = (((new Date()).getTime() - date.getTime()) / 1000),
        day_diff = Math.floor(diff / 86400);

    if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
        return;

    return day_diff == 0 && (
            diff < 60 && "à l'instant" ||
            diff < 120 && "il y a 1 minute" ||
            diff < 3600 && "il y a " + Math.floor( diff / 60 ) + " minutes" ||
            diff < 7200 && "il y a 1 heure" ||
            diff < 86400 && "il y a " + Math.floor( diff / 3600 ) + " heures") ||
        day_diff == 1 && "hier" ||
        day_diff < 7 && " il y a " + day_diff + " jours" ||
        day_diff < 31 && "il y a " + Math.ceil( day_diff / 7 ) + " semaines";
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

// ==============
// Initialisation
// ==============
document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px'; // Set dynamically the height of the map

window.onresize = function() {
    document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px';
} // Same thing on window resizing

// Set the map
var map = L.map('map').setView([48.84874, 2.34211], 18);

// Get location
navigator.geolocation.getCurrentPosition(geolocSuccessFunction, geolocErrorFunction, {enableHighAccuracy:true,  maximumAge:60000, timeout:500});

L.tileLayer(tiles_provider, {
    attribution: attribution,
    maxZoom: 19
}).addTo(map);

// ==========
// AJAX query
// ==========

var xhr;
var measures = false;
var markers = new Array();

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

                if(measures.length == 1) { //If there was an error
                    alert("Une erreur a été rencontrée pendant la récupération des mesures. Veuillez réessayer.");
                }
                else {
                    // Plot data
                    for(var measure in measures) {
                        var marker = L.circle([measure.latitude, measure.longitude], measure.spatial_validity / 2, {
                            color: colors[measure.level],
                            fillColor: fillColors[measure.level],
                            fillOpacity: getOpacity(measure.timestamp, measure.start_decrease, measure.fully_gone)
                        }).addTo(map);
                        marker.bindPopup("Mesure effectuée " + relativeDate(measure.timestamp) + ".<br/>" + measure.type_name + " : " + measure.measure + measure.unit + ".");

                        markers.push(marker);
                    }
                }
            }
        }
    };

    xhr.open("GET", "api.php?do=get&visu=1",  true);
}

// Demo data
// =========
/*var circle1 = L.circle([48.84874, 2.34211], 5, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 1
}).addTo(map);
circle1.bindPopup("Mesure effectuée il y a 30 mins.<br/>NO<sub>2</sub> : 200 µg/m<sup>3</sup>.");

var circle2 = L.circle([48.84946, 2.34011], 5, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5
}).addTo(map);
circle2.bindPopup("Mesure effectuée il y a 1h.<br/>NO<sub>2</sub> : 250 µg/m<sup>3</sup>.");

var circle3 = L.circle([48.84946, 2.3451], 5, {
    color: 'green',
    fillColor: '#3f3',
    fillOpacity: 1
}).addTo(map);
circle3.bindPopup("Mesure effectuée il y a 30 mins.<br/>NO<sub>2</sub> : 130 µg/m<sup>3</sup>.");


var circle4 = L.circle([48.84846, 2.3451], 5, {
    color: 'green',
    fillColor: '#3f3',
    fillOpacity: 0.15
}).addTo(map);
circle4.bindPopup("Mesure effectuée il y a 1 an.<br/>NO<sub>2</sub> : 100 µg/m<sup>3</sup>.");

var circle5 = L.circle([48.84886, 2.3391], 5, {
    color: 'orange',
    fillColor: '#F88017',
    fillOpacity: 0.5
}).addTo(map);
circle5.bindPopup("Mesure effectuée il y a 5 jours.<br/>NO<sub>2</sub> : 180 µg/m<sup>3</sup>.");*/
