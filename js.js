// TODO
// * onresize

document.getElementById("map").style.height = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - Math.max(document.getElementById('title').offsetHeight, document.getElementById('title').clientHeight || 0) + 'px';

// Settings
var tiles_provider = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';

// Set the map
var map = L.map('map').setView([48.84874, 2.34211], 18);

L.tileLayer(tiles_provider, {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 20
}).addTo(map);
