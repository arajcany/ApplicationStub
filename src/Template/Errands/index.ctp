<?php
/**
 * @var AppView $this
 * @var Errand[]|CollectionInterface $errands
 */

use App\Model\Entity\Errand;
use App\View\AppView;
use Cake\Collection\CollectionInterface;

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Errands') ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="errands index large-9 medium-8 columns content">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', 'ID') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('activation') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('expiration') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('domain') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('worker_link') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('worker_name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('class') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('method') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('progress_bar', 'Progress') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('priority') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('return_value') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($errands as $errand): ?>
                    <tr>
                        <td><?= $this->Number->format($errand->id) ?></td>
                        <td><?= h($errand->activation->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= h($errand->expiration->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= h($errand->domain) ?></td>
                        <td><?= h($errand->name) ?></td>
                        <td><?= $this->Number->format($errand->worker_link) ?></td>
                        <td><?= h($errand->worker_name) ?></td>
                        <td><?= h($errand->class) ?></td>
                        <td><?= h($errand->method) ?></td>
                        <td><?= h($errand->status) ?></td>
                        <td><?= $this->Number->format($errand->progress_bar) ?></td>
                        <td><?= $this->Number->format($errand->priority) ?></td>
                        <td><?= $this->Number->format($errand->return_value) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $errand->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first() ?>
                    <?= $this->Paginator->prev() ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next() ?>
                    <?= $this->Paginator->last() ?>
                </ul>
                <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
            </div>
        </div>
    </div>
</div>
