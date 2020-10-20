<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artifact $artifact
 */
?>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <?php
        $opts = [
            'class' => 'btn btn-primary float-right',
        ];
        echo $this->Html->link(__('Done'), ['action' => 'index'], $opts)
        ?>
    </div>
</div>

<div class="artifacts view large-9 medium-8 columns content">
    <h3><?= h($artifact->name) ?></h3>
    <table class="vertical-table table-bordered">
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
            <th scope="row"><?= __('URL') ?></th>
            <td>
                <?= h($artifact->url) ?>
                <br>
                <?= h($artifact->full_url) ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= __('UNC') ?></th>
            <td>
                <?= h($artifact->unc) ?>
                <br>
                <?= h($artifact->full_unc) ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= __('Artifact Metadata') ?></th>
            <td><?= $artifact->has('artifact_metadata') ? $this->Html->link($artifact->artifact_metadata->id, ['controller' => 'ArtifactMetadata', 'action' => 'view', $artifact->artifact_metadata->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('ID') ?></th>
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
        <tr>
            <th scope="row"><?= __('Preview') ?></th>
            <td><?= $this->Html->image($artifact->full_url) ?></td>
        </tr>
    </table>
</div>
