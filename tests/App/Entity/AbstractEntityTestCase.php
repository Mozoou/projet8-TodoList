<?php

namespace Tests\App\Entity;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEntityTestCase extends KernelTestCase
{
    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
    }

    abstract public function dataProviderValidateEntity(): array;

    abstract public function dataProviderPersistEntity(): array;

    /**
     * @dataProvider dataProviderValidateEntity
     */
    public function testValidateEntity($entityFqcn, array $data, array $errorsMapping = [])
    {
        $entity = new $entityFqcn();
        $this->fillEntity($entity, $data);
        $this->assertValidateEntity($entity, $errorsMapping);
    }

    /**
     * @dataProvider dataProviderPersistEntity
     */
    public function testPersistEntity(
        $entityFqcn,
        array $data,
        array $expectedEntityTypes,
        array $expectedEntityData,
        string $expectedExceptionFqcn = null,
        array $expectedExceptionMessages = []
    ) {
        $entity = new $entityFqcn();
        $this->fillEntity($entity, $data);

        if (null !== $expectedExceptionFqcn) {
            $this->expectException($expectedExceptionFqcn);

            foreach ($expectedExceptionMessages as $expectedExceptionMessage) {
                $this->expectExceptionMessage($expectedExceptionMessage);
            }
        }

        $entityManager = $this->container->get(EntityManagerInterface::class);

        $entityManager->persist($entity);
        $entityManager->flush();

        $this->assertEntity($expectedEntityTypes, $expectedEntityData, $entity);
        $this->assertNotNull($entity->getId());
    }

    protected function assertEntity(array $expectedTypes, array $expectedValues, $entity)
    {
        foreach ($expectedTypes as $property => $type) {
            if (
                interface_exists($type)
                || class_exists($type)
            ) {
                $this->assertInstanceOf($type, $entity->{'get'.ucfirst($property)}());
            } else {
                $this->assertSame($type, gettype($entity->{'get'.ucfirst($property)}()));
            }
        }

        foreach ($expectedValues as $property => $value) {
            $this->assertSame($value, $entity->{'get'.ucfirst($property)}());
        }
    }

    protected function fillEntity($entity, array $data)
    {
        foreach ($data as $property => $value) {
            $method = 'set'.ucfirst($property);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
            $method = 'add'.ucfirst($property);
            if (method_exists($entity, $method)) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $entity->$method($val);
                    }
                }
            }
        }
    }

    protected function assertValidateEntity($entity, array $errorsMapping = [])
    {
        $errors = $this->container->get(ValidatorInterface::class)->validate($entity);

        $countErrorMessages = 0;
        foreach ($errorsMapping as $property => $errorMessages) {
            ++$countErrorMessages;

            foreach ($errorMessages as $errorMessage) {
                $errorExists = false;

                if (0 < count($errors)) {
                    foreach ($errors as $error) {
                        $errorExists = $errorExists
                            || (
                                $property === $error->getPropertyPath()
                                && 1 === preg_match($errorMessage, $error->getMessage())
                            )
                        ;
                    }
                }

                if ($errorExists) {
                    $this->addToAssertionCount(1);
                } else {
                    $this->fail(
                        sprintf(
                            'Error message "%s" for property "%s" not in errors list returned by validate function',
                            $errorMessage,
                            $property
                        )
                    );
                }
            }
        }

        $this->assertCount($countErrorMessages, $errors);
    }
}
