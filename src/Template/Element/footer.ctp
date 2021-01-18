<?php
/**
 * @var App\View\AppView $this
 * @var array $version
 */

use Cake\Core\Configure;

$io = Configure::read('InternalOptions');
$guiText = 'https://github.com/arajcany';
if (isset($io['company_name']) && !empty(trim($io['company_name']))) {
    $guiText = trim($io['company_name']);
} elseif (isset($io['web']) && !empty(trim($io['web']))) {
    $guiText = trim($io['web']);
} elseif (isset($io['email']) && !empty(trim($io['email']))) {
    $guiText = trim($io['email']);
}
?>
    <footer class="footer">
        <div class="container-fluid">
            <span class="text-muted float-left">&copy; <?= date("Y") ?> <?= $guiText ?></span>
            <span class="text-muted float-right"><?= __("Version {0}", $version['tag']) ?></span>
        </div>
    </footer>
<?php
