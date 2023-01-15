<?php
/**
 * @var View $this
 * @var array $numeros
 */

$this->Html->css('numeros_index.css', ['inline' => false]);
$this->Html->script('numeros_index.js', ['inline' => false]);
?>

<div class="row">
<?php foreach ($numeros as $numero): ?>
    <div class="col-xs-6 col-md-3">
        <a<?php if (!$numero['Numero']['cd'] && $numero['Numero']['album']) echo ' href="', Router::url(['action' => 'view', $numero['Numero']['id']]), '"'; ?> class="thumbnail<?php if ($numero['Numero']['visite']) echo ' visited'; ?>">
            <div class="status" style="margin-bottom: 10px">
                &nbsp;
                <span class="glyphicon glyphicon-<?= !isset($numero['Numero']['statut']) ? 'question-sign text-yellow' : ($numero['Numero']['statut'] == 1 ? 'ok-sign text-green' : 'remove-sign text-red') ?> status-indicator" data-target="<?= Router::url(['action' => 'check', $numero['Numero']['id']]) ?>"></span>
                <span class="glyphicon glyphicon-<?= $numero['Numero']['visite'] ? 'folder-open text-yellow' : 'folder-close text-muted' ?>"></span>
            </div>
            <?= $this->Html->image("//www.abandonware-magazines.org/images_petitescouvertures/{$numero['Numero']['url_vignette']}", ['class' => 'vignette', 'style' => 'width:190px;height:230px']); ?>
            <div class="caption">
                <p<?php if (!$numero['Numero']['album']) echo ' class="text-muted"'; ?>>
                    <?= (!$numero['Numero']['hs']) ? 'N°' . $numero['Numero']['numero'] : 'HS' ?>,
                    <?= $numero['Numero']['date'] ?>
                    <br>
                    <?= $numero['Magazine']['nom'] ?>
                </p>
            </div>
        </a>
    </div>
<?php endforeach; ?>
</div>

<?= $this->element('Bs3.pagination') ?>
