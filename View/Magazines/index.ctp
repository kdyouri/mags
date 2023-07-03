<?php
/**
 * @var View $this
 * @var array $parGenre
 */

$this->Html->css('magazines_index.css', ['inline' => false]);
$this->Html->css('/vendor/imageflow/css/imageflow.min.css', ['inline' => false]);
$this->Html->script('/vendor/imageflow/js/imageflow.min.js', ['inline' => false]);
?>

<?php if (!empty($favories)): ?>
<div id="myImageFlow" class="imageflow" style="height: 200px;">
    <?php foreach ($favories as $numero):
        $image = "https://www.abandonware-magazines.org/images_grandescouvertures/{$numero['Numero']['url_vignette']}";
        $link = Router::url(['controller' => 'numeros', 'action' => 'view', $numero['Numero']['id']]);
        $desc = sprintf('%s : %s, %s', $numero['Magazine']['nom'], ($numero['Numero']['hs'] ? 'HS' : 'N°' . $numero['Numero']['numero']), $numero['Numero']['date']);
        ?>
    <img src="<?= $image ?>" width="250" height="328" alt="<?= $desc ?>" longdesc="<?= $link ?>" />
    <?php endforeach; ?>
</div>
<?php endif; ?>

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
