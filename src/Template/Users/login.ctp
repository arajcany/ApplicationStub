<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<div class="row">
    <div class="col-md-4 ml-auto mr-auto">
        <!-- BEGIN LOGIN FORM -->
        <?= $this->Form->create($user, ['class' => 'form-signin']) ?>
        <h3 class="form-title">Please sign in</h3>
        <?= $this->Flash->render() ?>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">Username</label>
            <?php
            $options = [
                'class' => "form-control form-control-solid placeholder-no-fix",
                'type' => "text",
                'autocomplete' => "off",
                'placeholder' => "Username or Email",
                'label' => false
            ];
            echo $this->Form->text('username', $options)
            ?>
        </div>

        <div class="form-group d-none">
            <?php //this div is hidden to prevent csrf fails ?>
            <label class="control-label visible-ie8 visible-ie9">Email</label>
            <?php
            $options = [
                'class' => "form-control form-control-solid placeholder-no-fix",
                'type' => "text",
                'autocomplete' => "off",
                'placeholder' => "Username or Email",
                'label' => false
            ];
            echo $this->Form->text('email', $options)
            ?>
        </div>

        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <?php
            $options = [
                'class' => "form-control form-control-solid placeholder-no-fix",
                'type' => "password",
                'autocomplete' => "off",
                'placeholder' => "Password",
                'label' => false
            ];
            echo $this->Form->password('password', $options)
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
$this->start('viewCustomScripts');
?>
<script>
    <?php //allows for username or email login by populating hidden 'email' field ?>
    $("input[name*='username']").keyup(function () {
        $("input[name*='email']").val(this.value);
    });
</script>
<?php
$this->end();
?>
