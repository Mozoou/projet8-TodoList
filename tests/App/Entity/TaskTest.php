<?php

namespace Tests\App\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskTest extends AbstractEntityTestCase
{
    public function dataProviderValidateEntity(): array
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findAll()[0];

        return [
            'Valid Task' => [
                Task::class,
                [
                    'title' => 'title',
                    'content' => 'content',
                    'author' => $user,
                    'createdAt' => new \DateTime(),
                ],
                [],
            ],
            'Invalid with empty title field' => [
                Task::class,
                [
                    'title' => '',
                    'content' => 'content',
                    'author' => $user,
                    'createdAt' => new \DateTime(),
                ],
                [
                    'title' => [
                        '/Vous devez saisir un titre./'
                    ]
                ],
            ],
            'Invalid with empty content field' => [
                Task::class,
                [
                    'title' => 'title',
                    'content' => '',
                    'author' => $user,
                    'createdAt' => new \DateTime(),
                ],
                [
                    'content' => [
                        '/Vous devez saisir du contenu./'
                    ]
                ],
            ],
        ];
    }

    public function dataProviderPersistEntity(): array
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findAll()[0];

        return [
            'Valid User' => [
                Task::class,
                [
                    'author' => $user,
                    'title' => 'title',
                    'content' => 'content',
                    'createdAt' => new \DateTime(),
                ],
                [
                    'author' => User::class
                ],
                [
                    'author' => $user,
                    'title' => 'title',
                    'content' => 'content',
                    'createdAt' => new \DateTime(),
                ],
                null,
                [],

            ],
        ];
    }

    public function testGetId(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);

        $this->assertIsNumeric($task->getId());
    }

    public function testGetCreatedAt(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);

        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
    }

    public function testTaskToggle(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);

        $this->assertFalse($task->isDone());
        $task->toggle(!$task->isDone());
        $this->assertTrue($task->isDone());
    }

    public function testGetAuthor(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);

        $this->assertNotNull($task->getAuthor());
        $this->assertInstanceOf(User::class, $task->getAuthor());
    }
}