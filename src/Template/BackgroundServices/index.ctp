<?php
/**
 * @var \App\View\AppView $this
 * @var array $services
 * @var Query|Heartbeat[] $heartbeats
 * @var HeartbeatsTable $HeartbeatsTable
 */

use App\Model\Entity\Heartbeat;
use App\Model\Table\HeartbeatsTable;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

$HeartbeatsTable = TableRegistry::getTableLocator()->get('Heartbeats');
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
                    <strong><?= __('Important Information') ?></strong>
                </div>
                <div class="card-body">
                    <?php
                    $options = [
                        'class' => ""
                    ];
                    $link = $this->Html->link(
                        __('Workers'),
                        ['controller' => 'workers',],
                        $options
                    )
                    ?>
                    <p>
                        Interacting with the Background Services on this page may have unintended side-effects.<br>
                        Killing a Background Services should be used as a last resort.
                        Consider using <?= $link ?> to gracefully recycle or stop Background Services.
                    </p>
                </div>
            </div>
        </div>
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
                            'class' => "btn btn-primary btn-sm"
                        ];
                        echo $this->Html->link(
                            __('Create Batch Files'),
                            ['action' => 'batch',],
                            $options
                        )
                        ?>
                        Create batch files that can be used to install/remove the Windows Services.
                    </p>
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary btn-sm"
                        ];
                        echo $this->Html->link(
                            __('Start'),
                            ['action' => 'start', 'all'],
                            $options
                        )
                        ?>
                        Start all Background Services that are currently STOPPED or PAUSED.
                    </p>
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary btn-sm"
                        ];
                        echo $this->Html->link(
                            __('Kill'),
                            ['action' => 'stop', 'all'],
                            $options
                        )
                        ?>
                        Kill all Background Services. If any Errands are being run, they will fail to complete.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="workers index large-9 medium-8 columns content">
            <h3><?= __('Installed Background Services') ?></h3>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Current State</th>
                    <th scope="col">Startup Type</th>
                    <th scope="col" class="actions"><?= __('Actions') ?> </th>
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
                                    echo $this->Html->link(__('Kill'), ['action' => 'stop', $service['name']]);
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

<div class="row mb-5">
    <div class="col-lg-12 mb-5">
        <div class="workers index large-9 medium-8 columns content">
            <h3><?= __('Service Monitoring') ?></h3>
            <div class="card">
                <div class="card-header">
                    <?php
                    $pulseLimit = 5;
                    ?>
                    <strong><?php echo __("Heartbeats with last {0} Pulses", $pulseLimit) ?></strong>
                </div>
                <div class="card-body">

                    <?php
                    if ($heartbeats) {
                        foreach ($heartbeats as $heartbeat) {
                            /**
                             * @var Heartbeat[] $pulses
                             */
                            $pulses = $HeartbeatsTable->findPulsesForHeartbeat($heartbeat, $pulseLimit);
                            ?>
                            <p>
                                <?php
                                echo "<strong>" . $heartbeat->context . "</strong>";
                                echo " - started ";
                                echo $heartbeat->created->timeAgoInWords();
                                echo " (";
                                echo $heartbeat->created->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ);
                                echo ")";
                                echo '<br>';
                                $pulseCounter = 0;
                                foreach ($pulses as $pulse) {
                                    echo " - ";
                                    echo $pulse->created->timeAgoInWords();
                                    echo " - ";
                                    echo $pulse->name;
                                    echo '<br>';
                                    $pulseCounter++;
                                }
                                if (!$pulseCounter) {
                                    echo " - No Pulse";
                                    echo '<br>';
                                }
                                ?>
                            </p>
                            <?php
                        }
                    }
                    ?>


                </div>
            </div>
        </div>
    </div>
</div>
