<?php

namespace Tests\App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractRepositoryTest extends KernelTestCase
{
    protected ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    abstract public function dataProivderSearchTerms(): array;

    /**
     * @dataProvider dataProivderSearchTerms
     */
    public function testSeach(
        string $entityFqcn,
        string $term,
        mixed $value,
    ): void {
        $object = $this->em
            ->getRepository($entityFqcn)
            ->findOneBy([$term => $value])
        ;

        $this->assertNotNull($object);
        $this->assertInstanceOf($entityFqcn, $object);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }
}