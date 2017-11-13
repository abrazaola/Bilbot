<?php

namespace Domain\UseCase;

use Domain\Repository\TaskRepository;
use Domain\UseCase\ListTasks\Command;
use Domain\UseCase\ListTasks\Responder;

class ListTasks
{
    /**
     * @param \Domain\UseCase\ListTasks\Command $command
     * @param \Domain\UseCase\ListTasks\Responder $responder
     */
    public function execute(Command $command, Responder $responder)
    {
        $responder->tasksSuccessfullyFound([]);
    }
}
