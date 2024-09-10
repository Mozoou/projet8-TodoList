<?php

namespace Tests\App\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'username' => 'username',
            'password' => [
                'first' => 'pass123',
                'second' => 'pass123',
            ],
            'email' => 'test@example.com',
            'roles' => [User::ROLE_ADMIN],
        ];

        $model = new User();
        // $model will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(UserType::class, $model);

        $expected = (new User())
            ->setUsername($formData['username'])
            ->setEmail($formData['email'])
            ->setRoles($formData['roles'])
            ->setPassword($formData['password']['first']);

        // submit the data to the form directly
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $model was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);
    }
}