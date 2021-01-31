<?php
/**
 * @var AppView $this
 * @var \App\Model\Entity\Worker[]|CollectionInterface $workers
 * @var array $services
 * @var Query|Heartbeat[] $heartbeats
 * @var HeartbeatsTable $HeartbeatsTable
 */

use App\Model\Entity\Heartbeat;
use App\Model\Table\HeartbeatsTable;
use App\View\AppView;
use Cake\Collection\CollectionInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

$HeartbeatsTable = TableRegistry::getTableLocator()->get('Heartbeats');
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
                    <strong><?= __('Important Information') ?></strong>
                </div>
                <div class="card-body">
                    <?php
                    $options = [
                        'class' => ""
                    ];
                    $link = $this->Html->link(
                        __('Background Services'),
                        ['controller' => 'background-services',],
                        $options
                    )
                    ?>
                    <p>
                        Workers act as the 'middle-men' to <?= $link ?>.
                        Workers report on the status of Background Services and can be used to gracefully recycle or
                        shutdown Background Services.
                    </p>
                </div>
            </div>
        </div>
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
                            __('Recycle'),
                            ['action' => 'retire', 'all'],
                            $options
                        )
                        ?>
                        Workers will gracefully stop/restart Background Services after the current Errand they are
                        running has completed.
                    </p>
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Shutdown'),
                            ['action' => 'stop', 'all'],
                            $options
                        )
                        ?>
                        Workers will gracefully stop Background Services after the current Errand they are
                        running has completed.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="workers index large-9 medium-8 columns content">
            <h3><?= __('Active Workers') ?></h3>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', 'ID') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('server') ?></th>
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
                        <td><?= h($worker->server) ?></td>
                        <td><?= h($worker->domain) ?></td>
                        <td><?= h($worker->name) ?></td>
                        <td><?= h($worker->type) ?></td>
                        <td><?= $this->Number->format($worker->errand_link) ?></td>
                        <td><?= h($worker->errand_name) ?></td>
                        <td><?= h($worker->retirement_date->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= $this->Number->format($worker->pid) ?></td>
                        <td class="actions">
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
