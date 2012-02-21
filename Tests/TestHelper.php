<?php
namespace RtxLabs\DataTransformationBundle\Tests;

use Symfony\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Symfony\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Bundle\DoctrineFixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class TestHelper
{
    /**
     * @static
     * @param Symfony\Bundle\FrameworkBundle\Console\Application $application
     * @return void
     */
    static function initDatabase($application, $loadFixtures=true)
    {
        static::runCommand($application,
                          new DropDatabaseDoctrineCommand(),
                          array("command" => "doctrine:database:drop", "--force" => true));
        static::runCommand($application,
                          new CreateDatabaseDoctrineCommand(),
                          array("command" => "doctrine:database:create"));
        static::runCommand($application,
                          new CreateSchemaDoctrineCommand(),
                          array("command" => "doctrine:schema:create"));

        if ($loadFixtures) {
            static::runCommand($application,
                new LoadDataFixturesDoctrineCommand(),
                array("command" => "doctrine:fixtures:load", "--fixtures" => __DIR__ . "/Fixtures/")
            );
        }
    }

    private static function runCommand($application, $command, $input)
    {
        $application->add($command);
        $input = new ArrayInput($input);
        $command->run($input, new ConsoleOutput());
    }
}