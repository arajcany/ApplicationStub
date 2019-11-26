<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>


<div class="row">
    <div class="col-md-4 ml-auto mr-auto">
        <!-- BEGIN LOGIN FORM -->
        <?= $this->Form->create($user, ['class' => 'form-signin', 'type' => 'POST']) ?>
        <h3 class="form-title">Please reset your password</h3>
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
                'label' => false
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
                'label' => false
            ];
            echo $this->Form->control('password_2', $options)
            ?>
        </div>
        <div class="form-actions">
            <?php
            $options = [
                'class' => "btn btn-lg btn-primary btn-block",
            ];
            echo $this->Form->hidden('login');
            echo $this->Form->button(__('Login'), $options);
            ?>
        </div>
        <?= $this->Form->end() ?>
        <!-- END LOGIN FORM -->
    </div>
    <div class="col-md-12 spacer50">
    </div>
</div>

<?php
return;
?>


<?php
$labelClass = 'col-md-3 form-control-label';
$inputClass = 'form-control';

$defaultOptions = [
    'label' => false,
    'options' => null,
    'class' => $inputClass,
];
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group mb-0">
            <div class="card p-4">
                <div class="card-body">
                    <?= $this->Flash->render() ?>
                    <h1>Reset Password</h1>
                    <p class="text-muted">Please enter a new matching passwords.</p>

                    <!-- BEGIN LOGIN FORM -->
                    <?= $this->Form->create($user, ['class' => 'form-signin']) ?>

                    <?php
                    $extraOptions = [
                        'type' => "password",
                        'placeholder' => 'Password',
                        'templateVars' => ['icon' => 'icon-lock']
                    ];
                    echo $this->Form->control('password', array_merge($defaultOptions, $extraOptions));

                    $extraOptions = [
                        'type' => "password",
                        'placeholder' => 'Repeat Password',
                        'templateVars' => ['icon' => 'icon-lock']
                    ];
                    echo $this->Form->control('password_2', array_merge($defaultOptions, $extraOptions));
                    ?>

                    <div class="row">
                        <div class="col-6">
                            <?php
                            $options = [
                                'class' => "btn btn-primary px-4",
                            ];
                            echo $this->Form->hidden('reset');
                            echo $this->Form->button(__('Reset'), $options);
                            ?>
                        </div>
                        <div class="col-6 text-right">
                            <?php
                            $options = [
                                'class' => "btn btn-link px-0",
                            ];
                            echo $this->Html->link('Back to Login',
                                ['controller' => 'users', 'action' => 'login',], $options);
                            echo " | ";
                            echo $this->Html->link('Forgot Password?',
                                ['controller' => 'users', 'action' => 'forgot',], $options);
                            ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                    <!-- END LOGIN FORM -->

                </div>
            </div>
        </div>
    </div>
</div>
