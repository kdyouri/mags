<?php
/**
 * @var View $this
 * @var array $numeros
 */

$this->Html->css('numeros_index.css', ['inline' => false]);
$this->Html->script('numeros_index.js', ['inline' => false]);
$this->Html->script('lib/xcrud.js', ['inline' => false]);
?>

<div class="xcrud-main">
    <div class="row">
    <?php foreach ($numeros as $numero): ?>
        <div class="col-xs-6 col-md-3">
            <div class="thumbnail">
                <div class="status clearfix" style="margin-bottom: 10px">
                    <div class="pull-left">
                        <span class="glyphicon glyphicon-<?= !isset($numero['Numero']['statut']) ? 'question-sign text-yellow' : ($numero['Numero']['statut'] == 1 ? 'ok-sign text-green' : 'remove-sign text-red') ?> status-indicator" data-target="<?= Router::url(['action' => 'check', $numero['Numero']['id']]) ?>"></span>
                        <span class="glyphicon glyphicon-<?= $numero['Numero']['visite'] ? 'folder-open text-yellow' : 'folder-close text-muted' ?>"></span>
                    </div>
                    <div class="pull-right">
                        <a href="<?= Router::url(['action' => 'fav_toggle', $numero['Numero']['id']]) ?>" class="xcrud-btn-action">
                            <span class="glyphicon glyphicon-heart text-<?= $numero['Numero']['favorie'] ? 'red' : 'muted' ?>"></span>
                        </a>
                    </div>
                </div>
                <a<?php if (!$numero['Numero']['cd'] && $numero['Numero']['album']) echo ' href="', Router::url(['action' => 'view', $numero['Numero']['id']]), '"'; ?> class="<?php if ($numero['Numero']['visite']) echo ' visited'; ?>">
                <?= $this->Html->image("//www.abandonware-magazines.org/images_petitescouvertures/{$numero['Numero']['url_vignette']}", ['class' => 'vignette', 'style' => 'width:190px;height:230px']); ?>
                </a>
                <div class="caption">
                    <p<?php if (!$numero['Numero']['album']) echo ' class="text-muted"'; ?>>
                        <?= (!$numero['Numero']['hs']) ? 'N°' . $numero['Numero']['numero'] : 'HS' ?>,
                        <?= $numero['Numero']['date'] ?>
                        <br>
                        <?= $numero['Magazine']['nom'] ?>
                    </p>
                </div>
                <?php if ($numero['Numero']['lu']): ?>
                <div class="progress" style="height: 5px; margin: -5px 0 0 0;">
                    <div class="progress-bar progress-bar-danger" style="width: <?=$numero['Numero']['lu'] ?>%;"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?= $this->element('Bs3.pagination') ?>
</div>

