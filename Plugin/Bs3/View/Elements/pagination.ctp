<?php if ($this->Paginator->param('pageCount') > 1): ?>
<!-- Pagination: -->
<div class="row">
    <div class="col-md-5 col-sm-12">
        <p style="margin:20px 0">
            <?= $this->Paginator->counter(['format' => __('Page {:page} of {:pages} ({:count} rows)')]) ?>
        </p>
    </div>
    <div class="col-md-7 col-sm-12">
        <nav aria-label="Page navigation">
            <ul class="pagination pull-right">
                <?php
                echo $this->Paginator->prev('«', ['tag' => 'li'], null, ['tag' => 'li', 'disabledTag' => 'a']);
                echo $this->Paginator->numbers(['separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a']);
                echo $this->Paginator->next('»', ['tag' => 'li'], null, ['tag' => 'li', 'disabledTag' => 'a']);
                ?>
            </ul>
        </nav>
    </div>
</div>
<?php endif; ?>
