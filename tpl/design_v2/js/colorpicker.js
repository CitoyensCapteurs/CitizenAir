function getOffset(elem) {
    var offset = null;
    if ( elem ) {
        offset = {left: 0, top: 0};
        do {
            offset.top += elem.offsetTop;
            offset.left += elem.offsetLeft;
            elem = elem.offsetParent;
        } while ( elem );
    }
    return offset;
}

window.onload = function () {
    var bCanPreview = true;
    var old_color = document.getElementById('color').value;

    document.getElementById('color').style.backgroundColor = old_color;

    var canvas = document.getElementById('picker');
    var ctx = canvas.getContext('2d');
    var image = new Image();
    image.src = 'tpl/img/colorwheel.png';
    image.onload = function () {
        ctx.drawImage(image, 0, 0, image.width, image.height); // draw the image on the canvas
    }

    canvas.addEventListener('mousemove', function(e) {
        if (bCanPreview) {
            var canvasOffset = getOffset(canvas);
            var canvasX = Math.floor(e.pageX - canvasOffset.left);
            var canvasY = Math.floor(e.pageY - canvasOffset.top);

            var imageData = ctx.getImageData(canvasX, canvasY, 1, 1);
            var pixel = imageData.data;

            var pixelColor = "rgb("+pixel[0]+", "+pixel[1]+", "+pixel[2]+")";
            document.getElementById('color').style.backgroundColor = pixelColor;

            var dColor = pixel[2] + 256 * pixel[1] + 65536 * pixel[0];
            document.getElementById('color').value = '#' + ('0000' + dColor.toString(16)).substr(-6);
        }
    });
    canvas.addEventListener('mouseout', function() {
        document.getElementById('color').style.backgroundColor = old_color;
        document.getElementById('color').value = old_color;
        bCanPreview = false;
    });
    canvas.addEventListener('mouseover', function() {
        bCanPreview = true;
    });
    canvas.addEventListener('click', function() {
        old_color = document.getElementById('color').value;
        bCanPreview = !bCanPreview;
    });
};
