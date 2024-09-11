<?php

namespace Tests\App\Fixtures;

use App\DataFixtures\AppFixtures;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

class WelabzErpGestionCommandeClientFixturesTest extends FixturesTestCase
{
    public const TABLES = [
        'task',
        'user',
    ];

    public function testTablesExists(): void
    {
        $connection = $this->em->getConnection();

        $expectedTables = static::TABLES;

        $this->cleanTables($expectedTables);
        $this->loadFixtures();

        $tables = $connection->createSchemaManager()->listTables();
        $tablesNames = [];
        foreach ($tables as $table) {
            $tablesNames[] = $table->getName();
        }

        $this->assertEqualsCanonicalizing($expectedTables, $tablesNames);
    }

    public function testTablesFilled(): void
    {
        $this->cleanTables(static::TABLES);

        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($metadatas as $metadata) {
            if (
                !$metadata->isMappedSuperclass
                && !$metadata->isEmbeddedClass
            ) {
                $entityClass = $metadata->getName();

                $this->assertSame(
                    0,
                    $this->em->getRepository($entityClass)->count([]),
                    sprintf('`%s` table must be empty', $entityClass)
                );
            }
        }

        $this->loadFixtures();

        foreach ($metadatas as $metadata) {
            if (
                !$metadata->isMappedSuperclass
                && !$metadata->isEmbeddedClass
                && !array_reduce(
                    [
                        ResetPasswordRequestInterface::class,
                    ],
                    static fn ($carry, $entityFqcn): bool => $carry || is_a($metadata->getName(), $entityFqcn, true),
                    false
                )
            ) {
                $this->assertGreaterThan(
                    0,
                    $this->em->getRepository($metadata->getName())->count([]),
                    sprintf('`%s` table must not be empty', $metadata->getName())
                );
            }
        }
    }

    protected function getFixturesFqcn(): string
    {
        return AppFixtures::class;
    }
}
