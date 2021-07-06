<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User|\Cake\Collection\CollectionInterface $user
 * @var bool $caseSensitive
 */
?>


<div class="row">
    <div class="col-md-6 ml-auto mr-auto">
        <h3 class="form-title"><?= APP_NAME ?> Base Configuration</h3>
        <?= $this->Flash->render() ?>
        <!-- BEGIN FORM -->
        <?= $this->Form->create($user, ['class' => 'form-signin', 'type' => 'POST']) ?>

        <p class="lead mt-4 mb-1">Please set an emergency email address for the lockout procedure.</p>
        <div class="row">
            <div class="col-md-12 ">
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Emergency Email Address</label>
                    <?php
                    $options = [
                        'class' => "form-control form-control-solid placeholder-no-fix",
                        'type' => "text",
                        'autocomplete' => "off",
                        'placeholder' => "Emergency Email Address",
                        'label' => false,
                        'value' => ''
                    ];
                    echo $this->Form->control('emergency_email', $options)
                    ?>
                </div>
            </div>
        </div>

        <p class="lead mt-4 mb-1">Please set a password for <?= $user->username ?>.</p>
        <div class="row">
            <div class="col-md-6 ">
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
            </div>
            <div class="col-md-6">
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
            </div>
        </div>


        <div class="form-actions mt-5">
            <?php
            $options = [
                'class' => "btn btn-lg btn-primary btn-block",
            ];
            echo $this->Form->hidden('configure');
            echo $this->Form->button(__('Configure'), $options);
            ?>
        </div>
        <?= $this->Form->end() ?>
        <!-- END FORM -->
    </div>
    <div class="col-md-12 spacer50">
    </div>
</div>
