<?php
/**
 * @var App\View\AppView $this
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $this->fetch('title') ?></title>

    <?php echo $this->element('corebs') ?>
    <?php echo $this->fetch('viewPluginCss'); ?>
</head>

<body>

<?php echo $this->element('navbar') ?>

<main role="main" class="container-fluid">

    <?= $this->Flash->render() ?>

    <?php

    if ($this->request->getParam('controller') == 'Pages') {
        ?>
        <div class="starter-template">
            <div class="row">
                <div class="col-12">
                    <h1>Application Stub</h1>

                    <?php if (1 == 1) { ?>
                        <p class="lead">
                            Please select an Action from the Menu.
                        </p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="row">
        <div class="col-12">
            <?= $this->fetch('content') ?>
        </div>
    </div>


</main><!-- /.container -->

<div class="m-5">
    &nbsp;
</div>

<footer class="footer">
    <div class="container-fluid">
        <span class="text-muted">&copy; <?= date("Y") ?> https://github.com/arajcany</span>
    </div>
</footer>

<?php echo $this->element('corejs') ?>

<!-- Plugin scripts required by this views -->
<?php echo $this->fetch('viewPluginScripts'); ?>

<!-- Custom scripts required by this view -->
<?php echo $this->fetch('viewCustomScripts'); ?>

</body>
</html>

