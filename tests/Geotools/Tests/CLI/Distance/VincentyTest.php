<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\Tests\CLI\Distance;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Geotools\Tests\TestCase;
use Geotools\CLI\Distance\Vincenty;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class VincentyTest extends TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new Vincenty());

        $this->command = $this->application->find('distance:vincenty');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteWithoutArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
        ));
    }

    /**
     * @expectedException Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage It should be a valid and acceptable ways to write geographic coordinates !
     */
    public function testExecuteInvalidArguments()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => 'foo, bar',
            'destination' => ' ',
        ));
    }

    public function testExecuteWithoutOptions()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/4629758\.7977236/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithKmOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--km'        => true,
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/4629\.7587977236/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithMileOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--mile'      => 'true',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/2876\.7987439128/', $this->commandTester->getDisplay());
    }

    public function testExecuteWithFtOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--ft'        => 'true',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/15189497\.36786/', $this->commandTester->getDisplay());
    }
}
