<?php
/**
 * @var \App\View\AppView $this
 * @var int $hits
 * @var int $timespan
 * @var string $urlRoot
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
        <div class="form large-9 medium-8 columns content border pt-1 pb-3 pl-3 pr-3">
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
                <p> Check out some
                    <?= $this->Html->link(__('Example URLs'), ['#'], ['type' => 'button', 'class' => '', 'data-toggle' => 'modal', 'data-target' => "#exampleUrlModal"]) ?>
                </p>

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

                <?= $this->Html->link(__('Cancel'), ['controller' => 'load-tests'], ['class' => 'btn btn-secondary float-left']) ?>
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
                <?= $k + 1 ?>
            </span>
            <?php
        }
        ?>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="chart">
            <h3><?= __("Performance Graph") ?></h3>
            <canvas id="canvas"></canvas>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12 ml-auto mr-auto">
        <div class="urls">
            <?php
            echo __("Here is the list of URLs that will be called...");
            echo "<textarea rows=\"6\" cols=\"150\" >";
            foreach ($finalUrls as $k => $imageUrl) {
                echo $imageUrl . "\n";
            }
            echo "</textarea>";
            ?>
        </div>
    </div>
</div>

<?php
$this->start('viewCustomScripts');
?>

<?php
echo $this->Html->script('https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js');

$data = [];
$labels = [];
foreach (range(1, $hits) as $k => $v) {
    $labels[] = $k + 1;
    $data[] = 0;
}
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


        function pingPerformanceUrl(targetUrl, timeout, counter) {
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
                    var seconds = (time / 1000).toFixed(2);

                    $('#feedback-' + counter).addClass('success').html(seconds + "s");
                    updateChartData(myBar, counter, seconds);
                },
                error: function (e) {
                    console.log(e);

                    //Calculate the difference in milliseconds then convert to secs rounded.
                    var time = performance.now() - this.startTime;
                    var seconds = (time / 1000).toFixed(2);

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

            for (i = 0; i < urlCount; i++) {
                currentUrl = imageUrls[i];
                currentTimeout = delayMatrix[i];
                clearTimeoutValue = setTimeout(pingPerformanceUrl, currentTimeout, currentUrl, currentTimeout, i)
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

            for (i = 0; i < urlCount; i++) {
                $('#feedback-' + i).html(i);
                updateChartData(myBar, i, 0);
            }
        });


        var barChartData = {
            labels: <?=  json_encode($labels) ?>,
            datasets: [{
                label: '<?= $hits ?> Hits Over <?= $timespan ?> Seconds',
                borderWidth: 1,
                data: <?=  json_encode($data) ?>
            }]
        };

        var ctx = document.getElementById('canvas').getContext('2d');
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Application Framework Response Times'
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Application Response Time'
                        },
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 1
                        }
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Hit Number'
                        },
                        ticks: {
                            min: 0,
                        }
                    }]

                }
            }
        });

        function updateChartData(chart, index, newValue) {
            myBar.data.datasets[0].data[index] = newValue;
            chart.update();
        }

    });

</script>
<?php
$this->end();
?>

<!-- Modal -->
<div class="modal fade" id="exampleUrlModal" tabindex="-1" role="dialog" aria-labelledby="exampleUrlModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleUrlModalLabel">Example URLs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <span class="lead">About Custom URLs</span>
                <p>You can use almost any valid URL. Type a base URL and the selected number requests will be sent to
                    the url. A querystring will be added to the url to prevent browser caching.</p>


                <span class="lead">Syntax</span>
                <p class="mb-1">The URL can contain special keywords that are replaced with data:</p>
                <code>{rnd_int:1-10} Will be replaced by a random integer inclusive of 1 to 10</code><br>
                <code>{rnd_int:5000-7000} Will be replaced by a random integer inclusive of 5000 to 7000</code><br>
                <code>{rnd_pad_int:1-1000} Will be replaced by a random integer inclusive of 0001 to 1000</code><br>
                <code>{rnd_word:5-7} Will be replaced by 5 to 7 random words</code><br>
                <p class="mt-1">You can use multiple keyword replacements in a URL.</p>

                <span class="lead">Example URLs</span>
                <p class="mb-0">
                    Test IIS/Apache Performance - bypasses the framework by calling a static test text file.
                </p>

                <code><?= $urlRoot ?>/webroot/load-tests/static.txt</code>
                <p class="mt-3 mb-0">
                    Test PHP on top of IIS/Apache Performance - bypasses the framework by calling a simple PHP file.
                </p>
                <code><?= $urlRoot ?>/webroot/load-tests/static.php</code>

                <p class="mt-3 mb-0">
                    Test Image Performance - generates dynamic image on the fly.
                </p>
                <code class="mb-3"><?= $urlRoot ?>/load-tests/image/{rnd_int:400-500}/auto/auto/{rnd_word:1}.jpg</code>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

