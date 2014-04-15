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
var curmonth = (new Date()).getMonth() + 1 
var curyear = (new Date()).getFullYear()

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

[].forEach.call(document.querySelectorAll('select[multiple=multiple] option'), function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            return false;
            });
});

