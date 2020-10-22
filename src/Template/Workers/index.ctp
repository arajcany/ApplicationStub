<?php
/**
 * @var AppView $this
 * @var \App\Model\Entity\Worker[]|CollectionInterface $workers
 */

use App\View\AppView;
use Cake\Collection\CollectionInterface;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Workers') ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-5">
        <div class="workers index large-9 medium-8 columns content">
            <div class="card">
                <div class="card-header">
                    <strong>Choose a Task</strong>
                </div>
                <div class="card-body">
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Retire all Workers'),
                            ['action' => 'retire', 'all'],
                            $options
                        )
                        ?>
                        This will shutdown Workers gracefully by raising a retirement flag in the DB for each Worker.
                    </p>
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Clean out Workers'),
                            ['action' => 'clean'],
                            $options
                        )
                        ?>
                        Clean out DB of Workers that are past termination date.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="workers index large-9 medium-8 columns content">
            <h3><?= __('Workers') ?></h3>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', 'ID') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('domain') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('errand_link') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('errand_name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('retirement_date') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('pid', 'PID') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($workers as $worker): ?>
                    <tr>
                        <td><?= $this->Number->format($worker->id) ?></td>
                        <td><?= h($worker->domain) ?></td>
                        <td><?= h($worker->name) ?></td>
                        <td><?= h($worker->type) ?></td>
                        <td><?= $this->Number->format($worker->errand_link) ?></td>
                        <td><?= h($worker->errand_name) ?></td>
                        <td><?= h($worker->retirement_date->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= $this->Number->format($worker->pid) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $worker->id]) ?>
                            <?= $this->Form->postLink(__('Retire'), ['action' => 'retire', $worker->id], ['confirm' => __('Are you sure you want to retire {0}?', $worker->name)]) ?>
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
