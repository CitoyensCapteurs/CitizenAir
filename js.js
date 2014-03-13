// TODO
// * onresize

document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px';

// Settings
//var tiles_provider = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png'; // OSM
//var tiles_provider = 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png' // Mapnik
//var tiles_provider = 'http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg' // Stamen
//var tiles_provider = 'http://c.tile.stamen.com/toner/{z}/{x}/{y}.jpg' // Stamen
var tiles_provider = 'http://otile1.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpg' // MapQuest

// Set the map
var map = L.map('map').setView([48.84874, 2.34211], 18);

L.tileLayer(tiles_provider, {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
}).addTo(map);
