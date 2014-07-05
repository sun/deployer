<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

class DeployerTester extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper');
        $dialog->expects($this->any())
            ->method('askHiddenResponse')
            ->will($this->returnValue('password'));

        $helperSetMock = $this->getMock('Symfony\Component\Console\Helper\HelperSet');
        $helperSetMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($dialog));

        $app = new Application();
        $app->setAutoExit(false);
        $app->setCatchExceptions(false);

        new Deployer(
            $app,
            $this->getMock('\Symfony\Component\Console\Input\InputInterface'),
            $this->getMock('\Symfony\Component\Console\Output\OutputInterface'),
            $helperSetMock
        );

        Deployer::$servers['main'] = $this->getMock('Deployer\Server\ServerInterface');
    }

    protected function tearDown()
    {
        Deployer::$parameters = [];
        Deployer::$servers = [];
        Deployer::$tasks = [];
    }

    protected function runCommand($command)
    {
        $dep = Deployer::get();
        $dep->transformTasksToConsoleCommands();
        $appTester = new ApplicationTester($dep->getConsole());
        $appTester->run(['command' => $command]);
        return $appTester;
    }
} 