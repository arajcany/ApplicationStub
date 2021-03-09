<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Message[]|\Cake\Collection\CollectionInterface $messages
 */
?>

<div class="row mt-2">
    <div class="col-12 ml-auto mr-auto">
        <?php
        $opts = [
            'class' => 'btn btn-sm btn-secondary float-right ml-1',
        ];
        ?>
    </div>
</div>

<div class="row">
    <div class="col-12 ml-auto mr-auto">
        <div class="messages index large-9 medium-8 columns content">
            <h3><?= __('Messages') ?></h3>
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id', "ID") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('started', 'Sent') ?></th>
                    <th scope="col">From/To/Subject</th>
                    <th scope="col"><?= $this->Paginator->sort('smtp_code', "SMTP Code") ?></th>
                    <th scope="col"><?= $this->Paginator->sort('smtp_message', "SMTP Message") ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= $this->Number->format($message->id) ?></td>
                        <td><?= h($message->type) ?></td>
                        <td><?= h($message->started->i18nFormat(DTF, TZ)) ?></td>
                        <td>
                            FROM: <?= json_encode($message->email_from) ?><br>
                            TO: <?= json_encode($message->email_to) ?><br>
                            SUBJECT: <?= h($message->subject) ?>
                        </td>
                        <td><?= $this->Number->format($message->smtp_code) ?></td>
                        <td><?= h($message->smtp_message) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $message->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $message->id]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first() ?>
                    <?= $this->Paginator->prev() ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next() ?>
                    <?= $this->Paginator->last() ?>
                </ul>
                <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
            </div>
        </div>
    </div>
</div>
