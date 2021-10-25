<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var bool $caseSensitive
 */

use Cake\Routing\Router;

?>

<?php echo $this->Html->css('/webroot/vendors/bootstrap-ui/signin.css'); ?>

<!-- BEGIN LOGIN FORM -->
<?= $this->Form->create($user, ['class' => 'form-signin']) ?>
<h1 class="h3 mb-3 font-weight-normal text-center">Sign In</h1>
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
    echo $this->Form->button(__('Sign In'), $options);
    ?>
    <span id="first-run" class="d-none">
        <?php
        echo $this->Html->link(__('First Run'), '#', ['class' => 'mt-2 float-left'])
        ?>
    </span>

    <?php
    echo $this->Html->link(__('Forgot Password'), ['controller' => 'forgot'], ['class' => 'mt-2 float-right'])
    ?>
</div>
<?= $this->Form->end() ?>
<!-- END LOGIN FORM -->

<?php
$this->append('viewCustomScripts');
$targetUrl = Router::url(['controller' => 'users', 'action' => 'pre-login'], true);
?>
<script>
    $(document).ready(function () {
        var usernameField = $("input[name*='username']");
        var usernameValue;
        var usernameFound = false;

        var emailField = $("input[name*='email']");

        usernameField.keyup(function () {
            emailField.val(this.value);
        }).change(function () {
            if (usernameFound === false) {
                runUser();
            }
        });

        runPageLoad();

        function runPageLoad() {

            var targetUrl = "<?= $targetUrl?>";
            var formData = new FormData();
            formData.append("page_load", true);

            $.ajax({
                type: "POST",
                url: targetUrl,
                async: true,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                timeout: 6000,

                success: function (response) {
                    if (response['redirect']) {
                        var url = (response['redirect']);
                        $("#first-run a").attr('href', url);
                        $("#first-run").removeClass('d-none');
                    }
                },
                error: function (e) {
                    //alert("An error occurred: " + e.responseText.message);
                    console.log(e);
                }
            })
        }

        function runUser() {
            usernameValue = usernameField.val();
            console.log(usernameValue);

            var targetUrl = "<?= $targetUrl?>";
            var formData = new FormData();
            formData.append("username", usernameValue);

            $.ajax({
                type: "POST",
                url: targetUrl,
                async: true,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                timeout: 60000,

                success: function (response) {
                    if (response === true) {
                        usernameFound = true;
                    }
                },
                error: function (e) {
                    //alert("An error occurred: " + e.responseText.message);
                    console.log(e);
                }
            })
        }

    });
</script>
<?php
$this->end();
?>
