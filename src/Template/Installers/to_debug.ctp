<?php
/**
 * @var \App\View\AppView $this
 *
 * @var mixed $toDebug
 */

use App\Utility\Feedback\DebugCapture;

?>

<div class="row">
    <div class="col-md-12 col-xl-8 m-xl-auto">
        <div class="tester">
            <div class="card">
                <div class="card-body">
                    <?php
                    $toDebug = DebugCapture::captureDump($toDebug);
                    pr($toDebug);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
