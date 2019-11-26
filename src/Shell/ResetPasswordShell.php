<?php

namespace App\Shell;

use App\Model\Entity\User;
use Cake\Console\Shell;
use App\Model\Table\UsersTable;

/**
 * ResetPassword shell command.
 * @property UsersTable $Users
 */
class ResetPasswordShell extends Shell
{
    public $Users;

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->loadModel('Users');

        $userIdentifier = $this->in('Please type the Username or Email of the Password you would like to reset.');

        /**
         * @var User $user
         */
        $user = $this->Users->find('all')
            ->where(['OR' => ['username' => $userIdentifier, 'email' => $userIdentifier]])
            ->first();

        if (!$user) {
            $this->abort(__("Sorry, User {0} was not found.", $userIdentifier), 1);
        }

        $newPassword = $this->in('Please type the new Password.');

        $user->password = $newPassword;
        $result = $this->Users->save($user);
        if ($result) {
            $this->abort(__("The password for {0} was successfully changed.", $userIdentifier), 1);
        } else {
            $this->abort(__("The was an issue when changing the password for {0}. Please try again.", $userIdentifier), 1);
        }
    }
}
