<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\Query $settings
 * @var \App\Model\Entity\Setting $setting
 * @var \App\Model\Entity\Setting[] $settingsKeyed
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
use Cake\I18n\FrozenTime;

?>

<?php
$labelClass = 'col-8 form-control-label pl-0 mb-1';
$inputClass = 'form-control mb-0';

$defaultOptions = [
    'label' => [
        'class' => $labelClass,
    ],
    'options' => null,
    'class' => $inputClass,
];

$settingsKeyed = [];
foreach ($settings as $setting) {
    $settingsKeyed[$setting->property_key] = $setting;
}

$templates = [
    'inputContainer' => '<div class="input settings {{type}}{{required}}">{{content}} <small id="emailHelp" class="form-text text-muted">{{help}}</small></div>',
];
$this->Form->setTemplates($templates);
?>

<div class="row">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="users">
            <div class="card mb-5">
                <div class="card-header">
                    <legend><?= __('Configure Repository Settings') ?></legend>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($setting) ?>
                    <?= $this->Form->hidden('forceRefererRedirect', ['value' => $this->request->referer(false)]); ?>
                    <fieldset>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_unc']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_url']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                            </div>
                        </div>


                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_mode']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_purge']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_purge_interval']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_purge_limit']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_host']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_port']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_username']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_password']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_timeout']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_sftp_path']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_icon']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_thumbnail']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_preview']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_lr']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_mr']);
                                echo $this->Form->control('property_value', $tmpOptions);
                                ?>
                                <?php
                                $tmpOptions = $defaultOptions;
                                $tmpOptions = $this->Form->settingsFormatOptions($tmpOptions, $settingsKeyed['repo_size_hr']);
                                echo $this->Form->control('property_value', $tmpOptions);
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

