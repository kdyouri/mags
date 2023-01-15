$(function(){
    check_status();
});

function check_status() {
    let $el = $('.status-indicator.glyphicon-question-sign.text-yellow').first();

    if ($el.length) {
        $el.addClass('blink');

        $.ajax($el.data().target)
            .done(function (response) {
                $el.removeClass('glyphicon-question-sign text-yellow blink');
                if (response.status === true) {
                    $el.addClass('glyphicon-ok-sign text-green');
                } else {
                    $el.addClass('glyphicon-remove-sign text-red');
                }
                check_status();
            });
    }
}