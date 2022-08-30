<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'logger:slice')]
class LoggerCommand extends Command
{ 

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED , 'User password')
            ->addArgument('date', InputArgument::REQUIRED , 'Date of slice');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try 
        {        
            $removeState = 0;
            if ($filename = $input->getArgument('filename')) {
                $out = array();
                $fn = fopen($filename,"r");
                while(! feof($fn))  {
                    $result = fgets($fn);
                    if(strpos($result, $input->getArgument('date')) !== false){
                        $removeState = 1;
                    }
                    if($removeState!=1){
                        $out[] = $result;
                    }
                }
            } else {
                throw new \RuntimeException("Please provide a filename or pipe template content to STDIN.");
            }
            $output->writeln(count($out));
            
            $fp = fopen($filename, "w+");
            flock($fp, LOCK_EX);
            foreach($out as $line) {
                fwrite($fp, $line);
            }
            flock($fp, LOCK_UN);
            fclose($fp);  
        } catch(Exception $e){
            echo $e->getMessage();
        }
        
        return Command::SUCCESS;
    }
}