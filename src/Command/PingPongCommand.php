<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime;

/**
 * PingPong command.
 */
class PingPongCommand extends Command
{
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
        $limit = $args->getArgumentAt(0);

        if (!$limit) {
            $limit = 2;
        }
        $io->out(__("I'm going to play Ping Pong for the next {0} seconds", $limit));

        $nextPlay = "Ping";
        $playtimeEnd = (new FrozenTime())->addSeconds($limit);

        $play = true;
        $hits = 1;
        while ($play) {

            if ($nextPlay == "Ping") {
                $io->out(__("Hit {0}: Ping! ---->", $hits));
                $nextPlay = "Pong";
            } else {
                $io->out(__("Hit {0}: <---- Pong!", $hits));
                $nextPlay = "Ping";
            }

            $hits++;

            $delay = mt_rand(500, 1200);
            usleep($delay);

            $currentTime = new FrozenTime();
            if ($currentTime->gte($playtimeEnd)) {
                $play = false;
            }
        }

        if ($nextPlay == "Ping") {
            $io->out("Pong Won!");
        } else {
            $io->out("Ping Won!");
        }

        $logContents = $currentTime->i18nFormat("yyyy-MM-dd HH:mm:ss", 'Australia/Sydney') . " Run Time: " . $limit . "\r\n";
        file_put_contents(TMP . "PingPongLog.txt", $logContents, FILE_APPEND);

    }


}
