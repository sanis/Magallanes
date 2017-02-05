<?php
/*
 * This file is part of the Magallanes package.
 *
 * (c) Andrés Montañez <andres@andresmontanez.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mage\Deploy\Strategy;

use Mage\Runtime\Exception\RuntimeException;
use Mage\Runtime\Runtime;

/**
 * Strategy for Deployment for Diff only
 *
 * @author Justinas Bolys <justinas.bolys@nfq.com>
 */
class DiffStrategy implements StrategyInterface
{
    /**
     * @var Runtime
     */
    protected $runtime;

    public function getName()
    {
        return 'Diff';
    }

    public function setRuntime(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }

    public function getPreDeployTasks()
    {
        $this->checkStage(Runtime::PRE_DEPLOY);
        $tasks = $this->runtime->getTasks();

        if ($this->runtime->getBranch() && !$this->runtime->inRollback() && !in_array('git/change-branch', $tasks)) {
            array_unshift($tasks, 'git/change-branch');
        }

        return $tasks;
    }

    public function getOnDeployTasks()
    {
        $this->checkStage(Runtime::ON_DEPLOY);
        $tasks = $this->runtime->getTasks();

        if (!$this->runtime->inRollback() && !in_array('deploy/diff-upload', $tasks)) {
            array_unshift($tasks, 'deploy/diff-upload');
        }

        return $tasks;
    }

    public function getOnReleaseTasks()
    {
        return [];
    }

    public function getPostReleaseTasks()
    {
        return [];
    }

    public function getPostDeployTasks()
    {
        $this->checkStage(Runtime::POST_DEPLOY);
        $tasks = $this->runtime->getTasks();

        if ($this->runtime->getBranch() && !$this->runtime->inRollback() && !in_array('git/change-branch', $tasks)) {
            array_push($tasks, 'git/change-branch');
        }

        return $tasks;
    }

    /**
     * Check the runtime stage is correct
     *
     * @param $stage
     * @throws RuntimeException
     */
    private function checkStage($stage)
    {
        if ($this->runtime->getStage() !== $stage) {
            throw new RuntimeException(sprintf('Invalid stage, got "%s" but expected "%"', $this->runtime->getStage(), $stage));
        }
    }
}
