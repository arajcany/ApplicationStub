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
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Releases') ?>
        </h1>
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
                        echo __("Please run the following CMD in Terminal to build a release...");
                        ?></pre>

                    <code><?php
                        echo __("{0}>bin\\BuildRelease.bat", ROOT);
                        ?></code>

                    <br>
                    <br>
                    <br>

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
