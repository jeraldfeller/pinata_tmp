$(document).ready(function() {
    var firstError = $('form .control-group.error:visible:first');

    if (firstError && 1 === firstError.size()) {
        $('body').animate({
            'scrollTop': (firstError.offset().top - 50)
        });
    }
});