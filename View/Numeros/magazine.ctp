<?php
/**
 * @var View $this
 * @var array $numero
 */

$this->Html->script([
    'extras/jquery.min.1.7.js',
    'extras/jquery-ui-1.8.20.custom.min.js',
    'extras/modernizr.2.5.3.min.js'
], ['inline' => false]);
?>
<h4>
    <?= $numero['Magazine']['nom'] ?> :
    <?= (!$numero['Numero']['hs']) ? 'N°' . $numero['Numero']['numero'] : 'HS' ?>
    (<?= $numero['Numero']['date'] ?>)
    <div class="progress"><div class="progress-bar" id="progressbar"></div></div>
</h4>
<div id="canvas">
    <div class="zoom-icon zoom-icon-in"></div>

    <div class="magazine-viewport">
        <div class="container">
            <div class="magazine">
                <!-- Next button -->
                <div ignore="1" class="next-button"></div>
                <!-- Previous button -->
                <div ignore="1" class="previous-button"></div>
            </div>
        </div>
        <div class="bottom">
            <div id="slider-bar" class="turnjs-slider">
                <div id="slider"></div>
            </div>
        </div>
    </div>
</div>

<?php $this->start('footer'); ?>
<script type="text/javascript">
    var id = <?= $numero['Numero']['id'] ?>;
    var pageCount = <?= $numero['Numero']['nbr_pages'] ?>;

    // Load all images :
    var loadPageImage = function(page) {
        var img = $('<img />');

        img.load(function(){
            if (page < pageCount) loadPageImage(++page);
        });
        img.attr('src', '/numeros/page/' + id + '/' + page);

        if (page === pageCount) {
            $('#progressbar').parent().hide();
        } else {
            var percent = 100 * page / pageCount;
            var p = percent.toFixed() + '%';

            $('#progressbar').css('width', p).text( page + '/' + pageCount + ' (' + p + ')');
        }
    }
    loadPageImage(1);

    function loadApp() {
        $('#canvas').fadeIn(1000);

        var flipbook = $('.magazine');

        // Check if the CSS was already loaded

        if (flipbook.width()==0 || flipbook.height()==0) {
            setTimeout(loadApp, 10);
            return;
        }

        // Create the flipbook
        flipbook.turn({
            // width: 500,
            // height: 326,
            width: 922,
            height: 600,
            duration: 1000,
            gradients: true,
            autoCenter: true,
            //elevation: 50,
            pages: <?= $numero['Numero']['nbr_pages'] ?>,
            when: {
                turning: function(event, page, view) {
                    var book = $(this),
                        currentPage = book.turn('page'),
                        pages = book.turn('pages');

                    // Show and hide navigation buttons
                    disableControls(page);
                },
                turned: function(event, page, view) {
                    disableControls(page);

                    $(this).turn('center');

                    $('#slider').slider('value', getViewNumber($(this), page));

                    if (page==1) {
                        $(this).turn('peel', 'br');
                    }
                },
                missing: function (event, pages) {
                    // Add pages that aren't in the magazine
                    for (var i = 0; i < pages.length; i++)
                        addPage(pages[i], $(this));
                }
            }
        });

        // Zoom.js
        $('.magazine-viewport').zoom({
            flipbook: $('.magazine'),
            max: function() {
                return largeMagazineWidth()/$('.magazine').width();
            },
            when: {
                swipeLeft: function() {
                    $(this).zoom('flipbook').turn('next');
                },
                swipeRight: function() {
                    $(this).zoom('flipbook').turn('previous');
                },
                resize: function(event, scale, page, pageElement) {
                    if (scale==1)
                        loadSmallPage(page, pageElement);
                    else
                        loadLargePage(page, pageElement);
                },
                zoomIn: function () {
                    $('#slider-bar').hide();
                    $('.made').hide();
                    $('.magazine').removeClass('animated').addClass('zoom-in');
                    $('.zoom-icon').removeClass('zoom-icon-in').addClass('zoom-icon-out');

                    if (!window.escTip && !$.isTouch) {
                        escTip = true;

                        $('<div />', {'class': 'exit-message'}).
                        html('<div>Press ESC to exit</div>').
                        appendTo($('body')).
                        delay(2000).
                        animate({opacity:0}, 500, function() {
                            $(this).remove();
                        });
                    }
                },
                zoomOut: function () {
                    $('#slider-bar').fadeIn();
                    $('.exit-message').hide();
                    $('.made').fadeIn();
                    $('.zoom-icon').removeClass('zoom-icon-out').addClass('zoom-icon-in');

                    setTimeout(function(){
                        $('.magazine').addClass('animated').removeClass('zoom-in');
                        resizeViewport();
                    }, 0);
                }
            }
        });

        // Zoom event
        if ($.isTouch)
            $('.magazine-viewport').bind('zoom.doubleTap', zoomTo);
        else
            $('.magazine-viewport').bind('zoom.tap', zoomTo);


        // Using arrow keys to turn the page
        $(document).keydown(function(e){
            var previous = 37, next = 39, esc = 27, start = 36, end = 35, enter = 13, fastbackward = 33, fastforward = 34;

            switch (e.keyCode) {
                case start:
                    $('.magazine').turn('page', 1);
                    e.preventDefault();
                    break;
                case end:
                    $('.magazine').turn('page', pageCount);
                    e.preventDefault();
                    break;
                case previous:
                    // left arrow
                    $('.magazine').turn('previous');
                    e.preventDefault();
                    break;
                case next:
                    //right arrow
                    $('.magazine').turn('next');
                    e.preventDefault();
                    break;
                case esc:
                    $('.magazine-viewport').zoom('zoomOut');
                    e.preventDefault();
                    break;
                case enter:
                    $('.magazine-viewport').zoom('zoomIn');
                    e.preventDefault();
                    break;
                case fastforward:
                    // page down
                    var page = $('.magazine').turn('page');
                    page += 10;
                    if (page > pageCount) page = pageCount;
                    $('.magazine').turn('page', page);
                    e.preventDefault();
                    break;
                case fastbackward:
                    // page up
                    var page = $('.magazine').turn('page');
                    page -= 10;
                    if (page < 1) page = 1;
                    $('.magazine').turn('page', page);
                    e.preventDefault();
            }
        });

        $(window).resize(function() {
            resizeViewport();
        }).bind('orientationchange', function() {
            resizeViewport();
        });

        // Events for the next button
        $('.next-button').bind($.mouseEvents.over, function() {
            $(this).addClass('next-button-hover');

        }).bind($.mouseEvents.out, function() {
            $(this).removeClass('next-button-hover');

        }).bind($.mouseEvents.down, function() {
            $(this).addClass('next-button-down');

        }).bind($.mouseEvents.up, function() {
            $(this).removeClass('next-button-down');

        }).click(function() {
            $('.magazine').turn('next');
        });

        // Events for the next button
        $('.previous-button').bind($.mouseEvents.over, function() {
            $(this).addClass('previous-button-hover');

        }).bind($.mouseEvents.out, function() {
            $(this).removeClass('previous-button-hover');

        }).bind($.mouseEvents.down, function() {
            $(this).addClass('previous-button-down');

        }).bind($.mouseEvents.up, function() {
            $(this).removeClass('previous-button-down');

        }).click(function() {
            $('.magazine').turn('previous');
        });

        // Slider
        $("#slider").slider({
            min: 1,
            max: numberOfViews(flipbook),
            start: function(event, ui) {

                /*if (!window._thumbPreview) {
                    _thumbPreview = $('<div />', {'class': 'thumbnail'}).html('<div></div>');
                    setPreview(ui.value);
                    _thumbPreview.appendTo($(ui.handle));
                } else
                    setPreview(ui.value);*/

                moveBar(false);

            },
            slide: function(event, ui) {

                //setPreview(ui.value);

            },
            stop: function() {

                if (window._thumbPreview)
                    _thumbPreview.removeClass('show');

                $('.magazine').turn('page', Math.max(1, $(this).slider('value')*2 - 2));

            }
        });

        resizeViewport();

        $('.magazine').addClass('animated');
    }

    // Zoom icon
    $('.zoom-icon').bind('mouseover', function() {

        if ($(this).hasClass('zoom-icon-in'))
            $(this).addClass('zoom-icon-in-hover');

        if ($(this).hasClass('zoom-icon-out'))
            $(this).addClass('zoom-icon-out-hover');

    }).bind('mouseout', function() {

        if ($(this).hasClass('zoom-icon-in'))
            $(this).removeClass('zoom-icon-in-hover');

        if ($(this).hasClass('zoom-icon-out'))
            $(this).removeClass('zoom-icon-out-hover');

    }).bind('click', function() {

        if ($(this).hasClass('zoom-icon-in'))
            $('.magazine-viewport').zoom('zoomIn');
        else if ($(this).hasClass('zoom-icon-out'))
            $('.magazine-viewport').zoom('zoomOut');

    });

    $('#canvas').hide();

    // Load the HTML4 version if there's not CSS transform
    yepnope({
        test : Modernizr.csstransforms,
        yep: ['/js/lib/turn.min.js'],
        nope: ['/js/lib/turn.html4.min.js', '/css/jquery.ui.html4.css'],
        both: ['/js/lib/zoom.min.js', '/css/jquery.ui.css', '/js/magazine.js', '/css/magazine.css'],
        complete: loadApp
    });
</script>
<?php $this->end();