<?php
/**
 * @var \App\View\AppView $this
 * @var $currentControllerName
 * @var $currentActionName
 * @var $currentModelName
 * @var array $versionHistoryIni
 * @var array $versionIni
 * @var string $gitBranch
 * @var array $gitCommits
 * @var array $gitModified
 *
 * @var string $remote_update_url
 * @var string $remote_update_sftp_host
 * @var string $remote_update_sftp_port
 * @var string $remote_update_sftp_username
 * @var string $remote_update_sftp_password
 * @var string $remote_update_sftp_timeout
 * @var string $remote_update_sftp_path
 * @var bool $isSFTP
 *
 */

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Releases') ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-5">
        <div class="workers index large-9 medium-8 columns content">
            <div class="card">
                <div class="card-header">
                    <strong>Remote Update Information</strong>
                </div>
                <div class="card-body">
                    <p>
                        <?php
                        if ($isSFTP) {
                            echo __("Connection to SFTP <strong>{0}@{1}:{2}</strong> established.", $remote_update_sftp_username, $remote_update_sftp_host, $remote_update_sftp_port);
                        } else {
                            echo __("Could not connect to SFTP <strong>{0}@{1}:{2}</strong>.", $remote_update_sftp_username, $remote_update_sftp_host, $remote_update_sftp_port);
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if ($isSFTP) {
                            echo __("Connection to URL <strong>{0}</strong> established.", $remote_update_url);
                        } else {
                            echo __("Could not connect to URL <strong>{0}</strong>.", $remote_update_url);
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if ($isSFTP) {
                            echo __("Releases will be automatically uploaded to the SFTP site.");
                            $text = __('Change Remote Update Settings');
                        } else {
                            echo __("Please configure if you would like to automatically upload releases to the SFTP site.");
                            $text = __('Configure Remote Update Settings');
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            $text,
                            ['action' => 'configure-remote-update',],
                            $options
                        )
                        ?>
                    </p>

                    <br>
                    <br>

                    <pre><?php
                        echo __("Please run the following CMD in Terminal to build a release...");
                        ?></pre>

                    <code><?php
                        echo __("{0}\bin\\BuildRelease.bat", ROOT);
                        ?></code>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="releases index">
            <div class="card">
                <div class="card-header">
                    <strong>Git Information</strong>
                </div>
                <div class="card-body">
                    <pre><?php
                        echo __("You are currently on the ''{0}'' branch.", $gitBranch);
                        ?></pre>
                    <?php if (count($gitModified) > 1) { ?>
                        <pre><?php
                            echo __("The following files have been modified.");
                            ?></pre>

                        <pre><?php
                            foreach ($gitModified as $item) {
                                echo __(" - {0}{1}", $item, LS);
                            }
                            ?></pre>
                    <?php } else { ?>
                        <pre><?php
                            echo __("No files have been modified.");
                            ?></pre>
                    <?php } ?>

                    <br>

                    <?php if (count($gitCommits) > 1) { ?>
                        <pre><?php
                            echo __("The following has been committed.");
                            ?></pre>

                        <pre><?php
                            foreach ($gitCommits as $k => $item) {
                                echo __(" - {0}: {1}{2}", $k, $item, LS);
                            }
                            ?></pre>
                    <?php } else { ?>
                        <pre><?php
                            echo __("No new commits.");
                            ?></pre>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
