<?php

namespace Beryllium\Llama;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * LlamaCommand allows you to define Symfony Console Component commands as lambda functions
 *
 * @package Beryllium\Llama
 */
class LlamaCommand extends Command
{
    protected $executor;
    protected $configurator;
    protected $interactor;
    protected $initializer;

    /**
     * @param null|string   $name           Command Name (optionally including a namespace such as "llama:")
     * @param null|Callable $configurator   Callable that defines the configuration for this command,
     *                                      including arguments and options. Example:
     *                                          function ($config) {
     *                                              $config->setDescription('Do some stuff!');
     *                                          }
     * @param null|Callable $executor       Callable that defines the execution logic for this command. Example:
     *                                          function (InputInterface $input, OutputInterface $output) {
     *                                              $output->writeln("It's ALIVE!");
     *                                          }
     * @param null|Callable $interactor     Optional callable that defines user interaction logic.
     * @param null|Callable $initializer    Optional callable that initializes parameters before interaction and exection.
     */
    public function __construct($name = null, $configurator = null, $executor = null, $interactor = null, $initializer = null)
    {
        $this->setConfigurator($configurator);
        $this->setExecutor($executor);
        $this->setInteractor($interactor);
        $this->setInitializer($initializer);
        parent::__construct($name);
    }

    /**
     * Callable that defines the execution logic for this command. Example:
     *
     *     function (InputInterface $input, OutputInterface $output) {
     *         $output->writeln("It's ALIVE!");
     *     }
     *
     * @param $callable Callable    our execution logic
     * @return $this
     */
    public function setExecutor($callable)
    {
        $this->executor = $callable;
        return $this;
    }

    /**
     * Callable that defines the configuration for this command,
     * including arguments and options. Example:
     *
     *     function ($config) {
     *         $config->setDescription('Do some stuff!');
     *     }
     *
     * @param $callable Callable    our configuration logic
     * @return $this
     */
    public function setConfigurator($callable)
    {
        $this->configurator = $callable;
        return $this;
    }

    /**
     * Callable that defines the interaction logic for this command. Example:
     *
     *     function (InputInterface $input, OutputInterface $output) {
     *         // Not sure. Maybe something to do with the Question Helper?
     *         // http://symfony.com/doc/current/components/console/helpers/questionhelper.html
     *     }
     *
     * @param $callable Callable    our interaction logic
     * @return $this
     */
    public function setInteractor($callable)
    {
        $this->interactor = $callable;
        return $this;
    }

    /**
     * Callable that defines the initialization logic for this command. Example:
     *
     *     function (InputInterface $input, OutputInterface $output) {
     *         // Honestly have no idea what to write as an example. Haven't used it.
     *     }
     *
     * @param $callable Callable    our initialization logic
     * @return $this
     */
    public function setInitializer($callable)
    {
        $this->initializer = $callable;
        return $this;
    }

    protected function configure()
    {
        $func = $this->configurator;
        if (is_callable($func)) {
            call_user_func($func, $this);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $func = $this->executor;
        if (is_callable($func)) {
            return call_user_func($func, $input, $output);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $func = $this->interactor;
        if (is_callable($func)) {
            return call_user_func($func, $input, $output);
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $func = $this->initializer;
        if (is_callable($func)) {
            return call_user_func($func, $input, $output);
        }
    }
}
