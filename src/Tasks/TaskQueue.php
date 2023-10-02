<?php

namespace App\Tasks;

use App\Tasks\Contracts\ITask;

class TaskQueue
{
    /** @var $tasks ITask[]  */
    protected array $tasks = [];

    public function push(ITask $task)
    {
        $this->tasks[] = $task;
    }

    /**
     * @return ITask[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function getNext(): ITask
    {
        return reset($this->tasks);
    }

    public function clear(): void
    {
        $this->tasks = [];
    }

    public function clearOfType(ITask $taskToClear) {
        $this->tasks = array_values(array_filter($this->getTasks(), function (ITask $task) use ($taskToClear) {
            return !$task instanceof $taskToClear;
        }));
    }
}