<?php
/**
 * @var App\View\AppView $this
 */
?>
<!-- Bootstrap core CSS -->
<?php
echo $this->Html->css('/webroot/vendors/bootstrap-4.1.3/css/bootstrap.css');
echo $this->Html->css('/webroot/vendors/bootstrap-4.1.3/css/bootstrap-grid.css');
echo $this->Html->css('/webroot/vendors/bootstrap-4.1.3/css/bootstrap-reboot.css');
?>

<!-- Custom styles for this template -->
<style>
    body {
        padding-top: 5rem;
    }

    .starter-template {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    /* Sticky footer styles
    -------------------------------------------------- */
    html {
        position: relative;
        min-height: 100%;
    }

    body {
        /* Margin bottom by footer height */
        margin-bottom: 60px;
    }

    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        /* Set the fixed height of the footer here */
        height: 60px;
        line-height: 60px; /* Vertically center the text there */
        background-color: #f5f5f5;
    }

    /* Custom page CSS
    -------------------------------------------------- */
    /* Not required for template or sticky footer method. */

    body > .container {
        padding: 60px 15px 0;
    }

    .footer > .container {
        padding-right: 15px;
        padding-left: 15px;
    }

    code {
        font-size: 80%;
    }

</style>
