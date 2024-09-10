<?php

namespace Tests\App\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends AbstractRepositoryTest
{
    public function dataProivderSearchTerms(): array
    {
        return [
            'search by username' => [
                User::class,
                'username',
                'user',
            ],
            'search by email' => [
                User::class,
                'email',
                'user@example.com',
            ],
            'search by id' => [
                User::class,
                'id',
                1,
            ]
        ];
    }

    public function testUpgradePassword(): void
    {
        $this->setUp();
        $userRepository = $this->em->getRepository(User::class);

        // Create a new user for testing
        $user = new User();
        $user->setEmail('email@test.com');
        $user->setUsername('testuser');
        $user->setPassword('oldpassword'); // Initial password
        
        // Persist the user
        $this->em->persist($user);
        $this->em->flush();

        // New hashed password
        $newHashedPassword = 'newhashedpassword';
        
        // Call the method to test
        $userRepository->upgradePassword($user, $newHashedPassword);

        // Refresh the user from the database
        $this->em->refresh($user);

        // Assert that the password has been updated
        $this->assertEquals($newHashedPassword, $user->getPassword());
    }

    public function testUpgradePasswordThrowsExceptionForNonUserInstance(): void
    {
        $this->setUp();
        $userRepository = $this->em->getRepository(User::class);

        $this->expectException(UnsupportedUserException::class);

        // Create a mock object that is not an instance of User
        $mock = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // Call the method with the mock object
        $userRepository->upgradePassword($mock, 'newhashedpassword');
    }

    public function testRemoveEntity(): void
    {
        $this->setUp();
        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);

        // Create a new user for testing
        $user = new User();
        $user->setEmail('newUser@test.com');
        $user->setUsername('newTestUser');
        $user->setPassword('oldpassword');
        
        // Persist the user
        $this->em->persist($user);
        $this->em->flush();

        $this->assertIsNumeric($user->getId());

        $userRepository->remove($user, true);

        $this->assertNull($user->getId());
    }
}