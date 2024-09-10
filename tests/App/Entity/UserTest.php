<?php

namespace Tests\App\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class UserTest extends AbstractEntityTestCase
{
    public function dataProviderValidateEntity(): array
    {
        return [
            'Valid User' => [
                User::class,
                [
                    'email' => 'test@example.com',
                    'password' => 'password',
                    'username' => 'test',
                ],
                []
            ],
            'Invalid email field' => [
                User::class,
                [
                    'email' => 'test@examp',
                    'password' => 'password',
                    'username' => 'test',
                ],
                [
                    'email' => [
                        "/Le format de l'adresse n'est pas correcte./"
                    ]
                ]
            ],
            'Empty email field' => [
                User::class,
                [
                    'email' => '',
                    'password' => 'password',
                    'username' => 'test',
                ],
                [
                    'email' => [
                        "/Vous devez saisir une adresse email./"
                    ]
                ]
            ],
            'Already used email' => [
                User::class,
                [
                    'email' => 'user@example.com',
                    'password' => 'password',
                    'username' => 'test',
                ],
                [
                    'email' => [
                        "/L'adresse mail est déjà existante./"
                    ]
                ]
            ],
            'Empty username field' => [
                User::class,
                [
                    'email' => 'test@example.com',
                    'password' => 'password',
                    'username' => '',
                ],
                [
                    'username' => [
                        "/Vous devez saisir un nom d'utilisateur./"
                    ]
                ]
            ],
            'Already used username' => [
                User::class,
                [
                    'email' => 'test@example.com',
                    'password' => 'password',
                    'username' => 'user',
                ],
                [
                    'username' => [
                        "/Ce nom d'utilisateur existe déjà./"
                    ]
                ]
            ],
        ];
    }

    public function dataProviderPersistEntity(): array
    {
        return [
            'Valid data entity' => [
                User::class,
                [
                    'email' => 'test@example.com',
                    'password' => 'abc123',
                    'username' => 'test',
                ],
                [],
                [
                    'email' => 'test@example.com',
                    'password' => 'abc123',
                    'username' => 'test',
                ],
                null,
                [],
            ],
            'Throw exception because user with same email exists' => [
                User::class,
                [
                    'email' => 'user@example.com',
                    'password' => 'abc123',
                    'username' => 'test',
                ],
                [],
                [],
                UniqueConstraintViolationException::class,
                [
                    "Duplicate entry 'user@example.com'",
                ],
            ],
            'Throw exception because user with same username exists' => [
                User::class,
                [
                    'email' => 'test1@example.com',
                    'password' => 'abc123',
                    'username' => 'user',
                ],
                [],
                [],
                UniqueConstraintViolationException::class,
                [
                    "Duplicate entry 'user'",
                ],
            ]
        ];
    }

    public function testAddTaskToUser(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);
        /** @var User $user */
        $user = $em->getRepository(User::class)->findAll()[0];

        $task->setAuthor(null);

        $user->addTask($task);
        $this->assertInstanceOf(User::class, $task->getAuthor());
        $this->assertInstanceOf(Collection::class, $user->getTasks());
        $this->assertCount(1, $user->getTasks());
    }

    public function testRemoveTaskToUser(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var User $user */
        $user = $em->getRepository(User::class)->findAll()[0];
        /** @var Task $task */
        $task = $em->getRepository(Task::class)->findOneBy(['isDone' => false]);

        $task->setAuthor(null);

        $user->addTask($task);
        $this->assertInstanceOf(User::class, $task->getAuthor());
        $this->assertCount(1, $user->getTasks());

        $user->removeTask($task);
        $this->assertNull($task->getAuthor());
        $this->assertCount(0, $user->getTasks());
    }

    public function testGetUserIdentifier(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var User $user */
        $user = $em->getRepository(User::class)->findAll()[0];

        $this->assertEquals($user->getEmail(), $user->getUserIdentifier());
    }

    public function testGetSalt(): void
    {
        $this->setUp();
        $em = $this->container->get(EntityManagerInterface::class);
        /** @var User $user */
        $user = $em->getRepository(User::class)->findAll()[0];

        $this->assertNull($user->getSalt());
    }
}
