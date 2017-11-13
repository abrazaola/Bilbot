<?php

namespace Domain\UseCase;

use Domain\Entity\Task;
use Domain\Repository\TaskRepository;
use Domain\UseCase\AddTask\Command;
use Domain\UseCase\AddTask\Responder;

class AddTask
{
    /**
     * @param \Domain\UseCase\AddTask\Command $command
     * @param \Domain\UseCase\AddTask\Responder $responder
     */
    public function execute(Command $command, Responder $responder)
    {
        $task = new Task($command->name);

        $responder->taskSuccessfullyAdded($task);
    }
}
