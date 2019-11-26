<?php
/**
 * @var \App\View\AppView $this
 * @var array $user
 * @var array $header
 * @var array $message
 */
?>
<div class="row">
    <div class="col-md-4 ml-auto mr-auto">
        <div class="card p-4">
            <div class="card-body">
                <?= $this->Flash->render() ?>
                <h1><?= $header ?></h1>
                <p class="text-muted">
                    <?= $this->Flash->render() ?>
                    <?= $message ?>
                    Please try to <?= $this->Html->link('login',
                        ['controller' => 'users', 'action' => 'login']) ?>
                    again to reset an expired password.
                </p>
            </div>
        </div>
    </div>
</div>
