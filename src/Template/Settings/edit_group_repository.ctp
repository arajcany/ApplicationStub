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
 *
 * @var string $repo_unc
 * @var string $repo_url
 * @var string $repo_sftp_host
 * @var string $repo_sftp_port
 * @var string $repo_sftp_username
 * @var string $repo_sftp_password
 * @var string $repo_sftp_timeout
 * @var string $repo_sftp_path
 * @var bool $isUrl
 * @var bool $isSFTP
 * @var bool $isUNC
 * @var array $remoteUpdateDebug
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

<div class="row mb-5">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="card">
            <div class="card-header">
                <legend><?= __('Connection Test Results') ?></legend>
            </div>
            <div class="card-body">

                <p>
                    <?php
                    if ($isUrl) {
                        echo __("Connection to URL <strong>{0}</strong> established.", $repo_url);
                    } else {
                        echo __("Could not connect to URL <strong>{0}</strong>.", $repo_url);
                    }
                    ?>
                </p>

                <p>
                    <?php
                    if ($isSFTP) {
                        echo __("Round trip connection to SFTP <strong>{0}@{1}:{2}</strong> established.", $repo_sftp_username, $repo_sftp_host, $repo_sftp_port);
                    } else {
                        echo __("Could not connect to SFTP <strong>{0}@{1}:{2}</strong>.", $repo_sftp_username, $repo_sftp_host, $repo_sftp_port);
                    }
                    ?>
                </p>

                <p>
                    <?php
                    if ($isUNC) {
                        echo __("Round trip connection to UNC path <strong>{0}</strong> established.", $repo_unc);
                    } else {
                        echo __("Could not connect to UNC path <strong>{0}</strong>.", $repo_unc);
                    }
                    ?>
                </p>

                <?php
                if (!$isSFTP || !$isUNC) {
                    ?>
                    <div class="card pb-0">
                        <div class="card-body">
                            <?php
                            echo __("Debugging information...");
                            foreach ($remoteUpdateDebug as $item) {
                                $colour = str_replace(['error'], ['danger'], $item['element']);
                                echo __('<div class="alert alert-' . $colour . ' mt-3 mb-0">');
                                echo __($item['message']);
                                echo __('</div>');
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="card">
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
                <?= $this->Html->link(__('Cancel'), $this->request->referer(false), ['class' => 'btn btn-secondary float-left']) ?>
                <?= $this->Form->button(__('Update'), ['class' => 'btn btn-primary float-right']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php
//restore the original templates
$this->Form->resetTemplates();
?>

