<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasIntegrationTest\Db;

use LaminasIntegrationTest\Db\Platform\FixtureLoader;
use LaminasIntegrationTest\Db\Platform\MysqlFixtureLoader;
use LaminasIntegrationTest\Db\Platform\PgsqlFixtureLoader;
use PHPUnit\Framework\BaseTestListener;
use PHPUnit_Framework_TestSuite as TestSuite;

class IntegrationTestListener extends BaseTestListener
{
    /**
     * @var FixtureLoader[]
     */
    private $fixtureLoaders = [];

    public function startTestSuite(TestSuite $suite)
    {
        if ($suite->getName() !== 'integration test') {
            return;
        }

        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_MYSQL')) {
            $this->fixtureLoaders[] = new MysqlFixtureLoader();
        }

        if (getenv('TESTS_LAMINAS_DB_ADAPTER_DRIVER_PGSQL')) {
            $this->fixtureLoaders[] = new PgsqlFixtureLoader();
        }

        if (empty($this->fixtureLoaders)) {
            return;
        }

        printf("\nIntegration test started.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->createDatabase();
        }
    }

    public function endTestSuite(TestSuite $suite)
    {
        if ($suite->getName() !== 'integration test'
            || empty($this->fixtureLoader)
        ) {
            return;
        }

        printf("\nIntegration test ended.\n");

        foreach ($this->fixtureLoaders as $fixtureLoader) {
            $fixtureLoader->dropDatabase();
        }
    }
}
