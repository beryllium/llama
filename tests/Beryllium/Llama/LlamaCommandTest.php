<?php

namespace Beryllium\Llama;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\BufferedOutput;

class LlamaCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicCommand()
    {
        $console = new Application();
        $console->add(
            new LlamaCommand(
                'test:llama-command',
                null,
                function ($input, $output) {
                    $output->writeln('works');
                }
            )
        );

        $this->assertTrue($console->has('test:llama-command'));
        $command = $console->get('test:llama-command');
        $this->assertInstanceOf('Beryllium\Llama\LlamaCommand', $command);
        $input  = new ArrayInput(array('test:llama-command'));
        $output = new BufferedOutput();
        $command->run($input, $output);
        $this->assertSame("works\n", $output->fetch());
    }

    public function testHelp()
    {
        $command = new LlamaCommand(
            'test:llama-help',
            function ($config) {
                $config->setDescription('Listen for stuff to do')
                    ->addArgument(
                        'items',
                        InputArgument::OPTIONAL,
                        'How much stuff to listen for'
                    );
            }
        );

        $this->assertInstanceOf('Beryllium\Llama\LlamaCommand', $command);
        $this->assertSame("test:llama-help [<items>]", $command->getSynopsis());
    }
}