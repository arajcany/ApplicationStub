<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting[]|\Cake\Collection\CollectionInterface $settings
 */
?>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="settings index large-9 medium-8 columns content">
            <h3><?= __('Settings') ?></h3>

            <div class="row">
                <div class="col-12 ml-auto mr-auto">
                    Edit by Group:
                    <?php
                    $q = $this->request->getQuery();
                    $opts = [
                        'class' => '',
                    ];
                    echo $this->Html->link(__('Repository'), ['action' => 'edit-group', 'repository', '?' => $q], $opts)
                    ?>
                </div>
            </div>

            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', "ID") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('description') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('property_group', "Group") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('property_key', "Key") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('property_value', "Value") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('is_masked') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($settings as $setting): ?>
                    <tr>
                        <td><?= $this->Number->format($setting->id) ?></td>
                        <td><?= h($setting->name) ?></td>
                        <td><?= h($setting->description) ?></td>
                        <td><?= h($setting->property_group) ?></td>
                        <td><?= h($setting->property_key) ?></td>
                        <td><?= h(Cake\Utility\Text::truncate($setting->property_value, 30)) ?></td>
                        <td><?= h($setting->is_masked) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $setting->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
                <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
            </div>
        </div>
    </div>
</div>
