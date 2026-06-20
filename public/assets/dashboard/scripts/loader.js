Pace.on('hide', function() {
    $("#m-b-b").fadeOut().removeClass("show");
});

Pace.on('start', function() {
    $("#m-b-b").fadeIn().addClass("show");
});