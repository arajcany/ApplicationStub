<?php
/**
 * @var \App\View\AppView $this
 * @var string $drive
 * @var string $gitBranch
 * @var array $gitModified
 */

use Cake\Core\Configure\Engine\PhpConfig;

?>

<?php
$labelClass = 'col-md-3 form-control-label';
$inputClass = 'form-control';

$defaultOptions = [
    'label' => [
        'class' => $labelClass,
    ],
    'options' => null,
    'class' => $inputClass,
];
?>

<?php
if (strtolower($gitBranch) != 'master') {
    ?>
    <div class="row">
        <div class="col-md-12">
            <p class="alert alert-warning">
                <?= __("Sorry, you are not on the 'Master' branch. Please commit and merge all changes into 'Master' before attempting to build a release.") ?>
            </p>
        </div>
    </div>
    <?php
    return;
}
?>

<?php
if (count($gitModified) > 0) {
    ?>
    <div class="row">
        <div class="col-md-12">
            <p class="alert alert-warning">
                <?= __("Sorry, there are uncommitted files the ''{0}'' branch. Please commit all changes into 'Master' before attempting to build a release.", $gitBranch) ?>
            </p>
            <?php
            $opts = [
                'class' => 'alert alert-warning',
            ];
            echo $this->Html->nestedList($gitModified, $opts);
            ?>
        </div>
    </div>
    <?php
    return;
}
?>

<div class="row">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="releasesBuild">
            <div class="card">
                <div class="card-header">
                    <legend><?= __('Build a Release of "{0}"', APP_NAME) ?></legend>
                </div>
                <div class="card-body">
                    <?php
                    $formOptions = ['type' => 'file'];
                    ?>
                    <?= $this->Form->create(null, $formOptions) ?>
                    <fieldset>
                        <?php
                        /* place_in_drive */
                        $opts = [
                            'label' => ['class' => $labelClass, 'text' => 'Zip Location'],
                            'class' => $inputClass,
                            'templateVars' => ['divClass' => ' form-group'],
                            'select' => ['class' => "form-control"],
                            'value' => $drive,
                        ];
                        echo $this->Form->control('place_in_drive', $opts);
                        ?>
                    </fieldset>
                </div>
                <div class="card-footer">
                    <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
//restore the original templates
$this->Form->resetTemplates();
?>

