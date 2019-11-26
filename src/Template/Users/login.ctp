<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<?php echo $this->Html->css('/webroot/vendors/bootstrap-ui/signin.css'); ?>

<!--<form class="form-signin">
    <h1 class="h3 mb-3 font-weight-normal text-center">Please sign in</h1>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>-->

<!-- BEGIN LOGIN FORM -->
<?= $this->Form->create($user, ['class' => 'form-signin']) ?>
<h1 class="h3 mb-3 font-weight-normal text-center">Please sign in</h1>
<?= $this->Flash->render() ?>
<div class="form-group">
    <label for="inputEmail" class="sr-only">Username or Email</label>
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

    <div class="d-none">
        <?php //this div is hidden to prevent csrf fails ?>
        <label for="inputEmail" class="sr-only">Email address</label>
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

    <label for="inputPassword" class="sr-only">Password</label>
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
