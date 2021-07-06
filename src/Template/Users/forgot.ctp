<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var bool $caseSensitive
 */
?>

<?php echo $this->Html->css('/webroot/vendors/bootstrap-ui/signin.css'); ?>

<!-- BEGIN LOGIN FORM -->
<?= $this->Form->create($user, ['class' => 'form-signin']) ?>
<h1 class="h3 mb-3 font-weight-normal text-center">Forgot Password</h1>
<?= $this->Flash->render() ?>
<div class="form-group">
    <label for="inputEmail" class="sr-only">Username or Email</label>
    <?php
    if ($caseSensitive) {
        $placeholder = " (Case Sensitive)";
    } else {
        $placeholder = "";
    }

    $options = [
        'class' => "form-control form-control-solid placeholder-no-fix",
        'type' => "text",
        'autocomplete' => "off",
        'placeholder' => "Username or Email" . $placeholder,
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
            'placeholder' => "Username or Email" . $placeholder,
            'label' => false
        ];
        echo $this->Form->text('email', $options);
        ?>
    </div>
</div>

<div class="form-actions">
    <?php
    $options = [
        'class' => "btn btn-lg btn-primary btn-block",
    ];
    echo $this->Form->hidden('forgot');
    echo $this->Form->button(__('Send Reset Link'), $options);

    echo $this->Html->link(__('Back To Sign In'), ['controller' => 'login'], ['class' => 'mt-2 float-right'])
    ?>
</div>
<?= $this->Form->end() ?>
<!-- END LOGIN FORM -->

<?php
$this->append('viewCustomScripts');
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
