<?php
/**
 * @var \App\View\AppView $this
 * @var array $services
 * @var Query|Heartbeat[] $heartbeats
 */

use App\Model\Entity\Heartbeat;
use Cake\ORM\Query;

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

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Load Tests') ?>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-5">
        <div class="workers index large-9 medium-8 columns content">
            <div class="card">
                <div class="card-header">
                    <strong>Choose a type of load test</strong>
                </div>
                <div class="card-body">
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Basic Performance'),
                            ['action' => 'application-performance',],
                            $options
                        )
                        ?>
                        Test the Applications basic performance. The test will call an internal URL that delivers back a JSON response.
                    </p>

                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Variable URL Performance'),
                            ['action' => 'url-performance',],
                            $options
                        )
                        ?>
                        Call almost any URL (internal or external) and insert random numbers and words. Can be used to test image rendering performance.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

