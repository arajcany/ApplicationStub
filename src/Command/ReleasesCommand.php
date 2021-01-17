<?php

namespace App\Command;

use App\Model\Table\InternalOptionsTable;
use App\Model\Table\SettingsTable;
use App\Utility\Install\Checker;
use App\Utility\Install\VersionControl;
use App\Utility\Release\BuildTasks;
use App\Utility\Release\GitTasks;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Releases command.
 *
 * @property VersionControl $VersionControl
 * @property GitTasks $GitTasks
 * @property BuildTasks $BuildTasks
 * @property InternalOptionsTable $InternalOptions
 * @property SettingsTable $Settings
 */
class ReleasesCommand extends Command
{
    private $VersionControl;
    private $GitTasks;
    private $BuildTasks;

    public function initialize()
    {
        parent::initialize();

        $this->GitTasks = new GitTasks();
        $this->VersionControl = new VersionControl();

        $this->loadModel('InternalOptions');
        $this->loadModel('Settings');
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
        if (strtolower($args->getArgumentAt(0)) == 'build') {
            return $this->build($args, $io);
        }

        return 1;
    }


    /**
     * Build the ZIP release.
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int
     */
    function build(Arguments $args, ConsoleIo $io)
    {
        $gitBranch = $this->GitTasks->getGitBranch();
        $gitCommits = $this->GitTasks->getCommitsSinceLastBuild();
        $gitModified = $this->GitTasks->getGitModified();
        $gitIgnored = $this->GitTasks->getIgnoredFiles();

        if ($gitModified) {
            $io->out(__("Sorry, cannot create a release as the following files have not been committed."));
            foreach ($gitModified as $gitModifiedFile) {
                $io->out(__("  - {0}", $gitModifiedFile));
            }
            return 1;
        }

        $this->BuildTasks = new BuildTasks();
        $this->BuildTasks->setArgs($args);
        $this->BuildTasks->setIo($io);

        $remote_update_url = $this->Settings->findByPropertyKey('remote_update_url')->first();
        $remote_update_url = $remote_update_url['property_value'];

        $remote_update_unc = $this->InternalOptions->getOption('remote_update_unc');
        $remote_update_sftp_host = $this->InternalOptions->getOption('remote_update_sftp_host');
        $remote_update_sftp_port = $this->InternalOptions->getOption('remote_update_sftp_port');
        $remote_update_sftp_username = $this->InternalOptions->getOption('remote_update_sftp_username');
        $remote_update_sftp_password = $this->InternalOptions->getOption('remote_update_sftp_password', true);
        $remote_update_sftp_timeout = $this->InternalOptions->getOption('remote_update_sftp_timeout');
        $remote_update_sftp_path = $this->InternalOptions->getOption('remote_update_sftp_path');

        $buildOptions = [
            'gitIgnored' => $gitIgnored,
            'remoteUpdateSftp' => null,
            'remoteUpdateUnc' => null,
        ];

        $sftpRoundTripSettings = [
            'url' => $remote_update_url,
            'host' => $remote_update_sftp_host,
            'port' => $remote_update_sftp_port,
            'username' => $remote_update_sftp_username,
            'password' => $remote_update_sftp_password,
            'timeout' => $remote_update_sftp_timeout,
            'path' => $remote_update_sftp_path,
        ];

        $uncRoundTripSettings = [
            'url' => $remote_update_url,
            'unc' => $remote_update_unc,
        ];

        $Checker = new Checker();
        $isSFTP = $Checker->checkSftpSettings($sftpRoundTripSettings);
        if ($isSFTP) {
            $buildOptions['remoteUpdateSftp'] = $sftpRoundTripSettings;
        }

        $isUNC = $Checker->checkUncSettings($uncRoundTripSettings);
        if ($isUNC) {
            $buildOptions['remoteUpdateUnc'] = $uncRoundTripSettings;
        }

        $this->BuildTasks->build($buildOptions);
    }

}
