<?php
/**
 * @var AppView $this
 * @var Errand[]|CollectionInterface $errands
 * @var int $readyToRun ;
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
        <p><?php
            if ($readyToRun === 0) {
                echo __('There are no Errands waiting to run.', $readyToRun);
            } elseif ($readyToRun === 1) {
                echo __('There is {0} Errand waiting to run.', $readyToRun);
            } elseif ($readyToRun > 1) {
                echo __('There are {0} Errands waiting to run.', $readyToRun);
            }
            ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="errands index large-9 medium-8 columns content">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', 'ID') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('worker_name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('class', 'Class & Method') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('progress_bar', 'Progress') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('priority') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($errands as $errand): ?>
                    <tr>
                        <td><?= $this->Number->format($errand->id) ?></td>
                        <td><?= h($errand->worker_name) ?></td>
                        <td><?= h($errand->class) ?><br><?= h($errand->method) ?></td>
                        <td><?= h($errand->status) ?></td>
                        <td><?= $this->Number->format($errand->progress_bar) ?></td>
                        <td><?= $this->Number->format($errand->priority) ?></td>
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
