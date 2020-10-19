<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artifact $artifact
 */
?>
<nav class="large-3 medium-4 columns d-none" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Artifacts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Artifact Metadata'), ['controller' => 'ArtifactMetadata', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Artifact Metadata'), ['controller' => 'ArtifactMetadata', 'action' => 'add']) ?></li>
    </ul>
</nav>

<div class="row mb-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="artifacts form large-9 medium-8 columns content">
            <?php
            $formOpts = [
            ];

            $labelClass = 'form-control-label';
            $inputClass = 'form-control mb-4';
            $checkboxClass = 'mr-2 mb-4';

            $defaultOptions = [
                'label' => [
            'class' => $labelClass,
            ],
            'options' => null,
            'class' => $inputClass,
            ];

            $checkboxOptions = array_merge($defaultOptions, ['class' => $checkboxClass]);
            ?>
            <?= $this->Form->create($artifact) ?>
            <fieldset>
                <legend><?= __('Add Artifact') ?></legend>
                <?php
                            echo $this->Form->control('name', $defaultOptions);
                    echo $this->Form->control('description', $defaultOptions);
                    echo $this->Form->control('size', $defaultOptions);
                    echo $this->Form->control('mime_type', $defaultOptions);
                    echo $this->Form->control('activation', $defaultOptions);
                    echo $this->Form->control('expiration', $defaultOptions);
                    echo $this->Form->control('auto_delete', $defaultOptions);
                    echo $this->Form->control('token', $defaultOptions);
                    echo $this->Form->control('url', $defaultOptions);
                    echo $this->Form->control('unc', $defaultOptions);
                ?>
            </fieldset>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary float-left']) ?>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
