<?php
/**
 * @var \App\View\AppView $this
 * @var array $services
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Background Services') ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-5">
        <div class="workers index large-9 medium-8 columns content">
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
                            __('Create Batch Files'),
                            ['action' => 'batch',],
                            $options
                        )
                        ?>
                        Create batch files that can be used to install/remove the Windows Services.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="workers index large-9 medium-8 columns content">
            <h3><?= __('Installed Services') ?></h3>
            <span class="float-right">
            <?php
            $startLink = $this->Html->link(__('Start All'), ['action' => 'start', 'all']);
            $stopLink = $this->Html->link(__('Stop All'), ['action' => 'stop', 'all']);
            $startStopLink = __("{0} | {1}", $startLink, $stopLink);
            ?>
            </span>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">State</th>
                    <th scope="col" class="actions"><?= __('Actions') ?> (<?= $startStopLink ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= $service['name'] ?></td>
                        <td><?= $service['state'] ?></td>
                        <td class="actions">
                            <?php
                            $showActions = true;
                            if ($showActions) {
                                if ($service['state'] == 'RUNNING' || $service['state'] == 'PAUSED') {
                                    echo $this->Html->link(__('Stop'), ['action' => 'stop', $service['name']]);
                                } elseif ($service['state'] == 'STOPPED') {
                                    echo $this->Html->link(__('Start'), ['action' => 'start', $service['name']]);
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
