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
                    <strong>Choose a Task</strong>
                </div>
                <div class="card-body">
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Build a Release ZIP Package'),
                            ['action' => 'build',],
                            $options
                        )
                        ?>
                    </p>
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
                        echo __("Your are currently on the ''{0}'' branch.", $gitBranch);
                        ?></pre>

                    <pre><?php
                        echo __("The following has been modified.");
                        ?></pre>

                    <pre><?php
                        pr($gitModified);
                        ?></pre>

                    <pre><?php
                        echo __("The following has been committed.");
                        ?></pre>

                    <pre><?php
                        pr($gitCommits);
                        ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>
