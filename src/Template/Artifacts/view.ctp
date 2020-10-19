<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artifact $artifact
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Artifact'), ['action' => 'edit', $artifact->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Artifact'), ['action' => 'delete', $artifact->id], ['confirm' => __('Are you sure you want to delete # {0}?', $artifact->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Artifacts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artifact'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Artifact Metadata'), ['controller' => 'ArtifactMetadata', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Artifact Metadata'), ['controller' => 'ArtifactMetadata', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="artifacts view large-9 medium-8 columns content">
    <h3><?= h($artifact->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($artifact->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Description') ?></th>
            <td><?= h($artifact->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Mime Type') ?></th>
            <td><?= h($artifact->mime_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Token') ?></th>
            <td><?= h($artifact->token) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Url') ?></th>
            <td><?= h($artifact->url) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Unc') ?></th>
            <td><?= h($artifact->unc) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Artifact Metadata') ?></th>
            <td><?= $artifact->has('artifact_metadata') ? $this->Html->link($artifact->artifact_metadata->id, ['controller' => 'ArtifactMetadata', 'action' => 'view', $artifact->artifact_metadata->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($artifact->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Size') ?></th>
            <td><?= $this->Number->format($artifact->size) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($artifact->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($artifact->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Activation') ?></th>
            <td><?= h($artifact->activation) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expiration') ?></th>
            <td><?= h($artifact->expiration) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Auto Delete') ?></th>
            <td><?= $artifact->auto_delete ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
