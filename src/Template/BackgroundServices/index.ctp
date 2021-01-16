<?php
/**
 * @var \App\View\AppView $this
 * @var array $services
 */
?>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <?php
        $opts = [
            'class' => 'btn btn-secondary float-right',
        ];
        echo $this->Html->link(__('Back'), ['controller' => ''], $opts)
        ?>
    </div>
</div>

<div class="row mb-2">
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
                    <th scope="col">Current State</th>
                    <th scope="col">Startup Type</th>
                    <th scope="col" class="actions"><?= __('Actions') ?> (<?= $startStopLink ?>)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service): ?>
                    <?php
                    /**
                     * Service states:
                     * RUNNING | PAUSED | STOPPED
                     *
                     * Service start types:
                     * DEMAND_START | DISABLED | (DELAYED) | AUTO_START
                     */
                    ?>

                    <tr>
                        <td><?= $service['name'] ?></td>
                        <td><?= $service['state'] ?></td>
                        <td><?= $service['start_type'] ?></td>
                        <td class="actions">
                            <?php
                            $showActions = true;
                            if ($showActions) {
                                if ($service['state'] == 'RUNNING' || $service['state'] == 'PAUSED') {
                                    echo $this->Html->link(__('Stop'), ['action' => 'stop', $service['name']]);
                                } elseif ($service['state'] == 'STOPPED' && $service['start_type'] != 'DISABLED') {
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
