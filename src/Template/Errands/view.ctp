<?php
/**
 * @var AppView $this
 * @var Errand $errand
 */

use App\Model\Entity\Errand;
use App\View\AppView;

?>

<div class="errands view large-9 medium-8 columns content">
    <h3><?= h($errand->name) ?></h3>
    <table class="vertical-table table-bordered">
        <tr>
            <th scope="row"><?= __('Domain') ?></th>
            <td><?= h($errand->domain) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($errand->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Worker Name') ?></th>
            <td><?= h($errand->worker_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Class') ?></th>
            <td><?= h($errand->class) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Method') ?></th>
            <td><?= h($errand->method) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Parameters') ?></th>
            <td>
                <pre><?php print_r($errand->parameters) ?></pre>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= h($errand->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Return Message') ?></th>
            <td><?= h($errand->return_message) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Errors Thrown') ?></th>
            <td><?= h($errand->errors_thrown) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($errand->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Wait For Link') ?></th>
            <td><?= $this->Number->format($errand->wait_for_link) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Worker Link') ?></th>
            <td><?= $this->Number->format($errand->worker_link) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Progress Bar') ?></th>
            <td><?= $this->Number->format($errand->progress_bar) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Priority') ?></th>
            <td><?= $this->Number->format($errand->priority) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Return Value') ?></th>
            <td><?= $this->Number->format($errand->return_value) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Errors Retry') ?></th>
            <td><?= $this->Number->format($errand->errors_retry) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Errors Retry Limit') ?></th>
            <td><?= $this->Number->format($errand->errors_retry_limit) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($errand->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($errand->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Activation') ?></th>
            <td><?= h($errand->activation) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expiration') ?></th>
            <td><?= h($errand->expiration) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Started') ?></th>
            <td><?= h($errand->started) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Completed') ?></th>
            <td><?= h($errand->completed) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Auto Delete') ?></th>
            <td><?= $errand->auto_delete ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
