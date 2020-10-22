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
                            __('Create Services'),
                            ['action' => 'create',],
                            $options
                        )
                        ?>
                        This will create Batch files that can be used to Install and Remove Windows Services.
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
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">State</th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= $service['name'] ?></td>
                        <td><?= $service['state'] ?></td>
                        <td class="actions">
                            <?php
                            if (1 == 2) {
                                if ($service['state'] == 'RUNNING') {
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
