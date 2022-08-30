<?php

namespace App\Tests\Command;

use App\Command\LoggerCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class LoggerCommandTest extends KernelTestCase
{
    public $slicedDate='2022-08-23';

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new LoggerCommand());

        $command = $application->find('logger:slice');
        $commandTester = new CommandTester($command);

        $temp = tmpfile();
        fwrite($temp,  "2022-08-25 Lorem Ipsum
                        2022-08-24 Lorem Ipsum
                        2022-08-23 Lorem Ipsum
                        2022-08-22 Lorem Ipsum
            ");
        $path = stream_get_meta_data($temp)['uri'];    

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'date' => $this->slicedDate,
            'filename' => $path,
        ));
           
        $numberAvailableLines = $commandTester->getDisplay();
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        
        $this->assertEquals(2, $numberAvailableLines);
        $this->assertNotContains($this->slicedDate, $lines);
        
        fclose($temp);
    }
}