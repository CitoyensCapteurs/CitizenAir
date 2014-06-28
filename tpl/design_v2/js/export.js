/* CitizenAir is free software: you can redistribute it and/or modify
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
 * along with CitizenAir.  If not, see <http://www.gnu.org/licenses/>.-->
 */

// Calendriers
// ===========
function buildCal(id, m, y, cM, cH, cDW, cD){
    var mn = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    var dim = [31, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    var current_value = document.getElementById(id.replace('_picker', '')).value;

    if(window.just_load === true && current_value !== '') {
        var current_date = new Date(parseInt(current_value) * 1000);
        m = current_date.getMonth() + 1;
        y = current_date.getFullYear();
    }
    window.just_load = false;

    var oD = new Date(y, m-1, 1);
    oD.od = oD.getDay() % 7;

    var todaydate = new Date();
    var scanfortoday = (y==todaydate.getFullYear() && m==todaydate.getMonth()+1) ? todaydate.getDate() : 0;

    dim[1] = (((oD.getFullYear()%100!=0)&&(oD.getFullYear()%4==0))||(oD.getFullYear()%400==0)) ? 29 : 28;

    var t = '<table><thead><tr>';
    var next_m = m + 1;
    var next_y = y;
    if (next_m > 12) {
        next_m = 1;
        next_y = y + 1;
    }
    var prev_m = m - 1;
    var prev_y = y;
    if (prev_m < 1) {
        prev_m = 12;
        prev_y = y - 1;
    }
    t += '<td><a href="javascript:void(0)" onclick="buildCal(\''+id+'\', '+prev_m+', '+prev_y+', \''+cM+'\', \''+cH+'\', \''+cDW+'\', \''+cD+'\');">«</a></td><td colspan="5" class="'+cH+'">'+mn[m-1]+' - '+y+'</td><td><a href="javascript:void(0)" onclick="buildCal(\''+id+'\', '+next_m+', '+next_y+', \''+cM+'\', \''+cH+'\', \''+cDW+'\', \''+cD+'\');">»</a></td></tr></thead><tr>';

    for (s=0; s<7; s++) {
        t += '<th class="'+cDW+'">'+"LMMJVSD".substr(s,1)+'</th>';
    }
    
    t += '</tr><tr>';


    for (i = 1; i < 36; i++) {
        var x = ((i-oD.od >= 0) && (i-oD.od < dim[m-1])) ? (i-oD.od+1) : '&nbsp;';
        if (Math.floor((new Date(y, m-1, x, 2, 0, 0)).getTime()/1000) == current_value) {
            x = '<span class="cal_selected">'+x+'</span>';
        }

        if (x == scanfortoday) {
            x = '<span class="aujourdhui">'+x+'</span>';
        }

        if (x !== '&nbsp;') {
            t += '<td class="'+cD+'"><a href="javascript:void(0)" onclick="selectDay(\''+id+'\', '+(i-oD.od + 1)+', '+m+', '+y+', this);">'+x+'</a></td>';
        }
        else {
            t += '<td class="'+cD+'">&nbsp;</td>';
        }

        if (i % 7 == 0) {
            t+='</tr><tr>';
        }
    }
    t += '</tr></table>';
    document.getElementById(id).innerHTML = t;
}

function selectDay(id, d, m, y, el) {
    document.getElementById(id.replace('_picker', '')).value = Math.floor((new Date(y, m-1, d, 2, 0, 0)).getTime()/1000);
    if(document.getElementById(id).querySelector('.cal_selected')) {
        document.getElementById(id).querySelector('.cal_selected').className = document.getElementById(id).querySelector('.cal_selected').className.replace('cal_selected', '');
    }
    el.className = el.className + " cal_selected";
}

var just_load = true;
var curmonth = (new Date()).getMonth() + 1;
var curyear = (new Date()).getFullYear();

buildCal('time_min_picker', curmonth, curyear, "cal", "mois", "jours_semaine", "jours");
buildCal('time_max_picker', curmonth, curyear, "cal", "mois", "jours_semaine", "jours");

document.getElementById('time_min').addEventListener('click', function () {
    document.getElementById('time_min_picker').style.display = 'block';
});

document.getElementById('time_min_picker').addEventListener('mousedown', function(ev) { // mousedown fires before blur
    ev.preventDefault();
});

document.getElementById('time_min').addEventListener('blur', function (ev) {
    document.getElementById('time_min_picker').style.display = 'none';
});

document.getElementById('time_max_picker').addEventListener('mousedown', function(ev) { // mousedown fires before blur
    ev.preventDefault();
});

document.getElementById('time_max').addEventListener('click', function () {
    document.getElementById('time_max_picker').style.display = 'block';
});

document.getElementById('time_max').addEventListener('blur', function () {
    document.getElementById('time_max_picker').style.display = 'none';
});


// Nominatim search
// ================
window.onload = function () {
    nominatim_direct();
    nominatim_reverse();
};


var xhr;
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

var old_address = '';
var lat_address = false;
var lng_adress = false;
var last_edit = 'reverse';
function nominatim_direct() {
    if(document.getElementById('address').value !== '' && document.getElementById('address').value !== window.old_address && last_edit === 'direct') {
        window.old_address = document.getElementById('address').value;
        document.getElementById('found_address').innerHTML = 'Recherche en cours…';

        if(window.xhr == false) {
            document.getElementById('found_address').innerHTML = 'Une erreur a été rencontrée pendant la récupération des adresses.';
        }
        else {
            window.xhr.onreadystatechange = function() {
                if(window.xhr.readyState == 4) {
                    if(window.xhr.status == 200) {
                        var result = JSON.parse(window.xhr.responseText);
                        if(result.length === 0) {
                            document.getElementById('found_address').innerHTML = "Adresse non trouvée.";
                        }
                        else {
                            result = result[0];

                            var address = 'Trouvé&nbsp;: ';

                            if(result.address.house_number !== undefined) {
                                address += result.address.house_number+', ';
                            }

                            if(result.address.road !== undefined) {
                                address += result.address.road+', ';
                            }

                            if(result.address.city_district !== undefined) {
                                address += result.address.city_district+', ';
                            }

                            if(result.address.city !== undefined) {
                                address += result.address.city;
                            }
                            document.getElementById('found_address').innerHTML = address;

                            window.lat_address = parseFloat(result.lat);
                            window.lng_address = parseFloat(result.lon);

                            if(document.getElementById('radius').value !== '') {
                                var radius = parseFloat(document.getElementById('radius').value);
                                document.getElementById('lat_min').value = window.lat_address - radius / 111111;
                                document.getElementById('lat_max').value = window.lat_address + radius / 111111;
                                document.getElementById('long_min').value = window.lng_address - radius / (111111 * Math.cos(window.lat_address));
                                document.getElementById('long_max').value = window.lng_address + radius / (111111 * Math.cos(window.lat_address));
                            }
                        }
                    }
                }
            };
        }

        xhr.open("GET", "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&countrycodes=fr&limit=1&addressdetails=1&q="+encodeURIComponent(document.getElementById('address').value));
        xhr.send();
    }
    setTimeout('nominatim_direct()', 200);
}

function radius_change() {
    if(document.getElementById('radius').value !== '' && window.lat_address !== false && window.lng_address !== false) {
        var radius = parseFloat(document.getElementById('radius').value);
        console.log(radius);
        document.getElementById('lat_min').value = window.lat_address - radius / 111111;
        document.getElementById('lat_max').value = window.lat_address + radius / 111111;
        document.getElementById('long_min').value = window.lng_address - radius / (111111 * Math.cos(window.lat_address));
        document.getElementById('long_max').value = window.lng_address + radius / (111111 * Math.cos(window.lat_address));
    }
}

var old_lat_min = '';
var old_lat_max = '';
var old_lng_min = '';
var old_lng_max = '';
function nominatim_reverse() {
    var lat_min = document.getElementById('lat_min').value;
    var lat_max = document.getElementById('lat_max').value;
    var lng_min = document.getElementById('long_min').value;
    var lng_max = document.getElementById('long_max').value;

    if(lat_min !== '' && lat_max !== '' && lng_min !== '' && lng_max !== '' && (lat_min !== window.old_lat_min || lat_max !== window.old_lat_max || lng_min !== window.old_lng_min || lng_max !== window.old_lng_max) && last_edit === 'reverse') {
        var lat = (parseFloat(lat_min) + parseFloat(lat_max))/2;
        var lng = (parseFloat(lng_min) + parseFloat(lng_max))/2;

        window.old_lat_min = lat_min;
        window.old_lat_max = lat_max;
        window.old_lng_min = lng_min;
        window.old_lng_max = lng_max;

        document.getElementById('found_address').innerHTML = 'Recherche en cours…';

        if(window.xhr == false) {
            document.getElementById('found_address').innerHTML = 'Une erreur a été rencontrée pendant la récupération des adresses.';
        }
        else {
            window.xhr.onreadystatechange = function() {
                if(window.xhr.readyState == 4) {
                    if(window.xhr.status == 200) {
                        var result = JSON.parse(window.xhr.responseText);

                        if(result.address !== undefined) {
                            var address = '';

                            if(result.address.house_number !== undefined) {
                                address += result.address.house_number+', ';
                            }

                            if(result.address.road !== undefined) {
                                address += result.address.road+', ';
                            }

                            if(result.address.city_district !== undefined) {
                                address += result.address.city_district+', ';
                            }

                            if(result.address.city !== undefined) {
                                address += result.address.city;
                            }
                            document.getElementById('found_address').innerHTML = 'Trouvé&nbsp;: '+address;
                            document.getElementById('address').value = address;

                            document.getElementById('radius').value = (parseFloat(lat_max) - parseFloat(lat_min))/2 * 111111;
                        }
                        else {
                            document.getElementById('found_address').innerHTML = 'Adresse non trouvée.';
                        }
                    }
                }
            };
        }

        xhr.open("GET", "http://open.mapquestapi.com/nominatim/v1/reverse.php?format=json&lat="+encodeURIComponent(lat)+"&lon="+encodeURIComponent(lng));
        xhr.send();
    }
    setTimeout('nominatim_reverse()', 200);
}
