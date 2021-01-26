<?php

namespace App\Command;

use App\Model\Table\ArtifactsTable;
use App\Model\Table\ErrandsTable;
use App\Model\Table\HeartbeatsTable;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;

/**
 * ArtifactDeleterCommand command.
 *
 * @property ArtifactsTable $Artifacts
 * @property HeartbeatsTable $Heartbeats
 * @property ErrandsTable $Errands
 *
 */
class ArtifactsDeleterCommand extends Command
{
    public $Artifacts;
    public $Heartbeats;
    public $Errands;

    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Artifacts');
        $this->loadModel('Heartbeats');
        $this->loadModel('Errands');
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser = parent::buildOptionParser($parser);

        $parser
            ->addOption('heartbeat-context', [
                'short' => 'h',
                'help' => 'Context when logging a Heartbeat',
                'default' => 'ArtifactDeleter',
            ])
            ->addOption('delay', [
                'short' => 'd',
                'help' => 'Delay the start by X seconds - handy if there are multiple instances',
                'default' => 0,
            ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        //delay if there are multiple Artifact Deleters.
        $delay = intval($args->getOption('delay'));
        if ($delay) {
            $io->out(__("Delaying start by {0} seconds...", $delay));
            sleep($delay);
        }

        $heartbeatContext = $args->getOption('heartbeat-context');

        $startTime = new FrozenTime();

        $io->out('Initiating Artifact Deleter.');

        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => 'Started Artifact Deletion Service',
        ];
        $this->Heartbeats->createHeartbeat($hbOptions);

        //===Expired Artifacts===========================================================
        $expiredDeletionCount = 0;

        foreach (range(1, 1000) as $loop) {
            $expiredDeletionCount += $this->Artifacts->deleteTopExpired(200);
        }

        $endTimeActual = new FrozenTime();
        $timeDiff = $endTimeActual->diffInSeconds($startTime);
        $io->out(__("Completed {0} deletions in {1} seconds.", $expiredDeletionCount, $timeDiff));

        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => __("Completed {0} deletions (expired Artifacts) in {1} seconds.", $expiredDeletionCount, $timeDiff)
        ];
        $this->Heartbeats->createPulse($hbOptions);
        //===============================================================================


        //===Missing Artifacts===========================================================
        $missingDeletionCount = 0;

        foreach (range(1, 1000) as $loop) {
            $missingDeletionCount += $this->Artifacts->deleteHasMissingArtifact(200);
        }

        $endTimeActual = new FrozenTime();
        $timeDiff = $endTimeActual->diffInSeconds($startTime);
        $io->out(__("Completed {0} deletions in {1} seconds.", $missingDeletionCount, $timeDiff));

        $hbOptions = [
            'context' => $heartbeatContext,
            'name' => __("Completed {0} deletions (missing Artifacts) in {1} seconds.", $missingDeletionCount, $timeDiff)
        ];
        $this->Heartbeats->createPulse($hbOptions);
        //===============================================================================

        //sleep until the artifactDeleter interval timer is up or if already exceeded quit now
        $artifactDeleterInterval = Configure::read("Settings.repo_purge_interval");

        $endTimeScheduled = (clone $startTime)->addMinute($artifactDeleterInterval);
        if ($endTimeActual->gte($endTimeScheduled)) {
            $io->out(__("Artifact Deleter interval of {0} minutes exceeded so quitting now.", $artifactDeleterInterval));
        } else {
            $sleepTime = $endTimeScheduled->diffInSeconds($endTimeActual);
            $io->out(__("Artifact Deleter interval of {0} minutes not yet reached so sleeping for {1} seconds.", $artifactDeleterInterval, $sleepTime));
            sleep($sleepTime);
            $io->out(__("Sleep is over so quitting now.", $artifactDeleterInterval));
        }

        $this->Heartbeats->purgePulses();
        return 0;
    }

}
