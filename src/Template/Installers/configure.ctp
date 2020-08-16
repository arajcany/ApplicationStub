<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User|\Cake\Collection\CollectionInterface $user
 */
?>


<div class="row">
    <div class="col-md-6 ml-auto mr-auto">
        <!-- BEGIN LOGIN FORM -->
        <?= $this->Form->create($user, ['class' => 'form-signin', 'type' => 'POST']) ?>
        <h3 class="form-title">Please set a password for <?=$user->username ?></h3>
        <?= $this->Flash->render() ?>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <?php
            $options = [
                'class' => "form-control form-control-solid placeholder-no-fix",
                'type' => "password",
                'autocomplete' => "off",
                'placeholder' => "Password",
                'label' => false,
                'value' => ''
            ];
            echo $this->Form->control('password', $options)
            ?>
        </div>

        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password Repeat</label>
            <?php
            $options = [
                'class' => "form-control form-control-solid placeholder-no-fix",
                'type' => "password",
                'autocomplete' => "off",
                'placeholder' => "Password Repeat",
                'label' => false,
                'value' => ''
            ];
            echo $this->Form->control('password_2', $options)
            ?>
        </div>

        <div class="form-actions">
            <?php
            $options = [
                'class' => "btn btn-lg btn-primary btn-block",
            ];
            echo $this->Form->hidden('configure');
            echo $this->Form->button(__('Configure'), $options);
            ?>
        </div>
        <?= $this->Form->end() ?>
        <!-- END LOGIN FORM -->
    </div>
    <div class="col-md-12 spacer50">
    </div>
</div>
