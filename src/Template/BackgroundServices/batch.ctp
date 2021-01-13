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

            $serviceStartTypes = [
                'SERVICE_AUTO_START' => 'Automatic startup',
                'SERVICE_DELAYED_START' => 'Delayed startup',
                'SERVICE_DEMAND_START' => 'Manual startup',
                'SERVICE_DISABLED' => 'Service is disabled'
            ];

            $usernameStartOptions = $defaultOptions;
            $usernameStartOptions['label']['text'] = __("Username the Windows Service will run under (Optional)");

            $passwordStartOptions = $defaultOptions;
            $usernameStartOptions['label']['text'] = __("Password the Windows Service will run under (Optional)");

            $serviceStartOptions = $defaultOptions;
            $serviceStartOptions['options'] = $serviceStartTypes;
            $serviceStartOptions['label']['text'] = __("Services Start Options on Server Start/Reboot");
            ?>
            <?= $this->Form->create(null, $formOpts) ?>
            <fieldset>
                <legend><?= __('Background Services Installation Options.') ?></legend>
                <?php
                echo $this->Form->control('username', $usernameStartOptions);
                echo $this->Form->control('password', $passwordStartOptions);
                echo $this->Form->control('service_start', $serviceStartOptions);
                ?>
            </fieldset>
            <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary float-left']) ?>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
