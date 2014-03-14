function errorFunction(error) //Handle errors
{
    switch(error.code)
    {
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


function successFunction(position)
{
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    window.map.setView([latitude, longitude], 18);
}


// Set dynamically the height of the map
document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px';

window.onresize = function() {
    document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px';
}

// Settings
// ========
//var tiles_provider = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png'; // OSM
//var tiles_provider = 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png' // Mapnik
//var tiles_provider = 'http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg' // Stamen Watercolor
var tiles_provider = 'http://c.tile.stamen.com/toner/{z}/{x}/{y}.jpg' // Stamen Toner
//var tiles_provider = 'http://otile1.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpg' // MapQuest
var attribution = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a> | <a href="http://www.citoyenscapteurs.net/">Citoyens Capteurs</a>';

// Set the map
// ===========
var map = L.map('map').setView([48.84874, 2.34211], 18);

// Get location
navigator.geolocation.getCurrentPosition(successFunction, errorFunction, {enableHighAccuracy:true,  maximumAge:60000, timeout:500});

L.tileLayer(tiles_provider, {
    attribution: attribution,
    maxZoom: 19
}).addTo(map);

// Add data
// ========
var circle1 = L.circle([48.84874, 2.34211], 5, {
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
circle5.bindPopup("Mesure effectuée il y a 5 jours.<br/>NO<sub>2</sub> : 180 µg/m<sup>3</sup>.");
