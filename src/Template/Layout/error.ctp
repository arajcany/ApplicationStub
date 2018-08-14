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

</head>

<body>

<?php echo $this->element('navbar') ?>

<main role="main" class="container">

    <?= $this->Flash->render() ?>

    <div class="starter-template">
        <div class="row">
            <div class="column-12">
                <h1>Error!</h1>

                <p class="lead">
                    Please try again...
                </p>
            </div>
        </div>
    </div>

</main><!-- /.container -->

<?php echo $this->element('corejs') ?>

</body>
</html>

