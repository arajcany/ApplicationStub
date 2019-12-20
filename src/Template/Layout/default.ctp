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

<main role="main" class="container">

    <?= $this->Flash->render() ?>

    <div class="row">
        <div class="col-12">
            <?= $this->fetch('content') ?>
        </div>
    </div>

</main><!-- /.container -->

<?php echo $this->element('footer') ?>

<?php echo $this->element('corejs') ?>

<!-- Plugin scripts required by this views -->
<?php echo $this->fetch('viewPluginScripts'); ?>

<!-- Custom scripts required by this view -->
<?php echo $this->fetch('viewCustomScripts'); ?>

</body>
</html>

