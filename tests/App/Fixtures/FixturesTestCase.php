<?php

namespace Tests\App\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class FixturesTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected Fixture $fixtures;

    abstract protected function getFixturesFqcn(): string;

    protected function setUp(): void
    {
        self::bootKernel();

        $fixtureFqcn = $this->getFixturesFqcn();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        /** @var ?Fixture $fixtures */
        $fixtures = static::getContainer()->get($fixtureFqcn);
        if (null !== $fixtures) {
            $this->fixtures = $fixtures;
        } else {
            $this->fail(sprintf('%s service not exists', $fixtureFqcn));
        }
    }

    protected function loadFixtures(): void
    {
        $this->fixtures->load($this->em);
    }

    /**
     * @param list<string> $tables
     */
    protected function cleanTables(array $tables): void
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            foreach ($tables as $table) {
                $truncateSql = $platform->getTruncateTableSQL($table, true);
                $connection->executeStatement($truncateSql);
            }
        } elseif ($platform instanceof MySQLPlatform) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($tables as $table) {
                $connection->executeStatement(sprintf('DELETE FROM %s', $table));
            }

            $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
        } else {
            $this->fail('Database platform not handled by this tests');
        }
    }
}
