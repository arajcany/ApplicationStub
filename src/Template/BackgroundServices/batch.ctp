<?php
/**
 * @var \App\View\AppView $this
 * @var bool $isNssm
 */
?>

<?php

if (!$isNssm) {
    return;
}

?>

<div class="row mb-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="background-services form large-9 medium-8 columns content">
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
            <?= $this->Form->create(null, $formOpts) ?>
            <fieldset>
                <legend><?= __('Please provide an Windows username and password to install the Background Services under.') ?></legend>
                <?php
                echo $this->Form->control('username', $defaultOptions);
                echo $this->Form->control('password', $defaultOptions);
                ?>
            </fieldset>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary float-left']) ?>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
