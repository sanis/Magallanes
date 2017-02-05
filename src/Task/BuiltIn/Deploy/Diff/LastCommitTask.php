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
        $this->runtime->setEnvOption('from', 'dc48f19b265150c4cc584cf6c0726d7cb78cef17');

        return true;
    }
}
