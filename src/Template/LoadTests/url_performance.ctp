<?php
/**
 * @var \App\View\AppView $this
 * @var int $hits
 * @var int $timespan
 * @var string $url
 * @var array $finalUrls
 * @var array $delayMatrix
 */
?>

<style>
    span.default {
        display: inline-block;
        width: 4%;
        height: 25px;
        background-color: #f1f0f0;
        padding-top: 0;
        margin: .5%;
    }

    span.running {
        background-color: #fcce99;
    }

    span.success {
        background-color: #94f894;
    }

    span.error {
        background-color: #f19999;
    }
</style>

<div class="row mb-5">
    <div class="col-lg-8 ml-auto mr-auto">
        <div class="form large-9 medium-8 columns content border pt-1 pb-1 pl-3 pr-3">
            <?php
            $formOpts = [
            ];

            $labelClass = 'form-control-label';
            $inputClass = 'form-control mb-4';
            $checkboxClass = 'mr-2 mb-4';

            $defaultOptions = [
                'label' => [
                    'class' => $labelClass,
                ],
                'options' => null,
                'class' => $inputClass,
                'type' => 'select'
            ];

            ?>
            <?= $this->Form->create(null, $formOpts) ?>
            <fieldset>
                <legend><?= __('Test Parameters') ?></legend>
                <?php
                $hitsOptions = $defaultOptions;
                $hitsOptions['options'] = [
                    10 => '10 Hits',
                    20 => '20 Hits',
                    30 => '30 Hits',
                    40 => '40 Hits',
                    50 => '50 Hits',
                    100 => '100 Hits',
                    200 => '200 Hits',
                    300 => '300 Hits',
                    600 => '600 Hits',
                    1200 => '1200 Hits',
                ];
                $hitsOptions['default'] = $hits;

                $timespanOptions = $defaultOptions;
                $timespanOptions['options'] = [
                    1 => '1 Second',
                    2 => '2 Seconds',
                    3 => '3 Seconds',
                    4 => '4 Seconds',
                    5 => '5 Seconds',
                    10 => '10 Seconds',
                    20 => '20 Seconds',
                    30 => '30 Seconds',
                    60 => '60 Seconds',
                    120 => '120 Seconds',
                ];
                $timespanOptions['default'] = $timespan;

                $urlOptions = $defaultOptions;
                $urlOptions['label']['text'] = 'URL';
                $urlOptions['type'] = 'text';
                $urlOptions['default'] = $url;

                echo $this->Form->control('hits', $hitsOptions);
                echo $this->Form->control('timespan', $timespanOptions);
                echo $this->Form->control('url', $urlOptions);
                ?>

                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary float-right']) ?>
                <?= $this->Form->end() ?>
            </fieldset>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-0 mb-5 text-center">
        <?php
        echo __("This will perform {0} calls to the URLs within a {1} second timeframe.", $hits, $timespan);
        echo "<br>";
        $rps = round(($hits / $timespan), 3);
        echo __("This equates to {0} requests per second.", $rps);
        ?>
    </div>
</div>
<div class="row">
    <div class="col-12 mb-2 text-center">
        <button type="button" class="btn btn-success" id="test-start">Start Testing</button>
        <button type="button" class="btn btn-danger" id="test-stop">Stop Testing</button>
        <button type="button" class="btn btn-secondary" id="test-clear">Clear Results</button>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <?php
        foreach ($finalUrls as $k => $imageUrl) {
            ?>
            <span id="feedback-<?= $k ?>" class="text-center default">
                <?= $k ?>
            </span>
            <?php
        }
        ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12 mb-2 text-center">
        <?php
        echo __("Here is the list or URLs that will be called...");
        echo "<textarea rows=\"6\" cols=\"150\" >";
        foreach ($finalUrls as $k => $imageUrl) {
            echo $imageUrl . "\n";
        }
        echo "</textarea>";
        ?>
    </div>
</div>

<?php
$this->start('viewCustomScripts');
?>
<script>

    $(document).ready(function ($) {
        var i;
        var urlCount = <?= $hits ?>;
        var imageUrls = <?= json_encode($finalUrls, JSON_PRETTY_PRINT) ?>;
        var timeSpan = <?= $timespan ?>;
        var delayMatrix = <?= json_encode($delayMatrix, JSON_PRETTY_PRINT) ?>;
        var clearTimeoutValue;
        var clearTimeoutValues = [];


        function pingImageUrl(targetUrl, timeout, counter) {
            //console.log(timeout + ' | ' + targetUrl);
            $('#feedback-' + counter).addClass('running');

            $.ajax({
                type: "GET",
                url: targetUrl,
                async: true,
                cache: false,
                contentType: false,
                processData: false,
                timeout: 60000,
                startTime: performance.now(),

                success: function (response) {
                    //console.log(response);

                    //Calculate the difference in milliseconds then convert to secs rounded.
                    var time = performance.now() - this.startTime;
                    var seconds = (time / 1000).toFixed(1);

                    $('#feedback-' + counter).addClass('success').html(seconds + "s");
                },
                error: function (e) {
                    console.log(e);

                    //Calculate the difference in milliseconds then convert to secs rounded.
                    var time = performance.now() - this.startTime;
                    var seconds = (time / 1000).toFixed(1);

                    $('#feedback-' + counter).addClass('error').html(seconds);
                }
            })
        }

        var currentUrl;
        var currentTimeout;
        $('#test-start').on('click', function () {
            console.log($(this).text());
            $('[id^="feedback-"]').removeClass('running').removeClass('error').removeClass('success');

            $('#feedback-').removeClass('running').removeClass('error').removeClass('success');

            for (i = 1; i <= urlCount; i++) {
                currentUrl = imageUrls[i];
                currentTimeout = delayMatrix[i];
                clearTimeoutValue = setTimeout(pingImageUrl, currentTimeout, currentUrl, currentTimeout, i)
                clearTimeoutValues.push(clearTimeoutValue);
            }
        });

        $('#test-stop').on('click', function () {
            console.log($(this).text());

            for (i = 0; i < clearTimeoutValues.length; i++) {
                clearTimeout(clearTimeoutValues[i]);
            }
            clearTimeoutValues = [];

        });

        $('#test-clear').on('click', function () {
            console.log($(this).text());

            $('[id^="feedback-"]').removeClass('running').removeClass('error').removeClass('success');

        });


    });

</script>
<?php
$this->end();
?>

