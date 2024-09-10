<?php

namespace Tests\App\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        // Sample form data
        $formData = [
            'title' => 'Test Task',
            'content' => 'This is a test task content.',
        ];

        // Create a new Task entity to bind to the form
        $model = new Task();

        // Create the form
        $form = $this->factory->create(TaskType::class, $model);

        // Expected entity after form submission
        $expected = (new Task())
            ->setTitle($formData['title'])
            ->setContent($formData['content']);

        // Submit the data to the form
        $form->submit($formData);

        // Ensure there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // Check that $model was modified as expected when the form was submitted
        $this->assertEquals($expected->getTitle(), $model->getTitle());
        $this->assertEquals($expected->getContent(), $model->getContent());

        // Check the form is valid
        $this->assertTrue($form->isValid());
    }
}
