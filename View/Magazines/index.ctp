<?php
/**
 * @var View $this
 * @var array $parGenre
 */
?>

<?php foreach ($parGenre as $genre => $magazines): ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= $genre ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
        <?php foreach ($magazines as $magazine): ?>
            <div class="col-md-2">
                <a href="<?= Router::url("/numeros/index/{$magazine['Magazine']['id']}") ?>" class="thumbnail">
                    <div class="clearfix" style="margin-bottom: 10px">
                        <span class="label label-danger pull-right"><?= $magazine[0]['count'] ?></span>
                    </div>
                    <?= $this->Html->image("//www.abandonware-magazines.org/images_logomags/{$magazine['Magazine']['logo']}", ['style' => 'width:auto;height:32px']); ?>
                    <div class="caption">
                        <div class="nowrap"><?= $magazine['Magazine']['nom'] ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
