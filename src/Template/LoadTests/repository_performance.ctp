<?php
/**
 * @var \App\View\AppView $this
 * @var int $cycles
 * @var int $sizeMb
 * @var array $performance
 */
?>

<style>
    span.default {
        display: inline-block;
        width: 4%;
        height: 25px;
        background-color: #f1f0f0;
        padding-top: 0;
        margin: .5%;
    }

    span.running {
        background-color: #fcce99;
    }

    span.success {
        background-color: #94f894;
    }

    span.error {
        background-color: #f19999;
    }
</style>

<div class="row">
    <div class="col-12 ml-auto mr-auto mb-3">
        <?php
        $opts = [
            'class' => 'btn btn-primary float-right',
        ];
        echo $this->Html->link(__('Edit Repository Settings'), ['controller' => 'settings', 'action' => 'edit-group', 'repository'], $opts)
        ?>
    </div>
</div>

<div class="row mb-5">
    <div class="col-lg-4 ml-auto mr-auto">
        <div class="form large-9 medium-8 columns content border pt-1 pb-3 pl-3 pr-3">
            <?php
            $formOpts = [
            ];

            $labelClass = 'form-control-label';
            $inputClass = 'form-control mb-4';
            $checkboxClass = 'mr-2 mb-4';

            $defaultOptions = [
                'label' => [
                    'class' => $labelClass,
                ],
                'options' => null,
                'class' => $inputClass,
                'type' => 'select'
            ];

            ?>
            <?= $this->Form->create(null, $formOpts) ?>
            <fieldset>
                <legend><?= __('Repository Test Parameters') ?></legend>
                <?php
                $cyclesOptions = $defaultOptions;
                $cyclesOptions['options'] = [
                    10 => '10 Cycles',
                    20 => '20 Cycles',
                    30 => '30 Cycles',
                    40 => '40 Cycles',
                    50 => '50 Cycles',
                    100 => '100 Cycles',
                    200 => '200 Cycles',
                    300 => '300 Cycles',
                    500 => '500 Cycles',
                ];
                $cyclesOptions['default'] = $cycles;

                $sizeOptions = $defaultOptions;
                $sizeOptions['options'] = [

                    '0.1' => '0.1 Mb',
                    '0.2' => '0.2 Mb',
                    '0.3' => '0.3 Mb',
                    '0.4' => '0.4 Mb',
                    '0.5' => '0.5 Mb',
                    '1' => '1 Mb',
                    '2' => '2 Mb',
                    '3' => '3 Mb',
                    '4' => '4 Mb',
                    '5' => '5 Mb',
                    '10' => '10 Mb',
                ];
                $sizeOptions['default'] = $sizeMb;

                echo $this->Form->control('cycles', $cyclesOptions);
                echo $this->Form->control('sizeMb', $sizeOptions);
                ?>

                <?= $this->Html->link(__('Cancel'), ['controller' => 'load-tests'], ['class' => 'btn btn-secondary float-left']) ?>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
                <?= $this->Form->end() ?>
            </fieldset>
        </div>
    </div>
</div>

<?php
if (isset($performance)) {
    ?>
    <div class="row mb-5 border">
        <div class="col-lg ml-auto mr-auto">
            <h4>Repository Test Results</h4>
            <pre><?php
                if (isset($performance)) {
                    print_r($performance);
                }
                ?></pre>
        </div>
    </div>
    <?php
}
?>
