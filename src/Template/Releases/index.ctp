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
 * @var string $remote_update_unc
 * @var string $remote_update_url
 * @var string $remote_update_sftp_host
 * @var string $remote_update_sftp_port
 * @var string $remote_update_sftp_username
 * @var string $remote_update_sftp_password
 * @var string $remote_update_sftp_timeout
 * @var string $remote_update_sftp_path
 * @var bool $isUrl
 * @var bool $isSFTP
 * @var bool $isUNC
 * @var array $remoteUpdateDebug
 *
 * @var string $onlineVersionHistoryHash
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
                    <pre><?php
                        echo __("Please run the following CMD in Terminal to build a release...");
                        ?></pre>

                    <code><?php
                        echo __("{0}\bin\\BuildRelease.bat", ROOT);
                        ?></code>

                    <br>
                    <br>

                    <p>
                        <?php
                        if ($isUrl) {
                            echo __("Connection to URL <strong>{0}</strong> established.", $remote_update_url);
                        } else {
                            echo __("Could not connect to URL <strong>{0}</strong>.", $remote_update_url);
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if ($isSFTP) {
                            echo __("Round trip connection to SFTP <strong>{0}@{1}:{2}</strong> established.", $remote_update_sftp_username, $remote_update_sftp_host, $remote_update_sftp_port);
                        } else {
                            echo __("Could not connect to SFTP <strong>{0}@{1}:{2}</strong>.", $remote_update_sftp_username, $remote_update_sftp_host, $remote_update_sftp_port);
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if ($isUNC) {
                            echo __("Round trip connection to UNC path <strong>{0}</strong> established.", $remote_update_unc);
                        } else {
                            echo __("Could not connect to UNC path <strong>{0}</strong>.", $remote_update_unc);
                        }
                        ?>
                    </p>

                    <p>
                        <?php
                        if ($isSFTP || $isUNC) {
                            echo __("Releases will be automatically uploaded to the remote update site via SFTP or UNC.");
                            $text = __('Change Remote Update Settings');
                        } else {
                            echo __("Please configure if you would like to automatically upload releases to the remote update site via SFTP or UNC.");
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

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="releases index">
            <div class="card">
                <div class="card-header">
                    <strong>OnlineVersionHistoryHash Debug</strong>
                    <?php echo $this->Html->link('Republish', ['action' => 'republish_version_history_hash']) ?>
                </div>
                <div class="card-body">
                    <pre><?php
                        print_r($onlineVersionHistoryHash);
                        ?></pre>
                    <pre><?php
                        $decrypted = \arajcany\ToolBox\Utility\Security\Security::decrypt64Url($onlineVersionHistoryHash);
                        print_r($decrypted);
                        ?></pre>
                    <pre><?php
                        $arrayed = json_decode($decrypted, JSON_OBJECT_AS_ARRAY);
                        print_r($arrayed);
                        ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>
