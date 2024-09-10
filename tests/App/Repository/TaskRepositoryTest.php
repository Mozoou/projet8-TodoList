<?php

namespace Tests\App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;

class TaskRepositoryTest extends AbstractRepositoryTest
{
    public function dataProivderSearchTerms(): array
    {
        return [
            'search by content' => [
                Task::class,
                'content',
                'content',
            ],
            'search by title' => [
                Task::class,
                'title',
                'Tache1',
            ],
            'search by id' => [
                Task::class,
                'id',
                2,
            ]
        ];
    }

    public function testAddNewTask(): void
    {
        $this->setUp();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->em->getRepository(Task::class);
        $user = $this->em->getRepository(User::class)->findAll()[0];

        $task = (new Task())
            ->setTitle('new title')
            ->setAuthor($user)
            ->setContent('New contenft');

        $taskRepository->add($task, true);

        $this->assertIsNumeric($task->getId());
    }

    public function testRemoveTask(): void
    {
        $this->setUp();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->em->getRepository(Task::class);
        $task = $taskRepository->findAll()[0];

        $taskRepository->remove($task, true);

        $this->assertNull($task->getId());
    }
}