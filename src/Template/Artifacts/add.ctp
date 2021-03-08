<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Artifact $artifact
 */
?>

<div class="row mb-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="artifacts form large-9 medium-8 columns content">
            <?php
            $formOpts = [
                'type' => 'file'
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

            $fileOptions = [
                'label' => false,
                'class' => 'mb-3',
                'type' => 'file'
            ];

            $checkboxOptions = array_merge($defaultOptions, ['class' => $checkboxClass]);
            ?>
            <?= $this->Form->create($artifact, $formOpts) ?>
            <fieldset>
                <legend><?= __('Add Artifact') ?></legend>
                <p>There is an upload limit of <?= ini_get('upload_max_filesize') ?></p>
                <?php
                echo $this->Form->control('file', $fileOptions);
                echo $this->Form->control('description', $defaultOptions);
                //echo $this->Form->control('activation', $defaultOptions);
                //echo $this->Form->control('expiration', $defaultOptions);
                //echo $this->Form->control('auto_delete', $defaultOptions);
                ?>
            </fieldset>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary float-left']) ?>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
