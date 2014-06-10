/* Timeline.js
 * Author : Phyks (http://phyks.me)
 * http://phyks.github.io/timeline.js

 * --------------------------------------------------------------------------------
 * "THE NO-ALCOHOL BEER-WARE LICENSE" (Revision 42):
 * Phyks (webmaster@phyks.me) wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff (and you can also do whatever you want
 * with this stuff without retaining it, but that's not cool...). If we meet some
 * day, and you think this stuff is worth it, you can buy me a <del>beer</del> soda
 * in return.
 * Phyks
 * ---------------------------------------------------------------------------------
 */


/* Initialization :
 * arg is an object with :
 * id = id of the parent block
 * height / width = size of the svg
 * grid = small / big / both
 * x_axis = true / false to show or hide x axis
 * line = none / line / dashed to choose line type
 * rounded = true / false to use splines to smoothen the graph
 * x_callback = function(args) { } or false is called to display the legend on the x axis
 * fill = true / false to fill below the graph or not
 */
function Timeline(arg) {
    this.ns = "http://www.w3.org/2000/svg";
    this.xlinkns = "http://www.w3.org/1999/xlink";

    this.marginBottom = 10;
    this.marginTop = 15;
    this.marginLeft = 10;
    this.marginRight = 10;
    this.rounded = false;
    this.x_axis = false;
    this.fill = true;
    this.line = 'line';
    this.dashed_style = '5, 5';

    this.parent_holder = false;
    this.holder = false;
    this.g = false;
    this.axis = false;
    this.graphs = [];
    this.raw_points = [];
    this.x_callback = false;

    var old = window.onresize || function () {};
    var obj = this;
    window.onresize = function() {
        old();

        // Redraw the Timeline to fit the new size
        if(obj.g !== false) {
            obj.g.setAttribute('transform', 'translate(0, ' + obj.parent_holder.offsetHeight + ') scale(1, -1)');
            if(obj.x_axis === true) {
                obj.axis.setAttribute('x2', obj.parent_holder.offsetWidth - obj.marginLeft - 3 - obj.marginRight);
            }
            [].forEach.call(obj.holder.querySelectorAll('.label, .over, .point, .line, .graph, .legend_x'), function(el) {
                el.parentNode.removeChild(el);
            });
            obj.draw();
        }
    };

    if(!document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image", "1.1")) {
        alert("ERROR : Your browser does not support embedded SVG.");
    }
    this.parent_holder = document.getElementById(arg.id);

    this.parent_holder.style.width = arg.width;
    this.parent_holder.style.height = arg.height;
    var svg = this.createElement('svg:svg', { 'width': '100%', 'height': '100%' });
    svg.setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xlink', this.xlinkns);
    this.parent_holder.appendChild(svg);

    this.holder = this.parent_holder.querySelector('svg');

    defs = this.createElement('defs', {});
    this.holder.appendChild(defs);

    if(arg.grid === 'small' || arg.grid === 'both') {
        var small_grid_pattern = this.createElement('pattern', { 'id': 'smallGrid', 'width': 8, 'height': 8, 'patternUnits': 'userSpaceOnUse' });

        var small_grid_path = this.createElement('path', { 'd': 'M 8 0 L 0 0 0 8', 'fill': 'none', 'stroke': 'gray', 'stroke-width': '0.5' });
        small_grid_pattern.appendChild(small_grid_path);

        defs.appendChild(small_grid_pattern);
    }
    if(arg.grid === 'big' || arg.grid === 'both') {
        var grid_pattern = this.createElement('pattern', { 'id': 'grid', 'width': 80, 'height': 80, 'patternUnits': 'userSpaceOnUse' });

        if(arg.grid === 'both') {
            var grid_rect = this.createElement('rect', {'width': 80, 'height': 80, 'fill': 'url(#smallGrid)' });
            grid_pattern.appendChild(grid_rect);
        }

        var grid_path = this.createElement('path', {'d': 'M 80 0 L 0 0 0 80', 'fill': 'none', 'stroke': 'gray', 'stroke-width': '1'});
        grid_pattern.appendChild(grid_path);

        defs.appendChild(grid_pattern);
    }
    this.grid = arg.grid;


    var marker = this.createElement('marker', {'id': 'markerArrow', 'markerWidth': 13, 'markerHeight': 13, 'refX': 2, 'refY': 6, 'orient': 'auto' });
    var marker_path = this.createElement('path', {'d': 'M2,2 L2,11 L10,6 L2,2', 'fill': 'gray' });
    marker.appendChild(marker_path);
    defs.appendChild(marker);

    this.g = this.createElement('g', {'transform': 'translate(0, ' + this.parent_holder.offsetHeight + ') scale(1, -1)'});
    this.holder.appendChild(this.g);

    if(arg.x_axis === true) {
        this.axis = this.createElement('line', {'x1': this.marginLeft, 'y1': this.parent_holder.offsetHeight / 2 + 1.5, 'x2': this.parent_holder.offsetWidth - 13 - this.marginRight, 'y2': this.parent_holder.offsetHeight / 2 + 1.5, 'stroke': 'gray', 'stroke-width': 3, 'marker-end': 'url("#markerArrow")'});
        this.g.appendChild(this.axis);
    }

    if(this.grid !== "none") {
        var grid = this.createElement('rect', {'width': '100%', 'height': '100%'});
        if(this.grid === 'big' || this.grid === 'both') {
            grid.setAttribute('fill', 'url(#grid)');
        }
        else {
            grid.setAttribute('fill', 'url(#smallGrid)');
        }
        this.g.appendChild(grid);
    }

    this.rounded = arg.rounded;
    this.x_axis = arg.x_axis;
    this.line = arg.line;
    this.fill = arg.fill;

    this.x_callback = arg.x_callback;
}


// Create an element "element" with the attributes "attrs"
Timeline.prototype.createElement = function (element, attrs) {
    var el = document.createElementNS(this.ns, element);
    for(attr in attrs) {
        el.setAttribute(attr, attrs[attr]);
    }

    return el;
};

// Check wether the element "element" has class "class"
Timeline.prototype.hasClass = function (element, cls) {
    return (' ' + element.getAttribute('class') + ' ').indexOf(' ' + cls + ' ') > -1;
};

// Add a new graph to the Timeline
Timeline.prototype.addGraph = function (graph, color) {
    this.graphs[graph] = color;
};

// Test wether a graph of name "graph" already exists
Timeline.prototype.hasGraph = function (graph) {
    if(typeof(this.graphs[graph]) === 'undefined') {
        return false;
    }
    else {
        return true;
    }
};

// Clear the specified graph data, or completely clear all the graph data
Timeline.prototype.clearGraph = function (graph) {
    if(typeof(graph) === 'undefined') {
        this.raw_points = [];
        this.graphs = [];
    }
    else {
        for(var i = 0; i < this.raw_points.length; i++) {
            if(this.raw_points[i].graph === graph) {
                this.raw_points[i] = undefined;
            }
        }
    }
};

// Add points to the specified graph
Timeline.prototype.addPoints = function (graph, data) {
    for(var point = 0; point < data.length; point++) {
        var insert = {'graph': graph, 'x': data[point].x, 'y': data[point].y};
        if(typeof(data[point].label) !== 'undefined') {
            insert.label = data[point].label;
        }
        else {
            insert.label = '';
        }
        if(typeof(data[point].click) !== 'undefined') {
            insert.click = data[point].click;
        }
        else {
            insert.click = false;
        }
        this.raw_points.push(insert);
    }

    this.raw_points.sort(function (a, b) {
        if(a.x < b.x) {
            return -1;
        }
        else if(a.x == b.x) {
            return 0;
        }
        else {
            return 1;
        }
    });
};

// Compute new coordinates, knowing the min and max value to fit the graph in the container
Timeline.prototype.newCoordinate = function(value, min, max, minValue, maxValue) {
    var a = (maxValue - minValue) / (max - min);
    return a *(value - min) + minValue;
};

// Compute new X and Y values
Timeline.prototype.getNewXY = function (minX, maxX, minY, maxY) {
    var obj = this;
    return function (x, y) {
        return { 
            'x': obj.newCoordinate(x, minX, maxX, obj.marginLeft, obj.parent_holder.offsetWidth - obj.marginRight),
            'y': obj.newCoordinate(y, minY, maxY, 2*obj.marginBottom, obj.parent_holder.offsetHeight - obj.marginTop)
        };
    };
};

// Get the necessary control points to smoothen the graph, is rounded is true
Timeline.prototype.getControlPoints = function (data) {
    // From http://www.particleincell.com/wp-content/uploads/2012/06/bezier-spline.js
    var p1 = new Array();
	var p2 = new Array();
	var n = data.length - 1;
	
	/*rhs vector*/
	var a = new Array();
	var b = new Array();
	var c = new Array();
	var r = new Array();
	
	/*left most segment*/
	a[0] = 0;
	b[0] = 2;
	c[0] = 1;
	r[0] = data[0] + 2*data[1];
	
	/*internal segments*/
	for (var i = 1; i < n - 1; i++) {
        a[i] = 1;
        b[i] = 4;
        c[i] = 1;
        r[i] = 4 * data[i] + 2 * data[i+1];
	}
			
	/*right segment*/
	a[n-1] = 2;
	b[n-1] = 7;
	c[n-1] = 0;
	r[n-1] = 8*data[n-1] + data[n];
	
	/*solves Ax=b with the Thomas algorithm (from Wikipedia)*/
    var m;
	for (var i = 1; i < n; i++) {
		m = a[i]/b[i-1];
		b[i] = b[i] - m * c[i - 1];
		r[i] = r[i] - m*r[i-1];
	}
 
	p1[n-1] = r[n-1]/b[n-1];
	for (var i = n - 2; i >= 0; --i) {
		p1[i] = (r[i] - c[i] * p1[i+1]) / b[i];
    }
		
	/*we have p1, now compute p2*/
	for (var i=0;i<n-1;i++) {
		p2[i] = 2*data[i+1] - p1[i+1];
    }
	
	p2[n-1] = 0.5*(data[n] + p1[n-1]);
	
	return {p1:p1, p2:p2};
};


// Get the scale so that graph fits with window
Timeline.prototype.scale = function(data) {
    var empty = true;
    for(graph in data) {
        empty = false;
        break;
    }
    if(empty) {
        return false;
    }

    var minX = false, minY = 0;
    var maxX = false, maxY = false;
    var circle = false, last_point = false, line = false;

    // Look for max and min values for both axis
    for(var point = 0; point < data.length; point++) {
        if(data[point].x < minX || minX === false) {
            minX = data[point].x;
        }
        if(data[point].x > maxX || maxX === false) {
            maxX = data[point].x;
        }
        if(data[point].y < minY) {
            minY = data[point].y;
        }
        if(data[point].y > maxY || maxY === false) {
            maxY = data[point].y;
        }
    }

    // Scale the grid, if needed
    var scale = this.getNewXY(minX, maxX, minY, maxY);
    var tmp = scale(Math.pow(10, Math.floor(Math.log(maxX - minX) / Math.log(10))), Math.pow(10, Math.floor(Math.log(maxY - minY) / Math.log(10))));
    var origin = scale(0, 0);
    var coordinates = {'x': tmp.x - origin.x, 'y': tmp.y - origin.y };
    if(this.grid === 'big' || this.grid === 'both') {
        var grid = this.holder.getElementById('grid');
        grid.setAttribute('width', coordinates.x);
        grid.setAttribute('height', coordinates.y);
        var big_coords = scale(Math.floor(minX / Math.pow(10, Math.floor(Math.log(maxX - minX) / Math.log(10)))) * Math.pow(10, Math.floor(Math.log(maxX - minX) / Math.log(10))), Math.floor(minY / Math.pow(10, Math.floor(Math.log(maxY - minY) / Math.log(10)))) * Math.pow(10, Math.floor(Math.log(maxY - minY) / Math.log(10))));
        grid.setAttribute('y', big_coords.y);
        grid.setAttribute('x', big_coords.x);
        grid.querySelector('path').setAttribute('d', 'M '+coordinates.x+' 0 L 0 0 0 '+coordinates.y);

        if(this.grid === 'both') {
            grid.querySelector('rect').setAttribute('width', coordinates.x);
            grid.querySelector('rect').setAttribute('height', coordinates.y);
        }
    }
    if(this.grid === 'small' || this.grid === 'both') {
        coordinates.x = coordinates.x / 10;
        coordinates.y = coordinates.y / 10;
        var grid = this.holder.getElementById('smallGrid');
        grid.setAttribute('width', coordinates.x);
        grid.setAttribute('height', coordinates.y);
        if(this.grid === 'small') {
            var small_coords = scale(Math.floor(minX / Math.pow(10, Math.floor(Math.log(maxX - minX) / Math.log(10)))) * Math.pow(10, Math.floor(Math.log(maxX - minX) / Math.log(10))), Math.floor(minY / Math.pow(10, Math.floor(Math.log(maxY - minY) / Math.log(10)))) * Math.pow(10, Math.floor(Math.log(maxY - minY) / Math.log(10))));
            grid.setAttribute('y', small_coords.y);
            grid.setAttribute('x', small_coords.x);
        }
        grid.querySelector('path').setAttribute('d', 'M '+coordinates.x+' 0 L 0 0 0 '+coordinates.y);
    }

    /* Draw axis */
    if(this.x_axis === true) {
        y = scale(0, 0).y;
        this.axis.setAttribute('y1', y);
        this.axis.setAttribute('y2', y);
    }

    return scale;
};

// Draw graphs
Timeline.prototype.draw = function() {
    var scale = this.scale(this.raw_points);
    var points = [], path;
    var px, py;
    var element;
    var obj = this;

    for(var point = 0; point < this.raw_points.length; point++) {
        var tmp = scale(this.raw_points[point].x, this.raw_points[point].y);
        points.push({'id': point, 'x': tmp.x, 'y': tmp.y, 'graph': this.raw_points[point].graph, 'click': this.raw_points[point].click, 'label': this.raw_points[point].label});
    }

    // Draw each graph
    for(var graph in this.graphs) {
        var filtered_points = points.filter(function(el) { return el.graph == graph; });
        path = '';

        // Draw line
        if(this.rounded === true) {
            var x = new Array(), y = new Array();
            for(var point = 0; point < filtered_points.length; point++) {
                x.push(filtered_points[point].x);
                y.push(filtered_points[point].y);
            }
            px = this.getControlPoints(x);
            py = this.getControlPoints(y);
            for(var point = 0; point < filtered_points.length - 1; point++) {
                path += 'C '+px.p1[point]+' '+py.p1[point]+' '+px.p2[point]+' '+py.p2[point]+' '+filtered_points[point+1].x+' '+filtered_points[point+1].y+' ';
            }
        }
        else {
            for(var point = 1; point < filtered_points.length; point++) {
                path += 'L '+filtered_points[point].x+' '+filtered_points[point].y+' ';
            }
        }

        if(this.line !== 'none' && filtered_points.length !== 0) {
            element = this.createElement('path', {'class': 'line', 'stroke': this.graphs[graph], 'stroke-width': 2, 'fill': 'none', 'd': 'M '+filtered_points[0].x+' '+filtered_points[0].y+' '+path});
            if(this.line === 'dashed') {
                element.setAttribute('style', 'stroke-dasharray: '+this.dashed_style);
            }
            this.g.appendChild(element);
        }

        // Draw fill
        if(this.fill) {
            element = this.createElement('path', {'class': 'graph', 'fill': this.graphs[graph], 'opacity': '0.25', 'stroke': 'none', 'd': 'M '+filtered_points[0].x+' '+2*this.marginBottom+' L '+filtered_points[0].x+' '+filtered_points[0].y+' '+ path + ' L '+filtered_points[filtered_points.length - 1].x+' '+2*this.marginBottom+' Z' });
            this.g.insertBefore(element, this.g.querySelectorAll('.over')[0]);
        }
    }

    // Hover effect
    var prev = 0;
    for(var point = 0; point < points.length;) {
        var rect = this.createElement('rect', {'class': 'over', 'id': 'over_'+point, 'y': 0, 'fill': 'white', 'opacity': 0, 'height': '100%'});
        var currents = [point];

        var next = point + 1;
        if(point < points.length - 1) {
            while(points[next].x == points[point].x) {
                if(i > points.length) {
                    break;
                }
                next++;
            }
        }

        for(var i = prev + 1; i < next; i++) {
            currents.push(i);
        }

        if(point == 0) {
            rect.setAttribute('x', 0);
        }
        else {
            rect.setAttribute('x', (points[point].x + points[prev].x) / 2);
        }

        if(point == points.length - 1) {
            rect.setAttribute('width', this.parent_holder.offsetWidth - (points[point].x + points[point - 1].x)/2 + 1);
        }
        else if(point == 0) {
            rect.setAttribute('width', (points[1].x + points[0].x)/2 + this.marginLeft + 1);
        }
        else {
            rect.setAttribute('width', (points[next].x - points[prev].x)/2 + 1);
        }

        this.g.appendChild(rect);

        rect.addEventListener('mouseover', (function(arg) {
            return function() {
                for(var i = 0; i < arg.length; i++) {
                    obj.holder.getElementById('point_'+arg[i]).setAttribute('r', '6');
                    obj.holder.getElementById('label_'+arg[i]).setAttribute('display', 'block');
                }
            };
        })(currents));

        rect.addEventListener('mouseout', function() {
                // Reinitialize all states
                [].forEach.call(obj.holder.querySelectorAll('.point'), function(el) {
                    el.setAttribute('r', '4');
                });

                [].forEach.call(obj.holder.querySelectorAll('.label'), function(el) {
                    el.setAttribute('display', 'none');
                });
            });

        // Clear also the labels when moving outside the SVG element
        this.holder.addEventListener('mouseout', function() {
                // Reinitialize all states
                [].forEach.call(obj.holder.querySelectorAll('.point'), function(el) {
                    el.setAttribute('r', '4');
                });

                [].forEach.call(obj.holder.querySelectorAll('.label'), function(el) {
                    el.setAttribute('display', 'none');
                });
            });

        if(this.x_callback !== false && points[point].x + 2.5 < this.parent_holder.offsetWidth - this.marginRight) {
            element = this.createElement('text', {'class': 'legend_x', 'fill': 'gray', 'transform': 'translate(0, ' + this.parent_holder.offsetHeight + ') scale(1, -1)'});
            element.appendChild(document.createTextNode(this.x_callback(this.raw_points[point].x)));
            this.g.appendChild(element);
            element.setAttribute('x', points[point].x - element.getBoundingClientRect().width / 2 + 2.5);
            var y_zero = scale(0, 0).y;
            element.setAttribute('y', this.parent_holder.offsetHeight - this.marginBottom - y_zero);

            element = this.createElement('line', {'class': 'legend_x', 'stroke': 'gray', 'stroke-width': 2, 'x1': points[point].x, 'x2': points[point].x, 'y1': y_zero - 5, 'y2': y_zero + 5});
            this.g.appendChild(element);
        }

        prev = next - 1;
        point = next;
    }

    // Draw points and labels
    for(var graph in this.graphs) {
        var filtered_points = points.filter(function(el) { return el.graph == graph; });

        for(var point = 0; point < filtered_points.length; point++) {
            element = this.createElement('circle', {'class': 'point', 'id': 'point_'+filtered_points[point].id, 'cx': filtered_points[point].x, 'cy': filtered_points[point].y, 'r': 4, 'fill': '#333', 'stroke': this.graphs[graph], 'stroke-width': 2});
            this.g.insertBefore(element, this.g.querySelectorAll('.label')[0]);

            if(filtered_points[point].click !== false) {
                element.onclick = filtered_points[point].click;
            }

            element.addEventListener('mouseover', function() {
                this.setAttribute('r', '6');
                obj.holder.getElementById(this.getAttribute('id').replace('point', 'label')).setAttribute('display', 'block');
            });

            if(filtered_points[point].label !== '') {
                var g = this.createElement('g', { 'class': 'label', 'id': 'label_'+filtered_points[point].id, 'transform': 'translate(0, ' + this.parent_holder.offsetHeight + ') scale(1, -1)'});
                this.g.appendChild(g);

                g.addEventListener('mouseover', function() {
                    obj.holder.getElementById(this.getAttribute('id').replace('label', 'point')).setAttribute('r', '6');
                    this.setAttribute('display', 'block');
                });

                element = this.createElement('text', {});
                var text = filtered_points[point].label.replace('</sup>', '<sup>').split('<sup>');
                for(var i = 0; i < text.length; i++) {
                    text[i] = text[i].replace(/(<([^>]+)>)/ig,"").replace('%y', this.raw_points[filtered_points[point].id].y).replace('%x', this.raw_points[filtered_points[point].id].x);
                    if(i % 2 == 0) {
                        element.appendChild(document.createTextNode(text[i]));

                    }
                    else {
                        var tmp = this.createElement('tspan', {'dy': '-5'});
                        tmp.appendChild(document.createTextNode(text[i]));
                        element.appendChild(tmp);
                    }
                }

                path = this.createElement('path', {'stroke': 'black', 'stroke-width': 2, 'fill': 'white', 'opacity': 0.5});

                // Append here to have them with the good z-index, update their attributes later
                g.appendChild(path);
                g.appendChild(element);

                var x_text = filtered_points[point].x - element.getBoundingClientRect().width / 2;
                var y_text = this.parent_holder.offsetHeight - filtered_points[point].y - this.marginTop - this.marginBottom;
                var element_width = element.getBoundingClientRect().width;
                var element_height = element.getBoundingClientRect().height;

                if(filtered_points[point].x - element.getBoundingClientRect().width / 2 < 0) {
                    x_text = filtered_points[point].x + this.marginLeft + this.marginRight;
                    y_text = this.parent_holder.offsetHeight - filtered_points[point].y + 5;
                    path.setAttribute('d', 'M '+(x_text - 5)+' '+(y_text + 5)+' L '+(x_text - 5)+' '+(y_text - element_height/2 + 7.5)+' L '+(x_text - 10)+' '+(y_text - element_height/2 + 5)+' L '+(x_text - 5)+' '+(y_text - element_height/2 + 2.5)+' L '+(x_text - 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text + 5)+' Z');
                }
                else if(filtered_points[point].y + element.getBoundingClientRect().height + this.marginBottom + 2 > this.parent_holder.offsetHeight) {
                    x_text = filtered_points[point].x + this.marginLeft + this.marginRight;
                    y_text = this.parent_holder.offsetHeight - filtered_points[point].y + 5;
                    path.setAttribute('d', 'M '+(x_text - 5)+' '+(y_text + 5)+' L '+(x_text - 5)+' '+(y_text - element_height/2 + 7.5)+' L '+(x_text - this.marginBottom)+' '+(y_text - element_height/2 + 5)+' L '+(x_text - 5)+' '+(y_text - element_height/2 + 2.5)+' L '+(x_text - 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text + 5)+' Z');

                    if(x_text + element_width > this.parent_holder.offsetWidth) {
                        x_text = filtered_points[point].x - element_width - this.marginLeft - this.marginRight;
                        y_text = this.parent_holder.offsetHeight - filtered_points[point].y + 5;
                        path.setAttribute('d', 'M '+(x_text - 5)+' '+(y_text + 5)+' L '+(x_text - 5)+' '+(y_text  - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height/2 + 2.5)+' L '+(x_text + element_width + 10)+' '+(y_text - element_height/2 + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height/2 + 7.5)+' L '+(x_text + element_width + 5)+' '+(y_text + 5)+' Z');
                    }
                }
                else if(filtered_points[point].x + element_width / 2 + this.marginLeft + 2 > this.parent_holder.offsetWidth) {
                    x_text = filtered_points[point].x - element_width - this.marginLeft - this.marginRight;
                    y_text = this.parent_holder.offsetHeight - filtered_points[point].y + 5;
                    path.setAttribute('d', 'M '+(x_text - 5)+' '+(y_text + 5)+' L '+(x_text - 5)+' '+(y_text  - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height/2 + 2.5)+' L '+(x_text + element_width + 10)+' '+(y_text - element_height/2 + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height/2 + 7.5)+' L '+(x_text + element_width + 5)+' '+(y_text + 5)+' Z');
                }
                else {
                    path.setAttribute('d', 'M '+(x_text - 5)+' '+(y_text + 5)+' L '+(x_text - 5)+' '+(y_text  - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text - element_height + 5)+' L '+(x_text + element_width + 5)+' '+(y_text + 5)+' L '+(x_text + element_width/2 + 2.5)+' '+(y_text + 5)+' L '+(x_text + element_width/2)+' '+(y_text + 10)+' L '+(x_text + element_width/2 - 2.5)+' '+(y_text + 5)+' Z');
                }
                element.setAttribute('x', x_text);
                element.setAttribute('y', y_text);
    

                g.setAttribute('display', 'none');
            }
        }
    }
};
