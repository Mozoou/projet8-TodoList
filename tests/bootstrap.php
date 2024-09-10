<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$kernel = new Kernel('test', true);

$application = new Application($kernel);
$application->setAutoExit(false);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--force' => true,
        '--if-exists' => true,
        '--no-interaction' => true,
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:database:create',
        '--no-interaction' => true,
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:schema:create',
        '--no-interaction' => true,
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:schema:validate',
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--no-interaction' => true,
        '--group' => ['test'],
    ]),
    new ConsoleOutput()
);

return $application;
