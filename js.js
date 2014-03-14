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

L.tileLayer(tiles_provider, {
    attribution: attribution,
    maxZoom: 19
}).addTo(map);

// Add data
// ========
L.circle([48.84874, 2.34211], 50, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5
}).addTo(map);

L.circle([48.84946, 2.34011], 50, {
    color: 'blue',
    fillColor: '#30f',
    fillOpacity: 0.5
}).addTo(map);

L.circle([48.84946, 2.3451], 50, {
    color: 'green',
    fillColor: '#3f3',
    fillOpacity: 0.5
}).addTo(map);
