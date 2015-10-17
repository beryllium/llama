<?php

namespace Beryllium\Llama;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LlamaCommand extends Command
{
    protected $executor;
    protected $configurator;
    protected $interactor;
    protected $initializer;

    public function setExecutor($callable)
    {
        $this->executor = $callable;
        return $this;
    }

    public function setConfigurator($callable)
    {
        $this->configurator = $callable;
        return $this;
    }

    public function setInteractor($callable)
    {
        $this->interactor = $callable;
        return $this;
    }

    public function setInitializer($callable)
    {
        $this->initializer = $callable;
        return $this;
    }

    protected function configure()
    {
        $func = $this->configurator;
        if (is_callable($func)) {
            $func($this);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $func = $this->executor;
        if (is_callable($func)) {
            return $func($input, $output);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $func = $this->interactor;
        if (is_callable($func)) {
            return $func($input, $output);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $func = $this->initializer;
        if (is_callable($func)) {
            return $func($input, $output);
        }
    }
}
