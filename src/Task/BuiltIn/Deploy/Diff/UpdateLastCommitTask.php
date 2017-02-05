<?php
namespace Mage\Task\BuiltIn\Deploy\Diff;

/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 12:50
 */
class UpdateLastCommitTask extends \Mage\Task\AbstractTask
{
    public function getName()
    {
        return 'deploy/update-last-commit';
    }

    public function getDescription()
    {
        return '[Deploy] Updating last commit file';
    }

    public function execute()
    {
        $process  = $this->runtime->runLocalCommand('git rev-parse --verify HEAD');
        $lastHash = $process->getOutput();

        $process = $this->runtime->runRemoteCommand("echo \"{$lastHash}\" >> .mage-deployment.log", false);

        return $process->isSuccessful();
    }
}
