jQuery(document).ready(function($){
    $('#color-picker').iris({
        width: 400,
        hide: true,
        change: function(event, ui) {
            // event = standard jQuery event, produced by whichever control was changed.
            // ui = standard jQuery UI object, with a color member containing a Color.js object
// change the headline color
            $("#color-picker").css( 'background', ui.color.toString());
        }

    });

    $(this).click(function() {
        $(this).iris('toggle'); //click came from somewhere else
    });
});
