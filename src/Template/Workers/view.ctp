<?php
/**
 * @var AppView $this
 * @var \App\Model\Entity\Worker $worker
 */

use App\View\AppView;

?>

<div class="workers view large-9 medium-8 columns content">
    <h3><?= h($worker->name) ?></h3>
    <table class="vertical-table table-bordered">
        <tr>
            <th scope="row"><?= __('Domain') ?></th>
            <td><?= h($worker->domain) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($worker->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= h($worker->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Errand Name') ?></th>
            <td><?= h($worker->errand_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($worker->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Errand Link') ?></th>
            <td><?= $this->Number->format($worker->errand_link) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Pid') ?></th>
            <td><?= $this->Number->format($worker->pid) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($worker->created->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($worker->modified->i18nFormat("yyyy-MM-dd HH:mm:ss", TZ)) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Appointment Date') ?></th>
            <td><?= h($worker->appointment_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Retirement Date') ?></th>
            <td><?= h($worker->retirement_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Termination Date') ?></th>
            <td><?= h($worker->termination_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Force Retirement') ?></th>
            <td><?= $worker->force_retirement ? __('Yes') : __('No'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Force Shutdown') ?></th>
            <td><?= $worker->force_shutdown ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
