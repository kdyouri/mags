<?php
/**
 * @var View $this
 * @var array $numero
 */

$this->Html->script([
    'extras/jquery.min.1.7.js',
    'extras/modernizr.2.5.3.min.js'
], ['inline' => false]);
?>
<h4>
    <?= $numero['Magazine']['nom'] ?> :
    <?= (!$numero['Numero']['hs']) ? 'N°' . $numero['Numero']['numero'] : 'HS' ?>
    (<?= $numero['Numero']['date'] ?>)
</h4>
<div id="canvas">
    <div class="zoom-icon zoom-icon-in"></div>
    <div class="magazine-viewport">
        <div class="container">
            <div class="magazine">
            <?php for ($page = 1; $page <= $numero['Numero']['nbr_pages']; $page++): ?>
                <div style="background-image:url(<?= Router::url(['action' => 'page', $numero['Numero']['id'], $page]) ?>"></div>
            <?php endfor; ?>
                <!-- Next button -->
                <div ignore="1" class="next-button"></div>
                <!-- Previous button -->
                <div ignore="1" class="previous-button"></div>
            </div>
        </div>
<!--        <div class="bottom">-->
<!--            <div id="slider-bar" class="turnjs-slider">-->
<!--                <div id="slider"></div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>
<?php $this->start('footer'); ?>
<script type="text/javascript">
    function loadApp() {
        // Create the flipbook
        $('.magazine').turn({
            width: 922,
            height: 600,
            duration: 1000,
            gradients: true,
            autoCenter: true
        });

        // Using arrow keys to turn the page
        $(document).keydown(function(e){
            var previous = 37, next = 39, esc = 27;

            switch (e.keyCode) {
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
            }
        });
    }

    // Load the HTML4 version if there's not CSS transform
    yepnope({
        test : Modernizr.csstransforms,
        yep: ['/js/lib/turn.min.js'],
        nope: ['/js/lib/turn.html4.min.js'],
        both: ['/css/basic.css'],
        complete: loadApp
    });
</script>
<?php $this->end();