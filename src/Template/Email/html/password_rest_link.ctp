<?php
/**
 * @var $this
 * @var array $dataForView
 */
?>
<h1>Hi <?= $dataForView['entities']['user']['first_name'] ?> <?= $dataForView['entities']['user']['last_name'] ?>,</h1>
<br>
Click on the link below to reset your password
<br>
<?= $dataForView['url'] ?>
