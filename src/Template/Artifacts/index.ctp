<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artifact[]|\Cake\Collection\CollectionInterface $artifacts
 */
?>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <?php
        $opts = [
            'class' => 'btn btn-primary float-right',
        ];
        echo $this->Html->link(__('New Artifact'), ['action' => 'add'], $opts)
        ?>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="artifacts index large-9 medium-8 columns content">
            <h3><?= __('Artifacts') ?></h3>
            <div class="row">
                <div class="col-12 ml-auto mr-auto">
                    Artifacts are stored in the Repository.
                    <?php
                    $q = $this->request->getQuery();
                    $opts = [
                        'class' => '',
                    ];
                    echo $this->Html->link(__('Edit Settings'), ['controller' => 'settings', 'action' => 'edit-group', 'repository', '?' => $q], $opts)
                    ?>.
                </div>
            </div>

            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('size') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('mime_type') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('activation') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('expiration') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('auto_delete') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('token') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($artifacts as $artifact): ?>
                    <tr>
                        <td><?= $this->Number->format($artifact->id) ?></td>
                        <td><?= h($artifact->name) ?></td>
                        <td><?= $this->Number->format($artifact->size) ?></td>
                        <td><?= h($artifact->mime_type) ?></td>
                        <td><?= h($artifact->activation->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= h($artifact->expiration->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
                        <td><?= h($artifact->auto_delete) ?></td>
                        <td><?= h($artifact->token) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $artifact->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $artifact->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artifact->id)]) ?>
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
