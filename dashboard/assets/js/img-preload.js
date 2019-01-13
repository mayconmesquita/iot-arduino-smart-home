function preload(images) {
    if (document.images) {
        var i = 0;
        var imageArray = new Array();
        imageArray = images.split(',');
        var imageObj = new Image();
        for(i=0; i<=imageArray.length-1; i++) {
            document.write('<img style="display:none" src="' + imageArray[i] + '" />'); // Write to page (uncomment to check images)
            imageObj.src=imageArray[i];
        }
    }
}

preload('assets/images/ground-on.jpg,assets/images/ground-off.jpg,assets/images/port.png,assets/images/lamp.png');