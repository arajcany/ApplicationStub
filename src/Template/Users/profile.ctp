<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

use Cake\Core\Configure\Engine\PhpConfig;

?>

<?php
$labelClass = 'col-md-3 form-control-label';
$inputClass = 'form-control';

$defaultOptions = [
    'label' => [
        'class' => $labelClass,
    ],
    'options' => null,
    'class' => $inputClass,
];
?>

<div class="row">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="users">
            <div class="card">
                <div class="card-header">
                    <legend><?= __('Update My Profile') ?></legend>
                </div>
                <div class="card-body">
                    <?php

                    ?>
                    <?= $this->Form->create($user) ?>
                    <fieldset>
                        <?php
                        echo $this->Form->control('email', $defaultOptions);
                        echo $this->Form->control('username', $defaultOptions);
                        echo $this->Form->control('password', $defaultOptions);
                        echo $this->Form->control('first_name', $defaultOptions);
                        echo $this->Form->control('last_name', $defaultOptions);
                        echo $this->Form->control('address_1', $defaultOptions);
                        echo $this->Form->control('address_2', $defaultOptions);
                        echo $this->Form->control('suburb', $defaultOptions);
                        echo $this->Form->control('state', $defaultOptions);
                        echo $this->Form->control('post_code', $defaultOptions);
                        echo $this->Form->control('country', $defaultOptions);
                        echo $this->Form->control('mobile', $defaultOptions);
                        echo $this->Form->control('phone', $defaultOptions);
                        ?>
                    </fieldset>
                </div>
                <div class="card-footer">
                    <?= $this->Form->button(__('Update'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
//restore the original templates
$this->Form->resetTemplates();
?>

