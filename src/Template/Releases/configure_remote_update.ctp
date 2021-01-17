<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 *
 * @var string $remote_update_unc
 * @var string $remote_update_sftp_host
 * @var string $remote_update_sftp_port
 * @var string $remote_update_sftp_username
 * @var string $remote_update_sftp_password
 * @var string $remote_update_sftp_timeout
 * @var string $remote_update_sftp_path
 */

use Cake\Core\Configure\Engine\PhpConfig;

?>

<?php
$labelClass = 'col-8 form-control-label pl-0 mb-1';
$inputClass = 'form-control mb-3';

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
                    <legend><?= __('Configure Remote Update Settings') ?></legend>
                </div>
                <div class="card-body">
                    <?php

                    ?>
                    <?= $this->Form->create($setting) ?>
                    <fieldset>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $remoteUpdateUrlOptions = $defaultOptions;
                                $remoteUpdateUrlOptions['label']['text'] = 'Remote Update URL';
                                echo $this->Form->control('property_value', $remoteUpdateUrlOptions);
                                ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update UNC';
                                $options['value'] = $remote_update_unc;
                                echo $this->Form->control('remote_update_unc', $options);
                                ?>
                            </div>
                        </div>

                        <div class="card mb-0">
                            <div class="card-body">
                                <?php
                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Host';
                                $options['value'] = $remote_update_sftp_host;
                                echo $this->Form->control('remote_update_sftp_host', $options);

                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Port';
                                $options['value'] = $remote_update_sftp_port;
                                echo $this->Form->control('remote_update_sftp_port', $options);

                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Username';
                                $options['value'] = $remote_update_sftp_username;
                                echo $this->Form->control('remote_update_sftp_username', $options);

                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Password';
                                $options['type'] = 'password';
                                $options['value'] = $remote_update_sftp_password;
                                echo $this->Form->control('remote_update_sftp_password', $options);

                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Timeout';
                                $options['options'] = ['' => '--Select--', 1 => '1 Second', 2 => '2 Seconds', 3 => '3 Seconds', 4 => '4 Seconds'];
                                $options['value'] = $remote_update_sftp_timeout;
                                echo $this->Form->control('remote_update_sftp_timeout', $options);

                                $options = $defaultOptions;
                                $options['label']['text'] = 'Remote Update sFTP Path';
                                $options['value'] = $remote_update_sftp_path;
                                echo $this->Form->control('remote_update_sftp_path', $options);
                                ?>
                            </div>
                        </div>


                    </fieldset>
                </div>
                <div class="card-footer">
                    <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary float-left']) ?>
                    <?= $this->Form->button(__('Update'), ['class' => 'btn btn-primary float-right']) ?>
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

