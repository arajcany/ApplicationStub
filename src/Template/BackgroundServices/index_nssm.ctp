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
        echo $this->Html->link(__('Cancel'), ['controller' => ''], $opts)
        ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Enable Background Services') ?>
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
                    <p>
                        To enable Background Services, <?= APP_NAME ?> needs to download and use a 'service manager'
                        application. <?= APP_NAME ?> uses <a href="https://nssm.cc/">NSSM</a> as the service manager and
                        you can find more information here <a href="https://nssm.cc/">https://nssm.cc/</a>.
                    </p>
                    <p>
                        If you choose to use Background Services, you agree to the terms and conditions of NSSM located
                        <a href="https://nssm.cc/download">here</a>.
                    </p>
                    <p>
                        <?php
                        $options = [
                            'class' => "btn btn-primary"
                        ];
                        echo $this->Html->link(
                            __('Download and Use NSSM'),
                            ['action' => 'download-nssm',],
                            $options
                        )
                        ?>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

