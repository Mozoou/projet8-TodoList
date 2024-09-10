<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setUsername('admin');
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $admin,
            'admin'
        );
        $admin->setPassword($hashedPassword);
        $admin->setRoles([User::ROLE_ADMIN]);

        $manager->persist($admin);
        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setUsername('user');
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            'user'
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setTitle('Tache'.$i);
            $task->setContent('content');
            $task->setAuthor($user);
            $manager->persist($task);
        }

        for ($i = 7; $i < 14; $i++) {
            $task = new Task();
            $task->setTitle('Tache'.$i);
            $task->setContent('content');
            $task->setAuthor(null);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
