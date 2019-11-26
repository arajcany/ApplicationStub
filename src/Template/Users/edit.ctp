<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\UserStatus[] $userStatuses
 * @var \App\Model\Entity\Role[] $roles
 *
 */
?>
<div class="row">
    <div class="col-10 ml-auto mr-auto">
        <div class="users form large-9 medium-8 columns content">
            <?php
            $formOpts = [
            ];

            $labelClass = 'form-control-label';
            $inputClass = 'form-control mb-4';

            $defaultOptions = [
                'label' => [
                    'class' => $labelClass,
                ],
                'options' => null,
                'class' => $inputClass,
            ];
            ?>
            <?= $this->Form->create($user) ?>
            <fieldset>
                <legend><?= __('Edit User') ?></legend>
                <?php
                echo $this->Form->control('email', $defaultOptions);
                echo $this->Form->control('username', $defaultOptions);
                echo $this->Form->control('password', $defaultOptions);
                echo $this->Form->control('first_name', $defaultOptions);
                echo $this->Form->control('last_name', $defaultOptions);
                echo $this->Form->control('address_1', $defaultOptions);
                echo $this->Form->control('address_2', $defaultOptions);
                echo $this->Form->control('suburb', $defaultOptions);
                echo $this->Form->control('state', $defaultOptions);
                echo $this->Form->control('post_code', $defaultOptions);
                echo $this->Form->control('country', $defaultOptions);
                echo $this->Form->control('mobile', $defaultOptions);
                echo $this->Form->control('phone', $defaultOptions);
                //echo $this->Form->control('activation', $defaultOptions);
                //echo $this->Form->control('expiration', $defaultOptions);
                echo $this->Form->control('is_confirmed', $defaultOptions);

                $customOpts = array_merge($defaultOptions, ['options' => $userStatuses, 'empty' => false]);
                echo $this->Form->control('user_statuses_id', $customOpts);

                //echo $this->Form->control('password_expiry', $defaultOptions);

                $customOpts = array_merge($defaultOptions, ['options' => $roles]);
                echo $this->Form->control('roles._ids', $customOpts);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
