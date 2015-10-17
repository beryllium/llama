Llama Commander
---

This library is a helpful addition to the Symfony Console Component that allows developers to define Symfony Console Commands inline instead of having to create a class for each command.

Why does it exist?
---

Because I find that early iterations of projects benefit from flexible definitions of logic, with a later effort being undertaken to organize things into a proper set of classes. Plus, it relieves the burden of having to name things.

How do I use it?
---

When you initialize a new ```LlamaCommand```, you pass it at minimum an "Executor" lambda that determines what the command does. You can also pass it a Configurator, an Initializer, or an Interactor. The Configurator lambda doesn't require any arguments, but the remaining lambdas should take this form:

    function ($input, $output) use (... any scope you want to invoke inside ...) {
    }

If you want to behave nicely, you can also typehint the lambda:

    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    
    ...
    
    function (InputInterface $input, OutputInterface $output) use (... any scope you want to invoke inside ...) {
    }
    
In the typical use case, you would register the command with the console application like so:

    $app     = new Application;        // Silex application
    $console = new ConsoleApplication; // Symfony console component
    
    /* ... extra application setup ... */
    
    $command = new Commands\GenericCommand('queue:listen');
    $console->add(
        $command->setConfigurator(
            function ($config) use ($app, $console) {
                $config->setDescription('Listen for stuff to do')
                       ->addArgument(
                           'items',
                           InputArgument::OPTIONAL,
                           'How much stuff to listen for'
                       );
            }
        )->setExecutor(
            function ($input, $output) use ($app, $console) {
                $pheanstalk = $app['pheanstalk'];
    
                do {
                    $job = $pheanstalk
                      ->watch('testtube')
                      ->ignore('default')
                      ->reserve();
    
                    $output->writeln('Raw data: ' . $job->getData());
                    $pheanstalk->delete($job);
                } while (strtolower($job->getData()) !== 'halt');
            }
        )
    );
    
    $console->run();
    
And that's all for now.