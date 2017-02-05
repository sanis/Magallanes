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
        return 'deploy/update-last-commit';
    }

    public function getDescription()
    {
        return '[Deploy] Updating last commit file';
    }

    public function execute()
    {
        return true;
    }
}
