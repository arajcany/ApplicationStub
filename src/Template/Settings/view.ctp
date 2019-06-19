<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Setting'), ['action' => 'edit', $setting->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Setting'), ['action' => 'delete', $setting->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $setting->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Settings'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Setting'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="settings view large-9 medium-8 columns content">
    <h3><?= h($setting->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($setting->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Description') ?></th>
            <td><?= h($setting->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Property Group') ?></th>
            <td><?= h($setting->property_group) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Property Key') ?></th>
            <td><?= h($setting->property_key) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Property Value') ?></th>
            <td><?= h($setting->property_value) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Html Select Type') ?></th>
            <td><?= h($setting->html_select_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Match Pattern') ?></th>
            <td><?= h($setting->match_pattern) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($setting->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($setting->created->i18nFormat(DTF, TZ)) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($setting->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Masked') ?></th>
            <td><?= $setting->is_masked ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Selections') ?></h4>
        <?= $this->Text->autoParagraph(h($setting->selections)); ?>
    </div>
</div>
