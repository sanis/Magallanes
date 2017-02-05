<?php
namespace Mage\Task\BuiltIn\Deploy\Diff;

/**
 * Created by PhpStorm.
 * User: jbolys
 * Date: 05/02/2017
 * Time: 12:50
 */
class LastCommitTask extends \Mage\Task\AbstractTask
{
    public function getName()
    {
        return 'deploy/last-commit';
    }

    public function getDescription()
    {
        return '[Deploy] Getting last commit hash from server';
    }

    public function execute()
    {
        $process = $this->runtime->runRemoteCommand('cat .mage-deployment.log', false);

        $lastDeployedHash = $process->getOutput();
        $this->runtime->setEnvOption('from', $lastDeployedHash);

        return $process->isSuccessful();
    }
}
