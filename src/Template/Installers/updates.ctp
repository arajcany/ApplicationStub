<?php
/**
 * @var \App\View\AppView $this
 *
 * @var array $versions
 * @var string $currentVersion
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?= __('Updates') ?>
        </h1>
    </div>
</div>

<?php
if ($versions == false) {
    ?>
    <div class="row">
        <div class="col-lg-12 installers update">
            <?php
            echo __("Sorry, something went wrong with the Update List. Please try again later.");
            return;
            ?>
        </div>
    </div>
    <?php
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="numbers index">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> <?= __('Published Updates') ?>
                </div>
                <div class="card-body">

                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th scope="col" width="10%"><?= __('Version') ?></th>
                            <th scope="col" width="20%"><?= __('Released') ?></th>
                            <th scope="col"><?= __('Note') ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($versions as $k => $versionInfo): ?>
                            <tr>
                                <td><?= $versionInfo['tag'] ?></td>
                                <td>
                                    <?php
                                    if (isset($versionInfo['release_date'])) {
                                        $date = $versionInfo['release_date'];
                                    } else {
                                        if (isset($versionInfo['installer_url'])) {
                                            $date = pathinfo($versionInfo['installer_url'], PATHINFO_FILENAME);
                                            $date = substr($date, 0, 15);
                                        } else {
                                            $date = null;
                                        }
                                    }
                                    echo $date;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (isset($versionInfo['desc'])) {
                                        echo h($versionInfo['desc']);
                                    } else {
                                        echo h('N/A');
                                    }
                                    ?>
                                </td>
                                <td class="actions">
                                    <?php
                                    if (isset($versionInfo['installer_url'])) {
                                        $upgradeUrlHashed = \arajcany\ToolBox\Utility\Security\Security::encrypt64Url($versionInfo['installer_url']);
                                        if (version_compare($currentVersion, $versionInfo['tag']) == 0) {
                                            echo $this->Html->link(__('Reinstall'), ['action' => 'upgrade', $upgradeUrlHashed]);
                                        } elseif (version_compare($currentVersion, $versionInfo['tag']) < 0) {
                                            echo $this->Html->link(__('Upgrade'), ['action' => 'upgrade', $upgradeUrlHashed]);
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
